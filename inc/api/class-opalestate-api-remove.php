<?php
/**
 * Opalestate API
 *
 * A front-facing JSON/XML API that makes it possible to query donation data.
 *
 * @package     Opalestate
 * @subpackage  Classes/API
 * @copyright   Copyright (c) 2019, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opalestate_API Class
 *
 * Renders API returns as a JSON/XML array
 *
 * @since  1.1
 */
class Opalestate_API {

	/**
	 * Latest API Version
	 */
	const VERSION = 1;

	/**
	 * Pretty Print?
	 *
	 * @var bool
	 * @access private
	 * @since  1.1
	 */
	private $pretty_print = false;

	/**
	 * Log API requests?
	 *
	 * @var bool
	 * @access public
	 * @since  1.1
	 */
	public $log_requests = true;

	/**
	 * Is this a valid request?
	 *
	 * @var bool
	 * @access private
	 * @since  1.1
	 */
	private $is_valid_request = false;

	/**
	 * User ID Perpropertying the API Request
	 *
	 * @var int
	 * @access public
	 * @since  1.1
	 */
	public $user_id = 0;

	/**
	 * Instance of Opalestate Stats class
	 *
	 * @var object
	 * @access private
	 * @since  1.1
	 */
	private $stats;

	/**
	 * Response data to return
	 *
	 * @var array
	 * @access private
	 * @since  1.1
	 */
	private $data = array();

	/**
	 *
	 * @var bool
	 * @access public
	 * @since  1.1
	 */
	public $override = true;

	/**
	 * Version of the API queried
	 *
	 * @var string
	 * @access public
	 * @since  1.1
	 */
	private $queried_version;

	/**
	 * All versions of the API
	 *
	 * @var string
	 * @access protected
	 * @since  1.1
	 */
	protected $versions = array();

	/**
	 * Queried endpoint
	 *
	 * @var string
	 * @access private
	 * @since  1.1
	 */
	private $endpoint;

	/**
	 * Endpoints routes
	 *
	 * @var object
	 * @access private
	 * @since  1.1
	 */
	private $routes;

	/**
	 * Setup the Opalestate API
	 *
	 * @since 1.1
	 * @access public
	 */
	public function __construct() {

		$this->versions = array(
			'v1' => 'Opalestate_API',
		);

	 

		add_action( 'init', array( $this, 'add_endpoint' ) );
		add_action( 'wp', array( $this, 'process_query' ), - 1 );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'show_user_profile', array( $this, 'user_key_field' ) );
		add_action( 'edit_user_profile', array( $this, 'user_key_field' ) );
		add_action( 'personal_options_update', array( $this, 'update_key' ) );
		add_action( 'edit_user_profile_update', array( $this, 'update_key' ) );
		add_action( 'opalestate_process_api_key', array( $this, 'process_api_key' ) );

		// Setup a backwards compatibility check for user API Keys
		add_filter( 'get_user_metadata', array( $this, 'api_key_backwards_compat' ), 10, 4 );

		// Determine if JSON_PRETTY_PRINT is available
		$this->pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;

