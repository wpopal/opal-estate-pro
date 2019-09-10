<?php
if ( ! function_exists( 'cmb2_get_metabox_form' ) || ! isset( $metaboxes ) ) {
	return;
}

$nonce = wp_nonce_field( 'submitted-property', 'submission_action', true );

?>
<div class="property-submission-form">
    <div class="box-content-inner">
        <div class="metabox-fields-front space-padding-lr-40 space-padding-tb-30">
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
			<div class="submission-heading text-center">
			<?php if ( ! $post_id ) : ?>
                <h1><?php esc_html_e( 'Add New Property', 'opalestate-pro' ); ?></h1>
			<?php else : ?>
                <h1><?php esc_html_e( 'Edit Property', 'opalestate-pro' ); ?></h1>
			<?php endif; ?>
			</div>

			<?php do_action( 'opalestate_submission_form_before' ); ?>
			<?php if ( $navigation ) : ?>
                <div class="opalestate-submission-tab">
                    <div class="opalestate-submission-tab-head">
						<?php foreach ( $navigation as $key => $value ): ?>
							<?php if ( $value['status'] ) : ?>
                                <a href="#opalestate-submission-<?php echo esc_attr( $key ); ?>" class="tab-item">
									<?php echo esc_html( $value['title'] ); ?>
                                </a>
							<?php endif; ?>
						<?php endforeach; ?>
                    </div>
                    <div class="opalestate-submission-tab-wrap">
						<?php
						echo cmb2_get_metabox_form( $metaboxes[ OPALESTATE_PROPERTY_PREFIX . 'front' ], $post_id, [
							'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form opalestate-submission-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-cmb" value="%4$s" class="button-primary btn btn-primary btn-submit-cmb">'.$nonce.'</form>',
							'save_button' => esc_html__( 'Save property', 'opalestate-pro' ),
						] ); ?>
                    </div>
                </div>
			<?php endif; ?>

			<?php do_action( 'opalestate_submission_form_after' ); ?>
        </div>
    </div>
</div>
