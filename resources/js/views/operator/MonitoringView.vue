<template>
    <div class="monitoring-page">
        <Transition name="fade-monitoring" mode="out-in">
            <!-- High-Fidelity Shimmer Skeleton Loading State -->
            <MonitoringSkeleton v-if="isInitialLoading" key="skeleton" />

            <!-- Loaded Live Monitoring Content -->
            <div v-else class="monitoring-loaded-container" key="content">
                <!-- Desktop & Tablet Layout -->
                <div class="monitoring-main-container">
                    <!-- Header Area -->
                    <div class="header-action-row">
                        <div class="title-group">
                            <div class="title-with-badge">
                                <h1 class="page-title">
                                    {{ $t("monitoring.title") }}
                                </h1>
                            </div>
                            <p class="page-subtitle text-live">
                                <span
                                    class="live-dot"
                                    :class="{ 'live-dot-active': isDataActive }"
                                ></span>
                                <span>{{
                                    isDataActive
                                        ? $t("monitoring.streamConnected")
                                        : $t("monitoring.streamStandby")
                                }}</span>
                            </p>
                        </div>

                        <div class="header-right-actions">
                            <button
                                class="btn-export-csv"
                                @click="exportTelemetryCsv"
                                :title="$t('monitoring.exportCsvTitle')"
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
                                        d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                                    ></path>
                                    <polyline
                                        points="7 10 12 15 17 10"
                                    ></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>{{ $t('monitoring.exportCsv') }}</span>
                            </button>

                            <div
                                class="system-active-badge"
                                :class="
                                    isDataActive
                                        ? 'badge-online'
                                        : 'badge-standby'
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
                                    <polyline
                                        points="23 4 23 10 17 10"
                                    ></polyline>
                                    <polyline
                                        points="1 20 1 14 7 14"
                                    ></polyline>
                                    <path
                                        d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"
                                    ></path>
                                </svg>
                                <span
                                    >{{ $t('common.status') }}:
                                    <strong>{{
                                        isDataActive ? $t('monitoring.live') : $t('monitoring.standby')
                                    }}</strong></span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Dual Connectivity Status & Control Card -->
                    <ConnectionStatusCard
                        @open-wifi-setup="isWifiSetupOpen = true"
                    />

                    <!-- Live Sensor Health & Anomaly Diagnostics Bar -->
                    <div
                        class="sensor-health-card"
                        :class="
                            anomalyHealth?.status === 'CRITICAL'
                                ? 'health-critical'
                                : anomalyHealth?.status === 'WARNING'
                                  ? 'health-warning'
                                  : 'health-optimal'
                        "
                    >
                        <div class="health-header-row">
                            <div class="health-title-group">
                                <div class="health-icon-box">
                                    <svg
                                        v-if="
                                            anomalyHealth?.status ===
                                                'HEALTHY' ||
                                            !anomalyHealth ||
                                            anomalyHealth?.anomalyCount === 0
                                        "
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                        ></path>
                                        <polyline
                                            points="9 12 11 14 15 10"
                                        ></polyline>
                                    </svg>
                                    <svg
                                        v-else
                                        width="20"
                                        height="20"
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
                                </div>
                                <div>
                                    <div class="health-badge-row">
                                        <span class="health-status-badge">
                                            {{
                                                anomalyHealth?.statusText ||
                                                $t("monitoring.healthOptimal")
                                            }}
                                            •
                                            {{
                                                anomalyHealth?.healthScore ||
                                                100
                                            }}% {{ $t("monitoring.healthIntegrity") }}
                                        </span>
                                    </div>
                                    <h3 class="health-card-heading">
                                        {{
                                            (anomalyHealth?.anomalyCount || 0) >
                                            0
                                                ? `${anomalyHealth.anomalyCount} ${$t("monitoring.anomaliesDetected")}`
                                                : $t("monitoring.allSensorsNormal")
                                        }}
                                    </h3>
                                </div>
                            </div>

                            <div class="health-actions">
                                <button
                                    v-if="
                                        (anomalyHealth?.anomalyCount || 0) > 0
                                    "
                                    type="button"
                                    class="btn-toggle-anomaly-details"
                                    @click="
                                        isAnomalyDetailsOpen =
                                            !isAnomalyDetailsOpen
                                    "
                                >
                                    <span>{{
                                        isAnomalyDetailsOpen
                                            ? $t("monitoring.hideDetails")
                                            : $t("monitoring.viewDiagnosis")
                                    }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="btn-scan-anomaly"
                                    :disabled="isAnomalyScanning"
                                    @click="handleRunAnomalyScan"
                                >
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <polyline
                                            points="23 4 23 10 17 10"
                                        ></polyline>
                                        <polyline
                                            points="1 20 1 14 7 14"
                                        ></polyline>
                                        <path
                                            d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"
                                        ></path>
                                    </svg>
                                    <span>{{
                                        isAnomalyScanning
                                            ? $t("monitoring.scanning")
                                            : $t("monitoring.rescan")
                                    }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Anomaly Diagnostics Detail List (If Anomalies Detected) -->
                        <Transition name="drawer-fade">
                            <div
                                v-if="
                                    (anomalyHealth?.anomalyCount || 0) > 0 &&
                                    isAnomalyDetailsOpen
                                "
                                class="anomaly-diagnosis-list"
                            >
                                <div
                                    v-for="(
                                        anom, aIdx
                                    ) in anomalyHealth.anomalies"
                                    :key="aIdx"
                                    class="anomaly-detail-item"
                                    :class="`level-${(anom.level || 'warning').toLowerCase()}`"
                                >
                                    <div class="anom-item-head">
                                        <span class="anom-level-chip">{{
                                            anom.level
                                        }}</span>
                                        <strong class="anom-title">{{
                                            anom.title
                                        }}</strong>
                                    </div>
                                    <p class="anom-desc">
                                        {{ anom.description }}
                                    </p>
                                    <div class="anom-meta-box">
                                        <div class="anom-cause">
                                            <span
                                                style="
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 4px;
                                                    font-weight: bold;
                                                "
                                            >
                                                <svg
                                                    width="13"
                                                    height="13"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.2"
                                                >
                                                    <circle
                                                        cx="11"
                                                        cy="11"
                                                        r="8"
                                                    ></circle>
                                                    <line
                                                        x1="21"
                                                        y1="21"
                                                        x2="16.65"
                                                        y2="16.65"
                                                    ></line>
                                                </svg>
                                                {{ $t("monitoring.rootCauseLabel") }}
                                            </span>
                                            {{ anom.rootCause }}
                                        </div>
                                        <div class="anom-action">
                                            <span
                                                style="
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 4px;
                                                    font-weight: bold;
                                                "
                                            >
                                                <svg
                                                    width="13"
                                                    height="13"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.2"
                                                >
                                                    <path
                                                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"
                                                    ></path>
                                                </svg>
                                                {{ $t("monitoring.actionLabel") }}
                                            </span>
                                            {{ anom.action }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- 4 Top KPI Cards Grid -->
                    <div class="stats-grid-4">
                        <!-- 1. Suhu Ruangan -->
                        <div
                            class="stat-card"
                            :class="{ 'stat-card-standby': !isDataActive }"
                        >
                            <div class="stat-card-header">
                                <span class="stat-label"
                                    >{{ $t("monitoring.kpiInternalTemp") }}</span
                                >
                                <div class="icon-circle icon-red-subtle">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#BA1A1A"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value-row">
                                <span class="stat-number">{{
                                    isDataActive
                                        ? telemetry.tempInternal.toFixed(1)
                                        : "--"
                                }}</span>
                                <span class="stat-unit">°C</span>
                            </div>
                            <div
                                class="stat-trend"
                                :class="
                                    isDataActive
                                        ? 'trend-up-green'
                                        : 'text-muted'
                                "
                            >
                                <span class="delta-chip"
                                    >ΔT:
                                    {{
                                        isDataActive
                                            ? (
                                                  telemetry.tempInternal -
                                                  telemetry.tempExternal
                                              ).toFixed(1)
                                            : "--"
                                    }}°C</span
                                >
                                <span
                                    >{{ $t("monitoring.kpiOutside") }}
                                    {{
                                        isDataActive
                                            ? telemetry.tempExternal.toFixed(
                                                  1,
                                               ) + "°C"
                                            : "--"
                                    }}</span
                                >
                            </div>
                        </div>

                        <!-- 2. Kelembapan -->
                        <div
                            class="stat-card"
                            :class="{ 'stat-card-standby': !isDataActive }"
                        >
                            <div class="stat-card-header">
                                <span class="stat-label"
                                    >{{ $t("monitoring.kpiInternalHum") }}</span
                                >
                                <div class="icon-circle icon-blue-subtle">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#005DB7"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value-row">
                                <span class="stat-number">{{
                                    isDataActive
                                        ? telemetry.humidityInternal.toFixed(0)
                                        : "--"
                                }}</span>
                                <span class="stat-unit">% RH</span>
                            </div>
                            <div
                                class="stat-trend"
                                :class="
                                    isDataActive
                                        ? 'trend-down-blue'
                                        : 'text-muted'
                                "
                            >
                                <span class="delta-chip-blue"
                                    >{{ $t("monitoring.kpiOutside") }}
                                    {{
                                        isDataActive
                                            ? telemetry.humidityExternal.toFixed(
                                                  0,
                                               ) + "%"
                                            : "--"
                                    }}</span
                                >
                                <span>{{ $t("monitoring.kpiTargetHum") }}</span>
                            </div>
                        </div>

                        <!-- 3. Kondisi Cahaya & Radiasi -->
                        <div
                            class="stat-card"
                            :class="{ 'stat-card-standby': !isDataActive }"
                        >
                            <div class="stat-card-header">
                                <span class="stat-label"
                                    >{{ $t("monitoring.kpiSolarRadiation") }}</span
                                >
                                <div class="icon-circle icon-amber-subtle">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#D97706"
                                        stroke-width="2.2"
                                    >
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <path d="M12 2v2"></path>
                                        <path d="M12 20v2"></path>
                                        <path d="m4.93 4.93 1.41 1.41"></path>
                                        <path d="m17.66 17.66 1.41 1.41"></path>
                                        <path d="M2 12h2"></path>
                                        <path d="M20 12h2"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value-row">
                                <span class="stat-number">{{
                                    isDataActive
                                        ? telemetry.solarRadiation.toFixed(0)
                                        : "--"
                                }}</span>
                                <span class="stat-unit">W/m²</span>
                            </div>
                            <div
                                class="stat-trend"
                                :class="
                                    isDataActive
                                        ? 'trend-up-amber'
                                        : 'text-muted'
                                "
                            >
                                <span class="delta-chip-amber">
                                    <svg
                                        v-if="telemetry.solarRadiation > 600"
                                        width="12"
                                        height="12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                        style="
                                            display: inline-block;
                                            vertical-align: middle;
                                            margin-right: 3px;
                                        "
                                    >
                                        <circle cx="12" cy="12" r="5"></circle>
                                        <line
                                            x1="12"
                                            y1="1"
                                            x2="12"
                                            y2="3"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="21"
                                            x2="12"
                                            y2="23"
                                        ></line>
                                        <line
                                            x1="4.22"
                                            y1="4.22"
                                            x2="5.64"
                                            y2="5.64"
                                        ></line>
                                        <line
                                            x1="18.36"
                                            y1="18.36"
                                            x2="19.78"
                                            y2="19.78"
                                        ></line>
                                    </svg>
                                    <svg
                                        v-else
                                        width="12"
                                        height="12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                        style="
                                            display: inline-block;
                                            vertical-align: middle;
                                            margin-right: 3px;
                                        "
                                    >
                                        <path
                                            d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"
                                        ></path>
                                    </svg>
                                    <span>{{
                                        telemetry.solarRadiation > 600
                                            ? $t("monitoring.kpiSunny")
                                            : $t("monitoring.kpiOvercast")
                                    }}</span>
                                </span>
                                <span>{{ $t("monitoring.kpiSolarGain") }}</span>
                            </div>
                        </div>

                        <!-- 4. Kadar Air & Bobot Hanjeli -->
                        <div
                            class="stat-card"
                            :class="{ 'stat-card-standby': !isDataActive }"
                        >
                            <div class="stat-card-header">
                                <span class="stat-label"
                                    >{{ $t("monitoring.kpiGrainMoisture") }}</span
                                >
                                <div class="icon-circle icon-purple-subtle">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#7E22CE"
                                        stroke-width="2.2"
                                    >
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <polyline
                                            points="12 7 12 12 15 15"
                                        ></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value-row">
                                <span class="stat-number">{{
                                    isDataActive
                                        ? telemetry.grainMoisture.toFixed(1)
                                        : "--"
                                }}</span>
                                <span class="stat-unit">%</span>
                            </div>
                            <div class="stat-trend">
                                <span class="delta-chip-purple"
                                    >{{ $t("monitoring.kpiTargetMoisture") }}</span
                                >
                                <span
                                    >{{ $t("monitoring.kpiWeight") }}
                                    {{
                                        isDataActive
                                            ? telemetry.weightCurrentKg.toFixed(
                                                  1,
                                               ) + " kg"
                                            : "--"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- SECTION 1: MASTER MULTI-SENSOR DUAL-AXIS ANALYTICS CHART -->
                    <!-- ========================================================================= -->
                    <div class="master-chart-card">
                        <!-- Master Chart Header & Interactive Series Controls -->
                        <div class="master-chart-header">
                            <div class="header-title-box">
                                <div class="chart-badge-icon">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#0D631B"
                                        stroke-width="2.2"
                                    >
                                        <polyline
                                            points="22 12 18 12 15 21 9 3 6 12 2 12"
                                        ></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="master-chart-heading">
                                        {{ $t("monitoring.masterChartTitle") }}
                                    </h2>
                                    <p class="master-chart-sub">
                                        {{ $t("monitoring.masterChartSub") }}
                                    </p>
                                </div>
                            </div>

                            <!-- Time Window Filter Buttons & Enlarge Fullscreen Button -->
                            <div class="chart-top-controls">
                                <div class="time-window-selector">
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active:
                                                timeWindow === 20 &&
                                                chartMode === 'live',
                                        }"
                                        @click="setTimeWindow(20, 'live')"
                                    >
                                        {{ $t("monitoring.live20") }}
                                    </button>
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active:
                                                timeWindow === 40 &&
                                                chartMode === 'live',
                                        }"
                                        @click="setTimeWindow(40, 'live')"
                                    >
                                        {{ $t("monitoring.live40") }}
                                    </button>
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active:
                                                timeWindow === 60 &&
                                                chartMode === 'live',
                                        }"
                                        @click="setTimeWindow(60, 'live')"
                                    >
                                        {{ $t("monitoring.live60") }}
                                    </button>
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active: chartMode === 'history',
                                        }"
                                        @click="setTimeWindow(24, 'history')"
                                    >
                                        {{ $t("monitoring.history24h") }}
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    class="btn-chart-enlarge"
                                    @click="openFullscreen('master')"
                                    :title="$t('monitoring.fullscreenTitle')"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <polyline
                                            points="15 3 21 3 21 9"
                                        ></polyline>
                                        <polyline
                                            points="9 21 3 21 3 15"
                                        ></polyline>
                                        <line
                                            x1="21"
                                            y1="3"
                                            x2="14"
                                            y2="10"
                                        ></line>
                                        <line
                                            x1="3"
                                            y1="21"
                                            x2="10"
                                            y2="14"
                                        ></line>
                                    </svg>
                                    <span>{{ $t("monitoring.fullscreen") }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Interactive Series Toggles (Legend Checkboxes) -->
                        <div class="series-toggle-toolbar">
                            <span class="series-lbl">{{ $t("monitoring.showParameters") }}</span>

                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.tempInternal }"
                                @click="
                                    seriesVisible.tempInternal =
                                        !seriesVisible.tempInternal
                                "
                            >
                                <span class="series-box-dot bg-red"></span>
                                <span>{{ $t("monitoring.seriesTempInt") }}</span>
                            </button>

                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.tempExternal }"
                                @click="
                                    seriesVisible.tempExternal =
                                        !seriesVisible.tempExternal
                                "
                            >
                                <span class="series-box-dot bg-sky"></span>
                                <span>{{ $t("monitoring.seriesTempExt") }}</span>
                            </button>

                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{
                                    active: seriesVisible.humidityInternal,
                                }"
                                @click="
                                    seriesVisible.humidityInternal =
                                        !seriesVisible.humidityInternal
                                "
                            >
                                <span class="series-box-dot bg-blue"></span>
                                <span>{{ $t("monitoring.seriesHum") }}</span>
                            </button>

                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.grainMoisture }"
                                @click="
                                    seriesVisible.grainMoisture =
                                        !seriesVisible.grainMoisture
                                "
                            >
                                <span class="series-box-dot bg-purple"></span>
                                <span>{{ $t("monitoring.seriesMoisture") }}</span>
                            </button>

                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{
                                    active: seriesVisible.solarRadiation,
                                }"
                                @click="
                                    seriesVisible.solarRadiation =
                                        !seriesVisible.solarRadiation
                                "
                            >
                                <span class="series-box-dot bg-amber"></span>
                                <span>{{ $t("monitoring.seriesSolar") }}</span>
                            </button>

                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.safetyZone }"
                                @click="
                                    seriesVisible.safetyZone =
                                        !seriesVisible.safetyZone
                                "
                            >
                                <span class="series-box-dot bg-green"></span>
                                <span>{{ $t("monitoring.seriesSafetyZone") }}</span>
                            </button>
                        </div>

                        <!-- Master SVG Dynamic Graph Canvas -->
                        <div
                            class="svg-canvas-wrapper"
                            @mousemove="handleChartHover"
                            @mouseleave="hoveredIndex = null"
                        >
                            <svg
                                viewBox="0 0 1000 360"
                                class="master-svg-chart"
                                preserveAspectRatio="none"
                            >
                                <defs>
                                    <!-- Gradients -->
                                    <linearGradient
                                        id="masterTempGrad"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#BA1A1A"
                                            stop-opacity="0.32"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#BA1A1A"
                                            stop-opacity="0.0"
                                        />
                                    </linearGradient>
                                    <linearGradient
                                        id="masterHumGrad"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#005DB7"
                                            stop-opacity="0.22"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#005DB7"
                                            stop-opacity="0.0"
                                        />
                                    </linearGradient>
                                    <linearGradient
                                        id="masterSolarGrad"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#D97706"
                                            stop-opacity="0.25"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#D97706"
                                            stop-opacity="0.02"
                                        />
                                    </linearGradient>
                                    <linearGradient
                                        id="masterSafetyGrad"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#10B981"
                                            stop-opacity="0.12"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#10B981"
                                            stop-opacity="0.06"
                                        />
                                    </linearGradient>
                                </defs>

                                <!-- 1. Background Grid Guidelines & Axis Labels -->
                                <!-- Left Y-Axis Grid (Suhu 0 - 70°C) -->
                                <line
                                    x1="50"
                                    y1="50"
                                    x2="950"
                                    y2="50"
                                    stroke="rgba(148, 163, 184, 0.25)"
                                    stroke-dasharray="3 3"
                                />
                                <text
                                    x="42"
                                    y="54"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="end"
                                >
                                    60°C
                                </text>
                                <text
                                    x="958"
                                    y="54"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="start"
                                >
                                    85%
                                </text>

                                <line
                                    x1="50"
                                    y1="120"
                                    x2="950"
                                    y2="120"
                                    stroke="rgba(148, 163, 184, 0.25)"
                                    stroke-dasharray="3 3"
                                />
                                <text
                                    x="42"
                                    y="124"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="end"
                                >
                                    50°C
                                </text>
                                <text
                                    x="958"
                                    y="124"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="start"
                                >
                                    70%
                                </text>

                                <line
                                    x1="50"
                                    y1="190"
                                    x2="950"
                                    y2="190"
                                    stroke="rgba(148, 163, 184, 0.25)"
                                    stroke-dasharray="3 3"
                                />
                                <text
                                    x="42"
                                    y="194"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="end"
                                >
                                    40°C
                                </text>
                                <text
                                    x="958"
                                    y="194"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="start"
                                >
                                    50%
                                </text>

                                <line
                                    x1="50"
                                    y1="260"
                                    x2="950"
                                    y2="260"
                                    stroke="rgba(148, 163, 184, 0.25)"
                                    stroke-dasharray="3 3"
                                />
                                <text
                                    x="42"
                                    y="264"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="end"
                                >
                                    30°C
                                </text>
                                <text
                                    x="958"
                                    y="264"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="start"
                                >
                                    30%
                                </text>

                                <line
                                    x1="50"
                                    y1="310"
                                    x2="950"
                                    y2="310"
                                    stroke="rgba(148, 163, 184, 0.6)"
                                    stroke-width="1.2"
                                />
                                <text
                                    x="42"
                                    y="314"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="end"
                                >
                                    20°C
                                </text>
                                <text
                                    x="958"
                                    y="314"
                                    font-size="10"
                                    fill="#94A3B8"
                                    text-anchor="start"
                                >
                                    10%
                                </text>

                                <!-- 2. Optimal Safety Temperature Zone Band (40°C - 50°C) -->
                                <rect
                                    v-if="seriesVisible.safetyZone"
                                    x="50"
                                    y="120"
                                    width="900"
                                    height="70"
                                    fill="url(#masterSafetyGrad)"
                                    stroke="#10B981"
                                    stroke-width="1"
                                    stroke-dasharray="4 4"
                                    opacity="0.9"
                                />
                                <text
                                    v-if="seriesVisible.safetyZone"
                                    x="55"
                                    y="132"
                                    font-size="9"
                                    fill="#047857"
                                    font-weight="700"
                                >
                                    {{ $t("monitoring.optimalZoneLabel") }}
                                </text>

                                <!-- 3. Overheating Critical Line (55°C) -->
                                <line
                                    x1="50"
                                    y1="85"
                                    x2="950"
                                    y2="85"
                                    stroke="#BA1A1A"
                                    stroke-width="1.5"
                                    stroke-dasharray="5 5"
                                    opacity="0.6"
                                />
                                <text
                                    x="945"
                                    y="80"
                                    font-size="8.5"
                                    fill="#BA1A1A"
                                    font-weight="700"
                                    text-anchor="end"
                                >
                                    {{ $t("monitoring.maxLimitLabel") }}
                                </text>

                                <!-- 4. Target Moisture Line (12.0%) -->
                                <line
                                    v-if="seriesVisible.grainMoisture"
                                    x1="50"
                                    y1="295"
                                    x2="950"
                                    y2="295"
                                    stroke="#7E22CE"
                                    stroke-width="1.5"
                                    stroke-dasharray="4 4"
                                    opacity="0.7"
                                />
                                <text
                                    v-if="seriesVisible.grainMoisture"
                                    x="945"
                                    y="290"
                                    font-size="8.5"
                                    fill="#7E22CE"
                                    font-weight="700"
                                    text-anchor="end"
                                >
                                    {{ $t("monitoring.targetMoistureLabel") }}
                                </text>

                                <!-- 5. Solar Radiation Area (Background) -->
                                <path
                                    v-if="
                                        seriesVisible.solarRadiation &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterSolarAreaPath"
                                    fill="url(#masterSolarGrad)"
                                />
                                <path
                                    v-if="
                                        seriesVisible.solarRadiation &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterSolarLinePath"
                                    fill="none"
                                    stroke="#D97706"
                                    stroke-width="1.8"
                                    opacity="0.8"
                                />

                                <!-- 6. Humidity Line & Area -->
                                <path
                                    v-if="
                                        seriesVisible.humidityInternal &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterHumAreaPath"
                                    fill="url(#masterHumGrad)"
                                />
                                <path
                                    v-if="
                                        seriesVisible.humidityInternal &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterHumLinePath"
                                    fill="none"
                                    stroke="#005DB7"
                                    stroke-width="2.2"
                                />

                                <!-- 7. Grain Moisture Line -->
                                <path
                                    v-if="
                                        seriesVisible.grainMoisture &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterMoistureLinePath"
                                    fill="none"
                                    stroke="#7E22CE"
                                    stroke-width="2.5"
                                />

                                <!-- 8. External Temperature Line (Dashed Sky Blue) -->
                                <path
                                    v-if="
                                        seriesVisible.tempExternal &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterTempExtLinePath"
                                    fill="none"
                                    stroke="#0284C7"
                                    stroke-width="2"
                                    stroke-dasharray="5 4"
                                />

                                <!-- 9. Internal Temperature (Master Red Line & Glow) -->
                                <path
                                    v-if="
                                        seriesVisible.tempInternal &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterTempIntAreaPath"
                                    fill="url(#masterTempGrad)"
                                />
                                <path
                                    v-if="
                                        seriesVisible.tempInternal &&
                                        activeDataset.length >= 2
                                    "
                                    :d="masterTempIntLinePath"
                                    fill="none"
                                    stroke="#BA1A1A"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                />

                                <!-- 10. Data Point Nodes & Head Pulsing -->
                                <g v-if="activeDataset.length >= 2">
                                    <g
                                        v-for="(pt, idx) in activeDataset"
                                        :key="'mpt-' + idx"
                                    >
                                        <!-- Internal Temp Point -->
                                        <circle
                                            v-if="seriesVisible.tempInternal"
                                            :cx="pt.x"
                                            :cy="pt.yTempInt"
                                            :r="
                                                idx === activeDataset.length - 1
                                                    ? 5
                                                    : hoveredIndex === idx
                                                      ? 6
                                                      : 2.5
                                            "
                                            :fill="
                                                idx === activeDataset.length - 1
                                                    ? '#BA1A1A'
                                                    : '#FFFFFF'
                                            "
                                            stroke="#BA1A1A"
                                            :stroke-width="
                                                hoveredIndex === idx ? 2.5 : 1.5
                                            "
                                        />
                                        <!-- Moisture Point -->
                                        <circle
                                            v-if="seriesVisible.grainMoisture"
                                            :cx="pt.x"
                                            :cy="pt.yMoist"
                                            :r="
                                                idx === activeDataset.length - 1
                                                    ? 4
                                                    : hoveredIndex === idx
                                                      ? 5
                                                      : 2
                                            "
                                            fill="#7E22CE"
                                            stroke="#FFFFFF"
                                            stroke-width="1.2"
                                        />
                                    </g>

                                    <!-- Pulsing Ring on Latest Point -->
                                    <circle
                                        v-if="seriesVisible.tempInternal"
                                        :cx="
                                            activeDataset[
                                                activeDataset.length - 1
                                            ].x
                                        "
                                        :cy="
                                            activeDataset[
                                                activeDataset.length - 1
                                            ].yTempInt
                                        "
                                        r="10"
                                        fill="none"
                                        stroke="#BA1A1A"
                                        stroke-width="1.5"
                                        class="pulse-ring"
                                    />
                                </g>

                                <!-- 11. Interactive Hover Crosshair Line -->
                                <g v-if="hoveredPoint">
                                    <line
                                        :x1="hoveredPoint.x"
                                        y1="30"
                                        :x2="hoveredPoint.x"
                                        y2="310"
                                        stroke="#071E27"
                                        stroke-width="1.2"
                                        stroke-dasharray="3 3"
                                    />
                                </g>

                                <!-- 12. Bottom X-Axis Time Labels -->
                                <text
                                    v-for="(pt, idx) in sampledXLabels"
                                    :key="'xl-' + idx"
                                    :x="pt.x"
                                    y="330"
                                    font-size="10"
                                    fill="#64748B"
                                    text-anchor="middle"
                                >
                                    {{ pt.timeLabel }}
                                </text>

                                <!-- Empty Data State Overlay -->
                                <text
                                    v-if="activeDataset.length < 2"
                                    x="500"
                                    y="180"
                                    font-size="14"
                                    fill="#94A3B8"
                                    text-anchor="middle"
                                >
                                    {{
                                        isDataActive
                                            ? $t("monitoring.collectingData")
                                            : $t("monitoring.waitingStream")
                                    }}
                                </text>
                            </svg>

                            <!-- Interactive Hover Tooltip Card -->
                            <div
                                v-if="hoveredPoint"
                                class="interactive-tooltip"
                                :style="{
                                    left: hoveredTooltipLeft + 'px',
                                    top: '15px',
                                }"
                            >
                                <div class="tooltip-header">
                                    <span class="tooltip-time">{{
                                        hoveredPoint.fullTime
                                    }}</span>
                                    <span class="tooltip-tag"
                                        >{{ $t("monitoring.dataPoint") }} #{{
                                            hoveredIndex + 1
                                        }}</span
                                    >
                                </div>
                                <div class="tooltip-grid">
                                    <div class="tooltip-row">
                                        <span class="t-dot bg-red"></span
                                        ><span class="t-label"
                                            >{{ $t("monitoring.tooltipTempInt") }}:</span
                                        >
                                        <strong
                                            >{{
                                                hoveredPoint.tempInternal.toFixed(
                                                    1,
                                                )
                                            }}°C</strong
                                        >
                                    </div>
                                    <div class="tooltip-row">
                                        <span class="t-dot bg-sky"></span
                                        ><span class="t-label">{{ $t("monitoring.tooltipTempExt") }}:</span>
                                        <strong
                                            >{{
                                                hoveredPoint.tempExternal.toFixed(
                                                    1,
                                                )
                                            }}°C</strong
                                        >
                                    </div>
                                    <div class="tooltip-row">
                                        <span class="t-dot bg-blue"></span
                                        ><span class="t-label"
                                            >{{ $t("monitoring.tooltipHum") }}:</span
                                        >
                                        <strong
                                            >{{
                                                hoveredPoint.humidityInternal.toFixed(
                                                    0,
                                                )
                                            }}% RH</strong
                                        >
                                    </div>
                                    <div class="tooltip-row">
                                        <span class="t-dot bg-purple"></span
                                        ><span class="t-label">{{ $t("monitoring.tooltipMoisture") }}:</span>
                                        <strong
                                            >{{
                                                hoveredPoint.grainMoisture.toFixed(
                                                    1,
                                                )
                                            }}%</strong
                                        >
                                    </div>
                                    <div class="tooltip-row">
                                        <span class="t-dot bg-amber"></span
                                        ><span class="t-label"
                                            >{{ $t("monitoring.tooltipSolar") }}:</span
                                        >
                                        <strong
                                            >{{
                                                hoveredPoint.solarRadiation.toFixed(
                                                    0,
                                                )
                                            }}
                                            W/m²</strong
                                        >
                                    </div>
                                    <div class="tooltip-row">
                                        <span class="t-dot bg-green"></span
                                        ><span class="t-label"
                                            >{{ $t("monitoring.tooltipDelta") }}:</span
                                        >
                                        <strong
                                            >+{{
                                                (
                                                    hoveredPoint.tempInternal -
                                                    hoveredPoint.tempExternal
                                                ).toFixed(1)
                                            }}°C</strong
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Master Chart Bottom Analytics Summary Strip -->
                        <div class="master-analytics-strip">
                            <div class="analytic-item">
                                <span class="an-lbl">{{ $t("monitoring.statMaxTemp") }}</span>
                                <strong class="an-val text-red"
                                    >{{ masterStats.maxTemp }}°C</strong
                                >
                            </div>
                            <div class="analytic-item">
                                <span class="an-lbl">{{ $t("monitoring.statAvgTemp") }}</span>
                                <strong class="an-val"
                                    >{{ masterStats.avgTemp }}°C</strong
                                >
                            </div>
                            <div class="analytic-item">
                                <span class="an-lbl"
                                    >{{ $t("monitoring.statMaxDelta") }}</span
                                >
                                <strong class="an-val text-green-dark"
                                    >+{{ masterStats.maxDelta }}°C</strong
                                >
                            </div>
                            <div class="analytic-item">
                                <span class="an-lbl">{{ $t("monitoring.statAvgHum") }}</span>
                                <strong class="an-val text-blue"
                                    >{{ masterStats.avgHum }}% RH</strong
                                >
                            </div>
                            <div class="analytic-item">
                                <span class="an-lbl">{{ $t("monitoring.statDryingRate") }}</span>
                                <strong class="an-val text-purple"
                                    >{{ masterStats.dryingRate }}%/jam</strong
                                >
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- SECTION 2: SPECIALIZED DUAL SUB-CHARTS -->
                    <!-- ========================================================================= -->
                    <div class="sub-charts-grid-2">
                        <!-- Sub-Chart 1: Kurva Kinetika Dehidrasi & Kadar Air Gabah -->
                        <div class="sub-chart-card">
                            <div class="sub-chart-header">
                                <div class="sub-title-wrap">
                                    <div class="sub-icon icon-purple-subtle">
                                        <svg
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#7E22CE"
                                            stroke-width="2.2"
                                        >
                                            <path
                                                d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="sub-heading">
                                            {{ $t("monitoring.kineticsHeading") }}
                                        </h3>
                                        <p class="sub-desc">
                                            {{ $t("monitoring.kineticsSub") }}
                                        </p>
                                    </div>
                                </div>
                                <div class="sub-header-actions">
                                    <span class="moisture-badge"
                                        >{{ $t("monitoring.targetMoistureBadge") }}</span
                                    >
                                    <button
                                        type="button"
                                        class="btn-sub-enlarge"
                                        @click="openFullscreen('kinetics')"
                                        :title="$t('monitoring.kineticsEnlargeTitle')"
                                    >
                                        <svg
                                            width="14"
                                            height="14"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                        >
                                            <polyline
                                                points="15 3 21 3 21 9"
                                            ></polyline>
                                            <polyline
                                                points="9 21 3 21 3 15"
                                            ></polyline>
                                            <line
                                                x1="21"
                                                y1="3"
                                                x2="14"
                                                y2="10"
                                            ></line>
                                            <line
                                                x1="3"
                                                y1="21"
                                                x2="10"
                                                y2="14"
                                            ></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Interactive SVG Box with Hover -->
                            <div
                                class="sub-svg-box"
                                @mousemove="handleMoistureSubHover"
                                @mouseleave="hoveredMoistureIndex = null"
                            >
                                <svg
                                    viewBox="0 0 450 160"
                                    class="sub-svg"
                                    preserveAspectRatio="none"
                                >
                                    <!-- Grid -->
                                    <line
                                        x1="20"
                                        y1="30"
                                        x2="430"
                                        y2="30"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="3 3"
                                    />
                                    <line
                                        x1="20"
                                        y1="75"
                                        x2="430"
                                        y2="75"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="3 3"
                                    />
                                    <line
                                        x1="20"
                                        y1="120"
                                        x2="430"
                                        y2="120"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="3 3"
                                    />
                                    <line
                                        x1="20"
                                        y1="140"
                                        x2="430"
                                        y2="140"
                                        stroke="rgba(148, 163, 184, 0.5)"
                                    />

                                    <!-- Target 12% Line -->
                                    <line
                                        x1="20"
                                        y1="110"
                                        x2="430"
                                        y2="110"
                                        stroke="#7E22CE"
                                        stroke-dasharray="4 4"
                                        stroke-width="1.2"
                                    />

                                    <!-- Path -->
                                    <path
                                        v-if="activeDataset.length >= 2"
                                        :d="moistureSubAreaPath"
                                        fill="url(#masterHumGrad)"
                                    />
                                    <path
                                        v-if="activeDataset.length >= 2"
                                        :d="moistureSubLinePath"
                                        fill="none"
                                        stroke="#7E22CE"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                    />

                                    <!-- Hover Crosshair & Highlight Point -->
                                    <g v-if="hoveredMoisturePoint">
                                        <line
                                            :x1="hoveredMoisturePoint.subX"
                                            y1="15"
                                            :x2="hoveredMoisturePoint.subX"
                                            y2="140"
                                            stroke="#7E22CE"
                                            stroke-width="1.2"
                                            stroke-dasharray="3 3"
                                        />
                                        <circle
                                            :cx="hoveredMoisturePoint.subX"
                                            :cy="hoveredMoisturePoint.subY"
                                            r="5"
                                            fill="#7E22CE"
                                            stroke="#FFFFFF"
                                            stroke-width="2"
                                        />
                                    </g>
                                </svg>

                                <!-- Subchart Hover Tooltip -->
                                <div
                                    v-if="hoveredMoisturePoint"
                                    class="interactive-tooltip sub-tooltip"
                                    :style="{
                                        left: hoveredMoistureTooltipLeft + 'px',
                                        top: '8px',
                                    }"
                                >
                                    <div class="tooltip-header">
                                        <span class="tooltip-time">{{
                                            hoveredMoisturePoint.fullTime
                                        }}</span>
                                        <span class="tooltip-tag"
                                            >{{ $t("monitoring.pointNumber") }} #{{
                                                hoveredMoistureIndex + 1
                                            }}</span
                                        >
                                    </div>
                                    <div class="tooltip-grid">
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-purple"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipMoisture") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    hoveredMoisturePoint.grainMoisture.toFixed(
                                                        1,
                                                    )
                                                }}%</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-green"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.sniTarget") }}:</span
                                            >
                                            <strong>12.0%</strong>
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-sky"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.remainingReduction") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    Math.max(
                                                        0,
                                                        hoveredMoisturePoint.grainMoisture -
                                                            12.0,
                                                    ).toFixed(1)
                                                }}%</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-red"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempInt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    hoveredMoisturePoint.tempInternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-blue"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipHum") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    hoveredMoisturePoint.humidityInternal.toFixed(
                                                        0,
                                                    )
                                                }}% RH</strong
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sub-chart-footer">
                                <div class="sc-stat">
                                    <span class="sc-lbl"
                                        >{{ $t("monitoring.currentMoisture") }}:</span
                                    >
                                    <strong class="text-purple">{{
                                        isDataActive
                                            ? telemetry.grainMoisture.toFixed(
                                                  1,
                                              ) + "%"
                                            : "--"
                                    }}</strong>
                                </div>
                                <div class="sc-stat">
                                    <span class="sc-lbl"
                                        >{{ $t("monitoring.remainingReduction") }}:</span
                                    >
                                    <strong>{{
                                        isDataActive
                                            ? Math.max(
                                                  0,
                                                  telemetry.grainMoisture -
                                                      12.0,
                                              ).toFixed(1) + "%"
                                            : "--"
                                    }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Chart 2: Efisiensi Termal & Retensi Panas (Delta T) -->
                        <div class="sub-chart-card">
                            <div class="sub-chart-header">
                                <div class="sub-title-wrap">
                                    <div class="sub-icon icon-amber-subtle">
                                        <svg
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="#D97706"
                                            stroke-width="2.2"
                                        >
                                            <path
                                                d="M12 2v20M17 5H7M19 12H5M17 19H7"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="sub-heading">
                                            {{ $t("monitoring.deltaHeading") }}
                                        </h3>
                                        <p class="sub-desc">
                                            {{ $t("monitoring.deltaSub") }}
                                        </p>
                                    </div>
                                </div>
                                <div class="sub-header-actions">
                                    <span class="thermal-badge"
                                        >{{ $t("monitoring.greenhouseEffectBadge") }}</span
                                    >
                                    <button
                                        type="button"
                                        class="btn-sub-enlarge"
                                        @click="openFullscreen('delta')"
                                        :title="$t('monitoring.deltaEnlargeTitle')"
                                    >
                                        <svg
                                            width="14"
                                            height="14"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                        >
                                            <polyline
                                                points="15 3 21 3 21 9"
                                            ></polyline>
                                            <polyline
                                                points="9 21 3 21 3 15"
                                            ></polyline>
                                            <line
                                                x1="21"
                                                y1="3"
                                                x2="14"
                                                y2="10"
                                            ></line>
                                            <line
                                                x1="3"
                                                y1="21"
                                                x2="10"
                                                y2="14"
                                            ></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Interactive SVG Box with Hover -->
                            <div
                                class="sub-svg-box"
                                @mousemove="handleDeltaSubHover"
                                @mouseleave="hoveredDeltaIndex = null"
                            >
                                <svg
                                    viewBox="0 0 450 160"
                                    class="sub-svg"
                                    preserveAspectRatio="none"
                                >
                                    <line
                                        x1="20"
                                        y1="30"
                                        x2="430"
                                        y2="30"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="3 3"
                                    />
                                    <line
                                        x1="20"
                                        y1="75"
                                        x2="430"
                                        y2="75"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="3 3"
                                    />
                                    <line
                                        x1="20"
                                        y1="120"
                                        x2="430"
                                        y2="120"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="3 3"
                                    />
                                    <line
                                        x1="20"
                                        y1="140"
                                        x2="430"
                                        y2="140"
                                        stroke="rgba(148, 163, 184, 0.5)"
                                    />

                                    <path
                                        v-if="activeDataset.length >= 2"
                                        :d="deltaSubAreaPath"
                                        fill="url(#masterSafetyGrad)"
                                    />
                                    <path
                                        v-if="activeDataset.length >= 2"
                                        :d="deltaSubLinePath"
                                        fill="none"
                                        stroke="#0D631B"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                    />

                                    <!-- Hover Crosshair & Highlight Point -->
                                    <g v-if="hoveredDeltaPoint">
                                        <line
                                            :x1="hoveredDeltaPoint.subX"
                                            y1="15"
                                            :x2="hoveredDeltaPoint.subX"
                                            y2="140"
                                            stroke="#0D631B"
                                            stroke-width="1.2"
                                            stroke-dasharray="3 3"
                                        />
                                        <circle
                                            :cx="hoveredDeltaPoint.subX"
                                            :cy="hoveredDeltaPoint.subY"
                                            r="5"
                                            fill="#0D631B"
                                            stroke="#FFFFFF"
                                            stroke-width="2"
                                        />
                                    </g>
                                </svg>

                                <!-- Subchart Hover Tooltip -->
                                <div
                                    v-if="hoveredDeltaPoint"
                                    class="interactive-tooltip sub-tooltip"
                                    :style="{
                                        left: hoveredDeltaTooltipLeft + 'px',
                                        top: '8px',
                                    }"
                                >
                                    <div class="tooltip-header">
                                        <span class="tooltip-time">{{
                                            hoveredDeltaPoint.fullTime
                                        }}</span>
                                        <span class="tooltip-tag"
                                            >{{ $t("monitoring.pointNumber") }} #{{
                                                hoveredDeltaIndex + 1
                                            }}</span
                                        >
                                    </div>
                                    <div class="tooltip-grid">
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-green"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipDelta") }}:</span
                                            >
                                            <strong
                                                >+{{
                                                    hoveredDeltaPoint.delta.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-red"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempInt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    hoveredDeltaPoint.tempInternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-sky"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempExt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    hoveredDeltaPoint.tempExternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-amber"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipSolar") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    hoveredDeltaPoint.solarRadiation.toFixed(
                                                        0,
                                                    )
                                                }}
                                                W/m²</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-purple"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipAuxHeater") }}:</span
                                            >
                                            <strong>{{
                                                actuators.auxHeaterStatus
                                                    ? $t("monitoring.active") + " (" +
                                                      actuators.auxHeaterLevel +
                                                      "%)"
                                                    : $t("monitoring.standbyUpper")
                                            }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sub-chart-footer">
                                <div class="sc-stat">
                                    <span class="sc-lbl">{{ $t("monitoring.currentDelta") }}:</span>
                                    <strong class="text-green-dark"
                                        >+{{
                                            isDataActive
                                                ? (
                                                      telemetry.tempInternal -
                                                      telemetry.tempExternal
                                                ).toFixed(1) + "°C"
                                                : "--"
                                        }}</strong
                                    >
                                </div>
                                <div class="sc-stat">
                                    <span class="sc-lbl"
                                        >{{ $t("monitoring.auxHeaterStatus") }}:</span
                                    >
                                    <strong>{{
                                        actuators.auxHeaterStatus
                                            ? $t("monitoring.active") + " (" +
                                              actuators.auxHeaterLevel +
                                              "%)"
                                            : $t("monitoring.standbyUpper")
                                    }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- FULLSCREEN HIGH-DEFINITION MOVING GRAPH THEATER MODAL -->
                <!-- ========================================================================= -->
                <div
                    v-if="isChartFullscreen"
                    class="fullscreen-chart-overlay"
                    tabindex="-1"
                    @keydown.esc="closeFullscreen"
                >
                    <div class="fullscreen-container">
                        <!-- Fullscreen Top Navigation Bar -->
                        <div class="fullscreen-header">
                            <div class="fs-title-box">
                                <div
                                    class="fs-live-badge"
                                    :class="
                                        isDataActive
                                            ? 'badge-live-active'
                                            : 'badge-live-standby'
                                    "
                                >
                                    <span class="live-dot-pulse"></span>
                                    <span>{{
                                        isDataActive ? $t("monitoring.fsLiveEsp32") : $t("monitoring.fsStandby")
                                    }}</span>
                                </div>
                                <div>
                                    <h2 class="fs-title">
                                        {{ $t("monitoring.fsTitle") }}
                                    </h2>
                                    <p class="fs-sub">
                                        {{ $t("monitoring.fsSub") }}
                                    </p>
                                </div>
                            </div>

                            <!-- View Switcher Tabs inside Fullscreen -->
                            <div class="fs-tabs">
                                <button
                                    type="button"
                                    class="fs-tab-btn"
                                    :class="{
                                        active: fullscreenViewType === 'master',
                                    }"
                                    @click="fullscreenViewType = 'master'"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <polyline
                                            points="22 12 18 12 15 21 9 3 6 12 2 12"
                                        ></polyline>
                                    </svg>
                                    <span>{{ $t("monitoring.fsMultiSensor") }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="fs-tab-btn"
                                    :class="{
                                        active:
                                            fullscreenViewType === 'kinetics',
                                    }"
                                    @click="fullscreenViewType = 'kinetics'"
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
                                            d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"
                                        ></path>
                                    </svg>
                                    <span>{{ $t("monitoring.fsKinetics") }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="fs-tab-btn"
                                    :class="{
                                        active: fullscreenViewType === 'delta',
                                    }"
                                    @click="fullscreenViewType = 'delta'"
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
                                            d="M12 2v20M17 5H7M19 12H5M17 19H7"
                                        />
                                    </svg>
                                    <span>{{ $t("monitoring.fsThermalEfficiency") }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="fs-tab-btn"
                                    :class="{
                                        active: fullscreenViewType === 'all',
                                    }"
                                    @click="fullscreenViewType = 'all'"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <rect
                                            x="3"
                                            y="3"
                                            width="7"
                                            height="7"
                                        ></rect>
                                        <rect
                                            x="14"
                                            y="3"
                                            width="7"
                                            height="7"
                                        ></rect>
                                        <rect
                                            x="14"
                                            y="14"
                                            width="7"
                                            height="7"
                                        ></rect>
                                        <rect
                                            x="3"
                                            y="14"
                                            width="7"
                                            height="7"
                                        ></rect>
                                    </svg>
                                    <span>{{ $t("monitoring.fsGrid3in1") }}</span>
                                </button>
                            </div>

                            <!-- Controls: Time Range & Close -->
                            <div class="fs-controls">
                                <div class="time-window-selector">
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active:
                                                timeWindow === 20 &&
                                                chartMode === 'live',
                                        }"
                                        @click="setTimeWindow(20, 'live')"
                                    >
                                        {{ $t("monitoring.fs20Points") }}
                                    </button>
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active:
                                                timeWindow === 40 &&
                                                chartMode === 'live',
                                        }"
                                        @click="setTimeWindow(40, 'live')"
                                    >
                                        {{ $t("monitoring.fs40Points") }}
                                    </button>
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active:
                                                timeWindow === 60 &&
                                                chartMode === 'live',
                                        }"
                                        @click="setTimeWindow(60, 'live')"
                                    >
                                        {{ $t("monitoring.fs60Points") }}
                                    </button>
                                    <button
                                        type="button"
                                        class="time-btn"
                                        :class="{
                                            active: chartMode === 'history',
                                        }"
                                        @click="setTimeWindow(24, 'history')"
                                    >
                                        {{ $t("monitoring.fs24Hours") }}
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    class="fs-action-icon-btn"
                                    @click="toggleNativeFullscreen"
                                    :title="
                                        isNativeFullscreen
                                            ? $t('monitoring.fsExitFullscreenTitle')
                                            : $t('monitoring.fsEnterFullscreenTitle')
                                    "
                                >
                                    <svg
                                        v-if="!isNativeFullscreen"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-else
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"
                                        ></path>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    class="fs-close-btn"
                                    @click="closeFullscreen"
                                    :title="$t('monitoring.fsCloseBtn')"
                                >
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                    >
                                        <line
                                            x1="18"
                                            y1="6"
                                            x2="6"
                                            y2="18"
                                        ></line>
                                        <line
                                            x1="6"
                                            y1="6"
                                            x2="18"
                                            y2="18"
                                        ></line>
                                    </svg>
                                    <span>{{ $t("monitoring.fsCloseBtn") }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Series Filter Strip (for Master and Grid views) -->
                        <div
                            v-if="
                                fullscreenViewType === 'master' ||
                                fullscreenViewType === 'all'
                            "
                            class="fs-series-strip"
                        >
                            <span class="fs-series-lbl">{{ $t("monitoring.fsFilterParams") }}</span>
                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.tempInternal }"
                                @click="
                                    seriesVisible.tempInternal =
                                        !seriesVisible.tempInternal
                                "
                            >
                                <span class="series-box-dot bg-red"></span>
                                <span>{{ $t("monitoring.seriesTempInt") }}</span>
                            </button>
                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.tempExternal }"
                                @click="
                                    seriesVisible.tempExternal =
                                        !seriesVisible.tempExternal
                                "
                            >
                                <span class="series-box-dot bg-sky"></span>
                                <span>{{ $t("monitoring.seriesTempExt") }}</span>
                            </button>
                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{
                                    active: seriesVisible.humidityInternal,
                                }"
                                @click="
                                    seriesVisible.humidityInternal =
                                        !seriesVisible.humidityInternal
                                "
                            >
                                <span class="series-box-dot bg-blue"></span>
                                <span>{{ $t("monitoring.seriesHum") }}</span>
                            </button>
                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.grainMoisture }"
                                @click="
                                    seriesVisible.grainMoisture =
                                        !seriesVisible.grainMoisture
                                "
                            >
                                <span class="series-box-dot bg-purple"></span>
                                <span>{{ $t("monitoring.seriesMoisture") }}</span>
                            </button>
                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{
                                    active: seriesVisible.solarRadiation,
                                }"
                                @click="
                                    seriesVisible.solarRadiation =
                                        !seriesVisible.solarRadiation
                                "
                            >
                                <span class="series-box-dot bg-amber"></span>
                                <span>{{ $t("monitoring.seriesSolar") }}</span>
                            </button>
                            <button
                                type="button"
                                class="series-toggle-pill"
                                :class="{ active: seriesVisible.safetyZone }"
                                @click="
                                    seriesVisible.safetyZone =
                                        !seriesVisible.safetyZone
                                "
                            >
                                <span class="series-box-dot bg-green"></span>
                                <span>{{ $t("monitoring.seriesSafetyZone") }}</span>
                            </button>
                        </div>

                        <!-- Fullscreen Canvas Main Body -->
                        <div class="fs-canvas-area">
                            <!-- VIEW 1: Master Multi-Sensor Fullscreen High-Res Waveform -->
                            <div
                                v-if="fullscreenViewType === 'master'"
                                class="fs-svg-wrapper"
                                @mousemove="handleFsChartHover"
                                @mouseleave="fsHoveredIndex = null"
                            >
                                <svg
                                    viewBox="0 0 1600 620"
                                    class="fs-master-svg"
                                    preserveAspectRatio="none"
                                >
                                    <defs>
                                        <linearGradient
                                            id="fsTempGrad"
                                            x1="0"
                                            y1="0"
                                            x2="0"
                                            y2="1"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#BA1A1A"
                                                stop-opacity="0.35"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#BA1A1A"
                                                stop-opacity="0.0"
                                            />
                                        </linearGradient>
                                        <linearGradient
                                            id="fsHumGrad"
                                            x1="0"
                                            y1="0"
                                            x2="0"
                                            y2="1"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#005DB7"
                                                stop-opacity="0.25"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#005DB7"
                                                stop-opacity="0.0"
                                            />
                                        </linearGradient>
                                        <linearGradient
                                            id="fsSolarGrad"
                                            x1="0"
                                            y1="0"
                                            x2="0"
                                            y2="1"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#D97706"
                                                stop-opacity="0.28"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#D97706"
                                                stop-opacity="0.02"
                                            />
                                        </linearGradient>
                                        <linearGradient
                                            id="fsSafetyGrad"
                                            x1="0"
                                            y1="0"
                                            x2="0"
                                            y2="1"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#10B981"
                                                stop-opacity="0.14"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#10B981"
                                                stop-opacity="0.05"
                                            />
                                        </linearGradient>
                                    </defs>

                                    <!-- Grid & Scale Lines -->
                                    <line
                                        x1="80"
                                        y1="80"
                                        x2="1520"
                                        y2="80"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="85"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                        font-weight="600"
                                    >
                                        60°C
                                    </text>
                                    <text
                                        x="1532"
                                        y="85"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="start"
                                        font-weight="600"
                                    >
                                        85%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="180"
                                        x2="1520"
                                        y2="180"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="185"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                        font-weight="600"
                                    >
                                        50°C
                                    </text>
                                    <text
                                        x="1532"
                                        y="185"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="start"
                                        font-weight="600"
                                    >
                                        70%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="280"
                                        x2="1520"
                                        y2="280"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="285"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                        font-weight="600"
                                    >
                                        40°C
                                    </text>
                                    <text
                                        x="1532"
                                        y="285"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="start"
                                        font-weight="600"
                                    >
                                        50%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="380"
                                        x2="1520"
                                        y2="380"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="385"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                        font-weight="600"
                                    >
                                        30°C
                                    </text>
                                    <text
                                        x="1532"
                                        y="385"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="start"
                                        font-weight="600"
                                    >
                                        30%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="480"
                                        x2="1520"
                                        y2="480"
                                        stroke="rgba(148, 163, 184, 0.6)"
                                        stroke-width="1.5"
                                    />
                                    <text
                                        x="68"
                                        y="485"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                        font-weight="600"
                                    >
                                        20°C
                                    </text>
                                    <text
                                        x="1532"
                                        y="485"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="start"
                                        font-weight="600"
                                    >
                                        10%
                                    </text>

                                    <!-- Optimal Temperature Zone (40-50°C) -->
                                    <rect
                                        v-if="seriesVisible.safetyZone"
                                        x="80"
                                        y="180"
                                        width="1440"
                                        height="100"
                                        fill="url(#fsSafetyGrad)"
                                        stroke="#10B981"
                                        stroke-width="1.2"
                                        stroke-dasharray="5 5"
                                    />
                                    <text
                                        v-if="seriesVisible.safetyZone"
                                        x="90"
                                        y="198"
                                        font-size="12"
                                        fill="#047857"
                                        font-weight="800"
                                    >
                                        {{ $t("monitoring.optimalZoneLabel") }}
                                    </text>

                                    <!-- Overheating Critical Line (55°C) -->
                                    <line
                                        x1="80"
                                        y1="130"
                                        x2="1520"
                                        y2="130"
                                        stroke="#BA1A1A"
                                        stroke-width="1.8"
                                        stroke-dasharray="6 6"
                                        opacity="0.7"
                                    />
                                    <text
                                        x="1510"
                                        y="122"
                                        font-size="11"
                                        fill="#BA1A1A"
                                        font-weight="800"
                                        text-anchor="end"
                                    >
                                        {{ $t("monitoring.maxLimitLabel") }}
                                    </text>

                                    <!-- Target Moisture Line (12.0%) -->
                                    <line
                                        v-if="seriesVisible.grainMoisture"
                                        x1="80"
                                        y1="460"
                                        x2="1520"
                                        y2="460"
                                        stroke="#7E22CE"
                                        stroke-width="1.8"
                                        stroke-dasharray="5 5"
                                        opacity="0.8"
                                    />
                                    <text
                                        v-if="seriesVisible.grainMoisture"
                                        x="1510"
                                        y="452"
                                        font-size="11"
                                        fill="#7E22CE"
                                        font-weight="800"
                                        text-anchor="end"
                                    >
                                        {{ $t("monitoring.targetMoistureLabel") }}
                                    </text>

                                    <!-- Waveform Paths -->
                                    <!-- Solar -->
                                    <path
                                        v-if="
                                            seriesVisible.solarRadiation &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsSolarAreaPath"
                                        fill="url(#fsSolarGrad)"
                                    />
                                    <path
                                        v-if="
                                            seriesVisible.solarRadiation &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsSolarLinePath"
                                        fill="none"
                                        stroke="#D97706"
                                        stroke-width="2.5"
                                        opacity="0.85"
                                    />

                                    <!-- Humidity -->
                                    <path
                                        v-if="
                                            seriesVisible.humidityInternal &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsHumAreaPath"
                                        fill="url(#fsHumGrad)"
                                    />
                                    <path
                                        v-if="
                                            seriesVisible.humidityInternal &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsHumLinePath"
                                        fill="none"
                                        stroke="#005DB7"
                                        stroke-width="3"
                                    />

                                    <!-- Grain Moisture -->
                                    <path
                                        v-if="
                                            seriesVisible.grainMoisture &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsMoistureLinePath"
                                        fill="none"
                                        stroke="#7E22CE"
                                        stroke-width="3.5"
                                    />

                                    <!-- External Temp -->
                                    <path
                                        v-if="
                                            seriesVisible.tempExternal &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsTempExtLinePath"
                                        fill="none"
                                        stroke="#0284C7"
                                        stroke-width="2.5"
                                        stroke-dasharray="6 5"
                                    />

                                    <!-- Internal Temp Area & Line -->
                                    <path
                                        v-if="
                                            seriesVisible.tempInternal &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsTempIntAreaPath"
                                        fill="url(#fsTempGrad)"
                                    />
                                    <path
                                        v-if="
                                            seriesVisible.tempInternal &&
                                            fsActiveDataset.length >= 2
                                        "
                                        :d="fsTempIntLinePath"
                                        fill="none"
                                        stroke="#BA1A1A"
                                        stroke-width="4"
                                        stroke-linecap="round"
                                    />

                                    <!-- Point Nodes & Pulsing Ring -->
                                    <g v-if="fsActiveDataset.length >= 2">
                                        <g
                                            v-for="(pt, idx) in fsActiveDataset"
                                            :key="'fspt-' + idx"
                                        >
                                            <circle
                                                v-if="
                                                    seriesVisible.tempInternal
                                                "
                                                :cx="pt.x"
                                                :cy="pt.yTempInt"
                                                :r="
                                                    idx ===
                                                    fsActiveDataset.length - 1
                                                        ? 6.5
                                                        : fsHoveredIndex === idx
                                                          ? 8
                                                          : 3.5
                                                "
                                                :fill="
                                                    idx ===
                                                    fsActiveDataset.length - 1
                                                        ? '#BA1A1A'
                                                        : '#FFFFFF'
                                                "
                                                stroke="#BA1A1A"
                                                :stroke-width="
                                                    fsHoveredIndex === idx
                                                        ? 3
                                                        : 2
                                                "
                                            />
                                            <circle
                                                v-if="
                                                    seriesVisible.grainMoisture
                                                "
                                                :cx="pt.x"
                                                :cy="pt.yMoist"
                                                :r="
                                                    idx ===
                                                    fsActiveDataset.length - 1
                                                        ? 5.5
                                                        : fsHoveredIndex === idx
                                                          ? 7
                                                          : 3
                                                "
                                                fill="#7E22CE"
                                                stroke="#FFFFFF"
                                                stroke-width="1.8"
                                            />
                                        </g>

                                        <circle
                                            v-if="seriesVisible.tempInternal"
                                            :cx="
                                                fsActiveDataset[
                                                    fsActiveDataset.length - 1
                                                ].x
                                            "
                                            :cy="
                                                fsActiveDataset[
                                                    fsActiveDataset.length - 1
                                                ].yTempInt
                                            "
                                            r="14"
                                            fill="none"
                                            stroke="#BA1A1A"
                                            stroke-width="2"
                                            class="pulse-ring"
                                        />
                                    </g>

                                    <!-- Hover Crosshair -->
                                    <g v-if="fsHoveredPoint">
                                        <line
                                            :x1="fsHoveredPoint.x"
                                            y1="40"
                                            :x2="fsHoveredPoint.x"
                                            y2="480"
                                            stroke="#38BDF8"
                                            stroke-width="1.8"
                                            stroke-dasharray="4 4"
                                        />
                                    </g>

                                    <!-- X-Axis Labels -->
                                    <text
                                        v-for="(pt, idx) in fsSampledXLabels"
                                        :key="'fsxl-' + idx"
                                        :x="pt.x"
                                        y="520"
                                        font-size="12"
                                        fill="#94A3B8"
                                        text-anchor="middle"
                                        font-weight="600"
                                    >
                                        {{ pt.timeLabel }}
                                    </text>

                                    <!-- Empty State -->
                                    <text
                                        v-if="fsActiveDataset.length < 2"
                                        x="800"
                                        y="280"
                                        font-size="18"
                                        fill="#94A3B8"
                                        text-anchor="middle"
                                    >
                                        {{
                                            isDataActive
                                                ? $t("monitoring.collectingData")
                                                : $t("monitoring.waitingStream")
                                        }}
                                    </text>
                                </svg>

                                <!-- Large Interactive Fullscreen Tooltip -->
                                <div
                                    v-if="fsHoveredPoint"
                                    class="interactive-tooltip fs-tooltip"
                                    :style="{
                                        left: fsHoveredTooltipLeft + 'px',
                                        top: '25px',
                                    }"
                                >
                                    <div class="tooltip-header">
                                        <span class="tooltip-time">{{
                                            fsHoveredPoint.fullTime
                                        }}</span>
                                        <span class="tooltip-tag"
                                            >{{ $t("monitoring.dataPoint") }} #{{
                                                fsHoveredIndex + 1
                                            }}</span
                                        >
                                    </div>
                                    <div class="tooltip-grid fs-tooltip-grid">
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-red"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempInt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsHoveredPoint.tempInternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-sky"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempExt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsHoveredPoint.tempExternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-blue"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipHum") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsHoveredPoint.humidityInternal.toFixed(
                                                        0,
                                                    )
                                                }}% RH</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-purple"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipMoisture") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsHoveredPoint.grainMoisture.toFixed(
                                                        1,
                                                    )
                                                }}%</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-amber"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipSolar") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsHoveredPoint.solarRadiation.toFixed(
                                                        0,
                                                    )
                                                }}
                                                W/m²</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-green"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipDelta") }}:</span
                                            >
                                            <strong
                                                >+{{
                                                    (
                                                        fsHoveredPoint.tempInternal -
                                                        fsHoveredPoint.tempExternal
                                                    ).toFixed(1)
                                                }}°C</strong
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VIEW 2: Fullscreen Kinetika Dehidrasi Gabah -->
                            <div
                                v-else-if="fullscreenViewType === 'kinetics'"
                                class="fs-svg-wrapper"
                                @mousemove="handleFsKineticsHover"
                                @mouseleave="fsKineticsHoverIndex = null"
                            >
                                <svg
                                    viewBox="0 0 1600 620"
                                    class="fs-master-svg"
                                    preserveAspectRatio="none"
                                >
                                    <!-- Grid -->
                                    <line
                                        x1="80"
                                        y1="80"
                                        x2="1520"
                                        y2="80"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="85"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        30%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="200"
                                        x2="1520"
                                        y2="200"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="205"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        25%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="320"
                                        x2="1520"
                                        y2="320"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="325"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        20%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="440"
                                        x2="1520"
                                        y2="440"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="445"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        15%
                                    </text>

                                    <line
                                        x1="80"
                                        y1="500"
                                        x2="1520"
                                        y2="500"
                                        stroke="rgba(148, 163, 184, 0.6)"
                                        stroke-width="1.5"
                                    />
                                    <text
                                        x="68"
                                        y="505"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        10%
                                    </text>

                                    <!-- Target 12% Line -->
                                    <line
                                        x1="80"
                                        y1="476"
                                        x2="1520"
                                        y2="476"
                                        stroke="#7E22CE"
                                        stroke-dasharray="6 6"
                                        stroke-width="2"
                                    />
                                    <text
                                        x="1510"
                                        y="468"
                                        font-size="12"
                                        fill="#7E22CE"
                                        font-weight="800"
                                        text-anchor="end"
                                    >
                                        {{ $t("monitoring.targetMoistureLabel") }}
                                    </text>

                                    <!-- Moisture Curve & Gradient Area -->
                                    <path
                                        v-if="fsActiveDataset.length >= 2"
                                        :d="fsKineticsMoistureAreaPath"
                                        fill="url(#fsHumGrad)"
                                    />
                                    <path
                                        v-if="fsActiveDataset.length >= 2"
                                        :d="fsKineticsMoistureLinePath"
                                        fill="none"
                                        stroke="#7E22CE"
                                        stroke-width="4.5"
                                        stroke-linecap="round"
                                    />

                                    <!-- Hover Crosshair -->
                                    <g v-if="fsKineticsHoverPoint">
                                        <line
                                            :x1="fsKineticsHoverPoint.kX"
                                            y1="40"
                                            :x2="fsKineticsHoverPoint.kX"
                                            y2="500"
                                            stroke="#7E22CE"
                                            stroke-width="1.8"
                                            stroke-dasharray="4 4"
                                        />
                                        <circle
                                            :cx="fsKineticsHoverPoint.kX"
                                            :cy="fsKineticsHoverPoint.kY"
                                            r="7"
                                            fill="#7E22CE"
                                            stroke="#FFFFFF"
                                            stroke-width="2.5"
                                        />
                                    </g>

                                    <!-- X Labels -->
                                    <text
                                        v-for="(pt, idx) in fsSampledXLabels"
                                        :key="'fskxl-' + idx"
                                        :x="pt.x"
                                        y="540"
                                        font-size="12"
                                        fill="#94A3B8"
                                        text-anchor="middle"
                                        font-weight="600"
                                    >
                                        {{ pt.timeLabel }}
                                    </text>
                                </svg>

                                <!-- Tooltip -->
                                <div
                                    v-if="fsKineticsHoverPoint"
                                    class="interactive-tooltip fs-tooltip"
                                    :style="{
                                        left: fsKineticsTooltipLeft + 'px',
                                        top: '25px',
                                    }"
                                >
                                    <div class="tooltip-header">
                                        <span class="tooltip-time">{{
                                            fsKineticsHoverPoint.fullTime
                                        }}</span>
                                        <span class="tooltip-tag"
                                            >{{ $t("monitoring.fsKinetics") }} #{{
                                                fsKineticsHoverIndex + 1
                                            }}</span
                                        >
                                    </div>
                                    <div class="tooltip-grid fs-tooltip-grid">
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-purple"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipMoisture") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsKineticsHoverPoint.grainMoisture.toFixed(
                                                        1,
                                                    )
                                                }}%</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-green"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.sniTarget") }}:</span
                                            >
                                            <strong>12.0%</strong>
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-sky"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.remainingReduction") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    Math.max(
                                                        0,
                                                        fsKineticsHoverPoint.grainMoisture -
                                                            12.0,
                                                    ).toFixed(1)
                                                }}%</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-red"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempInt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsKineticsHoverPoint.tempInternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-blue"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipHum") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsKineticsHoverPoint.humidityInternal.toFixed(
                                                        0,
                                                    )
                                                }}% RH</strong
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VIEW 3: Fullscreen Efisiensi Retensi Panas (ΔT) -->
                            <div
                                v-else-if="fullscreenViewType === 'delta'"
                                class="fs-svg-wrapper"
                                @mousemove="handleFsDeltaHover"
                                @mouseleave="fsDeltaHoverIndex = null"
                            >
                                <svg
                                    viewBox="0 0 1600 620"
                                    class="fs-master-svg"
                                    preserveAspectRatio="none"
                                >
                                    <!-- Grid -->
                                    <line
                                        x1="80"
                                        y1="80"
                                        x2="1520"
                                        y2="80"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="85"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        +25°C
                                    </text>

                                    <line
                                        x1="80"
                                        y1="180"
                                        x2="1520"
                                        y2="180"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="185"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        +20°C
                                    </text>

                                    <line
                                        x1="80"
                                        y1="280"
                                        x2="1520"
                                        y2="280"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="285"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        +15°C
                                    </text>

                                    <line
                                        x1="80"
                                        y1="380"
                                        x2="1520"
                                        y2="380"
                                        stroke="rgba(148, 163, 184, 0.2)"
                                        stroke-dasharray="4 4"
                                    />
                                    <text
                                        x="68"
                                        y="385"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        +10°C
                                    </text>

                                    <line
                                        x1="80"
                                        y1="500"
                                        x2="1520"
                                        y2="500"
                                        stroke="rgba(148, 163, 184, 0.6)"
                                        stroke-width="1.5"
                                    />
                                    <text
                                        x="68"
                                        y="505"
                                        font-size="13"
                                        fill="#94A3B8"
                                        text-anchor="end"
                                    >
                                        0°C
                                    </text>

                                    <!-- Delta Area & Line -->
                                    <path
                                        v-if="fsActiveDataset.length >= 2"
                                        :d="fsDeltaAreaPath"
                                        fill="url(#fsSafetyGrad)"
                                    />
                                    <path
                                        v-if="fsActiveDataset.length >= 2"
                                        :d="fsDeltaLinePath"
                                        fill="none"
                                        stroke="#0D631B"
                                        stroke-width="4.5"
                                        stroke-linecap="round"
                                    />

                                    <!-- Hover Crosshair -->
                                    <g v-if="fsDeltaHoverPoint">
                                        <line
                                            :x1="fsDeltaHoverPoint.dX"
                                            y1="40"
                                            :x2="fsDeltaHoverPoint.dX"
                                            y2="500"
                                            stroke="#0D631B"
                                            stroke-width="1.8"
                                            stroke-dasharray="4 4"
                                        />
                                        <circle
                                            :cx="fsDeltaHoverPoint.dX"
                                            :cy="fsDeltaHoverPoint.dY"
                                            r="7"
                                            fill="#0D631B"
                                            stroke="#FFFFFF"
                                            stroke-width="2.5"
                                        />
                                    </g>

                                    <!-- X Labels -->
                                    <text
                                        v-for="(pt, idx) in fsSampledXLabels"
                                        :key="'fsdxl-' + idx"
                                        :x="pt.x"
                                        y="540"
                                        font-size="12"
                                        fill="#94A3B8"
                                        text-anchor="middle"
                                        font-weight="600"
                                    >
                                        {{ pt.timeLabel }}
                                    </text>
                                </svg>

                                <!-- Tooltip -->
                                <div
                                    v-if="fsDeltaHoverPoint"
                                    class="interactive-tooltip fs-tooltip"
                                    :style="{
                                        left: fsDeltaTooltipLeft + 'px',
                                        top: '25px',
                                    }"
                                >
                                    <div class="tooltip-header">
                                        <span class="tooltip-time">{{
                                            fsDeltaHoverPoint.fullTime
                                        }}</span>
                                        <span class="tooltip-tag"
                                            >{{ $t("monitoring.fsThermalEfficiency") }} #{{
                                                fsDeltaHoverIndex + 1
                                            }}</span
                                        >
                                    </div>
                                    <div class="tooltip-grid fs-tooltip-grid">
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-green"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipDelta") }}:</span
                                            >
                                            <strong
                                                >+{{
                                                    fsDeltaHoverPoint.delta.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-red"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempInt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsDeltaHoverPoint.tempInternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-sky"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipTempExt") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsDeltaHoverPoint.tempExternal.toFixed(
                                                        1,
                                                    )
                                                }}°C</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-amber"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipSolar") }}:</span
                                            >
                                            <strong
                                                >{{
                                                    fsDeltaHoverPoint.solarRadiation.toFixed(
                                                        0,
                                                    )
                                                }}
                                                W/m²</strong
                                            >
                                        </div>
                                        <div class="tooltip-row">
                                            <span class="t-dot bg-purple"></span
                                            ><span class="t-label"
                                                >{{ $t("monitoring.tooltipAuxHeater") }}:</span
                                            >
                                            <strong>{{
                                                actuators.auxHeaterStatus
                                                    ? $t("monitoring.active") + " (" +
                                                      actuators.auxHeaterLevel +
                                                      "%)"
                                                    : $t("monitoring.standbyUpper")
                                            }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VIEW 4: Composite 3-in-1 Grid Fullscreen View -->
                            <div
                                v-else-if="fullscreenViewType === 'all'"
                                class="fs-grid-3in1"
                            >
                                        <div
                                    class="fs-grid-panel-main"
                                    @mousemove="handleFsChartHover"
                                    @mouseleave="fsHoveredIndex = null"
                                >
                                    <div class="fs-panel-title-bar">
                                        <span
                                            >{{ $t("monitoring.fsDualAxisTitle") }}</span
                                        >
                                        <span class="text-muted">Live</span>
                                    </div>
                                    <div class="fs-svg-wrapper-inner">
                                        <svg
                                            viewBox="0 0 1600 320"
                                            class="fs-master-svg"
                                            preserveAspectRatio="none"
                                        >
                                            <!-- Grid -->
                                            <line
                                                x1="60"
                                                y1="40"
                                                x2="1540"
                                                y2="40"
                                                stroke="rgba(148, 163, 184, 0.2)"
                                                stroke-dasharray="3 3"
                                            />
                                            <line
                                                x1="60"
                                                y1="120"
                                                x2="1540"
                                                y2="120"
                                                stroke="rgba(148, 163, 184, 0.2)"
                                                stroke-dasharray="3 3"
                                            />
                                            <line
                                                x1="60"
                                                y1="200"
                                                x2="1540"
                                                y2="200"
                                                stroke="rgba(148, 163, 184, 0.2)"
                                                stroke-dasharray="3 3"
                                            />
                                            <line
                                                x1="60"
                                                y1="280"
                                                x2="1540"
                                                y2="280"
                                                stroke="rgba(148, 163, 184, 0.5)"
                                            />

                                            <path
                                                v-if="
                                                    seriesVisible.tempInternal &&
                                                    fsActiveDataset.length >= 2
                                                "
                                                :d="fs3in1TempIntLinePath"
                                                fill="none"
                                                stroke="#BA1A1A"
                                                stroke-width="3"
                                            />
                                            <path
                                                v-if="
                                                    seriesVisible.tempExternal &&
                                                    fsActiveDataset.length >= 2
                                                "
                                                :d="fs3in1TempExtLinePath"
                                                fill="none"
                                                stroke="#0284C7"
                                                stroke-width="2"
                                                stroke-dasharray="4 4"
                                            />
                                            <path
                                                v-if="
                                                    seriesVisible.humidityInternal &&
                                                    fsActiveDataset.length >= 2
                                                "
                                                :d="fs3in1HumLinePath"
                                                fill="none"
                                                stroke="#005DB7"
                                                stroke-width="2.5"
                                            />
                                            <path
                                                v-if="
                                                    seriesVisible.grainMoisture &&
                                                    fsActiveDataset.length >= 2
                                                "
                                                :d="fs3in1MoistureLinePath"
                                                fill="none"
                                                stroke="#7E22CE"
                                                stroke-width="3"
                                            />
                                            <path
                                                v-if="
                                                    seriesVisible.solarRadiation &&
                                                    fsActiveDataset.length >= 2
                                                "
                                                :d="fs3in1SolarLinePath"
                                                fill="none"
                                                stroke="#D97706"
                                                stroke-width="2"
                                                opacity="0.8"
                                            />

                                            <g v-if="fsHoveredPoint">
                                                <line
                                                    :x1="fsHoveredPoint.x"
                                                    y1="20"
                                                    :x2="fsHoveredPoint.x"
                                                    y2="280"
                                                    stroke="#38BDF8"
                                                    stroke-width="1.5"
                                                    stroke-dasharray="3 3"
                                                />
                                            </g>
                                        </svg>

                                        <div
                                            v-if="fsHoveredPoint"
                                            class="interactive-tooltip fs-tooltip-compact"
                                            :style="{
                                                left:
                                                    fsHoveredTooltipLeft + 'px',
                                                top: '10px',
                                            }"
                                        >
                                            <div class="tooltip-header">
                                                <span class="tooltip-time">{{
                                                    fsHoveredPoint.fullTime
                                                }}</span>
                                            </div>
                                            <div class="tooltip-grid">
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-red"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.tooltipTempInt") }}:
                                                        <strong
                                                            >{{
                                                                fsHoveredPoint.tempInternal.toFixed(
                                                                    1,
                                                                )
                                                            }}°C</strong
                                                        ></span
                                                    >
                                                </div>
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-blue"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.tooltipHum") }}:
                                                        <strong
                                                            >{{
                                                                fsHoveredPoint.humidityInternal.toFixed(
                                                                    0,
                                                                )
                                                            }}%</strong
                                                        ></span
                                                    >
                                                </div>
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-purple"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.tooltipMoisture") }}:
                                                        <strong
                                                            >{{
                                                                fsHoveredPoint.grainMoisture.toFixed(
                                                                    1,
                                                                )
                                                            }}%</strong
                                                        ></span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom: 2 Sub-charts side by side -->
                                <div class="fs-grid-bottom-split">
                                    <!-- Left Subchart: Kinetics -->
                                    <div
                                        class="fs-grid-panel-sub"
                                        @mousemove="handleFsKineticsHover"
                                        @mouseleave="
                                            fsKineticsHoverIndex = null
                                        "
                                    >
                                        <div class="fs-panel-title-bar">
                                            <span
                                                >{{ $t("monitoring.kineticsHeading") }}</span
                                            >
                                            <span class="text-purple font-bold"
                                                >{{ $t("monitoring.targetMoistureBadge") }}</span
                                            >
                                        </div>
                                        <div class="fs-svg-wrapper-inner">
                                            <svg
                                                viewBox="0 0 750 200"
                                                class="fs-master-svg"
                                                preserveAspectRatio="none"
                                            >
                                                <line
                                                    x1="40"
                                                    y1="30"
                                                    x2="710"
                                                    y2="30"
                                                    stroke="rgba(148, 163, 184, 0.2)"
                                                    stroke-dasharray="3 3"
                                                />
                                                <line
                                                    x1="40"
                                                    y1="90"
                                                    x2="710"
                                                    y2="90"
                                                    stroke="rgba(148, 163, 184, 0.2)"
                                                    stroke-dasharray="3 3"
                                                />
                                                <line
                                                    x1="40"
                                                    y1="160"
                                                    x2="710"
                                                    y2="160"
                                                    stroke="rgba(148, 163, 184, 0.5)"
                                                />
                                                <line
                                                    x1="40"
                                                    y1="135"
                                                    x2="710"
                                                    y2="135"
                                                    stroke="#7E22CE"
                                                    stroke-dasharray="4 4"
                                                    stroke-width="1.5"
                                                />

                                                <path
                                                    v-if="
                                                        fsActiveDataset.length >=
                                                        2
                                                    "
                                                    :d="fs3in1KineticsAreaPath"
                                                    fill="url(#fsHumGrad)"
                                                />
                                                <path
                                                    v-if="
                                                        fsActiveDataset.length >=
                                                        2
                                                    "
                                                    :d="fs3in1KineticsLinePath"
                                                    fill="none"
                                                    stroke="#7E22CE"
                                                    stroke-width="3.5"
                                                    stroke-linecap="round"
                                                />

                                                <g v-if="fsKineticsHoverPoint">
                                                    <line
                                                        :x1="
                                                            fsKineticsHoverPoint.k3x
                                                        "
                                                        y1="20"
                                                        :x2="
                                                            fsKineticsHoverPoint.k3x
                                                        "
                                                        y2="160"
                                                        stroke="#7E22CE"
                                                        stroke-width="1.5"
                                                        stroke-dasharray="3 3"
                                                    />
                                                    <circle
                                                        :cx="
                                                            fsKineticsHoverPoint.k3x
                                                        "
                                                        :cy="
                                                            fsKineticsHoverPoint.k3y
                                                        "
                                                        r="5"
                                                        fill="#7E22CE"
                                                        stroke="#FFFFFF"
                                                        stroke-width="2"
                                                    />
                                                </g>
                                            </svg>

                                            <div
                                                v-if="fsKineticsHoverPoint"
                                                class="interactive-tooltip fs-tooltip-compact"
                                                :style="{
                                                    left:
                                                        Math.min(
                                                            500,
                                                            Math.max(
                                                                10,
                                                                fsKineticsTooltipLeft *
                                                                    0.5,
                                                                ),
                                                        ) + 'px',
                                                    top: '10px',
                                                }"
                                            >
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-purple"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.tooltipMoisture") }}:
                                                        <strong
                                                            >{{
                                                                fsKineticsHoverPoint.grainMoisture.toFixed(
                                                                    1,
                                                                )
                                                            }}%</strong
                                                        ></span
                                                    >
                                                </div>
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-green"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.sniTarget") }}:
                                                        <strong
                                                            >12.0%</strong
                                                        ></span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Subchart: Thermal Retention -->
                                    <div
                                        class="fs-grid-panel-sub"
                                        @mousemove="handleFsDeltaHover"
                                        @mouseleave="fsDeltaHoverIndex = null"
                                    >
                                        <div class="fs-panel-title-bar">
                                            <span
                                                >{{ $t("monitoring.deltaHeading") }}</span
                                            >
                                            <span
                                                class="text-green-dark font-bold"
                                                >+{{
                                                    (
                                                        telemetry.tempInternal -
                                                        telemetry.tempExternal
                                                    ).toFixed(1)
                                                }}°C</span
                                            >
                                        </div>
                                        <div class="fs-svg-wrapper-inner">
                                            <svg
                                                viewBox="0 0 750 200"
                                                class="fs-master-svg"
                                                preserveAspectRatio="none"
                                            >
                                                <line
                                                    x1="40"
                                                    y1="30"
                                                    x2="710"
                                                    y2="30"
                                                    stroke="rgba(148, 163, 184, 0.2)"
                                                    stroke-dasharray="3 3"
                                                />
                                                <line
                                                    x1="40"
                                                    y1="90"
                                                    x2="710"
                                                    y2="90"
                                                    stroke="rgba(148, 163, 184, 0.2)"
                                                    stroke-dasharray="3 3"
                                                />
                                                <line
                                                    x1="40"
                                                    y1="160"
                                                    x2="710"
                                                    y2="160"
                                                    stroke="rgba(148, 163, 184, 0.5)"
                                                />

                                                <path
                                                    v-if="
                                                        fsActiveDataset.length >=
                                                        2
                                                    "
                                                    :d="fs3in1DeltaAreaPath"
                                                    fill="url(#fsSafetyGrad)"
                                                />
                                                <path
                                                    v-if="
                                                        fsActiveDataset.length >=
                                                        2
                                                    "
                                                    :d="fs3in1DeltaLinePath"
                                                    fill="none"
                                                    stroke="#0D631B"
                                                    stroke-width="3.5"
                                                    stroke-linecap="round"
                                                />

                                                <g v-if="fsDeltaHoverPoint">
                                                    <line
                                                        :x1="
                                                            fsDeltaHoverPoint.d3x
                                                        "
                                                        y1="20"
                                                        :x2="
                                                            fsDeltaHoverPoint.d3x
                                                        "
                                                        y2="160"
                                                        stroke="#0D631B"
                                                        stroke-width="1.5"
                                                        stroke-dasharray="3 3"
                                                    />
                                                    <circle
                                                        :cx="
                                                            fsDeltaHoverPoint.d3x
                                                        "
                                                        :cy="
                                                            fsDeltaHoverPoint.d3y
                                                        "
                                                        r="5"
                                                        fill="#0D631B"
                                                        stroke="#FFFFFF"
                                                        stroke-width="2"
                                                    />
                                                </g>
                                            </svg>

                                            <div
                                                v-if="fsDeltaHoverPoint"
                                                class="interactive-tooltip fs-tooltip-compact"
                                                :style="{
                                                    left:
                                                        Math.min(
                                                            500,
                                                            Math.max(
                                                                10,
                                                                fsDeltaTooltipLeft *
                                                                    0.5,
                                                                ),
                                                        ) + 'px',
                                                    top: '10px',
                                                }"
                                            >
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-green"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.tooltipDelta") }}:
                                                        <strong
                                                            >+{{
                                                                fsDeltaHoverPoint.delta.toFixed(
                                                                    1,
                                                                )
                                                            }}°C</strong
                                                        ></span
                                                    >
                                                </div>
                                                <div class="tooltip-row">
                                                    <span
                                                        class="t-dot bg-amber"
                                                    ></span
                                                    ><span
                                                        >{{ $t("monitoring.tooltipSolar") }}:
                                                        <strong
                                                            >{{
                                                                fsDeltaHoverPoint.solarRadiation.toFixed(
                                                                    0,
                                                                )
                                                            }}
                                                            W/m²</strong
                                                        ></span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fullscreen Bottom Analytics & Status Bar Strip -->
                        <div class="fs-footer-bar">
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.tooltipTempInt") }}</span>
                                <strong class="fs-stat-val text-red"
                                    >{{
                                        isDataActive
                                            ? telemetry.tempInternal.toFixed(1)
                                            : "--"
                                    }}°C</strong
                                >
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.statMaxTemp") }}</span>
                                <strong class="fs-stat-val"
                                    >{{ masterStats.maxTemp }}°C</strong
                                >
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl"
                                    >{{ $t("monitoring.tooltipDelta") }}</span
                                >
                                <strong class="fs-stat-val text-green-dark"
                                    >+{{ masterStats.maxDelta }}°C</strong
                                >
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.statAvgHum") }}</span>
                                <strong class="fs-stat-val text-blue"
                                    >{{
                                        isDataActive
                                            ? telemetry.humidityInternal.toFixed(
                                                  0,
                                              )
                                            : "--"
                                    }}% RH</strong
                                >
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.seriesMoisture") }}</span>
                                <strong class="fs-stat-val text-purple"
                                    >{{
                                        isDataActive
                                            ? telemetry.grainMoisture.toFixed(1)
                                            : "--"
                                    }}%</strong
                                >
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.seriesSolar") }}</span>
                                <strong class="fs-stat-val text-amber"
                                    >{{
                                        isDataActive
                                            ? telemetry.solarRadiation.toFixed(
                                                  0,
                                              )
                                            : "--"
                                    }}
                                    W/m²</strong
                                >
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.tooltipAuxHeater") }}</span>
                                <strong class="fs-stat-val">{{
                                    actuators.auxHeaterStatus
                                        ? actuators.auxHeaterLevel + "%"
                                        : $t("monitoring.standbyUpper")
                                }}</strong>
                            </div>
                            <div class="fs-stat-chip">
                                <span class="fs-stat-lbl">{{ $t("monitoring.fsExhaustFan") }}</span>
                                <strong class="fs-stat-val">{{
                                    actuators.exhaustFanStatus
                                        ? actuators.exhaustFanSpeed + " RPM"
                                        : "OFF"
                                }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Wi-Fi & MQTT Setup Modal via Bluetooth BLE -->
        <DeviceSetupBleModal
            :is-open="isWifiSetupOpen"
            @close="isWifiSetupOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from "vue";
import { telemetryService } from "../../services/telemetryService";
import { anomalyService } from "../../services/anomalyService";
import { socketService } from "../../services/socketService";
import connectionManager from "../../services/connectionManager";
import ConnectionStatusCard from "../../components/ConnectionStatusCard.vue";
import DeviceSetupBleModal from "../../components/DeviceSetupBleModal.vue";
import MonitoringSkeleton from "../../components/MonitoringSkeleton.vue";

const isInitialLoading = ref(true);
const isWifiSetupOpen = ref(false);

const anomalyHealth = ref(null);
const isAnomalyDetailsOpen = ref(false);
const isAnomalyScanning = ref(false);

async function loadAnomalyHealth() {
    try {
        const res = await anomalyService.getHealthStatus();
        if (res && res.success) {
            anomalyHealth.value = res;
        }
    } catch (err) {
        console.warn("Anomaly health check notice:", err.message);
    }
}

async function handleRunAnomalyScan() {
    isAnomalyScanning.value = true;
    try {
        await anomalyService.runCheck();
        await loadAnomalyHealth();
    } catch (err) {
        console.warn("Anomaly scan warning:", err.message);
    } finally {
        isAnomalyScanning.value = false;
    }
}

const telemetry = ref({
    tempInternal: 0,
    tempExternal: 0,
    humidityInternal: 0,
    humidityExternal: 0,
    grainMoisture: 0,
    solarRadiation: 0,
    weightCurrentKg: 0,
    hasData: false,
});

const actuators = ref({
    exhaustFanStatus: false,
    exhaustFanSpeed: 0,
    blowerFanStatus: false,
    blowerFanSpeed: 0,
    auxHeaterStatus: false,
    auxHeaterLevel: 0,
    roofVentStatus: false,
    controlMode: "AUTOMATIC",
});

// History & Live Dataset Stream
const historyPoints = ref([]);
const livePoints = ref([]);
const chartMode = ref("live"); // 'live' | 'history'
const timeWindow = ref(20);

// Interactive Hover State (Master Chart)
const hoveredIndex = ref(null);
const hoveredTooltipLeft = ref(0);

// Interactive Hover State (Sub-Chart 1: Kinetika Dehidrasi)
const hoveredMoistureIndex = ref(null);
const hoveredMoistureTooltipLeft = ref(0);

// Interactive Hover State (Sub-Chart 2: Efisiensi Termal Delta T)
const hoveredDeltaIndex = ref(null);
const hoveredDeltaTooltipLeft = ref(0);

// Fullscreen Modal States
const isChartFullscreen = ref(false);
const fullscreenViewType = ref("master"); // 'master' | 'kinetics' | 'delta' | 'all'
const isNativeFullscreen = ref(false);

// Fullscreen Interactive Hover States
const fsHoveredIndex = ref(null);
const fsHoveredTooltipLeft = ref(0);

const fsKineticsHoverIndex = ref(null);
const fsKineticsTooltipLeft = ref(0);

const fsDeltaHoverIndex = ref(null);
const fsDeltaTooltipLeft = ref(0);

// Interactive Series Toggles
const seriesVisible = reactive({
    tempInternal: true,
    tempExternal: true,
    humidityInternal: true,
    grainMoisture: true,
    solarRadiation: true,
    safetyZone: true,
});

const isDataActive = computed(() => {
    if (connectionManager.state.sourceMode === "simulation") return true;
    return connectionManager.state.isHardwareActive && telemetry.value.hasData;
});

const setTimeWindow = (count, mode) => {
    chartMode.value = mode;
    timeWindow.value = count;
    if (mode === "history" && historyPoints.value.length === 0) {
        loadHistoryData();
    }
};

// Normalized Dynamic Master Dataset mapped to 1000 x 360 SVG dimensions
const activeDataset = computed(() => {
    const raw =
        chartMode.value === "live"
            ? livePoints.value.slice(-timeWindow.value)
            : historyPoints.value;

    if (raw.length < 2) return [];

    const count = raw.length;
    const startX = 60;
    const endX = 940;
    const stepX = (endX - startX) / (count - 1);

    return raw.map((pt, idx) => {
        const x = startX + idx * stepX;
        const tempInt = pt.tempInternal ?? pt.temp_internal ?? 0;
        const tempExt = pt.tempExternal ?? pt.temp_external ?? 28;
        const humInt = pt.humidityInternal ?? pt.humidity_internal ?? 0;
        const moist = pt.grainMoisture ?? pt.grain_moisture ?? 14;
        const solar = pt.solarRadiation ?? pt.solar_radiation ?? 0;

        // Y mappings (1000x360)
        const yTempInt =
            310 - Math.max(0, Math.min(1, (tempInt - 20) / 50)) * 260;
        const yTempExt =
            310 - Math.max(0, Math.min(1, (tempExt - 20) / 50)) * 260;
        const yHumInt = 310 - Math.max(0, Math.min(1, humInt / 100)) * 260;
        const yMoist = 310 - Math.max(0, Math.min(1, moist / 100)) * 260;
        const ySolar = 310 - Math.max(0, Math.min(1, solar / 1200)) * 130;

        const d = new Date(pt.timestamp || pt.recorded_at || Date.now());
        const timeLabel =
            pt.label ||
            d.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            });
        const fullTime = d.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        });

        return {
            x,
            yTempInt,
            yTempExt,
            yHumInt,
            yMoist,
            ySolar,
            tempInternal: tempInt,
            tempExternal: tempExt,
            humidityInternal: humInt,
            grainMoisture: moist,
            solarRadiation: solar,
            timeLabel,
            fullTime,
        };
    });
});