		// Allow API request logging to be turned off
		$this->log_requests = apply_filters( 'opalestate_api_log_requests', $this->log_requests );
 

	}

	/**
	 * Registers a new rewrite endpoint for accessing the API
	 *
	 * @access public
	 *
	 * @param array $rewrite_rules WordPress Rewrite Rules
	 *
	 * @since  1.1
	 */
	public function add_endpoint( $rewrite_rules ) {
		add_rewrite_endpoint( 'opalestate-api', EP_ALL );
	}

	/**
	 * Registers query vars for API access
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param array $vars Query vars
	 *
	 * @return string[] $vars New query vars
	 */
	public function query_vars( $vars ) {

		$vars[] = 'token';
		$vars[] = 'key';
		$vars[] = 'query';
		$vars[] = 'type';
		$vars[] = 'property';
		$vars[] = 'number';
		$vars[] = 'date';
		$vars[] = 'startdate';
		$vars[] = 'enddate';
		$vars[] = 'donor';
		$vars[] = 'propertyat';
		$vars[] = 'id';
		$vars[] = 'purchasekey';
		$vars[] = 'email';

		return $vars;
	}

	/**
	 * Retrieve the API versions
	 *
	 * @access public
	 * @since  1.1
	 * @return array
	 */
	public function get_versions() {
		return $this->versions;
	}

	/**
	 * Retrieve the API version that was queried
	 *
	 * @access public
	 * @since  1.1
	 * @return string
	 */
	public function get_queried_version() {
		return $this->queried_version;
	}

	/**
	 * Retrieves the default version of the API to use
	 *
	 * @access public
	 * @since  1.1
	 * @return string
	 */
	public function get_default_version() {

		$version = get_option( 'opalestate_default_api_version' );

		if ( defined( 'OPALESTATE_API_VERSION' ) ) {
			$version = OPALESTATE_API_VERSION;
		} elseif ( ! $version ) {
			$version = 'v1';
		}

		return $version;
	}

	/**
	 * Sets the version of the API that was queried.
	 *
	 * Falls back to the default version if no version is specified
	 *
	 * @access private
	 * @since  1.1
	 */
	private function set_queried_version() {

		global $wp_query;

		$version = $wp_query->query_vars['opalestate-api'];

		if ( strpos( $version, '/' ) ) {

			$version = explode( '/', $version );
			$version = strtolower( $version[0] );

			$wp_query->query_vars['opalestate-api'] = str_replace( $version . '/', '', $wp_query->query_vars['opalestate-api'] );

			if ( array_key_exists( $version, $this->versions ) ) {

				$this->queried_version = $version;

			} else {

				$this->is_valid_request = false;
				$this->invalid_version();
			}

		} else {

			$this->queried_version = $this->get_default_version();

		}

	}

	/**
	 * Validate the API request
	 *
	 * Checks for the user's public key and token against the secret key
	 *
	 * @access private
	 * @global object $wp_query WordPress Query
	 * @uses   Opalestate_API::get_user()
	 * @uses   Opalestate_API::invalid_key()
	 * @uses   Opalestate_API::invalid_auth()
	 * @since  1.1
	 * @return void
	 */
	private function validate_request() {
		global $wp_query;

		$this->override = false;

		// Make sure we have both user and api key
		if ( ! empty( $wp_query->query_vars['opalestate-api'] ) && ( $wp_query->query_vars['opalestate-api'] != 'properties' || ! empty( $wp_query->query_vars['token'] ) ) ) {

			if ( empty( $wp_query->query_vars['token'] ) || empty( $wp_query->query_vars['key'] ) ) {
				$this->missing_auth();
			}

			// Retrieve the user by public API key and ensure they exist
			if ( ! ( $user = $this->get_user( $wp_query->query_vars['key'] ) ) ) {

				$this->invalid_key();

			} else {

				$token  = urldecode( $wp_query->query_vars['token'] );
				$secret = $this->get_user_secret_key( $user );
				$public = urldecode( $wp_query->query_vars['key'] );

				if ( hash_equals( md5( $secret . $public ), $token ) ) {
					$this->is_valid_request = true;
				} else {
					$this->invalid_auth();
				}
			}
		} elseif ( ! empty( $wp_query->query_vars['opalestate-api'] ) && $wp_query->query_vars['opalestate-api'] == 'properties' ) {
			$this->is_valid_request = true;
			$wp_query->set( 'key', 'public' );
		}
	}

	/**
	 * Retrieve the user ID based on the public key provided
	 *
	 * @access public
	 * @since  1.1
	 * @global WPDB $wpdb Used to query the database using the WordPress
	 *                      Database API
	 *
	 * @param string $key Public Key
	 *
	 * @return bool if user ID is found, false otherwise
	 */
	public function get_user( $key = '' ) {
		global $wpdb, $wp_query;

		if ( empty( $key ) ) {
			$key = urldecode( $wp_query->query_vars['key'] );
		}

		if ( empty( $key ) ) {
			return false;
		}

		$user = get_transient( md5( 'opalestate_api_user_' . $key ) );

		if ( false === $user ) {
			$user = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s LIMIT 1", $key ) );
			set_transient( md5( 'opalestate_api_user_' . $key ), $user, DAY_IN_SECONDS );
		}

		if ( $user != null ) {
			$this->user_id = $user;

			return $user;
		}

		return false;
	}

	public function get_user_public_key( $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return '';
		}

		$cache_key       = md5( 'opalestate_api_user_public_key' . $user_id );
		$user_public_key = get_transient( $cache_key );

		if ( empty( $user_public_key ) ) {
			$user_public_key = $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->usermeta WHERE meta_value = 'opalestate_user_public_key' AND user_id = %d", $user_id ) );
			set_transient( $cache_key, $user_public_key, HOUR_IN_SECONDS );
		}

		return $user_public_key;
	}

	public function get_user_secret_key( $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return '';
		}

		$cache_key       = md5( 'opalestate_api_user_secret_key' . $user_id );
		$user_secret_key = get_transient( $cache_key );

		if ( empty( $user_secret_key ) ) {
			$user_secret_key = $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->usermeta WHERE meta_value = 'opalestate_user_secret_key' AND user_id = %d", $user_id ) );
			set_transient( $cache_key, $user_secret_key, HOUR_IN_SECONDS );
		}

		return $user_secret_key;
	}

	/**
	 * Displays a missing authentication error if all the parameters aren't
	 * provided
	 *
	 * @access private
	 * @uses   Opalestate_API::output()
	 * @since  1.1
	 */
	private function missing_auth() {
		$error          = array();
		$error['error'] = esc_html__( 'You must specify both a token and API key!', 'opalestate-pro' );

		$this->data = $error;
		$this->output( 401 );
	}

	/**
	 * Displays an authentication failed error if the user failed to provide valid
	 * credentials
	 *
	 * @access private
	 * @since  1.1
	 * @uses   Opalestate_API::output()
	 * @return void
	 */
	private function invalid_auth() {
		$error          = array();
		$error['error'] = esc_html__( 'Your request could not be authenticated!', 'opalestate-pro' );

		$this->data = $error;
		$this->output( 403 );
	}

	/**
	 * Displays an invalid API key error if the API key provided couldn't be
	 * validated
	 *
	 * @access private
	 * @since  1.1
	 * @uses   Opalestate_API::output()
	 * @return void
	 */
	private function invalid_key() {
		$error          = array();
		$error['error'] = esc_html__( 'Invalid API key!', 'opalestate-pro' );

		$this->data = $error;
		$this->output( 403 );
	}

	/**
	 * Displays an invalid version error if the version number passed isn't valid
	 *
	 * @access private
	 * @since  1.1
	 * @uses   Opalestate_API::output()
	 * @return void
	 */
	private function invalid_version() {
		$error          = array();
		$error['error'] = esc_html__( 'Invalid API version!', 'opalestate-pro' );

		$this->data = $error;
		$this->output( 404 );
	}

	/**
	 * Listens for the API and then processes the API requests
	 *
	 * @access public
	 * @global $wp_query
	 * @since  1.1
	 * @return void
	 */
	public function process_query() {

		global $wp_query;

		// Start logging how long the request takes for logging
		$before = microtime( true );

		// Check for opalestate-api var. Get out if not present
		if ( empty( $wp_query->query_vars['opalestate-api'] ) ) {
			return;
		}

		// Determine which version was queried
		$this->set_queried_version();

		// Determine the kind of query
		$this->set_query_mode();

		// Check for a valid user and set errors if necessary
		$this->validate_request();

		// Only proceed if no errors have been noted
		if ( ! $this->is_valid_request ) {
			return;
		}

		if ( ! defined( 'OPALESTATE_DOING_API' ) ) {
			define( 'OPALESTATE_DOING_API', true );
		}

		$data         = array();
		$this->routes = new $this->versions[$this->get_queried_version()];
		$this->routes->validate_request();

		switch ( $this->endpoint ) :

		 
			case 'properties' :

				$property = isset( $wp_query->query_vars['property'] ) ? $wp_query->query_vars['property'] : null;

				$data = $this->routes->get_properties( $property );

				break;
			case 'featured' :

				$property = isset( $wp_query->query_vars['property'] ) ? $wp_query->query_vars['property'] : null;

				$data = $this->routes->get_featured_properties( $property );

				break;
					
			case 'agents' :

				$agent = isset( $wp_query->query_vars['agent'] ) ? $wp_query->query_vars['agent'] : null;

				$data = $this->routes->get_agents( $agent );

				break;
	

		endswitch;

		// Allow extensions to setup their own return data
		$this->data = apply_filters( 'opalestate_api_output_data', $data, $this->endpoint, $this );

		$after                       = microtime( true );
		$request_time                = ( $after - $before );
		$this->data['request_speed'] = $request_time;

		// Log this API request, if enabled. We log it here because we have access to errors.
		$this->log_request( $this->data );

		// Send out data to the output function
		$this->output();
	}

	/**
	 * Returns the API endpoint requested
	 *
	 * @access public
	 * @since  1.1
	 * @return string $query Query mode
	 */
	public function get_query_mode() {

		return $this->endpoint;
	}

	/**
	 * Determines the kind of query requested and also ensure it is a valid query
	 *
	 * @access public
	 * @since  1.1
	 * @global $wp_query
	 */
	public function set_query_mode() {

		global $wp_query;

		// Whitelist our query options
		$accepted = apply_filters( 'opalestate_api_valid_query_modes', array(
			'agents',
			'properties',
			'featured'
		) );

		$query = isset( $wp_query->query_vars['opalestate-api'] ) ? $wp_query->query_vars['opalestate-api'] : null;
		$query = str_replace( $this->queried_version . '/', '', $query );

		$error = array();

		// Make sure our query is valid
		if ( ! in_array( $query, $accepted ) ) {
			$error['error'] = esc_html__( 'Invalid query!', 'opalestate-pro' );

			$this->data = $error;
			// 400 is Bad Request
			$this->output( 400 );
		}

		$this->endpoint = $query;
	}

	/**
	 * Get page number
	 *
	 * @access public
	 * @since  1.1
	 * @global $wp_query
	 * @return int $wp_query->query_vars['page'] if page number returned (default: 1)
	 */
	public function get_paged() {
		global $wp_query;

		return isset( $wp_query->query_vars['page'] ) ? $wp_query->query_vars['page'] : 1;
	}


	/**
	 * Number of results to display per page
	 *
	 * @access public
	 * @since  1.1
	 * @global $wp_query
	 * @return int $per_page Results to display per page (default: 10)
	 */
	public function per_page() {
		global $wp_query;

		$per_page = isset( $wp_query->query_vars['number'] ) ? $wp_query->query_vars['number'] : 10;
		
		return apply_filters( 'opalestate_api_results_per_page', $per_page );
	}
 
 
	/**
	 *
	 *
	 */
	public function get_agents( $agent = null ) {

		$agents = array();
		$error = array();

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

		return $agents;
	}

	/**
	 *
	 *
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
	 * Process Get Products API Request
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $property Opalestate Form ID
	 *
	 * @return array $customers Multidimensional array of the properties
	 */
	public function get_featured_properties( $property = null ) {

		$properties = array();
		$error = array();

		if ( $property == null ) {
			$properties['properties'] = array();

			$property_list = get_posts( array(
				'post_type'        => 'opalestate_property',
				'posts_per_page'   => $this->per_page(),
				'suppress_filters' => true,
				'paged'            => $this->get_paged(),
				'meta_key' 		   => OPALESTATE_PROPERTY_PREFIX . 'featured',
				'meta_value' 	   => 1,
				'meta_compare' 	   => '='
			) );

			if ( $property_list ) {
				$i = 0;
				foreach ( $property_list as $property_info ) {
					$properties['properties'][ $i ] = $this->get_property_data( $property_info );
					$i ++;
				}
			}
		} else {
			if ( get_post_type( $property ) == 'opalestate_property' ) {
				$property_info = get_post( $property );

				$properties['properties'][0] = $this->get_property_data( $property_info );

			} else {
				$error['error'] = sprintf(
					/* translators: %s: property */
					esc_html__( 'Form %s not found!', 'opalestate-pro' ),
					$property
				);

				return $error;
			}
		}

		return $properties;
	}

	/**
	 * Process Get Products API Request
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $property Opalestate Form ID
	 *
	 * @return array $customers Multidimensional array of the properties
	 */
	public function get_properties( $property = null ) {

		$properties = array();
		$error = array();

		if ( $property == null ) {
			$properties['properties'] = array();

			$property_list = get_posts( array(
				'post_type'        => 'opalestate_property',
				'posts_per_page'   => $this->per_page(),
				'suppress_filters' => true,
				'paged'            => $this->get_paged()
			) );

			if ( $property_list ) {
				$i = 0;
				foreach ( $property_list as $property_info ) {
					$properties['properties'][ $i ] = $this->get_property_data( $property_info );
					$i ++;
				}
			}
		} else {
			if ( get_post_type( $property ) == 'opalestate_property' ) {
				$property_info = get_post( $property );

				$properties['properties'][0] = $this->get_property_data( $property_info );

			} else {
				$error['error'] = sprintf(
					/* translators: %s: property */
					esc_html__( 'Form %s not found!', 'opalestate-pro' ),
					$property
				);

				return $error;
			}
		}

		return $properties;
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
	private function get_property_data( $property_info ) {

		$property = array();

		$property['info']['id']            = $property_info->ID;
		$property['info']['slug']          = $property_info->post_name;
		$property['info']['title']         = $property_info->post_title;
		$property['info']['create_date']   = $property_info->post_date;
		$property['info']['modified_date'] = $property_info->post_modified;
		$property['info']['status']        = $property_info->post_status;
		$property['info']['link']          = html_entity_decode( $property_info->guid );
		$property['info']['content']       = $property_info->post_content;
		$property['info']['thumbnail']     = wp_get_attachment_url( get_post_thumbnail_id( $property_info->ID ) );

		$data = opalesetate_property( $property_info->ID );
		$gallery = $data->get_gallery();
		$property['info']['gallery'] = isset($gallery[0]) && !empty($gallery[0]) ? $gallery[0]: array();
		$property['info']['price'] 	 = opalestate_price_format( $data->get_price() );
		$property['info']['map']	 = $data->get_map();
		$property['info']['address'] = $data->get_address();
		$property['meta'] 			= $data->get_meta_shortinfo();

		$property['status'] 		= $data->get_status();
		$property['locations'] 		= $data->get_locations();
		$property['amenities'] 		= $data->get_amenities();
		$property['types'] 			= $data->get_types_tax();
		
		
		if ( user_can( $this->user_id, 'view_opalestate_sensitive_data' ) || $this->override ) {

			//Sensitive data here
			do_action( 'opalestate_api_sensitive_data' );

		}

		return apply_filters( 'opalestate_api_properties_property', $property );

	}

	 
 

	/**
	 * Retrieve the output propertyat
	 *
	 * Determines whether results should be displayed in XML or JSON
	 *
	 * @since 1.1
     * @access public
	 *
	 * @return mixed|void
	 */
	public function get_output_propertyat() {
		global $wp_query;

		$propertyat = isset( $wp_query->query_vars['propertyat'] ) ? $wp_query->query_vars['propertyat'] : 'json';

		return apply_filters( 'opalestate_api_output_propertyat', $propertyat );
	}


	/**
	 * Log each API request, if enabled
	 *
	 * @access private
	 * @since  1.1
     *
	 * @global Opalestate_Logging $opalestate_logs
	 * @global WP_Query     $wp_query
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	private function log_request( $data = array() ) {
		if ( ! $this->log_requests ) {
			return;
		}

        /**
         * @var Opalestate_Logging $opalestate_logs
         */
		global $opalestate_logs;

        /**
         * @var WP_Query $wp_query
         */
        global $wp_query;

		$query = array(
			'opalestate-api'    => $wp_query->query_vars['opalestate-api'],
			'key'         => isset( $wp_query->query_vars['key'] ) ? $wp_query->query_vars['key'] : null,
			'token'       => isset( $wp_query->query_vars['token'] ) ? $wp_query->query_vars['token'] : null,
			'query'       => isset( $wp_query->query_vars['query'] ) ? $wp_query->query_vars['query'] : null,
			'type'        => isset( $wp_query->query_vars['type'] ) ? $wp_query->query_vars['type'] : null,
			'property'        => isset( $wp_query->query_vars['property'] ) ? $wp_query->query_vars['property'] : null,
			'customer'    => isset( $wp_query->query_vars['customer'] ) ? $wp_query->query_vars['customer'] : null,
			'date'        => isset( $wp_query->query_vars['date'] ) ? $wp_query->query_vars['date'] : null,
			'startdate'   => isset( $wp_query->query_vars['startdate'] ) ? $wp_query->query_vars['startdate'] : null,
			'enddate'     => isset( $wp_query->query_vars['enddate'] ) ? $wp_query->query_vars['enddate'] : null,
			'id'          => isset( $wp_query->query_vars['id'] ) ? $wp_query->query_vars['id'] : null,
			'purchasekey' => isset( $wp_query->query_vars['purchasekey'] ) ? $wp_query->query_vars['purchasekey'] : null,
			'email'       => isset( $wp_query->query_vars['email'] ) ? $wp_query->query_vars['email'] : null,
		);

		$log_data = array(
			'log_type'     => 'api_request',
			'post_excerpt' => http_build_query( $query ),
			'post_content' => ! empty( $data['error'] ) ? $data['error'] : '',
		);

		$log_meta = array(
			'request_ip' => opalestate_get_ip(),
			'user'       => $this->user_id,
			'key'        => isset( $wp_query->query_vars['key'] ) ? $wp_query->query_vars['key'] : null,
			'token'      => isset( $wp_query->query_vars['token'] ) ? $wp_query->query_vars['token'] : null,
			'time'       => $data['request_speed'],
			'version'    => $this->get_queried_version()
		);
	}


	/**
	 * Retrieve the output data
	 *
	 * @access public
	 * @since  1.1
	 * @return array
	 */
	public function get_output() {
		return $this->data;
	}

	/**
	 * Output Query in either JSON/XML. The query data is outputted as JSON
	 * by default
	 *
	 * @since 1.1
	 * @global WP_Query $wp_query
	 *
	 * @param int $status_code
	 */
	public function output( $status_code = 200 ) {
        /**
         * @var WP_Query $wp_query
         */
		global $wp_query;

		$propertyat = $this->get_output_propertyat();

		status_header( $status_code );

		do_action( 'opalestate_api_output_before', $this->data, $this, $propertyat );

		switch ( $propertyat ) :

			case 'xml' :

				require_once OPALESTATE_PLUGIN_DIR . 'inc/libraries/array2xml.php';
				$xml = Array2XML::createXML( 'opalestate-pro', $this->data );
				echo $xml->saveXML();

				break;

			case 'json' :

				header( 'Content-Type: application/json' );
				if ( ! empty( $this->pretty_print ) ) {
					echo json_encode( $this->data, $this->pretty_print );
				} else {
					echo json_encode( $this->data );
				}

				break;


			default :

				// Allow other propertyats to be added via extensions
				do_action( 'opalestate_api_output_' . $propertyat, $this->data, $this );

				break;

		endswitch;

		do_action( 'opalestate_api_output_after', $this->data, $this, $propertyat );

		die();
	}

	/**
	 * Modify User Profile
	 *
	 * Modifies the output of profile.php to add key generation/revocation
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param object $user Current user info
	 *
	 * @return void
	 */
	function user_key_field( $user ) {

		if ( ( opalestate_get_option( 'api_allow_user_keys', false ) || current_user_can( 'manage_opalestate_settings' ) ) && current_user_can( 'edit_user', $user->ID ) ) {
			$user = get_userdata( $user->ID );
			?>
			<hr class="clearfix clear">
			<table class="property-table">
				<tbody>
				<tr>
					<th>
						<?php esc_html_e( 'Opalestate API Keys', 'opalestate-pro' ); ?>
					</th>
					<td>
						<?php
						$public_key = $this->get_user_public_key( $user->ID );
						$secret_key = $this->get_user_secret_key( $user->ID );
						?>
						<?php if ( empty( $user->opalestate_user_public_key ) ) { ?>
							<input name="opalestate_set_api_key" type="checkbox" id="opalestate_set_api_key" value="0"/>
							<span class="description"><?php esc_html_e( 'Generate API Key', 'opalestate-pro' ); ?></span>
						<?php } else { ?>
							<strong style="display:inline-block; width: 125px;"><?php esc_html_e( 'Public key:', 'opalestate-pro' ); ?>&nbsp;</strong>
							<input type="text" disabled="disabled" class="regular-text" id="publickey" value="<?php echo esc_attr( $public_key ); ?>"/>
							<br/>
							<strong style="display:inline-block; width: 125px;"><?php esc_html_e( 'Secret key:', 'opalestate-pro' ); ?>&nbsp;</strong>
							<input type="text" disabled="disabled" class="regular-text" id="privatekey" value="<?php echo esc_attr( $secret_key ); ?>"/>
							<br/>
							<strong style="display:inline-block; width: 125px;"><?php esc_html_e( 'Token:', 'opalestate-pro' ); ?>&nbsp;</strong>
							<input type="text" disabled="disabled" class="regular-text" id="token" value="<?php echo esc_attr( $this->get_token( $user->ID ) ); ?>"/>
							<br/>
							<input name="opalestate_set_api_key" type="checkbox" id="opalestate_set_api_key" value="0"/>
							<span class="description"><label for="opalestate_set_api_key"><?php esc_html_e( 'Revoke API Keys', 'opalestate-pro' ); ?></label></span>
						<?php } ?>
					</td>
				</tr>
				</tbody>
			</table>
		<?php }
	}

	/**
	 * Process an API key generation/revocation
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public function process_api_key( $args ) {
		
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'opalestate-api-nonce' ) ) {

			wp_die( esc_html__( 'Nonce verification failed.', 'opalestate-pro' ), esc_html__( 'Error', 'opalestate-pro' ), array( 'response' => 403 ) );

		}

		if ( empty( $args['user_id'] ) ) {
			wp_die( esc_html__( 'User ID Required.', 'opalestate-pro' ), esc_html__( 'Error', 'opalestate-pro' ), array( 'response' => 401 ) );
		}

		if ( is_numeric( $args['user_id'] ) ) {
			$user_id = isset( $args['user_id'] ) ? absint( $args['user_id'] ) : get_current_user_id();
		} else {
			$userdata = get_user_by( 'login', $args['user_id'] );
			$user_id  = $userdata->ID;
		}
		$process = isset( $args['opalestate_api_process'] ) ? strtolower( $args['opalestate_api_process'] ) : false;

		if ( $user_id == get_current_user_id() && ! opalestate_get_option( 'allow_user_api_keys' ) && ! current_user_can( 'manage_opalestate_settings' ) ) {
			wp_die(
				sprintf(
					/* translators: %s: process */
					esc_html__( 'You do not have permission to %s API keys for this user.', 'opalestate-pro' ),
					$process
				),
				esc_html__( 'Error', 'opalestate-pro' ),
				array( 'response' => 403 )
			);
		} elseif ( ! current_user_can( 'manage_opalestate_settings' ) ) {
			wp_die(
				sprintf(
					/* translators: %s: process */
					esc_html__( 'You do not have permission to %s API keys for this user.', 'opalestate-pro' ),
					$process
				),
				esc_html__( 'Error', 'opalestate-pro' ),
				array( 'response' => 403 )
			);
		}

		switch ( $process ) {
			case 'generate':
				if ( $this->generate_api_key( $user_id ) ) {
					delete_transient( 'opalestate_total_api_keys' );
					wp_redirect( add_query_arg( 'opalestate-message', 'api-key-generated', 'edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api' ) );
					exit();
				} else {
					wp_redirect( add_query_arg( 'opalestate-message', 'api-key-failed', 'edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api' ) );
					exit();
				}
				break;
			case 'regenerate':
				$this->generate_api_key( $user_id, true );
				delete_transient( 'opalestate_total_api_keys' );
				wp_redirect( add_query_arg( 'opalestate-message', 'api-key-regenerated', 'edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api' ) );
				exit();
				break;
			case 'revoke':
				$this->revoke_api_key( $user_id );
				delete_transient( 'opalestate_total_api_keys' );
				wp_redirect( add_query_arg( 'opalestate-message', 'api-key-revoked', 'edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api' ) );
				exit();
				break;
			default;
				break;
		}
	}

	/**
	 * Generate new API keys for a user
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $user_id User ID the key is being generated for
	 * @param boolean $regenerate Regenerate the key for the user
	 *
	 * @return boolean True if (re)generated succesfully, false otherwise.
	 */
	public function generate_api_key( $user_id = 0, $regenerate = false ) {

		if ( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		$public_key = $this->get_user_public_key( $user_id );
		$secret_key = $this->get_user_secret_key( $user_id );

		if ( empty( $public_key ) || $regenerate == true ) {
			$new_public_key = $this->generate_public_key( $user->user_email );
			$new_secret_key = $this->generate_private_key( $user->ID );
		} else {
			return false;
		}

		if ( $regenerate == true ) {
			$this->revoke_api_key( $user->ID );
		}

		update_user_meta( $user_id, $new_public_key, 'opalestate_user_public_key' );
		update_user_meta( $user_id, $new_secret_key, 'opalestate_user_secret_key' );

		return true;
	}

	/**
	 * Revoke a users API keys
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $user_id User ID of user to revoke key for
	 *
	 * @return string
	 */
	public function revoke_api_key( $user_id = 0 ) {

		if ( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		$public_key = $this->get_user_public_key( $user_id );
		$secret_key = $this->get_user_secret_key( $user_id );
		if ( ! empty( $public_key ) ) {
			delete_transient( md5( 'opalestate_api_user_' . $public_key ) );
			delete_transient( md5( 'opalestate_api_user_public_key' . $user_id ) );
			delete_transient( md5( 'opalestate_api_user_secret_key' . $user_id ) );
			delete_user_meta( $user_id, $public_key );
			delete_user_meta( $user_id, $secret_key );
		} else {
			return false;
		}

		return true;
	}

	public function get_version() {
		return self::VERSION;
	}


	/**
	 * Generate and Save API key
	 *
	 * Generates the key requested by user_key_field and stores it in the database
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $user_id
	 *
	 * @return void
	 */
	public function update_key( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['opalestate_set_api_key'] ) ) {

			$user = get_userdata( $user_id );

			$public_key = $this->get_user_public_key( $user_id );
			$secret_key = $this->get_user_secret_key( $user_id );

			if ( empty( $public_key ) ) {
				$new_public_key = $this->generate_public_key( $user->user_email );
				$new_secret_key = $this->generate_private_key( $user->ID );

				update_user_meta( $user_id, $new_public_key, 'opalestate_user_public_key' );
				update_user_meta( $user_id, $new_secret_key, 'opalestate_user_secret_key' );
			} else {
				$this->revoke_api_key( $user_id );
			}
		}
	}

	/**
	 * Generate the public key for a user
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @param string $user_email
	 *
	 * @return string
	 */
	private function generate_public_key( $user_email = '' ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );

		return $public;
	}

	/**
	 * Generate the secret key for a user
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	private function generate_private_key( $user_id = 0 ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );

		return $secret;
	}

	/**
	 * Retrieve the user's token
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	public function get_token( $user_id = 0 ) {
		return hash( 'md5', $this->get_user_secret_key( $user_id ) . $this->get_user_public_key( $user_id ) );
	}

	/**
	 * API Key Backwards Compatibility
	 *
	 * A Backwards Compatibility call for the change of meta_key/value for users API Keys
	 *
	 * @since  1.3.6
	 *
	 * @param  string $check     Whether to check the cache or not
	 * @param  int    $object_id The User ID being passed
	 * @param  string $meta_key  The user meta key
	 * @param  bool   $single    If it should return a single value or array
	 *
	 * @return string            The API key/secret for the user supplied
	 */
	public function api_key_backwards_compat( $check, $object_id, $meta_key, $single ) {

		if ( $meta_key !== 'opalestate_user_public_key' && $meta_key !== 'opalestate_user_secret_key' ) {
			return $check;
		}

		$return = $check;

		switch ( $meta_key ) {
			case 'opalestate_user_public_key':
				$return = Opalestate()->api->get_user_public_key( $object_id );
				break;
			case 'opalestate_user_secret_key':
				$return = Opalestate()->api->get_user_secret_key( $object_id );
				break;
		}

		if ( ! $single ) {
			$return = array( $return );
		}

		return $return;

	}

}
