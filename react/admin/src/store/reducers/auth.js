import * as ActionType from '../constants'

export const initialState = {
    'me': null,
    'token': null
}

export const reducer = (state = initialState, action) => {
    switch( action.type ) {
        case ActionType.AUTH_LOGIN:
        case ActionType.AUTH_REGISTER:
            return {
                me: true,
                token: {_raw:'TEST'}
            };
        case ActionType.AUTH_LOGOUT:
            return {
                me: null,
                token: null
            };
        default:
            return state
    }
}

export default reducer
