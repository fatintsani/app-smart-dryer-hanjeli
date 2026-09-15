import { ref, reactive, computed } from 'vue';
import { telemetryService } from './telemetryService';
import { batchService } from './batchService';
import { socketService } from './socketService';

export const SCENARIOS = {
  optimal: {
    id: 'optimal',
    name: 'Pengeringan Normal (Optimal)',
    desc: 'Cuaca terik alami 42-46°C, kelembapan 45-52%, pengeringan berlangsung efisien.',
    icon: 'sun',
    color: '#0D631B',
    base: {
      tempInternal: 43.5,
      tempExternal: 32.0,
      humidityInternal: 49.0,
      humidityExternal: 66.0,
      grainMoisture: 18.0,
      solarRadiation: 750,
      weightCurrentKg: 130.0,
    }
  },
  high_heat: {
    id: 'high_heat',
    name: 'Panas Tinggi / Overheat (>50°C)',
    desc: 'Suhu ruangan melonjak tinggi, memicu sistem keselamatan & exhaust fan otomatis.',
    icon: 'flame',
    color: '#BA1A1A',
    base: {
      tempInternal: 54.5,
      tempExternal: 36.5,
      humidityInternal: 32.0,
      humidityExternal: 54.0,
      grainMoisture: 15.0,
      solarRadiation: 960,
      weightCurrentKg: 127.5,
    }
  },
  rainy: {
    id: 'rainy',
    name: 'Cuaca Hujan / Lembap (Pemanas Otomatis)',
    desc: 'Radiasi surya rendah & kelembapan tinggi, pemanas tambahan otomatis menyala.',
    icon: 'cloud-rain',
    color: '#005DB7',
    base: {
      tempInternal: 31.5,
      tempExternal: 26.0,
      humidityInternal: 79.0,
      humidityExternal: 89.0,
      grainMoisture: 22.0,
      solarRadiation: 150,
      weightCurrentKg: 136.0,
    }
  },
  target_reached: {
    id: 'target_reached',
    name: 'Gabah Kering (Target ≤12.0% Tercapai)',
    desc: 'Kadar air gabah telah mencapai target aman simpan standar industri (11.8%).',
    icon: 'target',
    color: '#D97706',
    base: {
      tempInternal: 41.2,
      tempExternal: 31.0,
      humidityInternal: 46.0,
      humidityExternal: 65.0,
      grainMoisture: 11.8,
      solarRadiation: 680,
      weightCurrentKg: 122.4,
    }
  },
  custom: {
    id: 'custom',
    name: 'Manual Sliders Kustom',
    desc: 'Kontrol manual penuh untuk menguji responsivitas sistem & aktuator.',
    icon: 'sliders',
    color: '#707A6C',
    base: {
      tempInternal: 42.0,
      tempExternal: 31.0,
      humidityInternal: 50.0,
      humidityExternal: 68.0,
      grainMoisture: 16.0,
      solarRadiation: 700,
      weightCurrentKg: 128.0,
    }
  }
};

class HardwareSimulator {
  constructor() {
    this.isRunning = ref(false);
    this.scenario = ref('optimal');
    this.speedMultiplier = ref(1); // 1x, 2x, 5x, 10x, 30x
    this.tickIntervalMs = ref(2000);
    this.packetsSent = ref(0);
    this.lastSentAt = ref(null);
    this.timer = null;

    // Database Active Batch State
    this.activeDbBatch = ref(null);
    this.isDbConnected = ref(true);

    // Rolling time-series history buffer (last 30 points)
    this.telemetryBuffer = ref([]);
    
    // Live TX/RX terminal logs
    this.terminalLogs = ref([]);
    this.logs = this.terminalLogs;

    // Live Physics State
    this.currentValues = reactive({
      tempInternal: 43.5,
      tempExternal: 32.0,
      humidityInternal: 49.0,
      humidityExternal: 66.0,
      grainMoisture: 18.0,
      solarRadiation: 750,
      weightCurrentKg: 130.0,
    });

    // Simulated Actuator State
    this.simulatedActuators = reactive({
      exhaustFanStatus: false,
      exhaustFanSpeed: 0,
      blowerFanStatus: false,
      blowerFanSpeed: 0,
      auxHeaterStatus: false,
      auxHeaterLevel: 0,
      roofVentStatus: true,
      controlMode: 'AUTOMATIC'
    });

    // Time elapsed in simulation seconds
    this.simulatedSeconds = ref(0);

    // Initial check for active batch in DB
    this.refreshActiveBatch();
  }

