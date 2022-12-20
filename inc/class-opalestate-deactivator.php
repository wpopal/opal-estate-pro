<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 **/
class Opalestate_Deactivator {

    /**
     * Deactivate
     */
    public static function deactivate() {
        $timestamp = wp_next_scheduled('opalestate_clean_update');
        wp_unschedule_event($timestamp, 'opalestate_clean_update');
    }
}
