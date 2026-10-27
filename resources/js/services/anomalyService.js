import api from './api';

export const anomalyService = {
  /**
   * Get real-time sensor health matrix and active anomaly diagnosis
   */
  async getHealthStatus() {
    return api.get('/anomalies/status');
  },

  /**
   * Trigger an on-demand anomaly scan
   */
  async runCheck() {
    return api.post('/anomalies/check');
  },
};

