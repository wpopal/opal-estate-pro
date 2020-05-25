<?php
$_id  = 'posts-block-' . rand( 1, 9 );
$item = apply_filters( 'opalesate_carousel_property_column', 3, isset( $args ) ? $args : [] );
$data = [
	'slidesPerView'  => 1,
	'spaceBetween'   => 10,
	// 'slidesPerGroup' => 1,
	'loop'           => false,
	'breakpoints'         => [ 1024 => [ "slidesPerView" => 3, 'spaceBetween'   => 30, ], 768 => [ "slidesPerView" => 2 ], 640 => [ "slidesPerView" => 1 ] ],
];

$template_style = isset( $args['style'] ) && $args['style'] ? sanitize_text_field( $args['style'] ) : 'content-property-grid';
?>
<div class="opalestate-box-content opalesate-related-properties">
    <h4 class="outbox-title"><?php echo esc_html( $heading ); ?></h4>
    <div class="opalesate-archive-bottom opalestate-rows">
		<?php if ( $query->have_posts() ) : ?>
            <div class="opalestate-swiper-play swiper-container" id="postcarousel-<?php echo esc_attr( $_id ); ?>" data-swiper="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
                <div class="swiper-wrapper">
					<?php
					$column = isset( $column ) ? $column : apply_filters( 'opalestate_properties_column_row', 3 );
					$clscol = floor( 12 / $column );
					while ( $query->have_posts() ) : $query->the_post(); ?>
                        <div class="swiper-slide">
							<?php echo opalestate_load_template_path( $template_style, null, $style ); ?>
                        </div>
					<?php endwhile; ?>
                </div>

				<?php if ( absint( $query->post_count ) > absint( $item ) ) : ?>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"> <i class="fas fa-angle-left"></i></div>
                    <div class="swiper-button-next">  <i class="fas fa-angle-right"></i></div>
				<?php endif; ?>
            </div>
		<?php else: ?>
			<?php echo opalestate_load_template_path( 'content-no-results' ); ?>
		<?php endif; ?>
    </div>
</div>
