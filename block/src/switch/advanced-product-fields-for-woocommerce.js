import AdvancedProductFieldsForWoocommerce from './compatibility/AdvancedProductFieldsForWoocommerce';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const apffw = new AdvancedProductFieldsForWoocommerce(
		viewConfig.originalTaxDisplay
	);
	apffw.init();
} );
