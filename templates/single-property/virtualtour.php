<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'virtual_tour' ) ) {
	return;
}

$virtual_tour = $property->get_virtual_tour();

if ( ! $virtual_tour ) {
	return;
}
?>

<div class="opalestate-box-content property-360-virtual-session">
    <h4 class="outbox-title" id="block-tour360"><?php esc_html_e( '360° Virtual Tour', 'opalestate-pro' ); ?></h4>
    <div class=" opalestate-box">
        <div class="box-info">
			<?php echo do_shortcode( $virtual_tour ); ?>
        </div>
    </div>
</div>
