<template>
  <div class="actuator-control-card" :class="{ 'is-override': actuators.isOverrideActive }">
    
    <!-- Header: Judul & Sakelar Mode Otomatis vs Manual -->
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
            <h3 class="acc-title">Kendali Sakelar Alat & Pemanas</h3>
            <span class="acc-mode-pill" :class="actuators.isOverrideActive ? 'mode-manual' : 'mode-auto'">
              {{ actuators.isOverrideActive ? 'MODE MANUAL' : 'MODE OTOMATIS (REKOMENDASI)' }}
            </span>
          </div>
          <p class="acc-subtitle">
            {{ actuators.isOverrideActive 
              ? 'Anda sedang mengendalikan alat secara manual.' 
              : 'Sistem otomatis mengatur pemanas sesuai suhu ideal (48°C - 50°C) dan kondisi cuaca.' 
            }}
          </p>
        </div>
      </div>

      <!-- Sakelar Mode Auto / Manual -->
      <div class="acc-mode-toggle-box">
        <button 
          type="button" 
          class="mode-btn" 
          :class="{ active: !actuators.isOverrideActive }"
          @click="setHeaterAuto()"
        >
          Otomatis
        </button>
        <button 
          type="button" 
          class="mode-btn" 
          :class="{ active: actuators.isOverrideActive }"
          @click="toggleOverrideMode()"
        >
          Manual
        </button>
      </div>
    </div>

    <!-- Tombol Kendali Cepat Pemanas -->
    <div class="quick-heater-bar">
      <div class="qh-status-info">
        <span class="qh-label">Kondisi Pemanas:</span>
        <strong class="qh-val" :class="isHardwareActive && actuators.auxHeaterStatus ? 'text-amber' : (isHardwareActive ? 'text-green' : 'text-neutral')">
          {{ isHardwareActive ? (actuators.auxHeaterStatus ? 'MENYALA (MEMANASKAN)' : 'MATI (STANDBY)') : 'ALAT BELUM TERHUBUNG' }}
        </strong>
      </div>

      <div class="qh-buttons">
        <button 
          type="button"
          class="btn-qh btn-qh-on"
          :class="{ active: actuators.isOverrideActive && actuators.auxHeaterStatus }"
          :disabled="isLedRedActive"
          @click="setHeaterManual(true)"
          title="Nyalakan pemanas secara manual"
        >
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polyline>
          </svg>
          <span>Nyalakan Pemanas</span>
        </button>

        <button 
          type="button"
          class="btn-qh btn-qh-off"
          :class="{ active: actuators.isOverrideActive && !actuators.auxHeaterStatus }"
          @click="setHeaterManual(false)"
          title="Matikan pemanas secara manual"
        >
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
          <span>Matikan Pemanas</span>
        </button>

        <button 
          type="button"
          class="btn-qh btn-qh-auto"
          :class="{ active: !actuators.isOverrideActive }"
          @click="setHeaterAuto()"
          title="Kembalikan ke pengaturan otomatis"
        >
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span>Atur Otomatis</span>
        </button>
      </div>
    </div>

    <!-- Feedback Message Toast Mini -->
    <Transition name="fade">
      <div v-if="feedbackMsg" class="acc-feedback-toast" :class="feedbackType">
        <span>{{ feedbackMsg }}</span>
      </div>
    </Transition>

    <!-- Kartu Sakelar Alat Satuan -->
    <div class="actuator-cards-grid">
      
      <!-- 1. Pemanas Listrik Tambahan -->
      <div class="actuator-item">
        <div class="ai-left">
          <div class="ai-icon" :class="actuators.auxHeaterStatus ? 'icon-amber' : 'icon-neutral'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
          </div>
          <div>
            <h4 class="ai-name">Pemanas Ruangan (Heater)</h4>
            <p class="ai-desc">Menjaga panas ruang pengering tetap di 48°C - 50°C saat cuaca mendung/malam.</p>
          </div>
        </div>
        <label class="switch-toggle">
          <input 
            type="checkbox" 
            v-model="actuators.auxHeaterStatus" 
            :disabled="isLedRedActive && !actuators.auxHeaterStatus"
            @change="handleActuatorToggle('auxHeaterStatus', actuators.auxHeaterStatus)"
          />
          <span class="slider"></span>
        </label>
      </div>

      <!-- 2. Kipas Pembuang Udara Lembap (Exhaust Fan) -->
      <div class="actuator-item">
        <div class="ai-left">
          <div class="ai-icon" :class="actuators.exhaustFanStatus ? 'icon-blue' : 'icon-neutral'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 12c2.5-3 6-3 6-3s0 3.5-3 6c-3 2.5-3 6-3 6s-3.5 0-6-3c-2.5-3-6-3-6-3s0-3.5 3-6c3-2.5 3-6 3-6s3.5 0 6 3z"></path>
            </svg>
          </div>
          <div>
            <h4 class="ai-name">Kipas Pembuang Udara (Exhaust)</h4>
            <p class="ai-desc">Membuang udara lembap dari dalam ruangan agar proses pengeringan lebih cepat.</p>
          </div>
        </div>
        <label class="switch-toggle">
          <input 
            type="checkbox" 
            v-model="actuators.exhaustFanStatus" 
            @change="handleActuatorToggle('exhaustFanStatus', actuators.exhaustFanStatus)"
          />
          <span class="slider"></span>
        </label>
      </div>

    </div>

    <!-- Status Lampu Indikator di Kotak Alat Fisik -->
    <div class="panel-led-bar">
      <div class="led-title-box">
        <strong>Lampu Indikator Kotak Alat:</strong>
        <span>Sesuai dengan warna lampu di panel fisik alat</span>
      </div>

      <div class="led-items-row">
        <div class="led-item" :class="{ active: isLedGreenActive }">
          <span class="led-bulb bulb-green"></span>
          <span>Hijau: Normal / Siaga</span>
        </div>
        <div class="led-item" :class="{ active: isLedYellowActive }">
          <span class="led-bulb bulb-yellow"></span>
          <span>Kuning: Sedang Memanaskan</span>
        </div>
        <div class="led-item" :class="{ active: isLedRedActive }">
          <span class="led-bulb bulb-red"></span>
          <span>Merah: Panas Maksimal (≥50°C)</span>
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

