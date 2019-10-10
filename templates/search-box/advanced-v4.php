<?php
/**
 * The template for advanced v4 search
 * // https://wpresidence.net/advanced-search-type-4/
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['group-info-column'] = 4;

$display_country      = isset( $display_country ) ? $display_country : true;
$display_state        = isset( $display_state ) ? $display_state : false;
$display_city         = isset( $display_city ) ? $display_city : false;
$display_more_options = isset( $display_more_options ) ? $display_more_options : true;

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--advanced-4',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>
<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
    <div class="opal-row">
        <div class="col-lg-12 col-md-12 col-sm-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/location' ); ?>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/radius' ); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3">
			<?php echo opalestate_load_template_path( 'search-box/fields/types' ); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3">
			<?php echo opalestate_load_template_path( 'search-box/fields/status' ); ?>
        </div>

		<?php if ( $display_country ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-3">
				<?php echo opalestate_load_template_path( 'search-box/fields/country-select' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( $display_state ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-3">
				<?php echo opalestate_load_template_path( 'search-box/fields/state-select' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( $display_city ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-3">
				<?php echo opalestate_load_template_path( 'search-box/fields/city-select' ); ?>
            </div>
		<?php endif; ?>

		<?php echo opalestate_load_template_path( 'search-box/fields/group-info' ); ?>

        <div class="col-lg-6 col-md-6 col-sm-6">
			<?php if ( opalestate_is_enable_price_field() ) : ?>
				<?php echo opalestate_load_template_path( 'search-box/fields/price' ); ?>
			<?php endif; ?>

			<?php if ( opalestate_is_enable_areasize_field() ) : ?>
				<?php echo opalestate_load_template_path( 'search-box/fields/areasize' ); ?>
			<?php endif; ?>
        </div>

		<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-3">
				<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
            </div>
		<?php endif; ?>
    </div>

	<?php
	if ( $display_more_options ) {
		echo opalestate_load_template_path( 'search-box/fields/more-options' );
	}
	?>

	<?php do_action( 'opalestate_after_search_properties_form' ); ?>
</form>
