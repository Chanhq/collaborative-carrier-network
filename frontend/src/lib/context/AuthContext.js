import React, {createContext, useEffect, useState} from 'react';
import PropTypes from 'prop-types';
import authApi from '../api/auth.js';
import windowLocationHelper from '../helper/window-location.js';
import sessionHelper from '../helper/session.js';
import routePermissionService from '../service/route-permission';

export const AuthContext = createContext();

function AuthProvider({ children }) {
	const [user, setUser] = useState(null);
	const [authenticated, setAuthenticated] = useState(false);

	const clearUserData = () => {
		setAuthenticated(false);
		setUser(null);
		sessionHelper.deleteUserSessionClientSide();
	};

	useEffect(() => {
		let user = sessionHelper.getUserSessionClientSide();

		if (!user) {
			windowLocationHelper.redirectToAuthPage();
		} else {
			authApi.getAuthedUser(user.token).then((response) => {
				if (response && response.status === 'success') {
					if (routePermissionService.isUserPermittedToAccessRoute(user)) {
						setUser({
							username: user.username,
							isAuctioneer: user.isAuctioneer,
							token: user.token
						});
						setAuthenticated(true);
					} else {
						alert('Forbidden. You are now being redirected to your homepage!');
						windowLocationHelper.redirectToHomePage(user.isAuctioneer);
					}
				} else {
					clearUserData();
				}
			}).catch(() => {
				clearUserData();
				windowLocationHelper.redirectToAuthPage();
			});
		}


	}, []);

	return (
		<AuthContext.Provider
			value={{
				user,
				setUser,
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
};
export default AuthProvider;