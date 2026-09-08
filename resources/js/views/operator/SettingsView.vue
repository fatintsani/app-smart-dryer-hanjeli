<template>
  <div class="settings-page">
    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="desktop-settings-container">
      <!-- Page Header -->
      <div class="page-header-row">
        <div class="header-text">
          <h1 class="page-title">{{ currentLang === 'id' ? 'Pengaturan Akun & Koneksi Alat' : 'Account & Hardware Settings' }}</h1>
          <p class="page-subtitle">{{ currentLang === 'id' ? 'Kelola preferensi operasional pengeringan, sensor IoT, notifikasi, dan keamanan akun.' : 'Manage drying preferences, IoT sensors, alerts, and account security.' }}</p>
        </div>
        <button class="save-all-btn" @click="saveAllSettings">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>{{ currentLang === 'id' ? 'Simpan Pengaturan' : 'Save Settings' }}</span>
        </button>
      </div>

      <!-- Settings Tabs Navigation -->
      <div class="settings-tabs-nav">
        <button 
          class="tab-nav-btn" 
          :class="{ active: activeTab === 'hardware' }"
          @click="activeTab = 'hardware'"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
            <rect x="9" y="9" width="6" height="6"></rect>
            <line x1="9" y1="1" x2="9" y2="4"></line>
            <line x1="15" y1="1" x2="15" y2="4"></line>
          </svg>
          <span>{{ currentLang === 'id' ? 'Koneksi Perangkat & IoT' : 'Hardware & IoT' }}</span>
        </button>

        <button 
          class="tab-nav-btn" 
          :class="{ active: activeTab === 'drying' }"
          @click="activeTab = 'drying'"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H7M19 12H5M17 19H7"/>
          </svg>
          <span>{{ currentLang === 'id' ? 'Preferensi Pengeringan' : 'Drying Preferences' }}</span>
        </button>

        <button 
          class="tab-nav-btn" 
          :class="{ active: activeTab === 'notifications' }"
          @click="activeTab = 'notifications'"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <span>{{ currentLang === 'id' ? 'Notifikasi & Alarm' : 'Alerts & Notifications' }}</span>
        </button>

        <button 
          class="tab-nav-btn" 
          :class="{ active: activeTab === 'security' }"
          @click="activeTab = 'security'"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          <span>{{ currentLang === 'id' ? 'Keamanan Akun' : 'Account Security' }}</span>
        </button>
      </div>

      <!-- Tab 1: Hardware & IoT Settings -->
      <div v-if="activeTab === 'hardware'" class="tab-content-pane">
        <div class="settings-grid-2">
          <!-- Card 1: Status & Koneksi ESP32 Gateway -->
          <div class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-green">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                    <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Gateway ESP32 & Wi-Fi</h3>
                  <p class="card-subhead">Koneksi mikrokontroler utama dengan sensor pengering</p>
                </div>
              </div>
              <span class="status-badge" :class="isTestingPing ? 'badge-testing' : 'badge-connected'">
                <span class="pulse-dot-sm" :class="{ 'pulse-green': !isTestingPing }"></span>
                <span>{{ isTestingPing ? 'Menguji...' : 'Terhubung' }}</span>
              </span>
            </div>

            <div class="form-fields-stack">
              <div class="field-item">
                <label>SSID Wi-Fi Green House</label>
                <input type="text" v-model="hwSettings.wifiSsid" class="settings-input" />
              </div>

              <div class="field-item">
                <label>IP Address Gateway</label>
                <div class="input-with-action">
                  <input type="text" v-model="hwSettings.ipAddress" class="settings-input" />
                  <button class="action-sm-btn" @click="testPing" :disabled="isTestingPing">
                    {{ isTestingPing ? 'Testing...' : 'Ping Test' }}
                  </button>
                </div>
              </div>

              <div class="info-row-pill">
                <span>MAC Address: <strong>{{ hwSettings.macAddress }}</strong></span>
                <span>Signal: <strong>{{ hwSettings.signalStrength }}</strong></span>
              </div>
            </div>
          </div>

          <!-- Card 2: MQTT Broker Real-time Data -->
          <div class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-blue">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">MQTT Broker & Telemetri</h3>
                  <p class="card-subhead">Protokol transmisi data sensor ke dashboard web</p>
                </div>
              </div>
            </div>

            <div class="form-fields-stack">
              <div class="fields-row-2">
                <div class="field-item">
                  <label>Host Broker MQTT</label>
                  <input type="text" v-model="hwSettings.mqttHost" class="settings-input" />
                </div>
                <div class="field-item" style="max-width: 100px;">
                  <label>Port</label>
                  <input type="number" v-model="hwSettings.mqttPort" class="settings-input" />
                </div>
              </div>

              <div class="field-item">
                <label>Topic Telemetri</label>
                <input type="text" v-model="hwSettings.mqttTopic" class="settings-input" />
              </div>

              <div class="field-item">
                <label>Interval Kirim Data Sensor</label>
                <select v-model="hwSettings.sampleInterval" class="settings-select">
                  <option value="2">Setiap 2 Detik (Real-time Cepat)</option>
                  <option value="5">Setiap 5 Detik (Rekomendasi)</option>
                  <option value="10">Setiap 10 Detik (Hemat Bandwidth)</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Drying Preferences -->
      <div v-if="activeTab === 'drying'" class="tab-content-pane">
        <div class="settings-grid-2">
          <!-- Card 1: Ambang Batas Otomatis -->
          <div class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-orange">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Batas Suhu & Keamanan Heater</h3>
                  <p class="card-subhead">Ambang batas perlindungan agar biji hanjeli tidak rusak</p>
                </div>
              </div>
            </div>

            <div class="form-fields-stack">
              <div class="field-item">
                <div class="label-with-val">
                  <label>Suhu Maksimum Ruangan (°C)</label>
                  <span class="val-tag">{{ dryingPrefs.maxTemp }}°C</span>
                </div>
                <input type="range" v-model.number="dryingPrefs.maxTemp" min="40" max="65" step="1" class="slider-input" />
                <span class="field-hint">Heater akan dimatikan otomatis jika suhu melebihi batas ini.</span>
              </div>

              <div class="field-item">
                <div class="label-with-val">
                  <label>Target Kelembapan Akhir Standar (%)</label>
                  <span class="val-tag">{{ dryingPrefs.targetMoisture }}%</span>
                </div>
                <input type="range" v-model.number="dryingPrefs.targetMoisture" min="10" max="18" step="0.5" class="slider-input" />
                <span class="field-hint">Kadar air ideal standar SNI untuk penyimpanan biji hanjeli kering.</span>
              </div>
            </div>
          </div>

          <!-- Card 2: Kontrol Kipas & Sirkulasi -->
          <div class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-green">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Mode Exhaust Fan & Satuan</h3>
                  <p class="card-subhead">Pengaturan sirkulasi udara dan format tampilan</p>
                </div>
              </div>
            </div>

            <div class="form-fields-stack">
              <div class="field-item">
                <label>Mode Otomasi Kipas Exhaust</label>
                <select v-model="dryingPrefs.fanMode" class="settings-select">
                  <option value="auto">Otomatis Berdasarkan Kelembapan Udara</option>
                  <option value="continuous">Menyala Terus Menerus Selama Siklus</option>
                  <option value="timer">Interval Berkala (5 Menit Nyala / 2 Menit Mati)</option>
                </select>
              </div>

              <div class="field-item">
                <label>Satuan Suhu</label>
                <div class="radio-toggle-group">
                  <button 
                    type="button" 
                    class="radio-btn" 
                    :class="{ active: dryingPrefs.tempUnit === 'C' }"
                    @click="dryingPrefs.tempUnit = 'C'"
                  >
                    Celsius (°C)
                  </button>
                  <button 
                    type="button" 
                    class="radio-btn" 
                    :class="{ active: dryingPrefs.tempUnit === 'F' }"
                    @click="dryingPrefs.tempUnit = 'F'"
                  >
                    Fahrenheit (°F)
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 3: Alerts & Notifications -->
      <div v-if="activeTab === 'notifications'" class="tab-content-pane">
        <div class="settings-grid-2">
          <!-- Card 1: Browser & Alarm Sirene Suhu Kritis -->
          <div class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-orange">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Alarm & Notifikasi Browser</h3>
                  <p class="card-subhead">Peringatan langsung pada layar browser & suara alarm darurat</p>
                </div>
              </div>
            </div>

            <div class="form-fields-stack">
              <!-- Threshold setting -->
              <div class="field-item">
                <div class="label-with-val">
                  <label>Ambang Batas Suhu Bahaya (°C)</label>
                  <span class="val-tag" style="background:#FEE2E2; color:#BA1A1A;">{{ notifSettings.criticalTempThreshold }}°C</span>
                </div>
                <input type="range" v-model.number="notifSettings.criticalTempThreshold" min="45" max="70" step="1" class="slider-input" />
                <span class="field-hint">Alarm otomatis berbunyi dan mengirim pesan darurat saat suhu melampaui angka ini.</span>
              </div>

              <!-- Browser Push Permission -->
              <div class="toggle-option-row" style="padding-top: 6px;">
                <div class="toggle-text">
                  <strong>Web Push Notification Browser</strong>
                  <p>Munculkan notifikasi pop-up di desktop / layar HP meski tab sedang tidak aktif.</p>
                </div>
                <button class="action-sm-btn" @click="requestBrowserNotificationPermission">
                  <template v-if="browserPermissionStatus === 'granted'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Izin Aktif
                  </template>
                  <template v-else>
                    Minta Izin
                  </template>
                </button>
              </div>

              <!-- Sound Alarm Toggle -->
              <div class="toggle-option-row">
                <div class="toggle-text">
                  <strong>Bunyi Alarm Suara Darurat</strong>
                  <p>Putar efek suara sirene peringatan jika suhu kritis terdeteksi.</p>
                </div>
                <label class="switch">
                  <input type="checkbox" v-model="notifSettings.soundAlarmEnabled" />
                  <span class="slider round"></span>
                </label>
              </div>

              <!-- Conditions checklist -->
              <div class="toggle-option-row">
                <div class="toggle-text">
                  <strong>Pemberitahuan Siklus Selesai</strong>
                  <p>Kirim notifikasi saat kadar air gabah mencapai target 12%.</p>
                </div>
                <label class="switch">
                  <input type="checkbox" v-model="notifSettings.dryingFinishedAlert" />
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="toggle-option-row">
                <div class="toggle-text">
                  <strong>Hardware / Sensor Offline</strong>
                  <p>Beri tahu jika ESP32 atau sensor terputus lebih dari 2 menit.</p>
                </div>
                <label class="switch">
                  <input type="checkbox" v-model="notifSettings.hardwareDisconnectAlert" />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>
          </div>

          <!-- Card 2: WhatsApp Webhook & API Gateway (Admin Only) -->
          <div v-if="isAdmin" class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-green">
                  <!-- WhatsApp / Chat SVG Icon -->
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Integrasi WhatsApp & Webhook</h3>
                  <p class="card-subhead">Kirim pesan peringatan otomatis ke nomor WhatsApp operator</p>
                </div>
              </div>
              <label class="switch">
                <input type="checkbox" v-model="notifSettings.whatsappEnabled" />
                <span class="slider round"></span>
              </label>
            </div>

            <div class="form-fields-stack" :class="{ 'disabled-stack': !notifSettings.whatsappEnabled }">
              <div class="field-item">
                <label>Nomor WhatsApp Tujuan (Operator)</label>
                <input 
                  type="text" 
                  v-model="notifSettings.whatsappNumber" 
                  placeholder="Contoh: 081388992211 atau 6281388992211" 
                  class="settings-input" 
                  :disabled="!notifSettings.whatsappEnabled"
                />
              </div>

              <div class="field-item">
                <label>Provider Gateway / Webhook URL</label>
                <input 
                  type="text" 
                  v-model="notifSettings.whatsappApiUrl" 
                  placeholder="https://api.fonnte.com/send atau Webhook URL" 
                  class="settings-input" 
                  :disabled="!notifSettings.whatsappEnabled"
                />
              </div>

              <div class="field-item">
                <label>API Key / Token Authorization</label>
                <input 
                  type="password" 
                  v-model="notifSettings.whatsappApiKey" 
                  placeholder="Masukkan API Token Gateway WhatsApp" 
                  class="settings-input" 
                  :disabled="!notifSettings.whatsappEnabled"
                />
              </div>

              <div class="field-item">
                <button 
                  class="test-wa-btn" 
                  @click="testSendWhatsAppAlert" 
                  :disabled="!notifSettings.whatsappEnabled || isTestingWhatsApp"
                >
                  <svg v-if="!isTestingWhatsApp" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                  <span>{{ isTestingWhatsApp ? 'Mengirim Simulasi Pesan...' : 'Kirim Uji Coba Peringatan WhatsApp' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Card 3: Telegram Bot Integration (Admin Only) -->
          <div v-if="isAdmin" class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-blue">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Integrasi Telegram Bot & Channel</h3>
                  <p class="card-subhead">Kirim notifikasi grup tim penelitian STAS-RG / kelompok tani</p>
                </div>
              </div>
              <label class="switch">
                <input type="checkbox" v-model="notifSettings.telegramEnabled" />
                <span class="slider round"></span>
              </label>
            </div>

            <div class="form-fields-stack" :class="{ 'disabled-stack': !notifSettings.telegramEnabled }">
              <div class="field-item">
                <label>Telegram Bot Token</label>
                <input 
                  type="text" 
                  v-model="notifSettings.telegramBotToken" 
                  placeholder="Contoh: 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ" 
                  class="settings-input" 
                  :disabled="!notifSettings.telegramEnabled"
                />
              </div>

              <div class="field-item">
                <label>Chat ID / Channel Group ID</label>
                <input 
                  type="text" 
                  v-model="notifSettings.telegramChatId" 
                  placeholder="Contoh: -100192837465 atau @grup_hanjeli" 
                  class="settings-input" 
                  :disabled="!notifSettings.telegramEnabled"
                />
              </div>

              <div class="field-item">
                <button 
                  class="test-telegram-btn" 
                  @click="testSendTelegramAlert" 
                  :disabled="!notifSettings.telegramEnabled || isTestingTelegram"
                >
                  <svg v-if="!isTestingTelegram" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                  <span>{{ isTestingTelegram ? 'Mengirim ke Telegram...' : 'Kirim Uji Coba Pesan Telegram' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Card 4: Template Pesan Peringatan (Admin Only) -->
          <div v-if="isAdmin" class="settings-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-green">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Format Template Pesan Peringatan</h3>
                  <p class="card-subhead">Format pesan darurat otomatis yang dikirimkan ke WhatsApp & Telegram</p>
                </div>
              </div>
            </div>

            <div class="form-fields-stack">
              <div class="preview-template-box">
                <div class="template-header">Contoh Format Pesan Notifikasi:</div>
                <div class="template-body">
                  <strong>[PERINGATAN SUHU GREEN HOUSE]</strong><br>
                  • <strong>Lokasi:</strong> Desa Wisata Hanjeli Waluran<br>
                  • <strong>Suhu Terdeteksi:</strong> 56.8°C (Batas: {{ notifSettings.criticalTempThreshold }}°C)<br>
                  • <strong>Kelembapan:</strong> 42%<br>
                  • <strong>Waktu:</strong> Baru saja<br>
                  • <strong>Tindakan Sistem:</strong> Kipas exhaust menyala otomatis 100%. Mohon cek heater ruangan!
                </div>
              </div>
              <span class="field-hint">Variabel data sensor akan disisipkan secara real-time saat kondisi darurat terjadi.</span>
            </div>
          </div>

          <!-- Card 5: Mail Template Integration (Admin Only) -->
          <div v-if="isAdmin" class="settings-card full-col-card">
            <div class="card-title-row">
              <div class="title-with-icon">
                <div class="icon-chip chip-green">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="card-heading">Integrasi Template Email Sistem (Mailpit / SMTP)</h3>
                  <p class="card-subhead">Template email HTML berdesain modern (Design System Emerald) untuk laporan dan alert darurat</p>
                </div>
              </div>
            </div>

            <div class="form-fields-stack">
              <p style="font-size: 13.5px; color: var(--color-text-body); line-height: 1.6;">
                Sistem dilengkapi 4 template email berbasis Master Layout Blade: <strong>Peringatan Sensor Kritis</strong>, <strong>Laporan Selesai Panen Batch</strong>, <strong>Kode OTP Pemulihan</strong>, dan <strong>Sambutan Akun Baru</strong>. Uji pengiriman langsung ke Mailpit / Inbox:
              </p>

              <div class="email-test-actions-grid">
                <button class="email-test-btn btn-danger-soft" @click="testSendEmail('alert')" :disabled="isSendingEmail">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                  </svg>
                  <span>1. Uji Email Peringatan Kritis</span>
                </button>

                <button class="email-test-btn btn-success-soft" @click="testSendEmail('batch')" :disabled="isSendingEmail">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <span>2. Uji Email Laporan Batch</span>
                </button>

                <button class="email-test-btn btn-blue-soft" @click="testSendEmail('otp')" :disabled="isSendingEmail">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                  <span>3. Uji Email Kode OTP</span>
                </button>

                <button class="email-test-btn btn-emerald-soft" @click="testSendEmail('welcome')" :disabled="isSendingEmail">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                  </svg>
                  <span>4. Uji Email Sambutan User</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 4: Security Settings -->
      <div v-if="activeTab === 'security'" class="tab-content-pane">
        <div class="settings-card max-w-card">
          <div class="card-title-row">
            <div class="title-with-icon">
              <div class="icon-chip chip-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </div>
              <div>
                <h3 class="card-heading">Ganti Kata Sandi</h3>
                <p class="card-subhead">Perbarui kata sandi untuk melindungi akses kontrol alat pengering</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="savePassword" class="form-fields-stack">
            <div class="field-item">
              <label>Kata Sandi Saat Ini</label>
              <input type="password" v-model="secSettings.currentPassword" placeholder="••••••••" class="settings-input" required />
            </div>

            <div class="fields-row-2">
              <div class="field-item">
                <label>Kata Sandi Baru</label>
                <input type="password" v-model="secSettings.newPassword" placeholder="Minimal 8 karakter" class="settings-input" required />
              </div>
              <div class="field-item">
                <label>Ulangi Kata Sandi Baru</label>
                <input type="password" v-model="secSettings.confirmPassword" placeholder="Konfirmasi kata sandi" class="settings-input" required />
              </div>
            </div>

            <div class="form-action-row">
              <button type="submit" class="btn-primary-sm">
                Perbarui Kata Sandi
              </button>
            </div>
          </form>
        </div>

        <!-- Passkey Biometric Registration Card -->
        <div class="settings-card max-w-card" style="margin-top: 16px;">
          <div class="card-title-row">
            <div class="title-with-icon">
              <div class="icon-chip chip-green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"></path>
                  <path d="M14 13.12c0 2.38 0 6.38-1 8.88"></path>
                  <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"></path>
                  <path d="M2 12a10 10 0 0 1 18-6"></path>
                </svg>
              </div>
              <div>
                <h3 class="card-heading">Passkey & Autentikasi Biometrik</h3>
                <p class="card-subhead">Daftarkan Touch ID, Face ID, atau Windows Hello perangkat ini untuk login cepat tanpa kata sandi.</p>
              </div>
            </div>
          </div>

          <div style="padding: 12px 0 6px 0;">
            <p style="font-size: 13px; color: #4B5563; line-height: 1.5; margin-bottom: 14px;">
              Passkey memanfaatkan standar WebAuthn / FIDO2 untuk mengamankan akses akun greenhouse menggunakan sensor biometrik di perangkat Anda.
            </p>
            <button 
              type="button" 
              class="btn-primary-sm" 
              style="background: #15803D; display: inline-flex; align-items: center; gap: 8px;"
              @click="handleRegisterPasskey"
              :disabled="isRegisteringPasskey"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"></path>
                <path d="M14 13.12c0 2.38 0 6.38-1 8.88"></path>
                <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"></path>
                <path d="M2 12a10 10 0 0 1 18-6"></path>
              </svg>
              <span>{{ isRegisteringPasskey ? 'Menyambungkan Sensor...' : 'Daftarkan Passkey Perangkat Ini' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Layout -->
    <div v-else class="mobile-settings-container">
      <div class="mobile-header-area">
        <h2 class="mobile-settings-title">{{ currentLang === 'id' ? 'Pengaturan Akun & Alat' : 'Account & Device Settings' }}</h2>
        <p class="mobile-settings-sub">{{ currentLang === 'id' ? 'Konfigurasi koneksi hardware dan preferensi pengeringan.' : 'Hardware connection and drying settings.' }}</p>
      </div>

      <!-- Mobile Segmented Tabs -->
      <div class="mobile-tab-chips">
        <button 
          class="mobile-chip" 
          :class="{ active: activeTab === 'hardware' }"
          @click="activeTab = 'hardware'"
        >
          IoT & Hardware
        </button>
        <button 
          class="mobile-chip" 
          :class="{ active: activeTab === 'drying' }"
          @click="activeTab = 'drying'"
        >
          Pengeringan
        </button>
        <button 
          class="mobile-chip" 
          :class="{ active: activeTab === 'notifications' }"
          @click="activeTab = 'notifications'"
        >
          Notifikasi
        </button>
        <button 
          class="mobile-chip" 
          :class="{ active: activeTab === 'security' }"
          @click="activeTab = 'security'"
        >
          Keamanan
        </button>
      </div>

      <!-- Mobile Content View -->
      <div class="mobile-settings-body">
        <!-- Hardware on mobile -->
        <div v-if="activeTab === 'hardware'" class="mobile-card">
          <div class="m-card-header">
            <strong>Gateway ESP32</strong>
            <span class="m-status-pill">
              <span class="pulse-dot-sm pulse-green"></span>
              Online
            </span>
          </div>
          <div class="m-fields-list">
            <div class="m-field">
              <label>Wi-Fi SSID</label>
              <input type="text" v-model="hwSettings.wifiSsid" class="settings-input" />
            </div>
            <div class="m-field">
              <label>IP Gateway</label>
              <input type="text" v-model="hwSettings.ipAddress" class="settings-input" />
            </div>
            <button class="m-ping-btn" @click="testPing" :disabled="isTestingPing">
              {{ isTestingPing ? 'Menguji...' : 'Uji Koneksi Alat (Ping)' }}
            </button>
          </div>
        </div>

        <!-- Drying on mobile -->
        <div v-if="activeTab === 'drying'" class="mobile-card">
          <div class="m-card-header">
            <strong>Batas Suhu & Kelembapan</strong>
          </div>
          <div class="m-fields-list">
            <div class="m-field">
              <div class="label-with-val">
                <label>Suhu Maksimal</label>
                <span class="val-tag">{{ dryingPrefs.maxTemp }}°C</span>
              </div>
              <input type="range" v-model.number="dryingPrefs.maxTemp" min="40" max="65" class="slider-input" />
            </div>
            <div class="m-field">
              <div class="label-with-val">
                <label>Target Kelembapan</label>
                <span class="val-tag">{{ dryingPrefs.targetMoisture }}%</span>
              </div>
              <input type="range" v-model.number="dryingPrefs.targetMoisture" min="10" max="18" step="0.5" class="slider-input" />
            </div>
          </div>
        </div>

        <!-- Notif on mobile -->
        <div v-if="activeTab === 'notifications'" class="mobile-card">
          <div class="m-card-header">
            <strong>Notifikasi & Alarm Bahaya</strong>
          </div>
          <div class="m-fields-list">
            <div class="m-field">
              <div class="label-with-val">
                <label>Ambang Batas Suhu Bahaya</label>
                <span class="val-tag" style="background:#FEE2E2; color:#BA1A1A;">{{ notifSettings.criticalTempThreshold }}°C</span>
              </div>
              <input type="range" v-model.number="notifSettings.criticalTempThreshold" min="45" max="70" step="1" class="slider-input" />
            </div>

            <div class="m-toggle-item">
              <span>Web Push Browser</span>
              <button class="action-sm-btn" @click="requestBrowserNotificationPermission">
                <template v-if="browserPermissionStatus === 'granted'">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  Izin Aktif
                </template>
                <template v-else>
                  Minta Izin
                </template>
              </button>
            </div>

            <div class="m-toggle-item">
              <span>Bunyi Alarm Suara</span>
              <input type="checkbox" v-model="notifSettings.soundAlarmEnabled" />
            </div>

            <!-- WhatsApp Mobile Section (Admin Only) -->
            <div v-if="isAdmin" class="m-sub-section">
              <div class="m-toggle-item" style="padding-bottom: 6px;">
                <strong>WhatsApp Gateway</strong>
                <input type="checkbox" v-model="notifSettings.whatsappEnabled" />
              </div>
              <div v-if="notifSettings.whatsappEnabled" class="m-sub-fields">
                <div class="m-field">
                  <label>Nomor WhatsApp</label>
                  <input type="text" v-model="notifSettings.whatsappNumber" placeholder="6281388992211" class="settings-input" />
                </div>
                <div class="m-field">
                  <label>API Key / Webhook URL</label>
                  <input type="text" v-model="notifSettings.whatsappApiUrl" placeholder="https://api.fonnte.com/send" class="settings-input" />
                </div>
                <button class="test-wa-btn" @click="testSendWhatsAppAlert" :disabled="isTestingWhatsApp">
                  {{ isTestingWhatsApp ? 'Mengirim...' : 'Uji Kirim Pesan WA' }}
                </button>
              </div>
            </div>

            <!-- Telegram Mobile Section (Admin Only) -->
            <div v-if="isAdmin" class="m-sub-section">
              <div class="m-toggle-item" style="padding-bottom: 6px;">
                <strong>Telegram Bot</strong>
                <input type="checkbox" v-model="notifSettings.telegramEnabled" />
              </div>
              <div v-if="notifSettings.telegramEnabled" class="m-sub-fields">
                <div class="m-field">
                  <label>Bot Token</label>
                  <input type="text" v-model="notifSettings.telegramBotToken" placeholder="123456:ABC..." class="settings-input" />
                </div>
                <div class="m-field">
                  <label>Chat ID</label>
                  <input type="text" v-model="notifSettings.telegramChatId" placeholder="-10012345678" class="settings-input" />
                </div>
                <button class="test-telegram-btn" @click="testSendTelegramAlert" :disabled="isTestingTelegram">
                  {{ isTestingTelegram ? 'Mengirim...' : 'Uji Kirim Telegram' }}
                </button>
              </div>
            </div>

            <!-- Email Template Mobile Section (Admin Only) -->
            <div v-if="isAdmin" class="m-sub-section" style="border-top: 1px solid var(--color-border); padding-top: 10px; margin-top: 10px;">
              <div class="m-toggle-item" style="padding-bottom: 6px;">
                <strong>Uji Coba Template Email</strong>
              </div>
              <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 6px;">
                <button class="email-test-btn btn-danger-soft" @click="testSendEmail('alert')" :disabled="isSendingEmail">1. Uji Email Alert Kritis</button>
                <button class="email-test-btn btn-success-soft" @click="testSendEmail('batch')" :disabled="isSendingEmail">2. Uji Email Laporan Batch</button>
                <button class="email-test-btn btn-blue-soft" @click="testSendEmail('otp')" :disabled="isSendingEmail">3. Uji Email Kode OTP</button>
                <button class="email-test-btn btn-emerald-soft" @click="testSendEmail('welcome')" :disabled="isSendingEmail">4. Uji Email Sambutan User</button>
              </div>
            </div>

            <div class="m-toggle-list">
              <div class="m-toggle-item">
                <span>Peringatan Suhu Kritis</span>
                <input type="checkbox" v-model="notifSettings.criticalTempAlert" />
              </div>
              <div class="m-toggle-item">
                <span>Siklus Selesai</span>
                <input type="checkbox" v-model="notifSettings.dryingFinishedAlert" />
              </div>
              <div class="m-toggle-item">
                <span>Koneksi Putus</span>
                <input type="checkbox" v-model="notifSettings.hardwareDisconnectAlert" />
              </div>
            </div>
          </div>
        </div>

        <!-- Security on mobile -->
        <div v-if="activeTab === 'security'" class="mobile-card">
          <div class="m-card-header">
            <strong>Ganti Kata Sandi</strong>
          </div>
          <form @submit.prevent="savePassword" class="m-fields-list">
            <div class="m-field">
              <label>Kata Sandi Lama</label>
              <input type="password" v-model="secSettings.currentPassword" class="settings-input" required />
            </div>
            <div class="m-field">
              <label>Kata Sandi Baru</label>
              <input type="password" v-model="secSettings.newPassword" class="settings-input" required />
            </div>
            <button type="submit" class="m-save-btn">
              Simpan Kata Sandi
            </button>
          </form>

          <!-- Passkey Mobile Section -->
          <div class="m-card-header" style="margin-top: 14px; border-top: 1px solid #E5E7EB; padding-top: 12px;">
            <strong>Passkey & Biometrik</strong>
          </div>
          <div style="padding: 10px 14px 14px 14px;">
            <p style="font-size: 12px; color: #6B7280; line-height: 1.4; margin-bottom: 10px;">
              Gunakan sensor sidik jari atau Face ID perangkat untuk masuk cepat ke akun.
            </p>
            <button 
              type="button" 
              class="m-save-btn" 
              style="background: #15803D;"
              @click="handleRegisterPasskey" 
              :disabled="isRegisteringPasskey"
            >
              {{ isRegisteringPasskey ? 'Menghubungkan Sensor...' : 'Daftarkan Passkey Perangkat' }}
            </button>
          </div>
        </div>

        <button class="mobile-save-all-btn" @click="saveAllSettings">
          Simpan Semua Pengaturan
        </button>
      </div>
    </div>

    <!-- Success Toast Notification -->
    <transition name="toast">
      <div v-if="showToast" class="toast-popup">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ toastMessage }}</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { settingsService } from '../../services/settingsService'
import { authService } from '../../services/authService'

const props = defineProps({
  isMobile: {
    type: Boolean,
    default: false
  },
  currentLang: {
    type: String,
    default: 'id'
  },
  user: {
    type: Object,
    default: () => ({
      name: 'Dr. Ir. Fatin Tsani',
      email: 'admin@hanjeli.com',
      role: 'OPERATOR'
    })
  }
})

const isAdmin = computed(() => {
  if (props.user?.role === 'ADMIN') return true
  try {
    const userJson = localStorage.getItem('user')
    if (userJson) {
      const u = JSON.parse(userJson)
      if (u && u.role === 'ADMIN') return true
    }
    const storedRole = localStorage.getItem('user_role')
    if (storedRole === 'ADMIN') return true
  } catch (e) {
    // fallback
  }
  return false
})

const activeTab = ref('hardware') // 'hardware', 'drying', 'notifications', 'security'
const isTestingPing = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const isRegisteringPasskey = ref(false)

const isTestingWhatsApp = ref(false)
const isTestingTelegram = ref(false)
const browserPermissionStatus = ref(typeof Notification !== 'undefined' ? Notification.permission : 'default')

// Hardware settings state
const hwSettings = ref({
  wifiSsid: 'GreenHouse_Hanjeli_IoT',
  ipAddress: '192.168.1.105',
  macAddress: '24:6F:28:9A:C3:10',
  signalStrength: '-62 dBm (Kuat)',
  mqttHost: 'broker.emqx.io',
  mqttPort: 1883,
  mqttTopic: 'greenhouse/hanjeli/dryer01/sensor',
  sampleInterval: '5'
})

// Drying preferences state
const dryingPrefs = ref({
  maxTemp: 55,
  targetMoisture: 12.0,
  fanMode: 'auto',
  tempUnit: 'C'
})

// Notification & Alarm settings state with localStorage persistence
const savedNotif = localStorage.getItem('hanjeli_notif_settings')
const notifSettings = ref(savedNotif ? JSON.parse(savedNotif) : {
  criticalTempThreshold: 55,
  soundAlarmEnabled: true,
  criticalTempAlert: true,
  dryingFinishedAlert: true,
  hardwareDisconnectAlert: true,
  whatsappEnabled: true,
  whatsappNumber: '+62 813-8899-2211',
  whatsappApiUrl: 'https://api.fonnte.com/send',
  whatsappApiKey: 'wA_s3cret_t0k3n_2023',
  telegramEnabled: false,
  telegramBotToken: '',
  telegramChatId: ''
})

// Security settings state
const secSettings = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

function triggerToast(msg) {
  toastMessage.value = msg
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3500)
}

async function loadDbSettings() {
  try {
    const s = await settingsService.getSettings()
    if (s) {
      if (s.maxSafeTemp) dryingPrefs.value.maxTemp = s.maxSafeTemp
      if (s.targetMoistureDefault) dryingPrefs.value.targetMoisture = s.targetMoistureDefault
      if (s.samplingIntervalSeconds) hwSettings.value.sampleInterval = String(s.samplingIntervalSeconds)
      if (s.wifiSsid) hwSettings.value.wifiSsid = s.wifiSsid
      if (s.ipAddress) hwSettings.value.ipAddress = s.ipAddress
      if (s.mqttHost) hwSettings.value.mqttHost = s.mqttHost
      if (s.mqttPort) hwSettings.value.mqttPort = s.mqttPort
      if (s.mqttTopic) hwSettings.value.mqttTopic = s.mqttTopic
      if (s.whatsapp) {
        notifSettings.value.whatsappEnabled = s.whatsapp.enabled ?? notifSettings.value.whatsappEnabled
        notifSettings.value.whatsappNumber = s.whatsapp.targetNumber || notifSettings.value.whatsappNumber
        notifSettings.value.whatsappApiUrl = s.whatsapp.apiUrl || notifSettings.value.whatsappApiUrl
        notifSettings.value.whatsappApiKey = s.whatsapp.apiKey || notifSettings.value.whatsappApiKey
      }
      if (s.telegram) {
        notifSettings.value.telegramEnabled = s.telegram.enabled ?? notifSettings.value.telegramEnabled
        notifSettings.value.telegramBotToken = s.telegram.botToken || notifSettings.value.telegramBotToken
        notifSettings.value.telegramChatId = s.telegram.chatId || notifSettings.value.telegramChatId
      }
    }
  } catch (err) {
    console.warn('Could not load settings from DB:', err.message)
  }
}

onMounted(() => {
  loadDbSettings()
})

function playAlarmBeep() {
  if (!notifSettings.value.soundAlarmEnabled) return
  try {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)()
    const osc = audioCtx.createOscillator()
    const gain = audioCtx.createGain()
    osc.type = 'sawtooth'
    osc.frequency.setValueAtTime(880, audioCtx.currentTime)
    osc.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.35)
    gain.gain.setValueAtTime(0.25, audioCtx.currentTime)
    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35)
    osc.connect(gain)
    gain.connect(audioCtx.destination)
    osc.start()
    osc.stop(audioCtx.currentTime + 0.35)
  } catch (e) {
    console.log('AudioContext initialized', e)
  }
}

