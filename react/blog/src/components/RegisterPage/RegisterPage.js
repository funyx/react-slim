import { Link, useHistory, useLocation } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import { useEffect } from "react";

const RegisterPage = () => {
    const history = useHistory();
    const location = useLocation();
    const { user, actions } = useAuth();
    const { from } = location.state || { from: { pathname: "/" } }

    useEffect(() => {
        if(actions.isAuthorized()) {
            history.replace(from);
        }
    },[user, actions, from, history]);

    let register = () => {
        const data = {me:true}
        actions.register(data);
    };

    return (
        <div>
            <p>You must register to view the page at {from.pathname}</p>
            <button onClick={register}>Register</button>
            <Link to={'/login'} className={'dropdown-item px-2 text-center'}
                  type="button"><small>Login</small></Link>
        </div>
    );
}

export default RegisterPage;
