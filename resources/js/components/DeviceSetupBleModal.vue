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
                <div class="header-title-row">
                  <h3 class="modal-title">Setup Wi-Fi & MQTT ESP32</h3>
                  <span class="tech-pill">BLE Provisioning</span>
                </div>
                <p class="modal-subtitle">Konfigurasi jaringan ESP32 langsung melalui Web Bluetooth tanpa kabel serial</p>
              </div>
            </div>
            <button type="button" class="btn-close-modal" @click="close" title="Tutup">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <div class="ble-modal-body">
            <!-- 1. Connection Status Card -->
            <div v-if="!isBleConnected" class="status-card card-disconnected">
              <div class="status-card-content">
                <div class="status-icon-wrap warning-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
                  </svg>
                </div>
                <div class="status-text-block">
                  <div class="status-title-row">
                    <h4 class="status-heading">ESP32 Belum Terhubung</h4>
                  </div>
                  <p class="status-desc">Hubungkan browser ke Bluetooth ESP32 untuk mulai mengirim pengaturan jaringan.</p>
                </div>
              </div>
              <button 
                type="button" 
                class="btn-action-connect"
                :disabled="isConnecting"
                @click="handleConnectBle"
              >
                <svg v-if="!isConnecting" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                  <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
                </svg>
                <span v-if="isConnecting" class="spinner-sm"></span>
                <span>{{ isConnecting ? 'Mencari ESP32...' : 'Sambungkan Bluetooth' }}</span>
              </button>
            </div>

            <div v-else class="status-card card-connected">
              <div class="status-card-content">
                <div class="status-icon-wrap success-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
                <div class="status-text-block">
                  <div class="status-title-row">
                    <h4 class="status-heading">Terhubung ke <strong>{{ currentDeviceId }}</strong></h4>
                    <span class="pulse-tag-emerald">GATT Active</span>
                  </div>
                  <p class="status-desc">Saluran Bluetooth BLE aktif. Anda dapat mengirim SSID & Password ke ESP32.</p>
                </div>
              </div>
              <button 
                type="button" 
                class="btn-action-disconnect"
                @click="handleDisconnectBle"
                title="Putus koneksi Bluetooth"
              >
                Putus
              </button>
            </div>

            <!-- 2. Form Fields -->
            <form @submit.prevent="submitConfig" class="ble-form-layout">
              <!-- Device ID & Preset -->
              <div class="form-group">
                <label class="field-label">
                  <span>DEVICE IDENTIFIER (DEVICE ID)</span>
                  <span class="label-optional">Wajib</span>
                </label>
                <div class="input-container">
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
                    class="custom-input" 
                    placeholder="Contoh: SRD-001"
                    required
                  />
                </div>
              </div>

              <!-- Wi-Fi SSID -->
              <div class="form-group">
                <label class="field-label">
                  <span>NAMA WI-FI (SSID 2.4 GHz)</span>
                  <span class="label-hint">Mendukung jaringan 2.4 GHz</span>
                </label>
                <div class="input-container">
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
                    class="custom-input" 
                    placeholder="Contoh: Greenhouse_Hanjeli_2.4G"
                    required
                  />
                </div>
              </div>

              <!-- Wi-Fi Password -->
              <div class="form-group">
                <label class="field-label">
                  <span>PASSWORD WI-FI</span>
                </label>
                <div class="input-container">
                  <div class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                  </div>
                  <input 
                    v-model="form.password" 
                    :type="showPassword ? 'text' : 'password'" 
                    class="custom-input with-toggle" 
                    placeholder="Masukkan kata sandi Wi-Fi"
                    required
                  />
                  <button 
                    type="button" 
                    class="btn-toggle-visibility"
                    @click="showPassword = !showPassword"
                    :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
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

              <!-- MQTT Broker Host & Port -->
              <div class="form-row-grid">
                <div class="form-group flex-2">
                  <div class="field-label-row">
                    <label class="field-label">MQTT BROKER HOST</label>
                    <button type="button" class="btn-preset-link" @click="useDefaultBroker">Gunakan HiveMQ</button>
                  </div>
                  <div class="input-container">
                    <div class="input-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                      </svg>
                    </div>
                    <input 
                      v-model="form.mqttBroker" 
                      type="text" 
                      class="custom-input" 
                      placeholder="broker.hivemq.com"
                      required
                    />
                  </div>
                </div>

                <div class="form-group flex-1">
                  <label class="field-label">PORT</label>
                  <div class="input-container">
                    <input 
                      v-model="form.mqttPort" 
                      type="number" 
                      class="custom-input no-left-icon text-center" 
                      placeholder="1883"
                      required
                    />
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

              <!-- Action Buttons -->
              <div class="modal-footer-actions">
                <button type="button" class="btn-cancel" @click="close">
                  Batal
                </button>
                <button 
                  type="submit" 
                  class="btn-submit"
                  :disabled="isSubmitting || isConnecting"
                  :title="!isBleConnected ? 'Klik untuk menghubungkan Bluetooth & mengirim konfigurasi' : 'Kirim konfigurasi ke ESP32'"
                >
                  <span v-if="isSubmitting || isConnecting" class="spinner-sm"></span>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                  <span>{{ isSubmitting ? 'Mengirim ke ESP32...' : (isConnecting ? 'Menghubungkan BLE...' : (!isBleConnected ? 'Hubungkan BLE & Kirim' : 'Kirim Konfigurasi via BLE')) }}</span>
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
const isSubmitting = ref(false);
const isConnecting = ref(false);
const feedbackMessage = ref('');
const feedbackSuccess = ref(false);

