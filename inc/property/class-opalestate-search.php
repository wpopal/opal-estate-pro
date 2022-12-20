<?php
/**
 * OpalEstate_Search
 *
 * @package    opalestate
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OpalEstate_Search {
    /**
     * Add action to ajax search to display query data results with json format.
     */
    public static function init() {
        add_action('wp_ajax_opalestate_ajx_get_properties', [__CLASS__, 'get_search_json']);
        add_action('wp_ajax_nopriv_opalestate_ajx_get_properties', [__CLASS__, 'get_search_json']);
        add_action('wp_ajax_opalestate_render_get_properties', [__CLASS__, 'render_get_properties']);
        add_action('wp_ajax_nopriv_opalestate_render_get_properties', [__CLASS__, 'render_get_properties']);
    }

    /**
     * Get Query Object to display collection of property with user request which submited via search form.
     *
     * @param int $limit Limit.
     */
    public static function get_search_results_query($limit = 9) {
        global $wp_query;

        $search_min_price = '';
        $search_max_price = '';
        if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
            $search_min_price = isset($_GET['min_price']) ? sanitize_text_field($_GET['min_price']) : '';
            $search_max_price = isset($_GET['max_price']) ? sanitize_text_field($_GET['max_price']) : '';
        } elseif (isset($_GET['range_price'])) {
            $range_price = explode('-', sanitize_text_field($_GET['range_price']));
            if (isset($range_price[0]) && isset($range_price[1])) {
                $search_min_price = 'min' !== $range_price[0] ? $range_price[0] : '';
                $search_max_price = 'max' !== $range_price[1] ? $range_price[1] : '';
            }
        }

        $search_min_area = '';
        $search_max_area = '';
        if (isset($_GET['min_area']) || isset($_GET['max_area'])) {
            $search_min_area = isset($_GET['min_area']) ? sanitize_text_field($_GET['min_area']) : '';
            $search_max_area = isset($_GET['max_area']) ? sanitize_text_field($_GET['max_area']) : '';
        } elseif (isset($_GET['range_area'])) {
            $range_area = explode('-', sanitize_text_field($_GET['range_area']));
            if (isset($range_area[0]) && isset($range_area[1])) {
                $search_min_area = 'min' !== $range_area[0] ? $range_area[0] : '';
                $search_max_area = 'max' !== $range_area[1] ? $range_area[1] : '';
            }
        }

        $s = isset($_GET['search_text']) ? sanitize_text_field($_GET['search_text']) : null;

        $posts_per_page = apply_filters('opalestate_search_property_per_page', opalestate_options('search_property_per_page', $limit));

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $paged = isset($wp_query->query['paged']) ? $wp_query->query['paged'] : $paged;
        $paged = !empty($_REQUEST['paged']) ? absint($_REQUEST['paged']) : $paged;

        if (isset($_GET['paged']) && absint($_GET['paged']) > 0) {
            $paged = absint($_GET['paged']);
        }

        $args = [
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'post_type'      => 'opalestate_property',
            'post_status'    => 'publish',
            's'              => $s,
        ];

        $tax_query = [];

        if (isset($_GET['location']) && $_GET['location'] != -1) {
            if (is_array($_GET['location'])) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_location',
                    'field'    => 'slug',
                    'terms'    => opalestate_clean($_GET['location']),
                ];
            } else {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_location',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['location']),
                ];
            }
        }

        if (isset($_GET['state']) && $_GET['state'] != -1) {
            if (is_array($_GET['state'])) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_state',
                    'field'    => 'slug',
                    'terms'    => opalestate_clean($_GET['state']),
                ];
            } else {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_state',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['state']),
                ];
            }
        }

        if (isset($_GET['city']) && $_GET['city'] != -1) {
            if (is_array($_GET['city'])) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_city',
                    'field'    => 'slug',
                    'terms'    => opalestate_clean($_GET['city']),
                ];
            } else {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_city',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['city']),
                ];
            }
        }

        if (isset($_GET['types']) && $_GET['types'] != -1) {
            if (is_array($_GET['types'])) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_types',
                    'field'    => 'slug',
                    'terms'    => opalestate_clean($_GET['types']),
                ];
            } else {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_types',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['types']),
                ];
            }
        }

        if (isset($_GET['cat']) && $_GET['cat'] != -1) {
            if (is_array($_GET['cat'])) {
                $tax_query[] = [
                    'taxonomy' => 'property_category',
                    'field'    => 'slug',
                    'terms'    => opalestate_clean($_GET['cat']),
                ];
            } else {
                $tax_query[] = [
                    'taxonomy' => 'property_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['cat']),
                ];
            }
        }

        if (isset($_GET['status']) && $_GET['status'] != -1) {
            if (is_array($_GET['status'])) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_status',
                    'field'    => 'slug',
                    'terms'    => opalestate_clean($_GET['status']),
                ];
            } else {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_status',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['status']),
                ];
            }

        }

        if (isset($_GET['amenities']) && is_array($_GET['amenities'])) {
            $tax_query[] = [
                'taxonomy' => 'opalestate_amenities',
                'field'    => 'slug',
                'terms'    => opalestate_clean($_GET['amenities']),
            ];
        }

        $tax_query = apply_filters('opalestate_search_results_tax_query', $tax_query);

        if ($tax_query) {
            $args['tax_query'] = ['relation' => 'AND'];
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        $args['meta_query'] = ['relation' => 'AND'];
        if (isset($_GET['info']) && is_array($_GET['info'])) {
            $metaquery = [];

            foreach ($_GET['info'] as $key => $value) {
                if (trim($value)) {
                    if (is_numeric(trim($value))) {
                        $fieldquery = [
                            'key'     => OPALESTATE_PROPERTY_PREFIX . $key,
                            'value'   => sanitize_text_field(trim($value)),
                            'compare' => apply_filters('opalestate_info_numeric_compare', '>='),
                            'type'    => 'NUMERIC',
                        ];
                    } else {
                        $fieldquery = [
                            'key'     => OPALESTATE_PROPERTY_PREFIX . $key,
                            'value'   => sanitize_text_field(trim($value)),
                            'compare' => 'LIKE',
                        ];
                    }
                    $sarg        = apply_filters('opalestate_search_field_query_' . $key, $fieldquery);
                    $metaquery[] = $sarg;
                }
            }

            $args['meta_query'] = array_merge($args['meta_query'], $metaquery);
        }

        if ($search_min_price != '' && $search_max_price != '' && is_numeric($search_min_price) && is_numeric($search_max_price)) {
            if ($search_min_price) {
                $args['meta_query'][] = [
                    'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                    'value'   => [$search_min_price, $search_max_price],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                ];
            } else {
                $args['meta_query'][] = [
                    [
                        [
                            'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                            'compare' => 'NOT EXISTS',
                        ],
                        'relation' => 'OR',
                        [
                            'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                            'value'   => $search_max_price,
                            'compare' => '<=',
                            'type'    => 'NUMERIC',
                        ],
                    ],
                ];
            }
        } elseif ($search_min_price != '' && is_numeric($search_min_price)) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                'value'   => $search_min_price,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        } elseif ($search_max_price != '' && is_numeric($search_max_price)) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'price',
                'value'   => $search_max_price,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ];
        }

        if ($search_min_area != '' && $search_max_area != '' && is_numeric($search_min_area) && is_numeric($search_max_area)) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'areasize',
                'value'   => [$search_min_area, $search_max_area],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ];
        } elseif ($search_min_area != '' && is_numeric($search_min_area)) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'areasize',
                'value'   => $search_min_area,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        } elseif ($search_max_area != '' && is_numeric($search_max_area)) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_PROPERTY_PREFIX . 'areasize',
                'value'   => $search_max_area,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ];
        }

        if (isset($_GET['geo_long']) && isset($_GET['geo_lat'])) {
            if ($_GET['location_text'] && (empty($_GET['geo_long']) || empty($_GET['geo_lat']))) {
                $args['meta_query'][] = [
                    'key'      => OPALESTATE_PROPERTY_PREFIX . 'map_address',
                    'value'    => sanitize_text_field(trim($_GET['location_text'])),
                    'compare'  => 'LIKE',
                    'operator' => 'OR',
                ];
            } elseif ($_GET['geo_lat'] && $_GET['geo_long']) {
                $radius_measure   = isset($_GET['radius_measure']) ? sanitize_text_field($_GET['radius_measure']) : 'km';
                $radius           = isset($_GET['geo_radius']) ? sanitize_text_field($_GET['geo_radius']) : 10;
                $post_ids         = Opalestate_Query::filter_by_location(sanitize_text_field($_GET['geo_lat']), sanitize_text_field($_GET['geo_long']), $radius, $radius_measure);
                $args['post__in'] = $post_ids;
            }
        }

        $ksearchs = [];

        if (isset($_REQUEST['opalsortable']) && !empty($_REQUEST['opalsortable'])) {
            $ksearchs = explode('_', $_REQUEST['opalsortable']);
        } elseif (isset($_SESSION['opalsortable']) && !empty($_SESSION['opalsortable'])) {
            $ksearchs = explode('_', $_SESSION['opalsortable']);
        }

        if (!empty($ksearchs) && count($ksearchs) == 2) {
            if ('featured' === $ksearchs[0]) {
                $args['orderby']  = ['meta_value' => 'DESC', 'date' => 'DESC'];
                $args['meta_key'] = OPALESTATE_PROPERTY_PREFIX . 'featured';
            } else {
                $args['meta_key'] = OPALESTATE_PROPERTY_PREFIX . $ksearchs[0];
                $args['orderby']  = 'meta_value_num';
                $args['order']    = $ksearchs[1];
            }
        } elseif ('on' === opalestate_options('show_featured_first', 'off')) {
            $args['orderby']  = ['meta_value' => 'DESC', 'date' => 'DESC'];
            $args['meta_key'] = OPALESTATE_PROPERTY_PREFIX . 'featured';
        }

        $metas = Opalestate_Property_MetaBox::metaboxes_info_fields();

        foreach ($metas as $meta) {
            if ($meta['id'] == OPALESTATE_PROPERTY_PREFIX . 'areasize') {
                continue;
            }

            $request             = str_replace(OPALESTATE_PROPERTY_PREFIX, '', $meta['id']);
            $setting_search_type = opalestate_options($meta['id'] . '_search_type', 'select');

            if ('range' === $setting_search_type) {
                $min_request = isset($_GET['min_' . $request]) ? sanitize_text_field($_GET['min_' . $request]) : '';
                $max_request = isset($_GET['max_' . $request]) ? sanitize_text_field($_GET['max_' . $request]) : '';

                if ($min_request != '' && $max_request != '' && is_numeric($min_request) && is_numeric($max_request)) {
                    $args['meta_query'][] = [
                        'key'     => $meta['id'],
                        'value'   => [$min_request, $max_request],
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    ];
                } elseif ($min_request != '' && is_numeric($min_request)) {
                    $args['meta_query'][] = [
                        'key'     => $meta['id'],
                        'value'   => $min_request,
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    ];
                } elseif ($max_request != '' && is_numeric($max_request)) {
                    $args['meta_query'][] = [
                        'key'     => $meta['id'],
                        'value'   => $max_request,
                        'compare' => '<=',
                        'type'    => 'NUMERIC',
                    ];
                }
            }
        }

        $args  = apply_filters('opalestate_get_search_results_query_args', $args);
        $query = new WP_Query($args);

        wp_reset_postdata();

        return $query;
    }

    /**
     * Get search query base on user request to filter collection of Agents
     */
    public static function get_search_agents_query($args = []) {
        $min = opalestate_options('search_agent_min_price', 0);
        $max = opalestate_options('search_agent_max_price', 10000000);

        $search_min_price = isset($_GET['min_price']) ? sanitize_text_field($_GET['min_price']) : '';
        $search_max_price = isset($_GET['max_price']) ? sanitize_text_field($_GET['max_price']) : '';

        $search_min_area = isset($_GET['min_area']) ? sanitize_text_field($_GET['min_area']) : '';
        $search_max_area = isset($_GET['max_area']) ? sanitize_text_field($_GET['max_area']) : '';
        $s               = isset($_GET['search_text']) ? sanitize_text_field($_GET['search_text']) : null;

        $paged   = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
        $default = [
            'post_type'      => 'opalestate_agent',
            'posts_per_page' => apply_filters('opalestate_agent_per_page', 12),
            'paged'          => $paged,
        ];
        $args    = array_merge($default, $args);

        $tax_query = [];


        if (isset($_GET['location']) && $_GET['location'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_location',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['location']),
            ];
        }

        if (isset($_GET['types']) && $_GET['types'] != -1) {
            $tax_query[]
                = [
                'taxonomy' => 'opalestate_types',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['types']),
            ];
        }

        if ($tax_query) {
            $args['tax_query'] = ['relation' => 'AND'];
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        $args['meta_query'] = ['relation' => 'AND'];

        if ($search_min_price != $min && is_numeric($search_min_price)) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_AGENT_PREFIX . 'target_min_price',
                'value'   => $search_min_price,
                'compare' => '>=',
                // 'type' => 'NUMERIC'
            ];
        }
        if (is_numeric($search_max_price) && $search_max_price != $max) {
            $args['meta_query'][] = [
                'key'     => OPALESTATE_AGENT_PREFIX . 'target_max_price',
                'value'   => $search_max_price,
                'compare' => '<=',
                // 'type' => 'NUMERIC'
            ];
        }

        return new WP_Query($args);
    }


    /**
     * Get search query base on user request to filter collection of Agents
     */
    public static function get_search_agencies_query($args = []) {
        $s = isset($_GET['search_text']) ? sanitize_text_field($_GET['search_text']) : null;

        $paged   = (get_query_var('paged') == 0) ? 1 : get_query_var('paged');
        $default = [
            'post_type'      => 'opalestate_agency',
            'posts_per_page' => apply_filters('opalestate_agency_per_page', 12),
            'paged'          => $paged,
        ];
        $args    = array_merge($default, $args);

        $tax_query = [];

        if (isset($_GET['location']) && $_GET['location'] != -1) {
            $tax_query[] = [
                'taxonomy' => 'opalestate_location',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['location']),
            ];
        }

        if (isset($_GET['types']) && $_GET['types'] != -1) {
            $tax_query[] = [
                'taxonomy' => 'opalestate_types',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['types']),
            ];
        }

        if ($tax_query) {
            $args['tax_query'] = ['relation' => 'AND'];
            $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
        }

        $args['meta_query'] = ['relation' => 'AND'];


        return new WP_Query($args);
    }

    public function filter_by_geolocations() {

    }

    /**
     * Get_setting_search_fields.
     */
    public static function get_setting_search_fields($option = '') {
        $default = apply_filters('opalestate_default_fields_setting', [
            OPALESTATE_PROPERTY_PREFIX . 'bedrooms'  => esc_html__('Bed Rooms', 'opalestate-pro'),
            OPALESTATE_PROPERTY_PREFIX . 'parking'   => esc_html__('Parking', 'opalestate-pro'),
            OPALESTATE_PROPERTY_PREFIX . 'bathrooms' => esc_html__('Bath Rooms', 'opalestate-pro'),
        ]);

        $metas = Opalestate_Property_MetaBox::metaboxes_info_fields();

        $esettings = [];
        $found     = false;
        foreach ($metas as $key => $meta) {
            $value = opalestate_options($meta['id'] . '_opt' . $option);

            if (preg_match("#areasize#", $meta['id'])) {
                continue;
            }

            if ('on' === $value) {
                $id             = str_replace(OPALESTATE_PROPERTY_PREFIX, '', $meta['id']);
                $esettings[$id] = $meta['name'];
            }

            if ($value == 0) {
                $found = true;
            }
        }

        if (!empty($esettings)) {
            return $esettings;
        } elseif ($found) {
            return [];
        }

        return $default;
    }

    /**
     * Get Json data by action ajax filter
     */
    public static function get_search_json() {
        $query = self::get_search_results_query();

        $output = [];

        while ($query->have_posts()) {

            $query->the_post();
            $property = opalesetate_property(get_the_ID());
            $output[] = $property->get_meta_search_objects();
        }

        wp_reset_query();

        echo json_encode($output);
        exit;
    }

    public static function render_get_properties() {
        echo opalestate_load_template_path('shortcodes/ajax-map-search-result');
        die;
    }

    /**
     * Render search property form in horizontal
     */
    public static function render_horizontal_form($atts = []) {
        echo opalestate_load_template_path('search-box/search-form-h', $atts);
    }

    /**
     * Render search property form in vertical
     */
    public static function render_vertical_form($atts = []) {
        echo opalestate_load_template_path('search-box/search-form-v', $atts);
    }

    /**
     *
     */
    public static function render_field_price() {

    }

    /**
     *
     */
    public static function render_field_area() {

    }
}

OpalEstate_Search::init();
