<?php

/**
 * The shared block functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.5.4
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The shared block functionality of the plugin.
 *
 * Defines shared functions for all the Tax Switch blocks.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Block_Shared {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.5.4
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.5.4
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	protected $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.5.4
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * @since 1.5.0
	 */
	public function register_frontend_scripts() {
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/shared/shared.asset.php' );

		wp_register_script(
			'wdevs-tax-switch-shared-script',
			plugin_dir_url( dirname( __FILE__ ) ) . 'build/shared/shared.js',
			$script_asset['dependencies'],
			$script_asset['version']
		);
	}
}
