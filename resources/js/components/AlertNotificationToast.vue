<template>
  <Transition name="framer-toast">
    <div 
      v-if="modelValue" 
      class="alert-toast-container" 
      :class="[`type-${alertData.type || 'info'}`]"
      role="alert"
    >
      <!-- Severity Icon Circle -->
      <div class="toast-icon-circle">
        <!-- Critical / Danger -->
        <svg v-if="alertData.type === 'critical'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>

        <!-- Warning -->
        <svg v-else-if="alertData.type === 'warning'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
          <line x1="12" y1="9" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>

        <!-- Success -->
        <svg v-else-if="alertData.type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>

        <!-- Info (Default) -->
        <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
      </div>

      <!-- Alert Content Body -->
      <div class="toast-body">
        <div class="toast-header-row">
          <span class="toast-badge">{{ typeBadgeText }}</span>
          <span class="toast-time">{{ alertData.time || 'Baru saja' }}</span>
        </div>
        <h4 class="toast-title">{{ displayTitle }}</h4>
        <p class="toast-desc">{{ displayMessage }}</p>

        <!-- Quick Action Button if available -->
        <div v-if="alertData.actionLabel" class="toast-action-row">
          <button class="toast-action-btn" @click="handleAction">
            <span>{{ alertData.actionLabel }}</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>

      <!-- Close Button -->
      <button class="toast-close-btn" @click="closeToast" title="Tutup Notifikasi">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <!-- Auto-dismiss Progress Bar -->
      <div v-if="duration > 0" class="toast-progress-bar" :style="{ animationDuration: `${duration}ms` }"></div>
    </div>
  </Transition>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  alertData: {
    type: Object,
    default: () => ({
      type: 'info',
      title: 'Pemberitahuan Sistem',
      message: 'Tidak ada pesan.',
      time: 'Baru saja',
      actionLabel: '',
      actionRoute: ''
    })
  },
  duration: {
    type: Number,
    default: 5000
  },
  currentLang: {
    type: String,
    default: 'id'
  }
})

const emit = defineEmits(['update:modelValue', 'action', 'close'])

let timer = null

function simplifyAlert(title = '', message = '') {
  let cleanTitle = title || 'Pemberitahuan Sistem'
  let cleanMsg = message || ''

  cleanTitle = cleanTitle
    .replace(/^Anomali Termal:\s*/i, '')
    .replace(/^Anomali Sensor:\s*/i, '')
    .replace(/^Anomali Kadar Air:\s*/i, '')
    .replace(/^Anomali Suhu:\s*/i, '')
    .replace(/^Peringatan:\s*/i, '')
    .trim()

  if (cleanTitle.toLowerCase().includes('pintu greenhouse terbuka') || cleanTitle.toLowerCase().includes('drop suhu')) {
    cleanTitle = 'Pintu Terbuka / Suhu Drop'
  } else if (cleanTitle.toLowerCase().includes('penurunan suhu mendadak')) {
    cleanTitle = 'Suhu Ruangan Turun'
  } else if (cleanTitle.toLowerCase().includes('lonjakan kelembaban') || cleanTitle.toLowerCase().includes('lonjakan kadar air')) {
    cleanTitle = 'Kadar Air Biji Naik'
  } else if (cleanTitle.toLowerCase().includes('pembacaan macet') || cleanTitle.toLowerCase().includes('flatline')) {
    cleanTitle = 'Koneksi Sensor Macet'
  }

  if (cleanMsg.includes('Auxiliary Heater') || cleanMsg.includes('LEBIH RENDAH')) {
    const tempMatch = cleanMsg.match(/Suhu ruang pengering \(([^)]+)\)/i)
    const temp = tempMatch ? tempMatch[1] : 'ruangan'
    cleanMsg = `Suhu ${temp} lebih dingin dari luar saat pemanas aktif. Mohon periksa apakah pintu greenhouse tertutup rapat.`
  } else if (cleanMsg.toLowerCase().includes('flatline') || cleanMsg.toLowerCase().includes('tidak berubah sama sekali')) {
    cleanMsg = 'Data sensor tidak bergerak. Periksa sambungan kabel sensor atau catu daya alat.'
  } else if (cleanMsg.toLowerCase().includes('melonjak naik') || cleanMsg.toLowerCase().includes('kebocoran atap')) {
    cleanMsg = 'Kadar air biji naik mendadak. Periksa apakah ada tetesan air atau atap bocor di atas rak.'
  } else if (cleanMsg.includes('Rekomendasi Aksi:')) {
    const parts = cleanMsg.split(/Rekomendasi Aksi:\s*/i)
    cleanMsg = parts[1] ? parts[1].trim() : parts[0].trim()
  }

  return { title: cleanTitle, message: cleanMsg }
}

const simplifiedData = computed(() => {
  return simplifyAlert(props.alertData.title, props.alertData.message)
})

const displayTitle = computed(() => simplifiedData.value.title)
const displayMessage = computed(() => simplifiedData.value.message)

const typeBadgeText = computed(() => {
  const isId = props.currentLang === 'id'
  switch (props.alertData.type) {
    case 'critical':
      return isId ? 'PERINGATAN KRITIS' : 'CRITICAL ALERT'
    case 'warning':
      return isId ? 'PERINGATAN' : 'WARNING'
    case 'success':
      return isId ? 'SUKSES' : 'SUCCESS'
    default:
      return isId ? 'INFO SISTEM' : 'SYSTEM INFO'
  }
})

function closeToast() {
  emit('update:modelValue', false)
  emit('close')
}

function handleAction() {
  emit('action', props.alertData)
  closeToast()
}