const hoveredPoint = computed(() => {
    if (hoveredIndex.value === null) return null;
    return activeDataset.value[hoveredIndex.value] || null;
});

const handleChartHover = (event) => {
    if (activeDataset.value.length < 2) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const relativeX = (mouseX / rect.width) * 1000;

    let closestIdx = 0;
    let minDiff = Infinity;
    activeDataset.value.forEach((pt, idx) => {
        const diff = Math.abs(pt.x - relativeX);
        if (diff < minDiff) {
            minDiff = diff;
            closestIdx = idx;
        }
    });

    hoveredIndex.value = closestIdx;
    hoveredTooltipLeft.value = Math.max(
        20,
        Math.min(rect.width - 220, mouseX - 100),
    );
};

// Sub-chart 1 (Moisture) Hover
const handleMoistureSubHover = (event) => {
    if (activeDataset.value.length < 2) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const relativeX = (mouseX / rect.width) * 450;

    const startX = 30;
    const endX = 420;
    const stepX = (endX - startX) / (activeDataset.value.length - 1);

    let closestIdx = 0;
    let minDiff = Infinity;
    activeDataset.value.forEach((_, idx) => {
        const ptX = startX + idx * stepX;
        const diff = Math.abs(ptX - relativeX);
        if (diff < minDiff) {
            minDiff = diff;
            closestIdx = idx;
        }
    });

    hoveredMoistureIndex.value = closestIdx;
    hoveredMoistureTooltipLeft.value = Math.max(
        10,
        Math.min(rect.width - 210, mouseX - 105),
    );
};

