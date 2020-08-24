<div class="property-preview">
	<?php
	global $property;
	$galleries         = $property->get_gallery();
	$thumb_image_size  = opalestate_get_option( 'opalestate_thumbnail_size', 'medium' );
	$single_image_size = opalestate_get_option( 'featured_image_size', 'full' );

	if ( isset( $galleries ) && $galleries ):
		?>
		<?php
		$_id  = 'posts-block-' . rand( 1, 9 );
		$item = apply_filters( 'opalesate_related_property_column', 1 );

		$data = [
			'slidesPerView'  => 1,
			'spaceBetween'   => 0,
			'loop'           => true,
			'autoHeight'     => 1,
			'pagination'     => 0,
			'effect'         => 'slide',
			'breakpoints'    => [ 1024 => [ "slidesPerView" => 1 ] ],
			'thumbnails_nav' => "#swiper-pagination-images",
			'navigation'          => [
				'nextEl' => '.swiper-button-next',
				'prevEl' => '.swiper-button-prev',
			],
		];

		$columns = apply_filters( 'opalestate_thumbnail_nav_column', 5 );

		$datanav = [
			'slidesPerView' => $columns,
			'spaceBetween'  => 10,
			'effect'        => 'slide',

			'slideToClickedSlide' => true,
			'touchRatio'          => 0.2,
			'loop'                => false,
			'breakpoints'         => [ 1024 => [ "slidesPerView" => 5 ], 768 => [ "slidesPerView" => 3 ], 0 => [ "slidesPerView" => 3 ] ],
			'navigation'          => [
				'nextEl' => '.swiper-button-next',
				'prevEl' => '.swiper-button-prev',
			],
		];
		?>

        <div class="opalestate-swiper-wrap">
            <div class="opalestate-swiper-play swiper-container"
                 id="postcarousel-<?php echo esc_attr( $_id ); ?>"
                 data-swiper="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
                <div class="swiper-wrapper opalestate-gallery">
					<?php if ( has_post_thumbnail() ): ?>
                        <div class="swiper-slide">
							<?php the_post_thumbnail( $single_image_size ); ?>
                        </div>
					<?php endif; ?>
					<?php if ( isset( $galleries ) && is_array( $galleries ) && $galleries ): ?>
						<?php foreach ( $galleries as $key => $src ): ?>
                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( wp_get_attachment_image_url( $key, $single_image_size ) ); ?>" alt="gallery">
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>
                <div class="swiper-button-prev"><i class="fas fa-angle-left"></i></div>
                <div class="swiper-button-next"><i class="fas fa-angle-right"></i></div>
            </div>

            <div class="swiper-pagination-images swiper-container" id="swiper-pagination-images" data-swiper="<?php echo esc_attr( wp_json_encode( $datanav ) ); ?>">
				<?php
				$count_galleries = 0;
				if ( has_post_thumbnail() ) {
					$count_galleries = $count_galleries + 1;
				}

				$count_galleries = $count_galleries + count( $galleries );
				?>
				<?php if ( $count_galleries > $columns ) : ?>
                    <div class="swiper-button-prev"><i class="fas fa-angle-left"></i></div>
                    <div class="swiper-button-next"><i class="fas fa-angle-right"></i></div>
				<?php endif; ?>

                <div class="swiper-wrapper">
					<?php if ( has_post_thumbnail() ):
						?>
                        <div class="swiper-slide">
                            <div style="background-image:url('<?php echo wp_get_attachment_thumb_url( get_post_thumbnail_id(), $thumb_image_size ); ?>');" class="thumb-nav"></div>
                        </div>
					<?php endif; ?>

					<?php if ( isset( $galleries ) && is_array( $galleries ) && $galleries ): ?>
						<?php foreach ( $galleries as $key => $src ): ?>
                            <div class="swiper-slide">
                                <div style="background-image:url('<?php echo wp_get_attachment_image_url( $key, $thumb_image_size ); ?>');" class="thumb-nav"></div>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>
            </div>
        </div>
	<?php else : ?>
		<?php if ( has_post_thumbnail() ): ?>
            <div class="property-thumbnail">
				<?php the_post_thumbnail( 'full' ); ?>
            </div>
		<?php endif; ?>
	<?php endif; ?>
</div>
