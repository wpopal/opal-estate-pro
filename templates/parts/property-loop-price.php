<?php
/**
 * Price HTML.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $property;

if ( ! opalestate_is_require_login_to_show_price() ) {
	?>
    <div class="opalestate-require-login-box">
        <div class="opalestate-require-login-notice"><?php esc_html_e( 'You need to login to see the price.', 'opalestate-pro' ); ?>
            <a href="#opalestate-user-form-popup" class="opalestate-need-login"><?php esc_html_e( 'Login', 'opalestate-pro' ) ?></a>
        </div>
    </div>
	<?php
	return;
}
?>

<div class="property-price">
	<?php if ( 'on' == $property->get_price_oncall() ): ?>
        <div class="call-to-price"><?php esc_html_e( 'Call to Price', 'opalestate-pro' ); ?></div>
	<?php elseif ( $property->get_price() ): ?>
		<?php if ( $property->get_before_price_label() ): ?>
            <span class="property-before-price-label"><?php echo esc_html( $property->get_before_price_label() ); ?></span>
		<?php endif; ?>

		<?php if ( $property->get_sale_price() ): ?>
            <span class="property-regular-price has-saleprice">
                <del><?php echo opalestate_price_format( $property->get_price() ); ?></del>
            </span>
            <span class="property-saleprice"><?php echo opalestate_price_format( $property->get_sale_price() ); ?></span>
		<?php else : ?>
            <span class="property-regular-price"><?php echo opalestate_price_format( $property->get_price() ); ?></span>
		<?php endif; ?>

		<?php if ( $property->get_price_label() ): ?>
            <span class="property-price-label"><?php echo esc_html( $property->get_price_label() ); ?></span>
		<?php endif; ?>
	<?php else: ?>
        <div class="property-no-price"><?php esc_html_e( 'Contact Property', 'opalestate-pro' ); ?></div>
	<?php endif; ?>
</div>
