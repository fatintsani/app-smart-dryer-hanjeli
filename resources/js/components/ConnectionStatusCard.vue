<template>
  <div class="connection-status-card" :class="{ 'is-collapsed': !isCardExpanded }">
    <!-- Mobile-Only Compact Dropdown Header / Trigger Bar -->
    <div 
      class="mobile-dropdown-header" 
      @click="toggleCard" 
      role="button" 
      tabindex="0" 
      @keydown.enter.prevent="toggleCard" 
      @keydown.space.prevent="toggleCard"
    >
      <div class="mobile-header-left">
        <div class="mobile-mode-icon-circle" :class="sourceMode === 'simulation' ? 'sim' : activeMode">
          <!-- Simulation Icon -->
          <svg v-if="sourceMode === 'simulation'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
            <path d="M10 2v7.31"></path>
            <path d="M14 9.3V2"></path>
            <path d="M8.5 2h7"></path>
            <path d="M14 9.3a6.5 6.5 0 1 1-4 0"></path>
            <path d="M5.52 16h12.96"></path>
          </svg>
          <!-- Wi-Fi Icon -->
          <svg v-else-if="activeMode === 'wifi'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
            <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
            <line x1="12" y1="20" x2="12.01" y2="20"></line>
          </svg>
          <!-- Bluetooth Icon -->
          <svg v-else-if="activeMode === 'ble'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
          </svg>
          <!-- USB Icon -->
          <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <rect x="7" y="2" width="10" height="7" rx="1.5"></rect>
            <line x1="9" y1="2" x2="9" y2="5"></line>
            <line x1="15" y1="2" x2="15" y2="5"></line>
            <path d="M12 9v7"></path>
            <rect x="8" y="16" width="8" height="6" rx="2"></rect>
          </svg>
        </div>

        <div class="mobile-header-info">
          <div class="mobile-title-row">
            <span class="mobile-card-title">{{ $t('connectionCard.sourceLabel') }}</span>
            <span class="mobile-mode-badge" :class="sourceMode === 'simulation' ? 'badge-sim' : 'badge-hw'">
              {{ sourceMode === 'simulation' ? $t('connectionCard.modeSimulation') : (activeMode === 'wifi' ? 'Wi-Fi Live' : (activeMode === 'ble' ? 'BLE Live' : 'USB Live')) }}
            </span>
          </div>
          <div class="mobile-status-sub-row">
            <span class="mobile-status-dot" :class="statusDotClass"></span>
            <span class="mobile-status-text" :class="statusTextClass">
              {{ sourceMode === 'simulation' ? $t('connectionCard.generatorRunning') : formattedStatus }}
            </span>
            <span v-if="sourceMode === 'hardware' && (connectionState.deviceId || connectionState.hotspotName)" class="mobile-dev-id">
              • {{ connectionState.deviceId || connectionState.hotspotName }}
            </span>
          </div>
        </div>
      </div>

      <div class="mobile-header-right">
        <button 
          type="button" 
          class="mobile-dropdown-toggle-pill" 
          :aria-expanded="isCardExpanded"
          :aria-label="isCardExpanded ? $t('connectionCard.dropdownOpen') : $t('connectionCard.dropdownClosed')"
          :title="isCardExpanded ? $t('connectionCard.dropdownOpen') : $t('connectionCard.dropdownClosed')"
        >
          <svg 
            class="chevron-icon" 
            :class="{ 'rotate-180': isCardExpanded }" 
            width="16" 
            height="16" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="2.5" 
            stroke-linecap="round" 
            stroke-linejoin="round"
          >
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
      </div>
    </div>

    <!-- 1. Top Section: Sumber Data Sensor IoT Switcher (Mode Alat Fisik Live vs Mode Simulasi IoT) -->
    <div class="source-mode-bar" :class="{ 'is-collapsed': !isCardExpanded }">
      <div class="source-mode-info">
        <div class="source-title-row">
          <span class="source-label">{{ $t('connectionCard.sourceLabel') }}</span>
          <span class="source-mode-badge" :class="sourceMode === 'simulation' ? 'badge-sim' : 'badge-hw'">
            <span class="badge-dot" :class="statusDotClass"></span>
            <span>{{ sourceMode === 'simulation' ? $t('connectionCard.modeSimulation') : (activeMode === 'wifi' ? 'Wi-Fi Live' : (activeMode === 'ble' ? 'BLE Live' : 'USB Live')) }}</span>
          </span>
        </div>
        <p class="source-sub">{{ $t('connectionCard.sourceSub') }}</p>
      </div>

      <div class="source-control-container">
        <div class="source-control-actions">
          <div class="source-segmented-control" :class="{ 'control-locked': !isAdmin }">
            <button 
              type="button" 
              class="source-segment-btn" 
              :class="{ active: sourceMode === 'hardware', 'btn-locked': !isAdmin }"
              :disabled="!isAdmin"
              :title="isAdmin ? $t('connectionCard.tooltipPhysical') : $t('connectionCard.tooltipAdminOnly')"
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
              <span>{{ $t('connectionCard.modePhysical') }}</span>
            </button>

            <button 
              type="button" 
              class="source-segment-btn" 
              :class="{ active: sourceMode === 'simulation', 'btn-locked': !isAdmin }"
              :disabled="!isAdmin"
              :title="isAdmin ? $t('connectionCard.tooltipSimulation') : $t('connectionCard.tooltipAdminOnly')"
              @click="switchSourceMode('simulation')"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                <path d="M10 2v7.31"></path>
                <path d="M14 9.3V2"></path>
                <path d="M8.5 2h7"></path>
                <path d="M14 9.3a6.5 6.5 0 1 1-4 0"></path>
                <path d="M5.52 16h12.96"></path>
              </svg>
              <span>{{ $t('connectionCard.modeSimulation') }}</span>
            </button>
          </div>

          <!-- Dropdown / Collapse Button for Card Body -->
          <button 
            type="button" 
            class="card-toggle-dropdown-btn" 
            :class="{ 'is-open': isCardExpanded }"
            @click="toggleCard" 
            :aria-expanded="isCardExpanded"
            :title="isCardExpanded ? $t('connectionCard.collapseCard') : $t('connectionCard.expandCard')"
          >
            <span class="toggle-btn-label">{{ isCardExpanded ? $t('connectionCard.collapseCard') : $t('connectionCard.expandCard') }}</span>
            <svg 
              class="toggle-chevron-icon" 
              :class="{ 'rotate-180': isCardExpanded }" 
              width="15" 
              height="15" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2.5" 
              stroke-linecap="round" 
              stroke-linejoin="round"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
        </div>

        <transition name="fade">
          <div v-if="permissionWarning" class="permission-alert-tooltip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>{{ $t('connectionCard.permissionWarning') }}</span>
          </div>
        </transition>
      </div>
    </div>

    <!-- Expandable / Collapsible Body -->
    <div class="card-collapsible-body" :class="{ 'is-collapsed': !isCardExpanded }">
      <!-- 2. Multi-Connectivity Selector & Status (When in Hardware Live Mode) -->
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
            <svg v-else-if="activeMode === 'ble'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
            </svg>
            <!-- USB Serial Cable Icon -->
            <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
              <rect x="7" y="2" width="10" height="7" rx="1.5"></rect>
              <line x1="9" y1="2" x2="9" y2="5"></line>
              <line x1="15" y1="2" x2="15" y2="5"></line>
              <path d="M12 9v7"></path>
              <rect x="8" y="16" width="8" height="6" rx="2"></rect>
            </svg>
          </div>
          <div class="title-texts">
            <h3 class="card-title">{{ $t('connectionCard.hwTitle') }}</h3>
            <p class="card-subtitle">{{ $t('connectionCard.hwSub') }}</p>
          </div>
        </div>

        <!-- Segmented Mode Selector Control (Wi-Fi vs BLE vs USB) -->
        <div class="mode-segmented-control">
          <button 
            type="button" 
            class="mode-segment-btn" 
            :class="{ active: activeMode === 'wifi' }"
            @click="switchMode('wifi')"
            :title="$t('connectionCard.tooltipWifi')"
          >
            <span class="dot-indicator wifi-dot"></span>
            <span>{{ $t('connectionCard.tabWifi') }}</span>
          </button>

          <button 
            type="button" 
            class="mode-segment-btn" 
            :class="{ active: activeMode === 'ble' }"
            @click="switchMode('ble')"
            :title="$t('connectionCard.tooltipBle')"
          >
            <span class="dot-indicator ble-dot"></span>
            <span>{{ $t('connectionCard.tabBle') }}</span>
          </button>

          <button 
            type="button" 
            class="mode-segment-btn" 
            :class="{ active: activeMode === 'usb' }"
            @click="switchMode('usb')"
            :title="$t('connectionCard.tooltipUsb')"
          >
            <span class="dot-indicator usb-dot"></span>
            <span>{{ $t('connectionCard.tabUsb') }}</span>
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
          <strong>{{ $t('connectionCard.espNotConnected') }}</strong>
          <span>{{ $t('connectionCard.espNotConnectedDesc') }}</span>
        </div>
      </div>

      <!-- 4 Info Metrics Grid -->
      <div class="connection-grid">
        <!-- 1. Status Box -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <span class="info-label">{{ $t('connectionCard.statusLabel') }}</span>
          <div class="status-pill-row">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" :class="isHardwareConnected ? 'text-green-icon' : (connectionState.isConnecting ? 'text-amber-icon' : 'text-muted-icon')">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            <span class="status-value-text" :class="statusTextClass">
              {{ formattedStatus }}
            </span>
          </div>
          <span class="info-meta-sub">
            {{ isHardwareConnected ? (activeMode === 'wifi' ? 'Wi-Fi MQTT Ingestion' : (activeMode === 'ble' ? 'BLE GATT Direct' : 'USB Serial Stream')) : $t('connectionCard.dataStandby') }}
          </span>
        </div>

        <!-- 2. Device ID Box with Edit Action -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <div class="info-header-row">
            <span class="info-label">{{ $t('connectionCard.deviceLabel') }}</span>
            <button 
              type="button" 
              class="btn-edit-device" 
              @click="openDeviceEditModal" 
              :title="$t('connectionCard.editDeviceTitle')"
            >
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
              </svg>
              <span>{{ $t('connectionCard.edit') }}</span>
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
            {{ isHardwareConnected ? $t('connectionCard.hwDetected') : $t('connectionCard.targetDevice') }}
          </span>
        </div>

        <!-- 3. Sinyal & Saluran Transmisi (Konek Lewat Apa) with Icons -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <span class="info-label">
            {{ activeMode === 'wifi' ? $t('connectionCard.connectViaWifi') : (activeMode === 'ble' ? $t('connectionCard.connectViaBle') : $t('connectionCard.connectViaUsb')) }}
          </span>
          <div class="transport-value">
            <div v-if="activeMode === 'wifi'" class="transport-badge">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :class="isHardwareConnected ? 'text-green-icon' : 'text-muted-icon'">
                <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
              </svg>
              <span>{{ isHardwareConnected ? $t('connectionCard.wifiConnected') : $t('connectionCard.wifiOffline') }}</span>
            </div>
            <div v-else-if="activeMode === 'ble'" class="transport-badge">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :class="isHardwareConnected ? 'text-blue-icon' : 'text-muted-icon'">
                <polyline points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"></polyline>
              </svg>
              <span>{{ isHardwareConnected ? $t('connectionCard.bleConnected') : (isBleSupported ? $t('connectionCard.bleReady') : $t('connectionCard.unsupportedBrowser')) }}</span>
            </div>
            <div v-else class="transport-badge">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :class="isHardwareConnected ? 'text-purple-icon' : 'text-muted-icon'">
                <rect x="7" y="2" width="10" height="7" rx="1.5"></rect>
                <path d="M12 9v7"></path>
                <rect x="8" y="16" width="8" height="6" rx="2"></rect>
              </svg>
              <span>{{ isHardwareConnected ? `${$t('connectionCard.usbConnected')} (${connectionState.transportDetails.usbBaudRate || 115200} bps)` : (isUsbSupported ? $t('connectionCard.usbReady') : $t('connectionCard.unsupportedBrowser')) }}</span>
            </div>
          </div>
          <span class="info-meta-sub text-truncate">
            <template v-if="activeMode === 'wifi'">
              Hotspot: <strong>{{ connectionState.hotspotName || connectionState.deviceId || 'OPPO Reno' }}</strong>
            </template>
            <template v-else-if="activeMode === 'ble'">
              Target: <strong>{{ connectionState.deviceId || 'ESP32 BLE' }}</strong>
            </template>
            <template v-else>
              Port: <strong>{{ connectionState.transportDetails.usbPortInfo?.usbVendorId || 'Port COM / USB' }}</strong> ({{ connectionState.transportDetails.usbBaudRate || 115200 }} baud)
            </template>
          </span>
        </div>

        <!-- 4. Data Terakhir & Status Sinkronisasi -->
        <div class="info-box" :class="{ 'box-standby': !isHardwareConnected }">
          <span class="info-label">{{ $t('connectionCard.lastDataLabel') }}</span>
          <div class="last-data-val">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="isHardwareConnected ? 'text-green-icon' : 'text-muted-icon'">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>{{ isHardwareConnected ? lastDataText : $t('connectionCard.waitingEsp') }}</span>
          </div>
          <span class="info-meta-sub">
            {{ isHardwareConnected ? $t('connectionCard.syncRealtime') : $t('connectionCard.dataStandby') }}
          </span>
        </div>
      </div>

      <!-- Wi-Fi Action Toolbar (Visible in Wi-Fi mode) -->
      <div v-if="activeMode === 'wifi'" class="wifi-action-toolbar">
        <div class="ble-toolbar-row">
          <div class="ble-helper-text">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span v-if="connectionState.isConnected">
              {{ $t('connectionCard.wifiHelperConnected') }} (<strong>{{ connectionState.hotspotName || connectionState.deviceId || 'OPPO Reno' }}</strong>) via Reverb & MQTT Ingestion.
            </span>
            <span v-else>
              {{ $t('connectionCard.wifiHelperNotConnected') }}
            </span>
          </div>

          <div class="ble-btn-group">
            <!-- Setup Wi-Fi Button -->
            <button 
              type="button" 
              class="btn-ble-action btn-ble-setup"
              @click="$emit('open-wifi-setup')"
              :title="$t('connectionCard.setupWifiTitle')"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
              <span>{{ $t('connectionCard.setupWifi') }}</span>
            </button>
          </div>
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
          <span>{{ $t('connectionCard.bleUnsupported') }}</span>
        </div>

        <div v-else class="ble-toolbar-row">
          <div class="ble-helper-text">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span v-if="connectionState.isConnected">
              {{ $t('connectionCard.bleHelperConnected') }} (<strong>{{ connectionState.deviceId }}</strong>).
            </span>
            <span v-else>
              {{ $t('connectionCard.bleHelperNotConnected') }}
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
              <span>{{ connectionState.isConnecting ? $t('connectionCard.searchingBle') : $t('connectionCard.connectBle') }}</span>
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
              <span>{{ $t('connectionCard.disconnectBle') }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- USB Serial Action Toolbar (Visible in USB Serial mode) -->
      <div v-if="activeMode === 'usb'" class="usb-action-toolbar">
        <div v-if="!isUsbSupported" class="ble-unsupported-alert">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <span>{{ $t('connectionCard.usbUnsupported') }}</span>
        </div>

        <div v-else class="ble-toolbar-row">
          <div class="ble-helper-text">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span v-if="connectionState.isConnected">
              {{ $t('connectionCard.usbHelperConnected') }} ({{ connectionState.transportDetails.usbBaudRate || 115200 }} baud).
            </span>
            <span v-else>
              {{ $t('connectionCard.usbHelperNotConnected') }}
            </span>
          </div>

          <div class="ble-btn-group">
            <div class="baud-selector-wrap" v-if="!connectionState.isConnected">
              <select v-model="selectedBaudRate" class="baud-select" :title="$t('connectionCard.baudSelectTitle')">
                <option :value="115200">{{ $t('connectionCard.baudStandard') }}</option>
                <option :value="57600">57600 bps</option>
                <option :value="9600">9600 bps</option>
                <option :value="230400">230400 bps (Fast)</option>
              </select>
            </div>

            <!-- Connect / Disconnect USB -->
            <button 
              v-if="!connectionState.isConnected" 
              type="button" 
              class="btn-ble-action btn-usb-connect"
              :disabled="connectionState.isConnecting"
              @click="handleUsbConnect"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="7" y="2" width="10" height="7" rx="1.5"></rect>
                <path d="M12 9v7"></path>
                <rect x="8" y="16" width="8" height="6" rx="2"></rect>
              </svg>
              <span>{{ connectionState.isConnecting ? $t('connectionCard.openingUsb') : $t('connectionCard.connectUsb') }}</span>
            </button>

            <button 
              v-else 
              type="button" 
              class="btn-ble-action btn-ble-disconnect"
              @click="handleUsbDisconnect"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
              <span>{{ $t('connectionCard.disconnectUsb') }}</span>
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
          <h4 class="sim-heading">{{ $t('connectionCard.simActiveHeading') }}</h4>
          <p class="sim-caption">{{ $t('connectionCard.simActiveDesc') }}</p>
        </div>
      </div>
      <div class="sim-badge-wrap">
        <span class="sim-live-badge">
          <span class="sim-dot-pulse"></span>
          <span>{{ $t('connectionCard.generatorRunning') }}</span>
        </span>
      </div>
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
                <h3 class="device-modal-title">{{ $t('connectionCard.modalTitle') }}</h3>
                <p class="device-modal-subtitle">{{ $t('connectionCard.modalSub') }}</p>
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
                <label class="field-label">{{ $t('connectionCard.fieldName') }}</label>
                <div class="input-wrap">
                  <input 
                    v-model="editDeviceForm.deviceId" 
                    type="text" 
                    class="device-input" 
                    :placeholder="$t('connectionCard.placeholderName')" 
                    required 
                  />
                </div>
                <!-- Quick Preset Chips -->
                <div class="preset-chips-row">
                  <span class="preset-label">{{ $t('connectionCard.quickPresets') }}</span>
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
                <label class="field-label">{{ $t('connectionCard.fieldHotspot') }}</label>
                <div class="input-wrap">
                  <input 
                    v-model="editDeviceForm.hotspotName" 
                    type="text" 
                    class="device-input" 
                    :placeholder="$t('connectionCard.placeholderHotspot')" 
                  />
                </div>
                <p class="field-helper">{{ $t('connectionCard.hotspotHelper') }}</p>
              </div>

              <!-- Feedback / Info Banner -->
              <div class="info-helper-box">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>{{ $t('connectionCard.modalNotice') }}</span>
              </div>

              <div class="modal-footer-row">
                <button type="button" class="btn-modal-cancel" @click="isDeviceEditOpen = false">
                  {{ $t('connectionCard.btnCancel') }}
                </button>
                <button type="submit" class="btn-modal-save">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>{{ $t('connectionCard.btnSave') }}</span>
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
import { useI18n } from '../i18n';
import connectionManager from '../services/connectionManager';
import { settingsService } from '../services/settingsService';
import bleService from '../services/ble/bleService';
import usbSerialService from '../services/serial/usbSerialService';
import authService from '../services/authService';

const { t } = useI18n();
const emit = defineEmits(['open-wifi-setup']);

const connectionState = connectionManager.state;
const sourceMode = computed(() => connectionState.sourceMode);
const activeMode = computed(() => connectionState.mode);
const isBleSupported = computed(() => bleService.isSupported());
const isUsbSupported = computed(() => usbSerialService.isSupported());
const isHardwareConnected = computed(() => connectionState.isHardwareActive);
const selectedBaudRate = ref(115200);

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

onMounted(async () => {
  try {
    const saved = localStorage.getItem('iot_source_card_expanded');
    if (saved !== null) {
      isCardExpanded.value = saved === 'true';
    }
  } catch (e) {}

  // Always fetch latest authoritative system settings from backend database
  await settingsService.getSettings();

  timer = setInterval(() => {
    now.value = Date.now();
  }, 5000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
  if (permTimer) clearTimeout(permTimer);
});

const switchSourceMode = async (mode) => {
  if (!isAdmin.value) {
    permissionWarning.value = true;
    if (permTimer) clearTimeout(permTimer);
    permTimer = setTimeout(() => {
      permissionWarning.value = false;
    }, 4000);
    return;
  }
  try {
    await settingsService.setIotMode(mode.toUpperCase());
  } catch (e) {
    console.warn('Failed to persist mode to backend:', e);
  }
  connectionManager.setSourceMode(mode);
};

const switchMode = (mode) => {
  connectionManager.setConnectionMode(mode);
};

const handleBleConnect = async () => {
  try {
    connectionManager.state.sourceMode = 'hardware';
    connectionManager.state.mode = 'ble';
    await connectionManager.connect();
  } catch (err) {
    console.warn('[BLE UI] Connection error/cancelled:', err);
  }
};

const handleBleDisconnect = async () => {
  await connectionManager.disconnect();
};

const handleUsbConnect = async () => {
  try {
    connectionManager.state.sourceMode = 'hardware';
    connectionManager.state.mode = 'usb';
    connectionManager.state.transportDetails.usbBaudRate = selectedBaudRate.value;
    await connectionManager.connect({ baudRate: selectedBaudRate.value });
  } catch (err) {
    console.warn('[USB UI] Connection error/cancelled:', err);
  }
};

const handleUsbDisconnect = async () => {
  await connectionManager.disconnect();
};

const formattedStatus = computed(() => {
  if (isHardwareConnected.value) return t('connectionCard.statusConnected');
  if (connectionState.isConnecting) return t('connectionCard.statusConnecting');
  if (activeMode.value === 'ble' && isBleSupported.value) return t('connectionCard.bleReady');
  if (activeMode.value === 'usb' && isUsbSupported.value) return t('connectionCard.usbReady');
  return t('connectionCard.statusNotConnected');
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
  if (!connectionState.lastDataTimestamp) return t('connectionCard.waitingEsp');
  const diffSec = Math.floor((now.value - new Date(connectionState.lastDataTimestamp).getTime()) / 1000);
  if (diffSec < 5) return t('connectionCard.timeJustNow');
  if (diffSec < 60) return `${diffSec} ${t('connectionCard.timeSecondsAgo')}`;
  return `${Math.floor(diffSec / 60)} ${t('connectionCard.timeMinutesAgo')}`;
});

// Dropdown Card Collapse Toggle State (persisted locally so operator preference is remembered)
const isCardExpanded = ref(true);

const toggleCard = () => {
  isCardExpanded.value = !isCardExpanded.value;
  try {
    localStorage.setItem('iot_source_card_expanded', isCardExpanded.value ? 'true' : 'false');
  } catch (e) {}
};

const statusDotClass = computed(() => {
  if (sourceMode.value === 'simulation' || isHardwareConnected.value) return 'dot-green';
  if (connectionState.isConnecting) return 'dot-amber';
  return 'dot-gray';
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
  background: #0B242F;
  border-color: #1E4E61;
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
  transition: all 0.25s ease;
}

.connection-status-card.is-collapsed .source-mode-bar {
  border-bottom: none;
  padding-bottom: 0;
}

:global(.dark-theme) .source-mode-bar {
  border-bottom-color: #1E4E61;
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
  flex-wrap: wrap;
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

.source-mode-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
  letter-spacing: 0.02em;
}

.source-mode-badge.badge-hw {
  background: #DCFCE7;
  color: #166534;
}

.source-mode-badge.badge-sim {
  background: #FEF3C7;
  color: #92400E;
}

:global(.dark-theme) .source-mode-badge.badge-hw {
  background: rgba(34, 197, 94, 0.2);
  color: #86EFAC;
}

:global(.dark-theme) .source-mode-badge.badge-sim {
  background: rgba(245, 158, 11, 0.2);
  color: #FDE68A;
}

.badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.badge-dot.dot-green {
  background: #22C55E;
  box-shadow: 0 0 5px rgba(34, 197, 94, 0.6);
}

.badge-dot.dot-amber {
  background: #F59E0B;
  box-shadow: 0 0 5px rgba(245, 158, 11, 0.6);
}

.badge-dot.dot-gray {
  background: #94A3B8;
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

.source-control-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

/* Card Toggle Dropdown Button */
.card-toggle-dropdown-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  background: var(--color-bg-light, #F1F5F9);
  border: 1px solid rgba(203, 213, 225, 0.7);
  color: var(--color-text-body, #334155);
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  user-select: none;
}

.card-toggle-dropdown-btn:hover {
  background: #E2E8F0;
  color: var(--color-primary-dark, #0D631B);
  border-color: rgba(13, 99, 27, 0.35);
  transform: translateY(-1px);
}

:global(.dark-theme) .card-toggle-dropdown-btn {
  background: #0E2C39;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .card-toggle-dropdown-btn:hover {
  background: #153E50;
  color: #6EE7B7;
  border-color: #2E7D32;
}

.toggle-chevron-icon {
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.toggle-chevron-icon.rotate-180 {
  transform: rotate(180deg);
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

.mode-icon-circle.usb {
  background: rgba(147, 51, 234, 0.1);
  color: #9333EA;
}

:global(.dark-theme) .mode-icon-circle.usb {
  background: rgba(147, 51, 234, 0.25);
  color: #C084FC;
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

/* Segmented Control (Wi-Fi vs BLE vs USB) */
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
.usb-dot { background: #9333EA; }

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
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .info-box.box-standby {
  background: rgba(7, 30, 39, 0.6);
  border-color: rgba(30, 78, 97, 0.4);
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

/* Wi-Fi & BLE Action Toolbar */
.wifi-action-toolbar,
.ble-action-toolbar {
  padding-top: 10px;
  border-top: 1px dashed var(--color-border-subtle, rgba(191, 202, 186, 0.4));
}

:global(.dark-theme) .wifi-action-toolbar,
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

:global(.dark-theme) .btn-ble-setup {
  background: rgba(74, 222, 128, 0.12);
  border-color: rgba(74, 222, 128, 0.35);
  color: #4ADE80;
}

:global(.dark-theme) .btn-ble-setup:hover {
  background: rgba(74, 222, 128, 0.22);
}

/* USB Action Toolbar & Buttons */
.usb-action-toolbar {
  padding-top: 10px;
  border-top: 1px dashed rgba(147, 51, 234, 0.35);
}

:global(.dark-theme) .usb-action-toolbar {
  border-top-color: rgba(192, 132, 252, 0.3);
}

.btn-usb-connect {
  background: #9333EA;
  color: #FFFFFF;
}

.btn-usb-connect:hover:not(:disabled) {
  background: #7E22CE;
}

.btn-usb-connect:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.baud-selector-wrap {
  display: flex;
  align-items: center;
}

.baud-select {
  padding: 5px 8px;
  border-radius: 7px;
  font-size: 11.5px;
  font-weight: 600;
  border: 1px solid var(--color-border-subtle, rgba(203, 213, 225, 0.8));
  background: var(--color-white, #FFFFFF);
  color: var(--color-text-main, #071E27);
  outline: none;
  cursor: pointer;
}

:global(.dark-theme) .baud-select {
  background: #071E27;
  border-color: #1E4E61;
  color: #F8FAFC;
}

.text-purple-icon {
  color: #9333EA;
  flex-shrink: 0;
}

:global(.dark-theme) .text-purple-icon {
  color: #C084FC;
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

/* Card Collapsible Body (smooth open & close animation) */
.card-collapsible-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  overflow: hidden;
  transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, margin 0.25s ease;
  max-height: 2500px;
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

.card-collapsible-body.is-collapsed {
  max-height: 0;
  opacity: 0;
  margin: 0;
  padding: 0;
  visibility: hidden;
  pointer-events: none;
}

/* Mobile Collapsible Dropdown Header (Hidden on Desktop) */
.mobile-dropdown-header {
  display: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .connection-status-card {
    padding: 12px 14px;
    gap: 0;
    border-radius: 12px;
  }

  .mobile-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    cursor: pointer;
    user-select: none;
    padding: 2px 0;
    background: transparent;
    border: none;
    outline: none;
  }

  .mobile-dropdown-header:focus-visible {
    outline: 2px solid var(--color-primary-dark, #0D631B);
    outline-offset: 2px;
    border-radius: 8px;
  }

  .mobile-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
  }

  .mobile-mode-icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(13, 99, 27, 0.1);
    color: var(--color-primary-dark, #0D631B);
    transition: all 0.2s ease;
  }

  .mobile-mode-icon-circle.wifi {
    background: rgba(13, 99, 27, 0.1);
    color: #0D631B;
  }

  .mobile-mode-icon-circle.ble {
    background: rgba(2, 132, 199, 0.12);
    color: #0284C7;
  }

  .mobile-mode-icon-circle.usb {
    background: rgba(147, 51, 234, 0.12);
    color: #9333EA;
  }

  .mobile-mode-icon-circle.sim {
    background: rgba(245, 158, 11, 0.12);
    color: #D97706;
  }

  :global(.dark-theme) .mobile-mode-icon-circle.wifi {
    background: rgba(74, 222, 128, 0.15);
    color: #4ADE80;
  }

  :global(.dark-theme) .mobile-mode-icon-circle.ble {
    background: rgba(56, 189, 248, 0.15);
    color: #38BDF8;
  }

  :global(.dark-theme) .mobile-mode-icon-circle.usb {
    background: rgba(192, 132, 252, 0.15);
    color: #C084FC;
  }

  :global(.dark-theme) .mobile-mode-icon-circle.sim {
    background: rgba(251, 191, 36, 0.15);
    color: #FBBF24;
  }

  .mobile-header-info {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
    flex: 1;
  }

  .mobile-title-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .mobile-card-title {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.04em;
    color: var(--color-text-title, #071E27);
  }

  :global(.dark-theme) .mobile-card-title {
    color: #FFFFFF;
  }

  .mobile-mode-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 6px;
    letter-spacing: 0.02em;
  }

  .mobile-mode-badge.badge-hw {
    background: #DCFCE7;
    color: #166534;
  }

  .mobile-mode-badge.badge-sim {
    background: #FEF3C7;
    color: #92400E;
  }

  :global(.dark-theme) .mobile-mode-badge.badge-hw {
    background: rgba(34, 197, 94, 0.2);
    color: #86EFAC;
  }

  :global(.dark-theme) .mobile-mode-badge.badge-sim {
    background: rgba(245, 158, 11, 0.2);
    color: #FDE68A;
  }

  .mobile-status-sub-row {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: var(--color-text-muted, rgba(64, 73, 61, 0.7));
  }

  :global(.dark-theme) .mobile-status-sub-row {
    color: #94A3B8;
  }

  .mobile-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .mobile-status-dot.dot-green {
    background: #22C55E;
    box-shadow: 0 0 6px rgba(34, 197, 94, 0.6);
  }

  .mobile-status-dot.dot-amber {
    background: #F59E0B;
    box-shadow: 0 0 6px rgba(245, 158, 11, 0.6);
  }

  .mobile-status-dot.dot-gray {
    background: #94A3B8;
  }

  .mobile-status-text {
    font-weight: 600;
  }

  .mobile-dev-id {
    font-size: 10.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    opacity: 0.85;
  }

  .mobile-header-right {
    flex-shrink: 0;
    margin-left: 8px;
  }

  .mobile-dropdown-toggle-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 8px;
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  .mobile-dropdown-toggle-pill:hover {
    background: #E2E8F0;
    color: #0F172A;
  }

  :global(.dark-theme) .mobile-dropdown-toggle-pill {
    background: #0E2C39;
    border-color: #1E4E61;
    color: #94A3B8;
  }

  :global(.dark-theme) .mobile-dropdown-toggle-pill:hover {
    background: #153E50;
    color: #F1F5F9;
  }

  .chevron-icon {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .chevron-icon.rotate-180 {
    transform: rotate(180deg);
  }

  /* When collapsed on mobile */
  .connection-status-card.is-collapsed .source-mode-bar {
    display: none;
  }

  .source-mode-bar {
    flex-direction: column;
    align-items: flex-start;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(203, 213, 225, 0.4);
  }

  :global(.dark-theme) .source-mode-bar {
    border-top-color: #1E4E61;
  }

  .source-control-container {
    width: 100%;
    align-items: stretch;
  }

  .source-control-actions {
    width: 100%;
  }

  .source-segmented-control {
    flex: 1;
    width: 100%;
  }

  .source-segment-btn {
    flex: 1;
    justify-content: center;
  }

  .card-toggle-dropdown-btn {
    display: none;
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
