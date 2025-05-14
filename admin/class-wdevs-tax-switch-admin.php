<?php

/**
 * The admin-specific functionality of the Wdevs Tax Switch plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/admin
 * @author     Wijnberg Developments
 */
class Wdevs_Tax_Switch_Admin {

	use Wdevs_Tax_Switch_Helper;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Enqueue styles for the block editor.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/wdevs-tax-switch-shared.css', array(), $this->version );

		$original_tax_display = $this->get_original_tax_display();
		$check_price_elements = false;

		wp_localize_script(
			'wdevs-tax-switch-editor-script',
			'wtsEditorObject',
			[
				'originalTaxDisplay' => $original_tax_display,
				'checkPriceElements' => $check_price_elements
			]
		);

		wp_localize_script(
			'wdevs-tax-switch-label-editor-script',
			'wtsEditorObject',
			[
				'originalTaxDisplay' => $original_tax_display,
				'checkPriceElements' => $check_price_elements
			]
		);

	}

	/**
	 * Enqueue scripts for the admin section.
	 *
	 * @since    1.4.0
	 */
	public function enqueue_admin_scripts() {
		if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'wdevs_tax_switch' ) {
			wp_enqueue_script( $this->plugin_name . '-admin-woocommerce', plugin_dir_url( __FILE__ ) . 'js/wdevs-tax-switch-woocommerce.js', [
				'jquery',
				'wc-backbone-modal',
				'wp-color-picker'
			], $this->version, true );

			wp_enqueue_style('wp-color-picker');
		}
	}
}
