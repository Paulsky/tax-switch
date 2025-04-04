# Tax Switch for WooCommerce

Enhances WooCommerce by allowing users to toggle between displaying prices including or excluding VAT. This plugin adds a customizable switch component and provides a flexible way to display both price versions.

<br/>
<img src="https://github.com/user-attachments/assets/a76b1145-b4c2-4ff8-83c1-2d721caefaa7" width="300" alt="Tax Switch for WooCommerce demo" style="max-width: 300px !important; height: auto !important;" />
<br/>
<br/>

For more WordPress plugins, check out our products at [Wijnberg Developments](https://wijnberg.dev).

## Built with

- [WooCommerce](https://github.com/woocommerce/woocommerce)
- [WordPress](https://github.com/WordPress/WordPress)

## Requirements

- WooCommerce plugin installed and activated
- WordPress 5.0 or higher (for Gutenberg block support)
- WooCommerce tax calculations enabled:
	1. Go to WooCommerce > Settings > General
	2. Check the box for "Enable tax rates and calculations"
	3. Click "Save changes"
- WooCommerce taxes configured:
	1. Go to WooCommerce > Settings > Tax
	2. Set up your tax rates and rules as needed for your store

Without proper tax configuration in WooCommerce, the Tax Switch for WooCommerce plugin will not function as intended.

## Installation

To install the plugin, follow these steps:

1. Download the `.zip` file from the [releases page](https://github.com/Paulsky/tax-switch/releases/).
2. In your WordPress admin dashboard, go to `Plugins` > `Add New`.
3. Click `Upload Plugin` at the top of the page.
4. Click `Choose File`, select the `.zip` file you downloaded, then click `Install Now`.
5. After installation, click `Activate Plugin`.

The plugin is now ready for use.

## Getting started

These instructions will guide you through the installation and basic setup of the Tax Switch for WooCommerce plugin, ensuring a smooth integration with your WooCommerce store.

### Configuration

Once activated, Tax Switch for WooCommerce requires minimal configuration:

1. Go to the Tax Switch for WooCommerce settings page located under the 'WooCommerce' menu in the WordPress admin area.
2. Customize the text for including and excluding VAT.
3. Save your changes.

### WPML

To translate the option texts via WPML:

1. Save your options first in: WooCommerce -> Settings -> Tax Switch
2. Then translate the texts in: WPML -> String Translations and search for your option values in the domain 'tax-switch-for-woocommerce'


### Usage

After configuration, you can add the tax switch to your pages in two ways:

1. Use the Gutenberg block "Tax Switch for WooCommerce" in your page or post editor.
2. Use the shortcode `[wdevs_tax_switch]` anywhere in your content.

The switch will toggle the display of prices including or excluding VAT across your site.

### Shortcode Usage

The plugin provides a shortcode that allows you to easily add the tax switch anywhere on your site.

Basic usage:

[wdevs_tax_switch]

This will display the default tax switch.

The shortcode also accepts several attributes to customize its appearance:

- `class-name`: Adds custom CSS classes to the switch.
    - Default: `is-style-default`
    - Options: `is-style-default`, `is-style-inline`, custom classes
- `switch-type`: Determines the style of the toggle.
	- Default: `switch`
	- Options: `switch`, `buttons`
- `switch-color`: Sets the color of the switch handle.
- `switch-color-checked`: Sets the color of the switch when it's in the "on" position.
- `switch-background-color`: Sets the background color of the switch.
- `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.
- `switch-text-color`: Sets the text color of the switch labels.
- `switch-label-incl`: Sets the text for the "including VAT" label.
    - Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
- `switch-label-excl`: Sets the text for the "excluding VAT" label.
    - Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.

Example with custom attributes:

`[wdevs_tax_switch class-name="is-style-inline" switch-type="switch" switch-color="#ffffff" switch-color-checked="#000000" switch-background-color="#000000" switch-background-color-checked="#4CAF50" switch-text-color="#FF0000" switch-label-incl="Incl. tax" switch-label-excl="Excl. tax"]`

This will display an inline-style switch with a white handle that turns black when on, a black background when off, green background when on, and custom labels for including and excluding tax.

You can use this shortcode in posts, pages, and even in your theme files by using the `do_shortcode()` function:

```php
<?php echo do_shortcode('[wdevs_tax_switch]'); ?>
```

### JavaScript Events

The switch fires a JavaScript event when the tax display is toggled. You can listen for this event to execute custom code when a user switches between inclusive and exclusive tax display. This is useful for when you need to perform additional actions based on the tax display state.

```
document.addEventListener('wdevs-tax-switch-changed', function(event) {
    console.log(event.detail);
	// event.detail contains:
	// - isSwitched: boolean - the raw switch state
	// - displayIncludingVat: boolean - whether prices now display including VAT
});
```

## Compatibility

This plugin is tested and compatible with the following:

### Themes

- GeneratePress
- Blocksy
- Thrive

### Plugins

- WooCommerce Product Table Lite (+ PRO)
- Tiered Pricing Table for WooCommerce (+ Premium)
- Measurement Price Calculator for WooCommerce
- Discount Rules for WooCommerce
- YITH WooCommerce Product Add-Ons (+ & Extra Options Premium)
- JetEngine Listing Grid (Elementor)
- Product Add-Ons for WooCommerce
- B2BKing – Ultimate WooCommerce Wholesale and B2B Solution (+ Premium)
- Advanced Product Fields Pro for WooCommerce
- WooCommerce Quantity Discounts, Rules & Swatches

If you encounter any conflicts with other themes or plugins, please report them by opening an issue or through our website.

## Known Issues

### Compatibility with WooCommerce Blocks

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin. You can fix this by using WooCommerce shortcodes instead of the WooCommerce Blocks.

For more information and updates on this issue, please refer to the following GitHub issue:
[WooCommerce Blocks Issue #8972](https://github.com/woocommerce/woocommerce-blocks/issues/8972)

We are monitoring this issue and will update the plugin accordingly when a solution becomes available. In the meantime, the tax switch functionality may not work as expected with certain WooCommerce Blocks.

### Incompatibility

After multiple attempts to create compatibility functions, reaching out to the plugin developers several times, and still finding no viable solution, the following plugins remain incompatible:

- Unlimited Elements for Elementor (+ Pro): AJAX pagination and filtering issues

## Language support

Currently supported languages:
- English
- Dutch
- Swedish

If you would like to add support for a new language or improve existing translations, please let us know by opening an issue or contacting us through our website.

## Contributing

Your contributions are welcome! If you'd like to contribute to the project, feel free to fork the repository, make your changes, and submit a pull request.

## Development and deployment

To prepare your development work for submission, ensure you have `npm` installed and run `npm run build`. This command compiles the React components and prepares the plugin for deployment.

### Steps:

1. Ensure `npm` is installed.
2. Navigate to the project root.
3. Run `npm run build`.

The compiled files are now ready for use. Please ensure your changes adhere to the project's coding standards.

## Security

If you discover any security related issues, please email us instead of using the issue tracker.

## License

This plugin is licensed under the GNU General Public License v2 or later.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
