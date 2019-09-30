<?php
/**
 * Define 
 * Note: only use for internal purpose.
 *
 * @package     OpalJob
 * @copyright   Copyright (c) 2019, WpOpal <https://www.wpopal.com>
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * @class Job_Api
 *
 * @since      1.0.0
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
class Agent_Api  extends  Base_Api {

	/**
	 * The unique identifier of the route resource.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string $base.
	 */
	public $base = '/agent';
 	
 	/**
	 * Register Routes
	 *
	 * Register all CURD actions with POST/GET/PUT and calling function for each
	 *
	 * @since 1.0
	 *
	 * @return avoid
	 */
	public function register_routes ( ) {  
	 	/// call http://domain.com/wp-json/job-api/v1/job/list  ////
		register_rest_route( $this->namespace, $this->base.'/list', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_list' ),
			'permission_callback' => array( $this, 'validate_request'  ),
		));
		/// call http://domain.com/wp-json/job-api/v1/job/1  ////
		register_rest_route( $this->namespace, $this->base.'/(?P<id>\d+)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_job' ),
			'permission_callback' => array( $this, 'validate_request' ),
		));

		/// call http://domain.com/wp-json/job-api/v1/job/create  ////
		register_rest_route( $this->namespace, $this->base.'/create', array(
			'methods' => 'GET',
			'callback' => array( $this, 'create' ),
			'permission_callback' => array( $this, 'validate_request' ),
		));
		/// call http://domain.com/wp-json/job-api/v1/job/edit  ////
		register_rest_route( $this->namespace, $this->base.'/edit', array(
			'methods' => 'GET',
			'callback' => array( $this, 'edit' ),
		));
		/// call http://domain.com/wp-json/job-api/v1/job/delete  ////
		register_rest_route( $this->namespace, $this->base.'/delete', array(
			'methods' => 'GET',
			'callback' => array( $this, 'delete' ),
			'permission_callback' => array( $this, 'validate_request' ),
		));

		/**
		 *  List job by tags and taxonmies 
		 */
		/// call http://domain.com/wp-json/job-api/v1/jobs  ////
		register_rest_route( $this->namespace, $this->base.'/tags', array(
			'methods' => 'GET',
			'callback' => array( $this, 'delete' ),
			'permission_callback' => array( $this, 'validate_request' ),
		));
	}

 

	/**
	 * Get List Of Job
	 *
	 * Based on request to get collection
	 *
	 * @since 1.0
	 *
	 * @return WP_REST_Response is json data
	 */
	public function get_list ( $request ) {

		$agents = array();
		$error = array();
		$agent = null;
		if ( $agent == null ) {
			$agents['agents'] = array();

			$property_list = get_posts( array(
				'post_type'        => 'opalestate_agent',
				'posts_per_page'   => $this->per_page(),
				'suppress_filters' => true,
				'paged'            => $this->get_paged()
			) );

			if ( $property_list ) {
				$i = 0;
				foreach ( $property_list as $agent_info ) {
					$agents['agents'][ $i ] = $this->get_agent_data( $agent_info );
					$i ++;
				}
			}
		} else {
			if ( get_post_type( $agent ) == 'opalestate_property' ) {
				$agent_info = get_post( $agent );

				$agents['agents'][0] = $this->get_agent_data( $agent_info );

			} else {
				$error['error'] = sprintf(
					/* translators: %s: property */
					esc_html__( 'Form %s not found!', 'opalestate-pro' ),
					$agent
				);

				return $error;
			}
		}

		$response['collection'] 	 = $agents['agents'];
		$response['pages'] 		     = 4; 
		$response['current'] 		 = 1;
		return $this->get_response( 200, $response );
	}


	/**
	 * Opalestaten a opalestate_property post object, generate the data for the API output
	 *
	 * @since  1.1
	 *
	 * @param  object $property_info The Download Post Object
	 *
	 * @return array                Array of post data to return back in the API
	 */

	public function get_agent_data( $agent_info ){
		$ouput = array();

		$ouput['info']['id']            = $agent_info->ID;
		$ouput['info']['slug']          = $agent_info->post_name;
		$ouput['info']['title']         = $agent_info->post_title;
		$ouput['info']['create_date']   = $agent_info->post_date;
		$ouput['info']['modified_date'] = $agent_info->post_modified;
		$ouput['info']['status']        = $agent_info->post_status;
		$ouput['info']['link']          = html_entity_decode( $agent_info->guid );
		$ouput['info']['content']       = $agent_info->post_content;
		$ouput['info']['thumbnail']     = wp_get_attachment_url( get_post_thumbnail_id( $agent_info->ID ) );

	

		$agent = new OpalEstate_Agent( $agent_info->ID );

		$ouput['info']['featured']     = (int)$agent->is_featured();
		$ouput['info']['email']		   = get_post_meta( $agent_info->ID, OPALESTATE_AGENT_PREFIX . 'email', true );
		$ouput['info']['address']	   = get_post_meta( $agent_info->ID, OPALESTATE_AGENT_PREFIX . 'address', true );

		$terms = wp_get_post_terms( $agent_info->ID, 'opalestate_agent_location' );
		$ouput['info']['location']	   =  $terms && !is_wp_error($terms) ? $terms : array();
	 
		$ouput['socials'] = $agent->get_socials(); 
		 
		$ouput['levels'] = wp_get_post_terms( $agent_info->ID, 'opalestate_agent_level' );


		return apply_filters( 'opalestate_api_agents', $ouput );
	}
	
	/**
	 * Delete job
	 *
	 * Based on request to get collection
	 *
	 * @since 1.0
	 *
	 * @return WP_REST_Response is json data
	 */
	public function delete( ) {

	}


	public function reviews () {

	}

	public function categories () {

	}
	
	public function tags () {
		
	}
}