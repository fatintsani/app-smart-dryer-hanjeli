<template>
    <div class="dashboard-page">
        <Transition name="fade-dashboard" mode="out-in">
            <!-- Loading State -->
            <DashboardSkeleton
                v-if="isInitialLoading"
                :is-mobile="isMobile"
                key="skeleton"
            />

            <!-- Loaded Farmer-Friendly Dashboard -->
            <div v-else class="dashboard-loaded-container" key="content">
                <div class="dashboard-main-content">
                    <!-- 1. Header Bar: Status Alat & Tombol Utama -->
                    <div class="dash-welcome-card">
                        <div class="welcome-left">
                            <div
                                class="welcome-status-pill"
                                :class="connectionStatusClass"
                            >
                                <span class="status-dot"></span>
                                <span>{{ connectionStatusLabel }}</span>
                            </div>
                            <h1 class="welcome-title">
                                Kondisi Ruang Pengering Hanjeli
                            </h1>
                            <p class="welcome-desc">
                                Pantau kondisi suhu, udara, dan proses
                                pengeringan gabah dengan mudah.
                            </p>
                        </div>

                        <div class="welcome-right">
                            <!-- If active drying session exists -->
                            <button
                                v-if="activeBatch"
                                class="btn-action-main btn-active-session"
                                @click="router.push('/drying/active')"
                            >
                                <div class="btn-icon-wrap">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                    >
                                        <polygon
                                            points="5 3 19 12 5 21 5 3"
                                        ></polygon>
                                    </svg>
                                </div>
                                <div class="btn-text-wrap">
                                    <span class="btn-title"
                                        >Lihat Pengeringan Aktif</span
                                    >
                                    <span class="btn-sub"
                                        >{{ activeBatch.cropVariety }} ({{
                                            activeBatch.batchCode
                                        }})</span
                                    >
                                </div>
                            </button>

                            <!-- If standby, start new batch -->
                            <button
                                v-else
                                class="btn-action-main btn-start-new"
                                @click="handleStartDryingClick"
                                :class="{
                                    'btn-hardware-offline': !isHardwareActive,
                                }"
                                :title="
                                    !isHardwareActive
                                        ? 'Alat sensor mikrokontroler harus terhubung terlebih dahulu'
                                        : 'Mulai Pengeringan Baru'
                                "
                            >
                                <div class="btn-icon-wrap">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                    >
                                        <line
                                            x1="12"
                                            y1="5"
                                            x2="12"
                                            y2="19"
                                        ></line>
                                        <line
                                            x1="5"
                                            y1="12"
                                            x2="19"
                                            y2="12"
                                        ></line>
                                    </svg>
                                </div>
                                <div class="btn-text-wrap">
                                    <span class="btn-title">{{
                                        isHardwareActive
                                            ? "Mulai Pengeringan Baru"
                                            : "Menunggu Alat Terbaca"
                                    }}</span>
                                    <span class="btn-sub">{{
                                        isHardwareActive
                                            ? "Masukkan gabah & mulai proses"
                                            : "Nyalakan alat untuk mulai"
                                    }}</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Banner Jika Sistem Sedang Dinonaktifkan Admin -->
                    <div
                        v-if="!systemState.isSystemActive"
                        class="system-offline-banner"
                    >
                        <div class="offline-icon">
                            <svg
                                width="22"
                                height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#DC2626"
                                stroke-width="2.2"
                            >
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="offline-text">
                            <strong>Sistem Sedang Dinonaktifkan</strong>
                            <p>
                                Operasional pengeringan sedang ditangguhkan
                                sementara untuk pemeliharaan.
                            </p>
                        </div>
                    </div>

                    <!-- Banner Pengeringan Aktif Ringkas (Jika Ada Batch Berjalan) -->
                    <div
                        v-if="activeBatch"
                        class="active-batch-banner"
                        @click="router.push('/drying/active')"
                    >
                        <div class="ab-left">
                            <div class="ab-badge">
                                <span class="pulse-ring"></span>
                                <span>SEDANG MENGERINGKAN</span>
                            </div>
                            <h2 class="ab-crop-name">
                                {{ activeBatch.cropVariety || "Biji Hanjeli" }}
                            </h2>
                            <div class="ab-meta-row">
                                <span
                                    >Kode:
                                    <strong>{{
                                        activeBatch.batchCode
                                    }}</strong></span
                                >
                                <span class="meta-dot">&bull;</span>
                                <span
                                    >Kadar Air Saat Ini:
                                    <strong class="text-green"
                                        >{{
                                            (isDataActive
                                                ? telemetry.grainMoisture
                                                : null) ??
                                            activeBatch.currentMoisturePercent ??
                                            15
                                        }}%</strong
                                    ></span
                                >
                                <span
                                    >Estimasi Kadar Air:
                                    <strong class="text-green"
                                        >{{
                                            (isDataActive
                                                ? telemetry.grainMoisture
                                                : null) ??
                                            activeBatch.currentMoisturePercent ??
                                            15
                                        }}%</strong
                                    >
                                    <small
                                        class="est-note"
                                        title="Dihitung dari model matematis laju pengeringan berdasarkan suhu dan durasi"
                                        >(Estimasi Model)</small
                                    ></span
                                >
                                <span class="meta-dot">&bull;</span>
                                <span
                                    >Target:
                                    <strong
                                        >{{
                                            activeBatch.targetMoisturePercent ||
                                            12
                                        }}%</strong
                                    ></span
                                >
                            </div>
                        </div>
                        <div class="ab-right">
                            <div class="ab-progress-box">
                                <div class="ab-progress-text">
                                    <span>Progres Pengeringan</span>
                                    <strong
                                        >{{ dryingProgressPercent }}%</strong
                                    >
                                </div>
                                <div class="ab-progress-bar">
                                    <div
                                        class="ab-progress-fill"
                                        :style="{
                                            width: dryingProgressPercent + '%',
                                        }"
                                    ></div>
                                </div>
                            </div>
                            <div class="ab-arrow">
                                <span>Pantau &rarr;</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Empat Informasi Utama Ruang Pengering (Hero Indicator Cards) -->
                    <div class="hero-indicators-grid">
                        <!-- 1. Suhu Ruangan -->
                        <div class="indicator-card card-temp">
                            <div class="card-top">
                                <div class="card-icon-wrap icon-temp">
                                    <svg
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#D97706"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"
                                        ></path>
                                    </svg>
                                </div>
                                <span class="card-tag" :class="tempStatusClass">
                                    {{ tempStatusLabel }}
                                </span>
                            </div>

                            <div class="card-body">
                                <span class="card-title">Suhu Ruangan</span>
                                <div class="card-number-row">
                                    <span class="card-big-number">{{
                                        isDataActive
                                            ? telemetry.tempInternal.toFixed(1)
                                            : "--"
                                    }}</span>
                                    <span class="card-unit">°C</span>
                                </div>
                                <p class="card-hint">
                                    {{ tempHintText }}
                                </p>
                            </div>
                        </div>

                        <!-- 2. Kelembapan Udara -->
                        <div class="indicator-card card-hum">
                            <div class="card-top">
                                <div class="card-icon-wrap icon-hum">
                                    <svg
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#0284C7"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"
                                        ></path>
                                    </svg>
                                </div>
                                <span
                                    class="card-tag"
                                    :class="humidityStatusClass"
                                >
                                    {{ humidityStatusLabel }}
                                </span>
                            </div>

                            <div class="card-body">
                                <span class="card-title">Kelembapan Udara</span>
                                <div class="card-number-row">
                                    <span class="card-big-number">{{
                                        isDataActive
                                            ? telemetry.humidityInternal.toFixed(
                                                  0,
                                              )
                                            : "--"
                                    }}</span>
                                    <span class="card-unit">%</span>
                                </div>
                                <p class="card-hint">
                                    {{ humidityHintText }}
                                </p>
                            </div>
                        </div>

                        <!-- 3. Cuaca & Kondisi Luar -->
                        <div class="indicator-card card-weather">
                            <div class="card-top">
                                <div class="card-icon-wrap icon-weather">
                                    <!-- Rain Icon -->
                                    <svg
                                        v-if="telemetry.isRaining"
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#0284C7"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"
                                        ></path>
                                        <line
                                            x1="8"
                                            y1="19"
                                            x2="8"
                                            y2="21"
                                        ></line>
                                        <line
                                            x1="16"
                                            y1="19"
                                            x2="16"
                                            y2="21"
                                        ></line>
                                    </svg>
                                    <!-- Sunny Icon -->
                                    <svg
                                        v-else-if="
                                            telemetry.solarRadiation >= 150
                                        "
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#D97706"
                                        stroke-width="2.2"
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
                                        <line
                                            x1="1"
                                            y1="12"
                                            x2="3"
                                            y2="12"
                                        ></line>
                                        <line
                                            x1="21"
                                            y1="12"
                                            x2="23"
                                            y2="12"
                                        ></line>
                                    </svg>
                                    <!-- Cloudy / Night Icon -->
                                    <svg
                                        v-else
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#64748B"
                                        stroke-width="2.2"
                                    >
                                        <path
                                            d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"
                                        ></path>
                                    </svg>
                                </div>
                                <span class="card-tag" :class="weatherTagClass">
                                    {{ weatherTagLabel }}
                                </span>
                            </div>

                            <div class="card-body">
                                <span class="card-title">Cuaca & Cahaya</span>
                                <div class="card-number-row">
                                    <span class="card-text-large">{{
                                        weatherConditionLabel
                                    }}</span>
                                </div>
                                <p class="card-hint">
                                    {{ weatherHintText }}
                                </p>
                            </div>
                        </div>

                        <!-- 4. Alat Pemanas Tambahan (Heater) -->
                        <div class="indicator-card card-heater">
                            <div class="card-top">
                                <div
                                    class="card-icon-wrap"
                                    :class="
                                        isHeaterActive
                                            ? 'icon-heater-on'
                                            : 'icon-heater-off'
                                    "
                                >
                                    <svg
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.2"
                                    >
                                        <polygon
                                            points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"
                                        ></polygon>
                                    </svg>
                                </div>
                                <span
                                    class="card-tag"
                                    :class="
                                        isHeaterActive
                                            ? 'tag-warning'
                                            : 'tag-neutral'
                                    "
                                >
                                    {{
                                        isHardwareActive
                                            ? isHeaterActive
                                                ? "SEDANG MEMANASKAN"
                                                : "STANDBY"
                                            : "ALAT OFFLINE"
                                    }}
                                </span>
                            </div>

                            <div class="card-body">
                                <span class="card-title">Pemanas Tambahan</span>
                                <div class="card-number-row">
                                    <span
                                        class="card-text-large"
                                        :class="
                                            isHeaterActive
                                                ? 'text-amber'
                                                : 'text-neutral'
                                        "
                                    >
                                        {{
                                            isHardwareActive
                                                ? isHeaterActive
                                                    ? "Pemanas Nyala"
                                                    : "Pemanas Mati"
                                                : "Menunggu Alat"
                                        }}
                                    </span>
                                </div>

                                <!-- Tombol Sakelar Pemanas Langsung -->
                                <div class="card-heater-btn-box">
                                    <button
                                        class="btn-heater-toggle"
                                        :class="
                                            isHeaterActive
                                                ? 'btn-heater-stop'
                                                : 'btn-heater-start'
                                        "
                                        :disabled="
                                            isTogglingHeater ||
                                            !isHardwareActive ||
                                            (telemetry.tempInternal >= 50 &&
                                                !isHeaterActive)
                                        "
                                        @click="handleToggleHeater"
                                    >
                                        <span>{{
                                            isTogglingHeater
                                                ? "Memproses..."
                                                : isHardwareActive
                                                  ? isHeaterActive
                                                      ? "Matikan Pemanas"
                                                      : "Nyalakan Pemanas"
                                                  : "Alat Offline"
                                        }}</span>
                                    </button>
                                </div>
                                <!-- Alasan Otomatisasi Pemanas (Heater Reason) -->
                                <div class="heater-reason-box" :class="heaterReasonInfo.statusClass">
                                    <div class="hrb-row">
                                        <span class="hrb-label">Status:</span>
                                        <strong class="hrb-status-text" :class="isHeaterActive ? 'text-amber' : (heaterReasonInfo.isWarning ? 'text-rose' : 'text-neutral')">
                                            {{ isHardwareActive ? heaterReasonInfo.statusText : 'MENUNGGU ALAT' }}
                                        </strong>
                                    </div>
                                    <div class="hrb-row hrb-reason-row">
                                        <span class="hrb-label">Alasan:</span>
                                        <p class="hrb-reason-text">
                                            {{ heaterReasonInfo.fullReason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Analisis Statistik Suhu & Kelembapan (1 Hari, 1 Minggu, 1 Bulan, Semua) -->
                    <div class="simple-chart-card analysis-chart-card">
                        <!-- Header: Title, Period Badge, Filter Tabs, and Export Button -->
                        <div class="chart-header">
                            <div class="chart-header-left">
                                <div class="chart-title-row">
                                    <h2 class="chart-title">
                                        Perkembangan Suhu & Kelembapan
                                    </h2>
                                    <span class="period-active-chip">{{
                                        periodLabel
                                    }}</span>
                                </div>
                                <p class="chart-subtitle">
                                    Grafik kestabilan suhu dan kelembapan ruang
                                    pengering selama proses.
                                </p>
                            </div>

                            <!-- Timeframe Selector Tabs & Export Action -->
                            <div class="chart-actions-toolbar">
                                <div class="timeframe-pill-selector">
                                    <button
                                        type="button"
                                        class="timeframe-btn"
                                        :class="{
                                            active: selectedPeriod === '1d',
                                        }"
                                        @click="setPeriod('1d')"
                                    >
                                        1 Hari
                                    </button>
                                    <button
                                        type="button"
                                        class="timeframe-btn"
                                        :class="{
                                            active: selectedPeriod === '1w',
                                        }"
                                        @click="setPeriod('1w')"
                                    >
                                        1 Minggu
                                    </button>
                                    <button
                                        type="button"
                                        class="timeframe-btn"
                                        :class="{
                                            active: selectedPeriod === '1m',
                                        }"
                                        @click="setPeriod('1m')"
                                    >
                                        1 Bulan
                                    </button>
                                    <button
                                        type="button"
                                        class="timeframe-btn"
                                        :class="{
                                            active: selectedPeriod === 'all',
                                        }"
                                        @click="setPeriod('all')"
                                    >
                                        Semua
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    class="btn-chart-export"
                                    @click="exportPeriodCsv"
                                    :title="`Unduh data analisis format CSV (${periodLabel})`"
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
                                        <line
                                            x1="12"
                                            y1="15"
                                            x2="12"
                                            y2="3"
                                        ></line>
                                    </svg>
                                    <span>Unduh CSV</span>
                                </button>
                            </div>
                        </div>

                        <!-- Ringkasan Metrik Ringkas & User-Friendly (Simple & Clean) -->
                        <div class="simple-metric-badges">
                            <div class="metric-chip">
                                <span class="metric-dot dot-red"></span>
                                <span class="metric-name">Rata-rata Suhu:</span>
                                <strong class="metric-val text-red"
                                    >{{ analyticsData.avgTemp }}°C</strong
                                >
                                <span class="metric-range"
                                    >({{ analyticsData.minTemp }}° –
                                    {{ analyticsData.maxTemp }}°)</span
                                >
                            </div>

                            <div class="metric-chip">
                                <span class="metric-dot dot-blue"></span>
                                <span class="metric-name"
                                    >Rata-rata Kelembapan:</span
                                >
                                <strong class="metric-val text-blue"
                                    >{{ analyticsData.avgHumidity }}% RH</strong
                                >
                            </div>

                            <div class="metric-chip">
                                <span class="metric-dot dot-green"></span>
                                <span class="metric-name">Suhu Ideal SNI:</span>
                                <strong class="metric-val text-green"
                                    >{{ analyticsData.idealRatioPercent }}%
                                    Optimal</strong
                                >
                            </div>

                            <div class="metric-chip">
                                <span class="metric-dot dot-amber"></span>
                                <span class="metric-name"
                                    >Aktivitas Pemanas:</span
                                >
                                <strong class="metric-val text-amber"
                                    >{{ analyticsData.heaterActivePercent }}%
                                    Sesi</strong
                                >
                            </div>
                        </div>

                        <!-- Legend & Hint Row -->
                        <div class="chart-legend-bar">
                            <div class="chart-legend">
                                <span class="legend-item"
                                    ><span
                                        class="legend-color legend-red"
                                    ></span>
                                    Suhu Ruang (°C)</span
                                >
                                <span class="legend-item"
                                    ><span
                                        class="legend-color legend-blue"
                                    ></span>
                                    Kelembapan Udara (%)</span
                                >
                                <span class="legend-item"
                                    ><span
                                        class="legend-color legend-green"
                                    ></span>
                                    Suhu Ideal SNI (40°C – 50°C)</span
                                >
                            </div>
                            <span class="chart-hint-text"
                                >Arahkan kursor atau sentuh kurva untuk melihat
                                rincian waktu</span
                            >
                        </div>

                        <!-- Chart Canvas Layout: Y-Axis + SVG Canvas + Overlay Badges -->
                        <div
                            class="chart-body-layout"
                            :class="{ 'chart-loading-pulse': isLoadingPeriod }"
                        >
                            <!-- Y-Axis Labels (Pure HTML, Bebas Distorsi Font) -->
                            <div class="chart-y-axis">
                                <span class="y-axis-lbl y-80">80°</span>
                                <span class="y-axis-lbl y-60">60°</span>
                                <span class="y-axis-lbl y-40">40°</span>
                                <span class="y-axis-lbl y-20">20°</span>
                            </div>

                            <!-- Main SVG Canvas Wrapper -->
                            <div
                                class="simple-svg-wrapper"
                                ref="chartWrapperRef"
                                @mousemove="handleChartMouseMove"
                                @mouseleave="handleChartMouseLeave"
                                @touchstart.passive="handleChartTouch"
                                @touchmove.passive="handleChartTouch"
                            >
                                <!-- Badge Suhu Ideal (Pure HTML Pill, Tidak Melar / Gepeng) -->
                                <div class="ideal-zone-overlay">
                                    <span class="ideal-zone-pill">
                                        <svg
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <polyline
                                                points="20 6 9 17 4 12"
                                            ></polyline>
                                        </svg>
                                        <span>Suhu Ideal (40°C – 50°C)</span>
                                    </span>
                                </div>

                                <svg
                                    viewBox="0 0 1000 240"
                                    class="simple-svg"
                                    preserveAspectRatio="none"
                                >
                                    <!-- Area Hijau Suhu Ideal (40 - 50°C) -->
                                    <rect
                                        x="0"
                                        y="110"
                                        width="1000"
                                        height="30"
                                        fill="#1B5E20"
                                        fill-opacity="0.08"
                                    />

                                    <!-- Horizontal Reference Grid Lines -->
                                    <line
                                        x1="0"
                                        y1="20"
                                        x2="1000"
                                        y2="20"
                                        stroke="#E2E8F0"
                                        stroke-dasharray="4 4"
                                        class="chart-grid-line"
                                    />
                                    <line
                                        x1="0"
                                        y1="80"
                                        x2="1000"
                                        y2="80"
                                        stroke="#E2E8F0"
                                        stroke-dasharray="4 4"
                                        class="chart-grid-line"
                                    />
                                    <line
                                        x1="0"
                                        y1="140"
                                        x2="1000"
                                        y2="140"
                                        stroke="#E2E8F0"
                                        stroke-dasharray="4 4"
                                        class="chart-grid-line"
                                    />
                                    <line
                                        x1="0"
                                        y1="200"
                                        x2="1000"
                                        y2="200"
                                        stroke="#CBD5E1"
                                        class="chart-baseline"
                                    />

                                    <!-- Data Lines -->
                                    <path
                                        v-if="humidityCurvePath"
                                        :d="humidityCurvePath"
                                        fill="none"
                                        stroke="#0284C7"
                                        stroke-width="2.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        v-if="tempCurvePath"
                                        :d="tempCurvePath"
                                        fill="none"
                                        stroke="#DC2626"
                                        stroke-width="3.2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />

                                    <!-- Hover Guidance Line & Highlighted Points -->
                                    <g
                                        v-if="activeHoverPoint"
                                        class="chart-hover-elements"
                                    >
                                        <line
                                            :x1="activeHoverPoint.x"
                                            y1="20"
                                            :x2="activeHoverPoint.x"
                                            y2="200"
                                            stroke="#64748B"
                                            stroke-width="1.5"
                                            stroke-dasharray="4 4"
                                            opacity="0.8"
                                        />
                                        <!-- Kelembapan Point (Blue) -->
                                        <circle
                                            :cx="activeHoverPoint.x"
                                            :cy="activeHoverPoint.yHum"
                                            r="8"
                                            fill="#0284C7"
                                            fill-opacity="0.25"
                                        />
                                        <circle
                                            :cx="activeHoverPoint.x"
                                            :cy="activeHoverPoint.yHum"
                                            r="4.5"
                                            fill="#FFFFFF"
                                            stroke="#0284C7"
                                            stroke-width="2.5"
                                        />

                                        <!-- Suhu Point (Red) -->
                                        <circle
                                            :cx="activeHoverPoint.x"
                                            :cy="activeHoverPoint.yTemp"
                                            r="8"
                                            fill="#DC2626"
                                            fill-opacity="0.25"
                                        />
                                        <circle
                                            :cx="activeHoverPoint.x"
                                            :cy="activeHoverPoint.yTemp"
                                            r="4.5"
                                            fill="#FFFFFF"
                                            stroke="#DC2626"
                                            stroke-width="2.5"
                                        />
                                    </g>
                                </svg>

                                <!-- Floating Tooltip Card -->
                                <Transition name="fade-tooltip">
                                    <div
                                        v-if="activeHoverPoint"
                                        class="chart-tooltip-bubble"
                                        :style="{
                                            left: `${activeHoverPoint.percentX}%`,
                                            transform:
                                                activeHoverPoint.percentX < 20
                                                    ? 'translateX(0%)'
                                                    : activeHoverPoint.percentX >
                                                        80
                                                      ? 'translateX(-100%)'
                                                      : 'translateX(-50%)',
                                        }"
                                    >
                                        <div class="tooltip-time-row">
                                            <svg
                                                width="12"
                                                height="12"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.2"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                ></circle>
                                                <polyline
                                                    points="12 6 12 12 16 14"
                                                ></polyline>
                                            </svg>
                                            <span>{{
                                                activeHoverPoint.fullDate
                                                    ? activeHoverPoint.fullDate
                                                    : `Pukul ${activeHoverPoint.time} WIB`
                                            }}</span>
                                        </div>

                                        <div class="tooltip-metric-row">
                                            <div class="tooltip-metric-left">
                                                <span
                                                    class="metric-dot dot-red"
                                                ></span>
                                                <span class="metric-title"
                                                    >Suhu:</span
                                                >
                                            </div>
                                            <div class="tooltip-metric-right">
                                                <strong
                                                    class="metric-value text-red"
                                                    >{{
                                                        activeHoverPoint.tempInternal
                                                    }}°C</strong
                                                >
                                                <span
                                                    class="status-chip"
                                                    :class="
                                                        activeHoverPoint.tempInternal >=
                                                            40 &&
                                                        activeHoverPoint.tempInternal <=
                                                            50
                                                            ? 'chip-ideal'
                                                            : activeHoverPoint.tempInternal <
                                                                40
                                                              ? 'chip-low'
                                                              : 'chip-high'
                                                    "
                                                >
                                                    {{
                                                        activeHoverPoint.tempInternal >=
                                                            40 &&
                                                        activeHoverPoint.tempInternal <=
                                                            50
                                                            ? "Ideal"
                                                            : activeHoverPoint.tempInternal <
                                                                40
                                                              ? "Rendah"
                                                              : "Tinggi"
                                                    }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="tooltip-metric-row">
                                            <div class="tooltip-metric-left">
                                                <span
                                                    class="metric-dot dot-blue"
                                                ></span>
                                                <span class="metric-title"
                                                    >Kelembapan:</span
                                                >
                                            </div>
                                            <div class="tooltip-metric-right">
                                                <strong
                                                    class="metric-value text-blue"
                                                    >{{
                                                        activeHoverPoint.humidityInternal
                                                    }}%</strong
                                                >
                                                <span class="metric-sub"
                                                    >RH</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- X-Axis Time Labels -->
                        <div class="simple-chart-time-row">
                            <span
                                v-for="(lbl, idx) in xTimeLabels"
                                :key="idx"
                                class="time-lbl"
                                >{{ lbl.time }}</span
                            >
                        </div>
                    </div>

                    <!-- 4. Pemberitahuan Singkat Terkini (Jika ada hal penting) -->
                    <div
                        v-if="recentAlerts.length > 0"
                        class="alerts-simple-card"
                    >
                        <h3 class="alerts-title">
                            Pemberitahuan Sistem Terkini
                        </h3>
                        <div class="alerts-list">
                            <div
                                v-for="(alert, idx) in recentAlerts.slice(0, 3)"
                                :key="alert.id || idx"
                                class="alert-item"
                                :class="
                                    alert.type === 'DANGER'
                                        ? 'alert-danger'
                                        : alert.type === 'WARNING'
                                          ? 'alert-warning'
                                          : 'alert-info'
                                "
                            >
                                <div class="alert-dot"></div>
                                <div class="alert-content">
                                    <strong class="alert-heading">{{
                                        alert.title
                                    }}</strong>
                                    <span class="alert-msg">{{
                                        alert.message
                                    }}</span>
                                </div>
                                <span class="alert-time">{{
                                    formatAlertTime(alert.createdAt)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Wi-Fi Setup Modal -->
        <DeviceSetupBleModal
            :is-open="isWifiSetupOpen"
            @close="isWifiSetupOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { telemetryService } from "../../services/telemetryService";
import { dashboardService } from "../../services/dashboardService";
import { alertService } from "../../services/alertService";
import { systemState } from "../../services/settingsService";
import { actuatorService } from "../../services/actuatorService";
import { alertDialog } from "../../services/confirmDialogService";
import connectionManager from "../../services/connectionManager";
import { resolveHeaterReason } from "../../services/heaterAutomationHelper";
import DeviceSetupBleModal from "../../components/DeviceSetupBleModal.vue";
import DashboardSkeleton from "../../components/DashboardSkeleton.vue";

const router = useRouter();
const isInitialLoading = ref(true);

defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["start-drying"]);

const isWifiSetupOpen = ref(false);
const isConnected = ref(true);

const telemetry = ref({
    tempInternal: 0,
    humidityInternal: 0,
    solarRadiation: 0,
    isRaining: false,
    heaterStatus: false,
    heaterReason: null,
    grainMoisture: 0,
    weightCurrentKg: 0,
    hasData: false,
    isLive: false,
});

const actuators = ref({
    auxHeaterStatus: false,
    controlMode: "AUTOMATIC",
});

const isTogglingHeater = ref(false);
const activeBatch = ref(null);
const recentAlerts = ref([]);
const liveHistoryPoints = ref([]);

const isHardwareActive = computed(() => {
    return Boolean(
        connectionManager.state.isHardwareActive || telemetry.value.isLive,
    );
});

const isDataActive = computed(() => {
    return Boolean(
        isHardwareActive.value &&
        (telemetry.value.tempInternal > 0 ||
            telemetry.value.humidityInternal > 0),
    );
});

const connectionStatusLabel = computed(() => {
    if (isHardwareActive.value) return "Alat Pengering Terhubung (Live)";
    return "Alat Pengering Belum Terhubung";
});

const connectionStatusClass = computed(() => {
    if (isHardwareActive.value) return "status-online";
    return "status-offline";
});

const isHeaterOn = computed(() => {
    return Boolean(
        telemetry.value.heaterStatus || actuators.value.auxHeaterStatus,
    );
});

const isHeaterActive = computed(() => {
    return Boolean(isHardwareActive.value && isHeaterOn.value);
});

// Status Helpers
const tempStatusLabel = computed(() => {
    if (!isDataActive.value) return "MENUNGGU ALAT";
    const t = telemetry.value.tempInternal;
    if (t >= 50) return "SUHU PANAS (≥50°C)";
    if (t >= 40) return "SUHU OPTIMAL";
    return "SUHU HANGAT";
});

const tempStatusClass = computed(() => {
    if (!isDataActive.value) return "tag-neutral";
    const t = telemetry.value.tempInternal;
    if (t >= 50) return "tag-danger";
    if (t >= 40) return "tag-success";
    return "tag-warning";
});

const tempHintText = computed(() => {
    if (!isDataActive.value) return "Menghubungkan ke sensor ruang...";
    const t = telemetry.value.tempInternal;
    if (t >= 50)
        return "Pemanas otomatis istirahat demi menjaga kualitas biji.";
    if (t >= 40)
        return "Suhu berada dalam batas ideal pengeringan biji hanjeli.";
    return "Suhu sedang naik menuju suhu pengeringan ideal (45°C - 50°C).";
});

const humidityStatusLabel = computed(() => {
    if (!isDataActive.value) return "MENUNGGU ALAT";
    const h = telemetry.value.humidityInternal;
    if (h <= 60) return "KERING TERKENDALI";
    if (h <= 70) return "CUKUP KERING";
    return "LEMBAP";
});

const humidityStatusClass = computed(() => {
    if (!isDataActive.value) return "tag-neutral";
    const h = telemetry.value.humidityInternal;
    if (h <= 60) return "tag-success";
    if (h <= 70) return "tag-info";
    return "tag-warning";
});

const humidityHintText = computed(() => {
    if (!isDataActive.value) return "Menghubungkan ke sensor udara...";
    const h = telemetry.value.humidityInternal;
    if (h <= 60)
        return "Udara dalam ruangan cukup kering untuk mengeringkan biji.";
    return "Kelembapan cukup tinggi, pemanas otomatis membantu mengeringkan.";
});

const weatherConditionLabel = computed(() => {
    if (!isDataActive.value) return "Memeriksa...";
    if (telemetry.value.isRaining) return "Sedang Hujan";
    if (telemetry.value.solarRadiation >= 150) return "Cerah Terik";
    if (telemetry.value.solarRadiation >= 50) return "Cukup Terang";
    return "Mendung / Malam";
});

const weatherTagLabel = computed(() => {
    if (!isDataActive.value) return "STANDBY";
    if (telemetry.value.isRaining) return "HUJAN DETEKSI";
    if (telemetry.value.solarRadiation >= 150) return "SINAR KUAT";
    return "SINAR LEMAH";
});

const weatherTagClass = computed(() => {
    if (!isDataActive.value) return "tag-neutral";
    if (telemetry.value.isRaining) return "tag-info";
    if (telemetry.value.solarRadiation >= 150) return "tag-warning";
    return "tag-neutral";
});

const weatherHintText = computed(() => {
    if (!isDataActive.value) return "Menunggu data cuaca...";
    if (telemetry.value.isRaining)
        return "Saat hujan, pemanas tambahan aktif otomatis agar gabah tidak lembap.";
    if (telemetry.value.solarRadiation >= 150)
        return "Sinar matahari sedang bagus dan membantu menghemat pemanas listrik.";
    return "Pemanas otomatis menyala saat sinar matahari berkurang.";
});

function getWibHour() {
    const now = new Date();
    const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
    const wib = new Date(utc + (7 * 3600000));
    return wib.getHours();
}

const isWithinNtpSchedule = computed(() => {
    const h = getWibHour();
    return (h >= 18 || h < 5);
});

const heaterScheduleHint = computed(() => {
    if (!isHardwareActive.value) {
        return "Hubungkan mikrokontroler ESP32 untuk memantau status pemanas.";
    }
    if (telemetry.value.tempInternal >= 50.0) {
        return "Suhu ruang telah mencapai batas aman (≥50°C). Pemanas dimatikan otomatis demi keselamatan biji hanjeli.";
    }
    if (isHeaterActive.value) {
        if (isWithinNtpSchedule.value) {
            return "Jadwal Malam NTP (18:00 - 05:00 WIB): Pemanas aktif otomatis menjaga kestabilan panas.";
        }
        if (telemetry.value.isRaining) {
            return "Otomatis Cuaca: Pemanas aktif karena sensor mendeteksi hujan di luar.";
        }
        if (telemetry.value.humidityInternal >= 70.0) {
            return "Otomatis Kelembapan: Pemanas aktif karena kelembapan ruangan tinggi (≥70% RH).";
        }
        if (telemetry.value.tempInternal <= 35.0) {
            return "Otomatis Suhu Dingin: Pemanas aktif untuk memulihkan suhu ideal (≤35°C).";
        }
        return "Pemanas aktif menjaga kestabilan mikroklimat ruang pengering.";
    } else {
        if (isWithinNtpSchedule.value) {
            return "Pemanas standby dalam jadwal malam (suhu ruang saat ini telah hangat / terkendali).";
        }
        return "Siang hari (05:00 - 18:00 WIB): Pemanas siaga; aktif otomatis jika hujan, dingin, atau lembap.";
    }
});

const heaterReasonInfo = computed(() => {
    return resolveHeaterReason(telemetry.value.heaterReason, {
        isHeaterActive: isHeaterActive.value,
        tempInternal: isHardwareActive.value ? telemetry.value.tempInternal : null,
        humidityInternal: isHardwareActive.value ? telemetry.value.humidityInternal : null,
        isRaining: Boolean(telemetry.value.isRaining),
        isWithinNtpSchedule: isWithinNtpSchedule.value,
        isOverrideActive: Boolean(actuators.value.controlMode === "MANUAL" || actuators.value.isOverrideActive),
        isHardwareActive: isHardwareActive.value,
    });
});

function handleStartDryingClick() {
    if (!isHardwareActive.value) {
        alertDialog({
            title: "Alat Pengering Belum Terbaca",
            message:
                "Alat sensor mikrokontroler (ESP32) belum terhubung atau belum mengirimkan data sensor. Pastikan alat pengering sudah dinyalakan dan sensor terbaca sebelum memulai pengeringan.",
            type: "warning",
            confirmText: "Mengerti",
        });
        return;
    }
    emit("start-drying");
}

const dryingProgressPercent = computed(() => {
    if (!activeBatch.value) return 0;
    const initial = activeBatch.value.initialMoisturePercent || 25.0;
    const target = activeBatch.value.targetMoisturePercent || 12.0;
    const current =
        (isDataActive.value ? telemetry.value.grainMoisture : null) ??
        activeBatch.value.currentMoisturePercent ??
        initial;
    if (initial <= target) return 100;
    const progress = ((initial - current) / (initial - target)) * 100;
    return Math.min(100, Math.max(0, Math.round(progress)));
});

// Toggle Heater
async function handleToggleHeater() {
    if (!isHardwareActive.value) {
        alertDialog({
            title: "Alat Belum Terhubung",
            message:
                "Mikrokontroler ESP32 belum terhubung. Pastikan alat dinyalakan sebelum mengatur status pemanas.",
            type: "warning",
            confirmText: "Mengerti",
        });
        return;
    }

    const currentStatus = isHeaterActive.value;
    const nextStatus = !currentStatus;

    if (nextStatus && telemetry.value.tempInternal >= 50) {
        alertDialog({
            title: "Batas Suhu Maksimal",
            message:
                "Pemanas tidak dapat dinyalakan: Suhu ruangan sudah mencapai 50°C demi keselamatan mutu gabah.",
            type: "warning",
            confirmText: "Mengerti",
        });
        return;
    }

    isTogglingHeater.value = true;
    try {
        await actuatorService.updateStatus({
            auxHeaterStatus: nextStatus,
            isOverrideActive: true,
            overrideMode: "MANUAL",
        });
        telemetry.value.heaterStatus = nextStatus;
        actuators.value.auxHeaterStatus = nextStatus;
    } catch (err) {
        console.warn("Gagal mengubah sakelar pemanas:", err);
    } finally {
        isTogglingHeater.value = false;
    }
}

// Timeframe & Analisis Multivariat (1 Hari, 1 Minggu, 1 Bulan, Semua)
const selectedPeriod = ref("1d");
const isLoadingPeriod = ref(false);

const analyticsData = ref({
    totalPoints: 0,
    avgTemp: "44.5",
    minTemp: "38.0",
    maxTemp: "49.0",
    avgHumidity: "58",
    minHumidity: "42",
    maxHumidity: "68",
    idealRatioPercent: 88,
    heaterActivePercent: 24,
    stabilityLabel: "Sangat Stabil (Standar SNI)",
    insightMessage:
        "Suhu ruang pengering terkendali stabil di zona ideal SNI (40°C - 50°C). Gabah kering merata tanpa retak termal.",
});

const periodLabel = computed(() => {
    switch (selectedPeriod.value) {
        case "1d":
            return "24 Jam Terakhir";
        case "1w":
            return "7 Hari Terakhir";
        case "1m":
            return "30 Hari Terakhir";
        case "all":
            return "Seluruh Riwayat";
        default:
            return "24 Jam Terakhir";
    }
});

async function setPeriod(period) {
    if (selectedPeriod.value === period && !isLoadingPeriod.value) return;
    selectedPeriod.value = period;
    await loadPeriodHistory(period);
}

async function loadPeriodHistory(period = "1d") {
    isLoadingPeriod.value = true;
    try {
        const res = await telemetryService.getHistory(period);
        let records = [];
        if (res && res.records && Array.isArray(res.records)) {
            records = res.records;
            if (res.analytics) {
                analyticsData.value = {
                    ...res.analytics,
                    avgTemp: Number(res.analytics.avgTemp || 0).toFixed(1),
                    minTemp: Number(res.analytics.minTemp || 0).toFixed(1),
                    maxTemp: Number(res.analytics.maxTemp || 0).toFixed(1),
                    avgHumidity: Math.round(
                        Number(res.analytics.avgHumidity || 0),
                    ),
                    minHumidity: Math.round(
                        Number(res.analytics.minHumidity || 0),
                    ),
                    maxHumidity: Math.round(
                        Number(res.analytics.maxHumidity || 0),
                    ),
                    idealRatioPercent: Math.round(
                        Number(res.analytics.idealRatioPercent || 0),
                    ),
                    heaterActivePercent: Math.round(
                        Number(res.analytics.heaterActivePercent || 0),
                    ),
                };
            }
        } else if (Array.isArray(res)) {
            records = res;
        }

        if (records.length > 0) {
            liveHistoryPoints.value = records.map((r) => ({
                time:
                    r.label ||
                    (r.timestamp
                        ? new Date(r.timestamp).toLocaleTimeString("id-ID", {
                              hour: "2-digit",
                              minute: "2-digit",
                          })
                        : "--:--"),
                fullDate:
                    r.fullDate ||
                    (r.timestamp
                        ? new Date(r.timestamp).toLocaleString("id-ID", {
                              dateStyle: "medium",
                              timeStyle: "short",
                          }) + " WIB"
                        : ""),
                tempInternal: Number(
                    Number(r.tempInternal ?? r.temp_internal ?? 0).toFixed(1),
                ),
                humidityInternal: Number(
                    Math.round(
                        Number(r.humidityInternal ?? r.humidity_internal ?? 0),
                    ),
                ),
                heaterStatus: Boolean(r.heaterStatus ?? r.heater_status),
            }));

            if (!res?.analytics) {
                computeClientAnalytics(liveHistoryPoints.value, period);
            }
        } else {
            seedInitialHistory();
            computeClientAnalytics(liveHistoryPoints.value, period);
        }
    } catch (err) {
        console.warn("Gagal memuat histori periode:", err);
        if (liveHistoryPoints.value.length === 0) {
            seedInitialHistory();
            computeClientAnalytics(liveHistoryPoints.value, period);
        }
    } finally {
        isLoadingPeriod.value = false;
    }
}

function computeClientAnalytics(points, period) {
    if (!points || points.length === 0) return;
    const count = points.length;
    const temps = points.map((p) => Number(p.tempInternal || 0));
    const hums = points.map((p) => Number(p.humidityInternal || 0));

    const sumT = temps.reduce((a, b) => a + b, 0);
    const sumH = hums.reduce((a, b) => a + b, 0);

    const avgT = (sumT / count).toFixed(1);
    const minT = Math.min(...temps).toFixed(1);
    const maxT = Math.max(...temps).toFixed(1);

    const avgH = Math.round(sumH / count);
    const minH = Math.round(Math.min(...hums));
    const maxH = Math.round(Math.max(...hums));

    const idealCount = temps.filter((t) => t >= 40 && t <= 50).length;
    const idealRatio = Math.round((idealCount / count) * 100);

    const heaterCount = points.filter((p) => p.heaterStatus).length;
    const heaterRatio = Math.round((heaterCount / count) * 100);

    let stability = "Optimal & Sesuai SNI";
    if (idealRatio < 50) {
        stability = "Perlu Penyesuaian Pemanas";
    } else if (idealRatio < 80) {
        stability = "Cukup Stabil";
    }

    const pName =
        period === "1w"
            ? "1 Minggu"
            : period === "1m"
              ? "1 Bulan"
              : period === "all"
                ? "Seluruh Riwayat"
                : "24 Jam Terakhir";
    const insight = `Dalam periode ${pName}, ${idealRatio}% waktu suhu ruangan berada di zona ideal SNI (40°C - 50°C). Suhu terendah ${minT}°C dan tertinggi ${maxT}°C dengan rata-rata kelembapan ${avgH}%.`;

    analyticsData.value = {
        totalPoints: count,
        avgTemp: avgT,
        minTemp: minT,
        maxTemp: maxT,
        avgHumidity: avgH,
        minHumidity: minH,
        maxHumidity: maxH,
        idealRatioPercent: idealRatio,
        heaterActivePercent: heaterRatio,
        stabilityLabel: stability,
        insightMessage: insight,
    };
}

function exportPeriodCsv() {
    const points = liveHistoryPoints.value;
    if (!points || points.length === 0) {
        alert("Tidak ada data telemetri yang tersedia untuk diunduh.");
        return;
    }

    const periodSlug = selectedPeriod.value;
    const dateStr = new Date().toISOString().slice(0, 10);
    const filename = `Analisis_Suhu_Kelembapan_${periodSlug}_${dateStr}.csv`;

    const headers = [
        "Waktu",
        "Suhu Internal (C)",
        "Kelembapan Internal (%)",
        "Status Suhu SNI",
        "Status Pemanas",
    ];
    const rows = points.map((pt) => {
        const isIdeal =
            pt.tempInternal >= 40 && pt.tempInternal <= 50
                ? "Ideal SNI"
                : pt.tempInternal < 40
                  ? "Kurang Panas"
                  : "Terlalu Panas";
        const heater = pt.heaterStatus ? "AKTIF" : "MATI";
        return [
            `"${pt.fullDate || pt.time}"`,
            pt.tempInternal,
            pt.humidityInternal,
            `"${isIdeal}"`,
            `"${heater}"`,
        ].join(",");
    });

    const csvContent =
        "data:text/csv;charset=utf-8,\uFEFF" +
        [headers.join(","), ...rows].join("\n");
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Chart Helpers
function appendTelemetryToHistory(data) {
    if (selectedPeriod.value !== "1d") return;
    if (!data || (!data.isLive && !connectionManager.state.isHardwareActive)) {
        return;
    }
    const now = new Date();
    const timeFormatted = now.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });
    const fullDateFormatted =
        now.toLocaleString("id-ID", {
            dateStyle: "medium",
            timeStyle: "short",
        }) + " WIB";

    const temp = Number(
        data.tempInternal ?? data.temp_internal ?? telemetry.value.tempInternal,
    );
    const hum = Number(
        data.humidityInternal ??
            data.humidity_internal ??
            telemetry.value.humidityInternal,
    );
    const heater = Boolean(
        data.heaterStatus ?? data.heater_status ?? telemetry.value.heaterStatus,
    );

    liveHistoryPoints.value.push({
        time: timeFormatted,
        fullDate: fullDateFormatted,
        tempInternal: Number(temp.toFixed(1)),
        humidityInternal: Number(hum.toFixed(0)),
        heaterStatus: heater,
    });

    if (liveHistoryPoints.value.length > 60) {
        liveHistoryPoints.value.shift();
    }
    computeClientAnalytics(liveHistoryPoints.value, "1d");
}

function seedInitialHistory() {
    liveHistoryPoints.value = [];
}

function mapToSvg(
    values,
    minVal,
    maxVal,
    width = 1000,
    height = 180,
    xOffset = 0,
    yOffset = 20,
) {
    if (!values || values.length < 2) return "";
    const stepX = width / (values.length - 1);
    return values
        .map((val, idx) => {
            const clamped = Math.max(minVal, Math.min(maxVal, val));
            const norm = (clamped - minVal) / (maxVal - minVal);
            const x = Math.round(xOffset + idx * stepX);
            const y = Math.round(yOffset + (1 - norm) * height);
            return `${idx === 0 ? "M" : "L"} ${x} ${y}`;
        })
        .join(" ");
}

const tempCurvePath = computed(() => {
    return mapToSvg(
        liveHistoryPoints.value.map((d) => d.tempInternal),
        20,
        80,
        1000,
        180,
        0,
        20,
    );
});

const humidityCurvePath = computed(() => {
    return mapToSvg(
        liveHistoryPoints.value.map((d) => d.humidityInternal),
        20,
        80,
        1000,
        180,
        0,
        20,
    );
});

const xTimeLabels = computed(() => {
    const pts = liveHistoryPoints.value;
    if (pts.length === 0) return [];
    const count = Math.min(5, pts.length);
    const labels = [];
    const stepIdx = Math.floor((pts.length - 1) / (count - 1 || 1));
    for (let i = 0; i < count; i++) {
        const idx = Math.min(i * stepIdx, pts.length - 1);
        labels.push({ time: pts[idx].time });
    }
    return labels;
});

// Interaktif Hover Tooltip untuk Grafik Suhu & Kelembapan
const hoveredChartIndex = ref(null);
const chartWrapperRef = ref(null);

const chartCoordinates = computed(() => {
    const pts = liveHistoryPoints.value;
    if (!pts || pts.length < 2) return [];
    const width = 1000;
    const height = 180;
    const xOffset = 0;
    const yOffset = 20;
    const minVal = 20;
    const maxVal = 80;
    const stepX = width / (pts.length - 1);

    return pts.map((pt, idx) => {
        const x = Math.round(xOffset + idx * stepX);
        const clampTemp = Math.max(
            minVal,
            Math.min(maxVal, pt.tempInternal ?? 0),
        );
        const normTemp = (clampTemp - minVal) / (maxVal - minVal);
        const yTemp = Math.round(yOffset + (1 - normTemp) * height);

        const clampHum = Math.max(
            minVal,
            Math.min(maxVal, pt.humidityInternal ?? 0),
        );
        const normHum = (clampHum - minVal) / (maxVal - minVal);
        const yHum = Math.round(yOffset + (1 - normHum) * height);

        const percentX = (x / 1000) * 100;

        return {
            ...pt,
            idx,
            x,
            yTemp,
            yHum,
            percentX,
        };
    });
});

const activeHoverPoint = computed(() => {
    if (hoveredChartIndex.value === null) return null;
    return chartCoordinates.value[hoveredChartIndex.value] || null;
});

function handleChartMouseMove(event) {
    if (!chartCoordinates.value.length || !chartWrapperRef.value) return;
    const rect = chartWrapperRef.value.getBoundingClientRect();
    if (!rect.width) return;

    const clientX =
        event.clientX !== undefined
            ? event.clientX
            : event.touches && event.touches[0]
              ? event.touches[0].clientX
              : null;
    if (clientX === null) return;

    const mouseSvgX = ((clientX - rect.left) / rect.width) * 1000;

    let closestIdx = 0;
    let minDist = Infinity;
    chartCoordinates.value.forEach((pt, idx) => {
        const dist = Math.abs(pt.x - mouseSvgX);
        if (dist < minDist) {
            minDist = dist;
            closestIdx = idx;
        }
    });

    hoveredChartIndex.value = closestIdx;
}

function handleChartTouch(event) {
    if (event.touches && event.touches.length > 0) {
        handleChartMouseMove(event.touches[0]);
    }
}

function handleChartMouseLeave() {
    hoveredChartIndex.value = null;
}

function formatAlertTime(dateStr) {
    if (!dateStr) return "Baru saja";
    try {
        const d = new Date(dateStr);
        return (
            d.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
            }) + " WIB"
        );
    } catch {
        return "Baru saja";
    }
}

