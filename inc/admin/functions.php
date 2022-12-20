<?php
/**
 * Admin functions
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @param string $key Options array key
 *
 * @return mixed        Option value
 * @since  0.1.0
 *
 */
function opalestate_get_option($key = '', $default = false) {
    global $opalestate_options;
    $value = !empty($opalestate_options[$key]) ? $opalestate_options[$key] : $default;
    $value = apply_filters('opalestate_get_option', $value, $key, $default);

    return apply_filters('opalestate_get_option_' . $key, $value, $key, $default);
}

/**
 * Update an option
 *
 * Updates an opalestate setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *          the key from the opalestate_options array.
 *
 * @param string $key The Key to update
 * @param string|bool|int $value The value to set the key to
 *
 * @return boolean True if updated, false if not.
 * @since 1.0
 *
 */
function opalestate_update_option($key = '', $value = false) {

    // If no key, exit
    if (empty($key)) {
        return false;
    }

    if (empty($value)) {
        $remove_option = opalestate_delete_option($key);

        return $remove_option;
    }

    // First let's grab the current settings
    $options = get_option('opalestate_settings');

    // Let's let devs alter that value coming in
    $value = apply_filters('opalestate_update_option', $value, $key);

    // Next let's try to update the value
    $options[$key] = $value;
    $did_update    = update_option('opalestate_settings', $options);

    // If it updated, let's update the global variable
    if ($did_update) {
        global $opalestate_options;
        $opalestate_options[$key] = $value;
    }

    return $did_update;
}

/**
 * Remove an option
 *
 * Removes an opalestate setting value in both the db and the global variable.
 *
 * @param string $key The Key to delete
 *
 * @return boolean True if updated, false if not.
 * @since 1.0
 *
 */
function opalestate_delete_option($key = '') {

    // If no key, exit
    if (empty($key)) {
        return false;
    }

    // First let's grab the current settings
    $options = get_option('opalestate_settings');

    // Next let's try to update the value
    if (isset($options[$key])) {

        unset($options[$key]);

    }

    $did_update = update_option('opalestate_settings', $options);

    // If it updated, let's update the global variable
    if ($did_update) {
        global $opalestate_options;
        $opalestate_options = $options;
    }

    return $did_update;
}


/**
 * Get Settings
 *
 * Retrieves all Opalestate plugin settings
 *
 * @return array Opalestate settings
 * @since 1.0
 */
function opalestate_get_settings() {

    $settings = get_option('opalestate_settings');

    return (array)apply_filters('opalestate_get_settings', $settings);

}

/**
 * Gateways Callback
 *
 * Renders gateways fields.
 *
 * @return void
 * @global $opalestate_options Array of all the Opalestate Options
 * @since 1.0
 *
 */
function opalestate_enabled_gateways_callback($field_object, $escaped_value, $object_id, $object_type, $field_type_object) {

    $id                = $field_type_object->field->args['id'];
    $field_description = $field_type_object->field->args['desc'];
    $gateways          = opalestate_get_payment_gateways();

    echo '<ul class="cmb2-checkbox-list cmb2-list">';

    foreach ($gateways as $key => $option) :

        if (is_array($escaped_value) && array_key_exists($key, $escaped_value)) {
            $enabled = '1';
        } else {
            $enabled = null;
        }

        echo '<li><input name="' . $id . '[' . $key . ']" id="' . $id . '[' . $key . ']" type="checkbox" value="1" ' . checked('1', $enabled, false) . '/>&nbsp;';
        echo '<label for="' . $id . '[' . $key . ']">' . $option['admin_label'] . '</label></li>';

    endforeach;

    if ($field_description) {
        echo '<p class="cmb2-metabox-description">' . $field_description . '</p>';
    }

    echo '</ul>';


}

/**
 * Gateways Callback (drop down)
 *
 * Renders gateways select menu
 *
 * @param $field_object , $escaped_value, $object_id, $object_type, $field_type_object Arguments passed by CMB2
 *
 * @return void
 * @since 1.0
 *
 */
