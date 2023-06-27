import React, {useContext, useEffect, useState} from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import MapVisualizer from '../Map/MapVisualizer';
import Typography from '@mui/material/Typography';
import AirportShuttleIcon from '@mui/icons-material/AirportShuttle';
import Drawer from '@mui/material/Drawer';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import carrierApi from '../../lib/api/carrier';

function CarrierHome() {
	const {user, authenticated} = useContext(AuthContext);
	const [transportRequests, setTransportRequests] = useState(null);
	const fetchData = async () => {
		if (user) {
			try {
				const transportRequestsData = await carrierApi.getTransportRequest(user.token);
				if (transportRequestsData) {
					setTransportRequests(transportRequestsData);
					console.log('TRs', transportRequestsData);
				}
			} catch (error) {
				console.log(error);
			}
		}
	};

	useEffect(() => {
		fetchData();
	}, [user]);

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
        	{	
        		transportRequests &&
				<Drawer
					sx={{
						width: '200px',
						flexShrink: 0,
						'& .MuiDrawer-paper': {
							width: '200px',
							boxSizing: 'border-box',
						},
					}}
					variant="permanent"
					anchor="left"
				>
					<Typography align="center" variant="h6">Transport Requests</Typography>
					<List sx={{marginLeft: '12px'}}>
						{transportRequests.map((transportRequest) => (
							<ListItem key={transportRequest.id} disablePadding>
								Pickup: {transportRequest.origin_node}, Delivery: {transportRequest.destination_node}
							</ListItem>
						))}
					</List>
				</Drawer>
        	}
        	<Navbar/>
        	<MapVisualizer/>
        </>
	);
}

export default CarrierHome;