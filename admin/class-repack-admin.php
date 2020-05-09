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
	}


	/**
	 * Get RePack order meta
	 *
	 * @param $order
	 *
	 * @return bool
	 */
	public function is_repack_order( $order ) {
		return wc_string_to_bool( get_post_meta( $order->id, '_' . $this->meta_name, true ) );
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
			return __( 'Shipping with reused packaging preferred!', 'repack' );
		} else {
			return $this->is_repack_order( $order );
		}
	}


	/**
	 * RePack section in order overwiew
	 *
	 * @param $order
	 */
	public function add_order_details( $order ) {
		if ( $this->is_repack_order( $order ) ) { ?>
			<div class="clear"></div>
			<h3><?php esc_html_e( 'Reuse Packaging', 'repack' ); ?></h3>
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
	 * Add RePack field to order shipping details
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function add_shipping_field( $fields ) {

		$fields['repack'] = array(
			'label'   => __( 'Reuse Packaging', 'repack' ),
			'type'    => 'select',
			'class'   => 'js_field-repack select short',
			'options' => array(
				''    => __( 'Please choose', 'repack' ),
				'yes' => __( 'Yes', 'repack' ),
				'no'  => __( 'No', 'repack' ),
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
				'label' => apply_filters( 'repack_email_label', __( 'Reused Packaging', 'repack' ) ),
				'value' => apply_filters( 'repack_email_text', __( 'Thanks for helping us save resources! We will prefer an used shipping packaging to a new one, if available.', 'repack' ) ),
			);
		}

		return $fields;
	}
}
