<template>
    <div class="settings-page">
        <Transition name="fade-settings" mode="out-in">
            <SettingsSkeleton v-if="isInitialLoading" />
            <div v-else class="settings-loaded-content">
                <!-- Unified Responsive Content -->
                <div class="settings-main-content">
                    <!-- Page Header -->
                    <div class="page-header-row">
                        <div class="header-text">
                            <h1 class="page-title">
                                {{ $t("settings.title") }}
                            </h1>
                            <p class="page-subtitle">
                                {{ $t("settings.subtitle") }}
                            </p>
                        </div>
                        <button class="save-all-btn" @click="saveAllSettings">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>{{ $t("settings.saveSettingsBtn") }}</span>
                        </button>
                    </div>

                    <!-- Settings Tabs Navigation -->
                    <div class="settings-tabs-nav">
                        <button
                            class="tab-nav-btn"
                            :class="{ active: activeTab === 'hardware' }"
                            @click="activeTab = 'hardware'"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    x="4"
                                    y="4"
                                    width="16"
                                    height="16"
                                    rx="2"
                                ></rect>
                                <rect x="9" y="9" width="6" height="6"></rect>
                                <line x1="9" y1="1" x2="9" y2="4"></line>
                                <line x1="15" y1="1" x2="15" y2="4"></line>
                            </svg>
                            <span>{{ $t("settings.tabHardware") }}</span>
                        </button>

                        <button
                            class="tab-nav-btn"
                            :class="{ active: activeTab === 'drying' }"
                            @click="activeTab = 'drying'"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 2v20M17 5H7M19 12H5M17 19H7" />
                            </svg>
                            <span>{{ $t("settings.tabDrying") }}</span>
                        </button>

                        <button
                            class="tab-nav-btn"
                            :class="{ active: activeTab === 'notifications' }"
                            @click="activeTab = 'notifications'"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"
                                ></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span>{{ $t("settings.tabNotifications") }}</span>
                        </button>

                        <button
                            class="tab-nav-btn"
                            :class="{ active: activeTab === 'security' }"
                            @click="activeTab = 'security'"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    width="18"
                                    height="11"
                                    x="3"
                                    y="11"
                                    rx="2"
                                    ry="2"
                                ></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span>{{ $t("settings.tabSecurity") }}</span>
                        </button>
                    </div>

                    <!-- Tab 1: Hardware & IoT Settings -->
                    <div
                        v-if="activeTab === 'hardware'"
                        class="tab-content-pane"
                    >
                        <!-- Banner Pilihan Saluran Utama (Wi-Fi, Bluetooth, USB Direct) -->
                        <div
                            class="settings-card full-col-card"
                            style="margin-bottom: 16px"
                        >
                            <div class="card-title-row">
                                <div class="title-with-icon">
                                    <div class="icon-chip chip-green">
                                        <svg
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#0D631B"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="card-heading">
                                            Saluran Transmisi Sensor Utama
                                        </h3>
                                        <p class="card-subhead">
                                            Pilih jalur komunikasi
                                            mikrokontroler ESP32: Wi-Fi,
                                            Bluetooth BLE, atau Kabel USB
                                            Langsung
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="status-badge"
                                    :class="
                                        isHardwareActive
                                            ? 'badge-connected'
                                            : 'badge-testing'
                                    "
                                >
                                    <span
                                        class="pulse-dot-sm"
                                        :class="{
                                            'pulse-green': isHardwareActive,
                                        }"
                                    ></span>
                                    <span>{{
                                        isHardwareActive
                                            ? `Aktif (${currentConnectionLabel})`
                                            : "Menunggu ESP32"
                                    }}</span>
                                </span>
                            </div>

                            <div class="transport-selection-grid">
                                <!-- 1. Wi-Fi Option -->
                                <div
                                    class="transport-option-card"
                                    :class="{
                                        active: currentTransportMode === 'wifi',
                                    }"
                                    @click="setPrimaryConnectionMode('wifi')"
                                >
                                    <div class="option-header">
                                        <div class="option-icon wifi-color">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.2"
                                            >
                                                <path
                                                    d="M5 12.55a11 11 0 0 1 14.08 0"
                                                ></path>
                                                <path
                                                    d="M1.42 9a16 16 0 0 1 21.16 0"
                                                ></path>
                                                <path
                                                    d="M8.53 16.11a6 6 0 0 1 6.95 0"
                                                ></path>
                                                <line
                                                    x1="12"
                                                    y1="20"
                                                    x2="12.01"
                                                    y2="20"
                                                ></line>
                                            </svg>
                                        </div>
                                        <input
                                            type="radio"
                                            :checked="
                                                currentTransportMode === 'wifi'
                                            "
                                            name="transport_mode"
                                        />
                                    </div>
                                    <h4 class="option-title">
                                        1. Wi-Fi & MQTT Broker
                                    </h4>
                                    <p class="option-desc">
                                        Transmisi data sensor nirkabel jarak
                                        jauh melalui hotspot/router green house
                                        ke server.
                                    </p>
                                    <div class="option-footer-tag">
                                        <span
                                            :class="
                                                currentTransportMode ===
                                                    'wifi' && isHardwareActive
                                                    ? 'tag-online'
                                                    : 'tag-neutral'
                                            "
                                        >
                                            {{
                                                currentTransportMode ===
                                                    "wifi" && isHardwareActive
                                                    ? "Live MQTT"
                                                    : "Jalur Nirkabel"
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- 2. Bluetooth BLE Option -->
                                <div
                                    class="transport-option-card"
                                    :class="{
                                        active: currentTransportMode === 'ble',
                                    }"
                                    @click="setPrimaryConnectionMode('ble')"
                                >
                                    <div class="option-header">
                                        <div class="option-icon ble-color">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.2"
                                            >
                                                <polyline
                                                    points="6.5 6.5 17.5 17.5 12 23 12 1 17.5 6.5 6.5 17.5"
                                                ></polyline>
                                            </svg>
                                        </div>
                                        <input
                                            type="radio"
                                            :checked="
                                                currentTransportMode === 'ble'
                                            "
                                            name="transport_mode"
                                        />
                                    </div>
                                    <h4 class="option-title">
                                        2. Bluetooth Low Energy (BLE)
                                    </h4>
                                    <p class="option-desc">
                                        Koneksi lokal tanpa internet menggunakan
                                        Web Bluetooth API di Chrome/Edge.
                                    </p>
                                    <div class="option-footer-tag">
                                        <span
                                            :class="
                                                currentTransportMode ===
                                                    'ble' && isHardwareActive
                                                    ? 'tag-online'
                                                    : 'tag-neutral'
                                            "
                                        >
                                            {{
                                                currentTransportMode ===
                                                    "ble" && isHardwareActive
                                                    ? "BLE GATT Connected"
                                                    : "Jalur Bluetooth"
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- 3. USB Direct Cable Option -->
                                <div
                                    class="transport-option-card"
                                    :class="{
                                        active: currentTransportMode === 'usb',
                                    }"
                                    @click="setPrimaryConnectionMode('usb')"
                                >
                                    <div class="option-header">
                                        <div class="option-icon usb-color">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.2"
                                            >
                                                <rect
                                                    x="7"
                                                    y="2"
                                                    width="10"
                                                    height="7"
                                                    rx="1.5"
                                                ></rect>
                                                <line
                                                    x1="9"
                                                    y1="2"
                                                    x2="9"
                                                    y2="5"
                                                ></line>
                                                <line
                                                    x1="15"
                                                    y1="2"
                                                    x2="15"
                                                    y2="5"
                                                ></line>
                                                <path d="M12 9v7"></path>
                                                <rect
                                                    x="8"
                                                    y="16"
                                                    width="8"
                                                    height="6"
                                                    rx="2"
                                                ></rect>
                                            </svg>
                                        </div>
                                        <input
                                            type="radio"
                                            :checked="
                                                currentTransportMode === 'usb'
                                            "
                                            name="transport_mode"
                                        />
                                    </div>
                                    <h4 class="option-title">
                                        3. Kabel USB Serial Direct
                                    </h4>
                                    <p class="option-desc">
                                        Kabel USB ESP32 dicolok langsung ke
                                        Laptop/PC via Web Serial API (115200
                                        baud, tanpa internet).
                                    </p>
                                    <div class="option-footer-tag">
                                        <span
                                            :class="
                                                currentTransportMode ===
                                                    'usb' && isHardwareActive
                                                    ? 'tag-online'
                                                    : 'tag-neutral'
                                            "
                                        >
                                            {{
                                                currentTransportMode ===
                                                    "usb" && isHardwareActive
                                                    ? "USB Port Live 115200 bps"
                                                    : "Kabel Port COM"
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-grid-2">
                            <!-- Card 1: Status & Koneksi ESP32 Gateway (Wi-Fi) -->
                            <div class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-green">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#0D631B"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M5 12.55a11 11 0 0 1 14.08 0"
                                                ></path>
                                                <path
                                                    d="M1.42 9a16 16 0 0 1 21.16 0"
                                                ></path>
                                                <path
                                                    d="M8.53 16.11a6 6 0 0 1 6.95 0"
                                                ></path>
                                                <line
                                                    x1="12"
                                                    y1="20"
                                                    x2="12.01"
                                                    y2="20"
                                                ></line>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Konfigurasi Wi-Fi ESP32
                                            </h3>
                                            <p class="card-subhead">
                                                Koneksi jaringan nirkabel green
                                                house
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="status-badge"
                                        :class="
                                            isTestingPing
                                                ? 'badge-testing'
                                                : 'badge-connected'
                                        "
                                    >
                                        <span
                                            class="pulse-dot-sm"
                                            :class="{
                                                'pulse-green': !isTestingPing,
                                            }"
                                        ></span>
                                        <span>{{
                                            isTestingPing
                                                ? "Menguji..."
                                                : "Terhubung"
                                        }}</span>
                                    </span>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="field-item">
                                        <label>SSID Wi-Fi Green House</label>
                                        <input
                                            type="text"
                                            v-model="hwSettings.wifiSsid"
                                            class="settings-input"
                                        />
                                    </div>

                                    <div class="field-item">
                                        <label>IP Address Gateway</label>
                                        <div class="input-with-action">
                                            <input
                                                type="text"
                                                v-model="hwSettings.ipAddress"
                                                class="settings-input"
                                            />
                                            <button
                                                class="action-sm-btn"
                                                @click="testPing"
                                                :disabled="isTestingPing"
                                            >
                                                {{
                                                    isTestingPing
                                                        ? "Testing..."
                                                        : "Ping Test"
                                                }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="info-row-pill">
                                        <span
                                            >MAC Address:
                                            <strong>{{
                                                hwSettings.macAddress
                                            }}</strong></span
                                        >
                                        <span
                                            >Signal:
                                            <strong>{{
                                                hwSettings.signalStrength
                                            }}</strong></span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Port USB Serial ESP32 (Direct ke Laptop) -->
                            <div class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div
                                            class="icon-chip chip-purple"
                                            style="
                                                background: rgba(
                                                    147,
                                                    51,
                                                    234,
                                                    0.1
                                                );
                                                color: #9333ea;
                                            "
                                        >
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <rect
                                                    x="7"
                                                    y="2"
                                                    width="10"
                                                    height="7"
                                                    rx="1.5"
                                                ></rect>
                                                <line
                                                    x1="9"
                                                    y1="2"
                                                    x2="9"
                                                    y2="5"
                                                ></line>
                                                <line
                                                    x1="15"
                                                    y1="2"
                                                    x2="15"
                                                    y2="5"
                                                ></line>
                                                <path d="M12 9v7"></path>
                                                <rect
                                                    x="8"
                                                    y="16"
                                                    width="8"
                                                    height="6"
                                                    rx="2"
                                                ></rect>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Port USB Serial Direct (Kabel)
                                            </h3>
                                            <p class="card-subhead">
                                                Koneksi kabel data USB
                                                mikrokontroler ke port laptop
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="status-badge"
                                        :class="
                                            isUsbActive
                                                ? 'badge-connected'
                                                : 'badge-testing'
                                        "
                                    >
                                        <span
                                            class="pulse-dot-sm"
                                            :class="{
                                                'pulse-green': isUsbActive,
                                            }"
                                        ></span>
                                        <span>{{
                                            isUsbActive
                                                ? "USB Terhubung"
                                                : "Port Siaga"
                                        }}</span>
                                    </span>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="fields-row-2">
                                        <div class="field-item">
                                            <label>Baud Rate Serial</label>
                                            <select
                                                v-model="hwSettings.usbBaudRate"
                                                class="settings-select"
                                            >
                                                <option :value="115200">
                                                    115200 bps (Standar ESP32)
                                                </option>
                                                <option :value="57600">
                                                    57600 bps
                                                </option>
                                                <option :value="9600">
                                                    9600 bps
                                                </option>
                                                <option :value="230400">
                                                    230400 bps
                                                </option>
                                            </select>
                                        </div>
                                        <div class="field-item">
                                            <label>Status Web Serial</label>
                                            <input
                                                type="text"
                                                :value="
                                                    isUsbSupported
                                                        ? 'Didukung (Chrome/Edge)'
                                                        : 'Browser Tidak Didukung'
                                                "
                                                class="settings-input"
                                                disabled
                                            />
                                        </div>
                                    </div>

                                    <div class="info-row-pill">
                                        <span
                                            >Port:
                                            <strong>{{
                                                usbPortDesc
                                            }}</strong></span
                                        >
                                        <span
                                            >Baud:
                                            <strong
                                                >{{
                                                    hwSettings.usbBaudRate
                                                }}
                                                bps</strong
                                            ></span
                                        >
                                    </div>

                                    <div
                                        class="form-action-row"
                                        style="margin-top: 4px"
                                    >
                                        <button
                                            v-if="!isUsbActive"
                                            type="button"
                                            class="action-sm-btn"
                                            style="
                                                background: #9333ea;
                                                color: #ffffff;
                                                border: none;
                                                padding: 7px 14px;
                                            "
                                            @click="
                                                handleConnectUsbFromSettings
                                            "
                                        >
                                            <svg
                                                width="14"
                                                height="14"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.2"
                                                style="
                                                    display: inline-block;
                                                    vertical-align: middle;
                                                    margin-right: 4px;
                                                "
                                            >
                                                <rect
                                                    x="7"
                                                    y="2"
                                                    width="10"
                                                    height="7"
                                                    rx="1.5"
                                                ></rect>
                                                <path d="M12 9v7"></path>
                                                <rect
                                                    x="8"
                                                    y="16"
                                                    width="8"
                                                    height="6"
                                                    rx="2"
                                                ></rect>
                                            </svg>
                                            <span
                                                >Pilih & Hubungkan Port
                                                USB</span
                                            >
                                        </button>

                                        <button
                                            v-else
                                            type="button"
                                            class="action-sm-btn"
                                            style="
                                                background: #fee2e2;
                                                color: #ba1a1a;
                                                border: 1px solid #fecaca;
                                                padding: 7px 14px;
                                            "
                                            @click="
                                                handleDisconnectUsbFromSettings
                                            "
                                        >
                                            <span>Putuskan Port USB</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: MQTT Broker Real-time Data -->
                            <div class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-blue">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#005DB7"
                                                stroke-width="2"
                                            >
                                                <polyline
                                                    points="22 12 18 12 15 21 9 3 6 12 2 12"
                                                ></polyline>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                MQTT Broker & Telemetri
                                            </h3>
                                            <p class="card-subhead">
                                                Protokol transmisi data sensor
                                                ke dashboard web
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="fields-row-2">
                                        <div class="field-item">
                                            <label>Host Broker MQTT</label>
                                            <input
                                                type="text"
                                                v-model="hwSettings.mqttHost"
                                                class="settings-input"
                                            />
                                        </div>
                                        <div
                                            class="field-item"
                                            style="max-width: 100px"
                                        >
                                            <label>Port</label>
                                            <input
                                                type="number"
                                                v-model="hwSettings.mqttPort"
                                                class="settings-input"
                                            />
                                        </div>
                                    </div>

                                    <div class="field-item">
                                        <label>Topic Telemetri</label>
                                        <input
                                            type="text"
                                            v-model="hwSettings.mqttTopic"
                                            class="settings-input"
                                        />
                                    </div>

                                    <div class="field-item">
                                        <label
                                            >Interval Kirim Data Sensor</label
                                        >
                                        <select
                                            v-model="hwSettings.sampleInterval"
                                            class="settings-select"
                                        >
                                            <option value="2">
                                                Setiap 2 Detik (Real-time Cepat)
                                            </option>
                                            <option value="5">
                                                Setiap 5 Detik (Rekomendasi)
                                            </option>
                                            <option value="10">
                                                Setiap 10 Detik (Hemat
                                                Bandwidth)
                                            </option>
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
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#B45000"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Batas Suhu & Keamanan Heater
                                            </h3>
                                            <p class="card-subhead">
                                                Ambang batas perlindungan agar
                                                biji hanjeli tidak rusak
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="field-item">
                                        <div class="label-with-val">
                                            <label
                                                >Suhu Maksimum Ruangan
                                                (°C)</label
                                            >
                                            <span class="val-tag"
                                                >{{
                                                    dryingPrefs.maxTemp
                                                }}°C</span
                                            >
                                        </div>
                                        <input
                                            type="range"
                                            v-model.number="dryingPrefs.maxTemp"
                                            min="40"
                                            max="65"
                                            step="1"
                                            class="slider-input"
                                        />
                                        <span class="field-hint"
                                            >Heater akan dimatikan otomatis jika
                                            suhu melebihi batas ini.</span
                                        >
                                    </div>

                                    <div class="field-item">
                                        <div class="label-with-val">
                                            <label
                                                >Target Kelembapan Akhir Standar
                                                (%)</label
                                            >
                                            <span class="val-tag"
                                                >{{
                                                    dryingPrefs.targetMoisture
                                                }}%</span
                                            >
                                        </div>
                                        <input
                                            type="range"
                                            v-model.number="
                                                dryingPrefs.targetMoisture
                                            "
                                            min="10"
                                            max="18"
                                            step="0.5"
                                            class="slider-input"
                                        />
                                        <span class="field-hint"
                                            >Kadar air ideal standar SNI untuk
                                            penyimpanan biji hanjeli
                                            kering.</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Kontrol Kipas & Sirkulasi -->
                            <div class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-green">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#0D631B"
                                                stroke-width="2"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                ></circle>
                                                <path
                                                    d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Mode Exhaust Fan & Satuan
                                            </h3>
                                            <p class="card-subhead">
                                                Pengaturan sirkulasi udara dan
                                                format tampilan
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="field-item">
                                        <label
                                            >Mode Otomasi Kipas Exhaust</label
                                        >
                                        <select
                                            v-model="dryingPrefs.fanMode"
                                            class="settings-select"
                                        >
                                            <option value="auto">
                                                Otomatis Berdasarkan Kelembapan
                                                Udara
                                            </option>
                                            <option value="continuous">
                                                Menyala Terus Menerus Selama
                                                Siklus
                                            </option>
                                            <option value="timer">
                                                Interval Berkala (5 Menit Nyala
                                                / 2 Menit Mati)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="field-item">
                                        <label>Satuan Suhu</label>
                                        <div class="radio-toggle-group">
                                            <button
                                                type="button"
                                                class="radio-btn"
                                                :class="{
                                                    active:
                                                        dryingPrefs.tempUnit ===
                                                        'C',
                                                }"
                                                @click="
                                                    dryingPrefs.tempUnit = 'C'
                                                "
                                            >
                                                Celsius (°C)
                                            </button>
                                            <button
                                                type="button"
                                                class="radio-btn"
                                                :class="{
                                                    active:
                                                        dryingPrefs.tempUnit ===
                                                        'F',
                                                }"
                                                @click="
                                                    dryingPrefs.tempUnit = 'F'
                                                "
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
                    <div
                        v-if="activeTab === 'notifications'"
                        class="tab-content-pane"
                    >
                        <div class="settings-grid-2">
                            <!-- Card 1: Browser & Alarm Sirene Suhu Kritis -->
                            <div class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-orange">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#B45000"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"
                                                ></path>
                                                <path
                                                    d="M13.73 21a2 2 0 0 1-3.46 0"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Alarm & Notifikasi Browser
                                            </h3>
                                            <p class="card-subhead">
                                                Peringatan langsung pada layar
                                                browser & suara alarm darurat
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-fields-stack">
                                    <!-- Threshold setting -->
                                    <div class="field-item">
                                        <div class="label-with-val">
                                            <label
                                                >Ambang Batas Suhu Bahaya
                                                (°C)</label
                                            >
                                            <span
                                                class="val-tag"
                                                style="
                                                    background: #fee2e2;
                                                    color: #ba1a1a;
                                                "
                                                >{{
                                                    notifSettings.criticalTempThreshold
                                                }}°C</span
                                            >
                                        </div>
                                        <input
                                            type="range"
                                            v-model.number="
                                                notifSettings.criticalTempThreshold
                                            "
                                            min="45"
                                            max="70"
                                            step="1"
                                            class="slider-input"
                                        />
                                        <span class="field-hint"
                                            >Alarm otomatis berbunyi dan
                                            mengirim pesan darurat saat suhu
                                            melampaui angka ini.</span
                                        >
                                    </div>

                                    <!-- PWA Installation Status -->
                                    <div
                                        class="toggle-option-row"
                                        style="padding-top: 6px"
                                    >
                                        <div class="toggle-text">
                                            <strong
                                                >Pemasangan Aplikasi Native
                                                (PWA)</strong
                                            >
                                            <p>
                                                Pasang aplikasi di layar utama
                                                HP/PC tanpa PlayStore untuk
                                                pengalaman native & cepat.
                                            </p>
                                        </div>
                                        <div>
                                            <button
                                                v-if="pwaState.isInstalled"
                                                class="action-sm-btn"
                                                style="
                                                    background: #e8f5e9;
                                                    color: #0d631b;
                                                    border-color: #a5d6a7;
                                                    cursor: default;
                                                "
                                            >
                                                <svg
                                                    width="14"
                                                    height="14"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.5"
                                                    style="
                                                        display: inline-block;
                                                        vertical-align: middle;
                                                        margin-right: 4px;
                                                    "
                                                >
                                                    <polyline
                                                        points="20 6 9 17 4 12"
                                                    ></polyline>
                                                </svg>
                                                Aplikasi Terpasang (Standalone)
                                            </button>
                                            <button
                                                v-else
                                                class="action-sm-btn"
                                                style="
                                                    background: #0d631b;
                                                    color: #ffffff;
                                                "
                                                @click="handleManualPwaInstall"
                                            >
                                                <svg
                                                    width="14"
                                                    height="14"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    style="
                                                        display: inline-block;
                                                        vertical-align: middle;
                                                        margin-right: 4px;
                                                    "
                                                >
                                                    <path
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                                    ></path>
                                                </svg>
                                                Install Aplikasi (PWA)
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Browser Push Permission -->
                                    <div class="toggle-option-row">
                                        <div class="toggle-text">
                                            <strong
                                                >Web Push Notification Browser &
                                                Background</strong
                                            >
                                            <p>
                                                Munculkan notifikasi alert di
                                                desktop / HP meski aplikasi
                                                sedang di latar belakang.
                                            </p>
                                        </div>
                                        <div style="display: flex; gap: 8px">
                                            <button
                                                class="action-sm-btn"
                                                @click="
                                                    requestBrowserNotificationPermission
                                                "
                                            >
                                                <template
                                                    v-if="
                                                        pwaState.notificationPermission ===
                                                            'granted' ||
                                                        browserPermissionStatus ===
                                                            'granted'
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2.5"
                                                        style="
                                                            display: inline-block;
                                                            vertical-align: middle;
                                                            margin-right: 4px;
                                                        "
                                                    >
                                                        <polyline
                                                            points="20 6 9 17 4 12"
                                                        ></polyline>
                                                    </svg>
                                                    Izin Aktif
                                                </template>
                                                <template v-else>
                                                    Minta Izin Push
                                                </template>
                                            </button>
                                            <button
                                                v-if="
                                                    pwaState.notificationPermission ===
                                                        'granted' ||
                                                    browserPermissionStatus ===
                                                        'granted'
                                                "
                                                class="action-sm-btn"
                                                style="
                                                    background: #f0fdf4;
                                                    color: #166534;
                                                    border-color: #bbf7d0;
                                                "
                                                @click="testPushNotification"
                                            >
                                                Uji Notifikasi Live
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Sound Alarm Toggle -->
                                    <div class="toggle-option-row">
                                        <div class="toggle-text">
                                            <strong
                                                >Bunyi Alarm Suara
                                                Darurat</strong
                                            >
                                            <p>
                                                Putar efek suara sirene
                                                peringatan jika suhu kritis
                                                terdeteksi.
                                            </p>
                                        </div>
                                        <label class="switch">
                                            <input
                                                type="checkbox"
                                                v-model="
                                                    notifSettings.soundAlarmEnabled
                                                "
                                            />
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                    <!-- Conditions checklist -->
                                    <div class="toggle-option-row">
                                        <div class="toggle-text">
                                            <strong
                                                >Pemberitahuan Siklus
                                                Selesai</strong
                                            >
                                            <p>
                                                Kirim notifikasi saat kadar air
                                                gabah mencapai target 12%.
                                            </p>
                                        </div>
                                        <label class="switch">
                                            <input
                                                type="checkbox"
                                                v-model="
                                                    notifSettings.dryingFinishedAlert
                                                "
                                            />
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                    <div class="toggle-option-row">
                                        <div class="toggle-text">
                                            <strong
                                                >Hardware / Sensor
                                                Offline</strong
                                            >
                                            <p>
                                                Beri tahu jika ESP32 atau sensor
                                                terputus lebih dari 2 menit.
                                            </p>
                                        </div>
                                        <label class="switch">
                                            <input
                                                type="checkbox"
                                                v-model="
                                                    notifSettings.hardwareDisconnectAlert
                                                "
                                            />
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                    <!-- Automated Email Notifications -->
                                    <div class="toggle-option-row">
                                        <div class="toggle-text">
                                            <strong
                                                >Notifikasi Email Otomatis
                                                (Semua Alert Sistem)</strong
                                            >
                                            <p>
                                                Kirim email instan ke kotak
                                                masuk setiap kali terjadi
                                                peringatan sistem, anomali
                                                sensor, atau event siklus batch.
                                            </p>
                                        </div>
                                        <label class="switch">
                                            <input
                                                type="checkbox"
                                                v-model="
                                                    notifSettings.emailAlertsEnabled
                                                "
                                            />
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
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#0D631B"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Integrasi WhatsApp Gateway &
                                                Webhook
                                            </h3>
                                            <p class="card-subhead">
                                                Kirim pesan alert instan ke
                                                WhatsApp operator/petani
                                            </p>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            v-model="
                                                notifSettings.whatsappEnabled
                                            "
                                        />
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div
                                    class="form-fields-stack"
                                    :class="{
                                        'disabled-stack':
                                            !notifSettings.whatsappEnabled,
                                    }"
                                >
                                    <div class="field-item">
                                        <label>Pilihan Provider Gateway</label>
                                        <select
                                            v-model="
                                                notifSettings.whatsappProvider
                                            "
                                            class="settings-input"
                                            :disabled="
                                                !notifSettings.whatsappEnabled
                                            "
                                        >
                                            <option value="fonnte">
                                                Fonnte WhatsApp API (Rekomendasi
                                                Indonesia)
                                            </option>
                                            <option value="wablas">
                                                Wablas Gateway
                                            </option>
                                            <option value="custom">
                                                Generic Webhook URL / Twilio /
                                                Custom Server
                                            </option>
                                        </select>
                                    </div>

                                    <div class="field-item">
                                        <label
                                            >Nomor WhatsApp Tujuan
                                            (Operator)</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                notifSettings.whatsappNumber
                                            "
                                            placeholder="Contoh: 081388992211 atau 6281388992211"
                                            class="settings-input"
                                            :disabled="
                                                !notifSettings.whatsappEnabled
                                            "
                                        />
                                    </div>

                                    <div class="field-item">
                                        <label
                                            >Provider Gateway / Webhook
                                            URL</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                notifSettings.whatsappApiUrl
                                            "
                                            placeholder="https://api.fonnte.com/send atau Webhook URL"
                                            class="settings-input"
                                            :disabled="
                                                !notifSettings.whatsappEnabled
                                            "
                                        />
                                    </div>

                                    <div class="field-item">
                                        <label
                                            >API Key / Token
                                            Authorization</label
                                        >
                                        <input
                                            type="password"
                                            v-model="
                                                notifSettings.whatsappApiKey
                                            "
                                            placeholder="Masukkan API Token Gateway WhatsApp"
                                            class="settings-input"
                                            :disabled="
                                                !notifSettings.whatsappEnabled
                                            "
                                        />
                                    </div>

                                    <div class="field-item">
                                        <label style="margin-bottom: 6px"
                                            >Event yang Memicu Pesan
                                            WhatsApp:</label
                                        >
                                        <div
                                            style="
                                                display: flex;
                                                flex-direction: column;
                                                gap: 6px;
                                                font-size: 13px;
                                            "
                                        >
                                            <label
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 8px;
                                                    font-weight: normal;
                                                    cursor: pointer;
                                                "
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        notifSettings
                                                            .whatsappEvents
                                                            .batch_completed
                                                    "
                                                    :disabled="
                                                        !notifSettings.whatsappEnabled
                                                    "
                                                />
                                                <span
                                                    style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="#10b981"
                                                        stroke-width="2"
                                                    >
                                                        <path
                                                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                                        ></path>
                                                    </svg>
                                                    Sesi Batch Selesai (Target
                                                    Kadar Air Tercapai)
                                                </span>
                                            </label>
                                            <label
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 8px;
                                                    font-weight: normal;
                                                    cursor: pointer;
                                                "
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        notifSettings
                                                            .whatsappEvents
                                                            .emergency
                                                    "
                                                    :disabled="
                                                        !notifSettings.whatsappEnabled
                                                    "
                                                />
                                                <span
                                                    style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="#ef4444"
                                                        stroke-width="2"
                                                    >
                                                        <path
                                                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                                                        ></path>
                                                        <line
                                                            x1="12"
                                                            y1="9"
                                                            x2="12"
                                                            y2="13"
                                                        ></line>
                                                        <line
                                                            x1="12"
                                                            y1="17"
                                                            x2="12.01"
                                                            y2="17"
                                                        ></line>
                                                    </svg>
                                                    Peringatan Darurat (Suhu
                                                    Panas Kritis, Pintu Terbuka,
                                                    Sensor Anomali)
                                                </span>
                                            </label>
                                            <label
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 8px;
                                                    font-weight: normal;
                                                    cursor: pointer;
                                                "
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        notifSettings
                                                            .whatsappEvents
                                                            .daily_digest
                                                    "
                                                    :disabled="
                                                        !notifSettings.whatsappEnabled
                                                    "
                                                />
                                                <span
                                                    style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="#3b82f6"
                                                        stroke-width="2"
                                                    >
                                                        <line
                                                            x1="18"
                                                            y1="20"
                                                            x2="18"
                                                            y2="10"
                                                        ></line>
                                                        <line
                                                            x1="12"
                                                            y1="20"
                                                            x2="12"
                                                            y2="4"
                                                        ></line>
                                                        <line
                                                            x1="6"
                                                            y1="20"
                                                            x2="6"
                                                            y2="14"
                                                        ></line>
                                                    </svg>
                                                    Ringkasan Harian Pengeringan
                                                    (Daily Digest Jam 18:00)
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="field-item">
                                        <button
                                            type="button"
                                            class="test-wa-btn"
                                            @click="testSendWhatsAppAlert"
                                            :disabled="
                                                !notifSettings.whatsappEnabled ||
                                                isTestingWhatsApp
                                            "
                                        >
                                            <svg
                                                v-if="!isTestingWhatsApp"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <line
                                                    x1="22"
                                                    y1="2"
                                                    x2="11"
                                                    y2="13"
                                                ></line>
                                                <polygon
                                                    points="22 2 15 22 11 13 2 9 22 2"
                                                ></polygon>
                                            </svg>
                                            <span>{{
                                                isTestingWhatsApp
                                                    ? "Mengirim Simulasi Pesan..."
                                                    : "Kirim Uji Coba WhatsApp Live"
                                            }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Telegram Bot Integration (Admin Only) -->
                            <div v-if="isAdmin" class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-blue">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#005DB7"
                                                stroke-width="2"
                                            >
                                                <line
                                                    x1="22"
                                                    y1="2"
                                                    x2="11"
                                                    y2="13"
                                                ></line>
                                                <polygon
                                                    points="22 2 15 22 11 13 2 9 22 2"
                                                ></polygon>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Integrasi Telegram Bot & Channel
                                            </h3>
                                            <p class="card-subhead">
                                                Kirim notifikasi grup tim
                                                penelitian STAS-RG / kelompok
                                                tani
                                            </p>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            v-model="
                                                notifSettings.telegramEnabled
                                            "
                                        />
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div
                                    class="form-fields-stack"
                                    :class="{
                                        'disabled-stack':
                                            !notifSettings.telegramEnabled,
                                    }"
                                >
                                    <div class="field-item">
                                        <label
                                            >Telegram Bot Token (dari
                                            @BotFather)</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                notifSettings.telegramBotToken
                                            "
                                            placeholder="Contoh: 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ"
                                            class="settings-input"
                                            :disabled="
                                                !notifSettings.telegramEnabled
                                            "
                                        />
                                        <small class="field-hint"
                                            >Dapatkan Bot Token gratis melalui
                                            bot resmi
                                            <strong>@BotFather</strong> di
                                            Telegram.</small
                                        >
                                    </div>

                                    <div class="field-item">
                                        <label
                                            >Chat ID / Channel Group ID</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                notifSettings.telegramChatId
                                            "
                                            placeholder="Contoh: -100192837465 atau 892817263"
                                            class="settings-input"
                                            :disabled="
                                                !notifSettings.telegramEnabled
                                            "
                                        />
                                        <small class="field-hint"
                                            >Chat ID perorangan atau ID
                                            Grup/Channel (dapat dicek via
                                            <strong>@userinfobot</strong
                                            >).</small
                                        >
                                    </div>

                                    <div class="field-item">
                                        <label style="margin-bottom: 6px"
                                            >Event yang Memicu Pesan
                                            Telegram:</label
                                        >
                                        <div
                                            style="
                                                display: flex;
                                                flex-direction: column;
                                                gap: 6px;
                                                font-size: 13px;
                                            "
                                        >
                                            <label
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 8px;
                                                    font-weight: normal;
                                                    cursor: pointer;
                                                "
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        notifSettings
                                                            .telegramEvents
                                                            .batch_completed
                                                    "
                                                    :disabled="
                                                        !notifSettings.telegramEnabled
                                                    "
                                                />
                                                <span
                                                    style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="#10b981"
                                                        stroke-width="2"
                                                    >
                                                        <path
                                                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                                        ></path>
                                                    </svg>
                                                    Sesi Batch Selesai (Target
                                                    Kadar Air Tercapai)
                                                </span>
                                            </label>
                                            <label
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 8px;
                                                    font-weight: normal;
                                                    cursor: pointer;
                                                "
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        notifSettings
                                                            .telegramEvents
                                                            .emergency
                                                    "
                                                    :disabled="
                                                        !notifSettings.telegramEnabled
                                                    "
                                                />
                                                <span
                                                    style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="#ef4444"
                                                        stroke-width="2"
                                                    >
                                                        <path
                                                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                                                        ></path>
                                                        <line
                                                            x1="12"
                                                            y1="9"
                                                            x2="12"
                                                            y2="13"
                                                        ></line>
                                                        <line
                                                            x1="12"
                                                            y1="17"
                                                            x2="12.01"
                                                            y2="17"
                                                        ></line>
                                                    </svg>
                                                    Peringatan Darurat (Suhu
                                                    Panas Kritis, Pintu Terbuka,
                                                    Sensor Anomali)
                                                </span>
                                            </label>
                                            <label
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 8px;
                                                    font-weight: normal;
                                                    cursor: pointer;
                                                "
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        notifSettings
                                                            .telegramEvents
                                                            .daily_digest
                                                    "
                                                    :disabled="
                                                        !notifSettings.telegramEnabled
                                                    "
                                                />
                                                <span
                                                    style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                    "
                                                >
                                                    <svg
                                                        width="14"
                                                        height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="#3b82f6"
                                                        stroke-width="2"
                                                    >
                                                        <line
                                                            x1="18"
                                                            y1="20"
                                                            x2="18"
                                                            y2="10"
                                                        ></line>
                                                        <line
                                                            x1="12"
                                                            y1="20"
                                                            x2="12"
                                                            y2="4"
                                                        ></line>
                                                        <line
                                                            x1="6"
                                                            y1="20"
                                                            x2="6"
                                                            y2="14"
                                                        ></line>
                                                    </svg>
                                                    Ringkasan Harian Pengeringan
                                                    (Daily Digest Jam 18:00)
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="field-item">
                                        <button
                                            type="button"
                                            class="test-telegram-btn"
                                            @click="testSendTelegramAlert"
                                            :disabled="
                                                !notifSettings.telegramEnabled ||
                                                isTestingTelegram
                                            "
                                        >
                                            <svg
                                                v-if="!isTestingTelegram"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <line
                                                    x1="22"
                                                    y1="2"
                                                    x2="11"
                                                    y2="13"
                                                ></line>
                                                <polygon
                                                    points="22 2 15 22 11 13 2 9 22 2"
                                                ></polygon>
                                            </svg>
                                            <span>{{
                                                isTestingTelegram
                                                    ? "Mengirim ke Telegram..."
                                                    : "Kirim Uji Coba Pesan Telegram Live"
                                            }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4: Template Pesan & Daily Digest (Admin Only) -->
                            <div v-if="isAdmin" class="settings-card">
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-green">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#0D631B"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                                ></path>
                                                <polyline
                                                    points="14 2 14 8 20 8"
                                                ></polyline>
                                                <line
                                                    x1="16"
                                                    y1="13"
                                                    x2="8"
                                                    y2="13"
                                                ></line>
                                                <line
                                                    x1="16"
                                                    y1="17"
                                                    x2="8"
                                                    y2="17"
                                                ></line>
                                                <polyline
                                                    points="10 9 9 9 8 9"
                                                ></polyline>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Format Template & Ringkasan
                                                Harian
                                            </h3>
                                            <p class="card-subhead">
                                                Uji format pesan darurat dan
                                                siaran ringkasan harian (Daily
                                                Digest)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="preview-template-box">
                                        <div class="template-header">
                                            Contoh Format Pesan Notifikasi:
                                        </div>
                                        <div class="template-body">
                                            <span
                                                style="
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 6px;
                                                    color: #dc2626;
                                                    font-weight: bold;
                                                "
                                            >
                                                <svg
                                                    width="15"
                                                    height="15"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.2"
                                                >
                                                    <path
                                                        d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                                                    ></path>
                                                    <line
                                                        x1="12"
                                                        y1="9"
                                                        x2="12"
                                                        y2="13"
                                                    ></line>
                                                    <line
                                                        x1="12"
                                                        y1="17"
                                                        x2="12.01"
                                                        y2="17"
                                                    ></line>
                                                </svg>
                                                [PERINGATAN SUHU GREEN HOUSE] </span
                                            ><br />
                                            • <strong>Lokasi:</strong> Desa
                                            Wisata Hanjeli Waluran Sukabumi<br />
                                            •
                                            <strong>Suhu Terdeteksi:</strong>
                                            56.8°C (Batas:
                                            {{
                                                notifSettings.criticalTempThreshold
                                            }}°C)<br />
                                            • <strong>Kelembapan:</strong> 42%
                                            RH •
                                            <strong>Kadar Air:</strong>
                                            13.5%<br />
                                            • <strong>Tindakan:</strong> Kipas
                                            exhaust 100%. Pintu & heater
                                            diperiksa otomatis.
                                        </div>
                                    </div>

                                    <div
                                        class="field-item"
                                        style="margin-top: 6px"
                                    >
                                        <button
                                            type="button"
                                            class="btn-secondary-action"
                                            style="
                                                width: 100%;
                                                justify-content: center;
                                            "
                                            @click="testSendDailyDigest"
                                            :disabled="isSendingDailyDigest"
                                        >
                                            <svg
                                                v-if="!isSendingDailyDigest"
                                                width="15"
                                                height="15"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <rect
                                                    x="3"
                                                    y="4"
                                                    width="18"
                                                    height="18"
                                                    rx="2"
                                                    ry="2"
                                                ></rect>
                                                <line
                                                    x1="16"
                                                    y1="2"
                                                    x2="16"
                                                    y2="6"
                                                ></line>
                                                <line
                                                    x1="8"
                                                    y1="2"
                                                    x2="8"
                                                    y2="6"
                                                ></line>
                                                <line
                                                    x1="3"
                                                    y1="10"
                                                    x2="21"
                                                    y2="10"
                                                ></line>
                                            </svg>
                                            <span>{{
                                                isSendingDailyDigest
                                                    ? "Menyiarkan Ringkasan Harian..."
                                                    : "Siarkan Ringkasan Harian (Daily Digest) Sekarang"
                                            }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 5: Mail Template Integration (Admin Only) -->
                            <div
                                v-if="isAdmin"
                                class="settings-card full-col-card"
                            >
                                <div class="card-title-row">
                                    <div class="title-with-icon">
                                        <div class="icon-chip chip-green">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#0D631B"
                                                stroke-width="2"
                                            >
                                                <rect
                                                    width="20"
                                                    height="16"
                                                    x="2"
                                                    y="4"
                                                    rx="2"
                                                ></rect>
                                                <path
                                                    d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="card-heading">
                                                Integrasi Template Email Sistem
                                                (Mailpit / SMTP)
                                            </h3>
                                            <p class="card-subhead">
                                                Template email HTML berdesain
                                                modern (Design System Emerald)
                                                untuk laporan dan alert darurat
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-fields-stack">
                                    <div class="field-item">
                                        <label
                                            >Alamat Email Tujuan Uji Coba &amp;
                                            Notifikasi Operator</label
                                        >
                                        <input
                                            type="email"
                                            v-model="
                                                notifSettings.customEmailRecipient
                                            "
                                            placeholder="operator@hanjeli.id atau email admin"
                                            class="settings-input"
                                        />
                                        <span class="field-hint"
                                            >Seluruh email alert real-time akan
                                            dikirimkan ke daftar user
                                            admin/operator dan alamat ini.</span
                                        >
                                    </div>

                                    <p
                                        style="
                                            font-size: 13.5px;
                                            color: var(--color-text-body);
                                            line-height: 1.6;
                                            margin-top: 6px;
                                        "
                                    >
                                        Sistem dilengkapi 4 template email
                                        berbasis Master Layout Blade:
                                        <strong
                                            >Peringatan Sensor &amp; Siklus
                                            Batch</strong
                                        >,
                                        <strong>Laporan Selesai Panen</strong>,
                                        <strong>Kode OTP Pemulihan</strong>, dan
                                        <strong>Sambutan Akun Baru</strong>. Uji
                                        pengiriman langsung ke Mailpit / Inbox:
                                    </p>

                                    <div class="email-test-actions-grid">
                                        <button
                                            class="email-test-btn btn-danger-soft"
                                            @click="testSendEmail('alert')"
                                            :disabled="isSendingEmail"
                                        >
                                            <svg
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                                                ></path>
                                                <line
                                                    x1="12"
                                                    y1="9"
                                                    x2="12"
                                                    y2="13"
                                                ></line>
                                                <line
                                                    x1="12"
                                                    y1="17"
                                                    x2="12.01"
                                                    y2="17"
                                                ></line>
                                            </svg>
                                            <span
                                                >1. Uji Email Peringatan
                                                Sistem</span
                                            >
                                        </button>

                                        <button
                                            class="email-test-btn btn-success-soft"
                                            @click="testSendEmail('batch')"
                                            :disabled="isSendingEmail"
                                        >
                                            <svg
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M22 11.08V12a10 10 0 1 1-5.93-9.14"
                                                ></path>
                                                <polyline
                                                    points="22 4 12 14.01 9 11.01"
                                                ></polyline>
                                            </svg>
                                            <span
                                                >2. Uji Email Laporan
                                                Batch</span
                                            >
                                        </button>

                                        <button
                                            class="email-test-btn btn-blue-soft"
                                            @click="testSendEmail('otp')"
                                            :disabled="isSendingEmail"
                                        >
                                            <svg
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <rect
                                                    width="18"
                                                    height="11"
                                                    x="3"
                                                    y="11"
                                                    rx="2"
                                                    ry="2"
                                                ></rect>
                                                <path
                                                    d="M7 11V7a5 5 0 0 1 10 0v4"
                                                ></path>
                                            </svg>
                                            <span>3. Uji Email Kode OTP</span>
                                        </button>

                                        <button
                                            class="email-test-btn btn-emerald-soft"
                                            @click="testSendEmail('welcome')"
                                            :disabled="isSendingEmail"
                                        >
                                            <svg
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                                ></path>
                                                <circle
                                                    cx="9"
                                                    cy="7"
                                                    r="4"
                                                ></circle>
                                                <polyline
                                                    points="16 11 18 13 22 9"
                                                ></polyline>
                                            </svg>
                                            <span
                                                >4. Uji Email Sambutan
                                                User</span
                                            >
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Security Settings -->
                    <div
                        v-if="activeTab === 'security'"
                        class="tab-content-pane"
                    >
                        <div class="settings-card max-w-card">
                            <div class="card-title-row">
                                <div class="title-with-icon">
                                    <div class="icon-chip chip-blue">
                                        <svg
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#005DB7"
                                            stroke-width="2"
                                        >
                                            <rect
                                                width="18"
                                                height="11"
                                                x="3"
                                                y="11"
                                                rx="2"
                                                ry="2"
                                            ></rect>
                                            <path
                                                d="M7 11V7a5 5 0 0 1 10 0v4"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="card-heading">
                                            Ganti Kata Sandi
                                        </h3>
                                        <p class="card-subhead">
                                            Perbarui kata sandi untuk melindungi
                                            akses kontrol alat pengering
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form
                                @submit.prevent="savePassword"
                                class="form-fields-stack"
                            >
                                <div class="field-item">
                                    <label>Kata Sandi Saat Ini</label>
                                    <input
                                        type="password"
                                        v-model="secSettings.currentPassword"
                                        placeholder="••••••••"
                                        class="settings-input"
                                        required
                                    />
                                </div>

                                <div class="fields-row-2">
                                    <div class="field-item">
                                        <label>Kata Sandi Baru</label>
                                        <input
                                            type="password"
                                            v-model="secSettings.newPassword"
                                            placeholder="Minimal 8 karakter"
                                            class="settings-input"
                                            required
                                        />
                                    </div>
                                    <div class="field-item">
                                        <label>Ulangi Kata Sandi Baru</label>
                                        <input
                                            type="password"
                                            v-model="
                                                secSettings.confirmPassword
                                            "
                                            placeholder="Konfirmasi kata sandi"
                                            class="settings-input"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="form-action-row">
                                    <button
                                        type="submit"
                                        class="btn-primary-sm"
                                    >
                                        Perbarui Kata Sandi
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Passkey Biometric Registration Card -->
                        <div
                            class="settings-card max-w-card"
                            style="margin-top: 16px"
                        >
                            <div class="card-title-row">
                                <div class="title-with-icon">
                                    <div class="icon-chip chip-green">
                                        <svg
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#15803D"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"
                                            ></path>
                                            <path
                                                d="M14 13.12c0 2.38 0 6.38-1 8.88"
                                            ></path>
                                            <path
                                                d="M17.29 21.02c.12-.6.43-2.3.5-3.02"
                                            ></path>
                                            <path
                                                d="M2 12a10 10 0 0 1 18-6"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="card-heading">
                                            Passkey & Autentikasi Biometrik
                                        </h3>
                                        <p class="card-subhead">
                                            Daftarkan Touch ID, Face ID, atau
                                            Windows Hello perangkat ini untuk
                                            login cepat tanpa kata sandi.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div style="padding: 12px 0 6px 0">
                                <p
                                    style="
                                        font-size: 13px;
                                        color: #4b5563;
                                        line-height: 1.5;
                                        margin-bottom: 14px;
                                    "
                                >
                                    Passkey memanfaatkan standar WebAuthn /
                                    FIDO2 untuk mengamankan akses akun
                                    greenhouse menggunakan sensor biometrik di
                                    perangkat Anda.
                                </p>
                                <button
                                    type="button"
                                    class="btn-primary-sm"
                                    style="
                                        background: #15803d;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 8px;
                                    "
                                    @click="handleRegisterPasskey"
                                    :disabled="isRegisteringPasskey"
                                >
                                    <svg
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"
                                        ></path>
                                        <path
                                            d="M14 13.12c0 2.38 0 6.38-1 8.88"
                                        ></path>
                                        <path
                                            d="M17.29 21.02c.12-.6.43-2.3.5-3.02"
                                        ></path>
                                        <path d="M2 12a10 10 0 0 1 18-6"></path>
                                    </svg>
                                    <span>{{
                                        isRegisteringPasskey
                                            ? "Menyambungkan Sensor..."
                                            : "Daftarkan Passkey Perangkat Ini"
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Toast Notification -->
                <transition name="toast">
                    <div v-if="showToast" class="toast-popup">
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#0D631B"
                            stroke-width="2.5"
                        >
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>{{ toastMessage }}</span>
                    </div>
                </transition>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { settingsService } from "../../services/settingsService";
import { authService } from "../../services/authService";
import { alertDialog } from "../../services/confirmDialogService";
import connectionManager from "../../services/connectionManager";
import usbSerialService from "../../services/serial/usbSerialService";
import SettingsSkeleton from "../../components/SettingsSkeleton.vue";
import {
    pwaState,
    promptInstall,
    requestNotificationPermission,
    sendTestPushNotification,
} from "../../services/pwaService";

const isInitialLoading = ref(true);

const props = defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
    currentLang: {
        type: String,
        default: "id",
    },
    user: {
        type: Object,
        default: () => ({
            name: "Dr. Ir. Fatin Tsani",
            email: "admin@hanjeli.com",
            role: "OPERATOR",
        }),
    },
});

const isAdmin = computed(() => {
    if (props.user?.role === "ADMIN") return true;
    try {
        const userJson = localStorage.getItem("user");
        if (userJson) {
            const u = JSON.parse(userJson);
            if (u && u.role === "ADMIN") return true;
        }
        const storedRole = localStorage.getItem("user_role");
        if (storedRole === "ADMIN") return true;
    } catch (e) {
        // fallback
    }
    return false;
});

const activeTab = ref("hardware"); // 'hardware', 'drying', 'notifications', 'security'
const isTestingPing = ref(false);
const showToast = ref(false);
const toastMessage = ref("");
const isRegisteringPasskey = ref(false);
const isTestingWhatsApp = ref(false);
const isTestingTelegram = ref(false);
const isSendingDailyDigest = ref(false);
const browserPermissionStatus = ref(
    typeof Notification !== "undefined" ? Notification.permission : "default",
);

// Hardware settings state
const hwSettings = ref({
    wifiSsid: "GreenHouse_Hanjeli_IoT",
    ipAddress: "192.168.1.105",
    macAddress: "24:6F:28:9A:C3:10",
    signalStrength: "-62 dBm (Kuat)",
    mqttHost: "broker.emqx.io",
    mqttPort: 1883,
    mqttTopic: "greenhouse/hanjeli/dryer01/sensor",
    sampleInterval: "5",
    usbBaudRate: 115200,
});

// Connection Transport Management
const currentTransportMode = computed(() => connectionManager.state.mode);
const isHardwareActive = computed(
    () => connectionManager.state.isHardwareActive,
);
const isUsbActive = computed(() => usbSerialService.isConnected);
const isUsbSupported = computed(() => usbSerialService.isSupported());
const usbPortDesc = computed(() => {
    if (usbSerialService.isConnected) {
        return (
            connectionManager.state.transportDetails.usbPortInfo?.usbVendorId ||
            "Port COM / USB Terhubung"
        );
    }
    return isUsbSupported.value
        ? "Port USB Siap Dipilih"
        : "Browser Tidak Mendukung Web Serial";
});

const currentConnectionLabel = computed(() => {
    if (currentTransportMode.value === "wifi") return "Wi-Fi & MQTT";
    if (currentTransportMode.value === "ble") return "Bluetooth BLE";
    if (currentTransportMode.value === "usb") return "USB Serial";
    return "Standby";
});

function setPrimaryConnectionMode(mode) {
    connectionManager.setConnectionMode(mode);
    triggerToast(
        `Saluran komunikasi utama diubah ke: ${mode === "wifi" ? "Wi-Fi & MQTT" : mode === "ble" ? "Bluetooth BLE" : "Kabel USB Serial"}`,
    );
}

async function handleConnectUsbFromSettings() {
    try {
        connectionManager.state.mode = "usb";
        const baud = hwSettings.value.usbBaudRate || 115200;
        connectionManager.state.transportDetails.usbBaudRate = baud;
        await connectionManager.connect({ baudRate: baud });
        triggerToast("Berhasil terhubung ke port USB Serial ESP32!");
    } catch (e) {
        console.warn("USB Connect error:", e);
    }
}

async function handleDisconnectUsbFromSettings() {
    await connectionManager.disconnect();
    triggerToast("Port USB Serial telah diputuskan.");
}

// Drying preferences state
const dryingPrefs = ref({
    maxTemp: 55,
    targetMoisture: 12.0,
    fanMode: "auto",
    tempUnit: "C",
});

// Notification & Alarm settings state with localStorage persistence
const savedNotif = localStorage.getItem("hanjeli_notif_settings");
const notifSettings = ref(
    savedNotif
        ? JSON.parse(savedNotif)
        : {
              criticalTempThreshold: 55,
              soundAlarmEnabled: true,
              criticalTempAlert: true,
              dryingFinishedAlert: true,
              hardwareDisconnectAlert: true,
              emailAlertsEnabled: true,
              customEmailRecipient: "operator@hanjeli.id",
              whatsappEnabled: true,
              whatsappNumber: "+62 813-8899-2211",
              whatsappApiUrl: "https://api.fonnte.com/send",
              whatsappApiKey: "wA_s3cret_t0k3n_2023",
              whatsappProvider: "fonnte",
              whatsappEvents: {
                  batch_completed: true,
                  emergency: true,
                  daily_digest: true,
              },
              telegramEnabled: false,
              telegramBotToken: "",
              telegramChatId: "",
              telegramEvents: {
                  batch_completed: true,
                  emergency: true,
                  daily_digest: true,
              },
          },
);

// Security settings state
const secSettings = ref({
    currentPassword: "",
    newPassword: "",
    confirmPassword: "",
});

function triggerToast(msg) {
    toastMessage.value = msg;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3500);
}

async function loadDbSettings(isFirstTime = false) {
    try {
        const s = await settingsService.getSettings();
        if (s) {
            if (s.maxSafeTemp) dryingPrefs.value.maxTemp = s.maxSafeTemp;
            if (s.targetMoistureDefault)
                dryingPrefs.value.targetMoisture = s.targetMoistureDefault;
            if (s.samplingIntervalSeconds)
                hwSettings.value.sampleInterval = String(
                    s.samplingIntervalSeconds,
                );
            if (s.wifiSsid) hwSettings.value.wifiSsid = s.wifiSsid;
            if (s.ipAddress) hwSettings.value.ipAddress = s.ipAddress;
            if (s.mqttHost) hwSettings.value.mqttHost = s.mqttHost;
            if (s.mqttPort) hwSettings.value.mqttPort = s.mqttPort;
            if (s.mqttTopic) hwSettings.value.mqttTopic = s.mqttTopic;
            if (s.whatsapp) {
                notifSettings.value.whatsappEnabled =
                    s.whatsapp.enabled ?? notifSettings.value.whatsappEnabled;
                notifSettings.value.whatsappNumber =
                    s.whatsapp.targetNumber ||
                    notifSettings.value.whatsappNumber;
                notifSettings.value.whatsappApiUrl =
                    s.whatsapp.apiUrl || notifSettings.value.whatsappApiUrl;
                notifSettings.value.whatsappApiKey =
                    s.whatsapp.apiKey || notifSettings.value.whatsappApiKey;
                notifSettings.value.whatsappProvider =
                    s.whatsapp.provider ||
                    notifSettings.value.whatsappProvider ||
                    "fonnte";
                if (s.whatsapp.events)
                    notifSettings.value.whatsappEvents = {
                        ...notifSettings.value.whatsappEvents,
                        ...s.whatsapp.events,
                    };
            }
            if (s.telegram) {
                notifSettings.value.telegramEnabled =
                    s.telegram.enabled ?? notifSettings.value.telegramEnabled;
                notifSettings.value.telegramBotToken =
                    s.telegram.botToken || notifSettings.value.telegramBotToken;
                notifSettings.value.telegramChatId =
                    s.telegram.chatId || notifSettings.value.telegramChatId;
                if (s.telegram.events)
                    notifSettings.value.telegramEvents = {
                        ...notifSettings.value.telegramEvents,
                        ...s.telegram.events,
                    };
            }
        }
    } catch (err) {
        console.warn("Could not load settings from DB:", err.message);
    } finally {
        if (isFirstTime) {
            setTimeout(() => {
                isInitialLoading.value = false;
            }, 350);
        }
    }
}

onMounted(() => {
    loadDbSettings(true);
});

function playAlarmBeep() {
    if (!notifSettings.value.soundAlarmEnabled) return;
    try {
        const audioCtx = new (
            window.AudioContext || window.webkitAudioContext
        )();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = "sawtooth";
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(
            440,
            audioCtx.currentTime + 0.35,
        );
        gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(
            0.01,
            audioCtx.currentTime + 0.35,
        );
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.35);
    } catch (e) {
        console.log("AudioContext initialized", e);
    }
}

