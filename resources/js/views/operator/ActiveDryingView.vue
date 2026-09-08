<template>
  <div class="active-drying-page">
    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="desktop-content">
      <!-- Back Link & Header -->
      <div class="header-action-row">
        <div class="title-group">
          <button class="back-link-btn" @click="$emit('back')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Dashboard</span>
          </button>
          <div class="title-with-badge">
            <h1 class="page-title">{{ activeBatch?.cropVariety || 'Pengeringan Gabah Hanjeli' }}</h1>
            <span class="badge-pill bg-purple-subtle text-purple">
              {{ activeBatch?.batchCode || 'HJ-2026-ACTIVE' }}
            </span>
          </div>
          <p class="page-subtitle">
            Tingkat Rak: <strong>{{ activeBatch?.trayLevel || 'Semua Rak (1, 2, 3)' }}</strong> • Mode: <strong>{{ actuators.controlMode === 'MANUAL' ? 'Manual Control' : (activeBatch?.dryingMode || 'Hybrid Solar-Electric Auto') }}</strong> • Operator: <strong>{{ activeBatch?.operatorName || activeBatch?.operator?.name || 'Operator Greenhouse' }}</strong>
          </p>
        </div>

        <!-- Top Right Actions -->
        <div class="header-right-btns">
          <button class="btn-pause-action" @click="handleTogglePause">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="6" y="4" width="4" height="16"></rect>
              <rect x="14" y="4" width="4" height="16"></rect>
            </svg>
            <span>{{ isPaused ? 'Lanjutkan Sesi' : 'Jeda Sementara' }}</span>
          </button>

          <button class="btn-primary btn-finish-session" @click="handleFinishDrying">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <rect x="4" y="4" width="16" height="16" rx="2"></rect>
            </svg>
            <span>Selesaikan & Simpan Batch</span>
          </button>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- DURATION, PROGRESS & MASS BALANCE BANNER -->
      <!-- ========================================================================= -->
      <div class="duration-progress-card">
        <div class="duration-header-row">
          <div class="dur-clock-block">
            <div class="clock-icon-wrap">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div>
              <span class="dur-label">WAKTU BERJALAN (ELAPSED TIME)</span>
              <div class="dur-clock">{{ formattedTime }}</div>
            </div>
          </div>

          <div class="etc-block">
            <span class="dur-label">ESTIMASI SISA WAKTU MENUJU TARGET 12.0%</span>
            <div class="etc-time-val">~{{ estimatedHoursRemaining }} Jam Lagi</div>
          </div>

          <div class="rate-block">
            <span class="dur-label">LAJU PENGERINGAN SAAT INI</span>
            <div class="rate-val text-green-dark">~{{ currentDryingRate }} %/jam</div>
          </div>
        </div>

        <!-- Progress Track Bar -->
        <div class="process-track-container">
          <div class="track-bar">
            <div class="track-bar-fill" :style="{ width: progressPercent + '%' }">
              <div class="track-shimmer"></div>
            </div>
          </div>
          <div class="track-labels">
            <span>Kadar Air Awal: <strong>{{ activeBatch?.initialMoisturePercent || 25.5 }}%</strong></span>
            <span class="track-marker-badge">
              Saat Ini: <strong>{{ telemetry.grainMoisture.toFixed(1) }}%</strong> (Progres {{ progressPercent }}%)
            </span>
            <span>Target Akhir: <strong>{{ activeBatch?.targetMoisturePercent || 12.0 }}%</strong></span>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- 6 LIVE TELEMETRY KPI CARDS -->
      <!-- ========================================================================= -->
      <div class="stats-grid-6">
        <!-- 1. Suhu Internal -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">SUHU RUANG PENGERING</span>
            <div class="icon-circle icon-red-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2.2">
                <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-red">{{ telemetry.tempInternal.toFixed(1) }}</span>
            <span class="stat-unit">°C</span>
            <span class="delta-chip">ΔT: +{{ (telemetry.tempInternal - telemetry.tempExternal).toFixed(1) }}°C</span>
          </div>
          <div class="stat-footer-text">
            <span>Luar Ruang: <strong>{{ telemetry.tempExternal.toFixed(1) }}°C</strong></span>
          </div>
        </div>

        <!-- 2. Kelembapan Ruang -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">KELEMBAPAN RUANG (RH)</span>
            <div class="icon-circle icon-blue-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-blue">{{ telemetry.humidityInternal.toFixed(0) }}</span>
            <span class="stat-unit">% RH</span>
            <span class="delta-chip-blue">Target: &lt;50%</span>
          </div>
          <div class="stat-footer-text">
            <span>RH Lingkungan: <strong>{{ telemetry.humidityExternal.toFixed(0) }}%</strong></span>
          </div>
        </div>

        <!-- 3. Radiasi Surya -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">RADIASI SURYA MATAHARI</span>
            <div class="icon-circle icon-amber-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2m-7.07-15.07 1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2m-15.07 7.07 1.41-1.41m11.32-11.32 1.41-1.41"/>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-amber">{{ telemetry.solarRadiation.toFixed(0) }}</span>
            <span class="stat-unit">W/m²</span>
            <span class="delta-chip-amber">
              <svg v-if="telemetry.solarRadiation > 500" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display:inline-block; vertical-align:middle; margin-right:3px;">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
              </svg>
              <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="display:inline-block; vertical-align:middle; margin-right:3px;">
                <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
              </svg>
              <span>{{ telemetry.solarRadiation > 500 ? 'Terik Alami' : 'Redup' }}</span>
            </span>
          </div>
          <div class="stat-footer-text">
            <span>Efisiensi Kolektor Atap: <strong>Tinggi</strong></span>
          </div>
        </div>

        <!-- 4. Kadar Air Gabah -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">KADAR AIR GABAH</span>
            <div class="icon-circle icon-purple-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E22CE" stroke-width="2.2">
                <circle cx="12" cy="12" r="9"></circle>
                <polyline points="12 7 12 12 15 15"></polyline>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-purple">{{ telemetry.grainMoisture.toFixed(1) }}</span>
            <span class="stat-unit">%</span>
            <span class="delta-chip-purple">Gap: {{ Math.max(0, telemetry.grainMoisture - (activeBatch?.targetMoisturePercent || 12.0)).toFixed(1) }}%</span>
          </div>
          <div class="stat-footer-text">
            <span>Target SNI Simpan: <strong>{{ activeBatch?.targetMoisturePercent || 12.0 }}%</strong></span>
          </div>
        </div>

        <!-- 5. Bobot Massa Gabah -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">BOBOT MASSA GABAH</span>
            <div class="icon-circle icon-emerald-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2">
                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                <line x1="4" y1="22" x2="4" y2="15"></line>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-emerald">{{ (telemetry.weightCurrentKg || 125.0).toFixed(1) }}</span>
            <span class="stat-unit">kg</span>
            <span class="delta-chip-emerald">Air: -{{ liveEvaporatedWaterKg }} kg</span>
          </div>
          <div class="stat-footer-text">
            <span>Bobot Masuk Awal: <strong>{{ activeBatch?.initialWeightKg || 135.0 }} kg</strong></span>
          </div>
        </div>

        <!-- 6. Status Aktuator & Kipas -->
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">AKTUATOR & SIRKULASI</span>
            <div class="icon-circle icon-green-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
              </svg>
            </div>
          </div>
          <div class="stat-value-row">
            <span class="stat-number text-green-dark">
              {{ actuators.exhaustFanStatus ? actuators.exhaustFanSpeed + '%' : 'OFF' }}
            </span>
            <span class="stat-unit">Fan</span>
            <span class="status-chip" :class="actuators.auxHeaterStatus ? 'chip-on' : 'chip-off'">
              Heater {{ actuators.auxHeaterStatus ? 'ON' : 'OFF' }}
            </span>
          </div>
          <div class="stat-footer-text">
            <span>Mode Kontrol: <strong>{{ actuators.controlMode || 'AUTOMATIC' }}</strong></span>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- MASTER REAL-TIME DUAL-AXIS WAVEFORM CHART -->
      <!-- ========================================================================= -->
      <div class="master-chart-card">
        <div class="master-chart-header">
          <div class="header-title-box">
            <div class="chart-badge-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
            </div>
            <div>
              <h2 class="master-chart-heading">Grafik Aliran Telemetri Real-Time (Dual-Axis Waveform)</h2>
              <p class="master-chart-sub">Pemantauan dinamis Suhu Ruang, Suhu Luar, RH Udara, Kadar Air Gabah, dan Radiasi Surya selama proses pengeringan aktif</p>
            </div>
          </div>

          <div class="time-window-selector">
            <span class="live-points-indicator">
              <span class="live-blink-dot"></span> {{ liveHistoryPoints.length }} Titik Data Terkumpul
            </span>
          </div>
        </div>

        <!-- Interactive Series Toggles (Legend Checkboxes) -->
        <div class="series-toggle-toolbar">
          <button 
            type="button" 
            class="series-btn" 
            :class="{ active: visibleSeries.tempInternal }"
            @click="visibleSeries.tempInternal = !visibleSeries.tempInternal"
          >
            <span class="legend-color-dot" style="background: #E11D48;"></span>
            <span>Suhu Internal (°C)</span>
          </button>

          <button 
            type="button" 
            class="series-btn" 
            :class="{ active: visibleSeries.tempExternal }"
            @click="visibleSeries.tempExternal = !visibleSeries.tempExternal"
          >
            <span class="legend-color-dot" style="background: #0D9488;"></span>
            <span>Suhu Lingkungan (°C)</span>
          </button>

          <button 
            type="button" 
            class="series-btn" 
            :class="{ active: visibleSeries.humidity }"
            @click="visibleSeries.humidity = !visibleSeries.humidity"
          >
            <span class="legend-color-dot" style="background: #0284C7;"></span>
            <span>Kelembapan Udara (% RH)</span>
          </button>

          <button 
            type="button" 
            class="series-btn" 
            :class="{ active: visibleSeries.grainMoisture }"
            @click="visibleSeries.grainMoisture = !visibleSeries.grainMoisture"
          >
            <span class="legend-color-dot" style="background: #9333EA;"></span>
            <span>Kadar Air Gabah (%)</span>
          </button>

          <button 
            type="button" 
            class="series-btn" 
            :class="{ active: visibleSeries.solarRadiation }"
            @click="visibleSeries.solarRadiation = !visibleSeries.solarRadiation"
          >
            <span class="legend-color-dot" style="background: #F59E0B;"></span>
            <span>Radiasi Surya (W/m²)</span>
          </button>
        </div>

        <!-- Master SVG Dual-Axis Canvas -->
        <div 
          class="master-svg-chart-wrapper"
          @mousemove="handleChartMouseMove"
          @mouseleave="handleChartMouseLeave"
        >
          <svg viewBox="0 0 1000 320" class="master-svg" preserveAspectRatio="none">
            <defs>
              <linearGradient id="activeTempGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#E11D48" stop-opacity="0.22"/>
                <stop offset="100%" stop-color="#E11D48" stop-opacity="0.0"/>
              </linearGradient>
              <linearGradient id="activeMoistureGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#9333EA" stop-opacity="0.20"/>
                <stop offset="100%" stop-color="#9333EA" stop-opacity="0.0"/>
              </linearGradient>
              <linearGradient id="activeSolarGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.15"/>
                <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.0"/>
              </linearGradient>
            </defs>

            <!-- Safety Zone Band (40°C - 50°C) -->
            <rect x="60" y="140" width="880" height="24" fill="#10B981" fill-opacity="0.08" />
            <text x="65" y="156" font-size="9.5" fill="#059669" font-weight="600">ZONA TEMPERATUR IDEAL PENGERINGAN (40°C - 50°C)</text>

            <!-- Critical Threshold Line (55°C) -->
            <line x1="60" y1="128" x2="940" y2="128" stroke="#EF4444" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.6"/>
            <text x="830" y="123" font-size="9" fill="#DC2626" font-weight="600">Ambang Kritis 55°C</text>

            <!-- Target Moisture Line (12.0%) -->
            <line x1="60" y1="231" x2="940" y2="231" stroke="#9333EA" stroke-width="1.5" stroke-dasharray="3 3" opacity="0.6"/>
            <text x="820" y="226" font-size="9" fill="#7E22CE" font-weight="600">Target Simpan 12.0%</text>

            <!-- Grid Horizontal Lines & Left/Right Axis Labels -->
            <g v-for="g in yGridLines" :key="g.val">
              <line x1="60" :y1="g.y" x2="940" :y2="g.y" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="2 2"/>
              <text x="20" :y="g.y + 4" font-size="10.5" fill="#64748B" font-weight="600">{{ g.val }}</text>
              <text x="948" :y="g.y + 4" font-size="10.5" fill="#D97706" font-weight="600">{{ g.solarVal }}</text>
            </g>

            <text x="948" y="14" font-size="9" fill="#D97706" font-weight="700">W/m²</text>
            <text x="20" y="14" font-size="9" fill="#64748B" font-weight="700">°C / %</text>

            <!-- Area Fills -->
            <path v-if="visibleSeries.tempInternal && tempAreaPath" :d="tempAreaPath" fill="url(#activeTempGrad)" />
            <path v-if="visibleSeries.grainMoisture && moistureAreaPath" :d="moistureAreaPath" fill="url(#activeMoistureGrad)" />
            <path v-if="visibleSeries.solarRadiation && solarAreaPath" :d="solarAreaPath" fill="url(#activeSolarGrad)" />

            <!-- Polyline Curves -->
            <!-- 1. Solar Radiation (Amber) -->
            <path 
              v-if="visibleSeries.solarRadiation && solarCurvePath" 
              :d="solarCurvePath" 
              fill="none" 
              stroke="#F59E0B" 
              stroke-width="2.5" 
              stroke-linecap="round"
            />

            <!-- 2. Humidity (Sky Blue) -->
            <path 
              v-if="visibleSeries.humidity && humidityCurvePath" 
              :d="humidityCurvePath" 
              fill="none" 
              stroke="#0284C7" 
              stroke-width="2.5" 
              stroke-linecap="round"
            />

            <!-- 3. External Temp (Teal) -->
            <path 
              v-if="visibleSeries.tempExternal && tempExtCurvePath" 
              :d="tempExtCurvePath" 
              fill="none" 
              stroke="#0D9488" 
              stroke-width="2" 
              stroke-dasharray="4 2"
              stroke-linecap="round"
            />

            <!-- 4. Grain Moisture (Purple) -->
            <path 
              v-if="visibleSeries.grainMoisture && moistureCurvePath" 
              :d="moistureCurvePath" 
              fill="none" 
              stroke="#9333EA" 
              stroke-width="3.5" 
              stroke-linecap="round"
            />

            <!-- 5. Internal Temp (Rose/Red) -->
            <path 
              v-if="visibleSeries.tempInternal && tempCurvePath" 
              :d="tempCurvePath" 
              fill="none" 
              stroke="#E11D48" 
              stroke-width="3.5" 
              stroke-linecap="round"
            />

            <!-- X-Axis Base Line -->
            <line x1="60" y1="260" x2="940" y2="260" stroke="#94A3B8" stroke-width="1.5"/>

            <!-- X-Axis Time Ticks & Labels -->
            <g v-for="(lbl, idx) in xTimeLabels" :key="idx">
              <line :x1="lbl.x" y1="260" :x2="lbl.x" y2="266" stroke="#64748B" stroke-width="1.5"/>
              <text :x="lbl.x" y="282" font-size="10" fill="#64748B" text-anchor="middle" font-weight="500">{{ lbl.time }}</text>
            </g>

            <!-- Interactive Hover Crosshair -->
            <g v-if="hoverData">
              <line 
                :x1="hoverData.svgX" 
                y1="20" 
                :x2="hoverData.svgX" 
                y2="260" 
                stroke="#1E293B" 
                stroke-width="1.5" 
                stroke-dasharray="3 3"
              />
              <circle v-if="visibleSeries.tempInternal" :cx="hoverData.svgX" :cy="hoverData.yTemp" r="5" fill="#E11D48" stroke="#FFFFFF" stroke-width="2"/>
              <circle v-if="visibleSeries.grainMoisture" :cx="hoverData.svgX" :cy="hoverData.yMoisture" r="5" fill="#9333EA" stroke="#FFFFFF" stroke-width="2"/>
              <circle v-if="visibleSeries.humidity" :cx="hoverData.svgX" :cy="hoverData.yHum" r="4.5" fill="#0284C7" stroke="#FFFFFF" stroke-width="2"/>
              <circle v-if="visibleSeries.solarRadiation" :cx="hoverData.svgX" :cy="hoverData.ySolar" r="4.5" fill="#F59E0B" stroke="#FFFFFF" stroke-width="2"/>
            </g>
          </svg>

          <!-- Floating Glassmorphic Tooltip -->
          <div 
            v-if="hoverData" 
            class="floating-chart-tooltip"
            :style="{ left: `${hoverData.domPercentX}%` }"
          >
            <div class="tooltip-header">
              <span class="tooltip-time-icon">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </span>
              <strong>{{ hoverData.item.timeFormatted || hoverData.item.time }}</strong>
            </div>
            <div class="tooltip-grid">
              <div class="tooltip-item" v-if="visibleSeries.tempInternal">
                <span class="tt-dot" style="background: #E11D48;"></span>
                <span class="tt-label">Suhu Ruang:</span>
                <span class="tt-val">{{ hoverData.item.tempInternal.toFixed(1) }}°C</span>
              </div>
              <div class="tooltip-item" v-if="visibleSeries.tempExternal">
                <span class="tt-dot" style="background: #0D9488;"></span>
                <span class="tt-label">Suhu Luar:</span>
                <span class="tt-val">{{ hoverData.item.tempExternal.toFixed(1) }}°C</span>
              </div>
              <div class="tooltip-item" v-if="visibleSeries.humidity">
                <span class="tt-dot" style="background: #0284C7;"></span>
                <span class="tt-label">Kelembapan:</span>
                <span class="tt-val">{{ hoverData.item.humidityInternal.toFixed(0) }}% RH</span>
              </div>
              <div class="tooltip-item" v-if="visibleSeries.grainMoisture">
                <span class="tt-dot" style="background: #9333EA;"></span>
                <span class="tt-label">Kadar Air:</span>
                <span class="tt-val font-bold">{{ hoverData.item.grainMoisture.toFixed(1) }}%</span>
              </div>
              <div class="tooltip-item" v-if="visibleSeries.solarRadiation">
                <span class="tt-dot" style="background: #F59E0B;"></span>
                <span class="tt-label">Radiasi:</span>
                <span class="tt-val">{{ hoverData.item.solarRadiation.toFixed(0) }} W/m²</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- 2 DEEP-DIVE LIVE ANALYTICS SUBPANELS -->
      <!-- ========================================================================= -->
      <div class="two-subpanels-grid">
        <!-- Subpanel 1: Kinetika Pengeringan Real-Time -->
        <div class="subpanel-card">
          <div class="subpanel-head">
            <div class="subpanel-title-wrap">
              <span class="subpanel-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9333EA" stroke-width="2.2">
                  <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                </svg>
              </span>
              <div>
                <h3 class="subpanel-title">Kinetika Dehidrasi Gabah (Drying Rate)</h3>
                <p class="subpanel-sub">Peluruhan kadar air (% per jam) sepanjang sesi</p>
              </div>
            </div>
            <span class="badge-pill bg-purple-subtle text-purple">{{ liveHistoryPoints.length }} Titik Log</span>
          </div>

          <div class="subpanel-svg-wrap">
            <svg viewBox="0 0 500 160" class="subpanel-svg">
              <line x1="40" y1="25" x2="480" y2="25" stroke="#F1F5F9" />
              <text x="10" y="29" font-size="9.5" fill="#94A3B8">30%</text>
              <line x1="40" y1="70" x2="480" y2="70" stroke="#F1F5F9" />
              <text x="10" y="74" font-size="9.5" fill="#94A3B8">20%</text>
              <line x1="40" y1="115" x2="480" y2="115" stroke="#F1F5F9" />
              <text x="10" y="119" font-size="9.5" fill="#94A3B8">10%</text>
              <line x1="40" y1="140" x2="480" y2="140" stroke="#CBD5E1" />

              <line x1="40" y1="105" x2="480" y2="105" stroke="#9333EA" stroke-dasharray="3 3" opacity="0.6"/>
              <text x="410" y="100" font-size="9" fill="#9333EA">Target 12%</text>

              <path :d="kineticsPath" fill="none" stroke="#9333EA" stroke-width="3.5" stroke-linecap="round" />
            </svg>
          </div>
          <p class="subpanel-desc">Sistem mengalkulasi kecepatan penurunan air rata-rata <strong>~{{ currentDryingRate }}%/jam</strong>, memastikan butir hanjeli tidak pecah akibat panas mendadak.</p>
        </div>

        <!-- Subpanel 2: Diferensial Termal (ΔT) -->
        <div class="subpanel-card">
          <div class="subpanel-head">
            <div class="subpanel-title-wrap">
              <span class="subpanel-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E11D48" stroke-width="2.2">
                  <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                </svg>
              </span>
              <div>
                <h3 class="subpanel-title">Retensi Panas Greenhouse (ΔT)</h3>
                <p class="subpanel-sub">Kenaikan suhu ruang terhadap lingkungan luar (Tin - Text)</p>
              </div>
            </div>
            <span class="badge-pill bg-red-subtle text-red">Thermal Lift</span>
          </div>

          <div class="subpanel-svg-wrap">
            <svg viewBox="0 0 500 160" class="subpanel-svg">
              <line x1="40" y1="25" x2="480" y2="25" stroke="#F1F5F9" />
              <text x="10" y="29" font-size="9.5" fill="#94A3B8">+20°C</text>
              <line x1="40" y1="65" x2="480" y2="65" stroke="#F1F5F9" />
              <text x="10" y="69" font-size="9.5" fill="#94A3B8">+15°C</text>
              <line x1="40" y1="105" x2="480" y2="105" stroke="#F1F5F9" />
              <text x="10" y="109" font-size="9.5" fill="#94A3B8">+10°C</text>
              <line x1="40" y1="140" x2="480" y2="140" stroke="#CBD5E1" />

              <path :d="deltaTPath" fill="none" stroke="#E11D48" stroke-width="3" stroke-linecap="round" />
            </svg>
          </div>
          <p class="subpanel-desc">Greenhouse mempertahankan selisih suhu <strong>+{{ (telemetry.tempInternal - telemetry.tempExternal).toFixed(1) }}°C</strong> di atas suhu luar, mengoptimalkan panas surya alami.</p>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MOBILE VIEW LAYOUT -->
    <!-- ========================================================================= -->
    <div v-else class="mobile-content">
      <!-- Main Mobile Card -->
      <div class="mobile-process-box">
        <div class="m-box-top">
          <button class="back-link-btn" @click="$emit('back')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali</span>
          </button>
          <span class="m-status-text">
            <span class="dot-live"></span> {{ activeBatch?.batchCode || 'Batch Aktif' }}
          </span>
        </div>

        <div class="m-batch-name-row">
          <h2 class="m-batch-title">{{ activeBatch?.cropVariety || 'Pengeringan Gabah Hanjeli' }}</h2>
          <span class="m-clock-text">{{ formattedTime }}</span>
        </div>

        <div class="m-bar-wrap">
          <div class="m-bar-fill" :style="{ width: progressPercent + '%' }"></div>
        </div>

        <div class="m-labels-row">
          <span>Awal: {{ activeBatch?.initialMoisturePercent || 25.5 }}%</span>
          <span class="text-bold">Saat Ini: {{ telemetry.grainMoisture.toFixed(1) }}%</span>
          <span>Target: {{ activeBatch?.targetMoisturePercent || 12.0 }}%</span>
        </div>
      </div>

      <!-- 4 Mini Metric Cards Grid -->
      <div class="m-metrics-grid">
        <div class="m-metric-card">
          <div class="m-metric-head">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2">
              <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
            </svg>
            <span>Suhu Ruang</span>
          </div>
          <div class="mini-val text-red">{{ telemetry.tempInternal.toFixed(1) }}<span class="unit-xs">°C</span></div>
        </div>

        <div class="m-metric-card">
          <div class="m-metric-head">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2">
              <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
            </svg>
            <span>Kelembaban</span>
          </div>
          <div class="mini-val text-blue">{{ telemetry.humidityInternal.toFixed(0) }}<span class="unit-xs">%</span></div>
        </div>

        <div class="m-metric-card">
          <div class="m-metric-head">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2">
              <circle cx="12" cy="12" r="4"></circle>
              <path d="M12 2v2M12 20v2M2 12h2M20 12h2"></path>
            </svg>
            <span>Radiasi Surya</span>
          </div>
          <div class="mini-val text-amber">{{ telemetry.solarRadiation.toFixed(0) }}<span class="unit-xs">W/m²</span></div>
        </div>

        <div class="m-metric-card">
          <div class="m-metric-head">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span>Kipas Exhaust</span>
          </div>
          <div class="mini-val text-green-dark">{{ actuators.exhaustFanStatus ? actuators.exhaustFanSpeed : 0 }}<span class="unit-xs">%</span></div>
        </div>
      </div>

      <!-- Mobile Chart Waveform -->
      <div class="mobile-chart-card">
        <div class="m-chart-head">
          <h4 class="m-chart-title">Grafik Aliran Suhu & Kadar Air</h4>
          <span class="badge-pill bg-purple-subtle text-purple">{{ liveHistoryPoints.length }} Titik</span>
        </div>
        <div class="m-svg-wrap">
          <svg viewBox="0 0 320 140" class="w-full">
            <line x1="30" y1="20" x2="310" y2="20" stroke="#F1F5F9" />
            <text x="5" y="24" font-size="9" fill="#94A3B8">30%</text>
            <line x1="30" y1="60" x2="310" y2="60" stroke="#F1F5F9" />
            <text x="5" y="64" font-size="9" fill="#94A3B8">20%</text>
            <line x1="30" y1="100" x2="310" y2="100" stroke="#CBD5E1" />
            <text x="5" y="104" font-size="9" fill="#94A3B8">10%</text>

            <line x1="30" y1="90" x2="310" y2="90" stroke="#9333EA" stroke-dasharray="3 3" opacity="0.6"/>

            <path :d="mobileMoisturePath" fill="none" stroke="#9333EA" stroke-width="3" stroke-linecap="round" />
            <path :d="mobileTempPath" fill="none" stroke="#E11D48" stroke-width="2.5" stroke-linecap="round" />
          </svg>
        </div>
        <div class="m-chart-legend">
          <span><span class="dot-sm" style="background: #9333EA;"></span> Kadar Air</span>
          <span><span class="dot-sm" style="background: #E11D48;"></span> Suhu Ruang</span>
        </div>
      </div>

      <!-- Mobile Stop Action -->
      <button class="btn-stop-mobile" @click="handleFinishDrying">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
          <rect x="4" y="4" width="16" height="16" rx="2"></rect>
        </svg>
        <span>Selesaikan & Simpan Pengeringan</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { telemetryService } from '../../services/telemetryService'
