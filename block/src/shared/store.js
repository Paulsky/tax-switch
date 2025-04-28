import { createReduxStore, select, register, dispatch } from '@wordpress/data';

const STORAGE_KEY = 'wdevs_tax_switch_is_switched';
const STORE_NAME = 'wdevs-tax-switch/store';
const SWITCH_TYPE = 'SET_IS_SWITCHED';

const getInitialState = () => {
	const storedValue = localStorage.getItem( STORAGE_KEY );
	return {
		isSwitched: storedValue ? JSON.parse( storedValue ) : false,
	};
};

const actions = {
	setIsSwitched( value ) {
		return {
			type: SWITCH_TYPE,
			value,
		};
	},
	saveIsSwitched( value ) {
		localStorage.setItem( STORAGE_KEY, JSON.stringify( value ) );
		return {
			type: SWITCH_TYPE,
			value,
		};
	},
};

const reducer = ( state = getInitialState(), action ) => {
	switch ( action.type ) {
		case SWITCH_TYPE:
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

let store = select( STORE_NAME );

if ( store === undefined ) {
	store = createReduxStore( STORE_NAME, {
		reducer,
		actions,
		selectors,
	} );

	register( store );
}

export function getIsSwitched() {
	return select( STORE_NAME ).getIsSwitched();
}

export function saveIsSwitched( value ) {
	return dispatch( STORE_NAME ).saveIsSwitched( value );
}

export function setIsSwitched( value ) {
	return dispatch( STORE_NAME ).setIsSwitched( value );
}
