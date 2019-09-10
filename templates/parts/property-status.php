<?php
global $property;
$statuses = $property->get_status();

if ( is_wp_error( $statuses ) || ! $statuses ) {
	return;
}

?>
<ul class="property-status">
	<?php foreach ( $statuses as $key => $value ) : ?>
        <li class="property-status-item property-status-<?php echo esc_attr( $value->slug ); ?>">
            <span class="label-status label"><?php echo esc_html( $value->name ); ?></span>
        </li>
	<?php endforeach; ?>
</ul>
