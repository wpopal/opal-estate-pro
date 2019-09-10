<?php
/**
 * The template for advanced v6 search
 * // http://homevillas.chimpgroup.com/demo-v7/
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$display_more_options = isset( $display_more_options ) ? $display_more_options : false;

$form_classes = [
	'opalestate-search-form',
	'opalestate-search-form--advanced-6',
	isset( $nobutton ) && $nobutton ? 'no-submit-btn' : 'has-submit-btn',
	isset( $hidden_labels ) && $hidden_labels ? 'hidden-labels' : '',
];

?>

<form class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $form_classes ) ) ); ?>" action="<?php echo esc_url( opalestate_get_search_link() ); ?>" method="GET">
	<div class="opalestate-search-form__item">
		<?php echo opalestate_load_template_path( 'search-box/fields/status' ); ?>
	</div>

	<div class="opalestate-search-form__item">
		<?php echo opalestate_load_template_path( 'search-box/fields/search-text' ); ?>
	</div>

	<?php if ( ! isset( $nobutton ) || ! $nobutton ) : ?>
		<?php echo opalestate_load_template_path( 'search-box/fields/submit-button' ); ?>
	<?php endif; ?>

	<?php
	if ( $display_more_options ) {
		echo opalestate_load_template_path( 'search-box/fields/more-options' );
	}
	?>

	<?php do_action( 'opalestate_after_search_properties_form' ); ?>
</form>
