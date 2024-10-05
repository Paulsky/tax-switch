import jQuery from 'jquery';
import TaxSwitchHelper from './TaxSwitchHelper';

class TieredPriceTableCompatibility {
	static initialize( originalTaxDisplay ) {
		//tpt is using 250?
		const vm = this;
		setTimeout( function () {
			vm.setPrices( originalTaxDisplay );
		}, 300 );

		this.registerWooCommerceTieredPriceTableEvents( originalTaxDisplay );
	}

	static registerWooCommerceTieredPriceTableEvents( originalTaxDisplay ) {
		const vm = this;
		if ( jQuery( '.tiered-pricing-dynamic-price-wrapper' ).length ) {
			const wtsBackup = jQuery( '.price .wts-price-wrapper' )
				.first()
				.html();

			jQuery( document ).ajaxComplete( function ( event, xhr, settings ) {
				if (
					settings.url.indexOf( 'wc-ajax=get_pricing_table' ) !== -1
				) {
					setTimeout( function () {
						vm.setPrices( originalTaxDisplay );
					}, 10 );
				}
			} );

			jQuery( document ).on(
				'tiered_price_update',
				function ( event, data ) {
					//	setTimeout(function() {
					vm.setFormattedPrices( originalTaxDisplay, data );
					//		}, 10);
				}
			);

			jQuery( '.variations_form' ).on( 'reset_data', function () {
				setTimeout( function () {
					jQuery( '.price .wts-price-wrapper' )
						.first()
						.html( wtsBackup );
					//in case switched in the meantime
					TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
				}, 10 );
			} );
		}
	}

	static getMainElement() {
		const wrapper = jQuery( '.single-product .tiered-pricing-wrapper' );
		if ( ! wrapper.length ) {
			return null;
		}

		const mainElement = wrapper.first().find( '[data-regular-price]' );
		if ( ! mainElement.length ) {
			return null;
		}

		return mainElement.first();
	}

	static getSingleUnitElement() {
		const mainElement = this.getMainElement();
		if ( ! mainElement ) {
			return null;
		}

		let singleUnitElement = mainElement
			.find( '[data-tiered-quantity="1"]' )
			.first();

		if ( ! singleUnitElement.length ) {
			let lowestQuantity = null;

			mainElement.find( '[data-tiered-quantity]' ).each( function () {
				let quantity = parseInt(
					jQuery( this ).attr( 'data-tiered-quantity' ),
					10
				);

				if ( ! lowestQuantity || quantity < lowestQuantity ) {
					lowestQuantity = quantity;
					singleUnitElement = jQuery( this );
				}
			} );

			if ( ! singleUnitElement || ! singleUnitElement.length ) {
				return null;
			}
		}

		return singleUnitElement;
	}

	static getLowestPrices( singleUnitElement ) {
		let priceIncludingVat = singleUnitElement.data(
			'tiered-price-include-taxes'
		);
		let priceExcludingVat = singleUnitElement.data(
			'tiered-price-exclude-taxes'
		);
		if ( ! priceIncludingVat || ! priceExcludingVat ) {
			return null;
		}
		if (
			typeof priceIncludingVat === 'string' ||
			priceIncludingVat instanceof String
		) {
			priceIncludingVat = priceIncludingVat.trim();
			priceExcludingVat = priceExcludingVat.trim();
		}

		return [ priceIncludingVat, priceExcludingVat ];
	}

	static setPrices( originalTaxDisplay ) {
		const mainElement = this.getMainElement();
		if ( ! mainElement ) {
			return;
		}
		const singleUnitElement = this.getSingleUnitElement();
		if ( ! singleUnitElement ) {
			return;
		}
		const lowestUnitPrices = this.getLowestPrices( singleUnitElement );

		if ( ! lowestUnitPrices ) {
			return;
		}

		const priceIncludingVat = lowestUnitPrices[ 0 ];
		const priceExcludingVat = lowestUnitPrices[ 1 ];

		const displayIncludingVat =
			TaxSwitchHelper.displayIncludingVat( originalTaxDisplay );

		if ( displayIncludingVat ) {
			mainElement.data( 'price', priceIncludingVat );
			mainElement.attr( 'data-price', priceIncludingVat );
			// mainElement.data('regular-price', priceIncludingVat);
			// mainElement.attr('data-regular-price', priceIncludingVat);
		} else {
			mainElement.data( 'price', priceExcludingVat );
			mainElement.attr( 'data-price', priceExcludingVat );
			// mainElement.data('regular-price', priceExcludingVat);
			// mainElement.attr('data-regular-price', priceExcludingVat);
		}

		const unitElements = mainElement.find( '[data-tiered-quantity]' );
		unitElements.each( function ( index, element ) {
			element = jQuery( element );
			let tieredIncludingVat = element.data(
				'tiered-price-include-taxes'
			);
			let tieredExcludingVat = element.data(
				'tiered-price-exclude-taxes'
			);
			if ( ! tieredIncludingVat || ! tieredExcludingVat ) {
				return true; //continue;
			}

			if ( displayIncludingVat ) {
				element.data( 'tiered-price', tieredIncludingVat );
				element.attr( 'data-tiered-price', tieredIncludingVat );
			} else {
				element.data( 'tiered-price', tieredExcludingVat );
				element.attr( 'data-tiered-price', tieredExcludingVat );
			}
		} );

		//tpt is using 300?
		setTimeout( function () {
			jQuery( '.qty' ).trigger( 'change' );
		}, 350 );
	}

