class TaxSwitchElementBuilder {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
	}

	build(
		displayIncludingVat,
		originalPrice,
		alternatePrice,
		vatTexts = null
	) {
		const vm = this;

		const prices = vm.getPricesBasedOnTaxDisplay(
			originalPrice,
			alternatePrice
		);
		const includingPrice = prices.including;
		const excludingPrice = prices.excluding;

		function getVisibilityClass( isVisible ) {
			return isVisible ? 'wts-active' : 'wts-inactive';
		}

		function createPriceElement( price, isIncludingVat ) {
			const visibilityClass = getVisibilityClass(
				isIncludingVat === displayIncludingVat
			);
			const priceType = isIncludingVat ? 'incl' : 'excl';

			return `
         <span class="wts-price-${ priceType } ${ visibilityClass }">
            ${ price }
         </span>
      `;
		}

		let template = `
      <span class="wts-price-container">
         <span class="wts-price-wrapper">
            ${ createPriceElement( includingPrice, true ) }
            ${ createPriceElement( excludingPrice, false ) }
         </span>
   `;

		if ( vatTexts ) {
			function createTextElement( text, isIncludingVat ) {
				const visibilityClass = getVisibilityClass(
					isIncludingVat === displayIncludingVat
				);
				const priceType = isIncludingVat ? 'incl' : 'excl';

				return `
            <span class="wts-price-${ priceType } ${ visibilityClass }">
               ${ text }
            </span>
         `;
			}

			template += `
         <span class="wts-price-wrapper">
            ${ createTextElement( vatTexts.including, true ) }
            ${ createTextElement( vatTexts.excluding, false ) }
         </span>
      `;
		}

		template += '</span>';

		return template.trim();
	}

	getPricesBasedOnTaxDisplay( originalPrice, alternatePrice ) {
		if ( this.originalTaxDisplay === 'incl' ) {
			return {
				including: originalPrice,
				excluding: alternatePrice,
			};
		}

		return {
			including: alternatePrice,
			excluding: originalPrice,
		};
	}

	static getVatTexts( existingWrapper = null ) {
		const space = document.createTextNode( ' ' ).nodeValue;
		let $includingText, $excludingText;
		if ( existingWrapper ) {
			const $wrapper = jQuery( existingWrapper );
			$includingText = $wrapper
				.find( '.wts-price-incl .wts-vat-text' )
				.first();
			$excludingText = $wrapper
				.find( '.wts-price-excl .wts-vat-text' )
				.first();

			if ( $includingText.length || $excludingText.length ) {
				return {
					including: $includingText.length
						? space + $includingText.clone().prop( 'outerHTML' )
						: '',
					excluding: $excludingText.length
						? space + $excludingText.clone().prop( 'outerHTML' )
						: '',
				};
			}
		}

		$includingText = jQuery(
			'.wts-price-wrapper .wts-price-incl .wts-vat-text'
		).first();
		$excludingText = jQuery(
			'.wts-price-wrapper .wts-price-excl .wts-vat-text'
		).first();

		return {
			including: $includingText.length
				? space + $includingText.clone().prop( 'outerHTML' )
				: '',
			excluding: $excludingText.length
				? space + $excludingText.clone().prop( 'outerHTML' )
				: '',
		};
	}

	static getVatTextElement(
		displayIncludingVat,
		includingText,
		excludingText
	) {
		return `<span class="wts-price-wrapper">
                    <span class="wts-price-incl ${
						displayIncludingVat ? 'wts-active' : 'wts-inactive'
					}">
                        ${ includingText }
                    </span>
                    <span class="wts-price-excl ${
						! displayIncludingVat ? 'wts-active' : 'wts-inactive'
					}">
                          ${ excludingText }
                    </span>
                </span>
            `;
	}
}

export default TaxSwitchElementBuilder;
