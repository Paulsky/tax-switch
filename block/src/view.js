import { render, Suspense } from '@wordpress/element';
import SwitchComponent from './components/SwitchComponent';
import TaxSwitchHelper from './includes/TaxSwitchHelper';
import ThirdPartyCompatibility from './includes/ThirdPartyCompatibility';

const renderSwitchComponent = ( element, ajaxConfig ) => {
	const attributes = {
		...element.dataset,
		...ajaxConfig,
	};

	render(
		<Suspense fallback={ <div className="wp-block-placeholder" /> }>
			<SwitchComponent { ...attributes } />
		</Suspense>,
		element
	);
};

const initPage = ( viewConfig ) => {
	TaxSwitchHelper.setPriceClasses( viewConfig.originalTaxDisplay );
	ThirdPartyCompatibility.initialize( viewConfig.originalTaxDisplay );
};

window.addEventListener( 'DOMContentLoaded', () => {
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
} );
