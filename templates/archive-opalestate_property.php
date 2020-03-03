<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query;
get_header();

$list_layout = 'content-property-' . opalestate_get_option( 'property_archive_list_layout', 'list' );
$grid_layout = 'content-property-' . opalestate_get_option( 'property_archive_grid_layout', 'grid' );

?>
<?php do_action( "opalestate_archive_property_page_before" ); ?>
<section id="main-container" class="site-main container" role="main">
    <div id="primary" class="content content-area clearfix">
		<?php if ( have_posts() ) : ?>
            <header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
            </header><!-- .page-header -->

            <div class="opaleslate-archive-container">

				<?php echo opalestate_load_template_path( 'parts/archive-simple-bars' ); ?>

                <div class="opalesate-archive-bottom opalestate-rows opalestate-collection">
                    <div class="opal-row">
						<?php if ( ( isset( $_GET['display'] ) && $_GET['display'] == 'list' ) || opalestate_get_display_mode( opalestate_options( 'displaymode', 'grid' ) ) == 'list' ) : ?>
							<?php while ( have_posts() ) : the_post(); ?>
                                <div class="col-lg-12 col-md-12 col-sm-12">
									<?php echo opalestate_load_template_path( $list_layout ); ?>
                                </div>
							<?php endwhile; ?>
						<?php else : ?>
							<?php
							$column = apply_filters( 'opalestate_properties_column_row', 3 );
							$clscol = floor( 12 / $column );
							$cnt    = 0;
							while ( have_posts() ) : the_post();
								$cls = '';
								if ( $cnt++ % $column == 0 ) {
									$cls .= ' first-child';
								}
								?>
                                <div class="<?php echo esc_attr( $cls ); ?> col-lg-<?php echo esc_attr( $clscol ); ?>  col-sm-6">
									<?php echo opalestate_load_template_path( $grid_layout ); ?>
                                </div>
							<?php endwhile; ?>
						<?php endif; ?>
                    </div>
                </div>
            </div>

			<?php echo opalestate_load_template_path( 'parts/pagination' ); ?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
    </div><!-- .site-main -->
</section><!-- .content-area -->

<?php do_action( 'opalestate_archive_property_page_after' ); ?>

<?php get_footer(); ?>
