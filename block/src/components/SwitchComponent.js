import { Component } from '@wordpress/element';
import { dispatch, select, subscribe } from '@wordpress/data';
import '../includes/store';

class SwitchComponent extends Component {
	constructor( props ) {
		super( props );

		let readOnly = this.parseBooleanValue( props.readOnly );

		this.state = {
			isSwitched: select( 'woo-tax-switch/store' ).getIsSwitched(),
			readOnly: readOnly,
		};

		this.handleChange = this.handleChange.bind( this );
		this.setCookie = this.setCookie.bind( this );

		this.unsubscribe = subscribe( () => {
			const newIsSwitched = select(
				'woo-tax-switch/store'
			).getIsSwitched();

			if ( this.state.isSwitched !== newIsSwitched ) {
				this.setState( { isSwitched: newIsSwitched } );
			}
		} );

		if ( this.parseBooleanValue( props.isSwitched ) ) {
			this.state.isSwitched = true;
			dispatch( 'woo-tax-switch/store' ).setIsSwitched( true );
		}
	}

	parseBooleanValue( value ) {
		if ( value ) {
			return JSON.parse( value );
		}
		return false;
	}

	componentWillUnmount() {
		// Unsubscribe from the store when the component is unmounted
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
	}

	handleChange( event ) {
		const isSwitched = ! this.state.isSwitched;
		dispatch( 'woo-tax-switch/store' ).setIsSwitched( isSwitched );

		if ( ! this.state.readOnly ) {
			this.setCookie();
		}
		const elements = document.querySelectorAll(
			'.wts-inactive, .wts-active'
		);
		elements.forEach( ( element ) => {
			if ( element.classList.contains( 'wts-active' ) ) {
				element.classList.replace( 'wts-active', 'wts-inactive' );
			} else if ( element.classList.contains( 'wts-inactive' ) ) {
				element.classList.replace( 'wts-inactive', 'wts-active' );
			}
		} );
	}

	setCookie() {
		const { ajaxUrl, ajaxNonce, ajaxAction } = this.props;

		if ( ! ajaxUrl || ! ajaxNonce || ! ajaxAction ) {
			return;
		}

		const data = new FormData();
		data.append( 'action', ajaxAction );
		data.append( 'nonce', ajaxNonce );
		data.append( 'is_switched', ! this.state.isSwitched );

		fetch( ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		} )
			.then( ( response ) => response.json() )
			.then( ( data ) => {} )
			.catch( ( error ) => {
				console.error( error );
			} );
	}

	render() {
		const {
			switchColor,
			switchBackgroundColor,
			switchBackgroundColorChecked,
		} = this.props;
		const { isSwitched } = this.state;

		return (
			<div
				className="woo-tax-switch"
				style={ {
					'--wts-color': switchColor,
					'--wts-bg-color': switchBackgroundColor,
					'--wts-bg-checked-color': switchBackgroundColorChecked,
				} }
			>
				<label className="woo-tax-switch-label">
					<input
						type="checkbox"
						name="woo-tax-switch-checkbox"
						onChange={ this.handleChange }
						checked={ isSwitched }
						className="woo-tax-switch-checkbox"
					/>
					<span className="woo-tax-switch-slider"></span>
				</label>
			</div>
		);
	}
}

export default SwitchComponent;
