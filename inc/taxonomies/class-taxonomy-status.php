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

class Opalestate_Taxonomy_Status {

	/**
	 * Opalestate_Taxonomy_Status constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'definition' ] );
		add_filter( 'opalestate_taxomony_status_metaboxes', [ $this, 'metaboxes' ] );
		add_action( 'cmb2_admin_init', [ $this, 'taxonomy_metaboxes' ] );
	}

	/**
	 *
	 */
	public function definition() {
		$labels = [
			'name'              => esc_html__( 'Status', 'opalestate-pro' ),
			'singular_name'     => esc_html__( 'Properties By Status', 'opalestate-pro' ),
			'search_items'      => esc_html__( 'Search Status', 'opalestate-pro' ),
			'all_items'         => esc_html__( 'All Status', 'opalestate-pro' ),
			'parent_item'       => esc_html__( 'Parent Status', 'opalestate-pro' ),
			'parent_item_colon' => esc_html__( 'Parent Status:', 'opalestate-pro' ),
			'edit_item'         => esc_html__( 'Edit Status', 'opalestate-pro' ),
			'update_item'       => esc_html__( 'Update Status', 'opalestate-pro' ),
			'add_new_item'      => esc_html__( 'Add New Status', 'opalestate-pro' ),
			'new_item_name'     => esc_html__( 'New Status', 'opalestate-pro' ),
			'menu_name'         => esc_html__( 'Status', 'opalestate-pro' ),
		];
		register_taxonomy( 'opalestate_status', 'opalestate_property', [
			'labels'       => apply_filters( 'opalestate_status_labels', $labels ),
			'hierarchical' => true,
			'query_var'    => 'status',
			'rewrite'      => [ 'slug' => esc_html__( 'status', 'opalestate-pro' ) ],
			'public'       => true,
			'show_ui'      => true,
		] );
	}


	/**
	 * Hook in and add a metabox to add fields to taxonomy terms
	 */
	public function taxonomy_metaboxes() {

		$prefix = 'opalestate_status_';
		/**
		 * Metabox to add fields to categories and tags
		 */
		$cmb_term = new_cmb2_box( [
			'id'           => $prefix . 'edit',
			'title'        => esc_html__( 'Category Metabox', 'opalestate-pro' ), // Doesn't output for term boxes
			'object_types' => [ 'term' ], // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'   => [ 'opalestate_status' ], // Tells CMB2 which taxonomies should have these fields
			// 'new_term_section' => true, // Will display in the "Add New Category" section
		] );
		$cmb_term->add_field( [
			'name' => esc_html__( 'Background', 'opalestate-pro' ),
			'desc' => esc_html__( 'Set background of label', 'opalestate-pro' ),
			'id'   => $prefix . 'lb_bg',
			'type' => 'colorpicker',
		] );
		$cmb_term->add_field( [
			'name' => esc_html__( 'Color', 'opalestate-pro' ),
			'desc' => esc_html__( 'Set background of text', 'opalestate-pro' ),
			'id'   => $prefix . 'lb_color',
			'type' => 'colorpicker',
		] );
		$cmb_term->add_field( [
			'name'       => esc_html__( 'Order', 'opalestate-pro' ),
			'desc'       => esc_html__( 'Set a priority to display', 'opalestate-pro' ),
			'id'         => $prefix . 'order',
			'type'       => 'text_small',
			'attributes' => [
				'type' => 'number',
			],
			'default'    => 0,
		] );
	}

	public function metaboxes() {

	}

	/**
	 * Gets list.
	 *
	 * @param array $args
	 * @return array|int|\WP_Error
	 */
	public static function get_list( $args = [] ) {
		$default = apply_filters( 'opalestate_status_args', [
			'taxonomy'     => 'opalestate_status',
			'hide_empty'   => false,
			'hierarchical' => false,
			'parent'       => 0,
			'order'        => 'ASC',
			'orderby'      => 'meta_value_num',
			'meta_query'   => [
				[
					'key'  => 'opalestate_status_order',
					'type' => 'NUMERIC',
				],
			],
		] );

		if ( $args ) {
			$default = array_merge( $default, $args );
		}

		return get_terms( $default );
	}

	/**
	 * Render dopdown list.
	 *
	 * @param int $selected
	 * @return string
	 */
	public static function dropdown_list( $selected = 0 ) {
		$id = 'palestate_status' . rand();

		$args = [
			'show_option_none' => esc_html__( 'Select Status', 'opalestate-pro' ),
			'id'               => $id,
			'class'            => 'form-control',
			'show_count'       => 0,
			'hierarchical'     => '',
			'name'             => 'status',
			'value_field'      => 'slug',
			'selected'         => $selected,
			'taxonomy'         => 'opalestate_status',
			'echo'             => 0,
		];

		$label = '<label class="opalestate-label opalestate-label--status" for="' . esc_attr( $id ) . '">' . esc_html__( 'Status', 'opalestate-pro' ) . '</label>';

		echo $label . wp_dropdown_categories( $args );
	}
}

new Opalestate_Taxonomy_Status();
