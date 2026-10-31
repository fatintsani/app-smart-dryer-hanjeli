<template>
  <Teleport to="body">
    <transition name="modal-fade">
      <div v-if="isOpen" class="ble-modal-overlay" @click.self="close">
        <div class="ble-modal-container" role="dialog" aria-modal="true">
          <!-- Modal Header -->
          <div class="ble-modal-header">
            <div class="header-main-info">
              <div class="header-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
                </svg>
              </div>
              <div class="header-titles">
                <h3 class="modal-title">{{ $t('modals.bleTitle') || 'Sambungkan Alat Pengering' }}</h3>
                <p class="modal-subtitle">{{ $t('modals.bleSub') || 'Hubungkan alat ke Wi-Fi atau Hotspot HP melalui Bluetooth.' }}</p>
              </div>
            </div>
            <button type="button" class="btn-close-modal" @click="close" :title="$t('common.close') || 'Tutup'">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <div class="ble-modal-body">
            <!-- 1. Connection Status Bar -->
            <div v-if="!isBleConnected" class="status-bar status-disconnected">
              <div class="status-info">
                <span class="status-dot dot-amber"></span>
                <span class="status-text">Bluetooth belum terhubung ke alat</span>
              </div>
              <button 
                type="button" 
                class="btn-status-action btn-status-amber"
                :disabled="isConnecting"
                @click="handleConnectBle"
              >
                <span v-if="isConnecting" class="spinner-xs"></span>
                <span v-else>{{ isConnecting ? 'Mencari...' : 'Sambungkan' }}</span>
              </button>
            </div>

            <div v-else class="status-bar status-connected">
              <div class="status-info">
                <span class="status-dot dot-green"></span>
                <span class="status-text">Terhubung: <strong>{{ currentDeviceId }}</strong></span>
              </div>
              <button 
                type="button" 
                class="btn-status-action btn-status-red"
                @click="handleDisconnectBle"
              >
                Putuskan
              </button>
            </div>

            <!-- 2. Form Fields -->
            <form @submit.prevent="submitConfig" class="ble-form">
              <!-- Device ID -->
              <div class="form-group">
                <div class="field-label-row">
                  <label class="field-label">Nama Alat Pengering</label>
                  <span class="field-hint">Bawaan: ESP32-GreenHouse</span>
                </div>
                <div class="input-wrap">
                  <div class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                      <rect x="9" y="9" width="6" height="6"></rect>
                      <line x1="9" y1="1" x2="9" y2="4"></line>
                      <line x1="15" y1="1" x2="15" y2="4"></line>
                      <line x1="9" y1="20" x2="9" y2="23"></line>
                      <line x1="15" y1="20" x2="15" y2="23"></line>
                    </svg>
                  </div>
                  <input 
                    v-model="form.deviceId" 
                    type="text" 
                    class="form-input" 
                    placeholder="Contoh: ESP32-GreenHouse"
                    required
                  />
                </div>
              </div>

              <!-- Wi-Fi SSID -->
              <div class="form-group">
                <div class="field-label-row">
                  <label class="field-label">Nama Wi-Fi / Hotspot HP</label>
                  <span class="field-hint">Jaringan 2.4 GHz</span>
                </div>
                <div class="input-wrap">
                  <div class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                      <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                      <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                      <line x1="12" y1="20" x2="12.01" y2="20"></line>
                    </svg>
                  </div>
                  <input 
                    v-model="form.ssid" 
                    type="text" 
                    class="form-input" 
                    placeholder="Nama Wi-Fi atau Hotspot Anda"
                    required
                  />
                </div>
              </div>

              <!-- Wi-Fi Password -->
              <div class="form-group">
                <div class="field-label-row">
                  <label class="field-label">Kata Sandi Wi-Fi</label>
                  <span class="field-hint">Kosongkan jika tanpa sandi</span>
                </div>
                <div class="input-wrap">
                  <div class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                  </div>
                  <input 
                    v-model="form.password" 
                    :type="showPassword ? 'text' : 'password'" 
                    class="form-input with-toggle" 
                    placeholder="Masukkan kata sandi Wi-Fi"
                  />
                  <button 
                    type="button" 
                    class="btn-toggle-eye"
                    @click="showPassword = !showPassword"
                    :title="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                  >
                    <svg v-if="!showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                      <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Collapsible Advanced Settings (MQTT) -->
              <div class="advanced-section">
                <button 
                  type="button" 
                  class="btn-toggle-advanced"
                  @click="showAdvanced = !showAdvanced"
                >
                  <span class="advanced-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="3"></circle>
                      <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Pengaturan Lanjutan (Server MQTT)
                  </span>
                  <svg class="chevron-icon" :class="{ 'rotate-180': showAdvanced }" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>

                <div v-if="showAdvanced" class="advanced-panel">
                  <div class="advanced-grid">
                    <div class="form-group flex-2">
                      <div class="field-label-row">
                        <label class="field-label-sm">Server MQTT</label>
                        <button type="button" class="btn-preset-link" @click="useDefaultBroker">Reset HiveMQ</button>
                      </div>
                      <input 
                        v-model="form.mqttBroker" 
                        type="text" 
                        class="form-input form-input-sm" 
                        placeholder="broker.hivemq.com"
                      />
                    </div>
                    <div class="form-group flex-1">
                      <label class="field-label-sm">Port</label>
                      <input 
                        v-model="form.mqttPort" 
                        type="number" 
                        class="form-input form-input-sm text-center" 
                        placeholder="1883"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Feedback Notice -->
              <transition name="fade">
                <div v-if="feedbackMessage" :class="feedbackClass" class="feedback-banner">
                  <div class="feedback-icon">
                    <svg v-if="feedbackSuccess" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="8" x2="12" y2="12"></line>
                      <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                  </div>
                  <span class="feedback-text">{{ feedbackMessage }}</span>
                </div>
              </transition>

              <!-- Modal Footer Actions -->
              <div class="modal-footer-actions">
                <button type="button" class="btn-cancel" @click="close">
                  Batal
                </button>
                <button 
                  type="submit" 
                  class="btn-submit"
                  :disabled="isSubmitting || isConnecting"
                >
                  <span v-if="isSubmitting || isConnecting" class="spinner-sm"></span>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                  <span>{{ submitButtonLabel }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import connectionManager from '../services/connectionManager';
import bleService from '../services/ble/bleService';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close']);

const showPassword = ref(false);
const showAdvanced = ref(false);
const isSubmitting = ref(false);
const isConnecting = ref(false);
const feedbackMessage = ref('');
const feedbackSuccess = ref(false);

const isBleConnected = computed(() => bleService.isConnected);
const currentDeviceId = computed(() => connectionManager.state.deviceId || 'ESP32-GreenHouse');

const form = reactive({
  deviceId: connectionManager.state.deviceId || 'ESP32-GreenHouse',
  ssid: connectionManager.state.hotspotName || '',
  password: '',
  mqttBroker: 'broker.hivemq.com',
  mqttPort: 1883,
});

watch(() => props.isOpen, (open) => {
  if (open) {
    form.deviceId = connectionManager.state.deviceId || 'ESP32-GreenHouse';
    form.ssid = connectionManager.state.hotspotName || '';
    feedbackMessage.value = '';
  }
});

const feedbackClass = computed(() => {
  return feedbackSuccess.value ? 'banner-success' : 'banner-error';
});

const submitButtonLabel = computed(() => {
  if (isSubmitting.value) return 'Menyimpan ke Alat...';
  if (isConnecting.value) return 'Mencari Bluetooth...';
  if (!isBleConnected.value) return 'Sambungkan & Simpan';
  return 'Simpan ke Alat';
});

const useDefaultBroker = () => {
  form.mqttBroker = 'broker.hivemq.com';
  form.mqttPort = 1883;
};

const handleConnectBle = async () => {
  if (isConnecting.value) return false;
  isConnecting.value = true;
  feedbackMessage.value = '';

  try {
    connectionManager.state.mode = 'ble';
    const result = await connectionManager.connect();
    if (result && result.success) {
      feedbackSuccess.value = true;
      feedbackMessage.value = 'Berhasil terhubung ke alat via Bluetooth!';
      return true;
    }
    return false;
  } catch (e) {
    if (e.message && !e.message.includes('dibatalkan')) {
      feedbackSuccess.value = false;
      feedbackMessage.value = `Gagal terhubung: ${e.message}`;
    }
    return false;
  } finally {
    isConnecting.value = false;
  }
};

const handleDisconnectBle = async () => {
  await connectionManager.disconnect();
  feedbackMessage.value = '';
};

const submitConfig = async () => {
  // Always save deviceId and hotspotName locally
  if (form.deviceId) connectionManager.setDeviceId(form.deviceId);
  if (form.ssid) connectionManager.setHotspotName(form.ssid);

  // If not connected to BLE yet, auto connect first
  if (!isBleConnected.value) {
    feedbackSuccess.value = false;
    feedbackMessage.value = 'Mencari dan menyambungkan ke Bluetooth alat...';
    const connected = await handleConnectBle();
    if (!connected) {
      feedbackMessage.value = 'Belum terhubung ke alat. Pastikan Bluetooth aktif dan alat pengering menyala.';
      return;
    }
  }

  isSubmitting.value = true;
  feedbackMessage.value = '';

  try {
    await connectionManager.configureWifiOverBle({
      ssid: form.ssid,
      password: form.password,
      mqttBroker: form.mqttBroker,
      mqttPort: form.mqttPort,
      deviceId: form.deviceId,
    });

    feedbackSuccess.value = true;
    feedbackMessage.value = 'Pengaturan Wi-Fi berhasil dikirim ke alat! Alat sedang menyambung ke jaringan.';
    
    // Mask password after successful submission
    form.password = '';
  } catch (err) {
    feedbackSuccess.value = false;
    feedbackMessage.value = `Gagal mengirim pengaturan: ${err.message}`;
  } finally {
    isSubmitting.value = false;
  }
};

const close = () => {
  feedbackMessage.value = '';
  emit('close');
};
</script>

<style scoped>
/* Modal Fade Transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from .ble-modal-container,
.modal-fade-leave-to .ble-modal-container {
  transform: scale(0.96) translateY(8px);
}

/* Modal Overlay Backdrop */
.ble-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  padding: 16px;
  overflow-y: auto;
}

