<template>
  <div class="admin-page">
    <div class="admin-content">
      <!-- Top Title & Action Bar -->
      <div class="header-action-row">
        <div class="title-group">
          <h1 class="page-title">Simulator & Uji Operasional IoT</h1>
          <p class="page-subtitle">Pusat kendali mode simulasi perangkat, live hardware ingestion, lingkungan server, dan generator telemetri sensor.</p>
        </div>

        <div class="header-actions">
          <button 
            class="btn-primary" 
            :class="{ 'btn-running': simulatorService.isRunning?.value }"
            @click="toggleSimulatorRunning"
          >
            <svg v-if="!simulatorService.isRunning?.value" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <rect x="6" y="4" width="4" height="16"></rect>
              <rect x="14" y="4" width="4" height="16"></rect>
            </svg>
            <span>{{ simulatorService.isRunning?.value ? 'Hentikan Generator IoT' : 'Jalankan Generator IoT' }}</span>
          </button>
        </div>
      </div>

      <!-- Alert / Feedback Banner -->
      <div v-if="adminAlert.text" class="admin-feedback-toast" :class="adminAlert.type">
        <svg v-if="adminAlert.type === 'success'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>{{ adminAlert.text }}</span>
        <button class="close-toast-btn" @click="adminAlert.text = ''">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- SECTION 1: MASTER SYSTEM OPERATIONAL & ENVIRONMENT CONTROLS -->
      <div class="content-card master-controls-card">
        <div class="card-header-flex">
          <div>
            <h2 class="card-section-heading">Mode Sumber Data & Lingkungan Sistem</h2>
            <p class="card-section-sub">Konfigurasi sumber telemetri data sensor (Simulasi vs Live ESP32), status daya, dan environment server.</p>
          </div>
          <div class="system-power-badge" :class="systemState.isSystemActive ? 'power-active' : 'power-standby'">
            <span class="power-dot"></span>
            <span>{{ systemState.isSystemActive ? 'SISTEM ONLINE' : 'SISTEM STANDBY' }}</span>
          </div>
        </div>

        <div class="mode-control-grid">
          <!-- 1. System Operational Power State -->
          <div class="control-block">
            <div class="block-header">
              <div class="block-icon" :class="systemState.isSystemActive ? 'bg-green' : 'bg-red'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                  <line x1="12" y1="2" x2="12" y2="12"></line>
                </svg>
              </div>
              <div>
                <strong class="block-title">Status Daya & Sistem</strong>
                <p class="block-desc">Aktifkan atau nonaktifkan operasional Smart Greenhouse</p>
              </div>
            </div>
            <div class="block-action">
              <button 
                class="btn-mode-toggle"
                :class="systemState.isSystemActive ? 'btn-active-state' : 'btn-inactive-state'"
                @click="toggleSystemActiveState"
              >
                <span class="toggle-circle"></span>
                <span>{{ systemState.isSystemActive ? 'Sistem Aktif (Operasional)' : 'Sistem Dinonaktifkan (Standby)' }}</span>
              </button>
            </div>
          </div>

          <!-- 2. IoT Data Source Mode -->
          <div class="control-block">
            <div class="block-header">
              <div class="block-icon" :class="systemState.iotMode === 'SIMULATION' ? 'bg-amber' : 'bg-green'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
              </div>
              <div>
                <strong class="block-title">Sumber Data Sensor IoT</strong>
                <p class="block-desc">Beralih antara Mode Simulasi IoT dan Mode Alat Fisik ESP32</p>
              </div>
            </div>
            <div class="block-action btn-group-selector">
              <button 
                class="btn-segment" 
                :class="{ active: systemState.iotMode === 'SIMULATION' }"
                @click="setIotSourceMode('SIMULATION')"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M10 2v7.31L4.17 18.5a2 2 0 0 0 1.66 3.5h12.34a2 2 0 0 0 1.66-3.5L14 9.31V2z"></path>
                </svg>
                <span>Mode Simulasi IoT</span>
              </button>
              <button 
                class="btn-segment" 
                :class="{ active: systemState.iotMode === 'HARDWARE' }"
                @click="setIotSourceMode('HARDWARE')"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <span>Mode Alat Fisik Live</span>
              </button>
            </div>
          </div>

          <!-- 3. Environment Mode -->
          <div class="control-block">
            <div class="block-header">
              <div class="block-icon" :class="systemState.environmentMode === 'PRODUCTION' ? 'bg-purple' : 'bg-blue'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                  <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
              </div>
              <div>
                <strong class="block-title">Lingkungan Server (Environment)</strong>
                <p class="block-desc">Pilih lingkungan pengoperasian server dan database</p>
              </div>
            </div>
            <div class="block-action btn-group-selector">
              <button 
                class="btn-segment" 
                :class="{ active: systemState.environmentMode === 'LOCAL' }"
                @click="setEnvironmentMode('LOCAL')"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                  <line x1="8" y1="21" x2="16" y2="21"></line>
                  <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                <span>Local Development</span>
              </button>
              <button 
                class="btn-segment" 
                :class="{ active: systemState.environmentMode === 'PRODUCTION' }"
                @click="setEnvironmentMode('PRODUCTION')"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="2" y1="12" x2="22" y2="12"></line>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                <span>Production Live</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: HARDWARE SIMULATOR CONTROLS (ACTIVE WHEN SIMULATION MODE) -->
      <div v-if="systemState.iotMode === 'SIMULATION'" class="content-card">
        <div class="card-header-flex">
          <div>
            <h2 class="card-section-heading">Preset Skenario Sensor & Simulasi Cuaca</h2>
            <p class="card-section-sub">Pilih skenario simulasi kondisi mikroklimat untuk menguji respons sistem dan aktuator otomatis.</p>
          </div>
          <button class="btn-secondary-action" @click="sendSinglePacket">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span>Kirim 1 Paket Data Sekarang</span>
          </button>
        </div>

        <!-- Scenario Tiles Grid -->
        <div class="scenarios-grid">
          <div 
            v-for="s in scenarios" 
            :key="s.id"
            class="scenario-card"
            :class="{ active: selectedScenario === s.id }"
            @click="chooseScenario(s.id)"
          >
            <div class="scenario-icon-box" :class="s.iconClass">
              <!-- Sunny icon -->
              <svg v-if="s.id === 'SUNNY'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
              </svg>
              <!-- Cloudy icon -->
              <svg v-else-if="s.id === 'CLOUDY'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
              </svg>
              <!-- Overheat icon -->
              <svg v-else-if="s.id === 'OVERHEAT'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
              </svg>
              <!-- Target reached icon -->
              <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="scenario-meta">
              <strong class="scenario-name">{{ s.name }}</strong>
              <p class="scenario-desc">{{ s.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Live Sliders for Manual Sensor Value Tuning -->
        <div class="sliders-container">
          <h3 class="sliders-title">Kalibrasi Nilai Sensor Telemetri Real-time</h3>
          <div class="sliders-grid">
            <!-- Suhu Internal -->
            <div class="slider-box">
              <div class="slider-header">
                <span class="slider-label">Suhu Internal (DHT22)</span>
                <strong class="slider-value text-orange">{{ simulatorService.currentValues.tempInternal.toFixed(1) }} °C</strong>
              </div>
              <input 
                type="range" 
                min="20" 
                max="75" 
                step="0.5" 
                v-model.number="simulatorService.currentValues.tempInternal" 
                class="range-slider range-orange"
              />
            </div>

            <!-- Kelembapan Internal -->
            <div class="slider-box">
              <div class="slider-header">
                <span class="slider-label">Kelembapan Internal (DHT22)</span>
                <strong class="slider-value text-blue">{{ simulatorService.currentValues.humidityInternal.toFixed(1) }} %</strong>
              </div>
              <input 
                type="range" 
                min="10" 
                max="95" 
                step="0.5" 
                v-model.number="simulatorService.currentValues.humidityInternal" 
                class="range-slider range-blue"
              />
            </div>

            <!-- Kadar Air Gabah -->
            <div class="slider-box">
              <div class="slider-header">
                <span class="slider-label">Kadar Air Hanjeli (Capacitive)</span>
                <strong class="slider-value text-green-dark">{{ simulatorService.currentValues.grainMoisture.toFixed(1) }} %</strong>
              </div>
              <input 
                type="range" 
                min="8" 
                max="35" 
                step="0.1" 
                v-model.number="simulatorService.currentValues.grainMoisture" 
                class="range-slider range-green"
              />
            </div>

            <!-- Radiasi Matahari -->
            <div class="slider-box">
              <div class="slider-header">
                <span class="slider-label">Radiasi Surya (Pyranometer)</span>
                <strong class="slider-value text-amber">{{ simulatorService.currentValues.solarRadiation.toFixed(0) }} W/m²</strong>
              </div>
              <input 
                type="range" 
                min="0" 
                max="1200" 
                step="10" 
                v-model.number="simulatorService.currentValues.solarRadiation" 
                class="range-slider range-amber"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: DATABASE SESSIONS & SEEDING INJECTOR -->
      <div class="content-card">
        <div class="card-header-flex">
          <div>
            <h2 class="card-section-heading">Perekaman Database MySQL & Pembuat Riwayat Sampel</h2>
            <p class="card-section-sub">Uji alur pembuatan batch aktif, perekaman database periodik, dan pembuatan 36 titik log riwayat.</p>
          </div>
        </div>

        <div class="db-tools-row">
          <div class="db-status-desc">
            <div class="db-active-indicator" :class="{ active: !!simulatorService.activeDbBatch?.value }">
              <span class="indicator-dot"></span>
              <span>
                {{ simulatorService.activeDbBatch?.value 
                  ? `Batch Aktif: ${simulatorService.activeDbBatch.value.batchCode} (${simulatorService.activeDbBatch.value.cropVariety || 'Hanjeli Ketan'})`
                  : 'Tidak ada sesi batch pengeringan aktif di database.' }}
              </span>
            </div>
          </div>

          <div class="db-action-buttons">
            <button 
              v-if="!simulatorService.activeDbBatch?.value" 
              class="btn-primary" 
              @click="handleStartDbBatch"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"></circle>
                <polygon points="10 8 16 12 10 16 10 8"></polygon>
              </svg>
              <span>Mulai Sesi Batch Baru di DB</span>
            </button>

            <button 
              v-else 
              class="btn-danger-action" 
              @click="handleCompleteDbBatch"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Selesaikan Sesi Batch Aktif</span>
            </button>

            <button 
              class="btn-secondary-action" 
              @click="handleGenerateHistorySample"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48 2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48 2.83-2.83"></path>
              </svg>
              <span>Generate Sampel Riwayat (36 Data Log)</span>
            </button>
          </div>
        </div>
      </div>

      <!-- SECTION 4: LIVE TELEMETRY LOGS & INGEST STREAM CONSOLE -->
      <div class="content-card">
        <div class="card-header-flex">
          <div>
            <h2 class="card-section-heading">Konsol Log Aliran Ingest Telemetri IoT</h2>
            <p class="card-section-sub">Daftar paket data HTTP/MQTT dan event status mikrokontroler.</p>
          </div>
          <button class="btn-clear-console" @click="simulatorService.clearLogs()">Bersihkan Konsol</button>
        </div>

        <div class="console-box">
          <div v-if="(simulatorService.logs?.value || []).length === 0" class="console-empty">
            <span>Menunggu paket telemetri atau aktivitas generator...</span>
          </div>
          <div 
            v-for="(log, idx) in (simulatorService.logs?.value || [])" 
            :key="log.id || idx" 
            class="console-line"
          >
            <span class="log-time">[{{ log.time }}]</span>
            <span class="log-msg" :class="{ 'log-err': (log.message || log.text || '').includes('ERROR'), 'log-succ': (log.message || log.text || '').includes('berhasil') || (log.message || log.text || '').includes('SELESAI') || (log.message || log.text || '').includes('200 OK') }">
              {{ log.message || log.text }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { settingsService, systemState } from '../../services/settingsService'
import { simulatorService } from '../../services/simulatorService'
import { confirmDialog } from '../../services/confirmDialogService'

const adminAlert = reactive({
  text: '',
  type: 'success'
})

function showAlert(text, type = 'success') {
  adminAlert.text = text
  adminAlert.type = type
  setTimeout(() => {
    if (adminAlert.text === text) adminAlert.text = ''
  }, 5000)
}

const selectedScenario = ref('SUNNY')

const scenarios = [
  { id: 'SUNNY', name: 'Cuaca Cerah (Optimal)', desc: 'Radiasi surya tinggi (750 W/m²), pengeringan normal cepat', iconClass: 'icon-sunny' },
  { id: 'CLOUDY', name: 'Mendung / Berawan', desc: 'Radiasi rendah (220 W/m²), memicu pemanas bantu keramik', iconClass: 'icon-cloudy' },
  { id: 'OVERHEAT', name: 'Peringatan Panas (>55°C)', desc: 'Suhu internal kritis (58°C), memicu exhaust fan 100%', iconClass: 'icon-hot' },
  { id: 'TARGET_REACHED', name: 'Target Selesai (<12%)', desc: 'Kadar air mencapai 11.5%, siap panen & sesi selesai', iconClass: 'icon-done' }
]

function chooseScenario(scenarioId) {
  selectedScenario.value = scenarioId
  simulatorService.setScenario(scenarioId)
  if (simulatorService.isRunning.value) {
    simulatorService.sendPacket()
  }
  showAlert(`Skenario simulasi diubah ke: ${scenarios.find(s => s.id === scenarioId)?.name}`)
}

function toggleSimulatorRunning() {
  if (simulatorService.isRunning.value) {
    simulatorService.stop()
    showAlert('Generator telemetri IoT simulasi dihentikan.')
  } else {
    simulatorService.start()
    showAlert('Generator telemetri IoT simulasi aktif & mengirim paket data secara periodik.')
  }
}

function sendSinglePacket() {
  simulatorService.sendPacket()
  showAlert('1 Paket data telemetri berhasil dikirim ke database MySQL.')
}

async function toggleSystemActiveState() {
  const targetState = !systemState.isSystemActive
  const confirmed = await confirmDialog({
    title: targetState ? 'Aktifkan Operasional Sistem?' : 'Nonaktifkan Operasional Sistem?',
    message: targetState 
      ? 'Sistem greenhouse akan kembali aktif dan siap menerima sesi pengeringan baru.' 
      : 'Sistem akan dialihkan ke mode Standby/Maintenance. Seluruh aktivitas batch pengeringan akan dipause.',
    type: targetState ? 'info' : 'warning',
    confirmText: targetState ? 'Ya, Aktifkan Sistem' : 'Ya, Nonaktifkan Sistem',
    cancelText: 'Batal',
  })

  if (confirmed) {
    try {
      await settingsService.setSystemActive(targetState)
      showAlert(targetState ? 'Sistem Smart Dryer berhasil diaktifkan.' : 'Sistem Smart Dryer dialihkan ke mode Nonaktif (Standby).')
    } catch (e) {
      showAlert(e.message || 'Gagal mengubah status sistem.', 'error')
    }
  }
}

async function setIotSourceMode(newMode) {
  if (systemState.iotMode === newMode) return

  const confirmed = await confirmDialog({
    title: newMode === 'SIMULATION' ? 'Beralih ke Mode Simulasi IoT?' : 'Beralih ke Mode Alat Fisik Live?',
    message: newMode === 'SIMULATION'
      ? 'Sistem akan mengaktifkan generator telemetri IoT buatan untuk pengujian software dan simulasi sensor.'
      : 'Sistem akan mengalirkan data sensor asli yang dikirim langsung dari mikrokontroler ESP32 di Greenhouse.',
    type: 'info',
    confirmText: newMode === 'SIMULATION' ? 'Ganti ke Mode Simulasi' : 'Ganti ke Alat Fisik Live',
    cancelText: 'Batal',
  })

  if (confirmed) {
    try {
      await settingsService.setIotMode(newMode)
      showAlert(`Mode IoT berhasil diubah ke: ${newMode === 'SIMULATION' ? 'Mode Simulasi IoT' : 'Mode Alat Fisik (ESP32)'}`)
    } catch (e) {
      showAlert(e.message || 'Gagal mengubah mode IoT.', 'error')
    }
  }
}

async function setEnvironmentMode(newEnv) {
  if (systemState.environmentMode === newEnv) return

  const confirmed = await confirmDialog({
    title: newEnv === 'PRODUCTION' ? 'Beralih ke Lingkungan Production?' : 'Beralih ke Lingkungan Local Testbed?',
    message: newEnv === 'PRODUCTION'
      ? 'Sistem akan menerapkan konfigurasi operasional riil Greenhouse Desa Wisata Hanjeli Waluran.'
      : 'Sistem akan beralih ke setelan pengembangan lokal & pengujian eksperimen.',
    type: 'warning',
    confirmText: `Beralih ke ${newEnv}`,
    cancelText: 'Batal',
  })

  if (confirmed) {
    try {
      await settingsService.setEnvironmentMode(newEnv)
      showAlert(`Lingkungan sistem berhasil diubah ke: ${newEnv}`)
    } catch (e) {
      showAlert(e.message || 'Gagal mengubah lingkungan sistem.', 'error')
    }
  }
}

async function handleStartDbBatch() {
  try {
    const res = await simulatorService.startDatabaseBatch({
      cropVariety: 'Hanjeli Ketan Sukabumi (Varietas Unggul)',
      initialWeightKg: 135.0,
      initialMoisturePercent: 24.5,
    })
    showAlert(`Sesi Batch ${res.batchCode} berhasil dibuat & diaktifkan di MySQL!`)
  } catch (err) {
    showAlert(`Gagal: ${err.message}`, 'error')
  }
}

async function handleCompleteDbBatch() {
  try {
    const res = await simulatorService.completeDatabaseBatch()
    showAlert(`Sesi Batch ${res?.batchCode || ''} berhasil diselesaikan & disimpan ke riwayat!`)
  } catch (err) {
    showAlert(`Gagal: ${err.message}`, 'error')
  }
}

async function handleGenerateHistorySample() {
  try {
    const res = await simulatorService.generateHistoricalSample('Hanjeli Ketan Sukabumi (Grade A)')
    showAlert(`Riwayat Batch ${res.batchCode} (36 data log) berhasil dibuat ke MySQL! Buka menu Riwayat untuk melihat grafik.`)
  } catch (err) {
    showAlert(`Gagal: ${err.message}`, 'error')
  }
}

onMounted(() => {
  settingsService.getSettings()
})
</script>

<style scoped>
.admin-page {
  width: 100%;
  min-height: 100%;
}

.admin-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 28px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-title {
  font-size: 30px;
  font-weight: 700;
  color: var(--color-text-title);
  line-height: 38px;
}

.page-subtitle {
  font-size: 15px;
  color: var(--color-text-muted);
  line-height: 24px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-primary {
  padding: 10px 20px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
  }

.btn-primary:hover {
  background: #15803D;
}

.btn-running {
  background: #DC2626;
}

.btn-running:hover {
  background: #B91C1C;
}

.admin-feedback-toast {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  }

.admin-feedback-toast.success {
  background: #E8F5E9;
  color: #0D631B;
  border: 1px solid #C8E6C9;
}

.admin-feedback-toast.error {
  background: #FFEBEE;
  color: #C62828;
  border: 1px solid #FFCDD2;
}

.close-toast-btn {
  background: transparent;
  border: none;
  font-size: 16px;
  cursor: pointer;
  margin-left: auto;
  color: inherit;
  opacity: 0.7;
}

.content-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 14px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.master-controls-card {
  border-left: 4px solid #0D631B;
}

.card-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-section-heading {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0 0 4px 0;
}

.card-section-sub {
  font-size: 13px;
  color: var(--color-text-muted);
  margin: 0;
}

.system-power-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.power-active {
  background: #DCFCE7;
  color: #15803D;
  border: 1px solid #86EFAC;
}

.power-active .power-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #16A34A;
  }

.power-standby {
  background: #FEE2E2;
  color: #DC2626;
  border: 1px solid #FECACA;
}

.power-standby .power-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #EF4444;
}

.mode-control-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.control-block {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 18px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 16px;
}

.block-header {
  display: flex;
  align-items: center;
  gap: 14px;
}

.block-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.block-icon.bg-green { background: #DCFCE7; color: #16A34A; }
.block-icon.bg-red { background: #FEE2E2; color: #DC2626; }
.block-icon.bg-amber { background: #FEF3C7; color: #D97706; }
.block-icon.bg-blue { background: #DBEAFE; color: #2563EB; }
.block-icon.bg-purple { background: #F3E8FF; color: #9333EA; }

.block-title {
  display: block;
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.block-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  line-height: 1.4;
  margin: 0;
}

.btn-mode-toggle {
  width: 100%;
  padding: 11px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  line-height: 1;
}

.btn-active-state {
  background: #16A34A;
  color: #FFFFFF;
}

.btn-inactive-state {
  background: #64748B;
  color: #FFFFFF;
}

.toggle-circle {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #FFFFFF;
  flex-shrink: 0;
  display: inline-block;
}

.btn-group-selector {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: rgba(0, 0, 0, 0.05);
  padding: 4px;
  border-radius: 10px;
  gap: 6px;
  align-items: center;
}

:global(.dark-theme) .btn-group-selector {
  background: rgba(255, 255, 255, 0.06);
}

.btn-segment {
  padding: 9px 12px;
  border-radius: 8px;
  border: none;
  font-size: 13px;
  font-weight: 600;
  background: transparent;
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-align: center;
  line-height: 1;
}

.btn-segment svg {
  flex-shrink: 0;
}

.btn-segment.active {
  background: #FFFFFF;
  color: #0F172A;
  font-weight: 700;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

:global(.dark-theme) .btn-segment.active {
  background: #1E293B;
  color: #F8FAFC;
  border-color: rgba(255, 255, 255, 0.1);
}

/* Scenarios */
.scenarios-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.scenario-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.scenario-card:hover {
  border-color: #0D631B;
  transform: translateY(-1px);
}

.scenario-card.active {
  background: #F0FDF4;
  border-color: #86EFAC;
}

:global(.dark-theme) .scenario-card.active {
  background: rgba(13, 99, 27, 0.15);
  border-color: #16A34A;
}

.scenario-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.scenario-icon-box.icon-sunny { background: #FEF3C7; color: #D97706; }
.scenario-icon-box.icon-cloudy { background: #DBEAFE; color: #2563EB; }
.scenario-icon-box.icon-hot { background: #FEE2E2; color: #DC2626; }
.scenario-icon-box.icon-done { background: #DCFCE7; color: #16A34A; }

.scenario-name {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.scenario-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.4;
}

/* Sliders */
.sliders-container {
  margin-top: 10px;
  padding-top: 20px;
  border-top: 1px solid var(--color-border);
}

.sliders-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 16px;
}

.sliders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}

.slider-box {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.slider-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.slider-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.slider-value {
  font-size: 15px;
  font-weight: 700;
}

.range-slider {
  width: 100%;
  accent-color: #0D631B;
  cursor: pointer;
}

.range-orange { accent-color: #EA580C; }
.range-blue { accent-color: #0284C7; }
.range-green { accent-color: #16A34A; }
.range-amber { accent-color: #D97706; }

/* DB Tools */
.db-tools-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.db-active-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-muted);
}

.db-active-indicator.active {
  color: #15803D;
}

.indicator-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #94A3B8;
}

.db-active-indicator.active .indicator-dot {
  background: #16A34A;
  }

.db-action-buttons {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.btn-secondary-action {
  padding: 10px 18px;
  background: var(--color-bg);
  color: var(--color-text-title);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary-action:hover {
  background: var(--color-white);
  border-color: #0D631B;
}

.btn-danger-action {
  padding: 10px 18px;
  background: #EF4444;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-danger-action:hover {
  background: #DC2626;
}

/* Console Box */
.console-box {
  background: #0F172A;
  border-radius: 10px;
  padding: 16px;
  max-height: 220px;
  overflow-y: auto;
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.console-empty {
  color: #64748B;
  font-style: italic;
}

.console-line {
  display: flex;
  gap: 8px;
  line-height: 1.4;
}

.log-time {
  color: #94A3B8;
  flex-shrink: 0;
}

.log-msg {
  color: #E2E8F0;
  word-break: break-all;
}

.log-err {
  color: #F87171;
}

.log-succ {
  color: #4ADE80;
}

.btn-clear-console {
  background: transparent;
  border: none;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  cursor: pointer;
  transition: color 0.2s;
}

.btn-clear-console:hover {
  color: #DC2626;
}

@media (max-width: 900px) {
  .admin-content { padding: 20px 16px; }
  .header-action-row { flex-direction: column; align-items: flex-start; gap: 14px; }
  .mode-control-grid { grid-template-columns: 1fr; }
}
</style>