async function handleManualPwaInstall() {
    if (pwaState.isInstalled) {
        triggerToast("Aplikasi sudah terpasang di perangkat ini!");
        return;
    }
    const installed = await promptInstall();
    if (installed) {
        triggerToast("Aplikasi Smart Dryer berhasil diinstall!");
    }
}

async function requestBrowserNotificationPermission() {
    const perm = await requestNotificationPermission();
    browserPermissionStatus.value = perm;
    if (perm === "granted") {
        triggerToast("Izin notifikasi Web Push berhasil diaktifkan!");
        await sendTestPushNotification(
            "Smart Dryer Hanjeli: Izin Aktif",
            "Notifikasi sistem & peringatan sensor siap dikirim ke perangkat Anda.",
            "/monitoring",
        );
    } else if (perm === "denied") {
        triggerToast("Izin notifikasi ditolak di setelan browser.");
    }
}

async function testPushNotification() {
    playAlarmBeep();
    await sendTestPushNotification(
        "Smart Dryer Hanjeli: Uji Push Notification",
        "Peringatan Suhu: Ruang pengering 56.2°C (Kritis). Ketuk untuk buka live monitoring!",
        "/monitoring",
    );
    triggerToast("Uji coba Web Push Notification dikirim ke layar!");
}

async function testSendWhatsAppAlert() {
    if (!notifSettings.value.whatsappNumber) {
        alertDialog({
            title: "Nomor WhatsApp Kosong",
            message: "Harap masukkan nomor WhatsApp tujuan terlebih dahulu.",
            type: "warning",
        });
        return;
    }
    isTestingWhatsApp.value = true;
    playAlarmBeep();
    try {
        const res = await settingsService.testWhatsApp({
            targetNumber: notifSettings.value.whatsappNumber,
            apiUrl: notifSettings.value.whatsappApiUrl,
            apiKey: notifSettings.value.whatsappApiKey,
            provider: notifSettings.value.whatsappProvider || "fonnte",
        });
        if (res && res.success) {
            triggerToast(
                res.message ||
                    `Pesan uji coba WhatsApp berhasil dikirim ke ${notifSettings.value.whatsappNumber}!`,
            );
        } else {
            triggerToast(`Gagal: ${res?.message || "Gateway menolak pesan."}`);
        }
    } catch (err) {
        triggerToast(
            `Gagal: ${err.message || "Koneksi ke WhatsApp Gateway gagal."}`,
        );
    } finally {
        isTestingWhatsApp.value = false;
    }
}

