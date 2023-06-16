import httpClient from '../infrastructure/http-client';

export default {
	startAuction: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.post['Authorization'] = 'Bearer ' + token;
			return await client.post('api/auctioneer-frontend/auction/start');
		} catch (error) {
			return error;
		}
	},
	getSelectedTransportRequests: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
			const response =  await client.get('api/auctioneer-frontend/auction/transport-requests');
			return response.data.data.transport_requests;
		} catch (error) {
			return error;
		}
	},
};