defineProps({
  currentLang: {
    type: String,
    default: 'id'
  }
})

const currentTemp = ref(32.0)
const isLiveTelemetry = ref(false)

const isHardwareActive = computed(() => {
  return Boolean(connectionManager.state.isHardwareActive || isLiveTelemetry.value)
})

const actuators = ref({
  exhaustFanStatus: true,
  auxHeaterStatus: false,
  isOverrideActive: false,
  overrideMode: 'AUTO'
})

const isLedRedActive = computed(() => {
  return isHardwareActive.value && currentTemp.value >= 50.0
})

const isLedYellowActive = computed(() => {
  return isHardwareActive.value && Boolean(actuators.value.auxHeaterStatus) && !isLedRedActive.value
})

const isLedGreenActive = computed(() => {
  return isHardwareActive.value && !actuators.value.auxHeaterStatus && !isLedRedActive.value
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
  }, 3500)
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
        ? 'Mode Manual Aktif. Anda dapat mengontrol pemanas dan kipas.' 
        : 'Mode Otomatis Aktif. Pemanas dikendalikan otomatis oleh sistem.'
    )
  } catch (err) {
    showFeedback('Gagal mengubah mode kendali.', 'error')
  }
}

async function handleActuatorToggle(field, value) {
  if (!isHardwareActive.value) {
    showFeedback('Alat belum terhubung. Nyalakan ESP32 terlebih dahulu.', 'error')
    return
  }

  const payload = { [field]: value }
  
  if (!actuators.value.isOverrideActive) {
    actuators.value.isOverrideActive = true
    actuators.value.overrideMode = 'MANUAL'
    payload.isOverrideActive = true
    payload.overrideMode = 'MANUAL'
  }

  try {
    await actuatorService.updateStatus(payload)
    showFeedback('Perintah sakelar berhasil dikirim ke alat.')
  } catch (err) {
    showFeedback('Gagal memperbarui status sakelar.', 'error')
  }
}

