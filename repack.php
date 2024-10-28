<?php

/**
 * WeRePack bootstrap file
 *
 * @link              https://WeRePack.org
 * @since             1.0.0
 * @package           Repack
 *
 * @wordpress-plugin
 * Plugin Name:       WeRePack - Reuse Packaging for WooCommerce
 * Plugin URI:        https://WeRePack.org/
 * Description:       Get permission from your customers to reuse already used shipping packaging. As a shop owner it is an easy way to save resources, money and above all to protect the environment.
 * Version:           1.4.6
 * Author:            WeRePack.org
 * Author URI:        https://WeRePack.org
 * Text Domain:       repack-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires Plugins:  		woocommerce
 * WC requires at least: 	3.6
 * WC tested up to: 		9.3
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
 */
define( 'REPACK_VERSION', '1.4.6' );
define( 'REPACK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'REPACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-repack-activator.php
 */
function activate_repack() {
	require_once REPACK_PLUGIN_DIR . 'includes/class-repack-activator.php';
	Repack_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-repack-deactivator.php
 */
function deactivate_repack() {
	require_once REPACK_PLUGIN_DIR . 'includes/class-repack-deactivator.php';
	Repack_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_repack' );
register_deactivation_hook( __FILE__, 'deactivate_repack' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require REPACK_PLUGIN_DIR . 'includes/class-repack.php';

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
