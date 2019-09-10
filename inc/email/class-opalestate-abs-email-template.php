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
class OpalEstate_Abstract_Email_Template {
 	
 	public $args = array();

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
		return esc_html__( 'Admin Notice of Expiring Job Listings', 'opalestate-pro' );
	}

	/**
	 * Get the description for this email notification.
	 *
	 * @type abstract
	 * @return string
	 */
	public function get_description() {
		return esc_html__( 'Send notices to the site administrator before a job listing expires.', 'opalestate-pro' );
	}

	public function to_email () {
		
	}

	public function get_content_template() {

	}


	public function set_args ( $args ) {
		return $this->args = $args;
	}
	
	public function replace_tags ( $template ) {
    	
    	$args = $this->args; 
    	$default  = array(
    		'receiver_name'	=> '',
    		'name'			=> '',
    		'receiver_email'			=> '',
    		'property_link' => '',
    		'message'		=> '',
    		'site_name'		=> bloginfo(),
    		'site_link'		=> get_home_url(),
    		'current_time'	=> date("F j, Y, g:i a"),
    		'phone'			=> '' 
    	);

    	$args   = array_merge( $default, $args );

		$tags 	= array();
		$values = array() ;	

		foreach ( $args as $key => $value ) {
			$tags[] = "{".$key."}";
			$values[] = $value;
		}	
		
		$message = str_replace( $tags, $values, $template );
		
		return $message;
    }

    public function get_subject () {

    }

    public function from_email() {
    	return opalestate_get_option( 'from_email' ,  get_bloginfo( 'admin_email' ) );
    }

    public function from_name() {
    	return opalestate_get_option('from_name',  get_bloginfo( 'name' ) );
    }

    public function get_cc() {

    }


	public function get_body(){

    	$template = $this->get_content_template(); 
		return $this->replace_tags( $template ); 
	}

	public function get_plain_text_body () {

	}
}
