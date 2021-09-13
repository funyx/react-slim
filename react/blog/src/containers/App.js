import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import * as AuthActions from '../store/actions/auth'
import App from '../components/App';
import { createContext } from 'react';
import { initialState } from '../store/reducers/auth';

export const authContext = createContext(initialState);

const mapStateToProps = (state) => {
    return {
        user: state.auth
    }
}

const mapDispatchToProps = (dispatch) => ({
    actions: bindActionCreators(AuthActions, dispatch)
})

export default connect(mapStateToProps, mapDispatchToProps)(({user, actions}) => {

    const state = {
        user,
        actions
    }

    return (
        <authContext.Provider value={state}>
            <App/>
        </authContext.Provider>
    )
})
