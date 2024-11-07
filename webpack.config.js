const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'block/src', 'index.js' ),
		view: path.resolve( process.cwd(), 'block/src', 'view.js' ),
		'woocommerce-tiered-price-table': path.resolve(
			process.cwd(),
			'block/src',
			'woocommerce-tiered-price-table.js'
		),
		'woocommerce-measurement-price-calculator': path.resolve(
			process.cwd(),
			'block/src',
			'woocommerce-measurement-price-calculator.js'
		),
		'yith-woocommerce-product-add-ons': path.resolve(
			process.cwd(),
			'block/src',
			'yith-woocommerce-product-add-ons.js'
		),
		'woocommerce-product-addons': path.resolve(
			process.cwd(),
			'block/src',
			'woocommerce-product-addons.js'
		),
	},
};
