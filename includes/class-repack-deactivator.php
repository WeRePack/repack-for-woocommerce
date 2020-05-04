<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://ouun.io
 * @since      1.0.0
 *
 * @package    Repack
 * @subpackage Repack/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Repack
 * @subpackage Repack/includes
 * @author     Philipp Wellmer <philipp@ouun.io>
 */
class Repack_Deactivator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Delete option
		delete_option( 'repack_counter' );
	}
}
