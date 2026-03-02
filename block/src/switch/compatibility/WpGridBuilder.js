import TaxSwitchHelper from '../../shared/TaxSwitchHelper';

class WpGridBuilder {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
	}

	init() {
		this.registerWpGridBuilderEvents();
	}

	registerWpGridBuilderEvents() {
		if ( ! window.WP_Grid_Builder || ! window.WP_Grid_Builder.on ) {
			return;
		}

		const vm = this;
		window.WP_Grid_Builder.on( 'init', function ( wpgb ) {
			if ( ! wpgb.facets || ! wpgb.facets.on ) {
				return;
			}

			wpgb.facets.on( 'appended', function () {
				TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
			} );
		} );
	}
}

export default WpGridBuilder;
