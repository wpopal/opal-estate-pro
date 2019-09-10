<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<section id="main-container" class="site-main" role="main">
    <div id="primary" class="content content-area">
        <div class="single-opalestate-container">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					$layout = opalestate_single_the_property_layout();

					do_action( 'opalestate_single_property_layout', $layout, 9 );
					?>
					<?php echo opalestate_load_template_path( 'content-single-property', [], $layout ); ?>

                    <div class="opalestate-single-bottom">
                        <div class="container">
							<?php do_action( 'opalestate_single_property_after_render' ); ?>
                        </div>
                    </div>
				<?php endwhile; ?>
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>
        </div>
    </div><!-- .site-main -->
</section><!-- .content-area -->

<?php get_footer(); ?>
