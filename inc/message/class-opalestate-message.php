<?php
/**
 * OpalEstate_User_Message
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

/**
 * OpalEstate_User_Message Class
 *
 * @since 1.0.0
 */
class OpalEstate_User_Message {

    /**
     * ID of current user id
     *
     * @return user_id
     */
    protected $user_id = 0;

    /**
     * Gets types.
     *
     * @return boolean $is_log
     */
    protected $is_log;

    /**
     * Gets a instance of this object.
     *
     * @return OpalEstate_User_Message
     */
    public static function get_instance() {
        static $_instance;
        if (!$_instance) {
            $_instance = new self();
        }

        return $_instance;
    }

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
     *
     */
    public function __construct() {

        add_action('init', [$this, 'init']);

        /// process ajax send message
        add_action('wp_ajax_send_email_contact', [$this, 'process_send_email']);
        add_action('wp_ajax_nopriv_send_email_contact', [$this, 'process_send_email']);

        // process ajax
        add_action('wp_ajax_send_email_contact_reply', [$this, 'process_send_reply_email']);
        add_action('wp_ajax_nopriv_send_email_contact_reply', [$this, 'process_send_reply_email']);

        add_filter('opalestate_user_content_messages_page', [$this, 'render_user_content_page']);
    }

    /**
     * Set values when user logined in system
     */
    public function init() {

        global $current_user;
        wp_get_current_user();

        $this->user_id = $current_user->ID;
        $this->is_log  = opalestate_get_option('message_log');
    }

    /**
     * Set values when user logined in system
     */
    public function send_equiry($post, $member) {
        $default = [
            'send_equiry_name' => '',
            'action'           => '',
            'post_id'          => '',
            'sender_id'        => '',
            'email'            => '',
            'phone'            => '',
            'message'          => '',
            'message_action'   => '',
        ];

        $post                  = array_merge($default, $post);
        $post['property_link'] = (int)$post['post_id'] ? get_permalink($post['post_id']) : get_home_url();
        $post['receive_name']  = isset($member['name']) ? $member['name'] : '';
        $subject               = html_entity_decode(esc_html__('You got a message', 'opalestate-pro'));
        $post['receiver_name'] = $member['receiver_name'];

        $property_id = absint($post['post_id']);
        $property    = get_post($property_id);

        $output = [
            'subject'            => $subject,
            'name'               => isset($post['name']) ? $post['name'] : '',
            'receiver_email'     => $member['receiver_email'],
            'receiver_id'        => $member['receiver_id'],
            'sender_id'          => get_current_user_id(),
            'sender_email'       => $post['email'],
            'property_name'      => $property->post_title,
            'property_link'      => $property_id ? get_permalink($property_id) : get_home_url(),
            'property_edit_link' => get_edit_post_link($property_id),
            'email'              => $post['email'],
            'phone'              => $post['phone'],
            'message'            => $post['message'],
            'post_id'            => $post['post_id'],
            'type'               => 'send_enquiry',
        ];

        if ($output['sender_id'] == $output['receiver_id']) {
            // return false;
        }

        return $output;
    }

    /**
     * Set values when user logined in system
     */
    public function send_contact($post) {

        $member = get_post($post['post_id']);

        if ($member->post_type == 'opalestate_agent') {
            $receiver_id = get_post_meta($member->ID, OPALESTATE_AGENT_PREFIX . 'user_id', true);
            $email       = get_post_meta($member->ID, OPALESTATE_AGENT_PREFIX . 'email', true);
        } else {
            $receiver_id = get_post_meta($member->ID, OPALESTATE_AGENCY_PREFIX . 'user_id', true);
            $email       = get_post_meta($member->ID, OPALESTATE_AGENCY_PREFIX . 'email', true);
        }

        $member = [
            'receiver_email' => $email,
            'receiver_name'  => $member->post_title,
            'receiver_id'    => $receiver_id,
        ];

        $default = [
            'send_equiry_name' => '',
            'action'           => '',
            'post_id'          => '',
            'sender_id'        => '',
            'email'            => '',
            'name'             => '',
            'phone'            => '',
            'message'          => '',
            'message_action'   => '',
        ];
        $post    = array_merge($default, $post);

        $post['link']         = (int)$post['post_id'] ? get_permalink($post['post_id']) : get_home_url();
        $post['receive_name'] = $member['name'];

        $subject = html_entity_decode(esc_html__('You got a message contact', 'opalestate-pro'));

        $post['receiver_name'] = $member['receiver_name'];

        $output = [
            'subject'        => $subject,
            'receiver_email' => $member['receiver_email'],
            'receiver_id'    => $member['receiver_id'],
            'sender_id'      => get_current_user_id(),
            'sender_email'   => $post['email'],
            'name'           => $post['name'],
            'phone'          => $post['phone'],
            'message'        => $post['message'],
            'post_id'        => $post['post_id'],
            'type'           => 'send_contact',
        ];

        if ($output['sender_id'] == $output['receiver_id']) {
            // return false;
        }

        return $output;
    }

