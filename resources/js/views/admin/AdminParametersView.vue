<template>
  <div class="admin-page">
    <Transition name="fade-admin-params" mode="out-in">
      <AdminSkeleton v-if="isInitialLoading" />
      <div v-else class="admin-params-loaded-content">
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

          <!-- Telegram Bot & WhatsApp Gateway Integration -->
          <div class="form-section-title" style="margin-top: 18px;">
            <h3>Integrasi Notifikasi Instan (Telegram Bot & WhatsApp)</h3>
            <p>Konfigurasi bot Telegram dan gateway WhatsApp untuk siaran otomatis alert darurat, batch selesai, dan ringkasan harian.</p>
          </div>

          <div class="form-grid-2">
            <!-- Telegram Bot Config -->
            <div class="notif-config-card">
              <div class="notif-card-header">
                <div class="notif-badge badge-telegram">Telegram Bot API</div>
                <label class="switch-small">
                  <input type="checkbox" v-model="masterSettings.telegram.enabled" />
                  <span class="slider-small round"></span>
                </label>
              </div>
              <div class="form-field-group" style="margin-top: 10px;">
                <label class="form-label">Telegram Bot Token</label>
                <input 
                  type="text" 
                  v-model="masterSettings.telegram.botToken" 
                  placeholder="Contoh: 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ" 
                  class="modern-input" 
                  :disabled="!masterSettings.telegram.enabled"
                />
                <small class="form-hint">Bot Token resmi dari <strong>@BotFather</strong> di Telegram.</small>
              </div>
              <div class="form-field-group">
                <label class="form-label">Chat ID / Channel Group ID</label>
                <input 
                  type="text" 
                  v-model="masterSettings.telegram.chatId" 
                  placeholder="Contoh: -100192837465 atau ID Pengguna" 
                  class="modern-input" 
                  :disabled="!masterSettings.telegram.enabled"
                />
                <small class="form-hint">ID grup / channel tim operator (cek via <strong>@userinfobot</strong>).</small>
              </div>
              <button 
                type="button" 
                class="btn-test-channel" 
                :disabled="!masterSettings.telegram.enabled || isTestingTelegram"
                @click="handleTestTelegram"
              >
                <span>{{ isTestingTelegram ? 'Menguji...' : 'Uji Kirim Telegram' }}</span>
              </button>
            </div>

            <!-- WhatsApp Gateway Config -->
            <div class="notif-config-card">
              <div class="notif-card-header">
                <div class="notif-badge badge-whatsapp">WhatsApp Gateway</div>
                <label class="switch-small">
                  <input type="checkbox" v-model="masterSettings.whatsapp.enabled" />
                  <span class="slider-small round"></span>
                </label>
              </div>
              <div class="form-field-group" style="margin-top: 10px;">
                <label class="form-label">Nomor WhatsApp Operator</label>
                <input 
                  type="text" 
                  v-model="masterSettings.whatsapp.targetNumber" 
                  placeholder="Contoh: 081388992211" 
                  class="modern-input" 
                  :disabled="!masterSettings.whatsapp.enabled"
                />
                <small class="form-hint">Nomor tujuan penerima alert darurat.</small>
              </div>
              <div class="form-field-group">
                <label class="form-label">API Key / Token Authorization</label>
                <input 
                  type="password" 
                  v-model="masterSettings.whatsapp.apiKey" 
                  placeholder="API Key Fonnte / Wablas" 
                  class="modern-input" 
                  :disabled="!masterSettings.whatsapp.enabled"
                />
                <small class="form-hint">Gateway API URL: {{ masterSettings.whatsapp.apiUrl || 'https://api.fonnte.com/send' }}</small>
              </div>
              <button 
                type="button" 
                class="btn-test-channel" 
                :disabled="!masterSettings.whatsapp.enabled || isTestingWhatsApp"
                @click="handleTestWhatsApp"
              >
                <span>{{ isTestingWhatsApp ? 'Menguji...' : 'Uji Kirim WhatsApp' }}</span>
              </button>
            </div>
          </div>

          <div class="form-action-foot">
            <button type="button" class="btn-secondary-action" @click="handleSendDailyDigest" :disabled="isSendingDailyDigest">
              <span>{{ isSendingDailyDigest ? 'Menyiarkan...' : 'Siarkan Daily Digest Sekarang' }}</span>
            </button>
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
    </Transition>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import AdminSkeleton from '../../components/AdminSkeleton.vue'
import { settingsService } from '../../services/settingsService'
import { confirmDialog } from '../../services/confirmDialogService'

const isInitialLoading = ref(true)
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

const isTestingTelegram = ref(false)
const isTestingWhatsApp = ref(false)
const isSendingDailyDigest = ref(false)

const masterSettings = reactive({
  isSystemActive: true,
  iotMode: 'SIMULATION',
  environmentMode: 'LOCAL',
  maxSafeTemp: 55.0,
  minHumidityTrigger: 70.0,
  targetMoistureDefault: 12.0,
  exhaustAutoThreshold: 48.0,
  heaterAutoThreshold: 38.0,
  samplingIntervalSeconds: 5,
  whatsapp: {
    enabled: true,
    targetNumber: '+62 813-8899-2211',
    apiUrl: 'https://api.fonnte.com/send',
    apiKey: '',
  },
  telegram: {
    enabled: false,
    botToken: '',
    chatId: '',
  }
})

