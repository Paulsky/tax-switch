import MeasurementPriceCalculator from './compatibility/MeasurementPriceCalculator';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};

	const mpc = new MeasurementPriceCalculator(
		viewConfig.originalTaxDisplay,
		wtsCompatibilityObject.baseTaxRate
	);
	mpc.init();
} );
