<?php
/**
 * Opalestate Admin API Keys Class
 *
 * @package Opalestate\API
 */

defined('ABSPATH') || exit;

/**
 * Opalestate_Admin_API_Keys.
 */
class Opalestate_Admin_API_Keys {

    /**
     * Initialize the API Keys admin actions.
     */
    public function __construct() {
        add_action('admin_init', [$this, 'actions']);
        add_action('load-opalestate_property_page_opalestate-settings', [$this, 'screen_option']);
        add_action('cmb2_render_api_keys', [$this, 'page_output']);
        add_filter('opalestate_save_settings_advanced_keys', [$this, 'allow_save_settings']);
    }

    /**
     * Check if should allow save settings.
     * This prevents "Your settings have been saved." notices on the table list.
     *
     * @param bool $allow If allow save settings.
     * @return bool
     */
    public function allow_save_settings($allow) {
        if (!isset($_GET['create-key'], $_GET['edit-key'])) { // WPCS: input var okay, CSRF ok.
            return false;
        }

        return $allow;
    }

    /**
     * Check if is API Keys settings page.
     *
     * @return bool
     */
    private function is_api_keys_settings_page() {
        return isset($_GET['page'], $_GET['tab']) && 'opalestate-settings' === $_GET['page'] && 'api_keys' === $_GET['tab']; // WPCS: input var okay, CSRF ok.
    }

    /**
     * Page output.
     */
    public static function page_output() {
        if (isset($_GET['create-key']) || isset($_GET['edit-key'])) {
            $key_id   = isset($_GET['edit-key']) ? absint($_GET['edit-key']) : 0; // WPCS: input var okay, CSRF ok.
            $key_data = static::get_key_data($key_id);
            $user_id  = absint($key_data['user_id']);

            if ($key_id && $user_id && !current_user_can('edit_user', $user_id)) {
                if (get_current_user_id() !== $user_id) {
                    wp_die(esc_html__('You do not have permission to edit this API Key', 'opalestate-pro'));
                }
            }

            include dirname(__FILE__) . '/html-keys-edit.php';
        } else {
            static::table_list_output();
        }
        ?>
        <style>
            input[name="submit-cmb"] {
                display: none !important;
            }
        </style>
        <?php
    }

    /**
     * Add screen option.
     */
    public function screen_option() {
        global $keys_table_list;

        if (!isset($_GET['create-key']) && !isset($_GET['edit-key']) && $this->is_api_keys_settings_page()) { // WPCS: input var okay, CSRF ok.
            $keys_table_list = new Opalestate_Admin_API_Keys_Table_List();

            // Add screen option.
            add_screen_option(
                'per_page', [
                    'default' => 10,
                    'option'  => 'opalestate_keys_per_page',
                ]
            );
        }
    }

    /**
     * Table list output.
     */
    private static function table_list_output() {
        global $wpdb, $keys_table_list;

        echo '<h2>' . esc_html__('REST API',
                'opalestate-pro') . ' <a href="' . esc_url(admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys&create-key=1')) . '" class="add-new-h2">' .
             esc_html__('Add key', 'opalestate-pro') . '</a></h2>';

        // Get the API keys count.
        $count = $wpdb->get_var("SELECT COUNT(key_id) FROM {$wpdb->prefix}opalestate_api_keys WHERE 1 = 1;");

        if (absint($count) && $count > 0) {
            $keys_table_list->prepare_items();

            echo '<input type="hidden" name="page" value="wc-settings" />';
            echo '<input type="hidden" name="tab" value="advanced" />';
            echo '<input type="hidden" name="section" value="keys" />';

            $keys_table_list->views();
            $keys_table_list->search_box(__('Search key', 'opalestate-pro'), 'key');
            $keys_table_list->display();
        } else {
            echo '<div class="opalestate-admin-api">';
            ?>
            <h2 class="opalestate-admin-api-message">
                <?php printf(
                    __('API keys allow users to use the <a href="%s">Opalestate REST API</a> to retrieve donation data in JSON or XML for external applications or devices, such as <a href="%s">Zapi_keyser</a>.',
                        'opalestate-pro'),
                    'https://wpopal.com/opalestate/documentation/opalestate-api_keys-reference/',
                    'https://wpopal.com/addons/opalestate/'
                ); ?>
            </h2>

            <a class="button-primary button" href="<?php echo esc_url(admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys&create-key=1')); ?>">
                <?php esc_html_e('Create an API key', 'opalestate-pro'); ?>
            </a>
            <style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions {
                    display: none;
                }</style>
            <?php
        }
    }

    /**
     * Get key data.
     *
     * @param int $key_id API Key ID.
     * @return array
     */
    private static function get_key_data($key_id) {
        global $wpdb;

        $empty = [
            'key_id'        => 0,
            'user_id'       => '',
            'description'   => '',
            'permissions'   => '',
            'truncated_key' => '',
            'last_access'   => '',
        ];

        if (0 === $key_id) {
            return $empty;
        }

        $key = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT key_id, user_id, description, permissions, truncated_key, last_access
				FROM {$wpdb->prefix}opalestate_api_keys
				WHERE key_id = %d",
                $key_id
            ), ARRAY_A
        );

