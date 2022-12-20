<?php
/**
 * Admin Rating.
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

class Opalestate_Admin_Rating {
    /**
     * Opalestate_Admin_Rating constructor.
     */
    public function __construct() {
        add_action('cmb2_admin_init', [$this, 'comment_metaboxes']);
        add_action('cmb2_admin_init', [$this, 'feature_metaboxes']);
        add_filter('opalestate_settings_tabs', [$this, 'register_admin_setting_tab'], 1);
        add_filter('opalestate_registered_review_settings', [$this, 'register_admin_settings'], 10, 1);

        // Save Rating Meta Boxes.
        add_filter('wp_update_comment_data', 'Opalestate_Rating_MetaBox::save', 1);
    }

    public function comment_metaboxes() {
        $metabox = new Opalestate_Rating_MetaBox();

        return $metabox->register_admin_comment_fields();
    }

    public function feature_metaboxes() {
        $metabox = new Opalestate_Rating_MetaBox();

        return $metabox->register_admin_feature_fields();
    }

    public function register_admin_setting_tab($tabs) {
        $tabs['review'] = esc_html__('Review', 'opalestate-pro');

        return $tabs;
    }

    public function register_admin_settings($fields) {
        $fields = [
            'id'      => 'options_page_review',
            'title'   => esc_html__('Review Settings', 'opalestate-pro'),
            'show_on' => ['key' => 'options-page', 'value' => ['opalestate_settings'],],
            'fields'  => apply_filters('opalestate_settings_review', [
                    [
                        'name' => esc_html__('Review Settings', 'opalestate-pro'),
                        'desc' => '<hr>',
                        'id'   => 'opalestate_title_review_settings',
                        'type' => 'title',
                    ],
                    [
                        'name'    => esc_html__('Enable property reviews', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable property reviews', 'opalestate-pro'),
                        'id'      => 'enable_property_reviews',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable agency reviews', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable agency reviews', 'opalestate-pro'),
                        'id'      => 'enable_agency_reviews',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable agent reviews', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable agent reviews', 'opalestate-pro'),
                        'id'      => 'enable_agent_reviews',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                ]
            ),
        ];

        return $fields;
    }
}

new Opalestate_Admin_Rating();
