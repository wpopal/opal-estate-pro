<?php 
	$package_id 			  = get_the_ID();
	$pack_listings            =   get_post_meta( $package_id, OPALMEMBERSHIP_PACKAGES_PREFIX.'package_listings', true );
	$pack_featured_listings   =   get_post_meta( $package_id, OPALMEMBERSHIP_PACKAGES_PREFIX.'package_featured_listings', true );
	$pack_unlimited_listings  =   get_post_meta( $package_id, OPALMEMBERSHIP_PACKAGES_PREFIX.'unlimited_listings', true );
	$unlimited_listings       = $pack_unlimited_listings == 'on' ? 0 : 1;
?>
<div class="pricing-more-info">
	<div class="item-info">
		<span>
			<?php if ( ( ( $pack_listings && ( -1 != $pack_listings ) ) || ( 0 == $pack_listings ) ) && ( $unlimited_listings  == 0 ) ) : ?>
				<?php echo trim( $pack_listings); ?><?php esc_html_e( ' Listings' , 'opalestate-pro' );?>
			<?php else: ?>
				<?php esc_html_e('Unlimited', 'opalestate-pro');?><?php esc_html_e( ' Listings' , 'opalestate-pro' );?>
			<?php endif; ?>
		</span>
	</div>
	<div class="item-info">
		<span>
			<?php if ( ( ( $pack_featured_listings && ( -1 != $pack_featured_listings ) ) || ( 0 == $pack_featured_listings ) ) && ( $unlimited_listings  == 0 ) ) : ?>
				<?php echo trim( $pack_featured_listings ); ?><?php esc_html_e( ' Featured', 'opalestate-pro' ); ?>
			<?php else: ?>
				<?php esc_html_e( 'Unlimited', 'opalestate-pro' ); ?><?php esc_html_e( ' Featured', 'opalestate-pro' ); ?>
			<?php endif; ?>
		</span>
	</div>
</div>
