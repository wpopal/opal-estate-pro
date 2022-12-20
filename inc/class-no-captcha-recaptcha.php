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

/**
 * @class   OpalEstate_Nocaptcha_Recaptcha
 *
 * @version 1.0
 */
class OpalEstate_Nocaptcha_Recaptcha {
    /**
     * OpalEstate_Nocaptcha_Recaptcha constructor.
     */
    public function __construct() {
        if (is_admin()) {

            add_filter('opalestate_settings_3rd_party_subtabs_nav', [$this, 'admin_tab_setting']);

            add_filter('opalestate_settings_3rd_party_subtabs_google_captcha_page_fields', [$this, 'admin_content_setting']);
        }

        if (opalestate_options('show_captcha') == 'on') {
            define('WPOPAL_CAPTCHA_LOAED', true);
            $this->theme = opalestate_options('captcha_theme', 'light');
            add_action('wp_head', [$this, 'add_custom_styles']);

            add_action('opalestate_message_form_after', [__CLASS__, 'show_captcha']);
            add_action('opalestate_process_send_email_before', [__CLASS__, 'ajax_verify_captcha']);
        }
    }

    /**
     *
     */
    public function add_custom_styles() {
        $lang = null;
        echo '<script src="https://www.google.com/recaptcha/api.js?render=reCAPTCHA_' . opalestate_options('site_key') . '" async defer></script>' . "\r\n";
    }

    /**
     *
     */
    public static function show_captcha() {

        if (isset($_GET['captcha']) && $_GET['captcha'] == 'failed') {

        }
        echo '<div style="transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;" class="g-recaptcha" data-sitekey="' . opalestate_options('site_key') . '" data-theme="' . opalestate_options('captcha_theme',
                'light') . '"></div>';
    }

    /**
     *
     */
    public static function ajax_verify_captcha() {
        $response = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';

        $remote_ip = $_SERVER["REMOTE_ADDR"];

        // make a GET request to the Google reCAPTCHA Server
        $request = wp_remote_get(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . opalestate_options('secret_key') . '&response=' . $response . '&remoteip=' . $remote_ip
        );

        // get the request response body
        $response_body = wp_remote_retrieve_body($request);

        $result = json_decode($response_body, true);

        if (isset($result['hostname']) && !empty($result['hostname']) && empty($result['success'])) {
            $result['success'] = 1;
        }
        if (!$result['success']) {
            $return = ['status' => false, 'msg' => esc_html__('The captcha is not verified, please try again!', 'opalestate-pro')];
            echo json_encode($return);
            die();
        }
    }

    /**
     *
     */
    public function admin_content_setting($fields) {
        $fields = apply_filters('opalestate_settings_google_captcha', [

            [
                'name'    => esc_html__('Show Captcha In Form', 'opalestate-pro'),
                'desc'    => __('Enable google captch in contact , register form. After Set yes, you change setting in Google Captcha Tab. Register here:<a href="https://www.google.com/recaptcha/admin" target="_blank"> https://www.google.com/recaptcha/admin</a> Version 2',
                    'opalestate-pro'),
                'id'      => 'show_captcha',
                'type'    => 'switch',
                'options' => [
                    'off' => esc_html__('No', 'opalestate-pro'),
                    'on'  => esc_html__('Yes', 'opalestate-pro'),
                ],
                'default' => 'on',
            ],

            [
                'name' => esc_html__('Google Captcha page Settings', 'opalestate-pro'),
                'desc' => '<hr>',
                'id'   => 'opalestate_title_google_captcha_settings',
                'type' => 'title',
            ],

            [
                'name' => esc_html__('Site Key', 'opalestate-pro'),
                'desc' => esc_html__('Used for displaying the CAPTCHA.', 'opalestate-pro'),
                'id'   => 'site_key',
                'type' => 'text',
            ],

            [
                'name' => esc_html__('Secret key', 'opalestate-pro'),
                'desc' => esc_html__('Used for communication between your site and Google. Grab it.', 'opalestate-pro'),
                'id'   => 'secret_key',
                'type' => 'text',
            ],

            [
                'name'    => esc_html__('Theme', 'opalestate-pro'),
                'desc'    => esc_html__('Display captcha box with color style.', 'opalestate-pro'),
                'id'      => 'captcha_theme',
                'type'    => 'select',
                'options' => [
                    'light' => esc_html__('Light', 'opalestate-pro'),
                    'dark'  => esc_html__('Dark', 'opalestate-pro'),
                ],
            ],

        ]);

        return $fields;
    }

    /**
     *
     */
    public function admin_tab_setting($tabs) {
        $tabs['google_captcha_page'] = esc_html__('Google Captcha', 'opalestate-pro');

        return $tabs;
    }
}

new OpalEstate_Nocaptcha_Recaptcha();
