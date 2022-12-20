<?php
/**
 * Submission form.
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

class Opalestate_Property_MetaBox_Submission {

    /**
     * Defines custom front end fields
     *
     * @access public
     * @param array $metaboxes
     * @return array
     */
    public function register_form(array $metaboxes) {

        $prefix = OPALESTATE_PROPERTY_PREFIX;

        $fields = array_merge(
            $this->metaboxes_general_fields_front(),

            $this->is_enabled_tab('media') ? $this->metaboxes_media_fields() : [],
            $this->is_enabled_tab('location') ? $this->metaboxes_location_fields() : [],
            $this->is_enabled_tab('information') ? $this->metaboxes_info_fields() : [],
            $this->is_enabled_tab('amenities') ? $this->metaboxes_amenities_fields() : [],
            $this->is_enabled_tab('facilities') ? $this->metaboxes_public_facilities_fields() : [],
            $this->is_enabled_tab('apartments') ? $this->metaboxes_public_apartments_fields() : [],
            $this->is_enabled_tab('floor_plans') ? $this->metaboxes_public_floor_plans_fields() : []
        );

        $metaboxes[$prefix . 'front'] = [
            'id'           => $prefix . 'front',
            'title'        => esc_html__('Name and Description', 'opalestate-pro'),
            'object_types' => ['opalestate_property'],
            'context'      => 'normal',
            'priority'     => 'high',
            'save_fields'  => false,
            'show_names'   => true,
            'fields'       => $fields,
            'cmb_styles'   => false,

        ];

        return $metaboxes;
    }

    public function get_fields_groups() {
        return [
            'general'     => ['status' => true, 'title' => esc_html__('General', 'opalestate-pro')],
            'media'       => ['status' => $this->is_enabled_tab('media'), 'title' => esc_html__('Media', 'opalestate-pro')],
            'location'    => ['status' => $this->is_enabled_tab('location'), 'title' => esc_html__('Location', 'opalestate-pro')],
            'information' => ['status' => $this->is_enabled_tab('information'), 'title' => esc_html__('Information', 'opalestate-pro')],
            'amenities'   => ['status' => $this->is_enabled_tab('amenities'), 'title' => esc_html__('Amenities', 'opalestate-pro')],
            'facilities'  => ['status' => $this->is_enabled_tab('facilities'), 'title' => esc_html__('Facilities', 'opalestate-pro')],
            'apartments'  => ['status' => $this->is_enabled_tab('apartments'), 'title' => esc_html__('Apartments', 'opalestate-pro')],
            'floor_plans' => ['status' => $this->is_enabled_tab('floor_plans'), 'title' => esc_html__('Floor plans', 'opalestate-pro')],
        ];
    }

    public function metaboxes_general_fields_front() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;

        $post_id = '';
        if (!empty($_GET['id'])) {
            $post           = get_post(intval($_GET['id']));
            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(absint($_GET['id'])));
            $post_id        = $post->ID;
        }

        $currency = opalestate_currency_symbol() ? ' (' . opalestate_currency_symbol() . ')' : ' ($)';

        $fields = [
            [
                'id'      => 'post_type',
                'type'    => 'hidden',
                'default' => 'opalestate_property',

            ],
            [
                'id'      => 'post_id',
                'type'    => 'hidden',
                'default' => $post_id,

            ],
            [
                'name'       => esc_html__('Title', 'opalestate-pro') . '<span class="required"> *</span>',
                'id'         => $prefix . 'title',
                'type'       => 'text',
                'default'    => !empty($post) ? $post->post_title : '',
                'before_row' => '<div id="opalestate-submission-general" class="opalestate-tab-content">',
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'name'       => esc_html__('Description', 'opalestate-pro'),
                'id'         => $prefix . 'text',
                'type'       => 'wysiwyg',
                'default'    => !empty($post) ? $post->post_content : '',
                'before_row' => '<hr>',
                'options'    => [
                    'media_buttons' => false,
                    'dfw'           => true,
                    'tinymce'       => true,
                    'quicktags'     => true,
                ],
            ],
            [
                'id'          => $prefix . 'price',
                'name'        => esc_html__('Regular Price', 'opalestate-pro') . $currency . '<span class="required"> *</span>',
                'type'        => 'text',
                'description' => esc_html__('Enter amount without currency', 'opalestate-pro'),
                'attributes'  => ['required' => 'required'],
                'before_row'  => '<hr><div class="field-row-2">',
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
            ],
            [
                'name'       => esc_html__('Status', 'opalestate-pro'),
                'id'         => $prefix . 'status',
                'type'       => 'taxonomy_select',
                'taxonomy'   => 'opalestate_status',
                'class'      => 'form-control',
                'before_row' => '</div><hr><div class="field-row-2">', // callback
            ],
            [
                'name'     => esc_html__('Label', 'opalestate-pro'),
                'id'       => $prefix . 'label',
                'type'     => 'taxonomy_select',
                'taxonomy' => 'opalestate_label',
                'class'    => 'form-control',
            ],
            [
                'name'     => esc_html__('Category', 'opalestate-pro'),
                'id'       => $prefix . 'category',
                'type'     => 'taxonomy_select',
                'taxonomy' => 'property_category',
                'class'    => 'form-control',
            ],
            [
                'name'      => esc_html__('Type', 'opalestate-pro'),
                'id'        => $prefix . 'type',
                'type'      => 'taxonomy_select',
                'taxonomy'  => 'opalestate_types',
                'class'     => 'form-control',
                'after_row' => '</div><hr><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>', // callback
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_general', $fields);
    }

    public function metaboxes_media_fields() {
        $id = 0;

        if (isset($_GET['id'])) {
            $post_id = intval($_GET['id']);
            $id      = get_post_thumbnail_id($post_id);
        }

        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [

            [
                'id'          => "{$prefix}featured_image",
                'name'        => esc_html__('Featured Image', 'opalestate-pro'),
                'type'        => 'uploader',
                'single'      => true,
                'value'       => $id,
                'description' => esc_html__('Select one or more images to show as gallery', 'opalestate-pro'),
                'before_row'  => '<div id="opalestate-submission-media" class="opalestate-tab-content">',

            ],

            [
                'id'          => "{$prefix}gallery",
                'name'        => esc_html__('Images Gallery', 'opalestate-pro'),
                'type'        => 'uploader',
                'description' => esc_html__('Select one or more images to show as gallery', 'opalestate-pro'),

            ],

            [
                'id'          => "{$prefix}video",
                'name'        => esc_html__('Video', 'opalestate-pro'),
                'type'        => 'text_url',
                'before_row'  => '<hr>',
                'description' => esc_html__('Input for videos, audios from Youtube, Vimeo and all supported sites by WordPress. It has preview feature.', 'opalestate-pro'),
            ],
            [
                'id'          => "{$prefix}virtual",
                'name'        => esc_html__('360° Virtual Tour', 'opalestate-pro'),
                'type'        => 'textarea_small',
                'description' => esc_html__('Input iframe to show 360° Virtual Tour.', 'opalestate-pro'),
                'before_row'  => '<hr>',
            ],
            [
                'id'          => "{$prefix}attachments",
                'name'        => esc_html__('Attachments', 'opalestate-pro'),
                'type'        => 'uploader',
                'before_row'  => '<hr>',
                'show_icon'   => true,
                'accept'      => 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'options'     => [
                    'url' => true, // Hide the text input for the url
                ],
                'description' => esc_html__('Select one or more files to allow download', 'opalestate-pro'),
                'after_row'   => '<hr><button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step ',
                        'opalestate-pro') . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>',
            ],
        ];

        return apply_filters('opalestate_postype_property_metaboxes_fields_price', $fields);
    }

    public function metaboxes_info_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;

        $fields = [
            [
                'name'        => esc_html__('Built year', 'opalestate-pro'),
                'id'          => $prefix . 'builtyear',
                'type'        => 'text',
                'description' => esc_html__('Enter built year', 'opalestate-pro'),
                'before_row'  => '<div id="opalestate-submission-information" class="opalestate-tab-content"><div class="field-row-2">',
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
                // 'after_row'   => '</div><hr><button type="button" class="submission-back-btn btn btn-primary">' . esc_html__( 'Previous Step',
                // 		'opalestate-pro' ) . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__( 'Next Step', 'opalestate-pro' ) . '</button></div>',

            ],
        ];

        $fields = apply_filters('opalestate_metaboxes_public_info_fields', $fields);

        $keys = array_keys($fields);
        $last = end($keys);

        $fields[$last]['after_row'] = '</div><hr><button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step',
                'opalestate-pro') . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>';

        return $fields;
    }

    public function metaboxes_location_fields() {
        $prefix     = OPALESTATE_PROPERTY_PREFIX;
        $management = [
            [
                'name'       => esc_html__('Country', 'opalestate-pro'),
                'id'         => $prefix . 'location',
                'type'       => 'taxonomy_select',
                'taxonomy'   => 'opalestate_location',
                'before_row' => '<div id="opalestate-submission-location" class="opalestate-tab-content"><div class="field-row-2">',
            ],
            [
                'name'     => esc_html__('States / Province', 'opalestate-pro'),
                'id'       => $prefix . 'state',
                'type'     => 'taxonomy_select',
                'taxonomy' => 'opalestate_state',
            ],
            [
                'name'     => esc_html__('City / Town', 'opalestate-pro'),
                'id'       => $prefix . 'city',
                'type'     => 'taxonomy_select',
                'taxonomy' => 'opalestate_city',
            ],
            [
                'name'      => esc_html__('Postal Code / Zip', 'opalestate-pro'),
                'id'        => $prefix . 'zipcode',
                'type'      => 'text',
                'after_row' => '</div><hr>',

            ],
            [
                'name'       => esc_html__('Address', 'opalestate-pro') . '<span class="required"> *</span>',
                'id'         => $prefix . 'address',
                'type'       => 'text',
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'name'        => esc_html__('Google Map View', 'opalestate-pro'),
                'id'          => $prefix . 'enablemapview',
                'type'        => 'switch',
                'options'     => [
                    0 => esc_html__('No', 'opalestate-pro'),
                    1 => esc_html__('Yes', 'opalestate-pro'),
                ],
                'description' => esc_html__('Enable Google Map', 'opalestate-pro'),
            ],
            [
                'id'              => $prefix . 'map',
                'name'            => esc_html__('Google Map', 'opalestate-pro'),
                'type'            => 'opal_map',
                'sanitization_cb' => 'opal_map_sanitise',
                'split_values'    => true,
                'after_row'       => '<button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step',
                        'opalestate-pro') . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>',
            ],
        ];

        return $management;
    }

    public function metaboxes_amenities_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'name'          => esc_html__('Amenities', 'opalestate-pro'),
                'id'            => $prefix . 'amenity',
                'type'          => 'taxonomy_multicheck',
                'before_row'    => '<div id="opalestate-submission-amenities" class="opalestate-tab-content">',
                'after_row'     => '<button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step',
                        'opalestate-pro') . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>',
                'taxonomy'      => 'opalestate_amenities',
                'render_row_cb' => [$this, 'amenities_html_callback'],
            ],
        ];

        return apply_filters('opalestate_metaboxes_amenities_fields', $fields);
    }

    public function metaboxes_public_facilities_fields() {

        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'id'           => $prefix . 'public_facilities_group',
                'type'         => 'group',
                'before_group' => '<div id="opalestate-submission-facilities" class="opalestate-tab-content">',
                'after_group'  => '<button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step',
                        'opalestate-pro') . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>',
                'fields'       => [
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
                'options'      => [
                    'group_title'   => esc_html__('Facility {#}', 'opalestate-pro'),
                    'add_button'    => esc_html__('Add more', 'opalestate-pro'),
                    'remove_button' => esc_html__('Remove', 'opalestate-pro'),
                    'sortable'      => true,
                    'closed'        => true,
                ],
            ],
        ];

        return apply_filters('opalestate_metaboxes_public_facilities_fields', $fields);
    }

    public function metaboxes_public_apartments_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'name'       => esc_html__('Apartments', 'opalestate-pro'),
                'id'         => $prefix . 'enable_apartments',
                'type'       => 'heading',
                'options'    => [
                    0 => esc_html__('No', 'opalestate-pro'),
                    1 => esc_html__('Yes', 'opalestate-pro'),
                ],
                'before_row' => '<div id="opalestate-submission-apartments" class="opalestate-tab-content">',
            ],
            [
                'id'          => $prefix . 'apartments',
                'type'        => 'group',
                'after_group' => '<button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step',
                        'opalestate-pro') . '</button><button type="button" class="submission-next-btn btn btn-primary">' . esc_html__('Next Step', 'opalestate-pro') . '</button></div>',
                'fields'      => [
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
                            'available'   => esc_html__('Available', 'opalestate-pro'),
                            'unavailable' => esc_html__('Unavailable', 'opalestate-pro'),
                        ]),
                        'before_row' => '<div class="field-row-2">',
                    ],
                    [
                        'id'        => $prefix . 'apartment_link',
                        'name'      => esc_html__('Link', 'opalestate-pro'),
                        'type'      => 'text',
                        'default'   => '#',
                        'after_row' => '</div>',
                    ],
                ],
                'options'     => [
                    'group_title'   => esc_html__('Apartment {#}', 'opalestate-pro'),
                    'add_button'    => esc_html__('Add more', 'opalestate-pro'),
                    'remove_button' => esc_html__('Remove', 'opalestate-pro'),
                    'sortable'      => true,
                    'closed'        => true,
                ],
            ],
        ];

        return apply_filters('opalestate_metaboxes_public_apartments_fields', $fields);
    }

    public function metaboxes_public_floor_plans_fields() {
        $prefix = OPALESTATE_PROPERTY_PREFIX;
        $fields = [
            [
                'name'       => esc_html__('Floor Plans', 'opalestate-pro'),
                'id'         => $prefix . 'enable_floor',
                'type'       => 'heading',
                'before_row' => '<div id="opalestate-submission-floor_plans" class="opalestate-tab-content">',
            ],
            [
                'id'          => $prefix . 'public_floor_group',
                'type'        => 'group',
                'after_group' => '<button type="button" class="submission-back-btn btn btn-primary">' . esc_html__('Previous Step', 'opalestate-pro') . '</button></div>',
                'fields'      => [
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
                        'id'     => "{$prefix}floor_image_id",
                        'name'   => esc_html__('Image Preview', 'opalestate-pro'),
                        'type'   => 'uploader',
                        'single' => 1,

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
                'options'     => [
                    'group_title'   => esc_html__('Floor {#}', 'opalestate-pro'),
                    'add_button'    => esc_html__('Add more', 'opalestate-pro'),
                    'remove_button' => esc_html__('Remove', 'opalestate-pro'),
                    'sortable'      => true,
                    'closed'        => false,
                ],
            ],
        ];

        return apply_filters('opalestate_metaboxes_public_floor_plans_fields', $fields);
    }

    protected function is_enabled_tab($tab) {
        return ('on' === opalestate_get_option('enable_submission_tab_' . $tab, true));
    }

    /**
     * Manually render a field column display.
     *
     * @param array $field_args Array of field arguments.
     * @param CMB2_Field $field The field object
     */
    public function amenities_html_callback($field_args, $field) {
        $id          = $field->args('id');
        $label       = $field->args('name');
        $name        = $field->args('_name');
        $value       = $field->escaped_value();
        $description = $field->args('description');

        $amenites = get_terms([
            'taxonomy'   => 'opalestate_amenities',
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
        ]);

        if (!$amenites) {
            return;
        }

        if (!empty($_GET['id'])) {
            $post = get_post(intval($_GET['id']));
        } else {
            $post = null;
        }


        ?>
        <div id="opalestate-submission-amenities" class="opalestate-tab-content">
            <div class="cmb-row cmb-type-taxonomy-multicheck cmb2-id-opalestate-ppt-amenity" data-fieldtype="taxonomy_multicheck">
                <div class="cmb-th">
                    <label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label); ?></label>
                </div>

                <div class="cmb-td">
                    <ul class="cmb2-checkbox-list list-inline cmb2-list">
                        <?php foreach ($amenites as $key => $amenity) : ?>
                            <?php $checked = (!empty($post) && has_term($amenity->term_id, 'opalestate_amenities', $post)) ? 'checked="checked"' : ''; ?>
                            <li>
                                <input type="checkbox" class="cmb2-option" name="<?php echo esc_attr($name); ?>[]" id="opalestate_ppt_amenity<?php echo esc_attr($key + 1); ?>"
                                       value="<?php echo esc_attr($amenity->slug); ?>" <?php echo $checked; ?>>
                                <label for="opalestate_ppt_amenity<?php echo esc_attr($key + 1); ?>">
                                    <?php
                                    if ($image_id = get_term_meta($amenity->term_id, 'opalestate_amt_image_id', true)) {
                                        echo opalestate_get_image_by_id($image_id);
                                    }
                                    ?>
                                    <?php echo esc_html($amenity->name); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="submission-back-btn btn btn-primary"><?php esc_html_e('Previous Step', 'opalestate-pro'); ?></button>
            <button type="button" class="submission-next-btn btn btn-primary"><?php esc_html_e('Next Step', 'opalestate-pro'); ?></button>
        </div>
        <?php
    }
}