    /**
     * Set values when user logined in system
     */
    public function get_member_email_data($post_id) {
        return opalestate_get_member_email_data($post_id);
    }

    /**
     * Set values when user logined in system
     */
    public function process_send_reply_email() {

        if (isset($_POST) && $this->is_log) {
            $id      = 2;
            $message = $this->get_message(intval($_POST['message_id']));


            if ($message) {

                $data = [
                    'message_id'  => $message->id,
                    'sender_id'   => $this->user_id,
                    'receiver_id' => $message->sender_id,
                    'message'     => sanitize_text_field($_POST['message']),
                    'created'     => current_time('mysql', 1),
                ];

                $id = $this->insert_reply($data);

                $reply = $this->get_reply($id);

                $data['data'] = [
                    'created' => $reply->created,
                    'message' => $reply->message,
                    'avatar'  => OpalEstate_User::get_author_picture($message->sender_id),
                ];
                // send email for user to inbox email.
                do_action('opalestate_send_email_notifycation', $data);
                $return = [
                    'status'  => true,
                    'msg'     => esc_html__('Email Sent successful', 'opalestate-pro'),
                    'heading' => esc_html__('Sending Message', 'opalestate-pro'),
                ];
            }
        } else {
            $return = ['status' => false, 'msg' => esc_html__('Unable to send a message.', 'opalestate-pro'), 'heading' => esc_html__('Sending Message', 'opalestate-pro')];
        }


        echo json_encode($return);
        die();
    }

    /**
     * Process send email.
     */
    public function process_send_email() {
        do_action('opalestate_process_send_email_before');
        if (isset($_POST['type']) && $_POST['type']) {
            $content = [];
            switch (trim($_POST['type'])) {
                case 'send_equiry':
                    if (wp_verify_nonce($_POST['message_action'], 'send-enquiry-form')) {
                        $member  = $this->get_member_email_data(absint($_POST['post_id']));
                        $content = $this->send_equiry($_POST, $member);
                    }
                    break;
                case 'send_contact':
                    if (wp_verify_nonce($_POST['message_action'], 'send-contact-form')) {
                        $content = $this->send_contact($_POST);
                    }
                    break;
                default:
                    break;
            }

            if ($content) {
                // only save in db for user only
                if ($content['receiver_id'] > 0 && $this->is_log) {
                    $this->insert($content);
                }

                // Send email for user to inbox email.
                do_action('opalestate_send_email_notifycation', $content);
            }
        }

        $return = ['status' => false, 'msg' => esc_html__('Unable to send a message.', 'opalestate-pro')];

        echo json_encode($return);
        die();
    }

    /**
     *
     */
    public function insert($data) {
        global $wpdb;

        $args = [
            'subject'      => '',
            'message'      => '',
            'sender_email' => '',
            'phone'        => '',
            'sender_id'    => '',
            'created'      => current_time('mysql', 1),
            'receiver_id'  => '',
            'post_id'      => '',
            'type'         => '',
        ];

        foreach ($args as $key => $value) {
            if (isset($data[$key])) {
                $args[$key] = $data[$key];
            }
        }

        $id = $wpdb->insert($wpdb->prefix . 'opalestate_message', $args);

        return $wpdb->insert_id;
    }

    public function insert_reply($data) {
        global $wpdb;

        $args = [
            'message_id'  => '',
            'message'     => '',
            'sender_id'   => '',
            'created'     => current_time('mysql', 1),
            'receiver_id' => '',
        ];

        foreach ($args as $key => $value) {
            if (isset($data[$key])) {
                $args[$key] = $data[$key];
            }
        }

        $id = $wpdb->insert($wpdb->prefix . 'opalestate_message_reply', $args);

        return $wpdb->insert_id;
    }

