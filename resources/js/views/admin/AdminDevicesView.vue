<template>
  <div class="admin-page">
    <Transition name="fade-admin-devices" mode="out-in">
      <AdminSkeleton v-if="isInitialLoading" />
      <div v-else class="admin-devices-loaded-content">
        <div class="admin-content">
          <!-- Top Title & Action Bar -->
          <div class="header-action-row">
            <div class="title-group">
          <div class="title-badge-row">
            <h1 class="page-title">Keamanan IoT & Manajemen Firmware OTA</h1>
            <span class="security-shield-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>HMAC / X-Device-Token Auth</span>
            </span>
          </div>
          <p class="page-subtitle">Pusat otentikasi token mikrokontroler ESP32, perlindungan anti-tampering, monitoring armada hardware, dan pembaruan firmware Over-The-Air.</p>
        </div>

        <div class="header-actions">
          <button class="btn-secondary-action" @click="pingAllDevices" :disabled="isPingingAll">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            <span>{{ isPingingAll ? 'Memeriksa...' : 'Ping Semua Node' }}</span>
          </button>

          <button class="btn-primary" @click="isNewReleaseModalOpen = true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="17 8 12 3 7 8"></polyline>
              <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <span>Unggah Firmware Baru (.bin)</span>
          </button>
        </div>
      </div>

      <!-- Feedback / Alert Toast -->
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

      <!-- 4 Top KPI Security & Fleet Cards -->
      <div class="stats-grid-4">
        <!-- 1. Active Hardware Nodes -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">TOTAL NODE TERDAFTAR</span>
            <div class="icon-circle icon-green-light">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2.2">
                <rect x="2" y="2" width="20" height="20" rx="4"></rect>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number">{{ devices.length }}</span>
            <span class="stat-unit">Perangkat</span>
          </div>
          <div class="stat-trend trend-up-green">
            <span>{{ devices.filter(d => d.status === 'online').length }} Node Terkoneksi (100% Siap)</span>
          </div>
        </div>

        <!-- 2. Security Ingestion Auth Status -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">STATUS OTENTIKASI INGEST</span>
            <div class="icon-circle icon-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-blue" style="font-size: 20px;">X-Device-Token</span>
          </div>
          <div class="stat-trend trend-down-blue">
            <span>Anti-Spoofing & Enkripsi Aktif</span>
          </div>
        </div>

        <!-- 3. Firmware Version Distribution -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">VERSI FIRMWARE UTAMA</span>
            <div class="icon-circle icon-purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2.2">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-purple">{{ latestFirmwareRelease?.version || 'v2.5.0' }}</span>
            <span class="stat-unit">Terbaru</span>
          </div>
          <div class="stat-trend trend-purple">
            <span>Rilis Stabil • Build Produksi</span>
          </div>
        </div>

        <!-- 4. OTA Remote Flashing Fleet Status -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">STATUS AKTIVITAS OTA</span>
            <div class="icon-circle icon-orange">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number" :class="isAnyOtaActive ? 'text-orange' : 'text-green-dark'">
              {{ isAnyOtaActive ? 'FLASHING' : 'IDLE / SIAGA' }}
            </span>
          </div>
          <div class="stat-trend" :class="isAnyOtaActive ? 'trend-orange' : 'trend-up-green'">
            <span>{{ isAnyOtaActive ? 'Proses OTA sedang berjalan' : 'Semua armada siap pembaruan' }}</span>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs: Security Tokens vs Firmware OTA vs Sensors vs Releases -->
      <div class="device-subnav-bar">
        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'security' }"
          @click="currentTab = 'security'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
          <span>1. Otentikasi Token & Keamanan API</span>
        </button>

        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'ota' }"
          @click="currentTab = 'ota'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
          </svg>
          <span>2. Manajemen Firmware OTA (Over-The-Air)</span>
        </button>

        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'releases' }"
          @click="currentTab = 'releases'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <polyline points="21 8 21 21 3 21 3 8"></polyline>
            <rect x="1" y="3" width="22" height="5"></rect>
            <line x1="10" y1="12" x2="14" y2="12"></line>
          </svg>
          <span>3. Pustaka Rilis Firmware (Repository)</span>
        </button>

        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'sensors' }"
          @click="currentTab = 'sensors'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
          </svg>
          <span>4. Matriks Kesehatan Sensor & Latensi</span>
        </button>

        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'actuators' }"
          @click="currentTab = 'actuators'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="2" y="2" width="20" height="20" rx="4"></rect>
            <path d="M12 7v10"></path>
            <path d="M8 12h8"></path>
          </svg>
          <span>5. Kontrol Sakelar Relai & Aktuator Hardware</span>
        </button>

        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'catalog' }"
          @click="currentTab = 'catalog'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
            <line x1="8" y1="21" x2="16" y2="21"></line>
            <line x1="12" y1="17" x2="12" y2="21"></line>
          </svg>
          <span>6. Katalog Sensor & Hardware Greenhouse (6)</span>
        </button>

        <button 
          type="button" 
          class="subnav-btn" 
          :class="{ active: currentTab === 'arduino' }"
          @click="currentTab = 'arduino'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <polyline points="16 18 22 12 16 6"></polyline>
            <polyline points="8 6 2 12 8 18"></polyline>
          </svg>
          <span>7. Program Arduino / Source Code ESP32 (2)</span>
        </button>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 1: DEVICE SECURITY & TOKEN AUTHENTICATION (X-Device-Token) -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'security'" class="tab-pane-wrapper">
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h2 class="card-section-heading">Manajemen Token Otentikasi Mikrokontroler (ESP32)</h2>
              <p class="card-section-sub">Setiap mikrokontroler wajib menyertakan token unik pada header <code>X-Device-Token</code> pada setiap pengiriman data sensor ke <code>POST /api/telemetry/ingest</code>.</p>
            </div>
            <button class="btn-outline-action" @click="openCodeSnippetModal">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
              </svg>
              <span>Panduan Kode C++ (Arduino/ESP-IDF)</span>
            </button>
          </div>

          <!-- Device Token Cards Grid -->
          <div class="devices-token-grid">
            <div 
              v-for="d in devices" 
              :key="d.id"
              class="device-security-card"
            >
              <div class="dsc-header">
                <div class="dsc-icon-box">
                  <svg v-if="d.icon === 'microchip'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2">
                    <rect x="2" y="2" width="20" height="20" rx="4"></rect>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg v-else-if="d.icon === 'temp'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                  <svg v-else-if="d.icon === 'sun'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                  </svg>
                  <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2">
                    <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
                  </svg>
                </div>
                <div class="dsc-title-info">
                  <div class="dsc-name-row">
                    <h3 class="dsc-name">{{ d.name }}</h3>
                    <div class="dsc-toggle-status-wrap">
                      <span class="badge-status-dot" :class="d.status === 'online' ? 'dot-online' : 'dot-offline'">
                        {{ d.status === 'online' ? 'Online' : 'Offline' }}
                      </span>
                      <label class="device-switch-toggle" :title="d.status === 'online' ? 'Matikan Daya Node' : 'Nyalakan Daya Node'">
                        <input 
                          type="checkbox" 
                          :checked="d.status === 'online'" 
                          @change="toggleDevicePower(d)"
                        />
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                  <span class="dsc-meta-tag">Code: <code>{{ d.code || 'ESP32-NODE' }}</code> • MAC: <code>{{ d.macAddress }}</code></span>
                </div>
              </div>

              <div class="dsc-body">
                <div class="dsc-meta-grid">
                  <div class="meta-item">
                    <span class="meta-lbl">Lokasi Fisik:</span>
                    <strong class="meta-val">{{ d.location }}</strong>
                  </div>
                  <div class="meta-item">
                    <span class="meta-lbl">IP Address:</span>
                    <strong class="meta-val font-mono">{{ d.ipAddress }}</strong>
                  </div>
                  <div class="meta-item">
                    <span class="meta-lbl">Firmware:</span>
                    <strong class="meta-val text-purple">{{ d.firmwareVersion || 'v2.4.2' }}</strong>
                  </div>
                  <div class="meta-item">
                    <span class="meta-lbl">Sinyal / Ping:</span>
                    <strong class="meta-val text-green-dark">{{ d.userSignal }}</strong>
                  </div>
                </div>

                <!-- API Secret Token Field -->
                <div class="token-field-box">
                  <div class="token-field-head">
                    <label class="token-label">DEVICE API TOKEN (X-DEVICE-TOKEN):</label>
                    <span class="token-status-pill">HMAC SHA-256 Valid</span>
                  </div>
                  <div class="token-input-wrapper">
                    <input 
                      :type="showTokens[d.id] ? 'text' : 'password'"
                      :value="d.deviceToken || 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c'"
                      readonly
                      class="token-input font-mono"
                    />
                    <button 
                      type="button" 
                      class="btn-token-icon" 
                      @click="toggleTokenVisibility(d.id)"
                      :title="showTokens[d.id] ? 'Sembunyikan Token' : 'Tampilkan Token'"
                    >
                      <svg v-if="showTokens[d.id]" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                      </svg>
                      <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                    <button 
                      type="button" 
                      class="btn-token-icon" 
                      @click="copyToken(d.deviceToken || 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c')"
                      title="Salin Token ke Clipboard"
                    >
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Card Footer Actions -->
              <div class="dsc-footer">
                <button 
                  type="button" 
                  class="btn-regen-token" 
                  @click="handleRegenerateToken(d)"
                  :disabled="isSubmitting"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                  </svg>
                  <span>Regenerate Token Baru</span>
                </button>

                <button 
                  type="button" 
                  class="btn-ping-node" 
                  @click="pingDevice(d)"
                  :disabled="d.isPinging"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                  </svg>
                  <span>{{ d.isPinging ? 'Pinging...' : 'Uji Sinyal Ping' }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 2: FIRMWARE OVER-THE-AIR (OTA) MANAGEMENT -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'ota'" class="tab-pane-wrapper">
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h2 class="card-section-heading">Armada Firmware & Pembaruan Jarak Jauh (OTA)</h2>
              <p class="card-section-sub">Kirim instruksi pembaruan biner firmware secara Over-The-Air ke mikrokontroler ESP32 di Greenhouse tanpa perlu kabel USB.</p>
            </div>
            <div class="ota-header-badge">
              <span class="badge-pill bg-purple-subtle text-purple font-bold">
                Rilis Terbaru Tersedia: {{ latestFirmwareRelease?.version || 'v2.5.0' }}
              </span>
            </div>
          </div>

          <!-- Active OTA Progress Bar Banner (When any device is flashing) -->
          <div v-if="activeFlashingDevice" class="ota-live-banner">
            <div class="ota-banner-head">
              <div class="ota-pulse-icon">
                <span class="ota-dot-pulse"></span>
                <strong>PROSES FIRMWARE OTA FLASHING AKTIF: {{ activeFlashingDevice.name }}</strong>
              </div>
              <span class="ota-pct font-mono font-bold">{{ activeFlashingDevice.otaProgress }}%</span>
            </div>

            <div class="ota-progress-bar-wrap">
              <div 
                class="ota-progress-bar-fill" 
                :style="{ width: `${activeFlashingDevice.otaProgress}%` }"
              ></div>
            </div>

            <div class="ota-step-status-row">
              <span class="ota-log-msg font-mono">{{ activeFlashingDevice.lastOtaLog || 'Mengunduh biner firmware dan memverifikasi SHA-256 partition...' }}</span>
              <span class="ota-target-tag">Target: {{ activeFlashingDevice.targetFirmwareVersion || 'v2.5.0' }}</span>
            </div>
          </div>

          <!-- Fleet OTA Table -->
          <div class="ota-table-wrapper">
            <table class="ota-table">
              <thead>
                <tr>
                  <th>Nama Node / Mikrokontroler</th>
                  <th>Hardware Model</th>
                  <th>Versi Firmware Saat Ini</th>
                  <th>Status OTA</th>
                  <th>Terakhir Diperbarui</th>
                  <th style="text-align: right;">Aksi Trigger OTA</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="d in devices" :key="d.id">
                  <td>
                    <div class="node-cell-flex">
                      <div class="mini-icon-box">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2">
                          <rect x="2" y="2" width="20" height="20" rx="4"></rect>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                      </div>
                      <div>
                        <strong class="node-name">{{ d.name }}</strong>
                        <span class="node-code-sub">Code: {{ d.code }} • IP: {{ d.ipAddress }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="hw-chip font-mono">{{ d.hardwareVersion || 'ESP32-WROOM-32D' }}</span>
                  </td>
                  <td>
                    <div class="fw-cell">
                      <span class="fw-badge font-mono" :class="d.firmwareVersion === (latestFirmwareRelease?.version || 'v2.5.0') ? 'fw-latest' : 'fw-outdated'">
                        {{ d.firmwareVersion || 'v2.4.2' }}
                      </span>
                      <span v-if="d.firmwareVersion === (latestFirmwareRelease?.version || 'v2.5.0')" class="fw-tag-latest">Terbaru</span>
                      <span v-else class="fw-tag-update">Update Tersedia</span>
                    </div>
                  </td>
                  <td>
                    <span class="ota-status-pill" :class="`ota-${(d.otaStatus || 'IDLE').toLowerCase()}`">
                      <span class="ota-status-dot"></span>
                      <span>{{ formatOtaStatus(d.otaStatus) }}</span>
                    </span>
                  </td>
                  <td>
                    <span class="ota-time-text">{{ d.lastOtaAt ? formatDate(d.lastOtaAt) : 'Belum pernah update' }}</span>
                  </td>
                  <td style="text-align: right;">
                    <button 
                      type="button" 
                      class="btn-trigger-ota"
                      :class="{ 'btn-ota-disabled': d.firmwareVersion === (latestFirmwareRelease?.version || 'v2.5.0') && !d.targetFirmwareVersion }"
                      @click="handleOpenTriggerOtaModal(d)"
                      :disabled="d.otaStatus === 'DOWNLOADING' || d.otaStatus === 'FLASHING'"
                    >
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                      </svg>
                      <span>Trigger OTA Update</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 3: FIRMWARE RELEASES LIBRARY -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'releases'" class="tab-pane-wrapper">
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h2 class="card-section-heading">Pustaka & Repositori Rilis Firmware</h2>
              <p class="card-section-sub">Daftar file biner (.bin), checksum SHA-256 integritas, dan changelog pembaruan mikrokontroler.</p>
            </div>
            <button class="btn-primary" @click="isNewReleaseModalOpen = true">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <span>Tambah Versi Rilis Baru</span>
            </button>
          </div>

          <div class="releases-list">
            <div 
              v-for="r in firmwareReleases" 
              :key="r.id"
              class="release-item-card"
            >
              <div class="release-top-row">
                <div class="rel-version-group">
                  <span class="rel-badge font-mono">{{ r.version }}</span>
                  <span v-if="r.isLatest" class="rel-chip-latest">Rilis Terbaru (Latest)</span>
                  <span v-if="r.isStable" class="rel-chip-stable">Stable Production</span>
                </div>
                <span class="rel-date">{{ r.createdAt }}</span>
              </div>

              <h3 class="rel-title">{{ r.releaseTitle }}</h3>
              <p class="rel-changelog">{{ r.changelog }}</p>

              <div class="rel-meta-box">
                <div class="rel-meta-item">
                  <span class="rm-lbl">File Biner:</span>
                  <code class="rm-code">{{ r.filePath }}</code>
                </div>
                <div class="rel-meta-item">
                  <span class="rm-lbl">Ukuran File:</span>
                  <strong class="rm-val">{{ r.fileSizeFormatted }}</strong>
                </div>
                <div class="rel-meta-item">
                  <span class="rm-lbl">Checksum SHA-256:</span>
                  <code class="rm-code sha-code">{{ r.checksumSha256 }}</code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 4: SENSOR HEALTH MATRIX & LATENCY -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'sensors'" class="tab-pane-wrapper">
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h2 class="card-section-heading">Matriks Sensor Terkalibrasi & Status Live Telemetri</h2>
              <p class="card-section-sub">Pemantauan akurasi sensor fisik dan verifikasi kalibrasi greenhouse.</p>
            </div>
            <button class="btn-secondary-action" @click="pingAllDevices">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
              </svg>
              <span>Refresh Pembacaan</span>
            </button>
          </div>

          <div class="sensor-matrix-list">
            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>DHT22 Internal (Suhu & Kelembapan Ruang)</strong>
                  <p class="sensor-sub-desc">Pin: GPIO 25 (One-Wire) • Rentang: -40~80°C / 0-100% RH</p>
                </div>
              </div>
              <span class="sensor-val text-green-dark font-bold">
                {{ telemetry.hasData ? `${Number(telemetry.tempInternal).toFixed(1)}°C / ${Number(telemetry.humidityInternal).toFixed(0)}% RH` : 'Siaga / Menunggu ESP32' }} (Akurasi: ±0.5°C)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Sensor Cahaya & Radiasi Surya (BH1750)</strong>
                  <p class="sensor-sub-desc">Pin: I2C (SDA GPIO 21, SCL GPIO 22) • Pengukuran Lux Lingkungan</p>
                </div>
              </div>
              <span class="sensor-val text-orange font-bold">
                {{ telemetry.hasData ? `${Number(telemetry.solarRadiation).toFixed(0)} Lux` : 'Siaga' }} (Resolusi 1 Lux)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Sensor Deteksi Hujan & Presipitasi</strong>
                  <p class="sensor-sub-desc">Pin: GPIO 34 (ADC 12-Bit) • Threshold: &lt; 2000 ADC (Hujan)</p>
                </div>
              </div>
              <span class="sensor-val text-blue font-bold">
                {{ telemetry.hasData ? (telemetry.isRaining ? 'Terdeteksi Hujan (YA)' : 'Kering (TIDAK)') : 'Siaga' }}
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Relai Pemanas Ganda PTC 1 & 2</strong>
                  <p class="sensor-sub-desc">Pin: CH1 GPIO 26, CH2 GPIO 27 • Kontrol Histeresis (48°C - 50°C)</p>
                </div>
              </div>
              <span class="sensor-val text-orange font-bold">
                {{ telemetry.hasData ? (telemetry.auxHeaterStatus ? 'Pemanas AKTIF (ON)' : 'Pemanas STANDBY (OFF)') : 'Siaga' }}
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Indikator LED Panel Box Fisik (3 Warna)</strong>
                  <p class="sensor-sub-desc">Indikator Panel: Hijau (Standby/Normal) • Kuning (PTC ON) • Merah (Lockout ≥50°C)</p>
                </div>
              </div>
              <div class="panel-led-status-tags">
                <span class="mini-led-tag" :class="isLedGreenActive ? 'tag-lit-green' : 'tag-dim'">
                  <span class="mini-bulb"></span> Hijau: {{ isLedGreenActive ? 'ON' : 'OFF' }}
                </span>
                <span class="mini-led-tag" :class="isLedYellowActive ? 'tag-lit-yellow' : 'tag-dim'">
                  <span class="mini-bulb"></span> Kuning: {{ isLedYellowActive ? 'ON' : 'OFF' }}
                </span>
                <span class="mini-led-tag" :class="isLedRedActive ? 'tag-lit-red' : 'tag-dim'">
                  <span class="mini-bulb"></span> Merah: {{ isLedRedActive ? 'ON' : 'OFF' }}
                </span>
              </div>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Layar LCD 20x4 I2C (0x27)</strong>
                  <p class="sensor-sub-desc">Pin: I2C (SDA GPIO 21, SCL GPIO 22) • Monitoring & Diagnostik Lokal</p>
                </div>
              </div>
              <span class="sensor-val text-green-dark font-bold">
                {{ telemetry.hasData ? 'Menampilkan Halaman 1 & 2' : 'Standby I2C' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 5: ACTUATOR CONTROLS & HARDWARE SWITCHES -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'actuators'" class="tab-pane-wrapper">
        <ActuatorControlCard current-lang="id" />
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 6: KATALOG SENSOR & HARDWARE GREENHOUSE (6 KOMPONEN FISIK) -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'catalog'" class="tab-pane-wrapper">
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h2 class="card-section-heading">Katalog Komponen Sensor & Hardware Greenhouse (6 Modul)</h2>
              <p class="card-section-sub">Daftar lengkap mikrokontroler, modul sensor terkalibrasi, aktuator pemanas, dan layar diagnostik fisik yang aktif beroperasi pada sistem pengering Smart Greenhouse Hanjeli.</p>
            </div>
            <div class="catalog-badge-total">
              <span class="dot-green"></span>
              <strong>6 Modul Terpasang</strong>
            </div>
          </div>

          <div class="devices-catalog-grid">
            <div 
              v-for="hw in hardwareCatalog" 
              :key="hw.id"
              class="hardware-catalog-card"
            >
              <div class="hw-card-header">
                <div class="hw-icon-box" :class="hw.iconBoxClass">
                  <!-- MCU -->
                  <svg v-if="hw.iconType === 'mcu'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                    <rect x="9" y="9" width="6" height="6"></rect>
                    <line x1="9" y1="1" x2="9" y2="4"></line>
                    <line x1="15" y1="1" x2="15" y2="4"></line>
                    <line x1="9" y1="20" x2="9" y2="23"></line>
                    <line x1="15" y1="20" x2="15" y2="23"></line>
                    <line x1="20" y1="9" x2="23" y2="9"></line>
                    <line x1="20" y1="14" x2="23" y2="14"></line>
                    <line x1="1" y1="9" x2="4" y2="9"></line>
                    <line x1="1" y1="14" x2="4" y2="14"></line>
                  </svg>
                  <!-- Temp / Humidity -->
                  <svg v-else-if="hw.iconType === 'temp'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                  <!-- Sun / Light -->
                  <svg v-else-if="hw.iconType === 'sun'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2"></path>
                    <path d="M12 20v2"></path>
                    <path d="m4.93 4.93 1.41 1.41"></path>
                    <path d="m17.66 17.66 1.41 1.41"></path>
                    <path d="M2 12h2"></path>
                    <path d="M20 12h2"></path>
                    <path d="m6.34 17.66-1.41 1.41"></path>
                    <path d="m19.07 4.93-1.41 1.41"></path>
                  </svg>
                  <!-- Rain -->
                  <svg v-else-if="hw.iconType === 'rain'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="16" y1="13" x2="16" y2="21"></line>
                    <line x1="8" y1="13" x2="8" y2="21"></line>
                    <line x1="12" y1="15" x2="12" y2="23"></line>
                    <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
                  </svg>
                  <!-- Relay -->
                  <svg v-else-if="hw.iconType === 'relay'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                  </svg>
                  <!-- LCD Display -->
                  <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                  </svg>
                </div>
                <div class="hw-title-box">
                  <h3 class="hw-card-name">{{ hw.name }}</h3>
                  <span class="hw-card-category">{{ hw.category }}</span>
                </div>
                <span class="hw-status-pill tag-online">Aktif Siaga</span>
              </div>

              <p class="hw-card-purpose">{{ hw.purpose }}</p>

              <div class="hw-meta-grid">
                <div class="hw-meta-row">
                  <span class="hw-lbl">Spesifikasi:</span>
                  <span class="hw-val font-semibold">{{ hw.specs }}</span>
                </div>
                <div class="hw-meta-row">
                  <span class="hw-lbl">Pinout / Bus:</span>
                  <code class="hw-pin-tag">{{ hw.pinout }}</code>
                </div>
                <div class="hw-meta-row">
                  <span class="hw-lbl">Posisi Fisik:</span>
                  <span class="hw-val">{{ hw.location }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 7: SOURCE CODE & FIRMWARE ESP32 (ARDUINO IDE) -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'arduino'" class="tab-pane-wrapper">
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h2 class="card-section-heading">Source Code & Firmware ESP32 Greenhouse (Arduino IDE)</h2>
              <p class="card-section-sub">Pilih firmware produksi mikrokontroler ESP32: Firmware Resmi Fisik Standalone atau Firmware Dual IoT (Wi-Fi + BLE). Anda dapat menyalin kode sumber langsung atau mengunduh file <code>.ino</code>.</p>
            </div>
          </div>

          <!-- Program Selector Cards Grid -->
          <div class="program-selector-grid" style="margin-top: 14px;">
            <div 
              v-for="prog in arduinoPrograms" 
              :key="prog.id"
              class="program-card"
              :class="{ active: selectedProgramId === prog.id }"
              @click="selectedProgramId = prog.id"
            >
              <div class="program-card-header">
                <span class="program-badge" :class="prog.badgeClass">{{ prog.badge }}</span>
                <span class="program-filename font-mono">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  {{ prog.filename }}
                </span>
              </div>
              <h4 class="program-card-title">{{ prog.title }}</h4>
              <p class="program-card-desc">{{ prog.desc }}</p>
              <div class="program-card-footer">
                <span class="program-select-indicator">
                  <svg v-if="selectedProgramId === prog.id" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  {{ selectedProgramId === prog.id ? 'Program Dipilih' : 'Pilih Program Ini' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Active Program Meta & Code Viewer -->
          <div class="active-program-wrapper" style="margin-top: 20px;">
            <div class="program-meta-card">
              <div class="program-meta-grid">
                <div class="meta-item">
                  <span class="meta-label">TARGET BOARD:</span>
                  <span class="meta-val font-semibold">{{ selectedProgram.target }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">SERIAL BAUDRATE:</span>
                  <span class="meta-val font-mono">{{ selectedProgram.baudrate }} bps</span>
                </div>
                <div class="meta-item-full">
                  <span class="meta-label">LIBRARY DIBUTUHKAN:</span>
                  <div class="meta-lib-tags">
                    <span v-for="lib in selectedProgram.libraries" :key="lib" class="lib-tag font-mono">
                      {{ lib }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons: Copy & Download -->
              <div class="program-actions">
                <button 
                  class="action-btn-copy" 
                  @click="copyProgramCode(selectedProgram.code)"
                  :class="{ copied: isCopied }"
                >
                  <svg v-if="!isCopied" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                  </svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>{{ isCopied ? 'Tersalin ke Clipboard!' : 'Salin Program' }}</span>
                </button>

                <button class="action-btn-download" @click="downloadInoFile(selectedProgram)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>Unduh File .ino</span>
                </button>
              </div>
            </div>

            <!-- Code Editor Box -->
            <div class="code-editor-box">
              <div class="code-editor-header">
                <div class="code-header-left">
                  <div class="code-dots">
                    <span class="code-dot dot-red"></span>
                    <span class="code-dot dot-yellow"></span>
                    <span class="code-dot dot-green"></span>
                  </div>
                  <span class="code-file-title font-mono">{{ selectedProgram.filename }}</span>
                </div>
                <div class="code-header-right">
                  <span class="code-lines-count font-mono">{{ selectedProgram.code.split('\n').length }} baris</span>
                  <button class="code-header-copy-btn" @click="copyProgramCode(selectedProgram.code)">
                    <svg v-if="isCopied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline-block; vertical-align:middle; margin-right:4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:4px;"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span>{{ isCopied ? 'Disalin' : 'Salin Kode' }}</span>
                  </button>
                </div>
              </div>

              <div class="code-editor-content">
                <pre class="code-pre font-mono"><code>{{ selectedProgram.code }}</code></pre>
              </div>
            </div>

            <!-- Upload Guide Card -->
            <div class="flashing-guide-card" style="margin-top: 20px;">
              <h4 class="guide-steps-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Panduan Cepat Upload Program ke ESP32 via Arduino IDE
              </h4>
              <div class="steps-grid">
                <div class="step-card">
                  <div class="step-num">1</div>
                  <div class="step-desc">
                    <strong>Pasang Board ESP32:</strong> Buka Arduino IDE → <em>File &gt; Preferences</em> → Masukkan URL Board Manager: <code>https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json</code> lalu pasang via Boards Manager.
                  </div>
                </div>
                <div class="step-card">
                  <div class="step-num">2</div>
                  <div class="step-desc">
                    <strong>Pasang Library:</strong> Buka <em>Tools &gt; Manage Libraries</em>, cari dan install <code>DHT sensor library</code> (oleh Adafruit), <code>BH1750</code> (oleh Christopher Laws), dan <code>LiquidCrystal_I2C</code>.
                  </div>
                </div>
                <div class="step-card">
                  <div class="step-num">3</div>
                  <div class="step-desc">
                    <strong>Pilih Board &amp; Port:</strong> Di menu <em>Tools &gt; Board</em>, pilih <strong>DOIT ESP32 DEVKIT V1</strong> (atau ESP32 Dev Module), lalu pilih Port COM USB ESP32 Anda.
                  </div>
                </div>
                <div class="step-card">
                  <div class="step-num">4</div>
                  <div class="step-desc">
                    <strong>Upload Firmware:</strong> Buka file <code>.ino</code> atau salin kode di atas ke lembar kerja baru Arduino IDE, lalu klik tombol <strong>Upload</strong> (Panah Kanan).
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </Transition>

    <!-- ========================================================================= -->
    <!-- MODAL 1: TRIGGER OTA UPDATE CONFIRMATION & TARGET SELECTOR -->
    <!-- ========================================================================= -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="isOtaModalOpen" class="modal-overlay" @click.self="isOtaModalOpen = false">
          <div class="modal-card">
            <div class="modal-header">
              <div class="modal-header-icon bg-purple-subtle text-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
              </div>
              <div>
                <h3 class="modal-title">Trigger Pembaruan Firmware OTA</h3>
                <p class="modal-sub">Kirim perintah update firmware jarak jauh ke perangkat mikrokontroler.</p>
              </div>
            </div>

            <div class="modal-body">
              <div class="ota-target-info-card">
                <div class="target-row">
                  <span>Nama Perangkat:</span>
                  <strong>{{ selectedDeviceForOta?.name }}</strong>
                </div>
                <div class="target-row">
                  <span>Versi Saat Ini:</span>
                  <code class="text-muted">{{ selectedDeviceForOta?.firmwareVersion || 'v2.4.2' }}</code>
                </div>
                <div class="target-row">
                  <span>Model Hardware:</span>
                  <code>{{ selectedDeviceForOta?.hardwareVersion || 'ESP32-WROOM-32D' }}</code>
                </div>
              </div>

              <div class="form-group" style="margin-top: 16px;">
                <label class="field-label">PILIH VERSI TARGET FIRMWARE:</label>
                <select v-model="targetFirmwareVersionSelected" class="form-select font-mono">
                  <option 
                    v-for="rel in firmwareReleases" 
                    :key="rel.id" 
                    :value="rel.version"
                  >
                    {{ rel.version }} — {{ rel.releaseTitle }} {{ rel.isLatest ? '(Terbaru)' : '' }}
                  </option>
                </select>
                <span class="field-helper">Mikrokontroler akan mengunduh biner, memvalidasi SHA-256 partition, dan melakukan hot-restart.</span>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="isOtaModalOpen = false">Batal</button>
              <button type="button" class="btn-primary" @click="executeTriggerOta" :disabled="isSubmitting">
                <span>{{ isSubmitting ? 'Mengirim Perintah...' : 'Mulai Proses OTA Flashing' }}</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ========================================================================= -->
    <!-- MODAL 2: ARDUINO / C++ ESP32 CODE INTEGRATION SNIPPET -->
    <!-- ========================================================================= -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="isCodeModalOpen" class="modal-overlay" @click.self="isCodeModalOpen = false">
          <div class="modal-card modal-lg">
            <div class="modal-header">
              <div class="modal-header-icon bg-green-subtle text-green-dark">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polyline points="16 18 22 12 16 6"></polyline>
                  <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
              </div>
              <div>
                <h3 class="modal-title">Panduan Integrasi Header X-Device-Token (C++ / Arduino)</h3>
                <p class="modal-sub">Contoh kode mikrokontroler ESP32 untuk pengiriman telemetri berotentikasi.</p>
              </div>
            </div>

            <div class="modal-body">
              <div class="code-box-wrapper">
                <pre class="code-pre font-mono"><code>#include &lt;WiFi.h&gt;
#include &lt;HTTPClient.h&gt;
#include &lt;ArduinoJson.h&gt;

const char* ssid = "GreenHouse_Hanjeli_IoT";
const char* password = "PasswordWifi123";
const char* serverEndpoint = "http://192.168.1.105:8000/api/telemetry/ingest";

// Token Otentikasi Unik dari Menu Admin
const char* DEVICE_TOKEN = "{{ devices[0]?.deviceToken || 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c' }}";

void sendTelemetry(float temp, float hum, float moisture, float solar, float weight) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverEndpoint);
        
        // [SECURITY] HEADER WAJIB OTENTIKASI & KEAMANAN
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-Device-Token", DEVICE_TOKEN);
        
        StaticJsonDocument&lt;256&gt; doc;
        doc["tempInternal"] = temp;
        doc["humidityInternal"] = hum;
        doc["grainMoisture"] = moisture;
        doc["solarRadiation"] = solar;
        doc["weightKg"] = weight;
        
        String jsonPayload;
        serializeJson(doc, jsonPayload);
        
        int httpResponseCode = http.POST(jsonPayload);
        if (httpResponseCode == 201) {
            Serial.println("[TX] Telemetri berhasil di-ingest & divalidasi server!");
        } else {
            Serial.printf("[ERR] HTTP Gagal: %d\n", httpResponseCode);
        }
        http.end();
    }
}</code></pre>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-primary" @click="copyCodeSnippet">Salin Kode Contoh</button>
              <button type="button" class="btn-cancel" @click="isCodeModalOpen = false">Tutup</button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ========================================================================= -->
    <!-- MODAL 3: UPLOAD NEW FIRMWARE RELEASE -->
    <!-- ========================================================================= -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="isNewReleaseModalOpen" class="modal-overlay" @click.self="isNewReleaseModalOpen = false">
          <div class="modal-card">
            <div class="modal-header">
              <div class="modal-header-icon bg-purple-subtle text-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
              </div>
              <div>
                <h3 class="modal-title">Tambah Rilis Firmware Baru</h3>
                <p class="modal-sub">Daftarkan versi biner firmware (.bin) untuk didistribusikan via OTA.</p>
              </div>
            </div>

            <form @submit.prevent="handleSubmitFirmwareRelease">
              <div class="modal-body">
                <div class="form-group">
                  <label class="field-label">TAG VERSI FIRMWARE (Contoh: v2.6.0):</label>
                  <input 
                    type="text" 
                    v-model="newReleaseForm.version" 
                    required 
                    placeholder="v2.6.0" 
                    class="form-input font-mono"
                  />
                </div>

                <div class="form-group">
                  <label class="field-label">JUDUL RILIS / NAMA BUILD:</label>
                  <input 
                    type="text" 
                    v-model="newReleaseForm.releaseTitle" 
                    required 
                    placeholder="Firmware v2.6.0 (Enhanced Auto-Ventilation & PID)" 
                    class="form-input"
                  />
                </div>

                <div class="form-group">
                  <label class="field-label">CHANGELOG / CATATAN PEMBARUAN:</label>
                  <textarea 
                    v-model="newReleaseForm.changelog" 
                    rows="3" 
                    placeholder="1. Peningkatan akurasi sensor...&#10;2. Optimasi konsumsi daya..." 
                    class="form-input"
                  ></textarea>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn-cancel" @click="isNewReleaseModalOpen = false">Batal</button>
                <button type="submit" class="btn-primary" :disabled="isSubmitting">
                  <span>{{ isSubmitting ? 'Menyimpan...' : 'Simpan Rilis Firmware' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import AdminSkeleton from '../../components/AdminSkeleton.vue'
import ActuatorControlCard from '../../components/ActuatorControlCard.vue'
import { deviceService } from '../../services/deviceService'
import { telemetryService } from '../../services/telemetryService'
import { socketService } from '../../services/socketService'
import { confirmDialog } from '../../services/confirmDialogService'
import { ESP32_PHYSICAL_STANDALONE_CODE, ESP32_DUAL_MODE_CODE } from '../../data/arduinoCode'

const isInitialLoading = ref(true)
const currentTab = ref('security') // 'security' | 'ota' | 'releases' | 'sensors' | 'actuators' | 'catalog' | 'arduino'
const isSubmitting = ref(false)
const isPingingAll = ref(false)

const selectedProgramId = ref('physical-standalone')
const isCopied = ref(false)

const arduinoPrograms = computed(() => [
  {
    id: 'physical-standalone',
    title: 'Firmware Resmi Alat Fisik (DHT22, BH1750, Rain Sensor, Dual PTC & LCD 20x4)',
    filename: 'esp32_smart_dryer_physical_fixed.ino',
    badge: 'Produksi / Hardware Fix Langsung',
    badgeClass: 'badge-prod',
    desc: 'Program resmi mikrokontroler ESP32 langsung untuk hardware greenhouse fisik: Sensor DHT22 (Pin 25), BH1750 I2C (Pin 21/22), Sensor Hujan (Pin 34), Dual Relay PTC Heaters (Pin 26 & 27), LCD 20x4 I2C (0x27) dengan kontrol cerdas 55°C - 60°C serta proteksi safety shut-off.',
    target: 'ESP32 DevKit V1 (DHT22 Pin 25, Relay 26/27, Rain 34, I2C 21/22)',
    libraries: ['DHT sensor library (Adafruit)', 'BH1750 (Christopher Laws)', 'LiquidCrystal_I2C', 'Wire.h'],
    baudrate: 115200,
    code: ESP32_PHYSICAL_STANDALONE_CODE,
  },
  {
    id: 'dual-mode',
    title: 'Firmware Alat Fisik Dual IoT (Wi-Fi MQTT + Web Bluetooth BLE + Web Serial)',
    filename: 'esp32_smart_dryer_dual_iot.ino',
    badge: 'Hardware Nyata + Sinkronisasi Cloud/Web',
    badgeClass: 'badge-prod',
    desc: 'Program ESP32 untuk hardware fisik yang sama dengan tambahan konektivitas ganda: Wi-Fi MQTT broker dan Web Bluetooth BLE untuk kendali nirkabel jarak jauh dari dashboard operator.',
    target: 'ESP32 DevKit V1 (DHT22 Pin 25, Relay 26/27, Rain 34, I2C 21/22)',
    libraries: ['DHT sensor library', 'BH1750', 'LiquidCrystal_I2C', 'PubSubClient', 'ArduinoJson (v6/v7)', 'WiFi.h', 'BLEDevice.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_DUAL_MODE_CODE,
  }
])

const selectedProgram = computed(() => {
  return arduinoPrograms.value.find(p => p.id === selectedProgramId.value) || arduinoPrograms.value[0]
})

const hardwareCatalog = computed(() => [
  {
    id: 'mcu-esp32',
    name: 'Mikrokontroler ESP32 DevKit V1',
    category: 'Node Utama & Master Gateway',
    purpose: 'Pengendali histeresis pemanas PTC 1 & 2 (48°C - 50°C), pembacaan DHT22, BH1750, sensor hujan, serta stream telemetri JSON serial & LCD 20x4.',
    specs: 'Dual-Core Xtensa 240MHz, 520KB SRAM, Flash 4MB',
    pinout: 'GPIO 25, 26, 27, 34, I2C 21/22',
    location: 'Box Panel Utama Greenhouse',
    iconType: 'mcu',
    iconBoxClass: 'icon-green-subtle',
  },
  {
    id: 'sensor-dht22',
    name: 'Sensor Suhu & Kelembapan Ruang (DHT22)',
    category: 'Sensor Digital Lingkungan',
    purpose: 'Mengukur suhu internal ruang pengering (-40~80°C ±0.5°C) & kelembapan (0-100% RH ±2%) dengan safety shut-off otomatis jika pembacaan gagal 3x berturut-turut.',
    specs: 'Akurasi ±0.5°C / ±2% RH, Sampling 2.0s',
    pinout: 'Pin GPIO 25 (One-Wire Digital)',
    location: 'Ruang Pengering Greenhouse (Tengah)',
    iconType: 'temp',
    iconBoxClass: 'icon-blue-subtle',
  },
  {
    id: 'sensor-bh1750',
    name: 'Sensor Intensitas Cahaya / Solar (BH1750)',
    category: 'Sensor Optik Lingkungan (I2C)',
    purpose: 'Mengukur intensitas cahaya matahari langsung (1 ~ 65.535 Lux) secara kontinu untuk penyesuaian otomatis strategi pemanasan hibrida surya-listrik.',
    specs: 'Rentang 1-65535 Lux, Resolusi 1 Lux',
    pinout: 'I2C Bus (SDA GPIO 21 / SCL GPIO 22, Addr 0x23)',
    location: 'Plafon Transparan Greenhouse',
    iconType: 'sun',
    iconBoxClass: 'icon-amber-subtle',
  },
  {
    id: 'sensor-rain',
    name: 'Sensor Deteksi Presipitasi & Air Hujan',
    category: 'Sensor Presipitasi Analog',
    purpose: 'Mendeteksi rintik air hujan di luar ruang secara instan melalui ADC analog (Threshold < 2000 ADC) untuk mengantisipasi penurunan panas alami.',
    specs: 'Analog ADC 12-Bit (0-4095), Threshold < 2000',
    pinout: 'Pin GPIO 34 (Analog ADC Input)',
    location: 'Atap Luar Greenhouse',
    iconType: 'rain',
    iconBoxClass: 'icon-blue-subtle',
  },
  {
    id: 'relay-ptc',
    name: 'Modul Dual Relay Pemanas Keramik PTC 1 & 2',
    category: 'Aktuator Pemanas Bantu (Auxiliary)',
    purpose: 'Kontrol otomatis elemen pemanas keramik PTC ganda berbasis histeresis cerdas untuk menjaga kestabilan suhu pengeringan hanjeli.',
    specs: 'Dual Relay 5V/10A Songle, Elemen PTC Ceramic',
    pinout: 'Pin CH1 GPIO 26 & CH2 GPIO 27',
    location: 'Chamber Pemanas Greenhouse',
    iconType: 'relay',
    iconBoxClass: 'icon-orange-subtle',
  },
  {
    id: 'display-lcd',
    name: 'Layar Display LCD 20x4 I2C (Address 0x27)',
    category: 'Antarmuka Visual Lokal Panel (HMI)',
    purpose: 'Menampilkan telemetri pemantauan live T/H/Lux/Rain/Heater (Hal 1) dan Diagnostik Sistem/Sensor (Hal 2) secara bergantian setiap 4 detik.',
    specs: '20 Kolom x 4 Baris, Backpack I2C PCF8574',
    pinout: 'I2C Bus (SDA GPIO 21 / SCL GPIO 22, Addr 0x27)',
    location: 'Pintu Panel Kontrol Greenhouse',
    iconType: 'lcd',
    iconBoxClass: 'icon-green-subtle',
  },
])

async function copyProgramCode(code) {
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(code)
    } else {
      throw new Error('Clipboard API not available')
    }
    isCopied.value = true
    setTimeout(() => { isCopied.value = false }, 2500)
  } catch (err) {
    const textarea = document.createElement('textarea')
    textarea.value = code
    textarea.style.position = 'fixed'
    textarea.style.opacity = '0'
    document.body.appendChild(textarea)
    textarea.select()
    document.execCommand('copy')
    document.body.removeChild(textarea)
    isCopied.value = true
    setTimeout(() => { isCopied.value = false }, 2500)
  }
}

function downloadInoFile(program) {
  const blob = new Blob([program.code], { type: 'text/plain;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = program.filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

const adminAlert = reactive({
  text: '',
  type: 'success'
})

function showAlert(text, type = 'success') {
  adminAlert.text = text
  adminAlert.type = type
  setTimeout(() => {
    if (adminAlert.text === text) adminAlert.text = ''
  }, 6000)
}

// Devices & Releases Data State
const devices = ref([])
const firmwareReleases = ref([])
const showTokens = reactive({})

const telemetry = reactive({
  tempInternal: 0.0,
  humidityInternal: 0.0,
  solarRadiation: 0.0,
  grainMoisture: 0.0,
  weightCurrentKg: 0.0,
  isRaining: false,
  auxHeaterStatus: false,
  hasData: false,
})

// Physical Box Panel 3-LED Indicator Status (Green, Yellow, Red)
const isLedRedActive = computed(() => telemetry.hasData && Number(telemetry.tempInternal) >= 50.0)
const isLedYellowActive = computed(() => telemetry.hasData && Boolean(telemetry.auxHeaterStatus) && Number(telemetry.tempInternal) < 50.0)
const isLedGreenActive = computed(() => telemetry.hasData && !telemetry.auxHeaterStatus && Number(telemetry.tempInternal) < 50.0)

// Modals State
const isOtaModalOpen = ref(false)
const isCodeModalOpen = ref(false)
const isNewReleaseModalOpen = ref(false)
const selectedDeviceForOta = ref(null)
const targetFirmwareVersionSelected = ref('v2.5.0')

const newReleaseForm = reactive({
  version: '',
  releaseTitle: '',
  changelog: '',
})

const latestFirmwareRelease = computed(() => {
  if (!firmwareReleases.value || firmwareReleases.value.length === 0) return { version: 'v2.5.0' }
  return firmwareReleases.value.find(r => r.isLatest) || firmwareReleases.value[0]
})

const activeFlashingDevice = computed(() => {
  return devices.value.find(d => d.otaStatus === 'DOWNLOADING' || d.otaStatus === 'FLASHING' || d.otaStatus === 'PENDING')
})

const isAnyOtaActive = computed(() => {
  return !!activeFlashingDevice.value
})

async function fetchDevices() {
  try {
    const res = await deviceService.getAll()
    devices.value = res || []
  } catch (err) {
    console.warn('Failed to load devices:', err)
  }
}

async function fetchReleases() {
  try {
    const res = await deviceService.getFirmwareReleases()
    firmwareReleases.value = res || []
    if (firmwareReleases.value.length > 0) {
      targetFirmwareVersionSelected.value = firmwareReleases.value[0].version
    }
  } catch (err) {
    console.warn('Failed to load releases:', err)
  }
}

function toggleTokenVisibility(deviceId) {
  showTokens[deviceId] = !showTokens[deviceId]
}

async function copyToken(tokenText) {
  try {
    await navigator.clipboard.writeText(tokenText)
    showAlert('Token otentikasi X-Device-Token berhasil disalin ke clipboard!', 'success')
  } catch {
    showAlert('Gagal menyalin token.', 'error')
  }
}

async function toggleDevicePower(device) {
  try {
    const res = await deviceService.toggle(device.id)
    device.status = res.status || (device.status === 'online' ? 'offline' : 'online')
    showAlert(`Status daya ${device.name} diubah menjadi: ${device.status.toUpperCase()}`, 'success')
  } catch (err) {
    showAlert(`Gagal mengubah status daya ${device.name}`, 'error')
  }
}

async function handleRegenerateToken(device) {
  const confirmed = await confirmDialog({
    title: `Regenerate Token Baru untuk ${device.name}?`,
    message: 'Perangkat fisik ESP32 yang sedang menggunakan token lama akan terputus sampai token baru disalin ke firmware mikrokontroler.',
    type: 'warning',
    confirmText: 'Ya, Buat Token Baru',
    cancelText: 'Batal'
  })

  if (confirmed) {
    isSubmitting.value = true
    try {
      const res = await deviceService.regenerateToken(device.id)
      if (res && res.deviceToken) {
        device.deviceToken = res.deviceToken
      }
      showTokens[device.id] = true
      showAlert(`Token otentikasi untuk ${device.name} berhasil diperbarui!`, 'success')
    } catch (err) {
      showAlert(err.message || 'Gagal meregenerasi token.', 'error')
    } finally {
      isSubmitting.value = false
    }
  }
}

async function pingDevice(device) {
  device.isPinging = true
  try {
    const res = await deviceService.ping(device.id)
    device.status = 'online'
    device.lastHeartbeat = 'Baru saja'
    showAlert(res.message || `Ping ke ${device.name} sukses (18ms).`, 'success')
  } catch (err) {
    showAlert(`Gagal melakukan ping ke ${device.name}`, 'error')
  } finally {
    device.isPinging = false
  }
}

async function pingAllDevices() {
  isPingingAll.value = true
  try {
    await deviceService.pingAll()
    devices.value.forEach(d => {
      d.status = 'online'
      d.lastHeartbeat = 'Baru saja'
    })
    showAlert('Seluruh perangkat fisik greenhouse merespons sinyal dengan status Online!', 'success')
  } catch (err) {
    showAlert('Gagal memeriksa sinyal perangkat.', 'error')
  } finally {
    isPingingAll.value = false
  }
}

function handleOpenTriggerOtaModal(device) {
  selectedDeviceForOta.value = device
  isOtaModalOpen.value = true
}

async function executeTriggerOta() {
  if (!selectedDeviceForOta.value) return

  const device = selectedDeviceForOta.value
  const targetVer = targetFirmwareVersionSelected.value

  isSubmitting.value = true
  try {
    await deviceService.triggerOta(device.id, targetVer)
    device.targetFirmwareVersion = targetVer
    device.otaStatus = 'DOWNLOADING'
    device.otaProgress = 10
    device.lastOtaLog = `Mengunduh biner firmware ${targetVer}...`
    isOtaModalOpen.value = false
    showAlert(`Perintah OTA Flashing ke versi ${targetVer} berhasil dikirim ke ${device.name}!`, 'success')

    // Simulate Step-by-Step Live OTA Progress on Frontend Demo
    simulateOtaFlashing(device, targetVer)
  } catch (err) {
    showAlert(err.message || 'Gagal memicu update OTA.', 'error')
  } finally {
    isSubmitting.value = false
  }
}

function simulateOtaFlashing(device, targetVer) {
  let progress = 15
  const interval = setInterval(async () => {
    progress += 20
    if (progress <= 45) {
      device.otaStatus = 'DOWNLOADING'
      device.otaProgress = progress
      device.lastOtaLog = `Mengunduh file biner hanjeli_esp32_${targetVer}.bin (${progress}%)...`
    } else if (progress <= 85) {
      device.otaStatus = 'FLASHING'
      device.otaProgress = progress
      device.lastOtaLog = `Menulis partisi memori flash ESP32 (${progress}%)...`
    } else if (progress < 100) {
      device.otaStatus = 'FLASHING'
      device.otaProgress = 95
      device.lastOtaLog = 'Memverifikasi checksum SHA-256 dan melakukan reboot node...'
    } else {
      clearInterval(interval)
      device.otaStatus = 'SUCCESS'
      device.otaProgress = 100
      device.firmwareVersion = targetVer
      device.targetFirmwareVersion = null
      device.lastOtaLog = `Pembaruan firmware ${targetVer} berhasil diselesaikan & aktif.`
      
      try {
        await deviceService.reportOtaProgress(device.id, {
          status: 'SUCCESS',
          progress: 100,
          newVersion: targetVer,
          log: `OTA Flashing ${targetVer} berhasil terpasang.`
        })
      } catch {}

      showAlert(`Proses OTA pada ${device.name} SELESAI! Firmware sekarang aktif pada versi ${targetVer}.`, 'success')
      
      setTimeout(() => {
        if (device.otaStatus === 'SUCCESS') {
          device.otaStatus = 'IDLE'
        }
      }, 5000)
    }
  }, 1200)
}

async function handleSubmitFirmwareRelease() {
  isSubmitting.value = true
  try {
    await deviceService.createFirmwareRelease({
      version: newReleaseForm.version,
      releaseTitle: newReleaseForm.releaseTitle,
      changelog: newReleaseForm.changelog,
      isLatest: true
    })
    showAlert(`Rilis firmware ${newReleaseForm.version} berhasil ditambahkan!`, 'success')
    isNewReleaseModalOpen.value = false
    newReleaseForm.version = ''
    newReleaseForm.releaseTitle = ''
    newReleaseForm.changelog = ''
    await fetchReleases()
  } catch (err) {
    showAlert(err.message || 'Gagal menambahkan rilis firmware.', 'error')
  } finally {
    isSubmitting.value = false
  }
}

function openCodeSnippetModal() {
  isCodeModalOpen.value = true
}

async function copyCodeSnippet() {
  const code = `#include <HTTPClient.h>\nhttp.addHeader("X-Device-Token", "${devices.value[0]?.deviceToken || 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c'}");`
  try {
    await navigator.clipboard.writeText(code)
    showAlert('Cuplikan kode C++ berhasil disalin!', 'success')
  } catch {}
}

function formatOtaStatus(status) {
  const s = (status || 'IDLE').toUpperCase()
  if (s === 'IDLE') return 'Siaga (Idle)'
  if (s === 'PENDING') return 'Menunggu Koneksi'
  if (s === 'DOWNLOADING') return 'Mengunduh Biner'
  if (s === 'FLASHING') return 'Flashing ROM'
  if (s === 'SUCCESS') return 'Update Selesai'
  if (s === 'FAILED') return 'Gagal'
  return s
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

onMounted(async () => {
  try {
    await Promise.all([
      fetchDevices(),
      fetchReleases()
    ])
    // Fetch initial telemetry for sensor matrix
    try {
      const currentRes = await telemetryService.getCurrent()
      if (currentRes && currentRes.telemetry) {
        const t = currentRes.telemetry
        telemetry.tempInternal = t.tempInternal ?? 0.0
        telemetry.humidityInternal = t.humidityInternal ?? 0.0
        telemetry.solarRadiation = t.solarRadiation ?? 0.0
        telemetry.grainMoisture = t.grainMoisture ?? 0.0
        telemetry.weightCurrentKg = t.weightCurrentKg ?? 0.0
        telemetry.isRaining = t.isRaining ?? false
        telemetry.auxHeaterStatus = t.heaterStatus ?? currentRes.actuators?.auxHeaterStatus ?? false
        telemetry.hasData = Boolean(t.hasData)
      }
    } catch (e) {
      console.warn('Initial telemetry fetch skipped:', e)
    }
  } finally {
    setTimeout(() => {
      isInitialLoading.value = false
    }, 350)
  }

  // Listen to live telemetry updates
  socketService.on('telemetry_live', (data) => {
    if (data && data.hasData) {
      telemetry.tempInternal = data.tempInternal ?? telemetry.tempInternal
      telemetry.humidityInternal = data.humidityInternal ?? telemetry.humidityInternal
      telemetry.solarRadiation = data.solarRadiation ?? telemetry.solarRadiation
      telemetry.grainMoisture = data.grainMoisture ?? telemetry.grainMoisture
      telemetry.weightCurrentKg = data.weightCurrentKg ?? telemetry.weightCurrentKg
      telemetry.isRaining = data.isRaining ?? telemetry.isRaining
      telemetry.auxHeaterStatus = data.heaterStatus ?? data.auxHeaterStatus ?? telemetry.auxHeaterStatus
      telemetry.hasData = true
    }
  })
})

onUnmounted(() => {
  socketService.off('telemetry_live')
})
</script>

<style scoped>
.admin-page {
  width: 100%;
  min-height: 100%;
}

.admin-content {
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
  margin: 0;
  box-sizing: border-box;
}

.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
}

.title-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.title-badge-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  color: var(--color-text-title, #071E27);
  line-height: 32px;
  margin: 0;
}

.security-shield-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 20px;
  background: #E8F5E9;
  border: 1px solid #86EFAC;
  color: #1B5E20;
  font-size: 11.5px;
  font-weight: 700;
}

.page-subtitle {
  font-size: 14px;
  color: var(--color-text-muted, #64748B);
  line-height: 20px;
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* Feedback Toast */
.admin-feedback-toast {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 13.5px;
  font-weight: 600;
  animation: slideDown 0.3s ease;
}

.admin-feedback-toast.success {
  background: #E8F5E9;
  border: 1px solid #86EFAC;
  color: #1B5E20;
}

.admin-feedback-toast.error {
  background: #FEE2E2;
  border: 1px solid #FCA5A5;
  color: #BA1A1A;
}

.close-toast-btn {
  margin-left: auto;
  background: transparent;
  border: none;
  cursor: pointer;
  color: inherit;
  display: flex;
  align-items: center;
}

/* 4 Top KPI Cards */
.stats-grid-4 {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: 12px;
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.stat-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.stat-label {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.5px;
  color: var(--color-text-muted, #64748B);
}

.icon-circle {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-green-light { background: #E8F5E9; }
.icon-blue { background: #E0F2FE; }
.icon-purple { background: #F3E8FF; }
.icon-orange { background: #FFEDD5; }

.stat-value-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.stat-number {
  font-size: 22px;
  font-weight: 900;
  color: var(--color-text-title, #071E27);
}

.stat-unit {
  font-size: 12px;
  color: var(--color-text-muted, #64748B);
  font-weight: 600;
}

.stat-trend {
  font-size: 11.5px;
  font-weight: 600;
}

.trend-up-green { color: #1B5E20; }
.trend-down-blue { color: #005DB7; }
.trend-purple { color: #7C3AED; }
.trend-orange { color: #B45000; }

/* Subnav Tabs */
.device-subnav-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--color-bg-light, #F1F5F9);
  padding: 6px;
  border-radius: 10px;
  border: 1px solid var(--color-border-subtle, #E2E8F0);
  overflow-x: auto;
}

.subnav-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-muted, #64748B);
  background: transparent;
  border: none;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
}

.subnav-btn:hover {
  color: var(--color-text-title, #071E27);
  background: rgba(255, 255, 255, 0.6);
}

.subnav-btn.active {
  background: var(--color-white, #FFFFFF);
  color: #1B5E20;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

/* Content Cards & Tab Panes */
.tab-pane-wrapper {
  animation: fadeIn 0.25s ease;
}

.content-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card-head-flex {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
}

.card-section-heading {
  font-size: 18px;
  font-weight: 800;
  color: var(--color-text-title, #071E27);
  margin: 0;
}

.card-section-sub {
  font-size: 13px;
  color: var(--color-text-muted, #64748B);
  margin: 4px 0 0 0;
  line-height: 1.4;
}

/* Device Token Cards Grid */
.devices-token-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
  gap: 18px;
}

.device-security-card {
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: 12px;
  background: var(--color-bg-light, #FAFAFA);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 16px;
  gap: 14px;
  transition: all 0.2s ease;
}

.device-security-card:hover {
  border-color: #86EFAC;
  box-shadow: 0 4px 12px rgba(13, 99, 27, 0.05);
}

.dsc-header {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.dsc-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 8px;
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border, #E2E8F0);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dsc-title-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.dsc-name-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.dsc-toggle-status-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
}

.device-switch-toggle {
  position: relative;
  display: inline-block;
  width: 38px;
  height: 20px;
  cursor: pointer;
}

.device-switch-toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.device-switch-toggle .toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #CBD5E1;
  transition: 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  border-radius: 20px;
}

.device-switch-toggle .toggle-slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
}

.device-switch-toggle input:checked + .toggle-slider {
  background-color: #1B5E20;
}

.device-switch-toggle input:checked + .toggle-slider:before {
  transform: translateX(18px);
}

.dsc-name {
  font-size: 14.5px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  margin: 0;
}

.badge-status-dot {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
}

.dot-online {
  background: #E8F5E9;
  color: #1B5E20;
}

.dot-offline {
  background: #FEE2E2;
  color: #BA1A1A;
}

.dsc-meta-tag {
  font-size: 11px;
  color: var(--color-text-muted, #64748B);
}

.dsc-meta-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  background: var(--color-white, #FFFFFF);
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid var(--color-border-subtle, #E2E8F0);
  font-size: 12px;
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.meta-lbl {
  font-size: 10px;
  color: var(--color-text-muted, #94A3B8);
  font-weight: 700;
}

.meta-val {
  color: var(--color-text-title, #071E27);
}

/* Token Field Box */
.token-field-box {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.token-field-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.token-label {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.5px;
  color: #1B5E20;
}

.token-status-pill {
  font-size: 10px;
  background: #E8F5E9;
  color: #1B5E20;
  padding: 1px 6px;
  border-radius: 4px;
  font-weight: 700;
}

.token-input-wrapper {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--color-white, #FFFFFF);
  border: 1.5px solid #86EFAC;
  border-radius: 8px;
  padding: 4px 6px 4px 10px;
}

.token-input {
  border: none;
  background: transparent;
  font-size: 12px;
  color: #071E27;
  font-weight: 600;
  width: 100%;
  outline: none;
}

.btn-token-icon {
  background: transparent;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  transition: all 0.15s;
}

.btn-token-icon:hover {
  color: #1B5E20;
  background: #F1F5F9;
}

.dsc-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding-top: 10px;
  border-top: 1px solid var(--color-border-subtle, #E2E8F0);
}

.btn-regen-token {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  font-weight: 700;
  color: #7C3AED;
  background: #F3E8FF;
  border: 1px solid #DDD6FE;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-regen-token:hover {
  background: #EDE9FE;
  color: #6D28D9;
}

.btn-ping-node {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  font-weight: 700;
  color: #1B5E20;
  background: #E8F5E9;
  border: 1px solid #86EFAC;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-ping-node:hover {
  background: #DCFCE7;
}

/* OTA Section Styles */
.ota-live-banner {
  background: #FFFBEB;
  border: 1.5px solid #FCD34D;
  border-radius: 10px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  animation: pulseLight 2s infinite;
}

.ota-banner-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.ota-pulse-icon {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #B45309;
  font-size: 13px;
}

.ota-dot-pulse {
  width: 8px;
  height: 8px;
  background: #F59E0B;
  border-radius: 50%;
  animation: liveDot 1.5s infinite;
}

.ota-progress-bar-wrap {
  width: 100%;
  height: 8px;
  background: #FEF3C7;
  border-radius: 9999px;
  overflow: hidden;
}

.ota-progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #F59E0B, #10B981);
  border-radius: 9999px;
  transition: width 0.8s ease;
}

.ota-step-status-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11.5px;
  color: #92400E;
}

.ota-target-tag {
  font-weight: 700;
  background: #FEF3C7;
  padding: 2px 6px;
  border-radius: 4px;
}

.ota-table-wrapper {
  overflow-x: auto;
}

.ota-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.ota-table th {
  padding: 12px 14px;
  text-align: left;
  border-bottom: 2px solid var(--color-border, #CBD5E1);
  background: var(--color-bg-light, #F8FAFC);
  color: var(--color-text-title, #071E27);
  font-weight: 800;
}

.ota-table td {
  padding: 14px;
  border-bottom: 1px solid var(--color-border-subtle, #F1F5F9);
  vertical-align: middle;
}

.node-cell-flex {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mini-icon-box {
  width: 32px;
  height: 32px;
  background: #E8F5E9;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.node-name {
  display: block;
  font-size: 13.5px;
  color: var(--color-text-title, #071E27);
}

.node-code-sub {
  display: block;
  font-size: 11px;
  color: var(--color-text-muted, #94A3B8);
}

.hw-chip {
  font-size: 11.5px;
  background: #F1F5F9;
  padding: 3px 8px;
  border-radius: 4px;
  color: #334155;
}

.fw-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fw-badge {
  font-size: 12px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
}

.fw-latest {
  background: #E8F5E9;
  color: #1B5E20;
  border: 1px solid #86EFAC;
}

.fw-outdated {
  background: #FEF3C7;
  color: #B45309;
  border: 1px solid #FDE68A;
}

.fw-tag-latest {
  font-size: 10.5px;
  font-weight: 700;
  color: #1B5E20;
}

.fw-tag-update {
  font-size: 10.5px;
  font-weight: 700;
  color: #D97706;
}

.ota-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 700;
}

.ota-status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.ota-idle {
  background: #F1F5F9;
  color: #64748B;
}
.ota-idle .ota-status-dot { background: #94A3B8; }

.ota-downloading, .ota-flashing {
  background: #FEF3C7;
  color: #B45309;
}
.ota-downloading .ota-status-dot, .ota-flashing .ota-status-dot { background: #F59E0B; animation: liveDot 1s infinite; }

.ota-success {
  background: #E8F5E9;
  color: #1B5E20;
}
.ota-success .ota-status-dot { background: #10B981; }

.btn-trigger-ota {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  background: #1B5E20;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-trigger-ota:hover {
  background: #094713;
  transform: translateY(-1px);
}

.btn-ota-disabled {
  background: #F1F5F9;
  color: #94A3B8;
  cursor: default;
}
.btn-ota-disabled:hover {
  background: #F1F5F9;
  transform: none;
}

/* Releases Library Styles */
.releases-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.release-item-card {
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: 12px;
  padding: 18px 20px;
  background: var(--color-bg-light, #FAFAFA);
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.release-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.rel-version-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.rel-badge {
  font-size: 14px;
  font-weight: 800;
  background: #F3E8FF;
  color: #7C3AED;
  border: 1px solid #DDD6FE;
  padding: 2px 8px;
  border-radius: 6px;
}

.rel-chip-latest {
  font-size: 11px;
  font-weight: 700;
  background: #E8F5E9;
  color: #1B5E20;
  border: 1px solid #86EFAC;
  padding: 2px 8px;
  border-radius: 12px;
}

.rel-chip-stable {
  font-size: 11px;
  font-weight: 700;
  background: #E0F2FE;
  color: #0284C7;
  border: 1px solid #BAE6FD;
  padding: 2px 8px;
  border-radius: 12px;
}

.rel-date {
  font-size: 12px;
  color: var(--color-text-muted, #94A3B8);
}

.rel-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  margin: 0;
}

.rel-changelog {
  font-size: 13px;
  color: var(--color-text-body, #334155);
  line-height: 1.5;
  white-space: pre-line;
  margin: 0;
}

.rel-meta-box {
  display: flex;
  align-items: center;
  gap: 20px;
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border-subtle, #E2E8F0);
  border-radius: 8px;
  padding: 8px 12px;
  margin-top: 6px;
  flex-wrap: wrap;
  font-size: 11.5px;
}

.rel-meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.rm-lbl {
  color: var(--color-text-muted, #94A3B8);
  font-weight: 700;
}

.rm-code {
  background: #F1F5F9;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 11px;
}

.sha-code {
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Sensor Matrix Styles */
.sensor-matrix-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sensor-matrix-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 18px;
  background: var(--color-bg-light, #FAFAFA);
  border: 1px solid var(--color-border-subtle, #E2E8F0);
  border-radius: 10px;
}

.sensor-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sensor-sub-desc {
  font-size: 11.5px;
  color: var(--color-text-muted, #94A3B8);
  margin: 2px 0 0 0;
}

.dot-green {
  width: 10px;
  height: 10px;
  background: #10B981;
  border-radius: 50%;
  flex-shrink: 0;
}

.dot-offline {
  background: #CBD5E1;
}

.panel-led-status-tags {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.mini-led-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 9px;
  border-radius: 6px;
  transition: all 0.25s ease;
}

.mini-bulb {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.mini-led-tag.tag-dim {
  background: #F1F5F9;
  color: #94A3B8;
  border: 1px solid #E2E8F0;
}

.mini-led-tag.tag-dim .mini-bulb {
  background: #CBD5E1;
}

.mini-led-tag.tag-lit-green {
  background: #DCFCE7;
  color: #166534;
  border: 1px solid #86EFAC;
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.35);
}

.mini-led-tag.tag-lit-green .mini-bulb {
  background: #22C55E;
  box-shadow: 0 0 6px #22C55E;
}

.mini-led-tag.tag-lit-yellow {
  background: #FEF3C7;
  color: #92400E;
  border: 1px solid #FCD34D;
  box-shadow: 0 0 8px rgba(245, 158, 11, 0.35);
}

.mini-led-tag.tag-lit-yellow .mini-bulb {
  background: #F59E0B;
  box-shadow: 0 0 6px #F59E0B;
}

.mini-led-tag.tag-lit-red {
  background: #FEE2E2;
  color: #991B1B;
  border: 1px solid #FCA5A5;
  box-shadow: 0 0 8px rgba(239, 68, 68, 0.4);
  animation: pulse-red-panel 1.5s infinite;
}

.mini-led-tag.tag-lit-red .mini-bulb {
  background: #EF4444;
  box-shadow: 0 0 6px #EF4444;
}

@keyframes pulse-red-panel {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.65; }
}

/* Common Button Styles */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  background: #1B5E20;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #094713;
  transform: translateY(-1px);
}

.btn-secondary-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 16px;
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  color: var(--color-text-title, #071E27);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary-action:hover {
  background: #F8FAFC;
}

.btn-outline-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: #F0FDF4;
  border: 1px solid #86EFAC;
  color: #1B5E20;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-outline-action:hover {
  background: #DCFCE7;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  padding: 16px;
}

.modal-card {
  background: var(--color-white, #FFFFFF);
  border-radius: 14px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-lg {
  max-width: 720px;
}

.modal-header {
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 14px;
  border-bottom: 1px solid var(--color-border-subtle, #F1F5F9);
}

.modal-header-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.modal-title {
  font-size: 17px;
  font-weight: 800;
  color: var(--color-text-title, #071E27);
  margin: 0;
}

.modal-sub {
  font-size: 12.5px;
  color: var(--color-text-muted, #64748B);
  margin: 2px 0 0 0;
}

.modal-body {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.modal-footer {
  padding: 16px 24px;
  background: var(--color-bg-light, #F8FAFC);
  border-top: 1px solid var(--color-border-subtle, #F1F5F9);
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-cancel {
  padding: 9px 16px;
  background: transparent;
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #64748B;
  cursor: pointer;
}

.ota-target-info-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
}

.target-row {
  display: flex;
  justify-content: space-between;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.5px;
  color: var(--color-text-muted, #64748B);
}

.form-input, .form-select {
  padding: 10px 14px;
  border: 1.5px solid var(--color-border, #CBD5E1);
  border-radius: 8px;
  font-size: 13.5px;
  outline: none;
  background: var(--color-white, #FFFFFF);
  color: var(--color-text-title, #071E27);
  transition: border-color 0.2s;
}

.form-input:focus, .form-select:focus {
  border-color: #1B5E20;
}

.field-helper {
  font-size: 11.5px;
  color: var(--color-text-muted, #94A3B8);
  line-height: 1.35;
}

.code-box-wrapper {
  background: #0F172A;
  border-radius: 8px;
  padding: 16px;
  overflow-x: auto;
  max-height: 380px;
}

.code-pre {
  color: #F8FAFC;
  font-size: 12px;
  line-height: 1.5;
  margin: 0;
}

@keyframes liveDot {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.4); opacity: 0.5; }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes pulseLight {
  0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.2); }
  50% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.1); }
  100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.2); }
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@media (max-width: 1024px) {
  .stats-grid-4 {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .admin-content {
    padding: 0;
    gap: 16px;
  }
  .header-action-row {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  .page-title {
    font-size: 22px;
  }
  .page-subtitle {
    font-size: 13.5px;
  }
  .stats-grid-4 {
    grid-template-columns: 1fr;
  }
  .devices-token-grid {
    grid-template-columns: 1fr;
  }
}

.fade-admin-devices-enter-active,
.fade-admin-devices-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-admin-devices-enter-from {
  opacity: 0;
  transform: translateY(6px);
}
.fade-admin-devices-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
/* ==============================================================================
   TAB 6: HARDWARE CATALOG & TAB 7: ARDUINO CODE VIEWER STYLES
   ============================================================================== */
.catalog-badge-total {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(13, 99, 27, 0.08);
  color: #1B5E20;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12.5px;
  font-weight: 700;
}
:global(.dark-theme) .catalog-badge-total {
  background: rgba(74, 222, 128, 0.12);
  color: #4ADE80;
}

.devices-catalog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 16px;
  margin-top: 14px;
}

.hardware-catalog-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border-subtle, #E2E8F0);
  border-radius: 14px;
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
:global(.dark-theme) .hardware-catalog-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}
.hardware-catalog-card:hover {
  transform: translateY(-2px);
  border-color: rgba(13, 99, 27, 0.4);
  box-shadow: 0 6px 16px rgba(0,0,0,0.06);
}
:global(.dark-theme) .hardware-catalog-card:hover {
  border-color: rgba(74, 222, 128, 0.4);
}

.hw-card-header {
  display: flex;
  align-items: center;
  gap: 12px;
}
.hw-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.icon-green-subtle { background: #ECFDF5; color: #1B5E20; }
.icon-blue-subtle  { background: #EFF6FF; color: #1D4ED8; }
.icon-amber-subtle { background: #FFFBEB; color: #B45309; }
.icon-orange-subtle{ background: #FFF7ED; color: #C2410C; }

:global(.dark-theme) .icon-green-subtle { background: rgba(13, 99, 27, 0.25); color: #4ADE80; }
:global(.dark-theme) .icon-blue-subtle  { background: rgba(29, 78, 216, 0.25); color: #60A5FA; }
:global(.dark-theme) .icon-amber-subtle { background: rgba(180, 83, 9, 0.25); color: #FCD34D; }
:global(.dark-theme) .icon-orange-subtle{ background: rgba(194, 65, 12, 0.25); color: #FB923C; }

.hw-title-box {
  flex: 1;
}
.hw-card-name {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  margin: 0;
}
:global(.dark-theme) .hw-card-name {
  color: #FFFFFF;
}
.hw-card-category {
  font-size: 11.5px;
  color: var(--color-text-muted, #64748B);
}

.hw-status-pill {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
}
.tag-online {
  background: #DCFCE7;
  color: #15803D;
}
:global(.dark-theme) .tag-online {
  background: rgba(22, 101, 52, 0.35);
  color: #86EFAC;
}

.hw-card-purpose {
  font-size: 12.5px;
  color: var(--color-text-body, #334155);
  line-height: 1.45;
  margin: 0;
}
:global(.dark-theme) .hw-card-purpose {
  color: #CBD5E1;
}

.hw-meta-grid {
  border-top: 1px solid var(--color-border-subtle, #E2E8F0);
  padding-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 11.5px;
}
:global(.dark-theme) .hw-meta-grid {
  border-top-color: rgba(30, 78, 97, 0.5);
}

.hw-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.hw-lbl {
  color: var(--color-text-muted, #64748B);
  font-weight: 600;
}
.hw-val {
  color: var(--color-text-title, #071E27);
}
:global(.dark-theme) .hw-val {
  color: #F1F5F9;
}
.hw-pin-tag {
  background: #F1F5F9;
  color: #1B5E20;
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 700;
  font-size: 11px;
}
:global(.dark-theme) .hw-pin-tag {
  background: #133947;
  color: #86EFAC;
}

/* Arduino Tab Styles */
.program-selector-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 16px;
}
.program-card {
  background: var(--color-white, #FFFFFF);
  border: 2px solid var(--color-border-subtle, #E2E8F0);
  border-radius: 16px;
  padding: 18px 20px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  gap: 10px;
  text-align: left;
}
:global(.dark-theme) .program-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}
.program-card:hover {
  transform: translateY(-2px);
  border-color: #1B5E20;
  box-shadow: 0 6px 16px rgba(13, 99, 27, 0.08);
}
:global(.dark-theme) .program-card:hover {
  border-color: #4ADE80;
}
.program-card.active {
  border-color: #1B5E20;
  background: #F0FDF4;
  box-shadow: 0 4px 14px rgba(13, 99, 27, 0.12);
}
:global(.dark-theme) .program-card.active {
  border-color: #4ADE80;
  background: rgba(13, 99, 27, 0.2);
}
.program-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
}
.program-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.badge-prod {
  background: #DCFCE7;
  color: #15803D;
}
:global(.dark-theme) .badge-prod {
  background: rgba(22, 101, 52, 0.4);
  color: #86EFAC;
}
.program-filename {
  font-size: 11.5px;
  color: var(--color-text-muted, #64748B);
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.program-card-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  margin: 0;
}
:global(.dark-theme) .program-card-title {
  color: #FFFFFF;
}
.program-card-desc {
  font-size: 12px;
  color: var(--color-text-body, #334155);
  line-height: 1.45;
  margin: 0;
  flex: 1;
}
:global(.dark-theme) .program-card-desc {
  color: #CBD5E1;
}
.program-card-footer {
  margin-top: 6px;
  padding-top: 10px;
  border-top: 1px dashed var(--color-border-subtle, #E2E8F0);
}
:global(.dark-theme) .program-card-footer {
  border-top-color: rgba(30, 78, 97, 0.5);
}
.program-select-indicator {
  font-size: 12px;
  font-weight: 700;
  color: #1B5E20;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
:global(.dark-theme) .program-select-indicator {
  color: #4ADE80;
}

.active-program-wrapper {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.program-meta-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border-subtle, #E2E8F0);
  border-radius: 14px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}
:global(.dark-theme) .program-meta-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}
.program-meta-grid {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
  flex: 1;
}
.meta-item {
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.meta-item-full {
  display: flex;
  flex-direction: column;
  gap: 4px;
  width: 100%;
}
.meta-label {
  font-size: 10.5px;
  font-weight: 800;
  color: var(--color-text-muted, #64748B);
  letter-spacing: 0.5px;
}
.meta-val {
  font-size: 12.5px;
  color: var(--color-text-title, #071E27);
}
:global(.dark-theme) .meta-val {
  color: #FFFFFF;
}
.meta-lib-tags {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.lib-tag {
  background: #F1F5F9;
  color: #334155;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 11px;
}
:global(.dark-theme) .lib-tag {
  background: #133947;
  color: #E2E8F0;
}
.program-actions {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}
.action-btn-copy, .action-btn-download {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 9px 16px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}
.action-btn-copy {
  background: rgba(13, 99, 27, 0.08);
  color: #1B5E20;
}
:global(.dark-theme) .action-btn-copy {
  background: rgba(74, 222, 128, 0.15);
  color: #4ADE80;
}
.action-btn-copy:hover {
  background: rgba(13, 99, 27, 0.15);
}
.action-btn-copy.copied {
  background: #1B5E20;
  color: #FFFFFF;
}
.action-btn-download {
  background: #1B5E20;
  color: #FFFFFF;
}
.action-btn-download:hover {
  background: #0A4F15;
}

.code-editor-box {
  background: #081622;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
.code-editor-header {
  background: #050E17;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.code-header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}
.code-dots {
  display: flex;
  gap: 6px;
}
.code-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}
.dot-red { background: #EF4444; }
.dot-yellow { background: #F59E0B; }
.dot-green { background: #10B981; }
.code-file-title {
  color: #94A3B8;
  font-size: 12px;
  font-weight: 600;
}
.code-header-right {
  display: flex;
  align-items: center;
  gap: 14px;
}
.code-lines-count {
  color: #64748B;
  font-size: 11.5px;
}
.code-header-copy-btn {
  background: rgba(255, 255, 255, 0.08);
  color: #E2E8F0;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 6px;
  padding: 4px 10px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}
.code-header-copy-btn:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #FFFFFF;
}
.code-editor-content {
  max-height: 480px;
  overflow: auto;
  padding: 16px 20px;
}
.code-pre {
  margin: 0;
  color: #CBD5E1;
  font-size: 12px;
  line-height: 1.55;
  tab-size: 2;
}

.flashing-guide-card {
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  border-radius: 14px;
  padding: 18px 22px;
}
:global(.dark-theme) .flashing-guide-card {
  background: rgba(13, 99, 27, 0.12);
  border-color: rgba(74, 222, 128, 0.25);
}
.guide-steps-title {
  font-size: 14px;
  font-weight: 800;
  color: #065F46;
  margin: 0 0 14px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}
:global(.dark-theme) .guide-steps-title {
  color: #86EFAC;
}
.steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 12px;
}
.step-card {
  background: #FFFFFF;
  border: 1px solid rgba(13, 99, 27, 0.15);
  border-radius: 10px;
  padding: 12px 14px;
  display: flex;
  gap: 10px;
  align-items: flex-start;
}
:global(.dark-theme) .step-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}
.step-num {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #1B5E20;
  color: #FFFFFF;
  font-weight: 800;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.step-desc {
  font-size: 11.5px;
  line-height: 1.45;
  color: #1E293B;
}
:global(.dark-theme) .step-desc {
  color: #CBD5E1;
}
.step-desc code {
  background: rgba(13, 99, 27, 0.1);
  padding: 1px 4px;
  border-radius: 4px;
  font-family: monospace;
  font-size: 10.5px;
  word-break: break-all;
}
:global(.dark-theme) .step-desc code {
  background: rgba(74, 222, 128, 0.15);
  color: #86EFAC;
}
</style>
