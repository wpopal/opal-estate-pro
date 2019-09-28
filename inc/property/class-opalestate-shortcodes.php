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

/**
 * @class   OpalEstate_Shortcodes
 *
 * @version 1.0
 */
class OpalEstate_Shortcodes {

	/**
	 * Static $shortcodes
	 */
	public $shortcodes;

	/**
	 * defined list of shortcode and functions of this for each.
	 */
	public function __construct() {

		$this->shortcodes = [

			'search_properties_form'   => [ 'code' => 'search_properties_form', 'label' => esc_html__( 'Search Properties Form', 'opalestate-pro' ) ],
			'properties'               => [ 'code' => 'properties', 'label' => esc_html__( 'Properties', 'opalestate-pro' ) ],
			'search_properties_result' => [ 'code' => 'search_properties_result', 'label' => esc_html__( 'Search Properties Result', 'opalestate-pro' ) ],
			'search_properties'        => [ 'code' => 'search_properties', 'label' => esc_html__( 'Search Properties', 'opalestate-pro' ) ],
			'search_properties_v'      => [ 'code' => 'search_properties_v', 'label' => esc_html__( 'Search Properties Vertical', 'opalestate-pro' ) ],

			'search_map_properties' => [ 'code' => 'search_map_properties', 'label' => esc_html__( 'Search Map Properties', 'opalestate-pro' ) ],
			'ajax_map_search'       => [ 'code' => 'ajax_map_search', 'label' => esc_html__( 'Ajax Search Map Properties', 'opalestate-pro' ) ],
			'ajax_map_quick_search' => [ 'code' => 'ajax_map_quick_search', 'label' => esc_html__( 'Ajax Search Map Properties', 'opalestate-pro' ) ],
			'register_form'         => [ 'code' => 'register_form', 'label' => esc_html__( 'Register User Form', 'opalestate-pro' ) ],
			'login_form'            => [ 'code' => 'login_form', 'label' => esc_html__( 'Login Form', 'opalestate-pro' ) ],
		];

		foreach ( $this->shortcodes as $shortcode ) {
			add_shortcode( 'opalestate_' . $shortcode['code'], [ $this, $shortcode['code'] ] );
		}

		if ( is_admin() ) {
			add_action( 'media_buttons', [ $this, 'shortcode_button' ] );
		}

	}

	public function shortcode_button() {

	}

	public function search_properties_form() {
		return opalestate_load_template_path( 'search-box/collapse-advanced' );
	}

	public function properties() {
		return opalestate_load_template_path( 'shortcodes/properties' );
	}

	public function search_properties_result() {
		return opalestate_load_template_path( 'shortcodes/search-properties-result' );
	}

	/**
	 * Display all properties follow user when logined
	 */
	public function agent_property() {
		return opalestate_load_template_path( 'shortcodes/agent-property-listing' );
	}

	/**
	 * Render search property page with horizontal form and map
	 */
	public function search_properties() {
		return opalestate_load_template_path( 'shortcodes/search-properties', [ 'loop' => '' ] );
	}

	/**
	 * Render search property page with vertical form and map
	 */
	public function search_properties_v() {
		return opalestate_load_template_path( 'shortcodes/search-properties-v', [ 'loop' => '' ] );
	}

	public function search_map_properties() {
		return opalestate_load_template_path( 'shortcodes/search-map-properties', [ 'loop' => '' ] );
	}

	public function ajax_map_search() {
		wp_enqueue_script( 'sticky-kit', OPALESTATE_PLUGIN_URL . 'assets/js/jquery.sticky-kit.min.js' );

		return opalestate_load_template_path( 'shortcodes/ajax-map-search', [ 'loop' => '' ] );
	}

	public function ajax_map_quick_search() {
		return opalestate_load_template_path( 'shortcodes/ajax-map-quick-search', [ 'loop' => '' ] );
	}

	/* register form show up */
	public function register_form( $atts = [] ) {
		$atts = shortcode_atts( [
			'message'    => '',
			'redirect'   => '',
			'hide_title' => false,
		], $atts );

		return opalestate_load_template_path( 'user/register-form', $atts );
	}

	/* sign in show up */
	public function login_form( $atts = [] ) {
		$atts = shortcode_atts( [
			'message'    => '',
			'redirect'   => '',
			'hide_title' => false,
		], $atts );

		return opalestate_load_template_path( 'user/login-form', $atts );
	}

}

new OpalEstate_Shortcodes();