        if (is_null($key)) {
            return $empty;
        }

        return $key;
    }

    /**
     * API Keys admin actions.
     */
    public function actions() {
        if ($this->is_api_keys_settings_page()) {
            // Revoke key.
            if (isset($_REQUEST['revoke-key'])) { // WPCS: input var okay, CSRF ok.
                $this->revoke_key();
            }

            // Bulk actions.
            if (isset($_REQUEST['action']) && isset($_REQUEST['key'])) { // WPCS: input var okay, CSRF ok.
                $this->bulk_actions();
            }
        }
    }

    /**
     * Notices.
     */
    public static function notices() {
        if (isset($_GET['revoked'])) { // WPCS: input var okay, CSRF ok.
            $revoked = absint($_GET['revoked']); // WPCS: input var okay, CSRF ok.

            /* translators: %d: count */
            // Opalestate_Admin_Settings::add_message( sprintf( _n( '%d API key permanently revoked.', '%d API keys permanently revoked.', $revoked, 'opalestate-pro' ), $revoked ) );
        }
    }

    /**
     * Revoke key.
     */
    private function revoke_key() {
        global $wpdb;

        check_admin_referer('revoke');

        if (isset($_REQUEST['revoke-key'])) { // WPCS: input var okay, CSRF ok.
            $key_id  = absint($_REQUEST['revoke-key']); // WPCS: input var okay, CSRF ok.
            $user_id = (int)$wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}opalestate_api_keys WHERE key_id = %d", $key_id));

            if ($key_id && $user_id && (current_user_can('edit_user', $user_id) || get_current_user_id() === $user_id)) {
                $this->remove_key($key_id);
            } else {
                wp_die(esc_html__('You do not have permission to revoke this API Key', 'opalestate-pro'));
            }
        }

        wp_safe_redirect(esc_url_raw(add_query_arg(['revoked' => 1], admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys'))));
        exit();
    }

    /**
     * Bulk actions.
     */
    private function bulk_actions() {
        check_admin_referer('opalestate-settings');

        if (!current_user_can('manage_opalestate')) {
            wp_die(esc_html__('You do not have permission to edit API Keys', 'opalestate-pro'));
        }

        if (isset($_REQUEST['action'])) { // WPCS: input var okay, CSRF ok.
            $action = sanitize_text_field(wp_unslash($_REQUEST['action'])); // WPCS: input var okay, CSRF ok.
            $keys   = isset($_REQUEST['key']) ? array_map('absint', (array)$_REQUEST['key']) : []; // WPCS: input var okay, CSRF ok.

            if ('revoke' === $action) {
                $this->bulk_revoke_key($keys);
            }
        }
    }

    /**
     * Bulk revoke key.
     *
     * @param array $keys API Keys.
     */
    private function bulk_revoke_key($keys) {
        if (!current_user_can('remove_users')) {
            wp_die(esc_html__('You do not have permission to revoke API Keys', 'opalestate-pro'));
        }

        $qty = 0;
        foreach ($keys as $key_id) {
            $result = $this->remove_key($key_id);

            if ($result) {
                $qty++;
            }
        }

        // Redirect to webhooks page.
        wp_safe_redirect(esc_url_raw(add_query_arg(['revoked' => $qty], admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys'))));
        exit();
    }

    /**
     * Remove key.
     *
     * @param int $key_id API Key ID.
     * @return bool
     */
    private function remove_key($key_id) {
        global $wpdb;

        $delete = $wpdb->delete($wpdb->prefix . 'opalestate_api_keys', ['key_id' => $key_id], ['%d']);

        return $delete;
    }
}

new Opalestate_Admin_API_Keys();
