<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$form = "search-agents-form-address";
get_header();
?>
<div class="opalestate-head opalestate-archive-agent">
    <div class="container">
        <div class="opalestate-heading">
            <h1 class="opalestate-head-title"><?php esc_html_e( 'Find The Best Real Estate Agent For Your', 'opalestate-pro' ); ?></h1>
            <p><?php esc_html_e( 'Browser home sales, rating and review to find the best agent to sell or lease your home', 'opalestate-pro' ); ?></p>
        </div>

        <div class="opalestate-head-form">
			<?php echo opalestate_load_template_path( 'parts/' . $form, [ 'current_uri' => true ] ); ?>
        </div>
    </div>
</div>

<section id="main-container" class="site-main container" role="main">
    <div id="primary" class="content content-area clearfix">
        <header class="page-header">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
        </header><!-- .page-header -->

		<?php if ( have_posts() ) : ?>
			<?php global $wp_query; ?>
            <div class="opalesate-archive-top">
                <div class="opal-row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="opalestate-results">
							<?php if ( $wp_query->found_posts ): ?>
                                <span><?php echo sprintf( esc_html__( 'Found %s Agents', 'opalestate-pro' ), $wp_query->found_posts ); ?></span>
							<?php endif; ?>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 text-right">
                        <div class="opalestate-sortable">
							<?php echo opalestate_render_sortable_dropdown(); ?>
                        </div>
						<?php opalestate_show_display_modes(); ?>
                    </div>
                </div>
            </div>

            <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
				<?php
				$layout = isset( $_GET['display'] ) && $_GET['display'] == 'list' ? 'list' : 'grid';
				$grid   = $layout == 'list' ? "col-lg-12 col-md-12 col-sm-12" : "col-lg-4 col-md-4 col-sm-6";
				while ( have_posts() ) : the_post(); ?>
                    <div class="<?php echo esc_attr( $grid ); ?>">
						<?php echo opalestate_load_template_path( 'content-agent-' . $layout ); ?>
                    </div>
				<?php endwhile; ?>
            </div>

			<?php echo opalestate_load_template_path( 'parts/pagination' ); ?>

		<?php else : ?>
			<?php echo opalestate_load_template_path( 'content-no-results' ); ?>
		<?php endif; ?>
    </div><!-- .site-main -->
</section><!-- .content-area -->

<?php get_footer(); ?>
