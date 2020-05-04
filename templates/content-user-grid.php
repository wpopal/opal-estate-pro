<?php
$agent_id    = get_user_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true );
$user        = get_userdata( $user_id );
$user        = $user->data;
$picture     = OpalEstate_User::get_author_picture( $user_id );

if ( $agent_id ) {
	$agent       = opalesetate_agent( $agent_id );
	$post        = get_post( $agent_id );
	$facebook    = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'facebook', true );
	$twitter     = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'twitter', true );
	$pinterest   = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'pinterest', true );
	$google      = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'google', true );
	$instagram   = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'instagram', true );
	$linkedIn    = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'linkedIn', true );
	$job         = get_post_meta( $agent_id, OPALESTATE_AGENT_PREFIX . 'job', true );
	$title       = $post->post_title;
	$author_link = get_permalink( $agent_id );
	wp_reset_query();
} else {
	$title       = $user->display_name;
	$facebook    = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'facebook', true );
	$twitter     = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'twitter', true );
	$pinterest   = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'pinterest', true );
	$google      = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'google', true );
	$instagram   = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'instagram', true );
	$linkedIn    = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'linkedIn', true );
	$job         = get_post_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'job', true );
	$author_link = get_author_posts_url( $user_id );
}
?>
<article <?php post_class( 'agent-grid-style', $agent_id ? $agent_id : 0 ); ?>>
    <div class="team-v1 agent-inner">
        <header class="team-header agent-header">
            <div class="agent-box-image">
                <img src="<?php echo esc_url( $picture ); ?>" alt="user image">
            </div>

			<?php if ( $agent ) : ?>
				<?php $agent->render_level(); ?>
				<?php if ( $agent->is_featured() ): ?>
                    <span class="agent-featured" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'Featured Agent', 'opalestate-pro' ); ?>">
                    <span class="agent-label">
                        <span><?php esc_html_e( 'Featured', 'opalestate-pro' ); ?></span>
                    </span>
                </span>
				<?php endif; ?>
				<?php if ( $agent->get_trusted() ): ?>
                    <span class="trusted-label hint--top" aria-label="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>" title="<?php esc_html_e( 'Trusted Member', 'opalestate-pro' ); ?>">
                    <i class="fas fa-star"></i>
                </span>
				<?php endif; ?>
			<?php endif; ?>
        </header>
        <div class="team-body agent-body">
            <div class="team-body-content">
                <h5 class="agent-box-title">
                    <a href="<?php echo esc_url( $author_link ); ?>"><?php echo esc_html( $title ); ?></a>
                </h5><!-- /.agent-box-title -->

				<?php if ( $title ) : ?>
                    <h3 class="team-name hide"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $job ) ) : ?>
                    <p class="agent-job"><?php echo esc_html( $job ); ?></p>
				<?php endif; ?>
            </div>
            <div class="agent-box-meta">

				<?php if ( $agent ) : ?>
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
                            <a href="<?php echo esc_attr( $web ); ?>" rel="nofollow" target="_blank">
                                <span><?php echo esc_html( $web ); ?></span>
                            </a>
                        </div>
					<?php endif; ?>
				<?php endif; ?>

                <div class="opalestate-social-icons">
					<?php if ( $facebook && $facebook != "#" && ! empty( $facebook ) ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-facebook" href="<?php echo esc_url( $facebook ); ?>"> <i class="fab fa-facebook"></i> </a>
					<?php endif; ?>

					<?php if ( $twitter && $twitter != "#" && ! empty( $twitter ) ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-twitter" href="<?php echo esc_url( $twitter ); ?>"><i class="fab fa-twitter"></i> </a>
					<?php endif; ?>

					<?php if ( $pinterest && $pinterest != "#" && ! empty( $pinterest ) ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-pinterest" href="<?php echo esc_url( $pinterest ); ?>"><i class="fab fa-pinterest"></i> </a>
					<?php endif; ?>

					<?php if ( $google && $google != "#" && ! empty( $google ) ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-google" href="<?php echo esc_url( $google ); ?>"> <i class="fab fa-google"></i></a>
					<?php endif; ?>

					<?php if ( $instagram && $instagram != "#" && ! empty( $instagram ) ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-instagram" href="<?php echo esc_url( $instagram ); ?>"> <i class="fab fa-instagram"></i></a>
					<?php endif; ?>

					<?php if ( $linkedIn && $linkedIn != "#" && ! empty( $linkedIn ) ) : ?>
                        <a class="opalestate-social-white radius-x opalestate-social-linkedIn" href="<?php echo esc_url( $linkedIn ); ?>"> <i class="fab fa-linkedIn"></i></a>
					<?php endif; ?>
                </div>
            </div><!-- /.agent-box-content -->
        </div>
    </div>
</article>
