<template>
  <div class="drying-form-page">
    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="desktop-content">
      <!-- Header -->
      <div class="page-header-group">
        <div class="title-with-tag">
          <h1 class="page-title">{{ $t('dryingForm.title') || 'Inisialisasi Batch Pengeringan Baru' }}</h1>
        </div>
        <p class="page-subtitle">
          Konfigurasi parameter varietas, massa gabah, mode kontrol hybrid, dan target kadar air standar mutu SNI.
        </p>
      </div>

      <!-- Quick Preset Selector Bar -->
      <div class="preset-selector-bar">
        <span class="preset-lbl">Preset Cepat:</span>
        <button 
          type="button" 
          class="preset-pill" 
          :class="{ active: selectedPreset === 'ketan_grade_a' }"
          @click="applyPreset('ketan_grade_a')"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9A9 9 0 0 1 3 11a9 9 0 0 1 9-9z"></path>
            <path d="M12 7v10M8 11l4-4 4 4"></path>
          </svg>
          <span>Hanjeli Ketan (Grade A)</span>
        </button>
        <button 
          type="button" 
          class="preset-pill" 
          :class="{ active: selectedPreset === 'pangan_lokal' }"
          @click="applyPreset('pangan_lokal')"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
          <span>Hanjeli Pangan</span>
        </button>
        <button 
          type="button" 
          class="preset-pill" 
          :class="{ active: selectedPreset === 'teh_sangrai' }"
          @click="applyPreset('teh_sangrai')"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
          </svg>
          <span>Hanjeli Teh</span>
        </button>
        <button 
          type="button" 
          class="preset-pill" 
          :class="{ active: selectedPreset === 'custom' }"
          @click="selectedPreset = 'custom'"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
          <span>Kustom Penuh</span>
        </button>
      </div>

      <!-- 2 Columns: Form on Left, Live Simulation & Summary on Right -->
      <div class="form-layout-grid">
        <!-- Left Form Card -->
        <div class="form-card-main">
          <!-- Section 1: Identitas & Varietas -->
          <div class="form-section">
            <div class="section-head">
              <div class="section-num">1</div>
              <div>
                <h3 class="section-title">Identitas Batch & Varietas Hanjeli</h3>
                <p class="section-sub">Tentukan varietas bibit dan penanggung jawab operasional</p>
              </div>
            </div>

            <div class="form-grid-2">
              <div class="form-group">
                <label class="form-label">Varietas Biji Hanjeli</label>
                <input 
                  type="text" 
                  v-model="formData.cropVariety" 
                  placeholder="Contoh: Hanjeli Ketan Sukabumi (Grade A)" 
                  class="form-input"
                  required
                />
              </div>

              <div class="form-group">
                <label class="form-label">Operator Penanggung Jawab</label>
                <input 
                  type="text" 
                  v-model="formData.operatorName" 
                  placeholder="Nama Operator Lapangan" 
                  class="form-input"
                />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Posisi Rak Pengeringan (Tray Level)</label>
              <select v-model="formData.trayLevel" class="form-input">
                <option value="Semua Rak (Tingkat 1, 2, 3)">Semua Rak (Tingkat 1, 2, 3) — Kapasitas Penuh</option>
                <option value="Rak Bawah Saja (Tingkat 1)">Rak Bawah Saja (Tingkat 1)</option>
                <option value="Rak Tengah Saja (Tingkat 2)">Rak Tengah Saja (Tingkat 2 — Sirkulasi Maksimal)</option>
                <option value="Rak Atas Saja (Tingkat 3)">Rak Atas Saja (Tingkat 3 — Radiasi Puncak)</option>
              </select>
            </div>
          </div>

          <!-- Section 2: Parameter Massa & Kadar Air -->
          <div class="form-section">
            <div class="section-head">
              <div class="section-num">2</div>
              <div>
                <h3 class="section-title">Parameter Massa & Kadar Air (Moisture)</h3>
                <p class="section-sub">Data awal hasil panen untuk kalkulasi rendemen & kesetimbangan massa</p>
              </div>
            </div>

            <div class="form-grid-3">
              <div class="form-group">
                <label class="form-label">Bobot Basah Masuk (W₀)</label>
                <div class="input-with-unit">
                  <input 
                    type="number" 
                    step="0.5" 
                    v-model.number="formData.initialWeightKg" 
                    class="form-input unit-pad"
                    min="1"
                    required
                  />
                  <span class="unit-tag">kg</span>
                </div>
                <span class="helper-text">Kapasitas maks: 200 kg</span>
              </div>

              <div class="form-group">
                <label class="form-label">Kadar Air Awal (M₀)</label>
                <div class="input-with-unit">
                  <input 
                    type="number" 
                    step="0.1" 
                    v-model.number="formData.initialMoisturePercent" 
                    class="form-input unit-pad"
                    min="10"
                    max="60"
                    required
                  />
                  <span class="unit-tag">%</span>
                </div>
                <span class="helper-text">Kondisi panen basah</span>
              </div>

              <div class="form-group">
                <label class="form-label">Target Kadar Air (Mₑ)</label>
                <div class="input-with-unit">
                  <input 
                    type="number" 
                    step="0.1" 
                    v-model.number="formData.targetMoisturePercent" 
                    class="form-input unit-pad"
                    min="8"
                    max="20"
                    required
                  />
                  <span class="unit-tag">%</span>
                </div>
                <span class="helper-text text-green-dark">Standar SNI: ≤ 12.0%</span>
              </div>
            </div>
          </div>

          <!-- Section 3: Mode Pengeringan & Profil Suhu -->
          <div class="form-section">
            <div class="section-head">
              <div class="section-num">3</div>
              <div>
                <h3 class="section-title">Mode Kontrol & Ambang Batas Suhu</h3>
                <p class="section-sub">Atur strategi pemanasan dan aktivasi aktuator otomatis</p>
              </div>
            </div>

            <div class="mode-cards-grid">
              <div 
                class="mode-select-card" 
                :class="{ active: formData.dryingMode === 'HYBRID_SOLAR_ELECTRIC' }"
                @click="formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'"
                role="button"
                tabindex="0"
                @keydown.enter.space="formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'"
              >
                <div class="mode-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                  </svg>
                </div>
                <div class="mode-info">
                  <h4 class="mode-name">Hybrid Solar-Electric (Rekomendasi)</h4>
                  <p class="mode-desc">Mengutamakan panas matahari. Pemanas keramik menyala otomatis saat mendung / sore hari.</p>
                </div>
                <div class="mode-check-circle">
                  <div v-if="formData.dryingMode === 'HYBRID_SOLAR_ELECTRIC'" class="mode-check-dot"></div>
                </div>
              </div>

              <div 
                class="mode-select-card" 
                :class="{ active: formData.dryingMode === 'PURE_SOLAR' }"
                @click="formData.dryingMode = 'PURE_SOLAR'"
                role="button"
                tabindex="0"
                @keydown.enter.space="formData.dryingMode = 'PURE_SOLAR'"
              >
                <div class="mode-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                  </svg>
                </div>
                <div class="mode-info">
                  <h4 class="mode-name">Pure Solar Greenhouse</h4>
                  <p class="mode-desc">100% menggunakan energi surya alami & sirkulasi kipas exhaust tanpa pemanas listrik.</p>
                </div>
                <div class="mode-check-circle">
                  <div v-if="formData.dryingMode === 'PURE_SOLAR'" class="mode-check-dot"></div>
                </div>
              </div>

              <div 
                class="mode-select-card" 
                :class="{ active: formData.dryingMode === 'ELECTRIC_BOOSTER' }"
                @click="formData.dryingMode = 'ELECTRIC_BOOSTER'"
                role="button"
                tabindex="0"
                @keydown.enter.space="formData.dryingMode = 'ELECTRIC_BOOSTER'"
              >
                <div class="mode-icon">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                  </svg>
                </div>
                <div class="mode-info">
                  <h4 class="mode-name">Electric Rapid Booster</h4>
                  <p class="mode-desc">Pemanasan kontinu intensif untuk pengeringan cepat pada malam hari atau musim hujan lebat.</p>
                </div>
                <div class="mode-check-circle">
                  <div v-if="formData.dryingMode === 'ELECTRIC_BOOSTER'" class="mode-check-dot"></div>
                </div>
              </div>
            </div>

            <div class="form-grid-2" style="margin-top: 16px;">
              <div class="form-group">
                <label class="form-label">Batas Ambang Suhu Kritis (T_max)</label>
                <div class="input-with-unit">
                  <input 
                    type="number" 
                    step="0.5" 
                    v-model.number="formData.maxTempLimit" 
                    class="form-input unit-pad"
                  />
                  <span class="unit-tag">°C</span>
                </div>
                <span class="helper-text">Kipas exhaust otomatis turbo jika suhu melebihi ambang ini</span>
              </div>

              <div class="form-group">
                <label class="form-label">Kecepatan Awal Kipas Sirkulasi</label>
                <select v-model="formData.initialFanSpeed" class="form-input">
                  <option value="AUTO">Otomatis Terkendali Sensor (Smart PID)</option>
                  <option value="50">Sedang (50% PWM)</option>
                  <option value="75">Tinggi (75% PWM)</option>
                  <option value="100">Maksimal (100% PWM Turbo)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Section 4: Catatan Sesi -->
          <div class="form-section">
            <div class="section-head">
              <div class="section-num">4</div>
              <div>
                <h3 class="section-title">Catatan & Evaluasi Awal</h3>
                <p class="section-sub">Informasi asal petani, lokasi panen, atau kondisi mutu awal</p>
              </div>
            </div>

            <div class="form-group">
              <textarea 
                v-model="formData.notes" 
                rows="2" 
                class="form-input" 
                placeholder="Masukkan catatan spesifikasi panen atau perlakuan khusus..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- RIGHT COLUMN: PREDICTIVE SIMULATION & SUMMARY CARD -->
        <!-- ===================================================================== -->
        <div class="summary-sidebar-col">
          <div class="summary-card-pro">
            <div class="sim-header">
              <div class="sim-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
              </div>
              <div>
                <h2 class="summary-heading">Kalkulator & Simulasi Prediktif</h2>
                <p class="summary-sub">Estimasi hasil pengeringan berdasarkan data yang dimasukkan</p>
              </div>
            </div>

            <!-- Mass Balance Projected Results -->
            <div class="mass-balance-box">
              <span class="box-tag">KESETIMBANGAN MASSA & RENDEMEN</span>
              <div class="mb-grid">
                <div class="mb-item">
                  <span class="mb-lbl">Estimasi Bobot Kering (W_f):</span>
                  <span class="mb-val text-green-dark">{{ estimatedFinalWeightKg }} kg</span>
                </div>
                <div class="mb-item">
                  <span class="mb-lbl">Air Yang Akan Diuapkan:</span>
                  <span class="mb-val text-purple">{{ estimatedWaterLossKg }} kg</span>
                </div>
                <div class="mb-item">
                  <span class="mb-lbl">Rendemen Gabah Kering:</span>
                  <span class="mb-val text-blue">{{ estimatedYieldPercent }}%</span>
                </div>
                <div class="mb-item">
                  <span class="mb-lbl">Estimasi Durasi Pengeringan:</span>
                  <span class="mb-val text-orange">~{{ estimatedDurationHours }} Jam</span>
                </div>
              </div>
            </div>

            <!-- Projected Moisture Curve Mini Chart -->
            <div class="projected-chart-box">
              <div class="proj-head">
                <span class="proj-title">Proyeksi Peluruhan Kadar Air (Projected Kinetics)</span>
                <span class="proj-badge">{{ formData.initialMoisturePercent }}% → {{ formData.targetMoisturePercent }}%</span>
              </div>
              <div class="proj-svg-wrap">
                <svg viewBox="0 0 360 110" class="w-full">
                  <line x1="25" y1="15" x2="340" y2="15" stroke="#F1F5F9"/>
                  <line x1="25" y1="50" x2="340" y2="50" stroke="#F1F5F9"/>
                  <line x1="25" y1="85" x2="340" y2="85" stroke="#CBD5E1"/>

                  <!-- Projected curve -->
                  <path :d="projectedCurvePath" fill="none" stroke="#0D631B" stroke-width="3" stroke-dasharray="4 2"/>
                  <circle :cx="25" :cy="projectedStartPoint.y" r="4" fill="#0D631B"/>
                  <circle :cx="340" :cy="projectedEndPoint.y" r="4" fill="#0D631B"/>
                </svg>
              </div>
              <div class="proj-time-labels">
                <span>0 Jam (Awal)</span>
                <span>Proyeksi {{ estimatedDurationHours }} Jam</span>
              </div>
            </div>

            <!-- Estimated Energy & Cost -->
            <div class="energy-est-box">
              <div class="energy-row">
                <div class="energy-col">
                  <span class="energy-lbl">Estimasi Energi Listrik</span>
                  <strong class="energy-val">{{ estimatedEnergyKwh }} kWh</strong>
                </div>
                <div class="energy-col text-right">
                  <span class="energy-lbl">Estimasi Biaya Listrik</span>
                  <strong class="energy-val text-green-dark">Rp {{ estimatedCostIdr }}</strong>
                </div>
              </div>
            </div>

            <!-- Start & Cancel Actions -->
            <div class="summary-actions">
              <button 
                class="btn-start-action" 
                @click="handleStart"
                :disabled="isSubmitting"
              >
                <svg width="15" height="15" viewBox="0 0 12 14" fill="currentColor">
                  <path d="M1.5 1.5L10.5 7L1.5 12.5V1.5Z"/>
                </svg>
                <span>{{ isSubmitting ? 'Memulai Sesi...' : 'Mulai Pengeringan Sekarang' }}</span>
              </button>

              <button class="btn-cancel-action" @click="$emit('cancel')">
                Batalkan & Kembali
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Layout -->
    <div v-else class="mobile-content">
      <div class="mobile-intro-card">
        <button class="back-link-btn" @click="$emit('cancel')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Kembali</span>
        </button>
        <h2 class="m-title">Inisialisasi Batch Baru</h2>
        <p class="intro-p">Atur parameter dan target pengeringan gabah hanjeli.</p>
      </div>

      <div class="mobile-form-fields">
        <div class="field-item">
          <label class="field-label">Varietas Biji Hanjeli</label>
          <input type="text" v-model="formData.cropVariety" class="field-input" required />
        </div>

        <div class="field-grid-2">
          <div class="field-item">
            <label class="field-label">Bobot Awal (kg)</label>
            <input type="number" step="0.5" v-model.number="formData.initialWeightKg" class="field-input" required />
          </div>
          <div class="field-item">
            <label class="field-label">Kadar Air Awal (%)</label>
            <input type="number" step="0.1" v-model.number="formData.initialMoisturePercent" class="field-input" required />
          </div>
        </div>

        <div class="field-item">
          <label class="field-label">Target Kadar Air Akhir (%)</label>
          <input type="number" step="0.1" v-model.number="formData.targetMoisturePercent" class="field-input" required />
          <span class="field-helper">Standar mutu SNI: ≤ 12.0%</span>
        </div>

        <div class="field-item">
          <label class="field-label">Mode Pengeringan</label>
          <select v-model="formData.dryingMode" class="field-input">
            <option value="HYBRID_SOLAR_ELECTRIC">Hybrid Solar-Electric (Otomatis)</option>
            <option value="PURE_SOLAR">Pure Solar Greenhouse</option>
            <option value="ELECTRIC_BOOSTER">Electric Booster</option>
          </select>
        </div>
      </div>

      <!-- Quick Mobile Summary -->
      <div class="m-calc-box">
        <div class="m-calc-row">
          <span>Estimasi Bobot Kering:</span>
          <strong>{{ estimatedFinalWeightKg }} kg</strong>
        </div>
        <div class="m-calc-row">
          <span>Air Teruapkan:</span>
          <strong>{{ estimatedWaterLossKg }} kg</strong>
        </div>
        <div class="m-calc-row">
          <span>Estimasi Durasi:</span>
          <strong class="text-green-dark">~{{ estimatedDurationHours }} Jam</strong>
        </div>
      </div>

      <div class="mobile-actions-col">
        <button class="btn-start-mobile" @click="handleStart" :disabled="isSubmitting">
          <svg width="14" height="14" viewBox="0 0 12 14" fill="currentColor">
            <path d="M1.5 1.5L10.5 7L1.5 12.5V1.5Z"/>
          </svg>
          <span>{{ isSubmitting ? 'Memulai...' : 'Mulai Pengeringan' }}</span>
        </button>
        <button class="btn-cancel-link" @click="$emit('cancel')">
          Batalkan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { batchService } from '../../services/batchService'

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['start-process', 'cancel'])

