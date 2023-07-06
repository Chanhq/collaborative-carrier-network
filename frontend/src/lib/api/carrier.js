import httpClient from '../infrastructure/http-client';

export default {
	getCostModel: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
			return (await client.get('api/carrier-frontend/cost-model')).data.data.cost_model;
		} catch (error) {
			return error;
		}
	},
	setCostModel: async (token, data) => {
		try {
			let client = httpClient;
			client.defaults.headers.post['Authorization'] = 'Bearer ' + token;
			await client.post('api/carrier-frontend/cost-model', data);
		} catch (error) {
			console.log(error);
		}
	},
	addTransportRequest: async (token, data) => {
		try {
			let client = httpClient;
			client.defaults.headers.post['Authorization'] = 'Bearer ' + token;
			return client.post('api/carrier-frontend/transport-request', data);
		} catch (error) {
			return error;
		}
	},
	getTransportRequest: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
			return client.get('api/carrier-frontend/transport-request');
		} catch (error) {
			return error;
		}
	},
	getAuctionEvaluationData: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
			return client.get('api/carrier-frontend/auction-evaluation');
		} catch (error) {
			return error;
		}
	},
	completeTransportRequests: async (token) => {
		try {
			let client = httpClient;
			client.defaults.headers.post['Authorization'] = 'Bearer ' + token;
			return client.post('api/carrier-frontend/transport-requests/complete');
		} catch (error) {
			return error;
		}
	},
};