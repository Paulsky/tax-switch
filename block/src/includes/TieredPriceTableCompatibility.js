import jQuery from 'jquery';
import TaxSwitchHelper from './TaxSwitchHelper';

class TieredPriceTableCompatibility {
	static initialize( originalTaxDisplay ) {
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
					vm.setPrices( originalTaxDisplay );
				}
			} );
			jQuery( '.tpt__tiered-pricing' ).on(
				'tiered_price_update',
				function ( event, data ) {
					// setTimeout( function () {
					vm.setFormattedPrices( originalTaxDisplay, data );
					// }, 10 );
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
		const wrapper = jQuery( '.tiered-pricing-wrapper' );
		if ( ! wrapper.length ) {
			return null;
		}

		const mainElement = wrapper.find( '[data-regular-price]' );
		if ( ! mainElement.length ) {
			return null;
		}

		return mainElement;
	}

	static getSinglePrices() {
		const mainElement = this.getMainElement();
		if ( ! mainElement ) {
			return null;
		}

		const singleUnitElement = mainElement.find(
			'[data-tiered-quantity="1"]'
		);
		if ( ! singleUnitElement.length ) {
			return null;
		}

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

		const singleUnitPrices = this.getSinglePrices();
		if ( ! singleUnitPrices ) {
			return;
		}

		const priceIncludingVat = singleUnitPrices[ 0 ];
		const priceExcludingVat = singleUnitPrices[ 1 ];

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

		jQuery( '.qty' ).trigger( 'change' );
	}

	static setFormattedPrices( originalTaxDisplay, wctptData ) {
		const wctptInstance = wctptData.__instance;
		if ( ! wctptInstance ) {
			return;
		}
		const mainElement = this.getMainElement();
		if ( ! mainElement ) {
			return;
		}

		const singleUnitPrices = this.getSinglePrices();
		if ( ! singleUnitPrices ) {
			return;
		}

		const priceIncludingVat = singleUnitPrices[ 0 ];
		const priceExcludingVat = singleUnitPrices[ 1 ];

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
				const html =
					'<div class="wts-price-wrapper">' +
					'<div class="wts-price-incl ' +
					( displayIncludingVat ? 'wts-active' : 'wts-inactive' ) +
					'">' +
					tieredIncludingVatHTML +
					'</div>' +
					'<div class="wts-price-excl ' +
					( ! displayIncludingVat ? 'wts-active' : 'wts-inactive' ) +
					'">' +
					tieredExcludingVatHTML +
					'</div>' +
					'</div>';
				element.find( '.amount' ).replaceWith( html );
			}

			if ( element.hasClass( 'tiered-pricing--active' ) ) {
				if ( element.attr( 'data-tiered-quantity' ) == 1 ) {
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
	}
}

export default TieredPriceTableCompatibility;
