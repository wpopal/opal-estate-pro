<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Send_Email_Request_Reviewing
 *
 * @version 1.0
 */
class OpalEstate_Send_Email_Request_Reviewing extends OpalEstate_Abstract_Email_Template {

    /**
     * Send Email
     */
    public function get_subject() {
        $content = esc_html__('You have a message request reviewing', 'opalestate-pro');
        $content = opalestate_options('request_review_email_subject', $content);

        return $content;
    }

    /**
     * Send Email
     */
    public function get_content_template() {
        $content = opalestate_options('request_review_email_body', self::get_default_template());

        return $content;
    }

    /**
     * Send Email
     */
    public function to_email() {
        return $this->args ['receiver_email'];
    }

    /**
     * Send Email
     */
    public function cc_email() {
        return $this->args ['sender_email'];
    }

    /**
     * Send Email
     */
    public function get_body() {
        $post = get_post($this->args['post_id']);

        // $this->args['email']         = $this->args['receiver_email'];
        $this->args['property_link'] = get_permalink($post->ID);
        $this->args['property_name'] = $post->post_title;

        return parent::get_body();
    }

    public static function get_default_template() {
        return opalestate_load_template_path('emails/request-reviewing');
    }
}
