<?php
global $property;

$infos = $property->get_meta_fullinfo();
$taxs  = $property->get_types_tax();

if ( ! $infos && ! $taxs ) {
	return;
}
?>

<div class="property-information box-inner-summary">
    <h5><?php esc_html_e( 'Quick Information', 'opalestate-pro' ); ?></h5>
    <div class="box-content">
        <ul class="list-info row">
			<?php if ( $taxs ): ?>
                <li class="wp-col-md-6">
                    <div class="property-label-type">
                        <h6><?php esc_html_e( 'Type:', 'opalestate-pro' ); ?></h6>
						<?php foreach ( $taxs as $tax ): ?>
                            <a href="<?php echo get_term_link( $tax->term_id ); ?>"><?php echo esc_html( $tax->name ); ?></a>
						<?php endforeach; ?>
                    </div>
                </li>
			<?php endif; ?>

			<?php if ( $infos ): ?>
				<?php foreach ( $infos as $key => $info ) : ?>
					<?php if ( $info['value'] ) : ?>
                        <li class="wp-col-md-6 ">
                            <div class="property-label-<?php echo esc_attr( $key ); ?>">
                                <h6><i class="<?php echo opalestate_get_property_meta_icon( $key ); ?>"></i> <?php echo esc_html( $info['label'] ); ?> : </h6>
                                <span><?php echo apply_filters( 'opalestate_' . $key . '_unit_format', trim( $info['value'] ) ); ?></span>
                            </div>
                        </li>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
        </ul>
    </div>
</div>
