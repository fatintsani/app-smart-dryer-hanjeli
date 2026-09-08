import api from './api';

export const alertService = {
  async getAll(unreadOnly = false) {
    return api.get(`/alerts?unreadOnly=${unreadOnly}`);
  },

  async markAsRead(id) {
    return api.patch(`/alerts/${id}/read`);
  },

  async markAllAsRead() {
    return api.patch('/alerts/read-all');
  },

  async deleteAlert(id) {
    return api.delete(`/alerts/${id}`);
  },

  async clearAll() {
    return api.delete('/alerts/clear');
  },
};
