import api from './api';

export const deviceService = {
  async getAll() {
    return api.get('/devices');
  },

  async create(data) {
    return api.post('/devices', data);
  },

  async update(id, data) {
    return api.put(`/devices/${id}`, data);
  },

  async ping(id) {
    return api.post(`/devices/${id}/ping`);
  },

  async toggle(id) {
    return api.patch(`/devices/${id}/toggle`);
  },

  async pingAll() {
    return api.post('/devices/ping-all');
  },

  async delete(id) {
    return api.delete(`/devices/${id}`);
  },
};
