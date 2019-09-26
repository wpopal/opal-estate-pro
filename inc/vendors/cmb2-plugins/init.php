<?php
/**
 * CMB2 checker.
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'OPALESTATE_CMB2_DIR', OPALESTATE_PLUGIN_DIR . 'inc/vendors/cmb2-plugins/' );

function opalestate_setup_cmb2_url() {
	return OPALESTATE_CMB2_DIR . 'cmb2';
}

if ( file_exists( WP_PLUGIN_DIR . '/cmb2/init.php' ) ) {
	require_once WP_PLUGIN_DIR . '/cmb2/init.php';
} elseif ( file_exists( OPALESTATE_CMB2_DIR . 'cmb2/init.php' ) ) {
	require_once OPALESTATE_CMB2_DIR . 'cmb2/init.php';
	//Customize CMB2 URL
	// add_filter( 'cmb2_meta_box_url', 'opalestate_setup_cmb2_url' );
}

function opalestate_load_cmb2_files() {
	if ( file_exists( OPALESTATE_CMB2_DIR . 'custom-fields/map/map.php' ) ) {
		require_once OPALESTATE_CMB2_DIR . 'custom-fields/map/map.php';
	}

	if ( file_exists( OPALESTATE_CMB2_DIR . 'custom-fields/user/user.php' ) ) {
		require_once OPALESTATE_CMB2_DIR . 'custom-fields/user/user.php';
	}

	if ( file_exists( OPALESTATE_CMB2_DIR . 'custom-fields/iconpicker/iconpicker.php' ) ) {
		require_once OPALESTATE_CMB2_DIR . 'custom-fields/iconpicker/providers/fontawesome.php';
		require_once OPALESTATE_CMB2_DIR . 'custom-fields/iconpicker/iconpicker.php';
	}

	require_once OPALESTATE_CMB2_DIR . 'cmb2-tabs/plugin.php';
	require_once OPALESTATE_CMB2_DIR . 'CMB2-Switch-Button/cmb2-switch-button.php';
	require_once OPALESTATE_CMB2_DIR . 'uploader/uploader.php';
}

add_action( 'init', 'opalestate_load_cmb2_files', 1 );
