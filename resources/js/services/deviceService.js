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

  async pingAll() {
    return api.post('/devices/ping-all');
  },

  async regenerateToken(id) {
    return api.post(`/devices/${id}/regenerate-token`);
  },

  async triggerOta(id, targetVersion) {
    return api.post(`/devices/${id}/trigger-ota`, { targetVersion });
  },

  async reportOtaProgress(id, payload) {
    return api.post('/firmware/ota/progress', { deviceId: id, ...payload });
  },

  async getFirmwareReleases() {
    return api.get('/firmware/releases');
  },

  async createFirmwareRelease(data) {
    return api.post('/firmware/releases', data);
  },

  async delete(id) {
    return api.delete(`/devices/${id}`);
  },
};

