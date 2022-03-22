<?php

/**
 * Fired during plugin activation
 *
 * @link       https://WeRePack.org
 * @since      1.0.0
 *
 * @package    Repack
 * @subpackage Repack/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Repack
 * @subpackage Repack/includes
 * @author     Philipp Wellmer <philipp@ouun.io>
 */
class Repack_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Add RePack counter option
		add_option( 'repack_counter' );
		// Add the start time
		add_option( 'repack_start', current_datetime()->getTimestamp() );
		// Schedule recurring event
		Repack_Telemetry::activate_telemetry();
	}
}
