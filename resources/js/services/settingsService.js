import { reactive } from 'vue';
import api from './api';
import connectionManager from './connectionManager';

const initialIotMode = typeof localStorage !== 'undefined' && localStorage.getItem('hanjeli_source_mode') === 'simulation' ? 'SIMULATION' : 'HARDWARE';

export const systemState = reactive({
  isSystemActive: true,
  iotMode: initialIotMode, // 'SIMULATION' | 'HARDWARE'
  environmentMode: 'LOCAL', // 'LOCAL' | 'PRODUCTION'
  maxSafeTemp: 55.0,
  minSafeTemp: 35.0,
  targetMoistureDefault: 12.0,
  samplingIntervalSeconds: 5,
  wifiSsid: 'GreenHouse_Hanjeli_IoT',
  ipAddress: '192.168.1.105',
  mqttHost: 'broker.emqx.io',
  mqttPort: 1883,
  mqttTopic: 'greenhouse/hanjeli/dryer01/sensor',
  whatsapp: {
    enabled: true,
    targetNumber: '+62 813-8899-2211',
    apiUrl: 'https://api.fonnte.com/send',
    apiKey: 'wA_s3cret_t0k3n_2023',
  },
  telegram: {
    enabled: false,
    botToken: '',
    chatId: '',
  },
});

export const settingsService = {
  async getSettings() {
    try {
      const res = await api.get('/settings');
      if (res) {
        Object.assign(systemState, {
          isSystemActive: res.isSystemActive !== undefined ? res.isSystemActive : systemState.isSystemActive,
          iotMode: res.iotMode || systemState.iotMode,
          environmentMode: res.environmentMode || systemState.environmentMode,
          maxSafeTemp: res.maxSafeTemp || systemState.maxSafeTemp,
          minSafeTemp: res.minSafeTemp || systemState.minSafeTemp,
          targetMoistureDefault: res.targetMoistureDefault || systemState.targetMoistureDefault,
          samplingIntervalSeconds: res.samplingIntervalSeconds || systemState.samplingIntervalSeconds,
          wifiSsid: res.wifiSsid || systemState.wifiSsid,
          ipAddress: res.ipAddress || systemState.ipAddress,
          mqttHost: res.mqttHost || systemState.mqttHost,
          mqttPort: res.mqttPort || systemState.mqttPort,
          mqttTopic: res.mqttTopic || systemState.mqttTopic,
          whatsapp: res.whatsapp || systemState.whatsapp,
          telegram: res.telegram || systemState.telegram,
        });

        // Always synchronize connectionManager with master backend database setting
        if (res.iotMode) {
          connectionManager.syncFromSettings(res.iotMode);
        }
      }
      return res || systemState;
    } catch (err) {
      console.warn('Using local cached system settings:', err);
      return systemState;
    }
  },

  async updateSettings(data) {
    try {
      const res = await api.put('/settings', data);
      if (res && res.settings) {
        Object.assign(systemState, res.settings);
        if (res.settings.iotMode) {
          connectionManager.syncFromSettings(res.settings.iotMode);
        }
      } else {
        Object.assign(systemState, data);
        if (data.iotMode) {
          connectionManager.syncFromSettings(data.iotMode);
        }
      }
      return res;
    } catch (err) {
      Object.assign(systemState, data);
      throw err;
    }
  },

  async setSystemActive(isActive) {
    return this.updateSettings({ isSystemActive: isActive });
  },

  async setIotMode(mode) {
    return this.updateSettings({ iotMode: mode });
  },

  async setEnvironmentMode(env) {
    return this.updateSettings({ environmentMode: env });
  },

  async testEmail(data) {
    return api.post('/settings/test-email', data);
  },

  async testTelegram(data) {
    return api.post('/settings/test-telegram', data);
  },

  async testWhatsApp(data) {
    return api.post('/settings/test-whatsapp', data);
  },

  async sendDailyDigestNow() {
    return api.post('/settings/daily-digest/send-now');
  },
};
