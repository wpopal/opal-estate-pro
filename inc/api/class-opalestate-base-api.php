<?php

/**
 * Abstract class to define/implement base methods for all controller classes
 *
 * @package    Opal_Job
 * @subpackage Opal_Job/controllers
 */
abstract class Opalestate_Base_API {

    /**
     * The unique identifier of this plugin.
     *
     * @access   protected
     * @var      string $plugin_base_name The string used to uniquely identify this plugin.
     */
    public $base;

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = '';

    /**
     * The unique identifier of this plugin.
     *
     * @access   protected
     * @var      string $plugin_base_name The string used to uniquely identify this plugin.
     */
    public $namespace = 'estate-api/v1';

    /**
     * Definition
     *
     * Register all Taxonomy related to Job post type as location, category, Specialism, Types
     */
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Definition
     *
     * Register all Taxonomy related to Job post type as location, category, Specialism, Types
     */
    public function register_routes() {


    }

    public function get_response($code, $output) {

        $response = [];

        $response['status'] = $code;
        $response           = array_merge($response, $output);

        return new WP_REST_Response($response);
    }

    public function output($code) {

        $this->data['status'] = $code;

        return new WP_REST_Response($this->data);
    }

    /**
     * Validate the API request.
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function validate_request(WP_REST_Request $request) {

        return true;
        $response = [];

        // Make sure we have both user and api key
        $api_admin = Opalestate_API_Admin::get_instance();

        if (empty($request['token']) || empty($request['key'])) {
            return $this->missing_auth();
        }

        // Retrieve the user by public API key and ensure they exist
        if (!($user = $api_admin->get_user($request['key']))) {

            $this->invalid_key();

        } else {

            $token  = urldecode($request['token']);
            $secret = $api_admin->get_user_secret_key($user);
            $public = urldecode($request['key']);

            if (hash_equals(md5($secret . $public), $token)) {
                return true;
            } else {
                $this->invalid_auth();
            }
        }

        return false;
    }

    /**
     * Get page number
     *
     * @access public
     * @return int $wp_query->query_vars['page'] if page number returned (default: 1)
     * @global $wp_query
     */
    public function get_paged() {
        global $wp_query;

        return isset($wp_query->query_vars['page']) ? $wp_query->query_vars['page'] : 1;
    }


    /**
     * Number of results to display per page
     *
     * @access public
     * @return int $per_page Results to display per page (default: 10)
     * @global $wp_query
     */
    public function per_page() {
        global $wp_query;

        $per_page = isset($wp_query->query_vars['number']) ? $wp_query->query_vars['number'] : 10;

        return apply_filters('opalestate_api_results_per_page', $per_page);
    }

    /**
     * Get object.
     *
     * @param int $id Object ID.
     * @return object WC_Data object or WP_Error object.
     */
    protected function get_object($id) {
        // translators: %s: Class method name.
        return new WP_Error('invalid-method', sprintf(__("Method '%s' not implemented. Must be overridden in subclass.", 'opalestate-pro'), __METHOD__), ['status' => 405]);
    }

    /**
     * Displays a missing authentication error if all the parameters aren't
     * provided
     *
     * @access private
     * @return WP_Error with message key rest_forbidden
     */
    private function missing_auth() {
        return new WP_Error('rest_forbidden', esc_html__('You must specify both a token and API key!', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
    }

    /**
     * Displays an authentication failed error if the user failed to provide valid
     * credentials
     *
     * @access private
     * @return WP_Error with message key rest_forbidden
     */
    private function invalid_auth() {
        return new WP_Error('rest_forbidden', esc_html__('Your request could not be authenticated!', 'opalestate-pro'), ['status' => 403]);
    }

    /**
     * Displays an invalid API key error if the API key provided couldn't be
     * validated
     *
     * @access private
     * @return WP_Error with message key rest_forbidden
     */
    private function invalid_key() {
        return new WP_Error('rest_forbidden', esc_html__('Invalid API key!', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
    }

    /**
     * Check if a given request has access to read items.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_items_permissions_check($request) {
        $is_valid = $this->is_valid_api_key($request);

        if (is_wp_error($is_valid)) {
            return $is_valid;
        } else {
            $route    = $request->get_route();
            $endpoint = explode('/', $route);
            $endpoint = end($endpoint);

            if (in_array($endpoint, ['properties', 'agencies', 'agents', 'search-form', 'search'])) {
                return true;
            }

            if (!opalestate_rest_check_post_permissions($this->post_type, 'read')) {
                return new WP_Error('opalestate_rest_cannot_view', __('Sorry, you cannot list resources.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
            }
        }

        return true;
    }

    /**
     * Check if a given request has access.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function is_valid_api_key($request) {
        if (isset($request['consumer_key']) && $request['consumer_secret']) {
            $user = opalestate_get_user_data_by_consumer_key($request['consumer_key']);

            if ($user) {
                if ($request['consumer_secret'] === $user->consumer_secret) {
                    return true;
                }
            }
        }

        return new WP_Error('opalestate_rest_cannot_access', __('Sorry, you cannot list resources. Invalid keys!', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
    }

    /**
     * Check if is request to our REST API.
     *
     * @return bool
     */
    protected function is_request_to_rest_api() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return false;
        }

        $rest_prefix = trailingslashit(rest_get_url_prefix());
        $request_uri = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));

        // Check if the request is to the Opalestate API endpoints.
        $opalestate = (false !== strpos($request_uri, $rest_prefix . 'estate-api/'));

        // Allow third party plugins use our authentication methods.
        $third_party = (false !== strpos($request_uri, $rest_prefix . 'estate-api-'));

        return apply_filters('opalestate_rest_is_request_to_rest_api', $opalestate || $third_party);
    }

    /**
     * Check if a given request has access to read an item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_item_permissions_check($request) {
        $object = $this->get_object((int)$request['id']);

        if ($object && 0 !== $object->get_id() && !opalestate_rest_check_post_permissions($this->post_type, 'read', $object->get_id())) {
            return new WP_Error('opalestate_rest_cannot_view', __('Sorry, you cannot view this resource.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Check if a given request has access to create an item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function create_item_permissions_check($request) {
        if (!opalestate_rest_check_post_permissions($this->post_type, 'create')) {
            return new WP_Error('opalestate_rest_cannot_create', __('Sorry, you are not allowed to create resources.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Check if a given request has access to update an item.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function update_item_permissions_check($request) {
        $object = $this->get_object((int)$request['id']);

        if ($object && 0 !== $object->get_id() && !opalestate_rest_check_post_permissions($this->post_type, 'edit', $object->get_id())) {
            return new WP_Error('opalestate_rest_cannot_edit', __('Sorry, you are not allowed to edit this resource.', 'opalestate-pro'), ['status' => rest_authorization_required_code()]);
        }

        return true;
    }

    /**
     * Get the query params for collections of attachments.
     *
     * @return array
     */
    public function get_collection_params() {
        $params['page']     = [
            'description'       => __('Current page of the collection.', 'opalestate-pro'),
            'type'              => 'integer',
            'default'           => 1,
            'sanitize_callback' => 'absint',
            'validate_callback' => 'rest_validate_request_arg',
            'minimum'           => 1,
        ];
        $params['per_page'] = [
            'description'       => __('Maximum number of items to be returned in result set.', 'opalestate-pro'),
            'type'              => 'integer',
            'default'           => 10,
            'minimum'           => 1,
            'maximum'           => 100,
            'sanitize_callback' => 'absint',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        return $params;
    }
}