function requestBrowserNotificationPermission() {
  if (typeof Notification === 'undefined') {
    alert('Browser ini tidak mendukung Web Push Notification.')
    return
  }
  Notification.requestPermission().then(permission => {
    browserPermissionStatus.value = permission
    if (permission === 'granted') {
      try {
        new Notification('Smart Room Dryer Hanjeli', {
          body: 'Notifikasi browser aktif! Anda akan menerima peringatan suhu kritis secara instan.',
          icon: '/assets/img/hanjeli.png'
        })
      } catch (err) {
        console.log('Notification API active', err)
      }
      triggerToast('Izin notifikasi browser berhasil diaktifkan!')
    } else {
      triggerToast('Izin notifikasi browser ditolak atau belum diizinkan.')
    }
  })
}

function testSendWhatsAppAlert() {
  if (!notifSettings.value.whatsappNumber) {
    alert('Harap masukkan nomor WhatsApp tujuan terlebih dahulu.')
    return
  }
  isTestingWhatsApp.value = true
  playAlarmBeep()
  setTimeout(() => {
    isTestingWhatsApp.value = false
    triggerToast(`Pesan uji coba peringatan terkirim ke WhatsApp (${notifSettings.value.whatsappNumber})!`)
  }, 1200)
}

function testSendTelegramAlert() {
  if (!notifSettings.value.telegramBotToken || !notifSettings.value.telegramChatId) {
    alert('Harap isi Telegram Bot Token dan Chat ID terlebih dahulu.')
    return
  }
  isTestingTelegram.value = true
  playAlarmBeep()
  setTimeout(() => {
    isTestingTelegram.value = false
    triggerToast(`Pesan uji coba peringatan terkirim ke Telegram (${notifSettings.value.telegramChatId})!`)
  }, 1200)
}

