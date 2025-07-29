import TaxSwitchHelper from '../../shared/TaxSwitchHelper';

class FiboFilters {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
	}

	init() {
		const vm = this;
		vm.registerFiboFiltersEvents();
	}

	registerFiboFiltersEvents() {
		const vm = this;

		if ( typeof window.fiboFilters !== 'undefined' ) {
			function fibofiltersGridCompleted() {
				TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
			}

			//for page init and pagination
			window.fiboFilters.hooks.addAction(
				'fiboFilters.renderer.products_loaded',
				'fibofilters',
				fibofiltersGridCompleted
			);

			//for lazy loaded products
			window.fiboFilters.hooks.addAction(
				'fiboFilters.renderer.product_placeholders_overwritten',
				'fibofilters',
				fibofiltersGridCompleted
			);
		}
	}
}

export default FiboFilters;
