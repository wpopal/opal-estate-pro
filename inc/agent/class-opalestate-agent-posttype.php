<?php
/**
 * Opalestate_PostType_Agent
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

class Opalestate_PostType_Agent {

    /**
     * Opalestate_PostType_Agent constructor.
     */
    public function __construct() {
        add_action('init', [__CLASS__, 'definition']);
    }

    /**
     * Register Post type and taxonomies
     */
    public static function definition() {
        if (!is_blog_installed() || post_type_exists('opalestate_agent')) {
            return;
        }

        $labels = [
            'name'               => esc_html__('Agents', 'opalestate-pro'),
            'singular_name'      => esc_html__('Property', 'opalestate-pro'),
            'add_new'            => esc_html__('Add New Agent', 'opalestate-pro'),
            'add_new_item'       => esc_html__('Add New Agent', 'opalestate-pro'),
            'edit_item'          => esc_html__('Edit Agent', 'opalestate-pro'),
            'new_item'           => esc_html__('New Agent', 'opalestate-pro'),
            'all_items'          => esc_html__('All Agents', 'opalestate-pro'),
            'view_item'          => esc_html__('View Agent', 'opalestate-pro'),
            'search_items'       => esc_html__('Search Agent', 'opalestate-pro'),
            'not_found'          => esc_html__('No Agents found', 'opalestate-pro'),
            'not_found_in_trash' => esc_html__('No Agents found in Trash', 'opalestate-pro'),
            'parent_item_colon'  => '',
            'menu_name'          => esc_html__('Agents', 'opalestate-pro'),
        ];

        $labels = apply_filters('opalestate_postype_agent_labels', $labels);

        register_post_type('opalestate_agent',
            apply_filters('opalestate_agent_post_type_parameters', [
                'labels'        => $labels,
                'supports'      => ['title', 'editor', 'thumbnail', 'comments', 'author', 'excerpt'],
                'public'        => true,
                'has_archive'   => true,
                'menu_position' => 51,
                'categories'    => [],
                'menu_icon'     => 'dashicons-groups',
                'rewrite'       => ['slug' => esc_html_x('agent', 'agent slug', 'opalestate-pro')],
            ])
        );

        static::register_taxonomies();
    }

    /**
     * Register Agency Agency Taxonomy
     */
    private static function register_taxonomies() {
        /// Register Agent Levels
        $labels = [
            'name'              => esc_html__('Agent Levels', 'opalestate-pro'),
            'singular_name'     => esc_html__('Level', 'opalestate-pro'),
            'search_items'      => esc_html__('Search Level', 'opalestate-pro'),
            'all_items'         => esc_html__('All Levels', 'opalestate-pro'),
            'parent_item'       => esc_html__('Parent Level', 'opalestate-pro'),
            'parent_item_colon' => esc_html__('Parent Level:', 'opalestate-pro'),
            'edit_item'         => esc_html__('Edit Level', 'opalestate-pro'),
            'update_item'       => esc_html__('Update Level', 'opalestate-pro'),
            'add_new_item'      => esc_html__('Add New Level', 'opalestate-pro'),
            'new_item_name'     => esc_html__('New Level Name', 'opalestate-pro'),
            'menu_name'         => esc_html__('Agent Levels', 'opalestate-pro'),
        ];
        ///
        register_taxonomy('opalestate_agent_level', ['opalestate_agent'],
            [
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'show_in_nav_menus' => true,
                'rewrite'           => [
                    'slug' => esc_html_x('agent-level', 'agent level slug', 'opalestate-pro'),
                ],
            ]);
    }
}

new Opalestate_PostType_Agent();
