import TieredPriceTable from './compatibility/TieredPriceTable';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const tpt = new TieredPriceTable( viewConfig.originalTaxDisplay );
	tpt.init();
} );
