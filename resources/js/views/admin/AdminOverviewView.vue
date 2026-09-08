<template>
  <div class="admin-page">
    <div class="admin-content">
      <!-- Top Title & Action Bar -->
      <div class="header-action-row">
        <div class="title-group">
          <h1 class="page-title">{{ $t('admin.tabOverview') }}</h1>
          <p class="page-subtitle">{{ $t('admin.subtitle') }}</p>
        </div>

        <div class="header-actions">
          <button class="btn-secondary-action" @click="refreshOverview">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
            </svg>
            <span>Perbarui Data</span>
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

      <!-- 4 Stat Cards Grid (Matching Operator Dashboard) -->
      <div class="stats-grid-4">
        <!-- 1. Total Pengguna -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">{{ $t('admin.totalUsers') }}</span>
            <div class="icon-circle icon-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number">{{ usersList.length }}</span>
            <span class="stat-unit">Akun</span>
          </div>
          <div class="stat-trend trend-down-blue">
            <span>{{ operatorCount }} Operator • {{ usersList.length - operatorCount }} Admin</span>
          </div>
        </div>

        <!-- 2. Total Batch Terdata -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">{{ $t('admin.totalBatches') }}</span>
            <div class="icon-circle icon-green-light">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number">{{ totalBatchesCount }}</span>
            <span class="stat-unit">Batch</span>
          </div>
          <div class="stat-trend trend-up-green">
            <span>Arsip Telemetri MySQL Aktif</span>
          </div>
        </div>

        <!-- 3. Server Laravel API -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">SERVER BACKEND</span>
            <div class="icon-circle icon-orange">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                <line x1="6" y1="6" x2="6.01" y2="6"></line>
                <line x1="6" y1="18" x2="6.01" y2="18"></line>
              </svg>
            </div>
          </div>
          <div class="stat-status-row">
            <span class="status-indicator-dot"></span>
            <span class="status-text-green" style="font-size: 24px;">ONLINE</span>
          </div>
          <div class="stat-trend">
            <span class="badge-pill bg-green-subtle text-green-dark">Port 8000 • Ingest Active</span>
          </div>
        </div>

        <!-- 4. Database MySQL -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">DATABASE UTAMA</span>
            <div class="icon-circle icon-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
              </svg>
            </div>
          </div>
          <div class="stat-status-row">
            <span class="status-indicator-dot"></span>
            <span class="status-text-green" style="font-size: 24px;">CONNECTED</span>
          </div>
          <div class="stat-trend">
            <span class="badge-pill bg-blue-subtle text-blue">MySQL 8.0 • Healthy</span>
          </div>
        </div>
      </div>

      <!-- MASTER SYSTEM CONTROL PANEL (ADMIN ONLY) -->
      <div class="content-card system-mode-control-card">
        <div class="card-header-flex">
          <div>
            <h2 class="card-section-heading">Kontrol Mode & Operasional Sistem</h2>
            <p class="card-section-sub">Atur mode simulasi/alat fisik, status operasional, serta lingkungan sistem Smart Dryer.</p>
          </div>
          <div class="system-power-badge" :class="systemState.isSystemActive ? 'power-active' : 'power-standby'">
            <span class="power-dot"></span>
            <span>{{ systemState.isSystemActive ? 'SISTEM ONLINE' : 'SISTEM STANDBY' }}</span>
          </div>
        </div>

        <div class="mode-control-grid">
          <!-- 1. System Operational State -->
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
                <span>{{ systemState.isSystemActive ? 'Sistem Aktif (Operasional)' : 'Sistem Dinonaktifkan' }}</span>
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
                <span>Mode Simulasi IoT</span>
              </button>
              <button 
                class="btn-segment" 
                :class="{ active: systemState.iotMode === 'HARDWARE' }"
                @click="setIotSourceMode('HARDWARE')"
              >
                <span>Mode Alat Fisik Live</span>
              </button>
            </div>
            <div v-if="systemState.iotMode === 'SIMULATION'" class="sub-action-row">
              <button class="btn-open-sim" @click="$emit('open-simulator')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <span>Buka Panel Simulator IoT</span>
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
                <strong class="block-title">Lingkungan Sistem (Environment)</strong>
                <p class="block-desc">Pilih lingkungan pengoperasian server dan database</p>
              </div>
            </div>
            <div class="block-action btn-group-selector">
              <button 
                class="btn-segment" 
                :class="{ active: systemState.environmentMode === 'LOCAL' }"
                @click="setEnvironmentMode('LOCAL')"
              >
                <span>Local Development</span>
              </button>
              <button 
                class="btn-segment" 
                :class="{ active: systemState.environmentMode === 'PRODUCTION' }"
                @click="setEnvironmentMode('PRODUCTION')"
              >
                <span>Production Live</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- System Status & Gateway Node Card -->
      <div class="system-status-card">
        <div class="system-status-left">
          <div class="icon-circle-lg icon-green-light">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
              <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
              <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
              <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
              <line x1="12" y1="20" x2="12.01" y2="20"></line>
            </svg>
          </div>
          <div class="system-info">
            <h2 class="system-title">Node Gateway ESP32-WROOM-32D</h2>
            <div class="badge-group">
              <span class="badge-pill bg-green-subtle text-green-dark">
                <span class="dot-green"></span> Online (-58 dBm Signal)
              </span>
              <span class="badge-pill bg-blue-subtle text-blue">
                Firmware v2.4.2
              </span>
              <span class="badge-pill bg-orange-subtle text-orange">
                Greenhouse Unit 1 (Waluran)
              </span>
            </div>
          </div>
        </div>
        <div class="system-status-right">
          <span class="meta-label">Frekuensi Pengiriman Sensor</span>
          <span class="meta-time">Setiap 5 Detik</span>
        </div>
      </div>

      <!-- Emergency Actuator Overrides Card -->
      <div class="content-card">
        <div class="card-header-flex">
          <div>
            <h2 class="card-section-heading">Kontrol & Override Darurat Aktuator</h2>
            <p class="card-section-sub">Pengendalian manual aktuator greenhouse untuk kebutuhan uji coba dan keselamatan.</p>
          </div>
          <span class="badge-pill" :class="actuatorState.controlMode === 'AUTOMATIC' ? 'bg-green-subtle text-green-dark' : 'bg-orange-subtle text-orange'">
            Mode: {{ actuatorState.controlMode }}
          </span>
        </div>

        <div class="actuators-tile-grid">
          <!-- Exhaust Fan -->
          <div class="actuator-tile" :class="{ 'tile-active': actuatorState.exhaustFanStatus }">
            <div class="tile-header">
              <div>
                <strong class="tile-title">Exhaust Fan</strong>
                <p class="tile-desc">Pembuang Udara Panas / Lembab</p>
              </div>
              <button 
                class="btn-toggle-switch" 
                :class="{ active: actuatorState.exhaustFanStatus }"
                @click="toggleActuator('exhaustFanStatus')"
              >
                {{ actuatorState.exhaustFanStatus ? 'ON' : 'OFF' }}
              </button>
            </div>
            <div v-if="actuatorState.exhaustFanStatus" class="tile-slider-box">
              <span>Kecepatan: {{ actuatorState.exhaustFanSpeed }}%</span>
              <input type="range" min="0" max="100" v-model.number="actuatorState.exhaustFanSpeed" @change="updateActuatorSpeed" />
            </div>
          </div>

          <!-- Blower Fan -->
          <div class="actuator-tile" :class="{ 'tile-active': actuatorState.blowerFanStatus }">
            <div class="tile-header">
              <div>
                <strong class="tile-title">Blower Sirkulasi Rak</strong>
                <p class="tile-desc">Distribusi Aliran Udara Internal</p>
              </div>
              <button 
                class="btn-toggle-switch" 
                :class="{ active: actuatorState.blowerFanStatus }"
                @click="toggleActuator('blowerFanStatus')"
              >
                {{ actuatorState.blowerFanStatus ? 'ON' : 'OFF' }}
              </button>
            </div>
            <div v-if="actuatorState.blowerFanStatus" class="tile-slider-box">
              <span>Kecepatan: {{ actuatorState.blowerFanSpeed }}%</span>
              <input type="range" min="0" max="100" v-model.number="actuatorState.blowerFanSpeed" @change="updateActuatorSpeed" />
            </div>
          </div>

          <!-- Auxiliary Heater -->
          <div class="actuator-tile" :class="{ 'tile-active': actuatorState.auxHeaterStatus }">
            <div class="tile-header">
              <div>
                <strong class="tile-title">Auxiliary Heater</strong>
                <p class="tile-desc">Pemanas Keramik Tambahan</p>
              </div>
              <button 
                class="btn-toggle-switch" 
                :class="{ active: actuatorState.auxHeaterStatus }"
                @click="toggleActuator('auxHeaterStatus')"
              >
                {{ actuatorState.auxHeaterStatus ? 'ON' : 'OFF' }}
              </button>
            </div>
            <div v-if="actuatorState.auxHeaterStatus" class="tile-meta">
              <span>Daya: Level {{ actuatorState.auxHeaterLevel }} / 3 (Aktif)</span>
            </div>
          </div>

          <!-- Roof Vent -->
          <div class="actuator-tile" :class="{ 'tile-active': actuatorState.roofVentStatus }">
            <div class="tile-header">
              <div>
                <strong class="tile-title">Ventilasi Atap (Roof Vent)</strong>
                <p class="tile-desc">Sirkulasi Atap Otomatis</p>
              </div>
              <button 
                class="btn-toggle-switch" 
                :class="{ active: actuatorState.roofVentStatus }"
                @click="toggleActuator('roofVentStatus')"
              >
                {{ actuatorState.roofVentStatus ? 'BUKA' : 'TUTUP' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity & Audit Logs Card -->
      <div class="activity-card">
        <div class="activity-head-flex">
          <h2 class="activity-heading">Aktivitas & Log Audit Terkini</h2>
          <router-link to="/admin/logs" class="btn-text-link">Lihat Seluruh Log &rarr;</router-link>
        </div>
        
        <div class="timeline-container">
          <div v-for="(log, idx) in systemAuditLogs.slice(0, 4)" :key="log.id || idx" class="timeline-item">
            <div class="timeline-marker">
              <div class="marker-circle" :class="log.level === 'WARNING' ? 'marker-orange' : log.level === 'ERROR' ? 'marker-red' : 'marker-blue'">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="12" cy="12" r="10"/>
                </svg>
              </div>
              <div v-if="idx < Math.min(systemAuditLogs.length, 4) - 1" class="marker-line"></div>
            </div>
            <div class="timeline-content">
              <div class="timeline-title">
                <span class="log-badge-inline" :class="`badge-${log.level.toLowerCase()}`">{{ log.level }}</span>
                <span class="log-cat-inline">[{{ log.category }}]</span>
                <span>{{ log.message }}</span>
              </div>
              <span class="timeline-time">{{ log.time }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { authService } from '../../services/authService'
import { actuatorService } from '../../services/actuatorService'
import { batchService } from '../../services/batchService'
import { settingsService, systemState } from '../../services/settingsService'
import { confirmDialog } from '../../services/confirmDialogService'

const emit = defineEmits(['open-simulator'])

const usersList = ref([])
const totalBatchesCount = ref(0)

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

const actuatorState = reactive({
  exhaustFanStatus: false,
  exhaustFanSpeed: 70,
  blowerFanStatus: true,
  blowerFanSpeed: 80,
  auxHeaterStatus: false,
  auxHeaterLevel: 0,
  roofVentStatus: true,
  controlMode: 'AUTOMATIC'
})

import { alertService } from '../../services/alertService'

const systemAuditLogs = ref([])

function formatLogTime(timestamp) {
  if (!timestamp) return 'Baru saja'
  try {
    const d = new Date(timestamp)
    return d.toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' })
  } catch {
    return 'Baru saja'
  }
}

async function fetchAuditLogs() {
  try {
    const res = await alertService.getAll()
    const alerts = Array.isArray(res) ? res : res?.alerts || []
    if (alerts.length > 0) {
      systemAuditLogs.value = alerts.slice(0, 6).map(a => ({
        id: a.id,
        time: formatLogTime(a.createdAt || a.created_at),
        level: a.type === 'DANGER' ? 'ERROR' : a.type === 'WARNING' ? 'WARNING' : 'INFO',
        category: a.category || 'SYSTEM',
        message: a.message || a.title
      }))
    } else {
      systemAuditLogs.value = []
    }
  } catch (err) {
    console.warn('Could not fetch audit logs for overview:', err.message)
    systemAuditLogs.value = []
  }
}

const operatorCount = computed(() => {
  return usersList.value.filter(u => u.role === 'OPERATOR').length
})

async function fetchUsers() {
  try {
    const res = await authService.getAllUsers()
    if (res && res.users && Array.isArray(res.users)) {
      usersList.value = res.users
    } else if (Array.isArray(res)) {
      usersList.value = res
    } else if (res && res.data && Array.isArray(res.data)) {
      usersList.value = res.data
    } else {
      usersList.value = []
    }
  } catch (err) {
    console.warn('Failed to fetch users for overview:', err.message)
    usersList.value = []
  }
}

async function fetchBatches() {
  try {
    const res = await batchService.getAll()
    totalBatchesCount.value = Array.isArray(res) ? res.length : 0
  } catch {
    totalBatchesCount.value = 0
  }
}

function refreshOverview() {
  fetchUsers()
  fetchBatches()
  fetchAuditLogs()
  settingsService.getSettings()
  showAlert('Data ringkasan sistem berhasil diperbarui!')
}

async function toggleActuator(key) {
  actuatorState[key] = !actuatorState[key]
  actuatorState.controlMode = 'MANUAL'
  try {
    await actuatorService.updateStatus(actuatorState)
    showAlert(`Status aktuator ${key} diperbarui.`)
  } catch (e) {
    console.warn('Actuator update note:', e.message)
  }
}

async function updateActuatorSpeed() {
  actuatorState.controlMode = 'MANUAL'
  try {
    await actuatorService.updateStatus(actuatorState)
  } catch (e) {
    console.warn('Actuator speed note:', e.message)
  }
}

onMounted(() => {
  fetchUsers()
  fetchBatches()
  fetchAuditLogs()
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
  gap: 32px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.title-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: var(--color-text-title);
  line-height: 40px;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
  line-height: 24px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
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

.stats-grid-4 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.stat-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 180px;
}

.stat-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.icon-circle {
  width: 36px;
  height: 36px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-orange { background: rgba(180, 80, 0, 0.12); }
.icon-blue { background: rgba(0, 93, 183, 0.12); }
.icon-green-light { background: rgba(13, 99, 27, 0.12); }

.stat-value-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
  margin-top: 8px;
}

.stat-number {
  font-size: 44px;
  font-weight: 700;
  color: var(--color-text-title);
  line-height: 52px;
}

.stat-unit {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text-muted);
}

.stat-status-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 8px;
}

.status-indicator-dot {
  width: 14px;
  height: 14px;
  background: #0D631B;
  border-radius: 9999px;
}

.status-text-green {
  font-weight: 700;
  color: #0D631B;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 500;
}

.trend-up-green { color: #0D631B; }
.trend-down-blue { color: #005DB7; }

.system-status-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.system-status-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.icon-circle-lg {
  width: 48px;
  height: 48px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.system-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.system-title {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text-title);
  line-height: 28px;
}

.badge-group {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.dot-green {
  width: 8px;
  height: 8px;
  background: #0D631B;
  border-radius: 50%;
  display: inline-block;
  margin-right: 4px;
}

.system-status-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 2px;
}

.meta-label {
  font-size: 12px;
  font-weight: 500;
  color: var(--color-text-muted);
}

.meta-time {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text-title);
}

.content-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.card-section-heading {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text-title);
}

.card-section-sub {
  font-size: 14px;
  color: var(--color-text-muted);
  margin-top: 4px;
}

.actuators-tile-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.actuator-tile {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 18px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 14px;
  transition: all 0.2s ease;
}

.actuator-tile.tile-active {
  border-color: #0D631B;
  background: rgba(13, 99, 27, 0.04);
}

.tile-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.tile-title {
  font-size: 15px;
  font-weight: 600;
  color: var(--color-text-title);
}

.tile-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

.btn-toggle-switch {
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 700;
  border-radius: 20px;
  border: 1px solid var(--color-border);
  background: var(--color-white);
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s;
}

.btn-toggle-switch.active {
  background: #0D631B;
  border-color: #0D631B;
  color: #FFFFFF;
}

.tile-slider-box {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 12px;
  font-weight: 500;
  color: var(--color-text-main);
}

.tile-slider-box input[type="range"] {
  accent-color: #0D631B;
}

.tile-meta {
  font-size: 12px;
  font-weight: 600;
  color: #0D631B;
}

.activity-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.activity-head-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--color-border);
}

