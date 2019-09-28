<?php 
if(  $existed != false || $existed != '' ) {
    $fav_class = 'fas fa-heart';
} else {
    $fav_class = 'far fa-heart';
}
$need_login = '';
if( !is_user_logged_in() ){
	$need_login .= ' opalestate-need-login';
}
?>
<span class="property-toggle-favorite <?php echo esc_attr( $need_login ); ?> hint--top" aria-label="<?php esc_html_e('Add To Favorite', 'opalestate-pro'); ?>" data-property-id="<?php echo intval( $property_id ); ?>" title="<?php esc_html_e('Add To Favorite', 'opalestate-pro'); ?>">
	<span class="<?php echo esc_attr( $fav_class ); ?>"></span>
</span>