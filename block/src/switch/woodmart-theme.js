import WoodmartTheme from './compatibility/WoodmartTheme';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const woodmartTheme = new WoodmartTheme( viewConfig.originalTaxDisplay );
	woodmartTheme.init();
} );
