<div class="property-preview">
    <div class="preview-gallery-slider">
		<?php
		global $property;
		$galleries = $property->get_gallery();

		$single_image_size = opalestate_get_option( 'featured_image_size', 'full' );

		if ( ! empty( $galleries ) && isset( $galleries ) ):
			?>
			<?php
			$_id  = 'posts-block-' . rand( 1, 9 );
			$item = apply_filters( 'opalesate_related_property_column', 1 );

			$data = [
				'slidesPerView' => 1,
				'autoHeight'    => 1,
				'spaceBetween'  => 0,
				'effect'        => 'fade',
				'loop'          => true,
				'pagination'    => 0,
				'breakpoints'   => [ 1024 => [ "slidesPerView" => 1 ] ],
			];

			$columns = apply_filters( 'opalestate_thumbnail_nav_column', 5 );
			?>

            <div class="opalestate-swiper-wrap">
                <div class="opalestate-swiper-play swiper-container"
                     id="postcarousel-<?php echo esc_attr( $_id ); ?>"
                     data-swiper="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
                    <div class="swiper-wrapper">

						<?php if ( has_post_thumbnail() ): ?>
                            <div class="swiper-slide"><?php the_post_thumbnail( $single_image_size ); ?></div>
						<?php endif; ?>

						<?php if ( isset( $galleries ) && is_array( $galleries ) ): ?>
							<?php foreach ( $galleries as $key => $src ): ?>
                                <div class="swiper-slide"><img src="<?php echo esc_url( wp_get_attachment_image_url( $key, $single_image_size ) ); ?>" alt="gallery"></div>
							<?php endforeach; ?>
						<?php endif; ?>

                    </div>
                </div>
            </div>
		<?php else : ?>

			<?php if ( has_post_thumbnail() ): ?>
                <div class="property-thumbnail">
					<?php the_post_thumbnail( $single_image_size ); ?>
                </div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( isset( $showinfo ) && $showinfo ): ?>
            <div class="property-abs-info">
                <div class="property-meta">
					<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
                    <div class="property-address clearfix">
                        <div class="pull-left">
							<?php if ( $property->latitude && $property->longitude ) : ?>
                                <a href="<?php echo esc_url( $property->get_google_map_link() ); ?>" rel="nofollow" target="_blank">
                                <span class="property-view-map">
							    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                </a>
							<?php endif; ?>
							<?php echo esc_html( $property->get_address() ); ?>
                        </div>
                    </div>
                </div>
				<?php opalestate_get_single_short_meta(); ?>

                <div class="single-price-content"><?php opalestate_property_loop_price(); ?></div>
            </div>
		<?php endif; ?>
    </div>
</div>
