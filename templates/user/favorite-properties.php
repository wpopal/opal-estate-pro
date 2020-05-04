<?php if( $loop->have_posts() ): ?>
<div class="property-listing my-favorite">
 		<div class="box-content">

			<div class="opalestate-rows">
				<div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row');?>">
				<?php $cnt=0; while ( $loop->have_posts() ) : $loop->the_post(); global $post;  ?>

	 				<div class="col-lg-4 col-md-4">
                    	<?php echo opalestate_load_template_path( 'content-property-grid' ); ?>
                	</div>

				<?php endwhile; ?>
				</div>
			</div>
			<?php opalestate_pagination( $loop->max_num_pages ); ?>
		</div>
</div>
<?php else : ?>
	<div class="opalestate-box">
	 	<div class="box-content">
		 	<div class="opalestate-message">
		 		<h3><?php esc_html_e( 'No item in your favorite', 'opalestate-pro' ) ;?></h3>
				<p><?php esc_html_e( 'You have not added any property as favorite.', 'opalestate-pro' ) ;?></p>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
