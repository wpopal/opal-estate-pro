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
	 * @var array
	 */
	protected static $icon_data = [];

	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	public static function init() {
		$icons           = new Fontawesome();
		static::$icon_data[] = $icons->get_icons();

		add_filter( 'cmb2_render_opal_iconpicker', [ __CLASS__, 'render_iconpicker' ], 10, 5 );
		add_filter( 'cmb2_sanitize_opal_iconpicker', [ __CLASS__, 'sanitize' ], 10, 4 );
	}

	/**
	 * Render field.
	 */
	public static function render_iconpicker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		self::setup_admin_scripts();

		$users = $field->value;

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
	public static function sanitize( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public static function setup_admin_scripts() {
		// Iconpicker.
		wp_register_style( 'fonticonpicker', plugins_url( 'assets/css/jquery.fonticonpicker.min.css', __FILE__ ), [], self::VERSION );
		wp_register_style( 'fonticonpicker-grey-theme', plugins_url( 'assets/themes/grey-theme/jquery.fonticonpicker.grey.min.css', __FILE__ ), [], self::VERSION );

		wp_enqueue_style( 'fonticonpicker' );
		wp_enqueue_style( 'fonticonpicker-grey-theme' );
	}
}

Opalestate_Field_Iconpicker::init();
