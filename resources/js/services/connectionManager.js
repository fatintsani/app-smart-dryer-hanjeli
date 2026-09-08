/**
 * Unified Connection Manager for Smart Room Dryer — Desa Wisata Hanjeli
 * 
 * Manages:
 * 1. Data Source Mode:
 *    - 'hardware': Mode Alat Fisik Live ESP32 (Wi-Fi/MQTT or BLE)
 *    - 'simulation': Mode Simulasi IoT
 * 2. Dual Connectivity Transports:
 *    - 'wifi': Wi-Fi + MQTT Broker (HiveMQ / Mosquitto & Reverb WebSockets)
 *    - 'ble': Bluetooth Low Energy (Web Bluetooth API)
 */

import { reactive } from 'vue';
import socketService from './socketService';
import bleService from './ble/bleService';
import { simulatorService } from './simulatorService';
import api from './api';
import { BLE_CONFIG, MQTT_CONFIG_DEFAULTS } from './ble/bleConstants';

class ConnectionManager {
  constructor() {
    this.listeners = new Map();

    const savedDeviceId = typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_device_id') || 'OPPO Reno') : 'OPPO Reno';
    const savedHotspot = typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_hotspot_name') || 'OPPO Reno') : 'OPPO Reno';

    // Reactive State for UI binding
    this.state = reactive({
      sourceMode: typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_source_mode') || 'hardware') : 'hardware', // 'hardware' | 'simulation'
      mode: typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_connection_mode') || 'wifi') : 'wifi',           // 'wifi' | 'ble'
      deviceId: savedDeviceId,
      hotspotName: savedHotspot,
      isConnected: false,
      isConnecting: false,
      isHardwareActive: false, // True ONLY when physical ESP32 is confirmed connected
      statusText: 'Disconnected',
      transportDetails: {
        wifiStatus: 'Standby',
        mqttBrokerStatus: 'Disconnected',
        mqttTopic: MQTT_CONFIG_DEFAULTS.TOPICS.telemetry(savedDeviceId),
        bleStatus: 'Disconnected',
        bleSupported: bleService.isSupported(),
        bleDeviceName: null,
        signalQuality: 'Good',
      },
      lastDataTimestamp: null,
      lastError: null,
    });

    this.initListeners();
    this.startWatchdog();
  }

  /**
   * Watchdog timer to automatically detect hardware disconnection/staleness
   */
  startWatchdog() {
    if (this.watchdogTimer) clearInterval(this.watchdogTimer);
    this.watchdogTimer = setInterval(() => {
      if (this.state.sourceMode === 'hardware') {
        if (this.state.mode === 'wifi') {
          const now = Date.now();
          const last = this.state.lastDataTimestamp ? new Date(this.state.lastDataTimestamp).getTime() : 0;
          const diffSec = (now - last) / 1000;
          // If no fresh live telemetry received in 15 seconds, mark hardware as inactive/standby
          if (diffSec > 15 && this.state.isHardwareActive) {
            this.state.isHardwareActive = false;
            this.state.statusText = 'Standby / Menunggu ESP32';
            this.emitBlankTelemetry();
            this.emit('connection_change', this.getStatusSnapshot());
          }
        } else if (this.state.mode === 'ble') {
          if (!bleService.isConnected && this.state.isHardwareActive) {
            this.state.isHardwareActive = false;
            this.state.isConnected = false;
            this.state.statusText = 'Disconnected';
            this.emitBlankTelemetry();
            this.emit('connection_change', this.getStatusSnapshot());
          }
        }
      }
    }, 3000);
  }

  /**
   * Emit blank/zeroed telemetry when hardware is disconnected or on standby
   */
  emitBlankTelemetry() {
    const blank = {
      deviceId: this.state.deviceId,
      tempInternal: 0.0,
      humidityInternal: 0.0,
      tempExternal: 0.0,
      humidityExternal: 0.0,
      solarRadiation: 0.0,
      grainMoisture: 0.0,
      weightCurrentKg: 0.0,
      hasData: false,
      isLive: false,
      timestamp: new Date().toISOString(),
      source: this.state.mode,
    };
    this.emit('telemetry_live', blank);
    this.emit('telemetry_update', blank);
  }

