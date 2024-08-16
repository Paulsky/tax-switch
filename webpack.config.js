const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'block/src', 'index.js' ),
		view: path.resolve( process.cwd(), 'block/src', 'view.js' ),
	},
};
