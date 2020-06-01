<?php
/**
 * Template Name: Opal Estate User Dashboard Page
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package wpopalbootstrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'opalestate_before_user_management_template' );

if ( ! class_exists( 'OpalEstate_User' ) ) {
	return;
}

$user_id      = get_current_user_id();
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$roles_classes = implode( ' ', array_map( 'sanitize_html_class', $roles ) )

?>
<?php if ( $user_id ): ?>
	<?php get_header( 'no-sidebar' ); ?>
    <div class="dashboard-navbar">
        <div class="clearfix">
            <div class="pull-left navbar-left">
                <button class="btn btn-link" id="show-user-sidebar-btn">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <div class=" pull-right">
                <ul class="list-inline">
                    <li>
                        <div class="opalestate-user-greeting opalestate-popup hover-align-right">
                            <span class="howdy"><?php printf( __( 'Howdy, %s', 'opalestate-pro' ), '<span class="display-name">' . $current_user->display_name . '</span>' ); ?></span>
                            <div class="popup-head">
                                <a href="#">
                                    <div class="opalestate-user-image">
                                        <img src="<?php echo esc_url( OpalEstate_User::get_author_picture( $user_id ) ); ?>" alt="<?php esc_attr_e( 'Avatar image', 'opalestate-pro' ); ?>"/>
                                    </div>
                                    <span class="notify active"></span>
                                </a>
                            </div>
                            <div class="popup-body">
                                <div class="account-dashboard-content">
									<?php
									if ( function_exists( 'opalestate_management_user_menu_tabs' ) ) {
										opalestate_management_user_menu_tabs();
									}
									?>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="wrapper opalestate-user-management <?php echo esc_attr( $roles_classes ); ?>" id="opalestate-user-management">
        <div class="container" id="content">
            <div class="opal-row-inner">
                <div class=" user-dasboard-sidebar">
                    <div class="navbar-brand">
						<?php if ( $dashboard_logo_id = opalestate_get_option( 'dashboard_logo_id' ) ) : ?>
							<?php
							printf(
								'<a href="%1$s" class="custom-logo-link" rel="home">%2$s</a>',
								esc_url( home_url( '/' ) ),
								wp_get_attachment_image( $dashboard_logo_id, 'full' )
							);
							?>
						<?php else : ?>
							<?php if ( ! has_custom_logo() ) : ?>
								<?php if ( is_front_page() && is_home() ) : ?>
                                    <h1 class="navbar-brand mb-0">
                                        <a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">
											<?php bloginfo( 'name' ); ?>
                                        </a>
                                    </h1>
								<?php else : ?>
                                    <a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"
                                       itemprop="url">
										<?php bloginfo( 'name' ); ?>
                                    </a>
								<?php endif; ?>
							<?php else :
								the_custom_logo();
							endif; ?><!-- end custom logo -->
						<?php endif; ?>
                    </div>

                    <div class="user-dasboard-sidebar-inner">

						<?php
						global $current_user;

						if ( is_user_logged_in() ) : ?>
                            <div class="profile-top">

                            </div>
                            <div class="profile-bottom">
								<?php opalestate_management_user_menu_tabs(); ?>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
                <div class="content-area" id="primary">
                    <?php $tab = isset( $_GET['tab'] ) && $_GET['tab'] ? sanitize_text_field( $_GET['tab'] ) : 'dashboard'; ?>
                    <div class="site-main" id="main" role="main">
						<?php if ( $tab && opalestate_is_user_management_page() ) : ?>
							<?php opalestate_management_show_content_page_tab(); ?>
						<?php else : ?>
							<?php while ( have_posts() ) : the_post(); ?>
								<?php get_template_part( 'partials/loop/content', 'page' ); ?>
								<?php
								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) :
									comments_template();
								endif;
								?>
							<?php endwhile; // end of the loop. ?>
						<?php endif; ?>
                    </div><!-- #main -->
                </div><!-- #primary -->
            </div><!-- .row end -->
        </div><!-- Container end -->
    </div><!-- Wrapper end -->
	<?php get_footer( 'header/no-sidebar' ); ?>
<?php else : ?>
	<?php get_header(); ?>
    <div class="wrapper opalestate-user-management <?php echo esc_attr( $roles_classes ); ?>" id="opalestate-user-management">
        <div class="container">
            <div class="opalestate-panel-myaccount">
                <div class="management-header text-center">
                    <h2><?php esc_html_e( 'Login to your account', 'opalestate-pro' ); ?></h2>
                    <p><?php esc_html_e( 'Logining in allows you to edit your property or submit a property, save favorite real estate.', 'opalestate-pro' ); ?></p>
                </div>
				<?php echo opalestate_load_template_path( 'user/my-account' ); ?>
            </div>
        </div>
    </div>
	<?php get_footer(); ?>
<?php endif; ?>
