import YithProductAddOns from './compatibility/YithProductAddOns';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};
	const ywpado = new YithProductAddOns(
		viewConfig.originalTaxDisplay,
		wtsCompatibilityObject.baseTaxRate
	);
	ywpado.init();
} );
