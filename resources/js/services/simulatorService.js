/**
 * Simulator Service (Retired for Production)
 *
 * This service has been permanently decommissioned as the system
 * operates exclusively in 100% Production Direct-Hardware Mode (ESP32 Live).
 */
import { ref, reactive } from 'vue';

class RetiredSimulator {
  constructor() {
    this.isRunning = ref(false);
    this.packetsSent = ref(0);
    this.lastSentAt = ref(null);
    this.logs = ref([]);
    this.activeDbBatch = ref(null);
    this.currentValues = reactive({
      tempInternal: 0,
      humidityInternal: 0,
      tempExternal: 0,
      humidityExternal: 0,
      solarRadiation: 0,
      grainMoisture: 0,
      weightKg: 0,
    });
  }

  start() {
    console.warn('[HardwareSimulator] Simulator is decommissioned in production mode. System is connected directly to physical ESP32.');
  }

  stop() {}
  clearLogs() {}
  setScenario() {}
  setSpeed() {}
  sendPacket() {}
}

export const simulatorService = new RetiredSimulator();
