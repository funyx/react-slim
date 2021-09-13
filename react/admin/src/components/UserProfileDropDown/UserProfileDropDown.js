import { Link, useHistory } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import { useEffect } from "react";

export const UserProfileDropDown = () => {
    const history = useHistory();
    const { user, actions } = useAuth();

    useEffect(() => {
        console.log(user)
        if(!actions.isAuthorized()){
            history.push("/")
        }
    }, [user, actions, history])

    const logout = () => {
        actions.logout();
    }

    return <div className={'dropdown d-flex'}>
        <button className={'btn align-self-center text-white-50'} id="dLabel" type="button" data-toggle="dropdown"
                aria-expanded="false">
            Demo User
        </button>
        <div className={'dropdown-menu dropdown-menu-right'} aria-labelledby="dLabel">
            <Link to={'/profile'} className={'dropdown-item px-2 text-center'}
                  type="button"><small>Profile</small></Link>
            <div className={'dropdown-divider'}>&nbsp;</div>
            <button onClick={logout} className={'dropdown-item px-2 text-center'} type="button"><small>Logout</small></button>
        </div>
    </div>
}
