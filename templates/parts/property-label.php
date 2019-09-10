<?php
global $property;

opalestate_property_featured_label();

$labels = $property->get_labels();

if ( is_wp_error( $labels ) || ! $labels ) {
	return;
}
?>

<ul class="property-label">
	<?php foreach ( $labels as $key => $value ) : ?>
        <li class="property-label-item property-label-<?php echo esc_attr( $value->slug ); ?>">
            <span class="label-label label"><?php echo esc_html( $value->name ); ?></span>
        </li>
	<?php endforeach; ?>
</ul>
