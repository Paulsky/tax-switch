=== Tax Switch for WooCommerce ===
Contributors: wijnbergdevelopments
Tags: woocommerce, tax, vat
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.1.8
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

For more information about this plugin, please visit the [plugin page](https://products.wijnberg.dev/product/wordpress/plugins/tax-switch-for-woocommerce/).

= Requirements =

* WooCommerce plugin installed and activated
* WooCommerce tax calculations enabled and configured

= Usage =

After installation and configuration, you can add the tax switch to your pages in two ways:

1. Use the Gutenberg block "Tax Switch for WooCommerce" in your page or post editor.
2. Use the shortcode `[wdevs_tax_switch]` anywhere in your content.

= Shortcode Usage =

Basic usage:
[wdevs_tax_switch]

The shortcode accepts several attributes to customize its appearance and behavior:

* `class-name`: Adds custom CSS classes to the switch.
    - Default: is-style-default
    - Options: is-style-default, is-style-inline, or custom classes
* `switch-color`: Sets the color of the switch handle.
* `switch-color-checked`: Sets the color of the switch when it's in the "on" position.
* `switch-background-color`: Sets the background color of the switch.
* `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.
* `switch-label-incl`: Sets the text for the "including VAT" label.
    - Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
* `switch-label-excl`: Sets the text for the "excluding VAT" label.
    - Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.

Example with custom attributes:

`[wdevs_tax_switch class-name="is-style-inline" switch-color="#ffffff" switch-color-checked="#000000" switch-background-color="#000000" switch-background-color-checked="#4CAF50" switch-label-incl="Inc. Tax" switch-label-excl="Ex. Tax"]`

This will display an inline-style switch with a white handle that turns black when on, a black background when off, green background when on, and custom labels for including and excluding tax.

You can also use this shortcode in your theme files with the do_shortcode() function:

`<?php echo do_shortcode('[wdevs_tax_switch]'); ?>`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tax-switch-for-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the WooCommerce -> Settings -> Tax Switch screen to configure the plugin.

== Frequently Asked Questions ==

= Are there any known compatibility issues? =

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin. You can fix this by using WooCommerce shortcodes instead of the WooCommerce Blocks.

== Changelog ==

= 1.1.8 =
* Added compatibility for YITH WooCommerce Product Add-ons & Extra Options Premium

= 1.1.7 =
* Added compatibility for [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/)

= 1.1.6 =
* Removed applying 'woocommerce_get_price_html' filter a second time to prevent nested duplications

= 1.1.5 =
* Added extra checks for only executing the filters on the frontend

= 1.1.4 =
* Improved compatibility for Tiered Pricing Table for WooCommerce

= 1.1.3 =
* Migrated filters from only using 'woocommerce_get_price_html' to 'woocommerce_get_price_html' in combination with 'wc_price'. This way, the plugin should be more compatible with other plugins.
* Added compatibility for [Discount Rules for WooCommerce](https://wordpress.org/plugins/woo-discount-rules/)
* Added compatibility for Tiered Pricing Table for WooCommerce on catalog pages

= 1.1.2 =
* Added compatibility for Measurement Price Calculator for WooCommerce.

= 1.1.1 =
* Added compatibility for Tiered Pricing Table for WooCommerce Premium (single product page).

= 1.1.0 =
* Added compatibility for [Tiered Pricing Table for WooCommerce](https://wordpress.org/plugins/tier-pricing-table/) (single product page).
* Added compatibility for [WooCommerce Product Table](https://wordpress.org/plugins/wc-product-table-lite/)

= 1.0.1 =
* Fixed bug where JS was not loaded when element was rendered by shortcode.

= 1.0.0 =
* Initial release of Tax Switch for WooCommerce.

== Additional Information ==

For more information and other WordPress plugins, visit [Wijnberg Developments](https://products.wijnberg.dev/product-category/wordpress/plugins/).

== Screenshots ==

1. This GIF demonstrates the main functionality of the plugin.
