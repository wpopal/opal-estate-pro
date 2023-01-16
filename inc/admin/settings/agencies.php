<?php
/**
 * Opalestate_Settings_Agencies_Tab
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Settings_Agencies_Tab extends Opalestate_Settings_Base_Tab {
    public function get_subtabs() {
        return apply_filters(
            'opalestate_settings_agencies_subtabs_nav',
            [
                'archive' => esc_html__('Archive', 'opalestate-pro'),
                'single'  => esc_html__('Single', 'opalestate-pro'),
            ]
        );
    }

    public function get_subtabs_content($key = "") {
        $fields = apply_filters('opalestate_settings_agencies_subtabs_' . $key . '_fields', []);
        if (empty($fields)) {
            switch ($key) {
                case 'single':
                    $fields = $this->get_subtab_agencies_single_fields();
                    break;
                default:
                    $fields = $this->get_subtab_agencies_archive_fields();
                    break;
            }
        }

        return [
            'id'               => 'options_page',
            'opalestate_title' => esc_html__('Agencies Settings', 'opalestate-pro'),
            'show_on'          => ['key' => 'options-page', 'value' => [$key],],
            'fields'           => $fields,
        ];
    }

    private function get_subtab_agencies_single_fields() {
        $fields   = [];
        $fields[] = [
            'name'    => esc_html__('Layout', 'opalestate-pro'),
            'id'      => 'agencies_single_layout',
            'type'    => 'select',
            'options' => apply_filters('opalestate_agencies_single_layout', [
                '' => esc_html__('default', 'opalestate-pro'),
            ]),
        ];
        return $fields;
    }

    private function get_subtab_agencies_archive_fields() {
        $fields = [];

        $fields[] = [
            'name'    => esc_html__('Default Display mode', 'opalestate-pro'),
            'id'      => 'agencies_archive_displaymode',
            'type'    => 'select',
            'options' => opalestate_display_modes(),
        ];

        $fields[] = [
            'name'    => esc_html__('Archive Grid layout', 'opalestate-pro'),
            'id'      => 'agencies_archive_grid_layout',
            'type'    => 'select',
            'options' => opalestate_get_loop_agencies_grid_layouts(),
        ];

        $fields[] = [
            'name'    => esc_html__('Archive List layout', 'opalestate-pro'),
            'id'      => 'agencies_archive_list_layout',
            'type'    => 'select',
            'options' => opalestate_get_loop_agencies_list_layouts(),
        ];
        return $fields;
    }
}
