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
		//const interactiveSelector =
		//   'a, button, input, select, textarea, label, [role="button"], [tabindex], .wdevs-tax-switch-slider, .wd-role-btn, [onclick]';
		//
		// function isInteractive(el) {
		//   return el.matches(interactiveSelector) || el.onclick !== null;
		// }
		//
		// function onFirstTouch(e) {
		//   const target = e.target;
		//   const interactiveEl = isInteractive(target) ? target : target.closest(interactiveSelector);
		//
		//
		//   document.removeEventListener('touchstart', onFirstTouch);
		//
		//   if (!interactiveEl) return;
		//
		//   if (interactiveEl.matches('input, textarea, select')) {
		//     interactiveEl.focus({ preventScroll: true });
		//     return;
		//   }
		//
		//
		//   if (
		//     interactiveEl.classList.contains('wdevs-tax-switch-slider') ||
		//     interactiveEl.classList.contains('wd-role-btn')
		//   ) {
		//     e.preventDefault();
		//     e.stopPropagation();
		//   }
		//
		//   interactiveEl.click();
		// }
		//
		// document.addEventListener('touchstart', onFirstTouch, { passive: false, once: true });
		//

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
