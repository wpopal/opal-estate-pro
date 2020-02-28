<?php
$agency = new OpalEstate_Agency();

$agency_id = get_the_ID();
$address   = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'address', true );
?>
<article <?php post_class( 'agency-grid-style' ); ?>>
    <div class="agency-inner">
        <header class="team-header agency-header">
			<?php opalestate_get_loop_agent_thumbnail(); ?>
			<?php if ( 'on' === $agency->is_featured() ): ?>
                <div class="agency-label">
					<span class="label label-featured" aria-label="<?php esc_attr_e( 'Featured Agency', 'opalestate-pro' ); ?>" title="<?php esc_attr_e( 'Featured Agency', 'opalestate-pro' ); ?>">
						<?php esc_html_e( 'Featured', 'opalestate-pro' ); ?>
					</span>
                </div>
			<?php endif; ?>

			<?php if ( 'on' === $agency->get_trusted() ): ?>
                <span class="trusted-label hint--top" aria-label="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>" title="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>">
				<i class="fa fa-star"></i>
			</span>
			<?php endif; ?>

        </header>

        <div class="agency-body-content clearfix">
            <div class="agency-info">
                <div class="agency-logo">
					<?php $data = OpalEstate_Agency::get_link( $agency_id ); ?>
                    <a href="<?php echo esc_url( $data['link'] ); ?>">
                        <img src="<?php echo esc_url( $data['avatar'] ); ?>">
                    </a>
                </div>
                <div class="agency-content">
                    <h6 class="agency-box-title text-uppercase">
                        <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
                    </h6><!-- /.agency-box-title -->
                    <h3 class="agency-name hide"><?php the_title(); ?></h3>
                    <p class="agency-address">
						<?php echo esc_html( $address ); ?>
                    </p>
                </div>
            </div>

            <div class="agency-box-meta">

				<?php $phone = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'phone', true ); ?>
				<?php if ( ! empty( $phone ) ) : ?>
                    <div class="agency-box-phone">
                        <i class="fa fa-phone"></i>
                        <a href="tel:<?php echo sanitize_title( $phone ); ?>">
                            <span><?php echo esc_html( $phone ); ?></span>
                        </a>
                    </div><!-- /.agency-box-phone -->
				<?php endif; ?>
				<?php $email = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'email', true ); ?>
				<?php if ( ! empty( $email ) ) : ?>
                    <div class="agency-box-email ">
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>">
                            <span><?php echo esc_html( $email ); ?></span>
                        </a>
                    </div><!-- /.agency-box-email -->
				<?php endif; ?>
            </div><!-- /.agency-box-meta -->
        </div>
    </div>
</article>	