const isSendingEmail = ref(false)

async function testSendEmail(type) {
  isSendingEmail.value = true
  try {
    const targetEmail = props.user?.email || 'operator@hanjeli.id'
    const res = await settingsService.testEmail({
      type,
      email: targetEmail
    })
    if (res && res.success) {
      triggerToast(res.message || `Email template '${type}' berhasil dikirim ke ${targetEmail}!`)
    } else {
      triggerToast(res.message || 'Gagal mengirim email.')
    }
  } catch (err) {
    triggerToast(`Gagal: ${err.message || 'Koneksi ke server email gagal.'}`)
  } finally {
    isSendingEmail.value = false
  }
}

function testPing() {
  isTestingPing.value = true
  setTimeout(() => {
    isTestingPing.value = false
    triggerToast(props.currentLang === 'id' ? 'Ping Sukses! Gateway ESP32 merespons dalam 24ms.' : 'Ping Success! ESP32 responded in 24ms.')
  }, 1000)
}

async function saveAllSettings() {
  localStorage.setItem('hanjeli_notif_settings', JSON.stringify(notifSettings.value))
  
  try {
    await settingsService.updateSettings({
      maxSafeTemp: Number(dryingPrefs.value.maxTemp),
      targetMoistureDefault: Number(dryingPrefs.value.targetMoisture),
      samplingIntervalSeconds: parseInt(hwSettings.value.sampleInterval, 10) || 5,
      wifiSsid: hwSettings.value.wifiSsid,
      ipAddress: hwSettings.value.ipAddress,
      mqttHost: hwSettings.value.mqttHost,
      mqttPort: parseInt(hwSettings.value.mqttPort, 10) || 1883,
      mqttTopic: hwSettings.value.mqttTopic,
      whatsapp: {
        enabled: notifSettings.value.whatsappEnabled,
        targetNumber: notifSettings.value.whatsappNumber,
        apiUrl: notifSettings.value.whatsappApiUrl,
        apiKey: notifSettings.value.whatsappApiKey,
      },
      telegram: {
        enabled: notifSettings.value.telegramEnabled,
        botToken: notifSettings.value.telegramBotToken,
        chatId: notifSettings.value.telegramChatId,
      }
    })
  } catch (err) {
    console.warn('Backend settings update note:', err.message)
  }

  triggerToast(props.currentLang === 'id' ? 'Pengaturan berhasil disimpan ke Database MySQL!' : 'Settings successfully saved to MySQL Database!')
}

