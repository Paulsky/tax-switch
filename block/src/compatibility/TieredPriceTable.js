import jQuery from 'jquery';
import TaxSwitchHelper from '../includes/TaxSwitchHelper';

class TieredPriceTable {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.priceBackups = new Map(); // Store backups for multiple elements
		this.vatTexts = null;
	}

	init() {
		this.registerWooCommerceEvents();
		this.initializePriceBackups();
	}

	initializePriceBackups() {
		// Store original price HTML for all relevant price wrappers
		jQuery( '.tiered-pricing-dynamic-price-wrapper' ).each(
			( _, element ) => {
				const $element = jQuery( element );
				const productId = $element.data( 'product-id' );
				if ( ! this.priceBackups.has( productId ) ) {
					this.priceBackups.set( productId, $element.html() );
				}
			}
		);
	}

	registerWooCommerceEvents() {
		// Handle tiered price updates
		jQuery( '.tpt__tiered-pricing' ).on(
			'tiered_price_update',
			( event, data ) => {
				this.updateAllPrices( data );
			}
		);

		const mainElement = jQuery( '.tpt__tiered-pricing' ).first();
		//mainElement is always inserted if the plugin is active...
		//but do NOT have children if there are no prices
		if ( mainElement.children().length ) {
			// Handle variation resets
			jQuery( document ).on( 'reset_data', ( e ) => {
				const $form = jQuery( e.target ).closest( '.variations_form' );
				if ( $form.length ) {
					const productId = $form.data( 'product_id' );
					this.resetPrices( productId );
				}
			} );

			// Handle variation changes
			jQuery( document ).on( 'found_variation', ( e, variation ) => {
				const productId = variation.variation_id;
				this.resetPrices( productId );
			} );
		}
	}

	updateAllPrices( data ) {
		if ( ! data.__instance ) return;

		const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
			this.originalTaxDisplay
		);

		// Update all price containers for this product
		const $priceContainers = jQuery(
			'.tiered-pricing-dynamic-price-wrapper'
		).filter( ( _, container ) => {
			const $container = jQuery( container );
			const containerProductId = parseInt(
				$container.data( 'product-id' )
			);
			const containerParentId = parseInt(
				$container.data( 'parent-id' )
			);

			return containerProductId === containerParentId
				? containerParentId === data.parentId
				: containerProductId === data.productId;
		} );

		$priceContainers.each( ( _, container ) => {
			const $container = jQuery( container );
			if ( $container.data( 'price-type' ) === 'dynamic' ) {
				const showOriginalPrice = this.shouldShowOriginalPrice( data );
				const originalPriceInclTax = showOriginalPrice
					? this.getOriginalPrice( data, true )
					: null;
				const originalPriceExclTax = showOriginalPrice
					? this.getOriginalPrice( data, false )
					: null;

				const newHtml = this.getWtsHtml(
					displayIncludingVat,
					data.__instance.formatting.formatPrice( data.price ),
					data.__instance.formatting.formatPrice(
						data.price_excl_tax
					),
					true,
					originalPriceInclTax
						? data.__instance.formatting.formatPrice(
								originalPriceInclTax
						  )
						: null,
					originalPriceExclTax
						? data.__instance.formatting.formatPrice(
								originalPriceExclTax
						  )
						: null
				);
				$container.html( newHtml );
			}
		} );

		this.updateSummaryTable( data, displayIncludingVat );
	}

	shouldShowOriginalPrice( data ) {
		// Show original price if product is in sale
		if ( data.__instance.dataProvider.isProductOnSale() ) {
			return true;
		}

		return (
			data.__instance.dataProvider.showTieredPriceAsDiscount() &&
			data.pricing.tieredQuantity >
				data.__instance.dataProvider.getMinimum()
		);
	}

	getOriginalPrice( data, includingTax = true ) {
		if ( data.__instance.dataProvider.isProductOnSale() ) {
			return includingTax
				? data.__instance.dataProvider.getRegularPrice()
				: data.__instance.dataProvider.getRegularPrice() *
						( data.price_excl_tax / data.price );
		} else {
			return includingTax
				? data.__instance.dataProvider.getOriginalPrice()
				: data.__instance.dataProvider.getOriginalPrice() *
						( data.price_excl_tax / data.price );
		}
	}

	updateSummaryTable( data, displayIncludingVat ) {
		const summaryTable = this.getSummaryTable( data.parentId );
		if ( ! summaryTable || ! summaryTable.length ) return;

		// Update product price
		const productPriceHtml = this.getWtsHtml(
			displayIncludingVat,
			data.__instance.formatting.formatPrice( data.price ),
			data.__instance.formatting.formatPrice( data.price_excl_tax ),
			true
		);

		// Update total price
		const totalHtml = this.getWtsHtml(
			displayIncludingVat,
			data.__instance.formatting.formatPrice(
				data.price * data.quantity
			),
			data.__instance.formatting.formatPrice(
				data.price_excl_tax * data.quantity
			),
			true
		);
		setTimeout( function () {
			summaryTable
				.find( '[data-tier-pricing-table-summary-product-price]' )
				.html( productPriceHtml );
			summaryTable
				.find( '[data-tier-pricing-table-summary-total]' )
				.html( totalHtml );
		}, 10 );
	}

	getSummaryTable( productId ) {
		return productId
			? jQuery( '.tier-pricing-summary-table' ).filter(
					`[data-product-id=${ productId }]`
			  )
			: jQuery( '.tier-pricing-summary-table' );
	}

	resetPrices( productId ) {
		const backup = this.priceBackups.get( productId );
		if ( backup ) {
			jQuery( '.tiered-pricing-dynamic-price-wrapper' )
				.filter( `[data-product-id=${ productId }]` )
				.html( backup );
		}
		TaxSwitchHelper.setPriceClasses( this.originalTaxDisplay );
	}

	getWtsHtml(
		displayIncludingVat,
		tieredIncludingVatHTML,
		tieredExcludingVatHTML,
		setText = false,
		originalPriceInclHTML = null,
		originalPriceExclHTML = null
	) {
		const vm = this;
		// Helper function for price with optional original price
		const getPriceHtml = ( currentPrice, originalPrice ) => {
			if ( originalPrice ) {
				return `<del>${ originalPrice }</del> <ins>${ currentPrice }</ins>`;
			}
			return currentPrice;
		};

		const priceSection = `
        <span class="wts-price-wrapper">
            <span class="wts-price-incl ${
				displayIncludingVat ? 'wts-active' : 'wts-inactive'
			}">
                ${ getPriceHtml(
					tieredIncludingVatHTML,
					originalPriceInclHTML
				) }
            </span>
            <span class="wts-price-excl ${
				! displayIncludingVat ? 'wts-active' : 'wts-inactive'
			}">
                ${ getPriceHtml(
					tieredExcludingVatHTML,
					originalPriceExclHTML
				) }
            </span>
        </span>
    `;

		if ( setText ) {
			if ( ! vm.vatTexts ) {
				vm.vatTexts = TaxSwitchHelper.getVatTexts();
			}

			if ( vm.vatTexts ) {
				const textSection = `
                <span class="wts-price-wrapper">
                    <span class="wts-price-incl ${
						displayIncludingVat ? 'wts-active' : 'wts-inactive'
					}">
                        ${ vm.vatTexts.including }
                    </span>
                    <span class="wts-price-excl ${
						! displayIncludingVat ? 'wts-active' : 'wts-inactive'
					}">
                        ${ vm.vatTexts.excluding }
                    </span>
                </span>
            `;

				return `
                <span class="wts-price-container">
                    ${ priceSection }
                    ${ textSection }
                </span>
            `;
			}
		}

		return `
        <span class="wts-price-container">
            ${ priceSection }
        </span>
    `;
	}
}

export default TieredPriceTable;
