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
 * @class   OpalEstate_Shortcodes
 *
 * @version 1.0
 */
class OpalEstate_Shortcodes {

    /**
     * Get instance of this object
     */
    public static function get_instance() {
        static $_instance;
        if (!$_instance) {
            $_instance = new OpalEstate_Shortcodes();
        }
        return $_instance;
    }

    /**
     * Static $shortcodes
     */
    public $shortcodes;

    /**
     * defined list of shortcode and functions of this for each.
     */
    public function __construct() {
        $this->shortcodes = [
            'search_properties_form'   => ['code' => 'search_properties_form', 'label' => esc_html__('Search Properties Form', 'opalestate-pro')],
            'properties'               => ['code' => 'properties', 'label' => esc_html__('Properties', 'opalestate-pro')],
            'search_properties_result' => ['code' => 'search_properties_result', 'label' => esc_html__('Search Properties Result', 'opalestate-pro')],
            'search_properties'        => ['code' => 'search_properties', 'label' => esc_html__('Search Properties', 'opalestate-pro')],
            'search_split_maps'        => ['code' => 'search_split_maps', 'label' => esc_html__('Search Split Maps', 'opalestate-pro')],
            'search_map_properties'    => ['code' => 'search_map_properties', 'label' => esc_html__('Show Map + Search Box and Properties', 'opalestate-pro')],
            'ajax_map_search'          => ['code' => 'ajax_map_search', 'label' => esc_html__('Ajax Search Map Properties And Horizontal Search', 'opalestate-pro')],
            'register_form'            => ['code' => 'register_form', 'label' => esc_html__('Register User Form', 'opalestate-pro')],
            'login_form'               => ['code' => 'login_form', 'label' => esc_html__('Login Form', 'opalestate-pro')],
        ];

        foreach ($this->shortcodes as $shortcode) {
            add_shortcode('opalestate_' . $shortcode['code'], [$this, $shortcode['code']]);
        }

        if (is_admin()) {
            add_action('media_buttons', [$this, 'shortcode_button']);
        }
    }

    /**
     * Display all properties follow user when logined
     */
    public function shortcode_button() {

    }

    /**
     * Display all properties follow user when logined
     */
    public function search_properties_form($atts = []) {

        $atts   = is_array($atts) ? $atts : [];
        $layout = 'collapse-advanced';

        $default = array(
            'hidden_labels'        => true,
            'display_more_options' => true,
            'nobutton'             => false,
            'layout'               => $layout
        );

        $atts = array_merge($default, $atts);

        return opalestate_load_template_path('search-box/' . $layout, $atts);
    }

    /**
     * Display all properties follow user when logined
     */
    public function properties($atts = []) {

        $atts = is_array($atts) ? $atts : [];

        $default = array(
            'posts_per_page'  => 9,
            'show_pagination' => true,
            'column'          => apply_filters('opalestate_properties_column_row', 3),
            'layout'          => 'content-property-grid-v2',
            'showmode'        => '',
            'categories'      => null,
            'types'           => null,
            'labels'          => null,
            'cities'          => null,
            'statuses'        => null,

        );

        $atts = array_merge($default, $atts);

        return opalestate_load_template_path('shortcodes/properties', $atts);
    }

    /**
     * [opalestate_search_properties_result] Display all properties follow user when logined
     */
    public function search_properties_result($atts = []) {

        $atts = is_array($atts) ? $atts : [];

        $default = array(
            'style'      => null,
            'style_list' => null,
            'column'     => null,
        );

        $atts = array_merge($default, $atts);

        $html = '<div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">';
        $html .= opalestate_load_template_path('shortcodes/ajax-map-search-result', $atts);
        $html .= '</div>';

        return $html;
    }

    /**
     * Display all properties follow user when logined
     */
    public function agent_property() {
        return opalestate_load_template_path('shortcodes/agent-property-listing');
    }

    /**
     * Render search property page with horizontal form and map
     */
    public function search_properties() {
        return opalestate_load_template_path('shortcodes/search-properties', ['loop' => '']);
    }

    /**
     * Render search property page with vertical form and map
     */
    public function search_split_maps($atts = []) {

        $atts = is_array($atts) ? $atts : [];

        $default = array(
            'search_form' => 'simple-city',
            'paged'       => 1
        );

        $atts = array_merge($default, $atts);

        return opalestate_load_template_path('shortcodes/search-split-maps', $atts);
    }

    /**
     * Render search property page with vertical form and map
     */
    public function search_map_properties() {
        return opalestate_load_template_path('shortcodes/search-map-properties', ['loop' => '']);
    }

    /**
     * Render search property page with vertical form and map
     */
    public function ajax_map_search() {
        wp_enqueue_script('sticky-kit', OPALESTATE_PLUGIN_URL . 'assets/js/jquery.sticky-kit.min.js');

        return opalestate_load_template_path('shortcodes/ajax-map-search', ['loop' => '']);
    }


    /*
     * Register form show up
     */
    public function register_form($atts = []) {
        $atts = shortcode_atts([
            'message'    => '',
            'redirect'   => '',
            'hide_title' => false,
        ], $atts);

        return opalestate_load_template_path('user/register-form', $atts);
    }

    /*
     * sign in show up
     */
    public function login_form($atts = []) {
        $atts = shortcode_atts([
            'message'    => '',
            'redirect'   => '',
            'hide_title' => false,
        ], $atts);

        return opalestate_load_template_path('user/login-form', $atts);
    }

}

OpalEstate_Shortcodes::get_instance();
