/**
 * Bluetooth Low Energy (BLE) UUIDs and Configuration Constants
 * Smart Room Dryer — Desa Wisata Hanjeli
 */

export const BLE_CONFIG = {
  // Primary Service UUID
  SERVICE_UUID: '19b10000-e8f2-537e-4f6c-d104768a1214',

  // Characteristics
  CHARACTERISTICS: {
    // Read / Notify: Realtime Sensor Telemetry (Temp, Hum, Moisture, Weight, Solar)
    SENSOR_DATA: '19b10001-e8f2-537e-4f6c-d104768a1214',

    // Read / Notify: Device Status & Operational Mode (Running, Paused, Error)
    DEVICE_STATUS: '19b10002-e8f2-537e-4f6c-d104768a1214',

    // Write / Read: Actuators Control & Commands (Relays, Fans, Heater, Override)
    CONTROL_COMMAND: '19b10003-e8f2-537e-4f6c-d104768a1214',

    // Write / Read: Wi-Fi & MQTT Provisioning Configuration
    DEVICE_CONFIG: '19b10004-e8f2-537e-4f6c-d104768a1214',
  },

  // Device advertising name filters
  DEVICE_NAME_PREFIXES: [
    'SmartDryer',
    'SRD-',
    'Hanjeli',
    'SmartRoomDryer',
  ],

  // Default Device ID fallback
  DEFAULT_DEVICE_ID: 'SRD-001',
};

export const MQTT_CONFIG_DEFAULTS = {
  DEFAULT_DEVICE_ID: 'SRD-001',
  TOPIC_PREFIX: 'smartroomdryer/device',
  TOPICS: {
    telemetry: (id = 'SRD-001') => `smartroomdryer/device/${id}/telemetry`,
    status: (id = 'SRD-001') => `smartroomdryer/device/${id}/status`,
    command: (id = 'SRD-001') => `smartroomdryer/device/${id}/command`,
    config: (id = 'SRD-001') => `smartroomdryer/device/${id}/config`,
  },
  // Also preserve existing legacy topics for backwards compatibility
  LEGACY_TOPICS: {
    telemetry: 'hanjeli/greenhouse/telemetry',
    actuators: 'hanjeli/greenhouse/actuators',
    control: 'hanjeli/greenhouse/control',
    command: 'hanjeli/greenhouse/command',
  },
};
