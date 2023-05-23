import axios from "axios";


const httpClient = axios.create({
        baseURL: 'http://localhost:80',
        headers: {'accept': 'application/json'}
    });

export default httpClient;