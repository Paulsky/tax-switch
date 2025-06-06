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

	public function shop_prices_include_tax() {
		return $this->get_original_tax_display() === 'incl';
	}

	public function get_original_tax_display() {
		$current_value = get_option( 'woocommerce_tax_display_shop' );

		return $current_value;
	}

	public function get_option_text( $key, $default ) {
		$text = get_option( $key, $default );

		// Check if WPML is active
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$text = apply_filters( 'wpml_translate_single_string', $text, 'tax-switch-for-woocommerce', $key );
		}

		return esc_html( $text );
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
		//return round($tax_rate, 2);
	}

	public function calculate_alternate_price( $price, $product = null ) {
		$prices_include_tax      = wc_prices_include_tax();
		$shop_prices_include_tax = $this->shop_prices_include_tax();
		$is_vat_exempt           = ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt();

		$calculator = new WC_Product_Simple();
		$calculator->set_price( $price );

		$pricesIncludeTaxFilter = false;

		if ( ! $product ) {
			$product = wc_get_product();
		}

		if ( $product instanceof WC_Product ) {
			$calculator->set_tax_class( $product->get_tax_class() );
			$calculator->set_tax_status( $product->get_tax_status() );
		} else {
			$calculator->set_tax_status( 'taxable' );
		}

		if ( $is_vat_exempt ) {
			WC()->customer->set_is_vat_exempt( false );
			$pre_option_woocommerce_tax_display_shop_filter = 'get_incl_option';
		} else {
			if ( $shop_prices_include_tax ) {
				$pre_option_woocommerce_tax_display_shop_filter = 'get_excl_option';
			} else {
				$pre_option_woocommerce_tax_display_shop_filter = 'get_incl_option';
			}
		}

		// Temporarily change the tax display setting
		add_filter( 'pre_option_woocommerce_tax_display_shop', [
			$this,
			$pre_option_woocommerce_tax_display_shop_filter
		], 1, 3 );

		// Temporarily change the prices_include_tax setting if necessary
		if ( $shop_prices_include_tax !== $prices_include_tax || $is_vat_exempt ) {
			if ( $is_vat_exempt ) {
				$woocommerce_prices_include_tax_filter = 'get_prices_exclude_tax_option';
			} else {
				if ( $prices_include_tax ) {
					$woocommerce_prices_include_tax_filter = 'get_prices_exclude_tax_option';
				} else {
					$woocommerce_prices_include_tax_filter = 'get_prices_include_tax_option';
				}
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

		if ( $is_vat_exempt ) {
			WC()->customer->set_is_vat_exempt( true );
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

	public function is_mail_context() {
		return (
			did_action( 'woocommerce_email_header' ) ||
			did_action( 'woocommerce_email_order_details' )
		);
	}

	/**
	 * @return bool
	 * @since 1.2.0
	 */
	public function is_in_cart_or_checkout() {
		if ( is_cart() || is_checkout() ) {
			return true;
		}
		//in shopping cart widget
		if ( Wdevs_Tax_Switch_Mini_Cart_Context::is_in_mini_cart() ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 * @since 1.2.5
	 */
	public function shop_displays_price_including_tax_by_default() {
		if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
			return false;
		}

		return $this->shop_prices_include_tax();
	}

	/**
	 * @return bool
	 * @since 1.5,2
	 */
	public function should_hide_on_non_wc_pages() {
		return ( get_option( 'wdevs_tax_switch_location', 'all' ) === 'woocommerce' );
	}

	/**
	 * @return bool
	 * @since 1.5,2
	 */
	public function should_hide_on_non_price_pages() {
		return ( get_option( 'wdevs_tax_switch_location', 'all' ) === 'prices' );
	}

	/**
	 * @return bool
	 * @since 1.5,2
	 */
	public function should_hide_on_current_page() {
		if ( $this->should_hide_on_non_wc_pages() ) {
			if ( ! is_woocommerce() && ! is_account_page() ) {
				//Already always disabled on cart and checkout: && ! is_cart() && ! is_checkout()
				return true;
			}
		}

		return false;
	}

	/**
	 * @since 1.5.4
	 *
	 * @return array
	 */
	public function register_script( $handle, $build_dir, $asset_name, $extra_dependencies = [] ) {
		$script_path = plugin_dir_url( dirname( __FILE__ ) ) . 'build/' . $build_dir . '/' . $asset_name . '.js';
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) .  'build/' . $build_dir . '/' . $asset_name .'.asset.php' );

		wp_register_script(
			$handle,
			$script_path,
			array_merge( $script_asset['dependencies'], [ 'wdevs-tax-switch-shared-script' ], $extra_dependencies ),
			$script_asset['version']
		);

		return $script_asset;
	}

	/**
	 * @since 1.5.4
	 *
	 * @return array
	 */
	public function enqueue_script( $handle, $build_dir, $asset_name, $extra_dependencies = [] ) {
		$script_path = plugin_dir_url( dirname( __FILE__ ) ) . 'build/' . $build_dir . '/' . $asset_name . '.js';
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) .  'build/' . $build_dir . '/' . $asset_name .'.asset.php' );

		wp_enqueue_script(
			$handle,
			$script_path,
			array_merge( $script_asset['dependencies'], [ 'wdevs-tax-switch-shared-script' ], $extra_dependencies ),
			$script_asset['version']
		);

		return $script_asset;
	}

}
