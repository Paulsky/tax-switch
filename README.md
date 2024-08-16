# Tax Switch for WooCommerce

Enhances WooCommerce by allowing users to toggle between displaying prices including or excluding VAT. This plugin adds a customizable switch on product pages and provides a flexible way to display both price versions.

<br/>
<img src="https://github.com/user-attachments/assets/e0cacc7f-9530-4f83-be27-d231e82ee0d6" width="300" alt="Woo Tax Switch demo" style="max-width: 300px !important; height: auto !important;" />
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

Without proper tax configuration in WooCommerce, the Woo Tax Switch plugin will not function as intended.

## Installation

To install the plugin, follow these steps:

1. Download the `.zip` file from the [releases page](https://github.com/Paulsky/woo-tax-switch/releases/).
2. In your WordPress admin dashboard, go to `Plugins` > `Add New`.
3. Click `Upload Plugin` at the top of the page.
4. Click `Choose File`, select the `.zip` file you downloaded, then click `Install Now`.
5. After installation, click `Activate Plugin`.

The plugin is now ready for use.

## Getting started

These instructions will guide you through the installation and basic setup of the Woo Tax Switch plugin, ensuring a smooth integration with your WooCommerce store.

### Configuration

Once activated, Woo Tax Switch requires minimal configuration:

1. Go to the Woo Tax Switch settings page located under the 'WooCommerce' menu in the WordPress admin area.
2. Customize the text for including and excluding VAT.
3. Save your changes.

### Usage

After configuration, you can add the tax switch to your pages in two ways:

1. Use the Gutenberg block "Woo Tax Switch" in your page or post editor.
2. Use the shortcode `[woo_tax_switch]` anywhere in your content.

The switch will toggle the display of prices including or excluding VAT across your site.

### Shortcode Usage

The plugin provides a shortcode that allows you to easily add the tax switch anywhere on your site.

Basic usage:

[woo_tax_switch]

This will display the default tax switch.

The shortcode also accepts several attributes to customize its appearance:

- `class-name`: Adds custom CSS classes to the switch.
	- Default: `is-style-default`
	- Options: `is-style-default`, `is-style-inline`, custom classes
- `switch-color`: Sets the color of the switch handle.
- `switch-background-color`: Sets the background color of the switch.
- `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.

Example with custom attributes:

[woo_tax_switch class-name="is-style-inline" switch-color="#ffffff" switch-background-color="#000000" switch-background-color-checked="#4CAF50"]

This will display an inline-style switch with a white handle, black background when off, and green background when on.

You can use this shortcode in posts, pages, and even in your theme files by using the `do_shortcode()` function:

```php
<?php echo do_shortcode('[woo_tax_switch]'); ?>
```

## Compatibility

This plugin is tested and compatible with the following:

### Themes

- GeneratePress

If you encounter any conflicts with other themes or plugins, please report them by opening an issue or through our website.

## Known Issues

### Compatibility with WooCommerce Blocks

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin.

For more information and updates on this issue, please refer to the following GitHub issue:
[WooCommerce Blocks Issue #8972](https://github.com/woocommerce/woocommerce-blocks/issues/8972)

We are monitoring this issue and will update the plugin accordingly when a solution becomes available. In the meantime, the tax switch functionality may not work as expected with certain WooCommerce Blocks.

## Language support

Currently supported languages:
- English
- Dutch (Nederlands)

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
