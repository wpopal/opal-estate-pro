<?php
global $property;

$virtualTour =  $property->get_virtual_tour();
?>
<?php if( $virtualTour  ) : ?>
<div class="opalestate-box-content property-360-virtual-session">
    <h4 class="outbox-title" id="block-tour360"><?php esc_html_e( '360° Virtual Tour', 'opalestate-pro'  ); ?></h4>
    <div class=" opalestate-box">
        <div class="box-info">
            <?php echo do_shortcode( $virtualTour ); ?>
        </div>
    </div>
</div>
<?php endif; ?>
