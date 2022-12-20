<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Admin_Agency {

    /**
     * Opalestate_Admin_Agency constructor.
     */
    public function __construct() {
        add_action('cmb2_admin_init', [$this, 'metaboxes']);
        add_action('save_post', [$this, 'on_save_post'], 13, 2);
        add_action('user_register', [$this, 'on_update_user'], 10, 1);
        add_action('profile_update', [$this, 'on_update_user'], 10, 1);

    }

    /**
     * Update relationship post and user data, auto update meta information from post to user
     */
    public function on_save_post($post_id) {
        $post_type = get_post_type($post_id);
        if ($post_type == 'opalestate_agency') {
            if (isset($_POST[OPALESTATE_AGENCY_PREFIX . 'user_id']) && $_POST[OPALESTATE_AGENCY_PREFIX . 'user_id']) {
                $user_id = absint($_POST[OPALESTATE_AGENCY_PREFIX . 'user_id']);
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $post_id);
                OpalEstate_Agency::update_user_data($user_id);
            }
        }
    }

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function on_update_user() {
        if (isset($_POST['user_id']) && (int)$_POST['user_id'] && isset($_POST['role'])) {
            if ($_POST['role'] == 'opalestate_agency') {
                $user_id = absint($_POST['user_id']);
                static::update_user_metas($user_id);

                $related_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                $post       = get_post($related_id);
                if (isset($post->ID) && $post->ID) {
                    OpalEstate_Agency::update_data_from_user($related_id);
                }
            }
        }
    }

    /**
     * Update some user metas.
     *
     * @param int $related_id Post ID.
     */
    public static function update_user_metas($user_id) {
        $terms = [
            'location',
            'state',
            'city',
        ];

        foreach ($terms as $term) {
            if (isset($_POST[OPALESTATE_USER_PROFILE_PREFIX . $term])) {
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . $term, $_POST[OPALESTATE_USER_PROFILE_PREFIX . $term]);
            }
        }
    }

    /**
     *
     */
    public function metaboxes_fields($prefix = '') {

        if (!$prefix) {
            $prefix = OPALESTATE_AGENCY_PREFIX;
        }

        $fields = [


            [
                'name' => esc_html__('Gallery', 'opalestate-pro'),
                'desc' => esc_html__('Select one, to add new you create in location of estate panel', 'opalestate-pro'),
                'id'   => $prefix . "gallery",
                'type' => 'file_list',
            ],

            [
                'name' => esc_html__('Slogan', 'opalestate-pro'),
                'id'   => "{$prefix}slogan",
                'type' => 'text',
            ],
        ];

        return apply_filters('opalestate_postype_agency_metaboxes_fields', $fields);
    }

    /**
     *
     */
    public function metaboxes() {

        global $pagenow;

        if (($pagenow == 'post.php' || $pagenow == 'post-new.php')) {

            $prefix = OPALESTATE_AGENCY_PREFIX;

            $metabox = new Opalestate_Agency_MetaBox();

            $fields = $this->metaboxes_fields();
            $fields = array_merge_recursive($fields,
                $metabox->get_office_fields($prefix),
                $metabox->get_address_fields($prefix)
            );


            $box_options = [
                'id'           => $prefix . 'edit',
                'title'        => esc_html__('Metabox', 'opalestate-pro'),
                'object_types' => ['opalestate_agency'],
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
                'fields' => $fields,
            ];

            $tabs_setting['tabs'][] = [
                'id'     => 'p-socials',
                'icon'   => 'dashicons-share',
                'title'  => esc_html__('Socials', 'opalestate-pro'),
                'fields' => $metabox->get_social_fields($prefix),
            ];


            $tabs_setting['tabs'][] = [
                'id'     => 'p-target',
                'icon'   => 'dashicons-admin-tools',
                'title'  => esc_html__('Team', 'opalestate-pro'),
                'fields' => $metabox->metaboxes_target(),
            ];
            // Set tabs
            $cmb->add_field([
                'id'   => '__tabs',
                'type' => 'tabs',
                'tabs' => $tabs_setting,
            ]);
        }
    }
}

new Opalestate_Admin_Agency();
