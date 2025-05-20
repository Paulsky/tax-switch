import { createRoot, Suspense } from '@wordpress/element';
import SwitchComponent from './components/SwitchComponent';
import TaxSwitchHelper from '../shared/TaxSwitchHelper';
import ThirdPartyCompatibility from './includes/ThirdPartyCompatibility';
import { shouldBeEnabled } from '../shared/utils/render';

const renderSwitchComponent = ( element, ajaxConfig ) => {
	const attributes = {
		...element.dataset,
		...ajaxConfig,
	};

	const root = createRoot( element );

	root.render(
		<Suspense fallback={ <div className="wp-block-placeholder" /> }>
			<SwitchComponent { ...attributes } />
		</Suspense>
	);
};

let isInitialized = false;

const initPage = ( viewConfig ) => {
	TaxSwitchHelper.setPriceClasses( viewConfig.originalTaxDisplay );
	if ( ! isInitialized ) {
		ThirdPartyCompatibility.initialize( viewConfig.originalTaxDisplay );
		isInitialized = true;
	}
};

const renderElements = () => {
	if ( ! shouldBeEnabled() ) {
		return;
	}

	const elements = document.querySelectorAll( '.wp-block-wdevs-tax-switch' );

	if ( elements.length > 0 ) {
		const viewConfig = window.wtsViewObject || {
			originalTaxDisplay: 'incl',
		};

		initPage( viewConfig );

		elements.forEach( ( element ) => {
			if ( element ) {
				renderSwitchComponent( element, viewConfig );
			}
		} );
	}
};

window.addEventListener( 'DOMContentLoaded', () => {
	renderElements();
} );

document.addEventListener( 'wdevs-tax-switch-appeared', () => {
	renderElements();
} );
