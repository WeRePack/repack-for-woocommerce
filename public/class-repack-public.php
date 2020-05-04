<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ouun.io
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
		$this->version = $version;
		$this->meta_name = $meta_name;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Repack_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Repack_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/repack-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Repack_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Repack_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/repack-public.js', array( 'jquery', 'wc-checkout' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'repack', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}

	/**
     * The WC custom form field args
     *
	 * @param array $args
	 *
	 * @return array
     * @since    1.0.0
	 */
	private function get_repack_form_field_args( $args = [] ) {
		$merged_args = array_merge( array(
			'label' => __( 'Yes, please reuse packaging if available.', 'repack' ),
			'description' => sprintf( __( 'Help us to protect the environment. With your consent we will prefere already used shipping packaging for your order. Learn more about the initiative on %s.', 'repack' ), '<a href="https://werepack.org/" target="_blank">WeRePack.org</a>'),
			'type' => 'checkbox',
			'required' => false,
			'priority' => 99,
			'class' => array(
				'repack',
				'repack-checkbox'
			),
		), $args );

		return apply_filters( 'repack_form_field_args', $merged_args );
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
        if( $cart->needs_shipping() ) {
            ?>
            <div id="order_repack-wrap">
                <p>
                    <strong>
                        <?php printf( _n(
                                'Another reused packaging',
                                'Another %s reused packaging',
                                count( $cart->get_shipping_packages() ),
                                'repack'
                        ), number_format_i18n( count( $cart->get_shipping_packages() ) ) ); ?>
                    </strong>
                </p>
                <div class="woocommerce-additional-fields__field-wrapper">
                    <?php echo woocommerce_form_field( 'shipping_repack', $this->get_repack_form_field_args( ['clear' => true ] ), $checkout->get_value( 'shipping_repack' )); ?>
                    <?php echo woocommerce_form_field( 'repack_counter', [ 'type' => 'number', 'custom_attributes' => [ 'style' => 'display: none;' ] ], count( $cart->get_shipping_packages() ) ); ?>
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
	function add_shipping_repack_field( $fields ) {
		// Add extra section in checkout!
		if(!is_checkout()) {
			$fields['shipping_repack'] = $this->get_repack_form_field_args();
		}

		return $fields;
	}

	/**
	 * Save the post meta
     *
	 * @since    1.0.0
	 */
	public function repack_apply_coupon() {
		global $woocommerce;

		// Customizable coupon name via 'repack_coupon_name' filter
		$coupon = sanitize_text_field( apply_filters( 'repack_coupon_name', 'WeRePack' ) );

		if ( ! empty( $_POST['shipping_repack'] ) && ! $woocommerce->cart->has_discount( $coupon ) ) {

			if ($woocommerce->cart->apply_coupon( $coupon ) ) {
				$saving = $woocommerce->cart->get_coupon_discount_amount( $coupon, false );
				wc_clear_notices();
				wc_add_notice( sprintf(
					__("Your order is over %s so a %s Discount has been Applied! Your total order weight is %s.", "woocommerce"),
					$saving, '10%', '<strong>' . 'YAY'. '</strong>'
				), "success");

				$woocommerce->cart->calculate_totals();
			}

			//$errors->add( 'repack_coupon', __( 'Yay, thanks a lot.', 'repack' ) . $saving );
			// Manually recalculate totals.  If you do not do this, a refresh is required before user will see updated totals when discount is removed.
			//
		} else if ( empty( $_POST['shipping_repack'] ) && $woocommerce->cart->has_discount( $coupon ) ) {

			if( $woocommerce->cart->remove_coupon( $coupon ) ) {
				wc_clear_notices();
				wc_add_notice( sprintf(
					__("Removed %s.", "woocommerce"),
					'hello', '10%', '<strong>' . 'YAY'. '</strong>'
				), "notice");

				$woocommerce->cart->calculate_totals();
			}



			//$errors->add( 'repack_coupon', __( 'NAY, thanks a lot.', 'repack' ) );

		}

	}


	public function repack_ajax_apply_coupon() {
		global $woocommerce;

		// Customizable coupon name via 'repack_coupon_name' filter
		$coupon = sanitize_text_field( apply_filters( 'repack_coupon_name', 'WeRePack' ) );

		$values = array();
		parse_str($_POST['post_data'], $values);

		if( isset($values['shipping_repack']) && $values['shipping_repack'] && ! $woocommerce->cart->has_discount( $coupon ) ) {
			$woocommerce->cart->apply_coupon( $coupon );
		} else if ( !isset( $values['shipping_repack'] ) && $woocommerce->cart->has_discount( $coupon ) ) {
			$woocommerce->cart->remove_coupon( $coupon );
		}

		$woocommerce->cart->calculate_totals();
		woocommerce_cart_totals();

		wp_die();
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
     * Save data to order and clients user meta
     *
	 * @param WC_Order $order
	 * @param $data
	 */
	public function repack_save_order( $order, $data ) {

        if ( isset( $_POST['shipping_repack'] ) && isset( $_POST['repack_counter'] ) && ! empty( $_POST['shipping_repack'] ) && ! empty( $_POST['repack_counter'] ) ) {
            // Save order meta & count saved packages
            $order->update_meta_data('_' . $this->meta_name, $_POST['shipping_repack'] );

            // Update global RePack counter
            $this->update_global_repack_counter( absint( $_POST['repack_counter'] ) );

            // Save customer decision for next order & count saved packages of user
            update_user_meta( $order->get_customer_id(), $this->meta_name, $_POST['shipping_repack'] );
            update_user_meta(
                $order->get_customer_id(),
                $this->meta_name . '_counter',
                (int) get_user_meta(
                    $order->get_customer_id(),
                    'repack_counter', true ) + absint( $_POST['repack_counter']
                )
            );
        }

	}

}
