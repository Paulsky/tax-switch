import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import { getIsSwitched, setIsDisabled } from '../../shared/store';
import { select, subscribe } from '@wordpress/data';

class FacetWP {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.facetHistory = {};
	}

	getStepPrecision( settings ) {
		const rawStep =
			typeof settings.step !== 'undefined'
				? parseFloat( settings.step )
				: NaN;

		if ( Number.isNaN( rawStep ) || rawStep <= 0 ) {
			return 1;
		}

		const stepString = rawStep.toString();
		if ( stepString.includes( '.' ) ) {
			return stepString.split( '.' )[ 1 ].length;
		}

		return 1;
	}

	roundToSliderStep( value, settings ) {
		if ( value === null || value === undefined ) {
			return value;
		}

		const numericValue = Number( value );
		if ( ! Number.isFinite( numericValue ) ) {
			return numericValue;
		}

		const precision = this.getStepPrecision( settings );
		const multiplier = Math.pow( 10, precision );

		const rounded = Math.round( numericValue * multiplier ) / multiplier;

		return Number( rounded.toFixed( precision ) );
	}

	countDecimals( value ) {
		if ( value == null || value === '' ) return 0;
		let str = value.toString().trim().replace( ',', '.' );
		const expMatch = str.match( /e-(\d+)$/i );
		if ( expMatch ) return parseInt( expMatch[ 1 ], 10 );
		const parts = str.split( '.' );
		return parts[ 1 ] ? parts[ 1 ].length : 0;
	}

	init() {
		this.registerFacetWPEvents();
		this.registerTaxSwitchListener();
		this.setupFacetWPLoadedListener();
	}

	calculateAndStoreRangeBounds() {
		if ( typeof window.FWP === 'undefined' || ! window.FWP.settings ) {
			return;
		}

		const vm = this;

		Object.keys( window.FWP.settings ).forEach( ( facetName ) => {
			const settings = window.FWP.settings[ facetName ];

			if ( window.FWP.facet_type[ facetName ] !== 'slider' ) {
				return;
			}

			const hasPriceContainer =
				( settings.suffix &&
					settings.suffix.includes( 'wts-price-container' ) ) ||
				( settings.prefix &&
					settings.prefix.includes( 'wts-price-container' ) );

			if ( ! hasPriceContainer ) {
				return;
			}

			// Get original values from server response (settings.start or lower/upper)
			const originalMin = vm.roundToSliderStep(
				parseFloat(
					settings.start ? settings.start[ 0 ] : settings.lower
				),
				settings
			);
			const originalMax = vm.roundToSliderStep(
				parseFloat(
					settings.start ? settings.start[ 1 ] : settings.upper
				),
				settings
			);

			// Get original range bounds from settings
			const originalRangeMin = vm.roundToSliderStep(
				parseFloat( settings.range?.min ?? originalMin ),
				settings
			);
			const originalRangeMax = vm.roundToSliderStep(
				parseFloat( settings.range?.max ?? originalMax ),
				settings
			);

			const alternateMin = vm.roundToSliderStep(
				TaxSwitchHelper.calculateAlternatePrice(
					originalMin,
					vm.originalTaxDisplay,
					vm.taxRate
				),
				settings
			);
			const alternateMax = vm.roundToSliderStep(
				TaxSwitchHelper.calculateAlternatePrice(
					originalMax,
					vm.originalTaxDisplay,
					vm.taxRate
				),
				settings
			);

			// Calculate alternate range bounds (prevent negative values)
			const alternateRangeMin = Math.max(
				0,
				vm.roundToSliderStep(
					TaxSwitchHelper.calculateAlternatePrice(
						originalRangeMin,
						vm.originalTaxDisplay,
						vm.taxRate
					),
					settings
				)
			);
			const alternateRangeMax = Math.max(
				0,
				vm.roundToSliderStep(
					TaxSwitchHelper.calculateAlternatePrice(
						originalRangeMax,
						vm.originalTaxDisplay,
						vm.taxRate
					),
					settings
				)
			);

			const historyData = {
				original: {
					lower: originalMin,
					upper: originalMax,
					rangeMin: originalRangeMin,
					rangeMax: originalRangeMax,
				},
				alternate: {
					lower: alternateMin,
					upper: alternateMax,
					rangeMin: alternateRangeMin,
					rangeMax: alternateRangeMax,
				},
			};

			vm.facetHistory[ facetName ] = historyData;
		} );
	}

	setupFacetWPLoadedListener() {
		window.addEventListener( 'facetwp-loaded', () => {
			this.calculateAndStoreRangeBounds();

			this.initializePriceSliders();

			setIsDisabled( false );
		} );
	}

	initializePriceSliders() {
		if ( typeof window.FWP === 'undefined' || ! window.FWP.settings ) {
			return;
		}

		const isSwitched = getIsSwitched();
		const vm = this;

		Object.keys( window.FWP.settings ).forEach( ( facetName ) => {
			const settings = window.FWP.settings[ facetName ];

			if ( window.FWP.facet_type[ facetName ] !== 'slider' ) {
				return;
			}

			const hasPriceContainer =
				( settings.suffix &&
					settings.suffix.includes( 'wts-price-container' ) ) ||
				( settings.prefix &&
					settings.prefix.includes( 'wts-price-container' ) );

			if ( ! hasPriceContainer ) {
				return;
			}

			if ( ! vm.facetHistory[ facetName ] ) {
				return;
			}

			const sliderElement = document.querySelector(
				`.facetwp-facet[data-name="${ facetName }"] .facetwp-slider`
			);

			if ( ! sliderElement || ! sliderElement.noUiSlider ) {
				return;
			}

			const history = vm.facetHistory[ facetName ];
			let rangeMin, rangeMax, valueMin, valueMax;

			if ( isSwitched ) {
				rangeMin = history.alternate.rangeMin;
				rangeMax = history.alternate.rangeMax;
				valueMin = history.alternate.lower;
				valueMax = history.alternate.upper;
			} else {
				rangeMin = history.original.rangeMin;
				rangeMax = history.original.rangeMax;
				valueMin = history.original.lower;
				valueMax = history.original.upper;
			}

			sliderElement.noUiSlider.updateOptions(
				{
					range: {
						min: rangeMin,
						max: rangeMax,
					},
				},
				false
			);

			sliderElement.noUiSlider.set( [ valueMin, valueMax ] );

			// Register event listeners to toggle price classes during slider interaction
			sliderElement.noUiSlider.on( 'slide', function () {
				TaxSwitchHelper.togglePriceClasses(
					vm.originalTaxDisplay,
					getIsSwitched()
				);
			} );

			sliderElement.noUiSlider.on( 'set', function () {
				TaxSwitchHelper.togglePriceClasses(
					vm.originalTaxDisplay,
					getIsSwitched()
				);
			} );
		} );
	}

	registerTaxSwitchListener() {
		let previousIsSwitched = getIsSwitched();
		const vm = this;

		subscribe( () => {
			const currentIsSwitched = select(
				'wdevs-tax-switch/store'
			).getIsSwitched();

			if ( currentIsSwitched !== previousIsSwitched ) {
				previousIsSwitched = currentIsSwitched;

				if ( typeof window.FWP !== 'undefined' ) {
					setIsDisabled( true );

					vm.updateAllPriceSliders( currentIsSwitched );
					window.FWP.refresh();
				}
			}
		} );
	}

	updateAllPriceSliders( isSwitched ) {
		if ( typeof window.FWP === 'undefined' || ! window.FWP.settings ) {
			return;
		}

		const vm = this;

		Object.keys( window.FWP.settings ).forEach( ( facetName ) => {
			const settings = window.FWP.settings[ facetName ];

			if ( window.FWP.facet_type[ facetName ] !== 'slider' ) {
				return;
			}

			const hasPriceContainer =
				( settings.suffix &&
					settings.suffix.includes( 'wts-price-container' ) ) ||
				( settings.prefix &&
					settings.prefix.includes( 'wts-price-container' ) );

			if ( ! hasPriceContainer ) {
				return;
			}

			if ( ! vm.facetHistory[ facetName ] ) {
				return;
			}

			const sliderElement = document.querySelector(
				`.facetwp-facet[data-name="${ facetName }"] .facetwp-slider`
			);

			if ( ! sliderElement || ! sliderElement.noUiSlider ) {
				return;
			}

			const history = vm.facetHistory[ facetName ];
			let rangeMin, rangeMax, valueMin, valueMax;

			if ( isSwitched ) {
				rangeMin = history.alternate.rangeMin;
				rangeMax = history.alternate.rangeMax;
				valueMin = history.alternate.lower;
				valueMax = history.alternate.upper;
			} else {
				rangeMin = history.original.rangeMin;
				rangeMax = history.original.rangeMax;
				valueMin = history.original.lower;
				valueMax = history.original.upper;
			}

			// Update slider range bounds
			sliderElement.noUiSlider.updateOptions(
				{
					range: {
						min: rangeMin,
						max: rangeMax,
					},
				},
				false
			);

			// Update slider values
			sliderElement.noUiSlider.set( [ valueMin, valueMax ] );
		} );
	}

	registerFacetWPEvents() {
		const vm = this;

		if ( typeof window.FWP !== 'undefined' && window.FWP.hooks ) {
			window.FWP.hooks.addAction(
				'facetwp/refresh/slider',
				function ( $facet, facetName ) {
					const isSwitched = getIsSwitched();

					// if ($facet.find('.wts-price-container').length===0) {
					// 	return;
					// }

					if ( ! vm.taxRate ) {
						return;
					}

					const settings = window.FWP.settings[ facetName ];
					if ( ! settings ) {
						return;
					}

					const hasPriceContainer =
						( settings.suffix &&
							settings.suffix.includes(
								'wts-price-container'
							) ) ||
						( settings.prefix &&
							settings.prefix.includes( 'wts-price-container' ) );

					if ( ! hasPriceContainer ) {
						return;
					}

					if ( isSwitched ) {
						const originalMin = vm.roundToSliderStep(
							parseFloat( settings.lower ),
							settings
						);
						const originalMax = vm.roundToSliderStep(
							parseFloat( settings.upper ),
							settings
						);

						// Get original range bounds from settings
						const originalRangeMin = vm.roundToSliderStep(
							parseFloat( settings.range?.min ?? originalMin ),
							settings
						);
						const originalRangeMax = vm.roundToSliderStep(
							parseFloat( settings.range?.max ?? originalMax ),
							settings
						);

						const alternateMin = vm.roundToSliderStep(
							TaxSwitchHelper.calculateOriginalPrice(
								originalMin,
								vm.originalTaxDisplay,
								vm.taxRate
							),
							settings
						);
						const alternateMax = vm.roundToSliderStep(
							TaxSwitchHelper.calculateOriginalPrice(
								originalMax,
								vm.originalTaxDisplay,
								vm.taxRate
							),
							settings
						);

						const alternateRangeMin = Math.max(
							0,
							vm.roundToSliderStep(
								TaxSwitchHelper.calculateOriginalPrice(
									originalRangeMin,
									vm.originalTaxDisplay,
									vm.taxRate
								),
								settings
							)
						);
						const alternateRangeMax = Math.max(
							0,
							vm.roundToSliderStep(
								TaxSwitchHelper.calculateOriginalPrice(
									originalRangeMax,
									vm.originalTaxDisplay,
									vm.taxRate
								),
								settings
							)
						);

						const historyData = {
							original: {
								lower: originalMin,
								upper: originalMax,
								rangeMin: originalRangeMin,
								rangeMax: originalRangeMax,
							},
							alternate: {
								lower: alternateMin,
								upper: alternateMax,
								rangeMin: alternateRangeMin,
								rangeMax: alternateRangeMax,
							},
						};

						vm.facetHistory[ facetName ] = historyData;

						const sliderElement = document.querySelector(
							`.facetwp-facet[data-name="${ facetName }"] .facetwp-slider`
						);

						let decimals = 2;
						if ( sliderElement && sliderElement.noUiSlider ) {
							const values = sliderElement.noUiSlider.get();
							if ( values && values.length ) {
								decimals = vm.countDecimals( values[ 0 ] );
							}
						}

						window.FWP.facets[ facetName ] = [
							Number( alternateMin ).toFixed( decimals ),
							Number( alternateMax ).toFixed( decimals ),
						];
					} else {
						if ( vm.facetHistory[ facetName ] ) {
							delete vm.facetHistory[ facetName ];
						}
					}
				}
			);
		}
	}
}

export default FacetWP;
