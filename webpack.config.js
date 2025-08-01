const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		'switch/index': path.resolve(
			process.cwd(),
			'block/src/switch',
			'index.js'
		),
		'switch/view': path.resolve(
			process.cwd(),
			'block/src/switch',
			'view.js'
		),
		'switch/woocommerce-tiered-price-table': path.resolve(
			process.cwd(),
			'block/src/switch',
			'woocommerce-tiered-price-table.js'
		),
		'switch/woocommerce-measurement-price-calculator': path.resolve(
			process.cwd(),
			'block/src/switch',
			'woocommerce-measurement-price-calculator.js'
		),
		'switch/yith-woocommerce-product-add-ons': path.resolve(
			process.cwd(),
			'block/src/switch',
			'yith-woocommerce-product-add-ons.js'
		),
		'switch/woocommerce-product-addons': path.resolve(
			process.cwd(),
			'block/src/switch',
			'woocommerce-product-addons.js'
		),
		'switch/advanced-product-fields-for-woocommerce': path.resolve(
			process.cwd(),
			'block/src/switch',
			'advanced-product-fields-for-woocommerce.js'
		),
		'switch/woocommerce-quantity-manager': path.resolve(
			process.cwd(),
			'block/src/switch',
			'woocommerce-quantity-manager.js'
		),
		'switch/product-extras-for-woocommerce': path.resolve(
			process.cwd(),
			'block/src/switch',
			'product-extras-for-woocommerce.js'
		),
		'switch/kapee-theme': path.resolve(
			process.cwd(),
			'block/src/switch',
			'kapee-theme.js'
		),
		'switch/fibofilters': path.resolve(
			process.cwd(),
			'block/src/switch',
			'fibofilters.js'
		),
		'label/index': path.resolve(
			process.cwd(),
			'block/src/label',
			'index.js'
		),
		'label/view': path.resolve(
			process.cwd(),
			'block/src/label',
			'view.js'
		),
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			...defaultConfig.optimization.splitChunks,
			cacheGroups: {
				...defaultConfig.optimization.splitChunks.cacheGroups,
				vendors: false,
				sharedGlobal: {
					name: 'shared/shared',
					chunks: 'all',
					minChunks: 2,
					enforce: true,
					test: /[\\/]block[\\/]src[\\/]/,
				},
			},
		},
	},
};
