<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Woo_Tax_Switch
 * @subpackage Woo_Tax_Switch/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Tax_Switch
 * @subpackage Woo_Tax_Switch/public
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Woo_Tax_Switch_Public {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/woo-tax-switch-shared.css', array(), $this->version, 'all' );

	}

	public function get_price_html( $price_html, $product ) {
		if ( empty( $price_html ) ) {
			return $price_html;
		}

		// Get the current setting and determine if it's inclusive
		$shop_display_is_incl = get_option( 'woocommerce_tax_display_shop' ) === 'incl';

		$filter = $shop_display_is_incl ? 'get_excl_option' : 'get_incl_option';

		// Get VAT text options
		$incl_vat_text = $this->get_vat_text(  'woo_tax_switch_incl_vat', 'Incl. VAT' );
		$excl_vat_text = $this->get_vat_text(  'woo_tax_switch_excl_vat', 'Excl. VAT' );

		// Generate prices
		$current_price_text   = $this->generate_price_with_text( $price_html, $shop_display_is_incl ? $incl_vat_text : $excl_vat_text );
		$alternate_price_text = $this->generate_alternate_price( $product, $filter, $shop_display_is_incl ? $excl_vat_text : $incl_vat_text );

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $current_price_text, $alternate_price_text, $shop_display_is_incl );
	}

	public function get_incl_option($pre_option, $option, $default_value) {
		return 'incl';
	}

	public function get_excl_option($pre_option, $option, $default_value) {
		return 'excl';
	}

	private function get_vat_text( $key, $default ) {
		$text = get_option($key, $default);
		return __($text, 'woo-tax-switch');
	}

	private function generate_price_with_text( $price_text, $vat_text ) {
		return sprintf( '%s <span class="wts-vat-text">%s</span>', $price_text, $vat_text );
	}

	private function generate_alternate_price( $product, $filter, $vat_text ) {
		// Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ] );

		// Generate the alternate price
		add_filter( 'pre_option_woocommerce_tax_display_shop', [ $this, $filter ], 1, 3 );
		$alternate_price_html = $product->get_price_html();
		remove_filter( 'pre_option_woocommerce_tax_display_shop', [ $this, $filter ], 1 );

		// Re-enable this filter and function
		add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );

		return $this->generate_price_with_text( $alternate_price_html, $vat_text );
	}

	private function combine_price_displays( $current_price_text, $alternate_price_text, $shop_display_is_incl ) {
		$classes = [ 'wts-price-incl', 'wts-price-excl' ];
		if ( ! $shop_display_is_incl ) {
			$classes = array_reverse( $classes );
		}

		return sprintf(
			'<span class="wts-price-wrapper"><span class="%s wts-active" >%s</span> <span class="%s wts-inactive">%s</span></span>',
			$classes[0],
			$current_price_text,
			$classes[1],
			$alternate_price_text
		);
	}

}
