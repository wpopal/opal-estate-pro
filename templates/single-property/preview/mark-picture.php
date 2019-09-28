<?php global $property; ?>
<div class="property-preview property-mark-pics-preview">

	<?php if ( has_post_thumbnail() ): ?>
        <div class="property-thumbnail">
			<?php the_post_thumbnail( 'full' ); ?>
        </div>
	<?php endif; ?>
    <div class="container property-heading-top">
        <div class="property-single-info">
            <div class="group-items">
				<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
				<?php opalestate_property_status(); ?>

                <div class="property-meta">
                    <div class="property-address clearfix">
                        <div class="pull-left">
							<?php if ( $property->latitude && $property->longitude ) : ?>
                                <a href="<?php echo esc_url( $property->get_google_map_link() ); ?>" rel="nofollow" target="_blank">
                                    <span class="property-view-map"><i class="fas fa-map-marker-alt"></i></span>
                                </a>
							<?php endif; ?>
							<?php echo esc_html( $property->get_address() ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-price-content"><?php opalestate_property_loop_price(); ?></div>
        </div>
    </div>
</div>
