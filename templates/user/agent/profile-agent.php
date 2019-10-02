<div class="property-submission-form">
    <div class="opalestate-admin-box">
        <div class="box-content">
            <h3><?php esc_html_e( 'Edit Agent Profile', 'opalestate-pro' ); ?></h3>

			<?php if ( isset( $metaboxes[ OPALESTATE_AGENT_PREFIX . 'front' ] ) ): ?>
				<?php
				do_action( 'opalestate_profile_agent_form_before' );

				if ( function_exists( 'cmb2_get_metabox_form' ) ) {
					echo cmb2_get_metabox_form( $metaboxes[ OPALESTATE_AGENT_PREFIX . 'front' ], $post_id, [
						'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<button type="submit" name="submit-cmb" class="button-primary btn btn-primary">%4$s</button></form>',
						'save_button' => esc_html__( 'Save Change', 'opalestate-pro' ),
					] );
				}

				do_action( 'opalestate_profile_agency_form_after' );
				?>
			<?php else : ?>
                <div class="alert alert-danger"><?php esc_html_e( 'Agent edit profile form is not avariable', 'opalestate-pro' ); ?></div>
			<?php endif; ?>
        </div>
    </div>
</div>
