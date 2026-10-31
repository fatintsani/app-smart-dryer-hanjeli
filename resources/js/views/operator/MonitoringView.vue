<template>
  <div class="monitoring-page">
    <Transition name="fade-monitoring" mode="out-in">
      <MonitoringSkeleton v-if="isInitialLoading" key="skeleton" />
      <div v-else class="monitoring-loaded-container" key="content">
        <div class="monitoring-main-container">
          
          <!-- 1. Header Bar: Status Alat & Tombol Unduh -->
          <div class="monitoring-header-card">
            <div class="header-left">
              <div class="header-status-pill" :class="connectionStatusClass">
                <span class="status-dot"></span>
                <span>{{ connectionStatusLabel }}</span>
              </div>
              <h1 class="page-title">Pemantauan Sensor Ruang Pengering</h1>
              <p class="page-subtitle">
                Pantau pembacaan sensor suhu, kelembapan, cahaya matahari, dan kendali alat pengering.
              </p>
            </div>

            <div class="header-right">
              <button class="btn-export-csv" @click="exportTelemetryCsv" title="Unduh data pengukuran ke file CSV">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="7 10 12 15 17 10"></polyline>
                  <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Unduh Data (CSV)</span>
              </button>
            </div>
          </div>

          <!-- 2. Kartu Indikator Sensor Ruangan (4 Kartu Utama) -->
          <div class="sensor-cards-grid">
            
            <!-- Suhu Ruang -->
            <div class="sensor-card">
              <div class="card-head">
                <span class="card-label">Suhu Ruang Pengering</span>
                <div class="icon-wrap icon-amber">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="card-value-row">
                <span class="card-big-val">{{ isDataActive ? telemetry.tempInternal.toFixed(1) : '--' }}</span>
                <span class="card-unit">°C</span>
              </div>
              <div class="card-status-badge">
                <span class="status-chip" :class="tempChipClass">
                  {{ tempChipLabel }}
                </span>
              </div>
            </div>

            <!-- Kelembapan Udara -->
            <div class="sensor-card">
              <div class="card-head">
                <span class="card-label">Kelembapan Udara (RH)</span>
                <div class="icon-wrap icon-blue">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2.2">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="card-value-row">
                <span class="card-big-val">{{ isDataActive ? telemetry.humidityInternal.toFixed(0) : '--' }}</span>
                <span class="card-unit">%</span>
              </div>
              <div class="card-status-badge">
                <span class="status-chip" :class="telemetry.humidityInternal <= 60 ? 'chip-green' : 'chip-blue'">
                  {{ isDataActive ? (telemetry.humidityInternal <= 60 ? 'Kering Terkendali' : 'Cukup Lembap') : 'Menunggu' }}
                </span>
              </div>
            </div>

            <!-- Sinar Matahari / Cahaya -->
            <div class="sensor-card">
              <div class="card-head">
                <span class="card-label">Intensitas Cahaya Matahari</span>
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
              <div class="card-value-row">
                <span class="card-big-val">{{ isDataActive ? telemetry.solarRadiation.toFixed(0) : '--' }}</span>
                <span class="card-unit">Lux</span>
              </div>
              <div class="card-status-badge">
                <span class="status-chip" :class="telemetry.solarRadiation >= 150 ? 'chip-amber' : 'chip-neutral'">
                  {{ isDataActive ? (telemetry.solarRadiation >= 150 ? 'Cerah Terik' : 'Redup / Malam') : 'Menunggu' }}
                </span>
              </div>
            </div>

            <!-- Sensor Hujan -->
            <div class="sensor-card">
              <div class="card-head">
                <span class="card-label">Sensor Pendeteksi Hujan</span>
                <div class="icon-wrap icon-blue">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2.2">
                    <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
                    <line x1="8" y1="19" x2="8" y2="21"></line>
                    <line x1="16" y1="19" x2="16" y2="21"></line>
                  </svg>
                </div>
              </div>
              <div class="card-value-row">
                <span class="card-text-val" :class="telemetry.isRaining ? 'text-blue' : 'text-green'">
                  {{ isDataActive ? (telemetry.isRaining ? 'HUJAN' : 'KERING') : '--' }}
                </span>
              </div>
              <div class="card-status-badge">
                <span class="status-chip" :class="telemetry.isRaining ? 'chip-blue' : 'chip-green'">
                  {{ isDataActive ? (telemetry.isRaining ? 'Pemanas Tambahan Siaga' : 'Cuaca Kering Aman') : 'Menunggu' }}
                </span>
              </div>
            </div>

          </div>

          <!-- 3. Pusat Kendali Alat & Pemanas (Actuator Control) -->
          <div class="actuator-control-section">
            <ActuatorControlCard :current-lang="currentLang" :telemetry-data="telemetry" />
          </div>

          <!-- 4. Analisis Statistik Suhu & Kelembapan (1 Hari, 1 Minggu, 1 Bulan, Semua) -->
          <div class="monitoring-chart-card analysis-chart-card">
            <div class="chart-header">
              <div class="chart-header-left">
                <div class="chart-title-row">
                  <h2 class="chart-title">Perkembangan Suhu & Kelembapan</h2>
                  <span class="period-active-chip">{{ periodLabel }}</span>
                </div>
                <p class="chart-subtitle">
                  Grafik kestabilan suhu dan kelembapan ruang pengering selama proses.
                </p>
              </div>

              <!-- Timeframe Selector Tabs & Export Action -->
              <div class="chart-actions-toolbar">
                <div class="timeframe-pill-selector">
                  <button 
                    type="button" 
                    class="timeframe-btn" 
                    :class="{ active: selectedPeriod === '1d' }"
                    @click="setPeriod('1d')"
                  >
                    1 Hari
                  </button>
                  <button 
                    type="button" 
                    class="timeframe-btn" 
                    :class="{ active: selectedPeriod === '1w' }"
                    @click="setPeriod('1w')"
                  >
                    1 Minggu
                  </button>
                  <button 
                    type="button" 
                    class="timeframe-btn" 
                    :class="{ active: selectedPeriod === '1m' }"
                    @click="setPeriod('1m')"
                  >
                    1 Bulan
                  </button>
                  <button 
                    type="button" 
                    class="timeframe-btn" 
                    :class="{ active: selectedPeriod === 'all' }"
                    @click="setPeriod('all')"
                  >
                    Semua
                  </button>
                </div>

                <button 
                  type="button" 
                  class="btn-chart-export" 
                  @click="exportPeriodCsv" 
                  :title="`Unduh data analisis format CSV (${periodLabel})`"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>Unduh CSV</span>
                </button>
              </div>
            </div>

            <!-- Ringkasan Metrik Ringkas & User-Friendly (Simple & Clean) -->
            <div class="simple-metric-badges">
              <div class="metric-chip">
                <span class="metric-dot dot-red"></span>
                <span class="metric-name">Rata-rata Suhu:</span>
                <strong class="metric-val text-red">{{ analyticsData.avgTemp }}°C</strong>
                <span class="metric-range">({{ analyticsData.minTemp }}° – {{ analyticsData.maxTemp }}°)</span>
              </div>

              <div class="metric-chip">
                <span class="metric-dot dot-blue"></span>
                <span class="metric-name">Rata-rata Kelembapan:</span>
                <strong class="metric-val text-blue">{{ analyticsData.avgHumidity }}% RH</strong>
              </div>

              <div class="metric-chip">
                <span class="metric-dot dot-green"></span>
                <span class="metric-name">Suhu Ideal SNI:</span>
                <strong class="metric-val text-green">{{ analyticsData.idealRatioPercent }}% Optimal</strong>
              </div>

              <div class="metric-chip">
                <span class="metric-dot dot-amber"></span>
                <span class="metric-name">Aktivitas Pemanas:</span>
                <strong class="metric-val text-amber">{{ analyticsData.heaterActivePercent }}% Sesi</strong>
              </div>
            </div>

            <!-- Legend & Hint Row -->
            <div class="chart-legend-bar">
              <div class="chart-legend-row">
                <span class="legend-pill"><span class="legend-color-dot dot-red"></span> Suhu Ruang (°C)</span>
                <span class="legend-pill"><span class="legend-color-dot dot-blue"></span> Kelembapan Udara (%)</span>
                <span class="legend-pill"><span class="legend-color-dot dot-green"></span> Suhu Ideal SNI (40°C – 50°C)</span>
              </div>
              <span class="chart-hint-text">Arahkan kursor atau sentuh kurva untuk melihat rincian waktu</span>
            </div>

            <!-- Chart Canvas Layout: Y-Axis + SVG Canvas + Overlay Badges -->
            <div class="chart-body-layout">
              <!-- Y-Axis Labels (Pure HTML, Bebas Distorsi Font) -->
              <div class="chart-y-axis">
                <span class="y-axis-lbl y-80">80°</span>
                <span class="y-axis-lbl y-60">60°</span>
                <span class="y-axis-lbl y-40">40°</span>
                <span class="y-axis-lbl y-20">20°</span>
              </div>

              <!-- Main SVG Canvas Wrapper -->
              <div 
                class="chart-svg-box"
                ref="monitoringChartWrapperRef"
                @mousemove="handleMonitoringMouseMove"
                @mouseleave="handleMonitoringMouseLeave"
                @touchstart.passive="handleMonitoringTouch"
                @touchmove.passive="handleMonitoringTouch"
              >
                <!-- Badge Suhu Ideal (Pure HTML Pill, Tidak Melar / Gepeng) -->
                <div class="ideal-zone-overlay">
                  <span class="ideal-zone-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Suhu Ideal (40°C – 50°C)</span>
                  </span>
                </div>

                <svg viewBox="0 0 1000 240" class="monitoring-svg" preserveAspectRatio="none">
                  <!-- Area Hijau Suhu Ideal (40 - 50°C) -->
                  <rect x="0" y="110" width="1000" height="30" fill="#1B5E20" fill-opacity="0.08" />

                  <!-- Gridlines -->
                  <line x1="0" y1="20" x2="1000" y2="20" stroke="#E2E8F0" stroke-dasharray="4 4" class="chart-grid-line" />
                  <line x1="0" y1="80" x2="1000" y2="80" stroke="#E2E8F0" stroke-dasharray="4 4" class="chart-grid-line" />
                  <line x1="0" y1="140" x2="1000" y2="140" stroke="#E2E8F0" stroke-dasharray="4 4" class="chart-grid-line" />
                  <line x1="0" y1="200" x2="1000" y2="200" stroke="#CBD5E1" class="chart-baseline" />

                  <!-- Paths -->
                  <path v-if="humLinePath" :d="humLinePath" fill="none" stroke="#0284C7" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" />
                  <path v-if="tempLinePath" :d="tempLinePath" fill="none" stroke="#DC2626" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" />

                  <!-- Hover Guidance Line & Highlighted Points -->
                  <g v-if="activeMonitoringHoverPoint" class="chart-hover-elements">
                    <line 
                      :x1="activeMonitoringHoverPoint.x" 
                      y1="20" 
                      :x2="activeMonitoringHoverPoint.x" 
                      y2="200" 
                      stroke="#64748B" 
                      stroke-width="1.5" 
                      stroke-dasharray="4 4" 
                      opacity="0.8"
                    />
                    <!-- Kelembapan Point (Blue) -->
                    <circle :cx="activeMonitoringHoverPoint.x" :cy="activeMonitoringHoverPoint.yHum" r="8" fill="#0284C7" fill-opacity="0.25" />
                    <circle :cx="activeMonitoringHoverPoint.x" :cy="activeMonitoringHoverPoint.yHum" r="4.5" fill="#FFFFFF" stroke="#0284C7" stroke-width="2.5" />
                    <!-- Suhu Point (Red) -->
                    <circle :cx="activeMonitoringHoverPoint.x" :cy="activeMonitoringHoverPoint.yTemp" r="8" fill="#DC2626" fill-opacity="0.25" />
                    <circle :cx="activeMonitoringHoverPoint.x" :cy="activeMonitoringHoverPoint.yTemp" r="4.5" fill="#FFFFFF" stroke="#DC2626" stroke-width="2.5" />
                  </g>
                </svg>

                <!-- Floating Tooltip Card -->
                <Transition name="fade-tooltip">
                  <div 
                    v-if="activeMonitoringHoverPoint" 
                    class="chart-tooltip-bubble"
                    :style="{
                      left: `${activeMonitoringHoverPoint.percentX}%`,
                      transform: activeMonitoringHoverPoint.percentX < 20 
                        ? 'translateX(0%)' 
                        : activeMonitoringHoverPoint.percentX > 80 
                          ? 'translateX(-100%)' 
                          : 'translateX(-50%)'
                    }"
                  >
                    <div class="tooltip-time-row">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                      <span>{{ activeMonitoringHoverPoint.fullDate ? activeMonitoringHoverPoint.fullDate : `Pukul ${activeMonitoringHoverPoint.time} WIB` }}</span>
                    </div>

                    <div class="tooltip-metric-row">
                      <div class="tooltip-metric-left">
                        <span class="metric-dot dot-red"></span>
                        <span class="metric-title">Suhu:</span>
                      </div>
                      <div class="tooltip-metric-right">
                        <strong class="metric-value text-red">{{ activeMonitoringHoverPoint.tempInternal }}°C</strong>
                        <span 
                          class="status-chip"
                          :class="activeMonitoringHoverPoint.tempInternal >= 40 && activeMonitoringHoverPoint.tempInternal <= 50 ? 'chip-ideal' : (activeMonitoringHoverPoint.tempInternal < 40 ? 'chip-low' : 'chip-high')"
                        >
                          {{ activeMonitoringHoverPoint.tempInternal >= 40 && activeMonitoringHoverPoint.tempInternal <= 50 ? 'Ideal' : (activeMonitoringHoverPoint.tempInternal < 40 ? 'Rendah' : 'Tinggi') }}
                        </span>
                      </div>
                    </div>

                    <div class="tooltip-metric-row">
                      <div class="tooltip-metric-left">
                        <span class="metric-dot dot-blue"></span>
                        <span class="metric-title">Kelembapan:</span>
                      </div>
                      <div class="tooltip-metric-right">
                        <strong class="metric-value text-blue">{{ activeMonitoringHoverPoint.humidityInternal }}%</strong>
                        <span class="metric-sub">RH</span>
                      </div>
                    </div>
                  </div>
                </Transition>
              </div>
            </div>

            <!-- Time Labels -->
            <div class="chart-time-row">
              <span v-for="(lbl, idx) in timeLabels" :key="idx">{{ lbl.time }}</span>
            </div>
          </div>

        </div>
      </div>
    </Transition>

    <!-- Wi-Fi Setup Modal -->
    <DeviceSetupBleModal :is-open="isWifiSetupOpen" @close="isWifiSetupOpen = false" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { telemetryService } from '../../services/telemetryService'