const hoveredMoisturePoint = computed(() => {
    if (hoveredMoistureIndex.value === null) return null;
    const pt = activeDataset.value[hoveredMoistureIndex.value];
    if (!pt) return null;
    const startX = 30;
    const endX = 420;
    const stepX = (endX - startX) / (activeDataset.value.length - 1);
    const subX = startX + hoveredMoistureIndex.value * stepX;
    const subY =
        140 - Math.max(0, Math.min(1, (pt.grainMoisture - 10) / 15)) * 100;
    return { ...pt, subX, subY };
});

// Sub-chart 2 (Delta T) Hover
const handleDeltaSubHover = (event) => {
    if (activeDataset.value.length < 2) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const relativeX = (mouseX / rect.width) * 450;

    const startX = 30;
    const endX = 420;
    const stepX = (endX - startX) / (activeDataset.value.length - 1);

    let closestIdx = 0;
    let minDiff = Infinity;
    activeDataset.value.forEach((_, idx) => {
        const ptX = startX + idx * stepX;
        const diff = Math.abs(ptX - relativeX);
        if (diff < minDiff) {
            minDiff = diff;
            closestIdx = idx;
        }
    });

    hoveredDeltaIndex.value = closestIdx;
    hoveredDeltaTooltipLeft.value = Math.max(
        10,
        Math.min(rect.width - 210, mouseX - 105),
    );
};

