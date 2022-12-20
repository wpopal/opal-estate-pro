<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Abstract_Email_Template
 *
 * @version 1.0
 */
class OpalEstate_Abstract_Email_Template {

    public $args = [];

    /**
     * Get the unique email notification key.
     *
     * @return string
     */
    public function get_key() {
        return 'opalestate-notification';
    }

    /**
     * Get the friendly name for this email notification.
     *
     * @return string
     */
    public function get_title() {
        return esc_html__('Admin Notice of Expiring Job Listings', 'opalestate-pro');
    }

    /**
     * Get the description for this email notification.
     *
     * @return string
     */
    public function get_description() {
        return esc_html__('Send notices to the site administrator before a job listing expires.', 'opalestate-pro');
    }

    public function to_email() {

    }

    /**
     * Get the content for this email notification.
     *
     * @return string
     */
    public function get_content_template() {

    }


    public function set_args($args) {
        return $this->args = $args;
    }

    public function replace_tags($template) {
        $args    = $this->args;
        $default = [
            'receiver_name'      => '',
            'name'               => '',
            'receiver_email'     => '',
            'property_name'      => '',
            'property_link'      => '',
            'property_edit_link' => '',
            'message'            => '',
            'site_name'          => get_bloginfo(),
            'site_link'          => get_home_url(),
            'current_time'       => date_i18n(opalestate_email_date_format()),
            'phone'              => '',
        ];

        $args = array_merge($default, $args);

        $tags   = [];
        $values = [];

        foreach ($args as $key => $value) {
            $tags[]   = "{" . $key . "}";
            $values[] = $value;
        }

        $message = str_replace($tags, $values, $template);

        return $message;
    }

    public function get_subject() {

    }

    public function from_email() {
        return opalestate_get_option('from_email', get_bloginfo('admin_email'));
    }

    public function from_name() {
        return opalestate_get_option('from_name', get_bloginfo('name'));
    }

    public function get_cc() {

    }

    public function get_body() {
        $template = $this->get_content_template();

        return $this->replace_tags($template);
    }

    public function get_plain_text_body() {

    }
}
