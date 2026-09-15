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

  /**
   * Trigger anomaly simulation for testing early warning systems
   * @param {'THERMAL_DROP_DOOR_OPEN'|'MOISTURE_SPIKE'|'SENSOR_OUT_OF_BOUNDS'} scenario
   */
  async simulateAnomaly(scenario) {
    return api.post('/anomalies/simulate', { scenario });
  },
};