async function loadDashboardData() {
    try {
        const statsRes = await dashboardService.getStats();
        if (statsRes) {
            activeBatch.value = statsRes.activeBatch || null;
            if (statsRes.currentTelemetry) {
                telemetry.value = {
                    ...telemetry.value,
                    ...statsRes.currentTelemetry,
                };
                if (statsRes.currentTelemetry.isLive) {
                    appendTelemetryToHistory(statsRes.currentTelemetry);
                }
            }
            if (statsRes.actuators) {
                actuators.value = { ...actuators.value, ...statsRes.actuators };
            }
        }
    } catch (err) {
        console.warn("Dashboard stats fallback:", err.message);
    }

    try {
        const res = await telemetryService.getCurrent();
        if (res && res.telemetry) {
            telemetry.value = { ...telemetry.value, ...res.telemetry };
            if (res.telemetry.isLive) {
                appendTelemetryToHistory(res.telemetry);
            }
        }
        if (res && res.actuators) {
            actuators.value = { ...actuators.value, ...res.actuators };
        }
    } catch (err) {
        console.warn("Telemetry fetch fallback:", err.message);
    }

    try {
        const alertsRes = await alertService.getAll();
        const list = Array.isArray(alertsRes)
            ? alertsRes
            : alertsRes?.alerts || [];
        recentAlerts.value = deduplicateAlerts(list).slice(0, 3);
    } catch (err) {
        console.warn("Alerts fetch fallback:", err.message);
    }
}

