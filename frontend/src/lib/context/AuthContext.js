import React, {createContext, useEffect, useState} from "react";
import PropTypes from "prop-types";
import authApi from "../api/auth.js";
import windowLocationHelper from "../helper/window-location.js";
import sessionHelper from "../helper/session.js";

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
        if (isNotOnAuthPage()) {
            windowLocationHelper.redirectTo('/auth');
        }
    }

    useEffect(() => {
        let user = sessionHelper.getUserSessionClientSide();
        console.log(user);

        if (!user) {
            redirectToAuthPage();
        } else {
            authApi.getAuthedUser(user.token).then((response) => {
                if (response && response.status === 'success') {
                    setUser({
                        username: user.username,
                        isAuctioneer: user.isAuctioneer,
                    });
                    setAuthenticated(true);
                } else {
                    clearUserData();
                }
            }).catch(() => {
                clearUserData();
                redirectToAuthPage();
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