import { Sigma, EdgeShapes } from 'react-sigma';
import { fetchMapData } from '../../lib/api/map';
import { AuthContext } from '../../lib/context/AuthContext';
import React, { useContext, useEffect, useState } from 'react';
import {CircularProgress, Stack} from '@mui/material';
import Typography from '@mui/material/Typography';

function MapVisualizer() {
	const { user } = useContext(AuthContext);
	const [graph, setGraph] = useState(null);


	const fetchData = async () => {
		try {
			const mapData = await fetchMapData(user.token);

			if (mapData) {
				setGraph(mapData);
			}
		} catch (error) {
			console.error('Error fetching map data:', error);
		}
	};

	useEffect(() => {
		fetchData();
	}, []);

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



