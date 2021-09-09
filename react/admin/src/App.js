import './App.scss';
import { Switch, Redirect, Route, Link, BrowserRouter as Router, useHistory, useLocation } from "react-router-dom";
import { createContext, useContext, useState } from "react";

// AUTH
const fakeAuth = {
    isAuthenticated: false,
    signin(cb) {
        fakeAuth.isAuthenticated = true;
        setTimeout(cb, 1000); // fake async
    },
    signout(cb) {
        fakeAuth.isAuthenticated = false;
        setTimeout(cb, 1000);
    }
};

/** For more details on
 * `authContext`, `ProvideAuth`, `useAuth` and `useProvideAuth`
 * refer to: https://usehooks.com/useAuth/
 */
const authContext = createContext();

function ProvideAuth({ children }) {
    const auth = useProvideAuth();
    return (
        <authContext.Provider value={auth}>
            {children}
        </authContext.Provider>
    );
}

function useAuth() {
    return useContext(authContext);
}

function useProvideAuth() {
    const [user, setUser] = useState(null);

    const signin = cb => {
        return fakeAuth.signin(() => {
            setUser("user");
            cb();
        });
    };

    const signout = cb => {
        return fakeAuth.signout(() => {
            setUser(null);
            cb();
        });
    };

    return {
        user,
        signin,
        signout
    };
}

// A wrapper for <Route> that redirects to the login
// screen if you're not yet authenticated.
function PrivateRoute({ children, ...rest }) {
    let auth = useAuth();
    return (
        <Route
            {...rest}
            render={({ location }) =>
                auth.user ? (
                    children
                ) : (
                    <Redirect
                        to={{
                            pathname: "/login",
                            state: { from: location }
                        }}
                    />
                )
            }
        />
    );
}

function LoginPage() {
    let history = useHistory();
    let location = useLocation();
    let auth = useAuth();

    let { from } = location.state || { from: { pathname: "/" } };
    let login = () => {
        auth.signin(() => {
            history.replace(from);
        });
    };

    return (
        <div>
            <p>You must log in to view the page at {from.pathname}</p>
            <button onClick={login}>Log in</button>
        </div>
    );
}
// END AUTH
const App = () => {
    return (
        <ProvideAuth>
            <Router>
                <Switch>
                    <Route exact path="/login">
                        <LoginPage />
                    </Route>
                    <PrivateRoute path="/">
                        <div className={'grid-container'}>
                            <Header />
                            <Aside />
                            <Main />
                        </div>
                    </PrivateRoute>
                </Switch>
            </Router>
        </ProvideAuth>
    );
}

const Header = () => <header className={'header'}><UserProfileDropDown /></header>

const Aside = () => <aside className={'sidenav'}>
    <div className={'d-flex flex-column'}>
        <span className={'d-flex flex-row justify-content-center'} style={{height:50}}>
            <Link to='/' className={'btn align-self-center text-white-50'}>Admin Panel</Link>
        </span>
    </div>
</aside>;

const Main = () => <main className={'main'} style={{position:"relative"}}>
    <Switch>
        <Route exact path="/">
            Home
        </Route>
        <Route exact path="/profile">
            Profile
        </Route>
        <Route path="*">
            <div
                style={{position:'absolute', width:'100%', height: '100%'}}
                className={'d-flex justify-content-center align-items-center'}
            >
                <p className={'text-center'}>404: Not Found</p>
            </div>
        </Route>
    </Switch>
</main>

const UserProfileDropDown = () => {
    const history = useHistory();
    const auth = useAuth();
    return <div className={'dropdown d-flex'}>
        <button className={'btn align-self-center text-white-50'} id="dLabel" type="button" data-toggle="dropdown"
                aria-expanded="false">
            Demo User
        </button>
        <div className={'dropdown-menu dropdown-menu-right'} aria-labelledby="dLabel">
            <Link to={'/profile'} className={'dropdown-item px-2 text-center'}
                  type="button"><small>Profile</small></Link>
            <div className={'dropdown-divider'}>&nbsp;</div>
            <button onClick={() => {
                auth.signout(() => history.push("/"));
            }} className={'dropdown-item px-2 text-center'} type="button"><small>Logout</small></button>
        </div>
    </div>
}

export default App;
