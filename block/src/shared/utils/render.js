export function shouldBeEnabled() {
	const viewConfig = window.wtsViewObject || {
		checkPriceElements: false,
	};

	if ( viewConfig.checkPriceElements ) {
		const prices = document.querySelectorAll( '.wts-price-wrapper' );
		if ( prices.length > 0 ) {
			return true;
		}

		return false;
	}

	return true;
}
