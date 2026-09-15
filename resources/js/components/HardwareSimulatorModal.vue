<template>
  <Transition name="modal-fade">
    <div v-if="isOpen" class="simulator-modal-backdrop" @click.self="$emit('close')">
      <div class="simulator-modal-card">
        <!-- Modal Header -->
        <div class="simulator-modal-header">
          <div class="header-left">
            <div class="icon-sim-badge">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
              </svg>
            </div>
            <div>
              <div class="title-with-badge">
                <h2 class="modal-title">Simulasi Kompleks Hardware IoT (ESP32)</h2>
                <span class="engine-badge">Physics & Thermodynamic Engine</span>
              </div>
              <p class="modal-subtitle">Simulasi dinamika termal greenhouse, evaporasi gabah, regulasi aktuator, dan transmisi paket ESP32.</p>
            </div>
          </div>
          <button class="close-btn" @click="$emit('close')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Master Switch & Hardware Node Info Bar -->
        <div class="master-switch-bar" :class="{ active: simulatorService.isRunning.value }">
          <div class="node-info">
            <div class="status-indicator-dot" :class="{ 'pulse-green': simulatorService.isRunning.value }"></div>
            <div>
              <div class="node-title">
                <span>Node Perangkat: <strong>ESP32-GH-HANJELI-01</strong></span>
                <span class="status-chip" :class="simulatorService.isRunning.value ? 'chip-active' : 'chip-standby'">
                  {{ simulatorService.isRunning.value ? 'STREAMING AKTIF (REALTIME)' : 'SIAGA (STANDBY)' }}
                </span>
              </div>
              <div class="node-meta">
                <span>Terkirim: <strong>{{ simulatorService.packetsSent.value }} paket</strong></span>
                <span v-if="simulatorService.lastSentAt.value"> • Terakhir: <strong>{{ simulatorService.lastSentAt.value }}</strong></span>
                <span> • Interval: <strong>2 detik</strong></span>
              </div>
            </div>
          </div>

          <!-- Actions Group: Speed & Toggle -->
          <div class="master-actions">
            <!-- Speed Multiplier -->
            <div class="speed-selector">
              <span class="speed-label">Kecepatan:</span>
              <div class="speed-buttons">
                <button 
                  v-for="s in [1, 2, 5, 10, 30]" 
                  :key="s"
                  class="speed-btn"
                  :class="{ active: simulatorService.speedMultiplier.value === s }"
                  @click="simulatorService.setSpeed(s)"
                >
                  {{ s }}x
                </button>
              </div>
            </div>

            <!-- Big Toggle Action -->
            <button 
              class="btn-toggle-sim"
              :class="simulatorService.isRunning.value ? 'btn-stop' : 'btn-start'"
              @click="handleToggle"
            >
              <svg v-if="!simulatorService.isRunning.value" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
              </svg>
              <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <rect x="6" y="4" width="4" height="16"></rect>
                <rect x="14" y="4" width="4" height="16"></rect>
              </svg>
              <span>{{ simulatorService.isRunning.value ? 'Hentikan Simulasi' : 'Jalankan Simulasi Realtime' }}</span>
            </button>
          </div>
        </div>

        <!-- Database Batch Session & Historical Recording Bar -->
        <div class="db-session-bar">
          <div class="db-session-info">
            <div class="db-icon-box" :class="{ 'active-db': simulatorService.activeDbBatch.value }">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
              </svg>
            </div>
            <div>
              <div class="db-title-row">
                <span class="db-title">Perekaman Database (MySQL)</span>
                <span v-if="simulatorService.activeDbBatch.value" class="db-batch-badge-active">
                  <span class="status-indicator-dot pulse-green mini-dot"></span>
                  <span>BATCH AKTIF: {{ simulatorService.activeDbBatch.value.batchCode }}</span>
                </span>
                <span v-else class="db-batch-badge-idle">
                  <span class="status-indicator-dot mini-dot"></span>
                  <span>STANDBY (Data Disimpan ke Database)</span>
                </span>
              </div>
              <p class="db-subtitle">
                {{ simulatorService.activeDbBatch.value 
                  ? `Sesi ${simulatorService.activeDbBatch.value.cropVariety || 'Hanjeli Ketan'} sedang aktif merekam setiap log sensor ke MySQL.` 
                  : 'Mulai batch baru atau buat sampel riwayat untuk melihat grafik & riwayat lengkap di menu Riwayat.' }}
              </p>
            </div>
          </div>

          <div class="db-actions-row">
            <!-- Start New Batch Button -->
            <button 
              v-if="!simulatorService.activeDbBatch.value"
              class="btn-db-action btn-db-start" 
              :disabled="isSubmitting"
              @click="handleStartDbBatch"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <polygon points="10 8 16 12 10 16 10 8"></polygon>
              </svg>
              <span>Mulai Sesi Batch Baru</span>
            </button>

            <!-- Complete Batch Button -->
            <button 
              v-else 
              class="btn-db-action btn-db-complete" 
              :disabled="isSubmitting"
              @click="handleCompleteDbBatch"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Selesaikan & Simpan ke Riwayat</span>
            </button>

            <!-- Generate Historical Batch Button -->
            <button 
              class="btn-db-action btn-db-seed" 
              :disabled="isSubmitting"
              @click="handleGenerateHistorySample"
              title="Membuat sesi pengeringan lengkap dengan 36 log data sensor ke MySQL"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
              </svg>
              <span>Generate Riwayat Sample (36 Data)</span>
            </button>
          </div>
        </div>

        <!-- Toast Feedback -->
        <Transition name="toast-slide">
          <div v-if="showModalToast" class="modal-toast-alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>{{ modalToastMessage }}</span>
          </div>
        </Transition>

        <!-- Preset Scenario Selector -->
        <div class="scenario-section">
          <div class="section-header-row">
            <h3 class="section-title">Pilih Skenario Dinamika Lingkungan</h3>
            <span class="section-hint">Profil lingkungan otomatis berubah dinamis setiap detik</span>
          </div>

          <div class="scenarios-grid">
            <div 
              v-for="(sc, key) in SCENARIOS" 
              :key="key"
              class="scenario-card"
              :class="{ selected: simulatorService.scenario.value === key }"
              @click="selectScenario(key)"
            >
              <div class="sc-header">
                <div class="sc-icon-wrap" :style="{ color: sc.color }">
                  <!-- Sun icon for optimal -->
                  <svg v-if="sc.icon === 'sun'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2m-7.07-15.07 1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2m-15.07 7.07 1.41-1.41m11.32-11.32 1.41-1.41"/>
                  </svg>
                  <!-- Flame icon for high_heat -->
                  <svg v-else-if="sc.icon === 'flame'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
                  </svg>
                  <!-- Cloud rain icon for rainy -->
                  <svg v-else-if="sc.icon === 'cloud-rain'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                    <path d="M16 14v6m-4-4v6m-4-2v6"></path>
                  </svg>
                  <!-- Target icon for target_reached -->
                  <svg v-else-if="sc.icon === 'target'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <circle cx="12" cy="12" r="6"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                  </svg>
                  <!-- Sliders icon for custom -->
                  <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                  </svg>
                </div>
                <span v-if="simulatorService.scenario.value === key" class="sc-active-badge">Aktif</span>
              </div>
              <div class="sc-name">{{ sc.name }}</div>
              <p class="sc-desc">{{ sc.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Live Sensor Sliders (Tuning) & Live Telemetry Monitors -->
        <div class="sliders-section">
          <div class="section-header-row">
            <h3 class="section-title">Live Parameter Sensor & Aktuator (Real-Time Physics)</h3>
            <button class="btn-pulse-single" @click="sendSinglePulse">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
              <span>Kirim 1 Paket Data Sekarang</span>
            </button>
          </div>

          <div class="sliders-grid">
            <!-- 1. Suhu Internal -->
            <div class="slider-item">
              <div class="slider-label-row">
                <span class="label-name">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2.2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                  <span>Suhu Ruangan (Internal)</span>
                </span>
                <span class="label-val text-red">{{ simulatorService.currentValues.tempInternal.toFixed(1) }} °C</span>
              </div>
              <input 
                type="range" 
                min="20" 
                max="65" 
                step="0.5" 
                v-model.number="simulatorService.currentValues.tempInternal"
                @input="onSliderChange"
                class="sim-range range-red"
              />
              <div class="range-scale"><span>20°C (Dingin)</span><span>45°C (Optimal)</span><span>65°C (Ekstrem)</span></div>
            </div>

            <!-- 2. Suhu Eksternal -->
            <div class="slider-item">
              <div class="slider-label-row">
                <span class="label-name">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                  <span>Suhu Luar (Eksternal)</span>
                </span>
                <span class="label-val">{{ simulatorService.currentValues.tempExternal.toFixed(1) }} °C</span>
              </div>
              <input 
                type="range" 
                min="18" 
                max="42" 
                step="0.5" 
                v-model.number="simulatorService.currentValues.tempExternal"
                @input="onSliderChange"
                class="sim-range range-orange"
              />
              <div class="range-scale"><span>18°C</span><span>30°C</span><span>42°C</span></div>
            </div>

            <!-- 3. Kelembapan Internal -->
            <div class="slider-item">
              <div class="slider-label-row">
                <span class="label-name">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                  </svg>
                  <span>Kelembapan Ruang (RH)</span>
                </span>
                <span class="label-val text-blue">{{ simulatorService.currentValues.humidityInternal.toFixed(0) }} %</span>
              </div>
              <input 
                type="range" 
                min="10" 
                max="95" 
                step="1" 
                v-model.number="simulatorService.currentValues.humidityInternal"
                @input="onSliderChange"
                class="sim-range range-blue"
              />
              <div class="range-scale"><span>10% (Kering)</span><span>50% (Ideal)</span><span>95% (Lembap)</span></div>
            </div>

            <!-- 4. Radiasi Surya -->
            <div class="slider-item">
              <div class="slider-label-row">
                <span class="label-name">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2m-7.07-15.07 1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2m-15.07 7.07 1.41-1.41m11.32-11.32 1.41-1.41"/>
                  </svg>
                  <span>Radiasi Surya (Pyranometer)</span>
                </span>
                <span class="label-val text-amber">{{ simulatorService.currentValues.solarRadiation.toFixed(0) }} W/m²</span>
              </div>
              <input 
                type="range" 
                min="0" 
                max="1100" 
                step="20" 
                v-model.number="simulatorService.currentValues.solarRadiation"
                @input="onSliderChange"
                class="sim-range range-amber"
              />
              <div class="range-scale"><span>0 (Malam/Hujan)</span><span>750 (Cerah)</span><span>1100 (Terik)</span></div>
            </div>

            <!-- 5. Kadar Air Gabah -->
            <div class="slider-item">
              <div class="slider-label-row">
                <span class="label-name">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                    <path d="M12 2v20M17 5H7M19 12H5M17 19H7"/>
                  </svg>
                  <span>Kadar Air Gabah (Moisture)</span>
                </span>
                <span class="label-val text-green">{{ simulatorService.currentValues.grainMoisture.toFixed(1) }} %</span>
              </div>
              <input 
                type="range" 
                min="9" 
                max="30" 
                step="0.1" 
                v-model.number="simulatorService.currentValues.grainMoisture"
                @input="onSliderChange"
                class="sim-range range-green"
              />
              <div class="range-scale"><span>9% (Kering)</span><span>12% (Target Selesai)</span><span>30% (Basah)</span></div>
            </div>

            <!-- 6. Berat Gabah -->
            <div class="slider-item">
              <div class="slider-label-row">
                <span class="label-name">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 3"></path>
                  </svg>
                  <span>Timbangan Berat (Loadcell)</span>
                </span>
                <span class="label-val">{{ simulatorService.currentValues.weightCurrentKg.toFixed(1) }} kg</span>
              </div>
              <input 
                type="range" 
                min="50" 
                max="250" 
                step="0.5" 
                v-model.number="simulatorService.currentValues.weightCurrentKg"
                @input="onSliderChange"
                class="sim-range"
              />
              <div class="range-scale"><span>50 kg</span><span>130 kg</span><span>250 kg</span></div>
            </div>
          </div>
        </div>

        <!-- Closed-Loop Actuator Feedback Live Indicator -->
        <div class="actuator-feedback-bar">
          <div class="af-item">
            <span class="af-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2a4 4 0 0 1 4 4c0 3-4 6-4 6s-4-3-4-6a4 4 0 0 1 4-4z"></path>
              </svg>
              <span>Exhaust Fan:</span>
            </span>
            <span class="af-val" :class="simulatorService.simulatedActuators.exhaustFanStatus ? 'text-green' : 'text-muted'">
              {{ simulatorService.simulatedActuators.exhaustFanStatus ? `ON (${simulatorService.simulatedActuators.exhaustFanSpeed}%)` : 'OFF' }}
            </span>
          </div>
          <div class="af-item">
            <span class="af-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                <path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"></path>
                <path d="M9.6 4.6A2 2 0 1 1 11 8H2"></path>
                <path d="M12.6 19.4A2 2 0 1 0 14 16H2"></path>
              </svg>
              <span>Blower Fan:</span>
            </span>
            <span class="af-val" :class="simulatorService.simulatedActuators.blowerFanStatus ? 'text-green' : 'text-muted'">
              {{ simulatorService.simulatedActuators.blowerFanStatus ? `ON (${simulatorService.simulatedActuators.blowerFanSpeed}%)` : 'OFF' }}
            </span>
          </div>
          <div class="af-item">
            <span class="af-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
              </svg>
              <span>Pemanas Tambahan:</span>
            </span>
            <span class="af-val" :class="simulatorService.simulatedActuators.auxHeaterStatus ? 'text-red' : 'text-muted'">
              {{ simulatorService.simulatedActuators.auxHeaterStatus ? `AKTIF (${simulatorService.simulatedActuators.auxHeaterLevel}%)` : 'STANDBY' }}
            </span>
          </div>
          <div class="af-item">
            <span class="af-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-2px; margin-right:4px;">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              </svg>
              <span>Ventilasi Atap:</span>
            </span>
            <span class="af-val text-green">TERBUKA</span>
          </div>
        </div>

        <!-- Real-Time UART Terminal Logs -->
        <div class="terminal-section">
          <div class="terminal-header">
            <div class="terminal-title-group">
              <span class="terminal-dot red"></span>
              <span class="terminal-dot yellow"></span>
              <span class="terminal-dot green"></span>
              <span class="terminal-title">ESP32 UART Telemetry Ingest Stream (Live Packets)</span>
            </div>
            <span class="terminal-counter">{{ simulatorService.terminalLogs.value.length }} baris log</span>
          </div>
          <div class="terminal-body">
            <div v-for="log in simulatorService.terminalLogs.value" :key="log.id" class="terminal-line">
              <span class="log-time">[{{ log.time }}]</span>
              <span class="log-text">{{ log.text }}</span>
            </div>
            <div v-if="simulatorService.terminalLogs.value.length === 0" class="terminal-empty">
              Menunggu aktivasi simulasi... Tekan "Jalankan Simulasi Realtime" untuk mulai mengirim paket.
            </div>
          </div>
        </div>

        <!-- Footer Notice -->
        <div class="simulator-modal-footer">
          <div class="footer-info">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Seluruh metrik dan grafik di Dashboard & Monitoring akan bergerak dinamis dan tersinkronisasi secara real-time.</span>
          </div>
          <button class="btn-primary-close" @click="$emit('close')">Tutup Panel</button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';
import { simulatorService, SCENARIOS } from '../services/simulatorService';
import { authService } from '../services/authService';

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
});

