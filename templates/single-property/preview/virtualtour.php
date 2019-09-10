<?php
global $property;
$virtualTour =  $property->get_virtual_tour();
?> 
<?php if( $virtualTour  ) : ?>
    <div class="property-preview property-preview-custom-size">
        <?php echo do_shortcode( $virtualTour ); ?>
    </div>
<?php else : ?>
<?php if ( has_post_thumbnail() ): ?>
		<div class="property-thumbnail">
			<?php the_post_thumbnail( 'full' ); ?>
		</div>	
	<?php endif; ?>    
<?php endif; ?>
