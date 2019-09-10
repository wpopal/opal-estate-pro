<div class="property-agent-contact">

	<div class="col-sm-3">
		<div class="property-agent-contact">
			<div class="agent-box">
			    <?php if ( has_post_thumbnail() ) : ?>
					<div class="box-image <?php if ( ! has_post_thumbnail() ) { echo 'without-image'; } ?>">
				        <a href="<?php the_permalink(); ?>" class="agent-box-image-inner <?php if ( ! empty( $agent ) ) : ?>has-agent<?php endif; ?>">
			                <?php the_post_thumbnail( 'agent-thumbnail' ); ?>
				        </a>
					</div><!-- /.agent-box-image -->
			    <?php endif; ?>

			    <div class="agent-box-meta">

			        <?php $email = get_post_meta( get_the_ID(), OPALESTATE_AGENT_PREFIX . 'email', true ); ?>
			        <?php if ( ! empty( $email ) ) : ?>
			            <div class="agent-box-email">
							<i class="fa fa-email"></i> 
				            <a href="mailto:<?php echo esc_attr( $email ); ?>">
			                   <?php echo esc_attr( $email ); ?>
				            </a>
			            </div><!-- /.agent-box-email -->
			        <?php endif; ?>

			        <?php $phone = get_post_meta( get_the_ID(), OPALESTATE_AGENT_PREFIX . 'phone', true ); ?>
			        <?php if ( ! empty( $phone ) ) : ?>
			            <div class="agent-box-phone">
			                <?php echo esc_attr( $phone ); ?>
			            </div><!-- /.agent-box-phone -->
			        <?php endif; ?>

				    <?php $web = get_post_meta( get_the_ID(), OPALESTATE_AGENT_PREFIX . 'web', true ); ?>
				    <?php if ( ! empty( $web ) ) : ?>
					    <div class="agent-box-web">
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
		<h3 class="agent-box-title">
            <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
        </h3>
        <div class="content">
        	<?php the_content(); ?>
        </div>
	</div>
</div>