.activity-heading {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text-title);
}

.btn-text-link {
  color: #0D631B;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
}

.btn-text-link:hover {
  text-decoration: underline;
}

.timeline-container {
  display: flex;
  flex-direction: column;
}

.timeline-item {
  display: flex;
  gap: 16px;
  position: relative;
}

.timeline-marker {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.marker-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.marker-blue { background: rgba(0, 93, 183, 0.12); color: #005DB7; }
.marker-orange { background: rgba(180, 80, 0, 0.12); color: #B45000; }
.marker-red { background: rgba(186, 26, 26, 0.12); color: #BA1A1A; }

.marker-line {
  width: 2px;
  flex: 1;
  background: var(--color-border);
  margin: 4px 0;
  min-height: 28px;
}

.timeline-content {
  flex: 1;
  padding-bottom: 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.timeline-title {
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text-title);
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.log-badge-inline {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
}

.log-badge-inline.badge-info { background: #E0F2FE; color: #0369A1; }
.log-badge-inline.badge-warning { background: #FEF3C7; color: #B45309; }
.log-badge-inline.badge-error { background: #FEE2E2; color: #DC2626; }

.log-cat-inline {
  color: var(--color-text-muted);
  font-weight: 600;
}

.timeline-time {
  font-size: 12px;
  color: var(--color-text-muted);
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

.system-mode-control-card {
  border-left: 4px solid #0D631B;
}

.system-power-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.03em;
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
    animation: pulse 1.8s infinite;
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
  transition: all 0.2s;
}

.control-block:hover {
  border-color: #94A3B8;
  }

.block-header {
  display: flex;
  align-items: flex-start;
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
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-active-state {
  background: #16A34A;
  color: #FFFFFF;
  }

.btn-active-state:hover {
  background: #15803D;
}

.btn-inactive-state {
  background: #64748B;
  color: #FFFFFF;
}

.btn-inactive-state:hover {
  background: #475569;
}

.toggle-circle {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #FFFFFF;
}

.btn-group-selector {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: rgba(0, 0, 0, 0.05);
  padding: 4px;
  border-radius: 10px;
  gap: 4px;
}

.btn-segment {
  padding: 8px 12px;
  border-radius: 8px;
  border: none;
  font-size: 12px;
  font-weight: 600;
  background: transparent;
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.btn-segment.active {
  background: #FFFFFF;
  color: #0F172A;
  font-weight: 700;
  }

:global(.dark-theme) .btn-segment.active {
  background: #1E293B;
  color: #F8FAFC;
}

.sub-action-row {
  display: flex;
  justify-content: flex-end;
}

.btn-open-sim {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #0D631B;
  background: rgba(13, 99, 27, 0.1);
  border: 1px solid rgba(13, 99, 27, 0.2);
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-open-sim:hover {
  background: #0D631B;
  color: #FFFFFF;
}

.badge-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
}

.bg-green-subtle { background: rgba(46, 125, 50, 0.15); }
.text-green-dark { color: #0D631B; }
.bg-blue-subtle { background: rgba(0, 93, 183, 0.12); }
.text-blue { color: #005DB7; }
.bg-orange-subtle { background: rgba(180, 80, 0, 0.12); }
.text-orange { color: #B45000; }

@media (max-width: 900px) {
  .admin-content { padding: 20px 16px; }
  .header-action-row { flex-direction: column; align-items: flex-start; }
  .system-status-card { flex-direction: column; align-items: flex-start; gap: 16px; }
  .system-status-right { align-items: flex-start; }
}
</style>
