<?php
/**
 * The template for advanced v5 search
 * // http://homevillas.chimpgroup.com/home-v5/
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $nobutton ) && $nobutton ) {
	$grid = [
		0 => 3,
		1 => 3,
		2 => 3,
		3 => 3,
		4 => 0,
	];
} else {
	$grid = [
		0 => 3,
		1 => 3,
		2 => 3,
		3 => 2,
		4 => 1,
	];
}

$display_more_options = isset( $display_more_options ) ? $display_more_options : false;

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--advanced-5',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>
<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
    <div class="opal-row">
        <div class="col-lg-<?php echo esc_attr( $grid[0] ); ?> col-md-<?php echo esc_attr( $grid[0] ); ?> col-sm-<?php echo esc_attr( $grid[0] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/search-text' ); ?>
        </div>

        <div class="col-lg-<?php echo esc_attr( $grid[1] ); ?> col-md-<?php echo esc_attr( $grid[1] ); ?> col-sm-<?php echo esc_attr( $grid[1] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/location' ); ?>
        </div>

        <div class="col-lg-<?php echo esc_attr( $grid[2] ); ?> col-md-<?php echo esc_attr( $grid[2] ); ?> col-sm-<?php echo esc_attr( $grid[2] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/status-bar', [ 'hide_default_status' => 1 ] ); ?>
        </div>

        <div class="col-lg-<?php echo esc_attr( $grid[3] ); ?> col-md-<?php echo esc_attr( $grid[3] ); ?> col-sm-<?php echo esc_attr( $grid[3] ); ?> col-xs-12">
			<?php echo opalestate_load_template_path( 'search-box/fields/types' ); ?>
        </div>

		<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
				<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
            </div>
		<?php endif; ?>

	    <?php if ( $display_more_options ) : ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			    <?php echo opalestate_load_template_path( 'search-box/fields/more-options' ); ?>
            </div>
	    <?php endif; ?>
    </div>

	<?php do_action( 'opalestate_after_search_properties_form' ); ?>
</form>
