<?php

/**
 * The third party compatibility functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.1.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The third party compatibility functionality of the plugin.
 *
 * Defines the hooks and functions for third party compatibility functionality.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Compatibility {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.0
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
	 * @since    1.1.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_compatibility_scripts() {
		if ( function_exists( 'is_plugin_active' ) ) {
			if ( is_product() ) {
				if ( is_plugin_active( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php' ) ) {
					$wcmpc_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/woocommerce-measurement-price-calculator.asset.php' );
					wp_enqueue_script( 'wdevs-tax-switch-woocommerce-measurement-price-calculator', plugin_dir_url( dirname( __FILE__ ) ) . 'build/woocommerce-measurement-price-calculator.js', $wcmpc_asset['dependencies'], $wcmpc_asset['version'] );
				}
			}
			if ( is_plugin_active( 'tier-pricing-table/tier-pricing-table.php' ) || is_plugin_active( 'tier-pricing-table-premium/tier-pricing-table.php' ) ) {
				$wctpt_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/woocommerce-tiered-price-table.asset.php' );
				wp_enqueue_script( 'wdevs-tax-switch-woocommerce-tiered-price-table', plugin_dir_url( dirname( __FILE__ ) ) . 'build/woocommerce-tiered-price-table.js', $wctpt_asset['dependencies'], $wctpt_asset['version'] );
			}
		}
	}

	public function activate_wc_product_table_compatibility( $element ) {
		$element['use_default_template'] = true;

		return $element;
	}

	public function add_prices_to_variation( $variation_data, $product, $variation ) {
		$variation_data['price_incl_vat'] = $variation->get_price_including_tax();
		$variation_data['price_excl_vat'] = $variation->get_price_excluding_tax();

		return $variation_data;
	}
}
