<template>
  <div class="dashboard-page">
    <Transition name="fade-dashboard" mode="out-in">
      <!-- High-Fidelity Shimmer Skeleton Loading State -->
      <DashboardSkeleton v-if="isInitialLoading" :is-mobile="isMobile" key="skeleton" />

      <!-- Loaded Dashboard Content -->
      <div v-else class="dashboard-loaded-container" key="content">
        <!-- Unified Full Dashboard Layout (Responsive Desktop, Tablet & Mobile) -->
        <div class="dashboard-main-content">
          <!-- Top Title & Action Bar -->
          <div class="header-action-row">
            <div class="title-group">
              <div class="title-with-badge">
                <h1 class="page-title">{{ $t('dashboard.title') }}</h1>
              </div>
              <p class="page-subtitle">{{ $t('dashboard.subtitle') }}</p>
            </div>
            <button class="btn-primary" @click="$emit('start-drying')" :disabled="!systemState.isSystemActive">
              <svg width="12" height="14" viewBox="0 0 12 14" fill="currentColor">
                <path d="M1.5 1.5L10.5 7L1.5 12.5V1.5Z"/>
              </svg>
              <span>{{ $t('dashboard.startNewBatch') }}</span>
            </button>
          </div>

          <!-- System Maintenance / Inactive Banner -->
          <div v-if="!systemState.isSystemActive" class="system-offline-banner">
            <div class="offline-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <div class="offline-text">
              <strong>{{ $t('dashboard.systemInactiveTitle') }}</strong>
              <p>{{ $t('dashboard.systemInactiveDesc') }}</p>
            </div>
          </div>

          <!-- Dual Connectivity Status & Control Card -->
          <ConnectionStatusCard @open-wifi-setup="isWifiSetupOpen = true" />

          <!-- Sensor Anomaly Alert Banner (AI Diagnostics) -->
          <div 
            v-if="anomalyHealth && (anomalyHealth.anomalyCount > 0 || anomalyHealth.status === 'CRITICAL')" 
            class="sensor-anomaly-banner"
            :class="anomalyHealth.status === 'CRITICAL' ? 'banner-critical' : 'banner-warning'"
          >
            <div class="anomaly-banner-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <div class="anomaly-banner-content">
              <div class="anomaly-banner-title">
                <strong>{{ anomalyHealth.anomalyCount }} {{ $t('dashboard.anomaliesDetected') }}</strong>
                <span class="anomaly-score-tag">{{ $t('dashboard.sensorIntegrity') }}: {{ anomalyHealth.healthScore }}%</span>
              </div>
              <p class="anomaly-banner-desc">
                {{ anomalyHealth.anomalies?.[0]?.description || $t('dashboard.sensorDeviationDefault') }}
                <span v-if="anomalyHealth.anomalies?.[0]?.suggestedAction" class="anomaly-solution-hint">
                  • {{ $t('dashboard.suggestedActionLabel') }}: {{ anomalyHealth.anomalies[0].suggestedAction }}
                </span>
              </p>
            </div>
            <router-link to="/operator/monitoring" class="btn-check-telemetry">
              <span>{{ $t('dashboard.checkTelemetry') }}</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </router-link>
          </div>

          <!-- 5 Stat Cards Grid -->
          <div class="stats-grid-5">
            <!-- 1. Suhu -->
            <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
              <div class="stat-card-header">
                <span class="stat-label">{{ $t('dashboard.tempInternal') }}</span>
                <div class="icon-circle icon-orange">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="stat-value-row">
                <span class="stat-number text-red">{{ isDataActive ? telemetry.tempInternal.toFixed(1) : '--' }}</span>
                <span class="stat-unit">°C</span>
                <span v-if="isDataActive" class="delta-chip">ΔT: +{{ (telemetry.tempInternal - telemetry.tempExternal).toFixed(1) }}°</span>
              </div>
              <div class="stat-trend" :class="isDataActive ? 'trend-up-green' : 'text-muted'">
                <svg v-if="isDataActive" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                  <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                  <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>{{ isDataActive ? `${$t('dashboard.externalLabel')}: ${telemetry.tempExternal.toFixed(1)}°C` : $t('dashboard.waitingEsp32') }}</span>
              </div>
            </div>

            <!-- 2. Kelembapan -->
            <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
              <div class="stat-card-header">
                <span class="stat-label">{{ $t('dashboard.humidityInternal') }}</span>
                <div class="icon-circle icon-blue">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="stat-value-row">
                <span class="stat-number text-blue">{{ isDataActive ? telemetry.humidityInternal.toFixed(0) : '--' }}</span>
                <span class="stat-unit">% RH</span>
                <span v-if="isDataActive" class="delta-chip-blue">{{ $t('dashboard.targetLabel') }}: &lt;50%</span>
              </div>
              <div class="stat-trend" :class="isDataActive ? 'trend-down-blue' : 'text-muted'">
                <svg v-if="isDataActive" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.5">
                  <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                  <polyline points="17 18 23 18 23 12"></polyline>
                </svg>
                <span>{{ isDataActive ? `${$t('dashboard.externalLabel')}: ${telemetry.humidityExternal.toFixed(0)}%` : $t('dashboard.waitingEsp32') }}</span>
              </div>
            </div>

            <!-- 3. Intensitas Radiasi Surya -->
            <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
              <div class="stat-card-header">
                <span class="stat-label">{{ $t('dashboard.solarRadiation') }}</span>
                <div class="icon-circle icon-amber">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                  </svg>
                </div>
              </div>
              <div class="stat-value-row">
                <span class="stat-number text-amber">{{ isDataActive ? telemetry.solarRadiation.toFixed(0) : '--' }}</span>
                <span class="stat-unit">W/m²</span>
                <span v-if="isDataActive" class="delta-chip-amber">
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
                  <span>{{ telemetry.solarRadiation > 500 ? $t('dashboard.sunnyNatural') : $t('dashboard.dimCloudy') }}</span>
                </span>
              </div>
              <div class="stat-trend">
                <span class="badge-pill" :class="isDataActive ? 'bg-amber-subtle text-amber-dark' : 'bg-gray-subtle text-muted'">
                  <span>{{ isDataActive ? $t('dashboard.statusSafe') : $t('dashboard.standbyUpper') }}</span>
                </span>
              </div>
            </div>

            <!-- 4. Kadar Air Gabah -->
            <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
              <div class="stat-card-header">
                <span class="stat-label">{{ $t('dashboard.grainMoisture') }}</span>
                <div class="icon-circle icon-purple-subtle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E22CE" stroke-width="2.2">
                    <circle cx="12" cy="12" r="9"></circle>
                    <polyline points="12 7 12 12 15 15"></polyline>
                  </svg>
                </div>
              </div>
              <div class="stat-value-row">
                <span class="stat-number text-purple">{{ activeBatch && isDataActive ? telemetry.grainMoisture.toFixed(1) : '--' }}</span>
                <span class="stat-unit">%</span>
              </div>
              <div class="progress-bar-wrapper">
                <div class="progress-track">
                  <div class="progress-fill" :style="{ width: (isDataActive ? dryingProgressPercent : 0) + '%' }"></div>
                </div>
                <div class="dash-eta-label-row">
                  <span class="progress-percentage-label">
                    {{ isDataActive ? (activeBatch ? `${$t('dashboard.targetLabel')}: ${activeBatch.targetMoisturePercent || 12.0}% (${dryingProgressPercent}%)` : $t('dashboard.noActiveBatchProgress')) : $t('dashboard.waitingEsp32Connection') }}
                  </span>
                  <span v-if="activeBatch && isDataActive" class="dash-ai-eta-pill" :title="`${$t('dashboard.estimatedCompletion')}: ${predictionData?.estimatedCompletionTimeOnly || '--:--'}`">
                    <span class="ai-spark-dot"></span>
                    ETA: {{ predictionData?.estimatedCompletionTimeOnly || $t('dashboard.etaHoursDefault') }}
                  </span>
                </div>
              </div>
            </div>

            <!-- 5. Kipas & Aktuator -->
            <div class="stat-card" :class="{ 'stat-card-standby': !isDataActive }">
              <div class="stat-card-header">
                <span class="stat-label">{{ $t('dashboard.auxHeater') }}</span>
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
                  {{ actuators.auxHeaterStatus ? $t('dashboard.heaterOn') : $t('dashboard.heaterOff') }}
                </span>
              </div>
              <div class="stat-footer-badge">
                <span class="status-meta-text">{{ $t('dashboard.actuatorStatus') }}:</span>
                <span class="badge-pill" :class="actuators.exhaustFanStatus ? 'bg-green-subtle text-green-dark' : 'bg-gray-subtle text-muted'">
                  {{ actuators.controlMode === 'AUTOMATIC' ? $t('dashboard.automatic') : $t('dashboard.manual') }}
                </span>
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
                  <h2 class="master-chart-heading">{{ $t('dashboard.chartHeading') }}</h2>
                  <p class="master-chart-sub">{{ $t('dashboard.chartSub') }}</p>
                </div>
              </div>

              <div class="time-window-selector">
                <span class="live-points-indicator">
                  <span class="live-blink-dot"></span> {{ liveHistoryPoints.length }} {{ $t('dashboard.liveDataPoints') }}
                </span>
              </div>
            </div>

            <!-- Series Toggles -->
            <div class="series-toggle-toolbar">
              <button 
                type="button" 
                class="series-btn" 
                :class="{ active: visibleSeries.tempInternal }"
                @click="visibleSeries.tempInternal = !visibleSeries.tempInternal"
              >
                <span class="legend-color-dot" style="background: #E11D48;"></span>
                <span>{{ $t('dashboard.seriesTempInternal') }}</span>
              </button>

              <button 
                type="button" 
                class="series-btn" 
                :class="{ active: visibleSeries.tempExternal }"
                @click="visibleSeries.tempExternal = !visibleSeries.tempExternal"
              >
                <span class="legend-color-dot" style="background: #0D9488;"></span>
                <span>{{ $t('dashboard.seriesTempExternal') }}</span>
              </button>

              <button 
                type="button" 
                class="series-btn" 
                :class="{ active: visibleSeries.humidity }"
                @click="visibleSeries.humidity = !visibleSeries.humidity"
              >
                <span class="legend-color-dot" style="background: #0284C7;"></span>
                <span>{{ $t('dashboard.seriesHumidity') }}</span>
              </button>

              <button 
                type="button" 
                class="series-btn" 
                :class="{ active: visibleSeries.grainMoisture }"
                @click="visibleSeries.grainMoisture = !visibleSeries.grainMoisture"
              >
                <span class="legend-color-dot" style="background: #9333EA;"></span>
                <span>{{ $t('dashboard.seriesGrainMoisture') }}</span>
              </button>

              <button 
                type="button" 
                class="series-btn" 
                :class="{ active: visibleSeries.solarRadiation }"
                @click="visibleSeries.solarRadiation = !visibleSeries.solarRadiation"
              >
                <span class="legend-color-dot" style="background: #F59E0B;"></span>
                <span>{{ $t('dashboard.seriesSolarRadiation') }}</span>
              </button>
            </div>

            <!-- Master SVG Canvas -->
            <div 
              class="master-svg-chart-wrapper"
              @mousemove="handleChartMouseMove"
              @mouseleave="handleChartMouseLeave"
            >
              <svg viewBox="0 0 1000 300" class="master-svg" preserveAspectRatio="none">
                <defs>
                  <linearGradient id="dashTempGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#E11D48" stop-opacity="0.22"/>
                    <stop offset="100%" stop-color="#E11D48" stop-opacity="0.0"/>
                  </linearGradient>
                  <linearGradient id="dashMoistureGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#9333EA" stop-opacity="0.20"/>
                    <stop offset="100%" stop-color="#9333EA" stop-opacity="0.0"/>
                  </linearGradient>
                  <linearGradient id="dashSolarGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.15"/>
                    <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.0"/>
                  </linearGradient>
                </defs>

                <!-- Safety Zone Band (40°C - 50°C) -->
                <rect x="60" y="130" width="880" height="24" fill="#10B981" fill-opacity="0.08" />
                <text x="65" y="145" font-size="9.5" fill="#059669" font-weight="600">{{ $t('dashboard.idealTempZone') }}</text>

                <!-- Critical Threshold (55°C) -->
                <line x1="60" y1="118" x2="940" y2="118" stroke="#EF4444" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.6"/>
                <text x="830" y="113" font-size="9" fill="#DC2626" font-weight="600">{{ $t('dashboard.criticalThreshold') }}</text>

                <!-- Target Moisture (12.0%) -->
                <line x1="60" y1="221" x2="940" y2="221" stroke="#9333EA" stroke-width="1.5" stroke-dasharray="3 3" opacity="0.6"/>
                <text x="820" y="216" font-size="9" fill="#7E22CE" font-weight="600">{{ $t('dashboard.storageTarget') }}</text>

                <!-- Grid Lines -->
                <g v-for="g in yGridLines" :key="g.val">
                  <line x1="60" :y1="g.y" x2="940" :y2="g.y" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="2 2"/>
                  <text x="20" :y="g.y + 4" font-size="10.5" fill="#64748B" font-weight="600">{{ g.val }}</text>
                  <text x="948" :y="g.y + 4" font-size="10.5" fill="#D97706" font-weight="600">{{ g.solarVal }}</text>
                </g>

                <text x="948" y="14" font-size="9" fill="#D97706" font-weight="700">W/m²</text>
                <text x="20" y="14" font-size="9" fill="#64748B" font-weight="700">°C / %</text>

                <!-- Area Fills -->
                <path v-if="visibleSeries.tempInternal && tempAreaPath" :d="tempAreaPath" fill="url(#dashTempGrad)" />
                <path v-if="visibleSeries.grainMoisture && moistureAreaPath" :d="moistureAreaPath" fill="url(#dashMoistureGrad)" />
                <path v-if="visibleSeries.solarRadiation && solarAreaPath" :d="solarAreaPath" fill="url(#dashSolarGrad)" />

                <!-- Curves -->
                <path v-if="visibleSeries.solarRadiation && solarCurvePath" :d="solarCurvePath" fill="none" stroke="#F59E0B" stroke-width="2.5" stroke-linecap="round"/>
                <path v-if="visibleSeries.humidity && humidityCurvePath" :d="humidityCurvePath" fill="none" stroke="#0284C7" stroke-width="2.5" stroke-linecap="round"/>
                <path v-if="visibleSeries.tempExternal && tempExtCurvePath" :d="tempExtCurvePath" fill="none" stroke="#0D9488" stroke-width="2" stroke-dasharray="4 2" stroke-linecap="round"/>
                <path v-if="visibleSeries.grainMoisture && moistureCurvePath" :d="moistureCurvePath" fill="none" stroke="#9333EA" stroke-width="3" stroke-linecap="round"/>
                <path v-if="visibleSeries.tempInternal && tempCurvePath" :d="tempCurvePath" fill="none" stroke="#E11D48" stroke-width="3.5" stroke-linecap="round"/>

                <!-- X-Axis Line -->
                <line x1="60" y1="250" x2="940" y2="250" stroke="#94A3B8" stroke-width="1.5"/>

                <!-- X-Axis Labels -->
                <g v-for="(lbl, idx) in xTimeLabels" :key="idx">
                  <line :x1="lbl.x" y1="250" :x2="lbl.x" y2="256" stroke="#64748B" stroke-width="1.5"/>
                  <text :x="lbl.x" y="272" font-size="10" fill="#64748B" text-anchor="middle" font-weight="500">{{ lbl.time }}</text>
                </g>

                <!-- Crosshair -->
                <g v-if="hoverData">
                  <line :x1="hoverData.svgX" y1="20" :x2="hoverData.svgX" y2="250" stroke="#1E293B" stroke-width="1.5" stroke-dasharray="3 3"/>
                  <circle v-if="visibleSeries.tempInternal" :cx="hoverData.svgX" :cy="hoverData.yTemp" r="5" fill="#E11D48" stroke="#FFFFFF" stroke-width="2"/>
                  <circle v-if="visibleSeries.grainMoisture" :cx="hoverData.svgX" :cy="hoverData.yMoisture" r="5" fill="#9333EA" stroke="#FFFFFF" stroke-width="2"/>
                  <circle v-if="visibleSeries.humidity" :cx="hoverData.svgX" :cy="hoverData.yHum" r="4.5" fill="#0284C7" stroke="#FFFFFF" stroke-width="2"/>
                  <circle v-if="visibleSeries.solarRadiation" :cx="hoverData.svgX" :cy="hoverData.ySolar" r="4.5" fill="#F59E0B" stroke="#FFFFFF" stroke-width="2"/>
                </g>
              </svg>

              <!-- Floating Tooltip -->
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
                    <span class="tt-label">{{ $t('dashboard.tooltipTempInternal') }}:</span>
                    <span class="tt-val">{{ hoverData.item.tempInternal.toFixed(1) }}°C</span>
                  </div>
                  <div class="tooltip-item" v-if="visibleSeries.tempExternal">
                    <span class="tt-dot" style="background: #0D9488;"></span>
                    <span class="tt-label">{{ $t('dashboard.tooltipTempExternal') }}:</span>
                    <span class="tt-val">{{ hoverData.item.tempExternal.toFixed(1) }}°C</span>
                  </div>
                  <div class="tooltip-item" v-if="visibleSeries.humidity">
                    <span class="tt-dot" style="background: #0284C7;"></span>
                    <span class="tt-label">{{ $t('dashboard.tooltipHumidity') }}:</span>
                    <span class="tt-val">{{ hoverData.item.humidityInternal.toFixed(0) }}% RH</span>
                  </div>
                  <div class="tooltip-item" v-if="visibleSeries.grainMoisture">
                    <span class="tt-dot" style="background: #9333EA;"></span>
                    <span class="tt-label">{{ $t('dashboard.tooltipGrainMoisture') }}:</span>
                    <span class="tt-val font-bold">{{ hoverData.item.grainMoisture.toFixed(1) }}%</span>
                  </div>
                  <div class="tooltip-item" v-if="visibleSeries.solarRadiation">
                    <span class="tt-dot" style="background: #F59E0B;"></span>
                    <span class="tt-label">{{ $t('dashboard.tooltipSolarRadiation') }}:</span>
                    <span class="tt-val">{{ hoverData.item.solarRadiation.toFixed(0) }} W/m²</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4 Quick Analytics Strip -->
            <div class="chart-quick-analytics-strip">
              <div class="strip-item">
                <span class="strip-lbl">{{ $t('dashboard.highestRecordedTemp') }}</span>
                <span class="strip-val text-red">{{ maxRecordedTemp }}°C</span>
              </div>
              <div class="strip-item">
                <span class="strip-lbl">{{ $t('dashboard.avgRecordedTemp') }}</span>
                <span class="strip-val text-orange">{{ avgRecordedTemp }}°C</span>
              </div>
              <div class="strip-item">
                <span class="strip-lbl">{{ $t('dashboard.peakDifferential') }}</span>
                <span class="strip-val text-green-dark">+{{ maxRecordedDeltaT }}°C</span>
              </div>
              <div class="strip-item">
                <span class="strip-lbl">{{ $t('dashboard.peakSolar') }}</span>
                <span class="strip-val text-amber">{{ maxRecordedSolar }} W/m²</span>
              </div>
            </div>
          </div>

          <!-- Status Sistem Card -->
          <div class="system-status-card">
            <div class="system-status-left">
              <div class="icon-circle-lg" :class="activeBatch ? 'icon-green-light' : 'icon-gray-light'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" :stroke="activeBatch ? '#0D631B' : '#707A6C'" stroke-width="2.2">
                  <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                  <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                  <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                  <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
              </div>
              <div class="system-info">
                <h2 class="system-title">{{ activeBatch ? $t('common.systemActive') : $t('dashboard.systemStandby') }}</h2>
                <div class="badge-group">
                  <span class="badge-pill" :class="isConnected ? 'bg-green-subtle text-green-dark' : 'bg-gray-subtle text-muted'">
                    <span class="dot-green" :style="{ background: isConnected ? '#0D631B' : '#707A6C' }"></span> {{ isConnected ? $t('common.online') : $t('common.offline') }}
                  </span>
                  <span class="badge-pill bg-blue-subtle text-blue">
                    {{ actuators.controlMode === 'AUTOMATIC' ? $t('dashboard.autoMode') : $t('dashboard.manualMode') }}
                  </span>
                  <span v-if="activeBatch" class="badge-pill bg-orange-subtle text-orange">
                    {{ activeBatch.batchCode }} ({{ activeBatch.cropVariety }})
                  </span>
                  <span v-if="activeBatch" class="badge-pill bg-purple-subtle text-purple">
                    ETA AI: {{ predictionData?.estimatedCompletionTimeOnly || '--:-- WIB' }} (~{{ predictionData?.formattedRemaining || $t('dashboard.etaHoursDefault') }})
                  </span>
                  <span v-else class="badge-pill bg-gray-subtle text-muted">
                    {{ $t('dashboard.noActiveBatchStatus') }}
                  </span>
                </div>
              </div>
            </div>
            <div class="system-status-right">
              <span class="meta-label">IoT Ingestion & MySQL</span>
              <span class="meta-time">{{ $t('common.operator') }}: {{ activeBatch?.operatorName || activeBatch?.operator?.name || $t('dashboard.defaultOperator') }}</span>
            </div>
          </div>

          <!-- Aktivitas Terkini Card -->
          <div class="activity-card">
            <h2 class="activity-heading">{{ $t('dashboard.recentAlerts') }}</h2>
            
            <div class="timeline-container">
              <div v-for="(alert, idx) in recentAlerts" :key="alert.id || idx" class="timeline-item">
                <div class="timeline-marker">
                  <div class="marker-circle" :class="alert.type === 'DANGER' ? 'marker-red' : alert.type === 'WARNING' ? 'marker-orange' : 'marker-green'">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                      <circle cx="12" cy="12" r="10"/>
                    </svg>
                  </div>
                  <div v-if="idx < recentAlerts.length - 1" class="marker-line"></div>
                </div>
                <div class="timeline-content">
                  <div class="timeline-title-row">
                    <h3 class="timeline-title">{{ alert.title }}</h3>
                  </div>
                  <p class="timeline-sub">{{ alert.message }}</p>
                  <span class="timeline-time">{{ formatAlertTime(alert.createdAt) }}</span>
                </div>
              </div>
              <div v-if="recentAlerts.length === 0" class="timeline-empty">
                <p style="color: #707A6C; font-size: 13px;">{{ $t('dashboard.noRecentAlerts') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Wi-Fi & MQTT Setup Modal via Bluetooth BLE -->
    <DeviceSetupBleModal :is-open="isWifiSetupOpen" @close="isWifiSetupOpen = false" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { t } from '../../i18n'
import { telemetryService } from '../../services/telemetryService'
import { dashboardService } from '../../services/dashboardService'
import { batchService } from '../../services/batchService'
import { alertService } from '../../services/alertService'
import { anomalyService } from '../../services/anomalyService'
import { systemState } from '../../services/settingsService'
import connectionManager from '../../services/connectionManager'
import ConnectionStatusCard from '../../components/ConnectionStatusCard.vue'
import DeviceSetupBleModal from '../../components/DeviceSetupBleModal.vue'
import DashboardSkeleton from '../../components/DashboardSkeleton.vue'

const isInitialLoading = ref(true)
const predictionData = ref(null)
const anomalyHealth = ref(null)

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

defineEmits(['start-drying'])

const isWifiSetupOpen = ref(false)
const isConnected = ref(true)
const telemetry = ref({
  tempInternal: 0,
  tempExternal: 0,
  humidityInternal: 0,
  humidityExternal: 0,
  grainMoisture: 0,
  solarRadiation: 0,
  weightCurrentKg: 0,
  hasData: false,
})

const actuators = ref({
  exhaustFanStatus: false,
  exhaustFanSpeed: 0,
  blowerFanStatus: false,
  blowerFanSpeed: 0,
  auxHeaterStatus: false,
  auxHeaterLevel: 0,
  uvSterilizerStatus: false,
  roofVentStatus: false,
  controlMode: 'AUTOMATIC',
})

const activeBatch = ref(null)
const recentAlerts = ref([])
const statsSummary = ref(null)

// Chart Series Toggles
const visibleSeries = reactive({
  tempInternal: true,
  tempExternal: true,
  humidity: true,
  grainMoisture: true,
  solarRadiation: true,
})

const hoverData = ref(null)
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

  if (liveHistoryPoints.value.length > 30) {
    liveHistoryPoints.value.shift()
  }
}

function seedInitialHistory() {
  const initial = []
  const count = 15
  const baseTime = new Date(Date.now() - count * 60000)
  for (let i = 0; i < count; i++) {
    const t = new Date(baseTime.getTime() + i * 60000)
    const progress = i / (count - 1)
    const tempIn = 39.0 + progress * 4.2 + (Math.sin(i) * 0.4)
    const tempExt = 28.5 + progress * 2.0
    const humIn = 62.0 - progress * 14.0 + (Math.sin(i * 1.5) * 1.5)
    const humExt = 74.0 - progress * 6.0
    const solar = 640.0 + progress * 150.0 + (Math.sin(i) * 25)
    const moisture = 18.5 - progress * 4.2
    const weight = 130.0 - progress * 4.5

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

const isDataActive = computed(() => {
  if (connectionManager.state.sourceMode === 'simulation') return true;
  return connectionManager.state.isHardwareActive && telemetry.value.hasData;
})

const dryingProgressPercent = computed(() => {
  if (!activeBatch.value) return 0
  const initial = activeBatch.value.initialMoisturePercent || 25.0
  const target = activeBatch.value.targetMoisturePercent || 12.0
  const current = (isDataActive.value ? telemetry.value.grainMoisture : null) ?? activeBatch.value.currentMoisturePercent ?? initial
  if (initial <= target) return 100
  const progress = ((initial - current) / (initial - target)) * 100
  return Math.min(100, Math.max(0, Math.round(progress)))
})

const maxRecordedTemp = computed(() => {
  if (liveHistoryPoints.value.length === 0) return '45.5'
  return Math.max(...liveHistoryPoints.value.map(p => p.tempInternal)).toFixed(1)
})

const avgRecordedTemp = computed(() => {
  if (liveHistoryPoints.value.length === 0) return '42.0'
  const avg = liveHistoryPoints.value.reduce((a, b) => a + b.tempInternal, 0) / liveHistoryPoints.value.length
  return avg.toFixed(1)
})

const maxRecordedDeltaT = computed(() => {
  if (liveHistoryPoints.value.length === 0) return '14.2'
  const max = Math.max(...liveHistoryPoints.value.map(p => p.tempInternal - p.tempExternal))
  return max.toFixed(1)
})

const maxRecordedSolar = computed(() => {
  if (liveHistoryPoints.value.length === 0) return '850'
  return Math.max(...liveHistoryPoints.value.map(p => p.solarRadiation)).toFixed(0)
})

// SVG Helpers
function mapPointsToSvg(values, yMin, yMax, width = 880, height = 230, xOffset = 60, yOffset = 20) {
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

function buildSvgArea(coords, baselineY = 250) {
  if (!coords || coords.length < 2) return ''
  const first = coords[0]
  const last = coords[coords.length - 1]
  const lineStr = coords.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.y}`).join(' ')
  return `${lineStr} L ${last.x} ${baselineY} L ${first.x} ${baselineY} Z`
}

const tempCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.tempInternal), 0, 100, 880, 230, 60, 20))
const tempExtCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.tempExternal), 0, 100, 880, 230, 60, 20))
const humCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.humidityInternal), 0, 100, 880, 230, 60, 20))
const moistureCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.grainMoisture), 0, 100, 880, 230, 60, 20))
const solarCoords = computed(() => mapPointsToSvg(liveHistoryPoints.value.map(d => d.solarRadiation), 0, 1000, 880, 230, 60, 20))

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
  { val: 80, unit: '', y: 66, solarVal: '800' },
  { val: 60, unit: '', y: 112, solarVal: '600' },
  { val: 40, unit: '', y: 158, solarVal: '400' },
  { val: 20, unit: '', y: 204, solarVal: '200' },
  { val: 0, unit: '', y: 250, solarVal: '0' },
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
    yTemp: Math.round(20 + (1 - normTemp) * 230),
    yHum: Math.round(20 + (1 - normHum) * 230),
    yMoisture: Math.round(20 + (1 - normMoist) * 230),
    ySolar: Math.round(20 + (1 - normSolar) * 230),
  }
}

function handleChartMouseLeave() {
  hoverData.value = null
}

function formatAlertTime(dateStr) {
  if (!dateStr) return t('common.justNow')
  try {
    const d = new Date(dateStr)
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  } catch {
    return t('common.justNow')
  }
}

async function loadInitialData(isFirstTime = false) {
  try {
    const statsRes = await dashboardService.getStats()
    if (statsRes) {
      if (statsRes.activeBatch) {
        activeBatch.value = statsRes.activeBatch
      } else {
        activeBatch.value = null
      }
      if (statsRes.summary) {
        statsSummary.value = statsRes.summary
      }
      if (statsRes.currentTelemetry && connectionManager.state.sourceMode === 'simulation') {
        telemetry.value = { ...telemetry.value, ...statsRes.currentTelemetry, hasData: true }
        appendTelemetryToHistory(statsRes.currentTelemetry)
      }
      if (statsRes.actuators) {
        actuators.value = { ...actuators.value, ...statsRes.actuators }
      }
    }
  } catch (err) {
    console.warn('Dashboard stats fallback:', err.message)
  }

  try {
    const res = await telemetryService.getCurrent()
    if (res.telemetry && res.telemetry.hasData && connectionManager.state.sourceMode === 'simulation') {
      telemetry.value = { ...telemetry.value, ...res.telemetry, hasData: true }
      appendTelemetryToHistory(res.telemetry)
    }
    if (res.actuators) {
      actuators.value = { ...actuators.value, ...res.actuators }
    }
  } catch (err) {
    console.warn('Could not fetch telemetry:', err.message)
  }

  try {
    const alertsRes = await alertService.getAll()
    const list = Array.isArray(alertsRes) ? alertsRes : alertsRes?.alerts || []
    recentAlerts.value = list.slice(0, 5)
  } catch (err) {
    console.warn('Could not fetch alerts:', err.message)
  }

  try {
    const predRes = await batchService.getActiveDryingPrediction()
    if (predRes && predRes.success) {
      predictionData.value = predRes
    }
  } catch (err) {
    // silent prediction fallback
  }

  try {
    const anomRes = await anomalyService.getHealthStatus()
    if (anomRes && anomRes.success) {
      anomalyHealth.value = anomRes.data
    }
  } catch (err) {
    // silent anomaly health fallback
  } finally {
    if (isFirstTime) {
      // Elegant minimal duration for smooth skeleton reveal
      setTimeout(() => {
        isInitialLoading.value = false
      }, 400)
    }
  }
}

let dashboardPollTimer = null

onMounted(() => {
  seedInitialHistory()
  loadInitialData(true)

  dashboardPollTimer = setInterval(() => loadInitialData(false), 5000)

  connectionManager.on('telemetry_live', (data) => {
    if (data && data.hasData) {
      telemetry.value = { ...telemetry.value, ...data, hasData: true }
      appendTelemetryToHistory(data)
    } else {
      telemetry.value = {
        tempInternal: 0,
        tempExternal: 0,
        humidityInternal: 0,
        humidityExternal: 0,
        grainMoisture: 0,
        solarRadiation: 0,
        weightCurrentKg: 0,
        hasData: false,
      }
    }
    isConnected.value = true
  })

  connectionManager.on('actuators_update', (data) => {
    if (data) actuators.value = { ...actuators.value, ...data }
  })

  connectionManager.on('alert_new', (newAlert) => {
    recentAlerts.value.unshift(newAlert)
    if (recentAlerts.value.length > 5) recentAlerts.value.pop()
  })

  connectionManager.connect()
})

onUnmounted(() => {
  if (dashboardPollTimer) clearInterval(dashboardPollTimer)
  connectionManager.off('telemetry_live')
  connectionManager.off('actuators_update')
  connectionManager.off('alert_new')
})
</script>

<style scoped>
.dashboard-page {
  width: 100%;
  min-height: 100%;
}

.dashboard-main-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1280px;
  margin: 0 auto;
  box-sizing: border-box;
  width: 100%;
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

.title-with-badge {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pro-analytics-tag {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 4px 10px;
  background: #E8F5E9;
  color: #0D631B;
  border-radius: 20px;
  border: 1px solid #A5D6A7;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--color-text-title);
  letter-spacing: -0.5px;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
}

.system-offline-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 12px;
  padding: 16px 20px;
}

.offline-icon {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: #FEE2E2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.offline-text strong {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: #DC2626;
  margin-bottom: 2px;
}

.offline-text p {
  font-size: 13px;
  color: #7F1D1D;
  margin: 0;
  line-height: 1.4;
}

/* 5 Stat Cards Grid */
.stats-grid-5 {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
}

.stat-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  min-width: 0;
  overflow: hidden;
  box-sizing: border-box;
}

:global(.dark-theme) .stat-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.stat-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
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
  margin: 8px 0;
}

.stat-number {
  font-size: 30px;
  font-weight: 800;
  line-height: 1.1;
}

.stat-unit {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-muted);
  margin-right: 4px;
}

.delta-chip, .delta-chip-blue, .delta-chip-amber {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
  margin-left: auto;
}

.delta-chip { background: #FFE4E6; color: #E11D48; }
.delta-chip-blue { background: #E0F2FE; color: #0284C7; }
.delta-chip-amber { background: #FEF3C7; color: #D97706; }

.stat-trend {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
}

.progress-bar-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 6px;
}

.progress-track {
  width: 100%;
  height: 8px;
  background: var(--color-bg-light);
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid rgba(203, 213, 225, 0.4);
}

.progress-fill {
  height: 100%;
  background: #0D631B;
  border-radius: 10px;
  transition: width 0.4s ease;
}

.progress-percentage-label {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.stat-footer-badge {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
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

@keyframes pulseDot {
  0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(13, 99, 27, 0.7); }
  70% { transform: scale(1.1); box-shadow: 0 0 0 7px rgba(13, 99, 27, 0); }
  100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(13, 99, 27, 0); }
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
  height: 300px;
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

.chart-quick-analytics-strip {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  background: var(--color-bg-light);
  border-radius: 12px;
  padding: 14px 18px;
  border: 1px solid rgba(203, 213, 225, 0.4);
}

:global(.dark-theme) .chart-quick-analytics-strip {
  border-color: rgba(30, 58, 43, 0.5);
}

.strip-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.strip-lbl {
  font-size: 11px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.strip-val {
  font-size: 17px;
  font-weight: 800;
}

/* System Status & Activity */
.system-status-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .system-status-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.system-status-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.icon-circle-lg {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-green-light { background: #E8F5E9; }
.icon-gray-light { background: #F1F5F9; }

.system-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 6px;
}

.badge-group {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.system-status-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.meta-label {
  font-size: 12px;
  color: var(--color-text-muted);
}

.meta-time {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.activity-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .activity-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.activity-heading {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 20px;
}

.timeline-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.timeline-item {
  display: flex;
  gap: 14px;
}

.timeline-marker {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.marker-circle {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.marker-green { background: #E8F5E9; color: #0D631B; }
.marker-orange { background: #FFEDD5; color: #EA580C; }
.marker-red { background: #FEE2E2; color: #DC2626; }

.marker-line {
  width: 2px;
  flex: 1;
  background: rgba(203, 213, 225, 0.4);
  margin-top: 4px;
}

.timeline-content {
  flex: 1;
}

.timeline-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title);
}

.timeline-sub {
  font-size: 13px;
  color: var(--color-text-muted);
  margin: 2px 0 4px;
}

.timeline-time {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

/* Mobile Responsive Rules for Dashboard */
@media (max-width: 768px) {
  .dashboard-main-content {
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
    font-size: 13px;
  }

  .btn-primary {
    width: 100%;
    justify-content: center;
    padding: 12px 16px;
    font-size: 14px;
  }

  .master-chart-card {
    padding: 14px;
    border-radius: 14px;
  }

  .master-chart-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .master-chart-heading {
    font-size: 16px;
  }

  .series-toggle-toolbar {
    flex-wrap: wrap;
    gap: 6px;
  }

  .series-btn {
    padding: 5px 8px;
    font-size: 11px;
  }

  .chart-quick-analytics-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    padding: 10px;
  }

  .system-status-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 14px;
    padding: 16px;
  }

  .system-status-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    width: 100%;
  }

  .system-status-right {
    width: 100%;
    border-top: 1px solid rgba(203, 213, 225, 0.4);
    padding-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  :global(.dark-theme) .system-status-right {
    border-top-color: #1E4E61;
  }

  .activity-card {
    padding: 16px;
  }

  .sensor-anomaly-banner {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .btn-check-telemetry {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 580px) {
  .stats-grid-5 {
    grid-template-columns: 1fr;
    gap: 12px;
  }
}

/* Global utility */
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

.icon-orange { background: #FFEDD5; }
.icon-blue { background: #E0F2FE; }
.icon-amber { background: #FEF3C7; }
.icon-purple-subtle { background: #F3E8FF; }
.icon-green-subtle { background: #E8F5E9; }

.text-red { color: #E11D48; }
.text-blue { color: #0284C7; }
.text-amber { color: #D97706; }
.text-purple { color: #9333EA; }
.text-orange { color: #EA580C; }
.text-green-dark { color: #0D631B; }

.status-chip {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}

.chip-on { background: #FEE2E2; color: #DC2626; }
.chip-off { background: #F1F5F9; color: #64748B; }

.bg-amber-subtle { background: #FEF3C7; }
.text-amber-dark { color: #B45309; }
.bg-green-subtle { background: #E8F5E9; }
.bg-blue-subtle { background: #E0F2FE; }
.bg-orange-subtle { background: #FFEDD5; }
.bg-gray-subtle { background: #F1F5F9; }

.dot-green {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

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

@media (max-width: 1280px) {
  .stats-grid-5 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .stats-grid-5 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .chart-quick-analytics-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

/* Dashboard Skeleton / Loaded Fade Transition */
.fade-dashboard-enter-active,
/* Dashboard AI ETA Pill Styling */
.dash-eta-label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 6px;
  margin-top: 4px;
}

.dash-ai-eta-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #F3E8FF;
  color: #7E22CE;
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 12px;
  border: 1px solid #E9D5FF;
  white-space: nowrap;
}

.ai-spark-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #9333EA;
  box-shadow: 0 0 6px #9333EA;
  animation: pulse-dot 1.8s infinite ease-in-out;
}

@keyframes pulse-dot {
  0%, 100% { transform: scale(1); opacity: 0.9; }
  50% { transform: scale(1.4); opacity: 1; }
}

/* ==========================================================================
   Sensor Anomaly Alert Banner
   ========================================================================== */
.sensor-anomaly-banner {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 18px;
  border-radius: 14px;
  margin-bottom: 18px;
  border-width: 1px;
  border-style: solid;
  animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.sensor-anomaly-banner.banner-warning {
  background: linear-gradient(135deg, rgba(217, 119, 6, 0.1) 0%, rgba(217, 119, 6, 0.04) 100%);
  border-color: rgba(217, 119, 6, 0.35);
}

.sensor-anomaly-banner.banner-critical {
  background: linear-gradient(135deg, rgba(186, 26, 26, 0.12) 0%, rgba(186, 26, 26, 0.04) 100%);
  border-color: rgba(186, 26, 26, 0.4);
}

:global(.dark-theme) .sensor-anomaly-banner.banner-warning {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(15, 23, 42, 0.7) 100%);
  border-color: rgba(245, 158, 11, 0.4);
}

:global(.dark-theme) .sensor-anomaly-banner.banner-critical {
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.18) 0%, rgba(15, 23, 42, 0.7) 100%);
  border-color: rgba(239, 68, 68, 0.45);
}

.anomaly-banner-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.banner-warning .anomaly-banner-icon {
  background: rgba(217, 119, 6, 0.18);
  color: #D97706;
}

.banner-critical .anomaly-banner-icon {
  background: rgba(186, 26, 26, 0.18);
  color: #BA1A1A;
}

:global(.dark-theme) .banner-warning .anomaly-banner-icon {
  background: rgba(245, 158, 11, 0.25);
  color: #FBBF24;
}

:global(.dark-theme) .banner-critical .anomaly-banner-icon {
  background: rgba(239, 68, 68, 0.25);
  color: #F87171;
}

.anomaly-banner-content {
  flex: 1;
  min-width: 0;
}

.anomaly-banner-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}

.anomaly-banner-title strong {
  font-size: 13.5px;
  color: #0F172A;
}

:global(.dark-theme) .anomaly-banner-title strong {
  color: #F8FAFC;
}

.anomaly-score-tag {
  font-size: 10.5px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.06);
  color: #475569;
}

:global(.dark-theme) .anomaly-score-tag {
  background: rgba(255, 255, 255, 0.1);
  color: #CBD5E1;
}

.anomaly-banner-desc {
  font-size: 12px;
  color: #475569;
  margin: 0;
  line-height: 1.4;
}

:global(.dark-theme) .anomaly-banner-desc {
  color: #94A3B8;
}

.anomaly-solution-hint {
  color: #0D631B;
  font-weight: 600;
  margin-left: 4px;
}

:global(.dark-theme) .anomaly-solution-hint {
  color: #34D399;
}

.btn-check-telemetry {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 7px 12px;
  border-radius: 8px;
  background: #0F172A;
  color: #FFFFFF;
  font-size: 11.5px;
  font-weight: 600;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.btn-check-telemetry:hover {
  background: #1E293B;
  transform: translateY(-1px);
}

:global(.dark-theme) .btn-check-telemetry {
  background: #334155;
  color: #F8FAFC;
}

:global(.dark-theme) .btn-check-telemetry:hover {
  background: #475569;
}
</style>
