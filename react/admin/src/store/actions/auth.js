import { AUTH_IS_AUTHORIZED, AUTH_LOGIN, AUTH_LOGOUT, AUTH_REGISTER } from "../constants";

export const isAuthorized = (data = null) => (dispatch, getState) => getState().auth.me != null
export const login = (payload = null) => (dispatch, getState) => dispatch({type: AUTH_LOGIN, payload})
export const register = (payload = null) => (dispatch, getState) => dispatch({ type: AUTH_REGISTER, payload });
export const logout = (payload = null) => (dispatch, getState) => dispatch({type: AUTH_LOGOUT, payload});
