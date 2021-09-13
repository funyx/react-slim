import { Link } from "react-router-dom";

export const Aside = () => <aside className={'sidenav'}>
    <div className={'d-flex flex-column'}>
        <span className={'d-flex flex-row justify-content-center'} style={{height:50}}>
            <Link to='/' className={'btn align-self-center text-white-50'}>Admin Panel</Link>
        </span>
    </div>
</aside>;
