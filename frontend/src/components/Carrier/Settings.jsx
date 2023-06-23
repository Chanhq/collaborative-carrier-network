import React, { useContext } from 'react';
import { AuthContext } from '../../lib/context/AuthContext';

function Settings() {
	const {user, authenticated} = useContext(AuthContext);

	return (
		(authenticated && !user.isAuctioneer) &&
        <>
        	<h1>Settings page!</h1>
        </>
	);
}

export default Settings;