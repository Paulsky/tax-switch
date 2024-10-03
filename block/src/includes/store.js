import { registerStore, select } from '@wordpress/data';

const STORAGE_KEY = 'wdevs_tax_switch_is_switched';

const getInitialState = () => {
	const storedValue = localStorage.getItem( STORAGE_KEY );
	return {
		isSwitched: storedValue ? JSON.parse( storedValue ) : false,
	};
};

const actions = {
	setIsSwitched( value ) {
		return {
			type: 'SET_IS_SWITCHED',
			value,
		};
	},
	saveIsSwitched( value ) {
		localStorage.setItem( STORAGE_KEY, JSON.stringify( value ) );
		return {
			type: 'SET_IS_SWITCHED',
			value,
		};
	},
};

const reducer = ( state = getInitialState(), action ) => {
	switch ( action.type ) {
		case 'SET_IS_SWITCHED':
			return {
				...state,
				isSwitched: action.value,
			};
		default:
			return state;
	}
};

const selectors = {
	getIsSwitched( state ) {
		return state.isSwitched;
	},
};

let store;

if ( ! select( 'wdevs-tax-switch/store' ) ) {
	store = registerStore( 'wdevs-tax-switch/store', {
		reducer,
		actions,
		selectors,
	} );
} else {
	store = select( 'wdevs-tax-switch/store' );
}

export default store;
