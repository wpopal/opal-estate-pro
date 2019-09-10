<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $property, $post;
$property = opalesetate_property( get_the_ID() );
$bg = get_the_post_thumbnail_url( get_the_ID(), opalestate_get_option( 'loop_image_size', 'large' ) );
$style = $bg ? 'style="background-image: url(' . esc_url( $bg ) . ');"' : '';
?>
<article itemscope itemtype="http://schema.org/Property" <?php post_class( 'property-mark-hover-item' ); ?>>
    <div class="property-grid">
        <header class="property-grid__header" <?php echo $style; ?>>
            <div class="entry-content">
				<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
                <div class="property-meta">
                    <div class="property-price-wrapper"><?php opalestate_property_loop_price(); ?></div>
                    <div class="property-areasize"><?php echo apply_filters( 'opalestate_areasize_unit_format', $property->get_metabox_value( 'areasize' ) ); ?></div>
                </div>
                <?php // opalestate_get_loop_short_meta(); ?>
            </div><!-- .entry-content -->
        </header>

		<?php do_action( 'opalestate_after_property_loop_item' ); ?>
    </div>    <!-- .property-grid -->
    <meta itemprop="url" content="<?php the_permalink(); ?>"/>
</article><!-- #post-## -->