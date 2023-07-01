import {EdgeShapes, Sigma,} from 'react-sigma';
import { fetchMapData } from '../../lib/api/map';
import { AuthContext } from '../../lib/context/AuthContext';
import React, { useContext, useEffect, useState } from 'react';
import {CircularProgress, Grid, Stack} from '@mui/material';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import TextField from '@mui/material/TextField';
import carrierApi from '../../lib/api/carrier';

function MapVisualizer() {
	const { user } = useContext(AuthContext);
	const [graph, setGraph] = useState(null);
	const [pickupNode, setPickupNode] = useState(null);
	const [deliveryNode, setDeliveryNode] = useState(null);

	const fetchData = async () => {
		try {
			const mapData = await fetchMapData(user.token);

			if (mapData) {
				setGraph(mapData);
			}
		} catch (error) {
			alert('Error fetching map data:', error);
		}
	};

	useEffect(() => {
		fetchData();
	}, []);

	const handleAddTransportRequestSubmit = async () => {
		const allowedNodes = Array.from({length: 69}, (_, i) => i);

		function validateTransportRequestNodeInputs() {
			if (pickupNode === null || deliveryNode === null || pickupNode === '' || pickupNode === '') {
				alert('Please enter both deliver and pickup node!');
				return false;
			}

			if (pickupNode === deliveryNode) {
				alert('Pickup and delivery must not be the same node');
				return false;
			}

			if (parseInt(pickupNode) === 0 || parseInt(deliveryNode) === 0) {
				alert('Pickup or delivery node must not be the depot');
				return false;
			}

			if (!allowedNodes.includes(parseInt(pickupNode)) || !allowedNodes.includes(parseInt((deliveryNode)))) {
				alert('Please select node ids from 1 to 70.');
				return false;
			}

			return true;
		}

		const isInputValid= validateTransportRequestNodeInputs();

		if (!isInputValid) {
			return;
		}

		const data = {
			origin_node: pickupNode,
			destination_node: deliveryNode,
		};

		await carrierApi.addTransportRequest(user.token, data).then((r) => {
			const response = r.response;
			if (response && response.status !== 201) {
				alert(response.data.message);
			} else {
				window.location.reload(false);
			}
		});
	};

	if (graph) {
		return (
			graph &&
			<div
				style={{
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'center',
				}}
			>
				<Stack spacing={1}>
					<Typography align="center" variant="subtitle1">Optimal route for your transport requests:</Typography>
					<Sigma
						graph={graph}
						style={{ width: '1000px', height: '600px' }}
						settings={{
							drawEdges: true,
							drawEdgeLabels: true,
							clone: false
						}}
					>
						<EdgeShapes default="curvedArrow" />
					</Sigma>
					<Grid container spacing={1} columns={3}>
						<Grid
							item
							xs={1}
							style={{
								display: 'flex',
								justifyContent: 'center',
							}}
						>
							<TextField
								onChange={(e) => {setPickupNode(e.target.value);}}
								id="pickup"
								label="Pickup"
								variant="standard"
							/>
						</Grid>
						<Grid
							item
							xs={1}
							style={{
								display: 'flex',
								justifyContent: 'center',
							}}
						>
							<TextField
								onChange={(e) => {setDeliveryNode(e.target.value);}}
								id="delivery"
								label="Delivery"
								variant="standard"
							/>
						</Grid>
						<Grid
							item
							xs={1}
							style={{
								display: 'flex',
								justifyContent: 'center',
							}}
						>
							<Button
								variant="contained"
								onClick={handleAddTransportRequestSubmit}
							>
								Add transport request
							</Button>
						</Grid>
					</Grid>
				</Stack>

			</div>
		);
	} else {
		return (
			<div
				style={{
					position: 'absolute', left: '50%', top: '50%',
					transform: 'translate(-50%, -50%)'
				}}
			>
				<CircularProgress />
			</div>
		);
	}

}

export default MapVisualizer;