const hoveredDeltaPoint = computed(() => {
    if (hoveredDeltaIndex.value === null) return null;
    const pt = activeDataset.value[hoveredDeltaIndex.value];
    if (!pt) return null;
    const startX = 30;
    const endX = 420;
    const stepX = (endX - startX) / (activeDataset.value.length - 1);
    const subX = startX + hoveredDeltaIndex.value * stepX;
    const delta = Math.max(0, pt.tempInternal - pt.tempExternal);
    const subY = 140 - Math.max(0, Math.min(1, delta / 25)) * 100;
    return { ...pt, subX, subY, delta };
});

// SVG Path Helpers (Master Standard)
const masterTempIntLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0
                ? `M ${p.x} ${p.yTempInt}`
                : `${acc} L ${p.x} ${p.yTempInt}`,
        "",
    );
});

const masterTempIntAreaPath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return `${masterTempIntLinePath.value} L ${pts[pts.length - 1].x} 310 L ${pts[0].x} 310 Z`;
});

const masterTempExtLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0
                ? `M ${p.x} ${p.yTempExt}`
                : `${acc} L ${p.x} ${p.yTempExt}`,
        "",
    );
});

const masterHumLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0
                ? `M ${p.x} ${p.yHumInt}`
                : `${acc} L ${p.x} ${p.yHumInt}`,
        "",
    );
});

