/* global jQuery, wp */
( function ( $ ) {
	const colorDefaults = {
		switch_color: '#333333',
		switch_color_checked: '#ffffff',
		switch_bg_color: '#e9e9ea',
		switch_bg_color_checked: '#34c759',
		switch_text_color: '#333333',
	};

	$( function () {
		// Basis shortcode bij load
		$( '#wdevs-tax-switch-shortcode' ).val(
			'[wdevs_tax_switch switch-type="switch" switch-color="#333333" switch-color-checked="#ffffff" switch-bg-color="#e9e9ea" switch-bg-color-checked="#34c759" switch-text-color="#333333" class-name="is-style-default"]'
		);

		// Modal openen
		$( '#wdevs-generate-shortcode' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( this ).WCBackboneModal( {
				template: 'wdevs-tax-switch-modal',
			} );
		} );

		// Modal response afhandelen
		$( document.body ).on(
			'wc_backbone_modal_response',
			function ( e, target, posted_data ) {
				if ( target === 'wdevs-tax-switch-modal' ) {
					let attributes = [];

					// Directe mapping naar shortcode attributes
					Object.keys( posted_data ).forEach( function ( key ) {
						if ( posted_data[ key ] ) {
							attributes.push(
								`${ key.replace( /_/g, '-' ) }="${
									posted_data[ key ]
								}"`
							);
						}
					} );

					$( '#wdevs-tax-switch-shortcode' )
						.val( `[wdevs_tax_switch ${ attributes.join( ' ' ) }]` )
						.select();
				}
			}
		);

		// Color pickers initialiseren
		$( document ).on( 'wc_backbone_modal_loaded', function ( e, target ) {
			if ( target === 'wdevs-tax-switch-modal' ) {
				$( '.color-picker' ).wpColorPicker();

				// Kleuren instellen
				Object.keys( colorDefaults ).forEach( function ( key ) {
					$( `input[name="${ key }"]` )
						.val( colorDefaults[ key ] )
						.wpColorPicker( 'color', colorDefaults[ key ] );
				} );
			}
		} );
	} );
} )( jQuery );
