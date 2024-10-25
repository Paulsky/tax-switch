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

	use Wdevs_Tax_Switch_Helper;

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
		$active_plugins = wp_get_active_and_valid_plugins();
		//$active_network_plugins = wp_get_active_network_plugins();
		if ( is_product() ) {
			$wmpc_plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php';
			if ( in_array( $wmpc_plugin_path, $active_plugins ) ) {
				$wcmpc_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/woocommerce-measurement-price-calculator.asset.php' );
				wp_enqueue_script( 'wdevs-tax-switch-woocommerce-measurement-price-calculator', plugin_dir_url( dirname( __FILE__ ) ) . 'build/woocommerce-measurement-price-calculator.js', $wcmpc_asset['dependencies'], $wcmpc_asset['version'] );
			}
			$ywpado_plugin_path  = trailingslashit( WP_PLUGIN_DIR ) . 'yith-woocommerce-product-add-ons/init.php';
			$ywpadop_plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'yith-woocommerce-advanced-product-options-premium/init.php';
			if ( in_array( $ywpado_plugin_path, $active_plugins ) || in_array( $ywpadop_plugin_path, $active_plugins ) ) {
				$ywpado_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/yith-woocommerce-product-add-ons.asset.php' );
				wp_enqueue_script( 'wdevs-tax-switch-yith-woocommerce-product-add-ons', plugin_dir_url( dirname( __FILE__ ) ) . 'build/yith-woocommerce-product-add-ons.js', array_merge( $ywpado_asset['dependencies'], [ 'yith_wapo_front' ] ), $ywpado_asset['version'] );

				// Localize de script met extra data
				wp_localize_script(
					'wdevs-tax-switch-yith-woocommerce-product-add-ons',
					'wtsCompatibilityObject',
					[
						'baseTaxRate' => $this->get_product_tax_rate( wc_get_product() )
					]
				);
			}
		}
		$tpt_plugin_path  = trailingslashit( WP_PLUGIN_DIR ) . 'tier-pricing-table/tier-pricing-table.php';
		$tptp_plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'tier-pricing-table-premium/tier-pricing-table.php';
		if ( in_array( $tpt_plugin_path, $active_plugins ) || in_array( $tptp_plugin_path, $active_plugins ) ) {
			$wctpt_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/woocommerce-tiered-price-table.asset.php' );
			wp_enqueue_script( 'wdevs-tax-switch-woocommerce-tiered-price-table', plugin_dir_url( dirname( __FILE__ ) ) . 'build/woocommerce-tiered-price-table.js', $wctpt_asset['dependencies'], $wctpt_asset['version'] );
		}

	}

	public function activate_wc_product_table_compatibility( $element ) {
		$element['use_default_template'] = true;

		return $element;
	}

	/**
	 * Includes these properties in the AJAX response for a variation
	 */
	public function add_prices_to_variation( $variation_data, $product, $variation ) {
		$variation_data['price_incl_vat'] = wc_get_price_including_tax( $variation );
		$variation_data['price_excl_vat'] = wc_get_price_excluding_tax( $variation );

		return $variation_data;
	}

	/**
	 * Includes these properties in the AJAX response for a variation
	 * @since 1.1.7
	 */
	public function add_tax_rate_to_variation( $variation_data, $product, $variation ) {
		$variation_data['tax_rate'] = $this->get_product_tax_rate( $variation );

		return $variation_data;
	}

}
