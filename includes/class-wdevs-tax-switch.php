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
	 * Ajax Manager class
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      object $ajax_manager The Ajax Manager class
	 */
	protected $ajax_manager;

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
		if ( defined( 'WOO_TAX_SWITCH_VERSION' ) ) {
			$this->version = WOO_TAX_SWITCH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wdevs-tax-switch';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_block_hooks();
		$this->define_ajax_hooks();
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
	 * - Wdevs_Tax_Switch_Block. Defines all hooks for the block functionality.
	 * - Wdevs_Tax_Switch_Ajax. Handles AJAX functionality.
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
		 * The class responsible for defining all actions that occur in the block-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'block/class-wdevs-tax-switch-block.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wdevs-tax-switch-public.php';

		/**
		 * The class responsible for AJAX request form fields.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-ajax.php';

		$this->loader       = new Wdevs_Tax_Switch_Loader();
		$this->ajax_manager = new Wdevs_Tax_Switch_Ajax();

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

		$plugin_admin = new Wdevs_Tax_Switch_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );

		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'add_settings_tab', 50 );
		$this->loader->add_action( 'woocommerce_settings_tabs_wdevs_tax_switch', $plugin_admin, 'settings_tab' );
		$this->loader->add_action( 'woocommerce_update_options_wdevs_tax_switch', $plugin_admin, 'update_settings' );

		$this->loader->add_filter( 'option_woocommerce_tax_display_shop', $plugin_admin, 'filter_woocommerce_tax_display_shop_option', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wdevs_Tax_Switch_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_filter( 'woocommerce_get_price_html', $plugin_public, 'get_price_html', 10, 2 );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the block functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_block_hooks() {

		$plugin_block = new Wdevs_Tax_Switch_Block( $this->get_plugin_name(), $this->get_version(), $this->ajax_manager );

		$this->loader->add_action( 'init', $plugin_block, 'init_block' );
		$this->loader->add_action( 'init', $plugin_block, 'register_shortcode' );

	}

	/**
	 * Register all of the hooks related to the AJAX functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_ajax_hooks() {
		$this->loader->add_action( 'wp_ajax_' . $this->ajax_manager->getFormAction(), $this->ajax_manager, $this->ajax_manager->getFormAction() . '_endpoint' );
		$this->loader->add_action( 'wp_ajax_nopriv_' . $this->ajax_manager->getFormAction(), $this->ajax_manager, $this->ajax_manager->getFormAction() . '_endpoint' );
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

}
