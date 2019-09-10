<?php
global $post;

$limit = apply_filters( 'opalesate_agent_properties_limit', 6 );
$query = Opalestate_Query::get_agent_property( null, get_the_ID(), $limit, 10, true );

if ( $query->have_posts() ) :

	$data = [
		'slidesPerView' => 1,
		'spaceBetween'  => 0,
		'effect'        => 'slide',
		'loop'          => true,
		'breakpoints'   => [ 1024 => [ "slidesPerView" => 1 ] ],
	];
	?>

    <div class="my-featured-section" id="block-featured-properties">
        <h4 class="box-heading"><?php echo sprintf( esc_html__( 'Featured Properties', 'opalestate-pro' ), $query->found_posts ); ?></h4>

        <div class="opalestate-swiper-play swiper-container" id="postcarousel-<?php echo esc_attr( get_the_ID() ); ?>"
             data-swiper="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
            <div class="swiper-wrapper">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="swiper-slide">
						<?php echo opalestate_load_template_path( 'content-property-grid' ); ?>
                    </div>
				<?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif;
wp_reset_postdata();
