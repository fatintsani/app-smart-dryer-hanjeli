<template>
  <div class="history-view-page">
    <div class="history-main-container">
      <!-- 1. Header Card with Title and Actions -->
      <div class="history-header-card">
        <div class="header-left">
          <div class="title-with-pill">
            <span class="brand-bar"></span>
            <h1 class="page-title">Riwayat Pengeringan</h1>
          </div>
          <p class="page-subtitle">
            Catatan hasil pengeringan biji hanjeli yang telah diproses di Green House.
          </p>
        </div>

        <div class="header-actions">
          <button
            type="button"
            class="btn-header-secondary"
            @click="openExportModal('csv')"
            title="Unduh Data Format Excel / CSV"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Unduh CSV</span>
          </button>

          <button
            type="button"
            class="btn-header-primary"
            @click="openExportModal('pdf')"
            title="Cetak Laporan Rekapitulasi PDF"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Laporan</span>
          </button>
        </div>
      </div>

      <!-- 2. Ringkasan Singkat (3 Kartu KPI Petani) -->
      <div class="kpi-summary-grid">
        <!-- Total Selesai -->
        <div class="kpi-metric-card">
          <div class="kpi-info-side">
            <span class="kpi-label">Total Pengeringan Selesai</span>
            <div class="kpi-value-row">
              <span class="kpi-number text-green">{{ countByStatus('completed') }}</span>
              <span class="kpi-unit">Sesi Selesai</span>
            </div>
            <p class="kpi-subtext">
              {{ countByStatus('running') > 0 ? `${countByStatus('running')} sesi sedang berjalan` : 'Semua proses selesai' }}
            </p>
          </div>
          <div class="kpi-icon-box bg-green-subtle text-green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
        </div>

        <!-- Total Bobot Kering -->
        <div class="kpi-metric-card">
          <div class="kpi-info-side">
            <span class="kpi-label">Total Gabah Kering</span>
            <div class="kpi-value-row">
              <span class="kpi-number text-dark">{{ aggregateTotalWeightKg }}</span>
              <span class="kpi-unit">kg</span>
            </div>
            <p class="kpi-subtext text-emerald">
              Sekitar {{ (aggregateTotalWeightKg / 1000).toFixed(2) }} ton biji siap jual
            </p>
          </div>
          <div class="kpi-icon-box bg-amber-subtle text-amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
        </div>

        <!-- Rata-rata Kadar Air -->
        <div class="kpi-metric-card">
          <div class="kpi-info-side">
            <span class="kpi-label">Rata-rata Kadar Air Akhir</span>
            <div class="kpi-value-row">
              <span class="kpi-number text-blue">{{ aggregateAvgFinalMoisture }}%</span>
              <span class="badge-sni">Standar SNI</span>
            </div>
            <p class="kpi-subtext">
              Penurunan rata-rata {{ aggregateAvgMoistureReduction }}%
            </p>
          </div>
          <div class="kpi-icon-box bg-blue-subtle text-blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- 3. Toolbar Pencarian & Filter Tab -->
      <div class="filter-toolbar-card">
        <div class="search-input-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari kode pengeringan atau jenis varietas..."
            class="search-input"
          />
          <button v-if="searchQuery" type="button" class="btn-clear-search" @click="searchQuery = ''">
            Hapus
          </button>
        </div>

        <div class="filter-pills-row">
          <button
            type="button"
            class="filter-pill"
            :class="{ active: currentFilter === 'all' }"
            @click="currentFilter = 'all'"
          >
            Semua ({{ batchRecords.length }})
          </button>
          <button
            type="button"
            class="filter-pill pill-amber"
            :class="{ active: currentFilter === 'running' }"
            @click="currentFilter = 'running'"
          >
            Sedang Berjalan ({{ countByStatus('running') }})
          </button>
          <button
            type="button"
            class="filter-pill pill-green"
            :class="{ active: currentFilter === 'completed' }"
            @click="currentFilter = 'completed'"
          >
            Selesai ({{ countByStatus('completed') }})
          </button>
        </div>
      </div>

      <!-- 4. Daftar Kartu Riwayat Pengeringan -->
      <div v-if="paginatedBatches.length > 0" class="batch-cards-grid">
        <div
          v-for="b in paginatedBatches"
          :key="b.id"
          class="batch-item-card"
        >
          <!-- Card Header: Kode & Status -->
          <div class="card-top-row">
            <span class="batch-code-badge">{{ b.id }}</span>
            <span
              class="status-pill"
              :class="b.status === 'completed' ? 'pill-completed' : 'pill-running'"
            >
              <span class="status-dot"></span>
              <span>{{ b.status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}</span>
            </span>
          </div>

          <h3 class="crop-variety-title">{{ b.cropVariety }}</h3>
          <p class="meta-date-text">
            {{ b.date }} • Operator: <strong>{{ b.operator }}</strong>
          </p>

          <!-- 3 Indikator Utama -->
          <div class="metrics-triple-box">
            <div class="metric-col">
              <span class="m-lbl">BOBOT KERING</span>
              <strong class="m-val">{{ b.weight }}</strong>
            </div>
            <div class="metric-col">
              <span class="m-lbl">KADAR AIR</span>
              <strong class="m-val text-blue">{{ b.finalHumidity }}</strong>
            </div>
            <div class="metric-col">
              <span class="m-lbl">LAMA PROSES</span>
              <strong class="m-val text-amber">{{ b.duration }}</strong>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="card-actions-row">
            <button
              type="button"
              class="btn-view-detail"
              @click="openDetail(b)"
            >
              <span>Lihat Hasil</span>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M5 12h14m-7-7l7 7-7 7" />
              </svg>
            </button>

            <button
              type="button"
              class="btn-qr-label"
              @click.stop="openQrModal(b)"
              title="Cetak Label QR Kemasan"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
            </button>

            <button
              v-if="b.status === 'running'"
              type="button"
              class="btn-quick-complete"
              @click.stop="openCompleteModal(b)"
              title="Selesaikan Sesi Ini"
            >
              Selesai
            </button>
          </div>
        </div>
      </div>

      <!-- 5. Empty State -->
      <div v-else class="empty-state-card">
        <div class="empty-icon-circle">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
          </svg>
        </div>
        <h3 class="empty-heading">Belum Ada Riwayat Pengeringan</h3>
        <p class="empty-subtext">
          {{ searchQuery ? `Tidak ditemukan data untuk pencarian "${searchQuery}".` : 'Mulai sesi pengeringan baru untuk menyimpan data proses gabah hanjeli.' }}
        </p>
        <div class="empty-actions">
          <button
            v-if="searchQuery || currentFilter !== 'all'"
            type="button"
            class="btn-reset-filter"
            @click="searchQuery = ''; currentFilter = 'all'"
          >
            Reset Filter & Pencarian
          </button>
          <button
            v-else
            type="button"
            class="btn-start-primary"
            @click="router.push('/drying/new')"
          >
            + Mulai Pengeringan Baru
          </button>
        </div>
      </div>

      <!-- 6. Pagination Bar -->
      <div v-if="totalPages > 1" class="pagination-footer-card">
        <span class="pagination-info">
          Menampilkan <strong>{{ startRecordIndex }}-{{ endRecordIndex }}</strong> dari total <strong>{{ totalFilteredBatches }}</strong> sesi
        </span>

        <div class="pagination-nav-group">
          <button
            type="button"
            class="btn-nav-page"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            Sebelumnya
          </button>

          <div class="page-pills">
            <template v-for="(p, pIdx) in paginationPages" :key="pIdx">
              <span v-if="p === '...'" class="page-dots">...</span>
              <button
                v-else
                type="button"
                class="btn-page-number"
                :class="{ active: p === currentPage }"
                @click="goToPage(p)"
              >
                {{ p }}
              </button>
            </template>
          </div>

          <button
            type="button"
            class="btn-nav-page"
            :disabled="currentPage >= totalPages"
            @click="goToPage(currentPage + 1)"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: SELESAIKAN BATCH -->
    <!-- ========================================================================= -->
    <div
      v-if="isCompleteModalOpen"
      class="modal-backdrop-fixed"
      @click.self="isCompleteModalOpen = false"
    >
      <div class="modal-dialog-box">
        <div class="modal-head-row">
          <div class="modal-icon-badge">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2.2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
          <div>
            <h3 class="modal-title">Selesaikan Pengeringan?</h3>
            <p class="modal-subtitle">Batch {{ selectedBatchToComplete?.id }}</p>
          </div>
        </div>

        <p class="modal-prompt-text">
          Apakah proses pengeringan varietas <strong>{{ selectedBatchToComplete?.cropVariety }}</strong> sudah selesai dan siap disimpan?
        </p>

        <div class="modal-recap-box">
          <p>✓ Kadar air saat ini: <strong>{{ selectedBatchToComplete?.finalHumidity }}</strong></p>
          <p>✓ Data akan tersimpan permanen di riwayat panen</p>
          <p>✓ Anda dapat langsung mencetak label kemasan QR setelahnya</p>
        </div>

        <div class="modal-footer-btns">
          <button type="button" class="btn-modal-cancel" @click="isCompleteModalOpen = false">
            Batal
          </button>
          <button
            type="button"
            class="btn-modal-confirm"
            :disabled="isCompletingBatch"
            @click="executeCompleteBatch"
          >
            {{ isCompletingBatch ? 'Menyimpan...' : 'Ya, Selesaikan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: UNDUH / CETAK LAPORAN -->
    <!-- ========================================================================= -->
    <div
      v-if="isExportModalOpen"
      class="modal-backdrop-fixed"
      @click.self="closeExportModal"
    >
      <div class="modal-dialog-box">
        <h3 class="modal-title">
          {{ selectedFormat === 'pdf' ? 'Cetak Laporan Rekapitulasi' : 'Unduh Rekap CSV' }}
        </h3>
        <p class="modal-subtitle">
          Pilih jangkauan data yang ingin dimasukkan ke dalam dokumen
        </p>

        <div class="export-options-list">
          <label class="export-option-item" :class="{ active: exportScope === 'all' }">
            <input type="radio" v-model="exportScope" value="all" />
            <div>
              <strong class="opt-title">Semua Data Riwayat</strong>
              <span class="opt-desc">Mencakup seluruh {{ batchRecords.length }} batch pengeringan</span>
            </div>
          </label>

          <label class="export-option-item" :class="{ active: exportScope === 'filtered' }">
            <input type="radio" v-model="exportScope" value="filtered" />
            <div>
              <strong class="opt-title">Data yang Sedang Difilter</strong>
              <span class="opt-desc">Hanya {{ effectiveFilteredBatches.length }} batch yang tampil di layar</span>
            </div>
          </label>
        </div>

        <div class="modal-footer-btns">
          <button type="button" class="btn-modal-cancel" @click="closeExportModal">
            Batal
          </button>
          <button type="button" class="btn-modal-confirm" @click="executeExport">
            {{ selectedFormat === 'pdf' ? 'Cetak Sekarang' : 'Unduh File' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Packaging QR Label Modal -->
    <PackagingQrLabelModal
      :is-open="isQrModalOpen"
      :batch="selectedQrBatch"
      @close="isQrModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { batchService } from '../../services/batchService'
import { alertDialog } from '../../services/confirmDialogService'
import { connectionManager } from '../../services/connectionManager'
import PackagingQrLabelModal from '../../components/PackagingQrLabelModal.vue'

const router = useRouter()

const props = defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['select-batch'])

const isInitialLoading = ref(true)
const batchRecords = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const currentFilter = ref('all') // 'all', 'running', 'completed'
const currentPage = ref(1)
const itemsPerPage = ref(6)

// Complete modal
const isCompleteModalOpen = ref(false)
const selectedBatchToComplete = ref(null)
const isCompletingBatch = ref(false)

// QR modal
const isQrModalOpen = ref(false)
const selectedQrBatch = ref(null)

// Export modal
const isExportModalOpen = ref(false)
const selectedFormat = ref('pdf')
const exportScope = ref('all')

function openDetail(batch) {
  const targetId = batch.rawId || batch.id
  emit('select-batch', targetId)
  router.push(`/history/${targetId}`)
}

function openQrModal(batch) {
  selectedQrBatch.value = {
    ...batch,
    batchCode: batch.id || batch.batchCode,
    cropVariety: batch.cropVariety,
    finalMoisturePercent: batch.humidityNum,
    qualityGrade: batch.qualityGrade || 'Grade A (Standar SNI)',
    completedAt: batch.completedAt || batch.date
  }
  isQrModalOpen.value = true
}

function openCompleteModal(batch) {
  selectedBatchToComplete.value = batch
  isCompleteModalOpen.value = true
}

async function executeCompleteBatch() {
  if (!selectedBatchToComplete.value) return
  isCompletingBatch.value = true
  const targetId = selectedBatchToComplete.value.rawId || selectedBatchToComplete.value.id
  try {
    await batchService.complete(targetId, {
      finalMoisturePercent: selectedBatchToComplete.value.humidityNum || 12.0
    })
    isCompleteModalOpen.value = false
    selectedBatchToComplete.value = null
    await fetchBatches(false)
  } catch (err) {
    await alertDialog({
      title: 'Gagal Menyelesaikan Batch',
      message: err.message || 'Gagal menyelesaikan batch pengeringan.',
      type: 'danger'
    })
  } finally {
    isCompletingBatch.value = false
  }
}

function transformBatch(b) {
  const isRunning = b.status === 'ACTIVE' || b.status === 'PAUSED' || b.status === 'running'
  const isCompleted = b.status === 'COMPLETED' || b.status === 'completed'
  const startDate = new Date(b.startedAt || b.createdAt)
  const endDate = b.completedAt ? new Date(b.completedAt) : null

  let durationHrs = Number(b.totalDurationHours || 0)
  if (durationHrs <= 0) {
    if (endDate) {
      durationHrs = Math.max(0.1, ((endDate - startDate) / (1000 * 60 * 60)))
    } else {
      durationHrs = Math.max(0.1, ((new Date() - startDate) / (1000 * 60 * 60)))
    }
  }

  const tempNum = Number(b.avgTemp !== undefined && b.avgTemp !== null ? b.avgTemp : 42.0)
  const avgTemp = `${tempNum.toFixed(1)}°C`
  
  const initialHumidityNum = Number(b.initialMoisturePercent !== undefined && b.initialMoisturePercent !== null ? b.initialMoisturePercent : 24.5)
  const humidityNum = Number(b.finalMoisturePercent !== null && b.finalMoisturePercent !== undefined 
    ? b.finalMoisturePercent 
    : (b.currentMoisturePercent !== null && b.currentMoisturePercent !== undefined ? b.currentMoisturePercent : (b.targetMoisturePercent || 12.0)))
  const finalHumidity = `${humidityNum.toFixed(1)}%`

  const weightNum = Number(b.finalWeightKg !== null && b.finalWeightKg !== undefined 
    ? b.finalWeightKg 
    : (b.currentWeightKg !== null && b.currentWeightKg !== undefined ? b.currentWeightKg : (b.initialWeightKg || 120)))
  const weight = `${weightNum.toFixed(1)} kg`

  const energyNum = Number(b.energyKwh !== undefined && b.energyKwh !== null ? b.energyKwh : (durationHrs * 1.45).toFixed(1))
  const costNum = Math.round(energyNum * 1500)

  return {
    id: b.batchCode || 'HJ-2026-001',
    batchCode: b.batchCode || 'HJ-2026-001',
    rawId: b.id,
    cropVariety: b.cropVariety || 'Hanjeli Ketan Sukabumi',
    status: isCompleted ? 'completed' : isRunning ? 'running' : 'aborted',
    statusLabel: isCompleted ? 'SELESAI' : isRunning ? 'SEDANG BERJALAN' : (b.status || 'DIBATALKAN'),
    date: startDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }),
    time: `${startDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} - ${endDate ? endDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'Sekarang'}`,
    weight,
    weightNum,
    avgTemp,
    tempNum,
    initialHumidityNum,
    finalHumidity,
    humidityNum,
    duration: `${durationHrs.toFixed(1)} Jam`,
    durationNum: durationHrs,
    energy: `${energyNum.toFixed(1)} kWh`,
    energyNum,
    cost: `Rp ${costNum.toLocaleString('id-ID')}`,
    operator: b.operatorName || b.operator?.name || 'Operator Green House',
    qualityGrade: b.qualityGrade || 'Grade A (Standar SNI)',
    qualityScore: Number(b.qualityScore || 95),
    notes: b.notes || '',
    completedAt: b.completedAt,
    startedAt: b.startedAt || b.createdAt
  }
}

async function fetchBatches(isFirstTime = false) {
  if (isFirstTime) isLoading.value = true
  try {
    const data = await batchService.getAll()
    if (Array.isArray(data)) {
      batchRecords.value = data.map(transformBatch)
    }
  } catch (err) {
    console.warn('Failed to fetch batches from API:', err.message)
  } finally {
    isLoading.value = false
    if (isFirstTime) {
      setTimeout(() => {
        isInitialLoading.value = false
      }, 300)
    }
  }
}

function handleLiveTelemetry(liveData) {
  if (!liveData || !liveData.hasData) return
  const runningBatch = batchRecords.value.find(b => b.status === 'running')
  if (runningBatch) {
    if (liveData.grainMoisture !== undefined && liveData.grainMoisture !== null) {
      runningBatch.humidityNum = Number(liveData.grainMoisture)
      runningBatch.finalHumidity = `${runningBatch.humidityNum.toFixed(1)}%`
    }
    if (liveData.weightCurrentKg !== undefined && liveData.weightCurrentKg !== null) {
      runningBatch.weightNum = Number(liveData.weightCurrentKg)
      runningBatch.weight = `${runningBatch.weightNum.toFixed(1)} kg`
    }
    if (liveData.tempInternal !== undefined && liveData.tempInternal !== null) {
      runningBatch.tempNum = Number(liveData.tempInternal)
      runningBatch.avgTemp = `${runningBatch.tempNum.toFixed(1)}°C`
    }
  }
}

let historyPollTimer = null

onMounted(() => {
  fetchBatches(true)

  historyPollTimer = setInterval(() => {
    if (batchRecords.value.some(b => b.status === 'running')) {
      fetchBatches(false)
    }
  }, 5000)

  connectionManager.on('telemetry_live', handleLiveTelemetry)
  connectionManager.on('telemetry_update', handleLiveTelemetry)
})

onUnmounted(() => {
  if (historyPollTimer) clearInterval(historyPollTimer)
  connectionManager.off('telemetry_live', handleLiveTelemetry)
  connectionManager.off('telemetry_update', handleLiveTelemetry)
})

function countByStatus(status) {
  return batchRecords.value.filter(b => b.status === status).length
}

// Aggregate Calculations
const aggregateTotalWeightKg = computed(() => {
  const sum = batchRecords.value.reduce((acc, b) => acc + (b.weightNum || 0), 0)
  return Number(sum.toFixed(1))
})

const aggregateAvgMoistureReduction = computed(() => {
  if (batchRecords.value.length === 0) return '0.0'
  const sum = batchRecords.value.reduce((acc, b) => acc + (b.initialHumidityNum - b.humidityNum), 0)
  return Math.max(0, sum / batchRecords.value.length).toFixed(1)
})

const aggregateAvgFinalMoisture = computed(() => {
  if (batchRecords.value.length === 0) return '0.0'
  const sum = batchRecords.value.reduce((acc, b) => acc + b.humidityNum, 0)
  return (sum / batchRecords.value.length).toFixed(1)
})

// Filter & Search Logic
const filteredBatches = computed(() => {
  let list = batchRecords.value
  if (currentFilter.value !== 'all') {
    list = list.filter(b => b.status === currentFilter.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(b => 
      b.id.toLowerCase().includes(q) || 
      b.cropVariety.toLowerCase().includes(q) || 
      b.operator.toLowerCase().includes(q)
    )
  }
  return list
})

const effectiveFilteredBatches = computed(() => filteredBatches.value)
const totalFilteredBatches = computed(() => effectiveFilteredBatches.value.length)

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(totalFilteredBatches.value / itemsPerPage.value))
})

