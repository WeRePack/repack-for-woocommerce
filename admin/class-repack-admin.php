<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://WeRePack.org
 * @since      1.0.0
 *
 * @package    Repack
 * @subpackage Repack/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Repack
 * @subpackage Repack/admin
 * @author     Philipp Wellmer <philipp@ouun.io>
 */
class Repack_Admin {


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

		/**
		 * The WooCommerce Settings Page.
		 */
		require_once REPACK_PLUGIN_DIR . 'admin/partials/repack-admin-settings.php';

		/**
		 * The class responsible for running telemetry.
		 */
		require_once REPACK_PLUGIN_DIR . 'admin/partials/class-repack-telemetry.php';

		/**
		 * Init Telemetry
		 */
		new Repack_Telemetry();
	}


	/**
	 * Get WeRePack order meta
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 */
	public function is_repack_order( $order ) {
		return wc_string_to_bool( get_post_meta( $order->get_id(), '_' . $this->meta_name, true ) );
	}


	/**
	 * Admin notice about resuing packaging
	 *
	 * @param $order
	 *
	 * @return bool|string|void
	 */
	public function get_order_repack_decision( $order ) {
		if ( $this->is_repack_order( $order ) ) {
			return __( 'Shipping with reused packaging preferred!', 'repack-for-woocommerce' );
		} else {
			return $this->is_repack_order( $order );
		}
	}


	/**
	 * WeRePack section in single order overview
	 *
	 * @param WC_Order $order
	 */
	public function add_order_details( $order ) {
		if ( $this->is_repack_order( $order ) ) { ?>
			<div class="clear"></div>
			<h3><?php esc_html_e( 'Reuse Packaging', 'repack-for-woocommerce' ); ?></h3>
			<div class="repack">
				<p class="form-field form-field-wide">
					<strong>
						<?php echo esc_html( $this->get_order_repack_decision( $order ) ); ?>
					</strong>
				</p>
			</div>

			<?php
		}
	}

	/**
	 * WeRePack section in order preview
	 *
	 * @param array $details
	 * @param WC_Order $order
	 * @return array
	 */
	public function preview_add_order_details( $details, $order ) {

		if ( $this->is_repack_order( $order ) ) {
			$details['shipping_via'] .= ' (' . esc_html( $this->get_order_repack_decision( $order ) ) . ')';
		}

		return $details;
	}

	/**
	 * Add WeRePack field to order shipping details
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function add_shipping_field( $fields ) {

		$fields['repack'] = array(
			'label'   => __( 'Reuse Packaging', 'repack-for-woocommerce' ),
			'type'    => 'select',
			'class'   => 'js_field-repack select short',
			'options' => array(
				''    => __( 'Please choose', 'repack-for-woocommerce' ),
				'yes' => __( 'Yes', 'repack-for-woocommerce' ),
				'no'  => __( 'No', 'repack-for-woocommerce' ),
			),
			'show'    => false,
		);

		return $fields;
	}

	/**
	 * Add a custom field to WC emails
	 *
	 * @param $fields
	 * @param $sent_to_admin
	 * @param $order
	 *
	 * @return mixed
	 */
	public function add_field_to_emails( $fields, $sent_to_admin, $order ) {

		if ( $this->is_repack_order( $order ) ) {
			$fields[ $this->meta_name ] = array(
				'label' => apply_filters( 'repack_email_label', __( 'Reused Packaging', 'repack-for-woocommerce' ) ),
				'value' => apply_filters( 'repack_email_text', __( 'Thanks for helping us save resources! We will prefer an used shipping packaging to a new one, if available.', 'repack-for-woocommerce' ) ),
			);
		}

		return $fields;
	}
}
