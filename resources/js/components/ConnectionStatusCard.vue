<template>
  <div class="connection-status-card">
    <!-- 1. Top Section: Sumber Data Sensor IoT Switcher (Mode Alat Fisik Live vs Mode Simulasi IoT) -->
    <div class="source-mode-bar">
      <div class="source-mode-info">
        <div class="source-title-row">
          <span class="source-label">SUMBER DATA SENSOR IOT</span>
        </div>
        <p class="source-sub">Beralih antara Mode Alat Fisik ESP32 dan Mode Simulasi IoT</p>
      </div>

      <div class="source-control-container">
        <div class="source-segmented-control" :class="{ 'control-locked': !isAdmin }">
          <button 
            type="button" 
            class="source-segment-btn" 
            :class="{ active: sourceMode === 'hardware', 'btn-locked': !isAdmin }"
            :disabled="!isAdmin"
            :title="isAdmin ? 'Gunakan data live dari hardware fisik ESP32' : 'Hak akses terbatas (Khusus Administrator)'"
            @click="switchSourceMode('hardware')"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
              <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
              <rect x="9" y="9" width="6" height="6"></rect>
              <line x1="9" y1="1" x2="9" y2="4"></line>
              <line x1="15" y1="1" x2="15" y2="4"></line>
              <line x1="9" y1="20" x2="9" y2="23"></line>
              <line x1="15" y1="20" x2="15" y2="23"></line>
            </svg>
            <span>Mode Alat Fisik Live</span>
          </button>

          <button 
            type="button" 
            class="source-segment-btn" 
            :class="{ active: sourceMode === 'simulation', 'btn-locked': !isAdmin }"
            :disabled="!isAdmin"
            :title="isAdmin ? 'Gunakan simulasi data matematika' : 'Hak akses terbatas (Khusus Administrator)'"
            @click="switchSourceMode('simulation')"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
              <path d="M10 2v7.31"></path>
              <path d="M14 9.3V2"></path>
              <path d="M8.5 2h7"></path>
              <path d="M14 9.3a6.5 6.5 0 1 1-4 0"></path>
              <path d="M5.52 16h12.96"></path>
            </svg>
            <span>Mode Simulasi IoT</span>
          </button>
        </div>

        <transition name="fade">
          <div v-if="permissionWarning" class="permission-alert-tooltip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Akses Ditolak: Hanya Administrator yang dapat mengubah mode sumber data sensor.</span>
          </div>
        </transition>
      </div>
    </div>

    <!-- 2. Dual Connectivity Selector & Status (When in Hardware Live Mode) -->
    <div v-if="sourceMode === 'hardware'" class="hardware-connection-section">
      <div class="card-header-row">
        <div class="header-title-group">
          <div class="mode-icon-circle" :class="activeMode">
            <!-- Wi-Fi Icon -->
            <svg v-if="activeMode === 'wifi'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
              <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
              <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
              <line x1="12" y1="20" x2="12.01" y2="20"></line>
            </svg>
            <!-- Bluetooth Icon -->
            <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
            </svg>
          </div>
          <div class="title-texts">
            <h3 class="card-title">Koneksi Hardware ESP32</h3>
            <p class="card-subtitle">Pilih saluran transmisi data fisik</p>
          </div>
        </div>

        <!-- Segmented Mode Selector Control (Wi-Fi vs BLE) -->
        <div class="mode-segmented-control">
          <button 
            type="button" 
            class="mode-segment-btn" 
            :class="{ active: activeMode === 'wifi' }"
            @click="switchMode('wifi')"
          >
            <span class="dot-indicator wifi-dot"></span>
            <span>Wi-Fi + MQTT</span>
          </button>

          <button 
            type="button" 
            class="mode-segment-btn" 
            :class="{ active: activeMode === 'ble' }"
            @click="switchMode('ble')"
          >
            <span class="dot-indicator ble-dot"></span>
            <span>Bluetooth BLE</span>
          </button>
        </div>
      </div>

      <!-- Standby Warning Banner if Hardware is NOT Connected -->
      <div v-if="!isHardwareConnected" class="standby-notice-banner">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="standby-icon">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div class="standby-text">
          <strong>Perangkat ESP32 Belum Terhubung</strong>
          <span>Data sensor di dashboard berstatus siaga (abu-abu). Hubungkan ESP32 ke Hotspot / Wi-Fi atau sambungkan via Bluetooth di bawah.</span>
        </div>
      </div>

      <!-- 4 Info Metrics Grid -->
      <div class="connection-grid">
        <!-- 1. Status Box -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <span class="info-label">STATUS KONEKSI</span>
          <div class="status-pill-row">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" :class="isHardwareConnected ? 'text-green-icon' : (connectionState.isConnecting ? 'text-amber-icon' : 'text-muted-icon')">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            <span class="status-value-text" :class="statusTextClass">
              {{ formattedStatus }}
            </span>
          </div>
          <span class="info-meta-sub">
            {{ isHardwareConnected ? (activeMode === 'wifi' ? 'Wi-Fi MQTT Ingestion' : 'BLE GATT Direct') : 'Siaga / Menunggu Data' }}
          </span>
        </div>

        <!-- 2. Device ID Box with Edit Action -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <div class="info-header-row">
            <span class="info-label">PERANGKAT (DEVICE ID)</span>
            <button 
              type="button" 
              class="btn-edit-device" 
              @click="openDeviceEditModal" 
              title="Sesuaikan nama perangkat dan hotspot"
            >
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
              </svg>
              <span>Ubah</span>
            </button>
          </div>
          <div class="device-badge">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="isHardwareConnected ? 'text-green-icon' : 'text-muted-icon'">
              <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
              <rect x="9" y="9" width="6" height="6"></rect>
              <line x1="9" y1="1" x2="9" y2="4"></line>
              <line x1="15" y1="1" x2="15" y2="4"></line>
              <line x1="9" y1="20" x2="9" y2="23"></line>
              <line x1="15" y1="20" x2="15" y2="23"></line>
            </svg>
            <span class="device-name-text">
              {{ connectionState.deviceId || 'OPPO Reno' }}
            </span>
          </div>
          <span class="info-meta-sub">
            {{ isHardwareConnected ? 'Hardware Terdeteksi' : 'Target Perangkat' }}
          </span>
        </div>

        <!-- 3. Sinyal & Saluran Transmisi (Konek Lewat Apa) with Icons -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <span class="info-label">
            {{ activeMode === 'wifi' ? 'KONEK VIA (WI-FI & MQTT)' : 'KONEK VIA (BLUETOOTH BLE)' }}
          </span>
          <div class="transport-value">
            <div v-if="activeMode === 'wifi'" class="transport-badge">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :class="isHardwareConnected ? 'text-green-icon' : 'text-muted-icon'">
                <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
              </svg>
              <span>{{ isHardwareConnected ? 'Connected (Reverb/HiveMQ)' : 'Standby / Offline' }}</span>
            </div>
            <div v-else class="transport-badge">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :class="isHardwareConnected ? 'text-blue-icon' : 'text-muted-icon'">
                <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
              </svg>
              <span>{{ isHardwareConnected ? 'BLE Connected (Good)' : (isBleSupported ? 'BLE Siap Dihubungkan' : 'Browser Tidak Didukung') }}</span>
            </div>
          </div>
          <span class="info-meta-sub text-truncate">
            <template v-if="activeMode === 'wifi'">
              Hotspot: <strong>{{ connectionState.hotspotName || connectionState.deviceId || 'OPPO Reno' }}</strong>
            </template>
            <template v-else>
              Target: <strong>{{ connectionState.deviceId || 'ESP32 BLE' }}</strong>
            </template>
          </span>
        </div>

        <!-- 4. Data Terakhir & Status Sinkronisasi -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <span class="info-label">DATA TERAKHIR</span>
          <div class="last-data-val">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="isHardwareConnected ? 'text-green-icon' : 'text-muted-icon'">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>{{ isHardwareConnected ? lastDataText : 'Menunggu ESP32...' }}</span>
          </div>
          <span class="info-meta-sub">
            {{ isHardwareConnected ? 'Sinkron Real-Time Ingest' : 'Data Siaga' }}
          </span>
        </div>
      </div>

      <!-- Bluetooth Action Toolbar (Visible in BLE mode) -->
      <div v-if="activeMode === 'ble'" class="ble-action-toolbar">
        <div v-if="!isBleSupported" class="ble-unsupported-alert">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <span>Web Bluetooth tidak didukung pada browser ini. Silakan gunakan <strong>Google Chrome</strong> atau <strong>Microsoft Edge</strong>.</span>
        </div>

        <div v-else class="ble-toolbar-row">
          <div class="ble-helper-text">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span v-if="connectionState.isConnected">
              Terhubung langsung ke ESP32 (<strong>{{ connectionState.deviceId }}</strong>) melalui BLE GATT Server lokal.
            </span>
            <span v-else>
              Klik tombol di sebelah kanan untuk mencari dan menghubungkan ESP32 secara lokal.
            </span>
          </div>

          <div class="ble-btn-group">
            <!-- Connect / Disconnect Bluetooth -->
            <button 
              v-if="!connectionState.isConnected" 
              type="button" 
              class="btn-ble-action btn-ble-connect"
              :disabled="connectionState.isConnecting"
              @click="handleBleConnect"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
              </svg>
              <span>{{ connectionState.isConnecting ? 'Mencari ESP32...' : 'Sambungkan Bluetooth' }}</span>
            </button>

            <button 
              v-else 
              type="button" 
              class="btn-ble-action btn-ble-disconnect"
              @click="handleBleDisconnect"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
              <span>Putus Bluetooth</span>
            </button>

            <!-- Setup Wi-Fi via BLE Button -->
            <button 
              type="button" 
              class="btn-ble-action btn-ble-setup"
              @click="$emit('open-wifi-setup')"
              title="Konfigurasi Wi-Fi & MQTT ke ESP32 melalui Bluetooth"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
              <span>Setup Wi-Fi ESP32</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. Simulation Mode Active Banner (When in Simulation Mode) -->
    <div v-else class="simulation-active-banner">
      <div class="sim-banner-content">
        <div class="sim-icon-box">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M10 2v7.31"></path>
            <path d="M14 9.3V2"></path>
            <path d="M8.5 2h7"></path>
            <path d="M14 9.3a6.5 6.5 0 1 1-4 0"></path>
            <path d="M5.52 16h12.96"></path>
          </svg>
        </div>
        <div class="sim-text-group">
          <h4 class="sim-heading">Mode Simulasi IoT Aktif</h4>
          <p class="sim-caption">Data sensor dihasilkan oleh generator matematika ruang pengeringan untuk uji coba & demonstrasi sistem.</p>
        </div>
      </div>
      <div class="sim-badge-wrap">
        <span class="sim-live-badge">
          <span class="sim-dot-pulse"></span>
          <span>Generator Berjalan</span>
        </span>
      </div>
    </div>

    <!-- 4. Device Identifier & Connection Setup Quick Modal -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div v-if="isDeviceEditOpen" class="device-modal-overlay" @click.self="isDeviceEditOpen = false">
          <div class="device-modal-card" role="dialog" aria-modal="true">
            <div class="device-modal-header">
              <div class="header-icon-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                  <rect x="9" y="9" width="6" height="6"></rect>
                  <line x1="9" y1="1" x2="9" y2="4"></line>
                  <line x1="15" y1="1" x2="15" y2="4"></line>
                  <line x1="9" y1="20" x2="9" y2="23"></line>
                  <line x1="15" y1="20" x2="15" y2="23"></line>
                </svg>
              </div>
              <div class="header-text-group">
                <h3 class="device-modal-title">Sesuaikan Perangkat & Saluran</h3>
                <p class="device-modal-subtitle">Atur nama device dan koneksi yang digunakan sistem</p>
              </div>
              <button type="button" class="btn-close-modal" @click="isDeviceEditOpen = false">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <form @submit.prevent="saveDeviceSettings" class="device-modal-body">
              <!-- Field 1: Device Name / ID -->
              <div class="form-group">
                <label class="field-label">NAMA PERANGKAT / DEVICE ID</label>
                <div class="input-wrap">
                  <input 
                    v-model="editDeviceForm.deviceId" 
                    type="text" 
                    class="device-input" 
                    placeholder="Contoh: OPPO Reno atau SRD-001" 
                    required 
                  />
                </div>
                <!-- Quick Preset Chips -->
                <div class="preset-chips-row">
                  <span class="preset-label">Pilihan Cepat:</span>
                  <button 
                    v-for="preset in devicePresets" 
                    :key="preset" 
                    type="button" 
                    class="chip-btn" 
                    :class="{ active: editDeviceForm.deviceId === preset }"
                    @click="editDeviceForm.deviceId = preset"
                  >
                    {{ preset }}
                  </button>
                </div>
              </div>

              <!-- Field 2: Hotspot / Wi-Fi SSID Name -->
              <div class="form-group">
                <label class="field-label">NAMA HOTSPOT / WI-FI (SSID)</label>
                <div class="input-wrap">
                  <input 
                    v-model="editDeviceForm.hotspotName" 
                    type="text" 
                    class="device-input" 
                    placeholder="Contoh: OPPO Reno" 
                  />
                </div>
                <p class="field-helper">Nama jaringan Wi-Fi Hotspot HP / Router tempat ESP32 terhubung.</p>
              </div>

              <!-- Feedback / Info Banner -->
              <div class="info-helper-box">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Pengaturan ini akan langsung tersimpan di sistem dan disinkronkan ke seluruh dasbor IoT.</span>
              </div>

              <div class="modal-footer-row">
                <button type="button" class="btn-modal-cancel" @click="isDeviceEditOpen = false">
                  Batal
                </button>
                <button type="submit" class="btn-modal-save">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>Simpan Perubahan</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import connectionManager from '../services/connectionManager';