import { socketService } from '../../services/socketService'
import connectionManager from '../../services/connectionManager'
import ActuatorControlCard from '../../components/ActuatorControlCard.vue'
import DeviceSetupBleModal from '../../components/DeviceSetupBleModal.vue'
import MonitoringSkeleton from '../../components/MonitoringSkeleton.vue'

defineProps({
  currentLang: {
    type: String,
    default: 'id'
  }
})

const isInitialLoading = ref(true)
const isWifiSetupOpen = ref(false)

const telemetry = ref({
  tempInternal: 0,
  humidityInternal: 0,
  solarRadiation: 0,
  isRaining: false,
  heaterStatus: false,
  heaterReason: null,
  grainMoisture: 0,
  weightCurrentKg: 0,
  hasData: false,
})

const actuators = ref({
  auxHeaterStatus: false,
  controlMode: 'AUTOMATIC',
})

const livePoints = ref([])

const isHardwareActive = computed(() => {
  return Boolean(connectionManager.state.isHardwareActive || telemetry.value.isLive)
})

const isDataActive = computed(() => {
  return Boolean(isHardwareActive.value && (telemetry.value.tempInternal > 0 || telemetry.value.humidityInternal > 0))
})

const connectionStatusLabel = computed(() => {
  if (isHardwareActive.value) return 'Alat Sensor Terhubung (Live)'
  return 'Alat Sensor Belum Terhubung'
})

