<?php
/**
 * $Desc
 *
 * @version     $Id$
 * @package     wpbase
 * @author      Team <info@wpopal.com >
 * @copyright   Copyright (C) 2019  wpopal.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/questions/
 */

class Opalestate_profile_info_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
        // Base ID of your widget
            'opalestate_profile_info_widget',
            // Widget name will appear in UI
            esc_html__('Estate: User Menu Profile', 'opalestate-pro'),
            // Widget description
            ['description' => esc_html__('Display Profile information in box and menu.', 'opalestate-pro'),]
        );
    }

    /**
     *
     */
    public function widget($args, $instance) {

        global $before_widget, $after_widget, $before_title, $after_title, $post;

        if (!is_user_logged_in()) {
            return;
        }

        extract($args);

        $title = apply_filters('widget_title', $instance['title']);


        echo($before_widget);


        if ($title) {
            echo ($before_title) . $title . ($after_title);
        }
        ?>
        <div class="widget-body">
            <?php
            global $current_user;

            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                ?>

                <div class="profile-menu">
                    <?php opalestate_management_user_menu(); ?>
                </div>
            <?php }
            ?>
        </div>
        <?php
        echo($after_widget);
    }


    /**
     * Form
     */
    public function form($instance) {
        //Set up some default widget settings.
        $defaults = [
            'title' => esc_html__('My Profile', 'opalestate-pro'),
        ];
        $instance = wp_parse_args((array)$instance, $defaults); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'opalestate-pro'); ?></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;"/>
        </p>

        <?php
    }

    //Update the widget

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        //Strip tags from title and name to remove HTML
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['num']   = $new_instance['num'];

        return $instance;
    }

}

register_widget('Opalestate_profile_info_Widget');

?>
