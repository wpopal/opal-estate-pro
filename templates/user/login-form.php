<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( is_user_logged_in() ) {
	esc_html_e( 'You are currently logged in.', 'opalestate-pro' );

	return;
}

?>

<div class="opalesate-form">
	<?php if ( $hide_title === false ) : ?>
        <h3><?php esc_html_e( 'Login', 'opalestate-pro' ); ?></h3>
	<?php endif; ?>

	<?php if ( $message ) : ?>
        <p><?php printf( '%s', $message ) ?></p>
	<?php endif; ?>

    <form method="POST" class="opalestate-login-form opalestate-member-form">
		<?php do_action( 'opalestate_member_before_login_form' ); ?>

        <p class="opalestate-form-field username validate-required">
            <label for="username"><?php esc_html_e( 'Username or email address', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <input type="text" class="opalestate-input text input-text" name="username" id="username" required="required" value="<?php if ( ! empty( $_POST['username'] ) ) {
				echo esc_attr( $_POST['username'] );
			} ?>"/>
        </p>

        <p class="opalestate-form-field password validate-required">
            <label for="password"><?php esc_html_e( 'Password', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <input class="opalestate-input text input-text" type="password" name="password" required="required" id="password"/>
        </p>

		<?php do_action( 'opalestate_member_login_form' ); ?>

        <p class="opalestate-form-field remberme">
            <label>
                <input class="opalestate-input checkbox" name="rememberme" type="checkbox" value="forever"/> <?php esc_html_e( 'Remember me', 'opalestate-pro' ); ?>
            </label>
        </p>

        <p class="opalestate-form-field submit">
			<?php wp_nonce_field( 'opalestate-login', 'opalestate-login-nonce' ); ?>
			<?php if ( $redirect ) : ?>
                <input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>">
			<?php endif; ?>
            <input type="submit" class="opalestate-button button btn btn-primary" name="login" value="<?php esc_attr_e( 'Login', 'opalestate-pro' ); ?>"/>
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'opalestate-pro' ); ?></a>
        </p>

        <!-- <p class="opalestate-form-field register">
            <a href="<?php echo esc_url( opalestate_get_register_page_uri() ); ?>"><?php esc_html_e( 'Register now!', 'opalestate-pro' ); ?></a>
        </p> -->

		<?php do_action( 'login_form' ); ?>

		<?php do_action( 'opalestate_member_after_login_form' ); ?>
    </form>
</div>