async function setHeaterManual(state) {
  if (!isHardwareActive.value) {
    showFeedback('Alat belum terhubung. Nyalakan ESP32 terlebih dahulu.', 'error')
    return
  }

  if (state && isLedRedActive.value) {
    showFeedback('Pemanas tidak dapat dinyalakan: Suhu ruangan sudah mencapai batas aman (≥50°C).', 'error')
    return
  }
  actuators.value.isOverrideActive = true
  actuators.value.overrideMode = 'MANUAL'
  actuators.value.auxHeaterStatus = state

  try {
    await actuatorService.updateStatus({
      auxHeaterStatus: state,
      isOverrideActive: true,
      overrideMode: 'MANUAL'
    })
    showFeedback(state ? 'Pemanas berhasil dinyalakan (ON).' : 'Pemanas berhasil dimatikan (OFF).')
  } catch (err) {
    showFeedback('Gagal mengirim perintah pemanas.', 'error')
  }
}

async function setHeaterAuto() {
  actuators.value.isOverrideActive = false
  actuators.value.overrideMode = 'AUTO'

  try {
    await actuatorService.updateStatus({
      isOverrideActive: false,
      overrideMode: 'AUTO'
    })
    showFeedback('Sistem kembali ke Mode Otomatis.')
  } catch (err) {
    showFeedback('Gagal beralih ke Mode Otomatis.', 'error')
  }
}

onMounted(() => {
  fetchStatus()

  telemetryService.getCurrent().then((res) => {
    if (res?.telemetry) {
      if (res.telemetry.tempInternal !== undefined) {
        currentTemp.value = Number(res.telemetry.tempInternal)
      }
      if (res.telemetry.isLive !== undefined) {
        isLiveTelemetry.value = Boolean(res.telemetry.isLive)
      }
    }
  }).catch(() => {})

  socketService.on('actuator_update', (data) => {
    if (data) actuators.value = { ...actuators.value, ...data }
  })

  socketService.on('telemetry_live', (data) => {
    if (data) {
      if (data.tempInternal !== undefined) {
        currentTemp.value = Number(data.tempInternal)
      }
      if (data.isLive !== undefined) {
        isLiveTelemetry.value = Boolean(data.isLive)
      }
    }
  })

  connectionManager.on('telemetry_live', (data) => {
    if (data) {
      if (data.tempInternal !== undefined) {
        currentTemp.value = Number(data.tempInternal)
      }
      if (data.isLive !== undefined) {
        isLiveTelemetry.value = Boolean(data.isLive)
      }
    }
  })
})

onUnmounted(() => {
  if (feedbackTimer) clearTimeout(feedbackTimer)
  socketService.off('actuator_update')
  socketService.off('telemetry_live')
  connectionManager.off('telemetry_live')
})
</script>

<style scoped>
.actuator-control-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 22px 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.actuator-control-card.is-override {
  border-color: #FDE68A;
}

/* Header */
.acc-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
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
}

.chip-auto {
  background: #E8F5E9;
  color: #1B5E20;
}

.chip-manual {
  background: #FEF3C7;
  color: #B45309;
}

.acc-heading-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 2px;
}

.acc-title {
  font-size: 17px;
  font-weight: 700;
  color: #0F291E;
  margin: 0;
}

.acc-mode-pill {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
}

.mode-auto {
  background: #DCFCE7;
  color: #166534;
}

.mode-manual {
  background: #FEF3C7;
  color: #92400E;
}

.acc-subtitle {
  font-size: 13px;
  color: #64748B;
  margin: 0;
}

.acc-mode-toggle-box {
  display: flex;
  background: #F1F5F9;
  padding: 4px;
  border-radius: 10px;
  gap: 4px;
}

.mode-btn {
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  background: transparent;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
}

