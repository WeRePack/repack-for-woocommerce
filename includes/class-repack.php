<?php

/**
* The file that defines the core plugin class
*
* A class definition that includes attributes and functions used across both the
* public-facing side of the site and the admin area.
*
* @link       https://WeRePack.org
* @since      1.0.0
*
* @package    Repack
* @subpackage Repack/includes
*/

/**
* The WeRePack core plugin class.
*
* This is used to define internationalization, admin-specific hooks, and
* public-facing site hooks.
*
* Also maintains the unique identifier of this plugin as well as the current
* version of the plugin.
*
* @since      1.0.0
* @package    Repack
* @subpackage Repack/includes
* @author     Philipp Wellmer <philipp@ouun.io>
*/
class Repack {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Repack_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The post meta name.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $meta_name    The post meta name.
	 */
	protected $meta_name;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'REPACK_VERSION' ) ) {
			$this->version = REPACK_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'repack';
		$this->meta_name   = 'shipping_repack';

		$this->load_dependencies();
		$this->set_locale();
		$this->set_settings();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Repack_Loader. Orchestrates the hooks of the plugin.
	 * - Repack_i18n. Defines internationalization functionality.
	 * - Repack_Admin. Defines all hooks for the admin area.
	 * - Repack_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once REPACK_PLUGIN_DIR . 'includes/class-repack-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once REPACK_PLUGIN_DIR . 'includes/class-repack-localization.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once REPACK_PLUGIN_DIR . 'public/class-repack-template-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once REPACK_PLUGIN_DIR . 'admin/class-repack-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once REPACK_PLUGIN_DIR . 'public/class-repack-public.php';

		$this->loader = new Repack_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Repack_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Repack_Localization();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Define the settings for this plugin, each is overridable via hooks.
	 * Keys are used as option key in DB
	 *
	 * @since    1.2.0
	 * @access   public
	 *
	 * @return array
	 */
	public function get_settings_options() {
		return array(
			// Consent Box
			'repack_checkout_consent_position'  => 'woocommerce_after_order_notes',
			'repack_consent_field_firework'     => true,
			'repack_consent_field_label'        => __( 'Yes, please reuse packaging if available.', 'repack-for-woocommerce' ),
			'repack_consent_field_description'  => sprintf(
			/* translators: %s: WeRePack website link */
				__( 'With your consent we prefer already used shipping packaging. Help us protect the environment and learn more about the initiative on %s.', 'repack-for-woocommerce' ),
				'<a href="https://werepack.org/" target="_blank">WeRePack.org</a>'
			),

			// Coupon
			'repack_coupon_name'                => 'WeRePack',
			'repack_coupon_applied_notice_text' => sprintf(
			/* translators: %s: Thank You */
				__( 'Your discount for reusing packaging has been applied. %s', 'repack-for-woocommerce' ),
				'<strong>' . __( 'Thank you!', 'repack-for-woocommerce' ) . '</strong>'
			),
			'repack_coupon_removed_notice_text' => sprintf(
				__( 'Your discount for reusing packaging has been removed.', 'repack-for-woocommerce' )
			),
		);
	}

	/**
	 * Set the settings for this plugin, each is overridable via hooks.
	 *
	 * @since    1.2.0
	 * @access   private
	 */
	private function set_settings() {
		foreach ( $this->get_settings_options() as $setting => $default ) {
			add_filter(
				$setting,
				function () use ( $setting, $default ) {
					$option = get_option( $setting, $default );
					return ! empty( $option ) ? $option : $default;
				},
				5
			);
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Repack_Admin( $this->get_plugin_name(), $this->get_version(), $this->meta_name );

		$this->loader->add_action( 'woocommerce_admin_order_data_after_shipping_address', $plugin_admin, 'add_order_details' );
		$this->loader->add_filter( 'woocommerce_admin_order_preview_get_order_details', $plugin_admin, 'preview_add_order_details', 10, 2 );
		$this->loader->add_filter( 'woocommerce_admin_shipping_fields', $plugin_admin, 'add_shipping_field' );

		$this->loader->add_filter( 'woocommerce_email_order_meta_fields', $plugin_admin, 'add_field_to_emails', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Repack_Public( $this->get_plugin_name(), $this->get_version(), $this->meta_name );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Register WeRePack Shortcodes
		$this->loader->add_action( 'init', $plugin_public, 'repack_shortcodes' );

		// Set the Coupon name for further usage
		$this->loader->add_action( 'init', $plugin_public, 'set_repack_coupon_name' );

		// Add WeRePack field to checkout and user shipping settings
		$this->loader->add_filter( 'woocommerce_shipping_fields', $plugin_public, 'add_shipping_repack_field', 20, 1 );
		$this->loader->add_filter( 'woocommerce_checkout_get_value', $plugin_public, 'repack_checkout_consent_value', 10, 2 );
		$this->loader->add_action(
			apply_filters(
				'repack_checkout_consent_position',
				'woocommerce_after_order_notes'
			),
			$plugin_public,
			'add_checkout_repack_field',
			20,
			1
		);

		// Apply Coupon
		$this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'repack_checkout_apply_coupon', 20, 1 );

		// AJAX Apply Coupon
		$this->loader->add_action( 'wp_ajax_nopriv_update_order_review', $plugin_public, 'repack_ajax_apply_coupon' );
		$this->loader->add_action( 'wp_ajax_update_order_review', $plugin_public, 'repack_ajax_apply_coupon' );

		// Save order and user meta
		$this->loader->add_action( 'woocommerce_checkout_create_order', $plugin_public, 'repack_save_order', 20, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		if ( $this->is_wc_active() ) {
			$this->loader->run();
		}
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Repack_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Check for WooCommerce
	 *
	 * @return bool
	 */
	public function is_wc_active() {
		return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
	}
}