function opalestate_default_gateway_callback($field_object, $escaped_value, $object_id, $object_type, $field_type_object) {

    $id                = $field_type_object->field->args['id'];
    $field_description = $field_type_object->field->args['desc'];
    $gateways          = opalestate_get_enabled_payment_gateways();

    echo '<select class="cmb2_select" name="' . $id . '" id="' . $id . '">';

    //Add a field to the Opalestate Form admin single post view of this field
    if ($field_type_object->field->object_type === 'post') {
        echo '<option value="global">' . esc_html__('Global Default', 'opalestate-pro') . '</option>';
    }

    foreach ($gateways as $key => $option) :

        $selected = isset($escaped_value) ? selected($key, $escaped_value, false) : '';


        echo '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($option['admin_label']) . '</option>';

    endforeach;

    echo '</select>';

    echo '<p class="cmb2-metabox-description">' . $field_description . '</p>';

}

/**
 * Opalestate Title
 *
 * Renders custom section titles output; Really only an <hr> because CMB2's output is a bit funky
 *
 * @param       $field_object , $escaped_value, $object_id, $object_type, $field_type_object
 *
 * @return void
 * @since 1.0
 *
 */
function opalestate_title_callback($field_object, $escaped_value, $object_id, $object_type, $field_type_object) {

    $id                = $field_type_object->field->args['id'];
    $title             = $field_type_object->field->args['name'];
    $field_description = $field_type_object->field->args['desc'];

    echo '<hr>';

}

/**
 * Gets a number of posts and displays them as options
 *
 * @param array $query_args Optional. Overrides defaults.
 * @param bool $force Force the pages to be loaded even if not on settings
 *
 * @return array An array of options that matches the CMB2 options array
 * @see: https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types
 */
function opalestate_cmb2_get_post_options($query_args, $force = false) {

    $post_options = ['' => '']; // Blank option

    if ((!isset($_GET['page']) || 'opalestate-settings' != $_GET['page']) && !$force) {
        return $post_options;
    }

    $args = wp_parse_args($query_args, [
        'post_type'   => 'page',
        'numberposts' => 10,
    ]);

    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $post) {

            $post_options[$post->ID] = $post->post_title;

        }
    }

    return $post_options;
}


/**
 * Modify CMB2 Default Form Output
 *
 * @param string @args
 *
 * @since 1.0
 */

add_filter('cmb2_get_metabox_form_format', 'opalestate_modify_cmb2_form_output', 10, 3);

function opalestate_modify_cmb2_form_output($form_format, $object_id, $cmb) {

    //only modify the opalestate settings form
    if ('opalestate_settings' == $object_id && 'options_page' == $cmb->cmb_id) {

        return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="opalestate-submit-wrap"><input type="submit" name="submit-cmb" value="' . esc_html__('Save Settings',
                'opalestate-pro') . '" class="button-primary"></div></form>';
    }

    return $form_format;

}


/**
 * Opalestate License Key Callback
 *
 * @description Registers the license field callback for EDD's Software Licensing
 * @param array $field_object , $escaped_value, $object_id, $object_type, $field_type_object Arguments passed by CMB2
 *
 * @return void
 * @since       1.0
 *
 */
