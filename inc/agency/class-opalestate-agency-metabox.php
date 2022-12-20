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

class Opalestate_Agency_MetaBox extends Opalestate_User_MetaBox {

    /**
     *
     */
    public function metaboxes_target() {

        $prefix = OPALESTATE_AGENCY_PREFIX;
        $fields = [
            [
                'id'          => "{$prefix}user_id",
                'name'        => esc_html__('Link To User ID', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Set relationship to existed user, allow user can edit Agency profile in front-end and show account info in each property.', 'opalestate-pro'),

            ],
            [
                'name' => esc_html__('Agent Team', 'opalestate-pro'),
                'desc' => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'   => $prefix . "team",
                'type' => 'adduser',
            ],
        ];

        return $fields;
    }

    /**
     *
     */
    public function metaboxes_admin_fields($prefix = '') {

        if (!$prefix) {
            $prefix = OPALESTATE_AGENCY_PREFIX;
        }

        $fields = [
            [
                'id'          => "{$prefix}featured",
                'name'        => esc_html__('Is Featured', 'opalestate-pro'),
                'type'        => 'switch',
                'description' => esc_html__('Set this agent as featured', 'opalestate-pro'),
                'options'     => [
                    0 => esc_html__('No', 'opalestate-pro'),
                    1 => esc_html__('Yes', 'opalestate-pro'),
                ],

            ],
        ];

        $fields = array_merge_recursive($fields,
            $this->get_base_fields($prefix),
            $this->get_address_fields($prefix)
        );

        return apply_filters('opalestate_postype_agency_metaboxes_fields', $fields);
    }

    /**
     *
     */
    public function get_front_fields($prefix) {
        return [
            'id'           => $prefix . 'front',
            'title'        => esc_html__('Name and Description', 'opalestate-pro'),
            'object_types' => ['opalestate_property'],
            'context'      => 'normal',
            'object_types' => ['user'],
            'priority'     => 'high',
            'show_names'   => true,
            'fields'       => $this->get_fields($prefix),
        ];
    }

    /**
     *
     */
    public function get_fields($prefix) {

        $management = [
            [
                'name'       => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'       => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'         => $prefix . 'avatar',
                'type'       => is_admin() ? 'file' : 'opal_upload',
                'avatar'     => true,
                'before_row' => '<div class="' . apply_filters('opalestate_row_container_class', 'row opal-row') . '"> <div class="col-lg-4">',
                'after_row'  => '</div>',
            ],

            [
                'id'         => 'first_name',
                'name'       => esc_html__('First Name', 'opalestate-pro'),
                'type'       => 'text',
                'attributes' => [
                    'required' => 'required',
                ],
                'before_row' => '<div class="col-lg-8">',

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

                'after_row' => '</div></div>',
            ],
            [
                'id'          => "{$prefix}job",
                'name'        => esc_html__('Title/Job', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Please enter position or job in your company.', 'opalestate-pro'),
                'before_row'  => '<div class="clearfix clear"></div><hr><div class="row-group-features group-has-two clearfix"><h3>' . __('Information', 'opalestate-pro') . '</h3>', // callback
            ],

            [
                'id'          => "{$prefix}company",
                'name'        => esc_html__('company', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Please enter company name.', 'opalestate-pro'),
            ],
            [
                'id'          => "{$prefix}email",
                'name'        => esc_html__('Contact email', 'opalestate-pro'),
                'type'        => 'text_email',
                'description' => esc_html__('Enter contact name that allow user contact you via the contact form of website.', 'opalestate-pro'),
            ],
            [
                'id'          => "{$prefix}phone",
                'name'        => esc_html__('Phone', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Enter your home phone.', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}skype",
                'name'        => esc_html__('Skype', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Input for skype account.', 'opalestate-pro'),
            ],

            [
                'id'          => "url",
                'name'        => esc_html__('Website URL', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Link to your website', 'opalestate-pro'),
                'after_row'   => '</div>',
            ],

            [
                'id'          => "{$prefix}facebook",
                'name'        => esc_html__('Facebook', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Enter your facebook profile or facebook newfeed', 'opalestate-pro'),
                'before_row'  => '<div class="row-group-features group-has-two group-price clearfix"><h3>' . __('Social', 'opalestate-pro') . '</h3>', // callback
            ],

            [
                'id'          => "{$prefix}linkedin",
                'name'        => esc_html__('Linkedin URL', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Input for linked in profile.', 'opalestate-pro'),
            ],
            [
                'id'          => "{$prefix}instagram",
                'name'        => esc_html__('Instagram URL', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Input for instagram profile.', 'opalestate-pro'),
            ],
            [
                'id'          => "{$prefix}pinterest",
                'name'        => esc_html__('Pinterest Url', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Input for pinterest feed', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}googleplus",
                'name'        => esc_html__('Google Plus Url', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Input for goolge plus profile or your newfeed.', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}youtube",
                'name'        => esc_html__('Youtube Url', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Input for your channel youtube.', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}vimeo",
                'name'        => esc_html__('Vimeo Url', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Input for your channel Vimeo', 'opalestate-pro'),
                'after_row'   => '</div>',
            ],
        ];

        return $management;
    }

    /**
     *
     */
    public function metaboxes_front_fields($prefix = '', $post_id = 0) {
        if (!$prefix) {
            $prefix = OPALESTATE_AGENCY_PREFIX;
        }
        $post = get_post($post_id);

        $fields = [

            [
                'id'      => $prefix . 'post_type',
                'type'    => 'hidden',
                'default' => 'opalestate_agency',
            ],
            [
                'id'      => 'post_id',
                'type'    => 'hidden',
                'default' => $post_id,
            ],

            [
                'name'       => esc_html__('Title / Name', 'opalestate-pro'),
                'id'         => $prefix . 'title',
                'type'       => 'text',
                'default'    => !empty($post) ? $post->post_title : '',
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'name' => esc_html__('Slogan', 'opalestate-pro'),
                'id'   => "{$prefix}slogan",
                'type' => 'text',
            ],
            [
                'name'       => esc_html__('Information', 'opalestate-pro'),
                'id'         => $prefix . 'text',
                'type'       => 'wysiwyg',
                'default'    => !empty($post) ? $post->post_content : '',
                'attributes' => [
                    'required' => 'required',
                ],
            ],

            [
                'name'     => esc_html__('Types', 'opalestate-pro'),
                'desc'     => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'       => $prefix . "type",
                'taxonomy' => 'opalestate_types',
                'type'     => 'taxonomy_select',

            ]
        ];


        $fields = array_merge_recursive($fields,
            $this->get_base_front_fields($prefix),
            $this->get_address_fields($prefix),
            $this->get_social_fields($prefix)
        );

        return apply_filters('opalestate_postype_office_metaboxes_fields', $fields);
    }

    public function get_base_front_fields($prefix) {
        return [
            [
                'id'          => "{$prefix}featured_image",
                'name'        => esc_html__('Banner', 'opalestate-pro'),
                'type'        => 'uploader',
                'is_featured' => true,
                'limit'       => 1,
                'single'      => 1,
                'description' => esc_html__('Select one or more images to show as gallery', 'opalestate-pro'),
                'before_row'  => '<hr>',
            ],
            [
                'name'   => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'   => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'     => $prefix . 'avatar',
                'type'   => 'hidden',
                'single' => 1,
                'limit'  => 1,
                'avatar' => true,

            ],
            [
                'name'   => esc_html__('Avatar Picture', 'opalestate-pro'),
                'desc'   => esc_html__('This image will display in user detail and profile box information', 'opalestate-pro'),
                'id'     => $prefix . 'avatar_id',
                'type'   => 'uploader',
                'single' => 1,
                'limit'  => 1,
                'avatar' => true,

            ],
            [
                'name'      => esc_html__('Gallery', 'opalestate-pro'),
                'desc'      => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'        => $prefix . "gallery",
                'type'      => 'uploader',
                'after_row' => '<hr>',
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
        ];
    }

    /**
     *
     */
    public function render_front_form($metaboxes, $post_id = 0) {

        $prefix = OPALESTATE_AGENCY_PREFIX;

        $metaboxes[$prefix . 'front'] = [
            'id'           => $prefix . 'front',
            'title'        => esc_html__('Agency Information', 'opalestate-pro'),
            'object_types' => ['opalestate_agency'],
            'context'      => 'normal',
            'priority'     => 'high',
            'show_names'   => true,
            'cmb_styles'   => false,
            'fields'       => $this->metaboxes_front_fields($prefix, $post_id),
        ];

        return $metaboxes;
    }
}
