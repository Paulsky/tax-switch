=== Tax Switch for WooCommerce ===
Contributors: wijnbergdevelopments
Tags: woocommerce, tax, vat
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.4.3
Requires PHP: 7.2
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Let customers toggle between inclusive and exclusive VAT pricing in your WooCommerce store.

== Description ==
Tax Switch for WooCommerce enhances your WooCommerce store by allowing users to toggle between displaying prices including or excluding VAT. This plugin adds a customizable switch component and provides a flexible way to display both price versions.

=== Key features ===

* Display customizable switches where you want
* Gutenberg block support
* Shortcode for easy integration (including shortcode generator)
* Flexible display options for prices with and without VAT
* Remembers the user's preference for future visits
* Choose between a toggle switch or buttons

For more information about this plugin, please visit the [plugin page](https://products.wijnberg.dev/product/wordpress/plugins/tax-switch-for-woocommerce/).

=== Compatibility ===

This plugin integrates with WooCommerce's standard filters and actions for price display and calculation. While most plugins work out of the box, some third-party plugins use custom price building methods that require specific compatibility integrations. The following plugins have been tested and confirmed compatible:

* WooCommerce Product Table Lite (+ PRO)
* Tiered Pricing Table for WooCommerce (+ Premium)
* Measurement Price Calculator for WooCommerce
* Discount Rules for WooCommerce
* YITH WooCommerce Product Add-Ons (+ & Extra Options Premium)
* JetEngine Listing Grid (Elementor)
* Product Add-Ons for WooCommerce
* B2BKing – Ultimate WooCommerce Wholesale and B2B Solution (+ Premium)
* Advanced Product Fields Pro for WooCommerce
* WooCommerce Quantity Discounts, Rules & Swatches

If you encounter any compatibility issues with other plugins, please let us know. Your feedback helps us improve the plugin and extend compatibility to more third-party solutions.

=== Incompatibility ===

After multiple attempts to create compatibility functions, reaching out to the plugin developers several times, and still finding no viable solution, the following plugins remain incompatible:

* Unlimited Elements for Elementor (+ Pro): AJAX pagination and filtering issues

=== WPML ===

To translate the option texts via WPML:

1. Save your options first in: WooCommerce -> Settings -> Tax Switch
2. Then translate the texts in: WPML -> String Translations and search for your option values in the domain 'tax-switch-for-woocommerce'

=== Requirements ===

* WooCommerce plugin installed and activated
* WooCommerce tax calculations enabled and configured

=== Usage ===

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
* `switch-type`: Determines the style of the toggle.
 	- Default: `switch`
 	- Options: `switch`, `buttons`
* `switch-color`: Sets the color of the switch handle.
* `switch-color-checked`: Sets the color of the switch when it's in the "on" position.
* `switch-background-color`: Sets the background color of the switch.
* `switch-text-color`: Sets the text color of the switch labels.
* `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.
* `switch-label-incl`: Sets the text for the "including VAT" label.
    - Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
* `switch-label-excl`: Sets the text for the "excluding VAT" label.
    - Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.

Example with custom attributes:

`[wdevs_tax_switch class-name="is-style-inline" switch-type="switch" switch-color="#ffffff" switch-color-checked="#000000" switch-background-color="#000000" switch-background-color-checked="#4CAF50" switch-text-color="#FF0000" switch-label-incl="Incl. tax" switch-label-excl="Excl. tax"]`

This will display an inline-style switch with a white handle that turns black when on, a black background when off, green background when on, and custom labels for including and excluding tax.

You can also use this shortcode in your theme files with the do_shortcode() function:

`<?php echo do_shortcode('[wdevs_tax_switch]'); ?>`

= JavaScript Events =

The switch fires a JavaScript event when the tax display is toggled. You can listen for this event to execute custom code when a user switches between inclusive and exclusive tax display. This is useful for when you need to perform additional actions based on the tax display state.

`
document.addEventListener('wdevs-tax-switch-changed', function(event) {
   console.log(event.detail);
   // event.detail contains:
   // - isSwitched: boolean - the raw switch state
   // - displayIncludingVat: boolean - whether prices now display including VAT
 });
`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tax-switch-for-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the WooCommerce -> Settings -> Tax Switch screen to configure the plugin.

== Frequently Asked Questions ==

= Are there any known compatibility issues? =

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin. You can fix this by using WooCommerce shortcodes instead of the WooCommerce Blocks.

= Why do prices stay the same after switching? =

Sometimes prices may not appear to change when toggled. This is often related to WooCommerce tax settings. If possible, select 'Shop based' in WooCommerce → Settings → Tax → "Calculate tax based on". Otherwise, WooCommerce requires a billing/shipping address to calculate taxes, which is typically only available after login or during checkout.

== Changelog ==
= 1.4.3 =
* Tested WordPress 6.8.0
* Tested WooCommerce 9.8.1
* Security improvement: escaped dynamic class names in HTML output (reported by Peter Thaleikis).

= 1.4.2 =
* Added compatibility for WooCommerce Quantity Discounts, Rules & Swatches

= 1.4.1 =
* Added compatibility for Advanced Product Fields Pro for WooCommerce

= 1.4.0 =
* Added shortcode generator

= 1.3.2 =
* Translate shortcode switch labels. [See this topic](https://wordpress.org/support/topic/language-support-in-shortcode/#post-18379279)

= 1.3.1 =
* Tested with WooCommerce 9.7.1

= 1.3.0 =
* Added 'buttons' switch type
* Added Swedish language (special thanks to Martin Hult)
* Added color setting for label text
* Added JavaScript 'wdevs-tax-switch-changed' event

= 1.2.5 =
* Fixed tax calculation if customer is VAT exempt

= 1.2.4 =
* Always register the block/shortcode scripts and styles. Only enqueue them if the block or shortcode is executed
* Fixed price display issues in Tiered Pricing Table for WooCommerce when shop prices are set to display excluding VAT by default

= 1.2.3 =
* Added incompatibility list
* Updated incompatibility for Unlimited Elements for Elementor (+ Pro)

= 1.2.2 =
* Updated compatibility for WooCommerce Product Table Lite / PRO

= 1.2.1 =
* Added compatibility for WPML

= 1.2.0 =
* Disable the plugin in the WooCommerce mini cart widget

= 1.1.11 =
* Added compatibility for Product Add-Ons for WooCommerce

= 1.1.10 =
* Added compatibility for JetEngine Listing Grid 'infinity scroll'

= 1.1.9 =
* Added extra check for filtering backend and frontend AJAX requests (which adds compatibility for [PDF Invoices & Packing Slips for WooCommerce](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/))
* Fixed a bug where the admin request checks failed when Wordpress is installed in a subdirectory

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
