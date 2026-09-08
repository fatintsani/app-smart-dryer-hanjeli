import api from './api';

export const actuatorService = {
  async getStatus() {
    return api.get('/actuators/status');
  },

  async updateStatus(data) {
    return api.patch('/actuators/control', data);
  },
};
