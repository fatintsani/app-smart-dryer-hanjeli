<template>
  <div class="admin-page">
    <Transition name="fade-admin-logs" mode="out-in">
      <AdminSkeleton v-if="isInitialLoading" />
      <div v-else class="admin-logs-loaded-content">
        <div class="admin-content">
      <!-- Top Title & Action Bar -->
      <div class="header-action-row">
        <div class="title-group">
          <h1 class="page-title">{{ $t('admin.tabLogs') }}</h1>
          <p class="page-subtitle">Rekam jejak aktivitas sistem, audit keamanan sesi, dan riwayat paket telemetri IoT.</p>
        </div>

        <div class="header-actions">
          <button class="btn-secondary-action" @click="exportLogsToCsv">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>{{ $t('admin.exportLogs') }}</span>
          </button>
        </div>
      </div>

      <!-- Logs Content Card -->
      <div class="content-card">
        <div class="card-toolbar">
          <div>
            <h2 class="card-section-heading">Catatan Audit Sistem</h2>
            <p class="card-section-sub">Menampilkan {{ filteredLogs.length }} dari {{ systemAuditLogs.length }} riwayat log tersimpan.</p>
          </div>

          <div class="log-filter-chips">
            <button 
              class="filter-chip" 
              :class="{ active: logFilter === 'ALL' }" 
              @click="logFilter = 'ALL'"
            >
              Semua ({{ systemAuditLogs.length }})
            </button>
            <button 
              class="filter-chip" 
              :class="{ active: logFilter === 'INFO' }" 
              @click="logFilter = 'INFO'"
            >
              Info
            </button>
            <button 
              class="filter-chip" 
              :class="{ active: logFilter === 'WARNING' }" 
              @click="logFilter = 'WARNING'"
            >
              Warning
            </button>
          </div>
        </div>

        <div class="logs-feed-list">
          <div 
            v-for="log in filteredLogs" 
            :key="log.id" 
            class="log-entry-row" 
            :class="`border-${log.level.toLowerCase()}`"
          >
            <span class="log-timestamp">{{ log.time }}</span>
            <span class="badge-pill" :class="log.level === 'WARNING' ? 'bg-orange-subtle text-orange' : log.level === 'ERROR' ? 'bg-red-subtle text-danger' : 'bg-blue-subtle text-blue'">
              {{ log.level }}
            </span>
            <span class="log-category-tag">[{{ log.category }}]</span>
            <span class="log-msg-text">{{ log.message }}</span>
          </div>
        </div>
        </div>
      </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AdminSkeleton from '../../components/AdminSkeleton.vue'
import { alertService } from '../../services/alertService'
import { socketService } from '../../services/socketService'

const isInitialLoading = ref(true)
const logFilter = ref('ALL')
const systemAuditLogs = ref([])
const isLoading = ref(false)

function formatTime(timestamp) {
  if (!timestamp) return 'Baru saja'
  try {
    const d = new Date(timestamp)
    return d.toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' })
  } catch {
    return 'Baru saja'
  }
}

async function fetchAuditLogs() {
  isLoading.value = true
  try {
    const res = await alertService.getAll()
    const alerts = Array.isArray(res) ? res : res?.alerts || []
    if (alerts.length > 0) {
      systemAuditLogs.value = alerts.map(a => ({
        id: a.id,
        time: formatTime(a.createdAt || a.created_at),
        level: a.type === 'DANGER' ? 'ERROR' : a.type === 'WARNING' ? 'WARNING' : 'INFO',
        category: a.category || 'SYSTEM',
        message: a.message || a.title
      }))
    } else {
      systemAuditLogs.value = []
    }
  } catch (err) {
    console.warn('Could not fetch audit logs:', err.message)
    systemAuditLogs.value = []
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  try {
    await fetchAuditLogs()
  } finally {
    setTimeout(() => {
      isInitialLoading.value = false
    }, 350)
  }

  socketService.on('alert_new', (newAlert) => {
    systemAuditLogs.value.unshift({
      id: newAlert.id || Date.now(),
      time: 'Baru saja',
      level: newAlert.type === 'DANGER' ? 'ERROR' : newAlert.type === 'WARNING' ? 'WARNING' : 'INFO',
      category: newAlert.category || 'SYSTEM',
      message: newAlert.message || newAlert.title
    })
  })
})

onUnmounted(() => {
  socketService.off('alert_new')
})

const filteredLogs = computed(() => {
  if (logFilter.value === 'ALL') return systemAuditLogs.value
  return systemAuditLogs.value.filter(l => l.level === logFilter.value)
})

function exportLogsToCsv() {
  const rows = [
    ['Waktu', 'Tingkat', 'Kategori', 'Pesan'],
    ...systemAuditLogs.value.map(l => [l.time, l.level, l.category, l.message])
  ]
  const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.join(',')).join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `smart_dryer_audit_logs_${Date.now()}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}
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

.content-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.card-section-heading {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text-title);
}

.card-section-sub {
  font-size: 13px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

.log-filter-chips {
  display: flex;
  gap: 8px;
}

.filter-chip {
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 6px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s;
}

.filter-chip.active {
  background: #0D631B;
  border-color: #0D631B;
  color: #FFFFFF;
}

.logs-feed-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-family: 'Consolas', monospace;
  font-size: 13px;
}

.log-entry-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: var(--color-bg);
  border-radius: 8px;
  border-left: 4px solid var(--color-border);
}

.log-entry-row.border-warning {
  border-left-color: #F59E0B;
  background: rgba(245, 158, 11, 0.05);
}

.log-timestamp {
  color: var(--color-text-muted);
  font-size: 12px;
  white-space: nowrap;
}

.log-category-tag {
  color: var(--color-text-muted);
  font-weight: 600;
}

.log-msg-text {
  color: var(--color-text-title);
  flex: 1;
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

.badge-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
}

.bg-blue-subtle { background: rgba(0, 93, 183, 0.12); }
.text-blue { color: #005DB7; }
.bg-orange-subtle { background: rgba(180, 80, 0, 0.12); }
.text-orange { color: #B45000; }
.bg-red-subtle { background: rgba(220, 38, 38, 0.12); }
.text-danger { color: #DC2626; }

@media (max-width: 900px) {
  .header-action-row { flex-direction: column; align-items: stretch; gap: 12px; }
}

@media (max-width: 768px) {
  .admin-content {
    padding: 0;
    gap: 16px;
  }
  .page-title {
    font-size: 22px;
  }
  .page-subtitle {
    font-size: 13.5px;
  }
}

.fade-admin-logs-enter-active,
.fade-admin-logs-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-admin-logs-enter-from {
  opacity: 0;
  transform: translateY(6px);
}
.fade-admin-logs-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
