<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ouun.io
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
		$this->version = $version;
		$this->meta_name = $meta_name;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/repack-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/repack-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Get RePack order meta
	 *
	 * @param $order
	 *
	 * @return bool
	 */
	public function is_repack_order( $order ) {
		return get_post_meta( $order->id, $this->meta_name, true );
	}


	public function get_order_repack_decision( $order ) {
	    if( $this->is_repack_order( $order ) ) {
	        return __( 'Shipping with reused packaging preferred!', 'stage' );
        } else {
		    return $this->is_repack_order( $order );
        }
	}


	public function add_order_details( $order ) {

		if( $this->is_repack_order( $order ) ) { ?>
            <div class="clear"></div>
            <h3><?php _e( 'Reused Packaging', 'repack' ); ?></h3>
            <div class="repack">
                <p class="form-field form-field-wide">
                    <strong>
                        <?php echo $this->get_order_repack_decision( $order ); ?>
                    </strong>
                </p>
            </div>

		<?php }
	}

	public function add_shipping_field( $fields ) {

		$fields['repack'] = [
			'label' => __( 'Reused Packaging', 'repack' ),
			'type' => 'checkbox',
			'show' => false,
            'value' => 'test',
		];

		return $fields;

	}

	/**
	 * Add a custom field to WC emails
	 */
	public function add_field_to_emails( $fields, $sent_to_admin, $order ) {

	    if($this->is_repack_order( $order )) {
		    $fields[ $this->meta_name ] = array(
			    'label' => __( 'Reused Packaging', 'repack' ),
			    'value' => __( 'Thanks for helping us save resources! We will prefer an used shipping packaging to a new one, if available.', 'repack' ),
		    );
	    }

		return $fields;
	}

}
