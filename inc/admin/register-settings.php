<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Plugin_Settings {

    /**
     * Option key, and option page slug
     *
     * @var string
     */
    private $key = 'opalestate_settings';

    /**
     * Array of metaboxes/fields
     *
     * @var array
     */
    protected $option_metabox = [];

    /**
     * Options Page title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Options Page hook
     *
     * @var string
     */
    protected $options_page = '';

    protected $subtabs = [];

    protected $setting_object = [];

    /**
     * Constructor
     *
     * @since 1.0
     */
    public function __construct() {

        add_action('admin_menu', [$this, 'admin_menu'], 10);

        add_action('admin_init', [$this, 'init']);

        //Custom CMB2 Settings Fields
        add_action('cmb2_render_opalestate_title', 'opalestate_title_callback', 10, 5);

        // add_action( 'cmb2_render_api_keys', 'opalestate_api_keys_callback', 10, 5 );
        // add_action( 'cmb2_render_license_key', 'opalestate_license_key_callback', 10, 5 );
        add_action("cmb2_save_options-page_fields", [$this, 'settings_notices'], 10, 3);

        // Include CMB CSS in the head to avoid FOUC
        add_action("admin_print_styles-opalestate_properties_page_opalestate-settings", ['CMB2_hookup', 'enqueue_cmb_css']);
    }

    public function admin_menu() {
        // Settings
        $opalestate_settings_page = add_submenu_page(
            'edit.php?post_type=opalestate_property',
            esc_html__('Opalestate Settings', 'opalestate-pro'),
            esc_html__('Settings', 'opalestate-pro'),
            'manage_opalestate_settings',
            'opalestate-settings',
            [$this, 'admin_page_display']);

        if ($opalestate_settings_page) {
            do_action('load-' . $opalestate_settings_page, $this);
        }

        // Addons
        $opalestate_addons_page = add_submenu_page(
            'edit.php?post_type=opalestate_property',
            esc_html__('Opalestate Addons', 'opalestate-pro'),
            esc_html__('Addons', 'opalestate-pro'),
            'manage_options',
            'opalestate-addons',
            [$this, 'admin_addons_page_display']);

        if ($opalestate_addons_page) {
            do_action('load-' . $opalestate_addons_page, $this);
        }
    }

    /**
     * Register our setting to WP
     *
     * @since  1.0
     */
    public function init() {
        register_setting($this->key, $this->key);
    }

    /**
     * Retrieve settings tabs
     *
     * @return array $tabs
     * @since 1.0
     */
    public function opalestate_get_settings_tabs() {

        $settings = $this->opalestate_settings(null);

        $tabs              = [];
        $tabs['general']   = esc_html__('General', 'opalestate-pro');
        $tabs['property']  = esc_html__('Property', 'opalestate-pro');
        $tabs['agents']    = esc_html__('Agents', 'opalestate-pro');
        $tabs['agencies']  = esc_html__('Agencies', 'opalestate-pro');
        $tabs['pages']     = esc_html__('Pages', 'opalestate-pro');
        $tabs['3rd_party'] = esc_html__('3rd Party', 'opalestate-pro');
        $tabs['api_keys']  = esc_html__('API', 'opalestate-pro');

        if (!empty($settings['addons']['fields'])) {
            $tabs['addons'] = esc_html__('Add-ons', 'opalestate-pro');
        }

        if (!empty($settings['licenses']['fields'])) {
            $tabs['licenses'] = esc_html__('Licenses', 'opalestate-pro');
        }

        return apply_filters('opalestate_settings_tabs', $tabs);
    }

    public function admin_addons_page_display() {
        require_once opalestate_get_admin_view('addons/list.php');
    }

    public function get_subtabs_link($tab_id, $stab_id) {
        $tab_url = esc_url(add_query_arg([
            'settings-updated' => false,
            'tab'              => $tab_id,
            'subtab'           => $stab_id,
        ]));

        return $tab_url;
    }

    /**
     * Admin page markup. Mostly handled by CMB2
     *
     * @since  1.0
     */
    public function admin_page_display() {

        $active_tab = isset($_GET['tab']) && array_key_exists($_GET['tab'], $this->opalestate_get_settings_tabs()) ? $_GET['tab'] : 'general';

        $sub_active_tab = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : '';

        $tabs_fields     = $this->opalestate_settings($active_tab);
        $sub_tabs_fields = [];

        if (empty($sub_active_tab) && $this->subtabs) {
            $first          = array_flip($this->subtabs);
            $sub_active_tab = reset($first);
        }

        if ($this->subtabs) {
            $sub_tabs_fields = $this->setting_object->get_subtabs_content($sub_active_tab);
        }
        ?>

        <div class="wrap opalestate_settings_page cmb2_options_page <?php echo $this->key; ?>">
            <h2 class="nav-tab-wrapper">
                <?php
                foreach ($this->opalestate_get_settings_tabs() as $tab_id => $tab_name) {
                    $tab_url = esc_url(add_query_arg([
                        'settings-updated' => false,
                        'tab'              => $tab_id,
                        'subtab'           => false,
                    ], admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings')));

                    $active = $active_tab == $tab_id ? ' nav-tab-active' : '';

                    echo '<a href="' . esc_url($tab_url) . '" title="' . esc_attr($tab_name) . '" class="nav-tab' . $active . '">';
                    echo esc_html($tab_name);

                    echo '</a>';
                }
                ?>
            </h2>
            <div class="form-settings-wrap">
                <?php if ($this->subtabs): ?>
                    <div class="subtab-settings-navs">
                        <ul>

                            <?php foreach ($this->subtabs as $key => $value): ?>
                                <li>
                                    <a <?php if ($key == $sub_active_tab): ?>class="active"<?php endif; ?> href="<?php echo esc_url($this->get_subtabs_link($active_tab, $key)); ?>">
                                        <?php echo $value; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-settings-form">
                    <?php if ($sub_active_tab): ?>
                        <?php cmb2_metabox_form($sub_tabs_fields, $this->key); ?>
                    <?php else : ?>
                        <?php cmb2_metabox_form($tabs_fields, $this->key); ?>
                    <?php endif; ?>
                </div>
            </div>


        </div><!-- .wrap -->

        <?php
    }

    /**
     * Define General Settings Metabox and field configurations.
     *
     * Filters are provided for each settings section to allow add-ons and other plugins to add their own settings
     *
     * @param $active_tab active tab settings; null returns full array
     *
     * @return array
     */
    public function opalestate_settings($active_tab) {

        $pages = opalestate_cmb2_get_post_options([
            'post_type'   => 'page',
            'numberposts' => -1,
        ]);

        $general             = [];
        $opalestate_settings = [];

        //Return all settings array if necessary

        if ($active_tab === null) {
            return apply_filters('opalestate_registered_settings', $opalestate_settings);
        }

        $output = apply_filters('opalestate_registered_' . $active_tab . '_settings', isset($opalestate_settings[$active_tab]) ? $opalestate_settings[$active_tab] : []);

        if (empty($output)) {
            $class = "Opalestate_Settings_" . ucfirst($active_tab) . "_Tab";

            if (class_exists($class)) {
                $tab                  = new $class($this->key);
                $this->setting_object = $tab;
                $this->subtabs        = $tab->get_subtabs();

                return $tab->get_tab_content($this->key);
            }

            return [$active_tab => []];
        }

        // Add other tabs and settings fields as needed
        return $output;

    }


    /**
     * Show Settings Notices
     *
     * @param $object_id
     * @param $updated
     * @param $cmb
     */
    public function settings_notices($object_id, $updated, $cmb) {

        //Sanity check
        if ($object_id !== $this->key) {
            return;
        }

        if (did_action('cmb2_save_options-page_fields') === 1) {
            settings_errors('opalestate-notices');
        }

        add_settings_error('opalestate-notices', 'global-settings-updated', esc_html__('Settings updated.', 'opalestate-pro'), 'updated');

    }


    /**
     * Public getter method for retrieving protected/private variables
     *
     * @param string $field Field to retrieve
     *
     * @return mixed          Field value or exception is thrown
     * @since  1.0
     *
     */
    public function __get($field) {

        // Allowed fields to retrieve
        if (in_array($field, ['key', 'fields', 'opalestate_title', 'options_page'], true)) {
            return $this->{$field};
        }
        if ('option_metabox' === $field) {
            return $this->option_metabox();
        }

        throw new Exception('Invalid property: ' . $field);
    }
}
