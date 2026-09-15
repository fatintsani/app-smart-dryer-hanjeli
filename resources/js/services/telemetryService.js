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
        'X-Device-Token': 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c',
        'x-api-key': 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c',
      },
    });
  },
};
