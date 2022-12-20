<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class      Agent_Api
 *
 * @since      1.0.0
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
class Opalestate_Agent_Api extends Opalestate_Base_API {

    /**
     * The unique identifier of the route resource.
     *
     * @since    1.0.0
     * @access   public
     * @var      string $base .
     */
    public $base = '/agents';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'opalestate_agent';

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
         * Get list of agents.
         *
         * Call http://domain.com/wp-json/estate-api/v1/agents
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
     * Get List Of agents.
     *
     * Based on request to get collection
     *
     * @return WP_REST_Response is json data
     * @since 1.0
     *
     */
    public function get_items($request) {
        $agents['agents'] = [];

        $per_page = isset($request['per_page']) && $request['per_page'] ? $request['per_page'] : 5;
        $paged    = isset($request['page']) && $request['page'] ? $request['page'] : 1;

        $agent_list = get_posts([
            'post_type'        => $this->post_type,
            'posts_per_page'   => $per_page,
            'paged'            => $paged,
            'suppress_filters' => true,
        ]);

        if ($agent_list) {
            $i = 0;
            foreach ($agent_list as $agent_info) {
                $agents['agents'][$i] = $this->get_agent_data($agent_info);
                $i++;
            }
        } else {
            return $this->get_response(404, ['collection' => [], 'message' => esc_html__('Not found!', 'opalestate-pro')]);
        }

        $response['collection'] = $agents['agents'];

        return $this->get_response(200, $response);
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
    public function get_item($request) {
        $response = [];
        if ($request['id'] > 0) {
            $post = get_post($request['id']);
            if ($post && $this->post_type == get_post_type($request['id'])) {
                $agent             = $this->get_agent_data($post);
                $response['agent'] = $agent ? $agent : [];
                $code              = 200;
            } else {
                $code              = 404;
                $response['error'] = sprintf(esc_html__('Agent ID: %s does not exist!', 'opalestate-pro'), $request['id']);
            }
        } else {
            $code              = 404;
            $response['error'] = sprintf(esc_html__('Invalid ID.', 'opalestate-pro'), $request['id']);
        }

        return $this->get_response($code, $response);
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

                $user_id = get_post_meta($request['id'], OPALESTATE_AGENT_PREFIX . 'user_id', true);

                $args = [
                    'post_type'      => 'opalestate_property',
                    'posts_per_page' => $per_page,
                    'post__not_in'   => [$request['id']],
                    'paged'          => $paged,
                ];

                $args['meta_query'] = ['relation' => 'AND'];

                if ($user_id) {
                    $args['author'] = $user_id;
                } else {
                    array_push($args['meta_query'], [
                        'key'     => OPALESTATE_PROPERTY_PREFIX . 'agent',
                        'value'   => $request['id'],
                        'compare' => '=',
                    ]);
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
                $response['error'] = sprintf(esc_html__('Agent ID: %s does not exist!', 'opalestate-pro'), $request['id']);
            }
        } else {
            $code              = 404;
            $response['error'] = sprintf(esc_html__('Invalid ID.', 'opalestate-pro'), $request['id']);
        }

        return $this->get_response($code, $response);
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
    public function get_agent_data($agent_info) {
        $agent                  = new OpalEstate_Agent($agent_info->ID);
        $ouput['id']            = $agent_info->ID;
        $ouput['name']          = $agent_info->post_title;
        $ouput['slug']          = $agent_info->post_name;
        $ouput['created_date']  = $agent_info->post_date;
        $ouput['modified_date'] = $agent_info->post_modified;
        $ouput['status']        = $agent_info->post_status;
        $ouput['permalink']     = html_entity_decode($agent_info->guid);
        $ouput['content']       = $agent_info->post_content;
        $ouput['avatar']        = $agent->get_meta('avatar');
        $ouput['thumbnail']     = wp_get_attachment_url(get_post_thumbnail_id($agent_info->ID));
        $ouput['featured']      = $agent->is_featured();
        $ouput['trusted']       = $agent->get_trusted();
        $ouput['email']         = $agent->get_meta('email');
        $ouput['website']       = $agent->get_meta('website');
        $ouput['phone']         = $agent->get_meta('phone');
        $ouput['mobile']        = $agent->get_meta('mobile');
        $ouput['fax']           = $agent->get_meta('fax');
        $ouput['job']           = $agent->get_meta('job');
        $ouput['company']       = $agent->get_meta('company');
        $ouput['address']       = $agent->get_meta('address');
        $ouput['map']           = $agent->get_meta('map');

        $terms                  = wp_get_post_terms($agent_info->ID, 'opalestate_agent_location');
        $ouput['location']      = $terms && !is_wp_error($terms) ? $terms : [];
        $ouput['socials']       = $agent->get_socials();
        $ouput['levels']        = wp_get_post_terms($agent_info->ID, 'opalestate_agent_level');
        $properties             = $this->get_properties($agent_info->ID);
        $ouput['listing_count'] = count($properties);
        $ouput['listing']       = $properties;

        return apply_filters('opalestate_api_agents', $ouput);
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
     * @param int $id Agent ID
     * @return array
     */
    public function get_properties($id) {
        $properties = [];
        if ($id > 0) {
            $post = get_post($id);
            if ($post && $this->post_type == get_post_type($id)) {

                $user_id = get_post_meta($id, OPALESTATE_AGENT_PREFIX . 'user_id', true);

                $args = [
                    'post_type'      => 'opalestate_property',
                    'posts_per_page' => -1,
                    'post__not_in'   => [$id],
                    'post_status'    => 'publish',
                ];

                $args['meta_query'] = ['relation' => 'AND'];

                if ($user_id) {
                    $args['author'] = $user_id;
                } else {
                    array_push($args['meta_query'], [
                        'key'     => OPALESTATE_PROPERTY_PREFIX . 'agent',
                        'value'   => $id,
                        'compare' => '=',
                    ]);
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
