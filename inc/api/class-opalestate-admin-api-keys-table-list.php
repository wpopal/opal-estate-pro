<?php
/**
 * Opalestate API Keys Table List
 *
 * @package Opalestate\Admin
 */

defined('ABSPATH') || exit;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * API Keys table list class.
 */
class Opalestate_Admin_API_Keys_Table_List extends WP_List_Table {

    /**
     * Initialize the API key table list.
     */
    public function __construct() {
        parent::__construct(
            [
                'singular' => 'key',
                'plural'   => 'keys',
                'ajax'     => false,
            ]
        );
    }

    /**
     * No items found text.
     */
    public function no_items() {
        esc_html_e('No keys found.', 'opalestate-pro');
    }

    /**
     * Get list columns.
     *
     * @return array
     */
    public function get_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => __('Description', 'opalestate-pro'),
            'truncated_key' => __('Consumer key ending in', 'opalestate-pro'),
            'user'          => __('User', 'opalestate-pro'),
            'permissions'   => __('Permissions', 'opalestate-pro'),
            'last_access'   => __('Last access', 'opalestate-pro'),
        ];
    }

    /**
     * Column cb.
     *
     * @param array $key Key data.
     * @return string
     */
    public function column_cb($key) {
        return sprintf('<input type="checkbox" name="key[]" value="%1$s" />', $key['key_id']);
    }

    /**
     * Return title column.
     *
     * @param array $key Key data.
     * @return string
     */
    public function column_title($key) {
        $url     = admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys&edit-key=' . $key['key_id']);
        $user_id = absint($key['user_id']);

        // Check if current user can edit other users or if it's the same user.
        $can_edit = current_user_can('edit_user', $user_id) || get_current_user_id() === $user_id;

        $output = '<strong>';
        if ($can_edit) {
            $output .= '<a href="' . esc_url($url) . '" class="row-title">';
        }
        if (empty($key['description'])) {
            $output .= esc_html__('API key', 'opalestate-pro');
        } else {
            $output .= esc_html($key['description']);
        }
        if ($can_edit) {
            $output .= '</a>';
        }
        $output .= '</strong>';

        // Get actions.
        $actions = [
            /* translators: %s: API key ID. */
            'id' => sprintf(__('ID: %d', 'opalestate-pro'), $key['key_id']),
        ];

        if ($can_edit) {
            $actions['edit']  = '<a href="' . esc_url($url) . '">' . __('View/Edit', 'opalestate-pro') . '</a>';
            $actions['trash'] = '<a class="submitdelete" aria-label="' . esc_attr__('Revoke API key', 'opalestate-pro') . '" href="' . esc_url(
                    wp_nonce_url(
                        add_query_arg(
                            [
                                'revoke-key' => $key['key_id'],
                            ], admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys')
                        ), 'revoke'
                    )
                ) . '">' . esc_html__('Revoke', 'opalestate-pro') . '</a>';
        }

        $row_actions = [];

        foreach ($actions as $action => $link) {
            $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
        }

        $output .= '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';

        return $output;
    }

    /**
     * Return truncated consumer key column.
     *
     * @param array $key Key data.
     * @return string
     */
    public function column_truncated_key($key) {
        return '<code>&hellip;' . esc_html($key['truncated_key']) . '</code>';
    }

    /**
     * Return user column.
     *
     * @param array $key Key data.
     * @return string
     */
    public function column_user($key) {
        $user = get_user_by('id', $key['user_id']);

        if (!$user) {
            return '';
        }

        if (current_user_can('edit_user', $user->ID)) {
            return '<a href="' . esc_url(add_query_arg(['user_id' => $user->ID], admin_url('user-edit.php'))) . '">' . esc_html($user->display_name) . '</a>';
        }

        return esc_html($user->display_name);
    }

    /**
     * Return permissions column.
     *
     * @param array $key Key data.
     * @return string
     */
    public function column_permissions($key) {
        $permission_key = $key['permissions'];
        $permissions    = [
            'read'       => __('Read', 'opalestate-pro'),
            'write'      => __('Write', 'opalestate-pro'),
            'read_write' => __('Read/Write', 'opalestate-pro'),
        ];

        if (isset($permissions[$permission_key])) {
            return esc_html($permissions[$permission_key]);
        } else {
            return '';
        }
    }

    /**
     * Return last access column.
     *
     * @param array $key Key data.
     * @return string
     */
    public function column_last_access($key) {
        if (!empty($key['last_access'])) {
            /* translators: 1: last access date 2: last access time */
            $date = sprintf(__('%1$s at %2$s', 'opalestate-pro'), date_i18n(get_option('date_format'), strtotime($key['last_access'])),
                date_i18n(get_option('time_format'), strtotime($key['last_access'])));

            return apply_filters('opalestate_api_key_last_access_datetime', $date, $key['last_access']);
        }

        return __('Unknown', 'opalestate-pro');
    }

    /**
     * Get bulk actions.
     *
     * @return array
     */
    protected function get_bulk_actions() {
        if (!current_user_can('remove_users')) {
            return [];
        }

        return [
            'revoke' => __('Revoke', 'opalestate-pro'),
        ];
    }

    /**
     * Search box.
     *
     * @param string $text Button text.
     * @param string $input_id Input ID.
     */
    public function search_box($text, $input_id) {
        if (empty($_REQUEST['s']) && !$this->has_items()) { // WPCS: input var okay, CSRF ok.
            return;
        }

        $input_id     = $input_id . '-search-input';
        $search_query = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : ''; // WPCS: input var okay, CSRF ok.

        echo '<p class="search-box">';
        echo '<label class="screen-reader-text" for="' . esc_attr($input_id) . '">' . esc_html($text) . ':</label>';
        echo '<input type="search" id="' . esc_attr($input_id) . '" name="s" value="' . esc_attr($search_query) . '" />';
        submit_button(
            $text, '', '', false,
            [
                'id' => 'search-submit',
            ]
        );
        echo '</p>';
    }

    /**
     * Prepare table list items.
     */
    public function prepare_items() {
        global $wpdb;

        $per_page     = $this->get_items_per_page('opalestate_keys_per_page');
        $current_page = $this->get_pagenum();

        if (1 < $current_page) {
            $offset = $per_page * ($current_page - 1);
        } else {
            $offset = 0;
        }

        $search = '';

        if (!empty($_REQUEST['s'])) { // WPCS: input var okay, CSRF ok.
            $search = "AND description LIKE '%" . esc_sql($wpdb->esc_like(opalestate_clean(wp_unslash($_REQUEST['s'])))) . "%' "; // WPCS: input var okay, CSRF ok.
        }

        // Get the API keys.
        $keys = $wpdb->get_results(
            "SELECT key_id, user_id, description, permissions, truncated_key, last_access FROM {$wpdb->prefix}opalestate_api_keys WHERE 1 = 1 {$search}" .
            $wpdb->prepare('ORDER BY key_id DESC LIMIT %d OFFSET %d;', $per_page, $offset), ARRAY_A
        ); // WPCS: unprepared SQL ok.

        $count = $wpdb->get_var("SELECT COUNT(key_id) FROM {$wpdb->prefix}opalestate_api_keys WHERE 1 = 1 {$search};"); // WPCS: unprepared SQL ok.

        $this->items = $keys;

        // Set the pagination.
        $this->set_pagination_args(
            [
                'total_items' => $count,
                'per_page'    => $per_page,
                'total_pages' => ceil($count / $per_page),
            ]
        );
    }
}
