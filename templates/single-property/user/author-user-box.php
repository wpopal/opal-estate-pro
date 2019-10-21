<?php if ( $author ): ?>
    <div class="property-agent-contact ">
		<?php
		if ( ! isset( $prefix ) ) {
			$prefix = OPALESTATE_USER_PROFILE_PREFIX;
		}

		$user_id    = $author->ID;
		$is_sticky  = get_user_meta( $user_id, $prefix . 'sticky', true );
		$desciption = get_user_meta( $user_id, 'description', true );

		$roles = opalestate_user_roles_by_user_id( $user_id );

		if ( ! is_array( $roles ) ) {
			$roles = [ $roles ];
		}

		$related = get_user_meta( $user_id, $prefix . 'related_id', true );
		$trusted = false;
		$post    = get_post( $related );

		if ( ( in_array( 'opalestate_agency', $roles ) || in_array( 'opalestate_agent', $roles ) ) && $related && $post ) {
			$link        = get_permalink( $related );
			$author_name = $post->post_title;

			if ( $post->post_type == 'opalestate_agency' ) {
				$prefixs = OPALESTATE_AGENCY_PREFIX;
				$picture = OpalEstate_Agency::get_avatar_url( $post->ID, 'full' );
			} else {
				$prefixs = OPALESTATE_AGENT_PREFIX;
				$picture = OpalEstate_Agent::get_avatar_url( $post->ID, 'full' );
			}

			$trusted = get_post_meta( $related, $prefixs . 'trusted', true );
		} else {
			$link        = get_author_posts_url( $user_id );
			$author_name = $author->display_name;
			$trusted     = get_user_meta( $user_id, $prefix . 'trusted', true );
			$picture     = OpalEstate_User::get_author_picture( $user_id );
		}
		?>
        <div class="agent-box">

            <div class="agent-preview">
                <div class="team-header <?php if ( empty( $picture ) ) {
					echo 'without-image';
				} ?>">
					<?php if ( $trusted ): ?>
                        <span class="trusted-label hint--top" aria-label="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>"
                              title="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>">
					<i class="fas fa-star"></i>
				</span>
					<?php endif; ?>

                    <a href="<?php echo esc_url( $link ); ?>" class="agent-box-image-inner">
                        <img src="<?php echo esc_url( $picture ); ?>" title="<?php echo esc_attr( $author_name ); ?>">
                    </a>
                </div>
            </div>

            <div class="agent-box-meta">
                <h4 class="agent-box-title">
                    <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $author_name ); ?></a>
                </h4>

				<?php
				$job = get_user_meta( $user_id, $prefix . 'job', true );
				?>
                <p class="agent-box-job"><?php echo esc_html( $job ); ?></p>

				<?php $email = get_userdata( $user_id )->user_email; ?>
				<?php if ( ! empty( $email ) ) : ?>
                    <div class="agent-box-email">
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>">
                            <span><?php echo esc_attr( $email ); ?></span>
                        </a>
                    </div>
				<?php endif; ?>

				<?php $phone = get_user_meta( $user_id, $prefix . 'phone', true ); ?>
				<?php if ( ! empty( $phone ) ) : ?>
                    <div class="agent-box-phone">
                        <i class="fa fa-phone"></i><span><a href="tel:<?php echo sanitize_title( $phone ); ?>"><?php echo esc_attr( $phone ); ?></a></span>
                    </div>
				<?php endif; ?>

				<?php $web = get_user_meta( $user_id, $prefix . 'web', true ); ?>
				<?php if ( ! empty( $web ) ) : ?>
                    <div class="agent-box-web">
                        <i class="fa fa-globe"></i>
                        <a href="<?php echo esc_attr( $web ); ?>">
                            <span><?php echo esc_attr( $web ); ?></span>
                        </a>
                    </div>
				<?php endif; ?>

				<?php
				$facebook  = get_user_meta( $user_id, $prefix . 'facebook', true );
				$twitter   = get_user_meta( $user_id, $prefix . 'twitter', true );
				$pinterest = get_user_meta( $user_id, $prefix . 'pinterest', true );
				$google    = get_user_meta( $user_id, $prefix . 'googleplus', true );
				$instagram = get_user_meta( $user_id, $prefix . 'instagram', true );
				$linkedIn  = get_user_meta( $user_id, $prefix . 'linkedIn', true );
				?>

                <div class="opalestate-social-icons">
					<?php if ( $facebook && $facebook != "#" && ! empty( $facebook ) ) { ?>
                        <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $facebook ); ?>" target="_blank"> <i class="fab fa-facebook"></i> </a>
					<?php } ?>
					<?php if ( $twitter && $twitter != "#" && ! empty( $twitter ) ) { ?>
                        <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $twitter ); ?>" target="_blank"><i class="fab fa-twitter"></i> </a>
					<?php } ?>
					<?php if ( $pinterest && $pinterest != "#" && ! empty( $pinterest ) ) { ?>
                        <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $pinterest ); ?>" target="_blank"><i class="fab fa-pinterest"></i> </a>
					<?php } ?>
					<?php if ( $google && $google != "#" && ! empty( $google ) ) { ?>
                        <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $google ); ?>" target="_blank"> <i class="fab fa-google-plus"></i></a>
					<?php } ?>

					<?php if ( $instagram && $instagram != "#" && ! empty( $instagram ) ) { ?>
                        <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $instagram ); ?>" target="_blank"> <i class="fab fa-instagram"></i></a>
					<?php } ?>

					<?php if ( $linkedIn && $linkedIn != "#" && ! empty( $linkedIn ) ) { ?>
                        <a class="opalestate-social-white radius-x" href="<?php echo esc_url( $linkedIn ); ?>" target="_blank"> <i class="fab fa-linkedIn"></i></a>
					<?php } ?>
                </div>
            </div><!-- /.agent-box-content -->
        </div><!-- /.agent-box-->
    </div>
<?php endif; ?>
