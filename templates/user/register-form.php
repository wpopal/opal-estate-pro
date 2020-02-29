<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    $package$
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

opalestate_print_notices();

if ( is_user_logged_in() ) {
	_e( 'You are currently logged in.', 'opalestate-pro' );

	return;
}

$types = OpalEstate_User::get_user_types();
$type_default = apply_filters( 'opalestate_register_form_default_type', 'subscriber' );
?>
<div class="opalesate-form">
	<?php if ( $hide_title === false ) : ?>
        <h3><?php esc_html_e( 'Register', 'opalestate-pro' ); ?></h3>
	<?php endif; ?>

	<?php if ( $message ) : ?>
        <p><?php printf( '%s', $message ) ?></p>
	<?php endif; ?>

    <form method="POST" class="opalestate-register-form opalestate-member-form">

		<?php do_action( 'opalestate_member_before_register_form' ); ?>

        <p class="opalestate-form-field username validate-required">
            <label for="reg_username"><?php esc_html_e( 'Username', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <input type="text" class="opalestate-input input-text" name="username" id="reg_username" required="required" value="<?php if ( ! empty( $_POST['username'] ) ) {
				echo esc_attr( $_POST['username'] );
			} ?>"/>
        </p>

        <p class="opalestate-form-field email validate-required">
            <label for="reg_email"><?php esc_html_e( 'Email address', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <input type="email" class="opalestate-input input-text" name="email" id="reg_email" required="required" value="<?php if ( ! empty( $_POST['email'] ) ) {
				echo esc_attr( $_POST['email'] );
			} ?>"/>
        </p>

        <p class="opalestate-form-field password validate-required">
            <label for="reg_password"><?php esc_html_e( 'Password', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <input type="password" class="opalestate-input input-text" name="password" required="required" id="reg_password"/>
        </p>

        <p class="opalestate-form-field password confirm-password validate-required">
            <label for="reg_password1"><?php esc_html_e( 'Repeat-Password', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <input type="password" class="opalestate-input input-text" name="password1" required="required" id="reg_password1"/>
        </p>

		<?php if ( $types ): ?>

            <p class="opalestate-form-field usertype validate-required">
                <label for="userrole"><?php esc_html_e( 'Type', 'opalestate-pro' ); ?> <span class="required">*</span></label>
                <select name="role" id="userrole" class="form-control">
					<?php foreach ( $types as $type => $label ): ?>
                        <option value="<?php echo $type; ?>" <?php selected( $type, $type_default, true ); ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
                </select>
            </p>
		<?php endif; ?>

        <?php $terms_page = get_permalink( opalestate_get_option( 'user_terms_page', '/' ) ); ?>
        <p class="opalestate-form-field i-agree validate-required">
            <label><?php esc_html_e( 'I agree with', 'opalestate-pro' ); ?> <span class="required">*</span></label>
            <a href="<?php echo esc_url( $terms_page ? $terms_page : '#' ); ?>" title="<?php esc_attr_e( 'terms & conditions', 'opalestate-pro' ); ?>" target="_blank"><?php esc_html_e( 'terms & conditions', 'opalestate-pro' ); ?></a>
            <input type="checkbox" name="confirmed_register" id="confirmed_register" required="required" class="comfirmed-box"/>
        </p>

		<?php do_action( 'opalestate_register_form' ); ?>
		<?php do_action( 'register_form' ); ?>

        <p class="opalestate-form-field submit">
			<?php wp_nonce_field( 'opalestate-register', 'opalestate-register-nonce' ); ?>
			<?php if ( $redirect ) : ?>
                <input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>">
			<?php endif; ?>
            <input type="submit" class="opalestate-button button btn btn-primary" name="register" value="<?php esc_attr_e( 'Register', 'opalestate-pro' ); ?>"/>
        </p>

		<?php do_action( 'opalestate_member_after_register_form' ); ?>
    </form>
</div>
