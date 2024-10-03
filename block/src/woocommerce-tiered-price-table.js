import TieredPriceTableCompatibility from './includes/TieredPriceTableCompatibility';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};
	TieredPriceTableCompatibility.initialize( viewConfig.originalTaxDisplay );
} );
