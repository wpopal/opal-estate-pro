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
class Agency_Api  extends  Base_Api {

	/**
	 * The unique identifier of the route resource.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string $base.
	 */
	public $base = '/agency';
 	
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

		$query = new Opalestate_Agency_Query(
			array(
				'post_type'        => 'opalestate_agency',
				'posts_per_page'   => $this->per_page(),
				'suppress_filters' => true,
				'paged'            => $this->get_paged()
			) 
		);

		$data = $query->get_api_list();
		$response['collection'] 	 = $data['collection'];
		$response['found'] 		 	 = $data['found'];
		$response['current'] 		 = 1;

		return $this->get_response( 200, $response );
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