async function fetchSettings() {
  try {
    const res = await settingsService.getSettings()
    if (res) {
      Object.assign(masterSettings, res)
      if (res.whatsapp) masterSettings.whatsapp = { ...masterSettings.whatsapp, ...res.whatsapp }
      if (res.telegram) masterSettings.telegram = { ...masterSettings.telegram, ...res.telegram }
    }
  } catch (e) {
    console.warn('Could not fetch settings:', e.message)
  }
}

async function handleTestTelegram() {
  if (!masterSettings.telegram.botToken || !masterSettings.telegram.chatId) {
    showAlert('Bot Token dan Chat ID Telegram wajib diisi terlebih dahulu.', 'error')
    return
  }
  isTestingTelegram.value = true
  try {
    const res = await settingsService.testTelegram({
      botToken: masterSettings.telegram.botToken,
      chatId: masterSettings.telegram.chatId,
    })
    if (res && res.success) {
      showAlert(res.message || 'Pesan Telegram berhasil terkirim!')
    } else {
      showAlert(`Gagal: ${res?.message || 'Telegram Bot API error.'}`, 'error')
    }
  } catch (err) {
    showAlert(`Gagal: ${err.message || 'Koneksi ke Telegram API gagal.'}`, 'error')
  } finally {
    isTestingTelegram.value = false
  }
}

async function handleTestWhatsApp() {
  if (!masterSettings.whatsapp.targetNumber) {
    showAlert('Nomor WhatsApp tujuan wajib diisi terlebih dahulu.', 'error')
    return
  }
  isTestingWhatsApp.value = true
  try {
    const res = await settingsService.testWhatsApp({
      targetNumber: masterSettings.whatsapp.targetNumber,
      apiUrl: masterSettings.whatsapp.apiUrl,
      apiKey: masterSettings.whatsapp.apiKey,
    })
    if (res && res.success) {
      showAlert(res.message || 'Pesan WhatsApp berhasil terkirim!')
    } else {
      showAlert(`Gagal: ${res?.message || 'Gateway menolak pengiriman.'}`, 'error')
    }
  } catch (err) {
    showAlert(`Gagal: ${err.message || 'Koneksi ke WhatsApp Gateway gagal.'}`, 'error')
  } finally {
    isTestingWhatsApp.value = false
  }
}

async function handleSendDailyDigest() {
  isSendingDailyDigest.value = true
  try {
    const res = await settingsService.sendDailyDigestNow()
    if (res && res.success) {
      showAlert(res.message || 'Ringkasan Harian (Daily Digest) berhasil disiarkan ke Telegram & WhatsApp!')
    } else {
      showAlert(`Gagal: ${res?.message || 'Gagal menyiarkan Daily Digest.'}`, 'error')
    }
  } catch (err) {
    showAlert(`Gagal: ${err.message || 'Koneksi server gagal.'}`, 'error')
  } finally {
    isSendingDailyDigest.value = false
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

onMounted(async () => {
  try {
    await fetchSettings()
  } finally {
    setTimeout(() => {
      isInitialLoading.value = false
    }, 350)
  }
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
  transition: all 0.2s ease;
}

select.modern-input {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px 16px;
  padding-right: 42px;
  cursor: pointer;
}

.modern-input:focus {
  border-color: #0D631B;
  outline: 2px solid rgba(13, 99, 27, 0.2) !important;
  outline-offset: 1px;
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
  .form-grid-2 { grid-template-columns: 1fr; }
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

.fade-admin-params-enter-active,
.fade-admin-params-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-admin-params-enter-from {
  opacity: 0;
  transform: translateY(6px);
}
.fade-admin-params-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* Notification Config Cards */
.notif-config-card {
  padding: 18px 20px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: all 0.2s ease;
}

.notif-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notif-badge {
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 3px 8px;
  border-radius: 6px;
}

.badge-telegram {
  background: rgba(0, 93, 183, 0.12);
  color: #005DB7;
}

.badge-whatsapp {
  background: rgba(13, 99, 27, 0.12);
  color: #0D631B;
}

:global(.dark-theme) .badge-telegram {
  background: rgba(56, 189, 248, 0.2);
  color: #38BDF8;
}

:global(.dark-theme) .badge-whatsapp {
  background: rgba(16, 185, 129, 0.2);
  color: #34D399;
}

.switch-small {
  position: relative;
  display: inline-block;
  width: 36px;
  height: 20px;
}

.switch-small input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider-small {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #CBD5E1;
  transition: .25s;
}

.slider-small.round {
  border-radius: 20px;
}

.slider-small.round:before {
  border-radius: 50%;
}

.slider-small:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .25s;
}

.switch-small input:checked + .slider-small {
  background-color: #0D631B;
}

.switch-small input:checked + .slider-small:before {
  transform: translateX(16px);
}

.btn-test-channel {
  align-self: flex-start;
  padding: 7px 14px;
  border-radius: 6px;
  background: #0F172A;
  color: #FFFFFF;
  border: none;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-test-channel:hover:not(:disabled) {
  background: #1E293B;
  transform: translateY(-1px);
}

.btn-test-channel:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

:global(.dark-theme) .btn-test-channel {
  background: #334155;
}
</style>
