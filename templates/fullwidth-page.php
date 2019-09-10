<?php
/**
 * Template Name: Full Width Page
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package wpopalbootstrap
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
get_header();
?>
<div class="wrapper" id="full-width-page-wrapper">

    <div class="container-full" id="content">
        <div class="opal-row">
            <div class="wp-col-md-12 content-area" id="primary">
                <div class="site-main" id="main" role="main">
                    <?php while (have_posts()) : the_post(); ?>
                       <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <div class="entry-content">
                                <?php
                                the_content();

                                wp_link_pages(
                                    array(
                                        'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'opalestate-pro' ) . '</span>',
                                        'after'       => '</div>',
                                        'link_before' => '<span>',
                                        'link_after'  => '</span>',
                                        'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'opalestate-pro' ) . ' </span>%',
                                        'separator'   => '<span class="screen-reader-text">, </span>',
                                    )
                                );
                                ?>
                            </div><!-- .entry-content -->
                        </article><!-- #post-<?php the_ID(); ?> -->
                    <?php endwhile; // end of the loop. ?>
                </div><!-- #main -->
            </div><!-- #primary -->
        </div><!-- .row end -->
    </div><!-- Container end -->
</div><!-- Wrapper end -->

<?php get_footer(); ?>
