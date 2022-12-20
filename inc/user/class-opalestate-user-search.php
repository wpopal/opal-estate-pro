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

class OpalEstate_User_Search {

    /**
     *
     */
    protected $user_id = 0;

    /**
     *
     */
    public static function get_instance() {
        static $_instance;
        if (!$_instance) {
            $_instance = new self();
        }

        return $_instance;
    }

    /**
     *
     */
    public function __construct() {
        add_action('init', [$this, 'init']);
    }

    /**
     * Set values when user logined in system
     */
    public function init() {

        global $current_user;
        wp_get_current_user();

        $this->user_id = $current_user->ID;

        add_filter('opalestate_management_user_menu', [$this, 'dashboard_menu']);
        add_action('wp_ajax_opalestate_ajx_save_search', [$this, 'do_save']);
        add_action('wp_ajax_nopriv_opalestate_ajx_save_search', [$this, 'do_save']);

        add_shortcode('opalestate_user_saved_search', [$this, 'savedsearch_page']);

        add_filter('opalestate_user_content_saved_search_page', [$this, 'savedsearch_page']);
    }

    /**
     *
     */
    public function get_search_by_code($code) {

        global $wpdb;

        $query = " SELECT * FROM " . $wpdb->prefix . "opalestate_usersearch WHERE code like %s  ";

        $items = $wpdb->get_results($wpdb->prepare($query, $code));

        if (isset($items[0])) {
            return $items[0];
        }

        return false;
    }

    /**
     *
     */
    public function has_existed($params) {
        return $this->get_search_by_code(md5($params));
    }

    /**
     *
     */
    public function insert($data) {
        global $wpdb;

        $args = [
            'name'    => '',
            'params'  => '',
            'code'    => '',
            'user_id' => $this->user_id,
        ];

        $args         = array_merge($args, $data);
        $args['code'] = md5($data['params']);

        $id = $wpdb->insert($wpdb->prefix . 'opalestate_usersearch', $args);

        return $id;
    }

    /**
     *
     */
    public static function install() {
        try {
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'opalestate_usersearch' . ' (
						id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						name VARCHAR(255),
						params VARCHAR(255),
						code VARCHAR(255),
						user_id INT(11) DEFAULT 0
					) ' . $charset_collate;
            dbDelta($sql);

        } catch (Exception $e) {

        }
    }

    /**
     *
     */
    public function do_save() {
        if ($this->user_id > 0 && isset($_POST['params']) && isset($_POST['name']) && !empty($_POST['name']) && !empty($_POST['params'])) {
            if (!$this->has_existed($_POST['params'])) {
                $this->insert(['name' => sanitize_text_field($_POST['name']), 'params' => $_POST['params']]);
                $result = ['status' => true, 'message' => esc_html__('Saved this search successful.', 'opalestate-pro')];
            } else {
                $result = ['status' => false, 'message' => esc_html__('You saved this search', 'opalestate-pro')];
            }
        } else {
            $result = ['status' => false, 'message' => esc_html__('Please sign in to save this search.', 'opalestate-pro')];
        }

        echo json_encode($result);

        die;
    }

    /**
     *
     */
    public function do_delete($id) {

        global $wpdb;
        if ($this->user_id) {
            $wpdb->delete($wpdb->prefix . "opalestate_usersearch", ['id' => $id, 'user_id' => $this->user_id], ['%d']);
        }
    }

    /**
     *
     */
    public function get_list() {

        global $wpdb;

        $query = " SELECT * FROM " . $wpdb->prefix . "opalestate_usersearch where user_id=" . $this->user_id;

        return $wpdb->get_results($query);
    }

    /**
     *
     */
    public function is_saved() {

    }

    /**
     *
     */
    public function dashboard_menu($menu) {
        if (opalestate_current_user_can_access_dashboard_page('savedsearch') && 'on' === opalestate_get_option('enable_dashboard_savedsearch', 'on')) {
            $menu['savedsearch'] = [
                'icon'  => 'fa fa-search',
                'link'  => 'saved_search',
                'title' => esc_html__('Saved Search', 'opalestate-pro'),
                'id'    => 0,
            ];
        }

        return $menu;
    }

    /**
     *
     */
    public function savedsearch_page() {
        if (isset($_GET['doaction']) && $_GET['doaction'] == 'delete' && isset($_GET['id'])) {
            $this->do_delete(absint($_GET['id']));
        }

        return opalestate_load_template_path('user-search/content-savedsearch');
    }

    /**
     *
     */
    public function render_button() {
        echo opalestate_load_template_path('user-search/render-form');
    }
}

if (opalestate_options('enable_saved_usersearch', 'on') == 'on') {
    OpalEstate_User_Search::get_instance();
}
