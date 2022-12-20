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
class Opalestate_form_builder_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

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
        return 'opalestate-form-builder';
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
        return esc_html__('Search: Form Builder', 'opalestate-pro');
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
            'form_builder_head',
            [
                'label' => esc_html__('Form Builder', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'opalestate-pro'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'fields',
            [
                'label'  => esc_html__('Brand Items', 'opalestate-pro'),
                'type'   => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name'  => 'field',
                        'label' => esc_html__('Field', 'opalestate-pro'),
                        'type'  => Controls_Manager::SELECT,

                        'options' => $this->field_types()
                    ],

                    [
                        'name'    => 'column',
                        'label'   => esc_html__('Column', 'opalestate-pro'),
                        'type'    => Controls_Manager::SELECT,
                        'options' => array(
                            '1'  => 1,
                            '2'  => 2,
                            '3'  => 3,
                            '4'  => 4,
                            '5'  => 5,
                            '6'  => 6,
                            '7'  => 7,
                            '12' => 12
                        ),
                        'default' => 4
                    ]
                ]
            ]
        );
        $this->end_controls_section();
    }

    public function field_types() {
        $files = glob(OPALESTATE_PLUGIN_DIR . '/templates/search-box/fields/*.php');

        $output = array();

        foreach ($files as $field) {
            $name          = str_replace(".php", "", basename($field));
            $label         = ucfirst(str_replace("-", " ", $name));
            $output[$name] = $label;
        }

        return $output;
    }
}
