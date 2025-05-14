import { render, Suspense } from '@wordpress/element';
import LabelComponent from './components/LabelComponent';
import { shouldBeEnabled } from '../shared/utils/render';

const renderLabelComponent = ( element, ajaxConfig ) => {
	const attributes = {
		...element.dataset,
		...ajaxConfig,
	};

	render(
		<Suspense fallback={ <div className="wp-block-placeholder" /> }>
			<LabelComponent { ...attributes } />
		</Suspense>,
		element
	);
};

window.addEventListener( 'DOMContentLoaded', () => {
	if ( ! shouldBeEnabled() ) {
		return;
	}

	const elements = document.querySelectorAll(
		'.wp-block-wdevs-tax-switch-label'
	);

	if ( elements.length > 0 ) {
		const viewConfig = window.wtsViewObject || {
			originalTaxDisplay: 'incl',
		};

		elements.forEach( ( element ) => {
			if ( element ) {
				renderLabelComponent( element, viewConfig );
			}
		} );
	}
} );
