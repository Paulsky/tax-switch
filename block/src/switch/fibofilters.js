import FiboFilters from './compatibility/FiboFilters';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const fiboFilters = new FiboFilters( viewConfig.originalTaxDisplay );
	fiboFilters.init();
} );