import { batchService } from '../../services/batchService'
import { socketService } from '../../services/socketService'

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['back', 'finish'])

const isConnected = ref(true)
const isPaused = ref(false)
const activeBatch = ref(null)
const seconds = ref(5400)
const formattedTime = ref('01:30:00')
let timer = null
let pollingTimer = null

const telemetry = ref({
  tempInternal: 42.5,
  tempExternal: 30.2,
  humidityInternal: 48.0,
  humidityExternal: 68.0,
  grainMoisture: 14.2,
  solarRadiation: 780.0,
  weightCurrentKg: 126.5,
})

const actuators = ref({
  exhaustFanStatus: true,
  exhaustFanSpeed: 75,
  auxHeaterStatus: false,
  auxHeaterLevel: 0,
  controlMode: 'AUTOMATIC'
})

// Series Visibility Toggles
const visibleSeries = reactive({
  tempInternal: true,
  tempExternal: true,
  humidity: true,
  grainMoisture: true,
  solarRadiation: true,
})

// Crosshair & Tooltip State
const hoverData = ref(null)

// Live Rolling Telemetry History
const liveHistoryPoints = ref([])

function appendTelemetryToHistory(data) {
  const now = new Date()
  const timeFormatted = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  liveHistoryPoints.value.push({
    time: timeFormatted,
    timeFormatted,
    tempInternal: Number(data.tempInternal ?? data.temp_internal ?? telemetry.value.tempInternal),
    tempExternal: Number(data.tempExternal ?? data.temp_external ?? telemetry.value.tempExternal),
    humidityInternal: Number(data.humidityInternal ?? data.humidity_internal ?? telemetry.value.humidityInternal),
    humidityExternal: Number(data.humidityExternal ?? data.humidity_external ?? telemetry.value.humidityExternal),
    solarRadiation: Number(data.solarRadiation ?? data.solar_radiation ?? telemetry.value.solarRadiation),
    grainMoisture: Number(data.grainMoisture ?? data.grain_moisture ?? telemetry.value.grainMoisture),
    weightKg: Number(data.weightCurrentKg ?? data.weight_kg ?? telemetry.value.weightCurrentKg),
  })

  // Limit rolling points to 35
  if (liveHistoryPoints.value.length > 35) {
    liveHistoryPoints.value.shift()
  }
}