// Deduplicate alerts so identical issues are never shown twice
function deduplicateAlerts(alerts) {
    if (!Array.isArray(alerts)) return [];
    const seen = new Set();
    const unique = [];
    for (const a of alerts) {
        if (!a) continue;
        const key = (a.title || "").trim().toLowerCase();
        if (key && !seen.has(key)) {
            seen.add(key);
            unique.push(a);
        }
    }
    return unique;
}

let pollTimer = null;

onMounted(async () => {
    seedInitialHistory();
    await loadPeriodHistory(selectedPeriod.value);
    await loadDashboardData();
    isInitialLoading.value = false;

    pollTimer = setInterval(loadDashboardData, 5000);

    connectionManager.on("telemetry_live", (data) => {
        if (data && data.hasData) {
            telemetry.value = { ...telemetry.value, ...data, hasData: true };
            appendTelemetryToHistory(data);
        }
        isConnected.value = true;
    });

    connectionManager.on("actuators_update", (data) => {
        if (data) actuators.value = { ...actuators.value, ...data };
    });

    connectionManager.on("alert_new", (newAlert) => {
        if (!newAlert) return;
        const key = (newAlert.title || "").trim().toLowerCase();
        const filtered = recentAlerts.value.filter(
            (a) => (a.title || "").trim().toLowerCase() !== key,
        );
        recentAlerts.value = [newAlert, ...filtered].slice(0, 3);
    });

    connectionManager.connect();
});

