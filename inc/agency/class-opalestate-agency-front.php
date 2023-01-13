<?php
/**
 * Opalestate_Agency_Front
 *
 * @author     Opal  Team <info@wpopal.com >
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Opalestate_Agency_Front {

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

    /**
     * Get instance.
     *
     * @return Opalestate_Agent_Front
     * @since  $Id
     * @access public
     */
    public static function get_instance() {
        if (null === static::$instance) {
            self::$instance = new static();

            self::$instance->init();
        }

        return self::$instance;
    }

    public $new_attachmenet_ids;

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function init() {
        add_action('opalestate_on_set_role_agency', [$this, 'on_set_role'], 1, 9);
        add_filter('opalestate_before_render_profile_agency_form', [$this, 'render_front_form'], 2, 2);

        add_action('save_post', [$this, 'on_save_post'], 13, 2);
        add_filter('pre_get_posts', [$this, 'archives_query'], 1);
        add_action('cmb2_after_init', [$this, 'on_save_front_data']);

        add_filter('opalestate_management_user_menu', [$this, 'render_extra_profile_link']);

        add_action('opalestate_user_content_agency_profile_page', [$this, 'render_profile']);
        add_action('opalestate_user_content_agency_team_page', [$this, 'render_team']);
        add_action('opalestate_user_init', [$this, 'process_action_member']);

        $this->register_shortcodes();
    }

    /**
     *
     */
    public function render_extra_profile_link($menu) {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role  = array_shift($user_roles);

        if ('on' === opalestate_get_option('enable_dashboard_agency_profile', 'on') && $user_role == 'opalestate_agency') {
            $menu['agency_profile'] = [
                'icon'  => 'fa fa-user',
                'link'  => "agency_profile",
                'title' => esc_html__('Agency Profile', 'opalestate-pro'),
                'id'    => 0,
            ];
            $menu['agency_team']    = [
                'icon'  => 'fa fa-users',
                'link'  => "agency_team",
                'title' => esc_html__('Agency Team', 'opalestate-pro'),
                'id'    => 0,
            ];
        }

        return $menu;
    }

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function archives_query($query) {
        if ($query->is_main_query() && is_post_type_archive('opalestate_agency')) {
            $tax_query = [];
            if (isset($_GET['location']) && $_GET['location'] != -1) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_location',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['location']),
                ];
            }
            if (isset($_GET['country']) && $_GET['country'] != -1) {
                $tax_query[] = [
                    'taxonomy' => 'opalestate_location',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['country']),
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
            if (isset($args['tax_query']) && $args['tax_query']) {
                $query->set('tax_query', $args['tax_query']);
            }
            if (isset($_GET['search_text'])) {
                $query->set('s', sanitize_text_field($_GET['search_text']));
            }
        }

        return $query;
    }

    /**
     *
     */
    private function update_data_agent_or_agency($prefix) {
        global $current_user;

        $post_id   = isset($_POST['object_id']) && absint($_POST['object_id']) ? $_POST['object_id'] : 0;
        $user_id   = get_current_user_id();
        $metaboxes = apply_filters('opalestate_before_render_profile_agency_form', [], $post_id);
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
                'post_type'    => 'opalestate_agency',
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

            if (empty($post->post_content) || empty($post->post_title) || !has_post_thumbnail($post_id)) {

                //	$data['post_status'] = 'public';
                //	$data['ID'] = $post_id;
                //	$post_id = wp_update_post( $data , true );
            }
            update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $post_id);

            /*
             * Processing upload files
             */
            $this->process_upload_files($post_id, $_POST);


            cmb2_get_metabox_form($metaboxes[$prefix . 'front'], $post_id);
            $cmb              = cmb2_get_metabox($prefix . 'front', $post_id);
            $sanitized_values = $cmb->get_sanitized_values($_POST);
            $cmb->save_fields($post_id, 'post', $sanitized_values);
            /// update
            // Create featured image
            $featured_image = get_post_meta($post_id, $prefix . 'featured_image', true);

            if (!empty($_POST[$prefix . 'featured_image']) && isset($_POST[$prefix . 'featured_image'])) {
                set_post_thumbnail($post_id, sanitize_text_field($_POST[$prefix . 'featured_image']));
                unset($_POST[$prefix . 'featured_image']);
            } else {
                delete_post_thumbnail($post_id);
            }

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

    /**
     *
     *
     */
    private function get_field_name($field) {
        return OPALESTATE_AGENCY_PREFIX . $field;
    }

    private function process_upload_files($post_id) {

        //upload images for featured and gallery images
        if (isset($_FILES) && !empty($_FILES)) {

            ///
            $fields = [
                $this->get_field_name('avatar_id'),
                $this->get_field_name('gallery'),
                $this->get_field_name('featured_image'),
            ];

            foreach ($_FILES as $key => $value) {
                // allow processing in fixed collection
                if (in_array($key, $fields)) {
                    $ufile = $_FILES[$key];

                    /// /////
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
     * Process upload images for properties
     */
    public function upload_image($submitted_file, $parent_id = 0) {
        return opalesate_upload_image($submitted_file, $parent_id);
    }

    public function on_save_front_data() {
        if (isset($_POST['nonce_CMB2php' . OPALESTATE_AGENCY_PREFIX . 'front'])) {
            $post_id = isset($_POST['object_id']) && $_POST['object_id'] ? absint($_POST['object_id']) : 0;
            $post    = get_post($post_id);

            $post_id = $this->update_data_agent_or_agency(OPALESTATE_AGENCY_PREFIX);

            if ($post_id) {
                OpalEstate_Agency::update_user_data(get_current_user_id());
            }

            return opalestate_output_msg_json(true,
                __('The data updated successful, please wait for redirecting', 'opalestate-pro'),
                [
                    'heading'  => esc_html__('Update Information', 'opalestate-pro'),
                    'redirect' => opalestate_get_user_management_page_uri(['tab' => 'agency_profile']),
                ]
            );

            return opalestate_output_msg_json(false,
                __('Currently, The data could not save!', 'opalestate-pro'),
                ['heading' => esc_html__('Update Information', 'opalestate-pro')]
            );
        }
    }

    /**
     *
     */
    public function register_shortcodes() {
        $this->shortcodes = [
            'search_agencies' => ['code' => 'search_agencies', 'label' => esc_html__('Search Agencies', 'opalestate-pro')],
            'agency_carousel' => ['code' => 'agency_carousel', 'label' => esc_html__('Agency Carousel', 'opalestate-pro')],
        ];

        foreach ($this->shortcodes as $shortcode) {
            add_shortcode('opalestate_' . $shortcode['code'], [$this, $shortcode['code']]);
        }
    }

    public function agency_carousel($atts) {

        $atts = is_array($atts) ? $atts : [];

        $default = [
            'current_uri'  => null,
            'column'       => 3,
            'limit'        => 12,
            'paged'        => 1,
            'onlyfeatured' => 0,
        ];

        $atts = array_merge($default, $atts);

        return opalestate_load_template_path('shortcodes/agency-carousel', $atts);
    }

    /**
     *
     */
    public function search_agencies($atts = []) {

        $atts   = is_array($atts) ? $atts : [];
        $layout = 'search-agency-form';

        $default = [
            'current_uri' => null,
            'form'        => $layout,
        ];

        $atts = array_merge($default, $atts);

        return opalestate_load_template_path('shortcodes/search-agencies', $atts);
    }

    /**
     *
     */
    public function render_profile() {
        $user_id = get_current_user_id();
        $post_id = OpalEstate_User::get_member_id();

        // Check if have not any relationship, create a new then update this meta value.
        if (!$post_id || $post_id < 0) {
            static::on_set_role($user_id);
        }

        $post_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
        $post    = get_post($post_id);

        if (isset($post->ID) && ($post->post_status != 'publish' || $post->ID == get_the_ID())) {
            opalestate_add_notice('warning', esc_html__('You need to enter some required information to publish your account.', 'opalestate-pro'));
            add_action('opalestate_profile_agency_form_before', 'opalestate_print_notices');
        }

        $metaboxes = $this->render_front_form([], $post_id);

        return opalestate_load_template_path('user/agency/profile-agency', ['metaboxes' => $metaboxes, 'post_id' => $post_id]);
    }

    /**
     *
     */
    public function render_team() {
        $user_id = get_current_user_id();
        $post_id = OpalEstate_User::get_member_id();

        $metaboxes = $this->render_front_form([], $post_id);

        return opalestate_load_template_path('user/agency/agency-team', ['metaboxes' => $metaboxes, 'post_id' => $post_id]);
    }

    /**
     *
     */
    public function process_action_member() {
        if (isset($_POST['add_team_action']) && wp_verify_nonce($_POST['add_team_action'], 'agency-add-member')) {

            if (isset($_POST['user_id'])) {

                $user_id = get_current_user_id();
                $post_id = OpalEstate_User::get_member_id();

                $team = get_post_meta($post_id, OPALESTATE_AGENCY_PREFIX . 'team', true);

                if (empty($team)) {
                    $team = [];
                }
                $team[] = intval($_POST['user_id']);
                $team   = array_unique($team);

                update_post_meta($post_id, OPALESTATE_AGENCY_PREFIX . 'team', $team);

            }
        }

        if (isset($_GET['tab']) && $_GET['tab'] == "agency_team" && isset($_GET['remove_id']) && $_GET['remove_id']) {

            $remove_id = intval($_GET['remove_id']);

            $user_id = get_current_user_id();
            $post_id = OpalEstate_User::get_member_id();

            $team = get_post_meta($post_id, OPALESTATE_AGENCY_PREFIX . 'team', true);

            if (empty($team)) {
                $team = [];
            }
            $team[] = $user_id;
            $team   = array_unique($team);
            foreach ($team as $key => $id) {
                if ($id == $remove_id) {
                    unset($team[$key]);
                }
            }
            update_post_meta($post_id, OPALESTATE_AGENCY_PREFIX . 'team', $team);

            wp_redirect(opalestate_get_user_management_page_uri(['tab' => 'agency_team']));
            die;
        }

    }

    /**
     *
     */
    public static function on_save_post($post_id) {
        $post_type = get_post_type($post_id);
        if ($post_type == 'opalestate_agency') {
            if (isset($_POST[OPALESTATE_AGENCY_PREFIX . 'user_id']) && $_POST[OPALESTATE_AGENCY_PREFIX . 'user_id']) {
                update_user_meta($_POST[OPALESTATE_AGENCY_PREFIX . 'user_id'], OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $post_id);
            }
        }
    }

    public static function on_set_role($user_id) {
        if ($user_id) {
            $args = [
                'post_type'      => 'opalestate_agency',
                'posts_per_page' => 10,
            ];

            $args['meta_key']     = OPALESTATE_AGENCY_PREFIX . 'user_id';
            $args['meta_value']   = $user_id;
            $args['meta_compare'] = '=';
            $args['post_status']  = ['publish', 'pending'];

            $post = get_posts($args);

            if (empty($post)) {

                $agency_id = self::create_agency([], $user_id);
                update_post_meta($agency_id, OPALESTATE_AGENCY_PREFIX . 'user_id', $user_id);
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $agency_id);
            }
        }
    }

    /**
     *
     */
    public static function create_agency($args, $user_id) {
        $data = get_user_by('id', $user_id);

        $post_title = sprintf(esc_html__('User ID: %s', 'opalestate-pro'), $user_id);

        $args = wp_parse_args($args, [
            'first_name' => $data->first_name,
            'last_name'  => $data->last_name,
            'avatar'     => '',
            'job'        => '',
            'email'      => '',
            'phone'      => '',
            'mobile'     => '',
            'fax'        => '',
            'web'        => '',
            'address'    => '',
            'twitter'    => '',
            'facebook'   => '',
            'google'     => '',
            'linkedin'   => '',
            'instagram'  => '',
        ]);

        if ($args['first_name'] && $args['last_name']) {
            $post_title = $args['first_name'] . ' ' . $args['last_name'];
        } elseif (isset($data->display_name) && $data->display_name) {
            $post_title = esc_html($data->display_name);
        }

        $agency_id = wp_insert_post([
            'post_title'   => $post_title,
            'post_content' => 'empty description',
            'post_excerpt' => 'empty excerpt',
            'post_type'    => 'opalestate_agency',
            'post_status'  => 'pending',
            'post_author'  => $user_id,
        ], true);

        do_action('opalesate_insert_user_agency', $agency_id);

        return $agency_id;
    }

    /**
     *
     */
    public function render_front_form($metaboxes, $post_id = 0) {
        $metabox = new Opalestate_Agency_MetaBox();

        return $metabox->render_front_form($metaboxes, $post_id);
    }
}

Opalestate_Agency_Front::get_instance();
