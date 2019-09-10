<div class="property-preview property-preview-custom-size">
	<?php
	global $property;
	$galleries = $property->get_gallery();


	$image_size = opalestate_get_option( 'opalestate_thumbnail_size' );
	if ( ! empty( $galleries ) && isset( $galleries ) ):
		?>
		<?php
		$_id  = 'posts-block-' . rand( 1, 9 );
		$item = apply_filters( 'opalesate_related_property_column', 1 );

		$data = [
			'slidesPerView' => 1,
			'spaceBetween'  => 0,
			'effect'        => 'fade',
			'loop'          => true,
			'pagination'    => 0,
			'breakpoints'   => [ 1024 => [ "slidesPerView" => 1 ] ],
		];

		$columns = apply_filters( 'opalestate_thumbnail_nav_column', 5 );
		$src     = wp_get_attachment_url( get_post_thumbnail_id() );

		$show  = 9;
		$items = array_chunk( $galleries, $show );

		if ( count( $items[0] ) < $show ) {
			for ( $i = count( $items[0] ); $i < $show; $i++ ) {
				$items[0][ $i ] = 'none';
			}
			$hasMore = false;
		} else {
			$hasMore = true;
		}

		//	echo '<pre>' . print_r( $items,1 );die;
		?>


        <div class="gallery-metro-preview opalestate-gallery">
            <div class="metro-big">
                <a href="<?php echo esc_url( $src ); ?>" style="background-image:url('<?php echo esc_url( $src ); ?>')"></a>
            </div>
            <div class="metro-group-small">
				<?php if ( isset( $items[0] ) && is_array( $items[0] ) ): ?>
					<?php foreach ( $items[0] as $key => $src ): ?>
						<?php if ( $src == "none" ) : ?>
                            <div class="metro-small no-image">
                                <div class="show-first-photo">
                                </div>
                            </div>
						<?php else: ?>
                            <div class="metro-small">
								<?php if ( $hasMore && $key == count( $items[0] ) - 1 ):
									$content = $show . "+"; ?>
                                    <a href="<?php echo esc_url( $src ); ?>" class="has-more" style="background-image:url('<?php echo esc_url( $src ); ?>')">
                                        <span><em><?php echo $content; ?></em></span>
                                    </a>
									<?php if ( isset( $items[1] ) ): ?>
									<?php foreach ( $items[1] as $_src ): ?>
                                        <a class="hide" href="<?php echo esc_url( $_src ); ?>" style="background-image:url('<?php echo esc_url( $_src ); ?>')"></a>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php else : ?>
                                    <a href="<?php echo esc_url( $src ); ?>" style="background-image:url('<?php echo esc_url( $src ); ?>')"></a>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
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
