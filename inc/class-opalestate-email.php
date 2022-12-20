<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    $package$
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class   OpalMembership_Checkout
 *
 * @version 1.0
 */
class Opalestate_Emails {
    /**
     * init action to automatic send email when user edit or submit a new submission and init setting form in plugin setting of admin
     */
    public static function init() {
        self::load();

        if (is_admin()) {
            add_filter('opalestate_settings_tabs', [__CLASS__, 'setting_email_tab'], 1);
            add_filter('opalestate_registered_emails_settings', [__CLASS__, 'setting_email_fields'], 10, 1);
        }

        if ('on' === opalestate_get_option('enable_customer_new_submission', 'on')) {
            add_action('opalestate_processed_new_submission', [__CLASS__, 'new_submission_email'], 10, 2);
        }

        if ('on' === opalestate_get_option('enable_admin_new_submission', 'on')) {
            add_action('opalestate_processed_new_submission', [__CLASS__, 'admin_new_submission_email'], 15, 2);
        }

        if ('on' === opalestate_get_option('enable_approve_property_email', 'on')) {
            add_action('transition_post_status', [__CLASS__, 'send_email_when_publish_property'], 10, 3);
            add_action('opalestate_processed_approve_publish_property', [__CLASS__, 'approve_publish_property_email'], 10, 1);
        }

        /**
         * Send email when User contact via Enquiry Form and Contact Form
         */
        add_action('opalestate_send_email_notifycation', [__CLASS__, 'send_notifycation']);
        add_action('opalestate_send_email_submitted', [__CLASS__, 'new_submission_email']);
        add_action('opalestate_send_email_submitted', [__CLASS__, 'admin_new_submission_email']);
        add_action('opalestate_send_email_request_reviewing', [__CLASS__, 'send_email_request_reviewing']);
    }

    /**
     * Load.
     */
    public static function load() {
        require_once OPALESTATE_PLUGIN_DIR . 'inc/email/class-opalestate-abs-email-template.php';
        require_once OPALESTATE_PLUGIN_DIR . 'inc/email/class-opalestate-email-notifycation.php';
        require_once OPALESTATE_PLUGIN_DIR . 'inc/email/class-opalestate-request-viewing.php';
        require_once OPALESTATE_PLUGIN_DIR . 'inc/email/class-opalestate-new-submitted.php';
        require_once OPALESTATE_PLUGIN_DIR . 'inc/email/class-opalestate-admin-new-submitted.php';
        require_once OPALESTATE_PLUGIN_DIR . 'inc/email/class-opalestate-approve.php';
    }

    /**
     * Send Email Notifycation with two types: Enquiry or Contact
     */
    public static function send_notifycation($content) {
        $mail = new OpalEstate_Send_Email_Notification();
        $mail->set_type($content);
        $mail->set_args($content);

        $return = self::send_mail_now($mail);

        if (isset($content['data'])) {
            $return['data'] = $content['data'];
        }

        echo json_encode($return);
        die();
    }


    /**
     * send email if agent submit a new property
     */
    public static function new_submission_email($user_id, $post_id) {
        $mail = new OpalEstate_Send_Email_New_Submitted();
        $mail->set_pros($post_id, $user_id);
        $return = self::send_mail_now($mail);
    }

    /**
     * send email if agent submit a new property
     */
    public static function admin_new_submission_email($user_id, $post_id) {
        $mail = new OpalEstate_Send_Email_Admin_New_Submitted();
        $mail->set_pros($post_id, $user_id);
        $return = self::send_mail_now($mail);
    }

    /**
     * Send email to requet viewing a property
     */
    public static function send_email_request_reviewing($content) {
        $mail = new OpalEstate_Send_Email_Request_Reviewing();
        $mail->set_args($content);

        $return = self::send_mail_now($mail);

        echo json_encode($return);
        die();
    }