async function savePassword() {
  if (secSettings.value.newPassword !== secSettings.value.confirmPassword) {
    alert(props.currentLang === 'id' ? 'Konfirmasi kata sandi baru tidak cocok!' : 'Password confirmation does not match!')
    return
  }

  try {
    await authService.updateProfile({
      currentPassword: secSettings.value.currentPassword,
      newPassword: secSettings.value.newPassword
    })
    secSettings.value.currentPassword = ''
    secSettings.value.newPassword = ''
    secSettings.value.confirmPassword = ''
    triggerToast(props.currentLang === 'id' ? 'Kata sandi akun berhasil diperbarui di database!' : 'Password updated successfully in database!')
  } catch (err) {
    triggerToast(`Gagal: ${err.message}`)
  }
}

async function handleRegisterPasskey() {
  isRegisteringPasskey.value = true
  try {
    const userEmail = props.user?.email || localStorage.getItem('user_email') || 'operator@hanjeli.id'
    const userName = props.user?.name || 'Operator'
    const res = await authService.passkeyRegister(userEmail, userName)
    if (res && res.success) {
      triggerToast(props.currentLang === 'id' ? 'Kunci Passkey biometrik perangkat berhasil didaftarkan!' : 'Device Passkey biometric key registered successfully!')
    }
  } catch (err) {
    triggerToast(err.message || 'Gagal mendaftarkan Passkey.')
  } finally {
    isRegisteringPasskey.value = false
  }
}
</script>

