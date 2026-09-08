<template>
  <div class="admin-page">
    <div class="admin-content">
      <!-- Top Title & Action Bar -->
      <div class="header-action-row">
        <div class="title-group">
          <h1 class="page-title">{{ $t('settings.safetyThresholds') }}</h1>
          <p class="page-subtitle">{{ $t('settings.subtitle') }}</p>
        </div>
      </div>

      <!-- Alert / Feedback Banner -->
      <div v-if="adminAlert.text" class="admin-feedback-toast" :class="adminAlert.type">
        <svg v-if="adminAlert.type === 'success'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>{{ adminAlert.text }}</span>
        <button class="close-toast-btn" @click="adminAlert.text = ''">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="content-card">
        <form @submit.prevent="saveMasterSettings" class="master-form-container">
          <!-- Master System Operational State & Mode Controls -->
          <div class="form-section-title">
            <h3>Mode & Status Operasional Sistem</h3>
            <p>Kontrol utama status aktivasi sistem, sumber aliran data IoT, dan lingkungan server.</p>
          </div>

          <div class="form-grid-3">
            <div class="form-field-group">
              <label class="form-label">Status Operasional Sistem</label>
              <select v-model="masterSettings.isSystemActive" class="modern-input">
                <option :value="true">Sistem Aktif (Operasional Penuh)</option>
                <option :value="false">Sistem Dinonaktifkan (Standby / Maintenance)</option>
              </select>
              <small class="form-hint">Saat nonaktif, sesi pengeringan baru ditangguhkan.</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">Mode Sumber Data IoT</label>
              <select v-model="masterSettings.iotMode" class="modern-input">
                <option value="SIMULATION">Mode Simulasi IoT (ESP32 Software)</option>
                <option value="HARDWARE">Mode Langsung Alat Fisik (ESP32 Live)</option>
              </select>
              <small class="form-hint">Pilih sumber telemetri data sensor greenhouse.</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">Lingkungan Sistem (Environment)</label>
              <select v-model="masterSettings.environmentMode" class="modern-input">
                <option value="LOCAL">Local Development / Testbed</option>
                <option value="PRODUCTION">Production Live Greenhouse</option>
              </select>
              <small class="form-hint">Target deployment & konfigurasi server.</small>
            </div>
          </div>

          <div class="form-section-title" style="margin-top: 12px;">
            <h3>Ambang Batas & Parameter Teknis Pengeringan</h3>
            <p>Batas keselamatan suhu, target kadar air, dan frekuensi sampling ESP32.</p>
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label class="form-label">{{ $t('settings.maxSafeTemp') }}</label>
              <input type="number" step="0.5" v-model.number="masterSettings.maxSafeTemp" class="modern-input" required />
              <small class="form-hint">{{ $t('settings.maxSafeTempDesc') }}</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">{{ $t('settings.minHumidityTrigger') }}</label>
              <input type="number" step="1" v-model.number="masterSettings.minHumidityTrigger" class="modern-input" required />
              <small class="form-hint">{{ $t('settings.minHumidityDesc') }}</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">{{ $t('settings.targetMoistureDefault') }}</label>
              <input type="number" step="0.1" v-model.number="masterSettings.targetMoistureDefault" class="modern-input" required />
              <small class="form-hint">{{ $t('settings.targetMoistureDesc') }}</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">{{ $t('settings.samplingInterval') }}</label>
              <input type="number" step="1" min="1" max="60" v-model.number="masterSettings.samplingIntervalSeconds" class="modern-input" required />
              <small class="form-hint">Interval pengiriman paket data sensor ESP32 ke database (Standar: 5 detik).</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">{{ $t('settings.exhaustAutoThreshold') }}</label>
              <input type="number" step="0.5" v-model.number="masterSettings.exhaustAutoThreshold" class="modern-input" required />
              <small class="form-hint">Suhu internal untuk menyalakan exhaust fan sirkulasi secara otomatis.</small>
            </div>

            <div class="form-field-group">
              <label class="form-label">{{ $t('settings.heaterAutoThreshold') }}</label>
              <input type="number" step="0.5" v-model.number="masterSettings.heaterAutoThreshold" class="modern-input" required />
              <small class="form-hint">Suhu batas bawah untuk mengaktifkan pemanas keramik bantu (PTC heater).</small>
            </div>
          </div>

          <div class="form-action-foot">
            <button type="button" class="btn-secondary-action" @click="handleResetDefaults" :disabled="isSavingSettings">
              Reset ke Standar
            </button>
            <button type="submit" class="btn-primary" :disabled="isSavingSettings">
              {{ isSavingSettings ? 'Menyimpan...' : $t('settings.saveSettingsBtn') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { settingsService } from '../../services/settingsService'
import { confirmDialog } from '../../services/confirmDialogService'

const isSavingSettings = ref(false)

const adminAlert = reactive({
  text: '',
  type: 'success'
})

function showAlert(text, type = 'success') {
  adminAlert.text = text
  adminAlert.type = type
  setTimeout(() => {
    if (adminAlert.text === text) adminAlert.text = ''
  }, 5000)
}

const masterSettings = reactive({
  isSystemActive: true,
  iotMode: 'SIMULATION',
  environmentMode: 'LOCAL',
  maxSafeTemp: 55.0,
  minHumidityTrigger: 70.0,
  targetMoistureDefault: 12.0,
  exhaustAutoThreshold: 48.0,
  heaterAutoThreshold: 38.0,
  samplingIntervalSeconds: 5
})

async function fetchSettings() {
  try {
    const res = await settingsService.getSettings()
    if (res) Object.assign(masterSettings, res)
  } catch (e) {
    console.warn('Could not fetch settings:', e.message)
  }
}

async function handleResetDefaults() {
  const confirmed = await confirmDialog({
    title: 'Reset Parameter ke Nilai Standar?',
    message: 'Seluruh nilai parameter ambang batas operasional akan dikembalikan ke setelan pabrik / riset STAS-RG.',
    details: 'Max Temp: 55°C • Target Moisture: 12% • Sampling: 5s',
    type: 'warning',
    confirmText: 'Ya, Kembalikan ke Standar',
    cancelText: 'Batal',
  })

  if (confirmed) {
    masterSettings.isSystemActive = true
    masterSettings.iotMode = 'SIMULATION'
    masterSettings.environmentMode = 'LOCAL'
    masterSettings.maxSafeTemp = 55.0
    masterSettings.minHumidityTrigger = 70.0
    masterSettings.targetMoistureDefault = 12.0
    masterSettings.exhaustAutoThreshold = 48.0
    masterSettings.heaterAutoThreshold = 38.0
    masterSettings.samplingIntervalSeconds = 5
    showAlert('Parameter dikembalikan ke nilai standar. Klik Simpan Konfigurasi untuk menerapkan.')
  }
}

async function saveMasterSettings() {
  isSavingSettings.value = true
  try {
    await settingsService.updateSettings(masterSettings)
    showAlert('Konfigurasi parameter master greenhouse berhasil disimpan ke MySQL!')
  } catch (err) {
    showAlert(err.message || 'Gagal menyimpan pengaturan.', 'error')
  } finally {
    isSavingSettings.value = false
  }
}

onMounted(() => {
  fetchSettings()
})
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

.admin-feedback-toast {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  }

.admin-feedback-toast.success {
  background: #E8F5E9;
  color: #0D631B;
  border: 1px solid #C8E6C9;
}

.admin-feedback-toast.error {
  background: #FFEBEE;
  color: #C62828;
  border: 1px solid #FFCDD2;
}

.close-toast-btn {
  background: transparent;
  border: none;
  font-size: 16px;
  cursor: pointer;
  margin-left: auto;
  color: inherit;
  opacity: 0.7;
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

.master-form-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-section-title {
  padding-bottom: 8px;
  border-bottom: 1px solid var(--color-border);
}

.form-section-title h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0 0 4px 0;
}

.form-section-title p {
  font-size: 13px;
  color: var(--color-text-muted);
  margin: 0;
}

.form-grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}

.form-grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.form-field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.modern-input {
  padding: 10px 14px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  background: var(--color-bg);
  color: var(--color-text-title);
  outline: none;
  transition: border-color 0.2s;
}

.modern-input:focus {
  border-color: #0D631B;
}

.form-hint {
  font-size: 12px;
  color: var(--color-text-muted);
}

.form-action-foot {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn-secondary-action {
  padding: 10px 20px;
  background: transparent;
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary-action:hover:not(:disabled) {
  background: var(--color-bg);
  color: var(--color-text-title);
  border-color: #94A3B8;
}

.btn-primary {
  padding: 10px 24px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
    transition: background 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #15803D;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 900px) {
  .admin-content { padding: 20px 16px; }
  .form-grid-2 { grid-template-columns: 1fr; }
}
</style>