const connectionStatusClass = computed(() => {
  if (isHardwareActive.value) return 'status-online'
  return 'status-offline'
})

const tempChipLabel = computed(() => {
  if (!isDataActive.value) return 'Menunggu Alat'
  const t = telemetry.value.tempInternal
  if (t >= 50) return 'Panas (≥50°C)'
  if (t >= 40) return 'Optimal (40-50°C)'
  return 'Hangat'
})

const tempChipClass = computed(() => {
  if (!isDataActive.value) return 'chip-neutral'
  const t = telemetry.value.tempInternal
  if (t >= 50) return 'chip-red'
  if (t >= 40) return 'chip-green'
  return 'chip-amber'
})

// Timeframe & Analisis Multivariat (1 Hari, 1 Minggu, 1 Bulan, Semua)
const selectedPeriod = ref('1d')
const isLoadingPeriod = ref(false)

const analyticsData = ref({
  totalPoints: 0,
  avgTemp: '44.5',
  minTemp: '38.0',
  maxTemp: '49.0',
  avgHumidity: '58',
  minHumidity: '42',
  maxHumidity: '68',
  idealRatioPercent: 88,
  heaterActivePercent: 24,
  stabilityLabel: 'Sangat Stabil (Standar SNI)',
  insightMessage: 'Suhu ruang pengering terkendali stabil di zona ideal SNI (40°C - 50°C). Gabah kering merata tanpa retak termal.',
})

