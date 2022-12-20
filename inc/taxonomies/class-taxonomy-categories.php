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

class Opalestate_Taxonomy_Categories {
    /**
     * Constant.
     */
    const OPALESTATE_CATEGORY = 'property_category';

    /**
     * Init
     */
    public static function init() {
        add_action('init', [__CLASS__, 'definition']);
        add_filter('opalestate_taxomony_category_metaboxes', [__CLASS__, 'metaboxes']);
        add_action('cmb2_admin_init', [__CLASS__, 'taxonomy_metaboxes'], 999);
    }

    public static function metaboxes() {

    }

    /**
     * Definition.
     */
    public static function definition() {
        $labels = [
            'name'          => esc_html__('Categories', 'opalestate-pro'),
            'add_new_item'  => esc_html__('Add New Category', 'opalestate-pro'),
            'new_item_name' => esc_html__('New Category', 'opalestate-pro'),
        ];

        register_taxonomy(static::OPALESTATE_CATEGORY, 'opalestate_property', [
            'labels'       => apply_filters('opalestate_category_labels', $labels),
            'public'       => true,
            'hierarchical' => true,
            'show_ui'      => true,
            'query_var'    => true,
            'rewrite'      => ['slug' => _x('property-category', 'slug', 'opalestate-pro'), 'with_front' => false, 'hierarchical' => true],
        ]);
    }


    /**
     * Hook in and add a metabox to add fields to taxonomy terms
     */
    public static function taxonomy_metaboxes() {

        $prefix = 'opalestate_category_';

        /**
         * Metabox to add fields to categories and tags
         */
        $cmb_term = new_cmb2_box([
            'id'           => $prefix . 'edit',
            'title'        => esc_html__('Category Metabox', 'opalestate-pro'),
            'object_types' => ['term'],
            'taxonomies'   => [static::OPALESTATE_CATEGORY],
        ]);

        $cmb_term->add_field([
            'name' => esc_html__('Image', 'opalestate-pro'),
            'desc' => esc_html__('Category image', 'opalestate-pro'),
            'id'   => $prefix . 'image',
            'type' => 'file',
        ]);
    }

    /**
     * Gets list.
     *
     * @param array $args
     * @return array|int|\WP_Error
     */
    public static function get_list($args = []) {
        $default = [
            'taxonomy'   => static::OPALESTATE_CATEGORY,
            'hide_empty' => true,
        ];

        if ($args) {
            $default = array_merge($default, $args);
        }

        return get_terms($default);
    }

    public static function dropdown_list($selected = 0) {
        $id = static::OPALESTATE_CATEGORY . rand();

        $args = [
            'show_option_none' => esc_html__('Select Category', 'opalestate-pro'),
            'id'               => $id,
            'class'            => 'form-control',
            'show_count'       => 0,
            'hierarchical'     => '',
            'name'             => 'cat',
            'selected'         => $selected,
            'value_field'      => 'slug',
            'taxonomy'         => static::OPALESTATE_CATEGORY,
            'echo'             => 0,
        ];

        $label = '<label class="opalestate-label opalestate-label--category" for="' . esc_attr($id) . '">' . esc_html__('Category', 'opalestate-pro') . '</label>';

        echo $label . wp_dropdown_categories($args);
    }

    public static function get_multi_check_list($scategory = '') {
        $list = self::get_list();

        echo opalestate_categories_multi_check($list);
    }
}

Opalestate_Taxonomy_Categories::init();
