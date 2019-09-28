<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( 'off' === opalestate_get_option( 'enable_facebook_login' ) ) {
	return;
}

$facebook_app_id = opalestate_get_option( 'facebook_app_id', '' );
$facebook_secret = opalestate_get_option( 'facebook_secret', '' );

if ( ! $facebook_app_id || ! $facebook_secret ) {
    return;
}

?>

<a href="javascript:void(0);" rel="nofollow" title="<?php esc_attr_e( 'Facebook', 'opalestate-pro' ); ?>" class="js-opal-facebook-login opalestate-social-login-facebook-btn">
    <i class="fab fa-facebook"></i><?php esc_html_e( 'Connect with Facebook', 'opalestate-pro' ); ?>
</a>
