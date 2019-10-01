<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $property, $post;
$property = opalesetate_property( get_the_ID() );

?>
<article itemscope itemtype="http://schema.org/Property" <?php post_class( 'property-featured-v1' ); ?>>
    <div class="property-featured">
        <div class="featured-info">
            <div class="entry-content">
                <div class="property-group-label">
		            <?php opalestate_property_label(); ?>
                </div>

				<?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>

                <div class="entry-summary">
                    <div class="property-address">
						<?php if ( $property->latitude && $property->longitude ) : ?>
                            <a href="<?php echo esc_url( $property->get_google_map_link() ); ?>" rel="nofollow" target="_blank">
                                <span class="property-view-map"><i class="fas fa-map-marker-alt"></i></span>
                            </a>
						<?php endif; ?>

	                    <?php if ( $property->get_address() ) : ?>
                            <span class="property-address__text"><?php echo esc_html( $property->get_address() ); ?></span>
	                    <?php endif; ?>
                    </div>
                    <p class="property-description">
						<?php echo opalestate_fnc_excerpt( 38, '...' ); ?>
                    </p>
                </div>
            </div><!-- .entry-content -->

			<?php opalestate_get_loop_short_meta(); ?>
            <div class="property-meta-bottom">
				<?php opalestate_property_loop_price(); ?>
            </div>
			<?php do_action( 'opalestate_after_property_loop_item' ); ?>
        </div>

        <header class="property-featured__header">
            <div class="property-group-status">
		        <?php opalestate_property_status(); ?>
            </div>
			<?php
			if ( has_post_thumbnail() ) {
				$image = get_the_post_thumbnail_url( get_the_ID(), opalestate_get_option( 'loop_image_size', 'large' ) );
			} else {
				$image = opalestate_get_image_placeholder( opalestate_get_option( 'loop_image_size', 'large' ) );
			}
			?>

            <div class="property-bg-thumbnail">
                <a href="<?php echo esc_url( get_permalink() ); ?>" style="background-image:url('<?php echo esc_url( $image ); ?>')"></a>
            </div>

            <div class="entry-content-bottom clearfix">
				<?php echo wp_kses_post( $property->render_author_link() ); ?>
				<?php echo do_shortcode( '[opalestate_favorite_button property_id=' . get_the_ID() . ']' ); ?>
            </div>
        </header>
    </div>    <!-- .property-featured -->
    <meta itemprop="url" content="<?php the_permalink(); ?>"/>
</article><!-- #post-## -->
