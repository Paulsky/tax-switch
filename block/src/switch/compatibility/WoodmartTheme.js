class WoodmartTheme {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
	}

	init() {
		this.registerWoodmartEvents();
	}

	registerWoodmartEvents() {
		// Listen to WoodMart's header clone event
		window.addEventListener( 'wdHeaderBuilderCloneCreated', function () {
			// Dispatch custom event to re-initialize Tax Switch components in cloned header
			document.dispatchEvent(
				new CustomEvent( 'wdevs-tax-switch-appeared' )
			);
		} );
	}
}

export default WoodmartTheme;
