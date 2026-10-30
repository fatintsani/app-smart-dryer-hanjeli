import api from './api';

export const telemetryService = {
  async getCurrent() {
    return api.get('/telemetry/current');
  },

  async getHistory(periodOrHours = '1d', batchId = null) {
    let period = '1d';
    let hours = 24;
    if (typeof periodOrHours === 'number') {
      hours = periodOrHours;
      period = hours <= 24 ? '1d' : (hours <= 168 ? '1w' : (hours <= 720 ? '1m' : 'all'));
    } else if (typeof periodOrHours === 'string') {
      period = periodOrHours;
      if (period === '1d') hours = 24;
      else if (period === '1w') hours = 168;
      else if (period === '1m') hours = 720;
      else if (period === 'all') hours = 87600;
    }
    let url = `/telemetry/history?period=${period}&hours=${hours}`;
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
