import React, {createContext, useEffect, useState} from "react";
import PropTypes from "prop-types";
import authApi from "../api/auth.js";
import locationHelper from "../helper/location-helper.js";
export const AuthContext = createContext();

function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState('');
    const [authenticated, setAuthenticated] = useState(false);

    const clearUserData = () => {
        setToken('');
        setAuthenticated(false);
        setUser(null);
    }

    const isNotOnAuthPage = () => {
        return !locationHelper.isAlreadyOnPath('/auth');
    }

    const redirectToAuthPage = () => {
        locationHelper.redirectTo('/auth');
    }

    useEffect(() => {
        let storageToken = localStorage.getItem('token');

        if (storageToken === null && isNotOnAuthPage()) {
            redirectToAuthPage();
        }

        authApi.getAuthedUser(storageToken).then((response) => {
            if (response.status === 'success') {
                setUser(response.user);
                setAuthenticated(true);
            } else {
                clearUserData();
            }
        });

    }, []);

    return (
      <AuthContext.Provider
        value={{
            user,
            setUser,
            token,
            setToken,
            authenticated,
            setAuthenticated
        }}
      >
          {children}
      </AuthContext.Provider>
    );
}


AuthProvider.propTypes = {
    children: PropTypes.object
}
export default AuthProvider;