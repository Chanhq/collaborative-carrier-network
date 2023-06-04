import {useContext} from 'react';
import {AuthContext} from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import React from 'react';

function CarrierHome() {
	const { user, authenticated } = useContext(AuthContext);

	return(
		(authenticated && !user.isAuctioneer) &&
        <>
        	<h1>Carrier Home works!</h1>
        	<Navbar/>
        </>
	);
}

export default CarrierHome;