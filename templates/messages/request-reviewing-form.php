<div id="<?php echo $id; ?>-popup" class="white-popup mfp-hide opalestate-mfp-popup">
    <h4><?php echo $heading; ?></h4>
    <p class="opalestate-note"><?php echo $description; ?></p>
    <?php echo '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="opalestate-message-form" method="post" id="'.$id.'" enctype="multipart/form-data" data-action="send_email_request_reviewing">'; ?>
	<?php do_action( 'opalestate_message_form_before' ); ?>
	<?php  echo $form; ?>
	 <?php  wp_nonce_field( $id, 'message_action' ); ?>

	<?php do_action( 'opalestate_message_form_after' ); ?>
	<button type="submit" name="submit" value="<?php esc_html_e( 'Send now', 'opalestate-pro' ); ?>" class="btn btn-primary">
		<?php esc_html_e( 'Send now', 'opalestate-pro' ); ?>
	</button>
	<?php echo '</form>'; ?>
</div>
