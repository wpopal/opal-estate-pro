<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Send_Email_Notification
 */
class OpalEstate_Send_Email_Notification extends OpalEstate_Abstract_Email_Template {

    public $type = '';

    public function set_type($content) {
        $type = '';
        if (isset($content['type']) && 'send_enquiry' === $content['type']) {
            $type = 'enquiry';
        }

        $this->type = $type;
    }

    /**
     * Send Email
     */
    public function get_subject() {
        switch ($this->type) {
            case 'enquiry':

                $subject = html_entity_decode(esc_html__('You got a message enquiry', 'opalestate-pro'));
                $subject = opalestate_options('enquiry_email_subject', $subject);

                break;

            default:

                $subject = html_entity_decode(esc_html__('You got a message contact', 'opalestate-pro'));
                $subject = opalestate_options('contact_email_subject', $subject);

                break;
        }

        return $this->replace_tags($subject);
    }

    /**
     * Send Email.
     */
    public function get_content_template() {
        switch ($this->type) {
            case 'enquiry':
                return opalestate_options('enquiry_email_body', self::get_default_template('enquiry'));
                break;
            default:
                return opalestate_options('contact_email_body', self::get_default_template());
                break;
        }
    }

    public function to_email() {
        return $this->args ['receiver_email'];
    }

    public function cc_email() {
        return $this->args ['sender_email'];
    }

    public function get_body() {
        $this->args['email'] = $this->args['sender_email'];

        return parent::get_body();
    }

    /**
     * Get default template.
     *
     * @param string $type
     * @return string
     */
    public static function get_default_template($type = 'contact') {
        if ($type == 'enquiry') {
            return opalestate_load_template_path('emails/enquiry');
        }

        return opalestate_load_template_path('emails/contact');
    }
}
