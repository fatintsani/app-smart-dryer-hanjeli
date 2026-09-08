<template>
  <div class="monitoring-page">
    <!-- Desktop & Tablet Layout -->
    <div class="monitoring-main-container">
      <!-- Header Area -->
      <div class="header-action-row">
        <div class="title-group">
          <div class="title-with-badge">
            <h1 class="page-title">{{ $t('monitoring.title') || 'Live Telemetri & Analitik Ruang Pengering' }}</h1>
          </div>
          <p class="page-subtitle text-live">
            <span class="live-dot" :class="{ 'live-dot-active': isDataActive }"></span> 
            <span>{{ isDataActive ? 'Aliran Data Real-time Sensor ESP32 Terhubung' : 'Standby — Menunggu Aliran Sensor ESP32' }}</span>
          </p>
        </div>

        <div class="header-right-actions">
          <button class="btn-export-csv" @click="exportTelemetryCsv" title="Unduh data riwayat sensor dalam format CSV">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Ekspor CSV</span>
          </button>

          <div class="system-active-badge" :class="isDataActive ? 'badge-online' : 'badge-standby'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polyline points="23 4 23 10 17 10"></polyline>
              <polyline points="1 20 1 14 7 14"></polyline>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            <span>Status: <strong>{{ isDataActive ? 'Live Streaming' : 'Standby' }}</strong></span>
          </div>
        </div>
      </div>

      <!-- Dual Connectivity Status & Control Card -->
      <ConnectionStatusCard @open-wifi-setup="isWifiSetupOpen = true" />

      <!-- 4 Top KPI Cards Grid -->
      <div class="stats-grid-4">
        <!-- 1. Suhu Ruangan -->
        <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
          <div class="stat-card-header">
            <span class="stat-label">SUHU INTERNAL RUANG</span>
            <div class="icon-circle icon-red-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2.2">
                <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number">{{ isDataActive ? telemetry.tempInternal.toFixed(1) : '--' }}</span>
            <span class="stat-unit">°C</span>
          </div>
          <div class="stat-trend" :class="isDataActive ? 'trend-up-green' : 'text-muted'">
            <span class="delta-chip">ΔT: {{ isDataActive ? (telemetry.tempInternal - telemetry.tempExternal).toFixed(1) : '--' }}°C</span>
            <span>Luar: {{ isDataActive ? telemetry.tempExternal.toFixed(1) + '°C' : '--' }}</span>
          </div>
        </div>

        <!-- 2. Kelembapan -->
        <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
          <div class="stat-card-header">
            <span class="stat-label">KELEMBAPAN RUANGAN</span>
            <div class="icon-circle icon-blue-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number">{{ isDataActive ? telemetry.humidityInternal.toFixed(0) : '--' }}</span>
            <span class="stat-unit">% RH</span>
          </div>
          <div class="stat-trend" :class="isDataActive ? 'trend-down-blue' : 'text-muted'">
            <span class="delta-chip-blue">Luar: {{ isDataActive ? telemetry.humidityExternal.toFixed(0) + '%' : '--' }}</span>
            <span>Target: &lt; 50%</span>
          </div>
        </div>

        <!-- 3. Kondisi Cahaya & Radiasi -->
        <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
          <div class="stat-card-header">
            <span class="stat-label">RADIASI SURYA MATAHARI</span>
            <div class="icon-circle icon-amber-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
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
            <span class="stat-number">{{ isDataActive ? telemetry.solarRadiation.toFixed(0) : '--' }}</span>
            <span class="stat-unit">W/m²</span>
          </div>
          <div class="stat-trend" :class="isDataActive ? 'trend-up-amber' : 'text-muted'">
            <span class="delta-chip-amber">
              <svg v-if="telemetry.solarRadiation > 600" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display:inline-block; vertical-align:middle; margin-right:3px;">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
              </svg>
              <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display:inline-block; vertical-align:middle; margin-right:3px;">
                <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
              </svg>
              <span>{{ telemetry.solarRadiation > 600 ? 'Terik Alami' : 'Redup / Malam' }}</span>
            </span>
            <span>Greenhouse Gain</span>
          </div>
        </div>

        <!-- 4. Kadar Air & Bobot Hanjeli -->
        <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
          <div class="stat-card-header">
            <span class="stat-label">KADAR AIR GABAH HANJELI</span>
            <div class="icon-circle icon-purple-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E22CE" stroke-width="2.2">
                <circle cx="12" cy="12" r="9"></circle>
                <polyline points="12 7 12 12 15 15"></polyline>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number">{{ isDataActive ? telemetry.grainMoisture.toFixed(1) : '--' }}</span>
            <span class="stat-unit">%</span>
          </div>
          <div class="stat-trend">
            <span class="delta-chip-purple">Target: 12.0%</span>
            <span>Bobot: {{ isDataActive ? telemetry.weightCurrentKg.toFixed(1) + ' kg' : '--' }}</span>
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
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
            </div>
            <div>
              <h2 class="master-chart-heading">Grafik Analisis Multi-Sensor (Dual-Axis Waveform)</h2>
              <p class="master-chart-sub">Korelasi simultan Suhu Ruang, Suhu Luar, Kelembapan, Kadar Air, dan Radiasi Surya</p>
            </div>
          </div>

          <!-- Time Window Filter Buttons -->
          <div class="time-window-selector">
            <button 
              type="button" 
              class="time-btn" 
              :class="{ active: timeWindow === 20 && chartMode === 'live' }"
              @click="setTimeWindow(20, 'live')"
            >
              Live 20 Titik
            </button>
            <button 
              type="button" 
              class="time-btn" 
              :class="{ active: timeWindow === 40 && chartMode === 'live' }"
              @click="setTimeWindow(40, 'live')"
            >
              Live 40 Titik
            </button>
            <button 
              type="button" 
              class="time-btn" 
              :class="{ active: chartMode === 'history' }"
              @click="setTimeWindow(24, 'history')"
            >
              Riwayat 24 Jam (DB)
            </button>
          </div>
        </div>

        <!-- Interactive Series Toggles (Legend Checkboxes) -->
        <div class="series-toggle-toolbar">
          <span class="series-lbl">Tampilkan Parameter:</span>
          
          <button 
            type="button" 
            class="series-toggle-pill" 
            :class="{ active: seriesVisible.tempInternal }"
            @click="seriesVisible.tempInternal = !seriesVisible.tempInternal"
          >
            <span class="series-box-dot bg-red"></span>
            <span>Suhu Internal (°C)</span>
          </button>

          <button 
            type="button" 
            class="series-toggle-pill" 
            :class="{ active: seriesVisible.tempExternal }"
            @click="seriesVisible.tempExternal = !seriesVisible.tempExternal"
          >
            <span class="series-box-dot bg-sky"></span>
            <span>Suhu Eksternal (°C)</span>
          </button>

          <button 
            type="button" 
            class="series-toggle-pill" 
            :class="{ active: seriesVisible.humidityInternal }"
            @click="seriesVisible.humidityInternal = !seriesVisible.humidityInternal"
          >
            <span class="series-box-dot bg-blue"></span>
            <span>Kelembapan Udara (% RH)</span>
          </button>

          <button 
            type="button" 
            class="series-toggle-pill" 
            :class="{ active: seriesVisible.grainMoisture }"
            @click="seriesVisible.grainMoisture = !seriesVisible.grainMoisture"
          >
            <span class="series-box-dot bg-purple"></span>
            <span>Kadar Air Gabah (%)</span>
          </button>

          <button 
            type="button" 
            class="series-toggle-pill" 
            :class="{ active: seriesVisible.solarRadiation }"
            @click="seriesVisible.solarRadiation = !seriesVisible.solarRadiation"
          >
            <span class="series-box-dot bg-amber"></span>
            <span>Radiasi Surya (W/m²)</span>
          </button>

          <button 
            type="button" 
            class="series-toggle-pill" 
            :class="{ active: seriesVisible.safetyZone }"
            @click="seriesVisible.safetyZone = !seriesVisible.safetyZone"
          >
            <span class="series-box-dot bg-green"></span>
            <span>Zona Suhu Ideal (40-50°C)</span>
          </button>
        </div>

        <!-- Master SVG Dynamic Graph Canvas -->
        <div class="svg-canvas-wrapper" @mousemove="handleChartHover" @mouseleave="hoveredIndex = null">
          <svg viewBox="0 0 1000 360" class="master-svg-chart" preserveAspectRatio="none">
            <defs>
              <!-- Gradients -->
              <linearGradient id="masterTempGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#BA1A1A" stop-opacity="0.32"/>
                <stop offset="100%" stop-color="#BA1A1A" stop-opacity="0.0"/>
              </linearGradient>
              <linearGradient id="masterHumGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#005DB7" stop-opacity="0.22"/>
                <stop offset="100%" stop-color="#005DB7" stop-opacity="0.0"/>
              </linearGradient>
              <linearGradient id="masterSolarGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#D97706" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#D97706" stop-opacity="0.02"/>
              </linearGradient>
              <linearGradient id="masterSafetyGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10B981" stop-opacity="0.12"/>
                <stop offset="100%" stop-color="#10B981" stop-opacity="0.06"/>
              </linearGradient>
            </defs>

            <!-- 1. Background Grid Guidelines & Axis Labels -->
            <!-- Left Y-Axis Grid (Suhu 0 - 70°C) -->
            <line x1="50" y1="50" x2="950" y2="50" stroke="rgba(148, 163, 184, 0.25)" stroke-dasharray="3 3"/>
            <text x="42" y="54" font-size="10" fill="#94A3B8" text-anchor="end">60°C</text>
            <text x="958" y="54" font-size="10" fill="#94A3B8" text-anchor="start">85%</text>

            <line x1="50" y1="120" x2="950" y2="120" stroke="rgba(148, 163, 184, 0.25)" stroke-dasharray="3 3"/>
            <text x="42" y="124" font-size="10" fill="#94A3B8" text-anchor="end">50°C</text>
            <text x="958" y="124" font-size="10" fill="#94A3B8" text-anchor="start">70%</text>

            <line x1="50" y1="190" x2="950" y2="190" stroke="rgba(148, 163, 184, 0.25)" stroke-dasharray="3 3"/>
            <text x="42" y="194" font-size="10" fill="#94A3B8" text-anchor="end">40°C</text>
            <text x="958" y="194" font-size="10" fill="#94A3B8" text-anchor="start">50%</text>

            <line x1="50" y1="260" x2="950" y2="260" stroke="rgba(148, 163, 184, 0.25)" stroke-dasharray="3 3"/>
            <text x="42" y="264" font-size="10" fill="#94A3B8" text-anchor="end">30°C</text>
            <text x="958" y="264" font-size="10" fill="#94A3B8" text-anchor="start">30%</text>

            <line x1="50" y1="310" x2="950" y2="310" stroke="rgba(148, 163, 184, 0.6)" stroke-width="1.2"/>
            <text x="42" y="314" font-size="10" fill="#94A3B8" text-anchor="end">20°C</text>
            <text x="958" y="314" font-size="10" fill="#94A3B8" text-anchor="start">10%</text>

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
            <text v-if="seriesVisible.safetyZone" x="55" y="132" font-size="9" fill="#047857" font-weight="700">
              ZONA PENGERINGAN OPTIMAL (40°C - 50°C)
            </text>

            <!-- 3. Overheating Critical Line (55°C) -->
            <line x1="50" y1="85" x2="950" y2="85" stroke="#BA1A1A" stroke-width="1.5" stroke-dasharray="5 5" opacity="0.6"/>
            <text x="945" y="80" font-size="8.5" fill="#BA1A1A" font-weight="700" text-anchor="end">
              BATAS MAKSIMAL (55°C)
            </text>

            <!-- 4. Target Moisture Line (12.0%) -->
            <line v-if="seriesVisible.grainMoisture" x1="50" y1="295" x2="950" y2="295" stroke="#7E22CE" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7"/>
            <text v-if="seriesVisible.grainMoisture" x="945" y="290" font-size="8.5" fill="#7E22CE" font-weight="700" text-anchor="end">
              TARGET KADAR AIR (12.0%)
            </text>

            <!-- 5. Solar Radiation Area (Background) -->
            <path 
              v-if="seriesVisible.solarRadiation && activeDataset.length >= 2"
              :d="masterSolarAreaPath" 
              fill="url(#masterSolarGrad)" 
            />
            <path 
              v-if="seriesVisible.solarRadiation && activeDataset.length >= 2"
              :d="masterSolarLinePath" 
              fill="none" 
              stroke="#D97706" 
              stroke-width="1.8" 
              opacity="0.8"
            />

            <!-- 6. Humidity Line & Area -->
            <path 
              v-if="seriesVisible.humidityInternal && activeDataset.length >= 2"
              :d="masterHumAreaPath" 
              fill="url(#masterHumGrad)" 
            />
            <path 
              v-if="seriesVisible.humidityInternal && activeDataset.length >= 2"
              :d="masterHumLinePath" 
              fill="none" 
              stroke="#005DB7" 
              stroke-width="2.2" 
            />

            <!-- 7. Grain Moisture Line -->
            <path 
              v-if="seriesVisible.grainMoisture && activeDataset.length >= 2"
              :d="masterMoistureLinePath" 
              fill="none" 
              stroke="#7E22CE" 
              stroke-width="2.5" 
            />

            <!-- 8. External Temperature Line (Dashed Sky Blue) -->
            <path 
              v-if="seriesVisible.tempExternal && activeDataset.length >= 2"
              :d="masterTempExtLinePath" 
              fill="none" 
              stroke="#0284C7" 
              stroke-width="2" 
              stroke-dasharray="5 4" 
            />

            <!-- 9. Internal Temperature (Master Red Line & Glow) -->
            <path 
              v-if="seriesVisible.tempInternal && activeDataset.length >= 2"
              :d="masterTempIntAreaPath" 
              fill="url(#masterTempGrad)" 
            />
            <path 
              v-if="seriesVisible.tempInternal && activeDataset.length >= 2"
              :d="masterTempIntLinePath" 
              fill="none" 
              stroke="#BA1A1A" 
              stroke-width="3" 
              stroke-linecap="round"
            />

            <!-- 10. Data Point Nodes & Head Pulsing -->
            <g v-if="activeDataset.length >= 2">
              <g v-for="(pt, idx) in activeDataset" :key="'mpt-' + idx">
                <!-- Internal Temp Point -->
                <circle 
                  v-if="seriesVisible.tempInternal"
                  :cx="pt.x" 
                  :cy="pt.yTempInt" 
                  :r="idx === activeDataset.length - 1 ? 5 : (hoveredIndex === idx ? 6 : 2.5)" 
                  :fill="idx === activeDataset.length - 1 ? '#BA1A1A' : '#FFFFFF'" 
                  stroke="#BA1A1A" 
                  :stroke-width="hoveredIndex === idx ? 2.5 : 1.5" 
                />
                <!-- Moisture Point -->
                <circle 
                  v-if="seriesVisible.grainMoisture"
                  :cx="pt.x" 
                  :cy="pt.yMoist" 
                  :r="idx === activeDataset.length - 1 ? 4 : (hoveredIndex === idx ? 5 : 2)" 
                  fill="#7E22CE" 
                  stroke="#FFFFFF" 
                  stroke-width="1.2" 
                />
              </g>

              <!-- Pulsing Ring on Latest Point -->
              <circle 
                v-if="seriesVisible.tempInternal"
                :cx="activeDataset[activeDataset.length - 1].x" 
                :cy="activeDataset[activeDataset.length - 1].yTempInt" 
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
            <text v-if="activeDataset.length < 2" x="500" y="180" font-size="14" fill="#94A3B8" text-anchor="middle">
              {{ isDataActive ? 'Mengumpulkan titik telemetri live...' : 'Menunggu aliran sensor ESP32 terhubung...' }}
            </text>
          </svg>

          <!-- Interactive Hover Tooltip Card -->
          <div 
            v-if="hoveredPoint" 
            class="interactive-tooltip" 
            :style="{ left: hoveredTooltipLeft + 'px', top: '15px' }"
          >
            <div class="tooltip-header">
              <span class="tooltip-time">{{ hoveredPoint.fullTime }}</span>
              <span class="tooltip-tag">Data Point #{{ hoveredIndex + 1 }}</span>
            </div>
            <div class="tooltip-grid">
              <div class="tooltip-row"><span class="t-dot bg-red"></span><span class="t-label">Suhu Internal:</span> <strong>{{ hoveredPoint.tempInternal.toFixed(1) }}°C</strong></div>
              <div class="tooltip-row"><span class="t-dot bg-sky"></span><span class="t-label">Suhu Luar:</span> <strong>{{ hoveredPoint.tempExternal.toFixed(1) }}°C</strong></div>
              <div class="tooltip-row"><span class="t-dot bg-blue"></span><span class="t-label">Kelembapan:</span> <strong>{{ hoveredPoint.humidityInternal.toFixed(0) }}% RH</strong></div>
              <div class="tooltip-row"><span class="t-dot bg-purple"></span><span class="t-label">Kadar Air:</span> <strong>{{ hoveredPoint.grainMoisture.toFixed(1) }}%</strong></div>
              <div class="tooltip-row"><span class="t-dot bg-amber"></span><span class="t-label">Radiasi Surya:</span> <strong>{{ hoveredPoint.solarRadiation.toFixed(0) }} W/m²</strong></div>
              <div class="tooltip-row"><span class="t-dot bg-green"></span><span class="t-label">Delta Panas (ΔT):</span> <strong>+{{ (hoveredPoint.tempInternal - hoveredPoint.tempExternal).toFixed(1) }}°C</strong></div>
            </div>
          </div>
        </div>

        <!-- Master Chart Bottom Analytics Summary Strip -->
        <div class="master-analytics-strip">
          <div class="analytic-item">
            <span class="an-lbl">Suhu Tertinggi (Max)</span>
            <strong class="an-val text-red">{{ masterStats.maxTemp }}°C</strong>
          </div>
          <div class="analytic-item">
            <span class="an-lbl">Rata-rata Suhu Ruang</span>
            <strong class="an-val">{{ masterStats.avgTemp }}°C</strong>
          </div>
          <div class="analytic-item">
            <span class="an-lbl">Delta Panas Maks (ΔT)</span>
            <strong class="an-val text-green-dark">+{{ masterStats.maxDelta }}°C</strong>
          </div>
          <div class="analytic-item">
            <span class="an-lbl">Kelembapan Rata-rata</span>
            <strong class="an-val text-blue">{{ masterStats.avgHum }}% RH</strong>
          </div>
          <div class="analytic-item">
            <span class="an-lbl">Laju Dehidrasi Gabah</span>
            <strong class="an-val text-purple">{{ masterStats.dryingRate }}%/jam</strong>
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E22CE" stroke-width="2.2">
                  <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="sub-heading">Kinetika Dehidrasi Gabah Hanjeli</h3>
                <p class="sub-desc">Progres penurunan kadar air menuju target standar aman 12.0%</p>
              </div>
            </div>
            <span class="moisture-badge">Target: 12.0%</span>
          </div>

          <div class="sub-svg-box">
            <svg viewBox="0 0 450 160" class="sub-svg" preserveAspectRatio="none">
              <!-- Grid -->
              <line x1="20" y1="30" x2="430" y2="30" stroke="rgba(148, 163, 184, 0.2)" stroke-dasharray="3 3"/>
              <line x1="20" y1="75" x2="430" y2="75" stroke="rgba(148, 163, 184, 0.2)" stroke-dasharray="3 3"/>
              <line x1="20" y1="120" x2="430" y2="120" stroke="rgba(148, 163, 184, 0.2)" stroke-dasharray="3 3"/>
              <line x1="20" y1="140" x2="430" y2="140" stroke="rgba(148, 163, 184, 0.5)"/>

              <!-- Target 12% Line -->
              <line x1="20" y1="110" x2="430" y2="110" stroke="#7E22CE" stroke-dasharray="4 4" stroke-width="1.2"/>

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
            </svg>
          </div>

          <div class="sub-chart-footer">
            <div class="sc-stat">
              <span class="sc-lbl">Kadar Air Saat Ini:</span>
              <strong class="text-purple">{{ isDataActive ? telemetry.grainMoisture.toFixed(1) + '%' : '--' }}</strong>
            </div>
            <div class="sc-stat">
              <span class="sc-lbl">Sisa Pengurangan:</span>
              <strong>{{ isDataActive ? Math.max(0, telemetry.grainMoisture - 12.0).toFixed(1) + '%' : '--' }}</strong>
            </div>
          </div>
        </div>

        <!-- Sub-Chart 2: Efisiensi Termal & Retensi Panas (Delta T) -->
        <div class="sub-chart-card">
          <div class="sub-chart-header">
            <div class="sub-title-wrap">
              <div class="sub-icon icon-amber-subtle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                  <path d="M12 2v20M17 5H7M19 12H5M17 19H7"/>
                </svg>
              </div>
              <div>
                <h3 class="sub-heading">Efisiensi Retensi Panas Greenhouse (ΔT)</h3>
                <p class="sub-desc">Peningkatan suhu internal di atas suhu lingkungan luar</p>
              </div>
            </div>
            <span class="thermal-badge">Greenhouse Effect</span>
          </div>

          <div class="sub-svg-box">
            <svg viewBox="0 0 450 160" class="sub-svg" preserveAspectRatio="none">
              <line x1="20" y1="30" x2="430" y2="30" stroke="rgba(148, 163, 184, 0.2)" stroke-dasharray="3 3"/>
              <line x1="20" y1="75" x2="430" y2="75" stroke="rgba(148, 163, 184, 0.2)" stroke-dasharray="3 3"/>
              <line x1="20" y1="120" x2="430" y2="120" stroke="rgba(148, 163, 184, 0.2)" stroke-dasharray="3 3"/>
              <line x1="20" y1="140" x2="430" y2="140" stroke="rgba(148, 163, 184, 0.5)"/>

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
            </svg>
          </div>

          <div class="sub-chart-footer">
            <div class="sc-stat">
              <span class="sc-lbl">ΔT Saat Ini:</span>
              <strong class="text-green-dark">+{{ isDataActive ? (telemetry.tempInternal - telemetry.tempExternal).toFixed(1) + '°C' : '--' }}</strong>
            </div>
            <div class="sc-stat">
              <span class="sc-lbl">Status Pemanas Bantu:</span>
              <strong>{{ actuators.auxHeaterStatus ? 'AKTIF (' + actuators.auxHeaterLevel + '%)' : 'STANDBY' }}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Wi-Fi & MQTT Setup Modal via Bluetooth BLE -->
    <DeviceSetupBleModal :is-open="isWifiSetupOpen" @close="isWifiSetupOpen = false" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { telemetryService } from '../../services/telemetryService';
