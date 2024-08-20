import { render, Suspense } from '@wordpress/element';
import SwitchComponent from './components/SwitchComponent';

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
	const ajaxConfig = window.wtsAjaxObject || {
		ajaxUrl: null,
		ajaxNonce: null,
		ajaxAction: null,
		isSwitched: false,
	};

	if ( elements.length > 0 ) {
		elements.forEach( ( element ) => {
			if ( element ) {
				renderSwitchComponent( element, ajaxConfig );
			}
		} );
	}
} );