defineEmits(['close']);

const isSubmitting = ref(false);
const showModalToast = ref(false);
const modalToastMessage = ref('');

const isAdmin = computed(() => authService.isAdmin());

function triggerToast(msg) {
  modalToastMessage.value = msg;
  showModalToast.value = true;
  setTimeout(() => {
    showModalToast.value = false;
  }, 4000);
}

function handleToggle() {
  if (!isAdmin.value) {
    triggerToast('Hak akses terbatas: Mode Simulasi hanya dapat dikontrol oleh Administrator.');
    return;
  }
  simulatorService.toggle();
}

async function handleStartDbBatch() {
  if (!isAdmin.value) {
    triggerToast('Hak akses terbatas: Hanya Administrator yang dapat memulai batch simulasi.');
    return;
  }
  isSubmitting.value = true;
  try {
    const res = await simulatorService.startDatabaseBatch({
      cropVariety: 'Hanjeli Ketan Sukabumi (Varietas Unggul)',
      initialWeightKg: 135.0,
      initialMoisturePercent: 24.5,
    });
    triggerToast(`Sesi Batch ${res.batchCode} berhasil dibuat & diaktifkan di MySQL!`);
  } catch (err) {
    triggerToast(`Gagal: ${err.message}`);
  } finally {
    isSubmitting.value = false;
  }
}

