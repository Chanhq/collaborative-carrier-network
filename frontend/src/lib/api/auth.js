import httpClient from "../infrastructure/http-client";

export default {
    getAuthedUser: async  (token) => {
        try {
            let client = httpClient;
            client.default.defaults.headers.post['Authorization'] = token;
            const res = await client.get('/api/auth/user');

            return await res.data.data;
        } catch (error) {
            return {
                status: 'error',
                message: 'Unauthenticated'
            }
        }
    }
}