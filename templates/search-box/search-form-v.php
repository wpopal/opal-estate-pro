<?php
/**
 * The template for vertival search
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['group-info-column'] = 1;

$display_category     = isset( $display_category ) ? $display_category : true;
$display_country      = isset( $display_country ) ? $display_country : true;
$display_state        = isset( $display_state ) ? $display_state : true;
$display_city         = isset( $display_city ) ? $display_city : true;
$display_more_options = isset( $display_more_options ) ? $display_more_options : true;
$info_number_input    = isset( $info_number_input ) ? $info_number_input : true;
$type                 = $info_number_input ? 'input' : 'select';

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--vertical',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>

<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
	<?php
	if ( $display_category ) {
		echo opalestate_load_template_path( 'search-box/fields/categories' );
	}

	if ( $display_country ) {
		echo opalestate_load_template_path( 'search-box/fields/country-select' );
	}

	if ( $display_state ) {
		echo opalestate_load_template_path( 'search-box/fields/state-select' );
	}

	if ( $display_city ) {
		echo opalestate_load_template_path( 'search-box/fields/city-select' );
	}
	?>

	<?php echo opalestate_load_template_path( 'search-box/fields/types' ); ?>

	<?php echo opalestate_load_template_path( 'search-box/fields/group-info', [ 'type' => $type ] ); ?>

	<?php if ( opalestate_is_enable_price_field() ) : ?>
		<?php echo opalestate_load_template_path( 'search-box/fields/price' ); ?>
	<?php endif; ?>

	<?php
	if ( $display_more_options ) {
		echo opalestate_load_template_path( 'search-box/fields/more-options' );
	}
	?>

	<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
		<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
	<?php endif; ?>

	<?php do_action( 'opalestate_after_search_properties_form' ); ?>
</form>