onUnmounted(() => {
    if (pollTimer) clearInterval(pollTimer);
    connectionManager.off("telemetry_live");
    connectionManager.off("actuators_update");
    connectionManager.off("alert_new");
});
</script>

<style scoped>
.dashboard-page {
    width: 100%;
    min-height: 100%;
}

.dashboard-main-content {
    padding: 24px 28px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
    margin: 0;
    box-sizing: border-box;
}

/* 1. Welcome Card & Top Bar */
.dash-welcome-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px 28px;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
}

.welcome-left {
    flex: 1;
    min-width: 280px;
}

.welcome-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 10px;
}

.welcome-status-pill.status-online {
    background: #e8f5e9;
    color: #1b5e20;
}

.welcome-status-pill.status-offline {
    background: #fee2e2;
    color: #dc2626;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.welcome-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f291e;
    margin-bottom: 6px;
    line-height: 1.25;
}

.welcome-desc {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.welcome-right {
    display: flex;
    align-items: center;
}

.btn-action-main {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    padding: 14px 24px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
}

.btn-start-new {
    background: #1b5e20;
    color: #ffffff;
}

.btn-start-new:hover:not(:disabled) {
    background: #144a18;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(27, 94, 32, 0.25);
}

.btn-start-new:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}

.btn-start-new.btn-hardware-offline {
    background: #64748b;
    opacity: 0.92;
}

