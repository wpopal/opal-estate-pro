<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( 'off' === opalestate_get_option( 'enable_google_login' ) ) {
	return;
}

$google_client_id     = opalestate_get_option( 'google_client_id', '' );
$google_client_secret = opalestate_get_option( 'google_client_secret', '' );
$google_api_key       = opalestate_get_option( 'google_api_key', '' );

if ( ! $google_client_id || ! $google_client_secret || ! $google_api_key ) {
	return;
}

?>
<a href="javascript:void(0);" rel="nofollow" title="<?php esc_attr_e( 'Google', 'opalestate-pro' ); ?>" class="js-opal-google-login opalestate-social-login-google-btn">
    <i class="fa fa-google-plus"></i><?php esc_html_e( 'Connect with Google', 'opalestate-pro' ); ?>
</a>
