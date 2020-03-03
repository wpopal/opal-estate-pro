<?php global $wp_query; ?>
<div class="opalesate-archive-top"><div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row');?>">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="opalestate-results">
			<?php if( $wp_query->found_posts ): ?> 
				<span><?php echo sprintf( esc_html__( 'Found %s Properties', 'opalestate-pro' ) , $wp_query->found_posts ); ?></span>
			<?php endif; ?>
		</div>		
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 text-right">
		<div class="opalestate-sortable">
			<?php echo opalestate_render_sortable_dropdown(); ?>
		</div>
		<?php opalestate_show_display_modes(); ?>
	</div>
</div></div>	
