<template>
  <div class="active-drying-page">
    <Transition name="fade-active-drying" mode="out-in">
      <ActiveDryingSkeleton v-if="isInitialLoading" />
      <div v-else class="active-drying-loaded-wrap">
        <div class="active-drying-main-content">
          
          <!-- 1. Header & Tombol Tindakan Cepat -->
          <div class="active-header-card">
            <div class="header-left">
              <div class="batch-title-row">
                <h1 class="batch-title">{{ activeBatch?.cropVariety || 'Biji Hanjeli' }}</h1>
                <span class="batch-code-badge">{{ activeBatch?.batchCode || 'HJ-AKTIF' }}</span>
                <span class="batch-status-pill" :class="isPaused ? 'status-paused' : 'status-running'">
                  <span class="status-pulse-dot"></span>
                  <span>{{ isPaused ? 'DIJEDA SEMENTARA' : 'SEDANG MENGERINGKAN' }}</span>
                </span>
              </div>

              <p class="batch-operator-info">
                Operator: <strong>{{ activeBatch?.operatorName || activeBatch?.operator?.name || 'Operator Greenhouse' }}</strong> &bull;
                Mode: <strong>{{ activeBatch?.dryingMode === 'PURE_SOLAR' ? 'Hanya Sinar Matahari' : 'Otomatis (Matahari + Pemanas Listrik)' }}</strong>
              </p>
            </div>

            <!-- Tombol Aksi Utama -->
            <div class="header-actions-group">
              <button 
                class="btn-finish-main"
                @click="handleFinishDrying"
                title="Selesaikan sesi pengeringan saat kadar air sudah tercapai"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>Selesaikan Pengeringan</span>
              </button>

              <div class="header-secondary-actions">
                <button 
                  class="btn-secondary-action"
                  @click="handleTogglePause"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <template v-if="isPaused">
                      <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </template>
                    <template v-else>
                      <rect x="6" y="4" width="4" height="16"></rect>
                      <rect x="14" y="4" width="4" height="16"></rect>
                    </template>
                  </svg>
                  <span>{{ isPaused ? 'Lanjutkan Proses' : 'Jeda Sementara' }}</span>
                </button>

                <button 
                  class="btn-secondary-action"
                  @click="isQrModalOpen = true"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2.2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                  </svg>
                  <span>Cetak QR Kemasan</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 2. Kartu Progres Kadar Air & Waktu (Besar & Jelas) -->
          <div class="drying-progress-card">
            <div class="progress-top-row">
              <div class="prog-time-box">
                <div class="clock-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </div>
                <div>
                  <span class="stat-small-label">WAKTU BERJALAN</span>
                  <div class="clock-display">{{ formattedTime }}</div>
                </div>
              </div>

              <div class="prog-eta-box">
                <span class="stat-small-label">ESTIMASI SELESAI</span>
                <div class="eta-val">~{{ estimatedHoursRemaining }} Jam Lagi</div>
              </div>

              <div class="prog-rate-box">
                <span class="stat-small-label">KECEPATAN PENURUNAN</span>
                <div class="rate-val text-green">~{{ currentDryingRate }}% / jam</div>
              </div>
            </div>

            <!-- Bar Progres Penurunan Kadar Air -->
            <div class="progress-track-wrapper">
              <div class="track-info-row">
                <span class="track-label-start">
                  Kadar Air Awal: <strong>{{ activeBatch?.initialMoisturePercent || 25 }}%</strong>
                </span>
                <span class="track-badge-current">
                  Estimasi Saat Ini: <strong>{{ telemetry.grainMoisture.toFixed(1) }}%</strong> (Progres {{ progressPercent }}%)
                </span>
                <span class="track-label-target">
                  Target Standar SNI: <strong>{{ activeBatch?.targetMoisturePercent || 12 }}%</strong>
                </span>
              </div>

              <div class="progress-bar-container">
                <div class="progress-bar-fill" :style="{ width: progressPercent + '%' }"></div>
              </div>
            </div>

            <!-- Callout Penjelasan Model Estimasi & Rekomendasi Grain Moisture Meter -->
            <div class="drying-model-notice">
              <div class="dmn-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
              </div>
              <div class="dmn-content">
                <div class="dmn-title">Estimasi Model Pengeringan (Berdasarkan Suhu & Waktu)</div>
                <p class="dmn-text">
                  Perangkat IoT ESP32 membaca suhu, kelembapan (DHT22), dan cahaya (BH1750). Nilai kadar air biji dan estimasi bobot dihitung melalui model matematis laju pengeringan (tanpa timbangan digital Load Cell). Operator disarankan memeriksa kadar air secara berkala menggunakan <strong>Grain Moisture Meter</strong> manual sebelum menyelesaikan sesi.
                </p>
              </div>
            </div>
          </div>

          <!-- 3. Empat Indikator Kondisi Ruang Saat Ini -->
          <div class="active-kpi-grid">
            
            <!-- Suhu Ruang -->
            <div class="active-kpi-card">
              <div class="kpi-top">
                <span class="kpi-title">Suhu Ruang Pengering</span>
                <div class="icon-wrap icon-amber">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="kpi-val-row">
                <span class="kpi-num">{{ telemetry.tempInternal.toFixed(1) }}</span>
                <span class="kpi-unit">°C</span>
              </div>
              <div class="kpi-footer-status">
                <span class="badge-chip" :class="telemetry.tempInternal >= 50 ? 'chip-red' : (telemetry.tempInternal >= 40 ? 'chip-green' : 'chip-amber')">
                  {{ telemetry.tempInternal >= 50 ? 'Panas Maksimal (Istirahat)' : (telemetry.tempInternal >= 40 ? 'Suhu Ideal Pengeringan' : 'Sedang Menghangatkan') }}
                </span>
              </div>
            </div>

            <!-- Kelembapan Ruang -->
            <div class="active-kpi-card">
              <div class="kpi-top">
                <span class="kpi-title">Kelembapan Ruang</span>
                <div class="icon-wrap icon-blue">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2.2">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="kpi-val-row">
                <span class="kpi-num">{{ telemetry.humidityInternal.toFixed(0) }}</span>
                <span class="kpi-unit">%</span>
              </div>
              <div class="kpi-footer-status">
                <span class="badge-chip chip-blue">
                  {{ telemetry.humidityInternal <= 60 ? 'Kering Terkendali' : 'Cukup Lembap' }}
                </span>
              </div>
            </div>

            <!-- Cahaya & Cuaca (BH1750 & Sensor Hujan) -->
            <div class="active-kpi-card">
              <div class="kpi-top">
                <span class="kpi-title">Cahaya & Cuaca (BH1750)</span>
                <div class="icon-wrap icon-amber">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                  </svg>
                </div>
              </div>
              <div class="kpi-val-row">
                <span class="kpi-num">{{ telemetry.solarRadiation.toFixed(0) }}</span>
                <span class="kpi-unit">Lux</span>
              </div>
              <div class="kpi-footer-status">
                <span class="badge-chip" :class="telemetry.isRaining ? 'chip-blue' : (telemetry.solarRadiation >= 150 ? 'chip-amber' : 'chip-neutral')">
                  {{ telemetry.isRaining ? 'Hujan (Sensor Basah)' : (telemetry.solarRadiation >= 150 ? 'Cerah Terik' : 'Redup / Berawan') }}
                </span>
              </div>
            </div>

            <!-- Pemanas Tambahan -->
            <div class="active-kpi-card">
              <div class="kpi-top">
                <span class="kpi-title">Pemanas Tambahan</span>
                <div class="icon-wrap" :class="actuators.auxHeaterStatus ? 'icon-amber' : 'icon-green'">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                  </svg>
                </div>
              </div>
              <div class="kpi-val-row">
                <span class="kpi-status-text" :class="actuators.auxHeaterStatus ? 'text-amber' : 'text-green'">
                  {{ actuators.auxHeaterStatus ? 'MENYALA' : 'STANDBY' }}
                </span>
              </div>
              <div class="kpi-footer-status">
                <span class="badge-chip chip-neutral">
                  {{ actuators.auxHeaterStatus ? 'Membantu Panas Alami' : 'Panas Alami Cukup' }}
                </span>
              </div>
            </div>

          </div>

          <!-- 4. Grafik Tren Sederhana: Penurunan Kadar Air & Suhu -->
          <div class="active-chart-card">
            <div class="chart-head">
              <div>
                <h2 class="chart-heading">Grafik Penurunan Kadar Air & Suhu</h2>
                <p class="chart-desc">Pantau penurunan kadar air gabah menuju target SNI 12% dan kestabilan suhu ruangan.</p>
              </div>
              <div class="chart-legend-box">
                <span class="legend-chip"><span class="color-dot dot-purple"></span> Kadar Air Gabah (%)</span>
                <span class="legend-chip"><span class="color-dot dot-red"></span> Suhu Ruangan (°C)</span>
              </div>
            </div>

            <div class="chart-canvas-box">
              <svg viewBox="0 0 800 200" class="active-svg" preserveAspectRatio="none">
                <!-- Target Line 12% SNI -->
                <line x1="50" y1="150" x2="770" y2="150" stroke="#7E22CE" stroke-width="1.5" stroke-dasharray="4 4" />
                <text x="56" y="145" font-size="11" fill="#7E22CE" font-weight="700">Target SNI (12.0%)</text>

                <!-- Grid lines -->
                <line x1="50" y1="20" x2="770" y2="20" stroke="#F1F5F9" />
                <text x="18" y="24" font-size="10" fill="#94A3B8">30% / 60°</text>

                <line x1="50" y1="85" x2="770" y2="85" stroke="#F1F5F9" />
                <text x="18" y="89" font-size="10" fill="#94A3B8">20% / 40°</text>

                <line x1="50" y1="150" x2="770" y2="150" stroke="#E2E8F0" />
                <text x="18" y="154" font-size="10" fill="#94A3B8">10% / 20°</text>

                <!-- Lines -->
                <path v-if="moisturePath" :d="moisturePath" fill="none" stroke="#7E22CE" stroke-width="3" stroke-linecap="round" />
                <path v-if="tempPath" :d="tempPath" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" />
              </svg>

              <div class="chart-time-labels">
                <span v-for="(lbl, idx) in timeLabels" :key="idx">{{ lbl.time }}</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Packaging QR Label Modal -->
        <PackagingQrLabelModal
          :is-open="isQrModalOpen"
          :batch="activeBatchFormatted"
          @close="isQrModalOpen = false"
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { telemetryService } from '../../services/telemetryService'
import { batchService } from '../../services/batchService'
import { socketService } from '../../services/socketService'
import connectionManager from '../../services/connectionManager'
import { confirmDialog, alertDialog } from '../../services/confirmDialogService'
import PackagingQrLabelModal from '../../components/PackagingQrLabelModal.vue'
import ActiveDryingSkeleton from '../../components/ActiveDryingSkeleton.vue'

