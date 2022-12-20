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

function opalestate_clean_attachments($user_id) {


    $query = new WP_Query(
        array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'author'      => $user_id,
            'meta_query'  => array(
                array(
                    'key'     => '_pending_to_use_',
                    'value'   => 1,
                    'compare' => '>=',
                )
            )
        )
    );

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wp_delete_attachment(get_the_ID());
        }
    }
    wp_reset_postdata();
}


/****/
add_filter('pre_get_posts', 'opalestate_archives_property_query', 1);
function opalestate_archives_property_query($query) {

    if ($query->is_main_query() && (is_post_type_archive('opalestate_property') || is_tax('property_category') || is_tax('opalestate_amenities') || is_tax('opalestate_location') || is_tax('opalestate_types'))) {

        $args     = array();
        $ksearchs = array();

        if (isset($_REQUEST['opalsortable']) && !empty($_REQUEST['opalsortable'])) {
            $ksearchs = explode("_", $_REQUEST['opalsortable']);
        }

        if (!empty($ksearchs) && count($ksearchs) == 2) {
            $args['meta_key'] = OPALESTATE_PROPERTY_PREFIX . $ksearchs[0];
            $args['orderby']  = 'meta_value_num';
            $args['order']    = $ksearchs[1];
        }

        if (isset($_GET['status']) && !empty($_GET['status']) && $_GET['status'] != 'all') {
            $tax_query         = array(
                array(
                    'taxonomy' => 'opalestate_status',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['status']),
                ),
            );
            $args['tax_query'] = array('relation' => 'AND');
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        if ($args) {
            foreach ($args as $key => $value) {
                $query->set($key, $value);
            }
        }

    }
}
