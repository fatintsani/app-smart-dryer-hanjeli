/**
 * USB Direct / Web Serial Client Service
 * Connects directly to ESP32 / Arduino microcontrollers via USB cable using Web Serial API
 * Supported in modern Chromium browsers (Google Chrome, Microsoft Edge, Opera, Brave).
 */

class UsbSerialService {
  constructor() {
    this.port = null;
    this.reader = null;
    this.isConnected = false;
    this.isConnecting = false;
    this.baudRate = 115200;
    this.portInfo = null;
    this.listeners = new Map();
    this.lineBuffer = '';
    this.readLoopActive = false;

    // Auto listen to device disconnect from OS
    if (this.isSupported()) {
      navigator.serial.addEventListener('disconnect', (event) => {
        if (event.target === this.port) {
          this.handleDisconnect('Kabel USB ESP32 terlepas atau dicabut dari laptop.');
        }
      });
    }
  }

  /**
   * Check if Web Serial API is supported by the current browser
   */
  isSupported() {
    return typeof navigator !== 'undefined' && 'serial' in navigator;
  }

  /**
   * Request user to pick a USB Serial Port and establish connection
   */
  async requestPortAndConnect(baudRate = 115200) {
    if (!this.isSupported()) {
      const msg = 'Web Serial API tidak didukung pada peramban ini. Harap gunakan Google Chrome atau Microsoft Edge.';
      this.emit('connection_state', { state: 'ERROR', error: msg });
      throw new Error(msg);
    }

    try {
      this.isConnecting = true;
      this.baudRate = Number(baudRate) || 115200;
      this.emit('connection_state', { state: 'CONNECTING', baudRate: this.baudRate });

      // Clean up previous reader/port if still open
      if (this.isConnected || this.port) {
        try {
          await this.disconnect();
        } catch (e) {
          console.warn('[UsbSerialService] Cleanup previous port warning:', e);
        }
      }

      // Request port from user browser picker popup
      const port = await navigator.serial.requestPort();
      this.port = port;

      await this.openPort();
      return true;
    } catch (error) {
      this.isConnecting = false;
      this.isConnected = false;
      const isCancelled = error.name === 'NotFoundError' || 
                          error.message?.includes('cancelled') || 
                          error.message?.includes('selected') || 
                          error.message?.includes('No port selected') ||
                          error.name === 'AbortError';
      const errMsg = isCancelled
        ? 'Pemilihan port USB Serial dibatalkan oleh pengguna.'
        : `Gagal membuka port USB Serial: ${error.message}`;

      this.emit('connection_state', {
        state: 'DISCONNECTED',
        error: isCancelled ? null : errMsg,
        cancelled: isCancelled
      });

      if (!isCancelled) {
        console.error('[UsbSerialService] Connect error:', error);
      }
      throw new Error(errMsg);
    }
  }

  /**
   * Open port with specified baud rate and start read stream
   */
  async openPort() {
    if (!this.port) return;

    try {
      await this.port.open({
        baudRate: this.baudRate,
        dataBits: 8,
        stopBits: 1,
        parity: 'none',
        bufferSize: 8192,
        flowControl: 'none'
      });
    } catch (err) {
      // If port is already opened by this session, we can proceed
      if (!err.message?.includes('already open')) {
        throw err;
      }
    }

    // Attempt to set DTR and RTS signals to assert USB communication on ESP32 DevKit boards
    if (this.port.setSignals) {
      try {
        await this.port.setSignals({ dataTerminalReady: true, requestToSend: true });
      } catch (e) {
        // Ignored if specific USB-UART bridge does not support signal manipulation
      }
    }

    const info = this.port.getInfo ? this.port.getInfo() : {};
    this.portInfo = {
      usbVendorId: info.usbVendorId ? `0x${info.usbVendorId.toString(16).toUpperCase()}` : 'ESP32 / Serial',
      usbProductId: info.usbProductId ? `0x${info.usbProductId.toString(16).toUpperCase()}` : 'CH340/CP2102',
      baudRate: this.baudRate,
    };

    this.isConnected = true;
    this.isConnecting = false;

    this.emit('connection_state', {
      state: 'CONNECTED',
      portInfo: this.portInfo,
      baudRate: this.baudRate,
      message: `Terhubung via Port USB Serial (${this.baudRate} baud)`
    });

    // Start robust stream reader loop
    this.startReadLoop();

    // Send initial handshake / newline to wake up ESP32 serial communication
    setTimeout(() => {
      if (this.isConnected) {
        this.sendCommand({ action: 'GET_TELEMETRY' }).catch(() => {});
      }
    }, 400);
  }