defineProps({
  isMobile: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['back', 'finish'])

const isInitialLoading = ref(true)
const isPaused = ref(false)
const isQrModalOpen = ref(false)
const activeBatch = ref(null)
const seconds = ref(5400)
const formattedTime = ref('01:30:00')
let timer = null
let pollingTimer = null

const activeBatchFormatted = computed(() => {
  return {
    ...(activeBatch.value || {}),
    batchCode: activeBatch.value?.batchCode || 'HJ-2026-ACTIVE',
    cropVariety: activeBatch.value?.cropVariety || 'Biji Hanjeli Sukabumi',
    finalMoisturePercent: Number(telemetry.value.grainMoisture.toFixed(1)),
    qualityGrade: activeBatch.value?.qualityGrade || 'Grade A (SNI)',
    completedAt: activeBatch.value?.completedAt || new Date().toISOString(),
  }
})

const telemetry = ref({
  tempInternal: 44.5,
  tempExternal: 30.2,
  humidityInternal: 52.0,
  humidityExternal: 68.0,
  grainMoisture: 14.8,
  solarRadiation: 750.0,
  weightCurrentKg: 126.5,
})

const actuators = ref({
  auxHeaterStatus: false,
  controlMode: 'AUTOMATIC',
})

const liveHistoryPoints = ref([])

const progressPercent = computed(() => {
  if (!activeBatch.value) return 70
  const initial = activeBatch.value.initialMoisturePercent || 25.0
  const target = activeBatch.value.targetMoisturePercent || 12.0
  const current = telemetry.value.grainMoisture || 14.8
  if (initial <= target) return 100
  const p = ((initial - current) / (initial - target)) * 100
  return Math.min(100, Math.max(0, Math.round(p)))
})

const estimatedHoursRemaining = computed(() => {
  const diff = (telemetry.value.grainMoisture || 14.8) - (activeBatch.value?.targetMoisturePercent || 12.0)
  if (diff <= 0) return '0.5'
  return Math.max(0.5, diff * 0.8).toFixed(1)
})

const currentDryingRate = computed(() => {
  return '1.25'
})

function updateFormattedTime() {
  const hrs = String(Math.floor(seconds.value / 3600)).padStart(2, '0')
  const mins = String(Math.floor((seconds.value % 3600) / 60)).padStart(2, '0')
  const secs = String(seconds.value % 60).padStart(2, '0')
  formattedTime.value = `${hrs}:${mins}:${secs}`
}

function appendHistory(data) {
  const now = new Date()
  const timeFormatted = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  liveHistoryPoints.value.push({
    time: timeFormatted,
    tempInternal: Number(data.tempInternal ?? data.temp_internal ?? telemetry.value.tempInternal),
    grainMoisture: Number(data.grainMoisture ?? data.grain_moisture ?? telemetry.value.grainMoisture),
  })

  if (liveHistoryPoints.value.length > 20) {
    liveHistoryPoints.value.shift()
  }
}

function seedHistory() {
  const initial = []
  const count = 10
  const baseTime = new Date(Date.now() - count * 15 * 60000)
  for (let i = 0; i < count; i++) {
    const t = new Date(baseTime.getTime() + i * 15 * 60000)
    const progress = i / (count - 1)
    const temp = 42.0 + progress * 3.5 + Math.sin(i) * 0.4
    const moisture = 22.0 - progress * 7.2
    initial.push({
      time: t.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
      tempInternal: Number(temp.toFixed(1)),
      grainMoisture: Number(moisture.toFixed(1)),
    })
  }
  liveHistoryPoints.value = initial
}

function mapToSvg(values, minVal, maxVal, width = 720, height = 150, xOffset = 50, yOffset = 20) {
  if (!values || values.length < 2) return ''
  const stepX = width / (values.length - 1)
  return values.map((val, idx) => {
    const clamped = Math.max(minVal, Math.min(maxVal, val))
    const norm = (clamped - minVal) / (maxVal - minVal)
    const x = Math.round(xOffset + idx * stepX)
    const y = Math.round(yOffset + (1 - norm) * height)
    return `${idx === 0 ? 'M' : 'L'} ${x} ${y}`
  }).join(' ')
}

const moisturePath = computed(() => {
  return mapToSvg(liveHistoryPoints.value.map(d => d.grainMoisture), 10, 30)
})

const tempPath = computed(() => {
  return mapToSvg(liveHistoryPoints.value.map(d => d.tempInternal), 20, 60)
})

const timeLabels = computed(() => {
  const pts = liveHistoryPoints.value
  if (pts.length === 0) return []
  const count = Math.min(5, pts.length)
  const labels = []
  const stepIdx = Math.floor((pts.length - 1) / (count - 1 || 1))
  for (let i = 0; i < count; i++) {
    const idx = Math.min(i * stepIdx, pts.length - 1)
    labels.push({ time: pts[idx].time })
  }
  return labels
})

async function loadData() {
  try {
    const [activeRes, telemRes] = await Promise.allSettled([
      batchService.getActiveBatch(),
      telemetryService.getCurrent(),
    ])

    if (activeRes.status === 'fulfilled' && activeRes.value?.activeBatch) {
      activeBatch.value = activeRes.value.activeBatch
      if (activeRes.value.activeBatch.startedAt) {
        const start = new Date(activeRes.value.activeBatch.startedAt)
        const diffSecs = Math.max(0, Math.floor((new Date() - start) / 1000))
        seconds.value = diffSecs
        updateFormattedTime()
      }
    }

    if (telemRes.status === 'fulfilled' && telemRes.value) {
      const res = telemRes.value
      if (res.telemetry) {
        telemetry.value = { ...telemetry.value, ...res.telemetry }
        appendHistory(res.telemetry)
      }
      if (res.actuators) {
        actuators.value = { ...actuators.value, ...res.actuators }
      }
    }
  } catch (err) {
    console.warn('Active drying data load fallback:', err.message)
  }
}

async function handleTogglePause() {
  if (!activeBatch.value?.id) return
  try {
    if (isPaused.value) {
      await batchService.resume(activeBatch.value.id)
      isPaused.value = false
    } else {
      await batchService.pause(activeBatch.value.id)
      isPaused.value = true
    }
  } catch (e) {
    await alertDialog({
      title: 'Perhatian',
      message: e.message || 'Gagal mengubah status proses pengeringan.',
      type: 'danger',
    })
  }
}

async function handleFinishDrying() {
  const confirmed = await confirmDialog({
    title: 'Selesaikan Pengeringan?',
    message: 'Apakah Anda yakin ingin menyelesaikan proses pengeringan ini? Data akhir akan disimpan ke buku riwayat.',
    details: 'Pastikan biji hanjeli sudah mencapai tingkat kekeringan yang diinginkan.',
    type: 'success',
    confirmText: 'Ya, Selesaikan',
    cancelText: 'Belum, Lanjutkan',
  })

  if (!confirmed) return

  if (activeBatch.value?.id) {
    try {
      await batchService.complete(activeBatch.value.id, {
        finalWeightKg: telemetry.value.weightCurrentKg || activeBatch.value.currentWeightKg || 120.0,
        finalMoisturePercent: telemetry.value.grainMoisture || activeBatch.value.currentMoisturePercent || 12.0,
        notes: 'Pengeringan selesai sesuai target.',
      })
    } catch (e) {
      console.warn('Finish batch notice:', e.message)
    }
  }
  emit('finish')
}

onMounted(async () => {
  seedHistory()
  await loadData()
  isInitialLoading.value = false

  timer = setInterval(() => {
    if (!isPaused.value) {
      seconds.value++
      updateFormattedTime()
    }
  }, 1000)

  pollingTimer = setInterval(loadData, 3000)

  socketService.on('telemetry_live', (data) => {
    if (data) {
      telemetry.value = { ...telemetry.value, ...data }
      appendHistory(data)
    }
  })

  socketService.on('actuators_update', (data) => {
    if (data) actuators.value = { ...actuators.value, ...data }
  })

  connectionManager.on('telemetry_live', (data) => {
    if (data && (data.hasData || data.tempInternal > 0)) {
      telemetry.value = { ...telemetry.value, ...data }
      appendHistory(data)
    }
  })
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
  if (pollingTimer) clearInterval(pollingTimer)
  socketService.off('telemetry_live')
  socketService.off('actuators_update')
  connectionManager.off('telemetry_live')
})
</script>

<style scoped>
.active-drying-page {
  width: 100%;
  min-height: 100%;
}

.active-drying-main-content {
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
  margin: 0;
  box-sizing: border-box;
}

/* 1. Active Header Card */
.active-header-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  flex-wrap: wrap;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.back-link-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #64748B;
  background: transparent;
  padding: 0;
  margin-bottom: 12px;
}

