<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Opalestate_Terms_Api
 *
 * @package Opalestate_Terms_Api
 */
class Opalestate_Terms_Api extends Opalestate_Base_API {

    /**
     * The unique identifier of the route resource.
     *
     * @access   public
     * @var      string $base .
     */
    public $base = '/terms';

    /**
     * Register Routes
     *
     * Register all CURD actions with POST/GET/PUT and calling function for each
     */
    public function register_routes() {
        /**
         * Get list of terms.
         *
         * Call http://domain.com/wp-json/estate-api/v1/terms
         */
        register_rest_route(
            $this->namespace,
            '/' . $this->base,
            [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_items'],
                    // 'permission_callback' => [ $this, 'get_items_permissions_check' ],
                    // 'args'                => $this->get_collection_params(),
                ],
            ]
        );
    }

    /**
     * Get List Of Taxonomies
     *
     * Based on request to get collection
     *
     * @return WP_REST_Response is json data
     */
    public function get_items($request) {
        $opalestate_terms = [
            'property_category',
            'opalestate_amenities',
            'opalestate_label',
            'opalestate_status',
            'opalestate_types',
            'opalestate_location',
            'opalestate_city',
            'opalestate_state',
        ];

        $all_terms = [];
        foreach ($opalestate_terms as $term_name) {
            $all_terms[$term_name] = get_terms(apply_filters('opalestate_all_terms_api_args', [
                'taxonomy'   => $term_name,
                'hide_empty' => false,
            ]));
        }

        if (!$all_terms) {
            return $this->get_response(404, ['collection' => [], 'message' => esc_html__('Not found!', 'opalestate-pro')]);
        }

        $response['collection'] = $all_terms;

        return $this->get_response(200, $response);
    }
}
