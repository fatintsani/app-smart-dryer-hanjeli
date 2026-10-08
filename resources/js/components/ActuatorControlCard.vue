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
              {{ actuators.isOverrideActive ? (currentLang === 'id' ? 'MANUAL OVERRIDE' : 'MANUAL OVERRIDE') : (currentLang === 'id' ? 'OTOMATIS (AI PID)' : 'AUTO (AI PID)') }}
            </span>
          </div>
          <p class="acc-subtitle">
            {{ actuators.isOverrideActive 
              ? (currentLang === 'id' ? 'Kontrol manual aktif: Anda memiliki kendali langsung atas sakelar relai daya.' : 'Manual control active: You have direct control over power relays.') 
              : (currentLang === 'id' ? 'Regulasi otomatis aktif berdasarkan target suhu dan kelembapan ruang pengering.' : 'Automatic regulation active based on target temperature and humidity.') 
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
      <!-- 1. Kipas Exhaust (Blower Pembuangan Lembap) -->
      <div class="actuator-item-card" :class="{ 'is-active': actuators.exhaustFanStatus }">
        <div class="aic-top-row">
          <div class="aic-icon-box" :class="{ 'spinning': actuators.exhaustFanStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 12c0-3 2.5-5.5 5.5-5.5S23 9 23 12s-2.5 5.5-5.5 5.5S12 15 12 12z"></path>
              <path d="M12 12c-3 0-5.5-2.5-5.5-5.5S9 1 12 1s5.5 2.5 5.5 5.5S15 12 12 12z"></path>
              <path d="M12 12c0 3-2.5 5.5-5.5 5.5S1 15 1 12s2.5-5.5 5.5-5.5S12 9 12 12z"></path>
              <path d="M12 12c3 0 5.5 2.5 5.5 5.5S15 23 12 23s-5.5-2.5-5.5-5.5S9 12 12 12z"></path>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Kipas Exhaust' : 'Exhaust Fan' }}</h4>
            <span class="aic-pin">Pin GPIO 18 &bull; Relai 1</span>
          </div>
          <!-- Toggle Switch -->
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.exhaustFanStatus" 
              @change="handleActuatorToggle('exhaustFanStatus', actuators.exhaustFanStatus)"
            />
            <span class="toggle-slider"></span>
          </label>
        </div>

        <!-- Slider Speed Control -->
        <div class="aic-slider-block">
          <div class="aic-slider-header">
            <span class="slider-caption">{{ currentLang === 'id' ? 'Kecepatan Blower' : 'Blower Speed' }}</span>
            <strong class="slider-val">{{ actuators.exhaustFanStatus ? `${actuators.exhaustFanSpeed}%` : '0%' }}</strong>
          </div>
          <input 
            type="range" 
            min="10" 
            max="100" 
            step="5" 
            v-model.number="actuators.exhaustFanSpeed" 
            :disabled="!actuators.exhaustFanStatus"
            @change="handleSpeedChange('exhaustFanSpeed', actuators.exhaustFanSpeed)"
            class="aic-range-slider"
          />
        </div>
      </div>

      <!-- 2. Kipas Intake (Pemasok Udara Bersih) -->
      <div class="actuator-item-card" :class="{ 'is-active': actuators.intakeFanStatus }">
        <div class="aic-top-row">
          <div class="aic-icon-box" :class="{ 'spinning': actuators.intakeFanStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"></path>
              <path d="M9.6 4.6A2 2 0 1 1 11 8H2"></path>
              <path d="M12.6 19.4A2 2 0 1 0 14 16H2"></path>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Kipas Intake Udara' : 'Intake Fresh Air' }}</h4>
            <span class="aic-pin">Pin GPIO 19 &bull; Relai 2</span>
          </div>
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.intakeFanStatus" 
              @change="handleActuatorToggle('intakeFanStatus', actuators.intakeFanStatus)"
            />
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag" :class="actuators.intakeFanStatus ? 'tag-on' : 'tag-off'">
            {{ actuators.intakeFanStatus ? (currentLang === 'id' ? 'MENYALA (AKTIF)' : 'RUNNING (ON)') : (currentLang === 'id' ? 'MATI (OFF)' : 'STOPPED (OFF)') }}
          </span>
          <span class="status-desc">{{ currentLang === 'id' ? 'Menarik udara kering dari luar' : 'Drawing dry ambient air' }}</span>
        </div>
      </div>

      <!-- 3. Kipas Sirkulasi Internal -->
      <div class="actuator-item-card" :class="{ 'is-active': actuators.circFanStatus }">
        <div class="aic-top-row">
          <div class="aic-icon-box" :class="{ 'spinning': actuators.circFanStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"></path>
              <line x1="12" y1="2" x2="12" y2="4"></line>
              <line x1="12" y1="20" x2="12" y2="22"></line>
              <line x1="2" y1="12" x2="4" y2="12"></line>
              <line x1="20" y1="12" x2="22" y2="12"></line>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Kipas Sirkulasi 360°' : 'Circulation Fan' }}</h4>
            <span class="aic-pin">Pin GPIO 21 &bull; Relai 3</span>
          </div>
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.circFanStatus" 
              @change="handleActuatorToggle('circFanStatus', actuators.circFanStatus)"
            />
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag" :class="actuators.circFanStatus ? 'tag-on' : 'tag-off'">
            {{ actuators.circFanStatus ? (currentLang === 'id' ? 'MENYALA (AKTIF)' : 'RUNNING (ON)') : (currentLang === 'id' ? 'MATI (OFF)' : 'STOPPED (OFF)') }}
          </span>
          <span class="status-desc">{{ currentLang === 'id' ? 'Pemerataan panas ruang gabah' : 'Even heat distribution' }}</span>
        </div>
      </div>

      <!-- 4. Pemanas Bantu Listrik (Aux Heater) -->
      <div class="actuator-item-card" :class="{ 'is-active': actuators.auxHeaterStatus, 'is-heater': true }">
        <div class="aic-top-row">
          <div class="aic-icon-box heater-icon" :class="{ 'heat-pulse': actuators.auxHeaterStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 2v20"></path>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Pemanas Bantu (Heater)' : 'Auxiliary Heater' }}</h4>
            <span class="aic-pin">Pin GPIO 22 &bull; Relai 4 (SSR)</span>
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

        <!-- Heat Level Slider -->
        <div class="aic-slider-block">
          <div class="aic-slider-header">
            <span class="slider-caption">{{ currentLang === 'id' ? 'Level Pemanasan' : 'Heating Level' }}</span>
            <strong class="slider-val text-orange">{{ actuators.auxHeaterStatus ? `${actuators.auxHeaterLevel}% (~${(actuators.auxHeaterLevel * 0.02 + 0.5).toFixed(1)} kW)` : '0 kW (OFF)' }}</strong>
          </div>
          <input 
            type="range" 
            min="10" 
            max="100" 
            step="5" 
            v-model.number="actuators.auxHeaterLevel" 
            :disabled="!actuators.auxHeaterStatus"
            @change="handleSpeedChange('auxHeaterLevel', actuators.auxHeaterLevel)"
            class="aic-range-slider slider-orange-track"
          />
        </div>
      </div>

      <!-- 5. Lampu Sanitasi UV-C -->
      <div class="actuator-item-card" :class="{ 'is-active': actuators.uvLightStatus, 'is-uv': true }">
        <div class="aic-top-row">
          <div class="aic-icon-box uv-icon" :class="{ 'uv-glow': actuators.uvLightStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 2a6 6 0 0 0-6 6v3a6 6 0 0 0 12 0V8a6 6 0 0 0-6-6z"></path>
              <line x1="12" y1="17" x2="12" y2="22"></line>
              <line x1="9" y1="22" x2="15" y2="22"></line>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Lampu UV-C Sanitasi' : 'UV-C Sterilizer' }}</h4>
            <span class="aic-pin">Pin GPIO 23 &bull; Relai 5</span>
          </div>
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.uvLightStatus" 
              @change="handleActuatorToggle('uvLightStatus', actuators.uvLightStatus)"
            />
            <span class="toggle-slider slider-purple"></span>
          </label>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag" :class="actuators.uvLightStatus ? 'tag-purple' : 'tag-off'">
            {{ actuators.uvLightStatus ? (currentLang === 'id' ? 'STERILISASI AKTIF' : 'UV ON') : (currentLang === 'id' ? 'MATI (OFF)' : 'OFF') }}
          </span>
          <span class="status-desc">{{ currentLang === 'id' ? 'Pencegahan jamur & spora gabah' : 'Anti-mold fungal protection' }}</span>
        </div>
      </div>

      <!-- 6. Motor Pemutar Rak Tray -->
      <div class="actuator-item-card" :class="{ 'is-active': actuators.rotaryTrayStatus }">
        <div class="aic-top-row">
          <div class="aic-icon-box" :class="{ 'spinning': actuators.rotaryTrayStatus }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
            </svg>
          </div>
          <div class="aic-meta">
            <h4 class="aic-name">{{ currentLang === 'id' ? 'Motor Pemutar Rak' : 'Rotary Tray Motor' }}</h4>
            <span class="aic-pin">Pin GPIO 25 &bull; Relai 6</span>
          </div>
          <label class="toggle-switch">
            <input 
              type="checkbox" 
              v-model="actuators.rotaryTrayStatus" 
              @change="handleActuatorToggle('rotaryTrayStatus', actuators.rotaryTrayStatus)"
            />
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="aic-status-footer">
          <span class="status-tag" :class="actuators.rotaryTrayStatus ? 'tag-on' : 'tag-off'">
            {{ actuators.rotaryTrayStatus ? (currentLang === 'id' ? 'BERPUTAR (ROTATING)' : 'ROTATING') : (currentLang === 'id' ? 'DIAM (STANDBY)' : 'IDLE') }}
          </span>
          <span class="status-desc">{{ currentLang === 'id' ? 'Rotasi lapisan hanjeli merata' : 'Even layer sun exposure' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { actuatorService } from '../services/actuatorService'
import { socketService } from '../services/socketService'

const props = defineProps({
  currentLang: {
    type: String,
    default: 'id'
  }
})

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
        : (props.currentLang === 'id' ? 'Mode Otomatis (AI PID) Aktif. Relai diatur cerdas oleh sistem.' : 'Auto Mode Active. Smart AI PID regulation enabled.')
    )
  } catch (err) {
    showFeedback(props.currentLang === 'id' ? 'Gagal mengubah mode kontrol.' : 'Failed to switch mode.', 'error')
  }
}

async function handleActuatorToggle(field, value) {
  try {
    const payload = { [field]: value }
    // If operator toggles a switch directly, activate override so hardware respects the manual input
    if (!actuators.value.isOverrideActive) {
      actuators.value.isOverrideActive = true
      actuators.value.overrideMode = 'MANUAL'
      payload.isOverrideActive = true
      payload.overrideMode = 'MANUAL'
    }
    
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

  // Real-time WebSocket sync
  socketService.on('actuator_update', (data) => {
    if (data) {
      actuators.value = { ...actuators.value, ...data }
    }
  })
})

onUnmounted(() => {
  if (feedbackTimer) clearTimeout(feedbackTimer)
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
}
</style>
