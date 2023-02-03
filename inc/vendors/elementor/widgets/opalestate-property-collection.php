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
class Opalestate_Property_collection_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

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
        return 'opalestate-property-collection';
    }

    /**
     * Retrieve the list of scripts the image carousel widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
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
        return esc_html__('Block: Property Collection', 'opalestate-pro');
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
            'head',
            [
                'label' => esc_html__('Property Collection', 'opalestate-pro'),
            ]
        );


        $this->add_control(
            'style',
            [
                'label'   => esc_html__('Style Item Layout', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => apply_filters('opalestate_get_loop_property_layouts', $this->get_template_post_type()),
                'default' => 'grid',
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
                    'enable_carousel' => '',
                ],
            ]
        );

        $this->add_control(
            'column_gap',
            [
                'label'     => esc_html__('Columns Gap', 'opalestate-pro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px'      => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'default' => 30
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-items-container' => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}}',

                ],
                'condition' => [
                    'enable_carousel' => '',
                ],
            ]
        );

        $this->add_control(
            'showmode',
            [
                'label'   => esc_html__('Show', 'opalestate-pro'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    'featured' => esc_html__('Featured only', 'opalestate-pro'),
                    'normal'   => esc_html__('Without Featured', 'opalestate-pro'),
                    'all'      => esc_html__('All', 'opalestate-pro'),
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


        $this->add_slick_controls(['enable_carousel' => 'yes'], ' .product-slick-carousel ');

        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => esc_html__('Posts Per Page', 'opalestate-pro'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
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
                    'post_date'  => esc_html__('Date', 'opalestate-pro'),
                    'post_title' => esc_html__('Title', 'opalestate-pro'),
                    'menu_order' => esc_html__('Menu Order', 'opalestate-pro'),
                    'rand'       => esc_html__('Random', 'opalestate-pro'),
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
                'label'    => esc_html__('Categories', 'opalestate-pro'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_post_categories(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'cat_operator',
            [
                'label'     => esc_html__('Category Operator', 'opalestate-pro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'IN',
                'options'   => [
                    'AND'    => esc_html__('AND', 'opalestate-pro'),
                    'IN'     => esc_html__('IN', 'opalestate-pro'),
                    'NOT IN' => esc_html__('NOT IN', 'opalestate-pro'),
                ],
                'condition' => [
                    'categories!' => '',
                ],
            ]
        );


        $this->add_control(
            'types',
            [
                'label'    => esc_html__('Types', 'opalestate-pro'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_post_types(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'labels',
            [
                'label'    => esc_html__('Lables', 'opalestate-pro'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_post_labels(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'statuses',
            [
                'label'    => esc_html__('Status', 'opalestate-pro'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_post_statuses(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'cities',
            [
                'label'    => esc_html__('Cities', 'opalestate-pro'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_post_cities(),
                'multiple' => true,
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'section_pagination',
            [
                'label' => esc_html__('Pagination', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label'   => esc_html__('Pagination', 'opalestate-pro'),
                'type'    => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    ''     => esc_html__('None', 'opalestate-pro'),
                    'show' => esc_html__('Show', 'opalestate-pro'),
                ],
            ]
        );

        $this->add_control(
            'pagination_page_limit',
            [
                'label'     => esc_html__('Page Limit', 'opalestate-pro'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => '3',
                'condition' => [
                    'pagination' => 'show',
                ],
            ]
        );

        // $this->add_control(
        // 	'pagination_numbers_shorten',
        // 	[
        // 		'label'     => esc_html__( 'Shorten', 'opalestate-pro' ),
        // 		'type'      => Controls_Manager::SWITCHER,
        // 		'default'   => '',
        // 		'condition' => [
        // 			'pagination_type' => [
        // 				'numbers',
        // 				'numbers_and_prev_next',
        // 			],
        // 		],
        // 	]
        // );
        //
        // $this->add_control(
        // 	'pagination_prev_label',
        // 	[
        // 		'label'     => esc_html__( 'Previous Label', 'opalestate-pro' ),
        // 		'default'   => esc_html__( '&laquo; Previous', 'opalestate-pro' ),
        // 		'condition' => [
        // 			'pagination_type' => [
        // 				'prev_next',
        // 				'numbers_and_prev_next',
        // 			],
        // 		],
        // 	]
        // );
        //
        // $this->add_control(
        // 	'pagination_next_label',
        // 	[
        // 		'label'     => esc_html__( 'Next Label', 'opalestate-pro' ),
        // 		'default'   => esc_html__( 'Next &raquo;', 'opalestate-pro' ),
        // 		'condition' => [
        // 			'pagination_type' => [
        // 				'prev_next',
        // 				'numbers_and_prev_next',
        // 			],
        // 		],
        // 	]
        // );

        // $this->add_control(
        // 	'pagination_align',
        // 	[
        // 		'label'     => esc_html__( 'Alignment', 'opalestate-pro' ),
        // 		'type'      => Controls_Manager::CHOOSE,
        // 		'options'   => [
        // 			'flex-start' => [
        // 				'title' => esc_html__( 'Left', 'opalestate-pro' ),
        // 				'icon'  => 'eicon-h-align-left',
        // 			],
        // 			'center'     => [
        // 				'title' => esc_html__( 'Center', 'opalestate-pro' ),
        // 				'icon'  => 'eicon-h-align-center',
        // 			],
        // 			'flex-end'   => [
        // 				'title' => esc_html__( 'Right', 'opalestate-pro' ),
        // 				'icon'  => 'eicon-h-align-righ',
        // 			],
        // 		],
        // 		'default'   => 'flex-start',
        // 		'selectors' => [
        // 			'{{WRAPPER}} .pagination' => 'justify-content: {{VALUE}};',
        // 		],
        // 		'condition' => [
        // 			'pagination_type!' => '',
        // 		],
        // 	]
        // );

        $this->end_controls_section();
    }

    protected function get_post_labels() {
        $categories = Opalestate_Taxonomy_Label::get_list();

        $results = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
    }

    protected function get_post_types() {
        $categories = Opalestate_Taxonomy_Type::get_list();

        $results = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
    }

    protected function get_post_categories() {

        $categories = Opalestate_Taxonomy_Categories::get_list();

        $results = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
    }


    protected function get_post_statuses() {

        $categories = Opalestate_Taxonomy_Status::get_list();

        $results = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
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


    private function get_template_post_type() {
        $folderes = glob(OPALESTATE_PLUGIN_DIR . '/templates/content-property-*');
        $output   = [];

        foreach ($folderes as $folder) {
            $folder          = str_replace("content-property-", '', str_replace('.php', '', wp_basename($folder)));
            $value           = str_replace('_', ' ', str_replace('-', ' ', ucfirst($folder)));
            $output[$folder] = $value;
        }

        return $output;
    }
}
