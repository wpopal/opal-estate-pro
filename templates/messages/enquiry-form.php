<?php
global $post;

if ( opalestate_get_option( 'enable_single_enquire_form', 'on' ) !== 'on' ) {
	return;
}

if ( ! opalestate_is_require_login_to_show_enquire_form() ) {
	?>
    <div class="opalestate-require-login-box">
        <p class="opalestate-require-login-notice"><?php esc_html_e( 'You need to login to contact.', 'opalestate-pro' ); ?></p>
        <a href="#opalestate-user-form-popup" class="opalestate-need-login button btn btn-primary btn-3d">
			<?php esc_html_e( 'Login', 'opalestate-pro' ) ?>
        </a>
    </div>
	<?php
	return;
}

$property_id = get_the_ID();
$infor_id    = apply_filters( 'opalestate_contact_form_infor_id', $property_id, $property_id );
$message     = sprintf( __( 'Hi, I am interested in %s (Property ID: %s)', 'opalestate-pro' ), get_the_title(), $infor_id );
$heading     = esc_html__( 'Enquire about property', 'opalestate-pro' );
$object      = OpalEstate_User_Message::get_instance();
$fields      = $object->get_equiry_form_fields( $message );
$form        = OpalEstate()->html->render_form( $fields );

$id = 'send-enquiry-form';
?>

<?php if ( isset( $nowrap ) && $nowrap ) : ?>
    <form method="post" class="opalestate-message-form">
		<?php do_action( 'opalestate_message_form_before' ); ?>

		<?php echo $form; ?>

		<?php do_action( 'opalestate_message_form_after' ); ?>
		<?php wp_nonce_field( $id, 'message_action' ); ?>
        <button class="button btn btn-primary btn-3d" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php esc_html_e( ' Processing', 'opalestate-pro' ); ?>" type="submit"
                name="contact-form"><?php echo esc_html__( 'Send message', 'opalestate-pro' ); ?></button>
    </form>
<?php else : ?>
    <div class="opalestate-box-content property-enquire-form">
        <div class="opalestate-box">
            <div class="property-equire-form-container">
                <h5 class="contact-form-title"><?php echo $heading; ?></h5>
                <div class="box-content">
                    <form method="post" class="opalestate-message-form">
						<?php do_action( 'opalestate_message_form_before' ); ?>

						<?php echo $form; ?>

						<?php do_action( 'opalestate_message_form_after' ); ?>
						<?php wp_nonce_field( $id, 'message_action' ); ?>
                        <button class="button btn btn-primary btn-3d" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php esc_html_e( ' Processing', 'opalestate-pro' ); ?>"
                                type="submit" name="contact-form"><?php echo esc_html__( 'Send message', 'opalestate-pro' ); ?></button>
                    </form>
                </div><!-- /.agent-contact-form -->
            </div><!-- /.agent-contact-->
        </div>
    </div>
<?php endif; ?>