const periodLabel = computed(() => {
  switch (selectedPeriod.value) {
    case '1d': return '24 Jam Terakhir'
    case '1w': return '7 Hari Terakhir'
    case '1m': return '30 Hari Terakhir'
    case 'all': return 'Seluruh Riwayat'
    default: return '24 Jam Terakhir'
  }
})

async function setPeriod(period) {
  if (selectedPeriod.value === period && !isLoadingPeriod.value) return
  selectedPeriod.value = period
  await loadPeriodHistory(period)
}

async function loadPeriodHistory(period = '1d') {
  isLoadingPeriod.value = true
  try {
    const res = await telemetryService.getHistory(period)
    let records = []
    if (res && res.records && Array.isArray(res.records)) {
      records = res.records
      if (res.analytics) {
        analyticsData.value = {
          ...res.analytics,
          avgTemp: Number(res.analytics.avgTemp || 0).toFixed(1),
          minTemp: Number(res.analytics.minTemp || 0).toFixed(1),
          maxTemp: Number(res.analytics.maxTemp || 0).toFixed(1),
          avgHumidity: Math.round(Number(res.analytics.avgHumidity || 0)),
          minHumidity: Math.round(Number(res.analytics.minHumidity || 0)),
          maxHumidity: Math.round(Number(res.analytics.maxHumidity || 0)),
          idealRatioPercent: Math.round(Number(res.analytics.idealRatioPercent || 0)),
          heaterActivePercent: Math.round(Number(res.analytics.heaterActivePercent || 0)),
        }
      }
    } else if (Array.isArray(res)) {
      records = res
    }

    if (records.length > 0) {
      livePoints.value = records.map(r => ({
        time: r.label || (r.timestamp ? new Date(r.timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '--:--'),
        fullDate: r.fullDate || (r.timestamp ? new Date(r.timestamp).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) + ' WIB' : ''),
        tempInternal: Number(Number(r.tempInternal ?? r.temp_internal ?? 0).toFixed(1)),
        humidityInternal: Number(Math.round(Number(r.humidityInternal ?? r.humidity_internal ?? 0))),
        heaterStatus: Boolean(r.heaterStatus ?? r.heater_status),
      }))

      if (!res?.analytics) {
        computeClientAnalytics(livePoints.value, period)
      }
    } else {
      seedPoints()
      computeClientAnalytics(livePoints.value, period)
    }
  } catch (err) {
    console.warn('Gagal memuat histori periode:', err)
    if (livePoints.value.length === 0) {
      seedPoints()
      computeClientAnalytics(livePoints.value, period)
    }
  } finally {
    isLoadingPeriod.value = false
  }
}

function computeClientAnalytics(points, period) {
  if (!points || points.length === 0) return
  const count = points.length
  const temps = points.map(p => Number(p.tempInternal || 0))
  const hums = points.map(p => Number(p.humidityInternal || 0))

  const sumT = temps.reduce((a, b) => a + b, 0)
  const sumH = hums.reduce((a, b) => a + b, 0)

  const avgT = (sumT / count).toFixed(1)
  const minT = Math.min(...temps).toFixed(1)
  const maxT = Math.max(...temps).toFixed(1)

  const avgH = Math.round(sumH / count)
  const minH = Math.round(Math.min(...hums))
  const maxH = Math.round(Math.max(...hums))

  const idealCount = temps.filter(t => t >= 40 && t <= 50).length
  const idealRatio = Math.round((idealCount / count) * 100)

  const heaterCount = points.filter(p => p.heaterStatus).length
  const heaterRatio = Math.round((heaterCount / count) * 100)

  let stability = 'Optimal & Sesuai SNI'
  if (idealRatio < 50) {
    stability = 'Perlu Penyesuaian Pemanas'
  } else if (idealRatio < 80) {
    stability = 'Cukup Stabil'
  }

  const pName = period === '1w' ? '1 Minggu' : (period === '1m' ? '1 Bulan' : (period === 'all' ? 'Seluruh Riwayat' : '24 Jam Terakhir'))
  const insight = `Dalam periode ${pName}, ${idealRatio}% waktu suhu ruangan berada di zona ideal SNI (40°C - 50°C). Suhu terendah ${minT}°C dan tertinggi ${maxT}°C dengan rata-rata kelembapan ${avgH}%.`

  analyticsData.value = {
    totalPoints: count,
    avgTemp: avgT,
    minTemp: minT,
    maxTemp: maxT,
    avgHumidity: avgH,
    minHumidity: minH,
    maxHumidity: maxH,
    idealRatioPercent: idealRatio,
    heaterActivePercent: heaterRatio,
    stabilityLabel: stability,
    insightMessage: insight,
  }
}

