<?php
$post_id = intval( $_GET['id'] );
$post    = get_post( $post_id );
$type    = OpalEstate()->session->get( 'submission' );
?>
<div class="opalestate-box-completed alert alert-success">
    <div class="inner">
		<?php if ( $type == 'addnew' ): ?>
            <div class="addnew-msg">
				<?php if ( 'on' != opalestate_get_option( 'admin_approve', 'on' ) ) : ?>
                    <h5><?php esc_html_e( 'Thanks for your submission, your property is published.', 'opalestate-pro' ); ?></h5>
				<?php else : ?>
                    <h5><?php esc_html_e( 'Thanks for your submission, it takes some time to review the property.', 'opalestate-pro' ); ?></h5>
				<?php endif; ?>

				<?php echo sprintf( esc_html__( 'Click %s here %s to view your listing or %s edit %s this.', 'opalestate-pro' ),
					'<a href="' . opalestate_submssion_list_page() . '">', '</a>',
					'<a href="' . opalestate_submssion_page( $post_id ) . '">', '</a>'
				); ?>
            </div>
		<?php else : ?>
            <div class="edit-msg">
				<?php esc_html_e( 'Your property is completed success.', 'opalestate-pro' ); ?>
				<?php echo sprintf( esc_html__( 'Click %s here %s to view your listing or %s edit %s this.', 'opalestate-pro' ),
					'<a href="' . opalestate_submssion_list_page() . '">', '</a>',
					'<a href="' . opalestate_submssion_page( $post_id ) . '">', '</a>'
				); ?>
            </div>
		<?php endif; ?>
    </div>
</div>

<?php OpalEstate()->session->set( 'submission', null ); ?>
