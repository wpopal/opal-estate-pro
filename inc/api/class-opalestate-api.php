<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opalestate_API
 *
 * @since      1.0.0
 * @package    Opalestate
 */
class Opalestate_API {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_base_name The string used to uniquely identify this plugin.
	 */
	public $base = 'estate-api';

	public function __construct() {
		$this->init();

		if ( is_admin() ) {
			new Opalestate_API_Admin();
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
	public function init() {
		$this->includes( [
			'class-opalestate-api-admin.php',
			'class-opalestate-base-api.php',
			'v1/property.php',
			'v1/agent.php',
			'v1/agency.php',
			'v1/search-form.php',
			'class-opalestate-api-auth.php',
			'functions.php',
		] );

		add_action( 'rest_api_init', [ $this, 'register_resources' ] );
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
	public function register_resources() {
		$api_classes = apply_filters( 'opalestate_api_classes',
			[
				'Opalestate_Property_Api',
				'Opalestate_Agent_Api',
				'Opalestate_Agency_Api',
				'Opalestate_Search_Form_Api',
			]
		);

		foreach ( $api_classes as $api_class ) {
			$api_class = new $api_class();
			$api_class->register_routes();
		}
	}
}
