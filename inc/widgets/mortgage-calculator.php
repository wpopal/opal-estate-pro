<?php
/**
 * Mortgage widget.
 *
 * A helper class for outputting common HTML elements, such as product drop downs
 *
 * @package     Opalestate
 * @since       1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Opalestate_Mortgage_Calculator_Widget')) {

    class Opalestate_Mortgage_Calculator_Widget extends WP_Widget {

        public function __construct() {
            parent::__construct(
            // Base ID of your widget
                'opalestate_mortgage_calculate_widget',
                // Widget name will appear in UI
                __('Estate: Mortgage Calculator', 'opalestate-pro'),
                // Widget description
                ['description' => esc_html__('Mortgage Calculator widget.', 'opalestate-pro'),]
            );
        }

        public function widget($instance, $args) {
            extract($args);
            extract($instance);

            //Check
            $tpl         = OPALESTATE_THEMER_WIDGET_TEMPLATES . 'parts/mortgage-calculator.php';
            $tpl_default = OPALESTATE_PLUGIN_DIR . 'templates/parts/mortgage-calculator.php';

            if (is_file($tpl)) {
                $tpl_default = $tpl;
            }
            require $tpl_default;
        }


        public function form($instance) {
            //Set up some default widget settings.
            $defaults = [
                'title' => esc_html__('Mortgage Calculator', 'opalestate-pro'),
            ];
            $instance = wp_parse_args((array)$instance, $defaults); ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'opalestate-pro'); ?></label>
                <input type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                       value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;"/>
            </p>

            <?php
        }
    }
}
register_widget('Opalestate_Mortgage_Calculator_Widget');
