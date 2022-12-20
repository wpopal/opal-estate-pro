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

if (defined('OpalMembership')) {
    return;
}

/**
 * @class   OpalEstate_Membership: as vendor class is using for processing logic with update/set permission for user submitting property.
 *
 * @version 1.0
 */
class OpalEstate_Membership {

    /*
     * Constructor
     */
    public static function init() {

        if (get_current_user_id()) {
            /* estate */
            add_action('show_admin_bar', [__CLASS__, 'hide_admin_toolbar']); // hide admin toolbar
            /* cmb2 meta box hook membership information */
            add_filter('opalmembership_postype_membership_metaboxes_fields', [__CLASS__, 'metabox'], 10);
            /* add user agent role after insert new user member */
            //	add_filter( 'opalmembership_create_user_data'			  , array( __CLASS__, 'add_user_role_data' ), 10 );
            //	add_action( 'opalmembership_create_new_user_successfully', array( __CLASS__, 'trigger_create_user_agent' ), 10, 3 );
            /**
             * save user meta when save user agent post type
             */
            add_action('cmb2_save_post_fields', [__CLASS__, 'trigger_update_user_meta'], 10, 4);
            add_action('profile_update', [__CLASS__, 'trigger_update_agent_meta'], 10, 2);
            /**
             *  Call Hook after updated membership information in user data.
             */
            add_action('opalmembership_after_update_user_membership', [__CLASS__, 'on_set_user_update_membership'], 10, 3);

            /**
             * HOOK TO My Properties Page Set action to check when user set property as featured.
             */
            add_filter('opalestate_set_feature_property_checked', [__CLASS__, 'feature_property_checked']);
            add_action('opalestate_toggle_featured_property_before', [__CLASS__, 'update_featured_remaining_listing'], 10, 2);

            /**
             * HOOK to Submssion Page: Check permission before submitting
             */
            // check validation before
            add_action('opalestate_process_submission_before', [__CLASS__, 'check_membership_validation'], 1);

            add_action('opalestate_submission_form_before', [__CLASS__, 'show_membership_warning'], 9);
            add_action('opalestate_submission_form_before', [__CLASS__, 'check_membership_validation_message']);

            add_action('opalestate_process_edit_submission_before', [__CLASS__, 'check_edit_post']);
            add_action('opalestate_process_add_submission_before', [__CLASS__, 'check_add_post']);
            /// check before uploading image
            add_action('opalestate_before_process_ajax_upload_file', [__CLASS__, 'check_ajax_upload']);
            add_action('opalestate_process_submission_after', [__CLASS__, 'update_remainng_listing'], 10, 3);

            /**
             * HOOK to user management Menu
             */
            add_filter('opalestate_management_user_menu', [__CLASS__, 'membership_menu']);

            /**
             * Hook to widget to show addition about current package.
             */
            add_action('opalmembership_current_package_summary_after', [__CLASS__, 'render_membership_summary'], 10, 2);

            /**
             * Add 'opalesate_agent' role to get_users member data
             */
            // add_action( 'opalmembership_member_table_arguments', array( __CLASS__, 'member_table_get_user_arguments' ) );
            // show in membership dashboard
            add_action('opalmembership_dashboard_container_before', [__CLASS__, 'check_membership_validation_message']);
            // included logic functions

            // require_once( 'free-package.php' );
            require_once('functions.php');

            add_action('opalmembership_current_package_summary_after', [__CLASS__, 'render_membership_summary'], 10, 2);

            add_action('cmb2_admin_init', [__CLASS__, 'register_user_package_metabox']);


            add_action('profile_update', [__CLASS__, 'on_update_user'], 10, 1);

            add_action('save_post', [__CLASS__, 'update_property_expired_time'], 10, 2);
        }

        /**
         * Hook to loop of package membership
         */
        add_action('opalmembership_content_single_before', [__CLASS__, 'render_membership_pricing_box']);


    }

