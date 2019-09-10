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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Opalestate_Taxonomy_Type {

	/**
	 *
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'definition' ] );
		add_action( 'cmb2_admin_init', [ $this, 'taxonomy_metaboxes' ] );
	}

	/**
	 * Hook in and add a metabox to add fields to taxonomy terms
	 */
	public function taxonomy_metaboxes() {

		$prefix = 'opalestate_type_';
		/**
		 * Metabox to add fields to categories and tags
		 */
		$cmb_term = new_cmb2_box( [
			'id'           => $prefix . 'edit',
			'title'        => esc_html__( 'Type Metabox', 'opalestate-pro' ), // Doesn't output for term boxes
			'object_types' => [ 'term' ], // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'   => [ 'opalestate_types' ], // Tells CMB2 which taxonomies should have these fields
			// 'new_term_section' => true, // Will display in the "Add New Category" section
		] );

		$cmb_term->add_field( [
			'name'         => esc_html__( 'Custom Icon Marker', 'opalestate-pro' ),
			'desc'         => esc_html__( 'This image will display in google map', 'opalestate-pro' ),
			'id'           => $prefix . 'iconmarker',
			'type'         => 'file',
			'preview_size' => 'small',
			'options'      => [
				'url' => false, // Hide the text input for the url
			],
		] );

		$cmb_term->add_field( [
			'name'         => esc_html__( 'Image', 'opalestate-pro' ),
			'desc'         => esc_html__( 'Type image', 'opalestate-pro' ),
			'id'           => $prefix . 'image',
			'type'         => 'file',
			'preview_size' => 'small',
			'options'      => [
				'url' => false, // Hide the text input for the url
			],
		] );
	}

	/**
	 *
	 */
	public function definition() {

		$labels = [
			'name'              => esc_html__( 'Types', 'opalestate-pro' ),
			'singular_name'     => esc_html__( 'Properties By Type', 'opalestate-pro' ),
			'search_items'      => esc_html__( 'Search Types', 'opalestate-pro' ),
			'all_items'         => esc_html__( 'All Types', 'opalestate-pro' ),
			'parent_item'       => esc_html__( 'Parent Type', 'opalestate-pro' ),
			'parent_item_colon' => esc_html__( 'Parent Type:', 'opalestate-pro' ),
			'edit_item'         => esc_html__( 'Edit Type', 'opalestate-pro' ),
			'update_item'       => esc_html__( 'Update Type', 'opalestate-pro' ),
			'add_new_item'      => esc_html__( 'Add New Type', 'opalestate-pro' ),
			'new_item_name'     => esc_html__( 'New Type', 'opalestate-pro' ),
			'menu_name'         => esc_html__( 'Types', 'opalestate-pro' ),
		];

		register_taxonomy( 'opalestate_types', [ 'opalestate_property' ], [
			'labels'       => apply_filters( 'opalestate_taxomony_types_labels', $labels ),
			'hierarchical' => true,
			'query_var'    => 'type',
			'rewrite'      => [ 'slug' => esc_html__( 'type', 'opalestate-pro' ) ],
			'public'       => true,
			'show_ui'      => true,
		] );
	}

	public function metaboxes() {

	}

	public static function get_list() {
		return get_terms( 'opalestate_types', [ 'hide_empty' => false ] );
	}

	public static function dropdown_list( $selected = 0 ) {

		$id = "opalestate_types" . rand();

		$args = [
			'show_option_none' => esc_html__( 'Select Type', 'opalestate-pro' ),
			'id'               => $id,
			'class'            => 'form-control',
			'show_count'       => 0,
			'hierarchical'     => '',
			'name'             => 'types',
			'selected'         => $selected,
			'value_field'      => 'slug',
			'taxonomy'         => 'opalestate_types',
			'echo'             => 0,
		];

		$label = '<label class="opalestate-label opalestate-label--type" for="' . esc_attr( $id ) . '">' . esc_html__( 'Type', 'opalestate-pro' ) . '</label>';

		echo $label . wp_dropdown_categories( $args );
	}

	public static function get_multi_check_list( $stypes ) {
		$list = self::get_list();

		echo opalestate_terms_multi_check( $list, $stypes );

	}
}

new Opalestate_Taxonomy_Type();
