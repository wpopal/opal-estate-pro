<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $property, $post;
$property = opalesetate_property( get_the_ID() );
?>
<article itemscope itemtype="http://schema.org/Property" <?php post_class( "property-list-style-v2" ); ?>>
    <div class="property-list container-cols-2">
        <header class="property-list__header">
            <div class="property-group-label">
		        <?php opalestate_property_label(); ?>
            </div>

            <div class="property-group-status">
		        <?php opalestate_property_status(); ?>
            </div>
			<?php opalestate_get_loop_thumbnail( opalestate_get_option( 'loop_image_size', 'large' ) ); ?>
        </header>

        <div class="abs-col-item">
            <div class="entry-content">

				<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>

                <div class="property-address">
					<?php echo esc_html( $property->get_address() ); ?>
                </div>

				<?php opalestate_property_loop_price(); ?>
            </div><!-- .entry-content -->
			<?php opalestate_get_loop_short_meta(); ?>
        </div>
    </div>
	<?php do_action( 'opalestate_after_property_loop_item' ); ?>
    <meta itemprop="url" content="<?php the_permalink(); ?>"/>
</article><!-- #post-## -->
