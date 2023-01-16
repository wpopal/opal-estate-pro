<?php
/**
 * Opalestate_Admin
 *
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

/**
 * @Class Wpopal_Core_Setup
 *
 * Entry point class to setup load all files and init working on frontend and process something logic in admin
 */
class Opalestate_Admin {
    /**
     * Opalestate_Admin constructor.
     */
    public function __construct() {
        add_action('init', [$this, 'setup']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * enqueue editor.js for edit mode
     */
    public function enqueue_scripts() {
        $screen    = get_current_screen();
        $screen_id = $screen ? $screen->id : '';

        wp_enqueue_style('opalestate-admin', OPALESTATE_PLUGIN_URL . 'assets/css/admin.css', [], '3.0.3');

        $suffix = '';
        wp_enqueue_style('select2', OPALESTATE_PLUGIN_URL . 'assets/3rd/select2/css/select2.min.css', null, '1.3');
        wp_enqueue_script('select2', OPALESTATE_PLUGIN_URL . 'assets/3rd/select2/js/select2.min.js', null, '1.3', true);

        wp_enqueue_script('opalestate-country-select', OPALESTATE_PLUGIN_URL . 'assets/js/country-select.js', ['jquery'], OPALESTATE_VERSION, true);
        wp_enqueue_script('opalestate-admin', OPALESTATE_PLUGIN_URL . 'assets/js/admin' . $suffix . '.js', ['jquery'], OPALESTATE_VERSION, true);
        wp_register_script('jquery-blockui', OPALESTATE_PLUGIN_URL . 'assets/3rd/jquery-blockui/jquery.blockUI' . $suffix . '.js', array('jquery'), '2.70', true);
        wp_register_script('opal-clipboard', OPALESTATE_PLUGIN_URL . 'assets/3rd/opal-clipboard.js', array('jquery'), OPALESTATE_VERSION, true);

        // API settings.
        if ('opalestate_property_page_opalestate-settings' === $screen_id && isset($_GET['tab']) && 'api_keys' == $_GET['tab']) {
            wp_register_script('opalestate-api-keys', OPALESTATE_PLUGIN_URL . 'assets/js/api-keys' . $suffix . '.js', array('jquery', 'opalestate-admin', 'underscore', 'backbone', 'wp-util', 'jquery-blockui', 'opal-clipboard'),
                OPALESTATE_VERSION, true);
            wp_enqueue_script('opalestate-api-keys');
            wp_localize_script(
                'opalestate-api-keys',
                'opalestate_admin_api_keys',
                array(
                    'ajax_url'         => admin_url('admin-ajax.php'),
                    'update_api_nonce' => wp_create_nonce('update-api-key'),
                    'clipboard_failed' => esc_html__('Copying to clipboard failed. Please press Ctrl/Cmd+C to copy.', 'opalestate-pro'),
                )
            );
        }
    }

    /**
     * Include all files from supported plugins.
     */
    public function setup() {
        $this->includes([
            'cron-jobs-functions.php',
            'agent/class-agent.php',
            'property/class-property.php',
            'agency/class-agency.php',
            'rating/class-rating.php',
            'class-user.php',
        ]);

        ///
        $this->includes([
            'settings/base.php',
            'settings/api_keys.php',
            'settings/email.php',
            'settings/3rd_party.php',
            'settings/searcharea.php',
            'settings/general.php',
            'settings/property.php',
            'settings/agencies.php',
            'settings/agents.php',
            'settings/pages.php',
        ]);

        // Get it started
        $Opalestate_Settings = new Opalestate_Plugin_Settings();
    }

    /**
     * Include list of collection files
     *
     * @var array $files
     */
    public function includes($files) {
        foreach ($files as $file) {
            $this->_include($file);
        }
    }

    /**
     * include single file if found
     *
     * @var string $file
     */
    private function _include($file = '') {
        $file = OPALESTATE_PLUGIN_DIR . 'inc/admin/' . $file;
        if (file_exists($file)) {
            include_once $file;
        }
    }
}

new Opalestate_Admin();
