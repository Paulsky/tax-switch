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
}
