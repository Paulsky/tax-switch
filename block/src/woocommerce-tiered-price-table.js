import TieredPriceTable from './compatibility/TieredPriceTable';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const tpt = new TieredPriceTable( viewConfig.originalTaxDisplay );
	tpt.init();
} );
