# Changelog
All notable changes to the Tax Switch for Woocommerce plugin will be documented in this file.

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
