import React, {useContext, useEffect, useState} from 'react';
import auctionApi from '../../lib/api/auction';
import {AuthContext} from '../../lib/context/AuthContext';
import { DataGrid } from '@mui/x-data-grid';
import Typography from '@mui/material/Typography';

function TransportRequestsTable() {
	const { user } = useContext(AuthContext);
	const [ transportRequests, setTransportRequests ] = useState(null);

	const columns = [
		{ field: 'requester_name', headerName: 'Requester Name', width: 170 },
		{ field: 'origin_node', headerName: 'Origin Node', width: 130 },
		{ field: 'destination_node', headerName: 'Destination Node', width: 130 },
	];

	const getTransportRequests = async () => {
		const transportRequestData = await auctionApi.getSelectedTransportRequests(user.token);
		setTransportRequests(transportRequestData);
	};

	const pollTransportRequests = async () => {
		getTransportRequests();
		const id = setInterval(getTransportRequests, 30000);

		return () => clearInterval(id);
	};

	useEffect(() => {
		pollTransportRequests();
	}, []);

	return(
		transportRequests &&
		<>
			{
				transportRequests.length !== 0 &&
				<div style={{
					margin: '0 10px 0 10px'
				}}>
					<Typography style={{margin: '0 0 5px 0'}} align="left" variant="h4">For auction selected transport requests: </Typography>
					<DataGrid
						rows={transportRequests}
						columns={columns}>
					</DataGrid>
				</div>
			}
			{transportRequests.length === 0 && <Typography align="center" variant="h6">There is no ongoing auction right now!</Typography>}
		</>
	);
}

export default TransportRequestsTable;