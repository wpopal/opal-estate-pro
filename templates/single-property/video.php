<?php
global $property;

if ( ! $property->get_block_setting( 'video' ) ) {
	return;
}

$videoURL = $property->get_video_url();
?>
<?php if ( $videoURL ) : ?>
<div class="opalestate-box-content property-video-session">
    <h4 class="outbox-title" id="block-video"><?php esc_html_e( 'Video', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="box-info">
			<?php echo wp_oembed_get( $videoURL ); ?>
        </div>
    </div>
</div>
<?php endif; ?>
