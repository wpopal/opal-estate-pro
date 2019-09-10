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


class Opalestate_Settings_3rd_party_Tab extends Opalestate_Settings_Base_Tab {

	
	public function get_subtabs () {
 		
 		$tabs =  (array)apply_filters (
 			'opalestate_settings_3rd_party_subtabs_nav', array() 
 		);

 		$tabs = array_merge_recursive( $tabs, array(
			'yelp' 		 => "Yelp",
			'walkcore'   => "Walkcore"
		) );

		return $tabs;
 	}

 	public function get_subtabs_content( $key ="" ) {  
 		// echo $key;die;
 		$fields = apply_filters ( 'opalestate_settings_3rd_party_subtabs_'.$key.'_fields', array()  ); 
 	 	
 	 	if( $key == 'yelp' ){
 	 		$fields = $this->get_yelp_fields();
 	 	}else if( $key == 'walkcore' ){
 	 		$fields = $this->get_walkscore_fields();
 	 	}

 		return [
			'id'               => 'options_page',
			'opalestate_title' => esc_html__( '3rd Party Settings', 'opalestate-pro' ),
			'show_on'          => [ 'key' => 'options-page', 'value' => [ $key ], ],
			'fields'			=> (array)$fields
		];
	}

	public function get_walkscore_fields(){
		return array(
			[
				'name'       => esc_html__( 'Walk Score', 'opalestate-pro' ),
				'desc'       => '',
				'type'       => 'opalestate_title',
				'id'         => 'opalestate_title_general_settings_walkscore',
				'before_row' => '<hr>',
				'after_row'  => '<hr>',
			],
			[
				'name' => esc_html__( 'Walk Score APi Key', 'opalestate-pro' ),
				'desc' => esc_html__( 'Add Walk Score API key. To get your Walk Score API key, go to your Walk Score Account.', 'opalestate-pro' ),
				'id'   => 'walkscore_api_key',
				'type' => 'text',
			]
		);
	}

	public function get_yelp_fields(){
		return array(
			[
				'name'       => esc_html__( 'Yelp', 'opalestate-pro' ),
				'desc'       => '',
				'type'       => 'opalestate_title',
				'id'         => 'opalestate_title_general_settings_yelp',
				'before_row' => '<hr>',
				'after_row'  => '<hr>',
			],
			[
				'name' => esc_html__( 'Yelp API Client ID', 'opalestate-pro' ),
				'desc' => esc_html__( 'Add Yelp client ID. To get your Yelp Api Client ID, go to your Yelp Account. Register <a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">here</a>',
					'opalestate-pro' ),
				'id'   => 'yelp_app_id',
				'type' => 'text',
			],
			[
				'name' => esc_html__( 'Yelp API Secret', 'opalestate-pro' ),
				'desc' => esc_html__( 'Add Yelp API Secret. Register <a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">here</a>',
					'opalestate-pro' ),
				'id'   => 'yelp_app_secret',
				'type' => 'text',
			],
			[
				'name' => esc_html__( 'Yelp App key', 'opalestate-pro' ),
				'desc' => esc_html__( 'You can find it in your Yelp Application Dashboard. Register <a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">here</a>',
					'opalestate-pro' ),
				'id'   => 'yelp_app_key',
				'type' => 'text',
			],
			[
				'name'    => esc_html__( 'Yelp Categories', 'opalestate-pro' ),
				'desc'    => esc_html__( 'Yelp Categories to show on front page', 'opalestate-pro' ),
				'id'      => 'yelp_categories',
				'type'    => 'multicheck',
				'options' => OpalEstate_Yelp::get_all_categories_options(),
			],
			[
				'name'       => esc_html__( 'Yelp - Number of results', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Number of results to show on listing page for each category.', 'opalestate-pro' ),
				'id'         => 'yelp_number_results',
				'type'       => 'text',
				'default'    => 3,
				'attributes' => [
					'type' => 'number',
					'min'  => 1,
				],
			],
			[
				'name'    => esc_html__( 'Yelp Distance Measurement Unit', 'opalestate-pro' ),
				'desc'    => esc_html__( 'Yelp Distance Measurement Unit', 'opalestate-pro' ),
				'id'      => 'yelp_measurement_unit',
				'type'    => 'select',
				'options' => [
					'miles'      => esc_html__( 'miles', 'opalestate-pro' ),
					'kilometers' => esc_html__( 'kilometers', 'opalestate-pro' ),
				],
				'default' => 'miles',
			]
		);
	}

}