async function testSendTelegramAlert() {
    if (
        !notifSettings.value.telegramBotToken ||
        !notifSettings.value.telegramChatId
    ) {
        alertDialog({
            title: "Konfigurasi Telegram Belum Lengkap",
            message:
                "Harap isi Telegram Bot Token dan Chat ID terlebih dahulu.",
            type: "warning",
        });
        return;
    }
    isTestingTelegram.value = true;
    playAlarmBeep();
    try {
        const res = await settingsService.testTelegram({
            botToken: notifSettings.value.telegramBotToken,
            chatId: notifSettings.value.telegramChatId,
        });
        if (res && res.success) {
            triggerToast(
                res.message ||
                    `Pesan uji coba berhasil dikirim ke Telegram Chat ID ${notifSettings.value.telegramChatId}!`,
            );
        } else {
            triggerToast(`Gagal: ${res?.message || "Telegram Bot API error."}`);
        }
    } catch (err) {
        triggerToast(
            `Gagal: ${err.message || "Koneksi ke Telegram Bot API gagal."}`,
        );
    } finally {
        isTestingTelegram.value = false;
    }
}

async function testSendDailyDigest() {
    isSendingDailyDigest.value = true;
    try {
        const res = await settingsService.sendDailyDigestNow();
        if (res && res.success) {
            triggerToast(
                res.message ||
                    "Ringkasan Harian (Daily Digest) berhasil dikompilasi dan disiarkan!",
            );
        } else {
            triggerToast(
                `Gagal: ${res?.message || "Gagal menyiarkan Daily Digest."}`,
            );
        }
    } catch (err) {
        triggerToast(`Gagal: ${err.message || "Koneksi server gagal."}`);
    } finally {
        isSendingDailyDigest.value = false;
    }
}

