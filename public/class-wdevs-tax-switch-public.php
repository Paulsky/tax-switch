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

		if ( $this->is_in_cart_or_checkout() && ! $this->should_switch_in_mini_cart() ) {
			return $return;
		}

		if ( $this->is_mail_context() ) {
			return $return;
		}

		if ( $this->should_be_disabled_in_action() ) {
			return $return;
		}

		if ( $this->should_hide_on_current_page() ) {
			return $return;
		}

		if ( $this->is_file_context() ) {
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

		if ( $this->should_hide_on_current_page() ) {
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
	 * @since 1.5.2
	 */
	public function wrap_inc_label($text) {
		return $this->wrap_tax_label($text, 'inc');
	}

	/**
	 * @since 1.5.2
	 */
	public function wrap_ex_label($text) {
		return $this->wrap_tax_label($text, 'ex');
	}

	/**
	 * Helper function to handle both tax label cases
	 *
	 * @since 1.5.2
	 * @param string $text The label text
	 * @param string $type Either 'inc' or 'ex' for including/excluding tax
	 * @return string
	 */
	protected function wrap_tax_label($text, $type) {
		if (empty(trim($text))) {
			return $text;
		}

		// Temporarily disable the opposite filter to prevent infinite loop
		$opposite_filter = ($type === 'inc') ? 'woocommerce_countries_ex_tax_or_vat' : 'woocommerce_countries_inc_tax_or_vat';
		$opposite_callback = ($type === 'inc') ? 'wrap_ex_label' : 'wrap_inc_label';

		remove_filter($opposite_filter, [$this, $opposite_callback], PHP_INT_MAX);

		$opposite_label = ($type === 'inc')
			? WC()->countries->ex_tax_or_vat()
			: WC()->countries->inc_tax_or_vat();

		add_filter($opposite_filter, [$this, $opposite_callback], PHP_INT_MAX, 1);
		//Re-enable this filter and function

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		return $this->combine_price_displays(
			$text,
			$opposite_label,
			($type === 'inc') ? $shop_prices_include_tax : !$shop_prices_include_tax
		);
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

		//disable in the total table row. The total, is the total what the customer paid. So this is absolute.
		if ( did_filter( 'woocommerce_order_get_total' ) ) {
			return true;
		}

		return false;
	}

}
