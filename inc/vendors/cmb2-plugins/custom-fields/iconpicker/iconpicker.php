<?php
/**
 * Opalestate_Field_Iconpicker
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Opalestate_Field_Iconpicker {
	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * Initialize the plugin by hooking into CMB2.
	 */
	public function __construct() {
		add_filter( 'cmb2_render_opal_iconpicker', [ $this, 'render_iconpicker' ], 10, 5 );
		add_filter( 'cmb2_sanitize_opal_iconpicker', [ $this, 'sanitize' ], 10, 4 );
	}

	public function get_icons() {
		$fontawesome_key = 'opalestate_fontawesome_data';

		$icon_data = [];

		if ( false === ( $fontawesome_icons = get_transient( $fontawesome_key ) ) ) {
			$fontawesome       = new Opalestate_Iconpicker_Fontawesome();
			$fontawesome_icons = $fontawesome->get_icons();
			set_transient( $fontawesome_key, $fontawesome_icons, 24 * 7 * HOUR_IN_SECONDS );
		}

		$icon_data = array_merge( $icon_data, $fontawesome_icons );

		return apply_filters( 'opalestate_get_font_data', $icon_data );
	}

	/**
	 * Render field.
	 */
	public function render_iconpicker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$this->setup_admin_scripts();

		$output = sprintf(
			'<select id="%1$s" class="opalestate-iconpicker" name="%2$s">',
			sanitize_key( $field->args['_id'] ),
			esc_attr( $field->args['_id'] )
		);

		foreach ( $this->get_icons() as $icon_item ) {
			$full_icon_class = $icon_item['prefix'] . ' ' . $icon_item['class'];
			$output          .= '<option value="' . $full_icon_class . '" ' . selected( $full_icon_class, $field->escaped_value(), false ) . '>' . esc_html( $icon_item['class'] ) . '</option>';
		}

		$output .= '</select>';
		echo '<p class="description">' . $field->args( 'description' ) . '</p>';
		echo $output;
	}

	/**
	 * Sanitize data.
	 */
	public function sanitize( $override_value, $value, $object_id, $field_args ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function setup_admin_scripts() {
		wp_register_style( 'fontawesome', OPALESTATE_PLUGIN_URL . 'assets/3rd/fontawesome/css/all.min.css', null, '5.11.2', false );

		// Iconpicker.
		wp_register_style( 'fonticonpicker', plugins_url( 'assets/css/jquery.fonticonpicker.min.css', __FILE__ ), [], self::VERSION );
		wp_register_style( 'fonticonpicker-grey-theme', plugins_url( 'assets/themes/grey-theme/jquery.fonticonpicker.grey.min.css', __FILE__ ), [ 'fontawesome' ], self::VERSION );

		wp_enqueue_style( 'fonticonpicker' );
		wp_enqueue_style( 'fonticonpicker-grey-theme' );

		wp_enqueue_script( 'fonticonpicker', plugins_url( 'assets/js/jquery.fonticonpicker.min.js', __FILE__ ), [], '2.0.0' );
		wp_enqueue_script( 'opalestate-fonticonpicker', plugins_url( 'assets/js/script.js', __FILE__ ), [ 'fonticonpicker' ], self::VERSION );
	}
}

new Opalestate_Field_Iconpicker();