const isBleConnected = computed(() => bleService.isConnected);
const currentDeviceId = computed(() => connectionManager.state.deviceId || 'OPPO Reno');

const form = reactive({
  deviceId: connectionManager.state.deviceId || 'OPPO Reno',
  ssid: connectionManager.state.hotspotName || 'OPPO Reno',
  password: '',
  mqttBroker: 'broker.hivemq.com',
  mqttPort: 1883,
});

watch(() => props.isOpen, (open) => {
  if (open) {
    form.deviceId = connectionManager.state.deviceId || 'OPPO Reno';
    form.ssid = connectionManager.state.hotspotName || 'OPPO Reno';
  }
});

const feedbackClass = computed(() => {
  return feedbackSuccess.value ? 'banner-success' : 'banner-error';
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
      feedbackMessage.value = '✓ Berhasil terhubung ke Bluetooth ESP32!';
      return true;
    }
    return false;
  } catch (e) {
    if (e.message && !e.message.includes('dibatalkan')) {
      feedbackSuccess.value = false;
      feedbackMessage.value = `Gagal terhubung Bluetooth: ${e.message}`;
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

  // If not connected to BLE yet, prompt to connect
  if (!isBleConnected.value) {
    feedbackSuccess.value = false;
    feedbackMessage.value = 'Menghubungkan ke Bluetooth ESP32 terlebih dahulu...';
    const connected = await handleConnectBle();
    if (!connected) {
      feedbackMessage.value = 'ESP32 belum terhubung via Bluetooth. Silakan klik tombol "Sambungkan Bluetooth" di kotak atas lalu ulangi kirim konfigurasi.';
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
    feedbackMessage.value = '✓ Kredensial Wi-Fi & MQTT berhasil disimpan ke NVS ESP32! ESP32 akan segera terhubung ke Wi-Fi.';
    
    // Mask password after successful submission
    form.password = '';
  } catch (err) {
    feedbackSuccess.value = false;
    feedbackMessage.value = `Gagal mengirim konfigurasi: ${err.message}`;
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
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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
  background: rgba(7, 30, 39, 0.72);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
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
  border-radius: 20px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 25px 50px -12px rgba(7, 30, 39, 0.35), 0 0 0 1px rgba(191, 202, 186, 0.4);
  overflow: hidden;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
}

:global(.dark-theme) .ble-modal-container {
  background: #0B242F;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(30, 78, 97, 0.6);
}

/* Modal Header */
.ble-modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  padding: 20px 24px 16px 24px;
  border-bottom: 1px solid rgba(191, 202, 186, 0.35);
  background: linear-gradient(180deg, #F8FCFE 0%, #FFFFFF 100%);
}

:global(.dark-theme) .ble-modal-header {
  background: linear-gradient(180deg, #0E2C39 0%, #0B242F 100%);
  border-bottom-color: rgba(30, 78, 97, 0.5);
}

.header-main-info {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-icon-badge {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #005DB7 0%, #2563EB 100%);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 93, 183, 0.25);
}

.header-titles {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.header-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.modal-title {
  font-size: 15.5px;
  font-weight: 800;
  color: #071E27;
  margin: 0;
  letter-spacing: -0.01em;
}

:global(.dark-theme) .modal-title {
  color: #FFFFFF;
}

.tech-pill {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 20px;
  background: rgba(0, 93, 183, 0.12);
  color: #005DB7;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}

:global(.dark-theme) .tech-pill {
  background: rgba(59, 130, 246, 0.2);
  color: #93C5FD;
}

.modal-subtitle {
  font-size: 11.5px;
  color: rgba(64, 73, 61, 0.75);
  margin: 0;
  line-height: 1.4;
}

:global(.dark-theme) .modal-subtitle {
  color: #94A3B8;
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
  background: rgba(0, 0, 0, 0.05);
  color: #071E27;
}

:global(.dark-theme) .btn-close-modal:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

/* Modal Body */
.ble-modal-body {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Status Cards */
.status-card {
  border-radius: 14px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  transition: all 0.2s ease;
}

.status-card-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.status-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.card-disconnected {
  background: #FFFBEB;
  border: 1px solid #FDE68A;
}

:global(.dark-theme) .card-disconnected {
  background: rgba(245, 158, 11, 0.12);
  border-color: rgba(245, 158, 11, 0.3);
}

.warning-icon {
  background: #FEF3C7;
  color: #D97706;
}

:global(.dark-theme) .warning-icon {
  background: rgba(217, 119, 6, 0.2);
  color: #FBBF24;
}

.card-connected {
  background: #ECFDF5;
  border: 1px solid #A7F3D0;
}

:global(.dark-theme) .card-connected {
  background: rgba(16, 185, 129, 0.12);
  border-color: rgba(16, 185, 129, 0.3);
}

.success-icon {
  background: #D1FAE5;
  color: #059669;
}

:global(.dark-theme) .success-icon {
  background: rgba(5, 150, 105, 0.2);
  color: #34D399;
}

.status-text-block {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.status-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-heading {
  font-size: 12.5px;
  font-weight: 700;
  color: #071E27;
  margin: 0;
}

:global(.dark-theme) .status-heading {
  color: #F8FAFC;
}

.status-desc {
  font-size: 11px;
  color: #64748B;
  margin: 0;
  line-height: 1.35;
}

:global(.dark-theme) .status-desc {
  color: #94A3B8;
}

.pulse-tag-amber {
  font-size: 10px;
  font-weight: 700;
  color: #B45309;
  background: #FDE68A;
  padding: 1px 6px;
  border-radius: 6px;
}

.pulse-tag-emerald {
  font-size: 10px;
  font-weight: 700;
  color: #047857;
  background: #A7F3D0;
  padding: 1px 6px;
  border-radius: 6px;
}

/* Action Button on Status Card */
.btn-action-connect {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 11.5px;
  font-weight: 700;
  color: #FFFFFF;
  background: linear-gradient(135deg, #005DB7 0%, #1D4ED8 100%);
  border: none;
  cursor: pointer;
  box-shadow: 0 3px 8px rgba(0, 93, 183, 0.25);
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.btn-action-connect:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 5px 12px rgba(0, 93, 183, 0.35);
  filter: brightness(1.08);
}

.btn-action-connect:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-action-disconnect {
  padding: 5px 10px;
  border-radius: 7px;
  font-size: 11px;
  font-weight: 700;
  color: #DC2626;
  background: rgba(220, 38, 38, 0.1);
  border: 1px solid rgba(220, 38, 38, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-action-disconnect:hover {
  background: #DC2626;
  color: #FFFFFF;
}

/* Form Styles */
.ble-form-layout {
  display: flex;
  flex-direction: column;
  gap: 13px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.field-label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.05em;
  color: #40493D;
}

:global(.dark-theme) .field-label {
  color: #CBD5E1;
}

.label-hint {
  font-size: 10px;
  font-weight: 500;
  color: #64748B;
  text-transform: none;
  letter-spacing: normal;
}

.label-optional {
  font-size: 10px;
  font-weight: 600;
  color: #059669;
  text-transform: none;
}

.field-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.btn-preset-link {
  background: transparent;
  border: none;
  color: #005DB7;
  font-size: 10.5px;
  font-weight: 700;
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
}

:global(.dark-theme) .btn-preset-link {
  color: #60A5FA;
}

/* Input Containers */
.input-container {
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

.custom-input {
  width: 100%;
  padding: 10px 12px 10px 38px;
  border-radius: 10px;
  border: 1px solid #CBD5E1;
  background: #F8FAFC;
  font-size: 12.5px;
  font-weight: 500;
  color: #071E27;
  outline: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

:global(.dark-theme) .custom-input {
  background: #071E27;
  border-color: #1E4E61;
  color: #F8FAFC;
}

.custom-input:focus {
  border-color: #005DB7;
  background: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(0, 93, 183, 0.15);
}

:global(.dark-theme) .custom-input:focus {
  background: #0B242F;
  border-color: #38BDF8;
  box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
}

.custom-input.with-toggle {
  padding-right: 38px;
}

.custom-input.no-left-icon {
  padding-left: 12px;
}

.btn-toggle-visibility {
  position: absolute;
  right: 10px;
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

.btn-toggle-visibility:hover {
  color: #071E27;
}

:global(.dark-theme) .btn-toggle-visibility:hover {
  color: #FFFFFF;
}

.form-row-grid {
  display: flex;
  gap: 10px;
}

.flex-2 { flex: 2; }
.flex-1 { flex: 1; }

/* Feedback Banner */
.feedback-banner {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 11.5px;
  font-weight: 600;
  line-height: 1.4;
}

.banner-success {
  background: #ECFDF5;
  border: 1px solid #A7F3D0;
  color: #065F46;
}

:global(.dark-theme) .banner-success {
  background: rgba(16, 185, 129, 0.15);
  border-color: rgba(16, 185, 129, 0.35);
  color: #6EE7B7;
}

.banner-error {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  color: #991B1B;
}

:global(.dark-theme) .banner-error {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.35);
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
  padding-top: 6px;
}

.btn-cancel {
  padding: 9px 18px;
  border-radius: 10px;
  border: 1px solid #CBD5E1;
  background: #FFFFFF;
  color: #475569;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

:global(.dark-theme) .btn-cancel {
  background: #0E2C39;
  border-color: #1E4E61;
  color: #CBD5E1;
}

.btn-cancel:hover {
  background: #F1F5F9;
  color: #0F172A;
}

:global(.dark-theme) .btn-cancel:hover {
  background: #133947;
  color: #FFFFFF;
}

.btn-submit {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 20px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #0D631B 0%, #2E7D32 100%);
  color: #FFFFFF;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(13, 99, 27, 0.25);
  transition: all 0.2s ease;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 5px 14px rgba(13, 99, 27, 0.35);
  filter: brightness(1.08);
}

.btn-submit:disabled {
  opacity: 0.55;
  cursor: not-allowed;
  transform: none;
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

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