const masterHumAreaPath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return `${masterHumLinePath.value} L ${pts[pts.length - 1].x} 310 L ${pts[0].x} 310 Z`;
});

const masterMoistureLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0 ? `M ${p.x} ${p.yMoist}` : `${acc} L ${p.x} ${p.yMoist}`,
        "",
    );
});

const masterSolarLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0 ? `M ${p.x} ${p.ySolar}` : `${acc} L ${p.x} ${p.ySolar}`,
        "",
    );
});

const masterSolarAreaPath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return `${masterSolarLinePath.value} L ${pts[pts.length - 1].x} 310 L ${pts[0].x} 310 Z`;
});

// Sub-chart 1 (Moisture)
const moistureSubLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    const startX = 30;
    const endX = 420;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y =
            140 - Math.max(0, Math.min(1, (p.grainMoisture - 10) / 15)) * 100;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const moistureSubAreaPath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return `${moistureSubLinePath.value} L 420 140 L 30 140 Z`;
});

// Sub-chart 2 (Delta T)
const deltaSubLinePath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    const startX = 30;
    const endX = 420;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const delta = Math.max(0, p.tempInternal - p.tempExternal);
        const y = 140 - Math.max(0, Math.min(1, delta / 25)) * 100;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const deltaSubAreaPath = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return "";
    return `${deltaSubLinePath.value} L 420 140 L 30 140 Z`;
});

