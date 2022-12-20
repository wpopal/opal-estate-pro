<?php
/**
 * OpalEstate_User_Request_Viewing
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * OpalEstate_User_Message Class
 *
 * @since 1.0.0
 */
class OpalEstate_User_Request_Viewing {

    /**
     * @var int
     */
    protected $user_id = 0;

    /**
     * Gets types.
     *
     * @return array
     */
    public function get_types() {
        return [
            'request_view' => '',
            'contact'      => '',
            'send_equiry'  => '',
        ];
    }

    /**
     * OpalEstate_User_Request_Viewing constructor.
     */
    public function __construct() {
        add_action('init', [$this, 'init']);

        /// process ajax send message
        add_action('wp_ajax_send_email_request_reviewing', [$this, 'process_send_email']);
        add_action('wp_ajax_nopriv_send_email_request_reviewing', [$this, 'process_send_email']);
    }

    /**
     * Set values when user logined in system
     */
    public function init() {
        global $current_user;

        wp_get_current_user();
        $this->user_id = $current_user->ID;
    }

    /**
     * get_member_email_data
     *
     * @param $post_id
     * @return array
     */
    public function get_member_email_data($post_id) {
        return opalestate_get_member_email_data($post_id);
    }

    /**
     * Process send email.
     */
    public function process_send_email() {
        if (wp_verify_nonce($_POST['message_action'], 'property-request-view')) {
            $post   = $_POST;
            $member = $this->get_member_email_data(absint($post['post_id']));
            $user   = get_userdata($this->user_id);
            $output = [
                'subject'        => isset($subject) && $subject ? esc_html($subject) : '',
                'name'           => $user->display_name ? esc_html($user->display_name) : esc_html($user->user_nicename),
                'receiver_email' => sanitize_email($member['receiver_email']),
                'receiver_id'    => sanitize_text_field($member['receiver_id']),
                'sender_id'      => get_current_user_id(),
                'sender_email'   => sanitize_email($post['email']),
                'phone'          => sanitize_text_field($post['phone']),
                'message'        => esc_html($post['message']),
                'schedule_time'  => sanitize_text_field($post['time']),
                'schedule_date'  => sanitize_text_field($post['date']),
                'post_id'        => absint($post['post_id']),
                'email'          => $user->user_email,
            ];

            $this->insert($output);

            // insert data into request_reviewing form
            do_action('opalestate_send_email_request_reviewing', $output);
        }

        $return = ['status' => false, 'msg' => esc_html__('Unable to send a message.', 'opalestate-pro')];
        echo json_encode($return);
        die();
    }

    public function insert($data) {

    }

    /**
     * Install.
     */
    public static function install() {
        try {
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'opalestate_message' . ' (
									  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
									  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
									  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
									  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
									  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
									  `sender_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
									  `sender_id` int(11) DEFAULT NULL,
									  `created` datetime NOT NULL,
									  `receiver_id` int(11) NOT NULL,
									  `post_id` int(11) NOT NULL,
									  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
									  `isread` tinyint(1) NOT NULL,
									  PRIMARY KEY  (id)
					) ' . $charset_collate;
            dbDelta($sql);

        } catch (Exception $e) {

        }
    }
}

new OpalEstate_User_Request_Viewing();
