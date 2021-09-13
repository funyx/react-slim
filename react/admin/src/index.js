import './loader';
import 'popper.js';
import 'bootstrap/dist/js/bootstrap.min';
import { Provider as ReduxProvider } from 'react-redux'
import { render } from 'react-dom';
import App from './containers/App';
// import reportWebVitals from './reportWebVitals';
import configure from "./store/configure";

const preloadedState = window.__PRELOADED_STATE__
const store = configure(preloadedState)

render(
    <ReduxProvider store={store}>
        <App/>
    </ReduxProvider>,
    document.getElementById('root')
)

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
// reportWebVitals();
