<?php
$agent = new OpalEstate_Agent();
?>
<div class="property-agent-contact ">
    <div class="agent-box">
        <div class="agent-preview">
			<?php if ( has_post_thumbnail() ) : ?>
                <div class="team-header <?php if ( ! has_post_thumbnail() ) {
					echo 'without-image';
				} ?>">
                    <a href="<?php the_permalink(); ?>" class="agent-box-image-inner <?php if ( ! empty( $agent ) ) : ?>has-agent<?php endif; ?>">
						<?php the_post_thumbnail( apply_filters( 'opalestate_single_agent_thumbnail', 'full' ) ); ?>
                    </a>
					<?php $agent->render_level(); ?>
					<?php if ( $agent->is_featured() ): ?>
                        <span class="agent-featured" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'Featured Agent', 'opalestate-pro' ); ?>">
					<span class="agent-label">
						<span><?php esc_html_e( 'Featured', 'opalestate-pro' ); ?></span>
					</span>
				</span>
					<?php endif; ?>

					<?php if ( $agent->get_trusted() ): ?>
                        <span class="trusted-label hint--top" aria-label="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>"
                              title="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>">
						<i class="fas fa-star"></i>
					</span>
					<?php endif; ?>
                </div><!-- /.agent-box-image -->
			<?php endif; ?>
        </div>
        <div class="agent-box-meta opalestate-sidebar-box">
            <h4 class="agent-box-title">
				<?php esc_html_e( 'Agent details', 'opalestate-pro' ); ?>
            </h4><!-- /.agent-box-title -->

			<?php $job = $agent->get_meta( 'job' ); ?>
            <p class="agent-box-job"><?php echo esc_html( $job ); ?></p>

	        <?php $email = $agent->get_meta( 'email' ); ?>
	        <?php if ( ! empty( $email ) ) : ?>
                <div class="agent-box-email">
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>">
                        <span><?php echo esc_attr( $email ); ?></span>
                    </a>
                </div>
	        <?php endif; ?>

	        <?php $phone = $agent->get_meta( 'phone' ); ?>
	        <?php if ( ! empty( $phone ) ) : ?>
                <div class="agent-box-phone">
                    <i class="fa fa-phone"></i>
                    <a href="tel:<?php echo sanitize_title( $phone ); ?>">
                        <span><?php echo esc_attr( $phone ); ?></span>
                    </a>
                </div>
	        <?php endif; ?>

	        <?php $mobile = $agent->get_meta( 'mobile' ); ?>
	        <?php if ( ! empty( $mobile ) ) : ?>
                <div class="agent-box-mobile">
                    <i class="fa fa-mobile"></i>
                    <a href="tel:<?php echo sanitize_title( $mobile ); ?>">
                        <span><?php echo esc_html( $mobile ); ?></span>
                    </a>
                </div>
	        <?php endif; ?>

	        <?php $fax = $agent->get_meta( 'fax' ); ?>
	        <?php if ( ! empty( $fax ) ) : ?>
                <div class="agent-box-fax">
                    <i class="fa fa-fax"></i><span><?php echo esc_attr( $fax ); ?></span>
                </div>
	        <?php endif; ?>

	        <?php $web = $agent->get_meta( 'web' ); ?>
	        <?php if ( ! empty( $web ) ) : ?>
                <div class="agent-box-web">
                    <i class="fa fa-globe"></i>
                    <a href="<?php echo esc_attr( $web ); ?>" rel="nofollow" target="_blank">
                        <span><?php echo esc_attr( $web ); ?></span>
                    </a>
                </div>
	        <?php endif; ?>

	        <?php $socials = $agent->get_socials(); ?>

	        <?php if ( $socials ) : ?>
                <div class="opalestate-social-icons">
			        <?php if ( isset( $socials['facebook'] ) && $socials['facebook'] ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-facebook" href="<?php echo esc_url( $socials['facebook'] ); ?>" target="_blank"> <i class="fab fa-facebook"></i>
                        </a>
			        <?php endif; ?>

			        <?php if ( isset( $socials['twitter'] ) && $socials['twitter'] ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-twitter" href="<?php echo esc_url( $socials['twitter'] ); ?>" target="_blank"><i class="fab fa-twitter"></i> </a>
			        <?php endif; ?>

			        <?php if ( isset( $socials['pinterest'] ) && $socials['pinterest'] ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-printerest" href="<?php echo esc_url( $socials['pinterest'] ); ?>" target="_blank"><i class="fab
                        fa-pinterest"></i> </a>
			        <?php endif; ?>

			        <?php if ( isset( $socials['google'] ) && $socials['google'] ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-google" href="<?php echo esc_url( $socials['google'] ); ?>" target="_blank"> <i class="fab fa-google"></i></a>
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

		<?php if ( is_single() && get_post_type() == 'opalestate_agent' ): ?>
		<?php else : ?>
            <div class="agent-box-bio">
				<?php the_excerpt(); ?>
            </div>
            <p class="agent-box-readmore">
                <a href="<?php the_permalink(); ?>">
					<?php esc_html_e( 'View Profile', 'opalestate-pro' ); ?>
                </a>
            </p>
		<?php endif; ?>
    </div><!-- /.agent-box-->
</div>
