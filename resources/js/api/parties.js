import axios from '../lib/axios';

export default {
    index: (params) => axios.get('/api/parties', { params }),
    show: (id) => axios.get(`/api/parties/${id}`),
    store: (data) => axios.post('/api/parties', data),
    update: (id, data) => axios.put(`/api/parties/${id}`, data),
    destroy: (id) => axios.delete(`/api/parties/${id}`),
};