.mode-btn.active {
  background: #FFFFFF;
  color: #0F291E;
  font-weight: 700;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

/* Quick Heater Bar */
.quick-heater-bar {
  background: #F8FAFC;
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 18px;
}

.qh-status-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.qh-label {
  color: #64748B;
}

.qh-val {
  font-weight: 700;
}

.text-green { color: #1B5E20; }
.text-amber { color: #D97706; }

.qh-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.btn-qh {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid #CBD5E1;
  background: #FFFFFF;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-qh-on:hover:not(:disabled), .btn-qh-on.active {
  background: #FEF3C7;
  border-color: #D97706;
  color: #92400E;
}

.btn-qh-off:hover, .btn-qh-off.active {
  background: #F1F5F9;
  border-color: #64748B;
  color: #334155;
}

.btn-qh-auto:hover, .btn-qh-auto.active {
  background: #E8F5E9;
  border-color: #1B5E20;
  color: #1B5E20;
}

.btn-qh:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Toast */
.acc-feedback-toast {
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 16px;
}

.acc-feedback-toast.success {
  background: #F0FDF4;
  color: #166534;
  border: 1px solid #BBF7D0;
}

.acc-feedback-toast.error {
  background: #FEF2F2;
  color: #991B1B;
  border: 1px solid #FECACA;
}

/* Actuator Items */
.actuator-cards-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 20px;
}

@media (max-width: 768px) {
  .actuator-cards-grid {
    grid-template-columns: 1fr;
  }
}

.actuator-item {
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.ai-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ai-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-amber { background: #FEF3C7; color: #D97706; }
.icon-blue { background: #E0F2FE; color: #0284C7; }
.icon-neutral { background: #F1F5F9; color: #64748B; }

.ai-name {
  font-size: 14px;
  font-weight: 700;
  color: #0F291E;
  margin-bottom: 2px;
}

.ai-desc {
  font-size: 12px;
  color: #64748B;
  margin: 0;
  line-height: 1.35;
}

/* Toggle Switch */
.switch-toggle {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
  flex-shrink: 0;
}

.switch-toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #CBD5E1;
  transition: .3s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #1B5E20;
}

input:checked + .slider:before {
  transform: translateX(20px);
}

/* Panel LED Bar */
.panel-led-bar {
  background: #F8FAFC;
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.led-title-box strong {
  display: block;
  font-size: 13px;
  color: #334155;
}

.led-title-box span {
  font-size: 12px;
  color: #64748B;
}

.led-items-row {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.led-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #94A3B8;
  font-weight: 500;
}

.led-item.active {
  color: #0F291E;
  font-weight: 700;
}

.led-bulb {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #CBD5E1;
}

.led-item.active .bulb-green {
  background: #22C55E;
  box-shadow: 0 0 8px #22C55E;
}

.led-item.active .bulb-yellow {
  background: #EAB308;
  box-shadow: 0 0 8px #EAB308;
}

.led-item.active .bulb-red {
  background: #EF4444;
  box-shadow: 0 0 8px #EF4444;
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR ACTUATOR CONTROL CARD
   ========================================================================== */
:global(.dark-theme) .actuator-control-card {
  background: #0B242F;
  border-color: #1E4E61;
}

:global(.dark-theme) .actuator-control-card.is-override {
  border-color: rgba(245, 158, 11, 0.4);
}

:global(.dark-theme) .acc-title,
:global(.dark-theme) .ai-name {
  color: #F8FAFC;
}

:global(.dark-theme) .acc-subtitle,
:global(.dark-theme) .qh-label,
:global(.dark-theme) .ai-desc {
  color: #94A3B8;
}

:global(.dark-theme) .acc-mode-toggle-box {
  background: #071E27;
}

:global(.dark-theme) .mode-btn {
  color: #94A3B8;
}

:global(.dark-theme) .mode-btn.active {
  background: #113847;
  color: #F8FAFC;
}

:global(.dark-theme) .quick-heater-bar {
  background: #071E27;
  border: 1px solid #1E4E61;
}

:global(.dark-theme) .btn-qh {
  background: #113847;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .btn-qh-on:hover:not(:disabled),
:global(.dark-theme) .btn-qh-on.active {
  background: rgba(217, 119, 6, 0.25);
  border-color: #D97706;
  color: #FBBF24;
}

:global(.dark-theme) .btn-qh-off:hover,
:global(.dark-theme) .btn-qh-off.active {
  background: #164657;
  border-color: #2D6B82;
  color: #F8FAFC;
}

:global(.dark-theme) .btn-qh-auto:hover,
:global(.dark-theme) .btn-qh-auto.active {
  background: rgba(27, 94, 32, 0.25);
  border-color: #1B5E20;
  color: #4ADE80;
}

:global(.dark-theme) .actuator-item {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .icon-amber { background: rgba(217, 119, 6, 0.18); color: #FBBF24; }
:global(.dark-theme) .icon-blue { background: rgba(2, 132, 199, 0.18); color: #38BDF8; }
:global(.dark-theme) .icon-neutral { background: #113847; color: #94A3B8; }

:global(.dark-theme) .mode-auto {
  background: rgba(27, 94, 32, 0.25);
  color: #4ADE80;
}

:global(.dark-theme) .mode-manual {
  background: rgba(217, 119, 6, 0.25);
  color: #FBBF24;
}

:global(.dark-theme) .led-item.active {
  color: #F8FAFC;
}
</style>
