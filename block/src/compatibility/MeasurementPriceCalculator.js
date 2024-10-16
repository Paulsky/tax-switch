import TaxSwitchHelper from '../includes/TaxSwitchHelper';
import jQuery from 'jquery';
import { select, subscribe } from '@wordpress/data';

class MeasurementPriceCalculator {
	constructor( originalTaxDisplay ) {
		this.isSwitched = false;
		this.unsubscribe = null;
		this.originalTaxDisplay = originalTaxDisplay;
		this.currentVariation = null;
	}

	init() {
		const vm = this;

		vm.unsubscribe = subscribe( () => {
			const newIsSwitched = select(
				'wdevs-tax-switch/store'
			).getIsSwitched();

			if ( vm.isSwitched !== newIsSwitched ) {
				vm.isSwitched = newIsSwitched;
				vm.handleSwitchChange();
			}
		} );

		vm.registerWooCommerceEvents();
	}

	registerWooCommerceEvents() {
		const vm = this;
		jQuery( '.single_variation, .single_variation_wrap' ).bind(
			'show_variation',
			function ( event, variation ) {
				setTimeout( function () {
					vm.currentVariation = variation;
					if ( window.wc_price_calculator_params ) {
						const currentPrice = vm.getCurrentPrice();
						//const minimumPrice = parseFloat( variation.minimum_price );

						window.wc_price_calculator_params.product_price =
							currentPrice; // set the current variation product price
						//window.wc_price_calculator_params.minimum_price = minimumPrice;

						variation.price = currentPrice;

						jQuery( '.qty' ).trigger( 'change' );
					}
				}, 500 );
			}
		);
	}

	handleSwitchChange() {
		if ( window.wc_price_calculator_params ) {
			const currentPrice = this.getCurrentPrice();
			if ( currentPrice ) {
				window.wc_price_calculator_params.product_price = currentPrice;
				jQuery( '.qty' ).trigger( 'change' );
			}
		}
	}

	getCurrentPrice() {
		if (
			this.currentVariation &&
			this.currentVariation.price_incl_vat &&
			this.currentVariation.price_excl_vat
		) {
			const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
				this.originalTaxDisplay
			);
			if ( displayIncludingVat ) {
				return parseFloat( this.currentVariation.price_incl_vat );
			} else {
				return parseFloat( this.currentVariation.price_excl_vat );
			}
		}

		return null;
	}

	cleanup() {
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
	}
}

export default MeasurementPriceCalculator;
