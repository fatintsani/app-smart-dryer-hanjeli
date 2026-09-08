/**
 * Bluetooth Low Energy (BLE) Client Service using Web Bluetooth API
 * Smart Room Dryer — Desa Wisata Hanjeli
 */

import { BLE_CONFIG } from './bleConstants';

class BleService {
  constructor() {
    this.device = null;
    this.server = null;
    this.service = null;
    this.characteristics = new Map();
    this.isConnected = false;
    this.listeners = new Map();
    this.deviceId = BLE_CONFIG.DEFAULT_DEVICE_ID;
    this.rssi = null;
    this.lastDataTime = null;
  }

  /**
   * Check if the current browser environment supports the Web Bluetooth API
   */
  isSupported() {
    return typeof navigator !== 'undefined' && 'bluetooth' in navigator;
  }

  /**
   * Request Bluetooth Device Pairing and Connect to GATT Server
   */
  async requestDeviceAndConnect() {
    if (!this.isSupported()) {
      const isHttps = typeof window !== 'undefined' && (window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1');
      let errMsg = 'Web Bluetooth API tidak didukung pada peramban ini. Harap gunakan Google Chrome atau Microsoft Edge.';
      if (!isHttps) {
        errMsg += ' (Catatan: Web Bluetooth memerlukan protokol HTTPS atau http://localhost/127.0.0.1)';
      }
      throw new Error(errMsg);
    }

    try {
      this.emit('connection_state', { state: 'CONNECTING', message: 'Mencari perangkat Smart Room Dryer...' });

      // Open native browser Bluetooth chooser directly on user gesture
      const device = await navigator.bluetooth.requestDevice({
        acceptAllDevices: true,
        optionalServices: [BLE_CONFIG.SERVICE_UUID.toLowerCase()],
      });

      if (!device) {
        throw new Error('Pemilihan perangkat Bluetooth dibatalkan.');
      }

      this.device = device;
      this.deviceId = device.name || BLE_CONFIG.DEFAULT_DEVICE_ID;

      // Handle spontaneous disconnection from hardware
      this.device.addEventListener('gattserverdisconnected', this.onDisconnected.bind(this));

      // Connect to GATT Server
      this.emit('connection_state', { state: 'CONNECTING', message: `Menghubungkan ke ${this.deviceId}...` });
      this.server = await this.device.gatt.connect();

      // Discover Primary Service
      this.service = await this.server.getPrimaryService(BLE_CONFIG.SERVICE_UUID.toLowerCase());

      // Discover Characteristics
      await this.setupCharacteristics();

      // Subscribe to real-time notifications
      await this.startNotifications();

      this.isConnected = true;
      this.lastDataTime = new Date();
      this.emit('connection_state', { 
        state: 'CONNECTED', 
        deviceId: this.deviceId,
        message: `Terhubung via Bluetooth BLE ke ${this.deviceId}` 
      });

      return {
        success: true,
        deviceId: this.deviceId,
      };
    } catch (error) {
      this.isConnected = false;
      const isCancelled = error.name === 'NotFoundError' || (error.message && error.message.includes('User cancelled'));
      const friendlyMessage = isCancelled 
        ? 'Pemilihan perangkat Bluetooth dibatalkan.' 
        : `Gagal terhubung Bluetooth: ${error.message}`;

      this.emit('connection_state', { 
        state: 'DISCONNECTED', 
        error: isCancelled ? null : error.message,
        message: friendlyMessage
      });
      
      if (!isCancelled) {
        throw error;
      }
      return { success: false, cancelled: true };
    }
  }

  /**
   * Resolve and map all required characteristics
   */
  async setupCharacteristics() {
    if (!this.service) return;

    for (const [key, uuid] of Object.entries(BLE_CONFIG.CHARACTERISTICS)) {
      try {
        const char = await this.service.getCharacteristic(uuid.toLowerCase());
        this.characteristics.set(key, char);
      } catch (err) {
        console.warn(`[BLE Service] Characteristic ${key} (${uuid}) not available on target device:`, err);
      }
    }
  }

  /**
   * Subscribe to notifications on Sensor Data and Device Status characteristics
   */
  async startNotifications() {
    // 1. Sensor Data Notification Listener
    const sensorChar = this.characteristics.get('SENSOR_DATA');
    if (sensorChar && sensorChar.properties.notify) {
      await sensorChar.startNotifications();
      sensorChar.addEventListener('characteristicvaluechanged', (event) => {
        const value = event.target.value;
        const decoded = this.decodeSensorPayload(value);
        if (decoded) {
          this.lastDataTime = new Date();
          this.emit('sensor_data', decoded);
        }
      });
    }

    // 2. Device Status Notification Listener
    const statusChar = this.characteristics.get('DEVICE_STATUS');
    if (statusChar && statusChar.properties.notify) {
      await statusChar.startNotifications();
      statusChar.addEventListener('characteristicvaluechanged', (event) => {
        const value = event.target.value;
        const decoded = this.decodeStatusPayload(value);
        if (decoded) {
          this.emit('device_status', decoded);
        }
      });
    }
  }

  /**
   * Send Control Command (e.g. Turn fan on/off, change speed, heater) to ESP32 over BLE
   */
  async sendControlCommand(commandPayload) {
    const controlChar = this.characteristics.get('CONTROL_COMMAND');
    if (!controlChar) {
      throw new Error('Karakteristik kontrol BLE tidak tersedia pada perangkat.');
    }

    const jsonString = typeof commandPayload === 'string' 
      ? commandPayload 
      : JSON.stringify(commandPayload);

    const encoder = new TextEncoder();
    const data = encoder.encode(jsonString);

    await controlChar.writeValue(data);
    return true;
  }

  /**
   * Send Wi-Fi & MQTT Provisioning Configuration to ESP32 over BLE
   */
  async sendWifiProvisioning({ ssid, password, mqttBroker, mqttPort = 1883, deviceId = 'SRD-001' }) {
    const configChar = this.characteristics.get('DEVICE_CONFIG');
    if (!configChar) {
      throw new Error('Karakteristik konfigurasi BLE tidak ditemukan pada ESP32.');
    }

    const payload = {
      action: 'SET_WIFI_CONFIG',
      ssid: ssid.trim(),
      password: password,
      mqtt_broker: (mqttBroker || 'broker.hivemq.com').trim(),
      mqtt_port: parseInt(mqttPort, 10) || 1883,
      device_id: deviceId.trim() || this.deviceId,
      timestamp: new Date().toISOString(),
    };

    const encoder = new TextEncoder();
    const data = encoder.encode(JSON.stringify(payload));

    await configChar.writeValue(data);
    return {
      success: true,
      message: 'Kredensial Wi-Fi & MQTT berhasil dikirim ke ESP32 via Bluetooth.',
    };
  }

  /**
   * Disconnect cleanly from the GATT server
   */
  async disconnect() {
    if (this.device && this.device.gatt.connected) {
      this.device.gatt.disconnect();
    }
    this.onDisconnected();
  }

  /**
   * Internal handler on device disconnection
   */
  onDisconnected() {
    this.isConnected = false;
    this.server = null;
    this.service = null;
    this.characteristics.clear();
    this.emit('connection_state', { 
      state: 'DISCONNECTED', 
      deviceId: this.deviceId,
      message: 'Koneksi Bluetooth Low Energy terputus.' 
    });
  }

  /**
   * Decode raw DataView from Sensor Data Characteristic into Normalized JS Object
   */
  decodeSensorPayload(dataView) {
    try {
      // Decode string UTF-8 JSON payload
      const decoder = new TextDecoder('utf-8');
      const text = decoder.decode(dataView);
      
      // If ESP32 sends JSON
      if (text.startsWith('{') && text.endsWith('}')) {
        const json = JSON.parse(text);
        return {
          deviceId: json.device_id || json.deviceId || this.deviceId,
          tempInternal: parseFloat(json.temperature ?? json.tempInternal ?? json.temp_internal ?? 0.0),
          humidityInternal: parseFloat(json.humidity ?? json.humidityInternal ?? json.humidity_internal ?? 0.0),
          tempExternal: parseFloat(json.tempExternal ?? json.temp_external ?? 30.0),
          humidityExternal: parseFloat(json.humidityExternal ?? json.humidity_external ?? 65.0),
          solarRadiation: parseFloat(json.solarRadiation ?? json.solar_radiation ?? 700.0),
          grainMoisture: parseFloat(json.grainMoisture ?? json.grain_moisture ?? 14.0),
          weightCurrentKg: parseFloat(json.weightCurrentKg ?? json.weight_kg ?? json.weightKg ?? 45.0),
          hasData: true,
          timestamp: json.timestamp || new Date().toISOString(),
          source: 'bluetooth',
        };
      }

      // If binary packed struct fallback (Float32Array / Int16):
      // Float32: TempInt(4), HumInt(4), TempExt(4), HumExt(4), Solar(4), Moisture(4), Weight(4)
      if (dataView.byteLength >= 28) {
        return {
          deviceId: this.deviceId,
          tempInternal: parseFloat(dataView.getFloat32(0, true).toFixed(1)),
          humidityInternal: parseFloat(dataView.getFloat32(4, true).toFixed(1)),
          tempExternal: parseFloat(dataView.getFloat32(8, true).toFixed(1)),
          humidityExternal: parseFloat(dataView.getFloat32(12, true).toFixed(1)),
          solarRadiation: parseFloat(dataView.getFloat32(16, true).toFixed(1)),
          grainMoisture: parseFloat(dataView.getFloat32(20, true).toFixed(1)),
          weightCurrentKg: parseFloat(dataView.getFloat32(24, true).toFixed(1)),
          hasData: true,
          timestamp: new Date().toISOString(),
          source: 'bluetooth',
        };
      }
    } catch (err) {
      console.error('[BLE Service] Error decoding sensor payload:', err);
    }
    return null;
  }

  /**
   * Decode raw DataView from Status Characteristic
   */
  decodeStatusPayload(dataView) {
    try {
      const decoder = new TextDecoder('utf-8');
      const text = decoder.decode(dataView);
      if (text.startsWith('{')) {
        return JSON.parse(text);
      }
      return { rawStatus: text };
    } catch (e) {
      return null;
    }
  }

  /**
   * Event emitter helpers
   */
  on(event, callback) {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, new Set());
    }
    this.listeners.get(event).add(callback);
    return () => this.off(event, callback);
  }

  off(event, callback) {
    if (this.listeners.has(event)) {
      if (callback) {
        this.listeners.get(event).delete(callback);
      } else {
        this.listeners.delete(event);
      }
    }
  }

  emit(event, data) {
    if (this.listeners.has(event)) {
      this.listeners.get(event).forEach((cb) => {
        try {
          cb(data);
        } catch (e) {
          console.error('[BLE Service] Callback error:', e);
        }
      });
    }
  }
}

export const bleService = new BleService();
export default bleService;
