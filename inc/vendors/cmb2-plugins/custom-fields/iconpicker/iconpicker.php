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
	 * Initialize the plugin by hooking into CMB2
	 */
	public function __construct() {
		add_filter( 'cmb2_render_opal_iconpicker', [ $this, 'render_iconpicker' ], 10, 5 );
		add_filter( 'cmb2_sanitize_opal_iconpicker', [ $this, 'sanitize' ], 10, 4 );
	}

	public function get_icons() {
		$fontawesome_key = 'opalestate_fontawesome_data';

		$icon_data = [];
		if ( false === ( $fontawesome_icons = get_transient( $fontawesome_key ) ) ) {
			$fontawesome = new Opalestate_Iconpicker_Fontawesome();
			$fontawesome_icons = $fontawesome->get_icons();
			set_transient( $fontawesome_key, $fontawesome_icons, 24 * 7 * HOUR_IN_SECONDS );
			$icon_data[] = $fontawesome_icons;
		}
		var_dump($icon_data);
		return $icon_data;
	}

	/**
	 * Render field.
	 */
	public function render_iconpicker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$this->setup_admin_scripts();

		$users = $field->value;

var_dump($this->get_icons());

		// $output = sprintf(
		// 	'<select id="%1$s" class="%2$s" name="%3$s">',
		// 	sanitize_key( $this->form->form_id . $args['id'] ),
		// 	esc_attr( $args['class'] ),
		// 	esc_attr( $args['id'] )
		// );
		//
		// foreach ( $this->icon_data as $icon_item ) {
		// 	$full_icon_class = $icon_item['prefix'] . ' ' . $icon_item['class'];
		// 	$output          .= '<option value="' . $full_icon_class . '" ' . selected( $full_icon_class, $value, false ) . '>' . esc_html( $icon_item['class'] ) . '</option>';
		// }
		//
		// $output .= '</select>';
		//
		// echo $output;
	}

	/**
	 * Sanitize data.
	 */
	public function sanitize( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function setup_admin_scripts() {
		// Iconpicker.
		wp_register_style( 'fonticonpicker', plugins_url( 'assets/css/jquery.fonticonpicker.min.css', __FILE__ ), [], self::VERSION );
		wp_register_style( 'fonticonpicker-grey-theme', plugins_url( 'assets/themes/grey-theme/jquery.fonticonpicker.grey.min.css', __FILE__ ), [], self::VERSION );

		wp_enqueue_style( 'fonticonpicker' );
		wp_enqueue_style( 'fonticonpicker-grey-theme' );
	}
}

new Opalestate_Field_Iconpicker();
