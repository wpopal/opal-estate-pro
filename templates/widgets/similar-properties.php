<?php
/**
 * Similar properties.
 *
 * @author     WpOpal Team <help@wpopal.com, info@wpopal.com>
 * @copyright  Copyright (C) 2015 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/questions/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$args = [
	'post_type'      => 'opalestate_property',
	'posts_per_page' => $num,
	'post__not_in'   => [ get_the_ID() ],
];

$terms = wp_get_post_terms( get_the_ID(), 'opalestate_types' );

$tax_query = [];
if ( $terms ) {
	$tax_query[] = [
		[
			'taxonomy' => 'opalestate_types',
			'field'    => 'slug',
			'terms'    => $terms[0]->slug,
		],
	];
}

$status = wp_get_post_terms( get_the_ID(), 'opalestate_status' );
if ( ! is_wp_error( $status ) && $status ) {
	$tax_query[] =
		[
			'taxonomy' => 'opalestate_status',
			'field'    => 'slug',
			'terms'    => $status[0]->slug,

		];
}


if ( $tax_query ) {
	$args['tax_query'] = [ 'relation' => 'AND' ];
	$args['tax_query'] = array_merge( $args['tax_query'], $tax_query );
}
$query = Opalestate_Query::get_property_query( $args );

if ( $query->have_posts() ):

	echo trim( $before_widget );
	//Our variables from the widget settings.
	$title = apply_filters( 'widget_title', esc_attr( $instance['title'] ) );

	if ( $title ) {
		echo ( $before_title ) . trim( $title ) . $after_title;
	}
	?>

    <div class="widget-content widget-properties">
		<?php

		while ( $query->have_posts() ): $query->the_post();
			$property = opalesetate_property( get_the_ID() );
			$meta     = $property->get_meta_shortinfo();
			?>
            <article itemscope itemtype="http://schema.org/Property" <?php post_class(); ?>>
                <div class="media">
                    <div class="media-left">
						<?php if ( has_post_thumbnail() ) : ?>
                            <div class="property-box-image">
                                <a href="<?php the_permalink(); ?>" class="image-inner ">
									<?php echo the_post_thumbnail( 'thumbnail' ); ?>
                                </a>
                            </div>
						<?php endif; ?>
                    </div>
                    <div class="media-body">
                        <div class="entry-content">
							<?php the_title( '<h6 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h6>' ); ?>

                            <div class="property-price">
                                <span class="text-primary"><?php echo opalestate_price_format( $property->get_price() ); ?></span>

								<?php if ( $property->get_sale_price() ): ?>
                                    <span class="property-saleprice">
                                        <?php echo opalestate_price_format( $property->get_sale_price() ); ?>
                                    </span>
								<?php endif; ?>

								<?php if ( $property->get_price_label() ): ?>
                                    <span class="property-price-label">
                                        <?php echo $property->get_price_label(); ?>
                                    </span>
								<?php endif; ?>
                            </div>
                        </div><!-- .entry-content -->
                    </div>
                </div>
            </article><!-- #post-## -->
		<?php endwhile; ?>
    </div>

	<?php echo trim( $after_widget ); ?>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
