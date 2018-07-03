<?php

/**
 * The plugin bootstrap file

 *
 * @link              blubirdinteractive.com
 * @since             1.0.0
 * @package           Bbilgcb
 *
 * @wordpress-plugin
 * Plugin Name:       Google campaign builder
 * Plugin URI:        blubirdinteractive.com
 * Description:       Google campaign builder and url shortner to make short url of your campaign.
 * Version:           1.0.0
 * Author:            BBIL
 * Author URI:        blubirdinteractive.com
 * License:
 * License URI:
 * Text Domain:       bbilgcb
 * Domain Path:
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );
/**
 * This file responsible for defining all the constants and declarations of the
 * core plugin.
 */
require_once plugin_dir_path( __FILE__ ) . 'config.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bbilgcb-activator.php
 */
function activate_bbilgcb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bbilgcb-activator.php';
	Bbilgcb_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bbilgcb-deactivator.php
 */
function deactivate_bbilgcb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bbilgcb-deactivator.php';
	Bbilgcb_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bbilgcb' );
register_deactivation_hook( __FILE__, 'deactivate_bbilgcb' );

/**
 * Common useful function list and definations usefull to run this plugin
 * This action is documented in lib/common_functions.php
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/common_functions.php';

/**
 * All ajax request calls are defined here
 * More details are documented on includes/ajax_functions_call.php
 */
require plugin_dir_path( __FILE__ ) . 'includes/ajax_functions_call.php';

/**
 * Main autolod file for running the google api
 * More details are documented on lib/analytics/vendor/autoload.php
 */
require plugin_dir_path( __FILE__ ) . 'lib/analytics/vendor/autoload.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bbilgcb.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bbilgcb() {

	$plugin = new Bbilgcb();
	$plugin->run();
}
run_bbilgcb();

// Redirect after plugin activation
function bbilgcb_activation_redirect( $plugin ) {
	if( $plugin == plugin_basename( __FILE__ ) ) {
		exit( wp_redirect( admin_url( 'admin.php?page=bbil_gcb' ) ) );
	}
}
add_action( 'activated_plugin', 'bbilgcb_activation_redirect' );