// Sampled X-axis labels to prevent text collision
const sampledXLabels = computed(() => {
    const pts = activeDataset.value;
    if (pts.length < 2) return [];
    const step = Math.max(1, Math.floor(pts.length / 6));
    return pts.filter((_, idx) => idx % step === 0 || idx === pts.length - 1);
});

// =========================================================================
// FULLSCREEN HIGH-RESOLUTION DATASET & PATH HELPERS (1600 x 620)
// =========================================================================
const fsActiveDataset = computed(() => {
    const raw =
        chartMode.value === "live"
            ? livePoints.value.slice(-timeWindow.value)
            : historyPoints.value;

    if (raw.length < 2) return [];

    const count = raw.length;
    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (count - 1);

    // Scalers (1600 x 620):
    // Baseline y = 480 (20°C / 0% / 0 W/m2)
    // Top y = 80 (60°C / 100% / 1200 W/m2) -> Total Height = 400px
    return raw.map((pt, idx) => {
        const x = startX + idx * stepX;
        const tempInt = pt.tempInternal ?? pt.temp_internal ?? 0;
        const tempExt = pt.tempExternal ?? pt.temp_external ?? 28;
        const humInt = pt.humidityInternal ?? pt.humidity_internal ?? 0;
        const moist = pt.grainMoisture ?? pt.grain_moisture ?? 14;
        const solar = pt.solarRadiation ?? pt.solar_radiation ?? 0;

        const yTempInt =
            480 - Math.max(0, Math.min(1, (tempInt - 20) / 50)) * 400;
        const yTempExt =
            480 - Math.max(0, Math.min(1, (tempExt - 20) / 50)) * 400;
        const yHumInt = 480 - Math.max(0, Math.min(1, humInt / 100)) * 400;
        const yMoist = 480 - Math.max(0, Math.min(1, moist / 100)) * 400;
        const ySolar = 480 - Math.max(0, Math.min(1, solar / 1200)) * 220;

        const d = new Date(pt.timestamp || pt.recorded_at || Date.now());
        const timeLabel =
            pt.label ||
            d.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            });
        const fullTime = d.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        });

        return {
            x,
            yTempInt,
            yTempExt,
            yHumInt,
            yMoist,
            ySolar,
            tempInternal: tempInt,
            tempExternal: tempExt,
            humidityInternal: humInt,
            grainMoisture: moist,
            solarRadiation: solar,
            timeLabel,
            fullTime,
        };
    });
});

const fsSampledXLabels = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return [];
    const step = Math.max(1, Math.floor(pts.length / 8));
    return pts.filter((_, idx) => idx % step === 0 || idx === pts.length - 1);
});

const fsHoveredPoint = computed(() => {
    if (fsHoveredIndex.value === null) return null;
    return fsActiveDataset.value[fsHoveredIndex.value] || null;
});

const handleFsChartHover = (event) => {
    if (fsActiveDataset.value.length < 2) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const relativeX = (mouseX / rect.width) * 1600;

    let closestIdx = 0;
    let minDiff = Infinity;
    fsActiveDataset.value.forEach((pt, idx) => {
        const diff = Math.abs(pt.x - relativeX);
        if (diff < minDiff) {
            minDiff = diff;
            closestIdx = idx;
        }
    });

    fsHoveredIndex.value = closestIdx;
    fsHoveredTooltipLeft.value = Math.max(
        20,
        Math.min(rect.width - 260, mouseX - 130),
    );
};

// Fullscreen Path Helpers
const fsTempIntLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0
                ? `M ${p.x} ${p.yTempInt}`
                : `${acc} L ${p.x} ${p.yTempInt}`,
        "",
    );
});

const fsTempIntAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fsTempIntLinePath.value} L ${pts[pts.length - 1].x} 480 L ${pts[0].x} 480 Z`;
});

const fsTempExtLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0
                ? `M ${p.x} ${p.yTempExt}`
                : `${acc} L ${p.x} ${p.yTempExt}`,
        "",
    );
});

const fsHumLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0
                ? `M ${p.x} ${p.yHumInt}`
                : `${acc} L ${p.x} ${p.yHumInt}`,
        "",
    );
});

const fsHumAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fsHumLinePath.value} L ${pts[pts.length - 1].x} 480 L ${pts[0].x} 480 Z`;
});

const fsMoistureLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0 ? `M ${p.x} ${p.yMoist}` : `${acc} L ${p.x} ${p.yMoist}`,
        "",
    );
});

const fsSolarLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return pts.reduce(
        (acc, p, idx) =>
            idx === 0 ? `M ${p.x} ${p.ySolar}` : `${acc} L ${p.x} ${p.ySolar}`,
        "",
    );
});

const fsSolarAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fsSolarLinePath.value} L ${pts[pts.length - 1].x} 480 L ${pts[0].x} 480 Z`;
});

// Fullscreen Kinetics Paths & Hover
const fsKineticsMoistureLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y =
            500 - Math.max(0, Math.min(1, (p.grainMoisture - 10) / 20)) * 420;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fsKineticsMoistureAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fsKineticsMoistureLinePath.value} L 1520 500 L 80 500 Z`;
});

const handleFsKineticsHover = (event) => {
    if (fsActiveDataset.value.length < 2) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const relativeX = (mouseX / rect.width) * 1600;

    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (fsActiveDataset.value.length - 1);

    let closestIdx = 0;
    let minDiff = Infinity;
    fsActiveDataset.value.forEach((_, idx) => {
        const ptX = startX + idx * stepX;
        const diff = Math.abs(ptX - relativeX);
        if (diff < minDiff) {
            minDiff = diff;
            closestIdx = idx;
        }
    });

    fsKineticsHoverIndex.value = closestIdx;
    fsKineticsTooltipLeft.value = Math.max(
        20,
        Math.min(rect.width - 260, mouseX - 130),
    );
};

const fsKineticsHoverPoint = computed(() => {
    if (fsKineticsHoverIndex.value === null) return null;
    const pt = fsActiveDataset.value[fsKineticsHoverIndex.value];
    if (!pt) return null;
    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (fsActiveDataset.value.length - 1);
    const kX = startX + fsKineticsHoverIndex.value * stepX;
    const kY =
        500 - Math.max(0, Math.min(1, (pt.grainMoisture - 10) / 20)) * 420;
    return {
        ...pt,
        kX,
        kY,
        k3x:
            40 +
            fsKineticsHoverIndex.value *
                ((710 - 40) / (fsActiveDataset.value.length - 1)),
        k3y: 160 - Math.max(0, Math.min(1, (pt.grainMoisture - 10) / 15)) * 130,
    };
});