function exportPeriodCsv() {
  const points = livePoints.value
  if (!points || points.length === 0) {
    alert('Tidak ada data telemetri yang tersedia untuk diunduh.')
    return
  }

  const periodSlug = selectedPeriod.value
  const dateStr = new Date().toISOString().slice(0, 10)
  const filename = `Analisis_Monitoring_${periodSlug}_${dateStr}.csv`

  const headers = ['Waktu', 'Suhu Internal (C)', 'Kelembapan Internal (%)', 'Status Suhu SNI', 'Status Pemanas']
  const rows = points.map(pt => {
    const isIdeal = pt.tempInternal >= 40 && pt.tempInternal <= 50 ? 'Ideal SNI' : (pt.tempInternal < 40 ? 'Kurang Panas' : 'Terlalu Panas')
    const heater = pt.heaterStatus ? 'AKTIF' : 'MATI'
    return [
      `"${pt.fullDate || pt.time}"`,
      pt.tempInternal,
      pt.humidityInternal,
      `"${isIdeal}"`,
      `"${heater}"`
    ].join(',')
  })

  const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + [headers.join(','), ...rows].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', filename)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

function exportTelemetryCsv() {
  exportPeriodCsv()
}

function pushLivePoint(data) {
  if (selectedPeriod.value !== '1d') return
  if (!data || (!data.isLive && !connectionManager.state.isHardwareActive)) {
    return
  }
  const now = new Date()
  const timeFormatted = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  const fullDateFormatted = now.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) + ' WIB'

  const temp = Number(data.tempInternal ?? data.temp_internal ?? telemetry.value.tempInternal)
  const hum = Number(data.humidityInternal ?? data.humidity_internal ?? telemetry.value.humidityInternal)
  const heater = Boolean(data.heaterStatus ?? data.heater_status ?? telemetry.value.heaterStatus)

  livePoints.value.push({
    time: timeFormatted,
    fullDate: fullDateFormatted,
    tempInternal: Number(temp.toFixed(1)),
    humidityInternal: Number(hum.toFixed(0)),
    heaterStatus: heater,
  })

  if (livePoints.value.length > 60) {
    livePoints.value.shift()
  }
  computeClientAnalytics(livePoints.value, '1d')
}

function seedPoints() {
  livePoints.value = []
}

