<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    $package$
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class Opalestate_Agency_Query extends OpalEstate_Abstract_Query {

    public $count = 0;

    /**
     * Default query arguments.
     *
     * Not all of these are valid arguments that can be passed to WP_Query. The ones that are not, are modified before
     * the query is run to convert them to the proper syntax.
     *
     * @param  $args array The array of arguments that can be passed in and used for setting up this form query.
     * @since  2.5.0
     * @access public
     *
     */
    public function __construct($args = array()) {

        $defaults = array(
            'output'    => 'collection',
            'post_type' => array('opalestate_agency'),
            'number'    => 20,
            'offset'    => 0,
            'paged'     => 1,
            'orderby'   => 'id',
            'order'     => 'DESC'
        );

        $args['update_post_meta_cache'] = false;

        $this->args = $this->_args = wp_parse_args($args, $defaults);
    }

    /**
     * Render Sidebar
     *
     *    Display Sidebar on left side and next is main content
     *
     * @return string
     * @since 1.0
     *
     */
    public function get_query_object() {
        /* @var WP_Query $query */
        $query = new WP_Query($this->args);
        return $query;
    }

    /**
     * Render Sidebar
     *
     *    Display Sidebar on left side and next is main content
     *
     * @return string
     * @since 1.0
     *
     */
    public function get_list() {

        $output = array(
            'founds'     => 0,
            'collection' => []
        );

        $query = $this->get_query_object();
        if ($query) {
            $i          = 0;
            $collection = array();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    global $post;
                    $collection[] = new OpalEstate_Agency($post->ID);
                }
            }
            wp_reset_postdata();
            $output['collection'] = $collection;
            $output['found']      = $query->found_posts;
        }


        return $output;
    }

    /**
     * Render Sidebar
     *
     *    Display Sidebar on left side and next is main content
     *
     * @return string
     * @since 1.0
     *
     */
    public function get_api_list() {
        $output = array(
            'founds'     => 0,
            'collection' => []
        );

        $query = $this->get_query_object();
        if ($query) {
            $i          = 0;
            $collection = array();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    global $post;
                    $collection[] = $this->get_agency_data($post);
                }
            }
            wp_reset_postdata();
            $output['collection'] = $collection;
            $output['found']      = $query->found_posts;
        }

        return $output;
    }

    /**
     * Opalestaten a opalestate_property post object, generate the data for the API output
     *
     * @param object $property_info The Download Post Object
     *
     * @return array                Array of post data to return back in the API
     * @since  1.1
     *
     */

    public function get_agency_data($agency_info) {

        $agency = new OpalEstate_Agency($agency_info->ID);

        $ouput = array();

        $ouput['info']['id']        = $agency_info->ID;
        $ouput['info']['slug']      = $agency_info->post_name;
        $ouput['info']['title']     = $agency_info->post_title;
        $ouput['info']['status']    = $agency_info->post_status;
        $ouput['info']['link']      = html_entity_decode($agency_info->guid);
        $ouput['info']['content']   = $agency_info->post_content;
        $ouput['info']['thumbnail'] = wp_get_attachment_url(get_post_thumbnail_id($agency_info->ID));

        $agency = new OpalEstate_Agency($agency_info->ID);

        $ouput['info']['featured'] = (int)$agency->is_featured();
        $ouput['info']['trusted']  = $agency->get_meta('trusted');
        $ouput['info']['avatar']   = $agency->get_meta('avatar');
        $ouput['info']['web']      = $agency->get_meta('web');
        $ouput['info']['phone']    = $agency->get_meta('phone');
        $ouput['info']['mobile']   = $agency->get_meta('mobile');
        $ouput['info']['fax']      = $agency->get_meta('fax');
        $ouput['info']['email']    = $agency->get_meta('email');
        $ouput['info']['address']  = $agency->get_meta('address');
        $ouput['info']['map']      = $agency->get_meta('map');

        $terms                     = wp_get_post_terms($agency_info->ID, 'opalestate_agency_location');
        $ouput['info']['location'] = $terms && !is_wp_error($terms) ? $terms : array();

        $ouput['socials'] = $agency->get_socials();

        $ouput['category'] = $agency->get_category_tax();

        return apply_filters('opalestate_api_agency', $ouput);
    }

    /**
     * Get search query base on user request to filter collection of Agents
     */
    public static function get_search_agencies_query($args = []) {
        $s = isset($_GET['search_text']) ? sanitize_text_field($_GET['search_text']) : null;

        $paged   = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
        $default = [
            'post_type'      => 'opalestate_agency',
            'posts_per_page' => apply_filters('opalestate_agency_per_page', 12),
            'paged'          => $paged,
        ];
        $args    = array_merge($default, $args);

        $tax_query = [];


        if (isset($_GET['location']) && $_GET['location'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_location',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['location']),
            ];
        }

        if (isset($_GET['types']) && $_GET['types'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_types',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['types']),
            ];
        }

        if ($tax_query) {
            $args['tax_query'] = ['relation' => 'AND'];
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        $args['meta_query'] = ['relation' => 'AND'];


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
}
