<?php

$args = [
	'posts_per_page' => -1,
];

$query = Opalestate_Query::get_property_query( $args );

$class = 'column-item';
?>

<div class="opalesate-property-collection">

	<?php if ( $query->have_posts() ): ?>
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <div class="column-item">
				<?php echo opalestate_load_template_path( 'content-property-grid-v2' ); ?>
            </div>
		<?php endwhile; ?>
	<?php else: ?>
		<?php echo opalestate_load_template_path( 'content-no-results' ); ?>
	<?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>