const startRecordIndex = computed(() => {
  if (totalFilteredBatches.value === 0) return 0
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const endRecordIndex = computed(() => {
  return Math.min(currentPage.value * itemsPerPage.value, totalFilteredBatches.value)
})

const paginatedBatches = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return effectiveFilteredBatches.value.slice(start, start + itemsPerPage.value)
})

const paginationPages = computed(() => {
  const total = totalPages.value
  const cur = currentPage.value
  if (total <= 5) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }
  if (cur <= 3) {
    return [1, 2, 3, 4, '...', total]
  }
  if (cur >= total - 2) {
    return [1, '...', total - 3, total - 2, total - 1, total]
  }
  return [1, '...', cur - 1, cur, cur + 1, '...', total]
})

function goToPage(p) {
  if (p === '...' || p === currentPage.value) return
  if (p < 1 || p > totalPages.value) return
  currentPage.value = p
}

function openExportModal(fmt = 'pdf') {
  selectedFormat.value = fmt
  isExportModalOpen.value = true
}

function closeExportModal() {
  isExportModalOpen.value = false
}

function executeExport() {
  const dataList = exportScope.value === 'filtered' ? effectiveFilteredBatches.value : batchRecords.value
  if (dataList.length === 0) {
    alertDialog({
      title: 'Data Tidak Tersedia',
      message: 'Tidak ada data yang dapat diekspor.',
      type: 'info'
    })
    return
  }

  if (selectedFormat.value === 'csv') {
    const headers = ['No', 'No_Batch', 'Varietas', 'Tanggal', 'Bobot_Kg', 'Status', 'Rata_Suhu', 'Kadar_Air_Akhir', 'Durasi', 'Energi_kWh', 'Biaya', 'Operator']
    const rows = dataList.map((b, i) => [
      i + 1,
      `"${b.id}"`,
      `"${b.cropVariety}"`,
      `"${b.date}"`,
      b.weightNum,
      `"${b.statusLabel}"`,
      `"${b.avgTemp}"`,
      `"${b.finalHumidity}"`,
      `"${b.duration}"`,
      `"${b.energy}"`,
      `"${b.cost}"`,
      `"${b.operator}"`
    ])
    const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.setAttribute('href', url)
    link.setAttribute('download', `rekap_riwayat_pengeringan_hanjeli.csv`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    closeExportModal()
  } else {
    // Print PDF
    const printWindow = window.open('', '_blank')
    if (!printWindow) {
      window.print()
      closeExportModal()
      return
    }

    const rowsHtml = dataList.map((b, idx) => `
      <tr style="border-bottom: 1px solid #E5E7EB;">
        <td style="padding: 10px; text-align: center;">${idx + 1}</td>
        <td style="padding: 10px; font-weight: bold; font-family: monospace;">${b.id}</td>
        <td style="padding: 10px;">${b.cropVariety}</td>
        <td style="padding: 10px;">${b.date}</td>
        <td style="padding: 10px; text-align: right; font-weight: bold;">${b.weight}</td>
        <td style="padding: 10px; text-align: right; color: #1D4ED8; font-weight: bold;">${b.finalHumidity}</td>
        <td style="padding: 10px; text-align: right;">${b.duration}</td>
        <td style="padding: 10px; text-align: center;">${b.status === 'completed' ? 'Selesai' : 'Sedang Berjalan'}</td>
      </tr>
    `).join('')

    const sTag = '<' + 'style>'
    const sClose = '<' + '/style>'
    const scTag = '<' + 'script>'
    const scClose = '<' + '/script>'

    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Rekap Riwayat Pengeringan Hanjeli</title>
        ${sTag}
          body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; padding: 24px; color: #1F2937; }
          h1 { color: #1B5E20; font-size: 20px; margin-bottom: 4px; }
          p { color: #6B7280; font-size: 13px; margin-top: 0; }
          table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 12px; }
          th { background: #F3F4F6; padding: 10px; text-align: left; font-weight: bold; border-bottom: 2px solid #E5E7EB; }
        ${sClose}
      </head>
      <body>
        <h1>Green House Smart Dryer Hanjeli</h1>
        <p>Laporan Rekapitulasi Hasil Pengeringan Biji Hanjeli • Dicetak pada ${new Date().toLocaleDateString('id-ID')}</p>
        <table>
          <thead>
            <tr>
              <th style="text-align: center;">No</th>
              <th>Kode Batch</th>
              <th>Varietas</th>
              <th>Tanggal</th>
              <th style="text-align: right;">Bobot</th>
              <th style="text-align: right;">Kadar Air</th>
              <th style="text-align: right;">Durasi</th>
              <th style="text-align: center;">Status</th>
            </tr>
          </thead>
          <tbody>${rowsHtml}</tbody>
        </table>
        ${scTag}
          window.onload = function() { window.print(); }
        ${scClose}
      </body>
      </html>
    `)
    printWindow.document.close()
    closeExportModal()
  }
}
</script>

<style scoped>
.history-view-page {
  width: 100%;
  min-height: 100%;
  background: var(--color-bg, #F4F7F5);
}

.history-main-container {
  padding: 24px 28px 48px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
  margin: 0;
  box-sizing: border-box;
}

/* 1. Header Card */
.history-header-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px 28px;
  border: 1px solid #E2E8F0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.header-left {
  flex: 1;
  min-width: 260px;
}

.title-with-pill {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-bar {
  width: 5px;
  height: 24px;
  background: #1B5E20;
  border-radius: 4px;
}

.page-title {
  font-size: 22px;
  font-weight: 700;
  color: #0F291E;
  margin: 0;
}

.page-subtitle {
  font-size: 13px;
  color: #64748B;
  margin: 6px 0 0 17px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-header-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #F8FAFC;
  color: #334155;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-header-secondary:hover {
  background: #F1F5F9;
  border-color: #94A3B8;
}

.btn-header-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: #1B5E20;
  color: #FFFFFF;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(27, 94, 32, 0.2);
}

.btn-header-primary:hover {
  background: #144818;
}

/* 2. KPI Summary Grid */
.kpi-summary-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

@media (max-width: 900px) {
  .kpi-summary-grid {
    grid-template-columns: 1fr;
  }
}

.kpi-metric-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px 24px;
  border: 1px solid #E2E8F0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.kpi-info-side {
  flex: 1;
}

.kpi-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748B;
  display: block;
}

.kpi-value-row {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-top: 6px;
}

.kpi-number {
  font-size: 28px;
  font-weight: 800;
  line-height: 1.1;
}

.text-green { color: #1B5E20; }
.text-dark { color: #0F172A; }
.text-blue { color: #1D4ED8; }
.text-amber { color: #D97706; }
.text-emerald { color: #059669; }

.kpi-unit {
  font-size: 13px;
  font-weight: 600;
  color: #64748B;
}

.badge-sni {
  background: #DCFCE7;
  color: #166534;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 9999px;
}

.kpi-subtext {
  font-size: 12px;
  color: #64748B;
  margin: 6px 0 0 0;
}

.kpi-icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.bg-green-subtle { background: #E8F5E9; }
.bg-amber-subtle { background: #FEF3C7; }
.bg-blue-subtle { background: #EFF6FF; }

/* 3. Toolbar Pencarian & Filter */
.filter-toolbar-card {
  background: #FFFFFF;
  border-radius: 14px;
  padding: 14px 20px;
  border: 1px solid #E2E8F0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.search-input-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #F8FAFC;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  padding: 8px 14px;
  flex: 1;
  min-width: 260px;
  max-width: 480px;
}

.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  color: #0F172A;
  width: 100%;
}

.btn-clear-search {
  border: none;
  background: transparent;
  font-size: 11px;
  color: #94A3B8;
  cursor: pointer;
  padding: 0;
}

.btn-clear-search:hover {
  color: #475569;
}

.filter-pills-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-pill {
  padding: 7px 14px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
  background: #F8FAFC;
  color: #475569;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-pill:hover {
  background: #F1F5F9;
}

.filter-pill.active {
  background: #1B5E20;
  color: #FFFFFF;
  border-color: #1B5E20;
}

.filter-pill.pill-amber.active {
  background: #D97706;
  border-color: #D97706;
  color: #FFFFFF;
}

.filter-pill.pill-green.active {
  background: #15803D;
  border-color: #15803D;
  color: #FFFFFF;
}

/* 4. Batch Cards Grid */
.batch-cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

@media (max-width: 1100px) {
  .batch-cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 680px) {
  .batch-cards-grid {
    grid-template-columns: 1fr;
  }
}

.batch-item-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px 22px;
  border: 1px solid #E2E8F0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  transition: all 0.2s ease;
}

.batch-item-card:hover {
  border-color: #CBD5E1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.card-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}

.batch-code-badge {
  font-family: monospace;
  font-size: 14px;
  font-weight: 700;
  color: #0F172A;
  background: #F1F5F9;
  padding: 3px 8px;
  border-radius: 6px;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 700;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

.pill-completed {
  background: #DCFCE7;
  color: #166534;
}

.pill-running {
  background: #FEF3C7;
  color: #B45309;
}

.crop-variety-title {
  font-size: 15px;
  font-weight: 700;
  color: #0F291E;
  margin: 0 0 4px 0;
}

.meta-date-text {
  font-size: 12px;
  color: #64748B;
  margin: 0 0 16px 0;
}

.metrics-triple-box {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 10px 12px;
  margin-bottom: 16px;
}

.metric-col {
  display: flex;
  flex-direction: column;
}

.m-lbl {
  font-size: 9px;
  font-weight: 700;
  color: #64748B;
  letter-spacing: 0.3px;
}

.m-val {
  font-size: 13px;
  font-weight: 700;
  color: #0F172A;
  margin-top: 3px;
}

.card-actions-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding-top: 14px;
  border-top: 1px solid #F1F5F9;
}

.btn-view-detail {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 9px 14px;
  background: #1B5E20;
  color: #FFFFFF;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-view-detail:hover {
  background: #144818;
}

.btn-qr-label {
  padding: 9px;
  background: #F8FAFC;
  color: #1B5E20;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-qr-label:hover {
  background: #E8F5E9;
  border-color: #1B5E20;
}

.btn-quick-complete {
  padding: 9px 12px;
  background: #DCFCE7;
  color: #166534;
  border: 1px solid #86EFAC;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

.btn-quick-complete:hover {
  background: #BBF7D0;
}

/* 5. Empty State */
.empty-state-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 48px 24px;
  border: 1px solid #E2E8F0;
  text-align: center;
}

.empty-icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #F1F5F9;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}

.empty-heading {
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 6px 0;
}

.empty-subtext {
  font-size: 13px;
  color: #64748B;
  margin: 0 auto 20px;
  max-width: 400px;
}

.btn-reset-filter {
  padding: 10px 18px;
  background: #F1F5F9;
  color: #334155;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-start-primary {
  padding: 10px 20px;
  background: #1B5E20;
  color: #FFFFFF;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

/* 6. Pagination Footer */
.pagination-footer-card {
  background: #FFFFFF;
  border-radius: 14px;
  padding: 14px 20px;
  border: 1px solid #E2E8F0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.pagination-info {
  font-size: 12px;
  color: #64748B;
}

.pagination-nav-group {
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-nav-page {
  padding: 6px 12px;
  background: #F8FAFC;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 12px;
  color: #334155;
  cursor: pointer;
}

.btn-nav-page:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.page-pills {
  display: flex;
  align-items: center;
  gap: 4px;
}

.btn-page-number {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  border: none;
  background: transparent;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-page-number.active {
  background: #1B5E20;
  color: #FFFFFF;
}

.page-dots {
  padding: 0 4px;
  font-size: 12px;
  color: #94A3B8;
}

/* Modals */
.modal-backdrop-fixed {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(2px);
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-dialog-box {
  background: #FFFFFF;
  border-radius: 18px;
  padding: 24px;
  max-width: 440px;
  width: 100%;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-head-row {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 14px;
}

.modal-icon-badge {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-title {
  font-size: 17px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
}

.modal-subtitle {
  font-size: 12px;
  color: #64748B;
  margin: 2px 0 0;
}

.modal-prompt-text {
  font-size: 13px;
  color: #334155;
  line-height: 1.5;
  margin: 0 0 14px;
}

.modal-recap-box {
  background: #F0FDF4;
  border: 1px solid #DCFCE7;
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 12px;
  color: #166534;
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 20px;
}

.modal-recap-box p {
  margin: 0;
}

.export-options-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin: 16px 0 20px;
}

.export-option-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  border: 1.5px solid #E2E8F0;
  background: #FFFFFF;
  cursor: pointer;
  transition: all 0.2s;
}

.export-option-item.active {
  border-color: #1B5E20;
  background: #F0FDF4;
}

.opt-title {
  display: block;
  font-size: 13px;
  color: #0F172A;
}

.opt-desc {
  font-size: 11px;
  color: #64748B;
}

.modal-footer-btns {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-modal-cancel {
  padding: 9px 16px;
  background: #F1F5F9;
  color: #475569;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-modal-confirm {
  padding: 9px 18px;
  background: #1B5E20;
  color: #FFFFFF;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR HISTORY VIEW
   ========================================================================== */
:global(.dark-theme) .history-view-page {
  background: #071E27;
}

:global(.dark-theme) .history-header-card,
:global(.dark-theme) .kpi-metric-card,
:global(.dark-theme) .filter-toolbar-card,
:global(.dark-theme) .batch-item-card,
:global(.dark-theme) .history-table-container,
:global(.dark-theme) .modal-dialog-box,
:global(.dark-theme) .empty-state-card {
  background: #0B242F;
  border-color: #1E4E61;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

:global(.dark-theme) .page-title,
:global(.dark-theme) .kpi-number,
:global(.dark-theme) .kpi-number.text-dark,
:global(.dark-theme) .batch-code,
:global(.dark-theme) .crop-name,
:global(.dark-theme) .export-modal-title,
:global(.dark-theme) .modal-title,
:global(.dark-theme) .opt-title,
:global(.dark-theme) .batch-code-link {
  color: #F8FAFC !important;
}

:global(.dark-theme) .page-subtitle,
:global(.dark-theme) .kpi-label,
:global(.dark-theme) .kpi-unit,
:global(.dark-theme) .meta-text,
:global(.dark-theme) .m-lbl,
:global(.dark-theme) .filter-label,
:global(.dark-theme) .opt-desc,
:global(.dark-theme) .empty-subtext,
:global(.dark-theme) .pagination-info,
:global(.dark-theme) .modal-subtitle {
  color: #94A3B8;
}

:global(.dark-theme) .card-metrics-box,
:global(.dark-theme) .modal-recap-box {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .search-input {
  background: #071E27;
  border-color: #1E4E61;
  color: #F8FAFC;
}

:global(.dark-theme) .search-input:focus {
  background: #0B242F;
  border-color: #16A34A;
}

:global(.dark-theme) .filter-pill {
  background: #113847;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .filter-pill.active {
  background: #1B5E20;
  border-color: #1B5E20;
  color: #FFFFFF;
}

:global(.dark-theme) .btn-header-secondary,
:global(.dark-theme) .btn-qr-label,
:global(.dark-theme) .btn-modal-cancel,
:global(.dark-theme) .btn-nav-page,
:global(.dark-theme) .btn-page-number {
  background: #113847;
  color: #F8FAFC;
  border-color: #1E4E61;
}

:global(.dark-theme) .btn-header-secondary:hover,
:global(.dark-theme) .btn-qr-label:hover,
:global(.dark-theme) .btn-modal-cancel:hover,
:global(.dark-theme) .btn-nav-page:hover:not(:disabled),
:global(.dark-theme) .btn-page-number:hover {
  background: #164657;
  border-color: #2D6B82;
}

:global(.dark-theme) .btn-page-number.active {
  background: #1B5E20;
  border-color: #1B5E20;
  color: #FFFFFF;
}

:global(.dark-theme) .table-head-th {
  background: #09202A;
  color: #94A3B8;
  border-bottom-color: #1E4E61;
}

:global(.dark-theme) .table-batch-row {
  border-bottom-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .table-batch-row:hover {
  background: rgba(255, 255, 255, 0.04);
}

:global(.dark-theme) .export-option-item {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .export-option-item.active {
  background: rgba(27, 94, 32, 0.2);
  border-color: #1B5E20;
}

:global(.dark-theme) .modal-prompt-text {
  color: #CBD5E1;
}
</style>
