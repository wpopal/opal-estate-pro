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

class Opalestate_Property_MetaBox {

    /**
     * Register admin fields.
     */
    public function register_admin_fields() {
        $prefix      = OPALESTATE_PROPERTY_PREFIX;
        $box_options = [
            'id'           => 'property_metabox',
            'title'        => esc_html__('Property Metabox', 'opalestate-pro'),
            'object_types' => ['opalestate_property'],
            'show_names'   => true,
        ];

        // Setup meta box
        $cmb = new_cmb2_box($box_options);

        // Setting tabs
        $tabs_setting = [
            'config' => $box_options,
            'layout' => 'vertical', // Default : horizontal
            'tabs'   => [],
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-general',
            'icon'   => 'dashicons-admin-home',
            'title'  => esc_html__('General', 'opalestate-pro'),
            'fields' => $this->metaboxes_management_fields(),
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-prices',
            'icon'   => 'dashicons-admin-tools',
            'title'  => esc_html__('Prices', 'opalestate-pro'),
            'fields' => $this->metaboxes_price_fields(),
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-information',
            'icon'   => 'dashicons-admin-post',
            'title'  => esc_html__('Information', 'opalestate-pro'),
            'fields' => $this->metaboxes_info_fields(),
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-facilities',
            'icon'   => 'dashicons-grid-view',
            'title'  => esc_html__('Facility', 'opalestate-pro'),
            'fields' => $this->metaboxes_public_facilities_fields(),
        ];


        $tabs_setting['tabs'][] = [
            'id'     => 'p-floor-plans',
            'icon'   => 'dashicons-grid-view',
            'title'  => esc_html__('Floor Plan', 'opalestate-pro'),
            'fields' => $this->metaboxes_floor_plans(),
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-apartments',
            'icon'   => 'dashicons-admin-multisite',
            'title'  => esc_html__('Apartments', 'opalestate-pro'),
            'fields' => $this->metaboxes_apartments(),
        ];

        ////
        $tabs_setting['tabs'][] = [
            'id'     => 'p-gallery',
            'icon'   => 'dashicons-format-gallery',
            'title'  => esc_html__('Gallery', 'opalestate-pro'),
            'fields' => [
                [
                    'id'          => "{$prefix}gallery",
                    'name'        => esc_html__('Images Gallery', 'opalestate-pro'),
                    'type'        => 'file_list',
                    'description' => esc_html__('Select one or more images to show as gallery', 'opalestate-pro'),
                ],
            ],
        ];
        ///
        $tabs_setting['tabs'][] = [
            'id'     => 'p-vt_gallery',
            'title'  => esc_html__('Virtual Tour 360', 'opalestate-pro'),
            'icon'   => 'dashicons-format-image',
            'fields' => [
                [
                    'id'          => "{$prefix}vt_gallery",
                    'name'        => esc_html__('Manual Images 360 ', 'opalestate-pro'),
                    'type'        => 'opal_upload',
                    'description' => esc_html__('Select one or more images to show as gallery', 'opalestate-pro'),
                ],
                [
                    'id'          => "{$prefix}virtual",
                    'name'        => esc_html__('Or 360° Virtual Tour', 'opalestate-pro'),
                    'type'        => 'textarea_code',
                    'description' => esc_html__('Input iframe to show 360° Virtual Tour.', 'opalestate-pro'),
                ],
            ],
        ];

        ///
        $tabs_setting['tabs'][] = [
            'id'     => 'p-attachments',
            'icon'   => 'dashicons-media-default',
            'title'  => esc_html__('Attachments', 'opalestate-pro'),
            'fields' => [
                [
                    'id'          => "{$prefix}attachments",
                    'name'        => esc_html__('Attachments', 'opalestate-pro'),
                    'type'        => 'file_list',
                    'options'     => [
                        'url' => false, // Hide the text input for the url
                    ],
                    'description' => esc_html__('Select one or more files to allow download', 'opalestate-pro'),
                ],
            ],
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-agents',
            'icon'   => 'dashicons-admin-users',
            'title'  => esc_html__('Contact Member', 'opalestate-pro'),
            'fields' => $this->metaboxes_members_fields(),
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-assignment',
            'icon'   => 'dashicons-admin-users',
            'title'  => esc_html__('User Assignment', 'opalestate-pro'),
            'fields' => $this->metaboxes_assignment_fields(),
        ];

        $tabs_setting['tabs'][] = [
            'id'     => 'p-layout',
            'title'  => esc_html__('Layout', 'opalestate-pro'),
            'fields' => $this->metaboxes_layout_fields(),
        ];

        // Set tabs
        $cmb->add_field([
            'id'   => '__tabs',
            'type' => 'tabs',
            'tabs' => $tabs_setting,
        ]);

        return true;
    }

