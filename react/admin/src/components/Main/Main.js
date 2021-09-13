import { Route, Switch } from "react-router-dom";

export const Main = () => (
    <main className={'main'} style={{position:"relative"}}>
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
);
