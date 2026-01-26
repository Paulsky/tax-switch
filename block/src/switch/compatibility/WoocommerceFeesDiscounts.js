import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class WoocommerceFeesDiscounts {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
		this.vatTexts = null;
	}

	init() {
		const vm = this;
		vm.vatTexts = TaxSwitchElementBuilder.getVatTexts();
		vm.extendOriginalFunction();
		vm.registerWoocommerceEvents();
	}

	extendOriginalFunction() {
		if ( typeof window.wcfad_wc_price !== 'function' ) {
			return;
		}

		const vm = this;
		const originalWcfadPrice = window.wcfad_wc_price;

		window.wcfad_wc_price = function ( price, price_only = false ) {
			if ( price_only ) {
				return originalWcfadPrice.apply( this, [ price, price_only ] );
			}

			const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
				vm.originalTaxDisplay
			);

			const priceValue = Number( price );
			const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
				priceValue,
				vm.originalTaxDisplay,
				vm.taxRate
			);

			const originalPriceDisplay = originalWcfadPrice.apply( this, [
				price,
				price_only,
			] );

			const alternatePriceDisplay = originalWcfadPrice.apply( this, [
				alternatePrice,
				price_only,
			] );

			return vm.taxSwitchElementBuilder.build(
				displayIncludingVat,
				originalPriceDisplay,
				alternatePriceDisplay,
				vm.vatTexts
			);
		}.bind( this );
	}

	registerWoocommerceEvents() {
		const vm = this;

		jQuery( 'body' ).on( 'pewc_do_percentages', function () {
			vm.setMainPriceVatTexts();
		} );

		jQuery( document ).ready( function () {
			vm.setMainPriceVatTexts();
		} );
	}

	//TODO; this is duplicated, maybe move to TaxSwitchElementBuilder or TaxSwitchHelper
	getVatTextElement() {
		const vm = this;
		if ( ! vm.vatTexts ) {
			vm.vatTexts = TaxSwitchElementBuilder.getVatTexts();
		}
		if ( vm.vatTexts && vm.vatTexts.including && vm.vatTexts.excluding ) {
			const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
				vm.originalTaxDisplay
			);

			const vatTextElement = TaxSwitchElementBuilder.getVatTextElement(
				displayIncludingVat,
				vm.vatTexts.including,
				vm.vatTexts.excluding
			);

			return vatTextElement;
		}
		return null;
	}

	setMainPriceVatTexts() {
		const vm = this;
		const mainPrice = jQuery( '.wcfad-main-price' );

		if ( ! mainPrice.length ) {
			return;
		}

		let priceContainer = mainPrice.children( '.wts-price-container' );

		if ( ! priceContainer.length ) {
			const directWrapper = mainPrice.children( '.wts-price-wrapper' );

			if ( directWrapper.length ) {
				mainPrice
					.children()
					.wrapAll( '<div class="wts-price-container"></div>' );
				priceContainer = mainPrice.children( '.wts-price-container' );
			}
		}

		if (
			priceContainer.length &&
			priceContainer.find( '.wts-price-wrapper' ).length === 1 //TODO: this is false/positive when there is a from - to price
		) {
			const vatTextElement = vm.getVatTextElement();

			if ( vatTextElement ) {
				priceContainer.append( vatTextElement );
			}

			TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
		}
	}
}

export default WoocommerceFeesDiscounts;
