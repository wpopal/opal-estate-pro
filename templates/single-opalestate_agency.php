<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( apply_filters( 'opalestate_fnc_get_header_layout', null ) ); ?>
    <div class="wpo-breadcrumb single-agent-breadcrumb">
		<?php do_action( 'opalestate_template_main_before' ); ?>
    </div>
    <section id="main-container" class="site-main container" role="main">
        <div id="primary" class="content content-area space-padding-lr-40">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php echo opalestate_load_template_path( 'content-single-agency' ); ?>
				<?php endwhile; ?>

				<?php echo opalestate_load_template_path( 'parts/pagination' ); ?>

			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>
        </div><!-- .site-main -->
    </section><!-- .content-area -->

<?php get_footer(); ?>
