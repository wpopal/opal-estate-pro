<?php
/**
 * Opalestate_Query
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Query {

    /**
     * Set active location
     */
    public static $LOCATION;

    /**
     * Get Query Object to display list of agents
     */
    public static function get_agents($args = [], $featured = false) {
        $default = [
            'post_type'      => 'opalestate_agent',
            'posts_per_page' => 10,
        ];

        $args = array_merge($default, $args);
        if ($featured) {
            $args['meta_key']     = OPALESTATE_AGENT_PREFIX . 'featured';
            $args['meta_value']   = 1;
            $args['meta_compare'] = '=';
        }

        return new WP_Query($args);
    }

    /**
     * Get Query Object to display list of agents
     */
    public static function get_agencies($args = [], $featured = false) {
        $default = [
            'post_type'      => 'opalestate_agency',
            'posts_per_page' => 10,
        ];
        $args    = array_merge($default, $args);
        if ($featured) {
            $args['meta_key']     = OPALESTATE_AGENCY_PREFIX . 'featured';
            $args['meta_value']   = 1;
            $args['meta_compare'] = '=';
        }

        return new WP_Query($args);
    }

    /**
     * Get Query Object By post and agent with setting items per page.
     */
    public static function get_agency_property($agency_id = null, $user_id = null, $per_page = 10, $page = null) {
        if (null == $agency_id) {
            $agency_id = get_the_ID();
        }

        $paged = $page ? $page : ((get_query_var('paged') == 0) ? 1 : get_query_var('paged'));

        // if this has not any relationship with any user
        if ($user_id) {

            $author = [$user_id];
            $team   = get_post_meta($agency_id, OPALESTATE_AGENCY_PREFIX . 'team', true);

            if (is_array($team)) {
                $author = array_merge($author, $team);
            }

            $args = [
                'post_type'      => 'opalestate_property',
                'author__in'     => $author,
                'posts_per_page' => $per_page,
                'paged'          => $paged,
            ];
        } else {
            $agents             = get_post_meta($agency_id, OPALESTATE_AGENCY_PREFIX . 'team', true);
            $args               = [
                'post_type'      => 'opalestate_property',
                'posts_per_page' => $per_page,
                'paged'          => $paged,
            ];
            $args['meta_query'] = ['relation' => 'OR'];
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'agency',
                'value'   => $agency_id,
                'compare' => '=',
            ]);

            if ($agents) {
                array_push($args['meta_query'], [
                    'key'   => OPALESTATE_PROPERTY_PREFIX . 'agent',
                    'value' => $agents,
                ]);
            }

        }

        $query = new WP_Query($args);

        return $query;
    }

    /**
     * Get Query Object By post and agent with setting items per page.
     */
    public static function get_agent_property($post_id = null, $agent_id = null, $per_page = 10, $isfeatured = false) {
        $user_id = null;
        if ($post_id) {
            $user_id = get_post_meta($post_id, OPALESTATE_AGENT_PREFIX . 'user_id', true);
        }

        $paged = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');

        $args = [
            'post_type'      => 'opalestate_property',
            'posts_per_page' => $per_page,
            'post__not_in'   => [$post_id],
            'paged'          => $paged,
        ];

        $args['meta_query'] = ['relation' => 'AND'];

        if ($user_id) {
            $args['author'] = $user_id;
        } elseif ($agent_id) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'agent',
                'value'   => $agent_id,
                'compare' => '=',
            ]);
        }

        if ($isfeatured) {
            array_push($args['meta_query'], [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'featured',
                'value'   => 'on',
                'compare' => '=',
            ]);
        }
        $query = new WP_Query($args);

        return $query;
    }

    /**
     * Get Query Object to show featured properties with custom setting via Arguments passing.
     */
    public static function get_featured_properties_query($args = []) {
        $default = [
            'post_type'      => 'opalestate_property',
            'posts_per_page' => 10,
            'meta_key'       => OPALESTATE_PROPERTY_PREFIX . 'featured',
            'meta_value'     => 1,
            'meta_compare'   => '=',

        ];

        $args = array_merge($default, $args);

        return new WP_Query($args);
    }

    /**
     * Set filter location to query when user set his location as global filterable.
     */
    public static function set_location($args) {
        if ($args && self::$LOCATION) {
            $tax_query         = [
                [
                    'taxonomy' => 'opalestate_location',
                    'field'    => 'slug',
                    'terms'    => self::$LOCATION,
                ],
            ];
            $args['tax_query'] = ['relation' => 'AND'];
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        return $args;
    }

    /**
     * Get WP Query Object with custom passing arguments and User request as get data.
     */
    public static function get_property_query($args = []) {
        $condition = [
            'post_type'      => 'opalestate_property',
            'posts_per_page' => isset($args['posts_per_page']) ? $args['posts_per_page'] : 5,
            'paged'          => isset($args['paged']) ? $args['paged'] : 1,
            'post_status'    => 'publish',
            'order_by'       => isset($args['orderby']) ? $args['orderby'] : 'post_date',
            'order'          => isset($args['order']) ? $args['order'] : 'DESC',
        ];

        $condition = array_merge($condition, $args);
        $relation  = "AND";

        $condition['meta_query'] = [];

        $condition['tax_query'] = [
            'relation' => $relation,
        ];

        if (!empty($args['categories'])) {
            array_push($condition['tax_query'], [
                'taxonomy' => 'property_category',
                'terms'    => implode(',', $args['categories']),
                'field'    => 'slug',
                'operator' => 'IN',
            ]);
        }

        if (!empty($args['types'])) {
            array_push($condition['tax_query'], [
                'taxonomy' => 'opalestate_types',
                'terms'    => $args['types'],
                'field'    => 'slug',
                'operator' => 'IN',
            ]);
        }


        if (!empty($args['statuses'])) {
            array_push($condition['tax_query'], [
                'taxonomy' => 'opalestate_status',
                'terms'    => $args['statuses'],
                'field'    => 'slug',
                'operator' => 'IN',
            ]);
        }


        if (!empty($args['labels'])) {
            array_push($condition['tax_query'], [
                'taxonomy' => 'opalestate_label',
                'terms'    => $args['labels'],
                'field'    => 'slug',
            ]);
        }

        if (!empty($args['cities'])) {
            array_push($condition['tax_query'], [
                'taxonomy' => 'opalestate_city',
                'terms'    => $args['cities'],
                'field'    => 'slug',
                'operator' => 'IN',
            ]);
        }

        if (!empty($args['showmode'])) {
            if ($args['showmode'] == 'featured') {
                array_push($condition['meta_query'], [
                    'key'     => OPALESTATE_PROPERTY_PREFIX . 'featured',
                    'value'   => 'on',
                    'compare' => '=',
                ]);
            } elseif ($args['showmode'] == 'normal') {
                array_push($condition['meta_query'], [
                    'relation' => 'OR',
                    [
                        'key'     => OPALESTATE_PROPERTY_PREFIX . 'featured',
                        'compare' => 'NOT EXISTS',
                        'value'   => '' // This is ignored, but is necessary...
                    ],
                    [
                        'key'     => OPALESTATE_PROPERTY_PREFIX . 'featured',
                        'value'   => 'on',
                        'compare' => '!=',
                    ],
                ]);
            }
        }

        $query = new WP_Query($condition);

        wp_reset_postdata();

        return $query;
    }

    /**
     * Get Agent id by property id
     */
    public static function get_agent_by_property($post_id = null) {
        if (null == $post_id) {
            $post_id = get_the_ID();
        }
        $agent_id = get_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'agent', true);

        return $agent_id;
    }

    /**
     * Get List of properties by user
     */
    public static function get_properties_by_user($oargs = [], $user_id = null) {

        $paged    = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
        $per_page = 9;

        $args = [
            'post_type'      => 'opalestate_property',
            'paged'          => $paged,
            'post_status'    => 'any',
            'author'         => $user_id,
            'posts_per_page' => $per_page,

        ];
        if (!empty($oargs) || is_array($oargs)) {
            $args = array_merge($args, $oargs);
        }

        if (isset($args['featured']) && $args['featured']) {
            $args = array_merge($args, [
                'meta_key'     => OPALESTATE_PROPERTY_PREFIX . 'featured',
                'meta_value'   => 'on',
                'meta_compare' => '=',
            ]);
            unset($args['featured']);

        }
        $query = new WP_Query($args);
        wp_reset_postdata();

        return $query;
    }

    /**
     *
     */
    public static function get_amenities() {
        return get_terms('opalestate_amenities', ['hide_empty' => false]);
    }

    /**
     * Filter_by_location
     */
    public static function filter_by_location($geo_lat, $geo_long, $radius, $radius_measure = '', $prefix = OPALESTATE_PROPERTY_PREFIX) {
        global $wpdb;

        switch ($radius_measure) {
            case 'km':
                $earth = 6371;
                break;
            case 'miles':
                $earth = 3959;
                break;
            default :
                $earth = 6371;
                break;
        }

        $latitude  = $prefix . 'map_latitude';
        $longitude = $prefix . 'map_longitude';

        $sql = "SELECT $wpdb->posts.ID,
            ( %s * acos(
                    cos( radians(%s) ) *
                    cos( radians( latitude.meta_value ) ) *
                    cos( radians( longitude.meta_value ) - radians(%s) ) +
                    sin( radians(%s) ) *
                    sin( radians( latitude.meta_value ) )
            ) )
            AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
            FROM $wpdb->posts
            INNER JOIN $wpdb->postmeta
                    AS latitude
                    ON $wpdb->posts.ID = latitude.post_id
            INNER JOIN $wpdb->postmeta
                    AS longitude
                    ON $wpdb->posts.ID = longitude.post_id
            WHERE 1=1

                    AND latitude.meta_key = '" . $latitude . "'
                    AND longitude.meta_key= '" . $longitude . "'
            HAVING distance < %s
            ORDER BY $wpdb->posts.menu_order ASC, distance ASC";

        $query = $wpdb->prepare($sql,
            $earth,
            $geo_lat,
            $geo_long,
            $geo_lat,
            $radius
        );

        $post_ids = $wpdb->get_results($query, OBJECT_K);
        if ($post_ids) {
            $post_ids = array_keys($post_ids);

            return $post_ids;
        }

        return [0];
    }
}