  async refreshActiveBatch() {
    try {
      const current = await telemetryService.getCurrent();
      if (current && current.activeBatch) {
        this.activeDbBatch.value = current.activeBatch;
      } else {
        const activeList = await batchService.getAll('ACTIVE');
        this.activeDbBatch.value = Array.isArray(activeList) && activeList.length > 0 ? activeList[0] : null;
      }
    } catch {
      this.activeDbBatch.value = null;
    }
  }

  async startDatabaseBatch(batchPayload = {}) {
    const defaultData = {
      cropVariety: batchPayload.cropVariety || 'Hanjeli Ketan Sukabumi (Varietas Unggul)',
      initialWeightKg: batchPayload.initialWeightKg || 135.0,
      initialMoisturePercent: batchPayload.initialMoisturePercent || 24.5,
      targetMoisturePercent: 12.0,
      dryingMode: 'HYBRID_SOLAR_ELECTRIC',
      notes: 'Sesi pengeringan terhubung live simulator IoT ESP32 ke MySQL.',
    };

    try {
      const res = await batchService.create(defaultData);
      this.activeDbBatch.value = res;
      this.currentValues.grainMoisture = defaultData.initialMoisturePercent;
      this.currentValues.weightCurrentKg = defaultData.initialWeightKg;
      this.addLog(`[DB-SESSION] Batch ${res.batchCode} berhasil dibuat & diaktifkan di MySQL!`);
      if (!this.isRunning.value) {
        this.start();
      }
      return res;
    } catch (err) {
      this.addLog(`[DB-ERROR] Gagal membuat batch: ${err.message}`);
      throw err;
    }
  }

  async completeDatabaseBatch() {
    if (!this.activeDbBatch.value) return null;
    const batchId = this.activeDbBatch.value.id || this.activeDbBatch.value.batchCode;
    try {
      const res = await batchService.complete(batchId, {
        finalMoisturePercent: +(this.currentValues.grainMoisture).toFixed(1),
        finalWeightKg: +(this.currentValues.weightCurrentKg).toFixed(1),
        qualityScore: 98,
        notes: 'Pengeringan selesai melalui monitoring & kontrol simulator IoT.',
      });
      const completedCode = this.activeDbBatch.value.batchCode;
      this.activeDbBatch.value = null;
      this.addLog(`[DB-SESSION] Batch ${completedCode} SELESAI & tersimpan permanen di riwayat database!`);
      return res;
    } catch (err) {
      this.addLog(`[DB-ERROR] Gagal menyelesaikan batch: ${err.message}`);
      throw err;
    }
  }

  async generateHistoricalSample(variety = 'Hanjeli Ketan Sukabumi (Grade A)') {
    try {
      const res = await batchService.seedSample(variety);
      this.addLog(`[DB-HISTORY] Riwayat batch ${res.batchCode} (${variety}) berhasil dibuat dengan 36 log sensor ke MySQL!`);
      return res;
    } catch (err) {
      this.addLog(`[DB-ERROR] Gagal membuat riwayat sample: ${err.message}`);
      throw err;
    }
  }

