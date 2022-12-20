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
class Opalestate_User_Api extends Opalestate_Base_API {

    /**
     * The unique identifier of the route resource.
     *
     * @access   public
     * @var      string $base .
     */
    public $base = '/users';

    /**
     * Register Routes
     *
     * Register all CURD actions with POST/GET/PUT and calling function for each
     */
    public function register_routes() {
        /**
         * Get list of properties.
         *
         * Call http://domain.com/wp-json/estate-api/v1/users
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
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
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
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/(?P<id>[\d]+)/favorites',
            [
                'args' => [
                    'id' => [
                        'description' => __('Unique identifier for the resource.', 'opalestate-pro'),
                        'type'        => 'integer',
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_favorites'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                ],
            ]
        );
    }

    /**
     * Check whether a given request has permission to read customers.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_items_permissions_check($request) {
        if (!opalestate_rest_check_user_permissions('read')) {
            return new WP_Error('opalestate_rest_cannot_view', __('Sorry, you cannot list resources.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Check if a given request has access create customers.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function create_item_permissions_check($request) {
        if (!opalestate_rest_check_user_permissions('create')) {
            return new WP_Error('opalestate_rest_cannot_create', __('Sorry, you are not allowed to create resources.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Check if a given request has access to read a customer.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_item_permissions_check($request) {
        $id = (int)$request['id'];

        if (!opalestate_rest_check_user_permissions('read', $id)) {
            return new WP_Error('opalestate_rest_cannot_view', __('Sorry, you cannot view this resource.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Check if a given request has access update a customer.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function update_item_permissions_check($request) {
        $id = (int)$request['id'];

        if (!opalestate_rest_check_user_permissions('edit', $id)) {
            return new WP_Error('opalestate_rest_cannot_edit', __('Sorry, you are not allowed to edit this resource.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Check if a given request has access delete a customer.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function delete_item_permissions_check($request) {
        $id = (int)$request['id'];

        if (!opalestate_rest_check_user_permissions('delete', $id)) {
            return new WP_Error('opalestate_rest_cannot_delete', __('Sorry, you are not allowed to delete this resource.', 'opalestate-pro'), [
                'status' => rest_authorization_required_code(),
            ]);
        }

        return true;
    }

    /**
     * Get all users.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request) {
        $prepared_args            = [];
        $prepared_args['exclude'] = $request['exclude'];
        $prepared_args['include'] = $request['include'];
        $prepared_args['order']   = $request['order'];
        $prepared_args['number']  = $request['per_page'];
        if (!empty($request['offset'])) {
            $prepared_args['offset'] = $request['offset'];
        } else {
            $prepared_args['offset'] = ($request['page'] - 1) * $prepared_args['number'];
        }
        $orderby_possibles        = [
            'id'              => 'ID',
            'include'         => 'include',
            'name'            => 'display_name',
            'registered_date' => 'registered',
        ];
        $prepared_args['orderby'] = $orderby_possibles[$request['orderby']];
        $prepared_args['search']  = $request['search'];

        if ('' !== $prepared_args['search']) {
            $prepared_args['search'] = '*' . $prepared_args['search'] . '*';
        }

        // Filter by email.
        if (!empty($request['email'])) {
            $prepared_args['search']         = $request['email'];
            $prepared_args['search_columns'] = ['user_email'];
        }

        // Filter by role.
        if ('all' !== $request['role']) {
            $prepared_args['role'] = $request['role'];
        }

        /**
         * Filter arguments, before passing to WP_User_Query, when querying users via the REST API.
         *
         * @see https://developer.wordpress.org/reference/classes/wp_user_query/
         *
         * @param array $prepared_args Array of arguments for WP_User_Query.
         * @param WP_REST_Request $request The current request.
         */
        $prepared_args = apply_filters('opalestate_rest_user_query', $prepared_args, $request);
        $query         = new WP_User_Query($prepared_args);

        $users = [];
        foreach ($query->results as $user) {
            $data    = $this->prepare_item_for_response($user, $request);
            $users[] = $this->prepare_response_for_collection($data);
        }

        $response = rest_ensure_response($users);

        // Store pagination values for headers then unset for count query.
        $per_page = (int)$prepared_args['number'];
        $page     = ceil((((int)$prepared_args['offset']) / $per_page) + 1);

        $prepared_args['fields'] = 'ID';

        $total_users = $query->get_total();
        if ($total_users < 1) {
            // Out-of-bounds, run the query again without LIMIT for total count.
            unset($prepared_args['number']);
            unset($prepared_args['offset']);
            $count_query = new WP_User_Query($prepared_args);
            $total_users = $count_query->get_total();
        }
        $response->header('X-WP-Total', (int)$total_users);
        $max_pages = ceil($total_users / $per_page);
        $response->header('X-WP-TotalPages', (int)$max_pages);

        $base = add_query_arg($request->get_query_params(), rest_url(sprintf('/%s/%s', $this->namespace, $this->base)));
        if ($page > 1) {
            $prev_page = $page - 1;
            if ($prev_page > $max_pages) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg('page', $prev_page, $base);
            $response->link_header('prev', $prev_link);
        }
        if ($max_pages > $page) {
            $next_page = $page + 1;
            $next_link = add_query_arg('page', $next_page, $base);
            $response->link_header('next', $next_link);
        }

        return $response;
    }