  /**
   * Background read loop using standard TextDecoder on stream chunks
   */
  async startReadLoop() {
    this.readLoopActive = true;
    const decoder = new TextDecoder();

    try {
      while (this.readLoopActive && this.port && this.port.readable) {
        this.reader = this.port.readable.getReader();
        try {
          while (this.readLoopActive) {
            const { value, done } = await this.reader.read();
            if (done) {
              break;
            }
            if (value) {
              const textChunk = decoder.decode(value, { stream: true });
              this.handleIncomingChunk(textChunk);
            }
          }
        } catch (readErr) {
          if (this.readLoopActive) {
            console.warn('[UsbSerialService] Read error:', readErr);
          }
        } finally {
          if (this.reader) {
            try {
              this.reader.releaseLock();
            } catch (e) {}
            this.reader = null;
          }
        }
      }
    } catch (error) {
      if (this.isConnected) {
        console.warn('[UsbSerialService] Stream closed:', error);
        this.handleDisconnect(`Koneksi USB terputus: ${error.message}`);
      }
    }
  }

  /**
   * Parse chunks into complete lines and extract telemetry JSON / CSV / Logs
   */
  handleIncomingChunk(chunk) {
    this.lineBuffer += chunk;
    const lines = this.lineBuffer.split(/\r?\n/);
    this.lineBuffer = lines.pop() || ''; // Keep incomplete trailing fragment

    for (const line of lines) {
      const trimmed = line.trim();
      if (!trimmed) continue;

      this.emit('raw_log', trimmed);

      // 1. Check for embedded JSON `{ ... }` anywhere in the line (e.g. `[MQTT Tx] { ... }` or pure `{ ... }`)
      const jsonStart = trimmed.indexOf('{');
      const jsonEnd = trimmed.lastIndexOf('}');

      if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
        const jsonCandidate = trimmed.substring(jsonStart, jsonEnd + 1);
        try {
          const parsed = JSON.parse(jsonCandidate);
          this.handleParsedPacket(parsed);
          continue;
        } catch (e) {
          // Fall through to other formats
        }
      }

      // 2. Fallback: Parse CSV telemetry format (e.g., 43.5, 54.0, 31.2, 66.0, 760, 13.8, 44.5)
      if (trimmed.includes(',')) {
        const parts = trimmed.split(',').map(s => s.trim());
        const numericParts = parts.map(Number);
        if (parts.length >= 4 && numericParts.every(n => !isNaN(n))) {
          this.handleParsedPacket({
            tempInternal: numericParts[0],
            humidityInternal: numericParts[1],
            tempExternal: numericParts[2] ?? 30.0,
            humidityExternal: numericParts[3] ?? 65.0,
            solarRadiation: numericParts[4] ?? 700.0,
            grainMoisture: numericParts[5] ?? 14.0,
            weightCurrentKg: numericParts[6] ?? 45.0,
          });
          continue;
        }
      }

      // 3. Fallback: Parse Key-Value format (e.g., "T:43.5 H:54.0" or "temp=43.5, hum=54.0")
      if (/[A-Za-z_]+[:=]\s*[-0-9.]+/.test(trimmed)) {
        const kvPairs = {};
        const matches = trimmed.matchAll(/([A-Za-z_]+)[:=]\s*([-0-9.]+)/g);
        for (const match of matches) {
          const key = match[1].toLowerCase();
          const val = parseFloat(match[2]);
          if (!isNaN(val)) {
            kvPairs[key] = val;
          }
        }
        if (Object.keys(kvPairs).length >= 2) {
          this.handleParsedPacket(kvPairs);
          continue;
        }
      }

      // 4. Any serial signal indicates hardware heartbeat
      if (
        trimmed.includes('ESP32') || 
        trimmed.includes('SMART ROOM DRYER') || 
        trimmed.includes('BLE') || 
        trimmed.includes('WiFi') || 
        trimmed.includes('MQTT') ||
        trimmed.includes('Connected')
      ) {
        this.emit('device_heartbeat', { raw: trimmed, timestamp: new Date().toISOString() });
      }
    }
  }

  /**
   * Normalize and emit sensor telemetry with comprehensive field support
   */
  handleParsedPacket(data) {
    if (!data || typeof data !== 'object') return;

    // Map common ESP32 keys (temperature, tempInternal, temp_internal, t, etc.)
    const tempIn = data.tempInternal ?? data.temp_internal ?? data.temperature ?? data.temp ?? data.t_in ?? data.t;
    const humIn = data.humidityInternal ?? data.humidity_internal ?? data.humidity ?? data.hum ?? data.h_in ?? data.h;
    const tempExt = data.tempExternal ?? data.temp_external ?? data.tempExt ?? data.temp_ext ?? data.t_ext;
    const humExt = data.humidityExternal ?? data.humidity_external ?? data.humidityExt ?? data.hum_ext ?? data.h_ext;
    const solar = data.solarRadiation ?? data.solar_radiation ?? data.solar ?? data.lux ?? data.radiation;
    const moisture = data.grainMoisture ?? data.grain_moisture ?? data.moisture ?? data.kadar_air ?? data.ka;
    const weight = data.weightCurrentKg ?? data.weight_current_kg ?? data.weight_kg ?? data.weight ?? data.bobot;
    const devId = data.deviceId || data.device_id || data.id || 'ESP32-USB';

    // Must have at least one valid measurement
    if (tempIn === undefined && humIn === undefined && moisture === undefined) {
      return;
    }

    const normalized = {
      deviceId: String(devId),
      tempInternal: typeof tempIn === 'number' ? tempIn : parseFloat(tempIn ?? 0.0),
      humidityInternal: typeof humIn === 'number' ? humIn : parseFloat(humIn ?? 0.0),
      tempExternal: typeof tempExt === 'number' ? tempExt : parseFloat(tempExt ?? 30.0),
      humidityExternal: typeof humExt === 'number' ? humExt : parseFloat(humExt ?? 65.0),
      solarRadiation: typeof solar === 'number' ? solar : parseFloat(solar ?? 700.0),
      grainMoisture: typeof moisture === 'number' ? moisture : parseFloat(moisture ?? 14.0),
      weightCurrentKg: typeof weight === 'number' ? weight : parseFloat(weight ?? 45.0),
      hasData: true,
      timestamp: data.timestamp || new Date().toISOString(),
      source: 'usb',
    };

    this.emit('sensor_data', normalized);

    // If actuator states are reported
    const exhaustFan = data.exhaustFanStatus ?? data.exhaust_fan_status ?? data.exhaustFan ?? data.fans ?? data.fan ?? data.relayFan;
    const exhaustSpeed = data.exhaustFanSpeed ?? data.exhaust_fan_speed ?? data.fanSpeed ?? 70;
    const intakeFan = data.intakeFanStatus ?? data.intake_fan_status ?? data.intakeFan;
    const circFan = data.circFanStatus ?? data.circ_fan_status ?? data.circFan;
    const heater = data.heaterStatus ?? data.heater_status ?? data.auxHeaterStatus ?? data.heaters ?? data.heater ?? data.relayHeater;
    const heaterLevel = data.heaterLevel ?? data.heater_level ?? data.auxHeaterLevel ?? 0;
    const isOverride = data.isOverrideActive ?? data.isManualOverride ?? data.manualOverride;
    const overrideMode = data.overrideMode ?? data.controlMode;

    if (exhaustFan !== undefined || heater !== undefined || data.actuators) {
      const actuators = data.actuators || {
        exhaustFanStatus: Boolean(exhaustFan),
        exhaustFanSpeed: Number(exhaustSpeed) || 70,
        intakeFanStatus: Boolean(intakeFan),
        circFanStatus: Boolean(circFan),
        auxHeaterStatus: Boolean(heater),
        auxHeaterLevel: Number(heaterLevel) || 0,
        controlMode: overrideMode || (isOverride ? 'MANUAL' : 'AUTOMATIC'),
      };
      this.emit('device_status', { actuators });
    }
  }

  /**
   * Send control command / string over USB serial
   */
  async sendCommand(commandData) {
    if (!this.isConnected || !this.port || !this.port.writable) {
      throw new Error('ESP32 belum terhubung via port USB Serial.');
    }

    let writer = null;
    try {
      const encoder = new TextEncoder();
      const payloadStr = typeof commandData === 'string'
        ? commandData
        : JSON.stringify(commandData);

      const data = encoder.encode(payloadStr + '\n');
      writer = this.port.writable.getWriter();
      await writer.write(data);
      return { success: true };
    } catch (error) {
      console.error('[UsbSerialService] Send command error:', error);
      throw new Error(`Gagal mengirim perintah via USB: ${error.message}`);
    } finally {
      if (writer) {
        try {
          writer.releaseLock();
        } catch (e) {}
      }
    }
  }

  /**
   * Disconnect USB Serial Port safely
   */
  async disconnect() {
    this.readLoopActive = false;

    if (this.reader) {
      try {
        await this.reader.cancel();
      } catch (e) {}
      try {
        this.reader.releaseLock();
      } catch (e) {}
      this.reader = null;
    }

    if (this.port) {
      try {
        await this.port.close();
      } catch (e) {
        console.warn('[UsbSerialService] Port close warning:', e);
      }
      this.port = null;
    }

    this.isConnected = false;
    this.isConnecting = false;
    this.portInfo = null;
    this.lineBuffer = '';

    this.emit('connection_state', {
      state: 'DISCONNECTED',
      message: 'Port USB Serial terputus.'
    });
  }

  /**
   * Handle unexpected disconnect
   */
  handleDisconnect(reason = 'Koneksi USB terputus.') {
    this.isConnected = false;
    this.isConnecting = false;
    this.readLoopActive = false;
    this.port = null;
    this.reader = null;
    this.lineBuffer = '';

    this.emit('connection_state', {
      state: 'DISCONNECTED',
      error: reason,
      message: reason
    });
  }

  /**
   * Event listeners management
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
          console.error(`[UsbSerialService] Event handler error (${event}):`, e);
        }
      });
    }
  }
}

export const usbSerialService = new UsbSerialService();
export default usbSerialService;