  setScenario(scenarioKey) {
    const keyMap = {
      'SUNNY': 'optimal',
      'CLOUDY': 'rainy',
      'OVERHEAT': 'high_heat',
      'TARGET_REACHED': 'target_reached',
      'optimal': 'optimal',
      'rainy': 'rainy',
      'high_heat': 'high_heat',
      'target_reached': 'target_reached',
      'custom': 'custom'
    };
    const mapped = keyMap[scenarioKey] || scenarioKey;
    if (!SCENARIOS[mapped]) return;
    this.scenario.value = mapped;
    const base = SCENARIOS[mapped].base;
    Object.assign(this.currentValues, base);
    this.addLog(`[SCENARIO] Skenario diubah ke '${SCENARIOS[mapped].name}'`);
  }

  clearLogs() {
    this.terminalLogs.value = [];
  }

  clearTerminal() {
    this.terminalLogs.value = [];
  }

  setSpeed(speed) {
    this.speedMultiplier.value = speed;
    this.addLog(`[SPEED] Simulation speed multiplier set to ${speed}x`);
  }

  start() {
    const activeSourceMode = typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_source_mode') || 'hardware') : 'hardware';
    if (activeSourceMode !== 'simulation') {
      this.stop();
      this.addLog(`[ESP32] Mode saat ini adalah 'Mode Alat Fisik Live'. Simulasi dinonaktifkan.`);
      return;
    }

    if (this.isRunning.value) return;
    this.isRunning.value = true;
    this.addLog(`[ESP32] Hardware Simulation STARTED. Node ID: ESP32-GH-HANJELI-01`);
    this.sendPacket();
    if (this.timer) {
      clearInterval(this.timer);
    }
    this.timer = setInterval(() => {
      const curMode = typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_source_mode') || 'hardware') : 'hardware';
      if (curMode !== 'simulation' || !this.isRunning.value) {
        this.stop();
        return;
      }
      this.stepPhysics();
      this.sendPacket();
    }, this.tickIntervalMs.value);
  }

  stop() {
    this.isRunning.value = false;
    if (this.timer) {
      clearInterval(this.timer);
      this.timer = null;
    }
    this.addLog(`[ESP32] Hardware Simulation STOPPED / PAUSED.`);
  }

  toggle() {
    const activeSourceMode = typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_source_mode') || 'hardware') : 'hardware';
    if (activeSourceMode !== 'simulation') {
      this.stop();
      return;
    }
    if (this.isRunning.value) {
      this.stop();
    } else {
      this.start();
    }
  }

  addLog(msg) {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    this.terminalLogs.value.unshift({ id: Date.now() + Math.random(), time: timeStr, text: msg, message: msg });
    if (this.terminalLogs.value.length > 50) {
      this.terminalLogs.value.pop();
    }
  }

