import api from './api';

export const batchService = {
  async getAll(status = null, search = null) {
    let url = '/batches';
    const params = [];
    if (status) params.push(`status=${encodeURIComponent(status)}`);
    if (search) params.push(`search=${encodeURIComponent(search)}`);
    if (params.length > 0) url += `?${params.join('&')}`;
    return api.get(url);
  },

  async getActiveBatch() {
    return api.get('/batches/active');
  },

  async getById(id) {
    return api.get(`/batches/${id}`);
  },

  async create(data) {
    return api.post('/batches', data);
  },

  async update(id, data) {
    return api.put(`/batches/${id}`, data);
  },

  async pause(id) {
    return api.patch(`/batches/${id}/pause`);
  },

  async resume(id) {
    return api.patch(`/batches/${id}/resume`);
  },

  async complete(id, data = {}) {
    return api.patch(`/batches/${id}/complete`, data);
  },

  async getExportData(id) {
    return api.get(`/batches/${id}/export`);
  },

  async seedSample(cropVariety = 'Hanjeli Ketan Sukabumi (Grade A)') {
    return api.post('/batches/seed-sample', { cropVariety });
  },
};
