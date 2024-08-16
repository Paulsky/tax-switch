=== Tax Switch for WooCommerce ===
Contributors: wijnbergdevelopments
Tags: woocommerce, tax, vat
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Let customers toggle between inclusive and exclusive VAT pricing in your WooCommerce store.

== Description ==

Tax Switch for WooCommerce enhances your WooCommerce store by allowing users to toggle between displaying prices including or excluding VAT. This plugin adds a customizable switch on product pages and provides a flexible way to display both price versions.

Key features:
* Display customizable switches where you want
* Gutenberg block support
* Shortcode for easy integration
* Flexible display options for prices with and without VAT

= Requirements =

* WooCommerce plugin installed and activated
* WooCommerce tax calculations enabled and configured

= Usage =

After installation and configuration, you can add the tax switch to your pages in two ways:

1. Use the Gutenberg block "Wdevs Tax Switch" in your page or post editor.
2. Use the shortcode `[wdevs_tax_switch]` anywhere in your content.

= Shortcode Usage =

Basic usage:
[wdevs_tax_switch]

The shortcode also accepts several attributes to customize its appearance:

* `class-name`: Adds custom CSS classes to the switch.
* `switch-color`: Sets the color of the switch handle.
* `switch-background-color`: Sets the background color of the switch.
* `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.

Example with custom attributes:
[wdevs_tax_switch class-name="is-style-inline" switch-color="#ffffff" switch-background-color="#000000" switch-background-color-checked="#4CAF50"]

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wdevs-tax-switch` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the WooCommerce -> Settings -> Tax Switch screen to configure the plugin.

== Frequently Asked Questions ==

= Are there any known compatibility issues? =

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin.

== Additional Information ==

For more information and other WordPress plugins, visit [Wijnberg Developments](https://wijnberg.dev).