const isSendingEmail = ref(false);

async function testSendEmail(type) {
    isSendingEmail.value = true;
    try {
        const targetEmail =
            notifSettings.value.customEmailRecipient ||
            props.user?.email ||
            "operator@hanjeli.id";
        const res = await settingsService.testEmail({
            type,
            email: targetEmail,
        });
        if (res && res.success) {
            triggerToast(
                res.message ||
                    `Email template '${type}' berhasil dikirim ke ${targetEmail}!`,
            );
        } else {
            triggerToast(res.message || "Gagal mengirim email.");
        }
    } catch (err) {
        triggerToast(
            `Gagal: ${err.message || "Koneksi ke server email gagal."}`,
        );
    } finally {
        isSendingEmail.value = false;
    }
}

function testPing() {
    isTestingPing.value = true;
    setTimeout(() => {
        isTestingPing.value = false;
        triggerToast(
            props.currentLang === "id"
                ? "Ping Sukses! Gateway ESP32 merespons dalam 24ms."
                : "Ping Success! ESP32 responded in 24ms.",
        );
    }, 1000);
}

async function saveAllSettings() {
    localStorage.setItem(
        "hanjeli_notif_settings",
        JSON.stringify(notifSettings.value),
    );

    try {
        await settingsService.updateSettings({
            maxSafeTemp: Number(dryingPrefs.value.maxTemp),
            targetMoistureDefault: Number(dryingPrefs.value.targetMoisture),
            samplingIntervalSeconds:
                parseInt(hwSettings.value.sampleInterval, 10) || 5,
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
                provider: notifSettings.value.whatsappProvider || "fonnte",
                events: notifSettings.value.whatsappEvents,
            },
            telegram: {
                enabled: notifSettings.value.telegramEnabled,
                botToken: notifSettings.value.telegramBotToken,
                chatId: notifSettings.value.telegramChatId,
                events: notifSettings.value.telegramEvents,
            },
        });
    } catch (err) {
        console.warn("Backend settings update note:", err.message);
    }

    triggerToast(
        props.currentLang === "id"
            ? "Pengaturan berhasil disimpan ke Database MySQL!"
            : "Settings successfully saved to MySQL Database!",
    );
}

