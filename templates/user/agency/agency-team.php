<?php
$agency = OpalEstate_Agency::get_instance( $post_id );

$users = $agency->get_members();

$sender_id = '';
$prefix    = '';

$fields = [
	[
		'id'   => 'sender_id',
		'name' => esc_html__( 'Sender ID', 'opalestate-pro' ),
		'type' => 'hidden',
	],
	[
		'id'       => "{$prefix}user_id",
		'name'     => esc_html__( 'Name', 'opalestate-pro' ),
		'type'     => 'select',
		'class'    => 'form-control opalesate-find-user',
		'required' => 'required',
	],
];

$form   = OpalEstate()->html->render_form( $fields );
$id     = 'agency-add-member';
?>
<div class="opalestate-agency-team opalestate-admin-box">
    <h3><a href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>" title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" target="_blank"><?php esc_html_e( 'Agency Team', 'opalestate-pro' );
    ?></a></h3>
    <div class="opal-row">
        <div class="col-lg-4 col-md-3">
            <div class="agency-add-team">
                <p><?php esc_html_e( 'As an author, you can add other users to your agency.', 'opalestate-pro' ); ?></p>
                <p><?php esc_html_e( 'Add someone to your agency, please enter extractly username in below input:', 'opalestate-pro' ); ?></p>
                <form method="post" id="opalestate-add-team-form">
					<?php echo $form; ?>
					<?php wp_nonce_field( $id, 'add_team_action' ); ?>
                    <button class="button btn btn-primary btn-3d" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php esc_html_e( 'Processing', 'opalestate-pro' ); ?>" type="submit"
                            name="contact-form"><?php esc_html_e( 'Add', 'opalestate-pro' ); ?></button>
                </form>
            </div>
        </div>

        <div class="col-lg-8 col-md-9">
            <div class="agency-listing-team">
                <table>
                    <tr>
                        <th><?php esc_html_e( 'Users in your team', 'opalestate-pro' ); ?></th>
                        <th width="100"><?php esc_html_e( 'Action', 'opalestate-pro' ); ?></th>
                    </tr>

					<?php foreach ( $users as $user ) : ?>
						<?php
						$agent_id = get_user_meta( $user['id'], OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true );
						$url = $agent_id ? get_the_permalink( $agent_id ) : '#';
						?>
                        <tr>
                            <td>
                                <div class="media">
                                    <a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $user['name'] ); ?>" target="_blank">
                                        <img src="<?php echo esc_url( $user['avatar_url'] ); ?>" width="80">
                                        <div class="fullname"><?php echo esc_html( $user['name'] ); ?> (<?php echo esc_html( $user['username'] ); ?> )</div>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo opalestate_get_user_management_page_uri( [ 'tab' => 'agency_team', 'remove_id' => $user['id'] ] ); ?>"
                                   title="<?php esc_html_e( 'Remove', 'opalestate-pro' ); ?>">
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
