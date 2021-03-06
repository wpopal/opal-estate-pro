<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $property, $post;
$property = opalesetate_property( get_the_ID() );
?>
<article itemscope itemtype="http://schema.org/Property" <?php post_class( 'property-grid-v1' ); ?>>

    <div class="property-grid">
        <div class="entry-content">

			<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>

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
            </div>
        </div><!-- .entry-content -->

        <header class="property-grid__header">
            <div class="property-group-label">
		        <?php opalestate_property_label(); ?>
            </div>

            <div class="property-group-status">
		        <?php opalestate_property_status(); ?>
            </div>
			<?php opalestate_get_loop_thumbnail( opalestate_get_option( 'loop_image_size', 'large' ) ); ?>
			<?php echo wp_kses_post( $property->render_author_link() ); ?>

            <div class="property-options">
				<?php if ( ( $count = $property->get_gallery_count() ) && $count > 1 ) : ?>
                    <a href="#view-gallery" class="opalestate-ajax-gallery" data-id="<?php echo get_the_ID(); ?>"><i class="fa fa-camera"></i></a>
				<?php endif; ?>
            </div>
        </header>

		<?php opalestate_get_loop_short_meta(); ?>

        <div class="entry-content-bottom clearfix">
            <div class="property-meta-bottom">
				<?php opalestate_property_loop_price(); ?>
            </div>

			<?php echo do_shortcode( '[opalestate_favorite_button property_id=' . get_the_ID() . ']' ); ?>
        </div>
    </div><!-- .property-grid -->
	<?php do_action( 'opalestate_after_property_loop_item' ); ?>
    <meta itemprop="url" content="<?php the_permalink(); ?>"/>
</article><!-- #post-## -->
