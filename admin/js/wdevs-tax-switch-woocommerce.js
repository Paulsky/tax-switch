( function ( $, window, _ ) {
	const colorDefaults = {
		switch_color: '#333333',
		switch_color_checked: '#ffffff',
		switch_background_color: '#e9e9ea',
		switch_background_color_checked: '#34c759',
		switch_text_color: '#333333',
	};
	const targetModal = 'wdevs-tax-switch-modal';

	$( '#wdevs-tax-switch-shortcode' ).val(
		'[wdevs_tax_switch switch-type="switch" switch-color="#333333" switch-color-checked="#ffffff" switch-background-color="#e9e9ea" switch-background-color-checked="#34c759" switch-text-color="#333333" class-name="is-style-default"]'
	);

	$( '#wdevs-generate-shortcode' ).on( 'click', function ( e ) {
		e.preventDefault();
		$( this ).WCBackboneModal( {
			template: 'wdevs-tax-switch-modal',
		} );
	} );

	$( document.body ).on(
		'wc_backbone_modal_response',
		function ( e, target, postedData ) {
			if ( target === targetModal ) {
				let attributes = [];

				//Direct mapping to shortcode attributes
				Object.keys( postedData ).forEach( function ( key ) {
					if ( postedData[ key ] ) {
						attributes.push(
							`${ key.replace( /_/g, '-' ) }="${
								postedData[ key ]
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

	const renderPreview = _.debounce( function ( data ) {
		$( '#preview' ).html(
			'<div class="spinner is-active" style="float:left;"></div>'
		);

		jQuery.ajax( {
			url: window.wtsAjaxObject.ajaxUrl,
			type: 'POST',
			data: {
				action: window.wtsAjaxObject.renderAction,
				nonce: window.wtsAjaxObject.nonce,
				attributes: data,
			},
			success: function ( response ) {
				if ( response.success ) {
					if ( response.data && response.data.shortcode ) {
						$( '#preview' ).html( response.data.shortcode );
						const appearedEvent = new CustomEvent(
							'wdevs-tax-switch-appeared'
						);
						document.dispatchEvent( appearedEvent );
					}
				}
			},
		} );
	}, 500 );

	$( document ).on( 'wc_backbone_modal_loaded', function ( e, target ) {
		if ( target === targetModal ) {
			$( '.color-picker' ).wpColorPicker( {
				change: function ( event, ui ) {
					$( this ).trigger( 'input' ); //Triggers wc_backbone_modal_validation
				},
			} );

			let selector;
			Object.keys( colorDefaults ).forEach( function ( key ) {
				selector = `input[name="${ key }"]`;
				if ( $( selector ).length ) {
					$( selector )
						.val( colorDefaults[ key ] )
						.wpColorPicker( 'color', colorDefaults[ key ] );
				}
			} );

			$( '#wdevs-style-selector' ).on(
				'change input',
				function ( event ) {
					event.preventDefault();

					$( 'input[name="class-name"]' ).val( $( this ).val() );
				}
			);

			//Initialize preview
			$( '#wc-backbone-modal-dialog form' ).trigger( 'input' );
		}
	} );

	$( document ).on(
		'wc_backbone_modal_validation',
		function ( e, target, data ) {
			renderPreview( data );
		}
	);
} )( jQuery, window, _ );
