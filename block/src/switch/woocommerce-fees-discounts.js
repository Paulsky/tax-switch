import WoocommerceFeesDiscounts from './compatibility/WoocommerceFeesDiscounts';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};
	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};

	const initCompatibility = new WoocommerceFeesDiscounts(
		viewConfig.originalTaxDisplay,
		wtsCompatibilityObject.baseTaxRate
	);
	initCompatibility.init();
} );
