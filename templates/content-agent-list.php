<?php
$agent = new OpalEstate_Agent();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'agent-list-style' ); ?>>
    <div class="team-v2">
        <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
            <div class="col-lg-3 col-md-4 col-sm-5">
                <header class="team-header">
					<?php opalestate_get_loop_agent_thumbnail( opalestate_get_option( 'agent_image_size', 'large' ) ); ?>
					<?php $agent->render_level(); ?>
					<?php if ( $agent->is_featured() ): ?>
                        <span class="agent-featured" data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Featured Agent', 'opalestate-pro' ); ?>">
						<span class="agent-label">
							<span><?php esc_html_e( 'Featured', 'opalestate-pro' ); ?></span>
						</span>
					</span>
					<?php endif; ?>
                </header>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-7">
                <div class="team-body">
                    <div class="team-body-content">
                        <h4 class="agent-box-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
							<?php if ( $agent->get_trusted() ): ?>
                                <span class="trusted-label hint--top" aria-label="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>"
                                      title="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>">
								<i class="fas fa-star"></i>
							    </span>
							<?php endif; ?>
                        </h4><!-- /.agent-box-title -->

                        <h3 class="team-name hide"><?php the_title(); ?></h3>
						<?php $job = $agent->get_meta( 'job' ); ?>
						<?php if ( ! empty( $job ) ) : ?>
                            <p class="agent-job"><?php echo esc_html( $job ); ?></p>
						<?php endif; ?>
                    </div>
                    <div class="agent-box-meta">

	                    <?php $socials = $agent->get_socials(); ?>

	                    <?php if ( $socials ) : ?>
                            <div class="opalestate-social-icons">
                                <?php if ( isset( $socials['facebook'] ) && $socials['facebook'] ) : ?>
                                    <a class="opalestate-social-white radius-x opalestate-social-facebook" href="<?php echo esc_url( $socials['facebook'] ); ?>" target="_blank"> <i class="fab
                                    fa-facebook"></i> </a>
                                <?php endif; ?>

                                <?php if ( isset( $socials['twitter'] ) && $socials['twitter'] ) : ?>
                                    <a class="opalestate-social-white radius-x opalestate-social-twitter" href="<?php echo esc_url( $socials['twitter'] ); ?>" target="_blank"><i class="fab
                                    fa-twitter"></i> </a>
                                <?php endif; ?>

                                <?php if ( isset( $socials['pinterest'] ) && $socials['pinterest'] ) : ?>
                                    <a class="opalestate-social-white radius-x opalestate-social-printerest" href="<?php echo esc_url( $socials['pinterest'] ); ?>" target="_blank"><i class="fab
                                    fa-pinterest"></i> </a>
                                <?php endif; ?>

                                <?php if ( isset( $socials['google'] ) && $socials['google'] ) : ?>
                                    <a class="opalestate-social-white radius-x opalestate-social-google" href="<?php echo esc_url( $socials['google'] ); ?>" target="_blank"> <i class="fab
                                    fa-google"></i></a>
                                <?php endif; ?>

                                <?php if ( isset( $socials['instagram'] ) && $socials['instagram'] ) : ?>
                                    <a class="opalestate-social-white radius-x opalestate-social-instagram" href="<?php echo esc_url( $socials['instagram'] ); ?>" target="_blank"> <i class="fab
                                    fa-instagram"></i></a>
                                <?php endif; ?>

                                <?php if ( isset( $socials['linkedIn'] ) && $socials['linkedIn'] ) : ?>
                                    <a class="opalestate-social-white radius-x opalestate-social-linkedIn" href="<?php echo esc_url( $socials['linkedIn'] ); ?>" target="_blank"> <i class="fab
                                    fa-linkedIn"></i></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div><!-- /.agent-box-meta -->
                </div>
                <p class="team-info">
					<?php echo opalestate_fnc_excerpt( 14, '...' ); ?>
                </p>
            </div>
        </div>
    </div>
</article>	
