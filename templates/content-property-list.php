<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$property = opalesetate_property( get_the_ID() );

global $property, $post;

?>
<article itemscope itemtype="http://schema.org/Property" <?php post_class('property-list-style-v1'); ?>>
	<div class="property-list container-cols-3">
		<header class="property-list__header">
            <div class="property-group-label">
				<?php opalestate_property_label(); ?>
            </div>

            <div class="property-group-status">
				<?php opalestate_property_status(); ?>
            </div>

            <div class="property-meta-bottom clearfix">
					
					<div class="meta-item"><?php echo wp_kses_post( $property->render_author_link() ); ?></div>
					<div class="meta-item">
						<?php echo do_shortcode('[opalestate_favorite_button property_id='.get_the_ID() .']'); ?>
					</div>
				</div>
				
			<?php opalestate_get_loop_thumbnail( opalestate_get_option('loop_image_size','large') ); ?>
			 	
		</header>

		<div class="abs-col-item">
			<div class="entry-content">
			
				<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
					
			  	<div class="property-address">
				  	<?php if( $property->latitude && $property->longitude ) : ?>
						<a href="<?php echo esc_url( $property->get_google_map_link() ); ?>" rel="nofollow" target="_blank">
                            <span class="property-view-map"><i class="fas fa-map-marker-alt"></i></span>
                        </a>
					<?php endif ; ?>

				    <?php if ( $property->get_address() ) : ?>
                        <span class="property-address__text"><?php echo esc_html( $property->get_address() ); ?></span>
				    <?php endif; ?>
				</div>

                <?php opalestate_property_loop_price(); ?>

			</div><!-- .entry-content -->
			<?php opalestate_get_loop_short_meta(); ?>
			
		</div> 
		
		<div class="entry-summary">
			<h5><?php echo esc_html__( 'Description', 'opalestate-pro' ); ?></h5>
			<?php echo wp_trim_words( get_the_excerpt(), 30, '...' ); ?>
		</div><!-- .entry-summary -->
	</div>	
	
	<?php do_action( 'opalestate_after_property_loop_item' ); ?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</article><!-- #post-## -->
