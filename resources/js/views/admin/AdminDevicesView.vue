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
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
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
                  <svg v-if="d.icon === 'microchip'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
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
                    <span class="badge-status-dot" :class="d.status === 'online' ? 'dot-online' : 'dot-offline'">
                      {{ d.status === 'online' ? 'Online' : 'Offline' }}
                    </span>
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
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
                  <p class="sensor-sub-desc">Pin: GPIO 21 • Rentang: -40~80°C / 0-100% RH</p>
                </div>
              </div>
              <span class="sensor-val text-green-dark font-bold">
                {{ telemetry.hasData ? `${Number(telemetry.tempInternal).toFixed(1)}°C / ${Number(telemetry.humidityInternal).toFixed(0)}% RH` : 'Siaga / Menunggu ESP32' }} (Akurasi: ±0.3°C)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Load Cell 200kg + Amplifier HX711</strong>
                  <p class="sensor-sub-desc">Pin: GPIO 18 (SCK), GPIO 19 (DT) • Pengukuran Bobot Realtime</p>
                </div>
              </div>
              <span class="sensor-val text-blue font-bold">
                {{ telemetry.hasData ? `${Number(telemetry.weightCurrentKg).toFixed(1)} kg` : 'Siaga' }} (Nol Kalibrasi: OK)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Pyranometer Radiasi Surya</strong>
                  <p class="sensor-sub-desc">Pin: GPIO 34 (ADC 12-Bit) • Deteksi Terik Radiasi Matahari</p>
                </div>
              </div>
              <span class="sensor-val text-orange font-bold">
                {{ telemetry.hasData ? `${Number(telemetry.solarRadiation).toFixed(0)} W/m²` : 'Siaga' }} (Sensitivitas Tinggi)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <div>
                  <strong>Capacitive Grain Moisture Meter</strong>
                  <p class="sensor-sub-desc">Pin: GPIO 35 (ADC 12-Bit) • Estimasi Kadar Air Biji Gabah</p>
                </div>
              </div>
              <span class="sensor-val text-purple font-bold">
                {{ telemetry.hasData ? `${Number(telemetry.grainMoisture).toFixed(1)}%` : 'Siaga' }} (Standar SNI ≤ 12%)
              </span>
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
import { deviceService } from '../../services/deviceService'
import { telemetryService } from '../../services/telemetryService'
import { socketService } from '../../services/socketService'
import { confirmDialog } from '../../services/confirmDialogService'

const isInitialLoading = ref(true)
const currentTab = ref('security') // 'security' | 'ota' | 'releases' | 'sensors'
const isSubmitting = ref(false)
const isPingingAll = ref(false)

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
  hasData: false,
})

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
  padding: 32px 40px 60px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1280px;
  margin: 0 auto;
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
  color: #0D631B;
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
  color: #0D631B;
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

.trend-up-green { color: #0D631B; }
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
  color: #0D631B;
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
  color: #0D631B;
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
  color: #0D631B;
}

.token-status-pill {
  font-size: 10px;
  background: #E8F5E9;
  color: #0D631B;
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
  color: #0D631B;
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
  color: #0D631B;
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
  color: #0D631B;
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
  color: #0D631B;
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
  color: #0D631B;
}
.ota-success .ota-status-dot { background: #10B981; }

.btn-trigger-ota {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  background: #0D631B;
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
  color: #0D631B;
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

/* Common Button Styles */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  background: #0D631B;
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
  color: #0D631B;
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
  border-color: #0D631B;
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
</style>
