import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class TieredPriceTable {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.isInclTaxDisplay = originalTaxDisplay === 'incl';
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
		if ( ! data.__instance ) {
			return;
		}

		const vm = this;
		const displayIncludingVat = TaxSwitchHelper.displayIncludingVat(
			this.originalTaxDisplay
		);
		const showOriginalPrice = vm.shouldShowOriginalPrice( data );

		const current = data.__instance.formatting.formatPrice( data.price );
		const alternate = data.__instance.formatting.formatPrice(
			vm.getAlternatePrice( data )
		);
		const original = showOriginalPrice
			? data.__instance.formatting.formatPrice(
					vm.getOriginalPrice( data, true )
			  )
			: null;
		const alternateOriginal = showOriginalPrice
			? data.__instance.formatting.formatPrice(
					vm.getOriginalPrice( data, false )
			  )
			: null;

		const $priceContainers = jQuery(
			'.tiered-pricing-dynamic-price-wrapper'
		).filter( ( _, container ) => {
			const $container = jQuery( container );
			const productId = parseInt( $container.data( 'product-id' ) );
			const parentId = parseInt( $container.data( 'parent-id' ) );

			return productId === parentId
				? parentId === data.parentId
				: productId === data.productId;
		} );

		$priceContainers.each( ( _, container ) => {
			const $container = jQuery( container );
			if ( $container.data( 'price-type' ) !== 'dynamic' ) {
				return;
			}
			$container.html(
				vm.getWtsHtml(
					displayIncludingVat,
					current,
					alternate,
					true,
					original,
					alternateOriginal
				)
			);
		} );

		vm.updateSummaryTable( data, displayIncludingVat );
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

	getOriginalPrice( data, getOriginalTaxPrice = true ) {
		const alternatePrice = this.getAlternatePrice( data );
		if ( data.__instance.dataProvider.isProductOnSale() ) {
			return getOriginalTaxPrice
				? data.__instance.dataProvider.getRegularPrice()
				: data.__instance.dataProvider.getRegularPrice() *
						( alternatePrice / data.price );
		}
		return getOriginalTaxPrice
			? data.__instance.dataProvider.getOriginalPrice()
			: data.__instance.dataProvider.getOriginalPrice() *
					( alternatePrice / data.price );
	}

	updateSummaryTable( data, displayIncludingVat ) {
		const summaryTable = this.getSummaryTable( data.parentId );
		if ( ! summaryTable || ! summaryTable.length ) {
			return;
		}

		const alternatePrice = this.getAlternatePrice( data );

		const productPriceHtml = this.getWtsHtml(
			displayIncludingVat,
			data.__instance.formatting.formatPrice( data.price ),
			data.__instance.formatting.formatPrice( alternatePrice ),
			true
		);

		const totalHtml = this.getWtsHtml(
			displayIncludingVat,
			data.__instance.formatting.formatPrice(
				data.price * data.quantity
			),
			data.__instance.formatting.formatPrice(
				alternatePrice * data.quantity
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
		tieredVatHTML,
		tieredAlternateVatHTML,
		setText = false,
		originalPriceHTML = null,
		originalPriceAlternateHTML = null
	) {
		const vm = this;

		const getPriceHtml = ( currentPrice, originalPrice ) => {
			if ( originalPrice ) {
				return `<del>${ originalPrice }</del> <ins>${ currentPrice }</ins>`;
			}
			return currentPrice;
		};

		// If original tax display is exclusive, we need to switch the order of the prices
		const [ inclPrice, exclPrice ] = this.isInclTaxDisplay
			? [ tieredVatHTML, tieredAlternateVatHTML ]
			: [ tieredAlternateVatHTML, tieredVatHTML ];

		const [ inclOriginalPrice, exclOriginalPrice ] = this.isInclTaxDisplay
			? [ originalPriceHTML, originalPriceAlternateHTML ]
			: [ originalPriceAlternateHTML, originalPriceHTML ];

		const priceSection = `
        <span class="wts-price-wrapper">
            <span class="wts-price-incl ${
				displayIncludingVat ? 'wts-active' : 'wts-inactive'
			}">
                ${ getPriceHtml( inclPrice, inclOriginalPrice ) }
            </span>
            <span class="wts-price-excl ${
				! displayIncludingVat ? 'wts-active' : 'wts-inactive'
			}">
                ${ getPriceHtml( exclPrice, exclOriginalPrice ) }
            </span>
        </span>
    `;

		if ( setText ) {
			if ( ! vm.vatTexts ) {
				vm.vatTexts = TaxSwitchElementBuilder.getVatTexts();
			}

			if ( vm.vatTexts ) {
				const textSection = TaxSwitchElementBuilder.getVatTextElement(
					displayIncludingVat,
					vm.vatTexts.including,
					vm.vatTexts.excluding
				);

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

	getAlternatePrice( data ) {
		if ( this.isInclTaxDisplay ) {
			return data.pricing.price_excl_tax;
		}
		return data.pricing.price_incl_tax;
	}
}

export default TieredPriceTable;
