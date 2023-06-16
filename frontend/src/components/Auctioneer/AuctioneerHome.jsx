import { useContext } from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import React from 'react';
import Button from '@mui/material/Button';
import auctionApi from '../../lib/api/auction';

function AuctioneerHome() {
	const { user, authenticated } = useContext(AuthContext);

	const startAuction = () => {
		auctionApi.startAuction(user.token).then(r => console.log(r));
	};

	return(
		(authenticated && user.isAuctioneer) &&
        <>
        	<h1>Auctioneer Home works!</h1>
        	<Button onClick={startAuction} variant="contained">Start auction</Button>
        	<Navbar/>
        </>
	);
}

export default AuctioneerHome;