// Populate initial synthetic rolling curve
function seedInitialHistory() {
  const initial = []
  const count = 18
  const baseTime = new Date(Date.now() - count * 60000)
  for (let i = 0; i < count; i++) {
    const t = new Date(baseTime.getTime() + i * 60000)
    const progress = i / (count - 1)
    const tempIn = 38.0 + progress * 4.5 + (Math.sin(i) * 0.4)
    const tempExt = 28.0 + progress * 2.2
    const humIn = 65.0 - progress * 17.0 + (Math.sin(i * 1.5) * 1.5)
    const humExt = 75.0 - progress * 7.0
    const solar = 620.0 + progress * 160.0 + (Math.sin(i) * 30)
    const moisture = 22.0 - progress * 7.8
    const weight = 135.0 - progress * 8.5

    initial.push({
      time: t.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
      timeFormatted: t.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
      tempInternal: Number(tempIn.toFixed(1)),
      tempExternal: Number(tempExt.toFixed(1)),
      humidityInternal: Number(humIn.toFixed(0)),
      humidityExternal: Number(humExt.toFixed(0)),
      solarRadiation: Number(solar.toFixed(0)),
      grainMoisture: Number(moisture.toFixed(1)),
      weightKg: Number(weight.toFixed(1)),
    })
  }
  liveHistoryPoints.value = initial
}

