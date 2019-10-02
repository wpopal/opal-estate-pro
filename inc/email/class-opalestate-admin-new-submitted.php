<?php
/**
 * OpalEstate_Send_Email_Admin_New_Submitted
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Send_Email_Admin_New_Submitted
 *
 * @version 1.0
 */
class OpalEstate_Send_Email_Admin_New_Submitted extends OpalEstate_Abstract_Email_Template {

	/**
	 * Send Email
	 */
	public function get_subject() {
		$propety_title = '';
		$d             = esc_html__( 'New Property Listing Submitted: {property_name}', 'opalestate-pro' );
		$s             = opalestate_get_option( 'admin_newproperty_email_subject', $d );

		return $s;
	}

	/**
	 * get collection of key and value base on tags which using to replace custom tags
	 */
	public function set_pros( $property_id, $user_id ) {
		$property = get_post( $property_id );
		$user     = get_userdata( $property->post_author );
		$email    = get_user_meta( $property->post_author, OPALESTATE_USER_PROFILE_PREFIX . 'email', true );
		$email    = $email ? $email : $user->data->user_email;

		$this->args = [
			'receiver_email' => $email,
			'user_mail'      => $email,
			'user_name'      => $user->display_name,
			'submitted_date' => $property->post_date,
			'property_name'  => $property->post_title,
			'property_link'  => get_permalink( $property_id ),
			'current_time'   => date( "F j, Y, g:i a" ),
		];

		return $this->args;
	}


	/**
	 * Send Email
	 */
	public function get_content_template() {
		$body = opalestate_get_option( 'admin_newproperty_email_body', self::get_default_template() );

		return $body;
	}

	/**
	 * Send Email
	 */
	public static function get_default_template() {
		return trim( preg_replace( '/\t+/', '', '
						You’ve received a submission from %s: {user_name},
						<br>
						You can review it by follow this link: {property_edit_link}
						<em>This message was sent by {site_link} on {current_time}.</em>'
		) );
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