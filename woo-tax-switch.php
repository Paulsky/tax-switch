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
 * @package           Woo_Tax_Switch
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Tax Switch
 * Plugin URI:        https://wijnberg.dev
 * Description:       Display WooCommerce prices with or without VAT, switchable by customers.
 * Version:           1.0.0
 * Author:            Wijnberg Developments
 * Author URI:        https://wijnberg.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-tax-switch
 * Domain Path:       /languages
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
 * This action is documented in includes/class-woo-tax-switch-activator.php
 */
function activate_woo_tax_switch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-tax-switch-activator.php';
	Woo_Tax_Switch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-tax-switch-deactivator.php
 */
function deactivate_woo_tax_switch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-tax-switch-deactivator.php';
	Woo_Tax_Switch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_tax_switch' );
register_deactivation_hook( __FILE__, 'deactivate_woo_tax_switch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-tax-switch.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_tax_switch() {

	$plugin = new Woo_Tax_Switch();
	$plugin->run();

}
run_woo_tax_switch();
