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
/**
 * Api_Auth class for authorizing to access api resources 
 *
 * @since      1.0.0
 * @package    Opal_Job
 * @subpackage Opal_Job/API
 */
class Opalestate_Api_Auth extends Opalestate_Base_API {

	/**
	 * Register user endpoints.
	 *
	 * to check post method need authorization to continue completing action 
	 *
	 * @since 1.0
	 *
	 * @return avoid
	 */
	public function register_routes() {  
		// check all request must to have public key and token
		register_rest_route( $this->namespace, '/job/list', array(
			'methods' => 'GET',
			'permission_callback' => array( $this, 'validate_request' ),
		), 9 );

		////////////////// Check User Authorizcation must to have account logined
		// check authorcation for all delete in route
		register_rest_route($this->namespace, '/(?P<path>[\S]+)/delete', array(
			'methods' => 'GET',
			'callback' => array( $this, 'check' ),
		));
		// check authorcation for all delete in route
		register_rest_route($this->namespace, '/(?P<path>[\S]+)/edit', array(
			'methods' => 'GET',
			'callback' => array( $this, 'check' ),
		));
		// check authorcation for all delete in route
		register_rest_route($this->namespace, '/(?P<path>[\S]+)/create', array(
			'methods' => 'GET',
			'callback' => array( $this, 'check' ),
		));
	}


	/**
	 * Check authorization
	 *
	 * check user request having passing username and password, then check them be valid or not.
	 *
	 * @param WP_REST_Request $request
	 * @since 1.0
	 *
	 * @return WP_REST_Response is json data   
	 */
	public function check( WP_REST_Request $request ) { 
		$response = array();

		$default = array(
			'username' => '',
			'password' => ''
		);
		
		$parameters = $request->get_params();
		$parameters = array_merge( $default, $parameters );

		$username = sanitize_text_field( $parameters['username'] );
		$password = sanitize_text_field( $parameters['password'] );

		// Error Handling.
		$error = new WP_Error();
		if ( empty( $username ) ) {
			$error->add(
				400,
				__( "Username field is required", 'rest-api-endpoints' ),
				array( 'status' => 400 )
				);
			return $error;
		}
		if ( empty( $password ) ) {
			$error->add(
				400,
				__( "Password field is required", 'rest-api-endpoints' ),
				array( 'status' => 400 )
			);
			return $error;
		}
		$user = wp_authenticate( $username, $password  );

		// If user found
		if ( ! is_wp_error( $user ) ) {
			$response['status'] = 200;
			$response['user'] = $user;
		} else {
			// If user not found
			$error->add( 406, esc_html_e( 'User not found. Check credentials', 'rest-api-endpoints' ) );
			return $error;
		}
		return new WP_REST_Response( $response );
	}
}
