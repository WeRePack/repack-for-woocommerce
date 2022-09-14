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
	 * The coupon name.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $coupon_name   The coupon name.
	 */
	protected $coupon_name;

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

		// Do not allow removal of WeRePack Coupon
		// @see https://github.com/WeRePack/repack-for-woocommerce/issues/4
		wp_add_inline_style( $this->plugin_name, '.woocommerce-remove-coupon[data-coupon="' . wc_strtolower( apply_filters( 'repack_coupon_name', null ) ) . '"] {display: none;}' );
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
				'options'  => array(
					'celebrate' => wc_string_to_bool( apply_filters( 'repack_consent_field_firework', null ) ),
				),
			)
		);
	}

	/**
	 * Set WeRePack Coupon Code for further usage
	 */
	public function set_repack_coupon_name() {
		$this->coupon_name = self::get_repack_coupon_name();
	}

	/**
	 * Get WeRePack Coupon Code
	 *
	 * @return string
	 */
	public static function get_repack_coupon_name() {
		return wc_sanitize_coupon_code( apply_filters( 'repack_coupon_name', null ) );
	}

	/**
	 * Get Coupon ID
	 *
	 * @return int
	 */
	public static function get_repack_coupon_id() {
		return wc_get_coupon_id_by_code( self::get_repack_coupon_name() );
	}

	/**
	 * Coupon exists
	 *
	 * @return bool
	 */
	public static function repack_coupon_exists() {
		return self::get_repack_coupon_id() > 0;
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
				'label'       => apply_filters( 'repack_consent_field_label', null ),
				'description' => apply_filters( 'repack_consent_field_description', null ),
				'type'        => 'checkbox',
				'required'    => false,
				'priority'    => 99,
				'class'       => array(
					'repack',
					'repack-checkbox',
				),
				'input_class' => array(
					'woocommerce-form__input',
					'woocommerce-form__input-checkbox',
					'input-checkbox',
				),
			),
			$args
		);

		return apply_filters( 'repack_consent_field_args', $merged_args );
	}

	/**
	 * Append WeRePack section to checkout fields.
	 *
	 * @param WC_Checkout $checkout
	 *
	 * @since    1.0.0
	 */
	public function add_checkout_repack_field( $checkout ) {

		global $woocommerce;
		$cart = $woocommerce->cart;

		// No shipping, no packaging!
		if ( $cart->needs_shipping() ) {
			// Count the amount of packages
			$packages = count( $cart->get_shipping_packages() );
			?>
			<div id="order_repack-wrap">
				<h3>
					<?php
					if ( 1 === $packages ) {
						printf(
							esc_html__( 'Save a packaging with us', 'repack-for-woocommerce' ),
							esc_html( number_format_i18n( $packages ) )
						);
					} else {
						printf(
							esc_html(
							/* translators: %d: Amount of packaging to send */
								_n(
									'Save %d packaging with us',
									'Save %d packaging with us',
									$packages,
									'repack-for-woocommerce'
								)
							),
							esc_html( number_format_i18n( $packages ) )
						);
					}
					?>
				</h3>
				<div class="woocommerce-additional-fields__field-wrapper">
					<?php
					echo esc_html(
						woocommerce_form_field(
							'shipping_repack',
							$this->get_repack_form_field_args(
								array( 'clear' => true )
							),
							// todo: $checkout is not available outside the fields form, what if position is e.g. 'woocommerce_review_order_before_submit'? Currently null.
							is_object( $checkout ) ? $checkout->get_value( 'shipping_repack' ) : null
						)
					);
					?>
					<?php
					echo esc_html(
						woocommerce_form_field(
							'repack_counter',
							array(
								'type'              => 'number',
								'custom_attributes' => array( 'style' => 'display: none;' ),
							),
							$packages
						)
					);
					?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Filters $checkout->get_value() to set default checkbox state
	 *
	 * @param $value
	 * @param $input
	 *
	 * @return bool|null
	 *
	 * @since    1.0.4
	 */
	public function repack_checkout_consent_value( $value, $input ) {
		if ( is_null( $value ) && 'shipping_repack' === $input ) {
			$value = $this->stage_checkout_checkbox_active_state();
		}

		return $value;
	}

	/**
	 * Evaluate the checkbox value
	 * That differs from availability of a coupon
	 *
	 * @return bool
	 *
	 * @since    1.0.4
	 */
	public function stage_checkout_checkbox_active_state() {
		global $woocommerce;

		if ( $this->repack_coupon_exists() && $woocommerce->cart->has_discount( $this->coupon_name ) ) {
			// Coupon is applied, or checkbox was checked
			$state = true;
		} elseif ( $woocommerce->customer->get_id() > 0 ) {
			// Get the users shipping setting
			$state = $this->get_user_repack_setting( $woocommerce->customer->get_id() );
		} else {
			// Lastly get site default
			$state = apply_filters( 'repack_checkout_consent_default_state', true );
		}

		return $state;
	}

	/**
	 * Add WeRePack field to shipping settings (e.g. My Account)
	 *
	 * @param $fields
	 *
	 * @return mixed
	 *
	 * @since    1.0.0
	 */
	public function add_shipping_repack_field( $fields ) {
		// Add shipping field outside checkout!
		if ( ! is_checkout() ) {
			$fields['shipping_repack'] = $this->get_repack_form_field_args();
		}

		return $fields;
	}

	/**
	 * Coupon Code handling
	 *
	 * @param bool|null $value  WeRePack checkbox value
	 *
	 * @since    1.0.0
	 */
	public function repack_apply_coupon( $value ) {
		global $woocommerce;

		// Fail early
		if ( ! $this->repack_coupon_exists() ) {
			return;
		}

		if ( isset( $value ) && $value && ! $woocommerce->cart->has_discount( $this->coupon_name ) ) {
			// Add coupon and show notice on success
			if ( $woocommerce->cart->apply_coupon( $this->coupon_name ) ) {
				wc_clear_notices();
				wc_add_notice(
					apply_filters(
						'repack_coupon_applied_notice_text',
						null
					),
					'success'
				);
			}
		} elseif ( ! $value && $woocommerce->cart->has_discount( $this->coupon_name ) ) {
			// Remove coupon and show notice on success
			if ( $woocommerce->cart->remove_coupon( $this->coupon_name ) ) {
				wc_clear_notices();
				wc_add_notice(
					apply_filters(
						'repack_coupon_removed_notice_text',
						null
					),
					'notice'
				);
			}
		}

	}

	/**
	 * AJAX Apply WeRePack Coupon
	 */
	public function repack_checkout_apply_coupon() {
		if ( $this->repack_coupon_exists() ) {
			try {
				$nonce_value = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

				if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
					WC()->session->set( 'refresh_totals', true );
					throw new Exception( __( 'We were unable to process your order, please try again.', 'repack-for-woocommerce' ) );
				}

				// Run coupon logic with checkbox value
				$this->repack_apply_coupon( $_POST['shipping_repack'] );

			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
		}
	}

	/**
	 * AJAX Apply WeRePack Coupon
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
	 * Global WeRePack counter update
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
			throw new Exception( __( 'We were unable to process your order, please try again.', 'repack-for-woocommerce' ) );
		}

		if ( isset( $_POST['shipping_repack'] ) && isset( $_POST['repack_counter'] ) && ! empty( $_POST['shipping_repack'] ) && ! empty( $_POST['repack_counter'] ) ) {
			// Save order meta & count saved packages
			$order->update_meta_data( '_' . $this->meta_name, wc_bool_to_string( $_POST['shipping_repack'] ) );

			// Add order note
			$order->add_order_note( __( 'Shipping with reused packaging preferred!', 'repack-for-woocommerce' ), true, false );

			// Update global WeRePack counter
			$this->update_global_repack_counter( absint( $_POST['repack_counter'] ) );

			// Save customer decision for next order & count saved packages of user
			update_user_meta( $order->get_customer_id(), $this->meta_name, wc_bool_to_string( $_POST['shipping_repack'] ) );
			update_user_meta(
				$order->get_customer_id(),
				$this->meta_name . '_counter',
				(int) $this->get_user_repack_counter( $order->get_customer_id() ) + absint( $_POST['repack_counter'] )
			);
		}
	}

	/**
	 * User: Get user repack setting
	 *
	 * @return bool|mixed|void
	 */
	public function get_user_repack_setting( $user_id ) {
		return wc_string_to_bool(
			get_user_meta(
				$user_id,
				$this->meta_name,
				true
			)
		);
	}

	/**
	 * User: Get total amount of repacked packages
	 *
	 * @return bool|mixed|void
	 */
	public function get_user_repack_counter( $user_id ) {
		$counter = get_user_meta(
			$user_id,
			$this->meta_name . '_counter',
			true
		);

		return ! empty( $counter ) ? $counter : '0';
	}

	/**
	 * Get total amount of repacked packages
	 *
	 * @return bool|mixed|void
	 */
	public static function get_global_repack_counter() {
		$counter = get_option( 'repack_counter' );
		return is_string( $counter ) ? $counter : '0';
	}

	/**
	 * Get calculated savings as value or string: Packages, Trees, Water & CO2
	 *
	 * @param string $type co2, water or trees. Default is packages
	 * @param null $packages The amount of packages used for calculation
	 * @param bool $value Return only value without unit
	 *
	 * @return string
	 */
	public static function get_repack_savings( $type = '', $packages = null, $value = false ) {
		// If no amount is passed, get total savings
		$packages = null !== $packages ? $packages : self::get_global_repack_counter();

		// Return Saved Water in litre
		if ( 'water' === $type ) {
			$water = round( $packages * 123, 1 );
			return $value ? $water : sprintf(
			/* translators: %s lites of water */
				__( '%s litres of water', 'repack-for-woocommerce' ),
				self::get_counter_span( $water )
			);
		}

		// Return CO2 Savings in gram
		if ( 'co2' === $type ) {
			$co2 = round( $packages * 156, 1 );
			return $value ? $co2 : sprintf(
			/* translators: %s grams of CO2 */
				__( '%s grams of CO2', 'repack-for-woocommerce' ),
				self::get_counter_span( $co2 )
			);
		}

		// Return Saved Mature Trees
		if ( 'trees' === $type ) {
			$trees = round( $packages * 0.0024, 1 );
			return $value ? $trees : sprintf(
			/* translators: %s mature trees */
				__( '%s mature trees', 'repack-for-woocommerce' ),
				self::get_counter_span( $trees, '', 1 )
			);
		}

		return $value ? $packages : sprintf(
		/* translators: %s packaging reused */
			__( '%s packaging', 'repack-for-woocommerce' ),
			self::get_counter_span( $packages )
		);
	}

	/**
	 * Get Counter Markup
	 *
	 * @param $value
	 * @param string $suffix
	 * @param int $decimals
	 * @return string
	 */
	public static function get_counter_span( $value, $suffix = '', $decimals = 0 ) {
		return wp_kses_post( "<span class='repack-counter' data-countup='$value' data-countup-suffix='$suffix'>" . number_format_i18n( $value, $decimals ) . $suffix . '</span>' );
	}

	/**
	 * Pass data and render saving template
	 *
	 * @param null $packages
	 * @return false|string
	 */
	public static function get_repack_summary( $packages = null ) {
		$template_loader = new Repack_Template_Loader();

		if ( ! $packages ) {
			$packages = self::get_repack_savings( 'packaging', $packages, true );
		}

		ob_start();
		$template_loader
			->set_template_data(
				apply_filters(
					'repack_template_summary_data',
					array(
						'start' => wp_date( get_option( 'date_format' ), get_option( 'repack_start' ) ),
						'logo'  => esc_url( REPACK_PLUGIN_URL . '/public/images/werepack-teal-mini.png' ),
						'title' => 'WeRePack.org',
						'url'   => esc_url( 'https://WeRePack.org/' ),
					)
				),
				'werepack'
			)
			->set_template_data(
				apply_filters(
					'repack_template_summary_saving',
					array(
						'counter'   => $packages,
						'next'      => $packages + 1 . self::get_repack_ordinal_suffix( $packages + 1 ),
						'packaging' => self::get_repack_savings( 'packaging', $packages ),
						'co2'       => self::get_repack_savings( 'co2', $packages ),
						'water'     => self::get_repack_savings( 'water', $packages ),
						'trees'     => self::get_repack_savings( 'trees', $packages ),
					)
				),
				'saving'
			)
			->get_template_part( 'summary' );
		return ob_get_clean();
	}

	/**
	 * WeRePack Shortcode
	 *
	 * @return void
	 */
	public function repack_shortcodes() {
		add_shortcode(
			'repack',
			function( $attributes ) {
				// Extract attributes
				$attributes = shortcode_atts(
					array(
						'prepend'  => '',
						'append'   => '',
						'type'     => null,
						'packages' => null,
						'value'    => false,
						'user_id'  => null,

					),
					$attributes,
					'repack'
				);

				// Get packages by user, value passed or site total
				$packages = (int) $attributes['user_id'] && ! $attributes['packages'] ?
					$this->get_user_repack_counter( (int) $attributes['user_id'] ) :
					$attributes['packages'];

				return $attributes['prepend'] . $this->get_repack_savings( $attributes['type'], null === $packages ? '0' : $packages, $attributes['value'] ) . $attributes['append'];
			}
		);

		add_shortcode(
			'repack_summary',
			function( $attributes ) {
				// Extract attributes
				$attributes = shortcode_atts(
					array(
						'prepend'  => '',
						'packages' => null,
						'append'   => '',
					),
					$attributes,
					'repack_summary'
				);

				return $attributes['prepend'] . $this->get_repack_summary( $attributes['packages'] ) . $attributes['append'];
			}
		);
	}

	/**
	 * Get Ordinal Suffix of a Number
	 *
	 * @param $num
	 *
	 * @return string
	 */
	public static function get_repack_ordinal_suffix( $num ) {
		if ( substr( get_locale(), 0, 2 ) !== 'en' ) {
			return '.';
		}

		$num = $num % 100; // protect against large numbers
		if ( $num < 11 || $num > 13 ) {
			switch ( $num % 10 ) {
				case 1:
					return 'st';
				case 2:
					return 'nd';
				case 3:
					return 'rd';
			}
		}
		return 'th';
	}
}
