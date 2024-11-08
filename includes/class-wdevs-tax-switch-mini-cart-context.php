<?php

/**
 * Controls the mini cart context state.
 *
 * Tracks whether the current execution context is within the WooCommerce mini cart widget.
 * This allows for different behavior inside versus outside the mini cart context.
 *
 * @since      1.2.0
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Mini_Cart_Context {

	/**
	 * Tracks whether we are currently in the mini cart context.
	 *
	 * @since 1.2.0
	 * @var bool
	 */
	private static $in_mini_cart = false;

	/**
	 * Sets the mini cart context state to active.
	 *
	 * Called at the start of the mini cart widget rendering
	 * via the 'woocommerce_before_mini_cart' hook.
	 *
	 * @since    1.2.0
	 */
	public static function before_mini_cart() {
		self::$in_mini_cart = true;
	}

	/**
	 * Sets the mini cart context state to inactive.
	 *
	 * Called at the end of the mini cart widget rendering
	 * via the 'woocommerce_after_mini_cart' hook.
	 *
	 * @since    1.2.0
	 */
	public static function after_mini_cart() {
		self::$in_mini_cart = false;
	}

	/**
	 * Checks if the current execution is within the mini cart context.
	 *
	 * Used to determine whether code is being executed within or outside
	 * the mini cart widget rendering process.
	 *
	 * @return   bool    True if in mini cart context, false otherwise.
	 * @since    1.2.0
	 */
	public static function is_in_mini_cart() {
		return self::$in_mini_cart;
	}

}
