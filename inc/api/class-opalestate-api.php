<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Opalestate_API
 *
 * @package    Opalestate
 */
class Opalestate_API {
    /**
     * The unique identifier of this plugin.
     *
     * @var string $base The string used to uniquely identify this plugin.
     */
    public $base = 'estate-api';

    public function __construct() {
        $this->init();

        add_filter('jwt_auth_token_before_dispatch', [$this, 'jwt_auth_token_before_dispatch'], 10, 2);
    }

    /**
     * Registers a new rewrite endpoint for accessing the API
     *
     * @param array $rewrite_rules WordPress Rewrite Rules
     */
    public function init() {
        $this->includes([
            'class-opalestate-admin-api-keys.php',
            'class-opalestate-admin-api-keys-table-list.php',
            'class-opalestate-rest-authentication.php',
            'class-opalestate-base-api.php',
            'v1/property.php',
            'v1/agent.php',
            'v1/agency.php',
            'v1/search-form.php',
            'v1/user.php',
            'v1/terms.php',
            'functions.php',
        ]);

        add_action('rest_api_init', [$this, 'register_resources']);
    }

    /**
     * Registers a new rewrite endpoint for accessing the API
     *
     * @param array $rewrite_rules WordPress Rewrite Rules
     */
    public function add_endpoint($rewrite_rules) {
        add_rewrite_endpoint($this->base, EP_ALL);
    }

    /**
     * Include list of collection files
     *
     * @var array $files
     */
    public function includes($files) {
        foreach ($files as $file) {
            $file = OPALESTATE_PLUGIN_DIR . 'inc/api/' . $file;
            include_once $file;
        }
    }

    /**
     * Registers a new rewrite endpoint for accessing the API
     *
     * @param array $rewrite_rules WordPress Rewrite Rules
     */
    public function register_resources() {
        $api_classes = apply_filters('opalestate_api_classes',
            [
                'Opalestate_Property_Api',
                'Opalestate_Agent_Api',
                'Opalestate_Agency_Api',
                'Opalestate_Search_Form_Api',
                'Opalestate_User_Api',
                'Opalestate_Terms_Api',
            ]
        );

        foreach ($api_classes as $api_class) {
            $api_class = new $api_class();
            $api_class->register_routes();
        }
    }

    /**
     * Add some information to JWT response.
     *
     * @param $data
     * @param $user
     * @return array
     */
    public function jwt_auth_token_before_dispatch($data, $user) {
        $data['user_id']   = $user->data->ID;
        $data['user_role'] = $user->roles;
        $data['avatar']    = opalestate_get_user_meta($user->data->ID, 'avatar');

        return $data;
    }

    /**
     * Create database.
     */
    public static function install() {
        try {
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'opalestate_api_keys' . ' (
						key_id BIGINT UNSIGNED NOT NULL auto_increment,
						user_id BIGINT UNSIGNED NOT NULL,
						description varchar(200) NULL,
						permissions varchar(10) NOT NULL,
						consumer_key char(64) NOT NULL,
						consumer_secret char(43) NOT NULL,
						nonces longtext NULL,
						truncated_key char(7) NOT NULL,
						last_access datetime NULL default null,
						PRIMARY KEY  (key_id),
						KEY consumer_key (consumer_key),
						KEY consumer_secret (consumer_secret)
					) ' . $charset_collate;
            dbDelta($sql);

        } catch (Exception $e) {

        }
    }
}