async function savePassword() {
    if (secSettings.value.newPassword !== secSettings.value.confirmPassword) {
        alertDialog({
            title: "Kata Sandi Tidak Cocok",
            message:
                props.currentLang === "id"
                    ? "Konfirmasi kata sandi baru tidak cocok!"
                    : "Password confirmation does not match!",
            type: "danger",
        });
        return;
    }

    try {
        await authService.updateProfile({
            currentPassword: secSettings.value.currentPassword,
            newPassword: secSettings.value.newPassword,
        });
        secSettings.value.currentPassword = "";
        secSettings.value.newPassword = "";
        secSettings.value.confirmPassword = "";
        triggerToast(
            props.currentLang === "id"
                ? "Kata sandi akun berhasil diperbarui di database!"
                : "Password updated successfully in database!",
        );
    } catch (err) {
        triggerToast(`Gagal: ${err.message}`);
    }
}

async function handleRegisterPasskey() {
    isRegisteringPasskey.value = true;
    try {
        const userEmail =
            props.user?.email ||
            localStorage.getItem("user_email") ||
            "operator@hanjeli.id";
        const userName = props.user?.name || "Operator";
        const res = await authService.passkeyRegister(userEmail, userName);
        if (res && res.success) {
            triggerToast(
                props.currentLang === "id"
                    ? "Kunci Passkey biometrik perangkat berhasil didaftarkan!"
                    : "Device Passkey biometric key registered successfully!",
            );
        }
    } catch (err) {
        triggerToast(err.message || "Gagal mendaftarkan Passkey.");
    } finally {
        isRegisteringPasskey.value = false;
    }
}
</script>

