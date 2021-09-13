import { useAuth } from "../hooks/useAuth";
import { Redirect, Route } from "react-router-dom";

const PrivateRoute = ({fallback = '/login', children, ...rest}) => {
    const {actions} = useAuth();
    return (
        <Route
            {...rest}
            render={({location}) =>
                actions.isAuthorized() ? (
                    children
                ) : (
                    <Redirect
                        to={{
                            pathname: fallback,
                            state: {from: location}
                        }}
                    />
                )
            }
        />
    );
}

export default PrivateRoute;
