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
	 *
	 * @return bool True if the plugin is active, false otherwise
	 * @since 1.4.1
	 */
	protected function is_plugin_active( $plugin_file ) {
		if ( self::$active_plugins === null ) {
			//see https://woocommerce.com/document/create-a-plugin/
			self::$active_plugins = wp_get_active_and_valid_plugins();
			//$active_network_plugins = wp_get_active_network_plugins();
		}

		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin_file;

		return in_array( $plugin_path, self::$active_plugins );
	}

	/**
	 * Checks if any of the specified plugins is active.
	 *
	 * Useful for checking if any plugin from a group of related plugins is active.
	 * Stops checking after finding the first active plugin (short-circuit evaluation).
	 *
	 * @param array $plugin_files Array of plugin main file paths to check
	 *                           Example: ['plugin1/plugin1.php', 'plugin2/plugin2.php']
	 *
	 * @return bool True if any of the plugins is active, false otherwise
	 * @since 1.4.1
	 */
	protected function is_any_plugin_active( array $plugin_files ) {
		foreach ( $plugin_files as $plugin_file ) {
			if ( $this->is_plugin_active( $plugin_file ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if a specific theme (or its child theme) is currently active.
	 *
	 * Compares the given theme slug with both the active theme's template and stylesheet,
	 * including parent themes in case of child themes. The check is case-insensitive.
	 *
	 * @param string $theme_name The theme identifier to check.
	 *
	 * @return bool True if the theme or its child theme is active, false otherwise.
	 * @since 1.5.8
	 *
	 */
	protected function is_theme_active( $theme_name ) {
		$current_theme = wp_get_theme();
		$theme_slug    = strtolower( $theme_name );

		if ( ! $current_theme->exists() ) {
			return false;
		}

		$active_templates = [
			strtolower( $current_theme->get_template() ),
			strtolower( $current_theme->get_stylesheet() ),
		];

		if ( in_array( $theme_slug, $active_templates ) ) {
			return true;
		}

		if ( $parent = $current_theme->parent() ) {
			if ( $parent->exists() ) {
				$parent_templates = [
					strtolower( $parent->get_template() ),
					strtolower( $parent->get_stylesheet() ),
				];
				if ( in_array( $theme_slug, $parent_templates ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
