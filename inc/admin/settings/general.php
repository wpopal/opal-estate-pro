<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Opalestate_Settings_General_Tab extends Opalestate_Settings_Base_Tab {

	public function get_tabnav() {

	}

	public function get_tab_content( $key = '' ) {
		return [
			'id'               => 'options_page',
			'opalestate_title' => esc_html__( 'General Settings', 'opalestate-pro' ),
			'show_on'          => [ 'key' => 'options-page', 'value' => [ $key, ], ],
			'fields'           => $this->get_tab_fields(),
		];
	}

	public function get_tab_fields( $key = '' ) {
		return apply_filters( 'opalestate_settings_general', [
				[
					'name' => esc_html__( 'General Settings', 'opalestate-pro' ),

					'type'       => 'opalestate_title',
					'id'         => 'opalestate_title_general_settings_1',
					'before_row' => '<hr>',
					'after_row'  => '<hr>',
				],
				[
					'name'    => esc_html__( 'Login and Redirect to Dashboard page', 'opalestate-pro' ),
					'desc'    => esc_html__( 'Redirect to User Management Dashboard page after login.', 'opalestate-pro' ),
					'id'      => 'enable_login_redirect_to_dashboard',
					'type'    => 'switch',
					'options' => [
						'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
						'off' => esc_html__( 'Disable', 'opalestate-pro' ),
					],
				],
				[
					'name'    => esc_html__( 'Register and Redirect to Dashboard page', 'opalestate-pro' ),
					'desc'    => esc_html__( 'Redirect to User Management Dashboard page after register.', 'opalestate-pro' ),
					'id'      => 'enable_register_redirect_to_dashboard',
					'type'    => 'switch',
					'options' => [
						'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
						'off' => esc_html__( 'Disable', 'opalestate-pro' ),
					],
				],
				[
					'name' => esc_html__( 'Maximum Upload Image Size', 'opalestate-pro' ),
					'desc' => esc_html__( 'Set maximum volumn size having < x MB', 'opalestate-pro' ),

					'id'      => 'upload_image_max_size',
					'type'    => 'text',
					'default' => '0.5',
				],
				[
					'name' => esc_html__( 'Maximum Upload Image Files', 'opalestate-pro' ),
					'desc' => esc_html__( 'Set maximum volumn size having < x MB', 'opalestate-pro' ),

					'id'      => 'upload_image_max_files',
					'type'    => 'text',
					'default' => '10',
				],
				[
					'name' => esc_html__( 'Maximum Upload Other Size', 'opalestate-pro' ),
					'desc' => esc_html__( 'Set maximum volumn size having < x MB for upload docx, pdf...', 'opalestate-pro' ),

					'id'      => 'upload_other_max_size',
					'type'    => 'text',
					'default' => '0.8',
				],
				[
					'name' => esc_html__( 'Maximum Upload Other Files', 'opalestate-pro' ),
					'desc' => esc_html__( 'Set maximum volumn size having < x MB for upload docx, pdf...', 'opalestate-pro' ),

					'id'        => 'upload_other_max_files',
					'type'      => 'text',
					'default'   => '10',
					'after_row' => '<hr>',
				],
				[
					'name' => esc_html__( 'Agent Image Size', 'opalestate-pro' ),
					'desc' => esc_html__( 'The Loop Image is an Agent that is chosen as the representative Agent in grid and list.', 'opalestate-pro' ),

					'id'      => 'agent_image_size',
					'type'    => 'select',
					'default' => 'medium',
					'options' => opalestate_get_featured_image_sizes(),

				],

				[
					'name' => esc_html__( 'Agent Image Size', 'opalestate-pro' ),
					'desc' => esc_html__( 'The Loop Image is an Agent that is chosen as the representative Agent in grid and list.', 'opalestate-pro' ),

					'id'      => 'agent_image_size',
					'type'    => 'select',
					'default' => 'medium',
					'options' => opalestate_get_featured_image_sizes(),

				],


				[
					'name' => esc_html__( 'Loop Image Size', 'opalestate-pro' ),
					'desc' => esc_html__( 'The Loop Image is an image that is chosen as the representative image in grid and list.', 'opalestate-pro' ),

					'id'      => 'loop_image_size',
					'type'    => 'select',
					'default' => 'large',
					'options' => opalestate_get_featured_image_sizes(),
				],


				[
					'name'      => esc_html__( 'Featured Image Size', 'opalestate-pro' ),
					'desc'      => esc_html__( 'The Featured Image is an image that is chosen as the representative image in single page. .', 'opalestate-pro' ),
					'id'        => 'featured_image_size',
					'type'      => 'select',
					'default'   => 'full',
					'options'   => opalestate_get_featured_image_sizes(),
					'after_row' => '<em>' . __( 'To generate images with new image sizes, you can use this <a href="https://goo.gl/FuXFex" target="_blank">Force Regenerate Thumbnails</a>',
							'opalestate-pro' ) . '</em>',
				],
				[
					'name'       => esc_html__( 'Minimum of Target Price For Agent', 'opalestate-pro' ),
					'desc'       => esc_html__( 'Enter minimum  of price for starting search agent by target', 'opalestate-pro' ),
					'id'         => 'search_agent_min_price',
					'type'       => 'text_medium',
					'attributes' => [
						'type' => 'number',
					],
					'default'    => 0,
				],
				[
					'name'       => esc_html__( 'Maximum of Target Price For Agent', 'opalestate-pro' ),
					'desc'       => esc_html__( 'Enter maximum of price for starting search agent by target', 'opalestate-pro' ),
					'id'         => 'search_agent_max_price',
					'type'       => 'text_medium',
					'attributes' => [
						'type' => 'number',
					],
					'default'    => 1000000,
				],
				[
					'name'    => esc_html__( 'Single Layout Page', 'opalestate-pro' ),
					'desc'    => esc_html__( 'Choose layout for single property.', 'opalestate-pro' ),
					'id'      => 'layout',
					'type'    => 'select',
					'options' => apply_filters( 'opalestate_single_layout_templates', [ '' => esc_html__( 'Inherit', 'opalestate-pro' ) ] ),
				],


				[
					'name'       => esc_html__( 'Currency Settings', 'opalestate-pro' ),
					'desc'       => '',
					'type'       => 'opalestate_title',
					'id'         => 'opalestate_title_general_settings_2',
					'before_row' => '<hr>',
					'after_row'  => '<hr>',
				],
				[
					'name'    => esc_html__( 'Currency', 'opalestate-pro' ),
					'desc'    => 'Choose your currency. Note that some payment gateways have currency restrictions.',
					'id'      => 'currency',
					'type'    => 'select',
					'options' => opalestate_get_currencies(),
					'default' => 'USD',
				],
				[
					'name'    => esc_html__( 'Currency Position', 'opalestate-pro' ),
					'desc'    => 'Choose the position of the currency sign.',
					'id'      => 'currency_position',
					'type'    => 'select',
					'options' => [
						'before' => esc_html__( 'Before - $10', 'opalestate-pro' ),
						'after'  => esc_html__( 'After - 10$', 'opalestate-pro' ),
					],
					'default' => 'before',
				],
				[
					'name'    => esc_html__( 'Thousands Separator', 'opalestate-pro' ),
					'desc'    => esc_html__( 'The symbol (typically , or .) to separate thousands', 'opalestate-pro' ),
					'id'      => 'thousands_separator',
					'type'    => 'text_small',
					'default' => ',',
				],
				[
					'name'    => esc_html__( 'Decimal Separator', 'opalestate-pro' ),
					'desc'    => esc_html__( 'The symbol (usually , or .) to separate decimal points', 'opalestate-pro' ),
					'id'      => 'decimal_separator',
					'type'    => 'text_small',
					'default' => '.',
				],
				[
					'name'       => esc_html__( 'Number of Decimals', 'opalestate-pro' ),
					'desc'       => esc_html__( 'This sets the number of decimal points shown in displayed prices.', 'opalestate-pro' ),
					'id'         => 'number_decimals',
					'type'       => 'text_small',
					'attributes' => [
						'type' => 'number',
					],
					'default'    => 2,
				],
				[
					'name'    => esc_html__( 'Measurement Unit', 'opalestate-pro' ),
					'desc'    => esc_html__( 'Select a measurement unit.', 'opalestate-pro' ),
					'id'      => 'measurement_unit',
					'type'    => 'select',
					'options' => opalestate_get_measurement_units(),
					'default' => 'sqft',
				],
				[
					'name'    => esc_html__( 'Time Format', 'opalestate-pro' ),
					'desc'    => esc_html__( 'Select a time format.', 'opalestate-pro' ),
					'id'      => 'time_format',
					'type'    => 'select',
					'options' => opalestate_get_time_formats(),
					'default' => '12_hour',
				],
			]
		);
	}
}
