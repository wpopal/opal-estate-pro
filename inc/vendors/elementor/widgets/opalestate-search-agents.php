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
class Opalestate_Search_Agents_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

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
        return 'opalestate-search-agents';
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
        return esc_html__('Block: Agents - Search Form', 'opalestate-pro');
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
            'agents_search_form',
            [
                'label' => esc_html__('Agents Search Form', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'search_form',
            [
                'label'   => esc_html__('Search Form', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    ''        => esc_html__('Advanded', 'opalestate-pro'),
                    'address' => esc_html__('Search By Address', 'opalestate-pro'),
                )
            ]
        );

        $this->add_control(
            'current_uri',
            [
                'label'   => esc_html__('Target Submit Page', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 1,
                'options' => array(
                    1 => esc_html__('Current Page', 'opalestate-pro'),
                    0 => esc_html__('Global Agent Search Page', 'opalestate-pro'),
                )
            ]
        );

        $this->add_control(
            'item_layout',
            [
                'label'   => esc_html__('Item Layout', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => apply_filters('search-agents-item-layout', [
                    'grid' => esc_html__('Grid', 'opalestate-pro'),
                    'list' => esc_html__('List', 'opalestate-pro'),
                ]),
            ]
        );
        $this->add_responsive_control(
            'column',
            [
                'label'        => esc_html__('Columns', 'opalestate-pro'),
                'type'         => \Elementor\Controls_Manager::SELECT,
                'default'      => 3,
                'options'      => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6],
                'prefix_class' => 'elementor-grid%s-',
                'condition'    => [
                    'item_layout!' => 'list',
                ],

            ]
        );


        $this->add_control(
            'column_gap',
            [
                'label'     => esc_html__('Columns Gap', 'opalestate-pro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-items-container' => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}}'

                ],
                'condition' => [
                    'item_layout' => 'grid'
                ]
            ]
        );
        $this->end_controls_section();
    }
}
