class TaxSwitchHelper {
	static togglePriceClasses( originalTaxDisplay, isSwitched ) {
		const displayIncludingVat = this.displayIncludingVat(
			originalTaxDisplay,
			isSwitched
		);
		const elements = document.querySelectorAll( '.wts-price-wrapper' );

		elements.forEach( ( element ) => {
			const inclElement = element.querySelector( '.wts-price-incl' );
			const exclElement = element.querySelector( '.wts-price-excl' );

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
}

export default TaxSwitchHelper;
