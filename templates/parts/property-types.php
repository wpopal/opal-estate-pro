<?php
global $property;

echo get_the_term_list(
	$property->get_id(),
	'opalestate_types',
	'<div class="property-types-list"><span class="property-types-list__label">' . esc_html__( 'Types:', 'opalestate-pro' ) . '</span>',
	', ',
	'</div>'
);
