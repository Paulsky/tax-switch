import { render, Suspense } from '@wordpress/element';
import SwitchComponent from './components/SwitchComponent';
import TaxSwitchHelper from './includes/TaxSwitchHelper';
import { select } from '@wordpress/data';
import './includes/store';

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

window.addEventListener( 'DOMContentLoaded', () => {
	const elements = document.querySelectorAll( '.wp-block-wdevs-tax-switch' );
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	if ( elements.length > 0 ) {
		elements.forEach( ( element ) => {
			if ( element ) {
				renderSwitchComponent( element, viewConfig );
			}
		} );

		TaxSwitchHelper.togglePriceClasses(
			viewConfig.originalTaxDisplay,
			select( 'wdevs-tax-switch/store' ).getIsSwitched()
		);
	}
} );