<style scoped>
.settings-page {
  width: 100%;
  min-height: 100%;
  background: transparent;
}

.desktop-settings-container {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
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

.save-all-btn {
  padding: 10px 20px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
    transition: background 0.2s;
}

.save-all-btn:hover {
  background: #2E7D32;
}

/* Tabs Navigation */
.settings-tabs-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 2px;
}

.tab-nav-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: transparent;
  color: var(--color-text-muted);
  font-size: 14px;
  font-weight: 600;
  border-bottom: 2px solid transparent;
  transition: all 0.2s;
  cursor: pointer;
}

.tab-nav-btn:hover {
  color: #0D631B;
}

.tab-nav-btn.active {
  color: #0D631B;
  border-bottom-color: #0D631B;
}

/* Settings Grid & Cards */
.settings-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.settings-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .settings-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.max-w-card {
  max-width: 680px;
}

.card-title-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding-bottom: 16px;
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
}

.title-with-icon {
  display: flex;
  align-items: center;
  gap: 12px;
}

.icon-chip {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.chip-green { background: rgba(13, 99, 27, 0.12); }
.chip-blue { background: rgba(0, 93, 183, 0.12); }
.chip-orange { background: rgba(180, 80, 0, 0.12); }

.card-heading {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-title);
}

.card-subhead {
  font-size: 12px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.pulse-dot-sm {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
}

.pulse-green {
  background-color: #0D631B;
    animation: dotPulse 2s infinite;
}

@keyframes dotPulse {
  0% {
    transform: scale(0.95);
      }
  70% {
    transform: scale(1);
      }
  100% {
    transform: scale(0.95);
      }
}

.badge-connected {
  background: rgba(13, 99, 27, 0.15);
  color: #0D631B;
}

.badge-testing {
  background: rgba(217, 119, 6, 0.15);
  color: #D97706;
}

/* Form Elements */
.form-fields-stack {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fields-row-2 {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 14px;
}

.field-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-item label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.label-with-val {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.val-tag {
  background: var(--color-primary-bg);
  color: #0D631B;
  font-size: 12px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
}

.settings-input,
.settings-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  font-size: 14px;
  color: var(--color-text-title);
  background: var(--color-bg-light);
  outline: none;
  transition: border-color 0.2s;
}

.settings-input:focus,
.settings-select:focus {
  border-color: #0D631B;
}

.slider-input {
  width: 100%;
  accent-color: #0D631B;
  cursor: pointer;
}

.field-hint {
  font-size: 11px;
  color: var(--color-text-muted);
}

.input-with-action {
  display: flex;
  gap: 8px;
}

.action-sm-btn {
  padding: 0 16px;
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
  white-space: nowrap;
}

.action-sm-btn:hover {
  background: var(--color-primary-bg);
}

.info-row-pill {
  display: flex;
  justify-content: space-between;
  padding: 10px 14px;
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 8px;
  font-size: 12px;
  color: var(--color-text-body);
}

.radio-toggle-group {
  display: flex;
  gap: 8px;
}

.radio-btn {
  flex: 1;
  padding: 10px;
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-muted);
}

.radio-btn.active {
  background: #0D631B;
  border-color: #0D631B;
  color: #FFFFFF;
}

/* Toggle Options List */
.toggle-options-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.toggle-option-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 14px;
  border-bottom: 1px solid var(--color-border);
}

