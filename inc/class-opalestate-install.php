<?php
if (!defined('ABSPATH')) {
    exit();
}

class Opalestate_Install {
    /**
     * Init.
     */
    public static function init() {
        add_filter('cron_schedules', [__CLASS__, 'cron_schedules']);
    }

    /**
     * Install Opalestate.
     */
    public static function install() {
        if (!is_blog_installed()) {
            return;
        }

        // Check if we are not already running this routine.
        if ('yes' === get_transient('opalestate_installing')) {
            return;
        }

        // If we made it till here nothing is running yet, lets set the transient now.
        set_transient('opalestate_installing', 'yes', MINUTE_IN_SECONDS * 10);

        static::create_options();
        static::create_tables();
        static::create_roles();
        static::setup_environment();
        static::create_cron_jobs();
        static::update_opalestate_version();

        if (function_exists('opalmembership_install')) {
            // opalmembership_install();
        }

        // Add the transient to redirect.
        set_transient('_opalestate_activation_redirect', true, 30);

        delete_transient('opalestate_installing');

        // Remove rewrite rules and then recreate rewrite rules.
        flush_rewrite_rules();

        do_action('opalestate_installed');
    }

    /**
     * Setup Opalestate environment - post types, taxonomies, endpoints.
     */
    private static function setup_environment() {
        Opalestate_PostType_Property::definition();
        Opalestate_PostType_Agent::definition();
        Opalestate_PostType_Agency::definition();
    }

    /**
     * Set up the database tables which the plugin needs to function.
     */
    private static function create_tables() {
        OpalEstate_User_Search::install();
        OpalEstate_User_Message::install();
        OpalEstate_User_Request_Viewing::install();
        Opalestate_API::install();
    }

    /**
     * Create roles and capabilities.
     */
    public static function create_roles() {
        $roles = new Opalestate_Roles();
        $roles->add_roles();
        $roles->add_caps();
    }