  // Complex dynamic physics, thermodynamics, and grain drying kinetics
  stepPhysics() {
    const mult = this.speedMultiplier.value;
    const stepSeconds = (this.tickIntervalMs.value / 1000) * mult;
    this.simulatedSeconds.value += stepSeconds;

    const noise = (amplitude) => (Math.random() - 0.5) * 2 * amplitude;

    // 1. Solar Radiation drift with multi-frequency solar waves
    let targetSolar = SCENARIOS[this.scenario.value]?.base?.solarRadiation || 750;
    if (this.scenario.value === 'optimal') {
      const solarWave1 = Math.sin(this.simulatedSeconds.value * 0.08) * 115;
      const solarWave2 = Math.cos(this.simulatedSeconds.value * 0.19) * 45;
      targetSolar = Math.max(450, Math.min(980, 760 + solarWave1 + solarWave2 + noise(20)));
    } else if (this.scenario.value === 'high_heat') {
      targetSolar = Math.max(900, Math.min(1080, 970 + Math.sin(this.simulatedSeconds.value * 0.1) * 60 + noise(25)));
    } else if (this.scenario.value === 'rainy') {
      targetSolar = Math.max(80, Math.min(260, 160 + Math.sin(this.simulatedSeconds.value * 0.05) * 40 + noise(15)));
    }
    this.currentValues.solarRadiation = Math.round(targetSolar);

    // 2. External Temperature & Humidity drift
    const baseExt = SCENARIOS[this.scenario.value]?.base?.tempExternal || 30.5;
    const ambientBreeze = Math.sin(this.simulatedSeconds.value * 0.04) * 1.8 + noise(0.35);
    this.currentValues.tempExternal = +(baseExt + ambientBreeze).toFixed(1);
    this.currentValues.humidityExternal = +(Math.max(45, Math.min(88, 66.0 - (ambientBreeze * 2.6) + noise(1.8)))).toFixed(1);

    // 3. Actuator Auto-Regulation logic (Simulated Firmware Controller)
    if (this.currentValues.tempInternal >= 49.0) {
      this.simulatedActuators.exhaustFanStatus = true;
      const speed = Math.min(100, Math.round(55 + (this.currentValues.tempInternal - 49.0) * 8));
      this.simulatedActuators.exhaustFanSpeed = speed;
      this.simulatedActuators.blowerFanStatus = true;
      this.simulatedActuators.blowerFanSpeed = Math.min(100, speed + 10);
    } else {
      this.simulatedActuators.exhaustFanStatus = false;
      this.simulatedActuators.exhaustFanSpeed = 0;
      this.simulatedActuators.blowerFanStatus = false;
      this.simulatedActuators.blowerFanSpeed = 0;
    }

    if (this.currentValues.tempInternal < 36.0 || (this.currentValues.humidityInternal > 75.0 && this.currentValues.solarRadiation < 300)) {
      this.simulatedActuators.auxHeaterStatus = true;
      this.simulatedActuators.auxHeaterLevel = 75;
      this.simulatedActuators.blowerFanStatus = true;
      this.simulatedActuators.blowerFanSpeed = 60;
    } else {
      this.simulatedActuators.auxHeaterStatus = false;
      this.simulatedActuators.auxHeaterLevel = 0;
    }

    // 4. Internal Temperature Thermodynamics (Greenhouse Effect + Convection Waves)
    const heaterContribution = this.simulatedActuators.auxHeaterStatus ? (this.simulatedActuators.auxHeaterLevel * 0.095) : 0;
    const fanCooling = this.simulatedActuators.exhaustFanStatus ? (this.simulatedActuators.exhaustFanSpeed * 0.055) : 0;
    const baseInternalTarget = this.currentValues.tempExternal + 10.5 + ((this.currentValues.solarRadiation - 500) * 0.016) + heaterContribution - fanCooling;

    // Organic convection waves to ensure live charts visibly pulse with rich waveforms
    const convectionWave = Math.sin(this.simulatedSeconds.value * 0.12) * 1.7 + Math.cos(this.simulatedSeconds.value * 0.27) * 0.9;
    const targetInternalTemp = baseInternalTarget + convectionWave + noise(0.45);

    let nextTemp = this.currentValues.tempInternal + (targetInternalTemp - this.currentValues.tempInternal) * 0.28;
    this.currentValues.tempInternal = Math.max(22, Math.min(65, +nextTemp.toFixed(1)));

    // 5. Internal Humidity (Psychrometric dynamic wave 38% - 68% RH)
    const baseInternalHum = 72.0 - ((this.currentValues.tempInternal - 30.0) * 1.75) - (this.simulatedActuators.exhaustFanStatus ? (this.simulatedActuators.exhaustFanSpeed * 0.18) : 0);
    const humidityWave = -Math.sin(this.simulatedSeconds.value * 0.12) * 2.8 + Math.cos(this.simulatedSeconds.value * 0.22) * 1.4;
    const targetInternalHum = baseInternalHum + humidityWave + noise(0.95);

    let nextHum = this.currentValues.humidityInternal + (targetInternalHum - this.currentValues.humidityInternal) * 0.25;
    this.currentValues.humidityInternal = Math.max(20, Math.min(95, +nextHum.toFixed(1)));

    // 6. Grain Drying Kinetics (Thin-Layer Dehydration towards target 12.0%)
    if (this.currentValues.grainMoisture > 12.0) {
      const thermalFactor = Math.max(0.3, (this.currentValues.tempInternal - 28.0) / 20.0);
      const microDryingSpeed = (0.045 * thermalFactor) * (1.0 + Math.sin(this.simulatedSeconds.value * 0.07) * 0.2) * stepSeconds;
      
      const nextMoisture = Math.max(12.0, this.currentValues.grainMoisture - microDryingSpeed);
      this.currentValues.grainMoisture = +nextMoisture.toFixed(2);

      // Mass evaporation: Weight loss is proportional to water mass removed
      const initialMoisture = 24.5;
      const initialWeight = 50.0;
      const currentMoisture = this.currentValues.grainMoisture;
      const calcWeight = initialWeight * ((100 - initialMoisture) / (100 - currentMoisture));
      this.currentValues.weightCurrentKg = +calcWeight.toFixed(2);
    } else {
      // Stabilized around target equilibrium
      const emcOscillation = Math.sin(this.simulatedSeconds.value * 0.1) * 0.08 + noise(0.03);
      this.currentValues.grainMoisture = +(12.0 + emcOscillation).toFixed(2);
      this.currentValues.weightCurrentKg = +(42.5 + emcOscillation * 0.05).toFixed(2);
    }
  }

