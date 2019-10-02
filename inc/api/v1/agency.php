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
 * @class      Job_Api
 *
 * @since      1.0.0
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
class Agency_Api extends Base_Api {

	/**
	 * The unique identifier of the route resource.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string $base .
	 */
	public $base = '/agency';

	/**
	 * Register Routes
	 *
	 * Register all CURD actions with POST/GET/PUT and calling function for each
	 *
	 * @return avoid
	 * @since 1.0
	 *
	 */
	public function register_routes() {
		/// call http://domain.com/wp-json/estate-api/v1/job/list  ////
		register_rest_route( $this->namespace, $this->base . '/list', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_list' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/// call http://domain.com/wp-json/estate-api/v1/job/1  ////
		register_rest_route( $this->namespace, $this->base . '/(?P<id>\d+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_detail' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/// call http://domain.com/wp-json/estate-api/v1/job/create  ////
		register_rest_route( $this->namespace, $this->base . '/create', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'create' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/// call http://domain.com/wp-json/estate-api/v1/job/edit  ////
		register_rest_route( $this->namespace, $this->base . '/edit', [
			'methods'  => 'GET',
			'callback' => [ $this, 'edit' ],
		] );

		/// call http://domain.com/wp-json/estate-api/v1/job/delete  ////
		register_rest_route( $this->namespace, $this->base . '/delete', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'delete' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/**
		 *  List job by tags and taxonmies
		 */
		/// call http://domain.com/wp-json/estate-api/v1/jobs  ////
		register_rest_route( $this->namespace, $this->base . '/tags', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'delete' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );
	}


	/**
	 * Get List Of agencies.
	 *
	 * Based on request to get collection
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function get_list( $request ) {
		$agencies = [];
		$error  = [];
		$agency  = null;
		if ( $agency == null ) {
			$agencies['agencies'] = [];

			$agency_list = get_posts( [
				'post_type'        => 'opalestate_agency',
				'posts_per_page'   => $this->per_page(),
				'suppress_filters' => true,
				'paged'            => $this->get_paged(),
			] );

			if ( $agency_list ) {
				$i = 0;
				foreach ( $agency_list as $agency_info ) {
					$agencies['agencies'][ $i ] = $this->get_agency_data( $agency_info );
					$i++;
				}
			}
		} else {
			if ( get_post_type( $agency ) == 'opalestate_agency' ) {
				$agency_info = get_post( $agency );

				$agencies['agencies'][0] = $this->get_agency_data( $agency_info );

			} else {
				$error['error'] = sprintf(
				/* translators: %s: agency */
					esc_html__( 'Form %s not found!', 'opalestate-pro' ),
					$agency
				);

				return $this->get_response( 404, $error );
			}
		}

		$response['collection'] = $agencies['agencies'];
		$response['pages']      = 4;
		$response['current']    = 1;

		return $this->get_response( 200, $response );
	}

	/**
	 * Get Agency
	 *
	 * Based on request to get a agency.
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function get_detail( $request ) {
		$response = [];
		if ( $request['id'] > 0 ) {
			$post = get_post( $request['id'] );
			if ( $post && 'opalestate_agency' == get_post_type( $request['id'] ) ) {
				$agency             = $this->get_agency_data( $post );
				$response['agency'] = $agency ? $agency : [];
				$code = 200;
			} else {
				$code = 404;
				$response['error'] = sprintf( esc_html__( 'Agency ID: %s does not exist!', 'opalestate-pro' ), $request['id'] );
			}
		} else {
			$code = 404;
			$response['error'] = sprintf( esc_html__( 'Invalid ID.', 'opalestate-pro' ), $request['id'] );
		}

		return $this->get_response( $code, $response );
	}

	/**
	 * The opalestate_agency post object, generate the data for the API output
	 *
	 * @param object $agency_info The Download Post Object
	 *
	 * @return array                Array of post data to return back in the API
	 * @since  1.0
	 *
	 */
	public function get_agency_data( $agency_info ) {
		$ouput                          = [];
		$ouput['info']['id']            = $agency_info->ID;
		$ouput['info']['slug']          = $agency_info->post_name;
		$ouput['info']['title']         = $agency_info->post_title;
		$ouput['info']['create_date']   = $agency_info->post_date;
		$ouput['info']['modified_date'] = $agency_info->post_modified;
		$ouput['info']['status']        = $agency_info->post_status;
		$ouput['info']['link']          = html_entity_decode( $agency_info->guid );
		$ouput['info']['content']       = $agency_info->post_content;
		$ouput['info']['thumbnail']     = wp_get_attachment_url( get_post_thumbnail_id( $agency_info->ID ) );

		$agency = new OpalEstate_Agency( $agency_info->ID );

		$ouput['info']['featured'] = (int) $agency->is_featured();
		$ouput['info']['email']    = get_post_meta( $agency_info->ID, OPALESTATE_AGENCY_PREFIX . 'email', true );
		$ouput['info']['address']  = get_post_meta( $agency_info->ID, OPALESTATE_AGENCY_PREFIX . 'address', true );

		$terms                     = wp_get_post_terms( $agency_info->ID, 'opalestate_agency_location' );
		$ouput['info']['location'] = $terms && ! is_wp_error( $terms ) ? $terms : [];
		$ouput['socials']          = $agency->get_socials();

		return apply_filters( 'opalestate_api_agencies', $ouput );
	}

	/**
	 * Delete job
	 *
	 * Based on request to get collection
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function delete() {

	}


	public function reviews() {

	}

	public function categories() {

	}

	public function tags() {

	}
}
