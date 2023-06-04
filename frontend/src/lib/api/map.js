// apiService.js

import axios from 'axios';

export async function fetchMapData() {
	try {
		const response = await axios.get('/api/maps');
		const mapData = response.data;
		return mapData;
	} catch (error) {
		console.error('Error fetching map data:', error);
		return null;
	}
}