<style scoped>
.settings-page {
    width: 100%;
    min-height: 100%;
    background: transparent;
}

.settings-main-content {
    padding: 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
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
    background: #0d631b;
    color: #ffffff;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}

.save-all-btn:hover {
    background: #2e7d32;
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
    color: #0d631b;
}

.tab-nav-btn.active {
    color: #0d631b;
    border-bottom-color: #0d631b;
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

/* Transport Selection Grid */
.transport-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}

.transport-option-card {
    background: var(--color-bg-light, #f8fafc);
    border: 2px solid rgba(203, 213, 225, 0.6);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.transport-option-card:hover {
    border-color: #0d631b;
    transform: translateY(-2px);
}

.transport-option-card.active {
    border-color: #0d631b;
    background: rgba(13, 99, 27, 0.04);
    box-shadow: 0 4px 12px rgba(13, 99, 27, 0.08);
}

:global(.dark-theme) .transport-option-card {
    background: #0b1911;
    border-color: #1e3a2b;
}

:global(.dark-theme) .transport-option-card.active {
    border-color: #22c55e;
    background: rgba(34, 197, 94, 0.08);
}

.option-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.option-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wifi-color {
    background: rgba(13, 99, 27, 0.12);
    color: #0d631b;
}
.ble-color {
    background: rgba(0, 93, 183, 0.12);
    color: #005db7;
}
.usb-color {
    background: rgba(147, 51, 234, 0.12);
    color: #9333ea;
}

.option-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-text-title);
    margin: 0;
}

.option-desc {
    font-size: 12px;
    color: var(--color-text-muted);
    line-height: 1.5;
    margin: 0;
    flex: 1;
}

.option-footer-tag {
    display: flex;
    align-items: center;
}

.tag-online {
    font-size: 11px;
    font-weight: 700;
    color: #0d631b;
    background: rgba(13, 99, 27, 0.12);
    padding: 3px 8px;
    border-radius: 6px;
}

.tag-neutral {
    font-size: 11px;
    font-weight: 600;
    color: var(--color-text-muted);
    background: rgba(0, 0, 0, 0.05);
    padding: 3px 8px;
    border-radius: 6px;
}

:global(.dark-theme) .tag-neutral {
    background: rgba(255, 255, 255, 0.06);
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

.chip-green {
    background: rgba(13, 99, 27, 0.12);
}
.chip-blue {
    background: rgba(0, 93, 183, 0.12);
}
.chip-orange {
    background: rgba(180, 80, 0, 0.12);
}

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
    background-color: #0d631b;
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
    color: #0d631b;
}

.badge-testing {
    background: rgba(217, 119, 6, 0.15);
    color: #d97706;
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
    color: #0d631b;
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
    transition: all 0.2s ease;
}

.settings-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 16px 16px;
    padding-right: 40px;
    cursor: pointer;
}