const progressPercent = computed(() => {
  if (!activeBatch.value) return 72
  const initial = activeBatch.value.initialMoisturePercent || 25.5
  const target = activeBatch.value.targetMoisturePercent || 12.0
  const current = telemetry.value.grainMoisture || 14.2
  if (initial <= target) return 100
  const p = ((initial - current) / (initial - target)) * 100
  return Math.min(100, Math.max(0, Math.round(p)))
})

const estimatedHoursRemaining = computed(() => {
  const diff = (telemetry.value.grainMoisture || 14.2) - (activeBatch.value?.targetMoisturePercent || 12.0)
  if (diff <= 0) return '0.5'
  return Math.max(0.5, (diff * 0.75)).toFixed(1)
})

const currentDryingRate = computed(() => {
  if (liveHistoryPoints.value.length < 2) return '1.45'
  const first = liveHistoryPoints.value[0].grainMoisture
  const last = liveHistoryPoints.value[liveHistoryPoints.value.length - 1].grainMoisture
  const drop = Math.max(0.2, first - last)
  return (drop * 3.5).toFixed(2)
})

const liveEvaporatedWaterKg = computed(() => {
  const init = Number(activeBatch.value?.initialWeightKg || 135.0)
  const curr = Number(telemetry.value.weightCurrentKg || 126.5)
  return Math.max(0, init - curr).toFixed(1)
})

