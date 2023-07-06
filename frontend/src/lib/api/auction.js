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
	getAuctionData: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
			const response =  await client.get('api/auctioneer-frontend/auction/transport-requests');
			return response.data.data;
		} catch (error) {
			return error;
		}
	},
	endAuction: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.post['Authorization'] = 'Bearer ' + token;
			const response =  await client.post('api/auctioneer-frontend/auction/end');
			return response.data.data;
		} catch (error) {
			return error;
		}
	}
};
