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
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Send_Email_Notification
 *
 * @version 1.0
 */
class OpalEstate_Send_Email_Approve extends OpalEstate_Abstract_Email_Template {
 
	/**
	 *  
	 */
	public function get_subject () {
		$propety_title = '' ;
		
		$subject = sprintf( esc_html__( 'The Property Listing Approved: {property_name}', 'opalestate-pro' ),  $propety_title );
		$subject = opalestate_options( 'approve_email_body' , $subject ); 

		return $subject;
	}

	/**
	 * get collection of key and value base on tags which using to replace custom tags
	 */
	public  function set_pros(  $property_id  ){
	 	
	 	$property 	   = get_post( $property_id );
		$user    	   = get_userdata( $property->post_author ); 
		$email 		   = get_user_meta( $property->post_author, OPALESTATE_USER_PROFILE_PREFIX . 'email', true ); 
 		$email  	   = $email ? $email : $user->data->user_email;

		$this->args = array(
			'receiver_email'	 => $email,
			'user_mail' 		 => $email,
			'user_name'			 => $user->display_name,
			'submitted_date'	 => $property->post_date,
			'property_name'	 	 => $property->post_title,
			'property_link'		 => get_permalink( $property_id ),
    		'current_time'		=>  date("F j, Y, g:i a"),
		); 

		return $this->args ;
	}

	/**
	 *  
	 */
	public function get_content_template() {

		$content = opalestate_options( 'approve_email_body' , self::get_default_template() ); 		
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
	public function to_email () {
		return $this->args ['receiver_email'];
	}

	/**
	 *  
	 */
	public function cc_email () {
		return $this->args ['sender_email'];
	}

	/**
	 *  
	 */
	public function get_body() {
		
		$post = get_post( $this->args['post_id'] ); 
		
		$this->args['email'] = $this->args['receiver_email'];
		$this->args['property_link'] = $post->post_title; 

		return parent::get_body();
	}
}
?>