<?php
/**
 * The template for advanced v1 search
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$display_country      = isset( $display_country ) ? $display_country : true;
$display_state        = isset( $display_state ) ? $display_state : false;
$display_city         = isset( $display_city ) ? $display_city : false;
$display_more_options = isset( $display_more_options ) ? $display_more_options : true;

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--advanced-1',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>

<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
    <div class="searchbox-top">
		<?php echo opalestate_load_template_path( 'search-box/fields/status-bar', [ 'style' => 2 ] ); ?>
    </div>

    <div class="opal-row">
		<?php if ( $display_country ) : ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/country-select' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( $display_state ) : ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/state-select' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( $display_city ) : ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/city-select' ); ?>
            </div>
		<?php endif; ?>

        <div class="col-lg-8 col-md-8 col-sm-12">
            <div class="opal-row">
				<?php echo opalestate_load_template_path( 'search-box/fields/group-info' ); ?>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/types' ); ?>
        </div>

		<?php if ( opalestate_is_enable_price_field() ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/price' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( opalestate_is_enable_areasize_field() ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/areasize' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
            <div class="col-lg-3 col-md-3 col-sm-12">
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
