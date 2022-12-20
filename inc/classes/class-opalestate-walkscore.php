<?php
/**
 * Opalestate_WalkScore
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * An implementation of the Walk Score API in PHP.
 *
 * @see http://www.walkscore.com/
 */
class Opalestate_WalkScore {

    /**
     * Constructs a WalkScore object
     *
     * @param string $wsapikey
     *   Walk Score API key obtainable at http://www.walkscore.com/request-api-key.php
     */
    function __construct($wsapikey) {
        // a Walk Score API key is required
        if (!isset($wsapikey)) {
            throw new Exception("Walk Score API key required");
        }
        $this->wsapikey = $wsapikey;
    }

    /**
     * Makes the API call using curl
     *
     * @param string $url
     *   The URL to send the request to
     * @param array $options
     *   The options to send as the query appended to the URL
     */
    private function make_api_call($url, $options) {
        $options['wsapikey'] = $this->wsapikey;
        $options['format']   = 'json';
        $query               = http_build_query($options);
        $response_url        = $url . '?' . $query;

        $response = wp_remote_get($response_url);
        $response = wp_remote_retrieve_body($response);
        $response = json_decode($response);

        return $response;
    }

    /**
     * Implement the Walk Score Public Transit API
     *
     * @param string $call
     *   Which call to make to the Public Transit API
     *   - score: Returns the Transit Score for a given location.
     *   - stop search: Returns information about stops near a given location.
     *   - network search: Returns connected stops and routes near a given location.
     *   - stop detail: Returns details for a single stop.
     *   - route detail: eturns details for a single route.
     *   - supported cities: Returns a list of cities for which scores are available.
     * @param array $options
     *   Options to send to the Public Transit API. Keys and values are dependent
     *   on the call made.
     * @return
     *   An object containing the results of the call.
     * @see http://www.walkscore.com/services/public-transit-api.php
     */
    public function PublicTransit($call, $options = array()) {

        $api_url = 'http://transit.walkscore.com/transit/';
        $calls   = array(
            'score'            => 'score/',
            'stop search'      => 'search/stops/',
            'network search'   => 'search/network/',
            'stop detail'      => 'stop/ID/',
            'route detail'     => 'route/ID/',
            'supported cities' => 'supported/cities/',
        );


        $api_url .= $calls[$call];
        return $this->make_api_call($api_url, $options);
    }

    /**
     * Implementation of the Walk Score API
     *
     * @param array $options
     *   An array of options. The array keys to pass are:
     *   - mode: string, one of 'walk', 'bike', 'drive', or 'transit'.
     *   - origin: string containing a comma-separated lat,lng.
     *   - destination: string containing a comma-separated lat,lng.
     * @todo Multiple destinations.
     *  @ see http://www.walkscore.com/professional/travel-time-api.php
     */
    public function TravelTime($options = array()) {
        if (!is_array($options)) {
            throw new Exception("Input parameter must be an array.");
        }
        $modes = array('walk', 'bike', 'drive', 'transit');
        if (!in_array($options['mode'], $modes)) {
            throw new Exception("Mode parameter must be one of 'walk', 'bike', 'drive', or 'transit'.");
        }
        $response = $this->make_api_call('http://www.walkscore.com/api/v1/traveltime/json', $options);
        return $response->response;
    }

    /**
     * Implementation of the Walk Score API
     *
     * @param array $options
     *   An array of options. The array keys to pass are:
     *   - address: string containing the street address of the location
     *   - lat: string or number containing the latitude of the location
     *   - lon: string or number containing the longitude of the location
     * @return
     *   An object containing the results of the call. An added property
     *   called status_description gives a human-readable description of
     *   the numeric status code returned in the object
     * @see http://www.walkscore.com/services/api.php
     */
    public function WalkScore($options = array()) {
        if (!is_array($options)) {
            throw new Exception("Input parameter must be an array.");
        }

        $response = $this->make_api_call('http://api.walkscore.com/score', $options);

        // stuff the status code description in the response object
        // so you don't have to look it up on the Walk Score website
        $status_descriptions = array(
            1  => 'Walk Score successfully returned.',
            2  => 'Score is being calculated and is not currently available.',
            30 => 'Invalid latitude/longitude.',
            40 => 'Your WSAPIKEY is invalid.',
            41 => 'Your daily API quota has been exceeded.',
            42 => 'Your IP address has been blocked.',
        );

        $response->status_description = $status_descriptions[$response->status];

        return $response;
    }

    /**
     * Implementation of the Walk Shed API
     *
     * @param array $options
     *   An array of options. The array keys to pass are:
     *   - lat: string or number containing the latitude of the location
     *   - lon: string or number containing the longitude of the location
     * @return
     *   An object containing the results of the call. An added property
     *   called status_description gives a human-readable description of
     *   the numeric status code returned in the object
     * @see http://www.walkscore.com/services/api.php
     */
    public function WalkShed($options = array()) {

        if (!is_array($options)) {
            throw new Exception("Input parameter must be an array.");
        }

        $response = $this->make_api_call('http://api.walkscore.com/walk_shed', $options);

        // stuff the status code description in the response object
        // so you don't have to look it up on the Walk Score website
        $status_descriptions          = array(
            1  => 'Walk shed successfully returned.',
            2  => 'Walk shed unavailable.',
            30 => 'Invalid latitude/longitude.',
            31 => 'Walk Score API internal error.',
            40 => 'Your WSAPIKEY is invalid.',
            41 => 'Your daily API quota has been exceeded.',
        );
        $response->status_description = $status_descriptions[$response->status];

        return $response;
    }
}
