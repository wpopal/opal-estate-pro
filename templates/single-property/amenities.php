<?php
global $property, $post;

$amenities  = $property->get_amenities();

?>
<?php if ( $property->get_block_setting( 'amenities' ) && $amenities ): ?>
    <div class="property-amenities box-inner-summary">
        <h5 class="list-group-item-heading"><?php esc_html_e( "Amenities", "opalestate" ); ?></h5>
        <div class="list-group-item-text">
            <div class="opal-row">
				<?php foreach ( $amenities as $amenity ): ?>
                    <div class="col-lg-4 col-sm-4 <?php if ( has_term( $amenity->term_id, 'opalestate_amenities', $post ) ) : ?>active<?php endif; ?>">
                        <?php
                        if ( $image_id = get_term_meta( $amenity->term_id, 'opalestate_amt_image_id', true )) {
                            echo wp_get_attachment_image( $image_id );
                        }
                        ?>
						<?php echo esc_html( $amenity->name ); ?> <i class="fa fa-check"></i>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
