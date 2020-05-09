<?php

/**
 * RePack bootstrap file
 *
 * @link              https://WeRePack.org
 * @since             1.0.0
 * @package           Repack
 *
 * @wordpress-plugin
 * Plugin Name:       RePack - Reuse Packaging for WooCommerce
 * Plugin URI:        https://WeRePack.org/download
 * Description:       Get permission from your customers to reuse already used shipping packaging. As a shop owner it is an easy way to save resources, money and above all to protect the environment.
 * Version:           1.0.3
 * Author:            werepack/WeRePack.org
 * Author URI:        https://WeRePack.org
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
define( 'REPACK_VERSION', '1.0.3' );

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
