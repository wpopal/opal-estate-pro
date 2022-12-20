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
if (!session_id()) {
    @session_start();
}

/**
 * @class   OpalEstate_Submission
 *
 * @version 1.0
 */
class OpalEstate_Submission {

    /**
     *
     *
     */
    public $metabox;

    /**
     *
     *
     */
    public $new_attachmenet_ids = [];

    /**
     * Constructor
     */
    public function __construct() {

        /**
         * Can not use $this->is_submission_page() || use 'wp_enqueue_scripts' here
         * because inside this hook global $post == null
         */
        add_action('wp_head', [$this, 'head_check_page']);

        add_action('cmb2_after_init', [$this, 'process_submission'], 10000);

        add_action('opalestate_single_property_before', [$this, 'render_button_edit']);

        if (is_admin()) {
            add_filter('opalestate_settings_tabs', [$this, 'setting_content_tab']);
            add_filter('opalestate_registered_submission_page_settings', [$this, 'setting_content_fields']);
        }

        add_action('opalestate_user_content_submission_list_page', [$this, 'submission_list']);
        add_action('opalestate_user_content_submission_page', [$this, 'submission']);
        add_action('wp_enqueue_scripts', [$this, 'scripts_styles'], 99);

        $this->register_shortcodes();
    }

    /**
     * Save post.
     */
    public function scripts_styles() {

        wp_register_style('opalesate-submission', OPALESTATE_PLUGIN_URL . 'assets/css/submission.css');
        wp_register_style('opalesate-cmb2-front', OPALESTATE_PLUGIN_URL . 'assets/css/cmb2-front.css');
        wp_register_script(
            'opalestate-submission',
            OPALESTATE_PLUGIN_URL . 'assets/js/frontend/submission.js',
            [
                'jquery',
            ],
            '1.0',
            true
        );

        wp_register_script(
            'jquery-wpopal-slick',
            trailingslashit(OPALESTATE_PLUGIN_URL) . 'assets/js/libs/slick.js',
            [
                'jquery',
            ],
            '1.8.1',
            true
        );
        wp_register_style('wpopal-slick', trailingslashit(OPALESTATE_PLUGIN_URL) . 'assets/css/slick.css');
    }

    /*
     * Is submission page. 'submission_page' option in General Setting
     */
    public function register_shortcodes() {
        $shortcodes = [
            'submission'      => [
                'code'  => 'submission',
                'label' => esc_html__('Submission Form', 'opalestate-pro'),
            ],
            'submission_list' => [
                'code'  => 'submission_list',
                'label' => esc_html__('My Properties', 'opalestate-pro'),
            ],
        ];

        foreach ($shortcodes as $shortcode) {
            add_shortcode('opalestate_' . $shortcode['code'], [$this, $shortcode['code']]);
        }
    }

    /*
     * Is submission page. 'submission_page' option in General Setting
     */
    public function setting_content_tab($tabs) {
        $tabs['submission_page'] = esc_html__('Submission', 'opalestate-pro');

        return $tabs;
    }