function updateFormattedTime() {
  const hrs = String(Math.floor(seconds.value / 3600)).padStart(2, '0')
  const mins = String(Math.floor((seconds.value % 3600) / 60)).padStart(2, '0')
  const secs = String(seconds.value % 60).padStart(2, '0')
  formattedTime.value = `${hrs}:${mins}:${secs}`
}

async function loadData() {
  try {
    const activeRes = await batchService.getActiveBatch()
    if (activeRes && activeRes.activeBatch) {
      activeBatch.value = activeRes.activeBatch
      if (activeRes.activeBatch.startedAt) {
        const start = new Date(activeRes.activeBatch.startedAt)
        const diffSecs = Math.max(0, Math.floor((new Date() - start) / 1000))
        seconds.value = diffSecs
        updateFormattedTime()
      }
    }
  } catch (err) {
    console.warn('Could not fetch active batch from API:', err.message)
  }

  try {
    const res = await telemetryService.getCurrent()
    if (res.telemetry) {
      telemetry.value = { ...telemetry.value, ...res.telemetry }
      appendTelemetryToHistory(res.telemetry)
    }
    if (res.actuators) {
      actuators.value = { ...actuators.value, ...res.actuators }
    }
  } catch (err) {
    console.warn('Could not fetch active drying state:', err.message)
  }
}

