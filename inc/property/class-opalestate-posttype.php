<?php
/**
 * Opalestate_PostType_Property
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
 * Class Opalestate_PostType_Agency
 *
 * @version 1.0
 */
class Opalestate_PostType_Property {

    /**
     * Opalestate_PostType_Property constructor.
     */
    public function __construct() {
        add_action('init', [__CLASS__, 'definition']);
        add_action('init', [__CLASS__, 'register_post_status']);
    }

    /**
     * Register Post type and taxonomies
     */
    public static function definition() {
        if (!is_blog_installed() || post_type_exists('opalestate_property')) {
            return;
        }

        $labels = [
            'name'               => esc_html__('Properties', 'opalestate-pro'),
            'singular_name'      => esc_html__('Property', 'opalestate-pro'),
            'add_new'            => esc_html__('Add New Property', 'opalestate-pro'),
            'add_new_item'       => esc_html__('Add New Property', 'opalestate-pro'),
            'edit_item'          => esc_html__('Edit Property', 'opalestate-pro'),
            'new_item'           => esc_html__('New Property', 'opalestate-pro'),
            'all_items'          => esc_html__('All Properties', 'opalestate-pro'),
            'view_item'          => esc_html__('View Property', 'opalestate-pro'),
            'search_items'       => esc_html__('Search Property', 'opalestate-pro'),
            'not_found'          => esc_html__('No Properties found', 'opalestate-pro'),
            'not_found_in_trash' => esc_html__('No Properties found in Trash', 'opalestate-pro'),
            'parent_item_colon'  => '',
            'menu_name'          => esc_html__('Properties', 'opalestate-pro'),
        ];

        $labels = apply_filters('opalestate_postype_property_labels', $labels);

        register_post_type('opalestate_property',
            apply_filters('opalestate_property_post_type_parameters', [
                'labels'              => $labels,
                'supports'            => ['title', 'editor', 'thumbnail', 'comments', 'author'],
                'public'              => true,
                'has_archive'         => true,
                'menu_position'       => 51,
                'categories'          => [],
                'menu_icon'           => 'dashicons-admin-home',
                'map_meta_cap'        => true,
                'publicly_queryable'  => true,
                'exclude_from_search' => false,
                'query_var'           => true,
                'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
                'show_in_nav_menus'   => true,
                'rewrite'             => ['slug' => esc_html_x('property', 'property slug', 'opalestate-pro')],
            ])
        );
    }

    /**
     * Register post status.
     */
    public static function register_post_status() {
        register_post_status('expired', [
            'label'                     => _x('Expired', 'Expired status', 'opalestate-pro'),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            /* translators: %s: number of orders */
            'label_count'               => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'opalestate-pro'),
        ]);
    }
}

new Opalestate_PostType_Property();
