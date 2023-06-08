import { Sigma, RandomizeNodePositions, EdgeShapes } from 'react-sigma';
import { fetchMapData } from '../../lib/api/map';
import { AuthContext } from "../../lib/context/AuthContext";
import { useContext, useEffect, useState } from 'react';

function MapVisualizer() {
  const { user } = useContext(AuthContext);
  const [graph, setGraph] = useState({ edges: [], vertices: [] });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const mapData = await fetchMapData(user.token);
        
        if (mapData) {
          const { edges, vertices } = mapData;
          console.log(mapData);
          console.log(edges);
          console.log(vertices);
          
          const edgesFormatted = edges.map((edge) => ({
            weight: edge.weight,
            source: edge.source,
            target: edge.target,
            isOnOptimalPath: edge.isOnOptimalPath,
            size: 1,
            color: '#000000'
          }));

          const verticesFormatted = vertices.map((vertex) => ({
            id: vertex.id,
            x: vertex.x,
            y: vertex.y,
            size: 5,
            color: '#FF0000'
          }));

          setGraph({ edges: edgesFormatted, vertices: verticesFormatted });
        }
      } catch (error) {
        console.error('Error fetching map data:', error);
      }
    };

    fetchData();
  }, [user.token]);

  return (
    <div>
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
      </div>
  );
}

export default MapVisualizer; 
 

/*import React from 'react';
import { Sigma, RandomizeNodePositions, EdgeShapes } from 'react-sigma';

function MapVisualizer() {
  const graph = {
    nodes: [
      { id: 'A', label: 'Node A', x: 0, y: 0, size: 1, color: '#FF0000' },
      { id: 'B', label: 'Node B', x: 100, y: 0, size: 1, color: '#00FF00' },
      { id: 'C', label: 'Node C', x: 50, y: 100, size: 1, color: '#0000FF' }
    ],
    edges: [
      { id: 'AB', source: 'A', target: 'B', color: '#FF0000' },
      { id: 'BC', source: 'B', target: 'C', color: '#00FF00' },
      { id: 'CA', source: 'C', target: 'A', color: '#0000FF' }
    ]
  };

  return (
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

export default MapVisualizer; */

