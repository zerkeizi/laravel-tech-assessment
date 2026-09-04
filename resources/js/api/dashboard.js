import axios from '../lib/axios';

export default {
    summary: () => axios.get('/api/dashboard'),
};
