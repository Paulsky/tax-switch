<?php

/**
 * Plugin compatibility functionality for the Tax Switch plugin.
 *
 * Provides methods to check plugin activation status to handle
 * compatibility with other WordPress plugins.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 * @since      1.4.1
 */
trait Wdevs_Tax_Switch_Plugins {

	private static $active_plugins = null;

	/**
	 * Checks if a specific plugin is active by its main file path.
	 *
	 * Caches the list of active plugins for better performance during multiple checks.
	 * The check is done by comparing the full plugin path with active plugins list.
	 *
	 * @param string $plugin_file The plugin main file path (relative to plugins directory)
	 *                           Example: 'woocommerce/woocommerce.php'
	 * @return bool True if the plugin is active, false otherwise
	 * @since 1.4.1
	 */
	protected function is_plugin_active($plugin_file) {
		if (self::$active_plugins === null) {
			//see https://woocommerce.com/document/create-a-plugin/
			self::$active_plugins = wp_get_active_and_valid_plugins();
			//$active_network_plugins = wp_get_active_network_plugins();
		}

		$plugin_path = trailingslashit(WP_PLUGIN_DIR) . $plugin_file;
		return in_array($plugin_path, self::$active_plugins);
	}

	/**
	 * Checks if any of the specified plugins is active.
	 *
	 * Useful for checking if any plugin from a group of related plugins is active.
	 * Stops checking after finding the first active plugin (short-circuit evaluation).
	 *
	 * @param array $plugin_files Array of plugin main file paths to check
	 *                           Example: ['plugin1/plugin1.php', 'plugin2/plugin2.php']
	 * @return bool True if any of the plugins is active, false otherwise
	 * @since 1.4.1
	 */
	protected function is_any_plugin_active(array $plugin_files) {
		foreach ($plugin_files as $plugin_file) {
			if ($this->is_plugin_active($plugin_file)) {
				return true;
			}
		}
		return false;
	}
}
