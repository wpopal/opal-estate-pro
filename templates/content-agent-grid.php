<?php
$agent = new OpalEstate_Agent();
?>
<article <?php post_class( 'agent-grid-style' ); ?>>
    <div class="team-v1 agent-inner">
        <header class="team-header agent-header">
			<?php opalestate_get_loop_agent_thumbnail( opalestate_get_option( 'agent_image_size', 'large' ) ); ?>
			<?php $agent->render_level(); ?>
			<?php if ( $agent->is_featured() ): ?>
                <span class="agent-featured" data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Featured Agent', 'opalestate-pro' ); ?>">
				<span class="agent-label">
					<span><?php esc_html_e( 'Featured', 'opalestate-pro' ); ?></span>
				</span>
			</span>
			<?php endif; ?>
			<?php if ( $agent->get_trusted() ): ?>
                <span class="trusted-label hint--top" aria-label="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>" title="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>">
				<i class="fas fa-star"></i>
			</span>
			<?php endif; ?>
        </header>
        <div class="team-body agent-body">

            <div class="team-body-content">
                <h5 class="agent-box-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
                </h5>
                <h3 class="team-name hide"><?php the_title(); ?></h3>
				<?php $job = $agent->get_meta( 'job' ); ?>
				<?php if ( ! empty( $job ) ) : ?>
                    <p class="agent-job"><?php echo esc_html( $job ); ?></p>
				<?php endif; ?>
            </div>

            <div class="agent-box-meta">
				<?php $email = $agent->get_meta( 'email' ); ?>
				<?php if ( ! empty( $email ) ) : ?>
                    <div class="agent-box-email">
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>">
                            <span><?php echo esc_html( $email ); ?></span>
                        </a>
                    </div>
				<?php endif; ?>

				<?php $phone = $agent->get_meta( 'phone' ); ?>
				<?php if ( ! empty( $phone ) ) : ?>
                    <div class="agent-box-phone">
                        <i class="fa fa-phone"></i>
                        <a href="tel:<?php echo sanitize_title( $phone ); ?>">
                            <span><?php echo esc_html( $phone ); ?></span>
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
                        <i class="fa fa-fax"></i><span><?php echo esc_html( $fax ); ?></span>
                    </div>
				<?php endif; ?>

				<?php $web = $agent->get_meta( 'web' ); ?>
				<?php if ( ! empty( $web ) ) : ?>
                    <div class="agent-box-web">
                        <i class="fa fa-globe"></i>
                        <a href="<?php echo esc_url( $web ); ?>" rel="nofollow" target="_blank">
                            <span><?php echo esc_url( $web ); ?></span>
                        </a>
                    </div>
				<?php endif; ?>

				<?php $socials = $agent->get_socials(); ?>

                <?php if ( $socials ) : ?>
                    <div class="opalestate-social-icons">
                        <?php if ( isset( $socials['facebook'] ) && $socials['facebook'] ) : ?>
                            <a class="opalestate-social-white radius-x opalestate-social-facebook" href="<?php echo esc_url( $socials['facebook'] ); ?>" target="_blank"> <i class="fab
                            fa-facebook"></i> </a>
                        <?php endif; ?>

                        <?php if ( isset( $socials['twitter'] ) && $socials['twitter'] ) : ?>
                            <a class="opalestate-social-white radius-x opalestate-social-twitter" href="<?php echo esc_url( $socials['twitter'] ); ?>" target="_blank"><i class="fab fa-twitter"></i>
                            </a>
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
            </div><!-- /.agent-box-content -->
        </div>
    </div>
</article>
