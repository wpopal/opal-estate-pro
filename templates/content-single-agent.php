<?php

global $post, $agent;
$agent   = opalesetate_agent( get_the_ID() );
$maps    = $agent->get_meta( 'map' );
$address = $agent->get_meta( 'address' );
$id      = time();
?>
<div class="agent-single-top">
    <div class="agency-single-stick-bars keep-top-bars ">
        <div class="container">
            <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
                <div class="col-md-4 col-sm-12"></div>
                <div class="col-md-8 col-sm-12">
                    <ul class="list-inline opalestate-scroll-elements">
                        <li><a href="#block-description" class="active"><?php esc_html_e( 'Description', 'opalestate-pro' ); ?></a></li>

						<?php if ( opalestate_agent_reviews_enabled() ) : ?>
                            <li><a href="#reviews"><?php esc_html_e( 'Review', 'opalestate-pro' ); ?></a></li>
						<?php endif; ?>

                        <li><a href="#block-my-properties"><?php esc_html_e( 'Properties', 'opalestate-pro' ); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <article id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/RealEstateAgent" <?php post_class( 'single-agent' ); ?>>
        <div class="opal-row" id="block-description">
            <div class="col-lg-4 col-md-4 col-sm-12 agent-sidebar">
                <div class="agent-box">
					<?php echo opalestate_load_template_path( 'single-agent/author-box' ); ?>
                    <div class="opalestate-sidebar-box">
						<?php
						$email = $agent->get_meta( 'email' );
						$args  = [
							'post_id' => get_the_ID(),
							'id'      => get_the_ID(),
							'email'   => $email,
							'message' => '',
							'type'    => 'agent',
						];
						echo apply_filters( 'opalestate_render_contact_form', opalestate_load_template_path( 'messages/contact-form', $args ), $args );
						?>
                    </div>
                </div>

				<?php do_action( 'opalestate_single_content_agent_sidebar' ); ?>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12">
                <header class="hide">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header>
                <div class="entry-content">
                    <div class="opalestate-box agent-description">
                        <h5 class="box-heading"><?php esc_html_e( 'About the Agent', 'opalestate-pro' ); ?></h5>
						<?php
						/* translators: %s: Name of current post */
						the_content( sprintf(
							__( 'Continue reading %s', 'opalestate-pro' ),
							the_title( '<span class="screen-reader-text">', '</span>', false )
						) );

						wp_link_pages( [
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'opalestate-pro' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						] );
						?>
                    </div>
                    <div class="content-bottom">
						<?php do_action( 'opalestate_single_agent_content_bottom' ); ?>
                    </div>
					<?php if ( isset( $maps ) ): ?>
                        <div class="opalestate-box agent-address-map">
                            <h5 class="box-heading"><?php esc_html_e( 'My Address', 'opalestate-pro' ); ?></h5>
                            <div class="agent-google-map-content">
								<?php if ( $address ): ?>
                                    <p>
                                        <i class="fas fa-map-marker-alt"></i> <span><?php esc_html_e( 'Address:', 'opalestate-pro' ); ?></span> <?php echo wp_kses_post( $address ); ?>.
										<?php
										$terms = wp_get_post_terms( get_the_ID(), 'opalestate_agent_location' );
										if ( $terms && ! is_wp_error( $terms ) ) {

											echo '<strong>' . esc_html__( 'Location:', 'opalestate-pro' ) . '</strong>';

											$output = '<span class="property-locations">';
											foreach ( $terms as $term ) {
												$output .= $term->name;
											}
											$output .= '</span>';
											echo $output;
										}
										?>
                                    </p>
								<?php endif; ?>
                                <div id="property-map<?php echo esc_attr( $id ); ?>" class="property-preview-map" style="height:400px"
                                     data-latitude="<?php echo( isset( $maps['latitude'] ) ? $maps['latitude'] : '' ); ?>"
                                     data-longitude="<?php echo( isset( $maps['longitude'] ) ? $maps['longitude'] : '' ); ?>" data-icon="<?php echo esc_url( OPALESTATE_CLUSTER_ICON_URL ); ?>"></div>
                            </div>
                        </div>
					<?php endif ?>
                </div><!-- .entry-content -->

				<?php
				if ( opalestate_agent_reviews_enabled() ) {
					comments_template();
				}
				?>

                <meta itemprop="url" content="<?php the_permalink(); ?>"/>

				<?php do_action( 'opalestate_single_content_agent_after' ); ?>
            </div>
        </div>
    </article><!-- #post-## -->
</div>
