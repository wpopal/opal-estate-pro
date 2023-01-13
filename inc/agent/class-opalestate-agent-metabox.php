<?php
/**
 * Opalestate_Agent_MetaBox.
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

class Opalestate_Agent_MetaBox extends Opalestate_User_MetaBox {

    /**
     *
     */
    public function metaboxes_admin_fields($prefix = '') {
        if (!$prefix) {
            $prefix = OPALESTATE_AGENT_PREFIX;
        }

        $fields = [];

        $fields = array_merge_recursive($fields,
            $this->get_base_fields($prefix),
            $this->get_job_fields($prefix),
            $this->get_address_fields($prefix)
        );

        return apply_filters('opalestate_postype_agent_metaboxes_fields', $fields);
    }

    public function metaboxes_target() {

        $prefix = OPALESTATE_AGENT_PREFIX;
        $fields = [
            [
                'id'          => "{$prefix}user_id",
                'name'        => esc_html__('Link to User', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Enter User ID to show information without using user info', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}target_min_price",
                'name'        => esc_html__('Target Min Price', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Enter min price of property which is for sale/rent...', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}target_max_price",
                'name'        => esc_html__('Target Max Price', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Enter max price of property which is for sale/rent...', 'opalestate-pro'),
            ],
            [
                'name'     => esc_html__('Types', 'opalestate-pro'),
                'desc'     => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'       => $prefix . "type",
                'taxonomy' => 'opalestate_types', //Enter Taxonomy Slug
                'type'     => 'taxonomy_select',
            ],
            [
                'name'     => esc_html__('Category', 'opalestate-pro'),
                'desc'     => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'       => $prefix . "category",
                'taxonomy' => 'property_category', //Enter Taxonomy Slug
                'type'     => 'taxonomy_select',
            ],
        ];

        return apply_filters('opalestate_postype_agent_metaboxes_target', $fields);
    }

    /**
     *
     */
    public function metaboxes_front_fields($prefix = '', $post_id = 0) {
        if (!$prefix) {
            $prefix = OPALESTATE_AGENT_PREFIX;
        }
        $post = get_post($post_id);

        $fields = [
            [
                'id'      => $prefix . 'post_type',
                'type'    => 'hidden',
                'default' => 'opalestate_agent',
            ],
            [
                'name'       => esc_html__('Title/Name', 'opalestate-pro'),
                'id'         => $prefix . 'title',
                'type'       => 'text',
                'default'    => !empty($post) ? $post->post_title : '',
                'attributes' => [
                    'required' => 'required',
                ],
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
                'taxonomy' => 'opalestate_types', //Enter Taxonomy Slug
                'type'     => 'taxonomy_select',

            ],
            [
                'id'          => "{$prefix}target_min_price",
                'name'        => esc_html__('Target Min Price', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Enter min price of property which is for sale/rent...', 'opalestate-pro'),
            ],

            [
                'id'          => "{$prefix}target_max_price",
                'name'        => esc_html__('Target Max Price', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Enter max price of property which is for sale/rent...', 'opalestate-pro'),
                'after_row'   => '</div>',
            ],
        ];


        $fields = array_merge_recursive($fields,
            $this->get_base_front_fields($prefix),
            $this->get_address_fields($prefix),
            $this->get_social_fields($prefix)
        );

        return apply_filters('opalestate_postype_agent_metaboxes_fields', $fields);
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
        $prefix                       = OPALESTATE_AGENT_PREFIX;
        $metaboxes[$prefix . 'front'] = [
            'id'           => $prefix . 'front',
            'title'        => esc_html__('Agent Information', 'opalestate-pro'),
            'object_types' => ['opalestate_agent'],
            'context'      => 'normal',
            'priority'     => 'high',
            'show_names'   => true,
            'fields'       => $this->metaboxes_front_fields($prefix, $post_id),
        ];

        return $metaboxes;
    }
}