.btn-start-new.btn-hardware-offline:hover {
    background: #475569;
    box-shadow: 0 4px 12px rgba(71, 85, 105, 0.25);
}

.btn-active-session {
    background: #d97706;
    color: #ffffff;
}

.btn-active-session:hover {
    background: #b45309;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
}

.btn-icon-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-text-wrap {
    display: flex;
    flex-direction: column;
}

.btn-title {
    font-size: 15px;
    font-weight: 700;
}

.btn-sub {
    font-size: 12px;
    opacity: 0.9;
}

/* Offline Banner */
.system-offline-banner {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.offline-text strong {
    display: block;
    font-size: 15px;
    color: #991b1b;
    margin-bottom: 2px;
}

.offline-text p {
    font-size: 13px;
    color: #b91c1c;
    margin: 0;
}

/* Active Batch Banner */
.active-batch-banner {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-wrap: wrap;
}

.active-batch-banner:hover {
    border-color: #86efac;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(27, 94, 32, 0.08);
}

.ab-left {
    flex: 1;
    min-width: 260px;
}

.ab-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #dcfce7;
    color: #166534;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.pulse-ring {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #166534;
}

.ab-crop-name {
    font-size: 20px;
    font-weight: 700;
    color: #0f291e;
    margin-bottom: 6px;
}

