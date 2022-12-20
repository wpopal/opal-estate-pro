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

class Opalestate_Social_Login {
    public function __construct() {
        add_action('opalestate_member_after_login_form', [$this, 'login_template']);
        add_action('opalelementor_after_render_login_form', [$this, 'login_template']);

        if (is_admin()) {
            add_filter('opalestate_settings_3rd_party_subtabs_nav', [$this, 'register_admin_setting_tab'], 1);
            add_filter('opalestate_settings_3rd_party_subtabs_social_login_fields', [$this, 'register_admin_settings'], 10, 1);
        }

        $this->inludes();
        $this->process();
    }

    public function inludes() {
        require_once 'class-opalestate-facebook-login.php';
        require_once 'class-opalestate-google-login.php';
    }

    public function process() {
        new Opalestate_Facebook_Login();
        new Opalestate_Google_Login();
    }

    public function login_template() {
        echo opalestate_load_template_path('user/social-login/social-login');
    }

    public function register_admin_setting_tab($tabs) {
        $tabs['social_login'] = esc_html__('Social Login', 'opalestate-pro');

        return $tabs;
    }

    public function register_admin_settings($fields) {
        $fields = apply_filters('opalestate_settings_review', [
            [
                'name'      => esc_html__('Google', 'opalestate-pro'),
                'desc'      => '',
                'type'      => 'opalestate_title',
                'id'        => 'opalestate_title_general_settings_google',
                'after_row' => '<hr>',
            ],
            [
                'name'    => esc_html__('Enable Google login', 'opalestate-pro'),
                'desc'    => esc_html__('Enable Google login', 'opalestate-pro'),
                'id'      => 'enable_google_login',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Enable', 'opalestate-pro'),
                    'off' => esc_html__('Disable', 'opalestate-pro'),
                ],
                'default' => 'off',
            ],
            [
                'name' => esc_html__('Google Client ID', 'opalestate-pro'),
                'desc' => esc_html__('Google Client ID is required for Google Login.', 'opalestate-pro'),
                'id'   => 'google_client_id',
                'type' => 'text',
            ],
            [
                'name' => esc_html__('Google Client Secret', 'opalestate-pro'),
                'desc' => esc_html__('Google Client Secret is required for Google Login.', 'opalestate-pro'),
                'id'   => 'google_client_secret',
                'type' => 'text',
            ],
            [
                'name' => esc_html__('Google API key', 'opalestate-pro'),
                'desc' => esc_html__('Google API key is required for Google Login.', 'opalestate-pro'),
                'id'   => 'google_api_key',
                'type' => 'text',
            ],
            [
                'name' => sprintf(__('Redirect URL: <code>%s</code>', 'opalestate-pro'), add_query_arg('opal_google_login', '1', trailingslashit(get_home_url()))),
                'desc' => esc_html__('You need to add this URL when you create API keys.', 'opalestate-pro'),
                'id'   => 'google_api_redirect_url',
                'type' => 'title',
            ],
            [
                'name'       => esc_html__('Facebook', 'opalestate-pro'),
                'desc'       => '',
                'type'       => 'opalestate_title',
                'id'         => 'opalestate_title_general_settings_facebook',
                'before_row' => '<hr>',
                'after_row'  => '<hr>',
            ],
            [
                'name'    => esc_html__('Enable Facebook login', 'opalestate-pro'),
                'desc'    => esc_html__('Enable Facebook login', 'opalestate-pro'),
                'id'      => 'enable_facebook_login',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Enable', 'opalestate-pro'),
                    'off' => esc_html__('Disable', 'opalestate-pro'),
                ],
                'default' => 'off',
            ],
            [
                'name' => esc_html__('Facebook Application ID', 'opalestate-pro'),
                'desc' => esc_html__('Facebook Application ID is required for Facebook login.', 'opalestate-pro'),
                'id'   => 'facebook_app_id',
                'type' => 'text',
            ],
            [
                'name' => esc_html__('Facebook Secret', 'opalestate-pro'),
                'desc' => esc_html__('Facebook Secret is required for Facebook login.', 'opalestate-pro'),
                'id'   => 'facebook_secret',
                'type' => 'text',
            ],
            [
                'name'      => sprintf(__('Redirect URL: <code>%s</code>', 'opalestate-pro'), add_query_arg('opal_facebook_login', '1', trailingslashit(get_home_url()))),
                'desc'      => esc_html__('You need to add this URL when you create API keys.', 'opalestate-pro'),
                'id'        => 'facebook_api_redirect_url',
                'type'      => 'title',
                'after_row' => '<hr>',
            ],
        ]);

        return $fields;
    }

    /**
     * Gets redirect URL.
     *
     * @return mixed|void
     */
    public static function get_redirect_url() {
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] != '') {
            $redirect = get_permalink(sanitize_text_field($_GET['redirect_to']));
        } else {
            $redirect = esc_url(home_url('/'));
        }

        return apply_filters('opal_social_login_redirect_to', $redirect);
    }
}

new Opalestate_Social_Login();
