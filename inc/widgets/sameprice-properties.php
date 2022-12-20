<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author      Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019  wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/questions/
 */

class Opalestate_sammeprice_properties_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
        // Base ID of your widget
            'opalestate_samepriceproperties_widget',
            // Widget name will appear in UI
            esc_html__('Estate: Same Price', 'opalestate-pro'),
            // Widget description
            array('description' => esc_html__('Similar Properties By Same Price with configured range and Status', 'opalestate-pro'),)
        );
    }

    public function widget($instance, $args) {
        $default = array(
            'num'         => 5,
            'range_price' => 100,
        );
        $args    = array_merge($default, $args);
        extract($args);
        extract($instance);
        //Check
        $tpl         = OPALESTATE_THEMER_WIDGET_TEMPLATES . 'widgets/sameprice-properties.php';
        $tpl_default = OPALESTATE_PLUGIN_DIR . 'templates/widgets/sameprice-properties.php';

        if (is_file($tpl)) {
            $tpl_default = $tpl;
        }
        require $tpl_default;
    }


    // Form

    public function form($instance) {
        //Set up some default widget settings.
        $defaults = array(
            'title'       => esc_html__('Same Price', 'opalestate-pro'),
            'num'         => '5',
            'range_price' => 1000
        );
        $instance = wp_parse_args((array)$instance, $defaults); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'opalestate-pro'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;"/>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num')); ?>"><?php echo esc_html_e('Limit:', 'opalestate-pro'); ?></label>
            <br>
            <input id="<?php echo esc_attr($this->get_field_id('num')); ?>" name="<?php echo esc_attr($this->get_field_name('num')); ?>" type="text" value="<?php echo esc_attr($instance['num']); ?>"/>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('range_price')); ?>"><?php echo esc_html__('Range Price:', 'opalestate-pro'); ?></label>
            <br>
            <input id="<?php echo esc_attr($this->get_field_id('range_price')); ?>" name="<?php echo esc_attr($this->get_field_name('range_price')); ?>" type="text" value="<?php echo esc_attr($instance['range_price']); ?>"/>
        </p>

        <?php
    }

    //Update the widget

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        //Strip tags from title and name to remove HTML
        $instance['title']       = strip_tags($new_instance['title']);
        $instance['num']         = $new_instance['num'];
        $instance['range_price'] = $new_instance['range_price'];
        return $instance;
    }

}

register_widget('Opalestate_sammeprice_properties_Widget');

?>
