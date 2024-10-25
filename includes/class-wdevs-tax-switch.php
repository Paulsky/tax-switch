<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks,
 * public-facing site hooks, and block-related functionality.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wdevs_Tax_Switch_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site, block functionality, and AJAX hooks.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WDEVS_TAX_SWITCH_VERSION' ) ) {
			$this->version = WDEVS_TAX_SWITCH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wdevs-tax-switch';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_woocommerce_hooks();
		$this->define_block_hooks();
		$this->define_compatibility_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wdevs_Tax_Switch_Loader. Orchestrates the hooks of the plugin.
	 * - Wdevs_Tax_Switch_i18n. Defines internationalization functionality.
	 * - Wdevs_Tax_Switch_Admin. Defines all hooks for the admin area.
	 * - Wdevs_Tax_Switch_Public. Defines all hooks for the public side of the site.
	 * - Wdevs_Tax_Switch_WooCommerce. Defines all hooks for the WooCommerce functionality.
	 * - Wdevs_Tax_Switch_Block. Defines all hooks for the block functionality.
	 * - Wdevs_Tax_Switch_Compatibility. Defines all functions for third party compatibility.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The trait with helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/trait-wdevs-tax-switch-helper.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wdevs-tax-switch-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wdevs-tax-switch-public.php';

		/**
		 * The class responsible for defining the WooCommerce functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-woocommerce.php';

		/**
		 * The class responsible for defining all actions that occur in the block-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-block.php';

		/**
		 * The class responsible for defining all functionality from adding compatibility with third party code
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-compatibility.php';


		$this->loader = new Wdevs_Tax_Switch_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wdevs_Tax_Switch_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wdevs_Tax_Switch_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		if ( is_admin() ) {
			$plugin_admin = new Wdevs_Tax_Switch_Admin( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		if ( ! $this->is_admin_request() || $this->is_post_editor() ) {
			$plugin_public = new Wdevs_Tax_Switch_Public( $this->get_plugin_name(), $this->get_version() );
			if ( ! $this->is_doing_ajax() ) {
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			}
			$this->loader->add_filter( 'wc_price', $plugin_public, 'wrap_wc_price', PHP_INT_MAX, 5 );
			$this->loader->add_filter( 'woocommerce_get_price_html', $plugin_public, 'get_price_html', PHP_INT_MIN, 2 );
		}
	}

	/**
	 * Register all of the hooks related to the Woocommerce functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_woocommerce_hooks() {
		$plugin_woocommerce = new Wdevs_Tax_Switch_Woocommerce( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'before_woocommerce_init', $plugin_woocommerce, 'declare_compatibility' );
		if ( is_admin() ) {
			$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_woocommerce, 'add_settings_tab', 50 );
			$this->loader->add_action( 'woocommerce_settings_tabs_wdevs_tax_switch', $plugin_woocommerce, 'settings_tab' );
			$this->loader->add_action( 'woocommerce_update_options_wdevs_tax_switch', $plugin_woocommerce, 'update_settings' );
		}
	}

	/**
	 * Register all of the hooks related to the block functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_block_hooks() {

		$plugin_block = new Wdevs_Tax_Switch_Block( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_block, 'init_block' );
		$this->loader->add_action( 'init', $plugin_block, 'register_shortcode' );

	}

	/**
	 * Register all the hooks related to the third party functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_compatibility_hooks() {
		if ( ! $this->is_admin_request() ) {
			$plugin_compatibility = new Wdevs_Tax_Switch_Compatibility( $this->get_plugin_name(), $this->get_version() );
			if ( ! $this->is_doing_ajax() ) {
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_compatibility, 'enqueue_compatibility_scripts' );
			}
			//wc product table compatibility
			$this->loader->add_filter( 'wcpt_element', $plugin_compatibility, 'activate_wc_product_table_compatibility', 10, 1 );

			//see https://woocommerce.com/document/create-a-plugin/
			$active_plugins = wp_get_active_and_valid_plugins();
			//$active_network_plugins = wp_get_active_network_plugins();

			//TODO: move check to somewhere else?
			$wmpc_plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php';
			$ywpado           = trailingslashit( WP_PLUGIN_DIR ) . 'yith-woocommerce-product-add-ons/init.php';
			$ywpadop          = trailingslashit( WP_PLUGIN_DIR ) . 'yith-woocommerce-advanced-product-options-premium/init.php';

			if ( in_array( $wmpc_plugin_path, $active_plugins ) ) {
				$this->loader->add_filter( 'woocommerce_available_variation', $plugin_compatibility, 'add_prices_to_variation', 10, 3 );
			}
			if ( in_array( $ywpado, $active_plugins ) || in_array( $ywpadop, $active_plugins ) ) {
				$this->loader->add_filter( 'woocommerce_available_variation', $plugin_compatibility, 'add_tax_rate_to_variation', 10, 3 );
			}

		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wdevs_Tax_Switch_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Check for enabling the switch in the editor
	 *
	 * @return bool
	 * @since     1.0.0
	 */
	private function is_post_editor() {

		if ( ! is_admin() ) {
			return false;
		}

		global $pagenow;

		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : null;
		if ( $post_id ) {
			$post_type = get_post_type( $post_id );
		} else {
			$post_type = get_post_type();
		}

		if ( $post_type == 'post' || $post_type == 'page' ) {
			return true;
		}

		//In older versions of Woocommerce, the URL is like this:
		//wp-admin/edit.php?post_type=shop_order
		//wp-admin/post.php?post=orderId&action=edit

		//but new versions are like this (will not enter this condition)
		//wp-admin/admin.php?page=wc-orders
		//wp-admin/admin.php?page=wc-orders&action=edit&id=orderId
		if ( $post_type == 'shop_order' ) {
			return false;
		}

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if this is a request at the backend.
	 *
	 * @link https://florianbrinkmann.com/en/wordpress-backend-request-3815/
	 * @return bool true if is admin request, otherwise false.
	 * @since 1.1.5
	 */
	private function is_admin_request() {
		/**
		 * Get current URL.
		 *
		 * @link https://wordpress.stackexchange.com/a/126534
		 */
		$current_url = home_url( add_query_arg( null, null ) );

		/**
		 * Get admin URL and referrer.
		 *
		 * @link https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/pluggable.php#L1076
		 */
		$admin_url = strtolower( admin_url() );
		$referrer  = strtolower( wp_get_referer() );

		/**
		 * Check if this is an admin request. If true, it
		 * could also be an AJAX request from the frontend.
		 */
		if ( 0 === strpos( $current_url, $admin_url ) ) {
			/**
			 * Check if the user comes from an admin page.
			 */
			if ( 0 === strpos( $referrer, $admin_url ) ) {
				return true;
			} else {
				return ! $this->is_doing_ajax();
			}
		} else {
			return false;
		}
	}

	/**
	 * Check for AJAX requests.
	 *
	 * @link https://gist.github.com/zitrusblau/58124d4b2c56d06b070573a99f33b9ed#file-lazy-load-responsive-images-php-L193
	 * @since 1.1.5
	 */
	private function is_doing_ajax() {

		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		} else {
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		}
	}

}
