<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Property_Api
 *
 * @package    Property_Api
 */
class Opalestate_Property_Api extends Opalestate_Base_API {

    /**
     * The unique identifier of the route resource.
     *
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
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ],
                // [
                // 	'methods'  => WP_REST_Server::CREATABLE,
                // 	'callback' => [ $this, 'create_item' ],
                // 	'permission_callback' => [ $this, 'create_item_permissions_check' ],
                // ],
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
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
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

        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/search',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_results'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_search_params(),
                ],
            ]
        );
    }

    /**
     * Get object.
     *
     * @param int $id Object ID.
     *
     * @return Opalestate_Property
     */
    protected function get_object($id) {
        return opalesetate_property($id);
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
    public function get_items($request) {
        $properties = [];

        $per_page = isset($request['per_page']) && $request['per_page'] ? $request['per_page'] : 5;
        $paged    = isset($request['page']) && $request['page'] ? $request['page'] : 1;

        $property_list = get_posts([
            'post_type'        => $this->post_type,
            'posts_per_page'   => $per_page,
            'paged'            => $paged,
            'suppress_filters' => true,
        ]);

        if ($property_list) {
            $i = 0;
            foreach ($property_list as $property_info) {
                $properties[$i] = $this->get_property_data($property_info);
                $i++;
            }
        } else {
            return $this->get_response(404, ['collection' => [], 'message' => esc_html__('Not found!', 'opalestate-pro')]);
        }

        $response['collection'] = $properties;

        return $this->get_response(200, $response);
    }

    /**
     * Get a property
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item($request) {
        $response = [];
        if ($request['id'] > 0) {
            $post = get_post($request['id']);
            if ($post && $this->post_type == get_post_type($request['id'])) {
                $property             = $this->get_property_data($post);
                $response['property'] = $property ? $property : [];
                $code                 = 200;
            } else {
                $code              = 404;
                $response['error'] = sprintf(esc_html__('Property ID: %s does not exist!', 'opalestate-pro'), $request['id']);
            }
        } else {
            $code              = 404;
            $response['error'] = sprintf(esc_html__('Invalid ID.', 'opalestate-pro'), $request['id']);
        }

        return $this->get_response($code, $response);
    }

    /**
     * Update a property.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update_item($request) {
        $id = absint($request['id']);

        $property = get_post($id);
        if (!$property || $this->post_type != $property->post_type) {
            $code              = 404;
            $response['error'] = sprintf(esc_html__('Property ID: %s does not exist!', 'opalestate-pro'), $id);
        } else {

        }

        return $this->get_response($code, $response);
    }

    /**
     * Delete a property.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function delete_item($request) {
        $id    = (int)$request['id'];
        $force = (bool)$request['force'];

        $property = get_post(absint($request['id']));
        if (!$property || $this->post_type != $property->post_type) {
            $response['test'] = 0;
        } else {
            wp_delete_post(absint($request['id']));
            $response['test'] = 1;
        }

        return $response;
    }

    public function get_results($request) {
        $properties    = [];
        $property_list = $this->get_search_results_query($request);

        if ($property_list) {
            $i = 0;
            foreach ($property_list as $property_info) {
                $properties[$i] = $this->get_property_data($property_info);
                $i++;
            }
        } else {
            return $this->get_response(404, ['collection' => esc_html__('Not found', 'opalestate-pro')]);
        }

        $response['collection'] = $properties;

        return $this->get_response(200, $response);
    }

    /**
     * The opalestate_property post object, generate the data for the API output
     *
     * @param object $property_info The Download Post Object
     *
     * @return array Array of post data to return back in the API
     */
    private function get_property_data($property_info) {
        return opalestate_api_get_property_data($property_info);
    }

    /**
     * Create a single item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function create_item($request) {
        if (!empty($request['id'])) {
            /* translators: %s: post type */
            return new WP_Error("opalestate_rest_{$this->post_type}_exists", sprintf(__('Cannot create existing %s.', 'opalestate-pro'), $this->post_type), ['status' => 400]);
        }