.ab-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #475569;
    flex-wrap: wrap;
}

.meta-dot {
    color: #cbd5e1;
}

.text-green {
    color: #1b5e20;
}

.est-note {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
    margin-left: 2px;
}

:global(.dark-theme) .est-note {
    color: #94a3b8;
}

.ab-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.ab-progress-box {
    width: 200px;
}

.ab-progress-text {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #475569;
    margin-bottom: 6px;
}

.ab-progress-bar {
    height: 8px;
    background: #dcfce7;
    border-radius: 9999px;
    overflow: hidden;
}

.ab-progress-fill {
    height: 100%;
    background: #1b5e20;
    border-radius: 9999px;
    transition: width 0.4s ease;
}

.ab-arrow {
    color: #1b5e20;
    font-size: 14px;
    font-weight: 700;
    white-space: nowrap;
}

/* 2. Hero Indicators Grid */
.hero-indicators-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

@media (max-width: 1024px) {
    .hero-indicators-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .hero-indicators-grid {
        grid-template-columns: 1fr;
    }
}

.indicator-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.indicator-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}

.card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.card-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-temp {
    background: #fef3c7;
}

.icon-hum {
    background: #e0f2fe;
}

.icon-weather {
    background: #f1f5f9;
}

.icon-heater-on {
    background: #fef3c7;
    color: #d97706;
}

