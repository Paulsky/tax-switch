import MeasurementPriceCalculator from './compatibility/MeasurementPriceCalculator';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};
	const mpc = new MeasurementPriceCalculator( viewConfig.originalTaxDisplay );
	mpc.init();
} );
