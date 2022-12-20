<?php
/**
 * $Desc$
 *
 * @version    $Id$
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

function opalestate_membership_settings_submission($fields) {
    $tmp = [
        [
            'name'       => esc_html__('Free Submission', 'opalestate-pro'),
            'id'         => 'opalestate_title_free_submission_settings',
            'type'       => 'title',
            'before_row' => '<hr>',
            'after_row'  => '<hr>',
        ],
        [
            'name'    => esc_html__('Enable Free Submission', 'opalestate-pro'),
            'desc'    => esc_html__('Allow set automatic a free package.', 'opalestate-pro'),
            'id'      => 'enabel_free_submission',
            'type'    => 'switch',
            'options' => [
                1 => esc_html__('Yes', 'opalestate-pro'),
                0 => esc_html__('No', 'opalestate-pro'),
            ],
        ],
        [
            'name'       => esc_html__('Number Free Listing', 'opalestate-pro'),
            'desc'       => esc_html__('Maximum number of Free Listing that users can submit.', 'opalestate-pro'),
            'id'         => 'free_number_listing',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
            ],
            'default'    => 3,
        ],
        [
            'name'       => esc_html__('Number Free Featured', 'opalestate-pro'),
            'desc'       => esc_html__('Maximum number of Free Featured that users can set.', 'opalestate-pro'),
            'id'         => 'free_number_featured',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
            ],
            'default'    => 3,
        ],
    ];

    return array_merge($fields, $tmp);
}

add_filter('opalestate_settings_submission', 'opalestate_membership_settings_submission');

if (opalestate_options('enabel_free_submission')) {
    function opalestate_check_is_membership_valid($status, $package_id, $user_id) {
        if ($package_id != -1) {
            return false;
        }
        $package_expired = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_expired', true);

        if (!is_numeric($package_expired)) {
            $package_expired = strtotime($package_expired);
        }
        if (!$package_expired || $package_expired <= time()) {
            return false;
        }

        return true;
    }

    add_filter('opalmembership_check_is_membership_valid', 'opalestate_check_is_membership_valid', 3, 3);

    /**
     *
     */
    function opalestate_get_freepackage_obj() {

        $object             = new Opalmembership_Package();
        $object->post_title = esc_html__('Free membership', 'opalestate-pro');

        return $object;

    }

    add_filter('opalmembership_get_object_membership', 'opalestate_get_freepackage_obj');

    /// free account
    add_action('user_register', 'opalestate_on_create_user', 10, 1);
    add_action('profile_update', 'opalestate_on_update_user');
    function opalestate_on_create_user($user_id) {
        if ($user_id) {
            opalestate_reset_user_free_package($user_id);
        }
    }

    function opalestate_on_update_user($user_id) {
        $package_id = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_id', true);
        if (empty($package_id)) {
            opalestate_reset_user_free_package($user_id);
        }
    }
}
?>