const selectedPreset = ref('ketan_grade_a')
const isSubmitting = ref(false)

const formData = reactive({
  cropVariety: 'Hanjeli Ketan Sukabumi (Grade A)',
  operatorName: 'Operator Green House',
  trayLevel: 'Semua Rak (Tingkat 1, 2, 3)',
  initialWeightKg: 135.0,
  initialMoisturePercent: 24.5,
  targetMoisturePercent: 12.0,
  dryingMode: 'HYBRID_SOLAR_ELECTRIC',
  maxTempLimit: 55.0,
  initialFanSpeed: 'AUTO',
  notes: 'Pengeringan gabah panen basah varietas unggul dengan kontrol suhu stabil.'
})

function applyPreset(presetKey) {
  selectedPreset.value = presetKey
  if (presetKey === 'ketan_grade_a') {
    formData.cropVariety = 'Hanjeli Ketan Sukabumi (Grade A)'
    formData.initialWeightKg = 135.0
    formData.initialMoisturePercent = 24.5
    formData.targetMoisturePercent = 11.5
    formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'
    formData.maxTempLimit = 52.0
    formData.notes = 'Standar ekspor mutu grade A, pengeringan suhu stabil tanpa panas kejut.'
  } else if (presetKey === 'pangan_lokal') {
    formData.cropVariety = 'Hanjeli Pangan Olahan Tepung'
    formData.initialWeightKg = 150.0
    formData.initialMoisturePercent = 26.0
    formData.targetMoisturePercent = 12.0
    formData.dryingMode = 'HYBRID_SOLAR_ELECTRIC'
    formData.maxTempLimit = 55.0
    formData.notes = 'Bahan baku tepung olahan pangan bergizi tinggi.'
  } else if (presetKey === 'teh_sangrai') {
    formData.cropVariety = 'Hanjeli Teh / Sangrai Aromatik'
    formData.initialWeightKg = 100.0
    formData.initialMoisturePercent = 22.0
    formData.targetMoisturePercent = 10.0
    formData.dryingMode = 'ELECTRIC_BOOSTER'
    formData.maxTempLimit = 58.0
    formData.notes = 'Kadar air sangat rendah untuk bahan teh seduh dan sangrai aromatik.'
  }
}