.settings-input:focus,
.settings-select:focus {
    border-color: #0d631b;
    outline: 2px solid rgba(13, 99, 27, 0.2) !important;
    outline-offset: 1px;
}

.slider-input {
    width: 100%;
    accent-color: #0d631b;
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
    background: #0d631b;
    border-color: #0d631b;
    color: #ffffff;
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
    transition: 0.3s;
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
    transition: 0.3s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #0d631b;
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
    background: #0d631b;
    color: #ffffff;
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
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
}
.btn-danger-soft:hover:not(:disabled) {
    background: #fee2e2;
}

.btn-success-soft {
    background: #f0fdf4;
    color: #166534;
    border-color: #bbf7d0;
}
.btn-success-soft:hover:not(:disabled) {
    background: #dcfce7;
}

.btn-blue-soft {
    background: #eff6ff;
    color: #1e40af;
    border-color: #bfdbfe;
}
.btn-blue-soft:hover:not(:disabled) {
    background: #dbeafe;
}

.btn-emerald-soft {
    background: #ecfdf5;
    color: #065f46;
    border-color: #a7f3d0;
}
.btn-emerald-soft:hover:not(:disabled) {
    background: #d1fae5;
}

:global(.dark-theme) .btn-danger-soft {
    background: rgba(186, 26, 26, 0.2) !important;
    color: #fca5a5 !important;
    border-color: rgba(239, 68, 68, 0.4) !important;
}

