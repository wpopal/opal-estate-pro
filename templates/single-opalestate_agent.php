<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 

get_header(); ?>
	<div class="opalestate-head opalestate-single-agent">
		<div class="container">
			<div class="opal-row">
				<div class="col-lg-4 col-md-4 col-sm-12"></div>
				<div class="col-lg-8 col-md-8 col-sm-12 opalestate-heading">
					<?php the_title( '<h2 class="opalestate-head-title">', '</h2>' ); ?>
				</div>
			</div>	
		</div>	
	</div>	
	<section id="main-container" class="site-main" role="main">
		<div id="primary" class="content content-area">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
                    <?php echo opalestate_load_template_path( 'content-single-agent' ); ?>
				<?php endwhile; ?>
			<?php else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

		</div><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_footer(); ?>