async function handleTogglePause() {
  if (!activeBatch.value || !activeBatch.value.id) return
  try {
    if (isPaused.value) {
      await batchService.resume(activeBatch.value.id)
      isPaused.value = false
    } else {
      await batchService.pause(activeBatch.value.id)
      isPaused.value = true
    }
  } catch (e) {
    alert(e.message || 'Gagal mengubah status jeda batch.')
  }
}

async function handleFinishDrying() {
  if (confirm('Apakah Anda yakin ingin menyelesaikan sesi pengeringan ini dan menyimpan data ke riwayat?')) {
    if (activeBatch.value && activeBatch.value.id) {
      try {
        await batchService.complete(activeBatch.value.id, {
          finalWeightKg: telemetry.value.weightCurrentKg || 125,
          finalMoisturePercent: telemetry.value.grainMoisture || 12.0,
          qualityScore: 95,
          notes: 'Siklus pengeringan selesai sesuai target SNI (&le; 12.0%).'
        })
      } catch (e) {
        console.warn('Finish batch notice:', e.message)
      }
    }
    emit('finish')
  }
}

// =========================================================================
// SVG PATH GENERATORS
// =========================================================================
function mapPointsToSvg(values, yMin, yMax, width = 880, height = 240, xOffset = 60, yOffset = 20) {
  if (!values || values.length < 2) return ''
  const stepX = width / (values.length - 1)
  return values.map((val, idx) => {
    const clamped = Math.max(yMin, Math.min(yMax, val))
    const norm = (clamped - yMin) / (yMax - yMin)
    const x = Math.round(xOffset + idx * stepX)
    const y = Math.round(yOffset + (1 - norm) * height)
    return { x, y }
  })
}

function buildSvgPath(coords) {
  if (!coords || coords.length === 0) return ''
  return coords.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.y}`).join(' ')
}

function buildSvgArea(coords, baselineY = 260) {
  if (!coords || coords.length < 2) return ''
  const first = coords[0]
  const last = coords[coords.length - 1]
  const lineStr = coords.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.y}`).join(' ')
  return `${lineStr} L ${last.x} ${baselineY} L ${first.x} ${baselineY} Z`
}

const tempCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.tempInternal), 0, 100, 880, 240, 60, 20))
const tempExtCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.tempExternal), 0, 100, 880, 240, 60, 20))
const humCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.humidityInternal), 0, 100, 880, 240, 60, 20))
const moistureCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.grainMoisture), 0, 100, 880, 240, 60, 20))
const solarCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.solarRadiation), 0, 1000, 880, 240, 60, 20))

const tempCurvePath = computed(() => buildSvgPath(tempCoords.value))
const tempAreaPath = computed(() => buildSvgArea(tempCoords.value))
const tempExtCurvePath = computed(() => buildSvgPath(tempExtCoords.value))
const humidityCurvePath = computed(() => buildSvgPath(humCoords.value))
const moistureCurvePath = computed(() => buildSvgPath(moistureCoords.value))
const moistureAreaPath = computed(() => buildSvgArea(moistureCoords.value))
const solarCurvePath = computed(() => buildSvgPath(solarCoords.value))
const solarAreaPath = computed(() => buildSvgArea(solarCoords.value))

const yGridLines = [
  { val: 100, unit: '', y: 20, solarVal: '1000' },
  { val: 80, unit: '', y: 68, solarVal: '800' },
  { val: 60, unit: '', y: 116, solarVal: '600' },
  { val: 40, unit: '', y: 164, solarVal: '400' },
  { val: 20, unit: '', y: 212, solarVal: '200' },
  { val: 0, unit: '', y: 260, solarVal: '0' },
]

