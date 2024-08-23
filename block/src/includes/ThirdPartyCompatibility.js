import jQuery from 'jquery';
import TaxSwitchHelper from './TaxSwitchHelper';

class ThirdPartyCompatibility {
	static initialize( originalTaxDisplay ) {
		this.registerWooCommerceEvents( originalTaxDisplay );
	}

	static registerWooCommerceEvents( originalTaxDisplay ) {
		jQuery( '.variations_form' ).on( 'found_variation', function () {
			setTimeout( function () {
				TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
			}, 10 );
		} );
	}
}

export default ThirdPartyCompatibility;
