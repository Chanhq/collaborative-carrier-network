import httpClient from "../infrastructure/http-client";
import sessionHelper from "../helper/session";
import windowLocationHelper from "../helper/window-location";


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
            let client = httpClient;

            const res = await client.post('/api/auth/login', {username, password});
            return await res.data;
        } catch (error) {
            return error.response.data;
        }
    },
    logout: () => {
        alert('You will now be logged of')
        // TODO: also invalidate session server side e.g. calling logout endpoint with current user token
        sessionHelper.deleteUserSessionClientSide();
        windowLocationHelper.redirectToAuthPage();
    }
}