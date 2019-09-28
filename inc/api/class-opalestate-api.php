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
// namespace Opal_Job\API;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use Opal_Job\API\Api_Auth;
use Opal_Job\API\API_Admin;

/**
 * Abstract class to define/implement base methods for all controller classes
 *
 * @since      1.0.0
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
class Opalestate_API {
	
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_base_name The string used to uniquely identify this plugin.
	 */	
	public $base = 'job-api';

	public function __construct () {
		return $this->init();
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
	public function init() {
		
		$this->includes( [
			'class-base-api.php',
			'v1/property.php',
			'v1/agent.php',
			'v1/agency.php',
			'class-api-auth.php'
		] );			

		add_action( 'rest_api_init', [$this,'register_resources'] ); 
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
		add_rewrite_endpoint( $this->base, EP_ALL );
	}

	/**
	 * Include list of collection files
	 *
	 * @var array $files
	 */
	public function includes( $files ) {
		foreach ( $files as $file ) {
			$file = OPALESTATE_PLUGIN_DIR . 'inc/api/' . $file;
			include_once $file;
		}
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
	public function register_resources (  ) {   
 
		$api_classes = apply_filters( 'opaljob_api_classes',
			array(
				'Property_Api',
				'Agent_Api',
				'Agency_Api'
			)
		);

	 
		foreach ( $api_classes as $api_class ) { 
			$api_class = new $api_class( );
			$api_class->register_routes();
		}
	}

}