/* Modal Container Dialog */
.ble-modal-container {
  background: #FFFFFF;
  border-radius: 18px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.dark-theme .ble-modal-container,
:global(.dark-theme) .ble-modal-container {
  background: #0B242F !important;
  border: 1px solid #1E4E61 !important;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
}

/* Modal Header */
.ble-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 18px 22px;
  border-bottom: 1px solid #E2E8F0;
  background: #FAFDFB;
}

.dark-theme .ble-modal-header,
:global(.dark-theme) .ble-modal-header {
  background: #09202A !important;
  border-bottom-color: #1E4E61 !important;
}

.header-main-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-icon-badge {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #E8F5E9;
  color: #1B5E20;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dark-theme .header-icon-badge,
:global(.dark-theme) .header-icon-badge {
  background: rgba(27, 94, 32, 0.25);
  color: #81C784;
}

.header-titles {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.modal-title {
  font-size: 15px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1.3;
}

.dark-theme .modal-title,
:global(.dark-theme) .modal-title {
  color: #F8FAFC !important;
}

.modal-subtitle {
  font-size: 12px;
  color: #64748B;
  margin: 0;
  line-height: 1.35;
}

.dark-theme .modal-subtitle,
:global(.dark-theme) .modal-subtitle {
  color: #94A3B8 !important;
}

.btn-close-modal {
  background: transparent;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-close-modal:hover {
  background: #F1F5F9;
  color: #0F172A;
}

:global(.dark-theme) .btn-close-modal:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

/* Modal Body */
.ble-modal-body {
  padding: 18px 22px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* Status Bar */
.status-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 12.5px;
  transition: all 0.2s ease;
}

.status-disconnected {
  background: #FFFBEB;
  border: 1px solid #FDE68A;
  color: #92400E;
}

:global(.dark-theme) .status-disconnected {
  background: rgba(245, 158, 11, 0.12);
  border-color: rgba(245, 158, 11, 0.25);
  color: #FCD34D;
}

.status-connected {
  background: #ECFDF5;
  border: 1px solid #A7F3D0;
  color: #065F46;
}

:global(.dark-theme) .status-connected {
  background: rgba(16, 185, 129, 0.12);
  border-color: rgba(16, 185, 129, 0.25);
  color: #6EE7B7;
}

.status-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.dot-amber {
  background: #D97706;
}

.dot-green {
  background: #10B981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
}

.status-text {
  font-weight: 500;
}

.btn-status-action {
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.btn-status-amber {
  background: #F59E0B;
  color: #FFFFFF;
}

.btn-status-amber:hover:not(:disabled) {
  background: #D97706;
}

.btn-status-red {
  background: #FEE2E2;
  color: #DC2626;
}

.btn-status-red:hover {
  background: #DC2626;
  color: #FFFFFF;
}

/* Form Styles */
.ble-form {
  display: flex;
  flex-direction: column;
  gap: 13px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.field-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.field-label {
  font-size: 12.5px;
  font-weight: 600;
  color: #1E293B;
}

.dark-theme .field-label,
:global(.dark-theme) .field-label {
  color: #E2E8F0;
}

.field-hint {
  font-size: 11px;
  color: #64748B;
}

.dark-theme .field-hint,
:global(.dark-theme) .field-hint {
  color: #94A3B8;
}

.input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #94A3B8;
  display: flex;
  align-items: center;
  pointer-events: none;
}

.form-input {
  width: 100%;
  height: 40px;
  padding: 0 12px 0 36px;
  border-radius: 10px;
  border: 1.5px solid #E2E8F0;
  background: #F8FAFC;
  font-size: 13px;
  color: #0F172A;
  outline: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

:global(.dark-theme) .form-input {
  background: #09202A;
  border-color: #1E3A47;
  color: #F8FAFC;
}

.form-input:focus {
  border-color: #1B5E20;
  background: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.12);
}

:global(.dark-theme) .form-input:focus {
  background: #0E2C39;
  border-color: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
}

.form-input.with-toggle {
  padding-right: 36px;
}

.btn-toggle-eye {
  position: absolute;
  right: 8px;
  background: transparent;
  border: none;
  color: #94A3B8;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  border-radius: 6px;
  transition: color 0.2s ease;
}

.btn-toggle-eye:hover {
  color: #0F172A;
}

:global(.dark-theme) .btn-toggle-eye:hover {
  color: #FFFFFF;
}

/* Collapsible Advanced */
.advanced-section {
  border-top: 1px dashed #E2E8F0;
  padding-top: 8px;
}

:global(.dark-theme) .advanced-section {
  border-top-color: #1E3A47;
}

.btn-toggle-advanced {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  background: transparent;
  border: none;
  padding: 6px 2px;
  color: #64748B;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.2s ease;
}

.btn-toggle-advanced:hover {
  color: #1B5E20;
}

:global(.dark-theme) .btn-toggle-advanced:hover {
  color: #81C784;
}

.advanced-title {
  display: flex;
  align-items: center;
  gap: 6px;
}

.chevron-icon {
  transition: transform 0.2s ease;
}

.rotate-180 {
  transform: rotate(180deg);
}

.advanced-panel {
  padding: 10px 0 2px 0;
}

.advanced-grid {
  display: flex;
  gap: 10px;
}

.flex-2 { flex: 2; }
.flex-1 { flex: 1; }

.field-label-sm {
  font-size: 11px;
  font-weight: 600;
  color: #64748B;
}

.form-input-sm {
  height: 36px;
  padding: 0 10px;
  font-size: 12px;
}

.btn-preset-link {
  background: transparent;
  border: none;
  color: #1B5E20;
  font-size: 10.5px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
}

:global(.dark-theme) .btn-preset-link {
  color: #81C784;
}

.text-center {
  text-align: center;
}

/* Feedback Banner */
.feedback-banner {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  line-height: 1.4;
}

.banner-success {
  background: #ECFDF5;
  border: 1px solid #A7F3D0;
  color: #065F46;
}

:global(.dark-theme) .banner-success {
  background: rgba(16, 185, 129, 0.15);
  border-color: rgba(16, 185, 129, 0.3);
  color: #6EE7B7;
}

.banner-error {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  color: #991B1B;
}

:global(.dark-theme) .banner-error {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.3);
  color: #FCA5A5;
}

.feedback-icon {
  flex-shrink: 0;
  margin-top: 1px;
}

/* Modal Footer Actions */
.modal-footer-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 4px;
}

.btn-cancel {
  padding: 9px 16px;
  border-radius: 10px;
  border: 1px solid #CBD5E1;
  background: #FFFFFF;
  color: #475569;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.dark-theme .btn-cancel,
:global(.dark-theme) .btn-cancel {
  background: #09202A;
  border-color: #1E3A47;
  color: #CBD5E1;
}

.btn-cancel:hover {
  background: #F1F5F9;
  color: #0F172A;
}

.dark-theme .btn-cancel:hover,
:global(.dark-theme) .btn-cancel:hover {
  background: #133947;
  color: #FFFFFF;
}

.btn-submit {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 9px 18px;
  border-radius: 10px;
  border: none;
  background: #1B5E20;
  color: #FFFFFF;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(27, 94, 32, 0.25);
  transition: all 0.2s ease;
}

.btn-submit:hover:not(:disabled) {
  background: #2E7D32;
  box-shadow: 0 4px 10px rgba(27, 94, 32, 0.35);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  box-shadow: none;
}

.spinner-sm {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

.spinner-xs {
  width: 11px;
  height: 11px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
