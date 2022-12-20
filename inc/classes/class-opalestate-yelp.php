<?php
/**
 * Opalestate_Yelp
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Yelp {
    // API constants, you shouldn't have to change these.
    const API_HOST      = "https://api.yelp.com";
    const SEARCH_PATH   = "/v3/businesses/search";
    const BUSINESS_PATH = "/v3/businesses/";  // Business ID will come after slash.
    const TOKEN_PATH    = "/oauth2/token";
    const GRANT_TYPE    = "client_credentials";

    public static function get_client_id() {
        return opalestate_get_option('yelp_app_id', '');
    }

    public static function get_app_secret() {
        return opalestate_get_option('yelp_app_secret', '');
    }

    public static function get_app_key() {
        return opalestate_get_option('yelp_app_key', '');
    }

    public static function get_categories() {
        return opalestate_get_option('yelp_categories', []);
    }

    public static function get_category_results_limit() {
        return opalestate_get_option('yelp_number_results', 3);
    }

    /**
     * Given a bearer token, send a GET request to the API.
     *
     * @return   OAuth bearer token, obtained using client_id and client_secret.
     */
    public function obtain_bearer_token() {
        $yelp_client_id     = static::get_client_id();
        $yelp_client_secret = static::get_app_secret();
        $yelp_app_key       = static::get_app_key();

        return $yelp_app_key;
    }


    /**
     * Makes a request to the Yelp API and returns the response
     *
     * @param    $bearer_token   API bearer token from obtain_bearer_token
     * @param    $host           The domain host of the API
     * @param    $path           The path of the API after the domain.
     * @param    $url_params     Array of query-string parameters.
     * @return   The JSON response from the request
     */
    public function request($bearer_token, $host, $path, $url_params = []) {
        // Send Yelp API Call
        try {
            $url  = $host . $path . "?" . http_build_query($url_params);
            $args = [
                'timeout'     => 30,
                'redirection' => 10,
                'httpversion' => CURL_HTTP_VERSION_1_1,
                'user-agent'  => '',
                'headers'     => [
                    'authorization' => 'Bearer ' . $bearer_token,
                ],
                'sslverify'   => false,
            ];

            $response = wp_remote_get($url, $args);
            $response = wp_remote_retrieve_body($response);
        } catch (Exception $e) {

        }

        return $response;
    }

    /**
     * Query the Search API by a search term and location
     *
     * @param    $bearer_token   API bearer token from obtain_bearer_token
     * @param    $term           The search term passed to the API
     * @param    $location       The search location passed to the API
     * @return   The JSON response from the request
     */
    public function search($bearer_token, $term, $latitude, $longitude) {
        $url_params = [];

        $url_params['term']      = $term;
        $url_params['latitude']  = $latitude;
        $url_params['longitude'] = $longitude;
        $url_params['limit']     = static::get_category_results_limit();

        return $this->request($bearer_token, static::API_HOST, static::SEARCH_PATH, $url_params);
    }

    /**
     * Query the Business API by business_id
     *
     * @param    $bearer_token   API bearer token from obtain_bearer_token
     * @param    $business_id    The ID of the business to query
     * @return   The JSON response from the request
     */
    public function get_business($bearer_token, $business_id) {
        $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id);

        return $this->request($bearer_token, $GLOBALS['API_HOST'], $business_path);
    }

    public function query_api($term, $latitude, $longitude) {
        $bearer_token = $this->obtain_bearer_token();

        $response = json_decode($this->search($bearer_token, $term, $latitude, $longitude));

        return $response;
    }

    public function get_results($term, $latitude, $longitude) {
        if (!static::get_categories()) {
            return false;
        }

        $results = $this->query_api($term, $latitude, $longitude);
        if (isset($results->error) && $results->error) {
            return false;
        }

        return $results;
    }

    public static function get_all_categories() {
        return apply_filters('opalestate_yelp_all_categories',
            [
                'active'             => [
                    'category'      => esc_html__('Active Life', 'opalestate-pro'),
                    'category_sign' => 'fa fa-bicycle',
                ],
                'arts'               => [
                    'category'      => esc_html__('Arts & Entertainment', 'opalestate-pro'),
                    'category_sign' => 'fa fa-music',
                ],
                'auto'               => [
                    'category'      => esc_html__('Automotive', 'opalestate-pro'),
                    'category_sign' => 'fa fa-car',
                ],
                'beautysvc'          => [
                    'category'      => esc_html__('Beauty & Spas', 'opalestate-pro'),
                    'category_sign' => 'fa fa-female',
                ],
                'education'          => [
                    'category'      => esc_html__('Education', 'opalestate-pro'),
                    'category_sign' => 'fa fa-graduation-cap',
                ],
                'eventservices'      => [
                    'category'      => esc_html__('Event Planning & Services', 'opalestate-pro'),
                    'category_sign' => 'fa fa-birthday-cake',
                ],
                'financialservices'  => [
                    'category'      => esc_html__('Financial Services', 'opalestate-pro'),
                    'category_sign' => 'fa fa-money',
                ],
                'food'               => [
                    'category'      => esc_html__('Food', 'opalestate-pro'),
                    'category_sign' => 'fa fa fa-cutlery',
                ],
                'health'             => [
                    'category'      => esc_html__('Health & Medical', 'opalestate-pro'),
                    'category_sign' => 'fa fa-medkit',
                ],
                'homeservices'       => [
                    'category'      => esc_html__('Home Services ', 'opalestate-pro'),
                    'category_sign' => 'fa fa-wrench',
                ],
                'hotelstravel'       => [
                    'category'      => esc_html__('Hotels & Travel', 'opalestate-pro'),
                    'category_sign' => 'fa fa-bed',
                ],
                'localflavor'        => [
                    'category'      => esc_html__('Local Flavor', 'opalestate-pro'),
                    'category_sign' => 'fa fa-coffee',
                ],
                'localservices'      => [
                    'category'      => esc_html__('Local Services', 'opalestate-pro'),
                    'category_sign' => 'fa fa-dot-circle-o',
                ],
                'massmedia'          => [
                    'category'      => esc_html__('Mass Media', 'opalestate-pro'),
                    'category_sign' => 'fa fa-television',
                ],
                'nightlife'          => [
                    'category'      => esc_html__('Nightlife', 'opalestate-pro'),
                    'category_sign' => 'fa fa-glass',
                ],
                'pets'               => [
                    'category'      => esc_html__('Pets', 'opalestate-pro'),
                    'category_sign' => 'fa fa-paw',
                ],
                'professional'       => [
                    'category'      => esc_html__('Professional Services', 'opalestate-pro'),
                    'category_sign' => 'fa fa-suitcase',
                ],
                'publicservicesgovt' => [
                    'category'      => esc_html__('Public Services & Government', 'opalestate-pro'),
                    'category_sign' => 'fa fa-university',
                ],
                'realestate'         => [
                    'category'      => esc_html__('Real Estate', 'opalestate-pro'),
                    'category_sign' => 'fa fa-building-o',
                ],
                'religiousorgs'      => [
                    'category'      => esc_html__('Religious Organizations', 'opalestate-pro'),
                    'category_sign' => 'fa fa-cloud',
                ],
                'restaurants'        => [
                    'category'      => esc_html__('Restaurants', 'opalestate-pro'),
                    'category_sign' => 'fa fa-cutlery',
                ],
                'shopping'           => [
                    'category'      => esc_html__('Shopping', 'opalestate-pro'),
                    'category_sign' => 'fa fa-shopping-bag',
                ],
                'transport'          => [
                    'category'      => esc_html__('Transportation', 'opalestate-pro'),
                    'category_sign' => 'fa fa-bus',
                ],
            ]
        );
    }

    public static function get_all_categories_options() {
        $categories = static::get_all_categories();

        $options = [];
        foreach ($categories as $key => $term) {
            $options[$key] = $term['category'];
        }

        return $options;
    }
}