.back-link-btn:hover {
  color: #1B5E20;
}

.batch-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 6px;
}

.batch-title {
  font-size: 22px;
  font-weight: 700;
  color: #0F291E;
}

.batch-code-badge {
  background: #F1F5F9;
  color: #475569;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
}

.batch-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.status-running {
  background: #DCFCE7;
  color: #166534;
}

.status-paused {
  background: #FEF3C7;
  color: #B45309;
}

.status-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: currentColor;
}

.batch-operator-info {
  font-size: 13px;
  color: #64748B;
  margin: 0;
}

.header-actions-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: flex-end;
}

.btn-finish-main {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #1B5E20;
  color: #FFFFFF;
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-finish-main:hover {
  background: #144A18;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(27, 94, 32, 0.25);
}

.header-secondary-actions {
  display: flex;
  gap: 8px;
}

.btn-secondary-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  color: #334155;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary-action:hover {
  background: #EDF2F7;
  color: #0F291E;
}

/* 2. Progress Card */
.drying-progress-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.progress-top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  padding-bottom: 20px;
  border-bottom: 1px solid #F1F5F9;
  margin-bottom: 20px;
}

.prog-time-box {
  display: flex;
  align-items: center;
  gap: 14px;
}

.clock-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-small-label {
  font-size: 11px;
  font-weight: 700;
  color: #94A3B8;
  letter-spacing: 0.5px;
  display: block;
}