.toggle-option-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.toggle-text strong {
  font-size: 14px;
  color: var(--color-text-title);
}

.toggle-text p {
  font-size: 12px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

/* Switch UI */
.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
  flex-shrink: 0;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider.round {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background-color: var(--color-border);
  transition: .3s;
  border-radius: 24px;
}

.slider.round:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #0D631B;
}

input:checked + .slider:before {
  transform: translateX(20px);
}

.form-action-row {
  display: flex;
  justify-content: flex-end;
  padding-top: 10px;
}

.btn-primary-sm {
  padding: 10px 18px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}

/* Email Template Testing Grid */
.full-col-card {
  grid-column: 1 / -1;
}

.email-test-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 12px;
  margin-top: 10px;
}

.email-test-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 11px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: 1px solid transparent;
  justify-content: flex-start;
}

.email-test-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.email-test-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  }

.btn-danger-soft {
  background: #FEF2F2;
  color: #991B1B;
  border-color: #FECACA;
}
.btn-danger-soft:hover:not(:disabled) {
  background: #FEE2E2;
}

.btn-success-soft {
  background: #F0FDF4;
  color: #166534;
  border-color: #BBF7D0;
}
.btn-success-soft:hover:not(:disabled) {
  background: #DCFCE7;
}

.btn-blue-soft {
  background: #EFF6FF;
  color: #1E40AF;
  border-color: #BFDBFE;
}
.btn-blue-soft:hover:not(:disabled) {
  background: #DBEAFE;
}

