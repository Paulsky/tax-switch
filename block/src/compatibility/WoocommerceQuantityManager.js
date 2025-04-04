import jQuery from 'jquery';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';
import TaxSwitchHelper from '../includes/TaxSwitchHelper';

class WoocommerceQuantityManager {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
		this.observer = null;
	}

	init() {
		this.registerWooCommerceEvents();
		this.registerObservers();
	}

	registerWooCommerceEvents() {
		const vm = this;
		jQuery( document ).on(
			'found_variation',
			function ( event, variation ) {
				if ( variation && variation.tax_rate ) {
					vm.taxRate = variation.tax_rate;
				}
			}
		);
	}

	registerObservers() {
		const vm = this;
		const handleMutation = ( mutationsList ) => {
			for ( const mutation of mutationsList ) {
				if (
					mutation.type !== 'childList' ||
					mutation.addedNodes.length === 0
				) {
					continue;
				}

				const shouldSkip = Array.from( mutation.addedNodes ).some(
					( node ) =>
						node.nodeType === Node.ELEMENT_NODE &&
						node.classList.contains( 'wts-price-container' )
				);

				if ( ! shouldSkip ) {
					vm.updateAllPriceElements();
				}
			}
		};

		const tableContainer = document.querySelector(
			'.wqm-pricing-table-wrapper'
		);
		if ( ! tableContainer ) return;

		vm.observer = new MutationObserver( handleMutation );
		vm.observer.observe( tableContainer, {
			childList: true,
			subtree: true,
		} );
	}

	updateAllPriceElements() {
		const vm = this;
		vm.observer.disconnect();

		const baseSelectors = [
			'[data-item="price"]',
			'[data-item="sale_price"]',
			'[data-item="total_price"]',
			'[data-item="total_sale_price"]',
		];

		const discountSelectors = vm.shouldUseDiscountSelectors()
			? [ '[data-item="discount"]', '[data-item="total_discount"]' ]
			: [];

		const allSelectors = [ ...baseSelectors, ...discountSelectors ].join(
			','
		);

		document.querySelectorAll( allSelectors ).forEach( ( element ) => {
			vm.updatePriceElement( element );
		} );

		const tableContainer = document.querySelector(
			'.wqm-pricing-table-wrapper'
		);

		if ( tableContainer ) {
			vm.observer.observe( tableContainer, {
				childList: true,
				subtree: true,
			} );
		}
	}

	updatePriceElement( element ) {
		const vm = this;
		const $element = jQuery( element );

		const processPrice = ( priceString ) => {
			const unformatted = window.accounting.unformat(
				priceString,
				window.wqm_config?.display_options.decimal
			);

			const alternatePrice = vm.formatPrice(
				TaxSwitchHelper.calculateAlternatePrice(
					unformatted,
					vm.originalTaxDisplay,
					vm.taxRate
				)
			);

			return {
				original: vm.formatPrice( unformatted ),
				alternate: alternatePrice,
			};
		};

		const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
			vm.originalTaxDisplay
		);

		if ( $element.find( 'del' ).length ) {
			const originalPriceContent = $element.find( 'del' ).text();
			const salePriceContent = $element.find( 'ins' ).text();

			const originalPriceData = processPrice( originalPriceContent );
			const originalPriceNewContent = vm.taxSwitchElementBuilder.build(
				displayIncludingVat,
				originalPriceData.original,
				originalPriceData.alternate,
				null
			);

			const salePriceData = processPrice( salePriceContent );
			const salePriceNewContent = vm.taxSwitchElementBuilder.build(
				displayIncludingVat,
				salePriceData.original,
				salePriceData.alternate,
				null
			);

			$element.find( 'del' ).html( originalPriceNewContent );
			$element.find( 'ins' ).html( salePriceNewContent );
		} else {
			const originalPrice = $element.text();
			const priceData = processPrice( originalPrice );

			const newContent = vm.taxSwitchElementBuilder.build(
				displayIncludingVat,
				priceData.original,
				priceData.alternate,
				null
			);

			$element.html( newContent );
		}
	}

	formatPrice( amount ) {
		if ( window.WAPF?.Util?.formatMoney ) {
			return window.WAPF.Util.formatMoney(
				amount,
				window.wapf_config?.display_options
			);
		}
		return amount;
	}

	shouldUseDiscountSelectors() {
		const wqmConfig = document.querySelector( '.wqm-config' );
		return wqmConfig?.dataset.percentOnTotal === '1';
	}

	destroy() {
		this.observer?.disconnect();
	}
}

export default WoocommerceQuantityManager;
