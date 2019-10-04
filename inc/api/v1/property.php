<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Property_Api
 *
 * @since      1.0.0
 * @package    Property_Api
 */
class Opalestate_Property_Api extends Opalestate_Base_API {

	/**
	 * The unique identifier of the route resource.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string $base .
	 */
	public $base = '/properties';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'opalestate_property';

	/**
	 * Register Routes
	 *
	 * Register all CURD actions with POST/GET/PUT and calling function for each
	 *
	 * @since 1.0
	 *
	 */
	public function register_routes() {
		/**
		 * Get list of properties.
		 *
		 * Call http://domain.com/wp-json/estate-api/v1/properties
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'  => WP_REST_Server::READABLE,
					'callback' => [ $this, 'get_items' ],
					// 'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'     => $this->get_collection_params(),
				],
				// [
				// 	'methods'  => WP_REST_Server::CREATABLE,
				// 	'callback' => [ $this, 'create_item' ],
				// 	// 'permission_callback' => [ $this, 'create_item_permissions_check' ],
				// ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[\d]+)',
			[
				'args' => [
					'id' => [
						'description' => __( 'Unique identifier for the resource.', 'opalestate-pro' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'  => WP_REST_Server::READABLE,
					'callback' => [ $this, 'get_item' ],
					// 'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				[
					'methods'  => WP_REST_Server::EDITABLE,
					'callback' => [ $this, 'update_item' ],
					// 'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
				// [
				// 	'methods'  => WP_REST_Server::DELETABLE,
				// 	'callback' => [ $this, 'delete_item' ],
				// 	// 'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				// 	'args'     => [
				// 		'force' => [
				// 			'default'     => false,
				// 			'description' => __( 'Whether to bypass trash and force deletion.', 'opalestate-pro' ),
				// 			'type'        => 'boolean',
				// 		],
				// 	],
				// ],
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
	public function get_items( $request ) {
		$properties = [];

		$per_page = isset( $request['per_page'] ) && $request['per_page'] ? $request['per_page'] : 5;
		$paged    = isset( $request['page'] ) && $request['page'] ? $request['page'] : 1;

		$property_list = get_posts( [
			'post_type'        => $this->post_type,
			'posts_per_page'   => $per_page,
			'paged'            => $paged,
			'suppress_filters' => true,
		] );

		if ( $property_list ) {
			$i = 0;
			foreach ( $property_list as $property_info ) {
				$properties[ $i ] = $this->get_property_data( $property_info );
				$i++;
			}
		}

		$response['collection'] = $properties;

		return $this->get_response( 200, $response );
	}

	/**
	 * Get Property
	 *
	 * Based on request to get a property.
	 *
	 * @return WP_REST_Response is json data
	 * @since 1.0
	 *
	 */
	public function get_item( $request ) {
		$response = [];
		if ( $request['id'] > 0 ) {
			$post = get_post( $request['id'] );
			if ( $post && $this->post_type == get_post_type( $request['id'] ) ) {
				$property             = $this->get_property_data( $post );
				$response['property'] = $property ? $property : [];
				$code                 = 200;
			} else {
				$code              = 404;
				$response['error'] = sprintf( esc_html__( 'Property ID: %s does not exist!', 'opalestate-pro' ), $request['id'] );
			}
		} else {
			$code              = 404;
			$response['error'] = sprintf( esc_html__( 'Invalid ID.', 'opalestate-pro' ), $request['id'] );
		}

		return $this->get_response( $code, $response );
	}

	public function delete_item( $request ) {
		$id     = (int) $request['id'];
		$force  = (bool) $request['force'];

		$property = get_post( absint( $request['id'] ) );
		if ( ! $property || $this->post_type != $property->post_type ) {
			$response['test'] = 0;
		} else {
			wp_delete_post( absint( $request['id'] ) );
			$response['test'] = 1;
		}

		return $response;
	}

	/**
	 * The opalestate_property post object, generate the data for the API output
	 *
	 * @param object $property_info The Download Post Object
	 *
	 * @return array                Array of post data to return back in the API
	 * @since  1.0
	 *
	 */
	private function get_property_data( $property_info ) {
		return opalestate_api_get_property_data( $property_info );
	}

	/**
	 * Create a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			/* translators: %s: post type */
			return new WP_Error( "opalestate_rest_{$this->post_type}_exists", sprintf( __( 'Cannot create existing %s.', 'opalestate-pro' ), $this->post_type ), [ 'status' => 400 ] );
		}

		$data = [
			'post_title'   => $request['post_title'],
			'post_type'    => $this->post_type,
			'post_content' => $request['post_content'],
		];

		$data['post_status'] = 'pending';
		$post_id             = wp_insert_post( $data, true );

		$response['id'] = $post_id;
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->base, $post_id ) ) );

		return $response;
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		return $params;
	}
}