// Agritech Predictive Calculations
const estimatedFinalWeightKg = computed(() => {
  const w0 = formData.initialWeightKg || 100
  const m0 = formData.initialMoisturePercent || 25
  const me = formData.targetMoisturePercent || 12
  if (m0 <= me) return w0.toFixed(1)
  const wf = w0 * ((100 - m0) / (100 - me))
  return wf.toFixed(1)
})

const estimatedWaterLossKg = computed(() => {
  const w0 = formData.initialWeightKg || 100
  const wf = parseFloat(estimatedFinalWeightKg.value)
  return Math.max(0, w0 - wf).toFixed(1)
})

const estimatedYieldPercent = computed(() => {
  const w0 = formData.initialWeightKg || 100
  const wf = parseFloat(estimatedFinalWeightKg.value)
  if (w0 === 0) return '88.5'
  return ((wf / w0) * 100).toFixed(1)
})

const estimatedDurationHours = computed(() => {
  const m0 = formData.initialMoisturePercent || 25
  const me = formData.targetMoisturePercent || 12
  const drop = Math.max(1, m0 - me)
  const rate = formData.dryingMode === 'ELECTRIC_BOOSTER' ? 1.8 : 1.45
  return (drop / rate).toFixed(1)
})

const estimatedEnergyKwh = computed(() => {
  const dur = parseFloat(estimatedDurationHours.value)
  const multiplier = formData.dryingMode === 'PURE_SOLAR' ? 0.35 : formData.dryingMode === 'ELECTRIC_BOOSTER' ? 2.5 : 1.25
  return (dur * multiplier).toFixed(1)
})

