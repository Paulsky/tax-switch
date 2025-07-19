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
				$wmpc_handle = 'wdevs-tax-switch-woocommerce-measurement-price-calculator';
				$this->enqueue_script($wmpc_handle, 'switch', 'woocommerce-measurement-price-calculator');
			}

			$tax_rate = $this->get_product_tax_rate( wc_get_product() );

			// YITH WooCommerce Product Add-Ons (both free and premium)
			if ( $this->is_any_plugin_active( [
				'yith-woocommerce-product-add-ons/init.php',
				'yith-woocommerce-advanced-product-options-premium/init.php'
			] ) ) {
				$ywpado_handle = 'wdevs-tax-switch-yith-woocommerce-product-add-ons';
				$ywpado_asset = $this->enqueue_script($ywpado_handle, 'switch', 'yith-woocommerce-product-add-ons', [ 'yith_wapo_front' ]);

				wp_localize_script(
					$ywpado_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// WooCommerce Product Addons
			if ( $this->is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
				$wpado_handle = 'wdevs-tax-switch-woocommerce-product-addons';
				$wpado_asset = $this->enqueue_script($wpado_handle, 'switch', 'woocommerce-product-addons', [ 'accounting' ]);

				wp_localize_script(
					$wpado_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// Advanced Product Fields Pro for WooCommerce
			if ( $this->is_plugin_active( 'advanced-product-fields-for-woocommerce-pro/advanced-product-fields-for-woocommerce-pro.php' ) ) {
				$apffw_handle = 'wdevs-tax-switch-advanced-product-fields-for-woocommerce';
				$apffw_asset = $this->enqueue_script($apffw_handle, 'switch', 'advanced-product-fields-for-woocommerce', ['wapf-frontend', 'accounting' ]);
			}

			// Woocommerce Quantity Manager
			if ( $this->is_plugin_active( 'woocommerce-quantity-manager-pro/woocommerce-quantity-manager-pro.php' ) ) {
				$wqm_handle = 'wdevs-tax-switch-woocommerce-quantity-manager';
				$wqm_asset = $this->enqueue_script($wqm_handle, 'switch', 'woocommerce-quantity-manager', [ 'accounting' ]);

				wp_localize_script(
					$wqm_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// Product Extras for Woocommerce (Woocommerce Product Add-Ons Ultimate)
			if ( $this->is_plugin_active( 'product-extras-for-woocommerce/product-extras-for-woocommerce.php' ) ) {
				$pewc_handle = 'wdevs-tax-switch-woocommerce-quantity-manager';
				$pewc_asset = $this->enqueue_script($pewc_handle, 'switch', 'product-extras-for-woocommerce', [ 'accounting' ]); //'pewc-script',breaks things, but I wonder if that correct....

				wp_localize_script(
					$pewc_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			//Kapee theme
			if($this->is_theme_active('Kapee')){
				$kapee_handle = 'wdevs-tax-switch-kapee-theme';
				$kapee_asset = $this->enqueue_script($kapee_handle, 'switch', 'kapee-theme', [ 'accounting', 'kapee-script' ]);

				wp_localize_script(
					$kapee_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}
		}

		// Tier Pricing Table (both free and premium)
		if ( $this->is_any_plugin_active( [
			'tier-pricing-table/tier-pricing-table.php',
			'tier-pricing-table-premium/tier-pricing-table.php'
		] ) ) {
			$wctpt_handle = 'wdevs-tax-switch-woocommerce-tiered-price-table';
			$wctpt_asset = $this->enqueue_script($wctpt_handle, 'switch', 'woocommerce-tiered-price-table');
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

	/**
	 * Adds the alternate price to the Product Extras for Woocommerce (WooCommerce Product Add-Ons Ultimate) price field
	 * @since 1.5.5
	 */
	public function render_pewc_price_field($original_output, $item, $product, $price=false){
		if(!$price){
			return $original_output;
		}

		$alternate_amount = $this->calculate_alternate_price( $price, $product );

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'pewc_field_formatted_price', [ $this, 'render_pewc_price_field' ], PHP_INT_MAX );

		//get the pricing format for the alternate amount
		$alternate_field = apply_filters(
			'pewc_field_formatted_price',
			pewc_wc_format_price( $alternate_amount ),
			$item,
			$product,
			$alternate_amount
		);

		//Re-enable this filter and function
		add_filter( 'pewc_field_formatted_price', [ $this, 'render_pewc_price_field' ], PHP_INT_MAX, 4 );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $original_output, $alternate_field, $shop_prices_include_tax );
	}

}
