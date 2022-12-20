<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
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
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_fields'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
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
    public function get_fields($request) {
        $response = [];

        $fields              = [];
        $fields['min_price'] = [
            'enable'  => opalestate_is_enable_price_field(),
            'default' => opalestate_options('search_min_price', 0),
        ];

        $fields['max_price'] = [
            'enable'  => opalestate_is_enable_price_field(),
            'default' => opalestate_options('search_max_price', 10000000),
        ];

        $fields['min_area'] = [
            'enable'  => opalestate_is_enable_areasize_field(),
            'default' => opalestate_options('search_min_area', 0),
        ];

        $fields['max_area'] = [
            'enable'  => opalestate_is_enable_areasize_field(),
            'default' => opalestate_options('search_max_area', 1000),
        ];

        $fields['search_text'] = [
            'default' => '',
        ];

        $fields['location_text'] = [
            'default' => '',
        ];

        $fields['geo_long'] = [
            'default' => '',
        ];

        $fields['geo_lat'] = [
            'default' => '',
        ];

        $fields['types'] = [
            'enable'  => true,
            'default' => -1,
            'data'    => Opalestate_Taxonomy_Type::get_list(),
        ];

        $fields['status'] = [
            'enable'  => true,
            'default' => -1,
            'data'    => Opalestate_Taxonomy_Status::get_list(),
        ];

        $fields['cat'] = [
            'enable'  => true,
            'default' => -1,
            'data'    => Opalestate_Taxonomy_Categories::get_list(),
        ];

        $fields['location'] = [
            'enable'  => true,
            'default' => -1,
            'data'    => Opalestate_Taxonomy_Location::get_list(),
        ];

        $fields['city'] = [
            'enable'  => true,
            'default' => -1,
            'data'    => Opalestate_Taxonomy_City::get_list(),
        ];

        $fields['state'] = [
            'enable'  => true,
            'default' => -1,
            'data'    => Opalestate_Taxonomy_State::get_list(),
        ];

        $fields['amenities'] = [
            'enable'  => true,
            'default' => [],
            'data'    => Opalestate_Taxonomy_Amenities::get_list(),
        ];

        $info_fields = OpalEstate_Search::get_setting_search_fields();
        $info        = [];
        if ($info_fields) {
            foreach ($info_fields as $field_key => $field_name) {
                $info[] = [
                    'key'  => $field_key,
                    'name' => $field_name,
                ];
            }
        }

        $fields['info']     = $info;
        $response['fields'] = $fields;

        return $this->get_response(200, $response);
    }
}