async function handleCompleteDbBatch() {
  if (!isAdmin.value) {
    triggerToast('Hak akses terbatas: Hanya Administrator yang dapat menyelesaikan batch simulasi.');
    return;
  }
  isSubmitting.value = true;
  try {
    const res = await simulatorService.completeDatabaseBatch();
    triggerToast(`Sesi Batch ${res?.batchCode || ''} berhasil diselesaikan & disimpan ke riwayat!`);
  } catch (err) {
    triggerToast(`Gagal: ${err.message}`);
  } finally {
    isSubmitting.value = false;
  }
}

async function handleGenerateHistorySample() {
  if (!isAdmin.value) {
    triggerToast('Hak akses terbatas: Hanya Administrator yang dapat membuat sampel data.');
    return;
  }
  isSubmitting.value = true;
  try {
    const res = await simulatorService.generateHistoricalSample('Hanjeli Ketan Sukabumi (Grade A)');
    triggerToast(`Riwayat Batch ${res.batchCode} (36 data log) berhasil dibuat ke MySQL! Buka menu Riwayat untuk melihat detail.`);
  } catch (err) {
    triggerToast(`Gagal: ${err.message}`);
  } finally {
    isSubmitting.value = false;
  }
}

function selectScenario(key) {
  simulatorService.setScenario(key);
  if (simulatorService.isRunning.value) {
    simulatorService.sendPacket();
  }
}

