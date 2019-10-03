<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @class      Agent_Api
 *
 * @since      1.0.0
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
class Agent_Api extends Base_Api {

	/**
	 * The unique identifier of the route resource.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string $base .
	 */
	public $base = '/agent';

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
		/**
		 * Get list of agents.
		 *
		 * Call http://domain.com/wp-json/estate-api/v1/agent/list
		 */
		register_rest_route( $this->namespace, $this->base . '/list', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_list' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/**
		 * Get agent detail.
		 *
		 * Call http://domain.com/wp-json/estate-api/v1/agent/1
		 */
		register_rest_route( $this->namespace, $this->base . '/(?P<id>\d+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_detail' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/**
		 * Create a agent.
		 *
		 * Call http://domain.com/wp-json/estate-api/v1/agent/create
		 */
		register_rest_route( $this->namespace, $this->base . '/create', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'create' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );

		/**
		 * Edit a agent.
		 *
		 * Call http://domain.com/wp-json/estate-api/v1/agent/edit
		 */
		register_rest_route( $this->namespace, $this->base . '/edit', [
			'methods'  => 'GET',
			'callback' => [ $this, 'edit' ],
		] );

		/**
		 * Delete a agent.
		 *
		 * Call http://domain.com/wp-json/estate-api/v1/agent/delete
		 */
		register_rest_route( $this->namespace, $this->base . '/delete', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'delete' ],
			'permission_callback' => [ $this, 'validate_request' ],
		] );
	}

	/**
	 * Get List Of agents.
	 *
	 * Based on request to get collection
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function get_list( $request ) {
		$agents = [];

		$agents['agents'] = [];


		$agent_list = get_posts( [
			'post_type'        => 'opalestate_agent',
			'posts_per_page'   => $this->per_page(),
			'suppress_filters' => true,
			'paged'            => $this->get_paged(),
		] );

		if ( $agent_list ) {
			$i = 0;
			foreach ( $agent_list as $agent_info ) {
				$agents['agents'][ $i ] = $this->get_agent_data( $agent_info );
				$i++;
			}
		}

		$response['collection'] = $agents['agents'];
		$response['pages']      = 4;
		$response['current']    = 1;

		return $this->get_response( 200, $response );
	}

	/**
	 * Get Agent
	 *
	 * Based on request to get a agent.
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function get_detail( $request ) {
		$response = [];
		if ( $request['id'] > 0 ) {
			$post = get_post( $request['id'] );
			if ( $post && 'opalestate_agent' == get_post_type( $request['id'] ) ) {
				$agent             = $this->get_agent_data( $post );
				$response['agent'] = $agent ? $agent : [];
				$code              = 200;
			} else {
				$code              = 404;
				$response['error'] = sprintf( esc_html__( 'Agent ID: %s does not exist!', 'opalestate-pro' ), $request['id'] );
			}
		} else {
			$code              = 404;
			$response['error'] = sprintf( esc_html__( 'Invalid ID.', 'opalestate-pro' ), $request['id'] );
		}

		return $this->get_response( $code, $response );
	}

	/**
	 * The opalestate_agent post object, generate the data for the API output
	 *
	 * @param object $agent_info The Download Post Object
	 *
	 * @return array                Array of post data to return back in the API
	 * @since  1.0
	 *
	 */
	public function get_agent_data( $agent_info ) {
		$ouput                          = [];
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

		$ouput['info']['avatar']   = $agent->get_meta( 'avatar' );
		$ouput['info']['featured'] = (int) $agent->is_featured();
		$ouput['info']['trusted']  = $agent->get_trusted();
		$ouput['info']['email']    = $agent->get_meta( 'email' );
		$ouput['info']['address']  = $agent->get_meta( 'address' );
		$ouput['info']['map']      = $agent->get_meta( 'map' );

		$terms                     = wp_get_post_terms( $agent_info->ID, 'opalestate_agent_location' );
		$ouput['info']['location'] = $terms && ! is_wp_error( $terms ) ? $terms : [];
		$ouput['socials']          = $agent->get_socials();
		$ouput['levels']           = wp_get_post_terms( $agent_info->ID, 'opalestate_agent_level' );

		return apply_filters( 'opalestate_api_agents', $ouput );
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
