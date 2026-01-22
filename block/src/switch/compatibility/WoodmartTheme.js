class WoodmartTheme {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
	}

	init() {
		this.registerWoodmartEvents();
	}

	registerWoodmartEvents() {
		// Single reported edge case:
		// On one site using the Woodmart theme, an issue was reported in Safari on iOS. Other browsers did work.
		// After page load, the first touch interaction did not work:
		// links, buttons, and other interactive elements were not triggered.
		// This problem did NOT occur in other browsers on iOS.
		//
		//a (heavy) workaround was:
		//function isInteractive(el) {
		//     return (
		//         el.matches('a, button, input, select, textarea, label, [role="button"], [tabindex], .wdevs-tax-switch-slider, .wd-role-btn') ||
		//         el.onclick !== null
		//     );
		// }
		//
		// function onFirstTouch(e) {
		//     const el = e.target;
		//     const interactiveEl = isInteractive(el) ? el : el.closest('a, button, input, select, textarea, label, [role="button"], [tabindex], .wdevs-tax-switch-slider, .wd-role-btn, [onclick]');
		//
		//     if (!interactiveEl) {
		//         document.removeEventListener('touchstart', onFirstTouch);
		//         return;
		//     }
		//
		//
		//     if (interactiveEl.matches('input, textarea, select')) {
		//         document.removeEventListener('touchstart', onFirstTouch);
		//         return;
		//     }
		//
		//     e.preventDefault();
		//     e.stopPropagation();
		//
		//     requestAnimationFrame(() => {
		//         interactiveEl.click();
		//     });
		// }
		//
		// document.addEventListener('touchstart', onFirstTouch, { passive: false, once: true });

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
