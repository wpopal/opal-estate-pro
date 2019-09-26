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
class Opalestate_Field_Map {

	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	public function __construct() {
		add_filter( 'cmb2_render_opal_map', array( $this, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_opal_map', array( $this, 'sanitize_map' ), 10, 4 );
	}

	/**
	 * Render field
	 */
	public function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$this->setup_admin_scripts();
		$address = (isset( $field_escaped_value['address'] ) ? $field_escaped_value['address'] : ''); 
 		
		echo '<div class="'.apply_filters('opalestate_row_container_class', 'row opal-row').'">
			<div class="opalestate-map-wrap col-sm-6">
				<div class="opal-map"></div>
			</div>
			<div class="col-sm-6">
					<div  class="form-group">
						<label>'.__( 'Map Address', 'opalestate-pro' ).'</label>
						<input type="text" class="large-text regular-text opal-map-search  form-control" id="' . $field->args( 'id' ) . '" 
						name="'.$field->args( '_name' ).'[address]" value="'.$address.'"/>';
				echo '</div>';
		 
				$field_type_object->_desc( true, true );

					echo ' <div class="form-group">';
					echo '<label>'.__( 'Latitude', 'opalestate-pro' ).'</label>';
					echo $field_type_object->input( array(
						'type'       => 'text',
						'name'       => $field->args( '_name' ) . '[latitude]',
						'value'      => isset( $field_escaped_value['latitude'] ) ? $field_escaped_value['latitude'] : '',
						'class'      => 'opal-map-latitude form-control',
						'desc'       => '',
					) );
					echo '</div>';
					echo ' <div class="form-group">';
					echo '<label>'.__( 'Longitude',  'opalestate-pro' ).'</label>';
					echo $field_type_object->input( array(
						'type'       => 'text',
						'name'       => $field->args( '_name' ) . '[longitude]',
						'value'      => isset( $field_escaped_value['longitude'] ) ? $field_escaped_value['longitude'] : '',
						'class'      => 'opal-map-longitude form-control',
						'desc'       => '',
					) );
					echo '</div>';
			echo '<p class="opal-map-desc">' . __( 'You need to register <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google API Key</a>, then put the key in plugin setting.',
				'opalestate-pro' ) . '</p>';

			echo '</div>';
		echo '</div>';	
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields
	 */
	public function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		if ( isset( $field_args['split_values'] ) && $field_args['split_values'] ) {
			if ( ! empty( $value['latitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_latitude', $value['latitude'] );
			}

			if ( ! empty( $value['longitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_longitude', $value['longitude'] );
			}  

			if ( ! empty( $value['address'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_address', $value['address'] );
			}
		}

		return $value;
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function setup_admin_scripts() {
		$api = opalestate_get_map_api_uri();
		
		wp_enqueue_script("opalestate-google-maps", $api, null, "0.0.1", false);

		wp_enqueue_script( 'opalestate-google-maps-js', plugins_url( 'js/script.js', __FILE__ ), array(  ) );
		wp_enqueue_style( 'opalestate-google-maps', plugins_url( 'css/style.css', __FILE__ ), array() );
 

	}
}

new Opalestate_Field_Map();
