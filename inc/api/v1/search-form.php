<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Property_Api
 *
 * @since      1.0.0
 * @package    Opalestate_Search_Form_Api
 */
class Opalestate_Search_Form_Api extends Opalestate_Base_API {

	/**
	 * The unique identifier of the route resource.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string $base .
	 */
	public $base = '/search-form';

	/**
	 * Register Routes
	 *
	 * Register all CURD actions with POST/GET/PUT and calling function for each
	 *
	 * @since 1.0
	 *
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'  => WP_REST_Server::READABLE,
					'callback' => [ $this, 'get_fields' ],
					// 'permission_callback' => [ $this, 'get_items_permissions_check' ],
					// 'args'     => $this->get_search_params(),
				],
			]
		);
	}

	/**
	 * Get List Of Properties
	 *
	 * Based on request to get collection
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function get_fields( $request ) {
		$response = [];

		$fields = [];

		$fields['types']     = Opalestate_Taxonomy_Type::get_list();
		$fields['status']    = Opalestate_Taxonomy_Status::get_list();
		$fields['cat']       = Opalestate_Taxonomy_Categories::get_list();
		$fields['amenities'] = Opalestate_Taxonomy_Amenities::get_list();
		$response['fields']  = $fields;

		return $this->get_response( 200, $response );
	}
}