  /**
   * Set and persist custom Device Name / Identifier
   */
  setDeviceId(id) {
    if (!id || !id.trim()) return;
    const cleanId = id.trim();
    this.state.deviceId = cleanId;
    this.state.transportDetails.mqttTopic = MQTT_CONFIG_DEFAULTS.TOPICS.telemetry(cleanId);
    if (typeof localStorage !== 'undefined') {
      try {
        localStorage.setItem('hanjeli_device_id', cleanId);
      } catch (e) {}
    }
    this.emit('connection_change', this.getStatusSnapshot());
  }

  /**
   * Set and persist Hotspot / Wi-Fi SSID name
   */
  setHotspotName(name) {
    if (!name || !name.trim()) return;
    const cleanName = name.trim();
    this.state.hotspotName = cleanName;
    if (typeof localStorage !== 'undefined') {
      try {
        localStorage.setItem('hanjeli_hotspot_name', cleanName);
      } catch (e) {}
    }
    this.emit('connection_change', this.getStatusSnapshot());
  }

  /**
   * Bind events from both socketService, bleService, and simulatorService
   */
  initListeners() {
    // 1. Wi-Fi / MQTT (SocketService) events
    socketService.on('connection_status', (status) => {
      if (this.state.sourceMode === 'hardware' && this.state.mode === 'wifi') {
        this.state.isConnected = status.connected;
        this.state.transportDetails.mqttBrokerStatus = status.connected ? 'Connected' : 'Disconnected';
        this.state.statusText = this.state.isHardwareActive ? 'Connected' : 'Standby / Menunggu ESP32';
        this.emit('connection_change', this.getStatusSnapshot());
      }
    });

    socketService.on('telemetry_live', (data) => {
      if (this.state.sourceMode === 'hardware' && this.state.mode === 'wifi') {
        if (data && data.hasData && data.isLive !== false) {
          this.state.isHardwareActive = true;
          this.state.isConnected = true;
          this.state.lastDataTimestamp = new Date();
          if (data.deviceId || data.device_id) {
            const incomingId = data.deviceId || data.device_id;
            if (incomingId && incomingId !== this.state.deviceId) {
              this.state.deviceId = incomingId;
              try { localStorage.setItem('hanjeli_device_id', incomingId); } catch (e) {}
            }
          }
          const normalized = this.normalizeData(data, 'wifi');
          this.emit('telemetry_live', normalized);
          this.emit('telemetry_update', normalized);
        } else if (data && data.hasData === false) {
          this.state.isHardwareActive = false;
          this.state.statusText = 'Standby / Menunggu ESP32';
          this.emitBlankTelemetry();
        }
      }
    });

    socketService.on('actuators_update', (data) => {
      if (this.state.sourceMode === 'hardware' && this.state.mode === 'wifi') {
        this.emit('actuators_update', data);
        this.emit('actuator_update', data);
      }
    });

    socketService.on('alert_new', (alert) => {
      this.emit('alert_new', alert);
    });

    // 2. Bluetooth BLE (BleService) events
    bleService.on('connection_state', (data) => {
      if (this.state.sourceMode === 'hardware' && this.state.mode === 'ble') {
        if (data.state === 'CONNECTED') {
          this.state.isConnected = true;
          this.state.isConnecting = false;
          this.state.isHardwareActive = true;
          this.state.statusText = 'Connected';
          this.state.deviceId = data.deviceId || this.state.deviceId;
          this.state.transportDetails.bleStatus = 'Connected';
          this.state.transportDetails.bleDeviceName = data.deviceId;
          this.state.lastError = null;
        } else if (data.state === 'CONNECTING') {
          this.state.isConnecting = true;
          this.state.isConnected = false;
          this.state.isHardwareActive = false;
          this.state.statusText = 'Connecting';
          this.state.transportDetails.bleStatus = 'Connecting';
        } else {
          this.state.isConnected = false;
          this.state.isConnecting = false;
          this.state.isHardwareActive = false;
          this.state.statusText = 'Disconnected';
          this.state.transportDetails.bleStatus = 'Disconnected';
          if (data.error) this.state.lastError = data.error;

          // When hardware disconnects, immediately notify UI with hasData: false
          this.emitBlankTelemetry();
        }
        this.emit('connection_change', this.getStatusSnapshot());
      }
    });

    bleService.on('sensor_data', (data) => {
      if (this.state.sourceMode === 'hardware' && this.state.mode === 'ble') {
        this.state.isHardwareActive = true;
        this.state.isConnected = true;
        this.state.lastDataTimestamp = new Date();
        const normalized = this.normalizeData(data, 'bluetooth');
        this.emit('telemetry_live', normalized);
        this.emit('telemetry_update', normalized);

        // Sync to backend DB in background
        this.syncBleTelemetryToBackend(normalized);
      }
    });

    bleService.on('device_status', (data) => {
      if (this.state.sourceMode === 'hardware' && this.state.mode === 'ble') {
        this.emit('device_status', data);
        if (data.actuators) {
          this.emit('actuators_update', data.actuators);
        }
      }
    });
  }

