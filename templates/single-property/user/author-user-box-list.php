<?php if ( $author ): ?>
    <div class="property-agent-contact ">
		<?php

		if ( ! isset( $prefix ) ) {
			$prefix = OPALESTATE_USER_PROFILE_PREFIX;
		}

		$user_id   = $author->ID;
		$is_sticky = get_user_meta( $user_id, $prefix . 'sticky', true );
		$picture   = OpalEstate_User::get_author_picture( $user_id );

		$desciption = get_user_meta( $user_id, 'description', true );


		$roles = opalestate_user_roles_by_user_id( $user_id );

		if ( ! is_array( $roles ) ) {
			$roles = [ $roles ];
		}

		$related = get_user_meta( $user_id, $prefix . 'related_id', true );
		$trusted = false;
		if ( in_array( 'opalestate_agency', $roles ) || in_array( 'opalestate_agent', $roles ) ) {
			$link        = get_permalink( $related );
			$author_name = get_the_title( $related );
			$trusted     = get_post_meta( $related, $prefix . 'trusted', true );
		} elseif ( $related ) {

			$link        = get_permalink( $related );
			$author_name = get_the_title( $related );
			$trusted     = get_user_meta( $user_id, $prefix . 'trusted', true );

		} else {
			$link        = get_author_posts_url( $user_id );
			$author_name = $author->display_name;
			$trusted     = get_user_meta( $user_id, $prefix . 'trusted', true );
		}

		?>
        <div class="agent-box-list">
            <div class="inner">
                <div class="agent-preview">
                    <a href="<?php echo esc_url( $link ); ?>" class="agent-box-image-inner">
                        <img src="<?php echo esc_url( $picture ); ?>" title="<?php echo esc_attr( $author_name ); ?>">
                    </a>
                </div>
                <div class="agent-box-meta">
                    <h4>
						<?php if ( $trusted ): ?>
                            <span class="trusted-label hint--top" aria-label="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>"
                                  title="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>">
						<i class="fas fa-star"></i>
					</span>
						<?php endif; ?>

                        <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $author_name ); ?></a>
                    </h4>

					<?php
					$job = get_user_meta( $user_id, $prefix . 'address', true );
					?>
                    <p class="agent-box-address"><?php echo esc_html( $job ); ?></p>
					<?php $phone = get_user_meta( $user_id, $prefix . 'phone', true ); ?>
					<?php if ( ! empty( $phone ) ) : ?>
                        <div class="agent-box-phone">
                            <i class="fas fa-phone"></i><span><a href="tel:<?php echo sanitize_title( $phone ); ?>"><?php echo esc_attr( $phone ); ?></a></span>
                        </div><!-- /.agent-box-phone -->
					<?php endif; ?>
                </div><!-- /.agent-box-content -->


				<?php
				$facebook  = get_user_meta( $user_id, $prefix . 'facebook', true );
				$twitter   = get_user_meta( $user_id, $prefix . 'twitter', true );
				$pinterest = get_user_meta( $user_id, $prefix . 'pinterest', true );
				$google    = get_user_meta( $user_id, $prefix . 'googleplus', true );
				$instagram = get_user_meta( $user_id, $prefix . 'instagram', true );
				$linkedIn  = get_user_meta( $user_id, $prefix . 'linkedIn', true );
				?>

            </div>

            <div class="opalestate-social-icons text-center">
				<?php if ( $facebook && $facebook != "#" && ! empty( $facebook ) ) { ?>
                    <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $facebook ); ?>"> <i class="fab fa-facebook"></i> </a>
				<?php } ?>
				<?php if ( $twitter && $twitter != "#" && ! empty( $twitter ) ) { ?>
                    <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $twitter ); ?>"><i class="fab fa-twitter"></i> </a>
				<?php } ?>
				<?php if ( $pinterest && $pinterest != "#" && ! empty( $pinterest ) ) { ?>
                    <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $pinterest ); ?>"><i class="fab fa-pinterest"></i> </a>
				<?php } ?>
				<?php if ( $google && $google != "#" && ! empty( $google ) ) { ?>
                    <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $google ); ?>"> <i class="fab fa-google-plus"></i></a>
				<?php } ?>

				<?php if ( $instagram && $instagram != "#" && ! empty( $instagram ) ) { ?>
                    <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $instagram ); ?>"> <i class="fab fa-instagram"></i></a>
				<?php } ?>

				<?php if ( $linkedIn && $linkedIn != "#" && ! empty( $linkedIn ) ) { ?>
                    <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $linkedIn ); ?>"> <i class="fab fa-linkedIn"></i></a>
				<?php } ?>
            </div>


        </div><!-- /.agent-box-->
    </div>
<?php endif; ?>
