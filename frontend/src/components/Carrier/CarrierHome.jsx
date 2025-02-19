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
	const [auctionEvaluationData, setAuctionEvaluationData] = useState(null);
	const fetchData = async () => {
		if (user) {
			const transportRequestsResponse = carrierApi.getTransportRequest(user.token);
			const auctionEvaluationResponse = carrierApi.getAuctionEvaluationData(user.token);

			transportRequestsResponse.then((response) => {
				const transportRequestsData = response.data.data.transport_requests;
				setTransportRequests(transportRequestsData);
			});

			auctionEvaluationResponse.then((response) => {
				if (response.status === 204) {
					setAuctionEvaluationData([]);
				} else if (response.status === 200) {
					setAuctionEvaluationData(response.data.data);
				}
			});
		}
	};

	const reFetchData = async () => {
		if (user) {
			const transportRequestsResponse = carrierApi.getTransportRequest(user.token);
			const auctionEvaluationResponse = carrierApi.getAuctionEvaluationData(user.token);

			transportRequestsResponse.then((response) => {
				const transportRequestsData = response.data.data.transport_requests;
				setTransportRequests(transportRequestsData);
			});

			auctionEvaluationResponse.then((response) => {
				if (response.status === 204) {
					setAuctionEvaluationData([]);
				} else if (response.status === 200) {
					reloadPage();
				}
			});
		}
	};

	const reloadPage = () => {
		window.location.reload(false);
	};

	useEffect(() => {
		fetchData().then();
		const id = setInterval(reFetchData, 20000);

		return () => clearInterval(id);
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
        	{
        		auctionEvaluationData &&
				<Drawer
					sx={{
						flexShrink: 0,
						'& .MuiDrawer-paper': {
							width: '325px',
							boxSizing: 'border-box',
						},
					}}
					variant="permanent"
					anchor="right"
				>
					<Typography align="center" variant="h6">Auction Evaluations</Typography>
					{
						auctionEvaluationData.length === 0 &&
						<div style={{marginLeft: '12px'}}>No auctioned transport requests detected</div>
					}
					{
						auctionEvaluationData.length !== 0 &&
						<List sx={{marginLeft: '12px'}}>
							{auctionEvaluationData.map((recapData) => (
								<ListItem key={recapData.auction_id} disablePadding>
									Auction: {recapData.auction_id}, Revenue Gain: {recapData.revenue_gain}, Price: {recapData.price_to_pay}
								</ListItem>
							))}
						</List>
					}
				</Drawer>
        	}
        	<Navbar/>
        	<MapVisualizer/>
        </>
	);
}

export default CarrierHome;