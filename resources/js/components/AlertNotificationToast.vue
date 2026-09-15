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
        <h4 class="toast-title">{{ alertData.title }}</h4>
        <p class="toast-desc">{{ alertData.message }}</p>

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
  max-width: 400px;
  width: calc(100vw - 48px);
  background: #FFFFFF;
  border-radius: 14px;
  padding: 16px 18px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
    border: 1px solid #E2E8F0;
  z-index: 9999;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

/* Severity Theme Variants */
.alert-toast-container.type-critical {
  border-left: 5px solid #BA1A1A;
  background: linear-gradient(135deg, #FFFFFF 85%, #FFF5F5 100%);
}
.alert-toast-container.type-critical .toast-icon-circle {
  background: rgba(186, 26, 26, 0.12);
  color: #BA1A1A;
}
.alert-toast-container.type-critical .toast-badge {
  background: rgba(186, 26, 26, 0.12);
  color: #BA1A1A;
}

.alert-toast-container.type-warning {
  border-left: 5px solid #D97706;
  background: linear-gradient(135deg, #FFFFFF 85%, #FFFBEB 100%);
}
.alert-toast-container.type-warning .toast-icon-circle {
  background: rgba(217, 119, 6, 0.12);
  color: #D97706;
}
.alert-toast-container.type-warning .toast-badge {
  background: rgba(217, 119, 6, 0.12);
  color: #D97706;
}

.alert-toast-container.type-success {
  border-left: 5px solid #0D631B;
  background: linear-gradient(135deg, #FFFFFF 85%, #F0FDF4 100%);
}
.alert-toast-container.type-success .toast-icon-circle {
  background: rgba(13, 99, 27, 0.12);
  color: #0D631B;
}
.alert-toast-container.type-success .toast-badge {
  background: rgba(13, 99, 27, 0.12);
  color: #0D631B;
}

.alert-toast-container.type-info {
  border-left: 5px solid #005DB7;
  background: linear-gradient(135deg, #FFFFFF 85%, #F0F9FF 100%);
}
.alert-toast-container.type-info .toast-icon-circle {
  background: rgba(0, 93, 183, 0.12);
  color: #005DB7;
}
.alert-toast-container.type-info .toast-badge {
  background: rgba(0, 93, 183, 0.12);
  color: #005DB7;
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
  gap: 4px;
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
  letter-spacing: 0.6px;
  padding: 2px 7px;
  border-radius: 4px;
}

.toast-time {
  font-size: 11px;
  color: #64748B;
}

.toast-title {
  font-size: 14px;
  font-weight: 700;
  color: #0F172A;
  margin-top: 2px;
}

.toast-desc {
  font-size: 12.5px;
  color: #475569;
  line-height: 1.4;
}

.toast-action-row {
  margin-top: 6px;
}

.toast-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 12px;
  background: #0F172A;
  color: #FFFFFF;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.toast-action-btn:hover {
  background: #1E293B;
  transform: translateX(2px);
}

/* Close Button */
.toast-close-btn {
  background: transparent;
  color: #94A3B8;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
}

.toast-close-btn:hover {
  background: rgba(0, 0, 0, 0.05);
  color: #334155;
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
  background: #0D631B !important;
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
