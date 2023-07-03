import React, {useContext, useEffect, useState} from 'react';
import auctionApi from '../../lib/api/auction';
import {AuthContext} from '../../lib/context/AuthContext';
import { DataGrid } from '@mui/x-data-grid';
import Typography from '@mui/material/Typography';
import {CircularProgress} from '@mui/material';

function TransportRequestsTable() {
	const { user } = useContext(AuthContext);
	const [ transportRequests, setTransportRequests ] = useState(null);
	const [ auctionStatus, setAuctionStatus] = useState(null);

	const columns = [
		{ field: 'id', headerName: 'Id', width: 80 },
		{ field: 'origin_node', headerName: 'Pickup', width: 130 },
		{ field: 'destination_node', headerName: 'Delivery', width: 130 },
		{ field: 'status', headerName: 'Status', width: 200 },
	];

	const setAuctionData = async () => {
		const transportRequestData = await auctionApi.getAuctionData(user.token);
		setTransportRequests(transportRequestData.transport_requests);
		setAuctionStatus(transportRequestData.auction_status);
	};

	const pollAuctionData = async () => {
		setAuctionData();
		const id = setInterval(setAuctionData, 30000);

		return () => clearInterval(id);
	};

	useEffect(() => {
		pollAuctionData();
	}, []);

	return(
		<>
			{
				auctionStatus === 'inactive' &&
				<div style={{
					margin: '0 10px 0 10px',

				}}>
					<Typography style={{margin: '0 0 5px 0'}} align="left" variant="h4">In auction involved transport requests: </Typography>
					<DataGrid
						rows={transportRequests}
						columns={columns}
						style={{
							display: 'flex',
							alignItems: 'center',
							justifyContent: 'center',
						}}
					>
					</DataGrid>
				</div>
			}
			{auctionStatus === 'completed' && <Typography align="center" variant="h6">There is no ongoing auction right now!</Typography>}
			{
				auctionStatus === 'active' &&
				<>
					<Typography align="center" variant="h6"> Currently performing auction</Typography>
					<div
						style={{
							position: 'absolute', left: '50%', top: '50%',
							transform: 'translate(-50%, -50%)'
						}}
					>
						<CircularProgress />
					</div>
				</>
			}
		</>
	);
}

export default TransportRequestsTable;