  async sendPacket() {
    const activeSourceMode = typeof localStorage !== 'undefined' ? (localStorage.getItem('hanjeli_source_mode') || 'hardware') : 'hardware';
    if (activeSourceMode !== 'simulation') {
      this.stop();
      return;
    }

    const payload = {
      batchId: this.activeDbBatch.value?.id || undefined,
      tempInternal: +(this.currentValues.tempInternal).toFixed(1),
      tempExternal: +(this.currentValues.tempExternal).toFixed(1),
      humidityInternal: +(this.currentValues.humidityInternal).toFixed(1),
      humidityExternal: +(this.currentValues.humidityExternal).toFixed(1),
      grainMoisture: +(this.currentValues.grainMoisture).toFixed(1),
      solarRadiation: Math.round(this.currentValues.solarRadiation),
      weightCurrentKg: +(this.currentValues.weightCurrentKg).toFixed(1),
      simulated: true,
      source: 'simulation',
    };

    const timestamp = new Date();
    const timeLabel = timestamp.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

    // Append to live rolling buffer
    const point = {
      ...payload,
      timestamp,
      timeLabel,
      fanSpeed: this.simulatedActuators.exhaustFanSpeed,
      heaterLevel: this.simulatedActuators.auxHeaterLevel
    };

    this.telemetryBuffer.value.push(point);
    if (this.telemetryBuffer.value.length > 30) {
      this.telemetryBuffer.value.shift();
    }

    try {
      await telemetryService.ingest(payload);
      this.packetsSent.value++;
      this.lastSentAt.value = timeLabel;
      this.addLog(`[TX] ESP32-SIM -> POST 200 OK | Temp: ${payload.tempInternal}°C | RH: ${payload.humidityInternal}% | Solar: ${payload.solarRadiation}W/m² | Moisture: ${payload.grainMoisture}%`);
    } catch (err) {
      // Broadcast locally through socket if backend is busy
      socketService.emit('telemetry_live', { ...payload, hasData: true, timestamp, simulated: true, source: 'simulation' });
      socketService.emit('actuators_update', { ...this.simulatedActuators });
      this.packetsSent.value++;
      this.lastSentAt.value = timeLabel;
      this.addLog(`[TX-LOCAL] Emitted Live Telemetry | Temp: ${payload.tempInternal}°C | RH: ${payload.humidityInternal}%`);
    }
  }
}

export const simulatorService = new HardwareSimulator();
