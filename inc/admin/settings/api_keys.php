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

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Settings_Api_keys_Tab extends Opalestate_Settings_Base_Tab {

    public function get_tabnav() {

    }

    public function get_tab_content($key = '') {
        return [
            'id'               => 'api_keys',
            'opalestate_title' => esc_html__('API', 'opalestate-pro'),
            'show_on'          => ['key' => 'options-page', 'value' => [$key],],
            'show_names'       => false, // Hide field names on the left
            'fields'           => apply_filters('opalestate_settings_api', [
                    [
                        'id'   => 'api_keys',
                        'name' => esc_html__('API', 'opalestate-pro'),
                        'type' => 'api_keys',
                    ],
                ]
            ),
        ];
    }

    public function get_tab_fields() {

    }

}