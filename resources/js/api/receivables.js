import axios from '../lib/axios';

export default {
    index: (params) => axios.get('/api/receivables', { params }),
    show: (id) => axios.get(`/api/receivables/${id}`),
    store: (data) => axios.post('/api/receivables', data),
    update: (id, data) => axios.put(`/api/receivables/${id}`, data),
    destroy: (id) => axios.delete(`/api/receivables/${id}`),
    receive: (id) => axios.patch(`/api/receivables/${id}/receive`),
};
