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

/**
 * @class Opalestate_Favorite_Property: work as wishlist function
 *
 * @version 1.0
 */
class Opalestate_Favorite_Property {

    /**
     * @var integer $userId
     */
    protected $userId;

    /**
     * Get instance of this object
     */
    public static function get_instance() {
        static $_instance;
        if (!$_instance) {
            $_instance = new Opalestate_Favorite_Property();
        }
        return $_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {

        add_shortcode('opalestate_favorite_button', array($this, 'favorite_button'));
        add_shortcode('opalestate_user_favious_properties', array($this, 'favorite_properties'));

        /**
         * Ajax action
         */
        add_action('wp_ajax_opalestate_toggle_status', array($this, 'toggle_status'));
        add_action('wp_ajax_nopriv_opalestate_toggle_status', array($this, 'toggle_status'));
        add_action('init', array($this, 'init'));


        // show content page in user dashboard
        add_filter('opalestate_user_content_favorite_page', array($this, 'favorite_properties'));
    }

    /**
     * Set values when user logined in system
     */
    public function init() {

        global $current_user;
        wp_get_current_user();
        $this->userId = $current_user->ID;
    }

    /**
     * Allow set or remove favorite
     */
    public function toggle_status() {

        if (isset($_POST['property_id'])) {

            $property_id = absint($_POST['property_id']);

            $items = (array)get_user_meta($this->userId, 'opalestate_user_favorite', true);

            $key = array_search($property_id, $items);
            if ($key != false || $key != '') {
                unset($items[$key]);
            } else {
                $items[] = $property_id;
            }
            // remove items emty
            foreach ($items as $key => $value) {
                if (empty($value)) {
                    unset($items[$key]);
                }
            }
            update_user_meta($this->userId, 'opalestate_user_favorite', $items);
        }

        echo $this->favorite_button(array('property_id' => $property_id));

        exit;
    }


    /**
     * render favorite button in loop
     */
    public function favorite_button($atts) {
        $atts['userId'] = $this->userId;
        if (!isset($atts['property_id'])) {
            $atts['property_id'] = get_the_ID();
        }

        $items = (array)get_user_meta($this->userId, 'opalestate_user_favorite', true);

        $key             = array_search($atts['property_id'], $items);
        $atts['existed'] = $key;

        ob_start();
        echo opalestate_load_template_path('user/favorite-button', $atts);
        $ouput = ob_get_contents();
        ob_end_clean();

        return $ouput;
    }

    /**
     * show all favorited properties with pagination.
     */
    public function favorite_properties() {

        $paged    = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
        $per_page = 9;
        $items    = (array)get_user_meta($this->userId, 'opalestate_user_favorite', true);

        $args = array(
            'post_type'      => 'opalestate_property',
            'paged'          => $paged,
            'posts_per_page' => $per_page,
            'post__in'       => !empty($items) ? $items : array(9999999)
        );

        $loop = new WP_Query($args);


        return opalestate_load_template_path('user/favorite-properties', array('loop' => $loop));
    }

}

Opalestate_Favorite_Property::get_instance();
