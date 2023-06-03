import Sigma from 'sigma';
import { fetchMapData } from '../../lib/api/map';

function MapVisualizer() {

    const graphData = fetchMapData();

  return (
    <div>
      <Sigma
        style={{ width: '100%', height: '500px' }} // Adjust the dimensions as needed
        graph={graphData}
        settings={{
          drawEdges: true,
          clone: false,
        }}
      />
    </div>
  );
}

export default MapVisualizer;
