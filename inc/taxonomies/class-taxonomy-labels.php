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

class Opalestate_Taxonomy_Label {
    /**
     * Constant.
     */
    const OPALESTATE_LABEL = 'opalestate_label';

    /**
     * Opalestate_Taxonomy_Label constructor.
     */
    public function __construct() {
        add_action('init', [$this, 'definition']);
        add_filter('opalestate_taxomony_label_metaboxes', [$this, 'metaboxes']);
        add_action('cmb2_admin_init', [$this, 'taxonomy_metaboxes'], 999);

        add_filter('get_opalestate_label', function ($term) {
            $term->meta = get_term_meta($term->term_id); // all metadata
            return $term;
        });
    }

    /**
     *
     */
    public function definition() {
        $labels = [
            'name'              => esc_html__('Label', 'opalestate-pro'),
            'singular_name'     => esc_html__('Properties By Label', 'opalestate-pro'),
            'search_items'      => esc_html__('Search Label', 'opalestate-pro'),
            'all_items'         => esc_html__('All Label', 'opalestate-pro'),
            'parent_item'       => esc_html__('Parent Label', 'opalestate-pro'),
            'parent_item_colon' => esc_html__('Parent Label:', 'opalestate-pro'),
            'edit_item'         => esc_html__('Edit Label', 'opalestate-pro'),
            'update_item'       => esc_html__('Update Label', 'opalestate-pro'),
            'add_new_item'      => esc_html__('Add New Label', 'opalestate-pro'),
            'new_item_name'     => esc_html__('New Label', 'opalestate-pro'),
            'menu_name'         => esc_html__('Label', 'opalestate-pro'),
        ];

        register_taxonomy(static::OPALESTATE_LABEL, 'opalestate_property', [
            'labels'       => apply_filters('opalestate_label_labels', $labels),
            'hierarchical' => true,
            'query_var'    => 'property-label',
            'rewrite'      => ['slug' => esc_html__('property-label', 'opalestate-pro')],
            'public'       => true,
            'show_ui'      => true,
        ]);
    }

    public function metaboxes() {

    }

    /**
     * Hook in and add a metabox to add fields to taxonomy terms
     */
    public function taxonomy_metaboxes() {

        $prefix = 'opalestate_label_';
        /**
         * Metabox to add fields to categories and tags
         */
        $cmb_term = new_cmb2_box([
            'id'           => $prefix . 'edit',
            'title'        => esc_html__('Category Metabox', 'opalestate-pro'),
            'object_types' => ['term'],
            'taxonomies'   => [static::OPALESTATE_LABEL],
        ]);
        $cmb_term->add_field([
            'name' => esc_html__('Background', 'opalestate-pro'),
            'desc' => esc_html__('Set background of label', 'opalestate-pro'),
            'id'   => $prefix . 'lb_bg',
            'type' => 'colorpicker',
        ]);
        $cmb_term->add_field([
            'name' => esc_html__('Color', 'opalestate-pro'),
            'desc' => esc_html__('Set color of text', 'opalestate-pro'),
            'id'   => $prefix . 'lb_color',
            'type' => 'colorpicker',
        ]);

        $cmb_term->add_field([
            'name'         => esc_html__('Image Logo', 'opalestate-pro'),
            'desc'         => esc_html__('Or Using Image Logo without using text', 'opalestate-pro'),
            'id'           => $prefix . 'lb_img',
            'type'         => 'file',
            'preview_size' => 'small',
            'options'      => [
                'url' => false, // Hide the text input for the url
            ],
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
            'taxonomy'   => static::OPALESTATE_LABEL,
            'hide_empty' => false,
        ];

        if ($args) {
            $default = array_merge($default, $args);
        }

        return get_terms($default);
    }

    /**
     * Render dopdown list.
     *
     * @param int $selected
     * @return string
     */
    public static function dropdown_list($selected = 0) {
        $id = static::OPALESTATE_LABEL . rand();

        $args = [
            'show_option_none' => esc_html__('Select Label', 'opalestate-pro'),
            'id'               => $id,
            'class'            => 'form-control',
            'show_count'       => 0,
            'hierarchical'     => '',
            'name'             => 'label',
            'value_field'      => 'slug',
            'selected'         => $selected,
            'taxonomy'         => static::OPALESTATE_LABEL,
        ];

        return wp_dropdown_categories($args);
    }
}

new Opalestate_Taxonomy_Label();
