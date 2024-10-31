<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              one2tek.com
 * @since             1.0.0
 * @package           Mybox_B2b
 *
 * @wordpress-plugin
 * Plugin Name:       Mybox B2B
 * Plugin URI:        https://www.mybox.com.pa/mybox-b2b
 * Description:       This plugin connects to Mybox API automatically trigger shipment request when sold with woocommerce.
 * Version:           3.1.3
 * Author:            One2tek
 * Author URI:        one2tek.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mybox-b2b
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MYBOX_B2B_VERSION', '2.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mybox-b2b-activator.php
 */
function activate_mybox_b2b() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mybox-b2b-activator.php';
	Mybox_B2b_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mybox-b2b-deactivator.php
 */
function deactivate_mybox_b2b() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mybox-b2b-deactivator.php';
	Mybox_B2b_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mybox_b2b' );
register_deactivation_hook( __FILE__, 'deactivate_mybox_b2b' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mybox-b2b.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mybox_b2b() {

	$plugin = new Mybox_B2b();
	$plugin->run();

}
run_mybox_b2b();
