<div class="property-agency-contact">

	<div class="col-sm-3">
		<div class="property-agency-contact">
			<div class="agency-box">
			    <?php if ( has_post_thumbnail() ) : ?>
					<div class="agency-box-image <?php if ( ! has_post_thumbnail() ) { echo 'without-image'; } ?>">
				        <a href="<?php the_permalink(); ?>" class="agency-box-image-inner <?php if ( ! empty( $agency ) ) : ?>has-agency<?php endif; ?>">
			                <?php the_post_thumbnail( 'agency-thumbnail' ); ?>
				        </a>
					</div><!-- /.agency-box-image -->
			    <?php endif; ?>

			    <div class="agency-box-meta">

			        <?php $email = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'email', true ); ?>
			        <?php if ( ! empty( $email ) ) : ?>
			            <div class="agency-box-email">
				            <a href="mailto:<?php echo esc_attr( $email ); ?>">
			                   <i class="fa fa-email"></i> <?php echo esc_attr( $email ); ?>
				            </a>
			            </div><!-- /.agency-box-email -->
			        <?php endif; ?>

			        <?php $phone = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'phone', true ); ?>
			        <?php if ( ! empty( $phone ) ) : ?>
			            <div class="agency-box-phone">
			                <?php echo esc_attr( $phone ); ?>
			            </div><!-- /.agency-box-phone -->
			        <?php endif; ?>

				    <?php $web = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'web', true ); ?>
				    <?php if ( ! empty( $web ) ) : ?>
					    <div class="agency-box-web">
						    <a href="<?php echo esc_attr( $web ); ?>">
						        <?php echo esc_attr( $web ); ?>
						    </a>
					    </div>
				    <?php endif; ?>
			    </div>
			</div>

		</div>
	</div>
	<div class="col-sm-9">
		<h3 class="agency-box-title">
            <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
        </h3>
        <div class="content">
        	<?php the_content(); ?>
        </div>
	</div>
</div>