<?php
/**
 * The template for vertival search
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['group-info-column'] = 1;

$display_country      = isset( $display_country ) ? $display_country : true;
$display_state        = isset( $display_state ) ? $display_state : true;
$display_city         = isset( $display_city ) ? $display_city : true;
$display_more_options = isset( $display_more_options ) ? $display_more_options : true;
$info_number_input    = isset( $info_number_input ) ? $info_number_input : true;
$type                 = $info_number_input ? 'input' : 'select';

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--vertical-3',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];
?>

<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
    <div class="opal-form-content">
		<?php if ( $display_country || $display_state || $display_city ) : ?>
            <div class="form-item form-item--location">
                <h6> <?php esc_html_e( 'Location', 'opalestate-pro' ); ?></h6>
				<?php
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
            </div>
		<?php endif; ?>

        <div class="form-item form-item--types">
            <h6> <?php esc_html_e( 'Types', 'opalestate-pro' ); ?></h6>
			<?php echo opalestate_load_template_path( 'search-box/fields/types', [ 'ismultiple' => true ] ); ?>
        </div>

        <div class="form-item form-item--information">
            <h6> <?php esc_html_e( 'Information', 'opalestate-pro' ); ?></h6>
			<?php echo opalestate_load_template_path( 'search-box/fields/group-info', [ 'type' => $type ] ); ?>
        </div>

		<?php if ( opalestate_is_enable_price_field() ) : ?>
            <div class="form-item form-item--price">
                <h6> <?php esc_html_e( 'Price', 'opalestate-pro' ); ?></h6>
				<?php echo opalestate_load_template_path( 'search-box/fields/price' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( opalestate_is_enable_areasize_field() ) : ?>
            <div class="form-item form-item--area">
                <h6> <?php esc_html_e( 'Area', 'opalestate-pro' ); ?></h6>
				<?php echo opalestate_load_template_path( 'search-box/fields/areasize' ); ?>
            </div>
		<?php endif; ?>

		<?php
		if ( $display_more_options ) {
			echo opalestate_load_template_path( 'search-box/fields/more-options' );
		}
		?>

		<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
            <div class="form-item form-item--submit">
				<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
            </div>
		<?php endif; ?>
    </div>

	<?php do_action( 'opalestate_after_search_properties_form' ); ?>
</form>