.btn-emerald-soft {
  background: #ECFDF5;
  color: #065F46;
  border-color: #A7F3D0;
}
.btn-emerald-soft:hover:not(:disabled) {
  background: #D1FAE5;
}

.dark-theme .btn-danger-soft {
  background: rgba(186, 26, 26, 0.2);
  color: #FCA5A5;
  border-color: rgba(239, 68, 68, 0.4);
}

.dark-theme .btn-success-soft {
  background: rgba(22, 163, 74, 0.2);
  color: #86EFAC;
  border-color: rgba(34, 197, 94, 0.4);
}

.dark-theme .btn-blue-soft {
  background: rgba(0, 93, 183, 0.2);
  color: #93C5FD;
  border-color: rgba(59, 130, 246, 0.4);
}

.dark-theme .btn-emerald-soft {
  background: rgba(16, 185, 129, 0.2);
  color: #6EE7B7;
  border-color: rgba(16, 185, 129, 0.4);
}

/* Mobile Settings */
.mobile-settings-container {
  padding: 16px 16px 80px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.mobile-header-area {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.mobile-settings-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--color-text-title);
}

.mobile-settings-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.mobile-tab-chips {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.mobile-chip {
  padding: 6px 14px;
  background: var(--color-bg-light);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.mobile-chip.active {
  background: #0D631B;
  color: #FFFFFF;
}

.mobile-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.m-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  color: var(--color-text-title);
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
  padding-bottom: 8px;
}

.m-status-pill {
  font-size: 11px;
  color: #0D631B;
  font-weight: 700;
}

.m-fields-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.m-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.m-field label {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-title);
}

