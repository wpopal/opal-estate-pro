<?php 
	$args = array( 'post_id' => get_the_ID() );

	if( isset($author_id) && $author_id ){
		 
		$args   = array( 
				'post_id' 	=> get_the_ID(),
				'id' 		=> $author_id, 
				'email'   	=> $email,
				'message' 	=> '', 
				'type'		=> 'user',
		);
		echo opalestate_load_template_path( 'messages/contact-form', $args );
	}else {
		$email 		 = get_post_meta( get_the_ID(), OPALESTATE_AGENT_PREFIX . 'email', true ); 
		$args   = array( 
				'post_id' 	=> get_the_ID(),
				'id' 		=> get_the_ID(), 
				'email'   	=> $email,
				'message' 	=> '', 
				'type'		=> 'agent',
		);
	 
		echo opalestate_load_template_path( 'messages/contact-form', $args );
	}	