const estimatedCostIdr = computed(() => {
  const kwh = parseFloat(estimatedEnergyKwh.value)
  return Math.round(kwh * 1500).toLocaleString('id-ID')
})

// Projected Kinetics Curve Path
const projectedStartPoint = { x: 25, y: 20 }
const projectedEndPoint = { x: 340, y: 85 }

const projectedCurvePath = computed(() => {
  return `M 25 20 C 110 25, 200 65, 340 85`
})

async function handleStart() {
  isSubmitting.value = true
  try {
    const payload = {
      cropVariety: formData.cropVariety || 'Hanjeli Ketan Sukabumi',
      operatorName: formData.operatorName || 'Operator Green House',
      trayLevel: formData.trayLevel || 'Semua Rak (1, 2, 3)',
      initialWeightKg: Number(formData.initialWeightKg) || 100,
      initialMoisturePercent: Number(formData.initialMoisturePercent) || 24.5,
      targetMoisturePercent: Number(formData.targetMoisturePercent) || 12.0,
      dryingMode: formData.dryingMode || 'HYBRID_SOLAR_ELECTRIC',
      notes: formData.notes || 'Siklus pengeringan aktif baru.'
    }
    await batchService.create(payload)
    emit('start-process')
  } catch (err) {
    console.warn('Backend create batch response:', err.message)
    emit('start-process')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.drying-form-page {
  width: 100%;
  min-height: 100%;
  background: transparent;
}

.desktop-content {
  padding: 32px 40px 48px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1280px;
  margin: 0 auto;
}

.page-header-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.header-breadcrumb {
  margin-bottom: 2px;
}

.back-link-btn {
  background: transparent;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  padding: 0;
  transition: color 0.15s ease;
}

.back-link-btn:hover {
  color: #0D631B;
}

.title-with-tag {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pro-tag {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 4px 10px;
  background: #E8F5E9;
  color: #0D631B;
  border-radius: 20px;
  border: 1px solid #A5D6A7;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--color-text-title);
  letter-spacing: -0.5px;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
  line-height: 1.5;
}

/* Preset Selector Bar */
.preset-selector-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  background: var(--color-white);
  padding: 12px 18px;
  border-radius: 14px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .preset-selector-bar {
  border-color: rgba(30, 58, 43, 0.6);
}

.preset-lbl {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.preset-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 20px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-bg-light);
  color: var(--color-text-title);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.preset-pill:hover {
  border-color: #0D631B;
}

.preset-pill.active {
  background: #E8F5E9;
  color: #0D631B;
  border-color: #0D631B;
  box-shadow: 0 1px 3px rgba(13, 99, 27, 0.15);
}

/* 2 Columns Layout */
.form-layout-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 24px;
  align-items: start;
}

.form-card-main {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .form-card-main {
  border-color: rgba(30, 58, 43, 0.6);
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--color-border-subtle, #F1F5F9);
}

.form-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.section-head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.section-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.section-title {
  font-size: 15.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.section-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  background: var(--color-bg-light);
  color: var(--color-text-title);
  font-size: 13.5px;
  transition: all 0.15s ease;
}

.form-input:focus {
  outline: none;
  border-color: #0D631B;
  background: var(--color-white);
  box-shadow: 0 0 0 3px rgba(13, 99, 27, 0.1);
}

.input-with-unit {
  position: relative;
  display: flex;
  align-items: center;
}

.unit-pad {
  padding-right: 40px;
}

.unit-tag {
  position: absolute;
  right: 12px;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-muted);
  pointer-events: none;
}

.helper-text {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

/* Mode Select Cards */
.mode-cards-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
}

.mode-select-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 13px 16px;
  border-radius: 12px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-bg-light);
  cursor: pointer;
  user-select: none;
  transition: all 0.18s ease;
}

