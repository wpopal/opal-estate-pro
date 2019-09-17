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

class Opalestate_Taxonomy_Categories {

	/**
	 *
	 */
	public static function init() {

		add_action( 'init', [ __CLASS__, 'definition' ] );
		add_filter( 'opalestate_taxomony_category_metaboxes', [ __CLASS__, 'metaboxes' ] );

		add_action( 'cmb2_admin_init', [ __CLASS__, 'taxonomy_metaboxes' ], 999 );

	}

	public static function metaboxes() {

	}


	/**
	 *
	 */
	public static function definition() {

		register_taxonomy( 'property_category', 'opalestate_property', apply_filters( 'opalestate_taxonomy_args_property_category', [
			'labels'       => [
				'name'          => esc_html__( 'Categories', 'opalestate-pro' ),
				'add_new_item'  => esc_html__( 'Add New Category', 'opalestate-pro' ),
				'new_item_name' => esc_html__( 'New Category', 'opalestate-pro' ),
			],
			'public'       => true,
			'hierarchical' => true,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => _x( 'property-category', 'slug', 'opalestate-pro' ), 'with_front' => false, 'hierarchical' => true ],
		] ) );
	}


	/**
	 * Hook in and add a metabox to add fields to taxonomy terms
	 */
	public static function taxonomy_metaboxes() {

		$prefix = 'opalestate_category_';
		/**
		 * Metabox to add fields to categories and tags
		 */
		$cmb_term = new_cmb2_box( [
			'id'           => $prefix . 'edit',
			'title'        => esc_html__( 'Category Metabox', 'opalestate-pro' ), // Doesn't output for term boxes
			'object_types' => [ 'term' ], // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'   => [ 'property_category' ], // Tells CMB2 which taxonomies should have these fields
			// 'new_term_section' => true, // Will display in the "Add New Category" section
		] );

		$cmb_term->add_field( [
			'name' => esc_html__( 'Image', 'opalestate-pro' ),
			'desc' => esc_html__( 'Category image', 'opalestate-pro' ),
			'id'   => $prefix . 'image',
			'type' => 'file',
		] );
	}

	public static function get_list( $args = [] ) {
		$default = [
			'taxonomy'   => 'property_category',
			'hide_empty' => true,
		];

		if ( $args ) {
			$default = array_merge( $default, $args );
		}

		return get_terms( $default );
	}

	public static function dropdown_list( $selected = 0 ) {

		$id = 'property_category' . rand();

		$args = [
			'show_option_none' => esc_html__( 'Select Category', 'opalestate-pro' ),
			'id'               => $id,
			'class'            => 'form-control',
			'show_count'       => 0,
			'hierarchical'     => '',
			'name'             => 'types',
			'selected'         => $selected,
			'value_field'      => 'slug',
			'taxonomy'         => 'property_category',
			'echo'             => 0,
		];

		$label = '<label class="opalestate-label opalestate-label--category" for="' . esc_attr( $id ) . '">' . esc_html__( 'Category', 'opalestate-pro' ) . '</label>';

		echo $label . wp_dropdown_categories( $args );
	}

	public static function get_multi_check_list( $scategory ) {
		$list = self::get_list();

		echo opalestate_terms_multi_check( $list, $scategory );
	}
}

Opalestate_Taxonomy_Categories::init();
