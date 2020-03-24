<?php
if ( ! opalestate_is_enable_price_field() ) {
	return;
}

$search_min_price = isset( $_GET['min_price'] ) ? sanitize_text_field( $_GET['min_price'] ) : opalestate_options( 'search_min_price', 0 );
$search_max_price = isset( $_GET['max_price'] ) ? sanitize_text_field( $_GET['max_price'] ) : opalestate_options( 'search_max_price', 10000000 );

$data = [
	'id'            => 'price',
	'decimals'      => opalestate_get_price_decimals(),
	'unit'          => opalestate_currency_symbol() . ' ',
	'ranger_min'    => opalestate_options( 'search_min_price', 0 ),
	'ranger_max'    => opalestate_options( 'search_max_price', 10000000 ),
	'input_min'     => $search_min_price,
	'input_max'     => $search_max_price,
	'unit_thousand' => opalestate_options( 'thousands_separator', ',' ),
];

if ( opalestate_options( 'currency_position', 'before' ) == 'before' ) {
	$data['unit_position'] = 'prefix';
}

if ( 'input' === opalestate_get_option( 'price_input_type', 'slider' ) ) {
	echo opalestate_load_template_path( 'search-box/fields/price-input', [ 'data' => $data ] );
} else {
	opalesate_property_slide_ranger_template( esc_html__( 'Price', 'opalestate-pro' ), $data );
}
