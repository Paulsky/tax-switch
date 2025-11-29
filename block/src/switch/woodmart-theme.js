import WoodmartTheme from './compatibility/WoodmartTheme';
import { onDomReady } from '../shared/utils/render';

onDomReady( () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	const woodmartTheme = new WoodmartTheme( viewConfig.originalTaxDisplay );
	woodmartTheme.init();
} );
