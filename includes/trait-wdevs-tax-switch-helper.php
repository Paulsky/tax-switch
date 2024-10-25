<?php

/**
 * The helper functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The helper functionality of the plugin.
 *
 * Defines helper methods for retrieving switch status,
 * checking shop display settings, and getting option text.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
trait Wdevs_Tax_Switch_Helper {

	public function is_shop_display_inclusive() {
		return get_option( 'woocommerce_tax_display_shop' ) === 'incl';
	}

	public function get_option_text( $key, $default ) {
		$text = get_option( $key, $default );

		return esc_html( $text );
	}

	public function get_original_tax_display() {
		$current_value = get_option( 'woocommerce_tax_display_shop' );

		return $current_value;
	}

	public function is_woocommerce_product( $product ) {
		if ( ! isset( $product ) ) {
			return false;
		}

		if ( ! ( $product instanceof WC_Product ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param $product
	 *
	 * @return float|int
	 * @since 1.1.7
	 */
	public function get_product_tax_rate( $product ) {
		if ( ! $product ) {
			return 0;
		}

		$price_excl_tax = wc_get_price_excluding_tax( $product );

		// Prevent division by zero
		if ( $price_excl_tax <= 0 ) {
			return 0;
		}

		$price_incl_tax = wc_get_price_including_tax( $product );

		$tax_rate = ( ( $price_incl_tax - $price_excl_tax ) / $price_excl_tax ) * 100;

		return $tax_rate;
	//	return round($tax_rate, 2);
	}

	public function calculate_alternate_price( $price ) {
		$prices_include_tax   = wc_prices_include_tax();
		$shop_display_is_incl = $this->is_shop_display_inclusive();

		$calculator = new WC_Product_Simple();
		$calculator->set_price( $price );

		$pricesIncludeTaxFilter = false;

		$product = wc_get_product();
		if ( $product ) {
			$calculator->set_tax_class( $product->get_tax_class() );
			$calculator->set_tax_status( $product->get_tax_status() );
		} else {
			$calculator->set_tax_status( 'taxable' );
		}

		if ( $shop_display_is_incl ) {
			$pre_option_woocommerce_tax_display_shop_filter = 'get_excl_option';
		} else {
			$pre_option_woocommerce_tax_display_shop_filter = 'get_incl_option';
		}

		// Temporarily change the tax display setting
		add_filter( 'pre_option_woocommerce_tax_display_shop', [
			$this,
			$pre_option_woocommerce_tax_display_shop_filter
		], 1, 3 );

		// Temporarily change the prices_include_tax setting if necessary
		if ( $shop_display_is_incl !== $prices_include_tax ) {
			if ( $prices_include_tax ) {
				$woocommerce_prices_include_tax_filter = 'get_prices_exclude_tax_option';
			} else {
				$woocommerce_prices_include_tax_filter = 'get_prices_include_tax_option';
			}
			$pricesIncludeTaxFilter = true;
			add_filter( 'woocommerce_prices_include_tax', [ $this, $woocommerce_prices_include_tax_filter ], 99, 1 );
		}

		$price = wc_get_price_to_display( $calculator, [ 'price' => $price ] );

		// Remove our temporary filters
		remove_filter( 'pre_option_woocommerce_tax_display_shop', [
			$this,
			$pre_option_woocommerce_tax_display_shop_filter
		], 1 );

		if ( $pricesIncludeTaxFilter ) {
			remove_filter( 'woocommerce_prices_include_tax', [ $this, $woocommerce_prices_include_tax_filter ], 99 );
		}

		unset( $calculator );

		return $price;
	}

	public function get_prices_include_tax_option( $include_tax ) {
		return true;
	}

	public function get_prices_exclude_tax_option( $exclude_tax ) {
		return false;
	}

	public function get_incl_option( $pre_option, $option, $default_value ) {
		return 'incl';
	}

	public function get_excl_option( $pre_option, $option, $default_value ) {
		return 'excl';
	}
}
