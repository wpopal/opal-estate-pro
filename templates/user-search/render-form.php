<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( opalestate_options( 'enable_saved_usersearch', 'on' ) == 'on' ): 
$args = array(); 

$message = sprintf( esc_html__( 'Hey there! I saved this search on %s, please check out these homes that are listed. Remember to save this search to be first to catch any new listings.', 'opalestate-pro' ) , get_bloginfo( 'name' ) );
$logined = is_user_logged_in();
?>
<div class="opalestate-popup">
    <div class="popup-head <?php if( ! $logined ): ?> opalestate-need-login <?php endif; ?>"><span><i class="fa fa-star" aria-hidden="true"></i> <?php esc_html_e('Save search', 'opalestate-pro') ?></span></div>

    <?php if( $logined ): ?> 
    <div class="popup-body">
        
        <div class="popup-close"><i class="fa fa-times" aria-hidden="true"></i></div>
      
            <div class="contact-share-form-container">
               
                <h6><?php echo esc_html__( 'Name this search.', 'opalestate-pro' ); ?></h6>

                <div class="box-content agent-contact-form">

                    <form method="post" id="opalestate-save-search-form">
                        <?php do_action('opalestate_contact_share_form_before'); ?>

                        <div class="form-group">
                            <input class="form-control" name="name" type="text" placeholder="<?php echo esc_html__( 'Name', 'opalestate-pro' ); ?>" required="required">
                        </div><!-- /.form-group -->

                        <?php do_action('opalestate_contact_share_form_after'); ?>
                        <button class="button btn btn-primary btn-3d btn-block" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo esc_html__( ' Processing', 'opalestate-pro' ); ?>" type="submit" name="contact-form"><?php echo esc_html__( 'Save', 'opalestate-pro' ); ?></button>
                    </form>
                </div><!-- /.agent-contact-form -->
            </div><!-- /.agent-contact-->
    </div>    
    <?php endif ; ?>

</div>
<?php endif; ?>
