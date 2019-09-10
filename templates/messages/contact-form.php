<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 
 
if( !isset($type) ){
    $type = 'property';
} 
if( !isset($heading) ){

    $heading =  esc_html__( 'Contact Me', 'opalestate-pro' ); 
}


$object         = OpalEstate_User_Message::get_instance();
$fields         = $object->get_contact_form_fields();
$form           = OpalEstate()->html->render_form( $fields );

$id = 'send-contact-form'
?>
<?php if ( ! empty( $email ) ) : ?>
    <div class="contact-form-container">

        <h5 class="contact-form-title"><?php echo $heading; ?></h5>

         <div class="property-equire-form-container">
            <div class="box-content">
                <form method="post" class="opalestate-message-form">
                    <?php do_action('opalestate_message_form_before'); ?>
                    
                    <?php echo $form;?>   

                    <?php do_action( 'opalestate_message_form_after' ); ?> 
                    <?php  wp_nonce_field( $id, 'message_action' ); ?>
                    <button class="button btn btn-primary btn-3d" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php esc_html_e( ' Processing', 'opalestate-pro' ); ?>" type="submit" name="contact-form"><?php echo esc_html__( 'Send message', 'opalestate-pro' ); ?></button>
                </form>
            </div><!-- /.agent-contact-form -->
        </div><!-- /.agent-contact-->


    </div><!-- /.agent-contact-->
<?php endif; ?>
