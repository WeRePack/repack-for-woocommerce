<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://WeRePack.org
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
	 * Remove metadata and global option
	 *
	 * By default no metadata is removed to keep functionality and counters on reactivation.
	 * To delete all data related to the plugin, use the filter 'repack_deactivate_remove_all_meta'
	 * in functions.php: add_filter( 'repack_deactivate_remove_all_meta', '__return_true' );
	 *
	 * @since    1.0.6
	 */
	public static function deactivate() {
		if ( apply_filters( 'repack_deactivate_remove_all_meta', false ) ) {
			// Delete Plugin Options
			foreach ( array( 'repack_counter', 'repack_start', 'repack_telemetry_sent', 'repack_telemetry_optin', 'repack_telemetry_no_consent' ) as $option ) {
				delete_option( $option );
			}

			// Delete all order meta
			delete_metadata(
				'post', // since we are deleting data for CPT
				0,
				'_shipping_repack',
				'',
				true
			);

			// Delete user meta
			foreach ( array( 'shipping_repack', 'shipping_repack_counter' ) as $meta ) {
				delete_metadata(
					'user',
					0,
					$meta,
					'',
					true
				);
			}
		}

        // Clear scheduled event
        wp_clear_scheduled_hook( 'repack_telemetry' );
	}
}
