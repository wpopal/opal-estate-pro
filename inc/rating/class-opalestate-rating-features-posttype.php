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
 * Class Opalestate_PostType_Rating_Features
 */
class Opalestate_PostType_Rating_Features {
    /**
     * Init action and filter data to define property post type
     */
    public function __construct() {
        add_action('init', [$this, 'definition']);
    }

    /**
     * Definition
     */
    public function definition() {
        if (!is_blog_installed()) {
            return;
        }

        $rating_supports = Opalestate_Rating::get_rating_supports();

        foreach ($rating_supports as $key => $support) {
            $this->register_post_type($support['features_cpt'], $support['post_type']);
        }
    }

    public function register_post_type($cpt_feature, $cpt_support) {
        if (post_type_exists($cpt_feature)) {
            return;
        }

        register_post_type($cpt_feature, apply_filters($cpt_feature . '_cpt_args', [
            'labels'              => [
                'name'                  => esc_html_x('Rating Features', 'Feature plural name', 'opalestate-pro'),
                'singular_name'         => esc_html_x('Rating Feature', 'Feature singular name', 'opalestate-pro'),
                'menu_name'             => esc_html_x('Rating Features', 'Admin menu name', 'opalestate-pro'),
                'add_new'               => esc_html__('Add rating feature', 'opalestate-pro'),
                'add_new_item'          => esc_html__('Add new rating feature', 'opalestate-pro'),
                'edit'                  => esc_html__('Edit', 'opalestate-pro'),
                'edit_item'             => esc_html__('Edit rating feature', 'opalestate-pro'),
                'new_item'              => esc_html__('New rating feature', 'opalestate-pro'),
                'view_item'             => esc_html__('View rating feature', 'opalestate-pro'),
                'search_items'          => esc_html__('Query rating features', 'opalestate-pro'),
                'not_found'             => esc_html__('No rating features found', 'opalestate-pro'),
                'not_found_in_trash'    => esc_html__('No rating features found in trash', 'opalestate-pro'),
                'parent'                => esc_html__('Parent rating features', 'opalestate-pro'),
                'filter_items_list'     => esc_html__('Filter rating features', 'opalestate-pro'),
                'items_list_navigation' => esc_html__('Rating Features navigation', 'opalestate-pro'),
                'items_list'            => esc_html__('Rating Features List', 'opalestate-pro'),
            ],
            'description'         => esc_html__('This is where store rating features are stored.', 'opalestate-pro'),
            'public'              => false,
            'hierarchical'        => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=' . $cpt_support,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'show_in_rest'        => true,
            'map_meta_cap'        => true,
            'supports'            => ['title'],
            'rewrite'             => false,
            'has_archive'         => false,
        ]));
    }
}

new Opalestate_PostType_Rating_Features();
