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

		jQuery( '.variations_form' ).on( 'reset_data', function () {
			setTimeout( function () {
				TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
			}, 10 );
		} );

		jQuery( document ).ajaxSuccess( function ( event, xhr, settings ) {
			const methods = [
				'get_variable_product_bulk_table', //Flycart Discount Rules for WooCommerce compatibility
			];

			const isMethodMatched = methods.some( ( method ) =>
				settings.data.includes( method )
			);

			if ( isMethodMatched ) {
				setTimeout( function () {
					TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
				}, 10 );
			}
		} );
	}
}

export default ThirdPartyCompatibility;
