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


class OpalEstate_Admin_User {

    /**
     * OpalEstate_Admin_User constructor.
     */
    public function __construct() {
        add_action('cmb2_admin_init', [$this, 'register_user_profile_metabox']);
        add_action('personal_options', [$this, 'show_message_user_profile']);
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
                    <p><?php echo sprintf(__('This user has role <strong>Opal Estate Agency</strong> and click here to <a href="%s">update Agency profile</a>', 'opalestate-pro'), $link); ?></p>
                </div>
            <?php elseif (in_array('opalestate_agent', $roles)) :
                $agent_id = get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true);
                if (!$agent_id) {
                    return;
                }
                $link = get_edit_post_link($agent_id);
                ?>
                <div id="message" class="updated fade">
                    <p><?php echo sprintf(__('This user has role <strong>Opal Estate Agent</strong> and click here to <a href="%s">update Agent profile</a>', 'opalestate-pro'), $link); ?></p>
                </div>
            <?php endif; ?>
        <?php
        endif;
    }

    /**
     *
     */
    public function shortcode_button() {

    }

    /**
     * Hook in and add a metabox to add fields to the user profile pages
     */
    public function register_user_profile_metabox() {
        global $pagenow;

        if ($pagenow == 'profile.php' || $pagenow == 'user-new.php' || $pagenow == 'user-edit.php') {
            if ($pagenow == 'profile.php' && !opalestate_current_user_can_access_dashboard_page()) {
                return;
            }

            if ($pagenow == 'user-edit.php') {
                $user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
                if (!$user_id) {
                    return;
                }

                if (!opalestate_user_has_estate_roles($user_id)) {
                    return;
                }
            }

            $prefix = OPALESTATE_USER_PROFILE_PREFIX;

            $metabox = new Opalestate_User_MetaBox();

            $box_options = [
                'id'           => $prefix . 'edit',
                'title'        => esc_html__('Metabox', 'opalestate-pro'),
                'object_types' => ['user'],
                'show_names'   => true,
            ];

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
                'fields' => $this->get_base_fields(),
            ];

            $tabs_setting['tabs'][] = [
                'id'     => 'p-socials',
                'icon'   => 'dashicons-share',
                'title'  => esc_html__('Socials', 'opalestate-pro'),
                'fields' => $metabox->get_social_fields($prefix),
            ];


            // Set tabs
            $cmb->add_field([
                'id'   => '__tabs',
                'type' => 'tabs',
                'tabs' => $tabs_setting,
            ]);

            /**
             * Metabox for the user profile screen
             */
            $cmb_user = new_cmb2_box([
                'id'               => $prefix . 'edit',
                'title'            => esc_html__('User Profile Metabox', 'opalestate-pro'), // Doesn't output for user boxes
                'object_types'     => ['user'], // Tells CMB2 to use user_meta vs post_meta
                'show_names'       => true,
                'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
            ]);

            $fields = $this->extra_info_fields();
            foreach ($fields as $field) {
                $cmb_user->add_field($field);
            }
        }
    }

    public function get_base_fields() {
        $prefix = OPALESTATE_USER_PROFILE_PREFIX;

        $metabox = new Opalestate_User_MetaBox();
        $fields  = array_merge_recursive(
            $metabox->get_base_fields($prefix),
            $metabox->get_job_fields($prefix),
            $metabox->get_address_fields($prefix)
        );

        return $fields;
    }

    /**
     *
     */
    public function extra_info_fields() {


        $prefix = OPALESTATE_USER_PROFILE_PREFIX;

        $management = [];


        $admin_fields   = [];
        $admin_fields[] = [
            'id'          => "{$prefix}block_submission",
            'name'        => esc_html__('Block Submssion', 'opalestate-pro'),
            'type'        => 'checkbox',
            'description' => esc_html__('Disable Submssion Functions to not allow submit property', 'opalestate-pro'),
            'before_row'  => '<hr>',

        ];
        $admin_fields[] = [
            'id'          => "{$prefix}block_submission_msg",
            'name'        => esc_html__('Block Submssion Message', 'opalestate-pro'),
            'type'        => 'textarea',
            'description' => esc_html__('Show message for disabled user', 'opalestate-pro'),
        ];
        $management     = array_merge_recursive($admin_fields, $management);


        return $management;
    }
}

new OpalEstate_Admin_User();