import { socketService } from '../../services/socketService';
import connectionManager from '../../services/connectionManager';
import ConnectionStatusCard from '../../components/ConnectionStatusCard.vue';
import DeviceSetupBleModal from '../../components/DeviceSetupBleModal.vue';

const isWifiSetupOpen = ref(false);

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
  controlMode: 'AUTOMATIC',
});

// History & Live Dataset Stream
const historyPoints = ref([]);
const livePoints = ref([]);
const chartMode = ref('live'); // 'live' | 'history'
const timeWindow = ref(20);

// Interactive Hover State
const hoveredIndex = ref(null);
const hoveredTooltipLeft = ref(0);

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
  if (connectionManager.state.sourceMode === 'simulation') return true;
  return connectionManager.state.isHardwareActive && telemetry.value.hasData;
});

const setTimeWindow = (count, mode) => {
  chartMode.value = mode;
  timeWindow.value = count;
  if (mode === 'history' && historyPoints.value.length === 0) {
    loadHistoryData();
  }
};

// Normalized Dynamic Master Dataset mapped to 1000 x 360 SVG dimensions
const activeDataset = computed(() => {
  const raw = chartMode.value === 'live' 
    ? livePoints.value.slice(-timeWindow.value) 
    : historyPoints.value;

  if (raw.length < 2) return [];

  const count = raw.length;
  const startX = 60;
  const endX = 940;
  const stepX = (endX - startX) / (count - 1);

  // Scalers:
  // Temp: 20°C (y=310) to 70°C (y=20)
  // Hum & Moisture: 0% (y=310) to 100% (y=20)
  // Solar: 0 (y=310) to 1200 W/m2 (y=180)
  return raw.map((pt, idx) => {
    const x = startX + idx * stepX;
    const tempInt = pt.tempInternal ?? pt.temp_internal ?? 0;
    const tempExt = pt.tempExternal ?? pt.temp_external ?? 28;
    const humInt = pt.humidityInternal ?? pt.humidity_internal ?? 0;
    const moist = pt.grainMoisture ?? pt.grain_moisture ?? 14;
    const solar = pt.solarRadiation ?? pt.solar_radiation ?? 0;

    // Y mappings
    const yTempInt = 310 - Math.max(0, Math.min(1, (tempInt - 20) / 50)) * 260;
    const yTempExt = 310 - Math.max(0, Math.min(1, (tempExt - 20) / 50)) * 260;
    const yHumInt = 310 - Math.max(0, Math.min(1, humInt / 100)) * 260;
    const yMoist = 310 - Math.max(0, Math.min(1, moist / 100)) * 260;
    const ySolar = 310 - Math.max(0, Math.min(1, solar / 1200)) * 130;

    const d = new Date(pt.timestamp || pt.recorded_at || Date.now());
    const timeLabel = pt.label || d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const fullTime = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

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

  // Find nearest point
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
  hoveredTooltipLeft.value = Math.max(20, Math.min(rect.width - 220, mouseX - 100));
};

// SVG Path Helpers
const masterTempIntLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return pts.reduce((acc, p, idx) => idx === 0 ? `M ${p.x} ${p.yTempInt}` : `${acc} L ${p.x} ${p.yTempInt}`, '');
});

const masterTempIntAreaPath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return `${masterTempIntLinePath.value} L ${pts[pts.length - 1].x} 310 L ${pts[0].x} 310 Z`;
});

const masterTempExtLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return pts.reduce((acc, p, idx) => idx === 0 ? `M ${p.x} ${p.yTempExt}` : `${acc} L ${p.x} ${p.yTempExt}`, '');
});

const masterHumLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return pts.reduce((acc, p, idx) => idx === 0 ? `M ${p.x} ${p.yHumInt}` : `${acc} L ${p.x} ${p.yHumInt}`, '');
});

const masterHumAreaPath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return `${masterHumLinePath.value} L ${pts[pts.length - 1].x} 310 L ${pts[0].x} 310 Z`;
});

const masterMoistureLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return pts.reduce((acc, p, idx) => idx === 0 ? `M ${p.x} ${p.yMoist}` : `${acc} L ${p.x} ${p.yMoist}`, '');
});

const masterSolarLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return pts.reduce((acc, p, idx) => idx === 0 ? `M ${p.x} ${p.ySolar}` : `${acc} L ${p.x} ${p.ySolar}`, '');
});

const masterSolarAreaPath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return `${masterSolarLinePath.value} L ${pts[pts.length - 1].x} 310 L ${pts[0].x} 310 Z`;
});

// Sub-chart 1 (Moisture)
const moistureSubLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  const startX = 30;
  const endX = 420;
  const stepX = (endX - startX) / (pts.length - 1);
  return pts.reduce((acc, p, idx) => {
    const x = startX + idx * stepX;
    const y = 140 - Math.max(0, Math.min(1, (p.grainMoisture - 10) / 15)) * 100;
    return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
  }, '');
});

