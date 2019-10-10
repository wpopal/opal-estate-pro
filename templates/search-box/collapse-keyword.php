<?php
/**
 * The template for collapse keyword search
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['group-info-column'] = 4;

if ( isset( $nobutton ) && $nobutton ) {
	$grid = [
		0 => 5,
		1 => 3,
		2 => 3,
		3 => 1,
		4 => 0,
	];
} else {
	$grid = [
		0 => 5,
		1 => 2,
		2 => 2,
		3 => 1,
		4 => 2,
	];
}

$display_country      = isset( $display_country ) ? $display_country : true;
$display_state        = isset( $display_state ) ? $display_state : false;
$display_city         = isset( $display_city ) ? $display_city : false;
$display_more_options = isset( $display_more_options ) ? $display_more_options : true;

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--collapse-keyword',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>

<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
    <div class="opal-row">
        <div class="col-lg-<?php echo absint( $grid[0] ); ?> col-md-<?php echo absint( $grid[0] ); ?> col-sm-<?php echo absint( $grid[0] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/search-text' ); ?>
        </div>

        <div class="col-lg-<?php echo absint( $grid[1] ); ?> col-md-<?php echo absint( $grid[1] ); ?> col-sm-<?php echo absint( $grid[1] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/types' ); ?>
        </div>

        <div class="col-lg-<?php echo absint( $grid[2] ); ?> col-md-<?php echo absint( $grid[2] ); ?> col-sm-<?php echo absint( $grid[2] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/status' ); ?>
        </div>

        <div class="col-lg-<?php echo absint( $grid[3] ); ?> col-md-<?php echo absint( $grid[3] ); ?> col-sm-<?php echo absint( $grid[3] ); ?> col-xs-12">
            <button type="button" class="opal-collapse-button opalestate-collapse-btn btn btn-primary" data-collapse="#collapse-keyword-<?php echo esc_attr( $unique_id ); ?>">
				<?php echo apply_filters( 'opalestate_search_form_collapse_button', '<i class="fa fa-caret-down" aria-hidden="true"></i>' ); ?>
            </button>
        </div>

		<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
            <div class="col-lg-<?php echo absint( $grid[4] ); ?> col-md-<?php echo absint( $grid[4] ); ?> col-sm-<?php echo absint( $grid[4] ); ?> col-xs-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
            </div>
		<?php endif; ?>
    </div>

    <div id="collapse-keyword-<?php echo esc_attr( $unique_id ); ?>" class="opal-collapse-container collapse-keyword-inputs">
        <div class="opal-row">
			<?php if ( $display_country ) : ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<?php echo opalestate_load_template_path( 'search-box/fields/country-select' ); ?>
                </div>
			<?php endif; ?>

			<?php if ( $display_state ) : ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<?php echo opalestate_load_template_path( 'search-box/fields/state-select' ); ?>
                </div>
			<?php endif; ?>

			<?php if ( $display_city ) : ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<?php echo opalestate_load_template_path( 'search-box/fields/city-select' ); ?>
                </div>
			<?php endif; ?>

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
