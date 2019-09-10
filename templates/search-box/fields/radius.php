<?php
$search_min_radius = isset( $_GET['min_radius'] ) ? sanitize_text_field( $_GET['min_radius'] ) : opalestate_options( 'search_min_radius', 0 );
$search_max_radius = isset( $_GET['max_radius'] ) ? sanitize_text_field( $_GET['max_radius'] ): opalestate_options( 'search_max_radius', 10000000 );

$data = [
	'id'         => 'radius',
	'unit'       => 'miles',
	'ranger_min' => opalestate_options( 'search_min_radius', 0 ),
	'ranger_max' => opalestate_options( 'search_max_radius', 10000000 ),
	'input_min'  => $search_min_radius,
	'input_max'  => $search_max_radius,
];

opalesate_property_slide_ranger_template( esc_html__( 'Radius:', 'opalestate-pro' ), $data );
?>