:global(.dark-theme) .mode-select-card {
  border-color: rgba(30, 58, 43, 0.6);
  background: #0B1911;
}

.mode-select-card:hover {
  border-color: #94A3B8;
  transform: translateY(-1px);
}

.mode-select-card.active {
  border-color: #0D631B;
  background: rgba(13, 99, 27, 0.08);
  box-shadow: 0 1px 4px rgba(13, 99, 27, 0.12);
}

:global(.dark-theme) .mode-select-card.active {
  border-color: #4ADE80;
  background: rgba(74, 222, 128, 0.12);
}

.mode-icon {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 8px;
  background: rgba(13, 99, 27, 0.1);
  color: #0D631B;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .mode-icon {
  background: rgba(46, 125, 50, 0.25);
  color: #4ADE80;
}

.mode-info {
  flex: 1;
  min-width: 0;
}

.mode-name {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0 0 2px 0;
}

:global(.dark-theme) .mode-name {
  color: #FFFFFF;
}

.mode-desc {
  font-size: 11.5px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.35;
}

:global(.dark-theme) .mode-desc {
  color: #94A3B8;
}

.mode-check-circle {
  width: 18px;
  height: 18px;
  min-width: 18px;
  border-radius: 50%;
  border: 2px solid rgba(148, 163, 184, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: auto;
  flex-shrink: 0;
  transition: all 0.15s ease;
}

.mode-select-card.active .mode-check-circle {
  border-color: #0D631B;
  background: #FFFFFF;
}

:global(.dark-theme) .mode-select-card.active .mode-check-circle {
  border-color: #4ADE80;
  background: #0B1911;
}

.mode-check-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #0D631B;
}

