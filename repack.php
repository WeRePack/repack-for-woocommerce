<?php

/**
 * RePack bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ouun.io
 * @since             1.0.0
 * @package           Repack
 *
 * @wordpress-plugin
 * Plugin Name:       RePack - Reuse Packaging for WooCommerce
 * Plugin URI:        https://ouun.io/plugins/repack
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Philipp Wellmer/ouun.io
 * Author URI:        https://ouun.io
 * Text Domain:       repack
 * Domain Path:       /languages
 *
 * WC requires at least: 3.6
 * WC tested up to: 4.1
 *
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
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
define( 'REPACK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-repack-activator.php
 */
function activate_repack() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-repack-activator.php';
	Repack_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-repack-deactivator.php
 */
function deactivate_repack() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-repack-deactivator.php';
	Repack_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_repack' );
register_deactivation_hook( __FILE__, 'deactivate_repack' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-repack.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_repack() {
	$plugin = new Repack();
	$plugin->run();
}

run_repack();
