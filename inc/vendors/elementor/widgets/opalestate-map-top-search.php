<?php

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor icon box widget.
 *
 * Elementor widget that displays an icon, a headline and a text.
 *
 */
class Opalestate_map_top_search_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve icon box widget name.
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'opalestate-map-top-search';
    }

    /**
     * Get widget title.
     *
     * Retrieve icon box widget title.
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Search: Maps Preview', 'opalestate-pro');
    }

    /**
     * Get widget icon.
     *
     * Retrieve icon box widget icon.
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return apply_filters('opalestate_' . $this->get_name(), 'eicon-search');
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['opalestate-pro', 'search'];
    }

    /**
     * Register icon box widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @access protected
     */
    protected function register_controls() {
        $this->start_controls_section(
            'map_preview_form',
            [
                'label'       => esc_html__('Map Preview Form', 'opalestate-pro'),
                'description' => ' '
            ]

        );

        $this->add_control(
            's_form_description',
            [
                'raw'             => esc_html__('This is often used for building seach page, it combines with block => Search: Property Form, Search: Property Results.', 'opalestate-pro'),
                'type'            => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
            ]
        );


        $this->add_control(
            'enable_static',
            [
                'label'   => esc_html__('Enable Static Map', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => ''
            ]
        );

        $this->add_control(
            'static_mode_right',
            [
                'label'     => esc_html__('Map On Right?', 'opalestate-pro'),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_static!' => ''
                ]
            ]
        );


        $this->add_responsive_control(
            'map_width',
            [
                'label' => esc_html__('Map Width %', 'opalestate-pro'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px'      => [
                        'min' => 20,
                        'max' => 100,
                    ],
                    'default' => 50
                ],

                'selectors' => [
                    '{{WRAPPER}} .opalestate-map-preview-wrap' => 'Width: {{SIZE}}%'

                ],
                'condition' => [
                    'enable_static!' => ''
                ]
            ]
        );

        $this->add_responsive_control(
            'map_height',
            [
                'label' => esc_html__('Map Height', 'opalestate-pro'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1200,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} #opalestate-map-preview' => 'min-height: {{SIZE}}{{UNIT}}!important'

                ]
            ]
        );


        $this->end_controls_section();
    }
}