const moistureSubAreaPath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return `${moistureSubLinePath.value} L 420 140 L 30 140 Z`;
});

// Sub-chart 2 (Delta T)
const deltaSubLinePath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  const startX = 30;
  const endX = 420;
  const stepX = (endX - startX) / (pts.length - 1);
  return pts.reduce((acc, p, idx) => {
    const x = startX + idx * stepX;
    const delta = Math.max(0, p.tempInternal - p.tempExternal);
    const y = 140 - Math.max(0, Math.min(1, delta / 25)) * 100;
    return idx === 0 ? `M ${x} ${y}` : `${acc} L ${x} ${y}`;
  }, '');
});

const deltaSubAreaPath = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return '';
  return `${deltaSubLinePath.value} L 420 140 L 30 140 Z`;
});

// Sampled X-axis labels to prevent text collision
const sampledXLabels = computed(() => {
  const pts = activeDataset.value;
  if (pts.length < 2) return [];
  const step = Math.max(1, Math.floor(pts.length / 6));
  return pts.filter((_, idx) => idx % step === 0 || idx === pts.length - 1);
});

// Aggregate Statistics Summary
const masterStats = computed(() => {
  const pts = activeDataset.value;
  if (pts.length === 0) {
    return { maxTemp: '--', avgTemp: '--', maxDelta: '--', avgHum: '--', dryingRate: '0.12' };
  }
  const temps = pts.map(p => p.tempInternal);
  const hums = pts.map(p => p.humidityInternal);
  const deltas = pts.map(p => Math.max(0, p.tempInternal - p.tempExternal));

  const avgTemp = (temps.reduce((a, b) => a + b, 0) / temps.length).toFixed(1);
  const maxTemp = Math.max(...temps).toFixed(1);
  const avgHum = (hums.reduce((a, b) => a + b, 0) / hums.length).toFixed(0);
  const maxDelta = Math.max(...deltas).toFixed(1);

  return {
    maxTemp,
    avgTemp,
    avgHum,
    maxDelta,
    dryingRate: '0.15',
  };
});

