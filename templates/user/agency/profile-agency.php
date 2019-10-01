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

    <div class="opalestate-admin-box">

        <div class="box-content">
            <h3><?php esc_html_e( 'Edit Agency Profile', 'opalestate-pro' ); ?></h3>

			<?php
			do_action( 'opalestate_profile_agency_form_before' );

			if ( function_exists( 'cmb2_get_metabox_form' ) ) {
				echo cmb2_get_metabox_form( $metaboxes[ OPALESTATE_AGENCY_PREFIX . 'front' ], $post_id, [
					'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<button type="submit" name="submit-cmb"   class="button-primary btn btn-primary">%4$s</button></form>',
					'save_button' => esc_html__( 'Save Change', 'opalestate-pro' ),
				] );
			}

			do_action( 'opalestate_profile_agency_form_after' );
			?>
        </div>
    </div>
</div>
