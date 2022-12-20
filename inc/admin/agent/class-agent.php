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

class Opalestate_Admin_Agent {

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function __construct() {
        add_action('cmb2_admin_init', [$this, 'metaboxes']);
        add_action('save_post', [$this, 'save_post'], 10, 3);
        add_action('user_register', [$this, 'on_update_user'], 10, 1);
        add_action('profile_update', [$this, 'on_update_user'], 10, 1);
    }

    /**
     * Auto update meta information to post from user data updated or created
     */
    public function on_update_user() {
        if (isset($_POST['user_id']) && (int)$_POST['user_id'] && isset($_POST['role'])) {
            if ($_POST['role'] == 'opalestate_agent') {
                $user_id = absint($_POST['user_id']);
                static::update_user_metas($user_id);

                $related_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                $post       = get_post($related_id);

                if (isset($post->ID) && $post->ID) {
                    OpalEstate_Agent::update_data_from_user($related_id);
                }
            }
        }
    }

    public function save_post($post_id, $post, $update) {
        $post_type = get_post_type($post_id);
        if ($post_type == 'opalestate_agent') {
            if (isset($_POST[OPALESTATE_AGENT_PREFIX . 'user_id']) && absint($_POST[OPALESTATE_AGENT_PREFIX . 'user_id'])) {
                $user_id = absint($_POST[OPALESTATE_AGENT_PREFIX . 'user_id']);
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $post_id);

                OpalEstate_Agent::update_user_data($user_id);
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
    public function metaboxes() {
        global $pagenow;
        if (($pagenow == 'post.php' || $pagenow == 'post-new.php')) {
            $prefix = OPALESTATE_AGENT_PREFIX;

            $metabox = new Opalestate_Agent_MetaBox();

            // echo '<pre>' . print_r( $metabox->get_social_fields( $prefix ) ,1 );die;
            $box_options = [
                'id'           => $prefix . 'edit',
                'title'        => esc_html__('Metabox', 'opalestate-pro'),
                'object_types' => ['opalestate_agent'],
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
                'fields' => $metabox->metaboxes_admin_fields(),
            ];

            $tabs_setting['tabs'][] = [
                'id'     => 'p-socials',
                'icon'   => 'dashicons-share',
                'title'  => esc_html__('Socials', 'opalestate-pro'),
                'fields' => $metabox->get_social_fields($prefix),
            ];

            $tabs_setting['tabs'][] = [
                'id'     => 'p-prices-target',
                'icon'   => 'dashicons-admin-tools',
                'title'  => esc_html__('Target Search', 'opalestate-pro'),
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

new Opalestate_Admin_Agent();
