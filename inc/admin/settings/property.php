<?php
/**
 * Opalestate_Settings_Property_Tab
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Settings_Property_Tab extends Opalestate_Settings_Base_Tab {
    public function get_subtabs() {
        return apply_filters(
            'opalestate_settings_property_subtabs_nav',
            [
                'property_general' => esc_html__('General', 'opalestate-pro'),
                'property_search'  => esc_html__('Search', 'opalestate-pro'),
                'property_detail'  => esc_html__('Single Page', 'opalestate-pro'),
            ]
        );
    }

    public function get_subtabs_content($key = "") {
        $fields = apply_filters('opalestate_settings_property_subtabs_' . $key . '_fields', []);
        if ($fields) {

        } else {
            switch ($key) {
                case 'property_search':
                    $fields = $this->get_subtab_search_fields();
                    break;

                case 'property_detail':
                    $fields = $this->get_subtab_detail_fields();
                    break;

                default:
                    $fields = $this->get_subtab_property_fields();
                    break;
            }
        }

        return [
            'id'               => 'options_page',
            'opalestate_title' => esc_html__('Property Settings', 'opalestate-pro'),
            'show_on'          => ['key' => 'options-page', 'value' => [$key],],
            'fields'           => $fields,
        ];
    }

    private function get_subtab_property_fields() {
        $fields = [];

        $fields[] = [
            'name'    => esc_html__('Enable User Submission', 'opalestate-pro'),
            'desc'    => esc_html__('Enable to allow user post/submit properties in front-end', 'opalestate-pro'),
            'id'      => 'enable_submission',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
        ];

        // show setting short meta infox
        $metabox = new Opalestate_Property_MetaBox();
        $metas   = $metabox->metaboxes_info_fields();

        $checkes = [];

        foreach ($metas as $key => $field) {
            $id            = str_replace(OPALESTATE_PROPERTY_PREFIX, '', $field['id']);
            $checkes [$id] = $field['name'];
        }

        $fields[] = [
            'name'    => esc_html__('Show Meta Information in property collection.', 'opalestate-pro'),
            'id'      => 'show_property_meta',
            'type'    => 'multicheck',
            'options' => $checkes,
        ];

        $fields[] = [
            'name'    => esc_html__('Default Search form', 'opalestate-pro'),
            'id'      => 'default_search_form',
            'type'    => 'select',
            'options' => opalestate_search_properties_form_styles(),
            'default' => 'collapse-city',
        ];

        $fields[] = [
            'name'    => esc_html__('Default Display mode', 'opalestate-pro'),
            'id'      => 'displaymode',
            'type'    => 'select',
            'options' => opalestate_display_modes(),
        ];

        $fields[] = [
            'name'    => esc_html__('Archive Grid layout', 'opalestate-pro'),
            'id'      => 'property_archive_grid_layout',
            'type'    => 'select',
            'options' => opalestate_get_loop_property_grid_layouts(),
        ];

        $fields[] = [
            'name'    => esc_html__('Archive List layout', 'opalestate-pro'),
            'id'      => 'property_archive_list_layout',
            'type'    => 'select',
            'options' => opalestate_get_loop_property_list_layouts(),
        ];

        return $fields;
    }

    private function get_subtab_search_fields() {
        $pages = opalestate_cmb2_get_post_options([
            'post_type'   => 'page',
            'numberposts' => -1,
        ]);

        $metabox = new Opalestate_Property_MetaBox();
        $metas   = $metabox->metaboxes_info_fields();

        $fields = [];

        if ($metas) {
            $fields[] = [
                'name'    => esc_html__('User Share Search', 'opalestate-pro'),
                'desc'    => esc_html__('Display Share Search Link Management', 'opalestate-pro'),
                'id'      => 'enable_share_earch',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Enable', 'opalestate-pro'),
                    'off' => esc_html__('Disable', 'opalestate-pro'),
                ],
            ];

            $fields[] = [
                'name'    => esc_html__('User Saved Search', 'opalestate-pro'),
                'desc'    => esc_html__('Display Save Search Link Management', 'opalestate-pro'),
                'id'      => 'enable_saved_usersearch',
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Enable', 'opalestate-pro'),
                    'off' => esc_html__('Disable', 'opalestate-pro'),
                ],
            ];


            $fields[] = [
                'name'    => esc_html__('Search Properties Page', 'opalestate-pro'),
                'desc'    => esc_html__('This is page to display result of properties after user searching via form.',
                    'opalestate-pro'),
                'id'      => 'search_map_properties_page',
                'type'    => 'select',
                'options' => opalestate_cmb2_get_post_options([
                    'post_type'   => 'page',
                    'numberposts' => -1,
                ]),
                'default' => '',
            ];

            $fields[] = [
                'name'       => esc_html__('Properties Per Page', 'opalestate-pro'),
                'desc'       => esc_html__('Enter min of properties display in search page', 'opalestate-pro'),
                'id'         => 'search_property_per_page',
                'type'       => 'text_small',
                'attributes' => [
                    'type' => 'number',
                ],
                'default'    => 9,
            ];


            $fields[] = [
                'name'    => esc_html__('Show Featured First', 'opalestate-pro'),
                'id'      => 'show_featured_first',
                'desc'    => esc_html__('Show featured first in page result, as default Newest is showed', 'opalestate-pro'),
                'type'    => 'switch',
                'options' => [
                    'on'  => esc_html__('Enable', 'opalestate-pro'),
                    'off' => esc_html__('Disable', 'opalestate-pro'),
                ],
                'default' => 'off',
            ];
            $fields[] = [
                'name'       => esc_html__('Minimum of Search Price', 'opalestate-pro'),
                'desc'       => esc_html__('Enter minimum of price for starting search', 'opalestate-pro'),
                'id'         => 'search_min_price',
                'type'       => 'text_medium',
                'attributes' => [
                    'type' => 'number',
                ],
                'default'    => 0,
            ];
            $fields[] = [
                'name'       => esc_html__('Maximum of Search Price', 'opalestate-pro'),
                'desc'       => esc_html__('Enter maximum of price for starting search', 'opalestate-pro'),
                'id'         => 'search_max_price',
                'type'       => 'text_medium',
                'attributes' => [
                    'type' => 'number',
                ],
                'default'    => 10000000,
            ];


            $fields[] = [
                'name'       => esc_html__('Minimum of Search Aea', 'opalestate-pro'),
                'desc'       => esc_html__('Enter minimum of area for starting search', 'opalestate-pro'),
                'id'         => 'search_min_area',
                'type'       => 'text_small',
                'attributes' => [
                    'type' => 'number',
                ],
                'default'    => 0,
            ];
            $fields[] = [
                'name'       => esc_html__('Maximum of Search Aea', 'opalestate-pro'),
                'desc'       => esc_html__('Enter maximum of area for starting search', 'opalestate-pro'),
                'id'         => 'search_max_area',
                'type'       => 'text_small',
                'attributes' => [
                    'type' => 'number',
                ],
                'default'    => 1000,
            ];

            $fields[] = [
                'name'    => esc_html__('Search Grid layout', 'opalestate-pro'),
                'id'      => 'property_search_grid_layout',
                'type'    => 'select',
                'options' => opalestate_get_loop_property_grid_layouts(),
            ];

            $fields[] = [
                'name'    => esc_html__('Search List layout', 'opalestate-pro'),
                'id'      => 'property_search_list_layout',
                'type'    => 'select',
                'options' => opalestate_get_loop_property_list_layouts(),
            ];

            $fields[] = [
                'name'       => esc_html__('Search Fields', 'opalestate-pro'),
                'type'       => 'opalestate_title',
                'id'         => 'opalestate_title_search_fields',
                'before_row' => '<hr>',
                'after_row'  => '<em>' . __('Enable/Disable fields appearing in search properties form.', 'opalestate-pro') . '</em><hr>',
            ];

            $fields[] = [
                'name'    => esc_html__('Show Price', 'opalestate-pro'),
                'id'      => OPALESTATE_PROPERTY_PREFIX . 'price_opt',
                'type'    => 'switch',
                'options' => [
                    0 => esc_html__('Disable', 'opalestate-pro'),
                    1 => esc_html__('Enable', 'opalestate-pro'),
                ],
            ];

            $fields[] = [
                'name'    => esc_html__('Price input type', 'opalestate-pro'),
                'options' => [
                    'slider' => esc_html__('Range slider', 'opalestate-pro'),
                    'input'  => esc_html__('Input', 'opalestate-pro'),
                ],
                'id'      => 'price_input_type',
                'type'    => 'select',
                'default' => 'slider',
            ];

            foreach ($metas as $key => $meta) {
                $fields[] = [
                    'name'    => $meta['name'],
                    'id'      => $meta['id'] . '_opt',
                    'type'    => 'switch',
                    'options' => [
                        0 => esc_html__('Disable', 'opalestate-pro'),
                        1 => esc_html__('Enable', 'opalestate-pro'),
                    ],
                ];
            }

            $fields[] = [
                'name'       => esc_html__('Search Fields type', 'opalestate-pro'),
                'type'       => 'opalestate_title',
                'id'         => 'opalestate_title_general_settings_type_search',
                'before_row' => '<hr>',
                'after_row'  => '<em>' . __('Input type for search fields.', 'opalestate-pro') . '</em>',
            ];

            $metas = Opalestate_Property_MetaBox::metaboxes_info_fields();

            wp_enqueue_script('opalestate-setting-custom-fields', OPALESTATE_PLUGIN_URL . 'assets/js/custom-fields.js', ['jquery'], OPALESTATE_VERSION, false);

            foreach ($metas as $meta) {
                if ($meta['id'] == OPALESTATE_PROPERTY_PREFIX . 'areasize') {
                    continue;
                }

                $fields[] = [
                    'name'       => $meta['name'],
                    'desc'       => '',
                    'type'       => 'opalestate_title',
                    'id'         => 'opalestate_title_search_field_' . $meta['id'],
                    'before_row' => '<hr>',
                ];

                $fields[] = [
                    'name'    => esc_html__('Field type', 'opalestate-pro'),
                    'options' => [
                        'select' => esc_html__('Select', 'opalestate-pro'),
                        'range'  => esc_html__('Range', 'opalestate-pro'),
                        'text'   => esc_html__('Text', 'opalestate-pro'),
                    ],
                    'id'      => $meta['id'] . '_search_type',
                    'type'    => 'radio_inline',
                    'default' => 'select',
                ];

                $fields[] = [
                    'name'        => esc_html__('Options', 'opalestate-pro'),
                    'description' => esc_html__('Options value select. Use "," to separate values.', 'opalestate-pro'),
                    'id'          => $meta['id'] . '_options_value',
                    'type'        => 'text',
                    'default'     => '1,2,3,4,5,6,7,8,9,10',
                ];

                $fields[] = [
                    'name'        => esc_html__('Min range', 'opalestate-pro'),
                    'description' => esc_html__('Min range', 'opalestate-pro'),
                    'id'          => $meta['id'] . '_min_range',
                    'type'        => 'text',
                    'default'     => 1,
                ];

                $fields[] = [
                    'name'        => esc_html__('Max range', 'opalestate-pro'),
                    'description' => esc_html__('Max range', 'opalestate-pro'),
                    'id'          => $meta['id'] . '_max_range',
                    'type'        => 'text',
                    'default'     => 10000000,
                ];

                $fields[] = [
                    'name'        => esc_html__('Unit thousand', 'opalestate-pro'),
                    'description' => esc_html__('Unit thousand', 'opalestate-pro'),
                    'id'          => $meta['id'] . '_unit_thousand',
                    'type'        => 'text',
                ];

                $fields[] = [
                    'name'        => esc_html__('Default text', 'opalestate-pro'),
                    'description' => esc_html__('Default text value', 'opalestate-pro'),
                    'id'          => $meta['id'] . '_default_text',
                    'type'        => 'text',
                    'default'     => '',
                ];
            }
        }

        return $fields;
    }

    /**
     * Get subtab detail fields.
     */
    private function get_subtab_detail_fields() {
        $fields = [];

        $fields[] = [
            'name'      => esc_html__('Single Layout Page', 'opalestate-pro'),
            'desc'      => esc_html__('Choose layout for single property.', 'opalestate-pro'),
            'id'        => 'layout',
            'type'      => 'select',
            'options'   => apply_filters('opalestate_single_layout_templates', ['' => esc_html__('Inherit', 'opalestate-pro')]),
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Single Preview Display', 'opalestate-pro'),
            'desc'      => esc_html__('Choose preview layout for single property.', 'opalestate-pro'),
            'id'        => 'single_preview',
            'type'      => 'select',
            'options'   => opalestate_single_layout_preview(),
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Enable Request Viewing', 'opalestate-pro'),
            'desc'    => esc_html__('Enable Request Viewing feature in the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_request_viewing',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'default' => 'on',
        ];

        $fields[] = [
            'name'       => esc_html__('Request Viewing Time Range (minute)', 'opalestate-pro'),
            'desc'       => esc_html__('Time range from 1-60 minutes.', 'opalestate-pro'),
            'id'         => 'request_viewing_time_range',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
                'min'  => 1,
                'max'  => 60,
            ],
            'default'    => 15,
            'after_row'  => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Show Amenities tab', 'opalestate-pro'),
            'desc'    => esc_html__('Show Amenities tab in the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_amenities',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
        ];

        $fields[] = [
            'name'      => esc_html__('Hide Unset amenities', 'opalestate-pro'),
            'desc'      => esc_html__('Hide unset amenities. Default: Show unset amenities with disable icons.', 'opalestate-pro'),
            'id'        => 'hide_unset_amenities',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Facilities tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Facilities tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_facilities',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Attachments tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Attachments tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_attachments',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Video tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Video tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_video',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Virtual Tour tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Virtual Tour tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_virtual_tour',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Map tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Map tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_map',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Nearby tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Nearby tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_nearby',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Walk Scores tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Walk Scores tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_walkscores',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Apartments tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Apartments tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_apartments',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Floor Plans tab', 'opalestate-pro'),
            'desc'      => esc_html__('Show Floor Plans tab in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_floor_plans',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Show Views Statistics tab', 'opalestate-pro'),
            'desc'    => esc_html__('Show Views Statistics tab in the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_views_statistics',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
        ];

        $fields[] = [
            'name'       => esc_html__('Views Statistics time limit (days)', 'opalestate-pro'),
            'desc'       => esc_html__('The number of days will be saved to the database.', 'opalestate-pro'),
            'id'         => 'single_views_statistics_limit',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
                'min'  => 1,
                'max'  => 365,
            ],
            'default'    => 8,
            'after_row'  => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Login to show Price', 'opalestate-pro'),
            'desc'      => esc_html__('Require users login to show Price', 'opalestate-pro'),
            'id'        => 'enable_single_login_to_show_price',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Show Author box', 'opalestate-pro'),
            'desc'    => esc_html__('Show Author box in the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_author_box',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
        ];

        $fields[] = [
            'name'      => esc_html__('Login to show Author box', 'opalestate-pro'),
            'desc'      => esc_html__('Require users login to show Author box', 'opalestate-pro'),
            'id'        => 'enable_single_login_to_show_author_box',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Show Enquire form', 'opalestate-pro'),
            'desc'    => esc_html__('Show Enquire form in the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_enquire_form',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
        ];

        $fields[] = [
            'name'      => esc_html__('Login to show Enquire form', 'opalestate-pro'),
            'desc'      => esc_html__('Require users login to show Enquire form', 'opalestate-pro'),
            'id'        => 'enable_single_login_to_enquire_form',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'      => esc_html__('Show Mortgage Calculator', 'opalestate-pro'),
            'desc'      => esc_html__('Show Mortgage Calculator in the single property page.', 'opalestate-pro'),
            'id'        => 'enable_single_mortgage',
            'type'      => 'switch',
            'options'   => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'after_row' => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Show Related properties', 'opalestate-pro'),
            'desc'    => esc_html__('Show Related properties the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_related_properties',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'default' => 'on',
        ];

        $fields[] = [
            'name'    => esc_html__('Related properties layout', 'opalestate-pro'),
            'desc'    => esc_html__('Select a layout for related properties.', 'opalestate-pro'),
            'id'      => 'single_related_properties_layout',
            'type'    => 'select',
            'options' => opalestate_get_loop_property_layouts(),
        ];

        $fields[] = [
            'name'       => esc_html__('Number of properties', 'opalestate-pro'),
            'id'         => 'single_related_number',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
                'min'  => 1,
            ],
            'default'    => 6,
            'after_row'  => '<hr>',
        ];

        $fields[] = [
            'name'    => esc_html__('Show Nearby properties', 'opalestate-pro'),
            'desc'    => esc_html__('Show Nearby properties in the single property page.', 'opalestate-pro'),
            'id'      => 'enable_single_nearby_properties',
            'type'    => 'switch',
            'options' => [
                'on'  => esc_html__('Enable', 'opalestate-pro'),
                'off' => esc_html__('Disable', 'opalestate-pro'),
            ],
            'default' => 'on',
        ];

        $fields[] = [
            'name'    => esc_html__('Nearby properties layout', 'opalestate-pro'),
            'desc'    => esc_html__('Select a layout for nearby properties.', 'opalestate-pro'),
            'id'      => 'single_nearby_properties_layout',
            'type'    => 'select',
            'options' => opalestate_get_loop_property_layouts(),
        ];

        $fields[] = [
            'name'       => esc_html__('Nearby Radius', 'opalestate-pro'),
            'id'         => 'single_nearby_radius',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
                'min'  => 1,
            ],
            'default'    => 5,
        ];

        $fields[] = [
            'name'    => esc_html__('Nearby Measure Unit', 'opalestate-pro'),
            'id'      => 'single_nearby_measure_unit',
            'type'    => 'select',
            'options' => [
                'km'    => esc_html__('km', 'opalestate-pro'),
                'miles' => esc_html__('miles', 'opalestate-pro'),
            ],
        ];

        $fields[] = [
            'name'       => esc_html__('Number of properties', 'opalestate-pro'),
            'id'         => 'single_nearby_number',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
                'min'  => 1,
            ],
            'default'    => 6,
            'after_row'  => '<hr>',
        ];

        return $fields;
    }
}
