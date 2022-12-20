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
 * @class   OpalEstate_Enqueue
 *
 * @version 1.0
 */
class OpalEstate_Enqueue {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'load_scripts']);
        add_action('wp_head', [$this, 'add_custom_styles']);
    }

    /**
     * Load javascript and css
     */
    public function load_scripts() {
        $api = opalestate_get_map_api_uri();

        wp_enqueue_script('opalestate-google-maps', $api, null, '0.0.1', false);

        wp_enqueue_script('infobox', OPALESTATE_PLUGIN_URL . 'assets/js/infobox.js', ['jquery'], OPALESTATE_VERSION, false);
        wp_enqueue_script('markerclusterer', OPALESTATE_PLUGIN_URL . 'assets/js/markerclusterer.js', ['jquery'], '1.3', false);

        wp_enqueue_script('opalestate-scripts', OPALESTATE_PLUGIN_URL . 'assets/js/opalestate.js', ['jquery'], OPALESTATE_VERSION, true);
        wp_enqueue_script('opalestate-country-select', OPALESTATE_PLUGIN_URL . 'assets/js/country-select.js', ['jquery'], OPALESTATE_VERSION, true);
        wp_enqueue_script('noUiSlider', OPALESTATE_PLUGIN_URL . 'assets/js/nouislider.min.js', ['jquery'], '1.0.0', true);
        wp_enqueue_script('fitvids', OPALESTATE_PLUGIN_URL . 'assets/js/jquery.fitvids.js', ['jquery'], '1.0.0', true);

        /**
         * Google map.
         */
        wp_enqueue_script('opalestate-gmap', OPALESTATE_PLUGIN_URL . 'assets/js/frontend/googlemaps.js', ['jquery'], OPALESTATE_VERSION, false);
        $custom_map_styles = json_decode((opalestate_options('google_map_custom_style', '')));
        wp_localize_script('opalestate-gmap', 'opalestateGmap', [
            'style'                     => opalestate_options('google_map_style', 'standard'),
            'autocomplete_restrictions' => opalestate_get_autocomplete_restrictions(),
            'custom_style'              => json_encode($custom_map_styles),
        ]);

        /**
         * Frontend property.
         */
        wp_enqueue_script('opalestate-messages', OPALESTATE_PLUGIN_URL . 'assets/js/frontend/property.js', ['jquery'], OPALESTATE_VERSION, false);

        /**
         * Main plugin style.
         */
        wp_enqueue_style('opalestate-style', OPALESTATE_PLUGIN_URL . '/assets/css/opalestate.css');

        /**
         * Enqueue 3rd.
         */
        wp_register_style('font-awesome', OPALESTATE_PLUGIN_URL . 'assets/3rd/fontawesome/css/all.min.css', null, '5.11.2', false);
        wp_enqueue_style('font-awesome');
        wp_enqueue_style('hint', OPALESTATE_PLUGIN_URL . 'assets/3rd/hint/hint.min.css', null, '1.3', false);
        wp_enqueue_style('select2', OPALESTATE_PLUGIN_URL . 'assets/3rd/select2/css/select2.min.css', null, '1.3', false);
        wp_enqueue_script('select2', OPALESTATE_PLUGIN_URL . 'assets/3rd/select2/js/select2.min.js', null, '1.3', false);
        wp_register_script('chart-js', OPALESTATE_PLUGIN_URL . 'assets/3rd/chartjs/chart.min.js', null, '2.8.0', true);
        wp_register_style('tooltipster', OPALESTATE_PLUGIN_URL . 'assets/3rd/tooltipster/css/tooltipster.bundle.min.css', [], false);
        wp_register_script('tooltipster', OPALESTATE_PLUGIN_URL . 'assets/3rd/tooltipster/js/tooltipster.bundle.min.js', ['jquery'], false, true);

        if (is_single_property()) {
            wp_enqueue_script('chart-js');
        }

        if (is_single_property() || is_single_agent() || is_single_agency()) {
            wp_enqueue_style('tooltipster');
            wp_enqueue_script('tooltipster');
        }

        // Load global variables
        wp_localize_script('opalestate-scripts', 'opalesateJS', [
            'ajaxurl'           => admin_url('admin-ajax.php'),
            'siteurl'           => get_template_directory_uri(),
            'mapiconurl'        => OPALESTATE_PLUGIN_URL . 'assets/map/',
            'rtl'               => is_rtl() ? 'true' : 'false',
            'confirmed'         => esc_html__('Are you sure to remove?', 'opalestate-pro'),
            'error_upload_size' => esc_html__('This file is has large volume size, please try to upload other.', 'opalestate-pro'),
            'size_image'        => opalestate_options('upload_image_max_size', 0.5) * 1000000,
            'mfile_image'       => opalestate_options('upload_image_max_files', 10),
            'size_other'        => opalestate_options('upload_other_max_size', 0.8) * 1000000,
            'mfile_other'       => opalestate_options('upload_other_max_files', 10),
        ]);

        $this->register_enqueue();
    }

    /**
     * Register and enqueue javascript, css library
     */
    public function register_enqueue() {

        wp_register_script(
            'jquery-modernizr',
            OPALESTATE_PLUGIN_URL . '/assets/3rd/magnific-popup/jquery.magnific-popup.min.js',
            [
                'jquery',
            ],
            '4.4.3',
            true
        );

        wp_enqueue_script('jquery-magnific-popup');
        wp_register_script('jquery-sticky-kit', trailingslashit(OPALESTATE_PLUGIN_URL) . 'assets/3rd/sticky/jquery.sticky-kit.min.js', [], null, true);
        wp_enqueue_script('jquery-sticky-kit');
        wp_enqueue_script('opalestate-elementor', OPALESTATE_PLUGIN_URL . 'assets/js/frontend/elementor.js', [], null, true);
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-datepicker-style', OPALESTATE_PLUGIN_URL . '/assets/3rd/datepicker.css');
        ///
        wp_register_script('jquery-toast',
            OPALESTATE_PLUGIN_URL . 'assets/3rd/toast/jquery.toast.js', [], null, true);

        wp_enqueue_script('jquery-toast');


        wp_register_script(
            'jquery-swiper',
            OPALESTATE_PLUGIN_URL . '/assets/3rd/swiper/js/swiper.min.js',
            [
                'jquery',
            ],
            '4.4.3',
            true
        );

        if (!defined("ELEMENTOR_VERSION") || is_single_property()) {
            wp_enqueue_style('jquery-swiper', OPALESTATE_PLUGIN_URL . '/assets/3rd/swiper/css/swiper.min.css', [], '4.5.0');
        }

        wp_enqueue_script('jquery-swiper');
    }

    /**
     * Add custom styles.
     */
    public function add_custom_styles() {
        $custom       = '';
        $status_color = $this->add_custom_property_status_color();
        if ($status_color) {
            $custom .= $status_color;
        }

        $label_color = $this->add_custom_property_label_color();
        if ($label_color) {
            $custom .= $label_color;
        }

        if ($custom) {
            echo '<style type="text/css">' . $custom . '</style>';
        }
    }

    /**
     * Add custom property status color.
     *
     * @return string
     */
    public function add_custom_property_status_color() {
        $statuses = Opalestate_Taxonomy_Status::get_list();
        $custom   = '';

        if ($statuses) {
            foreach ($statuses as $status) {
                $bg    = get_term_meta($status->term_id, 'opalestate_status_lb_bg', true);
                $color = get_term_meta($status->term_id, 'opalestate_status_lb_color', true);
                if ($bg || $color) {
                    $custom .= '.property-status-' . trim($status->slug) . ' { ';
                    if ($bg) {
                        $custom .= 'background-color:' . $bg . ' !important;';
                    }
                    if ($color) {
                        $custom .= 'color:' . $color . '!important';
                    }
                    $custom .= ' } ';
                }
            }
        }

        return $custom;
    }

    /**
     * Add custom property status color.
     *
     * @return string
     */
    public function add_custom_property_label_color() {
        $labels = Opalestate_Taxonomy_Label::get_list();
        $custom = '';

        if ($labels) {
            foreach ($labels as $label) {
                $bg    = get_term_meta($label->term_id, 'opalestate_label_lb_bg', true);
                $color = get_term_meta($label->term_id, 'opalestate_label_lb_color', true);
                if ($bg || $color) {
                    $custom .= '.property-label-' . trim($label->slug) . ' { ';
                    if ($bg) {
                        $custom .= 'background-color:' . $bg . ' !important;';
                    }
                    if ($color) {
                        $custom .= 'color:' . $color . '!important';
                    }
                    $custom .= ' } ';
                }
            }
        }

        return $custom;
    }

}

new OpalEstate_Enqueue();