    public function get_reply($id) {
        global $wpdb;

        $query = " SELECT * FROM " . $wpdb->prefix . "opalestate_message_reply where id=" . (int)$id;
        $reply = $wpdb->get_row($query);

        return $reply;
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

            ///

            $sql = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'opalestate_message_reply' . ' (
									      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
										  `message_id` int(11) NOT NULL,
										  `sender_id` int(11) NOT NULL,
										  `message` text NOT NULL,
										  `created` datetime NOT NULL,
										  `receiver_id` int(11) NOT NULL,
										  PRIMARY KEY  (id)
					) ' . $charset_collate;
            dbDelta($sql);


        } catch (Exception $e) {

        }
    }


    /**
     *
     */
    public function do_delete($id) {

        global $wpdb;
        if ($this->user_id) {
            $wpdb->delete($wpdb->prefix . "opalestate_message", ["id" => $id, 'user_id' => $this->user_id], ['%d']);
        }
    }

    /**
     *
     */
    public function get_list($args = []) {

        global $wpdb;

        $default = [
            'cpage'          => 1,
            'items_per_page' => 3,
        ];

        $args           = array_merge($default, $args);
        $items_per_page = $args['items_per_page'];
        $offset         = ($args['cpage'] * $items_per_page) - $items_per_page;

        $query = " SELECT * FROM " . $wpdb->prefix . "opalestate_message where receiver_id=" . $this->user_id . ' OR sender_id=' . $this->user_id;
        $query .= ' ORDER BY id DESC LIMIT ' . $offset . ', ' . $items_per_page;

        return $wpdb->get_results($query);
    }

    public function get_total() {

        global $wpdb;
        $query = " SELECT count(1) as total FROM " . $wpdb->prefix . "opalestate_message where receiver_id=" . $this->user_id . ' OR sender_id=' . $this->user_id;

        return $wpdb->get_var($query);
    }

    /**
     *
     */
    public function get_message($id) {

        global $wpdb;

        $query   = " SELECT * FROM " . $wpdb->prefix . "opalestate_message where ( sender_id=" . $this->user_id . " OR receiver_id=" . $this->user_id . ') and id=' . (int)$id;
        $message = $wpdb->get_results($query);

        if (isset($message[0])) {
            return $message[0];
        }

        return [];
    }

    public function get_replies($id) {

        global $wpdb;

        $query    = " SELECT * FROM " . $wpdb->prefix . "opalestate_message_reply where message_id=" . (int)$id . ' ORDER BY created ';
        $messages = $wpdb->get_results($query);

        return $messages;
    }

    /**
     *
     */
    public function is_saved() {

    }

    /**
     *
     */
    public function get_equiry_form_fields($msg = '') {

        $prefix = '';

        $id           = '';
        $sender_id    = '';
        $post_id      = get_the_ID();
        $email        = '';
        $current_user = wp_get_current_user();
        $name         = '';

        if (0 != $current_user->ID) {
            $email     = $current_user->user_email;
            $name      = $current_user->user_firstname . ' ' . $current_user->user_lastname;
            $sender_id = $current_user->ID;
        }

        $fields = [

            [
                'id'          => "type",
                'name'        => esc_html__('Type', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => 'send_equiry',
                'description' => "",
            ],
            [
                'id'          => "post_id",
                'name'        => esc_html__('Property ID', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => $post_id,
                'description' => "",
            ],

            [
                'id'          => "sender_id",
                'name'        => esc_html__('Sender ID', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => $sender_id,
                'description' => "",
            ],

            [
                'id'          => "{$prefix}name",
                'name'        => esc_html__('Name', 'opalestate-pro'),
                'type'        => 'text',
                'before_row'  => '',
                'required'    => 'required',
                'default'     => $name,
                'description' => "",
            ],
            [
                'id'          => "{$prefix}email",
                'name'        => esc_html__('Email', 'opalestate-pro'),
                'type'        => 'text',
                'default'     => $email,
                'description' => "",
                'required'    => 'required',
            ],

            [
                'id'          => "{$prefix}phone",
                'name'        => esc_html__('Phone', 'opalestate-pro'),
                'type'        => 'text',
                'description' => "",
                'required'    => 'required',
            ],

            [
                'id'          => "{$prefix}message",
                'name'        => esc_html__('Message', 'opalestate-pro'),
                'type'        => 'textarea',
                'description' => "",
                'default'     => $msg,
                'required'    => 'required',
            ],

        ];

        return $fields;
    }

    public function get_reply_form_fields() {
        $prefix = '';
        $fields = [
            [
                'id'          => "type",
                'name'        => esc_html__('Type', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => 'send_contact',
                'description' => "",
            ],
            [
                'id'          => "{$prefix}message",
                'name'        => esc_html__('Message', 'opalestate-pro'),
                'type'        => 'textarea',
                'description' => "",
                'required'    => 'required',
            ],
        ];

        return $fields;
    }

    /**
     *
     */
    public function get_contact_form_fields($msg = '') {

        $prefix       = '';
        $id           = '';
        $sender_id    = '';
        $post_id      = get_the_ID();
        $email        = '';
        $current_user = wp_get_current_user();
        $name         = '';

        if (0 != $current_user->ID) {
            $email     = $current_user->user_email;
            $name      = $current_user->user_firstname . ' ' . $current_user->user_lastname;
            $sender_id = $current_user->ID;
        }

        $fields = [
            [
                'id'          => "type",
                'name'        => esc_html__('Type', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => 'send_contact',
                'description' => "",
            ],
            [
                'id'          => "post_id",
                'name'        => esc_html__('Property ID', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => $post_id,
                'description' => "",
            ],
            [
                'id'          => "sender_id",
                'name'        => esc_html__('Sender ID', 'opalestate-pro'),
                'type'        => 'hidden',
                'default'     => $sender_id,
                'description' => "",
            ],
            [
                'id'          => "{$prefix}name",
                'name'        => esc_html__('Name', 'opalestate-pro'),
                'type'        => 'text',
                'default'     => $name,
                'required'    => 'required',
                'description' => "",
            ],
            [
                'id'          => "{$prefix}email",
                'name'        => esc_html__('Email', 'opalestate-pro'),
                'type'        => 'text',
                'default'     => $email,
                'description' => "",
                'required'    => 'required',
            ],

            [
                'id'          => "{$prefix}phone",
                'name'        => esc_html__('Phone', 'opalestate-pro'),
                'type'        => 'text',
                'description' => "",
                'required'    => 'required',
            ],

            [
                'id'          => "{$prefix}message",
                'name'        => esc_html__('Message', 'opalestate-pro'),
                'type'        => 'textarea',
                'description' => "",
                'default'     => $msg,
                'required'    => 'required',
            ],

        ];

        return $fields;
    }

    public function get_request_review_form_fields($msg = '') {
        global $wp_query;

        $prefix       = '';
        $id           = '';
        $sender_id    = '';
        $post_id      = $wp_query->post->ID;
        $email        = '';
        $current_user = wp_get_current_user();
        $name         = '';

        if (0 != $current_user->ID) {
            $email     = $current_user->user_email;
            $name      = $current_user->user_firstname . ' ' . $current_user->user_lastname;
            $sender_id = $current_user->ID;
        }

        $fields = [
            [
                'id'      => 'type',
                'name'    => esc_html__('Type', 'opalestate-pro'),
                'type'    => 'hidden',
                'default' => 'send_request_review',
            ],
            [
                'id'      => 'post_id',
                'name'    => esc_html__('Property ID', 'opalestate-pro'),
                'type'    => 'hidden',
                'default' => $post_id,
            ],
            [
                'id'      => 'sender_id',
                'name'    => esc_html__('Sender ID', 'opalestate-pro'),
                'type'    => 'hidden',
                'default' => $sender_id,
            ],
            [
                'id'         => "{$prefix}date",
                'name'       => esc_html__('Schedule', 'opalestate-pro'),
                'type'       => 'date',
                'before_row' => '',
                'required'   => 'required',
            ],
            [
                'id'      => "{$prefix}time",
                'name'    => esc_html__('Time', 'opalestate-pro'),
                'type'    => 'select',
                'options' => opalestate_get_request_viewing_time_list(),
            ],
            [
                'id'       => "{$prefix}phone",
                'name'     => esc_html__('Phone', 'opalestate-pro'),
                'type'     => 'text',
                'required' => 'required',
            ],
            [
                'id'       => "{$prefix}message",
                'name'     => esc_html__('Message', 'opalestate-pro'),
                'type'     => 'textarea',
                'default'  => $msg,
                'required' => 'required',
            ],
        ];

        return $fields;
    }


    public function render_user_content_page() {

        if (isset($_GET['message_id'])) {

            $message = $this->get_message(absint($_GET['message_id']));

            return opalestate_load_template_path('user/read-messages',
                [
                    'message' => $message,
                    'fields'  => $this->get_reply_form_fields(),
                    'replies' => $this->get_replies(absint($_GET['message_id'])),
                ]
            );

        } else {
            return opalestate_load_template_path('user/messages');
        }
    }
}

OpalEstate_User_Message::get_instance();
