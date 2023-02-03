<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Class Opalestate_Widget_Base
 */
abstract class Opalestate_Elementor_Widget_Base extends Widget_Base {

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['opalestate-pro'];
    }

    public function get_script_depends() {
        return ['jquery-wpopal-slick'];
    }

    public function get_style_depends() {
        return ['wpopal-slick'];
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $located = $this->locate_template($this->get_name() . '.php');

        if (!file_exists($located)) {
            return;
        }

        if (!empty($located) && file_exists($located)) {
            @ include apply_filters('opalestate_elementor_render_widget_templates', $located, $settings, $this);
        }
    }

    /**
     * Render widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @access protected
     */
    protected function content_template() {
        $located = $this->locate_template($this->get_name() . '-preview.php');

        if (!file_exists($located)) {
            return;
        }

        if (!empty($located) && file_exists($located)) {
            @ include apply_filters('opalestate_elementor_preview_widget_templates', $located, $this);
        }
    }

    /**
     * Locate template.
     *
     * @param string $template_name Template name.
     *
     * @return string
     */
    protected function locate_template($template_name) {
        // Locate in your {theme}/opalestate-elementor.
        $template = locate_template([
            trailingslashit('opalestate/elementor-templates/') . $template_name,
        ]);

        // Fallback to default template in the plugin.
        if (!$template) {
            $template = OPALESTATE_PLUGIN_DIR . 'templates/elementor-templates/' . $template_name;
        }


        // Return what we found.
        return apply_filters('abrs_elementor_locate_template', $template, $template_name);
    }


    /**
     * Register image carousel widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    public function get_settings_json($settings) {
        $column              = !empty($settings['slides_to_show']) ? $settings['slides_to_show'] : 4;
        $column_widescreen   = !empty($settings['slides_to_show_widescreen']) ? $settings['slides_to_show_widescreen'] : $column;
        $column_laptop       = !empty($settings['slides_to_show_laptop']) ? $settings['slides_to_show_laptop'] : $column_widescreen;
        $column_tablet_extra = !empty($settings['slides_to_show_tablet_extra']) ? $settings['slides_to_show_tablet_extra'] : $column_laptop;
        $column_tablet       = !empty($settings['slides_to_show_tablet']) ? $settings['slides_to_show_tablet'] : 2;
        $column_mobile_extra = !empty($settings['slides_to_show_mobile_extra']) ? $settings['slides_to_show_mobile_extra'] : $column_tablet;
        $column_mobile       = !empty($settings['slides_to_show_mobile']) ? $settings['slides_to_show_mobile'] : 1;
        $data                = [
            "slides_to_show"              => $column,
            "slides_to_show_mobile"       => $column_mobile,
            "slides_to_show_widescreen"   => $column_widescreen,
            "slides_to_show_laptop"       => $column_laptop,
            "slides_to_show_tablet"       => $column_tablet,
            "slides_to_show_tablet_extra" => $column_tablet_extra,
            "slides_to_show_mobile_extra" => $column_mobile_extra,
            "slides_to_scroll"            => $settings['slides_to_scroll'],
            "slides_column_gap"           => $settings['slides_column_gap'],
            "navigation"                  => $settings['navigation'],
            "pause_on_hover"              => $settings['pause_on_hover'],
            "autoplay"                    => $settings['autoplay'],
            "autoplay_speed"              => $settings['autoplay_speed'],
            "infinite"                    => $settings['infinite'],
            "speed"                       => $settings['speed'],
            "direction"                   => $settings['direction'],
        ];

        return wp_json_encode($data);
    }


    public function render_content() {
        $settings = $this->get_settings_for_display();

        if (!isset($settings['enable_carousel'])) {
            return parent::render_content();
        }

        if ($settings['enable_carousel'] === 'yes') {
            $arrows_position_class = isset($settings['arrows_position']) && $settings['arrows_position'] ? 'slick-arrows-' . $settings['arrows_position'] : '';
            $this->add_render_attribute('wrapper-style', 'class', 'elementor-slick-slider-row row-items ' . $arrows_position_class);

            $data = $this->get_settings_json($settings);
            $this->add_render_attribute('wrapper', 'data-settings', $data);

            echo '<div class="elementor-opal-slick-slider elementor-slick-slider" ' . $this->get_render_attribute_string('wrapper') . '>';
        } else {
            $this->add_render_attribute('wrapper-style', 'class', 'elementor-grid row-items elementor-items-container');
        }

        parent::render_content();

        if ($settings['enable_carousel'] === 'yes') {
            echo '</div>';
        }
    }

    /**
     * Register image carousel widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    public function add_slick_controls($condition, $slick_class) {

        $slick_class = ' .elementor-opal-slick-slider ';

        $this->start_controls_section(
            'section_carousel_options',
            [
                'label'     => esc_html__('Carousel Options', 'opalestate-pro'),
                'type'      => Controls_Manager::SECTION,
                'condition' => $condition,
            ]
        );


        $slides_to_show = range(1, 10);
        $slides_to_show = array_combine($slides_to_show, $slides_to_show);

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label'              => esc_html__('Slides to Show', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                                            '' => esc_html__('Default', 'opalestate-pro'),
                                        ] + $slides_to_show,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'slides_to_scroll',
            [
                'label'              => esc_html__('Slides to Scroll', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'description'        => esc_html__('Set how many slides are scrolled per swipe.', 'opalestate-pro'),
                'default'            => '2',
                'options'            => $slides_to_show,
                'condition'          => [
                    'slides_to_show!' => '1',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'slides_column_gap',
            [
                'label'              => esc_html__('Slides Columns Gap', 'opalestate-pro'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors'          => [
                    '{{WRAPPER}} .elementor-slick-slider-row'              => 'margin-left: calc({{SIZE}}{{UNIT}} / -2); margin-right: calc({{SIZE}}{{UNIT}}/ -2)',
                    '{{WRAPPER}} .elementor-slick-slider-row .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2)',

                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label'              => esc_html__('Navigation', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'both',
                'options'            => [
                    'both'   => esc_html__('Arrows and Dots', 'opalestate-pro'),
                    'arrows' => esc_html__('Arrows', 'opalestate-pro'),
                    'dots'   => esc_html__('Dots', 'opalestate-pro'),
                    'none'   => esc_html__('None', 'opalestate-pro'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'view',
            [
                'label'   => esc_html__('View', 'opalestate-pro'),
                'type'    => Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_additional_options',
            [
                'label'     => esc_html__('Carousel Additional Options', 'opalestate-pro'),
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label'              => esc_html__('Pause on Hover', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'yes',
                'options'            => [
                    'yes' => esc_html__('Yes', 'opalestate-pro'),
                    'no'  => esc_html__('No', 'opalestate-pro'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'              => esc_html__('Autoplay', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'yes',
                'options'            => [
                    'yes' => esc_html__('Yes', 'opalestate-pro'),
                    'no'  => esc_html__('No', 'opalestate-pro'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label'              => esc_html__('Autoplay Speed', 'opalestate-pro'),
                'type'               => Controls_Manager::NUMBER,
                'default'            => 5000,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label'              => esc_html__('Infinite Loop', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'yes',
                'options'            => [
                    'yes' => esc_html__('Yes', 'opalestate-pro'),
                    'no'  => esc_html__('No', 'opalestate-pro'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'effect',
            [
                'label'              => esc_html__('Effect', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'slide',
                'options'            => [
                    'slide' => esc_html__('Slide', 'opalestate-pro'),
                    'fade'  => esc_html__('Fade', 'opalestate-pro'),
                ],
                'condition'          => [
                    'slides_to_show' => '1',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'              => esc_html__('Animation Speed', 'opalestate-pro'),
                'type'               => Controls_Manager::NUMBER,
                'default'            => 500,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'direction',
            [
                'label'              => esc_html__('Direction', 'opalestate-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'ltr',
                'options'            => [
                    'ltr' => esc_html__('Left', 'opalestate-pro'),
                    'rtl' => esc_html__('Right', 'opalestate-pro'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        $a = array_merge($condition, [
            'navigation' => [
                'arrows',
                'dots',
                'both',

            ],
        ]);
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'     => esc_html__('Navigation', 'opalestate-pro'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => $a,
            ]
        );

        $this->add_control(
            'heading_style_arrows',
            [
                'label'     => esc_html__('Arrows', 'opalestate-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'arrows_position',
            [
                'label'     => esc_html__('Position', 'opalestate-pro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'inside',
                'options'   => [
                    'default' => esc_html__('Default', 'opalestate-pro'),
                    'inside'  => esc_html__('Inside', 'opalestate-pro'),
                    'outside' => esc_html__('Outside', 'opalestate-pro'),
                ],
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label'     => esc_html__('Size', 'opalestate-pro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 20,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $slick_class . '.slick-slider .slick-prev:before, {{WRAPPER}} ' . $slick_class . '.slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $slick_class . '.slick-slider .slick-prev:before, {{WRAPPER}} ' . $slick_class . '.slick-slider .slick-next:before' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'navigation' => ['arrows', 'both'],
                ],
            ]
        );

        $this->add_control(
            'heading_style_dots',
            [
                'label'     => esc_html__('Dots', 'opalestate-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'dots_position',
            [
                'label'     => esc_html__('Position', 'opalestate-pro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'outside',
                'options'   => [
                    'outside' => esc_html__('Outside', 'opalestate-pro'),
                    'inside'  => esc_html__('Inside', 'opalestate-pro'),
                ],
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'dots_size',
            [
                'label'     => esc_html__('Size', 'opalestate-pro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 5,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $slick_class . ' .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $slick_class . ' .slick-dots li button:before' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'navigation' => ['dots', 'both'],
                ],
            ]
        );

        $this->end_controls_section();

        //   if( $this->image_control ){
        //   $this->add_image_control( $condition, $slick_class );
        //  }

    }
}