function mapToSvg(values, minVal, maxVal, width = 1000, height = 180, xOffset = 0, yOffset = 20) {
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

const tempLinePath = computed(() => {
  return mapToSvg(livePoints.value.map(d => d.tempInternal), 20, 80, 1000, 180, 0, 20)
})

const humLinePath = computed(() => {
  return mapToSvg(livePoints.value.map(d => d.humidityInternal), 20, 80, 1000, 180, 0, 20)
})

const timeLabels = computed(() => {
  const pts = livePoints.value
  if (pts.length === 0) return []
  const count = Math.min(6, pts.length)
  const labels = []
  const stepIdx = Math.floor((pts.length - 1) / (count - 1 || 1))
  for (let i = 0; i < count; i++) {
    const idx = Math.min(i * stepIdx, pts.length - 1)
    labels.push({ time: pts[idx].time })
  }
  return labels
})

// Interaktif Hover Tooltip untuk Monitoring Tren
const hoveredMonitoringIndex = ref(null)
const monitoringChartWrapperRef = ref(null)

const monitoringCoordinates = computed(() => {
  const pts = livePoints.value
  if (!pts || pts.length < 2) return []
  const width = 1000
  const height = 180
  const xOffset = 0
  const yOffset = 20
  const minVal = 20
  const maxVal = 80
  const stepX = width / (pts.length - 1)

  return pts.map((pt, idx) => {
    const x = Math.round(xOffset + idx * stepX)
    const clampTemp = Math.max(minVal, Math.min(maxVal, pt.tempInternal ?? 0))
    const normTemp = (clampTemp - minVal) / (maxVal - minVal)
    const yTemp = Math.round(yOffset + (1 - normTemp) * height)

    const clampHum = Math.max(minVal, Math.min(maxVal, pt.humidityInternal ?? 0))
    const normHum = (clampHum - minVal) / (maxVal - minVal)
    const yHum = Math.round(yOffset + (1 - normHum) * height)

    const percentX = (x / 1000) * 100

    return {
      ...pt,
      idx,
      x,
      yTemp,
      yHum,
      percentX,
    }
  })
})

const activeMonitoringHoverPoint = computed(() => {
  if (hoveredMonitoringIndex.value === null) return null
  return monitoringCoordinates.value[hoveredMonitoringIndex.value] || null
})

function handleMonitoringMouseMove(event) {
  if (!monitoringCoordinates.value.length || !monitoringChartWrapperRef.value) return
  const rect = monitoringChartWrapperRef.value.getBoundingClientRect()
  if (!rect.width) return

  const clientX = event.clientX !== undefined ? event.clientX : (event.touches && event.touches[0] ? event.touches[0].clientX : null)
  if (clientX === null) return

  const mouseSvgX = ((clientX - rect.left) / rect.width) * 1000

  let closestIdx = 0
  let minDist = Infinity
  monitoringCoordinates.value.forEach((pt, idx) => {
    const dist = Math.abs(pt.x - mouseSvgX)
    if (dist < minDist) {
      minDist = dist
      closestIdx = idx
    }
  })

  hoveredMonitoringIndex.value = closestIdx
}

function handleMonitoringTouch(event) {
  if (event.touches && event.touches.length > 0) {
    handleMonitoringMouseMove(event.touches[0])
  }
}

function handleMonitoringMouseLeave() {
  hoveredMonitoringIndex.value = null
}

async function loadData() {
  try {
    const res = await telemetryService.getCurrent()
    if (res?.telemetry) {
      telemetry.value = { ...telemetry.value, ...res.telemetry }
      if (res.telemetry.isLive) {
        pushLivePoint(res.telemetry)
      }
    }
    if (res?.actuators) {
      actuators.value = { ...actuators.value, ...res.actuators }
    }
  } catch (err) {
    console.warn('Telemetry load warning:', err.message)
  }
}

let pollTimer = null

onMounted(async () => {
  seedPoints()
  await loadPeriodHistory(selectedPeriod.value)
  await loadData()
  isInitialLoading.value = false

  pollTimer = setInterval(loadData, 4000)

  socketService.on('telemetry_live', (data) => {
    if (data?.hasData) {
      telemetry.value = { ...telemetry.value, ...data, hasData: true }
      pushLivePoint(data)
    }
  })

  socketService.on('actuators_update', (data) => {
    if (data) actuators.value = { ...actuators.value, ...data }
  })

  connectionManager.on('telemetry_live', (data) => {
    if (data?.hasData) {
      telemetry.value = { ...telemetry.value, ...data, hasData: true }
      pushLivePoint(data)
    }
  })
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
  socketService.off('telemetry_live')
  socketService.off('actuators_update')
  connectionManager.off('telemetry_live')
})
</script>

<style scoped>
.monitoring-page {
  width: 100%;
  min-height: 100%;
}

.monitoring-main-container {
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
  margin: 0;
  box-sizing: border-box;
}

/* Header Card */
.monitoring-header-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.header-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 10px;
}

.status-online {
  background: #E8F5E9;
  color: #1B5E20;
}

.status-offline {
  background: #FEE2E2;
  color: #DC2626;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: currentColor;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: #0F291E;
  margin-bottom: 4px;
}

.page-subtitle {
  font-size: 14px;
  color: #64748B;
  margin: 0;
}

.btn-export-csv {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #F8FAFC;
  border: 1.5px solid #CBD5E1;
  color: #334155;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-export-csv:hover {
  background: #EDF2F7;
  color: #1B5E20;
  border-color: #1B5E20;
}

/* 4 Sensor Cards Grid */
.sensor-cards-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

@media (max-width: 1024px) {
  .sensor-cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .sensor-cards-grid {
    grid-template-columns: 1fr;
  }
}

