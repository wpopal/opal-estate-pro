<?php
$query = OpalEstate_Search::get_search_agents_query();
if ( ! isset( $colum ) ) {
	$colum = 4;
}
$colum = apply_filters( 'opalestate_agent_grid_column', $colum );
?>
<div class="search-agents-wrap">
	<?php echo opalestate_load_template_path( 'parts/search-agents-form' ); ?>
	<?php if ( $query->have_posts() ): ?>
        <div class="agents-container">

            <div class="agents-head">
                <div class="agent-result">
                    <h3><?php echo sprintf( esc_html__( 'Found %s Agents', 'opalestate-pro' ), '<span class="text-primary">' . $query->found_posts . '</span>' ) ?></h3>
                </div>

                <div class="display-mode">
					<?php opalestate_show_display_modes(); ?>
                </div>
            </div>

            <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
				<?php $cnt = 0;
				while ( $query->have_posts() ): $query->the_post(); ?>
                    <div class="col-lg-3 col-md-3 col-sm-3 <?php if ( $cnt++ % $colum == 0 ): ?>first-child<?php endif; ?>">
						<?php echo opalestate_load_template_path( 'content-agent-grid' ); ?>
                    </div>
				<?php endwhile; ?>
            </div>
			<?php if ( $query->max_num_pages ): ?>
                <div class="w-pagination">
					<?php opalestate_pagination( $query->max_num_pages ); ?>
                </div>
			<?php endif; ?>
        </div>

	<?php else: ?>
        <div class="agents-results">
			<?php echo opalestate_load_template_path( 'content-no-results' ); ?>
        </div>
	<?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>