    /**
     * Update user data.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update_item($request) {
        try {
            $id = (int)$request['id'];

            $user_data = get_userdata($id);

            if (!$user_data) {
                throw new Exception(__('Invalid resource ID.', 'opalestate-pro'), 400);
            }

            if (in_array('opalestate_agent', $user_data->roles)) {
                $this->update_agent_data($request);
            }

            if (in_array('opalestate_agency', $user_data->roles)) {
                $this->update_agency_data($request);
            }

            /**
             * Fires after a customer is created or updated via the REST API.
             *
             * @param WP_User $customer Data used to create the customer.
             * @param WP_REST_Request $request Request object.
             * @param boolean $creating True when creating customer, false when updating customer.
             */
            do_action('opalestate_rest_insert_customer', $user_data, $request, false);

            $request->set_param('context', 'edit');
            $response = $this->prepare_item_for_response($user_data, $request);
            $response = rest_ensure_response($response);

            return $response;
        } catch (Exception $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);
        }
    }

    /**
     * Update user data.
     *
     * @param WP_REST_Request $request Full details about the request.
     */
    public function update_agent_data($request) {
        $fields = OpalEstate_Agent::metaboxes_fields();

        $others = [
            'opalestate_agt_map' => '',
            'map'                => '',
        ];

        foreach ($fields as $key => $field) {
            $tmp = str_replace(OPALESTATE_AGENT_PREFIX, '', $field['id']);

            if (isset($request[$tmp]) && $request[$tmp]) {
                $related_id = get_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                $post       = get_post($related_id);

                if ('avatar' === $tmp) {
                    if (is_array($request[$tmp])) {
                        if (isset($post->ID) && $post->ID) {
                            $attach_id = opalestate_upload_base64_image($request[$tmp], $related_id);
                        } else {
                            $attach_id = opalestate_upload_base64_image($request[$tmp]);
                        }

                        $request[$tmp]         = wp_get_attachment_image_url($attach_id, 'full');
                        $request[$tmp . '_id'] = $attach_id;
                        update_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . $tmp . '_id', $attach_id);
                        update_post_meta($related_id, $field['id'] . '_id', $attach_id);
                    }
                }

                $data = is_string($request[$tmp]) ? sanitize_text_field($request[$tmp]) : $request[$tmp];

                update_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . $tmp, $data);

                if (isset($post->ID) && $post->ID) {
                    update_post_meta($related_id, $field['id'], $data);
                }
            }
        }

        $this->update_object_terms($request['id'], $request);

        // Update for others.
        foreach ($others as $key => $value) {
            if (isset($request[$key])) {
                $data = is_string($request[$key]) ? sanitize_text_field($request[$key]) : $request[$key];
                update_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . $key, $data);
            }
        }
    }

    /**
     * Update object terms.
     *
     * @param int $related_id Post ID.
     */
    public function update_object_terms($user_id, $request) {
        $terms = [
            'location',
            'state',
            'city',
        ];

        foreach ($terms as $term) {
            if (isset($request[$term])) {
                wp_set_object_terms($user_id, $request[$term], 'opalestate_' . $term);

                $related_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                $post       = get_post($related_id);

                if (isset($post->ID) && $post->ID) {
                    wp_set_object_terms($related_id, $request[$term], 'opalestate_' . $term);
                }
            }
        }
    }

    /**
     * Update agency data.
     *
     * @param WP_REST_Request $request Full details about the request.
     */
    public function update_agency_data($request) {
        $fields = OpalEstate_Agency::metaboxes_fields();

        $others = [
            'map' => '',
        ];

        foreach ($fields as $key => $field) {
            $tmp = str_replace(OPALESTATE_AGENCY_PREFIX, '', $field['id']);

            if (isset($request[$tmp]) && $request[$tmp]) {
                $related_id = get_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                $post       = get_post($related_id);

                if ('avatar' === $tmp) {
                    if (is_array($request[$tmp])) {
                        if (isset($post->ID) && $post->ID) {
                            $attach_id = opalestate_upload_base64_image($request[$tmp], $related_id);
                        } else {
                            $attach_id = opalestate_upload_base64_image($request[$tmp]);
                        }

                        $request[$tmp]         = wp_get_attachment_image_url($attach_id, 'full');
                        $request[$tmp . '_id'] = $attach_id;
                        update_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . $tmp . '_id', $attach_id);
                        update_post_meta($related_id, $field['id'] . '_id', $attach_id);
                    }
                }

                $data = is_string($request[$tmp]) ? sanitize_text_field($request[$tmp]) : $request[$tmp];

                update_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . $tmp, $data);

                if (isset($post->ID) && $post->ID) {
                    update_post_meta($related_id, $field['id'], $data);
                }
            }
        }

        $this->update_object_terms($request['id'], $request);

        // Update for others.
        foreach ($others as $key => $value) {
            $kpos = OPALESTATE_AGENCY_PREFIX . $key;
            if (isset($request[$kpos])) {
                $data = is_string($request[$kpos]) ? sanitize_text_field($request[$kpos]) : $request[$kpos];
                update_user_meta($request['id'], OPALESTATE_USER_PROFILE_PREFIX . $key, $data);
            }
        }
    }

    /**
     * Prepare a single customer output for response.
     *
     * @param WP_User $user_data User object.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response $response Response data.
     */
    public function prepare_item_for_response($user_data, $request) {
        $_data = $user_data->data;

        $data = [
            'id'           => $_data->ID,
            'role'         => $user_data->roles,
            'date_created' => $_data->user_registered,
            'email'        => $_data->user_email,
            'first_name'   => $user_data->first_name,
            'last_name'    => $user_data->last_name,
            'username'     => $_data->user_login,
            'avatar_url'   => opalestate_get_user_meta($_data->ID, 'avatar'),
        ];

        if (in_array('opalestate_agent', $user_data->roles)) {
            $data['agent_data'] = $this->parse_agent_data($user_data);
        }

        if (in_array('opalestate_agency', $user_data->roles)) {
            $data['agency_data'] = $this->parse_agency_data($user_data);
        }

        $context  = !empty($request['context']) ? $request['context'] : 'view';
        $response = rest_ensure_response($data);

        /**
         * Filter customer data returned from the REST API.
         *
         * @param WP_REST_Response $response The response object.
         * @param WP_User $user_data User object used to create response.
         * @param WP_REST_Request $request Request object.
         */
        return apply_filters('opalestate_rest_prepare_customer', $response, $user_data, $request);
    }

    /**
     * Prepare a response for inserting into a collection.
     *
     * @param WP_REST_Response $response Response object.
     * @return array Response data, ready for insertion into collection data.
     */
    public function prepare_response_for_collection($response) {
        if (!($response instanceof WP_REST_Response)) {
            return $response;
        }

        $data   = (array)$response->get_data();
        $server = rest_get_server();

        if (method_exists($server, 'get_compact_response_links')) {
            $links = call_user_func([$server, 'get_compact_response_links'], $response);
        } else {
            $links = call_user_func([$server, 'get_response_links'], $response);
        }

        if (!empty($links)) {
            $data['_links'] = $links;
        }

        return $data;
    }

    public function parse_agent_data($user_data) {
        $data   = [];
        $fields = OpalEstate_Agent::metaboxes_fields();

        $others = [
            'avatar_id'          => '',
            'opalestate_agt_map' => '',
            'map'                => '',
        ];

        foreach ($fields as $key => $field) {
            $tmp        = str_replace(OPALESTATE_AGENT_PREFIX, '', $field['id']);
            $data[$tmp] = get_user_meta($user_data->data->ID, OPALESTATE_USER_PROFILE_PREFIX . $tmp, true);
        }

        // Update for others.
        foreach ($others as $key => $value) {
            $data[$key] = get_user_meta($user_data->data->ID, OPALESTATE_USER_PROFILE_PREFIX . $key, true);
        }

        return $data;
    }

    public function parse_agency_data($user_data) {
        $data   = [];
        $fields = OpalEstate_Agency::metaboxes_fields();

        $others = [
            'avatar_id' => '',
            'map'       => '',
        ];

        foreach ($fields as $key => $field) {
            $tmp        = str_replace(OPALESTATE_AGENCY_PREFIX, '', $field['id']);
            $data[$tmp] = get_user_meta($user_data->data->ID, OPALESTATE_USER_PROFILE_PREFIX . $tmp, true);
        }

        // Update for others.
        foreach ($others as $key => $value) {
            $data[$key] = get_user_meta($user_data->data->ID, OPALESTATE_USER_PROFILE_PREFIX . $key, true);
        }

        return $data;
    }

    /**
     * Get a single customer.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item($request) {
        $id        = (int)$request['id'];
        $user_data = get_userdata($id);

        if (empty($id) || empty($user_data->ID)) {
            return new WP_Error('opalestate_rest_invalid_id', __('Invalid resource ID.', 'opalestate-pro'), ['status' => 404]);
        }

        $customer = $this->prepare_item_for_response($user_data, $request);
        $response = rest_ensure_response($customer);

        return $response;
    }

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
     * Show all favorited properties with pagination.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_favorites($request) {
        $id        = (int)$request['id'];
        $user_data = get_userdata($id);

        if (empty($id) || empty($user_data->ID)) {
            return new WP_Error('opalestate_rest_invalid_id', __('Invalid resource ID.', 'opalestate-pro'), ['status' => 404]);
        }

        $per_page = isset($request['per_page']) && $request['per_page'] ? $request['per_page'] : 5;
        $paged    = isset($request['page']) && $request['page'] ? $request['page'] : 1;
        $items    = (array)get_user_meta($request['id'], 'opalestate_user_favorite', true);

        $property_list = get_posts([
            'post_type'      => 'opalestate_property',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'post__in'       => !empty($items) ? $items : [9999999],
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
     * Get the query params for collections.
     *
     * @return array
     */
    public function get_collection_params() {
        $params = parent::get_collection_params();

        $params['context']['default'] = 'view';

        $params['exclude'] = [
            'description'       => __('Ensure result set excludes specific IDs.', 'opalestate-pro'),
            'type'              => 'array',
            'items'             => [
                'type' => 'integer',
            ],
            'default'           => [],
            'sanitize_callback' => 'wp_parse_id_list',
        ];
        $params['include'] = [
            'description'       => __('Limit result set to specific IDs.', 'opalestate-pro'),
            'type'              => 'array',
            'items'             => [
                'type' => 'integer',
            ],
            'default'           => [],
            'sanitize_callback' => 'wp_parse_id_list',
        ];
        $params['offset']  = [
            'description'       => __('Offset the result set by a specific number of items.', 'opalestate-pro'),
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'validate_callback' => 'rest_validate_request_arg',
        ];
        $params['order']   = [
            'default'           => 'asc',
            'description'       => __('Order sort attribute ascending or descending.', 'opalestate-pro'),
            'enum'              => ['asc', 'desc'],
            'sanitize_callback' => 'sanitize_key',
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
        ];
        $params['orderby'] = [
            'default'           => 'name',
            'description'       => __('Sort collection by object attribute.', 'opalestate-pro'),
            'enum'              => [
                'id',
                'include',
                'name',
                'registered_date',
            ],
            'sanitize_callback' => 'sanitize_key',
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
        ];
        $params['email']   = [
            'description'       => __('Limit result set to resources with a specific email.', 'opalestate-pro'),
            'type'              => 'string',
            'format'            => 'email',
            'validate_callback' => 'rest_validate_request_arg',
        ];
        $params['role']    = [
            'description'       => __('Limit result set to resources with a specific role.', 'opalestate-pro'),
            'type'              => 'string',
            'default'           => 'opalestate_agent',
            'enum'              => array_merge(['all'], $this->get_role_names()),
            'validate_callback' => 'rest_validate_request_arg',
        ];

        return $params;
    }

    /**
     * Get role names.
     *
     * @return array
     */
    protected function get_role_names() {
        global $wp_roles;

        return array_keys($wp_roles->role_names);
    }
}
