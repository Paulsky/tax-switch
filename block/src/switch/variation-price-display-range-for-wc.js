import jQuery from 'jquery';
import TaxSwitchHelper from '../shared/TaxSwitchHelper';

window.addEventListener( 'DOMContentLoaded', () => {
	const viewConfig = window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

	//See variation-price-display/public/js/public.js: @changePrice fadeOut / fadeIn
	let timeout = 200; //default in class-variation-price-display-front.php
	if ( window.vpd_public_object && vpd_public_object.animationSpeed ) {
		timeout = parseInt( vpd_public_object.animationSpeed );
	}
	//1.1 because of first fade out, then fade in. 1.1 should be after the first fade out
	timeout = timeout * 1.1;

	jQuery( document ).on( 'show_variation', function ( priceContainer ) {
		setTimeout( function () {
			TaxSwitchHelper.setPriceClasses( viewConfig.originalTaxDisplay );
		}, timeout );
	} );
} );
