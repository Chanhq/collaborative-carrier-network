import {useContext} from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import React from 'react';
import Button from '@mui/material/Button';
import auctionApi from '../../lib/api/auction';
import TransportRequestsTable from './TransportRequestTable';

function AuctioneerHome() {
	const { user, authenticated } = useContext(AuthContext);

	const startAuction = async () => {
		if (user !== null) {
			auctionApi.startAuction(user.token).then((r) => {
				if (r.response.status === 409) {
					alert('There is already an ongoing auction');
				} else {
					alert('Successfully started auction transport requests selection process.');
				}
			});
		}
	};

	return(
		(authenticated && user.isAuctioneer) &&
        <>
        	<h1>Auctioneer Home</h1>
        	<TransportRequestsTable/>
        	<Button  style={{
        		margin: '10px',
        	}} onClick={startAuction} variant="contained">Start auction</Button>
        	<Navbar/>
        </>
	);
}

export default AuctioneerHome;