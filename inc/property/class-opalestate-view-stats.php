<?php
/**
 * Property view stats.
 *
 * @package    opalestate
 * @author     Opal Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_View_Stats {
    protected $id;

    protected $record;

    public function __construct($id, $record = 8) {

        $this->id = $id;

        $this->record = $record;

        $this->count_page_stats();
    }

    /**
     * @return mixed
     */
    public static function get_real_ip_addr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        return $ip;
    }

    /**
     * Count page views.
     */
    public function count_page_stats() {
        // Get IPs.
        $ips = $this->get_ips_viewed();

        $current_ip = static::get_real_ip_addr();

        if (!in_array($current_ip, $ips)) {
            // Update IPS.
            array_push($ips, $current_ip);
            update_post_meta($this->id, 'opalestate_ips_viewed', $ips);

            // Count and update total views.
            $total_views = intval(get_post_meta($this->id, 'opalestate_total_views', true));
            if ($total_views == '') {
                $total_views = 1;
            } else {
                $total_views++;
            }

            update_post_meta($this->id, 'opalestate_total_views', $total_views);

            // Update detailed views.
            $today          = date('m-d-Y', time());
            $detailed_views = get_post_meta($this->id, 'opalestate_detailed_views', true);

            if ($detailed_views == '' || !is_array($detailed_views)) {
                $detailed_views         = [];
                $detailed_views[$today] = 1;
            } else {
                if (!isset($detailed_views[$today])) {
                    if (count($detailed_views) > 15) {
                        array_shift($detailed_views);
                    }

                    $detailed_views[$today] = 1;
                } else {
                    $detailed_views[$today] = intval($detailed_views[$today]) + 1;
                }
            }

            $detailed_views = update_post_meta($this->id, 'opalestate_detailed_views', $detailed_views);
        }
    }


    public function get_traffic_labels() {
        $detailed_views = get_post_meta($this->id, 'opalestate_detailed_views', true);

        if (!is_array($detailed_views)) {
            $detailed_views = [];
        }

        $array_label = array_keys($detailed_views);
        $array_label = array_slice($array_label, -1 * $this->record, $this->record, false);

        return $array_label;
    }


    public function get_traffic_data() {
        $detailed_views = get_post_meta($this->id, 'opalestate_detailed_views', true);
        if (!is_array($detailed_views)) {
            $detailed_views = [];
        }
        $array_values = array_values($detailed_views);
        $array_values = array_slice($array_values, -1 * $this->record, $this->record, false);

        return $array_values;
    }


    public function get_traffic_data_accordion() {
        $detailed_views = get_post_meta($this->id, 'opalestate_detailed_views', true);
        if (!is_array($detailed_views)) {
            $detailed_views = [];
        }

        // since this runs before we increment the visits - on acc page style
        $today = date('m-d-Y', time());

        if (isset($detailed_views[$today])) {
            $detailed_views[$today] = intval($detailed_views[$today]) + 1;
        }

        $array_values = array_values($detailed_views);
        $array_values = array_slice($array_values, -1 * $this->record, $this->record, false);

        return $array_values;
    }

    /**
     * Get IPs viewed.
     *
     * @return array
     */
    public function get_ips_viewed() {
        $ips = get_post_meta($this->id, 'opalestate_ips_viewed', true);
        if (!$ips) {
            $ips = [];
        }

        return $ips;
    }
}
