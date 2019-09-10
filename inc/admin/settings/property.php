<?php
/**
 * Opalestate_Settings_Property_Tab
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Opalestate_Settings_Property_Tab extends Opalestate_Settings_Base_Tab {
	public function get_subtabs() {
		return apply_filters(
			'opalestate_settings_property_subtabs_nav',
			[
				'property_general' => esc_html__( 'General', 'opalestate-pro' ),
				'property_search'  => esc_html__( 'Search Page', 'opalestate-pro' ),
				'property_detail'  => esc_html__( 'Single Page', 'opalestate-pro' ),
			]
		);
	}

	public function get_subtabs_content( $key = "" ) {
		$fields = apply_filters( 'opalestate_settings_property_subtabs_' . $key . '_fields', [] );
		if ( $fields ) {

		} else {
			switch ( $key ) {
				case 'property_search':
					$fields = $this->get_subtab_search_fields();
					break;

				case 'property_detail':
					$fields = $this->get_subtab_detail_fields();
					break;

				default:
					$fields = $this->get_subtab_property_fields();
					break;
			}
		}

		return [
			'id'               => 'options_page',
			'opalestate_title' => esc_html__( 'Property Settings', 'opalestate-pro' ),
			'show_on'          => [ 'key' => 'options-page', 'value' => [ $key ], ],
			'fields'           => $fields,
		];
	}

	private function get_subtab_property_fields() {
		$fields = [];

		$fields[] = [
			'name'    => esc_html__( 'Enable User Submission', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Enable to allow user post/submit properties in front-end', 'opalestate-pro' ),
			'id'      => 'enable_submission',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		// show setting short meta infox
		$metabox = new Opalestate_Property_MetaBox();
		$metas   = $metabox->metaboxes_info_fields();

		$checkes = [];

		foreach ( $metas as $key => $field ) {
			$id              = str_replace( OPALESTATE_PROPERTY_PREFIX, '', $field['id'] );
			$checkes [ $id ] = $field['name'];
		}

		$fields[] = [
			'name'    => esc_html__( 'Show Meta Information in Grid and Single Page', 'opalestate-pro' ),
			'id'      => 'show_property_meta',
			'type'    => 'multicheck',
			'options' => $checkes,
		];

		$fields[] = [
			'name'    => esc_html__( 'Archive Grid layout', 'opalestate-pro' ),
			'id'      => 'property_archive_grid_layout',
			'type'    => 'select',
			'options' => opalestate_get_loop_property_grid_layouts(),
		];

		$fields[] = [
			'name'    => esc_html__( 'Archive List layout', 'opalestate-pro' ),
			'id'      => 'property_archive_list_layout',
			'type'    => 'select',
			'options' => opalestate_get_loop_property_list_layouts(),
		];

		return $fields;
	}

	private function get_subtab_search_fields() {
		$pages = opalestate_cmb2_get_post_options( [
			'post_type'   => 'page',
			'numberposts' => -1,
		] );

		$metabox = new Opalestate_Property_MetaBox();
		$metas   = $metabox->metaboxes_info_fields();

		$fields = [];

		if ( $metas ) {
			$fields[] = [
				'name'       => esc_html__( 'User Share Search', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Display Share Search Link Management', 'opalestate-pro' ),
				'id'         => 'enable_share_earch',
				'type'       => 'switch',
				'options'    => [
					'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
					'off' => esc_html__( 'Disable', 'opalestate-pro' ),
				],
			];

			$fields[] = [
				'name'    => esc_html__( 'User Saved Search', 'opalestate-pro' ),
				'desc'    => esc_html__( 'Display Save Search Link Management', 'opalestate-pro' ),
				'id'      => 'enable_saved_usersearch',
				'type'    => 'switch',
				'options' => [
					'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
					'off' => esc_html__( 'Disable', 'opalestate-pro' ),
				],
			];


			$fields[] = [
				'name'    => esc_html__( 'Search Properties Page', 'opalestate-pro' ),
				'desc'    => esc_html__( 'This is page to display result of properties after user searching via form.',
					'opalestate-pro' ),
				'id'      => 'search_map_properties_page',
				'type'    => 'select',
				'options' => opalestate_cmb2_get_post_options( [
					'post_type'   => 'page',
					'numberposts' => -1,
				] ),
				'default' => '',
			];

			$fields[] = [
				'name'       => esc_html__( 'Properties Per Page', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Enter min of properties display in search page', 'opalestate-pro' ),
				'id'         => 'search_property_per_page',
				'type'       => 'text_small',
				'attributes' => [
					'type' => 'number',
				],
				'default'    => 9,
			];


			$fields[] = [
				'name'    => esc_html__( 'Show Featured First', 'opalestate-pro' ),
				'id'      => 'show_featured_first',
				'desc'    => esc_html__( 'Show featured first in page result, as default Newest is showed', 'opalestate-pro' ),
				'type'    => 'switch',
				'options' => [
					0 => esc_html__( 'Disable', 'opalestate-pro' ),
					1 => esc_html__( 'Enable', 'opalestate-pro' ),
				],
				'default' => 0,
			];
			$fields[] = [
				'name'       => esc_html__( 'Minimum of Search Price', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Enter minimum of price for starting search', 'opalestate-pro' ),
				'id'         => 'search_min_price',
				'type'       => 'text_medium',
				'attributes' => [
					'type' => 'number',
				],
				'default'    => 0,
			];
			$fields[] = [
				'name'       => esc_html__( 'Maximum of Search Price', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Enter maximum of price for starting search', 'opalestate-pro' ),
				'id'         => 'search_max_price',
				'type'       => 'text_medium',
				'attributes' => [
					'type' => 'number',
				],
				'default'    => 10000000,
			];


			$fields[] = [
				'name'       => esc_html__( 'Minimum of Search Aea', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Enter minimum of area for starting search', 'opalestate-pro' ),
				'id'         => 'search_min_area',
				'type'       => 'text_small',
				'attributes' => [
					'type' => 'number',
				],
				'default'    => 0,
			];
			$fields[] = [
				'name'       => esc_html__( 'Maximum of Search Aea', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Enter maximum of area for starting search', 'opalestate-pro' ),
				'id'         => 'search_max_area',
				'type'       => 'text_small',
				'attributes' => [
					'type' => 'number',
				],
				'default'    => 1000,
			];

			$fields[] = [
				'name'    => esc_html__( 'Search Grid layout', 'opalestate-pro' ),
				'id'      => 'property_search_grid_layout',
				'type'    => 'select',
				'options' => opalestate_get_loop_property_grid_layouts(),
			];

			$fields[] = [
				'name'    => esc_html__( 'Search List layout', 'opalestate-pro' ),
				'id'      => 'property_search_list_layout',
				'type'    => 'select',
				'options' => opalestate_get_loop_property_list_layouts(),
			];

			$fields[] = [
				'name'       => esc_html__( 'Horizontal Search Fields', 'opalestate-pro' ),
				'desc'       => esc_html__( 'Disable or enable fields appearing in search form', 'opalestate-pro' ),
				'type'       => 'opalestate_title',
				'id'         => 'opalestate_title_general_settings_1',
				'before_row' => '<hr>',
				'after_row'  => '<hr>',
			];

			$fields[] = [
				'name'    => esc_html__( 'Show Price', 'opalestate-pro' ),
				'id'      => OPALESTATE_PROPERTY_PREFIX . 'price_opt',
				'type'    => 'switch',
				'options' => [
					0 => esc_html__( 'Disable', 'opalestate-pro' ),
					1 => esc_html__( 'Enable', 'opalestate-pro' ),
				],
			];

			foreach ( $metas as $key => $meta ) {
				$fields[] = [
					'name'    => $meta['name'],
					'id'      => $meta['id'] . '_opt',
					'type'    => 'switch',
					'options' => [
						0 => esc_html__( 'Disable', 'opalestate-pro' ),
						1 => esc_html__( 'Enable', 'opalestate-pro' ),
					],
				];
			}

			$fields[] = [
				'name'       => esc_html__( 'Vertical Search Fields', 'opalestate-pro' ),
				'type'       => 'opalestate_title',
				'id'         => 'opalestate_title_general_settings_2',
				'before_row' => '<hr>',
				'after_row'  => '<hr>',
			];

			$fields[] = [
				'name'    => esc_html__( 'Show Price', 'opalestate-pro' ),
				'id'      => OPALESTATE_PROPERTY_PREFIX . 'price_opt_v',
				'type'    => 'switch',
				'options' => [
					0 => esc_html__( 'Disable', 'opalestate-pro' ),
					1 => esc_html__( 'Enable', 'opalestate-pro' ),
				],
			];

			foreach ( $metas as $key => $meta ) {
				$fields[] = [
					'name'    => $meta['name'],
					'id'      => $meta['id'] . '_opt_v',
					'type'    => 'switch',
					'options' => [
						0 => esc_html__( 'Disable', 'opalestate-pro' ),
						1 => esc_html__( 'Enable', 'opalestate-pro' ),
					],

				];
			}
		}

		return $fields;
	}

	/**
	 *
	 */
	private function get_subtab_detail_fields() {
		$fields = [];

		$fields[] = [
			'name'    => esc_html__( 'Show Amenities tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Amenities tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_amenities',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Facilities tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Facilities tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_facilities',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Attachments tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Attachments tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_attachments',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Video tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Video tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_video',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Map tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Map tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_map',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Nearby tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Nearby tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_nearby',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Walk Scores tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Walk Scores tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_walkscores',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Apartments tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Apartments tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_apartments',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Floor Plans tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Floor Plans tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_floor_plans',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'    => esc_html__( 'Show Views Statistics tab', 'opalestate-pro' ),
			'desc'    => esc_html__( 'Show Views Statistics tab in the single property page.', 'opalestate-pro' ),
			'id'      => 'enable_single_views_statistics',
			'type'    => 'switch',
			'options' => [
				'on'  => esc_html__( 'Enable', 'opalestate-pro' ),
				'off' => esc_html__( 'Disable', 'opalestate-pro' ),
			],
		];

		$fields[] = [
			'name'       => esc_html__( 'Views Statistics time limit (days)', 'opalestate-pro' ),
			'desc'       => esc_html__( 'The number of days will be saved to the database.', 'opalestate-pro' ),
			'id'         => 'single_views_statistics_limit',
			'type'       => 'text_small',
			'attributes' => [
				'type' => 'number',
				'min'  => 1,
				'max'  => 365,
			],
			'default'    => 8,
		];

		$fields[] = [
			'name'       => esc_html__( 'Related properties layout', 'opalestate-pro' ),
			'desc'       => esc_html__( 'Select a layout for related properties.', 'opalestate-pro' ),
			'id'         => 'single_related_properties_layout',
			'type'       => 'select',
			'options'    => opalestate_get_loop_property_layouts(),
		];

		$fields[] = [
			'name'       => esc_html__( 'Nearby properties layout', 'opalestate-pro' ),
			'desc'       => esc_html__( 'Select a layout for nearby properties.', 'opalestate-pro' ),
			'id'         => 'single_nearby_properties_layout',
			'type'       => 'select',
			'options'    => opalestate_get_loop_property_layouts(),
		];

		return $fields;
	}
}
