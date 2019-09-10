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
class Opalestate_Taxonomy_Neighborhood{

	/**
	 *
	 */
	public static function init(){
		add_action( 'init', array( $this, 'definition' ), 99 );
 
		add_action( 'cmb2_admin_init', array( $this, 'taxonomy_metaboxes' ), 9 );


	}

	/**
	 * Hook in and add a metabox to add fields to taxonomy terms
	 */
	public function taxonomy_metaboxes() {

		$prefix = 'opalestate_nb_';
		/**
		 * Metabox to add fields to categories and tags
		 */
		$cmb_term = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => esc_html__( 'Type Metabox', 'opalestate-pro' ), // Doesn't output for term boxes
			'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'       => array( 'opalestate_neighborhoods' ), // Tells CMB2 which taxonomies should have these fields
			// 'new_term_section' => true, // Will display in the "Add New Category" section
		) );
	
		$cmb_term->add_field( array(
			'name' 				=> esc_html__( 'Icon', 'opalestate-pro' ),
			'desc' 				=> esc_html__( 'This image will display in google map', 'opalestate-pro' ),
			'id'   				=> $prefix . 'icon',
			'type'              => 'file',
			'preview_size'		=>  'small',
			'options' => array(
				'url' => false, // Hide the text input for the url
			)
		) );
	}

	/**
	 *
	 */
	public function definition(){
		
		$labels = array(
			'name'              => esc_html__( 'Neighborhoods', 'opalestate-pro' ),
			'singular_name'     => esc_html__( 'Properties By Neighborhood', 'opalestate-pro' ),
			'search_items'      => esc_html__( 'Search Neighborhoods', 'opalestate-pro' ),
			'all_items'         => esc_html__( 'All Neighborhoods', 'opalestate-pro' ),
			'parent_item'       => esc_html__( 'Parent Neighborhood', 'opalestate-pro' ),
			'parent_item_colon' => esc_html__( 'Parent Neighborhood:', 'opalestate-pro' ),
			'edit_item'         => esc_html__( 'Edit Neighborhood', 'opalestate-pro' ),
			'update_item'       => esc_html__( 'Update Neighborhood', 'opalestate-pro' ),
			'add_new_item'      => esc_html__( 'Add New Neighborhood', 'opalestate-pro' ),
			'new_item_name'     => esc_html__( 'New Neighborhood', 'opalestate-pro' ),
			'menu_name'         => esc_html__( 'Neighborhoods', 'opalestate-pro' ),
		);

		register_taxonomy( 'opalestate_neighborhoods',  array( 'opalestate_property' ) , array(
			'labels'            => apply_filters( 'opalestate_taxomony_neighborhoods_labels', $labels ),
			'hierarchical'      => true,
			'query_var'         => 'property-neighborhood',
			'rewrite'           => array( 'slug' => esc_html__( 'property-neighborhood', 'opalestate-pro' ) ),
			'public'            => true,
			'show_ui'           => true,
		) );
	}

	public static function metaboxes(){

	}

	public static function dropdown_list( $selected=0 ){

		$id = "opalestate_neighborhoods".rand();
		
		$args = array( 
				'show_option_none' => esc_html__( 'Select Neighborhoods', 'opalestate-pro' ),
				'id' => $id,
				'class' => 'form-control',
				'show_count' => 0,
				'hierarchical'	=> '',
				'name'	=> 'neighborhoods',
				'selected'	=> $selected,
				'value_field'	=> 'slug',
				'taxonomy'	=> 'opalestate_neighborhoods'
		);		

		return wp_dropdown_categories( $args );
	}

}

new Opalestate_Taxonomy_Neighborhood();