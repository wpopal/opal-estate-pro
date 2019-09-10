<?php 

$agency = OpalEstate_Agency::get_instance( $post_id );

$users = $agency->get_members(); 

$sender_id = '';
$prefix = '';

$fields =  array(
			 
			array(
				'id'   		   => "sender_id",
				'name' 		   => esc_html__( 'Sender ID', 'opalestate-pro' ),
				'type' 		   => 'hidden',		
				'default'	   => "",		 
				'description'  => "",
			),
			array(
				'id'   		   => "{$prefix}user_id",
				'name' 		   => esc_html__( 'Name', 'opalestate-pro' ),
				'type' 		   => 'select',
				'class'			=> 'form-control opalesate-find-user',
				'default'	   =>  "",		 
				'required' 	   => 'required',
				'description'  => "",
			),
			

		);
$form   = OpalEstate()->html->render_form( $fields );
$id =  'agency-add-member';
?>
<div class="opalestate-agency-team">
	
	<h3><?php esc_html_e( 'Agency Team' , 'opalestate-pro' ); ?></h3>
	<div class="opal-row">
		<div class="col-lg-4 col-md-3">
			<div class="agency-add-team">
				<p><?php esc_html_e( "As an author, you can add other users to your agency.", "opalestate" ); ?></p>
				<p><?php esc_html_e( "Add someone to your agency, please enter extractly username in below input:", "opalestate" ); ?></p>
				  <form method="post" id="opalestate-add-team-form">
                    <?php echo $form;?>   
                    <?php  wp_nonce_field( $id, 'add_team_action' ); ?>
                    <button class="button btn btn-primary btn-3d" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php esc_html_e( 'Processing', 'opalestate-pro' ); ?>" type="submit" name="contact-form"><?php echo esc_html__( 'Send message', 'opalestate-pro' ); ?></button>
                </form>
			</div>
		</div>	

		<div class="col-lg-8 col-md-9">
			<div class="agency-listing-team">
				<table>
					<tr>
						<th><?php esc_html_e('Users in your team'); ?></th>
						<th width="100"><?php esc_html_e('Action'); ?></th>
					</tr>	
					
					<?php foreach( $users as $user ):   // echo '<pre>' . print_r( $user, 1);die;  ?>
					<tr>
						<td>
							<div class="media">
								<img src="<?php echo $user['avatar_url'];?>" width="80">
								<div class="fullname"><?php echo $user['name']; ?> (<?php echo $user['username']; ?> )</div>
							</div>
						</td>
						<td>
							<a href="<?php echo opalestate_get_user_management_page_uri( array('tab' => 'agency_team', 'remove_id' => $user['id'] ) ); ?>" title="<?php esc_html_e( "Remove" , "opalestate" ); ?>">
								<i class="fa fa-trash"></i>
							</a>
						</td>
					</tr>	
					<?php endforeach; ?>

				</table>
			</div>
		</div>	

	</div>
</div>	
