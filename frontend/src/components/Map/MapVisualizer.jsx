import { Sigma, RandomizeNodePositions, EdgeShapes } from 'react-sigma';
import { fetchMapData } from '../../lib/api/map';
import { AuthContext } from '../../lib/context/AuthContext';
import React, { useContext, useEffect, useState } from 'react';

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
  

	return (
		graph &&

      <Sigma
      	graph={graph}
      	style={{ width: '1000px', height: '600px' }}
      	settings={{
      		drawEdges: true,
      		drawEdgeLabels: true,
      		clone: false
      	}}
      >
      	<RandomizeNodePositions />
      	<EdgeShapes default="curvedArrow" />
      </Sigma>
     
	);
}

export default MapVisualizer; 



