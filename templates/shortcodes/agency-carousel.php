<?php 
$_id = time();
$query = Opalestate_Query::get_agencies( array("posts_per_page"=>$limit, 'paged' => $paged), $onlyfeatured );
$item = $column;
$data = [
	'slidesPerView'  => $item,
	'spaceBetween'   => 30,
	'slidesPerGroup' => $item,
	'loop'           => false,
];

?>

 <div class="opalesate-agency-carousel opalestate-rows">
		<?php if ( $query->have_posts() ) : ?>
        <div class="opalestate-swiper-play swiper-container" id="postcarousel-<?php echo esc_attr( $_id ); ?>" data-swiper="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
            <div class="swiper-wrapper">
				<?php
				$column = 5;
				$clscol = floor( 12 / $column );
				while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="swiper-slide">
						<?php echo opalestate_load_template_path( 'content-agency-grid' ); ?>
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