.clock-display {
  font-size: 26px;
  font-weight: 800;
  color: #0F291E;
  letter-spacing: 0.5px;
}

.eta-val {
  font-size: 20px;
  font-weight: 700;
  color: #D97706;
}

.rate-val {
  font-size: 20px;
  font-weight: 700;
}

.text-green {
  color: #1B5E20;
}

.progress-track-wrapper {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.track-info-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: #475569;
  flex-wrap: wrap;
  gap: 8px;
}

.track-badge-current {
  color: #1B5E20;
  font-weight: 600;
}

.progress-bar-container {
  height: 12px;
  background: #F1F5F9;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #1B5E20 0%, #22C55E 100%);
  border-radius: 9999px;
  transition: width 0.5s ease;
}

/* 3. KPI Grid */
.active-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

@media (max-width: 1024px) {
  .active-kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .active-kpi-grid {
    grid-template-columns: 1fr;
  }
}

.active-kpi-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.kpi-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.kpi-title {
  font-size: 13px;
  font-weight: 600;
  color: #64748B;
}

.icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-amber { background: #FEF3C7; color: #D97706; }
.icon-blue { background: #E0F2FE; color: #0284C7; }
.icon-purple { background: #F3E8FF; color: #7E22CE; }
.icon-green { background: #E8F5E9; color: #1B5E20; }

.kpi-val-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin-bottom: 10px;
}

.kpi-num {
  font-size: 32px;
  font-weight: 800;
  color: #0F291E;
  line-height: 1;
}

.text-purple { color: #7E22CE; }
.text-amber { color: #D97706; }

.kpi-unit {
  font-size: 15px;
  font-weight: 600;
  color: #64748B;
}

.kpi-status-text {
  font-size: 20px;
  font-weight: 800;
}

.badge-chip {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
}

.chip-green { background: #E8F5E9; color: #1B5E20; }
.chip-amber { background: #FEF3C7; color: #B45309; }
.chip-red { background: #FEE2E2; color: #DC2626; }
.chip-blue { background: #E0F2FE; color: #0369A1; }
.chip-purple { background: #F3E8FF; color: #6B21A8; }
.chip-neutral { background: #F1F5F9; color: #475569; }

/* 4. Chart Card */
.active-chart-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.chart-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 18px;
}

.chart-heading {
  font-size: 18px;
  font-weight: 700;
  color: #0F291E;
  margin-bottom: 4px;
}

.chart-desc {
  font-size: 13px;
  color: #64748B;
  margin: 0;
}

.chart-legend-box {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.legend-chip {
  display: flex;
  align-items: center;
  gap: 6px;
}

.color-dot {
  width: 12px;
  height: 4px;
  border-radius: 2px;
}

.dot-purple { background: #7E22CE; }
.dot-red { background: #DC2626; }

.chart-canvas-box {
  width: 100%;
}

.active-svg {
  width: 100%;
  height: 180px;
  display: block;
}

.chart-time-labels {
  display: flex;
  justify-content: space-between;
  padding: 8px 45px 0 45px;
  font-size: 11px;
  color: #94A3B8;
  font-weight: 500;
}

/* Responsive */
@media (max-width: 900px) {
  .active-drying-main-content {
    padding: 0;
    gap: 12px;
  }

  .active-header-card,
  .progress-drying-card,
  .active-chart-card {
    padding: 16px;
    border-radius: 14px;
  }

  .batch-title {
    font-size: 18px;
  }

  .header-actions-group {
    width: 100%;
    align-items: stretch;
  }

  .btn-finish-main {
    width: 100%;
    justify-content: center;
  }

  .header-secondary-actions {
    width: 100%;
    justify-content: space-between;
  }

  .btn-secondary-action {
    flex: 1;
    justify-content: center;
  }

  .progress-top-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 14px;
  }

  .active-svg {
    height: 150px;
  }
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR ACTIVE DRYING VIEW
   ========================================================================== */
:global(.dark-theme) .active-header-card,
:global(.dark-theme) .progress-drying-card,
:global(.dark-theme) .progress-stat-card,
:global(.dark-theme) .drying-kpi-card,
:global(.dark-theme) .active-chart-card {
  background: #0B242F;
  border-color: #1E4E61;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

:global(.dark-theme) .batch-title,
:global(.dark-theme) .kpi-num,
:global(.dark-theme) .chart-heading,
:global(.dark-theme) .prog-big-number,
:global(.dark-theme) .target-big-num {
  color: #F8FAFC;
}

:global(.dark-theme) .batch-code-badge {
  background: #113847;
  color: #CBD5E1;
}

:global(.dark-theme) .back-link-btn {
  color: #94A3B8;
}

:global(.dark-theme) .back-link-btn:hover {
  color: #4ADE80;
}

:global(.dark-theme) .kpi-title,
:global(.dark-theme) .kpi-unit,
:global(.dark-theme) .chart-desc,
:global(.dark-theme) .chart-time-labels,
:global(.dark-theme) .stat-mini-label,
:global(.dark-theme) .progress-target-hint {
  color: #94A3B8;
}

:global(.dark-theme) .chart-legend-box {
  color: #CBD5E1;
}

:global(.dark-theme) .btn-secondary-action {
  background: #113847;
  color: #F8FAFC;
  border-color: #1E4E61;
}

:global(.dark-theme) .btn-secondary-action:hover {
  background: #164657;
  border-color: #2D6B82;
}

:global(.dark-theme) .icon-amber { background: rgba(217, 119, 6, 0.18); color: #FBBF24; }
:global(.dark-theme) .icon-blue { background: rgba(2, 132, 199, 0.18); color: #38BDF8; }
:global(.dark-theme) .icon-purple { background: rgba(126, 34, 206, 0.18); color: #C084FC; }
:global(.dark-theme) .icon-green { background: rgba(27, 94, 32, 0.25); color: #4ADE80; }

:global(.dark-theme) .chip-green { background: rgba(27, 94, 32, 0.25); color: #4ADE80; }
:global(.dark-theme) .chip-amber { background: rgba(217, 119, 6, 0.25); color: #FBBF24; }
:global(.dark-theme) .chip-red { background: rgba(220, 38, 38, 0.25); color: #F87171; }
:global(.dark-theme) .chip-blue { background: rgba(2, 132, 199, 0.25); color: #38BDF8; }
:global(.dark-theme) .chip-purple { background: rgba(126, 34, 206, 0.25); color: #C084FC; }
:global(.dark-theme) .chip-neutral { background: #113847; color: #CBD5E1; }

:global(.dark-theme) .active-svg line[stroke="#E2E8F0"] {
  stroke: #1E4E61;
}

:global(.dark-theme) .active-svg text[fill="#94A3B8"] {
  fill: #64748B;
}

:global(.dark-theme) .active-svg rect[fill="#F3E8FF"] {
  fill: rgba(126, 34, 206, 0.2);
}

:global(.dark-theme) .active-svg text[fill="#7E22CE"] {
  fill: #C084FC;
}

/* Callout Model Estimasi */
.drying-model-notice {
  margin-top: 14px;
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.dmn-icon {
  color: #166534;
  flex-shrink: 0;
  margin-top: 2px;
}

.dmn-content {
  flex: 1;
}

.dmn-title {
  font-size: 13px;
  font-weight: 700;
  color: #166534;
  margin-bottom: 2px;
}

.dmn-text {
  font-size: 12px;
  color: #1F2937;
  line-height: 1.45;
  margin: 0;
}

:global(.dark-theme) .drying-model-notice {
  background: rgba(22, 101, 52, 0.2);
  border-color: rgba(34, 197, 94, 0.3);
}

:global(.dark-theme) .dmn-icon,
:global(.dark-theme) .dmn-title {
  color: #86EFAC;
}

:global(.dark-theme) .dmn-text {
  color: #E2E8F0;
}
</style>
