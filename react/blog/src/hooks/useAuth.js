import { useContext } from "react";
import { authContext } from "../containers/App";

export const useAuth = () => {
    return useContext(authContext);
}