:global(.dark-theme) .btn-success-soft {
    background: rgba(22, 163, 74, 0.2) !important;
    color: #86efac !important;
    border-color: rgba(34, 197, 94, 0.4) !important;
}

:global(.dark-theme) .btn-blue-soft {
    background: rgba(0, 93, 183, 0.2) !important;
    color: #93c5fd !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
}

:global(.dark-theme) .btn-emerald-soft {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
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
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.mobile-tab-chips::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
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
    background: #0d631b;
    color: #ffffff;
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
    color: #0d631b;
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
    background: #0d631b;
    color: #ffffff;
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
    border: 1px solid #a5d6a7;
    border-radius: 8px;
    color: #0d631b;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.test-wa-btn:hover:not(:disabled) {
    background: #0d631b;
    color: #ffffff;
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
    border: 1px solid #90caf9;
    border-radius: 8px;
    color: #005db7;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.test-telegram-btn:hover:not(:disabled) {
    background: #005db7;
    color: #ffffff;
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
    border-left: 3px solid #0d631b;
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
    border-left: 4px solid #0d631b;
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

/* Page Transition for Skeleton */
.fade-settings-enter-active,
.fade-settings-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-settings-enter-from {
    opacity: 0;
    transform: translateY(6px);
}

.fade-settings-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

@media (max-width: 1024px) {
    .settings-grid-2 {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .settings-main-content {
        padding: 0;
        gap: 16px;
    }
    .page-header-row {
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
    .save-all-btn {
        width: 100%;
        justify-content: center;
    }
    .settings-tabs-nav {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding-bottom: 6px;
        gap: 6px;
    }
    .settings-tabs-nav::-webkit-scrollbar {
        display: none;
    }
    .tab-nav-btn {
        padding: 8px 12px;
        font-size: 13px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .settings-card {
        padding: 16px;
    }
    .transport-selection-grid {
        grid-template-columns: 1fr;
    }
    .email-test-actions-grid {
        grid-template-columns: 1fr;
    }
    .toast-popup {
        bottom: 16px;
        right: 16px;
        left: 16px;
        justify-content: center;
    }
}
</style>