if (!function_exists('opalestate_license_key_callback')) {
    function opalestate_license_key_callback($field_object, $escaped_value, $object_id, $object_type, $field_type_object) {

        $id                = $field_type_object->field->args['id'];
        $field_description = $field_type_object->field->args['desc'];
        $license_status    = get_option($field_type_object->field->args['options']['is_valid_license_option']);
        $field_classes     = 'regular-text opalestate-license-field';
        $type              = empty($escaped_value) ? 'text' : 'password';

        if ($license_status === 'valid') {
            $field_classes .= ' opalestate-license-active';
        }

        $html = $field_type_object->input([
            'class' => $field_classes,
            'type'  => $type,
        ]);

        //License is active so show deactivate button
        if ($license_status === 'valid') {
            $html .= '<input type="submit" class="button-secondary opalestate-license-deactivate" name="' . $id . '_deactivate" value="' . esc_html__('Deactivate License', 'opalestate-pro') . '"/>';
        } else {
            //This license is not valid so delete it
            opalestate_delete_option($id);
        }

        $html .= '<label for="opalestate_settings[' . $id . ']"> ' . $field_description . '</label>';

        wp_nonce_field($id . '-nonce', $id . '-nonce');

        echo $html;
    }
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @param array $args Arguments passed by the setting
 *
 * @return void
 * @since 1.0
 *
 */
function opalestate_hook_callback($args) {
    do_action('opalestate_' . $args['id']);
}

/**
 * Searches for users via ajax and returns a list of results
 *
 * @return void
 * @since  1.0
 *
 */
function opalestate_ajax_search_agencies() {
    if (current_user_can('manage_opalestate_settings')) {
        $search_query = trim($_GET['q']);

        $agents_objects = Opalestate_Query::get_agencies([
            'posts_per_page' => -1,
            's'              => $search_query,
        ]);

        $agents = [];
        if (!empty($agents_objects->posts) && is_array($agents_objects->posts)) {
            foreach ($agents_objects->posts as $object) {
                $agents[] = [
                    'id'          => $object->ID,
                    'name'        => $object->post_title,
                    'avatar_url'  => 'https://avatars1.githubusercontent.com/u/9919?v=4',
                    'full_name'   => $object->post_title,
                    'description' => 'okokok',
                ];
            }
        }
        $output = [
            'total_count'        => count($agents),
            'items'              => $agents,
            'incomplete_results' => false,
        ];
        echo json_encode($output);
    }
    die();
}

add_action('wp_ajax_opalestate_search_agencies', 'opalestate_ajax_search_agencies');


/**
 * Searches for users via ajax and returns a list of results
 *
 * @return void
 * @since  1.0
 *
 */
function opalestate_ajax_search_agents() {
    if (current_user_can('manage_opalestate_settings')) {
        $search_query = trim($_GET['q']);

        $agents_objects = Opalestate_Query::get_agents([
            'posts_per_page' => -1,
            's'              => $search_query,
        ]);

        $agents = [];
        if (!empty($agents_objects->posts) && is_array($agents_objects->posts)) {
            foreach ($agents_objects->posts as $object) {
                $agents[] = [
                    'id'          => $object->ID,
                    'name'        => $object->post_title,
                    'avatar_url'  => 'https://avatars1.githubusercontent.com/u/9919?v=4',
                    'full_name'   => $object->post_title,
                    'description' => 'okokok',
                ];
            }
        }
        $output = [
            'total_count'        => count($agents),
            'items'              => $agents,
            'incomplete_results' => false,
        ];
        echo json_encode($output);
    }
    die();
}

add_action('wp_ajax_opalestate_search_agents', 'opalestate_ajax_search_agents');


/**
 * Searches for users via ajax and returns a list of results
 *
 * @return void
 * @since  1.0
 *
 */
function opalestate_ajax_search_users() {

    if (current_user_can('manage_opalestate_settings')) {

        $search_query = trim($_GET['q']);

        $get_users_args = [
            'number' => 9999,
            'search' => $search_query . '*',
        ];

        if (!empty($exclude)) {
            $exclude_array             = explode(',', $exclude);
            $get_users_args['exclude'] = $exclude_array;
        }

        $get_users_args = apply_filters('opalestate_search_users_args', $get_users_args);

        $found_users = apply_filters('opalestate_ajax_found_users', get_users($get_users_args), $search_query);

        $user_list = '<ul>';
        if ($found_users) {
            foreach ($found_users as $user) {
                $user_list .= '<li><a href="#" data-userid="' . esc_attr($user->ID) . '" data-login="' . esc_attr($user->user_login) . '">' . esc_html($user->user_login) . '</a></li>';
            }
        } else {
            $user_list .= '<li>' . esc_html__('No users found', 'opalestate-pro') . '</li>';
        }
        $user_list .= '</ul>';

        echo json_encode(['results' => $user_list]);

    }
    die();
}

add_action('wp_ajax_opalestate_search_users', 'opalestate_ajax_search_users');

function opalestate_ajax_search_username() {

    $search_query = trim($_POST['user_name']);
    $user         = get_userdatabylogin($search_query);

    $output = [];

    if ($user) {
        $data              = $user->data;
        $data->author_link = get_author_posts_url($user->data->ID);
        $data->avatar      = get_avatar_url($user->data->ID);
        $output['message'] = esc_html__('We could find this user', 'opalestate-pro');
        $output['status']  = true;
        $output['user']    = $data;
    } else {
        $output['message'] = esc_html__('We could not find this user', 'opalestate-pro');
        $output['status']  = false;
    }

    echo json_encode($output);
    exit;

}

add_action('wp_ajax_opalestate_ajax_search_username', 'opalestate_ajax_search_username');
