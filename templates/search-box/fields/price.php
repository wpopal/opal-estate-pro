<?php
$search_min_price = isset( $_GET['min_price'] ) ? sanitize_text_field( $_GET['min_price'] ): opalestate_options( 'search_min_price', 0 );
$search_max_price = isset( $_GET['max_price'] ) ? sanitize_text_field( $_GET['max_price'] ): opalestate_options( 'search_max_price', 10000000 );

$data = [
	'id'         => 'price',
	'decimals'   => opalestate_get_price_decimals(),
	'unit'       => opalestate_currency_symbol() . ' ',
	'ranger_min' => opalestate_options( 'search_min_price', 0 ),
	'ranger_max' => opalestate_options( 'search_max_price', 10000000 ),
	'input_min'  => $search_min_price,
	'input_max'  => $search_max_price,
];

if ( opalestate_options( 'currency_position', 'before' ) == 'before' ) {
	$data['unit_position'] = 'prefix';
}

?>
<?php opalesate_property_slide_ranger_template( esc_html__( 'Price', 'opalestate-pro' ), $data ); ?>
