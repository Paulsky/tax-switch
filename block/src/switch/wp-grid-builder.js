import WpGridBuilder from './compatibility/WpGridBuilder';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const wpGridBuilder = new WpGridBuilder( viewConfig.originalTaxDisplay );
	wpGridBuilder.init();
} );
