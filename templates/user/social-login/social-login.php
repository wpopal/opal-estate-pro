<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<div class="opalestate-social-login">
    <ul class="opalestate-social-login__buttons">
		<?php
		if ( 'on' === opalestate_get_option( 'enable_facebook_login' ) ) {
			echo '<li>' . opalestate_load_template_path( 'user/social-login/facebook-button' ) . '</li>';
		}
		?>

		<?php
		if ( 'on' === opalestate_get_option( 'enable_google_login' ) ) {
			echo '<li>' . opalestate_load_template_path( 'user/social-login/google-button' ) . '</li>';
		}
		?>
    </ul>
</div>
