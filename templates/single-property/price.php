<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $property, $post;
$fomart = $property->get_format_price();
?>
<div class="property-price">
	<?php esc_html_e( 'Price:', 'opalestate-pro' ); ?><?php echo esc_html( $fomart . $property->get_price() ); ?>
</div>
<div class="property-saleprice">
	<?php esc_html_e( 'Sale Price:', 'opalestate-pro' ); ?><?php echo esc_html( $fomart . $property->get_sale_price() ); ?>
</div>
