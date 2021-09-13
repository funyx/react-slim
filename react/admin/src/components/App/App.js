import './App.scss';
import { Switch, Route, BrowserRouter as Router } from "react-router-dom";
import LoginPage from "../LoginPage";
import RegisterPage from "../RegisterPage";
import PrivateRoute from "../../containers/PrivateRoute";
import Header from "../Header";
import Aside from "../Aside";
import Main from "../Main";

const App = () => {
    return (
            <Router>
                <Switch>
                    <Route exact path="/login">
                        <LoginPage />
                    </Route>
                    <Route exact path="/register">
                        <RegisterPage />
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
    );
}

export default App;
