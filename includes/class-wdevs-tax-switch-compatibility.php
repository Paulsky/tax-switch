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

	use Wdevs_Tax_Switch_Helper, Wdevs_Tax_Switch_Plugins, Wdevs_Tax_Switch_Display;

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
		if ( is_product() ) {
			// WooCommerce Measurement Price Calculator
			if ( $this->is_plugin_active( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php' ) ) {
				$wcmpc_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/woocommerce-measurement-price-calculator.asset.php' );
				wp_enqueue_script(
					'wdevs-tax-switch-woocommerce-measurement-price-calculator',
					plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/woocommerce-measurement-price-calculator.js',
					$wcmpc_asset['dependencies'],
					$wcmpc_asset['version']
				);
			}

			$tax_rate = $this->get_product_tax_rate( wc_get_product() );

			// YITH WooCommerce Product Add-Ons (both free and premium)
			if ( $this->is_any_plugin_active( [
				'yith-woocommerce-product-add-ons/init.php',
				'yith-woocommerce-advanced-product-options-premium/init.php'
			] ) ) {
				$ywpado_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/yith-woocommerce-product-add-ons.asset.php' );
				wp_enqueue_script(
					'wdevs-tax-switch-yith-woocommerce-product-add-ons',
					plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/yith-woocommerce-product-add-ons.js',
					array_merge( $ywpado_asset['dependencies'], [ 'yith_wapo_front' ] ),
					$ywpado_asset['version']
				);

				wp_localize_script(
					'wdevs-tax-switch-yith-woocommerce-product-add-ons',
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// WooCommerce Product Addons
			if ( $this->is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
				$wpado_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/woocommerce-product-addons.asset.php' );
				wp_enqueue_script(
					'wdevs-tax-switch-woocommerce-product-addons',
					plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/woocommerce-product-addons.js',
					array_merge( $wpado_asset['dependencies'], [ 'accounting' ] ),
					$wpado_asset['version']
				);
				wp_localize_script(
					'wdevs-tax-switch-woocommerce-product-addons',
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// Advanced Product Fields Pro for WooCommerce
			if ( $this->is_plugin_active( 'advanced-product-fields-for-woocommerce-pro/advanced-product-fields-for-woocommerce-pro.php' ) ) {
				$apffw_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/advanced-product-fields-for-woocommerce.asset.php' );
				wp_enqueue_script(
					'wdevs-tax-switch-advanced-product-fields-for-woocommerce',
					plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/advanced-product-fields-for-woocommerce.js',
					array_merge( $apffw_asset['dependencies'], [ 'wapf-frontend', 'accounting' ] ),
					$apffw_asset['version']
				);
			}
			// Woocommerce Quantity Manager
			if ( $this->is_plugin_active( 'woocommerce-quantity-manager-pro/woocommerce-quantity-manager-pro.php' ) ) {
				$wqm_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/woocommerce-quantity-manager.asset.php' );
				wp_enqueue_script(
					'wdevs-tax-switch-woocommerce-quantity-manager',
					plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/woocommerce-quantity-manager.js',
					array_merge( $wqm_asset['dependencies'], [ 'accounting' ] ), //'wqm-frontend',
					$wqm_asset['version']
				);

				wp_localize_script(
					'wdevs-tax-switch-woocommerce-quantity-manager',
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			if ( $this->is_plugin_active( 'variation-price-display/variation-price-display.php' ) ) {
				$vpdrfwc_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/variation-price-display-range-for-wc.asset.php' );
				wp_enqueue_script(
					'wdevs-tax-switch-woocommerce-quantity-manager',
					plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/variation-price-display-range-for-wc.js',
					$vpdrfwc_asset['dependencies'],
					$vpdrfwc_asset['version']
				);
			}
		}

		// Tier Pricing Table (both free and premium)
		if ( $this->is_any_plugin_active( [
			'tier-pricing-table/tier-pricing-table.php',
			'tier-pricing-table-premium/tier-pricing-table.php'
		] ) ) {
			$wctpt_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/switch/woocommerce-tiered-price-table.asset.php' );
			wp_enqueue_script(
				'wdevs-tax-switch-woocommerce-tiered-price-table',
				plugin_dir_url( dirname( __FILE__ ) ) . 'build/switch/woocommerce-tiered-price-table.js',
				$wctpt_asset['dependencies'],
				$wctpt_asset['version']
			);
		}
	}

	public function activate_wc_product_table_compatibility( $element ) {
		if ( isset( $element ) && isset( $element['type'] ) && $element['type'] === 'price' ) {
			$element['use_default_template'] = true;
		}

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

	/**
	 * Adds the alternate price to the Advanced Product Fields Pro hints
	 * @since 1.4.1
	 */
	public function render_wapf_pricing_hint( $original_output, $product, $amount, $type, $field = null, $option = null ) {
		if ( $this->is_in_cart_or_checkout() ) {
			return $original_output;
		}

		if ( ! class_exists( 'SW_WAPF_PRO\Includes\Classes\Helper' ) ) {
			return $original_output;
		}

		$alternate_amount = $this->calculate_alternate_price( $amount, $product );

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'wapf/html/pricing_hint', [ $this, 'render_wapf_pricing_hint' ], 10 );

		//get the pricing format for the alternate amount
		$alternate_hint = \SW_WAPF_PRO\Includes\Classes\Helper::format_pricing_hint(
			$type,
			$alternate_amount,
			$product,
			'shop',
			$field,
			$option
		);

		//Re-enable this filter and function
		add_filter( 'wapf/html/pricing_hint', [ $this, 'render_wapf_pricing_hint' ], 10, 6 );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $original_output, $alternate_hint, $shop_prices_include_tax );
	}

}
