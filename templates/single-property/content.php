<div class="entry-content">
	<h5 class="box-heading"><?php esc_html_e( 'Description', 'opalestate-pro' ); ?></h5>
	<?php
		/* translators: %s: Name of current post */
		the_content( sprintf(
			__( 'Continue reading %s ', 'opalestate-pro' ),
			the_title( '<span class="screen-reader-text">', '</span>', false )
		) );

		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'opalestate-pro' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		) );
	?>
</div><!-- .entry-content -->