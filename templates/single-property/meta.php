<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $property, $post;

$meta = $property->get_meta_shortinfo();
?>
<div class="property-meta">
    <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
        <div class="col-lg-3 col-md-3">
            <div class="property-price">
                <span><?php esc_html_e( 'Price:', 'opalestate-pro' ); ?><?php echo opalestate_price_format( $property->get_price() ); ?></span>

				<?php if ( $property->get_sale_price() ): ?>
                    <span class="property-saleprice"><?php echo opalestate_price_format( $property->get_sale_price() ); ?></span>
				<?php endif; ?>

				<?php if ( $property->get_price_label() ): ?>
                    <span class="property-price-label"><?php echo esc_html( $property->get_price_label() ); ?></span>
				<?php endif; ?>
            </div>
        </div>
        <div class="col-lg-9 col-md-9">
            <ul class="property-meta-list list-inline">
				<?php if ( $meta ) : ?>
					<?php foreach ( $meta as $key => $info ) : ?>
                        <li class="property-label-<?php echo esc_attr( $key ); ?>">
                            <i class="<?php echo opalestate_get_property_meta_icon( $key ); ?>"></i><?php echo esc_html( $info['label'] ); ?> <span>
							<?php echo apply_filters( 'opalestate-pro' . $key . '_unit_format', trim( $info['value'] ) ); ?></span>
                        </li>
					<?php endforeach; ?>
				<?php endif; ?>
            </ul>
        </div>
    </div>
</div>	
