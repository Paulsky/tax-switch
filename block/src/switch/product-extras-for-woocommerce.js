import ProductExtrasForWoocommerce from './compatibility/ProductExtrasForWoocommerce';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};

	const pewc = new ProductExtrasForWoocommerce(
		viewConfig.originalTaxDisplay,
		wtsCompatibilityObject.baseTaxRate
	);
	pewc.init();
} );
