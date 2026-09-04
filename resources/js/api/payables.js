import axios from '../lib/axios';

export default {
    index: (params) => axios.get('/api/payables', { params }),
    show: (id) => axios.get(`/api/payables/${id}`),
    store: (data) => axios.post('/api/payables', data),
    update: (id, data) => axios.put(`/api/payables/${id}`, data),
    destroy: (id) => axios.delete(`/api/payables/${id}`),
    pay: (id) => axios.patch(`/api/payables/${id}/pay`),
};
