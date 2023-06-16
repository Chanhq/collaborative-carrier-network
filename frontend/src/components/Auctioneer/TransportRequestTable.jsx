import React, {useContext, useEffect, useState} from 'react';
import auctionApi from '../../lib/api/auction';
import {AuthContext} from '../../lib/context/AuthContext';
import { DataGrid } from '@mui/x-data-grid';

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
				<DataGrid
					rows={transportRequests}
					columns={columns}>
				</DataGrid>
			}
			{transportRequests.length === 0 && <p>No TRs</p>}
		</>
	);
}

export default TransportRequestsTable;