    /**
     * Management fields.
     */
    public function metaboxes_management_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'name'    => esc_html__('Featured', 'opalestate-pro'),
                'id'      => $prefix . 'featured',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Yes', 'opalestate-pro'),
                    'off' => esc_html__('No', 'opalestate-pro'),
                ],
                'default' => 'off',
            ],
            [
                'name'        => esc_html__('Property SKU', 'opalestate-pro'),
                'id'          => $prefix . 'sku',
                'type'        => 'text',
                'description' => esc_html__('Please Enter Your Property SKU', 'opalestate-pro'),
            ],
            [
                'id'              => $prefix . 'map',
                'name'            => esc_html__('Location', 'opalestate-pro'),
                'type'            => 'opal_map',
                'sanitization_cb' => 'opal_map_sanitise',
                'split_values'    => true,
            ],
            [
                'name' => esc_html__('Postal Code / Zip', 'opalestate-pro'),
                'id'   => $prefix . 'zipcode',
                'type' => 'text',

            ],
            [
                'name'    => esc_html__('Google Map View', 'opalestate-pro'),
                'id'      => $prefix . 'enablemapview',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Yes', 'opalestate-pro'),
                    'off' => esc_html__('No', 'opalestate-pro'),
                ],
            ],
            [
                'name' => esc_html__('Address', 'opalestate-pro'),
                'id'   => $prefix . 'address',
                'type' => 'textarea_small',
            ],
            [
                'id'          => "{$prefix}video",
                'name'        => esc_html__('Video', 'opalestate-pro'),
                'type'        => 'text_url',
                'description' => esc_html__('Input for videos, audios from Youtube, Vimeo and all supported sites by WordPress. It has preview feature.', 'opalestate-pro'),
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_management', $fields);
    }

    /**
     * Price fields.
     */
    public function metaboxes_price_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;

        $currency = opalestate_currency_symbol() ? ' (' . opalestate_currency_symbol() . ')' : ' ($)';

        $fields = [
            [
                'id'          => $prefix . 'price',
                'name'        => esc_html__('Regular Price', 'opalestate-pro') . $currency,
                'type'        => 'text',
                'description' => esc_html__('Enter amount without currency', 'opalestate-pro'),
                'before_row'  => '<div class="row-group-features group-has-three group-price   clearfix"><h3>' . (is_admin() ? "" : esc_html__('Price', 'opalestate-pro')) . '</h3>', // callback
            ],
            [
                'id'          => $prefix . 'saleprice',
                'name'        => esc_html__('Sale Price', 'opalestate-pro') . $currency,
                'type'        => 'text',
                'description' => esc_html__('Enter amount without currency', 'opalestate-pro'),
            ],
            [
                'id'          => $prefix . 'before_pricelabel',
                'name'        => esc_html__('Before Price Label (optional)', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('Before Price Label (e.g. "from")', 'opalestate-pro'),
            ],
            [
                'id'          => $prefix . 'pricelabel',
                'name'        => esc_html__('After Price Label (optional)', 'opalestate-pro'),
                'type'        => 'text',
                'description' => esc_html__('After Price Label (e.g. "per month")', 'opalestate-pro'),
                'after_row'   => '</div>', // callback
            ],
            [
                'name'    => esc_html__('Is Price On Call', 'opalestate-pro'),
                'id'      => $prefix . 'price_oncall',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Yes', 'opalestate-pro'),
                    'off' => esc_html__('No', 'opalestate-pro'),
                ],
                'default' => 'off',
            ],

        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_price', $fields);
    }

    /**
     * Information fields.
     */
    public static function metaboxes_info_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;

        $fields = [
            [
                'name'        => esc_html__('Built year', 'opalestate-pro'),
                'id'          => $prefix . 'builtyear',
                'type'        => 'text',
                'description' => esc_html__('Enter built year', 'opalestate-pro'),
                'before_row'  => '<div class="row-group-features group-has-three group-property-info clearfix"><h3>' . (is_admin() ? "" : esc_html__('Property Information',
                        'opalestate-pro')) . '</h3>',
            ],
            [
                'name'        => esc_html__('Parking', 'opalestate-pro'),
                'id'          => $prefix . 'parking',
                'type'        => 'text',
                'description' => esc_html__('Enter number of Parking', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Bedrooms', 'opalestate-pro'),
                'id'          => $prefix . 'bedrooms',
                'type'        => 'text',
                'description' => esc_html__('Enter number of bedrooms', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Bathrooms', 'opalestate-pro'),
                'id'          => $prefix . 'bathrooms',
                'type'        => 'text',
                'description' => esc_html__('Enter number of bathrooms', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Plot Size', 'opalestate-pro'),
                'id'          => $prefix . 'plotsize',
                'type'        => 'text',
                'description' => esc_html__('Enter size of Plot as 20x30, 20x30x40, 20x30x40x50', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Area Size', 'opalestate-pro'),
                'id'          => $prefix . 'areasize',
                'type'        => 'text',
                'description' => esc_html__('Enter size of area in sqft', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Orientation', 'opalestate-pro'),
                'id'          => "{$prefix}orientation",
                'type'        => 'text',
                'description' => esc_html__('Enter Orientation of property', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Living Rooms', 'opalestate-pro'),
                'id'          => "{$prefix}livingrooms",
                'type'        => 'text',
                'description' => esc_html__('Enter Number of Living Rooms', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Kitchens', 'opalestate-pro'),
                'id'          => "{$prefix}kitchens",
                'type'        => 'text',
                'description' => esc_html__('Enter Number of Kitchens', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Rooms', 'opalestate-pro'),
                'id'          => "{$prefix}amountrooms",
                'type'        => 'text',
                'description' => esc_html__('Enter Number of Amount Rooms', 'opalestate-pro'),
                'after_row'   => '</div>',
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_info', $fields);
    }

    /**
     * Facilites fields.
     */
    public function metaboxes_public_facilities_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'id'      => $prefix . 'public_facilities_group',
                'type'    => 'group',
                'fields'  => [
                    [
                        'id'   => $prefix . 'public_facilities_key',
                        'name' => esc_html__('Label', 'opalestate-pro'),
                        'type' => 'text',
                    ],
                    [
                        'id'   => $prefix . 'public_facilities_value',
                        'name' => esc_html__('Content', 'opalestate-pro'),
                        'type' => 'text',
                    ],
                ],
                'options' => [
                    'group_title'   => esc_html__('Facility {#}', 'opalestate-pro'),
                    'add_button'    => esc_html__('Add more', 'opalestate-pro'),
                    'remove_button' => esc_html__('Remove', 'opalestate-pro'),
                    'sortable'      => true,
                    'closed'        => false,
                ],
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_public_facilities', $fields);
    }

    /**
     * Member fields.
     */
    public function metaboxes_members_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        global $post;

        $types = [
            'hide'   => esc_html__('Hide Author Information', 'opalestate-pro'),
            'user'   => esc_html__('User   Author Information', 'opalestate-pro'),
            'agent'  => esc_html__('Agent  Information', 'opalestate-pro'),
            'agency' => esc_html__('Agency Information', 'opalestate-pro'),
        ];

        // agent
        $agents = [
            0 => esc_html__('No', 'opalestate-pro'),
        ];
        if (isset($_GET['post']) && $_GET['post']) {
            $id = get_post_meta((int)$_GET['post'], OPALESTATE_PROPERTY_PREFIX . 'agent', true);
            if ($id) {
                $agents[$id] = get_the_title($id);
            }
        }
        // agency
        $agency = [
            0 => esc_html__('No', 'opalestate-pro'),
        ];
        if (isset($_GET['post']) && $_GET['post']) {
            $id = get_post_meta((int)$_GET['post'], OPALESTATE_PROPERTY_PREFIX . 'agency', true);
            if ($id) {
                $agency[$id] = get_the_title($id);
            }
        }

        $fields = [
            [
                'name'    => esc_html__('Author Information', 'opalestate-pro'),
                'id'      => "{$prefix}author_type",
                'type'    => 'select',
                'options' => $types,
                'default' => 'user',
            ],
            [
                'name'    => esc_html__('Agent', 'opalestate-pro'),
                'id'      => "{$prefix}agent",
                'type'    => 'select',
                'options' => $agents,
            ],
            [
                'name'    => esc_html__('Agency', 'opalestate-pro'),
                'id'      => "{$prefix}agency",
                'type'    => 'select',
                'options' => $agency,
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_agent', $fields);
    }

    /**
     * Assigment fields.
     */
    public function metaboxes_assignment_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        global $post;

        // users
        $users = [
            0 => esc_html__('Default User', 'opalestate-pro'),
        ];

        $all_users = get_users();

        foreach ($all_users as $user) {
            $users[$user->ID] = $user->display_name;
        }

        $fields = [
            [
                'name'        => esc_html__('User', 'opalestate-pro'),
                'id'          => "post_author_override",
                'type'        => 'select',
                "description" => esc_html__('Change to new owner of this property, which be listed in That user dashboard', 'opalestate-pro'),
                'options'     => $users,
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_assignment', $fields);
    }

    /**
     * Layout fields.
     */
    public function metaboxes_layout_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;

        $templates = opalestate_single_layout_preview();

        $fields = [
            [
                'name'        => esc_html__('Layout Display', 'opalestate-pro'),
                'id'          => "{$prefix}layout",
                'type'        => 'select',
                'options'     => apply_filters('opalestate_single_layout_templates', ['' => esc_html__('Inherit', 'opalestate-pro')]),
                'description' => esc_html__('Select a layout to display full information of this property', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Preview Display', 'opalestate-pro'),
                'id'          => "{$prefix}preview",
                'type'        => 'select',
                'options'     => $templates,
                'description' => esc_html__('Select a layout to display full information of this property', 'opalestate-pro'),
            ],
            [
                'name'        => esc_html__('Show Mortgage Calculator', 'opalestate-pro'),
                'id'          => "{$prefix}enable_single_mortgage",
                'type'        => 'select',
                'options'     => [
                    ''    => esc_html__('Inherit', 'opalestate-pro'),
                    'on'  => esc_html__('Enable', 'opalestate-pro'),
                    'off' => esc_html__('Disable', 'opalestate-pro'),
                ],
                'description' => esc_html__('Show Mortgage Calculator', 'opalestate-pro'),
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_layout', $fields);
    }

    /**
     * Floor plans fields.
     */
    public function metaboxes_floor_plans() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'id'      => $prefix . 'public_floor_group',
                'type'    => 'group',
                'fields'  => [
                    [
                        'id'   => $prefix . 'floor_name',
                        'name' => esc_html__('Name', 'opalestate-pro'),
                        'type' => 'text',

                    ],
                    [
                        'id'         => $prefix . 'floor_price',
                        'name'       => esc_html__('Price', 'opalestate-pro'),
                        'before_row' => '<div class="field-row-2">',
                        'type'       => 'text',
                    ],
                    [
                        'id'   => $prefix . 'floor_size',
                        'name' => esc_html__('Size', 'opalestate-pro'),
                        'type' => 'text',
                    ],
                    [
                        'id'   => $prefix . 'floor_room',
                        'name' => esc_html__('Rooms', 'opalestate-pro'),
                        'type' => 'text',
                    ],
                    [
                        'id'        => $prefix . 'floor_bath',
                        'name'      => esc_html__('Baths', 'opalestate-pro'),
                        'type'      => 'text',
                        'after_row' => '</div>',
                    ],
                    [
                        'id'   => $prefix . 'floor_content',
                        'name' => esc_html__('Content', 'opalestate-pro'),
                        'type' => 'textarea_small',
                    ],
                    [
                        'id'          => "{$prefix}floor_image",
                        'name'        => esc_html__('Image Preview', 'opalestate-pro'),
                        'type'        => 'file',
                        'query_args'  => [
                            'type' => [
                                'image/gif',
                                'image/jpeg',
                                'image/png',
                            ],
                        ],
                        'description' => esc_html__('Input iframe to show 360° Virtual Tour.', 'opalestate-pro'),
                    ],
                ],
                'options' => [
                    'group_title'   => esc_html__('Floor {#}', 'opalestate-pro'),
                    'add_button'    => esc_html__('Add more', 'opalestate-pro'),
                    'remove_button' => esc_html__('Remove', 'opalestate-pro'),
                    'sortable'      => true,
                    'closed'        => false,
                ],
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_floor_plans', $fields);
    }

    /**
     * Apartment fields.
     */
    public function metaboxes_apartments() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'id'      => $prefix . 'apartments',
                'type'    => 'group',
                'fields'  => [
                    [
                        'id'         => $prefix . 'apartment_plot',
                        'name'       => esc_html__('Plot', 'opalestate-pro'),
                        'before_row' => '<div class="field-row-2">',
                        'type'       => 'text',

                    ],
                    [
                        'id'   => $prefix . 'apartment_beds',
                        'name' => esc_html__('Beds', 'opalestate-pro'),
                        'type' => 'text',
                    ],
                    [
                        'id'   => $prefix . 'apartment_price_from',
                        'name' => esc_html__('Price from', 'opalestate-pro'),
                        'type' => 'text',
                    ],
                    [
                        'id'        => $prefix . 'apartment_floor',
                        'name'      => esc_html__('Floor', 'opalestate-pro'),
                        'type'      => 'text',
                        'after_row' => '</div>',
                    ],
                    [
                        'id'   => $prefix . 'apartment_building_address',
                        'name' => esc_html__('Building / Address', 'opalestate-pro'),
                        'type' => 'textarea_small',
                    ],
                    [
                        'id'         => $prefix . 'apartment_status',
                        'name'       => esc_html__('Status', 'opalestate-pro'),
                        'type'       => 'select',
                        'options'    => apply_filters('opalestate_property_apartment_statuses', [
                            ''            => esc_html__('None', 'opalestate-pro'),
                            'available'   => esc_html__('Available', 'opalestate-pro'),
                            'unavailable' => esc_html__('Unavailable', 'opalestate-pro'),
                        ]),
                        'before_row' => '<div class="field-row-2">',
                    ],
                    [
                        'id'        => $prefix . 'apartment_link',
                        'name'      => esc_html__('Link', 'opalestate-pro'),
                        'type'      => 'text',
                        'after_row' => '</div>',
                    ],
                ],
                'options' => [
                    'group_title'   => esc_html__('Apartment {#}', 'opalestate-pro'),
                    'add_button'    => esc_html__('Add more', 'opalestate-pro'),
                    'remove_button' => esc_html__('Remove', 'opalestate-pro'),
                    'sortable'      => true,
                    'closed'        => false,
                ],
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_apartments', $fields);
    }
}