    /*
     * Is submission page. 'submission_page' option in General Setting
     */
    public function setting_content_fields($fields = []) {
        $fields = [
            'id'      => 'submission_page',
            'title'   => esc_html__('Email Settings', 'opalestate-pro'),
            'show_on' => ['key' => 'options-page', 'value' => ['opalestate_settings'],],
            'fields'  => apply_filters('opalestate_settings_submission', [
                    [
                        'name'      => esc_html__('Submission Page Settings', 'opalestate-pro'),
                        'id'        => 'opalestate_title_submission_page_settings',
                        'type'      => 'title',
                        'after_row' => '<hr>',
                    ],
                    [
                        'name'    => esc_html__('Property Submission Page', 'opalestate-pro'),
                        'desc'    => __('This is the submission page. The <code>[opalestate_submission]</code> shortcode should be on this page.', 'opalestate-pro'),
                        'id'      => 'submission_page',
                        'type'    => 'select',
                        'options' => opalestate_cmb2_get_post_options([
                            'post_type'   => 'page',
                            'numberposts' => -1,
                        ]),
                    ],
                    [
                        'name'    => esc_html__('Show Content when User Not Login', 'opalestate-pro'),
                        'desc'    => esc_html__('Show Login/Register form and submission form if the user is not logged in.', 'opalestate-pro'),
                        'id'      => 'submission_show_content',
                        'type'    => 'select',
                        'default' => '',
                        'options' => [
                            ''                 => esc_html__('Show Login Form', 'opalestate-pro'),
                            'login_submission' => esc_html__('Show Login Form and Submission Form', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Admin Approve', 'opalestate-pro'),
                        'desc'    => esc_html__('Admin must review and approve before properties are published.', 'opalestate-pro'),
                        'id'      => 'admin_approve',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                        'default' => 'on',
                    ],
                    [
                        'name'       => esc_html__('Submission Tab Settings', 'opalestate-pro'),
                        'id'         => 'opalestate_title_submission_tab_settings',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                        'after_row'  => '<hr>',
                    ],
                    [
                        'name'    => esc_html__('Enable Media tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Media tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_media',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Location tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Location tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_location',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Information tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Information tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_information',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Amenities tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Amenities tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_amenities',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Facilities tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Facilities tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_facilities',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Apartments tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Apartments tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_apartments',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Enable Floor plans tab', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable Floor plans tab', 'opalestate-pro'),
                        'id'      => 'enable_submission_tab_floor_plans',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'       => esc_html__('Submission Settings', 'opalestate-pro'),
                        'id'         => 'opalestate_title_submission_settings',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                        'after_row'  => '<hr>',
                    ],
                    [
                        'name'    => esc_html__('Generate property SKU', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable automatic generate property SKU.', 'opalestate-pro'),
                        'id'      => 'enable_submission_generate_sku',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                    ],
                    [
                        'name'    => esc_html__('Property SKU format', 'opalestate-pro'),
                        'desc'    => __('Use <code>{property_id}</code> to generate the property ID.', 'opalestate-pro'),
                        'id'      => 'submission_sku_format',
                        'type'    => 'text',
                        'default' => 'SKU-{property_id}',
                    ],
                    [
                        'name'       => esc_html__('Upload Settings', 'opalestate-pro'),
                        'id'         => 'opalestate_title_upload_settings',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                        'after_row'  => '<hr>',
                    ],
                    [
                        'name' => esc_html__('Maximum Upload Image Size', 'opalestate-pro'),
                        'desc' => esc_html__('Set maximum volumn size having < x MB', 'opalestate-pro'),

                        'id'      => 'upload_image_max_size',
                        'type'    => 'text',
                        'default' => '0.5',
                    ],
                    [
                        'name' => esc_html__('Maximum Upload Image Files', 'opalestate-pro'),
                        'desc' => esc_html__('Set maximum volumn size having < x MB', 'opalestate-pro'),

                        'id'      => 'upload_image_max_files',
                        'type'    => 'text',
                        'default' => '10',
                    ],
                    [
                        'name' => esc_html__('Maximum Upload Other Size', 'opalestate-pro'),
                        'desc' => esc_html__('Set maximum volumn size having < x MB for upload docx, pdf...', 'opalestate-pro'),

                        'id'      => 'upload_other_max_size',
                        'type'    => 'text',
                        'default' => '0.8',
                    ],
                    [
                        'name' => esc_html__('Maximum Upload Other Files', 'opalestate-pro'),
                        'desc' => esc_html__('Set maximum volumn size having < x MB for upload docx, pdf...', 'opalestate-pro'),

                        'id'        => 'upload_other_max_files',
                        'type'      => 'text',
                        'default'   => '10',
                        'after_row' => '<hr>',
                    ],
                ]
            ),
        ];

        return $fields;
    }

    /*
     * Is submission page. 'submission_page' option in General Setting
     */
    public function head_check_page() {

    }

    /*
     * Is submission page. 'submission_page' option in General Setting
     */
    public function render_button_edit() {
        global $post, $current_user;

        wp_get_current_user();

        if ($current_user->ID == $post->post_author) {
            echo '<div class="property-button-edit">
				<a href="' . opalestate_submssion_page($post->ID) . '">' . esc_html__('Edit My Property', 'opalestate-pro') . '</a>
				</div>';
        }
    }

    /*
     * Is submission page. 'submission_page' option in General Setting
     */
    public function is_submission_page() {
        global $post;
        if (!$post || !isset($post->ID) || !$post->ID) {
            return false;
        }

        return opalestate_get_option('submission_page') == $post->ID;
    }

    /**
     * Register metabox.
     *
     * @return \Opalestate_Property_MetaBox_Submission
     */
    public function register_metabox() {
        $metabox = new Opalestate_Property_MetaBox_Submission();

        add_filter('cmb2_meta_boxes', [$metabox, 'register_form'], 9999);

        return $metabox;
    }

    /**
     * FrontEnd Submission
     */
    public function submission() {
        global $current_user;

        if (!is_user_logged_in()) {
            echo opalestate_load_template_path('submission/require-login');
            if (empty(opalestate_get_option("submission_show_content"))) {
                return "";
            }
        }

        if (isset($_GET['do']) && $_GET['do'] == 'completed') {
            OpalEstate()->session->set('submission', 'addnew');

            echo opalestate_load_template_path('submission/completed');

            return;
        }

        // remove all dirty images before edit/create new a property
        $this->cleanup();

        wp_enqueue_script('opalestate-submission');
        wp_enqueue_style('opalesate-submission');
        wp_enqueue_style('opalesate-cmb2-front');

        $metabox   = $this->register_metabox();
        $metaboxes = apply_filters('cmb2_meta_boxes', []);


        if (!isset($metaboxes[OPALESTATE_PROPERTY_PREFIX . 'front'])) {
            return esc_html__('A metabox with the specified \'metabox_id\' doesn\'t exist.', 'opalestate-pro');
        }
        $post_id = 0;

        if (is_user_logged_in()) {
            // CMB2 is getting fields values from current post what means it will fetch data from submission page
            // We need to remove all data before.
            $post_id = !empty($_GET['id']) ? absint($_GET['id']) : false;
            if (!$post_id) {
                unset($_POST);
                foreach ($metaboxes[OPALESTATE_PROPERTY_PREFIX . 'front']['fields'] as $field_name => $field_value) {
                    delete_post_meta(get_the_ID(), $field_value['id']);
                }
            }

            if (!empty($post_id) && !empty($_POST['object_id'])) {
                $post_id = absint($_POST['object_id']);

            }

            if ($post_id && !opalestate_is_own_property($post_id, $current_user->ID)) {
                echo opalestate_load_template_path('parts/has-warning');

                return;
            }
        }

        return opalestate_load_template_path('submission/submission-form',
            [
                'post_id'    => $post_id,
                'metaboxes'  => $metaboxes,
                'navigation' => $metabox->get_fields_groups(),
            ]);
    }

    /**
     *
     *
     */
    public function cmb2_get_metabox() {
        $object_id = 'fake-oject-id';

        return cmb2_get_metabox(OPALESTATE_PROPERTY_PREFIX . 'front', $object_id);
    }

    /**
     * FrontEnd Submission
     */
    public function process_submission() {
        if (isset($_POST['submission_action']) && !empty($_POST['submission_action'])) {
            if (wp_verify_nonce($_POST['submission_action'], 'submitted-property')) {
                $user_id = get_current_user_id();
                $edit    = false;
                $prefix  = OPALESTATE_PROPERTY_PREFIX;
                $blocked = OpalEstate_User::is_blocked();

                // Setup and sanitize data
                if (isset($_POST[$prefix . 'title']) && !$blocked && $user_id) {
                    $metabox   = $this->register_metabox();
                    $metaboxes = apply_filters('cmb2_meta_boxes', []);

                    $post_id = !empty($_POST['post_id']) ? absint($_POST['post_id']) : false;

                    if ($post_id) {
                        do_action('opalestate_process_edit_submission_before');
                    } else {
                        do_action('opalestate_process_submission_before');
                    }

                    $post_status = 'pending';

                    if ('on' !== opalestate_get_option('admin_approve', 'on')) {
                        $post_status = 'publish';
                    }

                    // If we are updating the post get old one. We need old post to set proper
                    // post_date value because just modified post will at the top in archive pages.
                    if (!empty($post_id)) {
                        $old_post  = get_post($post_id);
                        $post_date = $old_post->post_date;
                    } else {
                        $post_date = '';
                    }

                    $post_content = isset($_POST[$prefix . 'text']) ? wp_kses($_POST[$prefix . 'text'],
                        '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span><p>') : '';

                    $data = [
                        'post_title'   => sanitize_text_field($_POST[$prefix . 'title']),
                        'post_author'  => $user_id,
                        'post_status'  => $post_status,
                        'post_type'    => 'opalestate_property',
                        'post_date'    => $post_date,
                        'post_content' => $post_content,
                    ];

                    $unset_fields = [
                        'text',
                        'title',
                        'post_type',
                    ];

                    unset($_POST['post_type']);

                    foreach ($unset_fields as $field) {
                        unset($_POST[$prefix . $field]);
                    }

                    if (!empty($post_id)) {
                        $edit       = true;
                        $data['ID'] = $post_id;

                        do_action('opalestate_process_edit_submission_before');
                    } else {
                        do_action('opalestate_process_add_submission_before');
                    }

                    if (empty($data['post_title']) || empty($data['post_author'])) {
                        return opalestate_output_msg_json(false,
                            __('Please enter data for all require fields before submitting', 'opalestate-pro'),
                            [
                                'heading' => esc_html__('Submission Information', 'opalestate-pro'),
                            ]);
                    }

                    $post_id = wp_insert_post($data, true);

                    if (!empty($post_id) && !empty($_POST['object_id'])) {
                        $_POST['object_id'] = (int)$post_id;

                        $metaboxes = apply_filters('cmb2_meta_boxes', []);

                        /*
                         * Processing upload files
                         */
                        $this->process_upload_files($post_id);

                        /**
                         * Fetch sanitized values
                         */
                        cmb2_get_metabox_form($metaboxes[$prefix . 'front'], $post_id);
                        $cmb              = $this->cmb2_get_metabox();
                        $sanitized_values = $cmb->get_sanitized_values($_POST);
                        $cmb->save_fields($post_id, 'post', $sanitized_values);

                        // Create featured image
                        $featured_image = get_post_meta($post_id, $prefix . 'featured_image', true);

                        if (!empty($_POST[$prefix . 'featured_image']) && isset($_POST[$prefix . 'featured_image'])) {
                            if ($_POST[$prefix . 'featured_image'] && is_array($_POST[$prefix . 'featured_image'])) {
                                foreach ($_POST[$prefix . 'featured_image'] as $key => $value) {
                                    set_post_thumbnail($post_id, $key);
                                }
                            }
                            unset($_POST[$prefix . 'featured_image']);
                        } else {
                            delete_post_thumbnail($post_id);
                        }

                        // Remove meta field.
                        update_post_meta($post_id, $prefix . 'featured_image', null);

                        // Update SKU.
                        if ('on' === opalestate_get_option('enable_submission_generate_sku', 'off')) {
                            $_sku          = str_replace('{property_id}', $post_id, opalestate_options('submission_sku_format', 'SKU-{property_id}'));
                            $sku_generated = apply_filters('opalestate_submission_sku_generated', sanitize_text_field($_sku));
                            update_post_meta($post_id, $prefix . 'sku', $sku_generated);
                        }

                        update_post_meta($post_id, $prefix . 'featured', '');

                        //redirect
                        $_SESSION['messages'][] = ['success', esc_html__('Property has been successfully updated.', 'opalestate-pro')];

                        do_action("opalestate_process_submission_after", $user_id, $post_id, $edit);

                        if ($edit) {
                            $type    = OpalEstate()->session->set('submission', 'edit');
                            $message = esc_html__('The property has updated completed with new information', 'opalestate-pro');
                            do_action("opalestate_processed_edit_submission", $user_id, $post_id);
                        } else {
                            $type    = OpalEstate()->session->get('submission', 'addnew');
                            $message = esc_html__('You have submitted the property successful', 'opalestate-pro');
                            do_action("opalestate_processed_new_submission", $user_id, $post_id);
                        }

                        // set ready of attachment for use.
                        if ($this->new_attachmenet_ids) {
                            foreach ($this->new_attachmenet_ids as $_id) {
                                delete_post_meta($_id, '_pending_to_use_', 1);
                            }
                        }

                        return opalestate_output_msg_json(true,
                            $message,
                            [
                                'heading'  => esc_html__('Submission Information', 'opalestate-pro'),
                                'redirect' => opalestate_submssion_page($post_id, ['do' => 'completed']),
                            ]);
                    }
                } else {
                    return opalestate_output_msg_json(false,
                        __('Currently, your account was blocked, please keep contact admin to resolve this!.', 'opalestate-pro'),
                        ['heading' => esc_html__('Submission Information', 'opalestate-pro')]
                    );
                }
            }

            return opalestate_output_msg_json(false,
                __('Sorry! Your submitted datcould not save a at this time', 'opalestate-pro'),
                ['heading' => esc_html__('Submission Information', 'opalestate-pro')]
            );
        }
    }

    /**
     *
     *
     */
    private function get_field_name($field) {
        return OPALESTATE_PROPERTY_PREFIX . $field;
    }

    /**
     * Process upload files.
     *
     * @param int $post_id Post ID.
     */
    private function process_upload_files($post_id) {
        //upload images for featured and gallery images
        if (isset($_FILES) && !empty($_FILES)) {
            $fields = [
                $this->get_field_name('gallery'),
                $this->get_field_name('featured_image'),
            ];

            foreach ($_FILES as $key => $value) {
                // allow processing in fixed collection
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
                                $_POST[$key]                                          = isset($_POST[$key]) ? $_POST[$key] : [];
                                $_POST[$key][$new_atm['attachment_id']]               = $new_atm['url'];
                                $this->new_attachmenet_ids[$new_atm['attachment_id']] = $new_atm['attachment_id'];
                            }
                        }

                    } ///
                    elseif (isset($ufile['name'])) {
                        $new_atm = $this->upload_image($ufile, $post_id);
                        if ($new_atm) {
                            $_POST[$key][$new_atm['attachment_id']]               = $new_atm['url'];
                            $this->new_attachmenet_ids[$new_atm['attachment_id']] = $new_atm['attachment_id'];
                        }
                    }
                    //// / //
                }
            }

            // for group files
            $fields = [
                $this->get_field_name('public_floor_group'),
            ];

            foreach ($_FILES as $key => $value) {
                if (in_array($key, $fields)) {
                    $ufile = $_FILES[$key];

                    if (isset($ufile['name']) && is_array($ufile['name'])) {
                        $output = [];
                        foreach ($ufile['name'] as $f_key => $f_value) {

                            foreach ($f_value as $u_key => $u_v) {
                                $loop_file = [
                                    'name'     => $ufile['name'][$f_key][$u_key],
                                    'type'     => $ufile['type'][$f_key][$u_key],
                                    'tmp_name' => $ufile['tmp_name'][$f_key][$u_key],
                                    'error'    => $ufile['error'][$f_key][$u_key],
                                    'size'     => $ufile['size'][$f_key][$u_key],
                                ];

                                $new_atm = $this->upload_image($loop_file, $post_id);
                                if ($new_atm) {

                                    $_POST[$key][$f_key][$u_key]                          = $new_atm['attachment_id'];
                                    $this->new_attachmenet_ids[$new_atm['attachment_id']] = $new_atm['attachment_id'];
                                }
                            }
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

    /**
     * FrontEnd Submission
     */
    private function cleanup() {
        $user_id = get_current_user_id();
        opalestate_clean_attachments($user_id);
    }

    /**
     * FrontEnd Submission
     */
    public function submission_list() {
        if (!is_user_logged_in()) {
            echo opalestate_load_template_path('parts/not-allowed');

            return;
        }

        if (isset($_GET['id']) && isset($_GET['remove'])) {
            $is_allowed = Opalestate_Property::is_allowed_remove(get_current_user_id(), intval($_GET['id']));
            if (!$is_allowed) {
                echo opalestate_load_template_path('parts/not-allowed');

                return;
            }

            if (wp_delete_post(intval($_GET['id']))) {
                $_SESSION['messages'][] = ['success', esc_html__('Property has been successfully removed.', 'opalestate-pro')];
            } else {
                $_SESSION['messages'][] = ['danger', esc_html__('An error occured when removing an item.', 'opalestate-pro')];
            }

            wp_redirect(opalestate_submssion_list_page());
        }


        $args = [];

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $args['post_status'] = sanitize_text_field($_GET['status']);
        }

        $loop = Opalestate_Query::get_properties_by_user($args, get_current_user_id());

        return opalestate_load_template_path('user/my-properties', ['loop' => $loop]);
    }
}

new OpalEstate_Submission();
