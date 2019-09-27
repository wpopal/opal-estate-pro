<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="property-submission-form">
	<?php if ( ! empty( $_SESSION['messages'] ) ) : ?>
        <div class="opalesate_messages">
			<?php foreach ( $_SESSION['messages'] as $message ) : ?>

				<?php $status = isset( $message[0] ) ? $message[0] : 'success'; ?>
				<?php $msg = isset( $message[1] ) ? $message[1] : ''; ?>
                <div class="opalesate_message_line <?php echo esc_attr( $status ) ?>">
					<?php printf( '%s', $msg ) ?>
                </div>

			<?php endforeach;
			unset( $_SESSION['messages'] ); ?>
        </div>

	<?php endif; ?>

    <div class="opalestate-admin-box card-item inner">

        <div class="box-content">
			<?php
			$current_user = wp_get_current_user();
			$user_info    = '';
			if ( $current_user->display_name !== $current_user->user_login ) {
				$user_info = "<span class='username'>({$current_user->user_login})</span>";
			}
			?>

            <div class="heading">
                <i class="fa fa-user"></i>
                <span><?php esc_html_e( 'Edit User Profile', 'opalestate-pro' ); ?>&nbsp;<?php echo wp_kses_post( $user_info ); ?></span>

            </div>
			<?php
			do_action( 'opalestate_profile_form_before' );

			if ( function_exists( 'cmb2_get_metabox_form' ) ) {
				echo cmb2_get_metabox_form( $metaboxes[ OPALESTATE_USER_PROFILE_PREFIX . 'front' ], $user_id, [
					'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-cmb" value="%4$s" class="button-primary btn btn-primary"></form>',
					'save_button' => esc_html__( 'Save Change', 'opalestate-pro' ),
				] );
			}

			do_action( 'opalestate_profile_form_after' );
			?>
        </div>
    </div>

    <div class="opalestate-admin-box card-item inner">
        <div class="box-content">
            <div class="heading">
                <i class="fa fa-lock-open"></i>
                <span><?php esc_html_e( 'Change Password', 'opalestate-pro' ); ?></span>

            </div>

			<?php
			do_action( 'opalestate_profile_form_before' );

			if ( function_exists( 'cmb2_get_metabox_form' ) ) {
				echo cmb2_get_metabox_form( $metaboxes[ OPALESTATE_USER_PROFILE_PREFIX . 'frontchangepass' ], $user_id, [
					'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-cmb" value="%4$s" class="button-primary btn btn-primary"></form>',
					'save_button' => esc_html__( 'Save Change', 'opalestate-pro' ),
				] );
			}

			do_action( 'opalestate_profile_form_after' );
			?>
        </div>
    </div>
</div>
