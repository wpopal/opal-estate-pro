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
class OpalEstate_Send_Email_Notification extends OpalEstate_Abstract_Email_Template {

	public $type = '';
 
	/**
	 * Send Email
	 */
	public function get_subject () {
		switch ( $this->type ) {
			case 'enquiry':
				$subject = html_entity_decode( esc_html__('You got a message enquiry', 'opalestate-pro')  );
				break;
			
			default:
				$subject = html_entity_decode( esc_html__('You got a message contact', 'opalestate-pro')  );
				break;
		}

		return $subject; 
	}

	/**
	 * Send Email
	 */
	public function get_content_template() {

		switch ( $this->type ) {
			case 'enquiry':
				return opalestate_load_template_path( 'emails/enquiry' );
				break;
			
			default:
				return opalestate_load_template_path( 'emails/contact' );
				break;
		}
	}	

	public function to_email () {
		return $this->args ['receiver_email'];
	}

	public function cc_email () {
		return $this->args ['sender_email'];
	}

	public function get_body() {
		$this->args['email'] = $this->args['receiver_email'];
		return parent::get_body();
	}
}
?>