	static setFormattedPrices( originalTaxDisplay, wctptData ) {
		const vm = this;
		const wctptInstance = wctptData.__instance;
		if ( ! wctptInstance ) {
			return;
		}
		const mainElement = this.getMainElement();
		if ( ! mainElement ) {
			return;
		}

		const singleUnitElement = this.getSingleUnitElement();
		if ( ! singleUnitElement ) {
			return;
		}
		const lowestUnitPrices = this.getLowestPrices( singleUnitElement );
		if ( ! lowestUnitPrices ) {
			return;
		}

		const priceIncludingVat = lowestUnitPrices[ 0 ];
		const priceExcludingVat = lowestUnitPrices[ 1 ];

		const displayIncludingVat =
			TaxSwitchHelper.displayIncludingVat( originalTaxDisplay );

		const unitElements = mainElement.find( '[data-tiered-quantity]' );

		unitElements.each( function ( index, element ) {
			element = jQuery( element );
			let tieredIncludingVat = element.data(
				'tiered-price-include-taxes'
			);
			let tieredExcludingVat = element.data(
				'tiered-price-exclude-taxes'
			);
			if ( ! tieredIncludingVat || ! tieredExcludingVat ) {
				return true; //continue;
			}
			const tieredIncludingVatHTML =
				wctptInstance.formatting.formatPrice( tieredIncludingVat );
			const tieredExcludingVatHTML =
				wctptInstance.formatting.formatPrice( tieredExcludingVat );

			if ( element.find( '.wts-price-wrapper' ).length === 0 ) {
				const html = vm.getWtsHtml(
					displayIncludingVat,
					tieredIncludingVatHTML,
					tieredExcludingVatHTML
				);
				element.find( '.amount' ).replaceWith( html );
			}

			if ( element.hasClass( 'tiered-pricing--active' ) ) {
				if ( element.is( singleUnitElement ) ) {
					jQuery(
						'.wts-price-incl .tiered-pricing-dynamic-price-wrapper'
					).html( tieredIncludingVatHTML );
					jQuery(
						'.wts-price-excl .tiered-pricing-dynamic-price-wrapper'
					).html( tieredExcludingVatHTML );
				} else {
					const variationIncludingVatHTML =
						wctptInstance.formatting.formatPrice(
							tieredIncludingVat,
							priceIncludingVat
						);
					const variationExcludingVatHTML =
						wctptInstance.formatting.formatPrice(
							tieredExcludingVat,
							priceExcludingVat
						);
					jQuery(
						'.wts-price-incl .tiered-pricing-dynamic-price-wrapper'
					).html( variationIncludingVatHTML );
					jQuery(
						'.wts-price-excl .tiered-pricing-dynamic-price-wrapper'
					).html( variationExcludingVatHTML );
				}
			}
		} );

		const productId = wctptData.parentId;
		let summaryTable;
		if ( productId ) {
			summaryTable = jQuery( '.tier-pricing-summary-table' ).filter(
				`[data-product-id=${ productId }]`
			);
		} else {
			summaryTable = jQuery( '.tier-pricing-summary-table' );
		}

		if ( summaryTable ) {
			const summaryProductPriceHtml = vm.getWtsHtml(
				displayIncludingVat,
				wctptInstance.formatting.formatPrice(
					wctptData.pricing.price_incl_tax,
					false
				),
				wctptInstance.formatting.formatPrice(
					wctptData.pricing.price_excl_tax,
					false
				),
				true
			);
			summaryTable
				.find( '[data-tier-pricing-table-summary-product-price]' )
				.html( summaryProductPriceHtml );

			const summaryTotalHtml = vm.getWtsHtml(
				displayIncludingVat,
				wctptInstance.formatting.formatPrice(
					wctptData.pricing.price_incl_tax * wctptData.quantity,
					false
				),
				wctptInstance.formatting.formatPrice(
					wctptData.pricing.price_excl_tax * wctptData.quantity,
					false
				),
				true
			);
			summaryTable
				.find( '[data-tier-pricing-table-summary-total]' )
				.html( summaryTotalHtml );
		}
	}

	static getWtsHtml(
		displayIncludingVat,
		tieredIncludingVatHTML,
		tieredExcludingVatHTML,
		setText = false
	) {
		let includingTextHtml = '';
		let excludingTextHtml = '';
		if ( setText ) {
			const includingTextElement = jQuery(
				'.wts-price-wrapper .wts-price-incl .wts-vat-text'
			).first();
			if ( includingTextElement ) {
				const excludingTextElement = jQuery(
					'.wts-price-wrapper .wts-price-excl .wts-vat-text'
				).first();
				if ( excludingTextElement ) {
					const spaceNode = jQuery(
						document.createTextNode( ' ' )
					).get( 0 ).nodeValue;
					includingTextHtml =
						spaceNode +
						includingTextElement.clone().prop( 'outerHTML' );
					excludingTextHtml =
						spaceNode +
						excludingTextElement.clone().prop( 'outerHTML' );
				}
			}
		}

		const html =
			'<div class="wts-price-wrapper">' +
			'<div class="wts-price-incl ' +
			( displayIncludingVat ? 'wts-active' : 'wts-inactive' ) +
			'">' +
			tieredIncludingVatHTML +
			'' +
			includingTextHtml +
			'</div>' +
			'<div class="wts-price-excl ' +
			( ! displayIncludingVat ? 'wts-active' : 'wts-inactive' ) +
			'">' +
			tieredExcludingVatHTML +
			'' +
			excludingTextHtml +
			'</div>' +
			'</div>';
		return html;
	}
}

export default TieredPriceTableCompatibility;