import bleService from '../services/ble/bleService';
import authService from '../services/authService';

const emit = defineEmits(['open-wifi-setup']);

const connectionState = connectionManager.state;
const sourceMode = computed(() => connectionState.sourceMode);
const activeMode = computed(() => connectionState.mode);
const isBleSupported = computed(() => bleService.isSupported());
const isHardwareConnected = computed(() => connectionState.isHardwareActive);

// Only ADMIN is authorized to toggle IoT Sensor Data Source
const isAdmin = computed(() => {
  return authService.isAdmin();
});

const permissionWarning = ref(false);
let permTimer = null;

const now = ref(Date.now());
let timer = null;

// Device Customization State
const isDeviceEditOpen = ref(false);
const devicePresets = ['OPPO Reno', 'ESP32-GreenHouse', 'SRD-001', 'Smart-Dryer-01'];
const editDeviceForm = reactive({
  deviceId: 'OPPO Reno',
  hotspotName: 'OPPO Reno',
});

const openDeviceEditModal = () => {
  editDeviceForm.deviceId = connectionState.deviceId || 'OPPO Reno';
  editDeviceForm.hotspotName = connectionState.hotspotName || connectionState.deviceId || 'OPPO Reno';
  isDeviceEditOpen.value = true;
};

const saveDeviceSettings = () => {
  if (editDeviceForm.deviceId) {
    connectionManager.setDeviceId(editDeviceForm.deviceId);
  }
  if (editDeviceForm.hotspotName) {
    connectionManager.setHotspotName(editDeviceForm.hotspotName);
  }
  isDeviceEditOpen.value = false;
};

