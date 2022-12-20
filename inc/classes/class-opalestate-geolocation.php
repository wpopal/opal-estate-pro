<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OpalEstate_GeoLocation {

    /*
     * function to geocode address, it will return false if unable to geocode address
     */
    public static function get_points_in_miles($latitude, $longitude, $miles) {

        $equator = 69.172;

        $maxlat  = $latitude + $miles / $EQUATOR_LAT_MILE;
        $minlat  = $latitude - ($maxlat - $latitude);
        $maxlong = $longitude + $miles / (cos($minlat * M_PI / 180) * $equator);
        $minlong = $longitude - ($maxlong - $longitude);

        return array(
            'minlat'  => $minlat,
            'maxlat'  => $maxlat,
            'minlong' => $minlong,
            'maxlong' => $maxlong
        );
    }

    /*
    * function to geocode address, it will return false if unable to geocode address
    */
    public static function calculate($lat1, $long1, $lat2, $long2) {

        $EARTH_RADIUS_MILES = 3963;
        $dist               = 0;

        //convert degrees to radians
        $lat1  = $lat1 * M_PI / 180;
        $long1 = $long1 * M_PI / 180;
        $lat2  = $lat2 * M_PI / 180;
        $long2 = $long2 * M_PI / 180;

        if ($lat1 != $lat2 || $long1 != $long2) {

            $dist = sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($long2 - $long1);
            $dist = $EARTH_RADIUS_MILES * (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
        }
        return $dist;
    }

    /*
     * function to geocode address, it will return false if unable to geocode address
     */
    public static function geocode($address) {

        // url encode the address
        $address = urlencode($address);

        // google map geocode api url
        $url = opalestate_get_map_search_api_uri($address);

        // get the json response
        // $resp_json = file get contents($url);
        $resp_json = wp_remote_get($url);

        // decode the json
        $resp = json_decode($resp_json, true);


        // response status will be 'OK', if able to geocode given address 
        if ($resp['status'] == 'OK') {

            // get the important data
            $lati              = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi             = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
            $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

            // verify if data is complete
            if ($lati && $longi && $formatted_address) {
                // put the data in the array
                $data_arr = array();
                array_push(
                    $data_arr,
                    $lati,
                    $longi,
                    $formatted_address
                );

                return $data_arr;

            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
