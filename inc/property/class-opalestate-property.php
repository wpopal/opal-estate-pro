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

/**
 * @class   Opalestate_Property
 *
 * @version 1.0
 */
class Opalestate_Property {

    /**
     * @var Integer $post_id
     *
     * @access protected
     */
    public $post_id;

    /**
     * @var array $metabox_info
     *
     * @access protected
     */
    protected $metabox_info;

    /**
     * @var float $price
     *
     * @access protected
     */
    protected $price;

    /**
     * @var float $saleprice
     *
     * @access protected
     */
    protected $saleprice;

    /**
     * @var String $map
     *
     * @access protected
     */
    protected $map;

    /**
     * @var Integer $address
     *
     * @access protected
     */
    public $address;

    /**
     * @var String $sku
     *
     * @access protected
     */
    public $sku;

    /**
     * @var String $latitude
     *
     * @access protected
     */
    public $latitude;

    /**
     * @var String $longitude
     *
     * @access protected
     */
    public $longitude;

    /**
     * @var Integer $featured 1 or 0
     *
     * @access protected
     */
    public $featured;

    /**
     * @var array Property page settings $property_settings
     *
     * @access public
     */
    public $property_settings;

    /**
     * Constructor
     */
    public function __construct($post_id = null) {
        $this->post_id           = $post_id;
        $this->map               = $this->get_metabox_value('map');
        $this->address           = $this->get_metabox_value('address');
        $this->price             = $this->get_metabox_value('price');
        $this->saleprice         = $this->get_metabox_value('saleprice');
        $this->before_pricelabel = $this->get_metabox_value('before_pricelabel');
        $this->pricelabel        = $this->get_metabox_value('pricelabel');
        $this->featured          = $this->get_metabox_value('featured');
        $this->sku               = $this->get_metabox_value('sku');

        $this->latitude  = isset($this->map['latitude']) ? $this->map['latitude'] : '';
        $this->longitude = isset($this->map['longitude']) ? $this->map['longitude'] : '';
    }

    /**
     * Get A Instance Of Opalestate_Property
     */
    public static function get_instance($post_id) {

        static $_instance;

        if (!$_instance) {
            $_instance = new Opalestate_Property($post_id);
        }

        return $_instance;
    }

    public function get_block_setting($key) {
        if (!$this->property_settings) {
            $key_settings = [
                'amenities',
                'attachments',
                'facilities',
                'video',
                'virtual_tour',
                'map',
                'nearby',
                'walkscores',
                'apartments',
                'floor_plans',
                'views_statistics',
                'author_box',
                'enquire_form',
                'mortgage',
            ];

            foreach ($key_settings as $key_setting) {
                $this->property_settings[$key_setting] = opalestate_get_option('enable_single_' . $key_setting, 'on');
            }
        }

        return isset($this->property_settings[$key]) ? $this->property_settings[$key] : null;
    }

    /**
     * Gets Amenities
     *
     * @access public
     * @param string $all
     * @return array
     */
    public function get_meta_fullinfo() {
        if (empty($this->metabox_info)) {
            $fields = Opalestate_Property_MetaBox::metaboxes_info_fields();

            foreach ($fields as $a => $field) {
                $id = str_replace(OPALESTATE_PROPERTY_PREFIX, '', $field['id']);

                if ($field['type'] == 'multicheck' || $field['type'] == 'select') {
                    $opt_values = (array)get_post_meta($this->post_id, $field['id']);
                    if (!empty($opt_values) && isset($field['options'])) {
                        $tmp = [];
                        foreach ($opt_values as $key => $val) {
                            if (isset($field['options'][$val])) {
                                $tmp[$val] = $field['options'][$val];
                            }
                        }
                        $opt_values = $tmp;
                    }
                    $value = implode(', ', $opt_values);
                } else {
                    $value = get_post_meta($this->post_id, $field['id'], true);
                }

                $value = isset($field['unit']) && $field['unit'] ? $value . ' ' . $field['unit'] : $value;

                $this->metabox_info[$id] = [
                    'label' => $field['name'],
                    'value' => $value,
                    'icon'  => opalestate_get_property_meta_icon($id),
                ];
            }
        }

        return apply_filters('opalestate_property_metabox_info', $this->metabox_info);
    }

