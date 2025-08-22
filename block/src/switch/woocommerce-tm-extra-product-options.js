import WoocommerceTmExtraProductOptions from './compatibility/WoocommerceTmExtraProductOptions';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};

	const tmtepo = new WoocommerceTmExtraProductOptions(
		viewConfig.originalTaxDisplay,
		wtsCompatibilityObject.baseTaxRate
	);
	tmtepo.init();
} );
