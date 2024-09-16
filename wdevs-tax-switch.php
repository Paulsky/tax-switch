<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wijnberg.dev
 * @since             1.0.0
 * @package           Wdevs_Tax_Switch
 *
 * @wordpress-plugin
 * Plugin Name:          Tax Switch for WooCommerce
 * Plugin URI:           https://wijnberg.dev
 * Description:          Let customers toggle between inclusive and exclusive VAT pricing in your WooCommerce store.
 * Version:              1.0.0
 * Author:               Wijnberg Developments
 * Author URI:           https://wijnberg.dev/
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          tax-switch-for-woocommerce
 * Domain Path:          /languages
 * Tested up to:         6.6
 * Requires PHP:         7.2
 * Requires at least:    5.0
 * WC requires at least: 7.0.0
 * WC tested up to:      9.2.2
 * Requires Plugins:     woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOO_TAX_SWITCH_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wdevs-tax-switch-activator.php
 */
function activate_wdevs_tax_switch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-tax-switch-activator.php';
	Wdevs_Tax_Switch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wdevs-tax-switch-deactivator.php
 */
function deactivate_wdevs_tax_switch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-tax-switch-deactivator.php';
	Wdevs_Tax_Switch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wdevs_tax_switch' );
register_deactivation_hook( __FILE__, 'deactivate_wdevs_tax_switch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-tax-switch.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wdevs_tax_switch() {

	$plugin = new Wdevs_Tax_Switch();
	$plugin->run();

}

run_wdevs_tax_switch();