function pushLivePoint(data) {
  const d = new Date();
  const timeLabel = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  livePoints.value.push({
    ...data,
    timestamp: d,
    label: timeLabel,
  });
  if (livePoints.value.length > 50) {
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
    console.warn('History fetch warning:', err.message);
  }
}

async function loadData() {
  try {
    const res = await telemetryService.getCurrent();
    if (res.telemetry && res.telemetry.hasData) {
      telemetry.value = { ...telemetry.value, ...res.telemetry, hasData: true };
      pushLivePoint(res.telemetry);
    }
    if (res.actuators) {
      actuators.value = { ...actuators.value, ...res.actuators };
    }
  } catch (e) {
    // Ignored
  }
  loadHistoryData();
}

function exportTelemetryCsv() {
  const pts = activeDataset.value;
  if (pts.length === 0) return;

  const headers = ['Timestamp', 'Suhu Internal (°C)', 'Suhu Eksternal (°C)', 'Kelembapan (% RH)', 'Kadar Air (%)', 'Radiasi Surya (W/m2)'];
  const rows = pts.map(p => [
    `"${p.fullTime}"`,
    p.tempInternal.toFixed(2),
    p.tempExternal.toFixed(2),
    p.humidityInternal.toFixed(2),
    p.grainMoisture.toFixed(2),
    p.solarRadiation.toFixed(1)
  ]);

  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `smart_dryer_telemetry_${Date.now()}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

let pollTimer = null;

onMounted(() => {
  loadData();

  socketService.on('telemetry_live', (data) => {
    if (data && data.hasData) {
      telemetry.value = { ...telemetry.value, ...data, hasData: true };
      pushLivePoint(data);
    }
  });

  socketService.on('actuators_update', (data) => {
    if (data) actuators.value = { ...actuators.value, ...data };
  });

  connectionManager.on('telemetry_live', (data) => {
    if (data && data.hasData) {
      telemetry.value = { ...telemetry.value, ...data, hasData: true };
      pushLivePoint(data);
    }
  });

  pollTimer = setInterval(loadData, 4000);
});

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer);
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
  color: #F1F5F9;
}

.pro-analytics-tag {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  background: #E8F5E9;
  color: #0D631B;
  border: 1px solid #A5D6A7;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

:global(.dark-theme) .pro-analytics-tag {
  background: rgba(74, 222, 128, 0.15);
  color: #4ADE80;
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
  color: #94A3B8;
}

.live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #94A3B8;
}

.live-dot-active {
  background: #10B981;
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
  border: 1px solid #CBD5E1;
  background: #FFFFFF;
  color: #475569;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-export-csv:hover {
  background: #F1F5F9;
  color: #0F172A;
}

:global(.dark-theme) .btn-export-csv {
  background: #0B242F;
  border-color: #1E4E61;
  color: #CBD5E1;
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
  background: #ECFDF5;
  color: #065F46;
  border: 1px solid #A7F3D0;
}

:global(.dark-theme) .badge-online {
  background: rgba(16, 185, 129, 0.15);
  color: #6EE7B7;
  border-color: rgba(16, 185, 129, 0.3);
}

.badge-standby {
  background: #FFFBEB;
  color: #92400E;
  border: 1px solid #FDE68A;
}

:global(.dark-theme) .badge-standby {
  background: rgba(245, 158, 11, 0.15);
  color: #FDE68A;
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
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .stat-card {
  background: var(--color-white, #13271C);
  border-color: rgba(30, 58, 43, 0.6);
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
  color: #64748B;
}

:global(.dark-theme) .stat-label {
  color: #94A3B8;
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

.icon-red-subtle { background: rgba(186, 26, 26, 0.1); }
.icon-blue-subtle { background: rgba(0, 93, 183, 0.1); }
.icon-amber-subtle { background: rgba(217, 119, 6, 0.1); }
.icon-purple-subtle { background: rgba(126, 34, 206, 0.1); }

:global(.dark-theme) .icon-red-subtle { background: rgba(239, 68, 68, 0.2); }
:global(.dark-theme) .icon-blue-subtle { background: rgba(59, 130, 246, 0.2); }
:global(.dark-theme) .icon-amber-subtle { background: rgba(245, 158, 11, 0.2); }
:global(.dark-theme) .icon-purple-subtle { background: rgba(168, 85, 247, 0.2); }

.stat-value-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.stat-number {
  font-size: 26px;
  font-weight: 800;
  color: #071E27;
  letter-spacing: -0.02em;
}

:global(.dark-theme) .stat-number {
  color: #FFFFFF;
}

.stat-unit {
  font-size: 13px;
  font-weight: 700;
  color: #64748B;
}

.stat-trend {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11.5px;
  color: #64748B;
  padding-top: 4px;
  border-top: 1px solid rgba(191, 202, 186, 0.3);
}

:global(.dark-theme) .stat-trend {
  border-top-color: rgba(30, 78, 97, 0.4);
  color: #94A3B8;
}

.delta-chip {
  padding: 1px 6px;
  border-radius: 5px;
  background: #ECFDF5;
  color: #065F46;
  font-weight: 700;
  font-size: 10.5px;
}

.delta-chip-blue {
  padding: 1px 6px;
  border-radius: 5px;
  background: #EFF6FF;
  color: #1D4ED8;
  font-weight: 700;
  font-size: 10.5px;
}

.delta-chip-amber {
  padding: 1px 6px;
  border-radius: 5px;
  background: #FFFBEB;
  color: #B45309;
  font-weight: 700;
  font-size: 10.5px;
}

.delta-chip-purple {
  padding: 1px 6px;
  border-radius: 5px;
  background: #FAF5FF;
  color: #7E22CE;
  font-weight: 700;
  font-size: 10.5px;
}

/* ========================================================================= */
/* MASTER CHART CARD STYLES */
/* ========================================================================= */
.master-chart-card {
  background: #FFFFFF;
  border: 1px solid rgba(191, 202, 186, 0.45);
  border-radius: 18px;
  padding: 22px 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

:global(.dark-theme) .master-chart-card {
  background: #0B242F;
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
  color: #071E27;
  margin: 0;
}

:global(.dark-theme) .master-chart-heading {
  color: #FFFFFF;
}

.master-chart-sub {
  font-size: 11.5px;
  color: #64748B;
  margin: 2px 0 0 0;
}

:global(.dark-theme) .master-chart-sub {
  color: #94A3B8;
}

/* Time Window Selector */
.time-window-selector {
  display: inline-flex;
  background: #F1F5F9;
  border-radius: 9px;
  padding: 3px;
  gap: 2px;
}

:global(.dark-theme) .time-window-selector {
  background: #071E27;
}

.time-btn {
  padding: 6px 12px;
  border-radius: 7px;
  border: none;
  background: transparent;
  font-size: 11.5px;
  font-weight: 700;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s ease;
}

:global(.dark-theme) .time-btn {
  color: #94A3B8;
}

.time-btn.active {
  background: #FFFFFF;
  color: #0D631B;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

:global(.dark-theme) .time-btn.active {
  background: #1E4E61;
  color: #FFFFFF;
}

/* Series Toggle Toolbar */
.series-toggle-toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  padding: 8px 12px;
  background: #F8FCFE;
  border: 1px solid rgba(191, 202, 186, 0.3);
  border-radius: 10px;
}

:global(.dark-theme) .series-toggle-toolbar {
  background: #071E27;
  border-color: rgba(30, 78, 97, 0.4);
}

.series-lbl {
  font-size: 11px;
  font-weight: 700;
  color: #64748B;
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
  color: #94A3B8;
  cursor: pointer;
  transition: all 0.2s ease;
}

.series-toggle-pill.active {
  background: #FFFFFF;
  color: #071E27;
  border-color: rgba(191, 202, 186, 0.5);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

:global(.dark-theme) .series-toggle-pill.active {
  background: #0E2C39;
  color: #FFFFFF;
  border-color: #1E4E61;
}

.series-box-dot {
  width: 8px;
  height: 8px;
  border-radius: 2px;
}

.bg-red { background: #BA1A1A; }
.bg-sky { background: #0284C7; }
.bg-blue { background: #005DB7; }
.bg-purple { background: #7E22CE; }
.bg-amber { background: #D97706; }
.bg-green { background: #10B981; }

/* SVG Canvas & Tooltip */
.svg-canvas-wrapper {
  position: relative;
  width: 100%;
  background: #FFFFFF;
  border-radius: 12px;
  overflow: hidden;
}

:global(.dark-theme) .svg-canvas-wrapper {
  background: #081B23;
}

.master-svg-chart {
  width: 100%;
  height: 360px;
  display: block;
}

/* Interactive Floating Tooltip */
.interactive-tooltip {
  position: absolute;
  background: rgba(7, 30, 39, 0.88);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 10px;
  padding: 10px 14px;
  color: #FFFFFF;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
  pointer-events: none;
  z-index: 20;
  min-width: 200px;
  transition: left 0.08s ease-out;
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
  color: #38BDF8;
}

.tooltip-tag {
  font-size: 9.5px;
  color: #94A3B8;
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
  color: #CBD5E1;
  flex: 1;
}

.tooltip-row strong {
  color: #FFFFFF;
  font-weight: 700;
}

/* Master Bottom Analytics Strip */
.master-analytics-strip {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
  padding: 12px 16px;
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
}

@media (max-width: 768px) {
  .master-analytics-strip {
    grid-template-columns: repeat(2, 1fr);
  }
}

:global(.dark-theme) .master-analytics-strip {
  background: #13271C;
  border-color: rgba(30, 58, 43, 0.5);
}

.analytic-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.an-lbl {
  font-size: 10.5px;
  font-weight: 700;
  color: #64748B;
  text-transform: uppercase;
}

:global(.dark-theme) .an-lbl {
  color: #94A3B8;
}

.an-val {
  font-size: 14.5px;
  font-weight: 800;
  color: #071E27;
}

:global(.dark-theme) .an-val {
  color: #FFFFFF;
}

.text-green-dark { color: #0D631B; }
.text-red { color: #BA1A1A; }
.text-blue { color: #005DB7; }
.text-purple { color: #7E22CE; }

:global(.dark-theme) .text-green-dark { color: #4ADE80; }
:global(.dark-theme) .text-red { color: #F87171; }
:global(.dark-theme) .text-blue { color: #60A5FA; }
:global(.dark-theme) .text-purple { color: #C084FC; }

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
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

:global(.dark-theme) .sub-chart-card {
  background: #0B242F;
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
  color: #071E27;
  margin: 0;
}

:global(.dark-theme) .sub-heading {
  color: #FFFFFF;
}

.sub-desc {
  font-size: 11px;
  color: #64748B;
  margin: 1px 0 0 0;
}

:global(.dark-theme) .sub-desc {
  color: #94A3B8;
}

.moisture-badge {
  padding: 3px 8px;
  border-radius: 6px;
  background: #FAF5FF;
  color: #7E22CE;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid #E9D5FF;
}

:global(.dark-theme) .moisture-badge {
  background: rgba(126, 34, 206, 0.15);
  color: #C084FC;
  border-color: rgba(126, 34, 206, 0.3);
}

.thermal-badge {
  padding: 3px 8px;
  border-radius: 6px;
  background: #FFFBEB;
  color: #B45309;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid #FDE68A;
}

:global(.dark-theme) .thermal-badge {
  background: rgba(245, 158, 11, 0.15);
  color: #FDE68A;
  border-color: rgba(245, 158, 11, 0.3);
}

.sub-svg-box {
  background: #F8FCFE;
  border: 1px solid rgba(191, 202, 186, 0.3);
  border-radius: 12px;
  overflow: hidden;
}

:global(.dark-theme) .sub-svg-box {
  background: #081B23;
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
  background: #F8FCFE;
  border-radius: 8px;
  font-size: 11.5px;
}

:global(.dark-theme) .sub-chart-footer {
  background: #071E27;
}

.sc-stat {
  display: flex;
  align-items: center;
  gap: 6px;
}

.sc-lbl {
  color: #64748B;
  font-weight: 600;
}

:global(.dark-theme) .sc-lbl {
  color: #94A3B8;
}

.pulse-ring {
  animation: ripple 1.8s ease-out infinite;
}

@keyframes ripple {
  0% { r: 5px; opacity: 0.8; }
  100% { r: 16px; opacity: 0; }
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.4; transform: scale(0.85); }
}
</style>
