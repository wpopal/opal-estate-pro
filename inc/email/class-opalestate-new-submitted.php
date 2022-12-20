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

/**
 * @class   OpalEstate_Send_Email_Notification
 *
 */
class OpalEstate_Send_Email_New_Submitted extends OpalEstate_Abstract_Email_Template {

    /**
     * Send Email
     */
    public function get_subject() {
        $d = esc_html__('New Property Listing Submitted: {property_name}', 'opalestate-pro');
        $s = opalestate_get_option('newproperty_email_subject', $d);

        return $this->replace_tags($s);
    }

    /**
     * get collection of key and value base on tags which using to replace custom tags
     */
    public function set_pros($property_id, $user_id) {
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
     * Send Email
     */
    public function get_content_template() {

        $body = opalestate_get_option('newproperty_email_body', self::get_default_template());

        return $body;
    }

    /**
     * Send Email
     */
    public static function get_default_template() {

        return trim(preg_replace('/\t+/', '', '
						Hi {user_name},
						<br>
						Thanks you so much for submitting {property_name}  at  {site_name}:<br>
						 Give us a few moments to make sure that we are got space for you. You will receive another email from us soon.
						 If this request was made outside of our normal working hours, we may not be able to confirm it until we are open again.
						<br>
							You may review your property at any time by logging in to your client area.
						<br>
						<em>This message was sent by {site_link} on {current_time}.</em>'
        ));
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
        return parent::get_body();
    }
}
