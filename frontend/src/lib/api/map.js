import httpClient from '../infrastructure/http-client';

export async function fetchMapData(token) {

	try {
		let client = httpClient;
		client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
		const response = await client.get('api/carrier-frontend/map');
		return response.data.data.map;
	} catch (error) {
		console.error('Error fetching map data:', error);
		return null;
	}
}