// Fullscreen Delta Paths & Hover
const fsDeltaLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const delta = Math.max(0, p.tempInternal - p.tempExternal);
        const y = 500 - Math.max(0, Math.min(1, delta / 25)) * 420;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fsDeltaAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fsDeltaLinePath.value} L 1520 500 L 80 500 Z`;
});

const handleFsDeltaHover = (event) => {
    if (fsActiveDataset.value.length < 2) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const relativeX = (mouseX / rect.width) * 1600;

    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (fsActiveDataset.value.length - 1);

    let closestIdx = 0;
    let minDiff = Infinity;
    fsActiveDataset.value.forEach((_, idx) => {
        const ptX = startX + idx * stepX;
        const diff = Math.abs(ptX - relativeX);
        if (diff < minDiff) {
            minDiff = diff;
            closestIdx = idx;
        }
    });

    fsDeltaHoverIndex.value = closestIdx;
    fsDeltaTooltipLeft.value = Math.max(
        20,
        Math.min(rect.width - 260, mouseX - 130),
    );
};

const fsDeltaHoverPoint = computed(() => {
    if (fsDeltaHoverIndex.value === null) return null;
    const pt = fsActiveDataset.value[fsDeltaHoverIndex.value];
    if (!pt) return null;
    const startX = 80;
    const endX = 1520;
    const stepX = (endX - startX) / (fsActiveDataset.value.length - 1);
    const dX = startX + fsDeltaHoverIndex.value * stepX;
    const delta = Math.max(0, pt.tempInternal - pt.tempExternal);
    const dY = 500 - Math.max(0, Math.min(1, delta / 25)) * 420;
    return {
        ...pt,
        dX,
        dY,
        delta,
        d3x:
            40 +
            fsDeltaHoverIndex.value *
                ((710 - 40) / (fsActiveDataset.value.length - 1)),
        d3y: 160 - Math.max(0, Math.min(1, delta / 25)) * 130,
    };
});

// Fullscreen 3-in-1 Composite Paths
const fs3in1TempIntLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 60;
    const endX = 1540;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y =
            280 - Math.max(0, Math.min(1, (p.tempInternal - 20) / 50)) * 240;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1TempExtLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 60;
    const endX = 1540;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y =
            280 - Math.max(0, Math.min(1, (p.tempExternal - 20) / 50)) * 240;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1HumLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 60;
    const endX = 1540;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y =
            280 - Math.max(0, Math.min(1, p.humidityInternal / 100)) * 240;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1MoistureLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 60;
    const endX = 1540;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y = 280 - Math.max(0, Math.min(1, p.grainMoisture / 100)) * 240;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1SolarLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 60;
    const endX = 1540;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y = 280 - Math.max(0, Math.min(1, p.solarRadiation / 1200)) * 140;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1KineticsLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 40;
    const endX = 710;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const y =
            160 - Math.max(0, Math.min(1, (p.grainMoisture - 10) / 15)) * 130;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1KineticsAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fs3in1KineticsLinePath.value} L 710 160 L 40 160 Z`;
});

const fs3in1DeltaLinePath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    const startX = 40;
    const endX = 710;
    const stepX = (endX - startX) / (pts.length - 1);
    return pts.reduce((acc, p, idx) => {
        const x = startX + idx * stepX;
        const delta = Math.max(0, p.tempInternal - p.tempExternal);
        const y = 160 - Math.max(0, Math.min(1, delta / 25)) * 130;
        return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
    }, "");
});

const fs3in1DeltaAreaPath = computed(() => {
    const pts = fsActiveDataset.value;
    if (pts.length < 2) return "";
    return `${fs3in1DeltaLinePath.value} L 710 160 L 40 160 Z`;
});

// Fullscreen Open & Close
function openFullscreen(type = "master") {
    fullscreenViewType.value = type;
    isChartFullscreen.value = true;
    document.body.style.overflow = "hidden";
}

function closeFullscreen() {
    isChartFullscreen.value = false;
    document.body.style.overflow = "";
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => {});
    }
}

function toggleNativeFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement
            .requestFullscreen()
            .then(() => {
                isNativeFullscreen.value = true;
            })
            .catch(() => {});
    } else {
        document
            .exitFullscreen()
            .then(() => {
                isNativeFullscreen.value = false;
            })
            .catch(() => {});
    }
}

function handleKeydown(e) {
    if (e.key === "Escape" && isChartFullscreen.value) {
        closeFullscreen();
    }
}

function handleFullscreenChange() {
    isNativeFullscreen.value = !!document.fullscreenElement;
}

// Aggregate Statistics Summary
const masterStats = computed(() => {
    const pts = activeDataset.value;
    if (pts.length === 0) {
        return {
            maxTemp: "--",
            avgTemp: "--",
            maxDelta: "--",
            avgHum: "--",
            dryingRate: "0.12",
        };
    }
    const temps = pts.map((p) => p.tempInternal);
    const hums = pts.map((p) => p.humidityInternal);
    const deltas = pts.map((p) => Math.max(0, p.tempInternal - p.tempExternal));

    const avgTemp = (temps.reduce((a, b) => a + b, 0) / temps.length).toFixed(
        1,
    );
    const maxTemp = Math.max(...temps).toFixed(1);
    const avgHum = (hums.reduce((a, b) => a + b, 0) / hums.length).toFixed(0);
    const maxDelta = Math.max(...deltas).toFixed(1);

    return {
        maxTemp,
        avgTemp,
        avgHum,
        maxDelta,
        dryingRate: "0.15",
    };
});

function pushLivePoint(data) {
    const d = new Date();
    const timeLabel = d.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
    livePoints.value.push({
        ...data,
        timestamp: d,
        label: timeLabel,
    });
    if (livePoints.value.length > 80) {
        livePoints.value.shift();
    }
}

async function loadHistoryData() {
    try {
        const hist = await telemetryService.getHistory(24);
        if (Array.isArray(hist) && hist.length > 0) {
            historyPoints.value = hist;
        }
    } catch (err) {
        console.warn("History fetch warning:", err.message);
    }
}

async function loadData(isFirstTime = false) {
    try {
        const [res] = await Promise.allSettled([
            telemetryService.getCurrent(),
            loadHistoryData(),
            loadAnomalyHealth(),
        ]);
        if (res.status === "fulfilled" && res.value) {
            const val = res.value;
            if (val.telemetry && val.telemetry.hasData) {
                telemetry.value = {
                    ...telemetry.value,
                    ...val.telemetry,
                    hasData: true,
                };
                pushLivePoint(val.telemetry);
            }
            if (val.actuators) {
                actuators.value = { ...actuators.value, ...val.actuators };
            }
        }
    } catch (e) {
        // Ignored
    } finally {
        if (isFirstTime) {
            setTimeout(() => {
                isInitialLoading.value = false;
            }, 400);
        }
    }
}

