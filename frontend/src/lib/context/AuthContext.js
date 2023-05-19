import React, {createContext, useEffect, useState} from "react";
import PropTypes from "prop-types";
import authApi from "../api/auth.js";
import windowLocationHelper from "../helper/window-location.js";
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
        return !windowLocationHelper.isAlreadyOnPath('/auth');
    }

    const redirectToAuthPage = () => {
        windowLocationHelper.redirectTo('/auth');
    }

    useEffect(() => {
        let storageToken = localStorage.getItem('token');

        if (!storageToken && isNotOnAuthPage()) {
            redirectToAuthPage();
        }

        if (storageToken) {
            authApi.getAuthedUser(storageToken).then((response) => {
                if (response && response.status === 'success') {
                    setUser({
                        username: response.data.username,
                        isAuctioneer: response.data.isAuctioneer,
                    });
                    console.log(response.data);
                    setAuthenticated(true);
                } else {
                    clearUserData();
                }
            }).catch((error) => {
                // TODO Handle error -> redirect, session clearing
                console.log(error);
            });
        }

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