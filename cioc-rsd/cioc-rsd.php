<?php

/**
 * Plugin Name: CIOC Remote Search and Details
 * Description: This plugin provides integration with the CIOC Remote Search and Details API
 * Version: 1.0.0
 * Author: Katherine Lambacher
 * Author URI: http://www.kclsoftware.com
 * License: Apache 2.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/cioc-rsd-activator.php
 */
function activate_cioc_rsd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/cioc-rsd-activator.php';
	CIOC_RSD_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/cioc-rsd-deactivator.php
 */
function deactivate_cioc_rsd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/cioc-rsd-deactivator.php';
	CIOC_RSD_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cioc_rsd' );
register_deactivation_hook( __FILE__, 'deactivate_cioc_rsd' );

/**
 * The core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/cioc-rsd.php';

function run_cioc_rsd() {

	$plugin = new CIOC_RSD(plugin_dir_url( __FILE__ ));
	$plugin->run();

}
run_cioc_rsd();
