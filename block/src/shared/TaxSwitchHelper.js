import { getIsSwitched } from './store';

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
			isSwitched = getIsSwitched();
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
		return this.togglePriceClasses( originalTaxDisplay, getIsSwitched() );
	}

	static calculateAlternatePrice( price, originalTaxDisplay, taxRate ) {
		// Guard clauses
		if ( ! price || price <= 0 || ! taxRate ) {
			return price;
		}

		const displayIncludingVat = originalTaxDisplay === 'incl';
		const taxMultiplier = 1 + taxRate / 100;

		let alternatePrice;
		if ( displayIncludingVat ) {
			alternatePrice = price / taxMultiplier;
		} else {
			alternatePrice = price * taxMultiplier;
		}

		return Number( alternatePrice.toFixed( 2 ) );
	}
}

export default TaxSwitchHelper;
