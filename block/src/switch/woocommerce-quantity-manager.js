import WoocommerceQuantityManager from './compatibility/WoocommerceQuantityManager';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};
	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};

	const wqm = new WoocommerceQuantityManager(
		viewConfig.originalTaxDisplay,
		wtsCompatibilityObject.baseTaxRate
	);

	wqm.init();
} );
