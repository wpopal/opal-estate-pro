<?php
global $property;

if ( ! $property->is_featured() ) {
	return;
}
?>

<span class="label-featured label"><?php esc_html_e( 'Featured', 'opalestate-pro' ); ?></span>
