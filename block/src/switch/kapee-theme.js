import KapeeTheme from './compatibility/KapeeTheme';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const wtsCompatibilityObject = window.wtsCompatibilityObject || {
		baseTaxRate: 0,
	};

	const kt = new KapeeTheme(
			viewConfig.originalTaxDisplay,
			wtsCompatibilityObject.baseTaxRate
	);
	kt.init();
} );
