// apiService.js

import { useState } from 'react';
import axios from 'axios';

export async function fetchMapData() {

  const [graphData, setGraphData] = useState({ nodes: [], edges: [] });

  try {
    const response = await axios.get('/api/maps');
    setGraphData(response.data);
    return graphData;
  } catch (error) {
    console.error('Error fetching map data:', error);
    return null;
  }
}