    public function get_id() {
        return $this->post_id;
    }

    /**
     * Is featured?
     */
    public function is_featured() {
        return ('on' === $this->featured) || (1 == $this->featured);
    }

    /**
     *
     */
    public function get_meta_search_objects() {
        $prop     = new stdClass();
        $map      = $this->get_metabox_value('map');
        $image_id = get_post_thumbnail_id($this->post_id);
        if ($image_id) {
            $url = wp_get_attachment_url($image_id, opalestate_options('loop_image_size', 'large'), true);
        } else {
            $url = opalestate_get_image_placeholder(apply_filters('opalestate_loop_property_thumbnail', 'large'), true);
        }


        $prop->id    = $this->post_id;
        $prop->title = get_the_title();
        $prop->url   = get_permalink($this->post_id);

        $prop->lat     = $map['latitude'];
        $prop->lng     = $map['longitude'];
        $prop->address = $this->address;

        $prop->pricehtml  = opalestate_price_format($this->get_price());
        $prop->pricelabel = $this->get_price_label();
        $prop->thumb      = $url;

        if (file_exists(get_template_directory() . '/images/map/market_icon.png')) {
            $prop->icon = get_template_directory_uri() . '/images/map/market_icon.png';
        } else {
            $prop->icon = OPALESTATE_PLUGIN_URL . '/assets/map/market_icon.png';
        }

        $prop->icon = apply_filters('opalestate_prop_icon', $prop->icon);

        $prop->featured = $this->featured;

        $metas = Opalestate_Property_MetaBox::metaboxes_info_fields();

        foreach ($metas as $key => $field) {
            $id        = str_replace(OPALESTATE_PROPERTY_PREFIX, "", $field['id']);
            $prop->$id = get_post_meta($this->post_id, $field['id'], true);
        }
        $metas = $this->get_meta_shortinfo();

        $prop->metas  = $metas;
        $prop->status = $this->render_statuses();
        $terms        = wp_get_post_terms($this->post_id, 'opalestate_types');
        if ($terms) {
            $term = reset($terms);
            $icon = get_term_meta($term->term_id, 'opalestate_type_iconmarker', true);
            if ($icon) {
                $prop->icon = $icon;
            }
        }

        return $prop;
    }

    /**
     * Gets Amenities
     *
     * @access public
     * @param string $all
     * @return array
     */
    public function get_meta_shortinfo() {
        $output = [];

        $meta = opalestate_options('show_property_meta');
        $meta = apply_filters('opalestate_property_meta_shortinfo_fields', $meta);

        if (!empty($meta)) {
            $fields = $this->get_meta_fullinfo();
            foreach ($meta as $key => $value) {
                if (isset($fields[$value])) {
                    $output[$value] = $fields[$value];
                }
            }
        }

        return $output;
    }

    /**
     * Gets Amenities
     *
     * @access public
     * @param string $all
     * @return array
     */
    public function get_amenities($all = true) {

        if ($all) {
            $terms = Opalestate_Query::get_amenities();
        } else {
            $terms = wp_get_post_terms($this->post_id, 'opalestate_amenities');
        }

        return $terms;
    }

    /**
     * Get location.
     */
    public function get_locations() {
        $terms = wp_get_post_terms($this->post_id, 'opalestate_location');

        if ($terms) {
            return $terms;
        }

        return [];
    }

    /**
     * Gets locations
     *
     * @access public
     * @return array
     */
    public function render_locations() {
        $terms = wp_get_post_terms($this->post_id, 'opalestate_location');
        if ($terms) {
            $output = '<span class="property-locations">';
            foreach ($terms as $key => $term) {
                $output .= '<a href="' . get_term_link($term->term_id) . '" class="location-name">' . $term->name . '</a>';
                if ($key < (count($terms) - 1)) {
                    $output .= ", ";
                }
            }
            $output .= '</span>';
            echo $output;
        }
    }

