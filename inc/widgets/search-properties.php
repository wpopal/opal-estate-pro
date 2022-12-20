<?php
/**
 * Opalestate_search_properties_Widget
 *
 * @package     wpbase
 * @author      Team <info@wpopal.com >
 * @copyright   Copyright (C) 2019  wpopal.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/questions/
 */

class Opalestate_search_properties_Widget extends WP_Widget {
    /**
     * Opalestate_search_properties_Widget constructor.
     */
    public function __construct() {
        parent::__construct(
        // Base ID of your widget
            'opalestate_search_properties_widget',
            // Widget name will appear in UI
            __('Estate: Search Properties', 'opalestate-pro'),
            // Widget description
            ['description' => esc_html__('Search Properties widget.', 'opalestate-pro'),]
        );
    }

    public function widget($args, $instance) {
        extract($args);
        extract($instance);
        //Our variables from the widget settings.
        $title = apply_filters('widget_title', esc_attr($instance['title']));

        // Output the widget.
        echo $before_widget; // @WPCS: XSS OK.

        if ($title) {
            echo $before_title . $title . $after_title; // @WPCS: XSS OK.
        }
        ?>

        <div class="search-properies-form">
            <?php echo opalestate_load_template_path('search-box/' . $instance['style'], $instance); ?>
        </div>

        <?php
        echo $after_widget; // WPCS: XSS OK.
    }


    // Form

    public function form($instance) {
        //Set up some default widget settings.
        $defaults = apply_filters('opalestate_widget_search_properties_args', [
            'title'                => esc_html__('Search Properties', 'opalestate-pro'),
            'hidden_labels'        => 'true',
            'nobutton'             => '',
            'style'                => 'search-form-v',
            'display_country'      => 'true',
            'display_state'        => 'true',
            'display_city'         => 'true',
            'display_more_options' => 'true',
            'info_number_input'    => 'true',
        ]);


        $instance = wp_parse_args((array)$instance, $defaults); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'opalestate-pro'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;"/>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Layout', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <?php foreach (opalestate_search_properties_form_styles() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['style'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('hidden_labels')); ?>"><?php esc_html_e('Disable Labels', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('hidden_labels')); ?>" name="<?php echo esc_attr($this->get_field_name('hidden_labels')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['hidden_labels'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nobutton')); ?>"><?php esc_html_e('Disable Search button', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('nobutton')); ?>" name="<?php echo esc_attr($this->get_field_name('nobutton')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['nobutton'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_country')); ?>"><?php esc_html_e('Display Country select', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_country')); ?>" name="<?php echo esc_attr($this->get_field_name('display_country')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['display_country'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_state')); ?>"><?php esc_html_e('Display State select', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_state')); ?>" name="<?php echo esc_attr($this->get_field_name('display_state')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['display_state'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_city')); ?>"><?php esc_html_e('Display City select', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_city')); ?>" name="<?php echo esc_attr($this->get_field_name('display_city')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['display_city'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_more_options')); ?>"><?php esc_html_e('Display More Options', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_more_options')); ?>" name="<?php echo esc_attr($this->get_field_name('display_more_options')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['display_more_options'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('info_number_input')); ?>"><?php esc_html_e('Information number fields', 'opalestate-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('info_number_input')); ?>" name="<?php echo esc_attr($this->get_field_name('info_number_input')); ?>">
                <?php foreach ($this->get_boolean_options() as $option_key => $option_value) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $instance['info_number_input'], true); ?>><?php echo esc_html($option_value); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php
    }

    //Update the widget
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        //Strip tags from title and name to remove HTML
        $instance['title']                = strip_tags($new_instance['title']);
        $instance['hidden_labels']        = strip_tags($new_instance['hidden_labels']);
        $instance['nobutton']             = strip_tags($new_instance['nobutton']);
        $instance['style']                = strip_tags($new_instance['style']);
        $instance['display_country']      = strip_tags($new_instance['display_country']);
        $instance['display_state']        = strip_tags($new_instance['display_state']);
        $instance['display_city']         = strip_tags($new_instance['display_city']);
        $instance['display_more_options'] = strip_tags($new_instance['display_more_options']);
        $instance['info_number_input']    = strip_tags($new_instance['info_number_input']);

        return $instance;
    }

    protected function get_boolean_options() {
        return [
            ''     => esc_html__('No', 'opalestate-pro'),
            'true' => esc_html__('Yes', 'opalestate-pro'),
        ];
    }
}

register_widget('Opalestate_search_properties_Widget');
