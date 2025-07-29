import FiboFilters from './compatibility/FiboFilters';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const fiboFilters = new FiboFilters( viewConfig.originalTaxDisplay );
	fiboFilters.init();
} );
