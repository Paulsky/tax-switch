import AdvancedProductFieldsForWoocommerce from './compatibility/AdvancedProductFieldsForWoocommerce';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const apffw = new AdvancedProductFieldsForWoocommerce(
		viewConfig.originalTaxDisplay
	);
	apffw.init();
} );
