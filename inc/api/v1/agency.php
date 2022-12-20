<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class      Job_Api
 *
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
class Opalestate_Agency_Api extends Opalestate_Base_API {

    /**
     * The unique identifier of the route resource.
     *
     * @access   public
     * @var      string $base .
     */
    public $base = '/agencies';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'opalestate_agency';

    /**
     * Register Routes
     *
     * Register all CURD actions with POST/GET/PUT and calling function for each
     */
    public function register_routes() {
        /**
         * Get list of agencies.
         *
         * Call http://domain.com/wp-json/estate-api/v1/agencies
         */
        register_rest_route(
            $this->namespace,
            '/' . $this->base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/(?P<id>[\d]+)',
            [
                'args' => [
                    'id' => [
                        'description' => __('Unique identifier for the resource.', 'opalestate-pro'),
                        'type'        => 'integer',
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                ],
                // [
                // 	'methods'  => WP_REST_Server::EDITABLE,
                // 	'callback' => [ $this, 'update_item' ],
                // 	// 'permission_callback' => [ $this, 'update_item_permissions_check' ],
                // ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/listings/(?P<id>[\d]+)',
            [
                'args' => [
                    'id' => [
                        'description' => __('Unique identifier for the resource.', 'opalestate-pro'),
                        'type'        => 'integer',
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_listings'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                ],
            ]
        );
    }

    /**
     * Get object.
     *
     * @param int $id Object ID.
     *
     * @return Opalestate_Agency
     */
    protected function get_object($id) {
        return opalesetate_agency($id);
    }

    /**
     * Get List Of agencies.
     *
     * Based on request to get collection
     *
     * @return WP_REST_Response is json data
     */
    public function get_items($request) {
        $agencies['agencies'] = [];

        $per_page = isset($request['per_page']) && $request['per_page'] ? $request['per_page'] : 5;
        $paged    = isset($request['page']) && $request['page'] ? $request['page'] : 1;

        $agency_list = get_posts([
            'post_type'        => $this->post_type,
            'posts_per_page'   => $per_page,
            'paged'            => $paged,
            'suppress_filters' => true,
        ]);

        if ($agency_list) {
            $i = 0;
            foreach ($agency_list as $agency_info) {
                $agencies['agencies'][$i] = $this->get_agency_data($agency_info);
                $i++;
            }
        } else {
            return $this->get_response(404, ['collection' => [], 'message' => esc_html__('Not found!', 'opalestate-pro')]);
        }

        $response['collection'] = $agencies['agencies'];

        return $this->get_response(200, $response);
    }

    /**
     * Get Agency
     *
     * Based on request to get a agency.
     *
     * @return WP_REST_Response is json data
     */
    public function get_item($request) {
        $response = [];
        if ($request['id'] > 0) {
            $post = get_post($request['id']);
            if ($post && $this->post_type == get_post_type($request['id'])) {
                $agency             = $this->get_agency_data($post);
                $response['agency'] = $agency ? $agency : [];
                $code               = 200;
            } else {
                $code              = 404;
                $response['error'] = sprintf(esc_html__('Agency ID: %s does not exist!', 'opalestate-pro'), $request['id']);
            }
        } else {
            $code              = 404;
            $response['error'] = sprintf(esc_html__('Invalid ID.', 'opalestate-pro'), $request['id']);
        }

        return $this->get_response($code, $response);
    }

    /**
     * The opalestate_agency post object, generate the data for the API output
     *
     * @param object $agency_info The Download Post Object
     *
     * @return array Array of post data to return back in the API
     */
    public function get_agency_data($agency_info) {
        $agency                 = new OpalEstate_Agency($agency_info->ID);
        $ouput['id']            = $agency_info->ID;
        $ouput['slug']          = $agency_info->post_name;
        $ouput['name']          = $agency_info->post_title;
        $ouput['create_date']   = $agency_info->post_date;
        $ouput['modified_date'] = $agency_info->post_modified;
        $ouput['status']        = $agency_info->post_status;
        $ouput['link']          = html_entity_decode($agency_info->guid);
        $ouput['content']       = $agency_info->post_content;
        $ouput['avatar']        = $agency->get_meta('avatar');
        $ouput['thumbnail']     = wp_get_attachment_url(get_post_thumbnail_id($agency_info->ID));
        $ouput['featured']      = $agency->is_featured();
        $ouput['trusted']       = $agency->get_trusted();
        $ouput['email']         = $agency->get_meta('email');
        $ouput['website']       = $agency->get_meta('website');
        $ouput['phone']         = $agency->get_meta('phone');
        $ouput['mobile']        = $agency->get_meta('mobile');
        $ouput['fax']           = $agency->get_meta('fax');
        $ouput['address']       = $agency->get_meta('address');
        $ouput['map']           = $agency->get_meta('map');
        $terms                  = wp_get_post_terms($agency_info->ID, 'opalestate_agency_location');
        $ouput['location']      = $terms && !is_wp_error($terms) ? $terms : [];
        $ouput['socials']       = $agency->get_socials();
        $properties             = $this->get_properties($agency_info->ID);
        $ouput['listing_count'] = count($properties);
        $ouput['listings']      = $properties;

        return apply_filters('opalestate_api_agencies', $ouput);
    }

    /**
     * Get agent listings.
     *
     * @param $request
     * @return \WP_REST_Response
     */
    public function get_listings($request) {
        if ($request['id'] > 0) {
            $post = get_post($request['id']);
            if ($post && $this->post_type == get_post_type($request['id'])) {
                $per_page = isset($request['per_page']) && $request['per_page'] ? $request['per_page'] : 5;
                $paged    = isset($request['page']) && $request['page'] ? $request['page'] : 1;

                $user_id = get_post_meta($request['id'], OPALESTATE_AGENCY_PREFIX . 'user_id', true);
                $agents  = get_post_meta($request['id'], OPALESTATE_AGENCY_PREFIX . 'team', true);

                if ($user_id) {
                    $author = [$user_id];
                    $agents = get_post_meta($request['id'], OPALESTATE_AGENCY_PREFIX . 'team', true);

                    if (is_array($agents)) {
                        $author = array_merge($author, $agents);
                    }

                    $args = [
                        'post_type'      => 'opalestate_property',
                        'author__in'     => $author,
                        'posts_per_page' => $per_page,
                        'paged'          => $paged,
                    ];
                } else {

                    $args               = [
                        'post_type'      => 'opalestate_property',
                        'posts_per_page' => $per_page,
                        'paged'          => $paged,
                    ];
                    $args['meta_query'] = ['relation' => 'OR'];
                    array_push($args['meta_query'], [
                        'key'     => OPALESTATE_PROPERTY_PREFIX . 'agency',
                        'value'   => $request['id'],
                        'compare' => '=',
                    ]);

                    if ($agents) {
                        array_push($args['meta_query'], [
                            'key'   => OPALESTATE_PROPERTY_PREFIX . 'agent',
                            'value' => $agents,
                        ]);
                    }
                }

                $property_list = get_posts($args);

                if ($property_list) {
                    $i = 0;
                    foreach ($property_list as $property_info) {
                        $properties[$i] = opalestate_api_get_property_data($property_info);
                        $i++;
                    }
                }

                $response['listings'] = $properties ? $properties : [];
                $code                 = 200;
            } else {
                $code              = 404;
                $response['error'] = sprintf(esc_html__('Agency ID: %s does not exist!', 'opalestate-pro'), $request['id']);
            }
        } else {
            $code              = 404;
            $response['error'] = sprintf(esc_html__('Invalid ID.', 'opalestate-pro'), $request['id']);
        }

        return $this->get_response($code, $response);
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

    /**
     * Get agent listings.
     *
     * @param int $id Agency ID.
     * @return array
     */
    public function get_properties($id) {
        $properties = [];
        if ($id > 0) {
            $post = get_post($id);
            if ($post && $this->post_type == get_post_type($id)) {
                $user_id = get_post_meta($id, OPALESTATE_AGENCY_PREFIX . 'user_id', true);
                $agents  = get_post_meta($id, OPALESTATE_AGENCY_PREFIX . 'team', true);

                if ($user_id) {
                    $author = [$user_id];
                    $agents = get_post_meta($id, OPALESTATE_AGENCY_PREFIX . 'team', true);

                    if (is_array($agents)) {
                        $author = array_merge($author, $agents);
                    }

                    $args = [
                        'post_type'      => 'opalestate_property',
                        'author__in'     => $author,
                        'posts_per_page' => -1,
                    ];
                } else {
                    $args               = [
                        'post_type'      => 'opalestate_property',
                        'posts_per_page' => -1,
                    ];
                    $args['meta_query'] = ['relation' => 'OR'];
                    array_push($args['meta_query'], [
                        'key'     => OPALESTATE_PROPERTY_PREFIX . 'agency',
                        'value'   => $id,
                        'compare' => '=',
                    ]);

                    if ($agents) {
                        array_push($args['meta_query'], [
                            'key'   => OPALESTATE_PROPERTY_PREFIX . 'agent',
                            'value' => $agents,
                        ]);
                    }
                }

                $property_list = get_posts($args);

                if ($property_list) {
                    $i = 0;
                    foreach ($property_list as $property_info) {
                        $properties[$i] = opalestate_api_get_property_data($property_info);
                        $i++;
                    }
                }

            }
        }

        return $properties;
    }
}
