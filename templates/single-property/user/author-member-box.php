<?php
	if( !isset($id) ){
		$id = get_the_ID();
	}

	if( !isset($prefix) ){
		$prefix = OPALESTATE_AGENCY_PREFIX;
	}

	$avatar     = get_post_meta( $id , $prefix . "avatar", true );
	$post 		= get_post( $id );
	$link 	    = get_permalink( $id );
	$name	    = get_the_title( $id );
	$trusted 	= get_post_meta( $id, $prefix.'trusted', true );
?>
<div class="property-agent-contact ">
	<?php $is_sticky = get_post_meta( $id, $prefix . 'sticky', true ); ?>
	<div class="agent-box agency-box">
		<div class="team-header">
			<?php if( $type =='agency'): ?>
			<div  class="agent-preview has-avatar"><a href="<?php echo esc_url($link); ?>">
				<?php if ( has_post_thumbnail( $id ) ) : ?>
	 			  <?php echo get_the_post_thumbnail( $id, 'full' ); ?>
				<?php endif; ?>
		  	 	<img src="<?php echo esc_url($avatar);?> " class="agent-avatar"></a>

		  	 	<?php if( $trusted ): ?>
	        	<span class="trusted-label hint--top" aria-label="<?php esc_html_e('Trusted Member', 'opalestate-pro'); ?>" title="<?php esc_html_e('Trusted Member', 'opalestate-pro'); ?>">
					<i class="fas fa-star"></i>
				</span>
				<?php endif; ?>

			</div>
			<?php else : ?>
			<div  class="agent-preview"><a href="<?php echo esc_url($link); ?>">
				<img src="<?php echo esc_url($avatar);?> " class="agent-avatar"></a>
			</div>
			<?php endif; ?>
		</div>
	    <div class="agent-box-meta">
	        <h4 class="agent-box-title">
	        	<a href="<?php echo esc_url( $link ); ?>">
	        		<?php echo esc_html( $post->post_title ); ?>
	        	</a>
	        </h4><!-- /.agent-box-title -->


	        <?php $email = get_post_meta( $id, $prefix . 'email', true ); ?>
	        <?php if ( ! empty( $email ) ) : ?>
	            <div class="agent-box-email">
					<i class="fa fa-envelope"></i>
		            <a href="mailto:<?php echo esc_attr( $email ); ?>">
	                   <span><?php echo esc_html( $email ); ?></span>
		            </a>
	            </div><!-- /.agent-box-email -->
	        <?php endif; ?>


	        <?php $phone = get_post_meta( $id, $prefix . 'phone', true ); ?>
	        <?php if ( ! empty( $phone ) ) : ?>
	            <div class="agent-box-phone">
	               <i class="fa fa-phone"></i><span><a href="tel:<?php echo sanitize_title( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></span>
	            </div><!-- /.agent-box-phone -->
	        <?php endif; ?>

	        <?php $mobile = get_post_meta( $id, $prefix . 'mobile', true ); ?>
	        <?php if ( ! empty( $mobile ) ) : ?>
	            <div class="agent-box-mobile">
	                <i class="fa fa-mobile"></i><span><a href="tel:<?php echo sanitize_title( $mobile ); ?>"><?php echo esc_html( $mobile ); ?></a></span>
	            </div><!-- /.agent-box-phone -->
	        <?php endif; ?>

	        <?php $fax = get_post_meta( $id, $prefix . 'fax', true ); ?>
	        <?php if ( ! empty( $fax ) ) : ?>
	            <div class="agent-box-fax">
	                <i class="fa fa-fax"></i><span><?php echo esc_html( $fax ); ?></span>
	            </div><!-- /.agent-box-phone -->
	        <?php endif; ?>

		    <?php $web = get_post_meta( $id, $prefix . 'web', true ); ?>
		    <?php if ( ! empty( $web ) ) : ?>
			    <div class="agent-box-web">
					<i class="fa fa-globe"></i>
				    <a href="<?php echo esc_attr( $web ); ?>" rel="nofollow" target="_blank">
				        <span><?php echo esc_html( $web ); ?></span>
				    </a>
			    </div><!-- /.agent-box-web -->
		    <?php endif; ?>
		   	<?php
				$facebook 	= get_post_meta( $id, $prefix . 'facebook', true );
				$twitter 	= get_post_meta( $id, $prefix . 'twitter', true );
				$pinterest  = get_post_meta( $id, $prefix . 'pinterest', true );
				$google 	= get_post_meta( $id, $prefix . 'google', true );
				$instagram	= get_post_meta( $id, $prefix . 'instagram', true );
				$linkedIn   = get_post_meta( $id, $prefix . 'linkedIn', true );
			?>
	        <div class="opalestate-social-icons">
	        	<?php if( $facebook && $facebook != "#" && !empty($facebook) ){  ?>
				<a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $facebook ); ?>"> <i  class="fab fa-facebook"></i> </a>
					<?php } ?>
				<?php if( $twitter && $twitter != "#" && !empty($twitter) ){  ?>
				<a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $twitter ); ?>"><i  class="fab fa-twitter"></i> </a>
				<?php } ?>
				<?php if( $pinterest && $pinterest != "#" && !empty($pinterest)){  ?>
				<a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $pinterest ); ?>"><i  class="fab fa-pinterest"></i> </a>
				<?php } ?>
				<?php if( $google && $google != "#" && !empty($google) ){  ?>
				<a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $google ); ?>"> <i  class="fab fa-google"></i></a>
				<?php } ?>

				<?php if( $instagram && $instagram != "#" && !empty($instagram) ){  ?>
				<a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $instagram ); ?>"> <i  class="fab fa-instagram"></i></a>
				<?php } ?>

				<?php if( $linkedIn && $linkedIn != "#" && !empty($linkedIn) ){  ?>
				<a class="opalestate-social-white radius-x" rel="nofollow" href="<?php echo esc_url( $linkedIn ); ?>"> <i  class="fab fa-linkedIn"></i></a>
				<?php } ?>

	        </div>

	    </div><!-- /.agent-box-content -->
	</div><!-- /.agent-box-->
</div>
