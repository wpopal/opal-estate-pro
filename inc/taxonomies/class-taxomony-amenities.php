<?php
/**
 * Opalestate_Taxonomy_Amenities
 *
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

class Opalestate_Taxonomy_Amenities {

	/**
	 * Opalestate_Taxonomy_Amenities constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'definition' ] );
		add_action( 'cmb2_admin_init', [ $this, 'taxonomy_metaboxes' ], 999 );
	}

	/**
	 *
	 */
	public function definition() {

		$labels = [
			'name'              => esc_html__( 'Amenities', 'opalestate-pro' ),
			'singular_name'     => esc_html__( 'Properties By Amenity', 'opalestate-pro' ),
			'search_items'      => esc_html__( 'Search Amenities', 'opalestate-pro' ),
			'all_items'         => esc_html__( 'All Amenities', 'opalestate-pro' ),
			'parent_item'       => esc_html__( 'Parent Amenity', 'opalestate-pro' ),
			'parent_item_colon' => esc_html__( 'Parent Amenity:', 'opalestate-pro' ),
			'edit_item'         => esc_html__( 'Edit Amenity', 'opalestate-pro' ),
			'update_item'       => esc_html__( 'Update Amenity', 'opalestate-pro' ),
			'add_new_item'      => esc_html__( 'Add New Amenity', 'opalestate-pro' ),
			'new_item_name'     => esc_html__( 'New Amenity', 'opalestate-pro' ),
			'menu_name'         => esc_html__( 'Amenities', 'opalestate-pro' ),
		];

		register_taxonomy( 'opalestate_amenities', 'opalestate_property', [
			'labels'       => apply_filters( 'opalestate_taxomony_amenities_labels', $labels ),
			'hierarchical' => true,
			'query_var'    => 'amenity',
			'rewrite'      => [ 'slug' => _x( 'amenity', 'slug', 'opalestate-pro' ), 'with_front' => false, 'hierarchical' => true ],
			'public'       => true,
			'show_ui'      => true,
		] );
	}

	public static function get_list() {
		return get_terms( 'opalestate_amenities', [ 'hide_empty' => false ] );
	}

	public function taxonomy_metaboxes() {
		$prefix = 'opalestate_amt_';

		$cmb_term = new_cmb2_box( [
			'id'           => $prefix . 'edit',
			'title'        => esc_html__( 'Type Metabox', 'opalestate-pro' ),
			'object_types' => [ 'term' ],
			'taxonomies'   => [ 'opalestate_amenities' ],
		] );

		$cmb_term->add_field( [
			'name'         => esc_html__( 'Icon', 'opalestate-pro' ),
			'desc'         => esc_html__( 'Select an icon.', 'opalestate-pro' ),
			'id'           => $prefix . 'icon',
			'type'         => 'opal_iconpicker',
		] );

		$cmb_term->add_field( [
			'name'         => esc_html__( 'Image Icon', 'opalestate-pro' ),
			'desc'         => esc_html__( 'Select an image icon (SVG, PNG or JPEG).', 'opalestate-pro' ),
			'id'           => $prefix . 'image',
			'type'         => 'file',
			'preview_size' => [ 50, 50 ],
			'options'      => [
				'url' => false, // Hide the text input for the url
			],
			'query_args'   => [
				'type' => [
					'image/gif',
					'image/jpeg',
					'image/png',
				],
			],
		] );
	}
}

new Opalestate_Taxonomy_Amenities();