function onSliderChange() {
  simulatorService.scenario.value = 'custom';
  if (simulatorService.isRunning.value) {
    simulatorService.sendPacket();
  }
}

function sendSinglePulse() {
  simulatorService.sendPacket();
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.simulator-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(7, 30, 39, 0.65);
  backdrop-filter: blur(6px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.simulator-modal-card {
  background: var(--color-white);
  border-radius: 20px;
  width: 100%;
  max-width: 880px;
  max-height: 92vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 18px;
  padding: 24px 28px;
  border: 1px solid var(--color-border);
}

:global(.dark-theme) .simulator-modal-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.simulator-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  border-bottom: 1px solid var(--color-border-subtle);
  padding-bottom: 14px;
}

:global(.dark-theme) .simulator-modal-header {
  border-bottom-color: rgba(30, 78, 97, 0.5);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.icon-sim-badge {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(13, 99, 27, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .icon-sim-badge {
  background: rgba(46, 125, 50, 0.25);
}

.title-with-badge {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.modal-title {
  font-size: 19px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0;
}

:global(.dark-theme) .modal-title {
  color: #FFFFFF !important;
}

.engine-badge {
  font-size: 11px;
  font-weight: 700;
  background: #E0F2FE;
  color: #0369A1;
  padding: 2px 8px;
  border-radius: 6px;
}

:global(.dark-theme) .engine-badge {
  background: rgba(2, 132, 199, 0.25);
  color: #38BDF8;
}

.modal-subtitle {
  font-size: 12.5px;
  color: var(--color-text-muted);
  margin: 2px 0 0 0;
}

:global(.dark-theme) .modal-subtitle {
  color: #94A3B8 !important;
}

.close-btn {
  background: var(--color-bg-light);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s;
}

.close-btn:hover {
  background: var(--color-border-subtle);
  color: var(--color-text-title);
}

:global(.dark-theme) .close-btn {
  background: #071E27;
  border-color: #1E4E61;
  color: #94A3B8;
}

:global(.dark-theme) .close-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

/* Master Switch Bar */
.master-switch-bar {
  background: var(--color-bg-light);
  border: 1.5px solid var(--color-border);
  border-radius: 16px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  transition: all 0.3s;
}

:global(.dark-theme) .master-switch-bar {
  background: #071E27;
  border-color: #1E4E61;
}

.master-switch-bar.active {
  background: #F0FDF4;
  border-color: #86EFAC;
}

:global(.dark-theme) .master-switch-bar.active {
  background: rgba(13, 99, 27, 0.15);
  border-color: rgba(74, 222, 128, 0.4);
}

.node-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.status-indicator-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #94A3B8;
  flex-shrink: 0;
}

.status-indicator-dot.pulse-green {
  background: #16A34A;
}

.node-title {
  font-size: 13.5px;
  color: var(--color-text-title);
  display: flex;
  align-items: center;
  gap: 8px;
}

:global(.dark-theme) .node-title {
  color: #FFFFFF !important;
}

.status-chip {
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
}

.chip-active {
  background: #DCFCE7;
  color: #15803D;
}

:global(.dark-theme) .chip-active {
  background: rgba(34, 197, 94, 0.25);
  color: #4ADE80;
}

.chip-standby {
  background: #F1F5F9;
  color: #64748B;
}

:global(.dark-theme) .chip-standby {
  background: rgba(255, 255, 255, 0.1);
  color: #94A3B8;
}

.node-meta {
  font-size: 12px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

:global(.dark-theme) .node-meta {
  color: #94A3B8 !important;
}

.master-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.speed-selector {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--color-text-muted);
}

.speed-buttons {
  display: flex;
  gap: 3px;
  background: var(--color-white);
  border: 1px solid var(--color-border);
  padding: 2px;
  border-radius: 8px;
}

:global(.dark-theme) .speed-buttons {
  background: #0B242F;
  border-color: #1E4E61;
}

.speed-btn {
  background: transparent;
  border: none;
  font-size: 11px;
  font-weight: 700;
  color: var(--color-text-muted);
  padding: 3px 7px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.15s;
}

:global(.dark-theme) .speed-btn {
  color: #94A3B8;
}

.speed-btn.active {
  background: #0D631B;
  color: #FFFFFF;
}

:global(.dark-theme) .speed-btn.active {
  background: #16A34A;
  color: #FFFFFF;
}

.btn-toggle-sim {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  border-radius: 12px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-start {
  background: #0D631B;
  color: #FFFFFF;
}

.btn-start:hover {
  background: #094713;
}

.btn-stop {
  background: #BA1A1A;
  color: #FFFFFF;
}

.btn-stop:hover {
  background: #901414;
}

/* Scenarios Grid */
.scenario-section {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.section-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0;
}

:global(.dark-theme) .section-title {
  color: #FFFFFF !important;
}

.section-hint {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

:global(.dark-theme) .section-hint {
  color: #94A3B8 !important;
}

.scenarios-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 8px;
}

.scenario-card {
  border: 1.5px solid var(--color-border);
  border-radius: 12px;
  padding: 10px 12px;
  background: var(--color-white);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

:global(.dark-theme) .scenario-card {
  background: #0B242F;
  border-color: #1E4E61;
}

.scenario-card:hover {
  border-color: #94A3B8;
  transform: translateY(-1px);
}

:global(.dark-theme) .scenario-card:hover {
  border-color: #4ADE80;
}

.scenario-card.selected {
  border-color: #0D631B;
  background: #F0FDF4;
}

:global(.dark-theme) .scenario-card.selected {
  border-color: #4ADE80;
  background: rgba(13, 99, 27, 0.2);
}

.sc-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sc-icon {
  font-size: 18px;
}

.sc-active-badge {
  font-size: 10px;
  font-weight: 700;
  color: #0D631B;
  background: #DCFCE7;
  padding: 1px 6px;
  border-radius: 4px;
}

:global(.dark-theme) .sc-active-badge {
  background: rgba(34, 197, 94, 0.25);
  color: #4ADE80;
}

.sc-name {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--color-text-title);
}

:global(.dark-theme) .sc-name {
  color: #FFFFFF !important;
}

.sc-desc {
  font-size: 10.5px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.3;
}

:global(.dark-theme) .sc-desc {
  color: #94A3B8 !important;
}

/* Sliders Section */
.sliders-section {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-pulse-single {
  background: var(--color-bg-light);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 5px 11px;
  font-size: 11.5px;
  font-weight: 600;
  color: var(--color-text-title);
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-pulse-single:hover {
  background: var(--color-border-subtle);
  color: var(--color-text-title);
}

:global(.dark-theme) .btn-pulse-single {
  background: #071E27;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .btn-pulse-single:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

.sliders-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.slider-item {
  background: var(--color-bg-light);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

:global(.dark-theme) .slider-item {
  background: #071E27;
  border-color: #1E4E61;
}

.slider-label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.label-name {
  font-size: 11.5px;
  font-weight: 600;
  color: var(--color-text-title);
}

:global(.dark-theme) .label-name {
  color: #CBD5E1 !important;
}

.label-val {
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-title);
}

:global(.dark-theme) .label-val {
  color: #F8FAFC !important;
}

.text-red { color: #BA1A1A; }
.text-blue { color: #005DB7; }
.text-amber { color: #D97706; }
.text-green { color: #0D631B; }
.text-muted { color: #94A3B8; }

:global(.dark-theme) .text-red { color: #F87171; }
:global(.dark-theme) .text-blue { color: #60A5FA; }
:global(.dark-theme) .text-amber { color: #FBBF24; }
:global(.dark-theme) .text-green { color: #4ADE80; }

.sim-range {
  width: 100%;
  accent-color: #0D631B;
  cursor: pointer;
}

.range-red { accent-color: #BA1A1A; }
.range-blue { accent-color: #005DB7; }
.range-amber { accent-color: #D97706; }
.range-green { accent-color: #0D631B; }

.range-scale {
  display: flex;
  justify-content: space-between;
  font-size: 9.5px;
  color: var(--color-text-muted);
}

:global(.dark-theme) .range-scale {
  color: #64748B;
}

/* Actuator Feedback Bar */
.actuator-feedback-bar {
  background: #071E27;
  color: #FFFFFF;
  border-radius: 12px;
  padding: 10px 16px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  font-size: 12px;
}

:global(.dark-theme) .actuator-feedback-bar {
  background: #041319;
  border: 1px solid #1E4E61;
}

.af-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.af-label {
  color: #94A3B8;
  font-size: 11px;
}

.af-val {
  font-weight: 700;
}

/* Terminal Log Section */
.terminal-section {
  background: #0B132B;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #1C2541;
}

.terminal-header {
  background: #1C2541;
  padding: 6px 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.terminal-title-group {
  display: flex;
  align-items: center;
  gap: 6px;
}

.terminal-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
}
.terminal-dot.red { background: #EF4444; }
.terminal-dot.yellow { background: #F59E0B; }
.terminal-dot.green { background: #10B981; }

.terminal-title {
  font-family: monospace;
  font-size: 11px;
  font-weight: 600;
  color: #94A3B8;
  margin-left: 4px;
}

.terminal-counter {
  font-family: monospace;
  font-size: 10.5px;
  color: #64748B;
}

.terminal-body {
  padding: 8px 12px;
  max-height: 100px;
  overflow-y: auto;
  font-family: monospace;
  font-size: 11px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.terminal-line {
  display: flex;
  gap: 8px;
}

.log-time {
  color: #64748B;
  flex-shrink: 0;
}

.log-text {
  color: #38BDF8;
  word-break: break-all;
}

.terminal-empty {
  color: #64748B;
  font-style: italic;
  text-align: center;
  padding: 10px 0;
}

/* Footer */
.simulator-modal-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid var(--color-border-subtle);
  padding-top: 12px;
  gap: 12px;
}

:global(.dark-theme) .simulator-modal-footer {
  border-top-color: rgba(30, 78, 97, 0.5);
}

.footer-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11.5px;
  color: var(--color-text-muted);
}

:global(.dark-theme) .footer-info {
  color: #94A3B8 !important;
}

.btn-primary-close {
  background: #071E27;
  color: #FFFFFF;
  border: none;
  padding: 8px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

:global(.dark-theme) .btn-primary-close {
  background: #0D631B;
  color: #FFFFFF;
}

:global(.dark-theme) .btn-primary-close:hover {
  background: #16A34A;
}

/* Database Session Bar */
.db-session-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--color-bg-light);
  border: 1.5px dashed var(--color-border);
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 16px;
  gap: 16px;
}

:global(.dark-theme) .db-session-bar {
  background: #071E27;
  border-color: #1E4E61;
}

.db-session-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.db-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: var(--color-border-subtle);
  color: var(--color-text-muted);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s;
}

:global(.dark-theme) .db-icon-box {
  background: rgba(255, 255, 255, 0.1);
  color: #94A3B8;
}

.db-icon-box.active-db {
  background: #D5ECF8;
  color: #005DB7;
}

:global(.dark-theme) .db-icon-box.active-db {
  background: rgba(0, 93, 183, 0.3);
  color: #60A5FA;
}

.db-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.db-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

:global(.dark-theme) .db-title {
  color: #FFFFFF !important;
}

.db-batch-badge-active {
  font-size: 11px;
  font-weight: 700;
  background: #DDF7D8;
  color: #0D631B;
  padding: 2px 8px;
  border-radius: 6px;
}

:global(.dark-theme) .db-batch-badge-active {
  background: rgba(34, 197, 94, 0.25);
  color: #4ADE80;
}

.db-batch-badge-idle {
  font-size: 10.5px;
  font-weight: 600;
  background: var(--color-border-subtle);
  color: var(--color-text-muted);
  padding: 2px 8px;
  border-radius: 6px;
}

:global(.dark-theme) .db-batch-badge-idle {
  background: rgba(255, 255, 255, 0.1);
  color: #94A3B8;
}

.db-subtitle {
  font-size: 11.5px;
  color: var(--color-text-muted);
  margin: 2px 0 0;
}

:global(.dark-theme) .db-subtitle {
  color: #94A3B8 !important;
}

.db-actions-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  flex-shrink: 0;
}

.btn-db-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.15s ease;
}

.btn-db-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-db-start {
  background: #0D631B;
  color: #FFFFFF;
}

.btn-db-start:hover:not(:disabled) {
  background: #094713;
}

.btn-db-complete {
  background: #005DB7;
  color: #FFFFFF;
}

.btn-db-complete:hover:not(:disabled) {
  background: #004589;
}

.btn-db-seed {
  background: var(--color-bg-light);
  color: var(--color-text-title);
  border: 1px solid var(--color-border);
}

:global(.dark-theme) .btn-db-seed {
  background: #0E2C39;
  border-color: #1E4E61;
  color: #F8FAFC;
}

.btn-db-seed:hover:not(:disabled) {
  background: var(--color-border-subtle);
}

:global(.dark-theme) .btn-db-seed:hover:not(:disabled) {
  background: #133947;
}

/* Modal Toast Alert */
.modal-toast-alert {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #DDF7D8;
  border: 1px solid #7FD17B;
  color: #0D631B;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 14px;
}

:global(.dark-theme) .modal-toast-alert {
  background: rgba(34, 197, 94, 0.2);
  border-color: rgba(34, 197, 94, 0.4);
  color: #4ADE80;
}

.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all 0.25s ease;
}

.toast-slide-enter-from,
.toast-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@media (max-width: 640px) {
  .db-session-bar {
    flex-direction: column;
    align-items: stretch;
  }
  .db-actions-row {
    flex-direction: column;
    align-items: stretch;
  }
  .btn-db-action {
    justify-content: center;
  }
  .sliders-grid {
    grid-template-columns: 1fr;
  }
  .master-switch-bar {
    flex-direction: column;
    align-items: flex-start;
  }
  .master-actions {
    width: 100%;
    flex-direction: column;
    align-items: stretch;
  }
  .actuator-feedback-bar {
    grid-template-columns: repeat(2, 1fr);
  }
  .simulator-modal-footer {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