function exportTelemetryCsv() {
    const pts = activeDataset.value;
    if (pts.length === 0) return;

    const headers = [
        "Timestamp",
        "Suhu Internal (°C)",
        "Suhu Eksternal (°C)",
        "Kelembapan (% RH)",
        "Kadar Air (%)",
        "Radiasi Surya (W/m2)",
    ];
    const rows = pts.map((p) => [
        `"${p.fullTime}"`,
        p.tempInternal.toFixed(2),
        p.tempExternal.toFixed(2),
        p.humidityInternal.toFixed(2),
        p.grainMoisture.toFixed(2),
        p.solarRadiation.toFixed(1),
    ]);

    const csvContent =
        "data:text/csv;charset=utf-8," +
        [headers.join(","), ...rows.map((r) => r.join(","))].join("\n");
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `smart_dryer_telemetry_${Date.now()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

let pollTimer = null;

onMounted(() => {
    loadData(true);
    window.addEventListener("keydown", handleKeydown);
    document.addEventListener("fullscreenchange", handleFullscreenChange);

    socketService.on("telemetry_live", (data) => {
        if (data && data.hasData) {
            telemetry.value = { ...telemetry.value, ...data, hasData: true };
            pushLivePoint(data);
        }
    });

    socketService.on("actuators_update", (data) => {
        if (data) actuators.value = { ...actuators.value, ...data };
    });

    connectionManager.on("telemetry_live", (data) => {
        if (data && data.hasData) {
            telemetry.value = { ...telemetry.value, ...data, hasData: true };
            pushLivePoint(data);
        }
    });

    pollTimer = setInterval(() => loadData(false), 4000);
});

onUnmounted(() => {
    if (pollTimer) clearInterval(pollTimer);
    window.removeEventListener("keydown", handleKeydown);
    document.removeEventListener("fullscreenchange", handleFullscreenChange);
    document.body.style.overflow = "";
});
</script>

<style scoped>
.monitoring-page {
    width: 100%;
    padding: 32px 40px 60px;
    box-sizing: border-box;
    background: transparent;
}

.monitoring-main-container {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Header */
.header-action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.title-with-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.page-title {
    font-size: 32px;
    font-weight: 800;
    color: var(--color-text-title);
    margin: 0;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

:global(.dark-theme) .page-title {
    color: #f1f5f9;
}

.pro-analytics-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    background: #e8f5e9;
    color: #0d631b;
    border: 1px solid #a5d6a7;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

:global(.dark-theme) .pro-analytics-tag {
    background: rgba(74, 222, 128, 0.15);
    color: #4ade80;
    border-color: rgba(74, 222, 128, 0.3);
}

.page-subtitle {
    font-size: 16px;
    color: var(--color-text-muted);
    margin: 0;
    line-height: 1.5;
    display: flex;
    align-items: center;
    gap: 8px;
}

:global(.dark-theme) .page-subtitle {
    color: #94a3b8;
}

.live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #94a3b8;
}

.live-dot-active {
    background: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    animation: pulse 1.5s infinite;
}

.header-right-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-export-csv {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-export-csv:hover {
    background: #f1f5f9;
    color: #0f172a;
}

:global(.dark-theme) .btn-export-csv {
    background: #0b242f;
    border-color: #1e4e61;
    color: #cbd5e1;
}

.system-active-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12px;
}

.badge-online {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

:global(.dark-theme) .badge-online {
    background: rgba(16, 185, 129, 0.15);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.3);
}

.badge-standby {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
}

:global(.dark-theme) .badge-standby {
    background: rgba(245, 158, 11, 0.15);
    color: #fde68a;
    border-color: rgba(245, 158, 11, 0.3);
}

/* 4 Top KPI Cards */
.stats-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

@media (max-width: 1024px) {
    .stats-grid-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .stats-grid-4 {
        grid-template-columns: 1fr;
    }
}

.stat-card {
    background: var(--color-white, #ffffff);
    border: 1px solid rgba(203, 213, 225, 0.5);
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .stat-card {
    background: #0B242F;
    border-color: #1E4E61;
}

.stat-card-standby {
    opacity: 0.85;
}

.stat-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
}

.stat-label {
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.04em;
    color: #64748b;
}

:global(.dark-theme) .stat-label {
    color: #94a3b8;
}

.icon-circle {
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
}

.icon-red-subtle {
    background: rgba(186, 26, 26, 0.1);
}
.icon-blue-subtle {
    background: rgba(0, 93, 183, 0.1);
}
.icon-amber-subtle {
    background: rgba(217, 119, 6, 0.1);
}
.icon-purple-subtle {
    background: rgba(126, 34, 206, 0.1);
}

:global(.dark-theme) .icon-red-subtle {
    background: rgba(239, 68, 68, 0.2);
}
:global(.dark-theme) .icon-blue-subtle {
    background: rgba(59, 130, 246, 0.2);
}
:global(.dark-theme) .icon-amber-subtle {
    background: rgba(245, 158, 11, 0.2);
}
:global(.dark-theme) .icon-purple-subtle {
    background: rgba(168, 85, 247, 0.2);
}

.stat-value-row {
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.stat-number {
    font-size: 26px;
    font-weight: 800;
    color: #071e27;
    letter-spacing: -0.02em;
}

:global(.dark-theme) .stat-number {
    color: #ffffff;
}

.stat-unit {
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
}

.stat-trend {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    color: #64748b;
    padding-top: 4px;
    border-top: 1px solid rgba(191, 202, 186, 0.3);
}

:global(.dark-theme) .stat-trend {
    border-top-color: rgba(30, 78, 97, 0.4);
    color: #94a3b8;
}

.delta-chip {
    padding: 1px 6px;
    border-radius: 5px;
    background: #ecfdf5;
    color: #065f46;
    font-weight: 700;
    font-size: 10.5px;
}

.delta-chip-blue {
    padding: 1px 6px;
    border-radius: 5px;
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 700;
    font-size: 10.5px;
}

.delta-chip-amber {
    padding: 1px 6px;
    border-radius: 5px;
    background: #fffbeb;
    color: #b45309;
    font-weight: 700;
    font-size: 10.5px;
}

.delta-chip-purple {
    padding: 1px 6px;
    border-radius: 5px;
    background: #faf5ff;
    color: #7e22ce;
    font-weight: 700;
    font-size: 10.5px;
}

/* ========================================================================= */
/* MASTER CHART CARD STYLES */
/* ========================================================================= */
.master-chart-card {
    background: #ffffff;
    border: 1px solid rgba(191, 202, 186, 0.45);
    border-radius: 18px;
    padding: 22px 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    gap: 16px;
}

:global(.dark-theme) .master-chart-card {
    background: #0b242f;
    border-color: rgba(30, 78, 97, 0.6);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

.master-chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(191, 202, 186, 0.35);
}

:global(.dark-theme) .master-chart-header {
    border-bottom-color: rgba(30, 78, 97, 0.5);
}

.header-title-box {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chart-badge-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    background: rgba(13, 99, 27, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

:global(.dark-theme) .chart-badge-icon {
    background: rgba(74, 222, 128, 0.2);
}

.master-chart-heading {
    font-size: 15px;
    font-weight: 800;
    color: #071e27;
    margin: 0;
}

:global(.dark-theme) .master-chart-heading {
    color: #ffffff;
}

.master-chart-sub {
    font-size: 11.5px;
    color: #64748b;
    margin: 2px 0 0 0;
}

:global(.dark-theme) .master-chart-sub {
    color: #94a3b8;
}

/* Time Window Selector & Top Controls */
.chart-top-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.time-window-selector {
    display: inline-flex;
    background: #f1f5f9;
    border-radius: 9px;
    padding: 3px;
    gap: 2px;
}

:global(.dark-theme) .time-window-selector {
    background: #071e27;
}

.time-btn {
    padding: 6px 12px;
    border-radius: 7px;
    border: none;
    background: transparent;
    font-size: 11.5px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

:global(.dark-theme) .time-btn {
    color: #94a3b8;
}

.time-btn.active {
    background: #ffffff;
    color: #0d631b;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

:global(.dark-theme) .time-btn.active {
    background: #1e4e61;
    color: #ffffff;
}

.btn-chart-enlarge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid rgba(13, 99, 27, 0.3);
    background: rgba(13, 99, 27, 0.06);
    color: #0d631b;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-chart-enlarge:hover {
    background: #0d631b;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 99, 27, 0.25);
}

:global(.dark-theme) .btn-chart-enlarge {
    background: rgba(74, 222, 128, 0.12);
    border-color: rgba(74, 222, 128, 0.35);
    color: #4ade80;
}

:global(.dark-theme) .btn-chart-enlarge:hover {
    background: #4ade80;
    color: #052318;
}

/* Series Toggle Toolbar */
.series-toggle-toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    padding: 8px 12px;
    background: #f8fcfe;
    border: 1px solid rgba(191, 202, 186, 0.3);
    border-radius: 10px;
}

:global(.dark-theme) .series-toggle-toolbar {
    background: #071e27;
    border-color: rgba(30, 78, 97, 0.4);
}

.series-lbl {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
}

.series-toggle-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid transparent;
    background: transparent;
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.2s ease;
}

.series-toggle-pill.active {
    background: #ffffff;
    color: #071e27;
    border-color: rgba(191, 202, 186, 0.5);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

:global(.dark-theme) .series-toggle-pill.active {
    background: #0e2c39;
    color: #ffffff;
    border-color: #1e4e61;
}

.series-box-dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
}

.bg-red {
    background: #ba1a1a;
}
.bg-sky {
    background: #0284c7;
}
.bg-blue {
    background: #005db7;
}
.bg-purple {
    background: #7e22ce;
}
.bg-amber {
    background: #d97706;
}
.bg-green {
    background: #10b981;
}

/* SVG Canvas & Tooltip */
.svg-canvas-wrapper {
    position: relative;
    width: 100%;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
}

:global(.dark-theme) .svg-canvas-wrapper {
    background: #081b23;
}

.master-svg-chart {
    width: 100%;
    height: 360px;
    display: block;
}

/* Interactive Floating Tooltip */
.interactive-tooltip {
    position: absolute;
    background: rgba(7, 30, 39, 0.92);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 10px;
    padding: 10px 14px;
    color: #ffffff;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
    pointer-events: none;
    z-index: 25;
    min-width: 210px;
    transition: left 0.06s ease-out;
}

.sub-tooltip {
    min-width: 195px;
    padding: 8px 12px;
}

.tooltip-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    margin-bottom: 6px;
}

.tooltip-time {
    font-size: 11.5px;
    font-weight: 800;
    color: #38bdf8;
}

.tooltip-tag {
    font-size: 9.5px;
    color: #94a3b8;
}

.tooltip-grid {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.tooltip-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
}

.t-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.t-label {
    color: #cbd5e1;
    flex: 1;
}

.tooltip-row strong {
    color: #ffffff;
    font-weight: 700;
}

/* Master Bottom Analytics Strip */
.master-analytics-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    padding: 12px 16px;
    background: var(--color-white, #ffffff);
    border: 1px solid rgba(203, 213, 225, 0.4);
    border-radius: 12px;
}

@media (max-width: 768px) {
    .master-analytics-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}

:global(.dark-theme) .master-analytics-strip {
    background: #071E27;
    border-color: #1E4E61;
}

.analytic-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.an-lbl {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}

:global(.dark-theme) .an-lbl {
    color: #94a3b8;
}

.an-val {
    font-size: 14.5px;
    font-weight: 800;
    color: #071e27;
}

:global(.dark-theme) .an-val {
    color: #ffffff;
}

.text-green-dark {
    color: #0d631b;
}
.text-red {
    color: #ba1a1a;
}
.text-blue {
    color: #005db7;
}
.text-purple {
    color: #7e22ce;
}

:global(.dark-theme) .text-green-dark {
    color: #4ade80;
}
:global(.dark-theme) .text-red {
    color: #f87171;
}
:global(.dark-theme) .text-blue {
    color: #60a5fa;
}
:global(.dark-theme) .text-purple {
    color: #c084fc;
}

/* ========================================================================= */
/* SUB-CHARTS STYLES */
/* ========================================================================= */
.sub-charts-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

@media (max-width: 900px) {
    .sub-charts-grid-2 {
        grid-template-columns: 1fr;
    }
}

.sub-chart-card {
    background: var(--color-white, #ffffff);
    border: 1px solid rgba(203, 213, 225, 0.5);
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

:global(.dark-theme) .sub-chart-card {
    background: #0b242f;
    border-color: rgba(30, 78, 97, 0.6);
}

.sub-chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.sub-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sub-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-sub-enlarge {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #f8fafc;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-sub-enlarge:hover {
    background: #0d631b;
    color: #ffffff;
    border-color: #0d631b;
    transform: scale(1.05);
}

:global(.dark-theme) .btn-sub-enlarge {
    background: #071e27;
    border-color: rgba(30, 78, 97, 0.5);
    color: #94a3b8;
}

:global(.dark-theme) .btn-sub-enlarge:hover {
    background: #4ade80;
    color: #052318;
    border-color: #4ade80;
}

.sub-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sub-heading {
    font-size: 13.5px;
    font-weight: 800;
    color: #071e27;
    margin: 0;
}

:global(.dark-theme) .sub-heading {
    color: #ffffff;
}

.sub-desc {
    font-size: 11px;
    color: #64748b;
    margin: 1px 0 0 0;
}

:global(.dark-theme) .sub-desc {
    color: #94a3b8;
}

.moisture-badge {
    padding: 3px 8px;
    border-radius: 6px;
    background: #faf5ff;
    color: #7e22ce;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #e9d5ff;
}

:global(.dark-theme) .moisture-badge {
    background: rgba(126, 34, 206, 0.15);
    color: #c084fc;
    border-color: rgba(126, 34, 206, 0.3);
}

.thermal-badge {
    padding: 3px 8px;
    border-radius: 6px;
    background: #fffbeb;
    color: #b45309;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #fde68a;
}

:global(.dark-theme) .thermal-badge {
    background: rgba(245, 158, 11, 0.15);
    color: #fde68a;
    border-color: rgba(245, 158, 11, 0.3);
}

.sub-svg-box {
    position: relative;
    background: #f8fcfe;
    border: 1px solid rgba(191, 202, 186, 0.3);
    border-radius: 12px;
    overflow: hidden;
}

:global(.dark-theme) .sub-svg-box {
    background: #081b23;
    border-color: rgba(30, 78, 97, 0.4);
}

.sub-svg {
    width: 100%;
    height: 160px;
    display: block;
}

.sub-chart-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f8fcfe;
    border-radius: 8px;
    font-size: 11.5px;
}

:global(.dark-theme) .sub-chart-footer {
    background: #071e27;
}

.sc-stat {
    display: flex;
    align-items: center;
    gap: 6px;
}

.sc-lbl {
    color: #64748b;
    font-weight: 600;
}

:global(.dark-theme) .sc-lbl {
    color: #94a3b8;
}

.pulse-ring {
    animation: ripple 1.8s ease-out infinite;
}

@keyframes ripple {
    0% {
        r: 5px;
        opacity: 0.8;
    }
    100% {
        r: 16px;
        opacity: 0;
    }
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(0.85);
    }
}

/* ========================================================================= */
/* FULLSCREEN THEATER OVERLAY STYLES (Light & Dark Theme Adaptive) */
/* ========================================================================= */
.fullscreen-chart-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(248, 250, 252, 0.98);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: fadeIn 0.2s ease-out;
    color: #071e27;
}

:global(.dark-theme) .fullscreen-chart-overlay {
    background: #05141c;
    color: #ffffff;
}

.fullscreen-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 16px 24px;
    box-sizing: border-box;
    gap: 12px;
    overflow: hidden;
}

.fullscreen-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(203, 213, 225, 0.6);
}

:global(.dark-theme) .fullscreen-header {
    border-bottom-color: rgba(255, 255, 255, 0.1);
}

.fs-title-box {
    display: flex;
    align-items: center;
    gap: 12px;
}

.fs-live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.badge-live-active {
    background: #e8f5e9;
    color: #0d631b;
    border: 1px solid #a5d6a7;
}

:global(.dark-theme) .badge-live-active {
    background: rgba(16, 185, 129, 0.2);
    color: #4ade80;
    border-color: rgba(16, 185, 129, 0.4);
}

.badge-live-standby {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}

:global(.dark-theme) .badge-live-standby {
    background: rgba(148, 163, 184, 0.15);
    color: #94a3b8;
    border-color: rgba(148, 163, 184, 0.3);
}

.live-dot-pulse {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 8px #10b981;
    animation: pulse 1.2s infinite;
}

:global(.dark-theme) .live-dot-pulse {
    background: #4ade80;
    box-shadow: 0 0 8px #4ade80;
}

.fs-title {
    font-size: 18px;
    font-weight: 800;
    color: #071e27;
    margin: 0;
    letter-spacing: -0.3px;
}

:global(.dark-theme) .fs-title {
    color: #ffffff;
}

.fs-sub {
    font-size: 12px;
    color: #64748b;
    margin: 2px 0 0 0;
}

:global(.dark-theme) .fs-sub {
    color: #94a3b8;
}

/* Tabs */
.fs-tabs {
    display: inline-flex;
    background: #e2e8f0;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 3px;
    gap: 4px;
}

:global(.dark-theme) .fs-tabs {
    background: rgba(15, 23, 42, 0.8);
    border-color: rgba(255, 255, 255, 0.12);
}

.fs-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 7px;
    border: none;
    background: transparent;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

:global(.dark-theme) .fs-tab-btn {
    color: #94a3b8;
}

.fs-tab-btn:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.6);
}

:global(.dark-theme) .fs-tab-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
}

.fs-tab-btn.active {
    background: #0d631b;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(13, 99, 27, 0.35);
}

:global(.dark-theme) .fs-tab-btn.active {
    background: #1e4e61;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(30, 78, 97, 0.4);
}

/* Controls */
.fs-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.fs-action-icon-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.fs-action-icon-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

:global(.dark-theme) .fs-action-icon-btn {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    color: #cbd5e1;
}

:global(.dark-theme) .fs-action-icon-btn:hover {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
}

.fs-close-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 8px;
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #ba1a1a;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.fs-close-btn:hover {
    background: #ba1a1a;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(186, 26, 26, 0.3);
}

:global(.dark-theme) .fs-close-btn {
    background: rgba(186, 26, 26, 0.2);
    border-color: rgba(186, 26, 26, 0.5);
    color: #f87171;
}

:global(.dark-theme) .fs-close-btn:hover {
    background: #ba1a1a;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(186, 26, 26, 0.4);
}

/* Fullscreen Series Filter Strip */
.fs-series-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    padding: 6px 12px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid rgba(203, 213, 225, 0.6);
}

:global(.dark-theme) .fs-series-strip {
    background: rgba(15, 23, 42, 0.6);
    border-color: rgba(255, 255, 255, 0.08);
}

.fs-series-lbl {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    margin-right: 4px;
}

:global(.dark-theme) .fs-series-lbl {
    color: #94a3b8;
}

/* Fullscreen Canvas Area */
.fs-canvas-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    min-height: 0;
    background: #ffffff;
    border: 1px solid rgba(203, 213, 225, 0.6);
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

:global(.dark-theme) .fs-canvas-area {
    background: #081b24;
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: none;
}

.fs-svg-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.fs-master-svg {
    width: 100%;
    height: 100%;
    display: block;
}

.fs-tooltip {
    min-width: 250px;
    padding: 12px 16px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
}

.fs-tooltip-grid {
    gap: 6px;
}

.fs-tooltip-compact {
    min-width: 170px;
    padding: 8px 12px;
    font-size: 10.5px;
}

/* 3-in-1 Grid in Fullscreen */
.fs-grid-3in1 {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 8px;
    box-sizing: border-box;
}

.fs-grid-panel-main {
    flex: 1.2;
    position: relative;
    background: #f8fcfe;
    border: 1px solid rgba(203, 213, 225, 0.6);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

:global(.dark-theme) .fs-grid-panel-main {
    background: #0b242f;
    border-color: rgba(255, 255, 255, 0.08);
}

.fs-grid-bottom-split {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    min-height: 0;
}

.fs-grid-panel-sub {
    position: relative;
    background: #f8fcfe;
    border: 1px solid rgba(203, 213, 225, 0.6);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

:global(.dark-theme) .fs-grid-panel-sub {
    background: #0b242f;
    border-color: rgba(255, 255, 255, 0.08);
}

.fs-panel-title-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    background: #f1f5f9;
    border-bottom: 1px solid rgba(203, 213, 225, 0.5);
    font-size: 11.5px;
    font-weight: 700;
    color: #071e27;
}

:global(.dark-theme) .fs-panel-title-bar {
    background: rgba(0, 0, 0, 0.25);
    border-bottom-color: rgba(255, 255, 255, 0.06);
    color: #ffffff;
}

.fs-svg-wrapper-inner {
    flex: 1;
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

/* Fullscreen Footer Ribbon */
.fs-footer-bar {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 8px;
    padding: 8px 12px;
    background: #ffffff;
    border: 1px solid rgba(203, 213, 225, 0.6);
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .fs-footer-bar {
    background: rgba(15, 23, 42, 0.85);
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: none;
}

@media (max-width: 1100px) {
    .fs-footer-bar {
        grid-template-columns: repeat(4, 1fr);
    }
}

.fs-stat-chip {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.fs-stat-lbl {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}

:global(.dark-theme) .fs-stat-lbl {
    color: #94a3b8;
}

.fs-stat-val {
    font-size: 13.5px;
    font-weight: 800;
    color: #071e27;
}

:global(.dark-theme) .fs-stat-val {
    color: #ffffff;
}

/* Page Transition for Skeleton */
.fade-monitoring-enter-active,
.fade-monitoring-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-monitoring-enter-from {
    opacity: 0;
    transform: translateY(6px);
}

.fade-monitoring-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

/* ==========================================================================
   Sensor Health & Anomaly Diagnostics Styles
   ========================================================================== */
.sensor-health-card {
    border-radius: 16px;
    padding: 16px 20px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border-width: 1px;
    border-style: solid;
}

.sensor-health-card.health-optimal {
    background: linear-gradient(
        135deg,
        rgba(13, 99, 27, 0.05) 0%,
        rgba(13, 99, 27, 0.02) 100%
    );
    border-color: rgba(13, 99, 27, 0.25);
}

.sensor-health-card.health-warning {
    background: linear-gradient(
        135deg,
        rgba(217, 119, 6, 0.08) 0%,
        rgba(217, 119, 6, 0.03) 100%
    );
    border-color: rgba(217, 119, 6, 0.35);
}

.sensor-health-card.health-critical {
    background: linear-gradient(
        135deg,
        rgba(186, 26, 26, 0.1) 0%,
        rgba(186, 26, 26, 0.03) 100%
    );
    border-color: rgba(186, 26, 26, 0.4);
}

:global(.dark-theme) .sensor-health-card.health-optimal {
    background: linear-gradient(
        135deg,
        rgba(16, 185, 129, 0.1) 0%,
        rgba(15, 23, 42, 0.6) 100%
    );
    border-color: rgba(16, 185, 129, 0.3);
}

:global(.dark-theme) .sensor-health-card.health-warning {
    background: linear-gradient(
        135deg,
        rgba(245, 158, 11, 0.12) 0%,
        rgba(15, 23, 42, 0.7) 100%
    );
    border-color: rgba(245, 158, 11, 0.35);
}

:global(.dark-theme) .sensor-health-card.health-critical {
    background: linear-gradient(
        135deg,
        rgba(239, 68, 68, 0.15) 0%,
        rgba(15, 23, 42, 0.7) 100%
    );
    border-color: rgba(239, 68, 68, 0.4);
}

.health-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
}

.health-title-group {
    display: flex;
    align-items: center;
    gap: 14px;
}

.health-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.health-optimal .health-icon-box {
    background: rgba(13, 99, 27, 0.15);
    color: #0d631b;
}

.health-warning .health-icon-box {
    background: rgba(217, 119, 6, 0.15);
    color: #d97706;
}

.health-critical .health-icon-box {
    background: rgba(186, 26, 26, 0.15);
    color: #ba1a1a;
}

:global(.dark-theme) .health-optimal .health-icon-box {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
}

:global(.dark-theme) .health-warning .health-icon-box {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
}

:global(.dark-theme) .health-critical .health-icon-box {
    background: rgba(239, 68, 68, 0.2);
    color: #f87171;
}

.health-badge-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 3px;
    flex-wrap: wrap;
}

.health-score-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 2px 8px;
    border-radius: 6px;
    background: rgba(15, 23, 42, 0.08);
    color: #334155;
}

:global(.dark-theme) .health-score-pill {
    background: rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
}

.ai-spark-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #9333ea;
    box-shadow: 0 0 6px #9333ea;
}

.health-status-badge {
    font-size: 11px;
    font-weight: 700;
}

.health-optimal .health-status-badge {
    color: #0d631b;
}
.health-warning .health-status-badge {
    color: #d97706;
}
.health-critical .health-status-badge {
    color: #ba1a1a;
}

:global(.dark-theme) .health-optimal .health-status-badge {
    color: #34d399;
}
:global(.dark-theme) .health-warning .health-status-badge {
    color: #fbbf24;
}
:global(.dark-theme) .health-critical .health-status-badge {
    color: #f87171;
}

.health-card-heading {
    font-size: 14.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

:global(.dark-theme) .health-card-heading {
    color: #f8fafc;
}

.health-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-toggle-anomaly-details,
.btn-scan-anomaly {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-toggle-anomaly-details {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(203, 213, 225, 0.8);
    color: #1e293b;
}

.btn-toggle-anomaly-details:hover {
    background: #ffffff;
    border-color: #94a3b8;
    transform: translateY(-1px);
}

:global(.dark-theme) .btn-toggle-anomaly-details {
    background: rgba(30, 41, 59, 0.8);
    border-color: rgba(255, 255, 255, 0.15);
    color: #e2e8f0;
}

.btn-scan-anomaly {
    background: #0d631b;
    border: 1px solid transparent;
    color: #ffffff;
}

.btn-scan-anomaly:hover:not(:disabled) {
    background: #094713;
    transform: translateY(-1px);
}

.btn-scan-anomaly:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Anomaly Diagnosis List */
.anomaly-diagnosis-list {
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px dashed rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

:global(.dark-theme) .anomaly-diagnosis-list {
    border-top-color: rgba(255, 255, 255, 0.1);
}

.anomaly-detail-item {
    padding: 12px 16px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

:global(.dark-theme) .anomaly-detail-item {
    background: rgba(15, 23, 42, 0.6);
    border-color: rgba(255, 255, 255, 0.08);
}

.anomaly-detail-item.level-critical {
    border-left: 4px solid #ba1a1a;
}

.anomaly-detail-item.level-warning {
    border-left: 4px solid #d97706;
}

.anom-item-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.anom-level-chip {
    font-size: 10px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}

.level-critical .anom-level-chip {
    background: rgba(186, 26, 26, 0.15);
    color: #ba1a1a;
}

.level-warning .anom-level-chip {
    background: rgba(217, 119, 6, 0.15);
    color: #d97706;
}

.anom-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
}

:global(.dark-theme) .anom-title {
    color: #f1f5f9;
}

.anom-desc {
    font-size: 12.5px;
    color: #475569;
    margin: 0 0 8px 0;
    line-height: 1.45;
}

:global(.dark-theme) .anom-desc {
    color: #94a3b8;
}

.anom-meta-box {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 8px;
    padding: 8px 12px;
    background: rgba(0, 0, 0, 0.03);
    border-radius: 6px;
    font-size: 11.5px;
}

:global(.dark-theme) .anom-meta-box {
    background: rgba(255, 255, 255, 0.03);
}

.anom-cause strong,
.anom-solution strong {
    color: #0f172a;
}

:global(.dark-theme) .anom-cause strong,
:global(.dark-theme) .anom-solution strong {
    color: #e2e8f0;
}

.anom-cause span {
    color: #d97706;
}

.anom-solution span {
    color: #0d631b;
}

:global(.dark-theme) .anom-solution span {
    color: #34d399;
}
</style>
