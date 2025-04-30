# Changelog
All notable changes to the Tax Switch for WooCommerce plugin will be documented in this file.

## [1.5.0] - 2025-04-26
### Added
- Gutenberg block/shortcode for showing text about the currently selected tax setting. [See this topic](https://wordpress.org/support/topic/shortcode-for-wdevs-tax-switch-label-text/)
- Compatibility for Flatsome theme
- Compatibility for FacetWP
### Updated
- Tested WooCommerce 9.8.2
- Possible breaking change: refactored the (block) code structure to support multiple blocks

## [1.4.3] - 2025-04-16
### Updated
- Tested WordPress 6.8.0
- Tested WooCommerce 9.8.1
- Security improvement: escaped dynamic class names in HTML output (reported by Peter Thaleikis).

## [1.4.2] - 2025-04-03
### Added
- Compatibility for WooCommerce Quantity Discounts, Rules & Swatches

## [1.4.1] - 2025-03-27
### Added
- Compatibility for Advanced Product Fields Pro for WooCommerce

## [1.4.0] - 2025-03-25
### Added
- Shortcode generator

## [1.3.2] - 2025-03-25
### Updated
- Translate shortcode switch labels. [See this topic](https://wordpress.org/support/topic/language-support-in-shortcode/#post-18379279)

## [1.3.1] - 2025-03-24
### Updated
- Tested with WooCommerce 9.7.1

## [1.3.0] - 2025-02-26
### Added
- 'buttons' switch type
- Swedish language (special thanks to Martin Hult)
- Color setting for label text
- JavaScript 'wdevs-tax-switch-changed' event

## [1.2.5] - 2025-02-11
### Updated
- Fixed tax calculation if customer is VAT exempt

## [1.2.4] - 2025-02-06
### Updated
- Always register the block/shortcode scripts and styles. Only enqueue them if the block or shortcode is executed
- Fixed price display issues in Tiered Pricing Table for WooCommerce when shop prices are set to display excluding VAT by default

## [1.2.3] - 2025-02-04
### Added
- Incompatibility list
### Updated
- Incompatibility with Unlimited Elements for Elementor (+ Pro)

## [1.2.2] - 2025-02-03
### Updated
- Compatibility with WooCommerce Product Table Lite / PRO

## [1.2.1] - 2024-11-14
### Added
- Compatibility for WPML

## [1.2.0] - 2024-11-07
### Added
- Disable the plugin in the WooCommerce mini cart widget

## [1.1.11] - 2024-11-06
### Added
- Compatibility for Product Add-Ons for WooCommerce

## [1.1.10] - 2024-11-05
### Added
- Compatibility for JetEngine Listing Grid 'infinity scroll'

## [1.1.9] - 2024-11-04
### Added
- Extra check for filtering backend and frontend AJAX requests (which adds compatibility for [PDF Invoices & Packing Slips for WooCommerce](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/)).
### Updated
- Fixed a bug where the admin request checks failed when Wordpress is installed in a subdirectory.

## [1.1.8] - 2024-10-25
### Added
- Compatibility for YITH WooCommerce Product Add-ons & Extra Options Premium

## [1.1.7] - 2024-10-24
### Added
- Compatibility for [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/)

## [1.1.6] - 2024-10-22
### Updated
- Removed applying 'woocommerce_get_price_html' filter a second time to prevent nested duplications

## [1.1.5] - 2024-10-22
### Added
- Added extra checks for only executing the filters on the frontend

## [1.1.4] - 2024-10-19
### Updated
- Improved compatibility for Tiered Pricing Table for WooCommerce

## [1.1.3] - 2024-10-15
### Added
- Compatibility for [Discount Rules for WooCommerce](https://wordpress.org/plugins/woo-discount-rules/)
### Updated
- Migrated filters from only using 'woocommerce_get_price_html' to 'woocommerce_get_price_html' in combination with 'wc_price'. This way, the plugin should be more compatible with other plugins.
- Compatibility for Tiered Pricing Table for WooCommerce on catalog pages

## [1.1.2] - 2024-10-09
### Added
- Compatibility for Measurement Price Calculator for WooCommerce

## [1.1.1] - 2024-10-05
### Added
- Compatibility for Tiered Pricing Table for WooCommerce Premium

## [1.1.0] - 2024-10-04
### Added
- Compatibility for [Tiered Pricing Table for WooCommerce](https://wordpress.org/plugins/tier-pricing-table/) (Single product page)
- Compatibility for [WooCommerce Product Table](https://wordpress.org/plugins/wc-product-table-lite/)

## [1.0.1] - 2024-10-02
### Updated
- Fixed bug where JS was not loaded when element was rendered by shortcode.

## [1.0.0] - 2024-08-16
### Added
- Initial release of Wdevs Tax Switch plugin.
- Feature to display WooCommerce prices with or without VAT.
- Switch for customers to toggle between price displays.
- Support for WordPress blocks.
- Settings page in WooCommerce for customizing VAT texts.
- Multilingual support.