    /**
     *
     */
    public static function send_mail_now($mail) {
        $from_name  = $mail->from_name();
        $from_email = $mail->from_email();
        $headers    = sprintf("From: %s <%s>\r\n Content-type: text/html", $from_name, $from_email);

        $subject = $mail->get_subject();
        $message = $mail->get_body();

        if ($mail->to_email()) {

            if ($mail->get_cc()) {
                $status = @wp_mail($mail->get_cc(), $subject, $message, $headers);
            }

            $status = @wp_mail($mail->to_email(), $subject, $message, $headers);

            if ($status) {
                return ['status' => true, 'msg' => esc_html__('Message has been successfully sent.', 'opalestate-pro')];
            } else {
                return ['status' => true, 'msg' => esc_html__('Unable to send a message.', 'opalestate-pro')];
            }
        }

        return ['status' => true, 'msg' => esc_html__('Missing some information!', 'opalestate-pro')];
    }

    /**
     *
     */
    public static function send_email_when_publish_property($new_status, $old_status, $post) {
        if (is_object($post)) {
            if ($post->post_type == 'opalestate_property') {
                if ($new_status != $old_status) {
                    if ($new_status == 'publish') {
                        if ($old_status == 'draft' || $old_status == 'pending') {
                            // Send email
                            $post_id   = $post->ID;
                            $author_id = $post->post_author;
                            $author    = get_userdata($author_id);

                            if (!in_array('administrator', $author->roles) && !in_array('opalestate_manager', $author->roles) && !in_array('opalmembership_manager', $author->roles)) {
                                do_action('opalestate_processed_approve_publish_property', $post_id);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * add new tab Email in opalestate -> setting
     */
    public static function setting_email_tab($tabs) {
        $tabs['emails'] = esc_html__('Email', 'opalestate-pro');

        return $tabs;
    }

    public static function newproperty_email_body() {

    }

    public static function approve_email_body() {

    }

    /**
     * render setting email fields with default values
     */
    public static function setting_email_fields($fields) {
        $enquire_list_tags = '<div>
				<p class="tags-description">Use the following tags to automatically add property information to the emails. Tags labeled with an asterisk (*) can be used in the email subject as well.</p>
				
				<div class="opalestate-template-tags-box">
					<strong>{receive_name}</strong> Name of the agent who made the property
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_name}</strong> Name of the property
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_link}</strong> Link of the property
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_edit_link}</strong> Link for editing of the property (admin)
				</div>
	
				<div class="opalestate-template-tags-box">
					<strong>{name}</strong> Name of the user who contact via email form
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{email}</strong> Email of the user who contact via email form
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{phone}</strong> Phone number of who sent via form
				</div>
			
				<div class="opalestate-template-tags-box">
					<strong>{message}</strong> Message content of who sent via form
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{site_link}</strong> A link to this website
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{current_time}</strong> Current date and time
				</div>

				</div> ';
        $enquire_list_tags = apply_filters('opalestate_email_enquire_tags', $enquire_list_tags);

        $contact_list_tags = '<div>
				<p class="tags-description">Use the following tags to automatically add property information to the emails. Tags labeled with an asterisk (*) can be used in the email subject as well.</p>
				
				<div class="opalestate-template-tags-box">
					<strong>{receive_name}</strong> Name of the agent who made the property
				</div>
	
				<div class="opalestate-template-tags-box">
					<strong>{name}</strong> Name of the user who contact via email form
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{email}</strong> Email of the user who contact via email form
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{phone}</strong> Phone number of who sent via form
				</div>
			
				<div class="opalestate-template-tags-box">
					<strong>{message}</strong> * Message content of who sent via form
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{site_link}</strong> A link to this website
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{current_time}</strong> Current date and time
				</div>

				</div> ';
        $contact_list_tags = apply_filters('opalestate_email_contact_tags', $contact_list_tags);

        $review_list_tags = '<div>
				<p class="tags-description">Use the following tags to automatically add property information to the emails. Tags labeled with an asterisk (*) can be used in the email subject as well.</p>
				
				<div class="opalestate-template-tags-box">
					<strong>{receive_name}</strong> Name of the agent who made the property
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_name}</strong> Name of the property
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{property_link}</strong> Link of the property
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_edit_link}</strong> Link for editing of the property (admin)
				</div>
	
				<div class="opalestate-template-tags-box">
					<strong>{name}</strong> Name of the user who contact via email form
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{email}</strong> Email of the user who contact via email form
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{schedule_date}</strong> Schedule date
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{schedule_time}</strong> Schedule time
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{phone}</strong> Phone number of who sent via form
				</div>
			
				<div class="opalestate-template-tags-box">
					<strong>{message}</strong> * Message content of who sent via form
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{site_link}</strong> A link to this website
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{current_time}</strong> Current date and time
				</div>

				</div> ';
        $review_list_tags = apply_filters('opalestate_email_review_tags', $review_list_tags);

        $list_tags = '<div>
				<p class="tags-description">Use the following tags to automatically add property information to the emails. Tags labeled with an asterisk (*) can be used in the email subject as well.</p>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_name}</strong> Name of the property
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{property_link}</strong> Link of the property
				</div>
				
				<div class="opalestate-template-tags-box">
					<strong>{property_edit_link}</strong> Link for editing of the property (admin)
				</div>
	
				<div class="opalestate-template-tags-box">
					<strong>{user_email}</strong> Email of the user who made the property
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{submitted_date}</strong> Submitted date 
				</div>

				<div class="opalestate-template-tags-box">
					<strong>{user_name}</strong> Name of the user who made the property
				</div>
			
				<div class="opalestate-template-tags-box">
					<strong>{site_name}</strong> The name of this website
				</div>
				<div class="opalestate-template-tags-box">
					<strong>{site_link}</strong> A link to this website
				</div>
				<div class="opalestate-template-tags-box">
					<strong>{current_time}</strong> Current date and time when email sent
				</div></div>';

        $list_tags = apply_filters('opalestate_email_tags', $list_tags);


        $fields = [
            'id'      => 'options_page',
            'title'   => esc_html__('Email Settings', 'opalestate-pro'),
            'show_on' => ['key' => 'options-page', 'value' => ['opalestate_settings'],],
            'fields'  => apply_filters('opalestate_settings_emails', [
                    [
                        'name' => esc_html__('Email Settings', 'opalestate-pro'),
                        'desc' => '<hr>',
                        'id'   => 'opalestate_title_email_settings',
                        'type' => 'title',
                    ],
                    [
                        'id'      => 'from_name',
                        'name'    => esc_html__('From Name', 'opalestate-pro'),
                        'desc'    => esc_html__('The name donation receipts are said to come from. This should probably be your site or shop name.', 'opalestate-pro'),
                        'default' => get_bloginfo('name'),
                        'type'    => 'text',
                    ],
                    [
                        'id'      => 'from_email',
                        'name'    => esc_html__('From Email', 'opalestate-pro'),
                        'desc'    => esc_html__('Email to send donation receipts from. This will act as the "from" and "reply-to" address.', 'opalestate-pro'),
                        'default' => get_bloginfo('admin_email'),
                        'type'    => 'text',
                    ],


                    [
                        'name' => esc_html__('Email Submission Templates (Template Tags)', 'opalestate-pro'),
                        'desc' => $list_tags . '<br><hr>',
                        'id'   => 'opalestate_title_email_submission_template',
                        'type' => 'title',
                    ],


                    //------------------------------------------
                    [
                        'name' => esc_html__('New Property Submission (Customer)', 'opalestate-pro'),
                        'desc' => '<hr>',
                        'id'   => 'opalestate_title_email_new_property_customer',
                        'type' => 'title',
                    ],
                    [
                        'name'    => esc_html__('Enable', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable email for customers when they have a submission.', 'opalestate-pro'),
                        'id'      => 'enable_customer_new_submission',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                        'default' => 'on',
                    ],
                    [
                        'id'         => 'newproperty_email_subject',
                        'name'       => esc_html__('Email Subject', 'opalestate-pro'),
                        'type'       => 'text',
                        'desc'       => esc_html__('The email subject for admin notifications.', 'opalestate-pro'),
                        'attributes' => [
                            'rows' => 3,
                        ],
                        'default'    => esc_html__('New Property Listing Submitted: {property_name}', 'opalestate-pro'),

                    ],
                    [
                        'id'      => 'newproperty_email_body',
                        'name'    => esc_html__('Email Body', 'opalestate-pro'),
                        'type'    => 'wysiwyg',
                        'desc'    => esc_html__('Enter the email an admin should receive when an initial payment request is made.', 'opalestate-pro'),
                        'default' => OpalEstate_Send_Email_New_Submitted::get_default_template(),
                    ],
                    //------------------------------------------

                    [
                        'name'       => esc_html__('New Property Submission (Admin)', 'opalestate-pro'),
                        'desc'       => '<hr>',
                        'id'         => 'opalestate_title_email_new_property_admin',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                    ],
                    [
                        'name'    => esc_html__('Enable', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable email for admin when a property is submitted.', 'opalestate-pro'),
                        'id'      => 'enable_admin_new_submission',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                        'default' => 'on',
                    ],
                    [
                        'id'      => 'admin_newproperty_email_subject',
                        'name'    => esc_html__('Email Subject', 'opalestate-pro'),
                        'type'    => 'text',
                        'desc'    => esc_html__('The email subject for admin notifications.', 'opalestate-pro'),
                        'default' => esc_html__('You received a new submission: {property_name} from {user_mail}', 'opalestate-pro'),

                    ],
                    [
                        'id'      => 'admin_newproperty_email_body',
                        'name'    => esc_html__('Email Body', 'opalestate-pro'),
                        'type'    => 'wysiwyg',
                        'desc'    => esc_html__('Enter the email an admin should receive when an initial payment request is made.', 'opalestate-pro'),
                        'default' => OpalEstate_Send_Email_Admin_New_Submitted::get_default_template(),
                    ],
                    //------------------------------------------

                    [
                        'name'       => esc_html__('Approved property for publish (Customer)', 'opalestate-pro'),
                        'desc'       => '<hr>',
                        'id'         => 'opalestate_title_email_approved_property',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                    ],
                    [
                        'name'    => esc_html__('Enable approve property email', 'opalestate-pro'),
                        'desc'    => esc_html__('Enable approve property email.', 'opalestate-pro'),
                        'id'      => 'enable_approve_property_email',
                        'type'    => 'switch',
                        'options' => [
                            'on'  => esc_html__('Enable', 'opalestate-pro'),
                            'off' => esc_html__('Disable', 'opalestate-pro'),
                        ],
                        'default' => 'on',
                    ],
                    [
                        'id'      => 'approve_email_subject',
                        'name'    => esc_html__('Email Subject', 'opalestate-pro'),
                        'type'    => 'text',
                        'desc'    => esc_html__('The email subject a user should receive when they make an initial property request.', 'opalestate-pro'),
                        'default' => esc_html__('New Property Listing Approved: {property_name}', 'opalestate-pro'),
                    ],

                    [
                        'id'      => 'approve_email_body',
                        'name'    => esc_html__('Email Body', 'opalestate-pro'),
                        'type'    => 'wysiwyg',
                        'desc'    => esc_html__('Enter the email a user should receive when they make an initial payment request.', 'opalestate-pro'),
                        'default' => OpalEstate_Send_Email_Approve::get_default_template(),
                    ],
                    /// enquire contact template ////
                    [
                        'name'       => esc_html__('Email Enquire Contact Form (in the single property page)', 'opalestate-pro'),
                        'desc'       => $enquire_list_tags . '<br><hr>',
                        'id'         => 'opalestate_title_email_enquire_contact',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                    ],
                    [
                        'id'      => 'enquiry_email_subject',
                        'name'    => esc_html__('Email Subject', 'opalestate-pro'),
                        'type'    => 'text',
                        'desc'    => esc_html__('The email subject a user should receive when they make an initial property request.', 'opalestate-pro'),
                        'default' => esc_html__('You got a message', 'opalestate-pro'),
                    ],

                    [
                        'id'      => 'enquiry_email_body',
                        'name'    => esc_html__('Email Body', 'opalestate-pro'),
                        'type'    => 'wysiwyg',
                        'default' => OpalEstate_Send_Email_Notification::get_default_template('enquiry'),
                    ],
                    /// Email Request Review ///
                    [
                        'name'       => esc_html__('Email Request Review Form (in the single property page)', 'opalestate-pro'),
                        'desc'       => $review_list_tags . '<br><hr>',
                        'id'         => 'opalestate_title_email_request_review',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                    ],
                    [
                        'id'      => 'request_review_email_subject',
                        'name'    => esc_html__('Email Subject', 'opalestate-pro'),
                        'type'    => 'text',
                        'desc'    => esc_html__('The email subject a user should receive when they make an initial property request.', 'opalestate-pro'),
                        'default' => esc_html__('You have a message request reviewing', 'opalestate-pro'),
                    ],

                    [
                        'id'      => 'request_review_email_body',
                        'name'    => esc_html__('Email Body', 'opalestate-pro'),
                        'type'    => 'wysiwyg',
                        'default' => OpalEstate_Send_Email_Request_Reviewing::get_default_template(),
                    ],
                    /// email contact template ////
                    [
                        'name'       => esc_html__('Email Contact Host Form (in the Agent/Agency page)', 'opalestate-pro'),
                        'desc'       => $contact_list_tags . '<br><hr>',
                        'id'         => 'opalestate_title_email_contact_author_form',
                        'type'       => 'title',
                        'before_row' => '<hr>',
                    ],
                    [
                        'id'      => 'contact_email_subject',
                        'name'    => esc_html__('Email Subject', 'opalestate-pro'),
                        'type'    => 'text',
                        'desc'    => esc_html__('The email subject a user should receive when they make an initial property request.', 'opalestate-pro'),
                        'default' => esc_html__('You got a message', 'opalestate-pro'),
                    ],

                    [
                        'id'      => 'contact_email_body',
                        'name'    => esc_html__('Email Body', 'opalestate-pro'),
                        'type'    => 'wysiwyg',
                        'default' => OpalEstate_Send_Email_Notification::get_default_template(),
                    ],
                ]
            ),
        ];

        return $fields;
    }

    /**
     * get data of newrequest email
     *
     * @var $args  array: property_id , $body
     */
    public static function replace_shortcode($args, $body) {

        $tags = [
            'user_name'      => "",
            'user_mail'      => "",
            'submitted_date' => "",
            'property_name'  => "",
            'site_name'      => '',
            'site_link'      => '',
            'property_link'  => '',
        ];
        $tags = array_merge($tags, $args);

        extract($tags);

        $tags = [
            "{user_mail}",
            "{user_name}",
            "{submitted_date}",
            "{site_name}",
            "{site_link}",
            "{current_time}",
            '{property_name}',
            '{property_link}',
        ];

        $values = [
            $user_mail,
            $user_name,
            $submitted_date,
            get_bloginfo('name'),
            get_home_url(),
            date_i18n(opalestate_email_date_format()),
            $property_name,
            $property_link,
        ];

        $message = str_replace($tags, $values, $body);

        return $message;
    }

    public static function approve_publish_property_email($post_id) {
        $mail = new OpalEstate_Send_Email_Approve();
        $mail->set_pros($post_id);

        self::send_mail_now($mail);
    }
}

Opalestate_Emails::init();