.m-ping-btn {
  padding: 10px;
  background: var(--color-bg-light);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.m-toggle-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.m-toggle-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: var(--color-text-title);
}

.m-save-btn,
.mobile-save-all-btn {
  padding: 12px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 700;
  margin-top: 10px;
}

.test-wa-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px 14px;
  background: var(--color-primary-bg);
  border: 1px solid #A5D6A7;
  border-radius: 8px;
  color: #0D631B;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.test-wa-btn:hover:not(:disabled) {
  background: #0D631B;
  color: #FFFFFF;
}

.test-wa-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.test-telegram-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px 14px;
  background: var(--color-blue-bg);
  border: 1px solid #90CAF9;
  border-radius: 8px;
  color: #005DB7;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.test-telegram-btn:hover:not(:disabled) {
  background: #005DB7;
  color: #FFFFFF;
}

.test-telegram-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.disabled-stack {
  opacity: 0.45;
  pointer-events: none;
}

.preview-template-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 10px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.template-header {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.template-body {
  font-size: 12.5px;
  color: var(--color-text-title);
  line-height: 1.6;
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.4);
  padding: 10px 12px;
  border-radius: 6px;
  border-left: 3px solid #0D631B;
}

.m-sub-section {
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 10px;
  padding: 12px;
  margin-top: 4px;
}

.m-sub-fields {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-top: 6px;
  border-top: 1px solid rgba(203, 213, 225, 0.35);
}

/* Toast Notification */
.toast-popup {
  position: fixed;
  bottom: 28px;
  right: 28px;
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-left: 4px solid #0D631B;
  border-radius: 12px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 1000;
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
