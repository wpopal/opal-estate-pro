<?php
/**
 * Short meta HTML.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $property;

$meta_content = apply_filters( 'opalestate_loop_meta_info', '' );
if ( empty( $meta_content ) ) {
	$meta = $property->get_meta_shortinfo();
	?>
    <div class="property-meta">
        <ul class="property-meta-list list-inline">
			<?php if ( $meta ) : ?>
				<?php foreach ( $meta as $key => $info ) : ?>
					<?php if ( trim( $info['value'] ) ) : ?>
                        <li class="property-label-<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr( $info['label'] ); ?>">
                            <span class="hint--top" aria-label="<?php echo esc_attr( $info['label'] ); ?>" title="<?php echo esc_attr( $info['label'] ); ?>">
                                <i class="<?php echo opalestate_get_property_meta_icon( $key ); ?>"></i></span>
                            <span class="label-property"><?php echo esc_html( $info['label'] ); ?></span>
                            <span class="label-content"><?php echo apply_filters( 'opalestate-pro' . $key . '_unit_format', trim( $info['value'] ) ); ?></span>
                        </li>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
        </ul>
    </div>
	<?php
} else {
	echo wp_kses_post( $meta_content );
}
