<?php global $agency;
$address = $agency->get_meta( 'address' ); ?>
<div class="agency-box">

    <div class="opal-row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <div class="agency-thumb">
                <div class="agency-inner agency-grid-style">

                    <header class="team-header agency-header">
                        <div class="agent-box-image">

							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail(); ?>
							<?php else: ?>
								<?php echo opalestate_get_image_placeholder(); ?>
							<?php endif; ?>
                            </a>
                        </div>
						<?php if ( 'on' === $agency->is_featured() ): ?>
                            <div class="agency-label">
                                <span class="label label-featured" aria-label="<?php esc_html_e( 'Featured Agency', 'opalestate-pro' ); ?>"
                                      title="<?php esc_html_e( 'Featured Agency', 'opalestate-pro' ); ?>">
                                    <?php echo esc_html_e( 'Featured', 'opalestate-pro' ); ?>
                                </span>
                            </div>
						<?php endif; ?>

						<?php if ( 'on' === $agency->get_trusted() ): ?>
                            <span class="trusted-label hint--top" aria-label="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>" title="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>">
                                <i class="fas fa-star"></i>
                            </span>
						<?php endif; ?>
                    </header>

                    <div class="agency-body-content clearfix">
                        <div class="agency-info">
                            <div class="agency-logo">
								<?php $data = OpalEstate_Agency::get_link( get_the_ID() ); ?>

                                <img src="<?php echo esc_url( $data['avatar'] ); ?>" alt="<?php esc_attr_e( 'Agency avatar', 'opalestate-pro' ); ?>">
                            </div>
                            <div class="agency-content">
                                <h6 class="agency-box-title text-uppercase">
									<?php the_title() ?>
                                </h6><!-- /.agency-box-title -->
                                <h3 class="agency-name hide"><?php the_title(); ?></h3>
                                <p class="agency-address">
									<?php echo esc_html( $address ); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">

			<?php
			if ( ! isset( $id ) ) {
				$id = get_the_ID();
			}
			$picture = '';
			?>
            <div class="property-agency-contact">
				<?php $is_sticky = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'sticky', true ); ?>
                <div class="agency-top-meta">
                    <div class="agency-top-info">
						<?php the_title( '<h5 class="entry-title">', '</h5>' ); ?>
						<?php
						$slogan = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'slogan', true );
						?>
                        <p class="agency-slogan"><?php echo esc_html( $slogan ); ?></p>
                    </div>
					<?php
					$facebook  = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'facebook', true );
					$twitter   = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'twitter', true );
					$pinterest = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'pinterest', true );
					$google    = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'google', true );
					$instagram = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'instagram', true );
					$linkedIn  = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'linkedIn', true );
					?>
                </div>


                <div class="agency-box-meta">

					<?php $email = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'email', true ); ?>
					<?php if ( ! empty( $email ) ) : ?>
                        <div class="agency-box-email">
                            <i class="fa fa-envelope"></i>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>">
                                <span><?php echo esc_attr( $email ); ?></span>
                            </a>
                        </div><!-- /.agency-box-email -->
					<?php endif; ?>


					<?php $phone = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'phone', true ); ?>
					<?php if ( ! empty( $phone ) ) : ?>
                        <div class="agency-box-phone">
                            <i class="fa fa-phone"></i><span><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_attr( $phone ); ?></a></span>
                        </div><!-- /.agency-box-phone -->
					<?php endif; ?>

					<?php $mobile = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'mobile', true ); ?>
					<?php if ( ! empty( $mobile ) ) : ?>
                        <div class="agency-box-mobile">
                            <i class="fa fa-mobile"></i><span><a href="tel:<?php echo esc_attr( $mobile ); ?>"><?php echo esc_html( $mobile ); ?></a></span>
                        </div><!-- /.agency-box-phone -->
					<?php endif; ?>

					<?php $fax = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'fax', true ); ?>
					<?php if ( ! empty( $fax ) ) : ?>
                        <div class="agency-box-fax">
                            <i class="fa fa-fax"></i><span><?php echo esc_attr( $fax ); ?></span>
                        </div><!-- /.agency-box-phone -->
					<?php endif; ?>

					<?php $web = get_post_meta( $id, OPALESTATE_AGENCY_PREFIX . 'web', true ); ?>
					<?php if ( ! empty( $web ) ) : ?>
                        <div class="agency-box-web">
                            <i class="fa fa-globe"></i>
                            <a href="<?php echo esc_attr( $web ); ?>" rel="nofollow" target="_blank">
                                <span><?php echo esc_attr( $web ); ?></span>
                            </a>
                        </div><!-- /.agency-box-web -->
					<?php endif; ?>

                </div><!-- /.agency-box-meta -->


            </div>

            <div class="opalestate-social-icons">
				<?php if ( $facebook && $facebook != "#" && ! empty( $facebook ) ) { ?>
                    <a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $facebook ); ?>"> <i class="fab fa-facebook"></i> </a>
				<?php } ?>
				<?php if ( $twitter && $twitter != "#" && ! empty( $twitter ) ) { ?>
                    <a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $twitter ); ?>"><i class="fab fa-twitter"></i> </a>
				<?php } ?>
				<?php if ( $pinterest && $pinterest != "#" && ! empty( $pinterest ) ) { ?>
                    <a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $pinterest ); ?>"><i class="fab fa-pinterest"></i> </a>
				<?php } ?>
				<?php if ( $google && $google != "#" && ! empty( $google ) ) { ?>
                    <a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $google ); ?>"> <i class="fab fa-google"></i></a>
				<?php } ?>

				<?php if ( $instagram && $instagram != "#" && ! empty( $instagram ) ) { ?>
                    <a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $instagram ); ?>"> <i class="fab fa-instagram"></i></a>
				<?php } ?>

				<?php if ( $linkedIn && $linkedIn != "#" && ! empty( $linkedIn ) ) { ?>
                    <a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $linkedIn ); ?>"> <i class="fab fa-linkedIn"></i></a>
				<?php } ?>

            </div>
        </div>
    </div>
</div>