    /**
     * Default options.
     *
     * Sets up the default options used on the settings page.
     */
    private static function create_options() {
        global $opalestate_options;

        // Add Upgraded From Option
        $current_version = get_option('opalestate_version');
        if ($current_version) {
            update_option('opalestate_version_upgraded_from', $current_version);
        }

        // Setup some default options
        $options = [];

        //Fresh Install? Setup Test Mode, Base Country (US), Test Gateway, Currency
        if (empty($current_version)) {
            $options['test_mode']           = 1;
            $options['currency']            = 'USD';
            $options['currency_position']   = 'before';
            $options['measurement_unit']    = 'sqft';
            $options['google_map_api_keys'] = 'AIzaSyCfMVNIa7khIqYHCw6VBn8ShUWWm4tjbG8';
            $options['from_name']           = get_bloginfo('name');
            $options['from_email']          = get_bloginfo('admin_email');
            $options['message_log']         = 1;

            $options[OPALESTATE_PROPERTY_PREFIX . 'bedrooms_opt']    = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'parking_opt']     = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'bathrooms_opt']   = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'areasize_opt']    = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'price_opt']       = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'bedrooms_opt_v']  = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'parking_opt_v']   = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'bathrooms_opt_v'] = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'areasize_opt_v']  = 'on';
            $options[OPALESTATE_PROPERTY_PREFIX . 'price_opt_v']     = 'on';

            $options['enable_single_request_viewing']  = 'on';
            $options['enable_single_amenities']        = 'on';
            $options['enable_single_facilities']       = 'on';
            $options['enable_single_attachments']      = 'on';
            $options['enable_single_video']            = 'on';
            $options['enable_single_map']              = 'on';
            $options['enable_single_nearby']           = 'on';
            $options['enable_single_walkscores']       = 'on';
            $options['enable_single_apartments']       = 'on';
            $options['enable_single_floor_plans']      = 'on';
            $options['enable_single_views_statistics'] = 'on';
            $options['single_views_statistics_limit']  = 8;
            $options['enable_single_author_box']       = 'on';
            $options['enable_single_enquire_form']     = 'on';
            $options['enable_single_mortgage']         = 'on';

            $options['enable_property_reviews'] = 'on';
            $options['enable_agency_reviews']   = 'on';
            $options['enable_agent_reviews']    = 'on';

            $options['enable_customer_new_submission'] = 'on';
            $options['enable_admin_new_submission']    = 'on';
            $options['enable_approve_property_email']  = 'on';

            $options['admin_approve']                     = 'on';
            $options['enable_submission_tab_media']       = 'on';
            $options['enable_submission_tab_location']    = 'on';
            $options['enable_submission_tab_amenities']   = 'on';
            $options['enable_submission_tab_facilities']  = 'on';
            $options['enable_submission_tab_apartments']  = 'on';
            $options['enable_submission_tab_floor_plans'] = 'on';
        }

        // Checks if the Success Page option exists AND that the page exists
        if (!get_post(opalestate_get_option('user_management_page'))) {
            // Purchase Confirmation (Success) Page
            $profile_page = wp_insert_post(
                [
                    'post_title'     => esc_html__('User Dashboard Page', 'opalestate-pro'),
                    'post_content'   => '',
                    'post_status'    => 'publish',
                    'post_author'    => 1,
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                    'page_template'  => 'user-management.php',
                ]
            );

            // Store our page IDs
            $options['user_management_page'] = $profile_page;
        }

        // Checks if the Success Page option exists AND that the page exists
        if (!get_post(opalestate_get_option('user_myaccount_page'))) {
            $saved_link_page = wp_insert_post(
                [
                    'post_title'     => esc_html__('My Account', 'opalestate-pro'),
                    'post_content'   => esc_html__('[opalestate_myaccount]', 'opalestate-pro'),
                    'post_status'    => 'publish',
                    'post_author'    => 1,
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                ]
            );

            // Store our page IDs
            $options['user_myaccount_page'] = $saved_link_page;
        }

        // Checks if the Success Page option exists AND that the page exists
        if (!get_post(opalestate_get_option('submission_page'))) {
            // Purchase Confirmation (Success) Page
            $submission_page = wp_insert_post(
                [
                    'post_title'     => esc_html__('Property Submission Page', 'opalestate-pro'),
                    'post_content'   => esc_html__('[opalestate_submission]', 'opalestate-pro'),
                    'post_status'    => 'publish',
                    'post_author'    => 1,
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                ]
            );

            // Store our page IDs
            $options['submission_page'] = $submission_page;
        }

        // Checks if the Success Page option exists AND that the page exists
        // if ( ! get_post( opalestate_get_option( 'search_map_properties_page' ) ) ) {
        // 	// Purchase Confirmation (Success) Page
        // 	$search_map_properties_page = wp_insert_post(
        // 		[
        // 			'post_title'     => esc_html__( 'Search Map Properties Page', 'opalestate-pro' ),
        // 			'post_content'   => esc_html__( '[opalestate_search_map_properties]', 'opalestate-pro' ),
        // 			'post_status'    => 'publish',
        // 			'post_author'    => 1,
        // 			'post_type'      => 'page',
        // 			'comment_status' => 'closed',
        // 			'page_template'  => 'fullwidth-page.php',
        // 		]
        // 	);
        //
        // 	// Store our page IDs
        // 	$options['search_map_properties_page'] = $search_map_properties_page;
        // }

        // Populate some default values
        update_option('opalestate_settings', array_merge($opalestate_options, $options));

        // Add a temporary option to note that Give pages have been created
        set_transient('_opalestate_installed', $options, 30);
    }

    /**
     * Update Opalestate version to current.
     */
    private static function update_opalestate_version() {
        update_option('opalestate_version', OPALESTATE_VERSION);
    }

    /**
     * Add more cron schedules.
     *
     * @param array $schedules List of WP scheduled cron jobs.
     *
     * @return array
     */
    public static function cron_schedules($schedules) {
        $interval = opalestate_get_option('schedule', 0);

        $schedules['opalestate_corn'] = [
            'display'  => __('Opal Estate Pro Clean Up Interval', 'opalestate-pro'),
            'interval' => $interval,
        ];

        return $schedules;
    }

    /**
     * Create cron jobs (clear them first).
     */
    public static function create_cron_jobs() {
        wp_clear_scheduled_hook('opalestate_corn');
        wp_clear_scheduled_hook('opalestate_clean_update');

        if (!wp_next_scheduled('opalestate_clean_update')) {
            wp_schedule_event(time(), 'opalestate_corn', 'opalestate_clean_update');
        }
    }
}

/**
 * Install user roles on sub-sites of a network
 *
 * Roles do not get created when Give is network activation so we need to create them during admin_init
 *
 * @return void
 * @since 1.0
 */
function opalestate_install_roles_on_network() {
    global $wp_roles;

    if (!is_object($wp_roles)) {
        return;
    }

    if (!array_key_exists('opalestate_manager', $wp_roles->roles)) {
        $roles = new Opalestate_Roles;
        $roles->add_roles();
        $roles->add_caps();
    } else {
        // remove_role( 'opalestate_manager' );
        // remove_role( 'opalestate_manager' );
        // $roles = new Opalestate_Roles;
        // $roles->remove_caps();
    }
}

add_action('admin_init', 'opalestate_install_roles_on_network');