  /**
   * Switch between 'hardware' (Live ESP32) and 'simulation' (IoT Simulator)
   */
  setSourceMode(source) {
    if (this.state.sourceMode === source) return;
    this.state.sourceMode = source;
    try { localStorage.setItem('hanjeli_source_mode', source); } catch (e) {}

    if (source === 'simulation') {
      // Start Simulator
      this.state.isConnected = true;
      this.state.isHardwareActive = true;
      this.state.statusText = 'Simulation Active';
      simulatorService.start();
    } else {
      // Stop Simulator, Return to Physical Hardware
      simulatorService.stop();
      
      // Determine if hardware is currently connected
      if (this.state.mode === 'ble') {
        this.state.isConnected = bleService.isConnected;
        this.state.isHardwareActive = bleService.isConnected;
        this.state.statusText = bleService.isConnected ? 'Connected' : 'Disconnected';
      } else {
        this.state.isConnected = socketService.isConnected;
        this.state.isHardwareActive = socketService.isConnected;
        this.state.statusText = socketService.isConnected ? 'Connected' : 'Disconnected';
      }

      if (!this.state.isHardwareActive) {
        this.emitBlankTelemetry();
      }
    }

    this.emit('connection_change', this.getStatusSnapshot());
  }

  /**
   * Set and switch the active Connection Mode ('wifi' | 'ble')
   */
  async setConnectionMode(targetMode) {
    if (this.state.mode === targetMode) return;

    this.state.mode = targetMode;
    try { localStorage.setItem('hanjeli_connection_mode', targetMode); } catch (e) {}

    if (targetMode === 'wifi') {
      socketService.connect();
      this.state.transportDetails.mqttBrokerStatus = socketService.isConnected ? 'Connected' : 'Connecting';
      this.state.statusText = socketService.isConnected ? 'Connected' : 'Connecting';
    } else if (targetMode === 'ble') {
      if (!bleService.isSupported()) {
        this.state.lastError = 'Browser ini tidak mendukung Web Bluetooth API.';
        this.state.statusText = 'Bluetooth Not Supported';
      } else {
        this.state.statusText = bleService.isConnected ? 'Connected' : 'Bluetooth Ready';
      }
    }

    this.emit('connection_change', this.getStatusSnapshot());
  }

  /**
   * Initiate connection for current mode
   */
  async connect() {
    if (this.state.mode === 'wifi') {
      return socketService.connect();
    } else if (this.state.mode === 'ble') {
      return await bleService.requestDeviceAndConnect();
    }
  }

  /**
   * Disconnect from current mode
   */
  async disconnect() {
    if (this.state.mode === 'wifi') {
      socketService.stop();
      this.state.isConnected = false;
      this.state.isHardwareActive = false;
      this.state.statusText = 'Disconnected';
    } else if (this.state.mode === 'ble') {
      await bleService.disconnect();
      this.state.isHardwareActive = false;
    }
    this.emitBlankTelemetry();
    this.emit('connection_change', this.getStatusSnapshot());
  }