.icon-heater-off {
    background: #e8f5e9;
    color: #1b5e20;
}

.card-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 6px;
    letter-spacing: 0.3px;
}

.tag-success {
    background: #e8f5e9;
    color: #1b5e20;
}

.tag-warning {
    background: #fef3c7;
    color: #b45309;
}

.tag-danger {
    background: #fee2e2;
    color: #dc2626;
}

.tag-info {
    background: #e0f2fe;
    color: #0369a1;
}

.tag-neutral {
    background: #f1f5f9;
    color: #64748b;
}

.card-title {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    display: block;
    margin-bottom: 4px;
}

.card-number-row {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 8px;
}

.card-big-number {
    font-size: 34px;
    font-weight: 800;
    color: #0f291e;
    line-height: 1;
}

.card-unit {
    font-size: 16px;
    font-weight: 600;
    color: #64748b;
}

.card-text-large {
    font-size: 22px;
    font-weight: 700;
    color: #0f291e;
}

.card-text-large.text-amber {
    color: #d97706;
}

.card-text-large.text-neutral {
    color: #1b5e20;
}

.card-hint {
    font-size: 12px;
    color: #64748b;
    line-height: 1.4;
    margin: 0;
}

.card-heater-btn-box {
    margin-top: 10px;
}

.btn-heater-toggle {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-heater-start {
    background: #e8f5e9;
    color: #1b5e20;
}

.btn-heater-start:hover:not(:disabled) {
    background: #1b5e20;
    color: #ffffff;
}

.btn-heater-stop {
    background: #fef3c7;
    color: #b45309;
}

.btn-heater-stop:hover:not(:disabled) {
    background: #dc2626;
    color: #ffffff;
}

.btn-heater-toggle:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Heater Automation Reason Callout */
.heater-reason-box {
    margin-top: 12px;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    text-align: left;
    transition: all 0.25s ease;
}

.heater-reason-box.status-on {
    background: #fffbeb;
    border-color: #fde68a;
}

.heater-reason-box.status-lockout {
    background: #fef2f2;
    border-color: #fecaca;
}

.hrb-row {
    display: flex;
    align-items: baseline;
    gap: 6px;
    font-size: 12px;
}

.hrb-reason-row {
    align-items: flex-start;
}

.hrb-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    flex-shrink: 0;
    width: 48px;
}

.hrb-status-text {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.03em;
}

.hrb-status-text.text-amber {
    color: #b45309;
}

.hrb-status-text.text-neutral {
    color: #475569;
}

.hrb-status-text.text-rose {
    color: #dc2626;
}

.hrb-reason-text {
    margin: 0;
    font-size: 11.5px;
    line-height: 1.45;
    color: #334155;
    font-weight: 500;
}

:global(.dark-theme) .heater-reason-box {
    background: #0b242f;
    border-color: #1e4e61;
}

:global(.dark-theme) .heater-reason-box.status-on {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.3);
}

:global(.dark-theme) .heater-reason-box.status-lockout {
    background: rgba(220, 38, 38, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
}

:global(.dark-theme) .hrb-label {
    color: #94a3b8;
}

:global(.dark-theme) .hrb-status-text.text-amber {
    color: #fbbf24;
}

:global(.dark-theme) .hrb-status-text.text-neutral {
    color: #94a3b8;
}

:global(.dark-theme) .hrb-status-text.text-rose {
    color: #f87171;
}

:global(.dark-theme) .hrb-reason-text {
    color: #cbd5e1;
}

/* 3. Simple & Analytical Chart Card */
.simple-chart-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.chart-header-left {
    flex: 1;
    min-width: 260px;
}

.chart-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}

.chart-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f291e;
    margin: 0;
}

.period-active-chip {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    background: #e8f5e9;
    color: #1b5e20;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.2px;
}

:global(.dark-theme) .period-active-chip {
    background: rgba(74, 222, 128, 0.15);
    color: #86efac;
}

.chart-subtitle {
    font-size: 13px;
    color: #64748b;
    margin: 0;
}

