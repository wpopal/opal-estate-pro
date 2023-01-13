<?php
/**
 * Opalestate_Agent_Front
 *
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Opalestate_Agent_Front {

    /**
     * Instance.
     *
     * @access private
     * @var Opalestate_Agent_Front
     */
    static private $instance;

    /**
     * Singleton pattern.
     *
     * @since  $Id
     * @access private
     */
    private function __construct() {
    }

    public $new_attachmenet_ids;

    /**
     * Get instance.
     *
     * @access public
     * @return Opalestate_Agent_Front
     */
    public static function get_instance() {
        if (null === static::$instance) {
            self::$instance = new static();

            self::$instance->init();
        }

        return self::$instance;
    }

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function init() {
        add_action('opalestate_on_set_role_agent', [$this, 'on_set_role'], 1, 9);
        add_filter('opalestate_before_render_profile_agent_form', [$this, 'render_front_form'], 2, 2);
        add_filter('pre_get_posts', [$this, 'archives_query'], 1);
        add_action('cmb2_after_init', [$this, 'on_save_front_data']);

        add_action('opalestate_user_content_agent_profile_page', [$this, 'render_profile']);
        add_filter('opalestate_management_user_menu', [$this, 'render_extra_profile_link']);

        $this->register_shortcodes();
    }


    /**
     * Render extra profile link.
     *
     * @param $menu
     * @return mixed
     */
    public function render_extra_profile_link($menu) {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role  = array_shift($user_roles);
        if ('on' === opalestate_get_option('enable_dashboard_agent_profile', 'on') && $user_role == 'opalestate_agent') {
            $menu['extra_profile'] = [
                'icon'  => 'fa fa-user',
                'link'  => "agent_profile",
                'title' => esc_html__('Agent Profile', 'opalestate-pro'),
                'id'    => 0,
            ];
        }

        return $menu;
    }

    /**
     * render_profile
     */
    public function render_profile() {
        $user_id = get_current_user_id();
        $post_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);

        // Check if have not any relationship, create a new then update this meta value.
        if (!$post_id || $post_id < 0) {
            $this->on_set_role($user_id);
        }

        $post_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
        $post    = get_post($post_id);

        if (isset($post->ID) && ($post->post_status != 'publish' || $post->ID == get_the_ID())) {
            opalestate_add_notice('warning', esc_html__('You need to enter some required information to publish your account.', 'opalestate-pro'));
            add_action('opalestate_profile_agent_form_before', 'opalestate_print_notices');
        }

        $metaboxes = $this->render_front_form([], $post_id);

        return opalestate_load_template_path('user/agent/profile-agent', ['metaboxes' => $metaboxes, 'post_id' => $post_id]);
    }

    /**
     * Process upload images for properties
     */
    public function upload_image($submitted_file, $parent_id = 0) {
        return opalesate_upload_image($submitted_file, $parent_id);
    }

    /**
     * Remove dirty images of current user
     */
    public function remove_dirty_images($post_id, $user_id) {

        if (isset($_POST['remove_image_id']) && is_array($_POST['remove_image_id']) && $_POST['remove_image_id']) {
            foreach ($_POST['remove_image_id'] as $key => $value) {
                $post = get_post($value);
                if ($post->post_author == $user_id) {
                    wp_delete_attachment($value);
                }
            }
        }
    }

    /**
     * Process upload files.
     *
     * @param int $post_id Post ID.
     */
    private function process_upload_files($post_id) {
        // Upload images for featured and gallery images.
        if (isset($_FILES) && !empty($_FILES)) {
            $fields = [
                $this->get_field_name('gallery'),
                $this->get_field_name('avatar_id'),
                $this->get_field_name('featured_image'),
            ];

            foreach ($_FILES as $key => $value) {
                // Allow processing in fixed collection.
                if (in_array($key, $fields)) {
                    $ufile = $_FILES[$key];


                    if (isset($ufile['name']) && is_array($ufile['name'])) {
                        $output = [];

                        foreach ($ufile['name'] as $f_key => $f_value) {
                            $loop_file = [
                                'name'     => $ufile['name'][$f_key],
                                'type'     => $ufile['type'][$f_key],
                                'tmp_name' => $ufile['tmp_name'][$f_key],
                                'error'    => $ufile['error'][$f_key],
                                'size'     => $ufile['size'][$f_key],
                            ];
                            $new_atm   = $this->upload_image($loop_file, $post_id);
                            if ($new_atm) {
                                $_POST[$key]                                          = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : [];
                                $_POST[$key][$new_atm['attachment_id']]               = $new_atm['url'];
                                $this->new_attachmenet_ids[$new_atm['attachment_id']] = $new_atm['attachment_id'];
                            }
                        }

                    } ///
                    elseif (isset($ufile['name'])) {
                        $new_atm = $this->upload_image($ufile, $post_id);
                        if ($new_atm) {
                            $_POST[$key] = $new_atm['attachment_id'];

                            if (preg_match("#id#", $key)) {
                                $_key         = str_replace("_id", "", $key);
                                $_POST[$_key] = $new_atm['url'];
                            }

                            $this->new_attachmenet_ids[$new_atm['attachment_id']] = $new_atm['attachment_id'];
                        }
                    }
                }
            }
        }
    }

    /**
     * On save front data.
     *
     * @return false|mixed|string|void
     */
    public function on_save_front_data() {
        if (isset($_POST['nonce_CMB2php' . OPALESTATE_AGENT_PREFIX . 'front'])) {
            $post_id = $this->update_data_agent_or_agency(OPALESTATE_AGENT_PREFIX);

            if ($post_id) {
                OpalEstate_Agent::update_user_data(get_current_user_id());
            }

            return opalestate_output_msg_json(true,
                __('The data updated successful, please wait for redirecting', 'opalestate-pro'),
                [
                    'heading'  => esc_html__('Update Information', 'opalestate-pro'),
                    'redirect' => opalestate_get_user_management_page_uri(['tab' => 'agent_profile']),
                ]
            );


            return opalestate_output_msg_json(fales,
                __('Currently, The data could not save!', 'opalestate-pro'),
                ['heading' => esc_html__('Update Information', 'opalestate-pro')]
            );

            exit;
        }
    }

    /**
     * Get field name.
     */
    private function get_field_name($field) {
        return OPALESTATE_AGENT_PREFIX . $field;
    }

    /**
     * Update data for agent or agency.
     */
    private function update_data_agent_or_agency($prefix) {
        global $current_user;

        $post_id   = isset($_POST['object_id']) && absint($_POST['object_id']) ? $_POST['object_id'] : 0;
        $user_id   = get_current_user_id();
        $metaboxes = apply_filters('opalestate_before_render_profile_agent_form', [], $post_id);
        $metaboxes = apply_filters('cmb2_meta_boxes', $metaboxes);

        if (isset($metaboxes[$prefix . 'front'])) {
            if (!empty($post_id)) {
                $old_post  = get_post($post_id);
                $post_date = $old_post->post_date;
            } else {
                $post_date = '';
            }
            $post = get_post($post_id);

            $data = [
                'ID'           => $post->ID ? $post_id : null,
                'post_title'   => sanitize_text_field($_POST[$prefix . 'title']),
                'post_author'  => $user_id,
                'post_type'    => 'opalestate_agent',
                'post_date'    => $post_date,
                'post_content' => wp_kses($_POST[$prefix . 'text'], '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span><p>'),
            ];

            unset($_POST[$prefix . 'title']);
            unset($_POST[$prefix . 'text']);

            if ($data['ID'] > 0) {
                $data['post_status'] = 'publish';
                $post_id             = wp_update_post($data, true);
            } else {
                $data['post_status'] = 'pending';
                $post_id             = wp_insert_post($data, true);
            }

            $post = get_post($post_id);

            update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $post_id);

            /*
             * Processing upload files
             */
            $this->process_upload_files($post_id);

            cmb2_get_metabox_form($metaboxes[$prefix . 'front'], $post_id);
            $cmb              = cmb2_get_metabox($prefix . 'front', $post_id);
            $sanitized_values = $cmb->get_sanitized_values($_POST);
            $cmb->save_fields($post_id, 'post', $sanitized_values);
            /// update
            // Create featured image
            $featured_image = get_post_meta($post_id, $prefix . 'featured_image', true);

            if (!empty($_POST[$prefix . 'featured_image']) && isset($_POST[$prefix . 'featured_image'])) {

                set_post_thumbnail($post_id, $_POST[$prefix . 'featured_image']);
                unset($_POST[$prefix . 'featured_image']);
            } else {
                delete_post_thumbnail($post_id);
            }

            // remove dirty images
            $this->remove_dirty_images($post_id, $user_id);
            // set ready of attachment for use.
            if ($this->new_attachmenet_ids) {
                foreach ($this->new_attachmenet_ids as $_id) {
                    delete_post_meta($_id, '_pending_to_use_', 1);
                }
            }

            return $post_id;
        }

        return false;
    }

    public function register_shortcodes() {
        $this->shortcodes = [
            'change_agent_profile' => ['code' => 'change_agent_profile', 'label' => esc_html__('Agent Profile', 'opalestate-pro')],
            'search_agents'        => ['code' => 'search_agents', 'label' => esc_html__('Search Agents', 'opalestate-pro')],
            'agent_carousel'       => ['code' => 'agent_carousel', 'label' => esc_html__('Agent Carousel', 'opalestate-pro')],
        ];

        foreach ($this->shortcodes as $shortcode) {
            add_shortcode('opalestate_' . $shortcode['code'], [$this, $shortcode['code']]);
        }
    }

    public function agent_carousel($atts) {
        $atts   = is_array($atts) ? $atts : [];
        $layout = 'search-agency-form';

        $default = [
            'current_uri'  => null,
            'column'       => 4,
            'limit'        => 12,
            'paged'        => 1,
            'onlyfeatured' => 0,
            'form'         => $layout,
        ];

        $atts = array_merge($default, $atts);

        return opalestate_load_template_path('shortcodes/agent-carousel', $atts);
    }

    /**
     * Archive query.
     *
     * @param $query
     * @return mixed
     */
    public function archives_query($query) {
        if ($query->is_main_query() && is_post_type_archive('opalestate_agent')) {
            $args = [];

            $min              = opalestate_options('search_agent_min_price', 0);
            $max              = opalestate_options('search_agent_max_price', 10000000);
            $search_min_price = isset($_GET['min_price']) ? sanitize_text_field($_GET['min_price']) : '';
            $search_max_price = isset($_GET['max_price']) ? sanitize_text_field($_GET['max_price']) : '';

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

            if (isset($_GET['cat']) && $_GET['cat'] != -1) {
                $tax_query[]
                    = [
                    'taxonomy' => 'property_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['cat']),
                ];
            }
            if (isset($_GET['cities']) && $_GET['cities'] != -1) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_city',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['cities']),
                ];
            }

            if ($tax_query) {
                $args['tax_query'] = ['relation' => 'AND'];
                $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
            }

            $args['meta_query'] = ['relation' => 'AND'];

            if ($search_min_price != $min && is_numeric($search_min_price)) {
                array_push($args['meta_query'], [
                    'key'     => OPALESTATE_AGENT_PREFIX . 'target_min_price',
                    'value'   => $search_min_price,
                    'compare' => '>=',
                    'type'    => 'NUMERIC',
                ]);
            }
            if (is_numeric($search_max_price) && $search_max_price != $max) {
                array_push($args['meta_query'], [
                    'key'     => OPALESTATE_AGENT_PREFIX . 'target_max_price',
                    'value'   => $search_max_price,
                    'compare' => '<=',
                    'type'    => 'NUMERIC',
                ]);
            }

            ///// search by address and geo location ///
            if (isset($_GET['geo_long']) && isset($_GET['geo_lat'])) {

                $prefix = OPALESTATE_AGENT_PREFIX;
                if ($_GET['location_text'] && (empty($_GET['geo_long']) || empty($_GET['geo_lat']))) {
                    array_push($args['meta_query'], [
                        'key'      => $prefix . 'map_address',
                        'value'    => sanitize_text_field(trim($_GET['location_text'])),
                        'compare'  => 'LIKE',
                        'operator' => 'OR',
                    ]);

                } else {
                    $radius   = isset($_GET['geo_radius']) ? sanitize_text_field($_GET['geo_radius']) : 5;
                    $post_ids = Opalestate_Query::filter_by_location(
                        sanitize_text_field($_GET['geo_lat']),
                        sanitize_text_field($_GET['geo_long']), $radius, 'km', $prefix);


                    $args['post__in'] = $post_ids;
                    $query->set('post__in', $post_ids);
                }
            }

            if (isset($args['tax_query']) && $args['tax_query']) {
                $query->set('tax_query', $args['tax_query']);
            }
            if (isset($args['meta_query']) && $args['meta_query']) {
                $query->set('meta_query', $args['meta_query']);
            }
        }

        return $query;
    }

    /**
     * Search agents.
     *
     * @return string
     */
    public function search_agents() {
        return opalestate_load_template_path('shortcodes/search-agents');
    }

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function on_set_role($user_id) {
        if ($user_id) {

            $args = [
                'post_type'      => 'opalestate_agent',
                'posts_per_page' => 10,
            ];

            $args['meta_key']     = OPALESTATE_AGENT_PREFIX . 'user_id';
            $args['meta_value']   = $user_id;
            $args['meta_compare'] = '=';
            $args['post_status']  = ['publish', 'pending'];

            $post = get_posts($args);

            if (empty($post)) {
                $agent_id = $this->create_agent([], $user_id);
                update_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . 'user_id', $user_id);
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $agent_id);
            }
        }
    }

    /**
     * Create agent.
     *
     * @param array $args
     * @param       $user_id
     * @return int|\WP_Error
     */
    public function create_agent($args, $user_id) {
        $data = get_user_by('id', $user_id);

        $post_title = sprintf(esc_html__('User ID: %s', 'opalestate-pro'), $user_id);

        $args = wp_parse_args($args, [
            'first_name'  => $data->first_name,
            'last_name'   => $data->last_name,
            'post_author' => $user_id,
            'avatar'      => '',
            'job'         => '',
            'email'       => '',
            'phone'       => '',
            'mobile'      => '',
            'fax'         => '',
            'web'         => '',
            'address'     => '',
            'twitter'     => '',
            'facebook'    => '',
            'google'      => '',
            'linkedin'    => '',
            'instagram'   => '',
        ]);

        if ($args['first_name'] && $args['last_name']) {
            $post_title = $args['first_name'] . ' ' . $args['last_name'];
        } elseif (isset($data->display_name) && $data->display_name) {
            $post_title = esc_html($data->display_name);
        }

        $agent_id = wp_insert_post([
            'post_title'   => $post_title,
            'post_content' => '',
            'post_excerpt' => '',
            'post_type'    => 'opalestate_agent',
            'post_status'  => 'pending',
            'post_author'  => $user_id,
        ], true);

        do_action('opalesate_insert_user_agent', $agent_id);

        return $agent_id;
    }

    /**
     * Render front form.
     *
     * @param     $metaboxes
     * @param int $post_id
     * @return mixed
     */
    public function render_front_form($metaboxes, $post_id = 0) {
        $metabox = new Opalestate_Agent_MetaBox();

        return $metabox->render_front_form($metaboxes, $post_id);
    }
}

Opalestate_Agent_Front::get_instance();