watch(() => props.modelValue, (newVal) => {
  if (timer) clearTimeout(timer)
  if (newVal && props.duration > 0) {
    timer = setTimeout(() => {
      closeToast()
    }, props.duration)
  }
})
</script>

<style scoped>
.alert-toast-container {
  position: fixed;
  top: 24px;
  right: 24px;
  max-width: 420px;
  width: calc(100vw - 48px);
  background: #FFFFFF;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  z-index: 9999;
  overflow: hidden;
}

/* Severity Theme Variants (Soft, modern, no harsh thick border) */
.alert-toast-container.type-critical .toast-icon-circle {
  background: #FEE2E2;
  color: #DC2626;
}
.alert-toast-container.type-critical .toast-badge {
  background: #FEE2E2;
  color: #DC2626;
}

.alert-toast-container.type-warning .toast-icon-circle {
  background: #FEF3C7;
  color: #D97706;
}
.alert-toast-container.type-warning .toast-badge {
  background: #FEF3C7;
  color: #D97706;
}

.alert-toast-container.type-success .toast-icon-circle {
  background: #DCFCE7;
  color: #166534;
}
.alert-toast-container.type-success .toast-badge {
  background: #DCFCE7;
  color: #166534;
}

.alert-toast-container.type-info .toast-icon-circle {
  background: #E0F2FE;
  color: #0284C7;
}
.alert-toast-container.type-info .toast-badge {
  background: #E0F2FE;
  color: #0284C7;
}

/* Icon */
.toast-icon-circle {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Body */
.toast-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.toast-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.toast-badge {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.5px;
  padding: 2px 7px;
  border-radius: 4px;
}

.toast-time {
  font-size: 11px;
  color: #94A3B8;
  font-weight: 500;
}

.toast-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #0F172A;
  line-height: 1.35;
  margin: 1px 0 0;
}

.toast-desc {
  font-size: 12px;
  color: #475569;
  line-height: 1.45;
  margin: 2px 0 0;
}

.toast-action-row {
  margin-top: 6px;
}

.toast-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 12px;
  background: #E8F5E9;
  color: #1B5E20;
  border: none;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.toast-action-btn:hover {
  background: #1B5E20;
  color: #FFFFFF;
}

/* Close Button */
.toast-close-btn {
  background: transparent;
  color: #94A3B8;
  border: none;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}

.toast-close-btn:hover {
  background: #F1F5F9;
  color: #64748B;
}

/* Progress bar */
.toast-progress-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  width: 100%;
  background: currentColor;
  opacity: 0.25;
  animation: shrinkProgress linear forwards;
}

@keyframes shrinkProgress {
  from { width: 100%; }
  to { width: 0%; }
}

/* Framer Motion Slide In / Out */
.framer-toast-enter-active {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1),
              opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.framer-toast-leave-active {
  transition: transform 0.25s cubic-bezier(0.4, 0, 1, 1),
              opacity 0.25s cubic-bezier(0.4, 0, 1, 1);
}

.framer-toast-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.92);
}

.framer-toast-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.96);
}

/* Dark theme support */
:global(.dark-theme) .alert-toast-container {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}
:global(.dark-theme) .alert-toast-container.type-critical {
  border-left: 5px solid #EF4444 !important;
  background: linear-gradient(135deg, #0B242F 80%, rgba(239, 68, 68, 0.15) 100%) !important;
}
:global(.dark-theme) .alert-toast-container.type-critical .toast-icon-circle,
:global(.dark-theme) .alert-toast-container.type-critical .toast-badge {
  background: rgba(239, 68, 68, 0.2) !important;
  color: #F87171 !important;
}

:global(.dark-theme) .alert-toast-container.type-warning {
  border-left: 5px solid #F59E0B !important;
  background: linear-gradient(135deg, #0B242F 80%, rgba(245, 158, 11, 0.15) 100%) !important;
}
:global(.dark-theme) .alert-toast-container.type-warning .toast-icon-circle,
:global(.dark-theme) .alert-toast-container.type-warning .toast-badge {
  background: rgba(245, 158, 11, 0.2) !important;
  color: #FBBF24 !important;
}

:global(.dark-theme) .alert-toast-container.type-success {
  border-left: 5px solid #10B981 !important;
  background: linear-gradient(135deg, #0B242F 80%, rgba(16, 185, 129, 0.15) 100%) !important;
}
:global(.dark-theme) .alert-toast-container.type-success .toast-icon-circle,
:global(.dark-theme) .alert-toast-container.type-success .toast-badge {
  background: rgba(16, 185, 129, 0.2) !important;
  color: #34D399 !important;
}

:global(.dark-theme) .alert-toast-container.type-info {
  border-left: 5px solid #38BDF8 !important;
  background: linear-gradient(135deg, #0B242F 80%, rgba(56, 189, 248, 0.15) 100%) !important;
}
:global(.dark-theme) .alert-toast-container.type-info .toast-icon-circle,
:global(.dark-theme) .alert-toast-container.type-info .toast-badge {
  background: rgba(56, 189, 248, 0.2) !important;
  color: #38BDF8 !important;
}

:global(.dark-theme) .toast-title {
  color: #F8FAFC !important;
}
:global(.dark-theme) .toast-desc {
  color: #CBD5E1 !important;
}
:global(.dark-theme) .toast-time {
  color: #94A3B8 !important;
}
:global(.dark-theme) .toast-action-btn {
  background: #1B5E20 !important;
  color: #FFFFFF !important;
}
:global(.dark-theme) .toast-close-btn {
  color: #94A3B8 !important;
}
:global(.dark-theme) .toast-close-btn:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  color: #FFFFFF !important;
}
</style>
