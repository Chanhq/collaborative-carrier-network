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
};