    public static function show_membership_warning() {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            return true;
        }
        if (class_exists("Opalmembership_User")) {
            return Opalmembership_User::show_membership_warning();
        }
    }

    public static function render_membership_free_package() {
        echo opalestate_load_template_path('parts/membership-free-package', []);
    }

    public static function render_membership_pricing_box() {
        echo opalestate_load_template_path('parts/membership-pricing-info', []);
    }

    public static function member_table_get_user_arguments($args) {
        return array_merge_recursive($args, ['role__in' => ['opalestate_agent']]);
    }


    public static function set_properties_expired() {

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = [
            'post_type'   => 'opalestate_agent',
            'author'      => $user_id,
            'post_status' => 'any',
        ];

        $query = new WP_Query($args);

        while ($query->have_posts()) {
            $query->the_post();

            $prop = [
                'ID'          => $post->ID,
                'post_type'   => 'opalestate_agent',
                'post_status' => 'expired',
            ];

            wp_update_post($prop);
        }
        wp_reset_query();

    }

    /**
     * Before upload any file. this is called to check user having package which allows to upload or is not expired.
     *
     * @return void if everything is ok, or json data if it is not valid.
     */
    public static function check_ajax_upload() {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $has = opalesate_check_has_add_listing($user_id);


        $check = opalesate_is_membership_valid($user_id);

        if (!$check || !$has) {
            $std          = new stdClass();
            $std->status  = false;
            $std->message = esc_html__('Could not allow uploading image', 'opalestate-pro');
            echo json_encode($std);
            exit();
        }
    }

    /**
     * hide admin toolbar with user role agent
     */
    public static function hide_admin_toolbar($show) {
        if (!is_user_logged_in()) {
            return $show;
        }

        $user = wp_get_current_user();
        if (opalestate_get_option('hide_toolbar') && $user->roles && in_array('opalestate_agent', $user->roles)) {
            return false;
        }

        return $show;
    }

    /**
     *  Trigger to extend fields to inject into Metabox of property edit/add form.
     */
    public static function metabox($fields) {

        if (!defined('OPALMEMBERSHIP_PACKAGES_PREFIX')) {
            return $fields;
        }

        $prefix = OPALMEMBERSHIP_PACKAGES_PREFIX;

        $fields[] = [
            'name'        => esc_html__('Limited listings?', 'opalestate-pro'),
            'id'          => $prefix . 'unlimited_listings',
            'type'        => 'checkbox',
            'std'         => '1',
            'description' => esc_html__('Check if set limited listings. Default: Unlimited listings. Notice: Enter Number Of Properties when set limited listings.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Number Of Properties', 'opalestate-pro'),
            'id'          => $prefix . 'package_listings',
            'type'        => 'text',
            'attributes'  => [
                'type'    => 'number',
                'pattern' => '\d*',
                'min'     => 0,
            ],
            'std'         => '1',
            'description' => esc_html__('Number of properties with this package. If not set it will be unlimited.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Number Of Featured Properties', 'opalestate-pro'),
            'id'          => $prefix . 'package_featured_listings',
            'type'        => 'text',
            'attributes'  => [
                'type'    => 'number',
                'pattern' => '\d*',
                'min'     => 0,
            ],
            'std'         => '1',
            'description' => esc_html__('Number of properties can make featured with this package.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Accumulated listings?', 'opalestate-pro'),
            'id'          => $prefix . 'accumulated_listings',
            'type'        => 'checkbox',
            'description' => esc_html__('Increase Number Of Properties and Number Of Featured Properties from current remaining values when upgrading this package.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Enable Expired for properties ', 'opalestate-pro'),
            'id'          => $prefix . 'enable_property_expired',
            'type'        => 'checkbox',
            'description' => esc_html__('Do you want enable expired date for properties?', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Expired Property In', 'opalestate-pro'),
            'id'          => $prefix . 'property_duration',
            'type'        => 'text',
            'attributes'  => [
                'type'    => 'number',
                'pattern' => '\d*',
                'min'     => 0,
            ],
            'std'         => '1',
            'description' => esc_html__('Expired a property in. Enter expired number. Example 1, 2, 3', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Expired property Date Type', 'opalestate-pro'),
            'id'          => $prefix . 'property_duration_unit',
            'type'        => 'select',
            'options'     => opalmembership_package_expiry_labels(),
            'description' => esc_html__('Enter expired date type. Example Day(s), Week(s), Month(s), Year(s)', 'opalestate-pro'),
        ];

        return $fields;
    }

    /**
     * Hook Method to add more link for user management
     */
    public static function membership_menu($menu) {
        if (function_exists("opalmembership_get_dashdoard_page_uri")) {
            global $opalmembership_options;

            if (opalestate_current_user_can_access_dashboard_page('membership')) {
                $menu['membership'] = [
                    'icon'  => 'fa fa-user',
                    'link'  => opalmembership_get_dashdoard_page_uri(),
                    'title' => esc_html__('My Membership', 'opalestate-pro'),
                    'id'    => isset($opalmembership_options['dashboard_page']) ? $opalmembership_options['dashboard_page'] : 0,
                ];
            }

            if (opalestate_current_user_can_access_dashboard_page('membership_history')) {
                $menu['membership_history'] = [
                    'icon'  => 'fa fa-user',
                    'link'  => opalmembership_get_payment_history_page_uri(),
                    'title' => esc_html__('My Invoices', 'opalestate-pro'),
                    'id'    => isset($opalmembership_options['history_page']) ? $opalmembership_options['history_page'] : 0,
                ];
            }

            if (opalestate_current_user_can_access_dashboard_page('packages')) {
                $menu['packages'] = [
                    'icon'  => 'fa fa-certificate',
                    'link'  => opalmembership_get_membership_page_uri(),
                    'title' => esc_html__('Renew membership', 'opalestate-pro'),
                    'id'    => isset($opalmembership_options['membership_page']) ? $opalmembership_options['membership_page'] : 0,
                ];
            }
        }

        return $menu;
    }

    /**
     * This function add new user role in this case is add 'opalestate_agent' role to user created
     */
    public static function add_user_role_data($cred) {
        $cred['role'] = 'opalestate_agent';

        return $cred;
    }

    /**
     * trigger create new post type user agent
     */
    public static function trigger_create_user_agent($user_id, $user_data, $cred) {
        // create new post(opalestate_agent)
        $agent_id = opalesate_insert_user_agent([
            'first_name' => $cred['first_name'],
            'last_name'  => $cred['last_name'],
            'email'      => $cred['user_email'],
        ]);

        update_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . 'user_id', $user_id);
    }

    /**
     * save user meta data
     */
    public static function trigger_update_user_meta($agent_id, $cmb_id, $updated, $cmb2) {

        if ($cmb_id !== 'opalestate_agt_info' || empty($cmb2->data_to_save)) {
            return;
        }
        $user_id = get_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . 'user_id', true);

        if (!$user_id) {
            return;
        }
        foreach ($cmb2->data_to_save as $name => $value) {


            if (strpos($name, OPALESTATE_AGENT_PREFIX) === 0) {
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . substr($name, strlen(OPALESTATE_AGENT_PREFIX)), $value);
            }
        }
    }

    /**
     * trigger save agent post meta
     */
    public static function trigger_update_agent_meta($user_id, $old_user_meta) {
        if (empty($_POST)) {
            return;
        }
        global $wpdb;
        $sql      = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %d AND meta_key = %s", $user_id, OPALESTATE_AGENT_PREFIX . 'user_id');
        $agent_id = $wpdb->get_var($sql);

        if (!$agent_id) {
            return;
        }
        foreach ($_POST as $name => $value) {
            if (strpos($name, OPALESTATE_USER_PROFILE_PREFIX) === 0) {
                update_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . substr($name, strlen(OPALESTATE_USER_PROFILE_PREFIX)), $value);
            }
        }
    }

    /**
     * This function is called when payment status is completed. It will update new number of featured, listing for user.
     *
     * @return void
     */
    public static function on_set_user_update_membership($package_id, $user_id = 0, $payment_id = 0) {
        /**
         * Get some information from selected package.
         */
        $pack_listings          = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'package_listings', true);
        $pack_featured_listings = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'package_featured_listings', true);
        $is_unlimited_listings  = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'unlimited_listings', true);
        $accumulated_listings   = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'accumulated_listings', true);

        $pack_unlimited_listings = $is_unlimited_listings == 'on' ? 0 : 1;

        /**
         * Get package information with user logined
         */
        $current_listings         = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_listings', true);
        $curent_featured_listings = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_featured_listings', true);
        $current_pack             = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_id', true);

        $user_current_listings          = opalesate_get_user_current_listings($user_id); // get user current listings ( no expired )
        $user_current_featured_listings = opalesate_get_user_current_featured_listings($user_id); // get user current featured listings ( no expired )

        if ($accumulated_listings == 'on') {
            $new_listings          = ($current_listings != -1) ? ($pack_listings + $current_listings) : $pack_listings;
            $new_featured_listings = ($curent_featured_listings != -1) ? ($pack_featured_listings + $curent_featured_listings) : $pack_featured_listings;
        } else {
            if (!opalesate_check_package_downgrade_status($user_id, $package_id)) {
                $new_listings          = $pack_listings;
                $new_featured_listings = $pack_featured_listings;
            } else {
                $new_listings          = $pack_listings - $user_current_listings;
                $new_featured_listings = $pack_featured_listings - $user_current_featured_listings;
            }
        }

        // in case of downgrade
        if ($new_listings < 0) {
            $new_listings = 0;
        }

        if ($new_featured_listings < 0) {
            $new_featured_listings = 0;
        }

        if ($pack_unlimited_listings == 1) {
            $new_listings = -1;
        }

        /**
         * Update new number of packages listings and featured listing.
         */
        update_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_listings', $new_listings);
        update_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_featured_listings', $new_featured_listings);

        $enable_property_expired = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'enable_property_expired', true);
        $property_duration       = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'property_duration', true);
        $property_duration_unit  = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'property_duration_unit', true);

        update_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'enable_property_expired', $enable_property_expired);
        update_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'property_duration', $property_duration);
        update_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'property_duration_unit', $property_duration_unit);
    }

    /**
     * This function is called when user set property as featured.
     *
     * @return boolean. true is user having permission.
     */
    public static function feature_property_checked() {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        if (isset($_POST['property_id'])) {
            return opalesate_get_user_featured_remaining_listing($user_id);
        }

        return false;
    }

    /**
     * Reduce -1 when set featured status is done.
     */
    public static function update_featured_remaining_listing($user_id, $property_id) {
        opalesate_update_package_number_featured_listings($user_id);
    }

    /**
     *
     */
    public static function update_remainng_listing($user_id, $property_id, $isedit = true) {
        if ($isedit != true) {
            opalesate_update_package_number_listings($user_id);
        }
    }

    /**
     * Check user having any actived package and the package is not expired.
     * Auto redirect to membership packages package.
     */
    public static function check_membership_validation() {
        $check = opalesate_is_membership_valid();
        if (!$check) {

            return opalestate_output_msg_json(true,
                __('Your membership package is expired or Your package has 0 left listing, please upgrade now.', 'opalestate-pro'),
                [
                    'heading'  => esc_html__('Submission Information', 'opalestate-pro'),
                    'redirect' => opalmembership_get_membership_page_uri(['warning=1']),
                ]);
        }

        return;
    }

    /**
     * Check any action while editing page
     */
    public static function check_edit_post() {
        return true;
    }

    /**
     * Check permission to allow creating any property. The package is not valid, it is automatic redirect to membership page.
     */
    public static function check_add_post() {

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $has = opalesate_check_has_add_listing($user_id);
        if ($has == false) {
            wp_redirect(opalmembership_get_membership_page_uri(['warning=2']));
            exit;
        }
    }

    /**
     * Display membership warning at top of submission form.
     */
    public static function check_membership_validation_message() {

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            return;
        }

        echo opalestate_load_template_path('parts/membership-warning', ['user_id' => $user_id]);

    }

    /**
     * Hooked method to display more information about actived package.
     */
    public static function render_membership_summary($package_id = 0, $payment_id = 0) {

        global $current_user;

        wp_get_current_user();
        $user_id = $current_user->ID;

        $current_listings         = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_listings', true);
        $curent_featured_listings = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_featured_listings', true);

        ///
        $pack_listings           = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'package_listings', true);
        $pack_featured_listings  = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'package_featured_listings', true);
        $pack_unlimited_listings = get_post_meta($package_id, OPALMEMBERSHIP_PACKAGES_PREFIX . 'unlimited_listings', true);
        $unlimited_listings      = $pack_unlimited_listings == 'on' ? 0 : 1;
        ///

        $output = '';
        if ($unlimited_listings == 1 && $package_id > 0) {
            $output .= '<li><span>' . __('(Package) Listings Included:', 'opalestate-pro') . '</span> ' . __('Unlimited', 'opalestate-pro') . '</span></li>';
            $output .= '<li><span>' . __('(Package) Featured Included:', 'opalestate-pro') . '</span> ' . __('Unlimited', 'opalestate-pro') . '</li>';
        } else {
            if ($package_id > 0) {
                $output .= '<li><span>' . __('(Package) Listings Included:', 'opalestate-pro') . '</span> ' . $pack_listings . '</span></li>';
                $output .= '<li><span>' . __('(Package) Featured Included:', 'opalestate-pro') . '</span> ' . $pack_featured_listings . '</li>';
            }
            $output .= '<li><span>' . __('Listings Remaining:', 'opalestate-pro') . '</span> <span class="text-primary">' . $current_listings . '</span></li>';
            $output .= '<li><span>' . __('Featured Remaining:', 'opalestate-pro') . '</span>  <span class="text-primary">' . $curent_featured_listings . '</span></li>';
        }

        echo $output;
    }

    public static function membership_username_actions($actions, $items) {
        $actions['edit'] = sprintf('<a href="%s">%s</a>', get_edit_post_link(0), esc_html__('Edit', 'opalestate-pro'));

        return $actions;
    }

    /**
     * Hook in and add a metabox to add fields to the user profile pages
     */
    public static function register_user_package_metabox() {

        if (!defined("OPALMEMBERSHIP_USER_PREFIX_") || !current_user_can('manage_options')) {
            return;
        }

        $prefix = OPALMEMBERSHIP_USER_PREFIX_;
        $fields = [];


        foreach ($fields as $field) {
            $cmb_user->add_field($field);
        }
        $fields = [];
        $date   = null;

        $current_user = wp_get_current_user();

        if ((isset($_GET['user_id']) && $_GET['user_id'])) {
            $user_id = (int)$_GET['user_id'];
        } else {
            $user_id = get_current_user_id();
        }

        $date = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_expired', true);

        /**
         * Metabox for the user profile screen
         */
        $cmb_user = new_cmb2_box([
            'id'               => $prefix . 'package',
            'title'            => esc_html__('Membership Package', 'opalestate-pro'), // Doesn't output for user boxes
            'object_types'     => ['user'], // Tells CMB2 to use user_meta vs post_meta
            'show_names'       => true,
            'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
        ]);

        $fields[] = [
            'name'        => esc_html__('Package', 'opalestate-pro'),
            'id'          => $prefix . 'package_id',
            'type'        => 'text',
            'attributes'  => [
                'type'    => 'number',
                'pattern' => '\d*',
                'min'     => 0,
            ],
            'std'         => '1',
            'description' => esc_html__('Set package ID with -1 as free package.', 'opalestate-pro'),
            'before_row'  => '<hr><h3> ' . __('Membership Information', 'opalestate-pro') . ' </h3>',
        ];


        $fields[] = [
            'name'        => esc_html__('Number Of Properties', 'opalestate-pro'),
            'id'          => $prefix . 'package_listings',
            'type'        => 'text',
            'attributes'  => [
                'type'    => 'number',
                'pattern' => '\d*',
                'min'     => 0,
            ],
            'std'         => '1',
            'description' => esc_html__('Number of properties with this package. If not set it will be unlimited.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Number Of Featured Properties', 'opalestate-pro'),
            'id'          => $prefix . 'package_featured_listings',
            'type'        => 'text',
            'attributes'  => [
                'type'    => 'number',
                'pattern' => '\d*',
                'min'     => 0,
            ],
            'std'         => '1',
            'description' => esc_html__('Number of properties can make featured with this package.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Expired', 'opalestate-pro'),
            'id'          => $prefix . 'package_expired_date',
            'type'        => 'text_date',
            'default'     => $date,
            'std'         => '1',
            'description' => esc_html__('Show expired time in double format.', 'opalestate-pro'),
        ];

        $fields[] = [
            'name'        => esc_html__('Expired', 'opalestate-pro'),
            'id'          => $prefix . 'package_expired',
            'type'        => 'text',
            'std'         => '1',
            'description' => esc_html__('Show expired time in double format.', 'opalestate-pro'),
        ];


        foreach ($fields as $field) {
            $cmb_user->add_field($field);
        }
        // }
    }

    public static function on_update_user($user_id) {
        if ($user_id) {
            $prefix = OPALMEMBERSHIP_USER_PREFIX_;
            $field  = $prefix . 'package_expired_date';
            if (isset($_POST[$field]) && !empty($_POST[$field])) {
                $expired_time                       = strtotime($_POST[$field]);
                $_POST[$prefix . 'package_expired'] = $expired_time;
                update_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'package_expired', $expired_time);
            }
        }
    }

    /**
     * @param $user_id
     * @param $post_id
     */
    public static function update_property_expired_time($post_id) {
        $post    = get_post($post_id);
        $user_id = $post->post_author;

        $activated = get_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'expired_activated', true);

        if (!$activated) {
            static::handle_property_expired_time($user_id, $post_id);
        }
    }

    public static function handle_property_expired_time($user_id, $post_id) {
        $is_valid = Opalmembership_User::is_membership_valid($user_id);

        if (!$is_valid) {
            return;
        }

        $enable_property_expired = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'enable_property_expired', true);
        $post_status             = get_post_status($post_id);

        if ($enable_property_expired && 'publish' == $post_status) {
            $property_duration      = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'property_duration', true);
            $property_duration_unit = get_user_meta($user_id, OPALMEMBERSHIP_USER_PREFIX_ . 'property_duration_unit', true);

            $expired_time = time() + static::get_expiration_unit_time($property_duration, $property_duration_unit);

            update_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'expired_time', $expired_time);
            update_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'expired_activated', 1);
        }
    }

    /**
     * @param $duration
     * @param $unit
     * @return float|int
     */
    public static function get_expiration_unit_time($duration, $unit) {
        if (!($duration = absint($duration))) {
            $duration = 1;
        }

        switch ($unit) {
            case 'day':
                $seconds = 60 * 60 * 24;
                break;
            case 'week':
                $seconds = 60 * 60 * 24 * 7;
                break;
            case 'month':
                $seconds = 60 * 60 * 24 * 30;
                break;
            case 'year':
                $seconds = 60 * 60 * 24 * 365;
                break;
        }

        return $seconds * $duration;
    }
}

OpalEstate_Membership::init();
