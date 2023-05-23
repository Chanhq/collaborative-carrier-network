import httpClient from "../infrastructure/http-client";
import sessionHelper from "../helper/session";

export default {
    getAuthedUser: async  (token) => {
        try {
            let client = httpClient;
            client.defaults.headers.get['Authorization'] = 'Bearer ' + token;
            const res = await client.get('/api/auth/user');
            return await res.data;
        } catch (error) {
            return await error.data;
        }
    },
    login: async (username, password) => {
        try {
            const res = await httpClient.post('/api/auth/login', {username, password});
            return await res.data;
        } catch (error) {
            return error.response.data;
        }
    },
    logout: async (token) => {
        alert('You are now logged out.')

        // client-side
        sessionHelper.deleteUserSessionClientSide();

        // server-side
        let client = httpClient;
        client.defaults.headers.post['Authorization'] = 'Bearer ' + token;
        const res = await client.post('/api/auth/logout');
        return await res.data;
    }
}