:global(.dark-theme) .mode-check-dot {
  background: #4ADE80;
}

/* Right Summary Sidebar */
.summary-sidebar-col {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.summary-card-pro {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .summary-card-pro {
  border-color: rgba(30, 58, 43, 0.6);
}

.sim-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sim-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #E8F5E9;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.summary-heading {
  font-size: 16.5px;
  font-weight: 800;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.summary-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.mass-balance-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

:global(.dark-theme) .mass-balance-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.box-tag {
  font-size: 10.5px;
  font-weight: 800;
  color: var(--color-text-muted);
  letter-spacing: 0.5px;
}

.mb-grid {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.mb-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.mb-lbl {
  color: var(--color-text-muted);
}

.mb-val {
  font-weight: 800;
}

.projected-chart-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

:global(.dark-theme) .projected-chart-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.proj-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.proj-title {
  font-size: 12px;
  font-weight: 700;
  color: var(--color-text-title);
}

.proj-badge {
  font-size: 11px;
  font-weight: 700;
  color: #0D631B;
  background: #E8F5E9;
  padding: 2px 6px;
  border-radius: 4px;
}

.proj-svg-wrap {
  width: 100%;
  height: 110px;
}

.proj-time-labels {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: var(--color-text-muted);
}

.energy-est-box {
  border-top: 1px solid rgba(203, 213, 225, 0.4);
  padding-top: 14px;
}

:global(.dark-theme) .energy-est-box {
  border-top-color: rgba(30, 58, 43, 0.5);
}

.energy-row {
  display: flex;
  justify-content: space-between;
}

.energy-lbl {
  display: block;
  font-size: 11px;
  color: var(--color-text-muted);
  margin-bottom: 2px;
}

.energy-val {
  font-size: 16px;
  color: var(--color-text-title);
}

.summary-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 6px;
}

.btn-start-action {
  width: 100%;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
  box-shadow: 0 4px 12px rgba(13, 99, 27, 0.25);
}

.btn-start-action:hover {
  background: #094713;
}

.btn-start-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-cancel-action {
  width: 100%;
  background: transparent;
  color: var(--color-text-muted);
  border: 1px solid rgba(203, 213, 225, 0.5);
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-cancel-action:hover {
  background: var(--color-bg-light);
  color: var(--color-text-title);
}

/* Mobile Styling */
.mobile-content {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.mobile-intro-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
}

.m-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--color-text-title);
  margin: 6px 0 2px;
}

.intro-p {
  font-size: 13px;
  color: var(--color-text-muted);
  margin: 0;
}

.mobile-form-fields {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.field-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.field-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.field-label {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--color-text-title);
}

.field-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  background: var(--color-bg-light);
  font-size: 14px;
}

.field-helper {
  font-size: 11px;
  color: #0D631B;
}

.m-calc-box {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.m-calc-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

.mobile-actions-col {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-start-mobile {
  width: 100%;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
}

.btn-cancel-link {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 600;
  padding: 6px;
  cursor: pointer;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

/* Global utility */
.text-green-dark { color: #0D631B; }
.text-purple { color: #9333EA; }
.text-blue { color: #0284C7; }
.text-orange { color: #EA580C; }

@media (max-width: 1024px) {
  .form-layout-grid {
    grid-template-columns: 1fr;
  }
}
</style>
