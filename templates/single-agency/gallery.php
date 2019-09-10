<?php
global $agency;
$gallery = $agency->get_gallery();

if ( ! $gallery ) {
	return;
}
?>
<div class="opalestate-gallery">
    <h4 class="box-heading"><?php esc_html_e( 'Gallery', 'opalestate-pro' ); ?></h4>
    <div class="gallery-summery-style">
		<?php foreach ( $gallery as $key => $src ): ?>
            <a href="<?php echo esc_url( $src ); ?>" style="background-image:url('<?php echo esc_url( $src ); ?>')"></a>
		<?php endforeach; ?>
    </div>
</div>

