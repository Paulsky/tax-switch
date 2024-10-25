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

		if ( is_cart() || is_checkout() || $this->is_mail_context() ) {
			return $return;
		}

		//compatibility with YITH WooCommerce Oroduct Add Ons select
		if ( did_filter( 'yith_wapo_option_price' ) ) {
			return $return;
		}

		//already wrapped?
//		if ( str_contains( $return, 'wts-price-wrapper' ) ) {
//			return $return;
//		}

		if ( empty( $unformatted_price ) ) {
			return $return;
		}

		$shop_display_is_incl = $this->is_shop_display_inclusive();

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'wc_price', [ $this, 'wrap_wc_price' ], PHP_INT_MAX );

		$alternate_price = wc_price( $this->calculate_alternate_price( $unformatted_price ) );

		//Re-enable this filter and function
		add_filter( 'wc_price', [ $this, 'wrap_wc_price' ], PHP_INT_MAX, 5 );

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $return, $alternate_price, $shop_display_is_incl );
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

		if ( ! str_contains( $price_html, 'amount' ) ) {
			return $price_html;
		}

		if ( ! $this->is_woocommerce_product( $product ) ) {
			return $price_html;
		}

		//Temporarily disable this filter and function to prevent infinite loop
		//Is this still needed?
		remove_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], PHP_INT_MIN );

		//Execute all others filters
		//Causes duplications
		//$price_html = apply_filters( 'woocommerce_get_price_html', $price_html, $product );

		if ( empty( trim( $price_html ) ) ) {
			return $price_html;
		}

		$shop_display_is_incl = $this->is_shop_display_inclusive();

		// Get VAT text options
		$incl_vat_text = $this->get_option_text( 'wdevs_tax_switch_incl_vat', __( 'Incl. VAT', 'tax-switch-for-woocommerce' ) );
		$excl_vat_text = $this->get_option_text( 'wdevs_tax_switch_excl_vat', __( 'Excl. VAT', 'tax-switch-for-woocommerce' ) );

		if ( $shop_display_is_incl ) {
			$vat_text           = $incl_vat_text;
			$alternate_vat_text = $excl_vat_text;
		} else {
			$vat_text           = $excl_vat_text;
			$alternate_vat_text = $incl_vat_text;
		}

		//Re-enable this filter and function
		//Is this still needed?
		add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], PHP_INT_MIN, 2 );

		// Combine both price displays into one HTML string
		$html = $this->wrap_price_displays( $price_html, $shop_display_is_incl, $vat_text, $alternate_vat_text );;

		return $html;

	}

	private function combine_price_displays( $current_price_text, $alternate_price_text, $shop_display_is_incl ) {
		$classes = [ 'wts-price-incl', 'wts-price-excl' ];
		if ( ! $shop_display_is_incl ) {
			$classes = array_reverse( $classes );
		}

		return sprintf(
			'<span class="wts-price-wrapper"><span class="%s wts-active">%s</span><span class="%s wts-inactive">%s</span></span>',
			$classes[0],
			$current_price_text,
			$classes[1],
			$alternate_price_text
		);
	}

	private function wrap_price_displays( $price_html, $shop_display_is_incl, $vat_text, $alternate_vat_text ) {
		$classes = [ 'wts-price-incl', 'wts-price-excl' ];
		if ( ! $shop_display_is_incl ) {
			$classes = array_reverse( $classes );
		}

		return sprintf(
			'<span class="wts-price-container">%s <span class="wts-price-wrapper "><span class="%s wts-active" ><span class=" wts-vat-text">%s</span></span><span class="%s wts-inactive"><span class=" wts-vat-text">%s</span></span></span></span>',
			$price_html,
			$classes[0],
			$vat_text,
			$classes[1],
			$alternate_vat_text
		);

	}

	private function is_mail_context() {
		return (
			did_action( 'woocommerce_email_header' ) ||
			did_action( 'woocommerce_email_order_details' )
		);
	}
}
