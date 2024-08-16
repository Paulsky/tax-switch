import { registerStore } from '@wordpress/data';

const DEFAULT_STATE = {
	isSwitched: false,
};

const actions = {
	setIsSwitched( value ) {
		return {
			type: 'SET_IS_SWITCHED',
			value,
		};
	},
};

const reducer = ( state = DEFAULT_STATE, action ) => {
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

const store = registerStore( 'woo-tax-switch/store', {
	reducer,
	actions,
	selectors,
} );

export default store;
