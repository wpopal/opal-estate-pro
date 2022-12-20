<?php
/**
 * Class Opalestate_User_MetaBox
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

class Opalestate_User_MetaBox {

    public function get_front_base_field($prefix) {
        $fields = [
            [
                'name'   => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'   => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'     => $prefix . 'avatar',
                'type'   => 'hidden',
                'single' => 1,
                'limit'  => 1,
            ],
            [
                'name'   => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'   => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'     => $prefix . 'avatar_id',
                'type'   => 'uploader',
                'single' => 1,
                'limit'  => 1,
            ],
            [
                'id'         => 'first_name',
                'name'       => esc_html__('First Name', 'opalestate-pro'),
                'type'       => 'text',
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'         => 'last_name',
                'name'       => esc_html__('Last Name', 'opalestate-pro'),
                'type'       => 'text',
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'          => 'description',
                'name'        => esc_html__('Biographical Info', 'opalestate-pro'),
                'type'        => 'textarea',
                'description' => esc_html__('Share a little biographical information to fill out your profile. This may be shown publicly.', 'opalestate-pro'),
                'after_row'   => '<hr>',
            ],
        ];

        return apply_filters('opalestate_get_user_meta_front_base_field', $fields, $prefix);
    }

    public function get_avatar_fields($prefix) {
        return apply_filters('opalestate_get_user_matabox_avatar_fields', [
            [
                'name'   => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'   => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'     => $prefix . 'avatar',
                'type'   => is_admin() ? 'file' : 'opal_upload',
                'avatar' => true,

            ],
        ]);
    }

    public function get_address_fields($prefix) {
        return apply_filters('opalestate_get_user_matabox_address_fields', [
            [
                'name'       => esc_html__('Location', 'opalestate-pro'),
                'desc'       => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'         => $prefix . "location",
                'taxonomy'   => 'opalestate_location', //Enter Taxonomy Slug
                'type'       => 'taxonomy_select',
                'before_row' => '<div class="field-row-3">',

            ],
            [
                'name'     => esc_html__('State / Province', 'opalestate-pro'),
                'desc'     => esc_html__('Select one, to add new you create in state of estate panel', 'opalestate-pro'),
                'id'       => $prefix . "state",
                'taxonomy' => 'opalestate_state', //Enter Taxonomy Slug
                'type'     => 'taxonomy_select',
            ],
            [
                'name'      => esc_html__('City / Town', 'opalestate-pro'),
                'desc'      => esc_html__('Select one, to add new you create in city of estate panel', 'opalestate-pro'),
                'id'        => $prefix . "city",
                'taxonomy'  => 'opalestate_city', //Enter Taxonomy Slug
                'type'      => 'taxonomy_select',
                'after_row' => '</div>',
            ],
            [
                'name' => esc_html__('Address', 'opalestate-pro'),
                'id'   => "{$prefix}address",
                'type' => 'text',
            ],
            [
                'id'              => "{$prefix}map",
                'name'            => esc_html__('Map Location', 'opalestate-pro'),
                'type'            => 'opal_map',
                'sanitization_cb' => 'opal_map_sanitise',
                'split_values'    => true,
            ],
        ], $prefix);
    }

    public function get_job_fields($prefix) {
        return [
            [
                'name'       => esc_html__('Job', 'opalestate-pro'),
                'id'         => "{$prefix}job",
                'type'       => 'text',
                'before_row' => '<div class="field-row-2">',
            ],
            [
                'name'      => esc_html__('Company', 'opalestate-pro'),
                'id'        => "{$prefix}company",
                'type'      => 'text',
                'after_row' => '</div>',
            ],
        ];
    }

    public function get_office_fields($prefix) {
        return $this->get_base_fields($prefix);
    }

    public function get_base_front_fields($prefix) {
        return [
            [
                'name'       => esc_html__('Email', 'opalestate-pro'),
                'id'         => "{$prefix}email",
                'type'       => 'text',
                'before_row' => '<div class="field-row-2">',
            ],
            [
                'name' => esc_html__('Website', 'opalestate-pro'),
                'id'   => "{$prefix}web",
                'type' => 'text_url',
            ],
            [
                'name' => esc_html__('Phone', 'opalestate-pro'),
                'id'   => "{$prefix}phone",
                'type' => 'text',
            ],
            [
                'name' => esc_html__('Mobile', 'opalestate-pro'),
                'id'   => "{$prefix}mobile",
                'type' => 'text',
            ],
            [
                'name'      => esc_html__('Fax', 'opalestate-pro'),
                'id'        => "{$prefix}fax",
                'type'      => 'text',
                'after_row' => '</div>',
            ],
        ];
    }

    public function get_base_fields($prefix) {
        return apply_filters('opalestate_get_user_meta_base_fields', [
            [
                'id'          => "{$prefix}featured",
                'name'        => esc_html__('Is Featured', 'opalestate-pro'),
                'type'        => 'switch',
                'description' => esc_html__('Set member as featured', 'opalestate-pro'),
                'options'     => [
                    0 => esc_html__('No', 'opalestate-pro'),
                    1 => esc_html__('Yes', 'opalestate-pro'),
                ],
            ],
            [
                'id'          => "{$prefix}trusted",
                'name'        => esc_html__('Trusted', 'opalestate-pro'),
                'type'        => 'switch',
                'description' => esc_html__('Set this member as Trusted Member', 'opalestate-pro'),
                'options'     => [
                    0 => esc_html__('No', 'opalestate-pro'),
                    1 => esc_html__('Yes', 'opalestate-pro'),
                ],
            ],
            [
                'name'   => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'   => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'     => $prefix . 'avatar',
                'type'   => is_admin() ? 'file' : 'uploader',
                'single' => true,
                'avatar' => true,
            ],
            [
                'name'       => esc_html__('Email', 'opalestate-pro'),
                'id'         => "{$prefix}email",
                'type'       => 'text',
                'before_row' => '<div class="field-row-2">',
            ],
            [
                'name' => esc_html__('Website', 'opalestate-pro'),
                'id'   => "{$prefix}web",
                'type' => 'text_url',
            ],
            [
                'name' => esc_html__('Phone', 'opalestate-pro'),
                'id'   => "{$prefix}phone",
                'type' => 'text',
            ],

            [
                'name' => esc_html__('Mobile', 'opalestate-pro'),
                'id'   => "{$prefix}mobile",
                'type' => 'text',
            ],
            [
                'name'      => esc_html__('Fax', 'opalestate-pro'),
                'id'        => "{$prefix}fax",
                'type'      => 'text',
                'after_row' => '</div>',
            ],
        ], $prefix);
    }

    public function get_social_fields($prefix) {
        return apply_filters('opalestate_get_user_meta_social_fields', [
            [
                'name'       => esc_html__('Twitter', 'opalestate-pro'),
                'id'         => "{$prefix}twitter",
                'type'       => 'text_url',
                'before_row' => '<div class="field-row-2">',
            ],

            [
                'name' => esc_html__('Facebook', 'opalestate-pro'),
                'id'   => "{$prefix}facebook",
                'type' => 'text_url',
            ],

            [
                'name' => esc_html__('Google', 'opalestate-pro'),
                'id'   => "{$prefix}google",
                'type' => 'text_url',
            ],

            [
                'name' => esc_html__('LinkedIn', 'opalestate-pro'),
                'id'   => "{$prefix}linkedin",
                'type' => 'text_url',
            ],

            [
                'name' => esc_html__('Pinterest', 'opalestate-pro'),
                'id'   => "{$prefix}pinterest",
                'type' => 'text_url',
            ],
            [
                'name'      => esc_html__('Instagram', 'opalestate-pro'),
                'id'        => "{$prefix}instagram",
                'type'      => 'text_url',
                'after_row' => '</div>',
            ],
        ], $prefix);
    }
}