const xTimeLabels = computed(() => {
  const pts = liveHistoryPoints.value
  if (pts.length === 0) return []
  const count = Math.min(6, pts.length)
  const labels = []
  const stepIdx = Math.floor((pts.length - 1) / (count - 1 || 1))
  for (let i = 0; i < count; i++) {
    const idx = Math.min(i * stepIdx, pts.length - 1)
    const x = Math.round(60 + (idx / (pts.length - 1 || 1)) * 880)
    labels.push({ x, time: pts[idx].timeFormatted || pts[idx].time })
  }
  return labels
})

// Subpanels
const kineticsPath = computed(() => {
  const coords = mapPointsToSvg(liveHistoryPoints.value.map(d => d.grainMoisture), 5, 30, 440, 115, 40, 25)
  return buildSvgPath(coords)
})

const deltaTPath = computed(() => {
  const deltas = liveHistoryPoints.value.map(d => d.tempInternal - d.tempExternal)
  const coords = mapPointsToSvg(deltas, 0, 20, 440, 115, 40, 25)
  return buildSvgPath(coords)
})

const mobileMoisturePath = computed(() => {
  const coords = mapPointsToSvg(liveHistoryPoints.value.map(d => d.grainMoisture), 5, 30, 280, 80, 30, 20)
  return buildSvgPath(coords)
})

const mobileTempPath = computed(() => {
  const coords = mapPointsToSvg(liveHistoryPoints.value.map(d => d.tempInternal), 20, 60, 280, 80, 30, 20)
  return buildSvgPath(coords)
})

function handleChartMouseMove(e) {
  const pts = liveHistoryPoints.value
  if (pts.length === 0) return
  const rect = e.currentTarget.getBoundingClientRect()
  const offsetX = e.clientX - rect.left
  const percentX = Math.max(0, Math.min(1, offsetX / rect.width))
  
  const index = Math.round(percentX * (pts.length - 1))
  const item = pts[index]
  if (!item) return

  const svgX = Math.round(60 + percentX * 880)
  const normTemp = (item.tempInternal - 0) / 100
  const normHum = (item.humidityInternal - 0) / 100
  const normMoist = (item.grainMoisture - 0) / 100
  const normSolar = (item.solarRadiation - 0) / 1000

  hoverData.value = {
    index,
    item,
    svgX,
    domPercentX: percentX * 100,
    yTemp: Math.round(20 + (1 - normTemp) * 240),
    yHum: Math.round(20 + (1 - normHum) * 240),
    yMoisture: Math.round(20 + (1 - normMoist) * 240),
    ySolar: Math.round(20 + (1 - normSolar) * 240),
  }
}

function handleChartMouseLeave() {
  hoverData.value = null
}

onMounted(() => {
  seedInitialHistory()
  loadData()

  timer = setInterval(() => {
    if (!isPaused.value) {
      seconds.value++
      updateFormattedTime()
    }
  }, 1000)

  pollingTimer = setInterval(() => {
    telemetryService.getCurrent().then(res => {
      if (res.telemetry) {
        telemetry.value = { ...telemetry.value, ...res.telemetry }
        appendTelemetryToHistory(res.telemetry)
      }
    }).catch(() => {})
  }, 3000)

  socketService.on('telemetry_live', (data) => {
    telemetry.value = { ...telemetry.value, ...data }
    appendTelemetryToHistory(data)
    isConnected.value = true
  })

  socketService.on('actuators_update', (data) => {
    actuators.value = { ...actuators.value, ...data }
  })
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
  if (pollingTimer) clearInterval(pollingTimer)
  socketService.off('telemetry_live')
  socketService.off('actuators_update')
})
</script>

<style scoped>
.active-drying-page {
  width: 100%;
  min-height: 100%;
  background: transparent;
}

.desktop-content {
  padding: 32px 40px;
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
  gap: 20px;
}

.title-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.title-with-badge {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.back-link-btn {
  background: transparent;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  padding: 0;
  margin-bottom: 2px;
  transition: color 0.15s ease;
}

.back-link-btn:hover {
  color: #0D631B;
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

.live-active-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 5px 12px;
  background: #E8F5E9;
  color: #0D631B;
  border: 1px solid #A5D6A7;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.live-pulsing-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #0D631B;
  animation: pulseDot 1.5s infinite;
}

@keyframes pulseDot {
  0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(13, 99, 27, 0.7); }
  70% { transform: scale(1.1); box-shadow: 0 0 0 7px rgba(13, 99, 27, 0); }
  100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(13, 99, 27, 0); }
}

.header-right-btns {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-pause-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--color-white);
  color: var(--color-text-title);
  border: 1px solid rgba(203, 213, 225, 0.5);
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-pause-action:hover {
  background: var(--color-bg-light);
  border-color: #94A3B8;
}

.btn-finish-session {
  background: #BA1A1A !important;
  border-color: #BA1A1A !important;
  color: #FFFFFF !important;
}

.btn-finish-session:hover {
  background: #961515 !important;
}

/* Duration & Progress Card */
.duration-progress-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 22px 26px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .duration-progress-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.duration-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.dur-clock-block {
  display: flex;
  align-items: center;
  gap: 14px;
}

