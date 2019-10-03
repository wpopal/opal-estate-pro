<?php
global $property, $post;

$amenities = $property->get_amenities();

?>
<?php if ( 'on' === $property->get_block_setting( 'amenities' ) && $amenities ): ?>
    <div class="property-amenities box-inner-summary">
        <h5 class="list-group-item-heading"><?php esc_html_e( 'Amenities', 'opalestate-pro' ); ?></h5>
        <div class="list-group-item-text">
            <div class="opal-row">
				<?php foreach ( $amenities as $amenity ): ?>
					<?php
					if ( apply_filters( 'opalestate_hide_unset_amenity', false ) && ! has_term( $amenity->term_id, 'opalestate_amenities', $post ) ) {
						continue;
					}
					?>
                    <div class="col-lg-4 col-sm-4">
                        <div class="amenity-item <?php echo has_term( $amenity->term_id, 'opalestate_amenities', $post ) ? 'active' : ''; ?>">
							<?php
							if ( $icon = get_term_meta( $amenity->term_id, 'opalestate_amt_icon', true ) ) {
								echo '<span class="amenity-icon"><i class="' . esc_attr( $icon ) . '"></i></span>';
							} elseif ( $image_id = get_term_meta( $amenity->term_id, 'opalestate_amt_image_id', true ) ) {
								echo wp_get_attachment_image( $image_id );
							}
							?>
							<?php echo esc_html( $amenity->name ); ?>&nbsp;
							<?php if ( has_term( $amenity->term_id, 'opalestate_amenities', $post ) ) : ?>
                                <?php echo apply_filters( 'opalestate_amenity_check_icon', '<i class="fa fa-check"></i>' ); ?>
							<?php else : ?>
								<?php echo apply_filters( 'opalestate_amenity_uncheck_icon', '<i class="fa fa-check"></i>' ); ?>
							<?php endif; ?>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
