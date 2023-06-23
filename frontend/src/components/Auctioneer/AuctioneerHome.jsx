import {useContext} from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import React from 'react';
import TransportRequestsTable from './TransportRequestTable';
import Typography from '@mui/material/Typography';
import GavelIcon from '@mui/icons-material/Gavel';

function AuctioneerHome() {
	const { user, authenticated } = useContext(AuthContext);

	return(
		(authenticated && user.isAuctioneer) &&
        <>
        	<Typography align="center" variant="h1" gutterBottom>
				Coop Carrier Network -
        		<GavelIcon
        			style={{
        				width: '50px',
        				height: '50px',
        				margin: '0 0 0 25px',
        			}}
        		></GavelIcon>
        	</Typography>
        	<TransportRequestsTable/>
        	<Navbar/>
        </>
	);
}

export default AuctioneerHome;