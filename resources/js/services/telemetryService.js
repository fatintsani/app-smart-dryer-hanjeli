import api from './api';

export const telemetryService = {
  async getCurrent() {
    return api.get('/telemetry/current');
  },

  async getHistory(hours = 24, batchId = null) {
    let url = `/telemetry/history?hours=${hours}`;
    if (batchId) {
      url += `&batchId=${batchId}`;
    }
    return api.get(url);
  },

  async ingest(data) {
    return api.post('/telemetry/ingest', data, {
      headers: {
        'x-api-key': 'esp32-greenhouse-hanjeli-secret-token',
      },
    });
  },
};
