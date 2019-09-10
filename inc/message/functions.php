<?php 
	function opalestate_get_message_by_user( $args=array() ){
		return array(
			'items' =>  OpalEstate_User_Message::get_instance()->get_list( $args ),
			'total' =>  OpalEstate_User_Message::get_instance()->get_total( $args )
		);
	}

	function opalestate_get_member_email_data( $post_id ){

    	$output = array();
    	$type = get_post_meta( $post_id, OPALESTATE_PROPERTY_PREFIX . 'author_type', true );

    	$receiver_id = 0; 
    	switch ( $type ) {
	
			case 'agent':
				$related_id  = get_post_meta( $post_id, OPALESTATE_PROPERTY_PREFIX . 'related_id', true ); 
				$post 		 = get_post( $related_id );
				$name 		 = $post->post_title;
				$email       = get_post_meta( $related_id, OPALESTATE_AGENT_PREFIX . 'email', true );
				break;

			case 'agency':
				$related_id   = get_post_meta( $post_id, OPALESTATE_PROPERTY_PREFIX . 'related_id', true ); 
				$agent 		  = get_post( $related_id );
				$name 		  = $agent->post_title;
				$email 		  = get_post_meta( $related_id, OPALESTATE_AGENCY_PREFIX . 'email', true );
				break;
			default:
				$post  = get_post( $post_id );
				$user  = get_user_by( 'id', $post->post_author );
				$email = $user->data->user_email; 
				$name  = $user->data->display_name; 
				$receiver_id = $post->post_author;

				break;
		}

		return $output = array(
			'receiver_email' 	=> $email,
			'receiver_name'		=> $name,
			'receiver_id' 	    => $receiver_id			
		);
    }
?>