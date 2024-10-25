import { select } from '@wordpress/data';
import './store';

class TaxSwitchHelper {
	static togglePriceClasses( originalTaxDisplay, isSwitched ) {
		const displayIncludingVat = this.displayIncludingVat(
			originalTaxDisplay,
			isSwitched
		);
		const elements = document.querySelectorAll( '.wts-price-wrapper' );

		elements.forEach( ( element ) => {
			const inclElement = element.querySelector(
				':scope > .wts-price-incl'
			);
			const exclElement = element.querySelector(
				':scope > .wts-price-excl'
			);

			if ( displayIncludingVat ) {
				inclElement.classList.remove( 'wts-inactive' );
				inclElement.classList.add( 'wts-active' );
				exclElement.classList.remove( 'wts-active' );
				exclElement.classList.add( 'wts-inactive' );
			} else {
				inclElement.classList.remove( 'wts-active' );
				inclElement.classList.add( 'wts-inactive' );
				exclElement.classList.remove( 'wts-inactive' );
				exclElement.classList.add( 'wts-active' );
			}
		} );
	}

	static displayIncludingVat( originalTaxDisplay, isSwitched ) {
		if ( isSwitched === null || isSwitched === undefined ) {
			isSwitched = select( 'wdevs-tax-switch/store' ).getIsSwitched();
		}
		return (
			( originalTaxDisplay === 'incl' && ! isSwitched ) ||
			( originalTaxDisplay === 'excl' && isSwitched )
		);
	}

	static parseBooleanValue( value ) {
		if ( value ) {
			return JSON.parse( value );
		}
		return false;
	}

	static setPriceClasses( originalTaxDisplay ) {
		return this.togglePriceClasses(
			originalTaxDisplay,
			select( 'wdevs-tax-switch/store' ).getIsSwitched()
		);
	}

	static getVatTexts( existingWrapper = null ) {
		const space = document.createTextNode( ' ' ).nodeValue;
		let $includingText, $excludingText;
		if ( existingWrapper ) {
			const $wrapper = jQuery( existingWrapper );
			$includingText = $wrapper
				.find( '.wts-price-incl .wts-vat-text' )
				.first();
			$excludingText = $wrapper
				.find( '.wts-price-excl .wts-vat-text' )
				.first();

			if ( $includingText.length || $excludingText.length ) {
				return {
					including: $includingText.length
						? space + $includingText.clone().prop( 'outerHTML' )
						: '',
					excluding: $excludingText.length
						? space + $excludingText.clone().prop( 'outerHTML' )
						: '',
				};
			}
		}

		$includingText = jQuery(
			'.wts-price-wrapper .wts-price-incl .wts-vat-text'
		).first();
		$excludingText = jQuery(
			'.wts-price-wrapper .wts-price-excl .wts-vat-text'
		).first();

		return {
			including: $includingText.length
				? space + $includingText.clone().prop( 'outerHTML' )
				: '',
			excluding: $excludingText.length
				? space + $excludingText.clone().prop( 'outerHTML' )
				: '',
		};
	}
}

export default TaxSwitchHelper;
