<article itemscope itemtype="http://schema.org/Property" <?php post_class(); ?>>

	<?php do_action( 'opalestate_before_property_loop_item' ); ?>

    <header>
		<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
    </header>

    <div class="entry-content">
		<?php opalestate_get_loop_thumbnail( opalestate_get_option( 'loop_image_size', 'large' ) ); ?>
		<?php echo do_shortcode( '[opalestate_favorite_button property_id=' . get_the_ID() . ']' ); ?>
    </div><!-- .entry-content -->

	<?php do_action( 'opalestate_after_property_loop_item' ); ?>

    <meta itemprop="url" content="<?php the_permalink(); ?>"/>

</article><!-- #post-## -->
