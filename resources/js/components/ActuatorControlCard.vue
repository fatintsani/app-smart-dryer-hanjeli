<template>
  <div class="actuator-control-card" :class="{ 'is-override': actuators.isOverrideActive }">
    <!-- Card Header with Mode Toggle & Status -->
    <div class="acc-header">
      <div class="acc-title-group">
        <div class="acc-icon-chip" :class="actuators.isOverrideActive ? 'chip-manual' : 'chip-auto'">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
            <line x1="6" y1="6" x2="6.01" y2="6"></line>
            <line x1="6" y1="18" x2="6.01" y2="18"></line>
          </svg>
        </div>
        <div>
          <div class="acc-heading-row">
            <h3 class="acc-title">{{ currentLang === 'id' ? 'Pusat Kontrol Aktuator & Relai Hardware' : 'Actuator & Hardware Relay Control' }}</h3>
            <span class="acc-mode-pill" :class="actuators.isOverrideActive ? 'mode-manual' : 'mode-auto'">
              {{ actuators.isOverrideActive ? (currentLang === 'id' ? 'MANUAL OVERRIDE' : 'MANUAL OVERRIDE') : (currentLang === 'id' ? 'OTOMATIS (ON/OFF HISTESIS)' : 'AUTO (ON/OFF HYSTERESIS)') }}
            </span>
          </div>
          <p class="acc-subtitle">
            {{ actuators.isOverrideActive 
              ? (currentLang === 'id' ? 'Kontrol manual aktif: Anda memiliki kendali langsung atas sakelar relai daya.' : 'Manual control active: You have direct control over power relays.') 
              : (currentLang === 'id' ? 'Regulasi otomatis aktif: PTC Heater ON/OFF berbasis ambang cahaya lux (BH1750), hujan, dan histeresis suhu (48°C - 50°C).' : 'Automatic regulation active: PTC Heater ON/OFF based on BH1750 solar lux, rain detection, and temperature hysteresis (48°C - 50°C).') 
            }}
          </p>
        </div>
      </div>

      <!-- Override Mode Switch -->
      <div class="acc-mode-switch-wrap">
        <div class="mode-switch-container" @click="toggleOverrideMode" :title="currentLang === 'id' ? 'Beralih antara Mode Otomatis dan Manual Override' : 'Toggle between Auto and Manual Override'">
          <span class="switch-label-auto" :class="{ active: !actuators.isOverrideActive }">AUTO</span>
          <div class="mode-slider" :class="{ 'slider-manual': actuators.isOverrideActive }">
            <div class="mode-slider-thumb"></div>
          </div>
          <span class="switch-label-manual" :class="{ active: actuators.isOverrideActive }">MANUAL</span>
        </div>
      </div>
    </div>

    <!-- Feedback Toast Mini -->
    <Transition name="fade">
      <div v-if="feedbackMsg" class="acc-feedback-bar" :class="feedbackType">
        <span>{{ feedbackMsg }}</span>
      </div>
    </Transition>

    <!-- Actuators Grid -->
    <div class="actuators-grid">
      <!-- 1. Relai Pemanas PTC Channel 1 -->
      <div class="actuator-item-card is-active is-heater">
        <div class="aic-top-row">
          <div class="aic-icon-box heater-icon" :class="{ 'heat-pulse': actuators.auxHeaterStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 2v20"></path>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Relai Pemanas PTC - Channel 1' : 'PTC Heater Relay - Channel 1' }}</h4>
            <span class="aic-pin">Pin GPIO 26 &bull; Beban Pemanas Utama (ON/OFF)</span>
          </div>
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.auxHeaterStatus" 
              @change="handleActuatorToggle('auxHeaterStatus', actuators.auxHeaterStatus)"
            />
            <span class="toggle-slider slider-orange"></span>
          </label>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag" :class="actuators.auxHeaterStatus ? 'tag-orange' : 'tag-off'">
            {{ actuators.auxHeaterStatus 
              ? (currentLang === 'id' ? 'RELAY CH1: AKTIF (ON)' : 'RELAY CH1: ON') 
              : (currentLang === 'id' ? 'RELAY CH1: STANDBY (OFF)' : 'RELAY CH1: OFF') 
            }}
          </span>
          <span class="status-desc">
            {{ currentLang === 'id' ? 'Histeresis: Mati ≥50°C • Nyala ≤48°C' : 'Hysteresis: Cut-off ≥50°C • Restart ≤48°C' }}
          </span>
        </div>
      </div>

      <!-- 2. Relai Pemanas PTC Channel 2 -->
      <div class="actuator-item-card is-active is-heater">
        <div class="aic-top-row">
          <div class="aic-icon-box heater-icon" :class="{ 'heat-pulse': actuators.auxHeaterStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 2v20"></path>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Relai Pemanas PTC - Channel 2' : 'PTC Heater Relay - Channel 2' }}</h4>
            <span class="aic-pin">Pin GPIO 27 &bull; Beban Pemanas Sekunder (ON/OFF)</span>
          </div>
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.auxHeaterStatus" 
              @change="handleActuatorToggle('auxHeaterStatus', actuators.auxHeaterStatus)"
            />
            <span class="toggle-slider slider-orange"></span>
          </label>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag" :class="actuators.auxHeaterStatus ? 'tag-orange' : 'tag-off'">
            {{ actuators.auxHeaterStatus 
              ? (currentLang === 'id' ? 'RELAY CH2: AKTIF (ON)' : 'RELAY CH2: ON') 
              : (currentLang === 'id' ? 'RELAY CH2: STANDBY (OFF)' : 'RELAY CH2: OFF') 
            }}
          </span>
          <span class="status-desc">
            {{ currentLang === 'id' ? 'Sinkron Ganda: Menjaga kestabilan panas' : 'Dual-Synched Thermal Stage' }}
          </span>
        </div>
      </div>

      <!-- 3. Layar LCD 20x4 I2C -->
      <div class="actuator-item-card is-active">
        <div class="aic-top-row">
          <div class="aic-icon-box" style="background:#0D631B; color:#FFFFFF; border-color:#0D631B;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
              <line x1="8" y1="21" x2="16" y2="21"></line>
              <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Display Layar LCD 20x4 I2C' : 'LCD 20x4 I2C Display' }}</h4>
            <span class="aic-pin">I2C (SDA 21, SCL 22) &bull; Addr: 0x27</span>
          </div>
          <div class="status-tag tag-on" style="font-size:11px;">
            ONLINE
          </div>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag tag-on">
            {{ currentLang === 'id' ? 'AKTIF (HALAMAN 1 & 2)' : 'ACTIVE (PAGE 1 & 2)' }}
          </span>
          <span class="status-desc">
            {{ currentLang === 'id' ? 'Tampil Suhu, Lembap, Lux, Rain, PTC' : 'Displaying T, H, Lux, Rain, PTC' }}
          </span>
        </div>
      </div>
    </div>

    <!-- 7. Panel Physical Indicator LEDs Widget -->
    <div class="panel-led-indicator-bar">
      <div class="pli-left">
        <div class="pli-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="3" y="3" width="18" height="18" rx="3"></rect>
            <circle cx="8" cy="12" r="2.5" fill="currentColor"></circle>
            <circle cx="12" cy="12" r="2.5" fill="currentColor"></circle>
            <circle cx="16" cy="12" r="2.5" fill="currentColor"></circle>
          </svg>
        </div>
        <div>
          <h4 class="pli-title">{{ currentLang === 'id' ? 'Status Lampu LED Indikator Panel Box Fisik' : 'Physical Box Panel LED Indicators' }}</h4>
          <p class="pli-sub">{{ currentLang === 'id' ? 'Sinkron otomatis: Mengikuti status pemanas PTC & kondisi suhu proteksi sensor' : 'Auto-synced: Reflects PTC heater state & sensor thermal safety' }}</p>
        </div>
      </div>

      <div class="pli-led-group">
        <!-- 1. LED HIJAU -->
        <div class="pli-led-item" :class="{ 'is-lit-green': isLedGreenActive }">
          <span class="led-bulb bulb-green"></span>
          <div class="led-text-box">
            <span class="led-color-title">{{ currentLang === 'id' ? 'HIJAU' : 'GREEN' }}</span>
            <span class="led-condition">{{ isLedGreenActive ? (currentLang === 'id' ? 'NYALA (Standby/Normal)' : 'ON (Standby/Normal)') : (currentLang === 'id' ? 'MATI' : 'OFF') }}</span>
          </div>
        </div>

        <!-- 2. LED KUNING -->
        <div class="pli-led-item" :class="{ 'is-lit-yellow': isLedYellowActive }">
          <span class="led-bulb bulb-yellow"></span>
          <div class="led-text-box">
            <span class="led-color-title">{{ currentLang === 'id' ? 'KUNING' : 'YELLOW' }}</span>
            <span class="led-condition">{{ isLedYellowActive ? (currentLang === 'id' ? 'NYALA (PTC Aktif)' : 'ON (PTC Heating)') : (currentLang === 'id' ? 'MATI' : 'OFF') }}</span>
          </div>
        </div>

        <!-- 3. LED MERAH -->
        <div class="pli-led-item" :class="{ 'is-lit-red': isLedRedActive }">
          <span class="led-bulb bulb-red"></span>
          <div class="led-text-box">
            <span class="led-color-title">{{ currentLang === 'id' ? 'MERAH' : 'RED' }}</span>
            <span class="led-condition">{{ isLedRedActive ? (currentLang === 'id' ? 'NYALA (Lockout ≥50°C)' : 'ON (Lockout ≥50°C)') : (currentLang === 'id' ? 'MATI (Aman)' : 'OFF (Safe)') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { actuatorService } from '../services/actuatorService'
import { telemetryService } from '../services/telemetryService'
import { socketService } from '../services/socketService'
import connectionManager from '../services/connectionManager'

const props = defineProps({
  currentLang: {
    type: String,
    default: 'id'
  }
})

const currentTemp = ref(32.0)

const actuators = ref({
  exhaustFanStatus: true,
  exhaustFanSpeed: 60,
  intakeFanStatus: true,
  circFanStatus: true,
  auxHeaterStatus: true,
  auxHeaterLevel: 45,
  uvLightStatus: false,
  rotaryTrayStatus: true,
  dehumidifierStatus: true,
  isOverrideActive: false,
  overrideMode: 'AUTO'
})

// Physical Box 3-LED Indicators Computed Status
const isLedRedActive = computed(() => {
  return currentTemp.value >= 50.0
})

const isLedYellowActive = computed(() => {
  return Boolean(actuators.value.auxHeaterStatus) && !isLedRedActive.value
})

const isLedGreenActive = computed(() => {
  return !actuators.value.auxHeaterStatus && !isLedRedActive.value
})

const feedbackMsg = ref('')
const feedbackType = ref('success')
let feedbackTimer = null

function showFeedback(msg, type = 'success') {
  feedbackMsg.value = msg
  feedbackType.value = type
  if (feedbackTimer) clearTimeout(feedbackTimer)
  feedbackTimer = setTimeout(() => {
    feedbackMsg.value = ''
  }, 4000)
}

async function fetchStatus() {
  try {
    const res = await actuatorService.getStatus()
    if (res) {
      actuators.value = { ...actuators.value, ...res }
    }
  } catch (err) {
    console.warn('Failed to load actuator status:', err)
  }
}

async function toggleOverrideMode() {
  const newOverride = !actuators.value.isOverrideActive
  actuators.value.isOverrideActive = newOverride
  actuators.value.overrideMode = newOverride ? 'MANUAL' : 'AUTO'

  try {
    await actuatorService.updateStatus({
      isOverrideActive: newOverride,
      overrideMode: newOverride ? 'MANUAL' : 'AUTO'
    })
    showFeedback(
      newOverride 
        ? (props.currentLang === 'id' ? 'Mode Manual Override Aktif. Anda dapat mengontrol semua sakelar relai.' : 'Manual Override Active. Direct relay control enabled.')
        : (props.currentLang === 'id' ? 'Mode Otomatis (ON/OFF Histeresis) Aktif. Relai dikendalikan cerdas oleh ESP32.' : 'Auto Mode Active. Smart ON/OFF Hysteresis regulation enabled.')
    )
  } catch (err) {
    showFeedback(props.currentLang === 'id' ? 'Gagal mengubah mode kontrol.' : 'Failed to switch mode.', 'error')
  }
}

async function handleActuatorToggle(field, value) {
  const payload = { [field]: value }
  
  if (!actuators.value.isOverrideActive) {
    actuators.value.isOverrideActive = true
    actuators.value.overrideMode = 'MANUAL'
    payload.isOverrideActive = true
    payload.overrideMode = 'MANUAL'
  }

  try {
    await actuatorService.updateStatus(payload)
    showFeedback(props.currentLang === 'id' ? 'Perintah sakelar berhasil dikirim ke mikrokontroler.' : 'Relay switch command transmitted.')
  } catch (err) {
    showFeedback(props.currentLang === 'id' ? 'Gagal memperbarui status relai.' : 'Failed to toggle relay.', 'error')
  }
}

async function handleSpeedChange(field, val) {
  try {
    await actuatorService.updateStatus({ [field]: val })
  } catch (err) {
    console.warn('Failed to update speed/level:', err)
  }
}

onMounted(() => {
  fetchStatus()

  // Get initial telemetry temperature for LED indicators
  telemetryService.getCurrent().then((res) => {
    if (res?.telemetry?.tempInternal !== undefined) {
      currentTemp.value = Number(res.telemetry.tempInternal)
    }
  }).catch(() => {})

  // Real-time WebSocket sync
  socketService.on('actuator_update', (data) => {
    if (data) {
      actuators.value = { ...actuators.value, ...data }
    }
  })

  socketService.on('telemetry_live', (data) => {
    if (data && data.tempInternal !== undefined) {
      currentTemp.value = Number(data.tempInternal)
    }
  })
})

onUnmounted(() => {
  if (feedbackTimer) clearTimeout(feedbackTimer)
  socketService.off('actuator_update')
  socketService.off('telemetry_live')
})
</script>

<style scoped>
.actuator-control-card {
  background: var(--color-surface, #FFFFFF);
  border: 1px solid var(--color-border, #E2ECE2);
  border-radius: 18px;
  padding: 22px 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 20px rgba(7, 30, 39, 0.04);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.actuator-control-card.is-override {
  border-color: rgba(217, 119, 6, 0.4);
  box-shadow: 0 4px 24px rgba(217, 119, 6, 0.08);
}

.acc-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--color-border, #EDF5ED);
  margin-bottom: 20px;
}

.acc-title-group {
  display: flex;
  align-items: center;
  gap: 14px;
}

.acc-icon-chip {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.acc-icon-chip.chip-auto {
  background: #EAF8EB;
  color: #0D631B;
}

.acc-icon-chip.chip-manual {
  background: #FEF3C7;
  color: #D97706;
}

.acc-heading-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
  flex-wrap: wrap;
}

.acc-title {
  font-size: 17px;
  font-weight: 800;
  color: var(--color-text-main, #071E27);
  letter-spacing: -0.3px;
  margin: 0;
}

.acc-mode-pill {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.8px;
  padding: 3px 10px;
  border-radius: 20px;
}

.acc-mode-pill.mode-auto {
  background: #EAF8EB;
  color: #0D631B;
  border: 1px solid #C8E6C9;
}

.acc-mode-pill.mode-manual {
  background: #FEF3C7;
  color: #B45309;
  border: 1px solid #FDE68A;
}

.acc-subtitle {
  font-size: 13px;
  color: var(--color-text-muted, #64748B);
  margin: 0;
  line-height: 1.4;
}

/* Mode Switch UI */
.mode-switch-container {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--color-surface-subtle, #F1F5F9);
  padding: 6px 12px;
  border-radius: 30px;
  cursor: pointer;
  border: 1px solid var(--color-border, #E2E8F0);
  user-select: none;
  transition: all 0.2s ease;
}

.mode-switch-container:hover {
  background: #E2E8F0;
}

.switch-label-auto, .switch-label-manual {
  font-size: 11px;
  font-weight: 700;
  color: #94A3B8;
  letter-spacing: 0.5px;
  transition: color 0.2s ease;
}

.switch-label-auto.active {
  color: #0D631B;
}

.switch-label-manual.active {
  color: #D97706;
}

.mode-slider {
  width: 38px;
  height: 20px;
  background: #10B981;
  border-radius: 20px;
  position: relative;
  transition: background 0.3s ease;
}

.mode-slider.slider-manual {
  background: #F59E0B;
}

.mode-slider-thumb {
  width: 16px;
  height: 16px;
  background: #FFFFFF;
  border-radius: 50%;
  position: absolute;
  top: 2px;
  left: 2px;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.mode-slider.slider-manual .mode-slider-thumb {
  transform: translateX(18px);
}

/* Feedback Bar */
.acc-feedback-bar {
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 600;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
}

.acc-feedback-bar.success {
  background: #F0FDF4;
  color: #166534;
  border: 1px solid #DCFCE7;
}

.acc-feedback-bar.error {
  background: #FEF2F2;
  color: #991B1B;
  border: 1px solid #FEE2E2;
}

/* Actuators Grid */
.actuators-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  gap: 16px;
}

.actuator-item-card {
  background: var(--color-surface-subtle, #F8FAFC);
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: 14px;
  padding: 16px 18px;
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.actuator-item-card.is-active {
  background: #F4FAF5;
  border-color: #A7F3D0;
  box-shadow: 0 4px 14px rgba(13, 99, 27, 0.05);
}

.actuator-item-card.is-active.is-heater {
  background: #FFFBEB;
  border-color: #FDE68A;
}

.actuator-item-card.is-active.is-uv {
  background: #FAF5FF;
  border-color: #E9D5FF;
}

.aic-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}

.aic-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748B;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.actuator-item-card.is-active .aic-icon-box {
  background: #0D631B;
  color: #FFFFFF;
  border-color: #0D631B;
}

.actuator-item-card.is-active.is-heater .aic-icon-box {
  background: #D97706;
  color: #FFFFFF;
  border-color: #D97706;
}

.actuator-item-card.is-active.is-uv .aic-icon-box {
  background: #7C3AED;
  color: #FFFFFF;
  border-color: #7C3AED;
}

.aic-meta {
  flex: 1;
}

.aic-name {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-main, #0F172A);
  margin: 0 0 2px 0;
}

.aic-pin {
  font-size: 11px;
  color: var(--color-text-muted, #94A3B8);
  font-weight: 500;
}

/* Toggle Switch UI */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
  flex-shrink: 0;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #CBD5E1;
  transition: .3s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s cubic-bezier(0.16, 1, 0.3, 1);
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.toggle-switch input:checked + .toggle-slider {
  background-color: #10B981;
}

.toggle-switch input:checked + .toggle-slider.slider-orange {
  background-color: #F59E0B;
}

.toggle-switch input:checked + .toggle-slider.slider-purple {
  background-color: #8B5CF6;
}

.toggle-switch input:checked + .toggle-slider:before {
  transform: translateX(20px);
}

/* Slider Controls */
.aic-slider-block {
  margin-top: 6px;
  padding-top: 10px;
  border-top: 1px dashed rgba(0,0,0,0.06);
}

.aic-slider-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
  font-size: 12px;
}

.slider-caption {
  color: var(--color-text-muted, #64748B);
}

.slider-val {
  color: #0D631B;
  font-weight: 700;
}

.slider-val.text-orange {
  color: #D97706;
}

.aic-range-slider {
  width: 100%;
  height: 6px;
  border-radius: 3px;
  outline: none;
  accent-color: #0D631B;
  cursor: pointer;
}

.slider-orange-track {
  accent-color: #D97706;
}

/* Status Footers */
.aic-status-footer {
  margin-top: 6px;
  padding-top: 10px;
  border-top: 1px dashed rgba(0,0,0,0.06);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.status-tag {
  font-size: 10px;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 6px;
  letter-spacing: 0.5px;
}

.status-tag.tag-on {
  background: #EAF8EB;
  color: #0D631B;
}

.status-tag.tag-orange {
  background: #FEF3C7;
  color: #B45309;
}

.status-tag.tag-purple {
  background: #F3E8FF;
  color: #7E22CE;
}

.status-tag.tag-off {
  background: #E2E8F0;
  color: #64748B;
}

.status-desc {
  font-size: 11px;
  color: var(--color-text-muted, #94A3B8);
  text-align: right;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Animations */
@keyframes spin-cw {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.spinning svg {
  animation: spin-cw 2s linear infinite;
}

@keyframes pulse-heat {
  0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px #F59E0B); }
  50% { transform: scale(1.08); filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.6)); }
}

.heat-pulse svg {
  animation: pulse-heat 1.5s ease-in-out infinite;
}

@keyframes uv-glow {
  0%, 100% { filter: drop-shadow(0 0 2px rgba(139, 92, 246, 0.4)); }
  50% { filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.8)); }
}

.uv-glow svg {
  animation: uv-glow 2s ease-in-out infinite;
}

/* Panel LED Indicator Widget */
.panel-led-indicator-bar {
  margin-top: 22px;
  padding: 16px 20px;
  background: var(--color-bg-light, #F8FAFC);
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.pli-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.pli-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  flex-shrink: 0;
}

.pli-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title, #0F172A);
  margin: 0 0 2px 0;
}

.pli-sub {
  font-size: 11.5px;
  color: var(--color-text-muted, #64748B);
  margin: 0;
}

.pli-led-group {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.pli-led-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 14px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 10px;
  transition: all 0.25s ease;
}

.led-bulb {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #CBD5E1;
  border: 1.5px solid rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.led-text-box {
  display: flex;
  flex-direction: column;
}

.led-color-title {
  font-size: 10.5px;
  font-weight: 800;
  color: #64748B;
  letter-spacing: 0.5px;
}

.led-condition {
  font-size: 11.5px;
  font-weight: 700;
  color: #334155;
}

/* Green Lit */
.pli-led-item.is-lit-green {
  background: #F0FDF4;
  border-color: #86EFAC;
  box-shadow: 0 0 12px rgba(34, 197, 94, 0.2);
}

.pli-led-item.is-lit-green .led-bulb {
  background: #22C55E;
  box-shadow: 0 0 10px #22C55E;
  border-color: #16A34A;
}

.pli-led-item.is-lit-green .led-color-title {
  color: #166534;
}

.pli-led-item.is-lit-green .led-condition {
  color: #15803D;
}

/* Yellow Lit */
.pli-led-item.is-lit-yellow {
  background: #FFFBEB;
  border-color: #FCD34D;
  box-shadow: 0 0 12px rgba(245, 158, 11, 0.25);
}

.pli-led-item.is-lit-yellow .led-bulb {
  background: #F59E0B;
  box-shadow: 0 0 10px #F59E0B;
  border-color: #D97706;
}

.pli-led-item.is-lit-yellow .led-color-title {
  color: #92400E;
}

.pli-led-item.is-lit-yellow .led-condition {
  color: #B45309;
}

/* Red Lit */
.pli-led-item.is-lit-red {
  background: #FEF2F2;
  border-color: #FCA5A5;
  box-shadow: 0 0 14px rgba(239, 68, 68, 0.35);
  animation: pulse-red-widget 1.5s infinite;
}

.pli-led-item.is-lit-red .led-bulb {
  background: #EF4444;
  box-shadow: 0 0 10px #EF4444;
  border-color: #DC2626;
}

.pli-led-item.is-lit-red .led-color-title {
  color: #991B1B;
}

.pli-led-item.is-lit-red .led-condition {
  color: #DC2626;
}

@keyframes pulse-red-widget {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

@media (max-width: 640px) {
  .actuator-control-card {
    padding: 16px 14px;
    border-radius: 14px;
  }
  
  .acc-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .acc-mode-switch-wrap {
    width: 100%;
    display: flex;
    justify-content: flex-end;
  }

  .actuators-grid {
    grid-template-columns: 1fr;
  }

  .panel-led-indicator-bar {
    flex-direction: column;
    align-items: flex-start;
  }

  .pli-led-group {
    width: 100%;
  }

  .pli-led-item {
    flex: 1;
    min-width: 120px;
  }
}
</style>
