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
class Opalestate_City_List_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

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
        return 'opalestate-city-list';
    }

    /**
     * Retrieve the list of scripts the image carousel widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @return array Widget scripts dependencies.
     * @since  1.3.0
     * @access public
     *
     */
    public function get_script_depends() {
        return ['jquery-wpopal-slick'];
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
        return esc_html__('Block: City Listing', 'opalestate-pro');
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
            'category_search_form',
            [
                'label' => esc_html__('City Collection', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'search_form',
            [
                'label'   => esc_html__('Search Form', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    ''        => esc_html__('Advanded', 'opalestate-pro'),
                    'address' => esc_html__('Search By Address', 'opalestate-pro'),
                ],
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
                    'enable_carousel!' => 'yes',
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
                    '{{WRAPPER}} .elementor-items-container' => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}}',

                ],
                'condition' => [
                    'enable_carousel!' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => esc_html__('Enable Carousel', 'opalestate-pro'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );
        $this->end_controls_section();

        $this->add_slick_controls(['enable_carousel' => 'yes'], ' .agency-slick-carousel ');

        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'opalestate-pro'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => esc_html__('Order By', 'opalestate-pro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'post_date',
                'options' => [
                    'term_id' => esc_html__('ID', 'opalestate-pro'),
                    'name'    => esc_html__('Name', 'opalestate-pro'),
                    'count'   => esc_html__('Count', 'opalestate-pro'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => esc_html__('Order', 'opalestate-pro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__('ASC', 'opalestate-pro'),
                    'desc' => esc_html__('DESC', 'opalestate-pro'),
                ],
            ]
        );

        $this->add_control(
            'categories',
            [
                'label'    => esc_html__('Cities', 'opalestate-pro'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_post_cities(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'limit',
            [
                'label'       => esc_html__('Limit', 'opalestate-pro'),
                'description' => esc_html__('Maximum number of terms to return.', 'opalestate-pro'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 5,
            ]
        );

        $this->end_controls_section();
    }

    protected function get_post_cities() {
        $categories = Opalestate_Taxonomy_City::get_list();

        $results = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
    }
}
