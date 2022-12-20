<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Send_Email_Approve
 *
 * @version 1.0
 */
class OpalEstate_Send_Email_Approve extends OpalEstate_Abstract_Email_Template {

    /**
     * Get subject.
     */
    public function get_subject() {
        $subject = esc_html__('The Property Listing Approved: {property_name}', 'opalestate-pro');
        $subject = opalestate_options('approve_email_subject', $subject);

        return $this->replace_tags($subject);
    }

    /**
     * Get collection of key and value base on tags which using to replace custom tags
     */
    public function set_pros($property_id) {
        $property = get_post($property_id);
        $user     = get_userdata($property->post_author);
        $email    = get_user_meta($property->post_author, OPALESTATE_USER_PROFILE_PREFIX . 'email', true);
        $email    = $email ? $email : $user->data->user_email;

        $this->args = [
            'receiver_email' => $email,
            'user_mail'      => $email,
            'user_name'      => $user->display_name,
            'submitted_date' => $property->post_date,
            'property_name'  => $property->post_title,
            'property_link'  => get_permalink($property_id),
            'current_time'   => date_i18n(opalestate_email_date_format()),
        ];

        return $this->args;
    }

    /**
     *
     */
    public function get_content_template() {
        $content = opalestate_options('approve_email_body', self::get_default_template());

        return $content;
    }

    /**
     *
     */
    public static function get_default_template() {
        return trim(preg_replace('/\t+/', '', "Hi {user_name},<br>
				<br>
				Thank you so much for submitting to {site_name}.
				<br>
				 We have completed the auditing process for your property '{property_name}'  and are pleased to inform you that your submission has been accepted.
				 <br>
				<br>
				Thanks again for your contribution
				<br>
				&nbsp;<br>
				<br>
				<em>This message was sent by {site_link} on {current_time}.</em>"));
    }

    /**
     *
     */
    public function to_email() {
        return $this->args ['receiver_email'];
    }

    /**
     *
     */
    public function cc_email() {
        return $this->args ['sender_email'];
    }

    /**
     *
     */
    public function get_body() {
        $this->args['email'] = $this->args['receiver_email'];

        return parent::get_body();
    }
}
