<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://WeRePack.org
 * @since      1.0.0
 *
 * @package    Repack
 * @subpackage Repack/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Repack
 * @subpackage Repack/public
 * @author     Philipp Wellmer <philipp@ouun.io>
 */
class Repack_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The post meta name.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $meta_name    The post meta name.
	 */
	protected $meta_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 * @param string $meta_name The post meta name.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version, $meta_name ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->meta_name   = $meta_name;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/repack-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/repack-public-min.js', array( 'jquery', 'wc-checkout' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'repack',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'repack_ajax_nonce' ),
			)
		);
	}

	/**
	 * The WC custom form field args
	 *
	 * @param array $args
	 *
	 * @return array
	 * @since    1.0.0
	 */
	private function get_repack_form_field_args( $args = array() ) {
		$merged_args = array_merge(
			array(
				'label'       => apply_filters( 'repack_consent_field_label', __( 'Yes, please reuse packaging if available.', 'repack' ) ),
				'description' => apply_filters(
					'repack_consent_field_description',
					sprintf(
					/* translators: %s: WeRePack website link */
						__( 'With your consent we prefer already used shipping packaging. Help us protect the environment and learn more about the initiative on %s.', 'repack' ),
						'<a href="https://werepack.org/" target="_blank">WeRePack.org</a>'
					)
				),
				'type'        => 'checkbox',
				'required'    => false,
				'priority'    => 99,
				'class'       => array(
					'repack',
					'repack-checkbox',
				),
			),
			$args
		);

		return apply_filters( 'repack_consent_field_args', $merged_args );
	}

	/**
	 * Append RePack section to checkout fields.
	 *
	 * @param WC_Checkout $checkout
	 *
	 * @since    1.0.0
	 */
	public function add_checkout_repack_field( $checkout ) {

		global $woocommerce;
		$cart = $woocommerce->cart;

		// todo: No shipping, no packaging?
		if ( $cart->needs_shipping() ) {
			?>
			<div id="order_repack-wrap">
				<p>
					<strong>
						<?php
						printf(
							esc_html(
							/* translators: %s: Amount of packaging to send */
								_n(
									'%s packaging can be saved',
									'%s packaging can be saved',
									count( $cart->get_shipping_packages() ),
									'repack'
								)
							),
							esc_html( number_format_i18n( count( $cart->get_shipping_packages() ) ) )
						);
						?>
					</strong>
				</p>
				<div class="woocommerce-additional-fields__field-wrapper">
					<?php echo esc_html( woocommerce_form_field( 'shipping_repack', $this->get_repack_form_field_args( array( 'clear' => true ) ), $checkout->get_value( 'shipping_repack' ) ) ); ?>
					<?php
					echo esc_html(
						woocommerce_form_field(
							'repack_counter',
							array(
								'type'              => 'number',
								'custom_attributes' => array( 'style' => 'display: none;' ),
							),
							count( $cart->get_shipping_packages() )
						)
					);
					?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Add RePack field to shipping settings (e.g. My Account)
	 *
	 * @param $fields
	 *
	 * @return mixed
	 *
	 * @since    1.0.0
	 */
	public function add_shipping_repack_field( $fields ) {
		// Add extra section in checkout!
		if ( ! is_checkout() ) {
			$fields['shipping_repack'] = $this->get_repack_form_field_args();
		}

		return $fields;
	}

	/**
	 * Coupon Code handling
	 *
	 * @param bool|null $value  RePack checkbox value
	 *
	 * @since    1.0.0
	 */
	public function repack_apply_coupon( $value ) {
		global $woocommerce;

		// Customizable coupon name via 'repack_coupon_name' filter
		$coupon = wc_sanitize_coupon_code( apply_filters( 'repack_coupon_name', 'WeRePack' ) );

		// Fail early
		if ( ! $this->repack_coupon_exists() ) {
			return;
		}

		if ( isset( $value ) && $value && ! $woocommerce->cart->has_discount( $coupon ) ) {
			// Add coupon and show notice on success
			if ( $woocommerce->cart->apply_coupon( $coupon ) ) {
				wc_clear_notices();
				wc_add_notice(
					apply_filters(
						'repack_coupon_removed_notice_text',
						sprintf(
						/* translators: %s: Thank You */
							__( 'Your discount for reusing packaging has been applied. %s', 'repack' ),
							'<strong>' . __( 'Thank you!', 'repack' ) . '</strong>'
						)
					),
					'success'
				);
			}
		} elseif ( ! $value && $woocommerce->cart->has_discount( $coupon ) ) {
			// Remove coupon and show notice on success
			if ( $woocommerce->cart->remove_coupon( $coupon ) ) {
				wc_clear_notices();
				wc_add_notice(
					apply_filters(
						'repack_coupon_applied_notice_text',
						sprintf(
							__( 'Your discount for reusing packaging has been removed.', 'repack' )
						)
					),
					'notice'
				);
			}
		}

	}

	/**
	 * AJAX Apply RePack Coupon
	 */
	public function repack_checkout_apply_coupon() {
		if ( $this->repack_coupon_exists() ) {
			try {
				$nonce_value = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

				if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
					WC()->session->set( 'refresh_totals', true );
					throw new Exception( __( 'We were unable to process your order, please try again.', 'repack' ) );
				}

				// Run coupon logic with checkbox value
				$this->repack_apply_coupon( $_POST['shipping_repack'] );

			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
		}
	}

	/**
	 * AJAX Apply RePack Coupon
	 */
	public function repack_ajax_apply_coupon() {

		if ( check_ajax_referer( 'repack_ajax_nonce', 'nonce_token' ) ) {
			// Fail early
			if ( $this->repack_coupon_exists() ) {
				// Parse fields
				$values = array();
				parse_str( $_POST['post_data'], $values );

				// Run coupon logic with checkbox value
				$this->repack_apply_coupon( ! isset( $values['shipping_repack'] ) ? false : true );
			}
		}

		// Die
		wp_die();
	}

	/**
	 * Check for available coupon code?
	 *
	 * @return bool
	 */
	public function repack_coupon_exists() {
		// Customizable coupon name via 'repack_coupon_name' filter
		$coupon = wc_sanitize_coupon_code( apply_filters( 'repack_coupon_name', 'WeRePack' ) );

		return wc_get_coupon_id_by_code( $coupon ) > 0;
	}


	/**
	 * Global RePack counter update
	 *
	 * @param int $packages
	 *
	 * @since    1.0.0
	 */
	public function update_global_repack_counter( $packages = 0 ) {
		//Update the settings with the new count
		update_option(
			'repack_counter',
			get_option( 'repack_counter', 0 ) + $packages
		);
	}

	/**
	 * Get total amount of repacked packages
	 *
	 * @return bool|mixed|void
	 */
	public function get_global_reoack_counter() {
		return get_option( 'repack_counter' );
	}

	/**
	 * Get total amount of repacked packages
	 *
	 * @return bool|mixed|void
	 */
	public function get_user_reoack_counter( $user_id ) {
		return get_user_meta(
			$user_id,
			$this->meta_name,
			true
		);
	}

	/**
	 * Save data to order and clients user meta
	 *
	 * @param WC_Order $order
	 * @param $data
	 *
	 * @throws Exception
	 */
	public function repack_save_order( $order, $data ) {
		$nonce_value = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
			WC()->session->set( 'refresh_totals', true );
			throw new Exception( __( 'We were unable to process your order, please try again.', 'repack' ) );
		}

		if ( isset( $_POST['shipping_repack'] ) && isset( $_POST['repack_counter'] ) && ! empty( $_POST['shipping_repack'] ) && ! empty( $_POST['repack_counter'] ) ) {
			// Save order meta & count saved packages
			$order->update_meta_data( '_' . $this->meta_name, wc_bool_to_string( $_POST['shipping_repack'] ) );

			// Add order note
			$order->add_order_note( __( 'Shipping with reused packaging preferred!', 'repack' ), true, false );

			// Update global RePack counter
			$this->update_global_repack_counter( absint( $_POST['repack_counter'] ) );

			// Save customer decision for next order & count saved packages of user
			update_user_meta( $order->get_customer_id(), $this->meta_name, $_POST['shipping_repack'] );
			update_user_meta(
				$order->get_customer_id(),
				$this->meta_name,
				(int) get_user_meta(
					$order->get_customer_id(),
					$this->meta_name,
					true
				) + absint( $_POST['repack_counter'] )
			);
		}
	}

	/**
	 * RePack Shortcode
	 *
	 * @return void
	 */
	public function repack_shortcode() {
		add_shortcode(
			'repack',
			function( $attributes ) {
				// Extract attributes
				$attributes = shortcode_atts(
					array(
						'prepend' => '',
						'append'  => '',
						'user_id' => null,

					),
					$attributes,
					'repack'
				);

				if ( $attributes['user_id'] ) {
					return $attributes['prepend'] . $this->get_user_reoack_counter( (int) $attributes['user_id'] ) . $attributes['append'];
				}

				return $attributes['prepend'] . $this->get_global_reoack_counter() . $attributes['append'];
			}
		);
	}
}
