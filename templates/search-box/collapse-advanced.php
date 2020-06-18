<?php
/**
 * The template for collapse advanced search
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$unique_id                    = esc_attr( opalestate_unique_id() );
$GLOBALS['group-info-column'] = 3;

$display_more_options = isset( $display_more_options ) ? $display_more_options : true;
$max_range            = isset( $max_range ) && $max_range ? $max_range : apply_filters( 'opalestate_search_geo_max_range', 10 );
$range_unit           = isset( $range_unit ) ? $range_unit : 'km';

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--collapse-advanced',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>

<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
    <div class="searchbox-top">
		<?php echo opalestate_load_template_path( 'search-box/fields/status-bar', [ 'style' => 2 ] ); ?>
    </div>

    <div class="searchbox-main">
        <div class="searchbox-field searchbox-field--city-text">
			<?php echo opalestate_load_template_path( 'search-box/fields/search-city-text', [ 'max_range' => $max_range, 'range_unit' => $range_unit ] ); ?>
        </div>

        <div class="searchbox-field searchbox-field--types">
			<?php echo opalestate_load_template_path( 'search-box/fields/types' ); ?>
        </div>

        <div class="searchbox-field searchbox-field--categories">
			<?php echo opalestate_load_template_path( 'search-box/fields/categories' ); ?>
        </div>

        <div class="searchbox-field searchbox-field--collapse">
            <button type="button" class="opal-collapse-button opalestate-collapse-btn btn btn-primary" data-collapse="#collapse-city-<?php echo esc_attr( $unique_id ); ?>">
				<?php echo apply_filters( 'opalestate_search_form_collapse_button', '<i class="fa fa-caret-down" aria-hidden="true"></i>' ); ?>
            </button>
        </div>

		<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
            <div class="searchbox-field searchbox-field--submit">
				<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
            </div>
		<?php endif; ?>
    </div>

    <div id="collapse-city-<?php echo esc_attr( $unique_id ); ?>" class="opal-collapse-container collapse-city-inputs">
        <div class="opal-row">
			<?php echo opalestate_load_template_path( 'search-box/fields/group-info' ); ?>

			<?php if ( opalestate_is_enable_price_field() ) : ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<?php echo opalestate_load_template_path( 'search-box/fields/price' ); ?>
                </div>
			<?php endif; ?>

			<?php if ( opalestate_is_enable_areasize_field() ) : ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<?php echo opalestate_load_template_path( 'search-box/fields/areasize' ); ?>
                </div>
			<?php endif; ?>
        </div>

		<?php
		if ( $display_more_options ) {
			echo opalestate_load_template_path( 'search-box/fields/more-options' );
		}
		?>
    </div>

	<?php do_action( 'opalestate_after_search_properties_form' ); ?>
</form>
