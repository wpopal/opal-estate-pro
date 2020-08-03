<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'attachments' ) ) {
	return;
}

$attachments = $property->get_attachments();
?>
<?php if ( $attachments ) : ?>
    <div class="property-attachments box-inner-summary">
        <h5 class="list-group-item-heading"><?php esc_html_e( 'Attachments', 'opalestate-pro' ); ?></h5>
        <div class="list-group-item-text">
            <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
				<?php if ( is_array( $attachments ) ) : ?>
					<?php foreach ( $attachments as $id => $attachment ) : ?>
                        <div class="col-lg-4 col-sm-4">
                            <i class="text-secondary fa fa-file-text-o"></i>
                            <a class="property-attachments__name" href="<?php echo esc_url( $attachment ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a>
                            <a class="property-attachments__download" href="<?php echo esc_url( $attachment ); ?>" download><?php esc_html_e( 'Download', 'opalestate-pro' ); ?></a>
                        </div>
					<?php endforeach; ?>
				<?php else : ?>
					<?php $attachment_id = absint( $attachments ); ?>
                    <div class="col-lg-4 col-sm-4">
                        <i class="text-secondary fa fa-file-text-o"></i>
                        <a class="property-attachments__name" href="<?php echo esc_url( get_permalink( $attachment_id ) ); ?>"><?php echo esc_html( get_the_title( $attachment_id ) ); ?></a>
                        <a class="property-attachments__download" href="<?php echo esc_url( get_permalink( $attachment_id ) ); ?>" download><?php esc_html_e( 'Download', 'opalestate-pro' ); ?></a>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