.sensor-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.card-label {
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

.icon-amber { background: #FEF3C7; }
.icon-blue { background: #E0F2FE; }

.card-value-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin-bottom: 10px;
}

.card-big-val {
  font-size: 34px;
  font-weight: 800;
  color: #0F291E;
  line-height: 1;
}

.card-text-val {
  font-size: 24px;
  font-weight: 800;
  line-height: 1.2;
}

.text-blue { color: #0284C7; }
.text-green { color: #1B5E20; }

.card-unit {
  font-size: 15px;
  font-weight: 600;
  color: #64748B;
}

.status-chip {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
}

.chip-green { background: #E8F5E9; color: #1B5E20; }
.chip-amber { background: #FEF3C7; color: #B45309; }
.chip-red { background: #FEE2E2; color: #DC2626; }
.chip-blue { background: #E0F2FE; color: #0369A1; }
.chip-neutral { background: #F1F5F9; color: #64748B; }

/* Actuator Card Section */
.actuator-control-section {
  width: 100%;
}

/* Monitoring Chart & Analytics Card */
.monitoring-chart-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.chart-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.chart-header-left {
  flex: 1;
  min-width: 260px;
}

.chart-title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 4px;
}

.chart-title {
  font-size: 18px;
  font-weight: 700;
  color: #0F291E;
  margin: 0;
}

.period-active-chip {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 20px;
  background: #E8F5E9;
  color: #1B5E20;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.2px;
}

:global(.dark-theme) .period-active-chip {
  background: rgba(74, 222, 128, 0.15);
  color: #86EFAC;
}

.chart-subtitle {
  font-size: 13px;
  color: #64748B;
  margin: 0;
}

/* Actions toolbar: Pill buttons & CSV export */
.chart-actions-toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.timeframe-pill-selector {
  display: inline-flex;
  align-items: center;
  background: #F1F5F9;
  padding: 3px;
  border-radius: 10px;
  border: 1px solid #E2E8F0;
}

:global(.dark-theme) .timeframe-pill-selector {
  background: #071E27;
  border-color: #1E4E61;
}

.timeframe-btn {
  border: none;
  background: transparent;
  color: #64748B;
  font-size: 12px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 7px;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.timeframe-btn:hover {
  color: #0F291E;
}

:global(.dark-theme) .timeframe-btn:hover {
  color: #F8FAFC;
}

.timeframe-btn.active {
  background: #FFFFFF;
  color: #1B5E20;
  font-weight: 700;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

:global(.dark-theme) .timeframe-btn.active {
  background: #1B5E20;
  color: #FFFFFF;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.btn-chart-export {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid #CBD5E1;
  background: #FFFFFF;
  color: #334155;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-chart-export:hover {
  background: #F8FAFC;
  border-color: #94A3B8;
  color: #0F291E;
}

:global(.dark-theme) .btn-chart-export {
  background: #0B242F;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .btn-chart-export:hover {
  background: #113847;
  border-color: #2DD4BF;
  color: #FFFFFF;
}

/* Ringkasan Metrik Sederhana (Simple & Clean Badges) */
.simple-metric-badges {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 14px;
  padding: 8px 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
}

:global(.dark-theme) .simple-metric-badges {
  background: #071E27;
  border-color: #1E4E61;
}

.metric-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 8px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  font-size: 13px;
  color: #334155;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .metric-chip {
  background: #0B242F;
  border-color: #1E4E61;
  color: #E2E8F0;
}

.metric-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.dot-red { background: #DC2626; }
.dot-blue { background: #0284C7; }
.dot-green { background: #16A34A; }
.dot-amber { background: #D97706; }

.metric-name {
  color: #64748B;
  font-size: 12px;
}

:global(.dark-theme) .metric-name {
  color: #94A3B8;
}

.metric-val {
  font-weight: 700;
  font-size: 13px;
}

.metric-range {
  font-size: 11px;
  color: #94A3B8;
  margin-left: 2px;
}

@media (max-width: 900px) {
  .simple-metric-badges {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    padding: 8px;
  }

  .metric-chip {
    padding: 6px 10px;
    font-size: 12px;
  }
}

@media (max-width: 520px) {
  .simple-metric-badges {
    grid-template-columns: 1fr;
  }
}

/* Legend & Hint Row */
.chart-legend-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.chart-hint-text {
  font-size: 11px;
  color: #94A3B8;
  font-style: italic;
}

:global(.dark-theme) .chart-hint-text {
  color: #64748B;
}

.chart-top-bar {
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

.chart-sub {
  font-size: 13px;
  color: #64748B;
  margin: 0;
}

.chart-legend-row {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.legend-pill {
  display: flex;
  align-items: center;
  gap: 6px;
}

.legend-color-dot {
  width: 12px;
  height: 4px;
  border-radius: 2px;
}

.dot-red { background: #DC2626; }
.dot-blue { background: #0284C7; }

/* Chart Body Layout: Y-Axis + SVG Canvas */
.chart-body-layout {
  display: flex;
  align-items: stretch;
  gap: 12px;
  width: 100%;
  position: relative;
}

.chart-y-axis {
  width: 32px;
  position: relative;
  flex-shrink: 0;
  user-select: none;
}

.y-axis-lbl {
  position: absolute;
  right: 0;
  transform: translateY(-50%);
  font-size: 11px;
  font-weight: 700;
  color: #94A3B8;
  line-height: 1;
}

:global(.dark-theme) .y-axis-lbl {
  color: #64748B;
}

.y-80 { top: 8.33%; }
.y-60 { top: 33.33%; }
.y-40 { top: 58.33%; }
.y-20 { top: 83.33%; }

.chart-svg-box {
  flex: 1;
  position: relative;
  cursor: crosshair;
  user-select: none;
  -webkit-user-select: none;
  touch-action: pan-y;
  height: 270px;
}

.monitoring-svg {
  width: 100%;
  height: 100%;
  display: block;
}

.ideal-zone-overlay {
  position: absolute;
  top: 52%;
  left: 14px;
  transform: translateY(-50%);
  pointer-events: none;
  z-index: 5;
}

.ideal-zone-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 6px;
  background: rgba(27, 94, 32, 0.12);
  border: 1px solid rgba(27, 94, 32, 0.22);
  color: #1B5E20;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.2px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

:global(.dark-theme) .ideal-zone-pill {
  background: rgba(16, 185, 129, 0.18);
  border-color: rgba(16, 185, 129, 0.35);
  color: #6EE7B7;
}

.chart-time-row {
  display: flex;
  justify-content: space-between;
  margin-left: 44px;
  padding: 10px 4px 0 4px;
  font-size: 11px;
  color: #94A3B8;
  font-weight: 600;
}

:global(.dark-theme) .chart-grid-line {
  stroke: rgba(255, 255, 255, 0.08);
}

:global(.dark-theme) .chart-baseline {
  stroke: rgba(255, 255, 255, 0.18);
}

/* Floating Tooltip Bubble on Hover */
.chart-tooltip-bubble {
  position: absolute;
  top: 6px;
  pointer-events: none;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  padding: 9px 13px;
  box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.05);
  z-index: 25;
  min-width: 180px;
  transition: left 0.08s ease-out, transform 0.15s ease-out;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

:global(.dark-theme) .chart-tooltip-bubble {
  background: rgba(14, 52, 67, 0.95);
  border-color: #1E4E61;
  box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.5);
}

.tooltip-time-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 700;
  color: #64748B;
  padding-bottom: 4px;
  border-bottom: 1px solid #E2E8F0;
}

:global(.dark-theme) .tooltip-time-row {
  color: #94A3B8;
  border-bottom-color: rgba(255, 255, 255, 0.1);
}

.tooltip-metric-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  font-size: 12px;
}

.tooltip-metric-left {
  display: flex;
  align-items: center;
  gap: 6px;
}

.tooltip-metric-left .metric-title {
  color: #475569;
  font-weight: 600;
}

:global(.dark-theme) .tooltip-metric-left .metric-title {
  color: #CBD5E1;
}

.metric-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.tooltip-metric-right {
  display: flex;
  align-items: center;
  gap: 6px;
}

.metric-value {
  font-size: 13px;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
}

.text-red {
  color: #DC2626;
}

.text-blue {
  color: #0284C7;
}

:global(.dark-theme) .text-red {
  color: #F87171;
}

:global(.dark-theme) .text-blue {
  color: #38BDF8;
}

.metric-sub {
  font-size: 10px;
  font-weight: 700;
  color: #64748B;
}

:global(.dark-theme) .metric-sub {
  color: #94A3B8;
}

.status-chip {
  display: inline-flex;
  align-items: center;
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 700;
  line-height: 1.3;
}

.chip-ideal {
  background: #DCFCE7;
  color: #166534;
}

:global(.dark-theme) .chip-ideal {
  background: rgba(16, 185, 129, 0.2);
  color: #6EE7B7;
}

.chip-low {
  background: #FEF3C7;
  color: #B45309;
}

:global(.dark-theme) .chip-low {
  background: rgba(245, 158, 11, 0.2);
  color: #FCD34D;
}

.chip-high {
  background: #FEE2E2;
  color: #991B1B;
}

:global(.dark-theme) .chip-high {
  background: rgba(239, 68, 68, 0.2);
  color: #FCA5A5;
}

.chart-hover-elements {
  pointer-events: none;
}

/* Tooltip transition */
.fade-tooltip-enter-active,
.fade-tooltip-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.fade-tooltip-enter-from,
.fade-tooltip-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

@media (max-width: 900px) {
  .monitoring-main-container {
    padding: 0;
    gap: 12px;
  }

  .monitoring-header-card,
  .chart-full-card {
    padding: 16px;
    border-radius: 14px;
  }

  .sensor-cards-grid {
    gap: 10px;
  }

  .sensor-metric-card {
    padding: 14px 16px;
    border-radius: 14px;
  }

  .btn-export-csv {
    width: 100%;
    justify-content: center;
  }

  .chart-svg-box {
    height: 220px;
  }

  .ideal-zone-pill {
    font-size: 10px;
    padding: 2px 7px;
  }
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR MONITORING VIEW
   ========================================================================== */
:global(.dark-theme) .monitoring-header-card,
:global(.dark-theme) .sensor-card,
:global(.dark-theme) .monitoring-chart-card {
  background: #0B242F;
  border-color: #1E4E61;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

:global(.dark-theme) .page-title,
:global(.dark-theme) .card-big-val,
:global(.dark-theme) .card-text-val,
:global(.dark-theme) .chart-title {
  color: #F8FAFC;
}

:global(.dark-theme) .page-subtitle,
:global(.dark-theme) .card-label,
:global(.dark-theme) .card-unit,
:global(.dark-theme) .card-hint,
:global(.dark-theme) .chart-subtitle,
:global(.dark-theme) .chart-time-row {
  color: #94A3B8;
}

:global(.dark-theme) .chart-legend-box {
  color: #CBD5E1;
}

:global(.dark-theme) .btn-export-csv {
  background: #113847;
  color: #F8FAFC;
  border-color: #1E4E61;
}

:global(.dark-theme) .btn-export-csv:hover {
  background: #164657;
  border-color: #2D6B82;
}

:global(.dark-theme) .icon-amber { background: rgba(217, 119, 6, 0.18); color: #FBBF24; }
:global(.dark-theme) .icon-blue { background: rgba(2, 132, 199, 0.18); color: #38BDF8; }

:global(.dark-theme) .chip-green { background: rgba(27, 94, 32, 0.25); color: #4ADE80; }
:global(.dark-theme) .chip-amber { background: rgba(217, 119, 6, 0.25); color: #FBBF24; }
:global(.dark-theme) .chip-red { background: rgba(220, 38, 38, 0.25); color: #F87171; }
:global(.dark-theme) .chip-blue { background: rgba(2, 132, 199, 0.25); color: #38BDF8; }
:global(.dark-theme) .chip-neutral { background: #113847; color: #CBD5E1; }

:global(.dark-theme) .monitoring-svg line[stroke="#E2E8F0"] {
  stroke: #1E4E61;
}

:global(.dark-theme) .monitoring-svg text[fill="#94A3B8"] {
  fill: #64748B;
}

:global(.dark-theme) .monitoring-svg rect[fill="#DCFCE7"] {
  fill: rgba(22, 101, 52, 0.25);
}

:global(.dark-theme) .monitoring-svg text[fill="#166534"] {
  fill: #86EFAC;
}
</style>
