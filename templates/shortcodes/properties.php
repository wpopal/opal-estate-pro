<?php

$args = [
	'posts_per_page'	=> $posts_per_page,
	'showmode'		 	=> $showmode,
	'categories'		=> $categories,
	'types'				=> $types, 
	'labels'			=> $labels,
	'cities'			=> $cities,
	'statuses'			=> $statuses,
];

$query = Opalestate_Query::get_property_query( $args );

$class = 'column-item';
$clscol = floor( 12 / $column );

?>

<div class="opalesate-property-collection">

	<?php if ( $query->have_posts() ): ?>
		<div class="opal-row">
			<?php $cnt=0; while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php  
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
		</div>

		<?php if ( $query->max_num_pages > 1 && $show_pagination ): ?>
		    <div class="w-pagination"><?php opalestate_pagination( $query->max_num_pages ); ?></div>
		<?php endif; ?>

	<?php else: ?>
		<?php echo opalestate_load_template_path( 'content-no-results' ); ?>
	<?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>
