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

class Opalestate_Settings_Pages_Tab extends Opalestate_Settings_Base_Tab {

    public function get_tabnav() {

    }

    public function get_tab_content($key = '') {
        return [
            'id'               => 'options_page',
            'opalestate_title' => esc_html__('Pages', 'opalestate-pro'),
            'show_on'          => ['key' => 'options-page', 'value' => [$key,],],
            'fields'           => $this->get_tab_fields(),
        ];
    }

    public function get_tab_fields($key = '') {
        $pages = opalestate_cmb2_get_post_options([
            'post_type'   => 'page',
            'numberposts' => -1,
        ]);

        return apply_filters('opalestate_settings_pages', [
                [
                    'name' => esc_html__('Pages Settings', 'opalestate-pro'),

                    'type'       => 'opalestate_title',
                    'id'         => 'opalestate_title_pages_settings',
                    'before_row' => '<hr>',
                    'after_row'  => '<hr>',
                ],
                [
                    'name'    => esc_html__('User Management Page', 'opalestate-pro'),
                    'desc'    => esc_html__('This is page use User Management Page using for show content of management page such as profile, my properties', 'opalestate-pro'),
                    'id'      => 'user_management_page',
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'         => esc_html__('Dashboard Logo', 'opalestate-pro'),
                    'desc'         => esc_html__('Upload a logo for user dashboard page.', 'opalestate-pro'),
                    'id'           => 'dashboard_logo',
                    'type'         => 'file',
                    'preview_size' => [100, 100],
                    'options'      => [
                        'url' => false,
                    ],
                    'query_args'   => [
                        'type' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                        ],
                    ],
                ],
                [
                    'name'    => esc_html__('My Account Page', 'opalestate-pro'),
                    'desc'    => esc_html__('This is page used for login and register an account, or reset password.', 'opalestate-pro'),
                    'id'      => 'user_myaccount_page',
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => esc_html__('Terms and Conditions Page', 'opalestate-pro'),
                    'desc'    => esc_html__('This is page used for terms and conditions.', 'opalestate-pro'),
                    'id'      => 'user_terms_page',
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name' => esc_html__('Dashboard Settings', 'opalestate-pro'),

                    'type'       => 'opalestate_title',
                    'id'         => 'opalestate_title_pages_dashboard',
                    'desc'       => esc_html__('Settings for User Management Dashboard.', 'opalestate-pro'),
                    'before_row' => '<hr>',
                    'after_row'  => '<hr>',
                ],
                [
                    'name'    => esc_html__('Show Profile', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Profile menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_profile',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Show Agent Profile', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Agent Profile menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_agent_profile',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Show Agency Profile', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Agency Profile menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_agency_profile',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Show Favorite', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Favorite menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_favorite',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Show Reviews', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Reviews menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_reviews',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Enable Message Database', 'opalestate-pro'),
                    'desc'    => esc_html__('Allow User send message Contact/Equire via email and saved into database to exchange theirs message direct in User Message Management',
                        'opalestate-pro'),
                    'id'      => 'message_log',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'off',
                ],
                [
                    'name'    => esc_html__('Show Submission', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Submission menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_submission',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Show Properties', 'opalestate-pro'),
                    'desc'    => esc_html__('Show My Properties menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_properties',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
                [
                    'name'    => esc_html__('Show Saved Search', 'opalestate-pro'),
                    'desc'    => esc_html__('Show Saved Search menu page.', 'opalestate-pro'),
                    'id'      => 'enable_dashboard_savedsearch',
                    'type'    => 'switch',
                    'options' => [
                        'on'  => esc_html__('Enable', 'opalestate-pro'),
                        'off' => esc_html__('Disable', 'opalestate-pro'),
                    ],
                    'default' => 'on',
                ],
            ]
        );
    }
}
