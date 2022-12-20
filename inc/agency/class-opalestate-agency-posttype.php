<?php
/**
 * Opalestate_PostType_Agency
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Opalestate_PostType_Agency
 *
 * @version 1.0
 */
class Opalestate_PostType_Agency {

    /**
     * Init
     */
    public static function init() {
        add_action('init', [__CLASS__, 'definition']);
    }


    /**
     * Register Post type and taxonomies
     */
    public static function definition() {
        if (!is_blog_installed() || post_type_exists('opalestate_agency')) {
            return;
        }

        $labels = [
            'name'               => esc_html__('Agencies', 'opalestate-pro'),
            'singular_name'      => esc_html__('Property', 'opalestate-pro'),
            'add_new'            => esc_html__('Add New Agency', 'opalestate-pro'),
            'add_new_item'       => esc_html__('Add New Agency', 'opalestate-pro'),
            'edit_item'          => esc_html__('Edit Agency', 'opalestate-pro'),
            'new_item'           => esc_html__('New Agency', 'opalestate-pro'),
            'all_items'          => esc_html__('All Agencies', 'opalestate-pro'),
            'view_item'          => esc_html__('View Agency', 'opalestate-pro'),
            'search_items'       => esc_html__('Search Agency', 'opalestate-pro'),
            'not_found'          => esc_html__('No Agencies found', 'opalestate-pro'),
            'not_found_in_trash' => esc_html__('No Agencies found in Trash', 'opalestate-pro'),
            'parent_item_colon'  => '',
            'menu_name'          => esc_html__('Agencies', 'opalestate-pro'),
        ];

        $labels = apply_filters('opalestate_postype_agency_labels', $labels);

        register_post_type('opalestate_agency',
            apply_filters('opalestate_agency_post_type_parameters', [
                'labels'        => $labels,
                'supports'      => ['title', 'editor', 'thumbnail', 'comments', 'author', 'excerpt'],
                'public'        => true,
                'has_archive'   => true,
                'menu_position' => 51,
                'categories'    => [],
                'menu_icon'     => 'dashicons-groups',
                'rewrite'       => ['slug' => esc_html_x('agency', 'agency slug', 'opalestate-pro')],
            ])
        );

        ///
        $labels = [
            'name'              => esc_html__('Agency Categories', 'opalestate-pro'),
            'singular_name'     => esc_html__('Category', 'opalestate-pro'),
            'search_items'      => esc_html__('Search Category', 'opalestate-pro'),
            'all_items'         => esc_html__('All Categories', 'opalestate-pro'),
            'parent_item'       => esc_html__('Parent Category', 'opalestate-pro'),
            'parent_item_colon' => esc_html__('Parent Category:', 'opalestate-pro'),
            'edit_item'         => esc_html__('Edit Category', 'opalestate-pro'),
            'update_item'       => esc_html__('Update Category', 'opalestate-pro'),
            'add_new_item'      => esc_html__('Add New Category', 'opalestate-pro'),
            'new_item_name'     => esc_html__('New Category Name', 'opalestate-pro'),
            'menu_name'         => esc_html__('Agency Categories', 'opalestate-pro'),
        ];
        ///
        register_taxonomy('opalestate_agency_cat', ['opalestate_agency'],
            [
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'show_in_nav_menus' => true,
                'rewrite'           => [
                    'slug' => esc_html_x('agency-category', 'agency category slug', 'opalestate-pro'),
                ],
            ]);
    }
}

Opalestate_PostType_Agency::init();