onMounted(() => {
  timer = setInterval(() => {
    now.value = Date.now();
  }, 5000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
  if (permTimer) clearTimeout(permTimer);
});

const switchSourceMode = (mode) => {
  if (!isAdmin.value) {
    permissionWarning.value = true;
    if (permTimer) clearTimeout(permTimer);
    permTimer = setTimeout(() => {
      permissionWarning.value = false;
    }, 4000);
    return;
  }
  connectionManager.setSourceMode(mode);
};

const switchMode = (mode) => {
  connectionManager.setConnectionMode(mode);
};

const handleBleConnect = async () => {
  try {
    connectionManager.state.mode = 'ble';
    await connectionManager.connect();
  } catch (err) {
    console.warn('[BLE UI] Connection error/cancelled:', err);
  }
};

const handleBleDisconnect = async () => {
  await connectionManager.disconnect();
};

const formattedStatus = computed(() => {
  if (isHardwareConnected.value) return 'Connected';
  if (connectionState.isConnecting) return 'Connecting...';
  if (activeMode.value === 'ble' && isBleSupported.value) return 'Bluetooth Siap';
  return 'Belum Terhubung';
});

const statusClass = computed(() => {
  if (isHardwareConnected.value) return 'pulse-emerald';
  if (connectionState.isConnecting) return 'pulse-amber';
  return 'pulse-gray';
});

const statusTextClass = computed(() => {
  if (isHardwareConnected.value) return 'text-status-online';
  if (connectionState.isConnecting) return 'text-status-connecting';
  return 'text-status-offline';
});

const lastDataText = computed(() => {
  if (!connectionState.lastDataTimestamp) return 'Menunggu data...';
  const diffSec = Math.floor((now.value - new Date(connectionState.lastDataTimestamp).getTime()) / 1000);
  if (diffSec < 5) return 'Baru saja';
  if (diffSec < 60) return `${diffSec} dtk lalu`;
  return `${Math.floor(diffSec / 60)} mnt lalu`;
});
</script>

<style scoped>
.connection-status-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 22px;
  box-sizing: border-box;
  width: 100%;
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
  gap: 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .connection-status-card {
  background: var(--color-white, #13271C);
  border-color: rgba(30, 58, 43, 0.6);
}

/* 1. Source Mode Switcher Bar */
.source-mode-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .source-mode-bar {
  border-bottom-color: rgba(30, 58, 43, 0.5);
}

.source-mode-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.source-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.source-label {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.05em;
  color: var(--color-text-title, #071E27);
}

:global(.dark-theme) .source-label {
  color: #FFFFFF;
}

.source-sub {
  font-size: 11.5px;
  color: var(--color-text-muted, rgba(64, 73, 61, 0.7));
  margin: 0;
}

:global(.dark-theme) .source-sub {
  color: #94A3B8;
}

.source-control-container {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
  position: relative;
}

.permission-alert-tooltip {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 6px;
  padding: 4px 8px;
  font-size: 11px;
  font-weight: 600;
  color: #DC2626;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

:global(.dark-theme) .permission-alert-tooltip {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.35);
  color: #FCA5A5;
}

.source-segmented-control.control-locked {
  opacity: 0.85;
}

.source-segment-btn.btn-locked:not(.active) {
  cursor: not-allowed;
  opacity: 0.6;
}

.source-segmented-control {
  display: inline-flex;
  background: var(--color-bg-light, #F3FAFF);
  border: 1px solid var(--color-border-subtle, rgba(191, 202, 186, 0.4));
  padding: 3px;
  border-radius: 9px;
  gap: 3px;
}

:global(.dark-theme) .source-segmented-control {
  background: #071E27;
  border-color: var(--color-border, #1E4E61);
}

.source-segment-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 700;
  color: var(--color-text-body, #40493D);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

:global(.dark-theme) .source-segment-btn {
  color: #CBD5E1;
}

.source-segment-btn.active {
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
}

:global(.dark-theme) .source-segment-btn.active {
  background: var(--color-primary, #2E7D32);
  color: #FFFFFF;
}

/* 2. Hardware Connection Section */
.hardware-connection-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.card-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.header-title-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mode-icon-circle {
  width: 36px;
  height: 36px;
  min-width: 36px;
  min-height: 36px;
  max-width: 36px;
  max-height: 36px;
  aspect-ratio: 1 / 1;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.mode-icon-circle.wifi {
  background: var(--color-primary-bg, rgba(46, 125, 50, 0.1));
  color: var(--color-primary-dark, #0D631B);
}

:global(.dark-theme) .mode-icon-circle.wifi {
  background: rgba(46, 125, 50, 0.25);
  color: #6EE7B7;
}

.mode-icon-circle.ble {
  background: var(--color-blue-bg, rgba(0, 93, 183, 0.1));
  color: var(--color-blue, #005DB7);
}

:global(.dark-theme) .mode-icon-circle.ble {
  background: rgba(0, 93, 183, 0.25);
  color: #93C5FD;
}

.title-texts {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.card-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  margin: 0;
}

:global(.dark-theme) .card-title {
  color: #FFFFFF;
}

.card-subtitle {
  font-size: 11.5px;
  color: var(--color-text-muted, rgba(64, 73, 61, 0.7));
  margin: 0;
}

:global(.dark-theme) .card-subtitle {
  color: #94A3B8;
}

/* Standby Warning Banner */
.standby-notice-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #FEF3C7;
  border: 1px solid #FDE68A;
  border-radius: 9px;
  padding: 10px 14px;
  color: #92400E;
}

:global(.dark-theme) .standby-notice-banner {
  background: rgba(245, 158, 11, 0.15);
  border-color: rgba(245, 158, 11, 0.3);
  color: #FDE68A;
}

.standby-icon {
  flex-shrink: 0;
  color: #D97706;
}

.standby-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 12px;
  line-height: 1.35;
}

.standby-text strong {
  font-size: 12.5px;
  font-weight: 700;
}

/* Segmented Control (Wi-Fi vs BLE) */
.mode-segmented-control {
  display: inline-flex;
  background: var(--color-bg-light, #F3FAFF);
  border: 1px solid var(--color-border-subtle, rgba(191, 202, 186, 0.4));
  padding: 3px;
  border-radius: 9px;
  gap: 3px;
}

:global(.dark-theme) .mode-segmented-control {
  background: #071E27;
  border-color: var(--color-border, #1E4E61);
}

.mode-segment-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-body, #40493D);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

:global(.dark-theme) .mode-segment-btn {
  color: #CBD5E1;
}

.mode-segment-btn.active {
  background: var(--color-white, #FFFFFF);
  color: var(--color-text-title, #071E27);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

:global(.dark-theme) .mode-segment-btn.active {
  background: #133947;
  color: #FFFFFF;
}

.dot-indicator {
  width: 7px;
  height: 7px;
  border-radius: 50%;
}

.wifi-dot { background: #10B981; }
.ble-dot { background: #3B82F6; }

/* 4 Info Boxes Grid */
.connection-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
  gap: 10px;
}

.info-box {
  background: var(--color-bg-light, #F8FAFC);
  border: 1px solid rgba(203, 213, 225, 0.4);
  padding: 10px 14px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  transition: all 0.2s ease;
}

.info-box.box-standby {
  background: rgba(148, 163, 184, 0.06);
  border-color: rgba(148, 163, 184, 0.2);
}

:global(.dark-theme) .info-box {
  background: #0B1911;
  border-color: rgba(30, 58, 43, 0.5);
}

:global(.dark-theme) .info-box.box-standby {
  background: rgba(11, 25, 17, 0.4);
  border-color: rgba(30, 58, 43, 0.4);
}

.info-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.info-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: var(--color-text-muted, rgba(64, 73, 61, 0.7));
  text-transform: uppercase;
}

:global(.dark-theme) .info-label {
  color: #94A3B8;
}

.btn-edit-device {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: transparent;
  border: none;
  color: var(--color-primary-dark, #0D631B);
  font-size: 10px;
  font-weight: 700;
  cursor: pointer;
  padding: 1px 4px;
  border-radius: 4px;
  transition: all 0.15s ease;
}

.btn-edit-device:hover {
  background: rgba(13, 99, 27, 0.1);
}

:global(.dark-theme) .btn-edit-device {
  color: #6EE7B7;
}

:global(.dark-theme) .btn-edit-device:hover {
  background: rgba(110, 231, 183, 0.15);
}

.status-pill-row {
  display: flex;
  align-items: center;
  gap: 6px;
}

.pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}

.pulse-mini-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.pulse-emerald { background: #10B981; }
.pulse-amber { background: #F59E0B; }
.pulse-gray { background: #94A3B8; }

.status-value-text {
  font-size: 13px;
  font-weight: 700;
}

.text-status-online { color: var(--color-primary-dark, #0D631B); }
:global(.dark-theme) .text-status-online { color: #6EE7B7; }

.text-status-connecting { color: #B45000; }
:global(.dark-theme) .text-status-connecting { color: #FBBF24; }

.text-status-offline { color: #94A3B8; }

.device-badge {
  display: flex;
  align-items: center;
  gap: 6px;
}

.text-green-icon {
  color: #0D631B;
  flex-shrink: 0;
}

:global(.dark-theme) .text-green-icon {
  color: #4ADE80;
}

.text-blue-icon {
  color: #005DB7;
  flex-shrink: 0;
}

:global(.dark-theme) .text-blue-icon {
  color: #60A5FA;
}

.text-amber-icon {
  color: #D97706;
  flex-shrink: 0;
}

:global(.dark-theme) .text-amber-icon {
  color: #FBBF24;
}

.text-muted-icon {
  color: #94A3B8;
  flex-shrink: 0;
}

:global(.dark-theme) .text-muted-icon {
  color: #64748B;
}

.device-name-text {
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

:global(.dark-theme) .device-name-text { color: #FFFFFF; }

.transport-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-body, #40493D);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

:global(.dark-theme) .transport-badge { color: #CBD5E1; }

.bg-green-dot { background: #10B981; }
.bg-amber-dot { background: #F59E0B; }
.bg-blue-dot { background: #3B82F6; }
.bg-gray-dot { background: #94A3B8; }

.last-data-val {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-body, #40493D);
}

:global(.dark-theme) .last-data-val { color: #CBD5E1; }

.info-meta-sub {
  font-size: 10.5px;
  color: var(--color-text-muted, rgba(64, 73, 61, 0.65));
}

:global(.dark-theme) .info-meta-sub {
  color: #64748B;
}

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* BLE Action Toolbar */
.ble-action-toolbar {
  padding-top: 10px;
  border-top: 1px dashed var(--color-border-subtle, rgba(191, 202, 186, 0.4));
}

:global(.dark-theme) .ble-action-toolbar {
  border-top-color: var(--color-border-subtle, rgba(30, 78, 97, 0.5));
}

.ble-toolbar-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
}

.ble-helper-text {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  color: var(--color-text-muted, rgba(64, 73, 61, 0.7));
}

:global(.dark-theme) .ble-helper-text { color: #94A3B8; }

.ble-btn-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-ble-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-ble-connect {
  background: var(--color-blue, #005DB7);
  color: #FFFFFF;
}

.btn-ble-connect:hover:not(:disabled) {
  background: #004b94;
}

.btn-ble-connect:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.btn-ble-disconnect {
  background: rgba(186, 26, 26, 0.1);
  border: 1px solid rgba(186, 26, 26, 0.25);
  color: var(--color-red, #BA1A1A);
}

.btn-ble-disconnect:hover {
  background: rgba(186, 26, 26, 0.18);
}

.btn-ble-setup {
  background: var(--color-primary-bg, rgba(46, 125, 50, 0.1));
  border: 1px solid var(--color-primary, #2E7D32);
  color: var(--color-primary-dark, #0D631B);
}

.btn-ble-setup:hover {
  background: var(--color-primary-badge, rgba(46, 125, 50, 0.2));
}

/* Simulation Mode Active Banner */
.simulation-active-banner {
  background: rgba(13, 99, 27, 0.05);
  border: 1px solid rgba(13, 99, 27, 0.2);
  border-radius: 10px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

:global(.dark-theme) .simulation-active-banner {
  background: rgba(46, 125, 50, 0.12);
  border-color: rgba(46, 125, 50, 0.3);
}

.sim-banner-content {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  min-width: 260px;
}

.sim-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(13, 99, 27, 0.12);
  color: var(--color-primary-dark, #0D631B);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .sim-icon-box {
  background: rgba(46, 125, 50, 0.25);
  color: #6EE7B7;
}

.sim-text-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.sim-heading {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-primary-dark, #0D631B);
  margin: 0;
}

:global(.dark-theme) .sim-heading {
  color: #6EE7B7;
}

.sim-caption {
  font-size: 12.5px;
  color: var(--color-text-body, #40493D);
  margin: 0;
  line-height: 1.4;
}

:global(.dark-theme) .sim-caption {
  color: #94A3B8;
}

.sim-badge-wrap {
  display: flex;
  align-items: center;
}

.sim-live-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 700;
  background: rgba(13, 99, 27, 0.12);
  color: var(--color-primary-dark, #0D631B);
  border: 1px solid rgba(13, 99, 27, 0.25);
}

:global(.dark-theme) .sim-live-badge {
  background: rgba(46, 125, 50, 0.25);
  color: #6EE7B7;
  border-color: rgba(46, 125, 50, 0.4);
}

.sim-dot-pulse {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #0D631B;
  animation: pulse-dot 1.8s infinite;
}

:global(.dark-theme) .sim-dot-pulse {
  background: #10B981;
}

/* Device Modal Overlay & Container */
.device-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(7, 30, 39, 0.72);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  padding: 16px;
}

.device-modal-card {
  background: #FFFFFF;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 16px;
  width: 100%;
  max-width: 460px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

:global(.dark-theme) .device-modal-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
}

.device-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 18px 20px 14px 20px;
  border-bottom: 1px solid rgba(203, 213, 225, 0.4);
  background: #F8FAFC;
}

:global(.dark-theme) .device-modal-header {
  background: #0E2C39;
  border-bottom-color: rgba(30, 78, 97, 0.5);
}

.header-icon-badge {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: rgba(13, 99, 27, 0.12);
  color: var(--color-primary-dark, #0D631B);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .header-icon-badge {
  background: rgba(46, 125, 50, 0.25);
  color: #6EE7B7;
}

.header-text-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.device-modal-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #071E27;
  margin: 0;
}

:global(.dark-theme) .device-modal-title {
  color: #FFFFFF;
}

.device-modal-subtitle {
  font-size: 11.5px;
  color: #64748B;
  margin: 0;
}

:global(.dark-theme) .device-modal-subtitle {
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
}

.btn-close-modal:hover {
  background: rgba(0, 0, 0, 0.06);
  color: #071E27;
}

:global(.dark-theme) .btn-close-modal:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

.device-modal-body {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.04em;
  color: #40493D;
}

:global(.dark-theme) .field-label {
  color: #CBD5E1;
}

.input-wrap {
  width: 100%;
}

.device-input {
  width: 100%;
  padding: 9px 12px;
  border-radius: 9px;
  border: 1px solid #CBD5E1;
  background: #F8FAFC;
  font-size: 13px;
  font-weight: 600;
  color: #071E27;
  box-sizing: border-box;
  outline: none;
  transition: all 0.2s ease;
}

:global(.dark-theme) .device-input {
  background: #071E27;
  border-color: #1E4E61;
  color: #F8FAFC;
}

.device-input:focus {
  border-color: #0D631B;
  box-shadow: 0 0 0 3px rgba(13, 99, 27, 0.15);
}

:global(.dark-theme) .device-input:focus {
  border-color: #2E7D32;
  box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.3);
}

.preset-chips-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  margin-top: 4px;
}

.preset-label {
  font-size: 10.5px;
  color: #64748B;
  font-weight: 600;
}

.chip-btn {
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  background: #F1F5F9;
  border: 1px solid #CBD5E1;
  color: #334155;
  cursor: pointer;
  transition: all 0.15s ease;
}

.chip-btn:hover {
  background: #E2E8F0;
  color: #0F172A;
}

.chip-btn.active {
  background: rgba(13, 99, 27, 0.12);
  border-color: #0D631B;
  color: #0D631B;
  font-weight: 700;
}

:global(.dark-theme) .chip-btn {
  background: #0E2C39;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .chip-btn.active {
  background: rgba(46, 125, 50, 0.25);
  border-color: #6EE7B7;
  color: #6EE7B7;
}

.field-helper {
  font-size: 11px;
  color: #64748B;
  margin: 0;
}

:global(.dark-theme) .field-helper {
  color: #94A3B8;
}

.info-helper-box {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: #EFF6FF;
  border: 1px solid #BFDBFE;
  border-radius: 8px;
  font-size: 11px;
  color: #1E40AF;
  line-height: 1.35;
}

:global(.dark-theme) .info-helper-box {
  background: rgba(30, 64, 175, 0.15);
  border-color: rgba(59, 130, 246, 0.3);
  color: #93C5FD;
}

.info-helper-box svg {
  flex-shrink: 0;
}

.modal-footer-row {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  padding-top: 6px;
}

.btn-modal-cancel {
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  background: transparent;
  border: 1px solid #CBD5E1;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-modal-cancel:hover {
  background: #F1F5F9;
}

:global(.dark-theme) .btn-modal-cancel {
  border-color: #1E4E61;
  color: #CBD5E1;
}

.btn-modal-save {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-modal-save:hover {
  background: #094713;
}

/* Mobile Responsive */
@media (max-width: 640px) {
  .source-mode-bar {
    flex-direction: column;
    align-items: flex-start;
  }
  .source-segmented-control {
    width: 100%;
  }
  .source-segment-btn {
    flex: 1;
    justify-content: center;
  }
  .card-header-row {
    flex-direction: column;
    align-items: flex-start;
  }
  .mode-segmented-control {
    width: 100%;
  }
  .mode-segment-btn {
    flex: 1;
    justify-content: center;
  }
  .ble-toolbar-row {
    flex-direction: column;
    align-items: flex-start;
  }
  .ble-btn-group {
    width: 100%;
  }
  .btn-ble-action {
    flex: 1;
    justify-content: center;
  }
}
</style>
