<?php
/**
 * OpalEstate_User
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

class OpalEstate_User {

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $current_user_id;

    /**
     * @var mixed|void
     */
    public $enable_extra_profile;

    /**
     * @var
     */
    public $roles;

    /**
     * @var
     */
    public $user_id;

    /**
     * @var
     */
    public $new_attachmenet_ids;

    /**
     * OpalEstate_User constructor.
     */
    public function __construct() {
        define("OPALESTATE_USER_PROFILE_PREFIX", 'opalestate_user_');

        $shortcodes = [
            'user_profile' => ['code' => 'user_profile', 'label' => esc_html__('User Profile', 'opalestate-pro')],
            'myaccount'    => ['code' => 'myaccount', 'label' => esc_html__('My Account', 'opalestate-pro')],
        ];

        foreach ($shortcodes as $shortcode) {
            add_shortcode('opalestate_' . $shortcode['code'], [$this, $shortcode['code']]);
        }
        $this->enable_extra_profile = opalestate_options('enable_extra_profile', 'on');

        add_action('init', [$this, 'process_frontend_submit'], 99999);
        add_action('cmb2_render_text_password', [$this, 'cmb2_render_text_password'], 10, 5);

        /**
         * Ajax action
         */
        add_action('wp_ajax_opalestate_save_changepass', [$this, 'save_change_password']);
        add_action('wp_ajax_nopriv_opalestate_save_changepass', [$this, 'save_change_password']);

        add_action('cmb2_after_init', [$this, 'process_submission'], 100000);

        /**
         * Check User  Block Submission
         */
        add_action('opalestate_submission_form_before', [$this, 'show_message'], 9);
        add_action('opalestate_before_process_ajax_upload_file', [$this, 'check_blocked']);
        add_action('opalestate_before_process_ajax_upload_user_avatar', [$this, 'check_blocked']);
        add_action('opalestate_profile_form_process_before', [$this, 'check_blocked']);
        add_action('opalestate_toggle_featured_property_before', [$this, 'check_blocked']);

        add_action('user_register', [$this, 'on_create_user'], 10, 1);
        add_action('profile_update', [$this, 'on_create_user'], 10, 1);
        add_action('opalestate_after_register_successfully', [$this, 'on_regiser_user'], 10, 1);

        add_action('init', [$this, 'disable'], 100000);
        add_action('init', [$this, 'init_user_management']);

        add_action('wp_enqueue_scripts', [$this, 'scripts_styles'], 99);

        add_filter('pre_get_posts', [$this, 'show_current_user_attachments']);
        add_action('init', [$this, 'block_users_backend']);
    }

    /**
     * Redirect contribute roles to front-end dashboard.
     */
    public function block_users_backend() {
        global $current_user;

        if (!is_a($current_user, 'WP_User')) {
            return;
        }

        if (is_admin() && !wp_doing_ajax() && (in_array('opalestate_agent', $current_user->roles) || in_array('opalestate_agency', $current_user->roles))) {
            $redirect = opalestate_get_user_management_page_uri();
            $redirect = $redirect ? $redirect : home_url();
            wp_redirect($redirect);
            exit;
        }
    }


    /**
     * FrontEnd Submission
     */
    public function show_current_user_attachments($wp_query_obj) {

        global $current_user, $pagenow;

        if (!is_a($current_user, 'WP_User')) {
            return;
        }

        if (!in_array($pagenow, ['upload.php', 'admin-ajax.php'])) {
            return;
        }

        if (!empty($current_user->roles)) {
            if (in_array('opalestate_agent', $current_user->roles) || in_array('opalestate_agency', $current_user->roles)) {
                $wp_query_obj->set('author', $current_user->ID);
            }
        }

        return;
    }


    public function scripts_styles() {
        if (isset($_GET['tab'])) {
            wp_register_style('opalesate-cmb2-front', OPALESTATE_PLUGIN_URL . 'assets/cmb2-front.css');
            wp_enqueue_style('opalesate-cmb2-front');
            wp_register_script(
                'opalestate-dashboard',
                OPALESTATE_PLUGIN_URL . 'assets/js/frontend/dashboard.js',
                [
                    'jquery',
                ],
                '1.0',
                true
            );
            wp_enqueue_script('opalestate-dashboard');
        }
    }

    public function disable() {
        if (!current_user_can('manage_options')) {
            // add_action( 'wp_before_admin_bar_render', [ $this, 'disable_profile_page' ] );
            // add_action( 'admin_init', [ $this, 'disable_profile_page' ] );
            add_filter('show_admin_bar', [$this, 'disable_admin_bar']);
        }
    }

    public function init_user_management() {
        add_action('opalestate_user_content_profile_page', [$this, 'user_profile']);
    }

    public function disable_admin_bar($show_admin_bar) {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $roles        = $current_user->roles;

            if (in_array('opalestate_agent', $roles) || in_array('opalestate_agency', $roles)) {
                return false;
            }

            if (in_array('subscriber', $roles)) {
                return false;
            }
        }

        return $show_admin_bar;
    }

    /**
     *
     */
    public function show_message_user_profile() {
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        $roles   = opalestate_user_roles_by_user_id($user_id);
        if ($roles):
            if (in_array('opalestate_agency', $roles)):
                $agency_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                if (!$agency_id) {
                    return;
                }
                $link = get_edit_post_link($agency_id);
                ?>
                <div id="message" class="updated fade">
                    <p><?php echo sprintf(__('This user has role <strong>Opal Estate Agency</strong> and click here to <a target="_blank" href="%s">update Agency profile</a>',
                            'opalestate-pro'), $link); ?></p>
                </div>
            <?php elseif (in_array('opalestate_agent', $roles)) :
                $agent_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                if (!$agent_id) {
                    return;
                }
                $link = get_edit_post_link($agent_id);
                ?>
                <div id="message" class="updated fade">
                    <p><?php echo sprintf(__('This user has role <strong>Opal Estate Agent</strong> and click here to <a target="_blank" href="%s">update Agent profile</a>',
                            'opalestate-pro'), $link); ?></p>
                </div>
            <?php endif; ?>
        <?php
        endif;
    }

    /**
     * On Register user.
     *
     * @param int $user_id User ID
     */
    public function on_regiser_user($user_id) {
        if (isset($_POST['role'])) {
            // Fetch the WP_User object of our user.
            $u = new WP_User($user_id);
            $u->remove_role('subscriber');

            // Replace the current role with 'editor' role
            $u->set_role(sanitize_text_field($_POST['role']));

            $roles = opalestate_user_roles_by_user_id($user_id);

            if ($roles && in_array($_POST['role'], $roles)) {
                $role = str_replace('opalestate_', '', sanitize_text_field($_POST['role']));

                do_action('opalestate_on_set_role_' . $role, $user_id);
            }
        }
    }

    /**
     *
     */
    public function on_create_user($user_id) {
        if (isset($_POST['role'])) {
            $roles = opalestate_user_roles_by_user_id($user_id);

            if ($roles && in_array($_POST['role'], $roles)) {
                $role = sanitize_text_field(str_replace('opalestate_', '', $_POST['role']));
                do_action('opalestate_on_set_role_' . $role, $user_id);
            }
        }
    }

    /**
     *
     */
    public function disable_profile_page() {

        // Remove AdminBar Link
        if (
            'wp_before_admin_bar_render' === current_filter()
            && !current_user_can('manage_options')
        ) {
            return $GLOBALS['wp_admin_bar']->remove_menu('edit-profile', 'user-actions');
        }

        // Remove (sub)menu items
        //  remove_menu_page( 'profile.php' );
        if (function_exists("remove_submenu_page")) {
            remove_submenu_page('users.php', 'profile.php');
        }
        // Deny access to the profile page and redirect upon try
        if (
            defined('IS_PROFILE_PAGE')
            && IS_PROFILE_PAGE
            && !current_user_can('manage_options')
        ) {
            // wp_redirect( admin_url() );
            exit;
        }
    }

    /**
     *
     */
    public function show_message() {
        if ($this->is_blocked()) {

            echo apply_filters('opalestate_user_block_submission_message',
                '<div class="alert alert-danger">' . __('Your account was blocked to use the submission form, so you could not submit any property.', 'opalestate-pro') . '</div>');
        }
    }

    /**
     *
     */
    public function check_blocked() {
        $check = $this->is_blocked();
        if ($check) {
            $std          = new stdClass();
            $std->status  = false;
            $std->message = esc_html__('Your account is blocked, you could not complete this action', 'opalestate-pro');
            $std->msg     = $std->message;
            echo json_encode($std);
            wp_die();
        }
    }

    /**
     *
     */
    public static function get_user_types() {

        return apply_filters('opalestate_usertypes', [
            'subscriber'        => esc_html__('Subscriber', 'opalestate-pro'),
            'opalestate_agent'  => esc_html__('Agent', 'opalestate-pro'),
            'opalestate_agency' => esc_html__('Agency', 'opalestate-pro'),
        ]);
    }

    /**
     *
     */
    public function process_submission() {

        global $current_user;
        // Verify Nonce
        $user_id = get_current_user_id();

        $key = 'nonce_CMB2phpopalestate_user_front';

        if (!isset($_POST[$key]) || empty($_POST[$key]) || !is_user_logged_in()) {
            return;
        }

        $this->process_upload_files(0);

        $prefix  = OPALESTATE_USER_PROFILE_PREFIX;
        $post_id = $user_id;

        $metaboxes = apply_filters('cmb2_meta_boxes', $this->front_edit_fields([]));
        cmb2_get_metabox_form($metaboxes[$prefix . 'front'], $post_id);
        $cmb = cmb2_get_metabox($prefix . 'front', $post_id);

        $sanitized_values = $cmb->get_sanitized_values($_POST);
        $cmb->save_fields($user_id, 'user', $sanitized_values);

        $posts = [
            'first_name',
            'last_name',
            'description',
        ];

        foreach ($posts as $post) {
            if (isset($_POST[$post])) {
                update_user_meta($current_user->ID, $post, esc_attr($_POST[$post]));
            }
        }

        if ($this->new_attachmenet_ids) {
            foreach ($this->new_attachmenet_ids as $_id) {
                delete_post_meta($_id, '_pending_to_use_', 1);
            }
        }

        $this->remove_dirty_images($user_id);

        return opalestate_output_msg_json(true,
            __('The data updated successful, please wait for redirecting', 'opalestate-pro'),
            [
                'heading'  => esc_html__('Update Information', 'opalestate-pro'),
                'redirect' => opalestate_get_user_management_page_uri(['tab' => 'profile']),
            ]
        );
    }

    /**
     * Remove dirty images of current user
     */
    public function remove_dirty_images($user_id) {

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
     *
     *
     */
    private function get_field_name($field) {
        return OPALESTATE_USER_PROFILE_PREFIX . $field;
    }

    /**
     * Process upload images for properties
     */
    public function upload_image($submitted_file, $parent_id = 0) {
        return opalesate_upload_image($submitted_file, $parent_id);
    }


    private function process_upload_files($post_id) {

        //upload images for featured and gallery images
        if (isset($_FILES) && !empty($_FILES)) {

            ///
            $fields = [
                $this->get_field_name('avatar_id'),
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
                                $_POST[$key]                                          = isset($_POST[$key]) ? $_POST[$key] : [];
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
                    //// / //
                }
            }
        }
    }

    /**
     *
     */
    public static function is_blocked() {

        global $current_user;
        // Verify Nonce
        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return true;
        }
        $blocked = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'block_submission', true);

        return $blocked;
    }

    /**
     *
     */
    public function get_avatar_url($user_id) {

        return get_avatar_url($user_id);
    }

    /**
     *
     */
    public static function get_author_picture($user_id) {
        $avatar = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'avatar', true);

        if (!$avatar) {
            $avatar = opalestate_get_image_avatar_placehold();
        }

        return $avatar;
    }

    /**
     *
     */
    public function shortcode_button() {

    }

    /**
     *
     */
    public function save_change_password() {
        global $current_user;

        $nonce = 'nonce_CMB2phpopalestate_user_frontchangepass';
        if (!isset($_POST[$nonce], $_POST['oldpassword'], $_POST['new_password'], $_POST['confirm_password']) || !wp_verify_nonce($_POST[$nonce], $nonce)) {
            return false;
        }

        do_action('opalestate_profile_form_process_before');
        $output          = new stdClass();
        $output->status  = false;
        $output->message = esc_html__('Found a problem while updating', 'opalestate-pro');

        wp_get_current_user();

        $userID = $current_user->ID;

        $oldpassword      = sanitize_text_field($_POST['oldpassword']);
        $new_password     = sanitize_text_field($_POST['new_password']);
        $confirm_password = sanitize_text_field($_POST['confirm_password']);

        if (empty($oldpassword) || empty($new_password) || empty($confirm_password)) {
            $output->message = esc_html__('Passwords fields are not empty', 'opalestate-pro');
            echo json_encode($output);
            exit;
        }

        if ($new_password != $confirm_password) {
            $output->message = esc_html__('New password is not same confirm password', 'opalestate-pro');
            echo json_encode($output);
            exit;
        }


        $user = get_user_by('id', $userID);
        if ($user && wp_check_password($oldpassword, $user->data->user_pass, $userID)) {
            wp_set_password($new_password, $userID);
            $output->status  = true;
            $output->message = esc_html__('Password Updated', 'opalestate-pro');
        } else {
            $output->message = esc_html__('Old password is not correct', 'opalestate-pro');
        }

        echo json_encode($output);
        die();
    }

    /**
     * Defines custom front end fields
     *
     * @access public
     * @param array $metaboxes
     * @return array
     */
    public function front_edit_fields(array $metaboxes) {
        $post_id = 0;
        $prefix  = OPALESTATE_USER_PROFILE_PREFIX;
        global $current_user;

        $default = [];

        $user_roles = $current_user->roles;
        $user_role  = array_shift($user_roles);

        $metabox = new Opalestate_User_MetaBox();

        ///
        if ($this->get_member_id()) {
            $fields = array_merge_recursive($default,
                $metabox->get_front_base_field($prefix)
            );
        } else {
            $fields = array_merge_recursive($default,
                $metabox->get_front_base_field($prefix),
                $metabox->get_job_fields($prefix),
                $metabox->get_base_front_fields($prefix),
                $metabox->get_address_fields($prefix)
            );
        }


        $metaboxes[$prefix . 'front'] = [
            'id'           => $prefix . 'front',
            'title'        => esc_html__('Name and Description', 'opalestate-pro'),
            'object_types' => ['opalestate_property'],
            'context'      => 'normal',
            'object_types' => ['user'], // Tells CMB2 to use user_meta vs post_meta
            'priority'     => 'high',
            'show_names'   => true,
            'cmb_styles'   => false,
            'fields'       => $fields,
        ];


        $metaboxes[$prefix . 'frontchangepass'] = [
            'id'           => $prefix . 'frontchangepass',
            'title'        => esc_html__('Name and Description', 'opalestate-pro'),
            'object_types' => ['opalestate_property'],
            'context'      => 'normal',
            'object_types' => ['user'], // Tells CMB2 to use user_meta vs post_meta
            'priority'     => 'high',
            'show_names'   => true,
            'fields'       => [
                [
                    'id'          => "oldpassword",
                    'name'        => esc_html__('Old Password', 'opalestate-pro'),
                    'type'        => 'text_password',
                    'attributes'  => [
                        'required' => 'required',
                    ],
                    'description' => esc_html__('Please enter your old password', 'opalestate-pro'),
                ],
                [
                    'id'          => "new_password",
                    'name'        => esc_html__('New Password', 'opalestate-pro'),
                    'type'        => 'text_password',
                    'attributes'  => [
                        'required' => 'required',
                    ],
                    'description' => esc_html__('Please enter your new password.', 'opalestate-pro'),
                ],
                [
                    'id'          => "confirm_password",
                    'name'        => esc_html__('Confirm Password', 'opalestate-pro'),
                    'type'        => 'text_password',
                    'attributes'  => [
                        'required' => 'required',
                    ],
                    'description' => esc_html__('Please enter your confirm password.', 'opalestate-pro'),
                ],
            ],
        ];


        return $metaboxes;
    }

    public function cmb2_render_text_password($field_args, $escaped_value, $object_id, $object_type, $field_type_object) {
        echo $field_type_object->input(['type' => 'password', 'class' => 'form-control']);
    }


    public function myaccount() {
        return opalestate_load_template_path('user/my-account');
    }

    /**
     * FrontEnd Submission
     */
    public function user_profile() {

        global $current_user;

        if (!is_user_logged_in()) {
            echo opalestate_load_template_path('parts/not-allowed');

            return;
        }

        $user_id = get_current_user_id();


        $metaboxes = apply_filters('cmb2_meta_boxes', $this->front_edit_fields([]));

        return opalestate_load_template_path('user/profile', ['metaboxes' => $metaboxes, 'user_id' => $user_id]);

    }

    public function process_frontend_submit() {

        if (opalestate_options('enable_extra_profile', 'on') != 'on') {
            return;
        }

        global $current_user;
    }

    /**
     *
     */
    private function update_data_agent_or_agency($prefix) {

        global $current_user;


        $post_id   = isset($_POST['object_id']) && absint($_POST['object_id']) ? absint($_POST['object_id']) : 0;
        $user_id   = get_current_user_id();
        $metaboxes = apply_filters('opalestate_before_render_profile_' . $_GET['tab'] . '_form', [], $post_id);
        $metaboxes = apply_filters('cmb2_meta_boxes', $metaboxes);

        if (isset($metaboxes[$prefix . 'front'])) {
            if (!empty($post_id)) {
                $old_post  = get_post($post_id);
                $post_date = $old_post->post_date;
            } else {
                $post_date = '';
            }

            $data = [
                'ID'           => $post_id,
                'post_title'   => $current_user->display_name,
                'post_author'  => $user_id,
                'post_status'  => 'publish',
                'post_type'    => 'opalestate_agent',
                'post_date'    => $post_date,
                'post_content' => wp_kses($_POST[$prefix . 'text'], '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span><p>'),
            ];
            unset($_POST[$prefix . 'text']);


            if ($post_id > 0) {
                $post_id = wp_update_post($data, true);
            } else {
                $post_id = wp_insert_post($data, true);
            }

            $post = get_post($post_id);

            if (empty($post->post_content) || empty($post->post_title) || !has_post_thumbnail($post_id)) {

                $data['post_status'] = 'pending';
                $post_id             = wp_update_post($data, true);
            }
            update_user_meta($user_id, $prefix . 'related_id', $post_id);
            cmb2_get_metabox_form($metaboxes[$prefix . 'front'], $post_id);

            return $post_id;
        }

        return false;
    }

    public static function get_member_id() {
        $user_id = get_current_user_id();

        return get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
    }
}

new OpalEstate_User();
