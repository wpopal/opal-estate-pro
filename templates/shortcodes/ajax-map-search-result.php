<?php
$list_layout = 'content-property-' . opalestate_get_option( 'property_archive_list_layout', 'list' );
$grid_layout = 'content-property-' . opalestate_get_option( 'property_archive_grid_layout', 'grid' );

if ( ! class_exists( 'OpalEstate_Search' ) ) {
	return;
}

$query = OpalEstate_Search::get_search_results_query();

$show = ( isset( $_GET['display'] ) && $_GET['display'] == 'list' ) || ( opalestate_options( 'displaymode', 'grid' ) == 'list' ) || ( isset( $style ) && $style == 'list' ) ? 'list' : 'grid';
?>
    <div class="opaleslate-ajax-search-results-container">
        <div class="opalesate-archive-top">
            <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="opalestate-results">
						<?php if ( $query->found_posts ): ?>
                            <span><?php echo sprintf( esc_html__( 'Found %s Properties', 'opalestate-pro' ), $query->found_posts ); ?></span>
						<?php endif; ?>
                        <div class="pull-right"> <?php echo opalestate_load_template_path( 'user-search/render-form' ); ?>    </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-right">

                    <div class="opalestate-sortable">
						<?php echo opalestate_render_sortable_dropdown(); ?>
                    </div>
					<?php opalestate_show_display_modes( 'grid' ); ?>
                </div>
            </div>
        </div>

        <div class="opalesate-archive-bottom opalestate-rows opalestate-collection">
			<?php if ( $query->have_posts() ): ?>
                <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
					<?php if ( $show == 'list' ): ?>
						<?php $cnt = 0;
						while ( $query->have_posts() ) : $query->the_post(); ?>
                            <div class="col-lg-12 col-md-12 col-sm-12" data-related="map" data-id="<?php echo $cnt++; ?>">
								<?php echo opalestate_load_template_path( $list_layout ); ?>
                            </div>
						<?php endwhile; ?>
					<?php else : ?>
						<?php
						$column = isset( $column ) ? $column : apply_filters( 'opalestate_properties_column_row', 3 );
						$layout = isset( $style ) ? 'content-property-' . $style : $grid_layout;
						$clscol = floor( 12 / $column );
						$cnt    = 0;
						while ( $query->have_posts() ) : $query->the_post();
							$cls = '';
							if ( $cnt++ % $column == 0 ) {
								$cls .= ' first-child';
							}
							?>
                            <div class="<?php echo $cls; ?> col-lg-<?php echo esc_attr( $clscol ); ?> col-md-<?php echo esc_attr( $clscol ); ?> col-sm-6" data-related="map" data-id="<?php echo
							esc_attr( $cnt - 1 );
							?>">
								<?php echo opalestate_load_template_path( $layout ); ?>
                            </div>
						<?php endwhile; ?>
					<?php endif; ?>
                </div>

			<?php else: ?>
				<?php echo opalestate_load_template_path( 'content-no-results' ); ?>
			<?php endif; ?>
        </div>
    </div>
<?php if ( $query->max_num_pages > 1 ): ?>
    <div class="w-pagination"><?php opalestate_pagination( $query->max_num_pages ); ?></div>
<?php endif; ?>
<?php
wp_reset_postdata();

