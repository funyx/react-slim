import { Link, useHistory, useLocation } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import { useEffect } from "react";

const LoginPage = () => {
    const history = useHistory();
    const location = useLocation();
    const {user, actions} = useAuth();
    const {from} = location.state || {from: {pathname: "/"}}

    useEffect(() => {
        if( actions.isAuthorized() ) {
            history.replace(from);
        }
    }, [user, actions, from, history]);

    let login = () => {
        const data = {me: true}
        actions.login(data);
    };

    return (
        <div>
            <p>You must log in to view the page at {from.pathname}</p>
            <button onClick={login}>Log in</button>
            <Link to={'/register'} className={'dropdown-item px-2 text-center'}
                  type="button"><small>Register</small></Link>
        </div>
    );
}

export default LoginPage;
