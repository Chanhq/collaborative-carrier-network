import React, { useContext } from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import MapVisualizer from '../Map/MapVisualizer';
import Typography from '@mui/material/Typography';
import AirportShuttleIcon from '@mui/icons-material/AirportShuttle';


function CarrierHome() {
	const {user, authenticated} = useContext(AuthContext);

	return (
		(authenticated && !user.isAuctioneer) &&
        <>
        	<Typography align="center" variant="h1" gutterBottom>
				Coop Carrier Network -
        		<AirportShuttleIcon
        			style={{
        				width: '50px',
        				height: '50px',
        				margin: '0 0 0 25px',
        			}}
        		></AirportShuttleIcon>
        	</Typography>
        	<Navbar/>
        	<MapVisualizer/>
        </>
	);
}

export default CarrierHome;