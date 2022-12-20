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

class Opalestate_Settings_Email_Tab extends Opalestate_Settings_Base_Tab {

    public function get_tabnav() {

    }

    public function get_tab_content($key = '') {
        return [
            'id'               => 'options_page',
            'opalestate_title' => esc_html__('General Settings', 'opalestate-pro'),
            'show_on'          => ['key' => 'options-page', 'value' => [$key],],
            'fields'           => $this->get_tab_fields()
        ];
    }

    public function get_tab_fields() {

    }

}