import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class ProductExtrasForWoocommerce {
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
		vm.extendOriginalFunctions();
		vm.wrapPrices();
		vm.registerWoocommerceEvents();
	}

	extendOriginalFunctions() {
		if ( typeof window.pewc_wc_price !== 'function' ) {
			return;
		}

		const vm = this;
		const originalPewcWcPrice = window.pewc_wc_price;

		window.pewc_wc_price = function (
			price,
			price_only = false,
			update_pewc_total_calc_price = true
		) {
			const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
				vm.originalTaxDisplay
			);
			const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
				price,
				vm.originalTaxDisplay,
				vm.taxRate
			);

			const originalPriceDisplay = originalPewcWcPrice.apply( vm, [
				price,
				price_only,
				update_pewc_total_calc_price,
			] );

			const alternatePriceDisplay = originalPewcWcPrice.apply( vm, [
				alternatePrice,
				price_only,
				update_pewc_total_calc_price,
			] );

			return vm.taxSwitchElementBuilder.build(
				displayIncludingVat,
				originalPriceDisplay,
				alternatePriceDisplay,
				null
			);
		}.bind( this );

		const originalPewcWcPriceWithoutCurrency = window.pewc_wc_price;

		window.pewc_wc_price_without_currency = function ( price ) {
			const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
				vm.originalTaxDisplay
			);
			const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
				price,
				vm.originalTaxDisplay,
				vm.taxRate
			);

			const originalPriceDisplay =
				originalPewcWcPriceWithoutCurrency.apply( vm, [ price ] );

			const alternatePriceDisplay =
				originalPewcWcPriceWithoutCurrency.apply( vm, [
					alternatePrice,
				] );

			return vm.taxSwitchElementBuilder.build(
				displayIncludingVat,
				originalPriceDisplay,
				alternatePriceDisplay,
				null
			);
		}.bind( this );
	}

	wrapPrices() {
		if (
			typeof window.accounting === 'undefined' ||
			typeof window.accounting.unformat === 'undefined' ||
			typeof window.pewc_vars === 'undefined'
		) {
			return;
		}

		const vm = this;

		jQuery( '.pewc-option-cost-label, .pewc-checkbox-price' ).each(
			function ( index, element ) {
				const $element = jQuery( element );
				const html = $element.html();

				const newHtml = vm.getPriceHtml( html );

				$element.html( newHtml );
			}
		);

		vm.processPriceSeparatorElements( '.pewc-radio-option-text' );
		vm.processPriceSeparatorElements( '.pewc-radio-image-desc span' );
	}

	registerWoocommerceEvents() {
		const vm = this;
		jQuery( 'body' ).on( 'pewc_after_update_total_js', function () {
			if ( jQuery( '.pewc-main-price' ).length ) {
				const priceContainer = jQuery( '.pewc-main-price' ).find(
					'.wts-price-container'
				);

				if (
					priceContainer.length &&
					priceContainer.find( '.wts-price-wrapper' ).length === 1
				) {
					const vatTextElement = vm.getVatTextElement();
					if ( vatTextElement ) {
						priceContainer.append( vatTextElement );
					}
				}
			}

			if ( window.pewc_vars && window.pewc_vars.show_suffix == 'yes' ) {
				const elementIds = [
					'#pewc-per-product-total',
					'#pewc-options-total',
					'#pewc-grand-total',
					'#pewc-calculation-value',
				];
				elementIds.forEach( function ( id ) {
					const $element = jQuery( id );
					vm.replaceWoocommerceSuffix( $element );
				} );
			}
		} );
	}

	processPriceSeparatorElements( selector ) {
		const vm = this;

		jQuery( selector ).each( function () {
			const $fullSpan = jQuery( this );

			$fullSpan
				.contents()
				.filter( function () {
					return (
						this.nodeType === Node.TEXT_NODE &&
						jQuery( this ).prev( '.pewc-separator' ).length
					);
				} )
				.each( function () {
					const newHtml = vm.getPriceHtml( this.nodeValue.trim() );
					const newNode = jQuery( newHtml );
					jQuery( this ).replaceWith( newNode );
				} );
			if ( window.pewc_vars && window.pewc_vars.show_suffix == 'yes' ) {
				vm.replaceWoocommerceSuffix( $fullSpan );
			}
		} );
	}

	getPriceHtml( html ) {
		const price = window.accounting.unformat(
			html,
			window.pewc_vars.decimal_separator
		);

		const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
			this.originalTaxDisplay
		);

		const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
			price,
			this.originalTaxDisplay,
			this.taxRate
		);

		const alternatePriceDisplay = window.accounting.formatMoney(
			alternatePrice,
			{
				symbol: window.pewc_vars.currency_symbol,
				decimal: window.pewc_vars.decimal_separator,
				thousand: window.pewc_vars.thousand_separator,
				precision: window.pewc_vars.decimals,
				format: window.pewc_vars.price_format,
			}
		);

		return this.taxSwitchElementBuilder.build(
			displayIncludingVat,
			html,
			alternatePriceDisplay,
			null
		);
	}

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

	replaceWoocommerceSuffix( $element ) {
		const vm = this;
		if ( ! $element.length ) return;

		const $suffixElement = $element.find( '.woocommerce-price-suffix' );
		if ( ! $suffixElement.length ) return;

		const vatTextElement = vm.getVatTextElement();
		if ( ! vatTextElement ) return;

		const $directPriceContainer = $element.children(
			'.wts-price-container'
		);

		if ( $directPriceContainer.length > 0 ) {
			$suffixElement.remove();
			$directPriceContainer.append( vatTextElement );
		} else {
			$suffixElement.replaceWith( vatTextElement );
		}
	}
}

export default ProductExtrasForWoocommerce;
