<?php
global $property;

echo get_the_term_list(
	$property->get_id(),
	'property_category',
	'<div class="property-categories-list"><span class="property-categories-list__label">' . esc_html__( 'Categories:', 'opalestate-pro' ) . '</span>',
	', ',
	'</div>'
);