        $data = [
            'post_title'   => $request['post_title'],
            'post_type'    => $this->post_type,
            'post_content' => $request['post_content'],
        ];

        $data['post_status'] = 'pending';
        $post_id             = wp_insert_post($data, true);

        $response['id'] = $post_id;
        $response       = rest_ensure_response($response);
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('/%s/%s/%d', $this->namespace, $this->base, $post_id)));

        return $response;
    }

    /**
     * Get Query Object to display collection of property with user request which submited via search form.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return
     */
    public function get_search_results_query($request) {
        $search_min_price = isset($request['min_price']) ? sanitize_text_field($request['min_price']) : '';
        $search_max_price = isset($request['max_price']) ? sanitize_text_field($request['max_price']) : '';
        $search_min_area  = isset($request['min_area']) ? sanitize_text_field($request['min_area']) : '';
        $search_max_area  = isset($request['max_area']) ? sanitize_text_field($request['max_area']) : '';
        $s                = isset($request['search_text']) ? sanitize_text_field($request['search_text']) : null;
        $per_page         = isset($request['per_page']) && $request['per_page'] ? $request['per_page'] : opalestate_options('search_property_per_page', 5);
        $paged            = isset($request['page']) && $request['page'] ? $request['page'] : 1;

        if (isset($request['paged']) && intval($request['paged']) > 0) {
            $paged = intval($request['paged']);
        }

        $args = [
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'post_type'      => $this->post_type,
            'post_status'    => 'publish',
            's'              => $s,
        ];

        $tax_query = [];

        if (isset($request['location']) && $request['location'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_location',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($request['location']),
            ];
        }

        if (isset($request['state']) && $request['state'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_state',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($request['state']),
            ];
        }

        if (isset($request['city']) && $request['city'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_city',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($request['city']),
            ];
        }

        if (isset($request['types']) && $request['types'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_types',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($request['types']),
            ];
        }

        if (isset($request['cat']) && $request['cat'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'property_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($request['cat']),
            ];
        }

        if (isset($request['status']) && $request['status'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_status',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($request['status']),
            ];
        }

        if (isset($request['amenities']) && is_array($request['amenities'])) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_amenities',
                'field'    => 'slug',
                'terms'    => ($request['amenities']),
            ];
        }

        if ($tax_query) {
            $args['tax_query'] = ['relation' => 'AND'];
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        $args['meta_query'] = ['relation' => 'AND'];

        if (isset($request['info'])) {
            $info_array = [];
            if (is_array($request['info'])) {
                $info_array = $request['info'];
            } elseif (is_string($request['info'])) {
                $info  = $request['info'];
                $array = json_decode($info);
                $array = json_decode(json_encode($array), true);

                if (is_array($array)) {
                    $info_array = $array;
                }
            }

            if ($info_array && !empty($info_array)) {
                $metaquery = [];
                foreach ($info_array as $key => $value) {
                    if (trim($value)) {
                        if (is_numeric(trim($value))) {
                            $fieldquery = [
                                'key'     => OPALESTATE_PROPERTY_PREFIX . $key,
                                'value'   => sanitize_text_field(trim($value)),
                                'compare' => apply_filters('opalestate_info_numeric_compare', '>='),
                                'type'    => 'NUMERIC',
                            ];
                        } else {
                            $fieldquery = [
                                'key'     => OPALESTATE_PROPERTY_PREFIX . $key,
                                'value'   => sanitize_text_field(trim($value)),
                                'compare' => 'LIKE',
                            ];
                        }
                        $sarg        = apply_filters('opalestate_search_field_query_' . $key, $fieldquery);
                        $metaquery[] = $sarg;
                    }
                }

                $args['meta_query'] = array_merge($args['meta_query'], $metaquery);
            }
        }

        if ($search_min_price != '' && $search_min_price != '' && is_numeric($search_min_price) && is_numeric($search_max_price)) {
            if ($search_min_price) {
                array_push($args['meta_query'], [
                    'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                    'value'   => [$search_min_price, $search_max_price],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                ]);
            } else {
                array_push($args['meta_query'], [
                    [
                        [
                            'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                            'compare' => 'NOT EXISTS',
                        ],
                        'relation' => 'OR',
                        [
                            'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                            'value'   => $search_max_price,
                            'compare' => '<=',
                            'type'    => 'NUMERIC',
                        ],
                    ],
                ]);
            }

        } elseif ($search_min_price != '' && is_numeric($search_min_price)) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                'value'   => $search_min_price,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ]);
        } elseif ($search_max_price != '' && is_numeric($search_max_price)) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                'value'   => $search_max_price,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ]);
        }

        if ($search_min_area != '' && $search_min_area != '' && is_numeric($search_min_area) && is_numeric($search_max_area)) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'areasize',
                'value'   => [$search_min_area, $search_max_area],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ]);
        } elseif ($search_min_area != '' && is_numeric($search_min_area)) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'areasize',
                'value'   => $search_min_area,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ]);
        } elseif ($search_max_area != '' && is_numeric($search_max_area)) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'areasize',
                'value'   => $search_max_area,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ]);
        }

        if (isset($request['geo_long']) && isset($request['geo_lat'])) {
            if ($request['location_text'] && (empty($request['geo_long']) || empty($request['geo_lat']))) {
                array_push($args['meta_query'], [
                    'key'      => OPALESTATE_PROPERTY_PREFIX . 'map_address',
                    'value'    => sanitize_text_field(trim($request['location_text'])),
                    'compare'  => 'LIKE',
                    'operator' => 'OR',
                ]);
            } elseif ($request['geo_lat'] && $request['geo_long']) {
                $radius_measure   = isset($request['radius_measure']) ? sanitize_text_field($request['radius_measure']) : 'km';
                $radius           = isset($request['geo_radius']) ? sanitize_text_field($request['geo_radius']) : 10;
                $post_ids         = Opalestate_Query::filter_by_location(sanitize_text_field($request['geo_lat']), sanitize_text_field($request['geo_long']), $radius, $radius_measure);
                $args['post__in'] = $post_ids;
            }
        }

        $ksearchs = [];

        if (isset($request['opalsortable']) && !empty($request['opalsortable'])) {
            $ksearchs = explode('_', $request['opalsortable']);
        }

        if (!empty($ksearchs) && count($ksearchs) == 2) {
            $args['meta_key'] = OPALESTATE_PROPERTY_PREFIX . $ksearchs[0];
            $args['orderby']  = 'meta_value_num';
            $args['order']    = $ksearchs[1];
        }

        $args = apply_filters('opalestate_api_get_search_results_query_args', $args);

        return get_posts($args);
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
     * Get the query params for collections of attachments.
     *
     * @return array
     */
    public function get_search_params() {
        $params = parent::get_collection_params();

        $params['min_price'] = [
            'description'       => __('Min price', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['max_price'] = [
            'description'       => __('Min price', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['min_area'] = [
            'description'       => __('Min area', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['max_area'] = [
            'description'       => __('Max area', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['search_text'] = [
            'description'       => __('Search text', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['location_text'] = [
            'description'       => __('Location text', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['geo_long'] = [
            'description'       => __('Geo long', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['geo_lat'] = [
            'description'       => __('Geo lat', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['location'] = [
            'description'       => __('Location', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['state'] = [
            'description'       => __('State', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['city'] = [
            'description'       => __('City', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['cat'] = [
            'description'       => __('Categories', 'opalestate-pro'),
            'type'              => 'array',
            // 'default'           => '',
            // 'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['types'] = [
            'description'       => __('Types', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['status'] = [
            'description'       => __('Status', 'opalestate-pro'),
            'type'              => 'string',
            // 'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['amenities'] = [
            'description'       => __('Amenities', 'opalestate-pro'),
            'type'              => 'array',
            // 'default'           => '',
            // 'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        // $params['info'] = [
        // 	'description'       => __( 'Info', 'opalestate-pro' ),
        // 	'type'              => 'array',
        // 	// 'default'           => '',
        // 	// 'sanitize_callback' => 'sanitize_text_field',
        // 	'validate_callback' => 'rest_validate_request_arg',
        // ];

        return $params;
    }
}
