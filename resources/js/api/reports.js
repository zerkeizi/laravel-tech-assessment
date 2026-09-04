import axios from '../lib/axios';

export default {
    generate: (params) => axios.get('/api/reports', { params }),
};
