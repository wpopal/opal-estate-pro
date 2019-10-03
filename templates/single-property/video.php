<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'video' ) ) {
	return;
}

$video_url = $property->get_video_url();

if ( ! $video_url ) {
	return;
}
?>

<div class="opalestate-box-content property-video-session">
    <h4 class="outbox-title" id="block-video"><?php esc_html_e( 'Video', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="box-info">
			<?php echo wp_oembed_get( $video_url ); ?>
        </div>
    </div>
</div>