/* Actions toolbar: Pill buttons & CSV export */
.chart-actions-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.timeframe-pill-selector {
    display: inline-flex;
    align-items: center;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

:global(.dark-theme) .timeframe-pill-selector {
    background: #071e27;
    border-color: #1e4e61;
}

.timeframe-btn {
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.timeframe-btn:hover {
    color: #0f291e;
}

:global(.dark-theme) .timeframe-btn:hover {
    color: #f8fafc;
}

.timeframe-btn.active {
    background: #ffffff;
    color: #1b5e20;
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

:global(.dark-theme) .timeframe-btn.active {
    background: #1b5e20;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.btn-chart-export {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-chart-export:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f291e;
}

:global(.dark-theme) .btn-chart-export {
    background: #0b242f;
    border-color: #1e4e61;
    color: #cbd5e1;
}

:global(.dark-theme) .btn-chart-export:hover {
    background: #113847;
    border-color: #2dd4bf;
    color: #ffffff;
}

/* Ringkasan Metrik Sederhana (Simple & Clean Badges) */
.simple-metric-badges {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 14px;
    padding: 8px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

:global(.dark-theme) .simple-metric-badges {
    background: #071e27;
    border-color: #1e4e61;
}

.metric-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    font-size: 13px;
    color: #334155;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .metric-chip {
    background: #0b242f;
    border-color: #1e4e61;
    color: #e2e8f0;
}

.metric-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.dot-red {
    background: #dc2626;
}
.dot-blue {
    background: #0284c7;
}
.dot-green {
    background: #16a34a;
}
.dot-amber {
    background: #d97706;
}

.metric-name {
    color: #64748b;
    font-size: 12px;
}

:global(.dark-theme) .metric-name {
    color: #94a3b8;
}

.metric-val {
    font-weight: 700;
    font-size: 13px;
}

.metric-range {
    font-size: 11px;
    color: #94a3b8;
    margin-left: 2px;
}

@media (max-width: 900px) {
    .simple-metric-badges {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        padding: 8px;
    }

    .metric-chip {
        padding: 6px 10px;
        font-size: 12px;
    }
}

@media (max-width: 520px) {
    .simple-metric-badges {
        grid-template-columns: 1fr;
    }
}

/* Legend & Hint Row */
.chart-legend-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.chart-hint-text {
    font-size: 11px;
    color: #94a3b8;
    font-style: italic;
}

:global(.dark-theme) .chart-hint-text {
    color: #64748b;
}

.chart-loading-pulse {
    opacity: 0.6;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.chart-legend {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.legend-color {
    width: 12px;
    height: 4px;
    border-radius: 2px;
}

.legend-red {
    background: #dc2626;
}

.legend-blue {
    background: #0284c7;
}

/* Chart Body Layout: Y-Axis + SVG Canvas */
.chart-body-layout {
    display: flex;
    align-items: stretch;
    gap: 12px;
    width: 100%;
    position: relative;
}

.chart-y-axis {
    width: 32px;
    position: relative;
    flex-shrink: 0;
    user-select: none;
}

.y-axis-lbl {
    position: absolute;
    right: 0;
    transform: translateY(-50%);
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    line-height: 1;
}

:global(.dark-theme) .y-axis-lbl {
    color: #64748b;
}

.y-80 {
    top: 8.33%;
}
.y-60 {
    top: 33.33%;
}
.y-40 {
    top: 58.33%;
}
.y-20 {
    top: 83.33%;
}

.simple-svg-wrapper {
    flex: 1;
    position: relative;
    cursor: crosshair;
    user-select: none;
    -webkit-user-select: none;
    touch-action: pan-y;
    height: 270px;
}

.simple-svg {
    width: 100%;
    height: 100%;
    display: block;
}

.ideal-zone-overlay {
    position: absolute;
    top: 52%;
    left: 14px;
    transform: translateY(-50%);
    pointer-events: none;
    z-index: 5;
}

.ideal-zone-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    background: rgba(27, 94, 32, 0.12);
    border: 1px solid rgba(27, 94, 32, 0.22);
    color: #1b5e20;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.2px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

:global(.dark-theme) .ideal-zone-pill {
    background: rgba(16, 185, 129, 0.18);
    border-color: rgba(16, 185, 129, 0.35);
    color: #6ee7b7;
}

.simple-chart-time-row {
    display: flex;
    justify-content: space-between;
    margin-left: 44px;
    padding: 10px 4px 0 4px;
    font-size: 11px;
    color: #94a3b8;
    font-weight: 600;
}

:global(.dark-theme) .chart-grid-line {
    stroke: rgba(255, 255, 255, 0.08);
}

:global(.dark-theme) .chart-baseline {
    stroke: rgba(255, 255, 255, 0.18);
}

/* Floating Tooltip Bubble on Hover */
.chart-tooltip-bubble {
    position: absolute;
    top: 6px;
    pointer-events: none;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 9px 13px;
    box-shadow:
        0 10px 25px -4px rgba(0, 0, 0, 0.12),
        0 4px 8px -2px rgba(0, 0, 0, 0.05);
    z-index: 25;
    min-width: 180px;
    transition:
        left 0.08s ease-out,
        transform 0.15s ease-out;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

:global(.dark-theme) .chart-tooltip-bubble {
    background: rgba(14, 52, 67, 0.95);
    border-color: #1e4e61;
    box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.5);
}

.tooltip-time-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    padding-bottom: 4px;
    border-bottom: 1px solid #e2e8f0;
}

:global(.dark-theme) .tooltip-time-row {
    color: #94a3b8;
    border-bottom-color: rgba(255, 255, 255, 0.1);
}

.tooltip-metric-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 12px;
}

.tooltip-metric-left {
    display: flex;
    align-items: center;
    gap: 6px;
}

.tooltip-metric-left .metric-title {
    color: #475569;
    font-weight: 600;
}

:global(.dark-theme) .tooltip-metric-left .metric-title {
    color: #cbd5e1;
}

.metric-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.tooltip-metric-right {
    display: flex;
    align-items: center;
    gap: 6px;
}

.metric-value {
    font-size: 13px;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
}

.text-red {
    color: #dc2626;
}

.text-blue {
    color: #0284c7;
}

:global(.dark-theme) .text-red {
    color: #f87171;
}

:global(.dark-theme) .text-blue {
    color: #38bdf8;
}

.metric-sub {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
}

:global(.dark-theme) .metric-sub {
    color: #94a3b8;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.3;
}

.chip-ideal {
    background: #dcfce7;
    color: #166534;
}

:global(.dark-theme) .chip-ideal {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
}

.chip-low {
    background: #fef3c7;
    color: #b45309;
}

:global(.dark-theme) .chip-low {
    background: rgba(245, 158, 11, 0.2);
    color: #fcd34d;
}

.chip-high {
    background: #fee2e2;
    color: #991b1b;
}

:global(.dark-theme) .chip-high {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}

.chart-hover-elements {
    pointer-events: none;
}

/* Tooltip transition */
.fade-tooltip-enter-active,
.fade-tooltip-leave-active {
    transition:
        opacity 0.15s ease,
        transform 0.15s ease;
}

.fade-tooltip-enter-from,
.fade-tooltip-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

/* 4. Simple Alerts Card */
.alerts-simple-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.alerts-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f291e;
    margin-bottom: 14px;
}

.alerts-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
}

.alert-danger {
    background: #fef2f2;
    color: #991b1b;
}

.alert-warning {
    background: #fffbeb;
    color: #92400e;
}

.alert-info {
    background: #f0fdf4;
    color: #166534;
}

.alert-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.alert-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.alert-heading {
    font-weight: 700;
}

.alert-msg {
    font-size: 12px;
    opacity: 0.9;
}

.alert-time {
    font-size: 11px;
    opacity: 0.7;
    white-space: nowrap;
}

/* Responsive adjustments */
@media (max-width: 900px) {
    .dashboard-main-content {
        padding: 0;
        gap: 12px;
    }

    .dash-welcome-card {
        padding: 16px;
        border-radius: 14px;
    }

    .welcome-title {
        font-size: 19px;
    }

    .hero-indicators-grid {
        gap: 10px;
    }

    .indicator-card {
        padding: 14px 16px;
        border-radius: 14px;
    }

    .btn-action-main {
        width: 100%;
        justify-content: center;
    }

    .active-batch-banner {
        padding: 14px 16px;
        border-radius: 14px;
    }

    .simple-chart-card,
    .alerts-simple-card {
        padding: 16px;
        border-radius: 14px;
    }

    .ab-right {
        width: 100%;
        justify-content: space-between;
    }

    .ab-progress-box {
        width: 100%;
    }

    .simple-svg-wrapper {
        height: 200px;
    }

    .ideal-zone-pill {
        font-size: 10px;
        padding: 2px 7px;
    }
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR OPERATOR DASHBOARD
   ========================================================================== */
:global(.dark-theme) .dash-welcome-card,
:global(.dark-theme) .indicator-card,
:global(.dark-theme) .simple-chart-card,
:global(.dark-theme) .alerts-simple-card {
    background: #0b242f;
    border-color: #1e4e61;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

:global(.dark-theme) .welcome-title,
:global(.dark-theme) .card-big-number,
:global(.dark-theme) .card-text-large,
:global(.dark-theme) .chart-title,
:global(.dark-theme) .alerts-title,
:global(.dark-theme) .ab-crop-name {
    color: #f8fafc;
}

:global(.dark-theme) .welcome-desc,
:global(.dark-theme) .card-title,
:global(.dark-theme) .card-unit,
:global(.dark-theme) .card-hint,
:global(.dark-theme) .chart-subtitle,
:global(.dark-theme) .ab-meta-row,
:global(.dark-theme) .ab-progress-text,
:global(.dark-theme) .simple-chart-time-row {
    color: #94a3b8;
}

:global(.dark-theme) .chart-legend {
    color: #cbd5e1;
}

:global(.dark-theme) .active-batch-banner {
    background: #071e27;
    border-color: #166534;
}

:global(.dark-theme) .ab-badge {
    background: rgba(22, 101, 52, 0.35);
    color: #86efac;
}

:global(.dark-theme) .pulse-ring {
    background: #4ade80;
}

:global(.dark-theme) .ab-progress-bar {
    background: #113847;
}

:global(.dark-theme) .ab-arrow {
    color: #4ade80;
}

:global(.dark-theme) .icon-temp {
    background: rgba(217, 119, 6, 0.18);
}

:global(.dark-theme) .icon-hum {
    background: rgba(2, 132, 199, 0.18);
}

:global(.dark-theme) .icon-weather {
    background: rgba(148, 163, 184, 0.18);
}

:global(.dark-theme) .icon-heater-on {
    background: rgba(217, 119, 6, 0.25);
    color: #fbbf24;
}

:global(.dark-theme) .icon-heater-off {
    background: rgba(27, 94, 32, 0.25);
    color: #4ade80;
}

:global(.dark-theme) .btn-heater-start {
    background: rgba(27, 94, 32, 0.25);
    color: #4ade80;
    border: 1px solid rgba(74, 222, 128, 0.3);
}

:global(.dark-theme) .btn-heater-start:hover:not(:disabled) {
    background: #1b5e20;
    color: #ffffff;
}

:global(.dark-theme) .btn-heater-stop {
    background: rgba(217, 119, 6, 0.25);
    color: #fbbf24;
    border: 1px solid rgba(251, 191, 36, 0.3);
}

:global(.dark-theme) .tag-neutral {
    background: #113847;
    color: #cbd5e1;
}

:global(.dark-theme) .tag-success {
    background: rgba(27, 94, 32, 0.25);
    color: #4ade80;
}

:global(.dark-theme) .tag-warning {
    background: rgba(217, 119, 6, 0.25);
    color: #fbbf24;
}

:global(.dark-theme) .tag-danger {
    background: rgba(220, 38, 38, 0.25);
    color: #f87171;
}

:global(.dark-theme) .tag-info {
    background: rgba(2, 132, 199, 0.25);
    color: #38bdf8;
}

:global(.dark-theme) .alert-item {
    border: 1px solid transparent;
}

:global(.dark-theme) .alert-danger {
    background: rgba(220, 38, 38, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.3) !important;
    color: #fca5a5 !important;
}

:global(.dark-theme) .alert-warning {
    background: rgba(217, 119, 6, 0.15) !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
    color: #fde68a !important;
}

:global(.dark-theme) .alert-info {
    background: rgba(22, 101, 52, 0.25) !important;
    border-color: rgba(34, 197, 94, 0.25) !important;
    color: #86efac !important;
}

:global(.dark-theme) .alert-heading {
    color: #f8fafc !important;
}

:global(.dark-theme) .alert-msg {
    color: #cbd5e1 !important;
}

:global(.dark-theme) .alert-time {
    color: #94a3b8 !important;
}

:global(.dark-theme) .card-text-large.text-neutral {
    color: #4ade80;
}

/* SVG Chart Elements in Dark Mode */
:global(.dark-theme) .simple-svg line[stroke="#E2E8F0"] {
    stroke: #1e4e61;
}

:global(.dark-theme) .simple-svg text[fill="#94A3B8"] {
    fill: #64748b;
}

:global(.dark-theme) .simple-svg rect[fill="#DCFCE7"] {
    fill: rgba(22, 101, 52, 0.25);
}

:global(.dark-theme) .simple-svg text[fill="#166534"] {
    fill: #86efac;
}
</style>
