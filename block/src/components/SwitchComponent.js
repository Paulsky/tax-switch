import { Component } from '@wordpress/element';
import { dispatch, select, subscribe } from '@wordpress/data';
import TaxSwitchHelper from '../includes/TaxSwitchHelper';
import '../includes/store';

class SwitchComponent extends Component {
	constructor( props ) {
		super( props );

		const { readOnly, isSwitched } = this.getInitialState( props );

		this.state = {
			readOnly,
			isSwitched,
		};

		this.handleChange = this.handleChange.bind( this );

		this.unsubscribe = subscribe( () => {
			const newIsSwitched = select(
				'wdevs-tax-switch/store'
			).getIsSwitched();
			if ( this.state.isSwitched !== newIsSwitched ) {
				this.setState( { isSwitched: newIsSwitched } );
			}
		} );
	}

	getInitialState( props ) {
		const readOnly = TaxSwitchHelper.parseBooleanValue( props.readOnly );
		const originalTaxDisplay = props.originalTaxDisplay || 'incl';
		let isSwitched;

		if ( readOnly ) {
			isSwitched = ! ( originalTaxDisplay === 'incl' );
		} else {
			isSwitched = select( 'wdevs-tax-switch/store' ).getIsSwitched();
		}

		return { readOnly, isSwitched };
	}

	componentWillUnmount() {
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
	}

	handleChange() {
		const newIsSwitched = ! this.state.isSwitched;
		this.setState( { isSwitched: newIsSwitched }, () => {
			if ( ! this.state.readOnly ) {
				dispatch( 'wdevs-tax-switch/store' ).saveIsSwitched(
					newIsSwitched
				);

				this.fireSwitchChangeEvent( newIsSwitched );
			} else {
				dispatch( 'wdevs-tax-switch/store' ).setIsSwitched(
					newIsSwitched
				);
			}

			this.togglePriceClasses();
		} );
	}

	displayIncludingVat() {
		const { originalTaxDisplay = 'incl' } = this.props;
		const { isSwitched } = this.state;

		return TaxSwitchHelper.displayIncludingVat(
			originalTaxDisplay,
			isSwitched
		);
	}

	togglePriceClasses() {
		const { originalTaxDisplay = 'incl' } = this.props;
		const { isSwitched } = this.state;

		return TaxSwitchHelper.togglePriceClasses(
			originalTaxDisplay,
			isSwitched
		);
	}

	getCurrentLabel() {
		const { switchLabelIncl, switchLabelExcl } = this.props;
		if ( this.displayIncludingVat() ) {
			return switchLabelIncl || '';
		} else {
			return switchLabelExcl || '';
		}
	}

	fireSwitchChangeEvent( isSwitched ) {
		const vm = this;
		const switchEvent = new CustomEvent( 'wdevs-tax-switch-changed', {
			detail: {
				isSwitched: isSwitched,
				displayIncludingVat: vm.displayIncludingVat(),
			},
		} );

		document.dispatchEvent( switchEvent );
	}

	renderToggleSwitch() {
		const {
			switchColor,
			switchColorChecked,
			switchBackgroundColor,
			switchBackgroundColorChecked,
			switchTextColor,
			switchLabelIncl,
			switchLabelExcl,
		} = this.props;

		const isChecked = this.displayIncludingVat();
		const showLabel = switchLabelIncl || switchLabelExcl;

		return (
			<div
				className="wdevs-tax-switch"
				style={ {
					'--wts-color': switchColor,
					'--wts-color-checked': switchColorChecked,
					'--wts-bg-color': switchBackgroundColor,
					'--wts-bg-color-checked': switchBackgroundColorChecked,
					'--wts-text-color': switchTextColor,
				} }
			>
				<label className="wdevs-tax-switch-label">
					<input
						type="checkbox"
						name="wdevs-tax-switch-checkbox"
						onChange={ this.handleChange }
						checked={ isChecked }
						className="wdevs-tax-switch-checkbox"
					/>
					<span className="wdevs-tax-switch-slider"></span>
				</label>
				{ showLabel && (
					<span
						className="wdevs-tax-switch-label-text"
						onClick={ this.handleChange }
					>
						{ this.getCurrentLabel() }
					</span>
				) }
			</div>
		);
	}

	renderButtons() {
		const {
			switchColor,
			switchColorChecked,
			switchBackgroundColor,
			switchBackgroundColorChecked,
			switchTextColor,
			switchLabelIncl,
			switchLabelExcl,
		} = this.props;

		const isIncl = this.displayIncludingVat();

		const setInclusive = () => {
			if ( ! isIncl ) {
				this.handleChange();
			}
		};

		const setExclusive = () => {
			if ( isIncl ) {
				this.handleChange();
			}
		};

		return (
			<div
				className="wdevs-tax-switch wdevs-tax-buttons"
				style={ {
					'--wts-color': switchColor,
					'--wts-color-checked': switchColorChecked,
					'--wts-bg-color': switchBackgroundColor,
					'--wts-bg-color-checked': switchBackgroundColorChecked,
					'--wts-text-color': switchTextColor,
				} }
			>
				<button
					type="button"
					className={ `wdevs-tax-button ${
						isIncl ? 'wdevs-tax-button-active' : ''
					}` }
					onClick={ setInclusive }
				>
					{ switchLabelIncl || 'Incl. VAT' }
				</button>
				<button
					type="button"
					className={ `wdevs-tax-button ${
						! isIncl ? 'wdevs-tax-button-active' : ''
					}` }
					onClick={ setExclusive }
				>
					{ switchLabelExcl || 'Excl. VAT' }
				</button>
			</div>
		);
	}

	render() {
		const { switchType = 'switch' } = this.props;

		if ( switchType === 'buttons' ) {
			return this.renderButtons();
		}

		return this.renderToggleSwitch();
	}
}

export default SwitchComponent;
