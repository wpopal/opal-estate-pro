<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor icon box widget.
 *
 * Elementor widget that displays an icon, a headline and a text.
 *
 */
class Opalestate_Searchbox_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

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
        return 'opalestate-searchbox';
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
        return esc_html__('Search: Property Form ', 'opalestate-pro');
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
            'property_search_form',
            [
                'label' => esc_html__('Property Search Form', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            's_form_description',
            [
                'raw'             => esc_html__('This is often used for building seach page, it combines with block => Search: Map Preview, Search: Property Results.', 'opalestate-pro'),
                'type'            => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $this->add_control(
            'style',
            [
                'label'   => esc_html__('Layout', 'opalestate-pro'),
                'type'    => Controls_Manager::SELECT,
                'options' => opalestate_search_properties_form_styles(),
                'default' => 'search-form-h',
            ]
        );

        $this->add_control(
            'hidden_labels',
            [
                'label'   => esc_html__('Disable Labels', 'opalestate-pro'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'nobutton',
            [
                'label' => esc_html__('Disable Search button', 'opalestate-pro'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'display_category',
            [
                'label'     => esc_html__('Display Category select', 'opalestate-pro'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'style' => 'search-form-v',
                ],
            ]
        );

        $this->add_control(
            'display_country',
            [
                'label'     => esc_html__('Display Country select', 'opalestate-pro'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'style' => $this->has_location_fields(),
                ],
            ]
        );

        $this->add_control(
            'display_state',
            [
                'label'     => esc_html__('Display State select', 'opalestate-pro'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'style' => $this->has_location_fields(),
                ],
            ]
        );

        $this->add_control(
            'display_city',
            [
                'label'     => esc_html__('Display City select', 'opalestate-pro'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'style' => $this->has_location_fields(),
                ],
            ]
        );

        $this->add_control(
            'display_more_options',
            [
                'label'   => esc_html__('Display More Options', 'opalestate-pro'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'info_number_input',
            [
                'label'     => esc_html__('Information number fields', 'opalestate-pro'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'style' => $this->get_vertical_form(),
                ],
            ]
        );

        $this->add_control(
            'range_unit',
            [
                'label'     => esc_html__('Range Unit', 'opalestate-pro'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'km'    => esc_html__('km', 'opalestate-pro'),
                    'miles' => esc_html__('miles', 'opalestate-pro'),
                ],
                'default'   => 'km',
                'condition' => [
                    'style' => $this->get_radius_form(),
                ],
            ]
        );

        $this->add_control(
            'max_range',
            [
                'label'     => esc_html__('Max Range', 'opalestate-pro'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '10',
                'condition' => [
                    'style' => $this->get_radius_form(),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_label_style_content',
            [
                'label' => esc_html__('Label', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_label_style');

        $this->start_controls_tab(
            'tab_label_normal',
            [
                'label' => __('Normal', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form label.opalestate-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .opalestate-search-form label.opalestate-label',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_label_hover',
            [
                'label' => __('Hover', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form label.opalestate-label:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_input_style_content',
            [
                'label' => esc_html__('Input', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'input_color',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form input, {{WRAPPER}} .opalestate-search-form input::placeholder, {{WRAPPER}} .opalestate-search-form select, {{WRAPPER}} .opalestate-search-form .select2-container--default .select2-selection--single .select2-selection__rendered, {{WRAPPER}} .opalestate-search-form .opalestate-popup .popup-head > span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_background_color',
            [
                'label'     => esc_html__('Background color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form input, {{WRAPPER}} .opalestate-search-form select, {{WRAPPER}} .opalestate-search-form .select2-container.select2-container--default .select2-selection--single' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color',
            [
                'label'     => esc_html__('Border color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form input, {{WRAPPER}} .opalestate-search-form select, {{WRAPPER}} .opalestate-search-form .select2-container.select2-container--default .select2-selection--single' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'input_typography',
                'selector' => '{{WRAPPER}} .opalestate-search-form input, {{WRAPPER}} .opalestate-search-form select, {{WRAPPER}} .opalestate-search-form .select2-container.select2-container--default .select2-selection--single',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_button_style_content',
            [
                'label' => esc_html__('Button', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form .btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label'     => esc_html__('Background color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form .btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'button_typography',
                'selector' => '{{WRAPPER}} .opalestate-search-form .btn.btn-search',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form .btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color_hover',
            [
                'label'     => esc_html__('Background color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .opalestate-search-form .btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function has_location_fields() {
        return [
            'search-form-h',
            'advanced-v2',
            'advanced-v3',
            'advanced-v4',
            'search-form-v',
            'search-form-v3',
            'collapse-keyword',
        ];
    }

    protected function get_vertical_form() {
        return [
            'search-form-v',
            'search-form-v2',
            'search-form-v3',
        ];
    }

    protected function get_radius_form() {
        return [
            'search-form-v2',
            'collapse-advanced',
            'collapse-city',
            'simple-city',
        ];
    }
}
