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
          const { data: responseData } = mapData;
          const { data: mapDataNested } = responseData;
          const { map } = mapDataNested;
          const { edges, vertices } = map;
          const edgesArray = edges;
          const verticesArray = vertices;

          
          const edgesFormatted = edgesArray.map((edge) => ({
            weight: edge.weight,
            source: edge.source,
            target: edge.target,
            isOnOptimalPath: edge.isOnOptimalPath,
            size: 1,
            color: '#0000ff'
          }));

          
          const verticesFormatted = verticesArray.map((vertex) => ({
            id: vertex.id,
            x: vertex.x,
            y: vertex.y,
            size: 5,
            color: '#00ff00'
          }));
       
          setGraph({ edges: edgesFormatted, vertices: verticesFormatted });

        }
      } catch (error) {
        console.error('Error fetching map data:', error);
      }
    };

    fetchData();
    

  }, [user.token]);


  console.log(graph);

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
 

