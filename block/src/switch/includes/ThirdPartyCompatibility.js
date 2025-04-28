import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';

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
			if ( settings && settings.data ) {
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
			}
		} );

		const thirdPartyEvents = [
			'jet-engine/listing-grid/after-load-more', //JetEngine Listing Grid 'infinity scroll' compatibility
		];

		thirdPartyEvents.forEach( function ( eventName ) {
			jQuery( document ).on( eventName, function ( event, response ) {
				TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
			} );
		} );
	}
}

export default ThirdPartyCompatibility;