    /**
     * Gets labels
     *
     * @access public
     * @return array
     */
    public function get_labels() {
        return wp_get_post_terms($this->post_id, 'opalestate_label');
    }

    /**
     * Render labels.
     *
     * @access public
     * @return string
     */
    public function render_labels() {
        $labels = $this->get_labels();

        if (empty($labels)) {
            return;
        }

        $output = '<ul class="property-labels">';
        foreach ($labels as $key => $value) {
            $output .= '<li class="property-labels-item property-label-' . trim($value->slug) . '"><span class="label-status label">' . esc_html($value->name) . '</span></li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * Gets statuses
     *
     * @access public
     * @return array
     */
    public function get_status() {
        $terms = wp_get_post_terms($this->post_id, 'opalestate_status');

        return $terms;
    }

    /**
     * Render statuses.
     *
     * @access public
     * @return string
     */
    public function render_statuses() {
        $statuses = $this->get_status();

        if (empty($statuses)) {
            return;
        }

        $output = '<ul class="property-status">';
        foreach ($statuses as $key => $value) {
            $output .= '<li class="property-status-item property-status-' . esc_attr($value->slug) . '"><span class="label-status label">' . esc_html($value->name) . '</span></li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     *
     */
    public function getAuthor() {

    }


    public function get_author_type() {
        return $this->get_metabox_value('author_type');
    }

    /**
     *
     */
    public function render_author_link() {
        $data = $this->get_author_link_data();
        if (!$data) {
            return;
        }

        $avatar = $data['avatar'] ? $data['avatar'] : opalestate_get_image_avatar_placehold();
        $avatar = '<img class="avatar" src="' . esc_url($avatar) . '" alt="' . $data['name'] . '" />';

        return '<a  href="' . $data['link'] . '" aria-label="' . $data['name'] . '" class="author-link"><span aria-label="' . $data['name'] . '" class="author-avatar hint--top">' . $avatar . '</span><span class="author-name">' . $data['name'] . '</span></a>';
    }

    public function get_author_link_data() {
        $data = [];
        switch ($this->get_author_type()) {
            case 'hide':
                $data = [];
                break;

            case 'agent':
                $agent_id = $this->get_metabox_value('agent');
                $data     = OpalEstate_Agent::get_link($agent_id);
                break;

            case 'agency':
                $agency_id = $this->get_metabox_value('agency');
                $data      = OpalEstate_Agency::get_link($agency_id);
                break;
            default:
                $data = $this->get_author_link();
                break;
        }

        return $data;
    }

    public function get_author_link() {
        $user_id    = get_post_field('post_author', $this->get_id());
        $image_id   = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'avatar_id', true);
        $related_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);

        if ($image_id) {
            $url = wp_get_attachment_image_url($image_id, 'thumbnail');
        } else {
            $url = get_avatar_url(get_the_author_meta('email', $user_id));
        }

        if ($related_id) {
            $authorlink = get_permalink($related_id);
            $author     = get_the_title($related_id);
        } else {
            $authorlink = get_author_posts_url($user_id);
            $author     = get_the_author();
        }

        return [
            'name'   => $author,
            'avatar' => $url,
            'link'   => $authorlink,
        ];
    }

    /**
     * Gets status
     *
     * @access public
     * @return array
     */
    public function get_category_tax() {
        $terms = wp_get_post_terms($this->post_id, 'property_category');

        return $terms;
    }

    public function get_types_tax() {
        $terms = wp_get_post_terms($this->post_id, 'opalestate_types');

        return $terms;
    }

    /**
     * Gets meta box value
     *
     * @access public
     * @param $key
     * @param $single
     * @return string|array
     */
    public function get_metabox_value($key, $single = true) {
        return get_post_meta($this->post_id, OPALESTATE_PROPERTY_PREFIX . $key, $single);
    }

    /**
     * Gets map value
     *
     * @access public
     * @return string
     */
    public function get_map() {
        return $this->map;
    }

    /**
     * Gets address value
     *
     * @access public
     * @return string
     */
    public function get_address() {
        return $this->address;
    }

    /**
     * Gets sku value
     *
     * @access public
     * @return string
     */
    public function get_sku() {
        return $this->sku;
    }

    /**
     * Gets video url value
     *
     * @access public
     * @return string
     */

    public function get_video_url() {
        return $this->get_metabox_value('video');
    }

    /**
     * Gets 360 virtual tour value
     *
     * @access public
     * @return string
     */
    public function get_virtual_tour() {
        return $this->get_metabox_value('virtual');
    }

    /**
     * Gets gallery ids value
     *
     * @access public
     * @return array
     */
    public function get_gallery() {
        return $this->get_metabox_value('gallery');
    }

    /**
     * Count gallery images.
     *
     * @return int
     */
    public function get_gallery_count() {
        $count = $this->get_gallery();

        return is_array($count) && $count ? count($count) : 0;
    }

    /**
     * Gets price value
     *
     * @access public
     * @return string
     */
    public function get_price() {
        return $this->price;
    }

    /**
     * Gets sale price value
     *
     * @access public
     * @return string
     */
    public function get_sale_price() {
        return $this->saleprice;
    }

    /**
     * Gets price value
     *
     * @access public
     * @return string
     */
    public function get_before_price_label() {
        return $this->before_pricelabel;
    }

    /**
     * Gets price value
     *
     * @access public
     * @return string
     */
    public function get_price_label() {
        return $this->pricelabel;
    }

    /**
     * Gets price format value
     *
     * @access public
     * @return string
     */
    public function get_format_price() {
        return $this->get_metabox_value('formatprice');
    }

    public function enable_google_mapview() {
        return $this->get_metabox_value('enablemapview');
    }

    public function get_google_map_link() {
        $url = 'https://maps.google.com/maps?q=' . $this->address . '&ll=' . $this->latitude . ',' . $this->longitude . '&z=17';

        return $url;
    }

    public static function is_allowed_remove($user_id, $item_id) {
        $item = get_post($item_id);

        if (!empty($item->post_author)) {
            if ($item->post_author == $user_id) {
                return true;
            }
        }

        return false;
    }

    public function get_price_oncall() {
        return $this->get_metabox_value('price_oncall');
    }

    public function get_facilities() {
        return $this->get_metabox_value('public_facilities_group');
    }

    public function get_attachments() {
        return $this->get_metabox_value('attachments');
    }

    public function get_content_single_layout() {
        return $this->get_metabox_value('layout');
    }

    public function get_preview_template() {
        return $this->get_metabox_value('preview');
    }

    /**
     * Get rating count.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_rating_counts() {
        return $this->get_metabox_value('rating_count') ? $this->get_metabox_value('rating_count') : 0;
    }

    /**
     * Get average rating.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return float
     */
    public function get_average_rating() {
        return $this->get_metabox_value('average_rating') ? $this->get_metabox_value('average_rating') : 0;
    }

    /**
     * Get review count.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_review_count() {
        return $this->get_metabox_value('review_count') ? $this->get_metabox_value('review_count') : 0;
    }

    public function get_rating_count_stats() {
        return $this->get_metabox_value('rating_count_stats') ? $this->get_metabox_value('rating_count_stats') : [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
    }

    public function get_rating_average_stats() {
        return $this->get_metabox_value('rating_average_stats');
    }

    public function get_apartments() {
        return $this->get_metabox_value('apartments');
    }

    public function get_floor_plans() {
        return $this->get_metabox_value('public_floor_group');
    }

    public function get_posted() {
        return human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . esc_html__('ago', 'opalestate-pro');
    }

    public function get_expiry_date() {
        $expired_activated = get_post_meta($this->get_id(), OPALESTATE_PROPERTY_PREFIX . 'expired_activated', true);
        $expired_time      = get_post_meta($this->get_id(), OPALESTATE_PROPERTY_PREFIX . 'expired_time', true);

        return ($expired_activated && $expired_time) ? $expired_time : '';
    }
}
