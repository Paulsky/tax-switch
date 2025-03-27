<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/public
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Public {

	use Wdevs_Tax_Switch_Helper, Wdevs_Tax_Switch_Display;

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/wdevs-tax-switch-shared.css', array(), $this->version );
		wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'css/wdevs-tax-switch-public.css', array(), $this->version );
	}

	public function wrap_wc_price( $return, $price, $args, $unformatted_price, $original_price ) {

		if ( $this->is_in_cart_or_checkout() ) {
			return $return;
		}

		if ( $this->is_mail_context() ) {
			return $return;
		}

		if ( $this->should_be_disabled_in_action() ) {
			return $return;
		}

		//already wrapped?
//		if ( str_contains( $return, 'wts-price-wrapper' ) ) {
//			return $return;
//		}

		if ( empty( $unformatted_price ) ) {
			return $return;
		}

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'wc_price', [ $this, 'wrap_wc_price' ], PHP_INT_MAX );

		$alternate_price = wc_price( $this->calculate_alternate_price( $unformatted_price ) );

		//Re-enable this filter and function
		add_filter( 'wc_price', [ $this, 'wrap_wc_price' ], PHP_INT_MAX, 5 );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $return, $alternate_price, $shop_prices_include_tax );
	}

	/**
	 * Add 'excl. vat' or 'incl. vat' text
	 *
	 * @param $price_html
	 * @param $product
	 *
	 * @return mixed|string
	 */
	public function get_price_html( $price_html, $product ) {

		if ( empty( trim( $price_html ) ) ) {
			return $price_html;
		}

		if ( ! str_contains( $price_html, 'amount' ) ) {
			return $price_html;
		}

		if ( ! $this->is_woocommerce_product( $product ) ) {
			return $price_html;
		}

		//Temporarily disable this filter and function to prevent infinite loop
		//Is this still needed?
		//remove_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], PHP_INT_MIN );

		//Execute all others filters
		//Causes duplications
		//$price_html = apply_filters( 'woocommerce_get_price_html', $price_html, $product );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Get VAT text options
		$incl_vat_text = $this->get_option_text( 'wdevs_tax_switch_incl_vat', __( 'Incl. VAT', 'tax-switch-for-woocommerce' ) );
		$excl_vat_text = $this->get_option_text( 'wdevs_tax_switch_excl_vat', __( 'Excl. VAT', 'tax-switch-for-woocommerce' ) );

		if ( $shop_prices_include_tax ) {
			$vat_text           = $incl_vat_text;
			$alternate_vat_text = $excl_vat_text;
		} else {
			$vat_text           = $excl_vat_text;
			$alternate_vat_text = $incl_vat_text;
		}

		//Re-enable this filter and function
		//Is this still needed?
		//add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], PHP_INT_MIN, 2 );

		// Combine both price displays into one HTML string
		$html = $this->wrap_price_displays( $price_html, $shop_prices_include_tax, $vat_text, $alternate_vat_text );

		return $html;
	}


	/**
	 * @return bool
	 * @since 1.2.0
	 */
	private function should_be_disabled_in_action() {

		//compatibility with YITH WooCommerce Product Add Ons select
		if ( did_filter( 'yith_wapo_option_price' ) ) {
			return true;
		}

		return false;
	}

}