.clock-icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dur-label {
  font-size: 11px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.dur-clock {
  font-size: 32px;
  font-weight: 900;
  color: #0D631B;
  font-family: monospace;
  line-height: 1.1;
}

.etc-time-val, .rate-val {
  font-size: 20px;
  font-weight: 800;
  color: var(--color-text-title);
}

.process-track-container {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.track-bar {
  width: 100%;
  height: 14px;
  background: var(--color-bg-light);
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(203, 213, 225, 0.4);
  position: relative;
}

.track-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #10B981, #0D631B);
  border-radius: 20px;
  position: relative;
  overflow: hidden;
  transition: width 0.6s ease;
}

.track-shimmer {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shimmerAnim 2s infinite;
}

@keyframes shimmerAnim {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.track-labels {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12.5px;
  color: var(--color-text-muted);
}

.track-marker-badge {
  background: #E8F5E9;
  color: #0D631B;
  padding: 2px 10px;
  border-radius: 12px;
  font-weight: 700;
}

/* 6 KPI Cards Grid */
.stats-grid-6 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.stat-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .stat-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.stat-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
}

.stat-label {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.stat-value-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.stat-number {
  font-size: 28px;
  font-weight: 800;
  line-height: 1.1;
}

.stat-unit {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-muted);
  margin-right: 6px;
}

.delta-chip, .delta-chip-blue, .delta-chip-amber, .delta-chip-purple, .delta-chip-emerald {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
  margin-left: auto;
}

.delta-chip { background: #FFE4E6; color: #E11D48; }
.delta-chip-blue { background: #E0F2FE; color: #0284C7; }
.delta-chip-amber { background: #FEF3C7; color: #D97706; }
.delta-chip-purple { background: #F3E8FF; color: #7E22CE; }
.delta-chip-emerald { background: #D1FAE5; color: #059669; }

.stat-footer-text {
  font-size: 12px;
  color: var(--color-text-muted);
  border-top: 1px solid var(--color-border-subtle, #F1F5F9);
  padding-top: 6px;
}

/* Master Waveform Chart */
.master-chart-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .master-chart-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.master-chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title-box {
  display: flex;
  align-items: center;
  gap: 12px;
}

.chart-badge-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.master-chart-heading {
  font-size: 17px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.master-chart-sub {
  font-size: 12.5px;
  color: var(--color-text-muted);
}

.live-points-indicator {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #0D631B;
  background: #E8F5E9;
  padding: 4px 10px;
  border-radius: 20px;
}

.live-blink-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #0D631B;
  animation: pulseDot 1s infinite;
}

.series-toggle-toolbar {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.series-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-bg-light);
  color: var(--color-text-muted);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  opacity: 0.55;
  transition: all 0.15s ease;
}

.series-btn.active {
  opacity: 1;
  background: var(--color-white);
  border-color: #94A3B8;
}

.legend-color-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.master-svg-chart-wrapper {
  position: relative;
  width: 100%;
  height: 320px;
  background: #FCFDFD;
  border: 1px solid var(--color-border-subtle, #F1F5F9);
  border-radius: 10px;
  overflow: hidden;
  cursor: crosshair;
}

:global(.dark-theme) .master-svg-chart-wrapper {
  background: #0F172A;
  border-color: #1E293B;
}

.master-svg {
  width: 100%;
  height: 100%;
}

.floating-chart-tooltip {
  position: absolute;
  top: 15px;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(10px);
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  padding: 10px 14px;
  pointer-events: none;
  z-index: 20;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  min-width: 220px;
}

:global(.dark-theme) .floating-chart-tooltip {
  background: rgba(15, 23, 42, 0.92);
  border-color: #334155;
}

.tooltip-header {
  font-size: 12px;
  color: var(--color-text-title);
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 6px;
  margin-bottom: 8px;
}

.tooltip-grid {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.tooltip-item {
  display: flex;
  align-items: center;
  font-size: 12px;
}

.tt-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  margin-right: 6px;
}

.tt-label {
  color: var(--color-text-muted);
  margin-right: auto;
}

.tt-val {
  font-weight: 700;
  color: var(--color-text-title);
}

/* 2 Subpanels Grid */
.two-subpanels-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.subpanel-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .subpanel-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.subpanel-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.subpanel-title-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}

.subpanel-icon {
  font-size: 20px;
}

.subpanel-title {
  font-size: 14.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

.subpanel-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.subpanel-svg-wrap {
  width: 100%;
  height: 160px;
}

.subpanel-svg {
  width: 100%;
  height: 100%;
}

.subpanel-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  line-height: 1.4;
}

/* Mobile Styling */
.mobile-content {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.mobile-process-box {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.m-box-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.m-status-text {
  font-size: 12px;
  font-weight: 700;
  color: #0D631B;
  display: flex;
  align-items: center;
  gap: 6px;
}

.dot-live {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #0D631B;
  animation: pulseDot 1.5s infinite;
}

.m-batch-name-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
}

.m-batch-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--color-text-title);
}

.m-clock-text {
  font-size: 18px;
  font-weight: 800;
  color: #0D631B;
  font-family: monospace;
}

.m-bar-wrap {
  width: 100%;
  height: 10px;
  background: var(--color-bg-light);
  border-radius: 10px;
  overflow: hidden;
}

.m-bar-fill {
  height: 100%;
  background: #0D631B;
  border-radius: 10px;
}

.m-labels-row {
  display: flex;
  justify-content: space-between;
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.m-metrics-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.m-metric-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 12px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.m-metric-head {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: var(--color-text-muted);
}

.mini-val {
  font-size: 18px;
  font-weight: 800;
}

.unit-xs {
  font-size: 12px;
  font-weight: 600;
  margin-left: 2px;
}

.mobile-chart-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.m-chart-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.m-chart-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

.m-svg-wrap {
  width: 100%;
  height: 140px;
}

.m-chart-legend {
  display: flex;
  justify-content: center;
  gap: 16px;
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.dot-sm {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 4px;
}

.btn-stop-mobile {
  width: 100%;
  background: #BA1A1A;
  color: #FFFFFF;
  border: none;
  padding: 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
}

/* Global utility classes */
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

.icon-red-subtle { background: #FFE4E6; }
.icon-blue-subtle { background: #E0F2FE; }
.icon-amber-subtle { background: #FEF3C7; }
.icon-purple-subtle { background: #F3E8FF; }
.icon-emerald-subtle { background: #D1FAE5; }
.icon-green-subtle { background: #E8F5E9; }

.status-chip {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  margin-left: auto;
}

.chip-on { background: #FEE2E2; color: #DC2626; }
.chip-off { background: #F1F5F9; color: #64748B; }

.bg-purple-subtle { background: #F3E8FF; }
.text-purple { color: #9333EA; }
.bg-red-subtle { background: #FFE4E6; }
.text-red { color: #E11D48; }
.bg-blue-subtle { background: #E0F2FE; }
.text-blue { color: #0284C7; }
.bg-amber-subtle { background: #FEF3C7; }
.text-amber { color: #D97706; }
.text-green-dark { color: #0D631B; }

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

@media (max-width: 1024px) {
  .stats-grid-6 {
    grid-template-columns: repeat(2, 1fr);
  }
  .two-subpanels-grid {
    grid-template-columns: 1fr;
  }
}
</style>
