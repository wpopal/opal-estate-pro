<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(); 

$message = sprintf( esc_html__( 'Hey there! I saved this search on %s, please check out these homes that are listed. Remember to save this search to be first to catch any new listings.', 'opalestate-pro' ) , get_bloginfo( 'name' ) );

if( opalestate_options('enable_share_earch','on') == 'on' ): 
?>

<div class="opalestate-popup">
    <div class="popup-head <?php if( !is_user_logged_in() ): ?> opalestate-need-login <?php endif; ?>"><span class="text-primary"><i class="fa fa-envelope" aria-hidden="true"></i> <?php esc_html_e('Share this Search', 'opalestate-pro') ?></span> </div>
    <?php if( is_user_logged_in() ):
    global $current_user;
    ?>
    <div class="popup-body">
        <div class="popup-close"><i class="fa fa-times" aria-hidden="true"></i></div>
        
            <div class="share-content-form-container">
               
                <h6><?php echo esc_html__( 'Are you searching with anyone? Share this search.', 'opalestate-pro' ); ?></h6>

                <div class="box-content share-content-form">

                    <form method="post" class="opalestate-share-content-form">
                        <?php do_action('opalestate_contact_share_form_before'); ?>

                        <div class="form-group">
                            <input class="form-control inputs-emails" name="friend_email[]" type="email" placeholder="<?php echo esc_html__( 'Friend Email', 'opalestate-pro' ); ?>" value="" required="required">
                        </div><!-- /.form-group -->

                        <div class="form-group">
                            <input class="form-control" name="name" type="text" placeholder="<?php echo esc_html__( 'Name', 'opalestate-pro' ); ?>" value="<?php echo esc_attr( $current_user->data->display_name ); ?>"
                                   required="required">
                        </div><!-- /.form-group -->

                        <div class="form-group">
                            <input class="form-control" name="email" type="email" placeholder="<?php echo esc_html__( 'E-mail', 'opalestate-pro' ); ?>" required="required" value="<?php echo
                            esc_attr( $current_user->data->user_email ); ?>">
                        </div><!-- /.form-group -->

                        <div class="form-group">
                            <textarea class="form-control" name="message" placeholder="<?php echo esc_html__( 'Message', 'opalestate-pro' ); ?>" style="overflow: hidden; word-wrap: break-word;
                            min-height: 108px;"><?php echo esc_html( $message ); ?></textarea>
                        </div><!-- /.form-group -->

                        <?php do_action('opalestate_contact_share_form_after'); ?>
                        <button class="button btn btn-primary btn-3d btn-block"  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo esc_html__( ' Processing', 'opalestate-pro' ); ?>" type="submit" name="contact-form"><?php echo esc_html__( 'Send message', 'opalestate-pro' ); ?></button>
                    </form>
                </div><!-- /.agent-contact-form -->
            </div><!-- /.agent-contact-->
  
    </div>   
    <?php endif ;  ?> 
</div>
<?php endif; ?>