  /**
   * Emit blank/standby telemetry state when hardware is disconnected
   */
  emitBlankTelemetry() {
    this.emit('telemetry_live', {
      tempInternal: 0,
      tempExternal: 0,
      humidityInternal: 0,
      humidityExternal: 0,
      grainMoisture: 0,
      solarRadiation: 0,
      weightCurrentKg: 0,
      hasData: false,
      timestamp: new Date().toISOString(),
      source: 'offline',
    });
  }

  /**
   * Send unified control command to ESP32
   */
  async sendCommand(commandData) {
    if (this.state.sourceMode === 'simulation') {
      // Apply directly to simulator
      Object.assign(simulatorService.simulatedActuators, commandData);
      return { success: true };
    }

    if (this.state.mode === 'ble' && bleService.isConnected) {
      // Direct BLE characteristic write
      return await bleService.sendControlCommand({
        device_id: this.state.deviceId,
        command: 'ACTUATOR_CONTROL',
        timestamp: new Date().toISOString(),
        data: commandData,
      });
    }

    // Default to Wi-Fi / API & MQTT publish
    const response = await api.patch('/actuators/control', commandData);
    return response;
  }

  /**
   * Configure ESP32 Wi-Fi & MQTT credentials over BLE
   */
  async configureWifiOverBle({ ssid, password, mqttBroker, mqttPort, deviceId }) {
    if (!bleService.isConnected) {
      throw new Error('ESP32 belum terhubung via Bluetooth BLE. Silakan sambungkan Bluetooth terlebih dahulu.');
    }
    return await bleService.sendWifiProvisioning({ ssid, password, mqttBroker, mqttPort, deviceId });
  }

  /**
   * Sync sensor data obtained via BLE to Laravel backend so historical logs and batches stay updated
   */
  async syncBleTelemetryToBackend(normalized) {
    try {
      await api.post('/telemetry/ingest', {
        tempInternal: normalized.tempInternal,
        humidityInternal: normalized.humidityInternal,
        tempExternal: normalized.tempExternal,
        humidityExternal: normalized.humidityExternal,
        solarRadiation: normalized.solarRadiation,
        grainMoisture: normalized.grainMoisture,
        weightKg: normalized.weightCurrentKg,
      });
    } catch (e) {
      // Background sync error ignored
    }
  }

  /**
   * Normalize telemetry data into a standard object
   */
  normalizeData(raw, source = 'wifi') {
    return {
      deviceId: raw.deviceId || raw.device_id || this.state.deviceId,
      tempInternal: typeof raw.tempInternal === 'number' ? raw.tempInternal : parseFloat(raw.temp_internal ?? 0.0),
      humidityInternal: typeof raw.humidityInternal === 'number' ? raw.humidityInternal : parseFloat(raw.humidity_internal ?? 0.0),
      tempExternal: typeof raw.tempExternal === 'number' ? raw.tempExternal : parseFloat(raw.temp_external ?? 30.0),
      humidityExternal: typeof raw.humidityExternal === 'number' ? raw.humidityExternal : parseFloat(raw.humidity_external ?? 65.0),
      solarRadiation: typeof raw.solarRadiation === 'number' ? raw.solarRadiation : parseFloat(raw.solar_radiation ?? 700.0),
      grainMoisture: typeof raw.grainMoisture === 'number' ? raw.grainMoisture : parseFloat(raw.grain_moisture ?? 14.0),
      weightCurrentKg: typeof raw.weightCurrentKg === 'number' ? raw.weightCurrentKg : parseFloat(raw.weight_kg ?? raw.weightKg ?? 45.0),
      hasData: true,
      timestamp: raw.timestamp || raw.recorded_at || new Date().toISOString(),
      source: source,
    };
  }

  /**
   * Snapshot of current connection state
   */
  getStatusSnapshot() {
    return {
      sourceMode: this.state.sourceMode,
      mode: this.state.mode,
      deviceId: this.state.deviceId,
      isConnected: this.state.isConnected,
      isHardwareActive: this.state.isHardwareActive,
      statusText: this.state.statusText,
      transportDetails: { ...this.state.transportDetails },
      lastDataTimestamp: this.state.lastDataTimestamp,
    };
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
          console.error('[ConnectionManager] Callback error:', e);
        }
      });
    }
  }
}

export const connectionManager = new ConnectionManager();
export default connectionManager;
