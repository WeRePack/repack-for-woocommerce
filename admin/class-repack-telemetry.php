<?php

/**
 * The telemetry-specific functionality of the plugin:
 * We respect your privacy, you decide if and which data you share with us.
 * We use the collected data to measure the success of the WeRePack initiative & to improve the code.
 * Websites which opt-in for our WeRePack Directory are listed and linked as supporter shops.
 *
 * @link       https://WeRePack.org
 * @since      1.1.0
 *
 * @package    Repack
 * @subpackage Repack/admin
 */

/**
 * The telemetry-specific functionality of the plugin.
 * This is based on the Telemetry solution of
 *
 * @package    Repack
 * @subpackage Repack/admin
 * @author     Philipp Wellmer <philipp@ouun.io>
 */
class Repack_Telemetry {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.1.0
	 */
	public function __construct() {
		// Early exit if telemetry is disabled.
		if ( ! apply_filters( 'repack_telemetry', true ) ) {
			return;
		}

		add_action( 'repack_field_init', array( $this, 'field_init' ), 10, 2 );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	/**
	 * Additional actions that run on init.
	 *
	 * @access public
	 * @since 1.1.0
	 * @return void
	 */
	public function init() {
		$this->dismiss_notice();
		$this->consent();

		// Scheduled event to trigger telemetry
        add_action( 'repack_telemetry', array($this, 'maybe_send_data') );
	}

	/**
	 * Maybe send data.
	 *
	 * @access public
	 * @since 1.1.0
	 * @return void
	 */
	public function maybe_send_data() {
		// Check if the user has consented to the data sending.
		if ( ! get_option( 'repack_telemetry_optin' ) ) {
			return;
		}

		// Send data & update sent value
        if(!is_wp_error($this->send_data())) {
            update_option( 'repack_telemetry_sent', time() );
        }
	}

    /**
     * Sends data.
     *
     * @access private
     * @since 1.1.0
     * @return array|WP_Error
     */
	private function send_data() {
		// Ping remote server.
		return wp_remote_post(
			'https://werepack.org/?action=repack-stats',
			array(
				'method'   => 'POST',
				'blocking' => false,
				'body'     => array_merge(
					array(
						'action' => 'repack-stats',
					),
					$this->get_data([
                        'repackLastSent' => time()
                    ])
				),
			)
		);
	}

	/**
	 * The admin-notice.
	 *
	 * @access private
	 * @since 1.1.0
	 * @return void
	 */
	public function admin_notice() {

		// Early exit if the user has dismissed the consent, or if they have opted-in.
		if ( get_option( 'repack_telemetry_no_consent' ) || get_option( 'repack_telemetry_optin' ) ) {
			return;
		}
		$data = $this->get_data();
		?>
		<div class="notice notice-info repack-telemetry">
			<h3><strong><?php esc_html_e( 'Help us reducing packaging waste. Join the WeRePack initiative.', 'repack-for-woocommerce' ); ?></strong></h3>
			<p style="max-width: 76em;">
				<?php _e( 'We want to win you as a supporter and measure our joint success. To do this, you can share certain data with us in order to be listed in the supporter directory on WeRePack.org. This way, we can measure our positive impact on e-commerce and give you a platform that recognises your commitment to the environment. <br><strong>No sensitive user data is transferred.</strong>', 'repack-for-woocommerce' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</p>
			<table class="data-to-send hidden">
				<thead>
				<tr>
					<th colspan="2"><?php esc_html_e( 'Data that will be sent', 'repack-for-woocommerce' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td style="min-width: 200px;"><?php esc_html_e( 'Website URL', 'repack-for-woocommerce' ); ?></td>
					<td><code><?php echo esc_html( $data['siteURL'] ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Website Language', 'repack-for-woocommerce' ); ?></td>
					<td><code><?php echo esc_html( $data['siteLang'] ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'WeRePack Start', 'repack-for-woocommerce' ); ?></td>
					<td><code><?php echo esc_html( wp_date( get_option( 'date_format' ), $data['repackStart'] ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'WeRePack Consents', 'repack-for-woocommerce' ); ?></td>
					<td><code><?php echo esc_html( ! empty( $data['repackCounter'] ) ? $data['repackCounter'] : __( 'None yet', 'repack-for-woocommerce' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'WeRePack Consent Ratio', 'repack-for-woocommerce' ); ?></td>
					<td><code><?php echo esc_html( $data['repackRatio'] . '%' ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'WeRePack Coupon', 'repack-for-woocommerce' ); ?></td>
					<td><code><?php echo esc_html( $data['repackCoupon'] ? __( 'Available', 'repack-for-woocommerce' ) : __( 'Not available', 'repack-for-woocommerce' ) ); ?></code></td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<th colspan="2">
						<?php
						printf(
						/* translators: %1$s: URL to the server plugin code. %2$s: URL to the stats page. */
							__( 'We believe in complete transparency. You can see the code used on our server <a href="%1$s" target="_blank" rel="nofollow">here</a>, and the results of the statistics we\'re gathering on <a href="%2$s" target="_blank" rel="nofollow">this page</a>.', 'repack-for-woocommerce' ), // phpcs:ignore WordPress.Security.EscapeOutput
							'https://github.com/ouun/repack-telemetry-server/',
							'https://werepack.org/repack-telemetry-statistics/'
						);
						?>
					</th>
				</tr>
				</tfoot>
			</table>
			<p class="actions">

				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-consent-notice', 'telemetry' ) ) ); ?>" class="button button-primary consent"><?php esc_html_e( 'I agree', 'repack-for-woocommerce' ); ?></a>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-hide-notice', 'telemetry' ) ) ); ?>" class="button button-secondary dismiss"><?php esc_html_e( 'No thanks', 'repack-for-woocommerce' ); ?></a>
				<a class="button button-link details details-show"><?php esc_html_e( 'Show me the data', 'repack-for-woocommerce' ); ?></a>
				<a class="button button-link details details-hide hidden"><?php esc_html_e( 'Collapse data', 'repack-for-woocommerce' ); ?></a>
			</p>
			<script>
				jQuery( '.repack-telemetry a.details' ).on( 'click', function() {
					jQuery( '.repack-telemetry .data-to-send' ).toggleClass( 'hidden' );
					jQuery( '.repack-telemetry a.details-show' ).toggleClass( 'hidden' );
					jQuery( '.repack-telemetry a.details-hide' ).toggleClass( 'hidden' );
				});
			</script>
		</div>
		<?php

		$this->table_styles();
	}

    /**
     * Builds and returns the data or uses cached if data already exists.
     *
     * @access private
     * @param array $data
     * @return array
     * @since 1.1.0
     */
	private function get_data( $data = [] ) {
		// Build data and return the array.
		return wp_parse_args($data, array(
			'siteURL'        => home_url( '/' ),
			'siteLang'       => get_locale(),
			'repackStart'    => get_option( 'repack_start' ),
			'repackCounter'  => get_option( 'repack_counter' ),
			'repackRatio'    => $this->get_repack_ratio( get_option( 'repack_counter' ) ),
			'repackCoupon'   => Repack_Public::repack_coupon_exists(),
			'repackLastSent' => get_option( 'repack_telemetry_sent' ),
		));
	}

	/**
	 * Calculate the ratio: orders / consents
	 *
	 * @param $consents
	 * @return float|string
	 */
	private function get_repack_ratio( $consents ) {
		// Orders since starting WeRePack support
		$orders = new WP_Query(
			array(
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post_type'      => 'shop_order',
				'post_status'    => array_keys( wc_get_order_statuses() ),
				'date_query'     => array(
					array(
						'column' => 'post_date_gmt',
						'after'  => wp_date( 'Y-m-d', strtotime( '-1 day', get_option( 'repack_start' ) ) ),
					),
				),
			)
		);

		if ( $consents === 0 || $orders->found_posts === 0 ) {
			return '0';
		}

		$ratio = round( $consents * 100 / $orders->found_posts );

		return (string) $ratio > 0 ? $ratio : '0';
	}

	/**
	 * Dismisses the notice.
	 *
	 * @access private
	 * @since 1.1.0
	 * @return void
	 */
	private function dismiss_notice() {

		// Check if this is the request we want.
		if ( isset( $_GET['_wpnonce'] ) && isset( $_GET['repack-hide-notice'] ) ) {
			if ( 'telemetry' === sanitize_text_field( wp_unslash( $_GET['repack-hide-notice'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// Check the wp-nonce.
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
					// All good, we can save the option to dismiss this notice.
					update_option( 'repack_telemetry_no_consent', true );
				}
			}
		}
	}

	/**
	 * Dismisses the notice.
	 *
	 * @access private
	 * @since 1.1.0
	 * @return void
	 */
	private function consent() {

		// Check if this is the request we want.
		if ( isset( $_GET['_wpnonce'] ) && isset( $_GET['repack-consent-notice'] ) ) {
			if ( 'telemetry' === sanitize_text_field( wp_unslash( $_GET['repack-consent-notice'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// Check the wp-nonce.
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
                    // Initially send the data in a minute
                    if( wp_schedule_single_event(time() + MINUTE_IN_SECONDS, 'repack_telemetry') ) {
                        // All good, we can save the option to dismiss this notice.
                        update_option( 'repack_telemetry_optin', true );
                    }
				}
			}
		}
	}

	/**
	 * Prints the table styles.
	 *
	 * Normally we'd just use the .widefat CSS class for the table,
	 * however apparently there's an obscure bug in WP causing this: https://github.com/aristath/repack/issues/2067
	 * This CSS is a copy of some styles from common.css in wp-core.
	 *
	 * @access private
	 * @since 1.1.0
	 * @return void
	 */
	private function table_styles() {
		?>
		<style>
			/* .widefat - main style for tables */
			.data-to-send { border-spacing: 0; width: 100%; clear: both; }
			.data-to-send * { word-wrap: break-word; }
			.data-to-send a, .data-to-send button.button-link { text-decoration: none; }
			.data-to-send td, .data-to-send th { padding: 8px 10px; }
			.data-to-send thead th, .data-to-send thead td { border-bottom: 1px solid #e1e1e1; }
			.data-to-send tfoot th, .data-to-send tfoot td { border-top: 1px solid #e1e1e1; border-bottom: none; }
			.data-to-send .no-items td { border-bottom-width: 0; }
			.data-to-send td { vertical-align: top; }
			.data-to-send td, .data-to-send td p, .data-to-send td ol, .data-to-send td ul { font-size: 13px; line-height: 1.5em; }
			.data-to-send th, .data-to-send thead td, .data-to-send tfoot td { text-align: left; line-height: 1.3em; font-size: 14px; }
			.data-to-send th input, .updates-table td input, .data-to-send thead td input, .data-to-send tfoot td input { margin: 0 0 0 8px; padding: 0; vertical-align: text-top; }
		</style>
		<?php
	}
}
