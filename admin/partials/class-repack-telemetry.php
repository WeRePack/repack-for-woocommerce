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
		$this->run_action();

		// Scheduled event to trigger telemetry
		add_action( 'repack_telemetry', array( $this, 'maybe_send_data' ) );
	}

	/**
	 * Maybe send data.
	 *
	 * @access public
	 * @since 1.1.0
	 *
	 * @param array $args
	 * @return void
	 */
	public function maybe_send_data( $args = array() ) {
		// Check if the user has consented to the data sending.
		if ( ! get_option( 'repack_telemetry_optin' ) ) {
			return;
		}

		// Send data
		$request = $this->send_data( $args );

		// Get Response
		$api_response      = json_decode( wp_remote_retrieve_body( $request ), true );
		$api_response_code = wp_remote_retrieve_response_code( $request );

		// Blocking request (e.g. Sync) retrieve a proper debuggable response
		if ( isset( $args['blocking'] ) && $args['blocking'] ) {
			foreach ( $api_response as $message ) {
				if ( $message ) {
					$classes = strval( $api_response_code ) === '200' ? 'notice notice-success' : 'notice notice-error';
					add_action(
						'admin_notices',
						function () use ( $message, $classes ) {
							echo '<div class="' . esc_html( $classes ) . '"><p><b>' . esc_html__( 'WeRePack.org API Response:', 'repack-for-woocommerce' ) . '</b> ' . esc_html( $message ) . '</p></div>';
						}
					);
				}
			}

			if ( strval( $api_response_code ) !== '200' ) {
				return;
			}
		}

		update_option( 'repack_telemetry_sent', time() );
	}

	/**
	 * Sends data.
	 *
	 * @access private
	 * @since 1.1.0
	 *
	 * @param array $args
	 * @return array|WP_Error
	 */
	private function send_data( $args = array() ) {
		// Ping remote server.
		return wp_remote_post(
			'https://werepack.org/api/community/v1/sites',
			wp_parse_args(
				$args,
				array(
					'method'   => 'POST',
					'blocking' => false,
                    'timeout'  => 30,
					'body'     => $this->get_data(
						array(
							'repack_last_sent' => time(),
						)
					),
				)
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
		if ( get_option( 'repack_telemetry_consent_dismissed' ) ||
             get_option( 'repack_telemetry_optin' ) ||
           ! function_exists( 'wc_get_order_statuses' )
        ) {
			return;
		}

		$template_loader = new Repack_Template_Loader();
		$data            = $this->get_data();
		?>
		<div class="notice notice-info repack-telemetry">
			<h3><strong><?php esc_html_e( 'Help us reducing packaging waste. Join the WeRePack Community.', 'repack-for-woocommerce' ); ?></strong></h3>
			<p style="max-width: 76em;">
				<?php _e( 'We want to win you as a supporter and measure our joint success. To do this, you can share certain data with us in order to be listed in the supporter directory on WeRePack.org. This way, we can measure our positive impact on e-commerce and give you a platform that recognises your commitment to the environment. <br><strong>No sensitive user data is transferred.</strong>', 'repack-for-woocommerce' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</p>
			<div class="toggle-hidden hidden">
				<?php
				$template_loader
					->set_template_data( $data )
					->get_template_part( 'telemetry-data' );
				?>
			</div>
			<p class="actions">
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-action', 'telemetry' ) ) ); ?>" class="button button-primary consent"><?php esc_html_e( 'I agree', 'repack-for-woocommerce' ); ?></a>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'repack-action', 'hide-notice' ) ) ); ?>" class="button button-secondary dismiss"><?php esc_html_e( 'No thanks', 'repack-for-woocommerce' ); ?></a>
				<a class="button button-link details details-show"><?php esc_html_e( 'Show me the data', 'repack-for-woocommerce' ); ?></a>
				<a class="button button-link details details-hide hidden"><?php esc_html_e( 'Collapse data', 'repack-for-woocommerce' ); ?></a>
			</p>
			<script>
				jQuery( '.repack-telemetry a.details' ).on( 'click', function() {
					jQuery( '.repack-telemetry .toggle-hidden' ).toggleClass( 'hidden' );
					jQuery( '.repack-telemetry a.details-show' ).toggleClass( 'hidden' );
					jQuery( '.repack-telemetry a.details-hide' ).toggleClass( 'hidden' );
				});
			</script>
		</div>
		<?php
	}

	/**
	 * Builds and returns the data or uses cached if data already exists.
	 *
	 * @access private
	 * @since 1.1.0
	 *
	 * @param array $data
	 * @return array
	 */
	public function get_data( $data = array() ) {
		// Build data and return the array.
		return wp_parse_args(
			$data,
			array(
				'site_url'           => home_url( '/' ),
				'site_lang'          => get_locale(),
				'repack_start'       => get_option( 'repack_start' ),
				'repack_counter'     => get_option( 'repack_counter' ),
				'repack_ratio'       => $this->get_repack_ratio( get_option( 'repack_counter' ) ),
				'repack_coupon'      => Repack_Public::repack_coupon_exists(),
				'repack_coupon_code' => Repack_Public::get_repack_coupon_name(),
				'repack_last_sent'   => get_option( 'repack_telemetry_sent' ),
			)
		);
	}

	/**
	 * Calculate the ratio: orders / consents
	 *
	 * @param $consents
	 * @return string
	 */
	private function get_repack_ratio( $consents ) {
		// Orders since starting WeRePack support
		$orders = new WP_Query(
			array(
				'posts_per_page' => -1,
				'fields'          => 'ids',
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

		if ( 0 === $consents || 0 === $orders->found_posts ) {
			return '0';
		}

		$ratio = round( $consents * 100 / $orders->found_posts );

		// Edge Case: Ratio can become >100% when deleting orders
		// We need to fix the amount of consents to not confuse
		if ( $ratio > 100 ) {
			// Get the overhead
			$overhead = $consents - $orders->found_posts;
			// Update consents field to fix overhead
			// Sure we are sad to do that, but we need to follow math rules
			update_option( 'repack_counter', $consents - $overhead );
		}

		return (string) $ratio > 0 ? $ratio : '0';
	}

	/**
	 * Run action by URL.
	 *
	 * @access private
	 * @since 1.2.0
	 * @return void
	 */
	private function run_action() {

		// Check if this is the request we want.
		if ( isset( $_GET['_wpnonce'] ) && isset( $_GET['repack-action'] ) ) {

			// Hide Notice
			if ( 'hide-notice' === sanitize_text_field( wp_unslash( $_GET['repack-action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// Check the wp-nonce.
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
					// All good, we can save the option to dismiss this notice.
					update_option( 'repack_telemetry_consent_dismissed', true );
				}
			}

			// Telemetry Consent
			if ( 'telemetry' === sanitize_text_field( wp_unslash( $_GET['repack-action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// Check the wp-nonce.
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
					// Add recurring events
					if ( self::activate_telemetry() ) {
						// All good, we can save the option to dismiss this notice.
						update_option( 'repack_telemetry_optin', true );

						// Initially send the data in a minute
						wp_schedule_single_event( time() + MINUTE_IN_SECONDS * 30, 'repack_telemetry' );
					}
				}
			}

			// Revoke Telemetry Consent
			if ( 'revoke-telemetry' === sanitize_text_field( wp_unslash( $_GET['repack-action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// Check the wp-nonce.
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
					// Remove recurring events
					self::deactivate_telemetry();
					// Remove consent
					update_option( 'repack_telemetry_optin', false );
				}
			}

			// Sync with WeRePack.org
			if ( 'sync' === sanitize_text_field( wp_unslash( $_GET['repack-action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// Check the wp-nonce.
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
					$this->maybe_send_data( array( 'blocking' => true ) );
				}
			}
		}
	}

	/**
	 * Activate Telemetry Events
	 * @since    1.4.0
	 *
	 * @return bool|WP_Error
	 */
	public static function activate_telemetry() {
        // Remove existing events
        self::deactivate_telemetry();

		// Note: No data sending without consent
		if ( ! wp_next_scheduled( 'repack_telemetry' ) ) {
			return wp_schedule_event( time(), 'weekly', 'repack_telemetry' );
		}

		return false;
	}

	/**
	 * Deactivate Telemetry Events
	 * @since    1.4.0
	 *
	 * @return false|int|WP_Error
	 */
	public static function deactivate_telemetry() {
		return wp_clear_scheduled_hook( 'repack_telemetry' );
	}
}
