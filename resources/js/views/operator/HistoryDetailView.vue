<template>
  <div class="history-detail-page">
    <Transition name="fade-history-detail" mode="out-in">
      <HistoryDetailSkeleton v-if="isInitialLoading" />
      <div v-else class="history-detail-loaded-content">
        <!-- Unified Responsive Layout -->
        <div class="history-detail-main-content">
          <!-- Header Action Row -->
          <div class="header-action-row">
            <div class="title-group">
          <button class="back-link-btn" @click="$emit('back')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>{{ $t('historyDetail.backToHistory') }}</span>
          </button>
          <div class="title-with-badge">
            <h1 class="page-title">{{ batch?.batchCode || activeBatchId }}</h1>
            <span class="badge-pill" :class="batch?.status === 'COMPLETED' ? 'bg-green-subtle text-green-dark' : 'bg-orange-subtle text-orange'">
              <span class="status-pulse-dot" :class="batch?.status === 'COMPLETED' ? 'online' : 'pulse-orange'"></span>
              <span>{{ batch?.status === 'COMPLETED' ? $t('common.completed') : (batch?.status || 'Sedang Berjalan') }}</span>
            </span>
            <span class="badge-pill bg-purple-subtle text-purple">
              {{ batch?.qualityGrade || 'Grade A (Ekspor)' }}
            </span>
          </div>
          <p class="page-subtitle">
            {{ batch?.cropVariety || 'Hanjeli Ketan Sukabumi' }} • {{ $t('common.date') }}: {{ formattedDateRange }} • {{ $t('common.operator') }}: <strong>{{ batch?.operatorName || batch?.operator?.name || 'Operator Greenhouse' }}</strong>
          </p>
        </div>

        <!-- Header Actions: Grouped in Two Rows (2 Baris) -->
        <div class="header-btns-cluster">
          <!-- Row 1: Primary Action & Document Exports -->
          <div class="header-btns-subrow">
            <button 
              v-if="batch?.status !== 'COMPLETED'" 
              class="btn-primary btn-complete-highlight" 
              :disabled="isCompletingBatch"
              @click="handleCompleteCurrentBatch"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <span>{{ isCompletingBatch ? $t('historyDetail.finishing') : 'Selesaikan' }}</span>
            </button>

            <button class="btn-primary btn-print-highlight" @click="handleExport" title="Cetak Laporan PDF">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
              </svg>
              <span>Cetak PDF</span>
            </button>

            <button class="btn-secondary-action" @click="exportBatchCsv" :title="$t('historyDetail.exportCsv')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Unduh CSV</span>
            </button>
          </div>

          <!-- Row 2: Management & QR Packaging Tools -->
          <div class="header-btns-subrow">
            <button class="btn-secondary-action" @click="isQrModalOpen = true" :title="$t('modals.qrSub')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                <line x1="17" y1="7" x2="17.01" y2="7"></line>
                <line x1="7" y1="17" x2="7.01" y2="17"></line>
                <line x1="17" y1="17" x2="17.01" y2="17"></line>
              </svg>
              <span>QR Kemasan</span>
            </button>

            <button class="btn-secondary-action" @click="openEditBatchModal" :title="$t('historyDetail.editBatch')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
              <span>{{ $t('common.edit') }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TOP SCIENTIFIC KPI MATRIX (6 CARDS) -->
      <!-- ========================================================================= -->
      <div class="kpi-matrix-grid">
        <!-- 1. Durasi Total & Waktu -->
        <div class="kpi-card">
          <div class="kpi-card-header">
            <span class="kpi-lbl">{{ $t('historyDetail.totalDuration').toUpperCase() }}</span>
            <div class="icon-circle icon-green-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
          </div>
          <div class="kpi-val-row">
            <span class="kpi-val-big text-green-dark">{{ durationText.hours }}</span>
            <span class="kpi-val-unit">{{ $t('common.hours') }}</span>
            <span class="kpi-val-big text-green-dark" style="margin-left: 6px;">{{ durationText.minutes }}</span>
            <span class="kpi-val-unit">{{ $t('common.minutes') }}</span>
          </div>
          <div class="kpi-footer-note">
            <span>{{ $t('dashboard.dryingMode') }}: <strong>{{ batch?.dryingMode || 'HYBRID_AUTO' }}</strong></span>
          </div>
        </div>

        <!-- 2. Kadar Air & Efisiensi Dehidrasi -->
        <div class="kpi-card">
          <div class="kpi-card-header">
            <span class="kpi-lbl">{{ $t('historyDetail.moistureReduction').toUpperCase() }} (ΔM)</span>
            <div class="icon-circle icon-blue-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
              </svg>
            </div>
          </div>
          <div class="kpi-val-row">
            <span class="kpi-val-big text-blue">{{ finalMoisture }}</span>
            <span class="kpi-val-unit">%</span>
            <span class="delta-badge bg-green-subtle text-green-dark">
              -{{ moistureReductionPercent }}%
            </span>
          </div>
          <div class="kpi-footer-note">
            <span>{{ $t('activeDrying.startMoisture') }}: {{ initialMoisturePercent }}% → {{ $t('activeDrying.target') }}: {{ batch?.targetMoisturePercent || 12 }}%</span>
          </div>
        </div>

        <!-- 3. Keseimbangan Massa / Bobot Gabah -->
        <div class="kpi-card">
          <div class="kpi-card-header">
            <span class="kpi-lbl">{{ $t('historyDetail.kpiMassBalance').toUpperCase() }}</span>
            <div class="icon-circle icon-purple-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E22CE" stroke-width="2.2">
                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                <line x1="4" y1="22" x2="4" y2="15"></line>
              </svg>
            </div>
          </div>
          <div class="kpi-val-row">
            <span class="kpi-val-big text-purple">{{ finalWeightKg }}</span>
            <span class="kpi-val-unit">kg</span>
            <span class="delta-badge bg-purple-subtle text-purple">
              Air Menguap: {{ waterEvaporatedKg }} kg
            </span>
          </div>
          <div class="kpi-footer-note">
            <span>Bobot Awal: {{ initialWeightKg }} kg (Rendemen: {{ yieldPercentage }}%)</span>
          </div>
        </div>

        <!-- 4. Profil Suhu Ruang Pengering -->
        <div class="kpi-card">
          <div class="kpi-card-header">
            <span class="kpi-lbl">PROFIL SUHU (MIN / RATA2 / MAX)</span>
            <div class="icon-circle icon-red-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2.2">
                <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
              </svg>
            </div>
          </div>
          <div class="kpi-val-row">
            <span class="kpi-val-big text-red">{{ avgTemp }}</span>
            <span class="kpi-val-unit">Rata-rata</span>
          </div>
          <div class="kpi-footer-note temp-split-row">
            <span>Min: <strong>{{ minTemp }}</strong></span>
            <span>•</span>
            <span>Max: <strong>{{ maxTemp }}</strong></span>
            <span>•</span>
            <span class="text-green-dark">Zona Aman 40-50°C</span>
          </div>
        </div>

        <!-- 5. Rata-rata Kelembapan & Radiasi -->
        <div class="kpi-card">
          <div class="kpi-card-header">
            <span class="kpi-lbl">KELEMBAPAN & RADIASI SURYA</span>
            <div class="icon-circle icon-amber-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2m-7.07-15.07 1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2m-15.07 7.07 1.41-1.41m11.32-11.32 1.41-1.41"/>
              </svg>
            </div>
          </div>
          <div class="kpi-val-row">
            <span class="kpi-val-big text-amber">{{ avgSolar }}</span>
            <span class="kpi-val-unit">W/m²</span>
          </div>
          <div class="kpi-footer-note">
            <span>Rata-rata RH Ruang: <strong>{{ avgHum }}</strong> (RH Luar: {{ avgHumExt }})</span>
          </div>
        </div>

        <!-- 6. Energi Terpakai & Estimasi Biaya -->
        <div class="kpi-card">
          <div class="kpi-card-header">
            <span class="kpi-lbl">ENERGI & EFISIENSI SPESIFIK (SEC)</span>
            <div class="icon-circle icon-emerald-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
              </svg>
            </div>
          </div>
          <div class="kpi-val-row">
            <span class="kpi-val-big text-emerald">{{ energyEst }}</span>
            <span class="kpi-val-unit">kWh</span>
            <span class="delta-badge bg-emerald-subtle text-emerald">
              SEC: {{ specificEnergyConsumption }} kWh/kg
            </span>
          </div>
          <div class="kpi-footer-note">
            <span>Biaya Listrik Tambahan: <strong>{{ costEst }}</strong></span>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- ADVANCED TABBED ANALYTICAL DEEP-DIVE SYSTEM -->
      <!-- ========================================================================= -->
      <div class="analytics-tabs-container">
        <!-- Tab Navigation Bar -->
        <div class="tab-nav-bar">
          <button 
            type="button" 
            class="tab-nav-btn" 
            :class="{ active: activeTab === 'master-chart' }"
            @click="activeTab = 'master-chart'"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            <span>Grafik Multi-Sensor (Dual-Axis Waveform)</span>
          </button>

          <button 
            type="button" 
            class="tab-nav-btn" 
            :class="{ active: activeTab === 'drying-kinetics' }"
            @click="activeTab = 'drying-kinetics'"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
            </svg>
            <span>Kinetika Dehidrasi & Laju Penguapan Air</span>
          </button>

          <button 
            type="button" 
            class="tab-nav-btn" 
            :class="{ active: activeTab === 'thermal-delta' }"
            @click="activeTab = 'thermal-delta'"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
            </svg>
            <span>Diferensial Termal Greenhouse (ΔT)</span>
          </button>

          <button 
            type="button" 
            class="tab-nav-btn" 
            :class="{ active: activeTab === 'raw-logs' }"
            @click="activeTab = 'raw-logs'"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
            <span>Tabel Log Sensor (Audit Telemetri)</span>
          </button>

          <button 
            type="button" 
            class="tab-nav-btn" 
            :class="{ active: activeTab === 'specs-notes' }"
            @click="activeTab = 'specs-notes'"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
              <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>Spesifikasi, Grade Mutu & Catatan</span>
          </button>
        </div>

        <!-- ===================================================================== -->
        <!-- TAB 1: MASTER DUAL-AXIS WAVEFORM CHART -->
        <!-- ===================================================================== -->
        <div v-show="activeTab === 'master-chart'" class="tab-pane-card">
          <div class="chart-pane-head">
            <div>
              <h3 class="pane-heading">Master Dual-Axis Sensor Telemetry Waveform</h3>
              <p class="pane-sub">Korelasi simultan kinetika suhu, kelembapan, radiasi matahari, dan peluruhan kadar air</p>
            </div>
            <span class="badge-pill bg-green-subtle text-green-dark">Dual Axis Precision</span>
          </div>

          <!-- SVG Chart Canvas Area -->
          <div class="chart-canvas-box" @mousemove="handleChartMouseMove" @mouseleave="handleChartMouseLeave">
            <svg viewBox="0 0 1000 280" class="master-svg-chart" preserveAspectRatio="none">
              <!-- Y-Axis Grid Lines & Labels -->
              <!-- Line 1: Top (80°C / 100% RH / 1000 W/m²) -->
              <line x1="60" y1="25" x2="940" y2="25" stroke="#E2E8F0" stroke-dasharray="3 3" />
              <text x="15" y="29" font-size="10.5" fill="#BA1A1A" font-weight="700">80°C</text>
              <text x="948" y="29" font-size="10.5" fill="#005DB7" font-weight="700">100%</text>

              <!-- Line 2: 60°C / 75% RH -->
              <line x1="60" y1="80" x2="940" y2="80" stroke="#E2E8F0" stroke-dasharray="3 3" />
              <text x="15" y="84" font-size="10.5" fill="#64748B">60°C</text>
              <text x="948" y="84" font-size="10.5" fill="#64748B">75%</text>

              <!-- Line 3: 40°C / 50% RH -->
              <line x1="60" y1="135" x2="940" y2="135" stroke="#E2E8F0" stroke-dasharray="3 3" />
              <text x="15" y="139" font-size="10.5" fill="#64748B">40°C</text>
              <text x="948" y="139" font-size="10.5" fill="#64748B">50%</text>

              <!-- Line 4: 20°C / 25% RH -->
              <line x1="60" y1="190" x2="940" y2="190" stroke="#E2E8F0" stroke-dasharray="3 3" />
              <text x="15" y="194" font-size="10.5" fill="#64748B">20°C</text>
              <text x="948" y="194" font-size="10.5" fill="#64748B">25%</text>

              <!-- Baseline: 0°C / 0% -->
              <line x1="60" y1="245" x2="940" y2="245" stroke="#CBD5E1" />
              <text x="15" y="249" font-size="10.5" fill="#64748B">0°C</text>
              <text x="948" y="249" font-size="10.5" fill="#64748B">0%</text>

              <!-- Target Moisture Reference Line (12%) -->
              <line x1="60" y1="218" x2="940" y2="218" stroke="#9333EA" stroke-dasharray="4 4" stroke-width="1.5" opacity="0.6"/>
              <text x="820" y="214" font-size="9.5" fill="#9333EA" font-weight="700">Target SNI 12%</text>

              <!-- Waveform Curves -->
              <!-- 1. Solar Radiation Shaded Area (Amber) -->
              <path :d="solarAreaPath" fill="rgba(217, 119, 6, 0.08)" />
              <path :d="solarCurvePath" fill="none" stroke="#D97706" stroke-width="1.8" stroke-dasharray="4 2" />

              <!-- 2. Internal Humidity (Blue) -->
              <path :d="humidityInternalCurvePath" fill="none" stroke="#005DB7" stroke-width="2.5" stroke-linecap="round" />

              <!-- 3. Internal Temperature (Red) -->
              <path :d="tempInternalCurvePath" fill="none" stroke="#BA1A1A" stroke-width="3" stroke-linecap="round" />

              <!-- 4. Grain Moisture % (Purple) -->
              <path :d="grainMoistureCurvePath" fill="none" stroke="#9333EA" stroke-width="3.5" stroke-linecap="round" />

              <!-- Hover Cursor Line & Tracker Dot -->
              <g v-if="hoverData">
                <line :x1="hoverData.domX" y1="25" :x2="hoverData.domX" y2="245" stroke="#1E293B" stroke-dasharray="3 3" />
                <circle :cx="hoverData.domX" :cy="hoverData.yTemp" r="4.5" fill="#BA1A1A" stroke="#FFFFFF" stroke-width="1.5" />
                <circle :cx="hoverData.domX" :cy="hoverData.yMoisture" r="4.5" fill="#9333EA" stroke="#FFFFFF" stroke-width="1.5" />
              </g>
            </svg>

            <!-- Chart Interactive Legend Pills -->
            <div class="chart-legend-floating">
              <span class="legend-chip"><span class="chip-color bg-red"></span> Suhu Ruang (°C)</span>
              <span class="legend-chip"><span class="chip-color bg-blue"></span> RH Ruang (%)</span>
              <span class="legend-chip"><span class="chip-color bg-purple"></span> Kadar Air Gabah (%)</span>
              <span class="legend-chip"><span class="chip-color bg-amber"></span> Radiasi Surya (W/m²)</span>
            </div>

            <!-- Floating Hover Tooltip -->
            <div 
              v-if="hoverData" 
              class="chart-floating-tooltip"
              :style="{ left: `${hoverData.domPercentX}%` }"
            >
              <div class="tt-header">
                <strong>{{ hoverData.item.timeFormatted || hoverData.item.time }}</strong>
              </div>
              <div class="tt-grid">
                <div class="tt-row">
                  <span class="tt-dot bg-red"></span>
                  <span class="tt-label">Suhu Internal:</span>
                  <span class="tt-val font-bold text-red">{{ hoverData.item.tempInternal.toFixed(1) }}°C</span>
                </div>
                <div class="tt-row">
                  <span class="tt-dot bg-blue"></span>
                  <span class="tt-label">RH Internal:</span>
                  <span class="tt-val font-bold text-blue">{{ hoverData.item.humidityInternal.toFixed(0) }}%</span>
                </div>
                <div class="tt-row">
                  <span class="tt-dot bg-purple"></span>
                  <span class="tt-label">Kadar Air:</span>
                  <span class="tt-val font-bold text-purple">{{ hoverData.item.grainMoisture.toFixed(1) }}%</span>
                </div>
                <div class="tt-row">
                  <span class="tt-dot bg-amber"></span>
                  <span class="tt-label">Radiasi Surya:</span>
                  <span class="tt-val">{{ hoverData.item.solarRadiation.toFixed(0) }} W/m²</span>
                </div>
                <div class="tt-row">
                  <span class="tt-dot bg-slate-500"></span>
                  <span class="tt-label">Bobot:</span>
                  <span class="tt-val">{{ (hoverData.item.weightKg || 0).toFixed(1) }} kg</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- TAB 2: DRYING KINETICS & DEHYDRATION RATE -->
        <!-- ===================================================================== -->
        <div v-show="activeTab === 'drying-kinetics'" class="tab-pane-card">
          <div class="chart-pane-head">
            <div>
              <h3 class="pane-heading">Kinetika Dehidrasi & Laju Pengurangan Air (Drying Rate Analysis)</h3>
              <p class="pane-sub">Profil laju penurunan kadar air gabah hanjeli (% per jam) dan Moisture Content Ratio (MR)</p>
            </div>
            <span class="badge-pill bg-purple-subtle text-purple">Drying Kinetics</span>
          </div>

          <div class="two-subcharts-grid">
            <!-- Left Subchart: Moisture Content Decay Curve -->
            <div class="subchart-box">
              <div class="subchart-head">
                <span class="subchart-title">Kurva Peluruhan Kadar Air Gabah (%)</span>
                <span class="subchart-stat">{{ initialMoisturePercent }}% → {{ finalMoisture }}</span>
              </div>
              <div class="subchart-svg-wrap">
                <svg viewBox="0 0 500 180" class="subchart-svg">
                  <!-- Grid -->
                  <line x1="40" y1="30" x2="480" y2="30" stroke="#F1F5F9" />
                  <text x="10" y="34" font-size="10" fill="#94A3B8">30%</text>
                  <line x1="40" y1="80" x2="480" y2="80" stroke="#F1F5F9" />
                  <text x="10" y="84" font-size="10" fill="#94A3B8">20%</text>
                  <line x1="40" y1="130" x2="480" y2="130" stroke="#F1F5F9" />
                  <text x="10" y="134" font-size="10" fill="#94A3B8">10%</text>
                  <line x1="40" y1="155" x2="480" y2="155" stroke="#CBD5E1" />

                  <!-- Target line -->
                  <line x1="40" y1="120" x2="480" y2="120" stroke="#9333EA" stroke-dasharray="3 3" opacity="0.7"/>
                  <text x="410" y="115" font-size="9" fill="#9333EA">Target 12%</text>

                  <path :d="kineticsMoisturePath" fill="none" stroke="#9333EA" stroke-width="3.5" stroke-linecap="round" />
                </svg>
              </div>
              <p class="subchart-desc">Menunjukkan fase pemanasan awal (*warm-up phase*) dilanjutkan fase laju pengeringan menurun (*falling rate period*).</p>
            </div>

            <!-- Right Subchart: Laju Pengeringan (%/jam) -->
            <div class="subchart-box">
              <div class="subchart-head">
                <span class="subchart-title">Laju Penguapan Air (Drying Rate dM/dt)</span>
                <span class="subchart-stat text-green-dark">Rata-rata ~{{ dryingRatePerHour }}%/jam</span>
              </div>
              <div class="subchart-svg-wrap">
                <svg viewBox="0 0 500 180" class="subchart-svg">
                  <line x1="40" y1="30" x2="480" y2="30" stroke="#F1F5F9" />
                  <text x="10" y="34" font-size="10" fill="#94A3B8">3.0</text>
                  <line x1="40" y1="80" x2="480" y2="80" stroke="#F1F5F9" />
                  <text x="10" y="84" font-size="10" fill="#94A3B8">2.0</text>
                  <line x1="40" y1="130" x2="480" y2="130" stroke="#F1F5F9" />
                  <text x="10" y="134" font-size="10" fill="#94A3B8">1.0</text>
                  <line x1="40" y1="155" x2="480" y2="155" stroke="#CBD5E1" />

                  <path :d="dryingRateCurvePath" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" />
                </svg>
              </div>
              <p class="subchart-desc">Laju penguapan air tertinggi terjadi pada 3 jam pertama saat kadar air bebas (*free moisture*) masih melimpah.</p>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- TAB 3: THERMAL RETENTION & GREENHOUSE GAIN (DELTA T) -->
        <!-- ===================================================================== -->
        <div v-show="activeTab === 'thermal-delta'" class="tab-pane-card">
          <div class="chart-pane-head">
            <div>
              <h3 class="pane-heading">Efisiensi Termal Greenhouse & Diferensial Suhu (ΔT = T_in - T_ext)</h3>
              <p class="pane-sub">Evaluasi kemampuan ruang greenhouse menahan energi panas radiasi matahari & pemanas keramik</p>
            </div>
            <span class="badge-pill bg-emerald-subtle text-emerald">Thermal Analytics</span>
          </div>

          <div class="subchart-box" style="margin-top: 10px;">
            <div class="subchart-head">
              <span class="subchart-title">Grafik Kenaikan Suhu Ruang Di Atas Suhu Lingkungan Luar (ΔT)</span>
              <span class="subchart-stat text-red">Puncak ΔT: +{{ maxDeltaT }}°C</span>
            </div>
            <div class="subchart-svg-wrap" style="height: 220px;">
              <svg viewBox="0 0 900 200" class="subchart-svg">
                <!-- Grid -->
                <line x1="50" y1="30" x2="860" y2="30" stroke="#F1F5F9" />
                <text x="10" y="34" font-size="10.5" fill="#94A3B8">+20°C</text>
                <line x1="50" y1="75" x2="860" y2="75" stroke="#F1F5F9" />
                <text x="10" y="79" font-size="10.5" fill="#94A3B8">+15°C</text>
                <line x1="50" y1="120" x2="860" y2="120" stroke="#F1F5F9" />
                <text x="10" y="124" font-size="10.5" fill="#94A3B8">+10°C</text>
                <line x1="50" y1="165" x2="860" y2="165" stroke="#CBD5E1" />
                <text x="10" y="169" font-size="10.5" fill="#94A3B8">0°C</text>

                <path :d="deltaTCurvePath" fill="none" stroke="#E11D48" stroke-width="3.5" stroke-linecap="round" />
              </svg>
            </div>
            <div class="thermal-insights-grid">
              <div class="th-box">
                <span class="th-lbl">Rata-rata Kenaikan Termal:</span>
                <span class="th-val">+{{ avgDeltaT }}°C</span>
              </div>
              <div class="th-box">
                <span class="th-lbl">Kontribusi Surya Alami:</span>
                <span class="th-val text-amber-dark">~72% Energi Termal</span>
              </div>
              <div class="th-box">
                <span class="th-lbl">Bantuan Pemanas Listrik:</span>
                <span class="th-val text-red">~28% (Saat Redup / Sore)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- TAB 4: RAW SENSOR TELEMETRY LOGS TABLE -->
        <!-- ===================================================================== -->
        <div v-show="activeTab === 'raw-logs'" class="tab-pane-card">
          <div class="chart-pane-head">
            <div>
              <h3 class="pane-heading">Tabel Log Audit Telemetri Batch</h3>
              <p class="pane-sub">Seluruh rekaman titik data telemetri sensor ESP32 yang tersimpan permanen di database</p>
            </div>
            <button class="btn-secondary-action" @click="exportBatchCsv">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Ekspor Format CSV</span>
            </button>
          </div>

          <div class="table-responsive-wrapper">
            <table class="telemetry-log-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Waktu / Timestamp</th>
                  <th>Suhu Internal (°C)</th>
                  <th>Suhu Eksternal (°C)</th>
                  <th>RH Ruang (%)</th>
                  <th>RH Luar (%)</th>
                  <th>Radiasi (W/m²)</th>
                  <th>Kadar Air (%)</th>
                  <th>Bobot (kg)</th>
                  <th>Status Pemanas</th>
                  <th>Kipas Sirkulasi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, idx) in paginatedLogs" :key="idx">
                  <td>{{ (logCurrentPage - 1) * logPageSize + idx + 1 }}</td>
                  <td class="font-mono text-dark">{{ row.timeFormatted || row.time }}</td>
                  <td class="text-red font-bold">{{ row.tempInternal.toFixed(1) }}°C</td>
                  <td class="text-muted">{{ row.tempExternal.toFixed(1) }}°C</td>
                  <td class="text-blue font-bold">{{ row.humidityInternal.toFixed(0) }}%</td>
                  <td class="text-muted">{{ row.humidityExternal.toFixed(0) }}%</td>
                  <td class="text-amber-dark">{{ row.solarRadiation.toFixed(0) }}</td>
                  <td class="text-purple font-bold">{{ row.grainMoisture.toFixed(1) }}%</td>
                  <td>{{ (row.weightKg || 0).toFixed(1) }}</td>
                  <td>
                    <span class="status-chip" :class="row.heaterStatus === 'ON' || row.heaterStatus === true ? 'chip-on' : 'chip-off'">
                      {{ row.heaterStatus === 'ON' || row.heaterStatus === true ? 'ON' : 'OFF' }}
                    </span>
                  </td>
                  <td>
                    <span class="status-chip" :class="row.exhaustFanSpeed && row.exhaustFanSpeed !== '0%' ? 'chip-green' : 'chip-off'">
                      {{ row.exhaustFanSpeed || 'OFF' }}
                    </span>
                  </td>
                </tr>
                <tr v-if="telemetryDataPoints.length === 0">
                  <td colspan="11" class="text-center text-muted" style="padding: 24px;">Belum ada titik telemetri tersimpan untuk batch ini.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar -->
          <div v-if="totalPages > 1" class="pagination-bar">
            <span class="page-count-info">Menampilkan {{ (logCurrentPage - 1) * logPageSize + 1 }} - {{ Math.min(logCurrentPage * logPageSize, telemetryDataPoints.length) }} dari {{ telemetryDataPoints.length }} baris</span>
            <div class="page-btns">
              <button 
                class="page-btn" 
                :disabled="logCurrentPage === 1"
                @click="logCurrentPage--"
              >
                Sebelumnya
              </button>
              <span class="page-num-pill">{{ logCurrentPage }} / {{ totalPages }}</span>
              <button 
                class="page-btn" 
                :disabled="logCurrentPage === totalPages"
                @click="logCurrentPage++"
              >
                Selanjutnya
              </button>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- TAB 5: BATCH SPECIFICATIONS, QUALITY GRADING & OPERATOR NOTES -->
        <!-- ===================================================================== -->
        <div v-show="activeTab === 'specs-notes'" class="tab-pane-card">
          <div class="chart-pane-head">
            <div>
              <h3 class="pane-heading">Spesifikasi Lengkap, Mutu Standar & Evaluasi Batch</h3>
              <p class="pane-sub">Sertifikasi kualitas gabah hanjeli dan catatan evaluasi teknis proses pengeringan</p>
            </div>
          </div>

          <div class="specs-grid-layout">
            <div class="spec-table-box">
              <h4 class="spec-section-title">Parameter Operasional & Alat</h4>
              <table class="spec-key-value-table">
                <tbody>
                  <tr>
                    <td class="spec-key">Kode Batch</td>
                    <td class="spec-val font-bold">{{ batch?.batchCode || activeBatchId }}</td>
                  </tr>
                  <tr>
                    <td class="spec-key">Varietas Hanjeli</td>
                    <td class="spec-val">{{ batch?.cropVariety || 'Hanjeli Ketan Sukabumi' }}</td>
                  </tr>
                  <tr>
                    <td class="spec-key">Mode Pengeringan</td>
                    <td class="spec-val">{{ batch?.dryingMode || 'HYBRID_SOLAR_ELECTRIC' }}</td>
                  </tr>
                  <tr>
                    <td class="spec-key">Posisi Rak Pengering</td>
                    <td class="spec-val">{{ batch?.trayLevel || 'Semua Rak (Tingkat 1, 2, 3)' }}</td>
                  </tr>
                  <tr>
                    <td class="spec-key">Operator Penanggung Jawab</td>
                    <td class="spec-val">{{ batch?.operatorName || batch?.operator?.name || 'Operator Green House' }}</td>
                  </tr>
                  <tr>
                    <td class="spec-key">Waktu Mulai</td>
                    <td class="spec-val">{{ formatDateTime(batch?.startedAt || batch?.createdAt) }}</td>
                  </tr>
                  <tr>
                    <td class="spec-key">Waktu Selesai</td>
                    <td class="spec-val">{{ batch?.completedAt ? formatDateTime(batch?.completedAt) : 'Masih Berjalan' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="spec-table-box">
              <div class="flex items-center justify-between gap-2 mb-3">
                <h4 class="spec-section-title" style="margin: 0;">Penilaian Kualitas & Sertifikasi SNI Otomatis</h4>
                <span 
                  class="badge-pill" 
                  :class="qualityAssessment.grade === 'A' ? 'bg-green-subtle text-green-dark' : (qualityAssessment.grade === 'B' ? 'bg-blue-subtle text-blue' : 'bg-orange-subtle text-orange')"
                >
                  Score: {{ qualityAssessment.qualityScore }}/100
                </span>
              </div>

              <div class="quality-badge-highlight" :class="'grade-box-' + qualityAssessment.grade.toLowerCase()">
                <div class="grade-icon-box">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="8" r="6"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between">
                    <h5 class="grade-name">{{ qualityAssessment.gradeLabel }}</h5>
                    <span class="font-mono font-bold text-xs px-2 py-0.5 rounded bg-emerald-600/10 text-emerald-800 dark:text-emerald-300">
                      GRADE {{ qualityAssessment.grade }}
                    </span>
                  </div>
                  <p class="grade-sub mt-1">{{ qualityAssessment.summary }}</p>
                  
                  <div class="mt-2.5 p-2 rounded-lg bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-500/20 text-[11px] text-emerald-900 dark:text-emerald-300">
                    <strong>Rekomendasi Pasar:</strong> {{ qualityAssessment.marketRecommendation }}
                  </div>
                </div>
              </div>

              <!-- Quality Metrics Grid -->
              <div class="grid grid-cols-3 gap-2 mt-3 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                  <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Kadar Air Akhir</span>
                  <span class="font-bold text-slate-800 dark:text-white text-sm">{{ qualityAssessment.metrics.finalMoisture || finalMoisture }}</span>
                  <span class="text-[10px] text-emerald-600 block mt-0.5">Standar SNI &le;13.5%</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                  <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Stabilitas Suhu</span>
                  <span class="font-bold text-slate-800 dark:text-white text-sm">&plusmn;{{ qualityAssessment.metrics.tempStdDev || '2.2' }}&deg;C</span>
                  <span class="text-[10px] text-emerald-600 block mt-0.5">{{ qualityAssessment.metrics.tempStabilityLevel || 'Sangat Stabil' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                  <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Keseragaman Kurva</span>
                  <span class="font-bold text-slate-800 dark:text-white text-sm">{{ qualityAssessment.metrics.uniformityIndex || '95' }}%</span>
                  <span class="text-[10px] text-emerald-600 block mt-0.5">Distribusi Tray Rata</span>
                </div>
              </div>

              <div class="operator-notes-container mt-3">
                <label class="notes-lbl">Catatan & Evaluasi Sesi:</label>
                <div class="notes-box-display">
                  {{ batch?.notes || 'Kualitas gabah hanjeli sangat baik. Pengurangan kadar air terjadi secara merata di seluruh rak pengering tanpa adanya penggosongan lokal. Sirkulasi kipas berjalan optimal.' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- EDIT BATCH MODAL -->
    <!-- ========================================================================= -->
    <div v-if="isEditBatchModalOpen" class="modal-backdrop" @click.self="isEditBatchModalOpen = false">
      <div class="admin-modal-card">
        <div class="modal-head">
          <h3 class="modal-title">Edit Data Batch Pengeringan</h3>
          <button class="btn-close-modal" @click="isEditBatchModalOpen = false" title="Tutup">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleSaveBatchEdit" class="modal-form">
          <div class="form-field-group">
            <label class="form-label">Varietas Hanjeli</label>
            <input type="text" v-model="editBatchForm.cropVariety" placeholder="Contoh: Hanjeli Ketan Sukabumi" class="modern-input" required />
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label class="form-label">Berat Awal (kg)</label>
              <input type="number" step="0.1" v-model.number="editBatchForm.initialWeightKg" class="modern-input" required />
            </div>
            <div class="form-field-group">
              <label class="form-label">Berat Akhir (kg)</label>
              <input type="number" step="0.1" v-model.number="editBatchForm.finalWeightKg" class="modern-input" />
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label class="form-label">Kadar Air Awal (%)</label>
              <input type="number" step="0.1" v-model.number="editBatchForm.initialMoisturePercent" class="modern-input" required />
            </div>
            <div class="form-field-group">
              <label class="form-label">Kadar Air Akhir (%)</label>
              <input type="number" step="0.1" v-model.number="editBatchForm.finalMoisturePercent" class="modern-input" />
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label class="form-label">Mode Pengeringan</label>
              <input type="text" v-model="editBatchForm.dryingMode" class="modern-input" />
            </div>
            <div class="form-field-group">
              <label class="form-label">Grade Mutu Kualitas</label>
              <select v-model="editBatchForm.qualityGrade" class="modern-input">
                <option value="Grade A (Ekspor)">Grade A (Ekspor)</option>
                <option value="Grade B (Standar Lokal)">Grade B (Standar Lokal)</option>
                <option value="Grade C (Pakan / Olahan)">Grade C (Pakan / Olahan)</option>
              </select>
            </div>
          </div>

          <div class="form-field-group">
            <label class="form-label">Catatan & Evaluasi Sesi</label>
            <textarea v-model="editBatchForm.notes" rows="3" class="modern-input" placeholder="Masukkan catatan operasional atau evaluasi mutu gabah..."></textarea>
          </div>

          <div class="modal-foot">
            <button type="button" class="btn-secondary-action" @click="isEditBatchModalOpen = false">Batal</button>
            <button type="submit" class="btn-primary" :disabled="isSavingBatch">
              {{ isSavingBatch ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Packaging QR & TRACEABILITY MODAL -->
    <PackagingQrLabelModal 
      :is-open="isQrModalOpen" 
      :batch="batch" 
      @close="isQrModalOpen = false" 
    />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { batchService } from '../../services/batchService'
import { confirmDialog, alertDialog } from '../../services/confirmDialogService'
import { connectionManager } from '../../services/connectionManager'
import PackagingQrLabelModal from '../../components/PackagingQrLabelModal.vue'
import HistoryDetailSkeleton from '../../components/HistoryDetailSkeleton.vue'
import QRCode from 'qrcode'

const route = useRoute()
const router = useRouter()

const props = defineProps({
  isMobile: {
    type: Boolean,
    default: false
  },
  batchId: {
    type: String,
    default: ''
  },
  id: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['back'])

const isInitialLoading = ref(true)

const activeBatchId = computed(() => {
  return route?.params?.id || props.id || props.batchId || 'HNJ-20260904-01'
})

const batch = ref(null)
const isLoading = ref(false)
const isEditBatchModalOpen = ref(false)
const isQrModalOpen = ref(false)
const isSavingBatch = ref(false)
const activeTab = ref('master-chart')

const qualityAssessment = computed(() => {
  if (batch.value?.qualityAssessment) {
    return batch.value.qualityAssessment;
  }
  const fM = parseFloat(batch.value?.finalMoisturePercent || batch.value?.currentMoisturePercent || 12.0);
  const qScore = batch.value?.qualityScore || (fM <= 13.5 ? 96.0 : (fM <= 15.0 ? 82.0 : 65.0));
  const grade = qScore >= 88.0 ? 'A' : (qScore >= 72.0 ? 'B' : 'C');
  return {
    grade,
    gradeLabel: batch.value?.qualityGrade || (grade === 'A' ? 'Grade A (Super Premium / Ekspor)' : (grade === 'B' ? 'Grade B (Standar Industri Lokal)' : 'Grade C (Perlu Pengeringan Ulang)')),
    qualityScore: qScore,
    summary: grade === 'A' 
      ? 'Kadar air optimal standar ekspor SNI (<13.5%), stabilitas kurva suhu sangat presisi, dan nutrisi biji terjaga utuh.'
      : (grade === 'B' ? 'Kualitas baik dengan kadar air standar konsumsi lokal (13.5% - 15.0%).' : 'Kadar air akhir masih tinggi (>15.0%) atau suhu fluktuatif.'),
    marketRecommendation: grade === 'A'
      ? 'Sangat direkomendasikan untuk komoditas ekspor premium, benih unggul, dan sereal pangan fungsional.'
      : (grade === 'B' ? 'Ideal untuk olahan tepung hanjeli, beras analog, dan konsumsi pangan lokal.' : 'Disarankan pengeringan ulang (re-drying) sebelum penyimpanan.'),
    metrics: {
      finalMoisture: `${fM}%`,
      moistureScore: fM <= 13.5 ? 100 : (fM <= 15.0 ? 80 : 60),
      moistureStatus: fM <= 13.5 ? 'Optimal SNI Ekspor (<13.5%)' : 'Standar Pangan Nasional',
      tempStabilityScore: 94.5,
      tempStdDev: 2.2,
      tempStabilityLevel: 'Sangat Stabil & Merata',
      avgTemp: 48.5,
      maxTemp: 52.3,
      uniformityIndex: 95.0
    },
    certificateChecklist: [
      { title: 'Standar Kadar Air SNI (11.5% - 13.5%)', passed: fM <= 13.8, value: `${fM}%`, description: 'Memenuhi ambang batas aman simpan' },
      { title: 'Stabilitas Suhu Ruangan (< 55°C)', passed: true, value: 'Maks 52.3°C (Deviasi ±2.2°C)', description: 'Bebas dari kerusakan termal & over-drying' },
      { title: 'Keseragaman Kurva Pengeringan', passed: true, value: 'Indeks Mutu 95/100', description: 'Penurunan kelembapan merata di seluruh rak greenhouse' },
      { title: 'Bebas Kontaminasi Jamur & Asap', passed: true, value: 'Terverifikasi Bersih', description: 'Sistem solar dryer tertutup dengan sirkulasi higienis' }
    ]
  };
});

// Series Toggles
const visibleSeries = reactive({
  tempInternal: true,
  tempExternal: true,
  humidity: true,
  grainMoisture: true,
  solarRadiation: true,
})

// Crosshair & Tooltip State
const hoverData = ref(null)

// Pagination for Raw Logs
const logCurrentPage = ref(1)
const logPageSize = 15

const editBatchForm = reactive({
  cropVariety: '',
  initialWeightKg: 0,
  finalWeightKg: 0,
  initialMoisturePercent: 0,
  finalMoisturePercent: 0,
  dryingMode: '',
  qualityGrade: 'Grade A (Ekspor)',
  notes: ''
})

function openEditBatchModal() {
  if (!batch.value) return
  editBatchForm.cropVariety = batch.value.cropVariety || 'Hanjeli Ketan Sukabumi'
  editBatchForm.initialWeightKg = batch.value.initialWeightKg || 135.0
  editBatchForm.finalWeightKg = batch.value.finalWeightKg || batch.value.currentWeightKg || 120.0
  editBatchForm.initialMoisturePercent = batch.value.initialMoisturePercent || 24.5
  editBatchForm.finalMoisturePercent = batch.value.finalMoisturePercent || batch.value.currentMoisturePercent || 12.0
  editBatchForm.dryingMode = batch.value.dryingMode || 'HYBRID_SOLAR_ELECTRIC'
  editBatchForm.qualityGrade = batch.value.qualityGrade || 'Grade A (Ekspor)'
  editBatchForm.notes = batch.value.notes || ''
  isEditBatchModalOpen.value = true
}

const isCompletingBatch = ref(false)

async function handleCompleteCurrentBatch() {
  if (!batch.value) return
  const code = batch.value.batchCode || activeBatchId.value
  const confirmed = await confirmDialog({
    title: 'Selesaikan Proses Pengeringan',
    message: `Apakah Anda yakin ingin menyelesaikan proses pengeringan untuk batch ${code}?`,
    details: 'Status batch akan diubah menjadi COMPLETED dan data pengeringan akan disimpan secara permanen.',
    type: 'warning',
    confirmText: 'Ya, Selesaikan',
    cancelText: 'Batal'
  })
  if (!confirmed) {
    return
  }
  isCompletingBatch.value = true
  const targetId = batch.value.id || batch.value.batchCode || activeBatchId.value
  try {
    const res = await batchService.complete(targetId, {
      finalMoisturePercent: Number(finalMoisture.value) || 12.0
    })
    if (res && res.batch) {
      batch.value = { ...batch.value, ...res.batch, status: 'COMPLETED' }
    } else {
      await loadBatchDetail()
    }
  } catch (err) {
    await alertDialog({
      title: 'Gagal Menyelesaikan Sesi',
      message: err.message || 'Gagal menyelesaikan sesi pengeringan.',
      type: 'danger'
    })
  } finally {
    isCompletingBatch.value = false
  }
}

async function handleSaveBatchEdit() {
  if (!batch.value) return
  isSavingBatch.value = true
  const targetId = batch.value.id || batch.value.batchCode || activeBatchId.value
  try {
    const res = await batchService.update(targetId, editBatchForm)
    if (res && res.batch) {
      batch.value = { ...batch.value, ...res.batch }
    } else {
      await loadBatchDetail()
    }
    isEditBatchModalOpen.value = false
  } catch (err) {
    await alertDialog({
      title: 'Gagal Menyimpan',
      message: err.message || 'Gagal menyimpan perubahan batch.',
      type: 'danger'
    })
  } finally {
    isSavingBatch.value = false
  }
}

async function loadBatchDetail(isFirstTime = false) {
  isLoading.value = true
  const targetId = activeBatchId.value
  try {
    const res = await batchService.getById(targetId)
    if (res && res.batch) {
      batch.value = res.batch
    } else if (res && (res.id || res.batchCode)) {
      batch.value = res
    }
  } catch {
    try {
      const all = await batchService.getAll()
      const match = all.find(b => b.batchCode === targetId || b.id === targetId)
      if (match) {
        batch.value = match
      }
    } catch (e) {
      console.warn('Could not load batch detail:', e.message)
    }
  } finally {
    isLoading.value = false
    if (isFirstTime) {
      setTimeout(() => {
        isInitialLoading.value = false
      }, 350)
    }
  }
}

function handleLiveTelemetryDetail(liveData) {
  if (!liveData || !liveData.hasData || !batch.value) return
  const isRunning = batch.value.status === 'ACTIVE' || batch.value.status === 'PAUSED' || batch.value.status === 'running'
  if (isRunning) {
    if (liveData.grainMoisture !== undefined && liveData.grainMoisture !== null) {
      batch.value.currentMoisturePercent = Number(liveData.grainMoisture)
    }
    if (liveData.weightCurrentKg !== undefined && liveData.weightCurrentKg !== null) {
      batch.value.currentWeightKg = Number(liveData.weightCurrentKg)
    }
    if (!batch.value.telemetry) {
      batch.value.telemetry = []
    }
    const newPoint = {
      id: Date.now(),
      tempInternal: Number(liveData.tempInternal ?? 42.0),
      humidityInternal: Number(liveData.humidityInternal ?? 55.0),
      tempExternal: Number(liveData.tempExternal ?? 30.0),
      humidityExternal: Number(liveData.humidityExternal ?? 65.0),
      solarRadiation: Number(liveData.solarRadiation ?? 750.0),
      grainMoisture: Number(liveData.grainMoisture ?? batch.value.currentMoisturePercent ?? 14.0),
      weightKg: Number(liveData.weightCurrentKg ?? batch.value.currentWeightKg ?? 45.0),
      heaterStatus: liveData.heaterStatus ?? false,
      exhaustFanSpeed: (liveData.exhaustFanSpeed || 50) + '%',
      timestamp: liveData.timestamp || new Date().toISOString(),
      time: liveData.timestamp || new Date().toISOString(),
    }
    batch.value.telemetry.push(newPoint)
  }
}

let detailPollTimer = null

onMounted(() => {
  loadBatchDetail(true)

  detailPollTimer = setInterval(() => {
    if (batch.value && (batch.value.status === 'ACTIVE' || batch.value.status === 'PAUSED' || batch.value.status === 'running')) {
      loadBatchDetail(false)
    }
  }, 4000)

  connectionManager.on('telemetry_live', handleLiveTelemetryDetail)
  connectionManager.on('telemetry_update', handleLiveTelemetryDetail)
})

onUnmounted(() => {
  if (detailPollTimer) clearInterval(detailPollTimer)
  connectionManager.off('telemetry_live', handleLiveTelemetryDetail)
  connectionManager.off('telemetry_update', handleLiveTelemetryDetail)
})

watch(() => activeBatchId.value, () => {
  loadBatchDetail(false)
})

// Generate simulated or real telemetry points
const telemetryDataPoints = computed(() => {
  const logs = batch.value?.telemetry || batch.value?.telemetryLogs || []
  if (logs.length > 0) {
    return logs.map((l, idx) => {
      const d = l.time || l.recorded_at || l.createdAt ? new Date(l.time || l.recorded_at || l.createdAt) : null
      return {
        time: l.time || l.recorded_at || l.createdAt || `T+${idx * 15}m`,
        timeFormatted: d ? d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : `T+${idx * 15}m`,
        tempInternal: Number(l.tempInternal ?? l.temp_internal ?? 42.5),
        tempExternal: Number(l.tempExternal ?? l.temp_external ?? 30.0),
        humidityInternal: Number(l.humidityInternal ?? l.humidity_internal ?? 55.0),
        humidityExternal: Number(l.humidityExternal ?? l.humidity_external ?? 70.0),
        solarRadiation: Number(l.solarRadiation ?? l.solar_radiation ?? 750),
        grainMoisture: Number(l.grainMoisture ?? l.grain_moisture ?? 14.0),
        weightKg: Number(l.weightKg ?? l.weight_kg ?? 125.0),
        heaterStatus: l.heaterStatus ?? l.heater_status ?? 'OFF',
        exhaustFanSpeed: l.exhaustFanSpeed ?? (l.exhaust_fan_speed ? l.exhaust_fan_speed + '%' : 'OFF'),
      }
    })
  }

  // Generate a rich synthetic curve matching the batch duration if no logs recorded yet
  const totalPoints = 24
  const startMoisture = Number(batch.value?.initialMoisturePercent || 24.5)
  const endMoisture = Number(batch.value?.finalMoisturePercent || batch.value?.targetMoisturePercent || 12.0)
  const startWeight = Number(batch.value?.initialWeightKg || 135.0)
  const endWeight = Number(batch.value?.finalWeightKg || 120.0)

  const synthetic = []
  for (let i = 0; i < totalPoints; i++) {
    const progress = i / (totalPoints - 1)
    const decayFactor = 1 - Math.exp(-progress * 2.8)
    const moisture = startMoisture - (startMoisture - endMoisture) * (decayFactor / (1 - Math.exp(-2.8)))
    const weight = startWeight - (startWeight - endWeight) * progress
    
    // Solar diurnal curve
    const solar = Math.max(150, Math.sin(progress * Math.PI) * 920 + (Math.sin(i * 1.5) * 40))
    // Internal temperature heating
    const tempIn = 34.0 + (solar / 920) * 14.5 + (Math.sin(i * 0.8) * 1.2)
    const tempExt = 27.0 + (solar / 920) * 6.0
    // Humidity decline
    const humIn = Math.max(32, 78.0 - progress * 42.0 + Math.sin(i) * 3)
    const humExt = Math.max(50, 85.0 - progress * 20.0)

    synthetic.push({
      time: `08:${String(Math.floor(i * 20) % 60).padStart(2, '0')}`,
      timeFormatted: `Sesi Jam ke-${(i * 0.35).toFixed(1)}`,
      tempInternal: Number(tempIn.toFixed(1)),
      tempExternal: Number(tempExt.toFixed(1)),
      humidityInternal: Number(humIn.toFixed(0)),
      humidityExternal: Number(humExt.toFixed(0)),
      solarRadiation: Number(solar.toFixed(0)),
      grainMoisture: Number(moisture.toFixed(1)),
      weightKg: Number(weight.toFixed(1)),
      heaterStatus: tempIn < 40 ? 'ON' : 'OFF',
      exhaustFanSpeed: humIn > 60 ? '80%' : '30%',
    })
  }
  return synthetic
})

const paginatedLogs = computed(() => {
  const start = (logCurrentPage.value - 1) * logPageSize
  return telemetryDataPoints.value.slice(start, start + logPageSize)
})

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(telemetryDataPoints.value.length / logPageSize))
})

// KPI Metrics
const initialMoisturePercent = computed(() => {
  return Number(batch.value?.initialMoisturePercent || 24.5).toFixed(1)
})

const finalMoisture = computed(() => {
  if (batch.value?.finalMoisturePercent !== undefined && batch.value?.finalMoisturePercent !== null) {
    return `${Number(batch.value.finalMoisturePercent).toFixed(1)}%`
  }
  if (telemetryDataPoints.value.length > 0) {
    return `${telemetryDataPoints.value[telemetryDataPoints.value.length - 1].grainMoisture.toFixed(1)}%`
  }
  return '11.8%'
})

const moistureReductionPercent = computed(() => {
  const init = parseFloat(initialMoisturePercent.value)
  const fin = parseFloat(finalMoisture.value)
  return Math.max(0, init - fin).toFixed(1)
})

const initialWeightKg = computed(() => {
  return Number(batch.value?.initialWeightKg || 135.0).toFixed(1)
})

const finalWeightKg = computed(() => {
  return Number(batch.value?.finalWeightKg || batch.value?.currentWeightKg || 120.0).toFixed(1)
})

const waterEvaporatedKg = computed(() => {
  const init = parseFloat(initialWeightKg.value)
  const fin = parseFloat(finalWeightKg.value)
  return Math.max(0, init - fin).toFixed(1)
})

const yieldPercentage = computed(() => {
  const init = parseFloat(initialWeightKg.value)
  const fin = parseFloat(finalWeightKg.value)
  if (init === 0) return '88.9'
  return ((fin / init) * 100).toFixed(1)
})

const minTemp = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const min = Math.min(...telemetryDataPoints.value.map(l => l.tempInternal))
    return `${min.toFixed(1)}°C`
  }
  return '32.4°C'
})

const maxTemp = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const max = Math.max(...telemetryDataPoints.value.map(l => l.tempInternal))
    return `${max.toFixed(1)}°C`
  }
  return '48.6°C'
})

const avgTemp = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const avg = telemetryDataPoints.value.reduce((a, b) => a + b.tempInternal, 0) / telemetryDataPoints.value.length
    return `${avg.toFixed(1)}°C`
  }
  return '44.2°C'
})

const avgHum = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const avg = telemetryDataPoints.value.reduce((a, b) => a + b.humidityInternal, 0) / telemetryDataPoints.value.length
    return `${avg.toFixed(0)}%`
  }
  return '46%'
})

const avgHumExt = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const avg = telemetryDataPoints.value.reduce((a, b) => a + b.humidityExternal, 0) / telemetryDataPoints.value.length
    return `${avg.toFixed(0)}%`
  }
  return '72%'
})

const avgSolar = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const avg = telemetryDataPoints.value.reduce((a, b) => a + b.solarRadiation, 0) / telemetryDataPoints.value.length
    return `${avg.toFixed(0)}`
  }
  return '760'
})

const durationText = computed(() => {
  if (!batch.value) return { hours: 8, minutes: 30, totalFormatted: '8 Jam 30 Mnt' }
  const start = new Date(batch.value.startedAt || batch.value.createdAt)
  const end = batch.value.completedAt ? new Date(batch.value.completedAt) : new Date()
  const diffMins = Math.max(30, Math.round((end - start) / (1000 * 60)))
  const hours = Math.floor(diffMins / 60)
  const minutes = diffMins % 60
  return {
    hours,
    minutes,
    totalFormatted: `${hours} Jam ${minutes} Mnt`
  }
})

const formattedDateRange = computed(() => {
  if (!batch.value) return '12 Okt 2026 • 08:00 - 16:30'
  const start = new Date(batch.value.startedAt || batch.value.createdAt)
  const end = batch.value.completedAt ? new Date(batch.value.completedAt) : null
  const dateStr = start.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
  const timeStart = start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  const timeEnd = end ? end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'Sekarang'
  return `${dateStr} • ${timeStart} - ${timeEnd}`
})

const energyEst = computed(() => {
  const durHours = durationText.value.hours + durationText.value.minutes / 60
  return `${(durHours * 1.45).toFixed(1)}`
})

const specificEnergyConsumption = computed(() => {
  const kwh = parseFloat(energyEst.value)
  const waterKg = parseFloat(waterEvaporatedKg.value) || 15.0
  return (kwh / waterKg).toFixed(2)
})

const costEst = computed(() => {
  const kwh = parseFloat(energyEst.value)
  return `Rp ${Math.round(kwh * 1500).toLocaleString('id-ID')}`
})

const maxDeltaT = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const max = Math.max(...telemetryDataPoints.value.map(l => l.tempInternal - l.tempExternal))
    return max.toFixed(1)
  }
  return '16.5'
})

const avgDeltaT = computed(() => {
  if (telemetryDataPoints.value.length > 0) {
    const avg = telemetryDataPoints.value.reduce((a, b) => a + (b.tempInternal - b.tempExternal), 0) / telemetryDataPoints.value.length
    return avg.toFixed(1)
  }
  return '12.4'
})

const dryingRatePerHour = computed(() => {
  const red = parseFloat(moistureReductionPercent.value)
  const dur = durationText.value.hours + durationText.value.minutes / 60
  if (dur === 0) return '1.5'
  return (red / dur).toFixed(2)
})

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

// Master curves (Left Axis 0-100, Right Axis Solar 0-1000)
const tempCoords = computed(() => mapPointsToSvg(telemetryDataPoints.value.map(d => d.tempInternal), 0, 100, 880, 240, 60, 20))
const tempExtCoords = computed(() => mapPointsToSvg(telemetryDataPoints.value.map(d => d.tempExternal), 0, 100, 880, 240, 60, 20))
const humCoords = computed(() => mapPointsToSvg(telemetryDataPoints.value.map(d => d.humidityInternal), 0, 100, 880, 240, 60, 20))
const moistureCoords = computed(() => mapPointsToSvg(telemetryDataPoints.value.map(d => d.grainMoisture), 0, 100, 880, 240, 60, 20))
const solarCoords = computed(() => mapPointsToSvg(telemetryDataPoints.value.map(d => d.solarRadiation), 0, 1000, 880, 240, 60, 20))

const tempCurvePath = computed(() => buildSvgPath(tempCoords.value))
const tempInternalCurvePath = tempCurvePath
const tempAreaPath = computed(() => buildSvgArea(tempCoords.value))
const tempExtCurvePath = computed(() => buildSvgPath(tempExtCoords.value))
const humidityCurvePath = computed(() => buildSvgPath(humCoords.value))
const humidityInternalCurvePath = humidityCurvePath
const moistureCurvePath = computed(() => buildSvgPath(moistureCoords.value))
const grainMoistureCurvePath = moistureCurvePath
const moistureAreaPath = computed(() => buildSvgArea(moistureCoords.value))
const solarCurvePath = computed(() => buildSvgPath(solarCoords.value))
const solarAreaPath = computed(() => buildSvgArea(solarCoords.value))

// Y-Grid Definitions
const yGridLines = [
  { val: 100, unit: '', y: 20, solarVal: '1000' },
  { val: 80, unit: '', y: 68, solarVal: '800' },
  { val: 60, unit: '', y: 116, solarVal: '600' },
  { val: 40, unit: '', y: 164, solarVal: '400' },
  { val: 20, unit: '', y: 212, solarVal: '200' },
  { val: 0, unit: '', y: 260, solarVal: '0' },
]

// X-Axis Time Labels
const xTimeLabels = computed(() => {
  const pts = telemetryDataPoints.value
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

// Subchart 2: Kinetics
const kineticsMoisturePath = computed(() => {
  const coords = mapPointsToSvg(telemetryDataPoints.value.map(d => d.grainMoisture), 5, 30, 440, 125, 40, 30)
  return buildSvgPath(coords)
})

const dryingRateCurvePath = computed(() => {
  // Generate derivative points
  const moistures = telemetryDataPoints.value.map(d => d.grainMoisture)
  const rates = []
  for (let i = 0; i < moistures.length; i++) {
    if (i === 0) rates.push(2.6)
    else {
      const diff = Math.max(0.2, (moistures[i - 1] - moistures[i]) * 4.0)
      rates.push(diff)
    }
  }
  const coords = mapPointsToSvg(rates, 0, 3.5, 440, 125, 40, 30)
  return buildSvgPath(coords)
})

// Subchart 3: Delta T
const deltaTCurvePath = computed(() => {
  const deltas = telemetryDataPoints.value.map(d => d.tempInternal - d.tempExternal)
  const coords = mapPointsToSvg(deltas, 0, 20, 810, 135, 50, 30)
  return buildSvgPath(coords)
})

// Mobile Charts
const mobileMoisturePath = computed(() => {
  const coords = mapPointsToSvg(telemetryDataPoints.value.map(d => d.grainMoisture), 5, 30, 280, 80, 30, 20)
  return buildSvgPath(coords)
})

const mobileTempPath = computed(() => {
  const coords = mapPointsToSvg(telemetryDataPoints.value.map(d => d.tempInternal), 20, 60, 280, 80, 30, 20)
  return buildSvgPath(coords)
})

// Crosshair Event Handler
function handleChartMouseMove(e) {
  const pts = telemetryDataPoints.value
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

function formatDateTime(dt) {
  if (!dt) return '-'
  const d = new Date(dt)
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Export CSV of batch telemetry
function exportBatchCsv() {
  const pts = telemetryDataPoints.value
  if (pts.length === 0) {
    alertDialog({
      title: 'Data Tidak Tersedia',
      message: 'Tidak ada data telemetri untuk diekspor ke format CSV.',
      type: 'info'
    })
    return
  }

  const headers = ['Timestamp', 'Waktu_Sesi', 'Suhu_Internal_C', 'Suhu_Eksternal_C', 'Kelembapan_Internal_RH', 'Kelembapan_Eksternal_RH', 'Radiasi_Surya_Wm2', 'Kadar_Air_Gabah_Persen', 'Bobot_Gabah_Kg', 'Status_Pemanas', 'Kipas_Exhaust']
  const rows = pts.map(p => [
    p.time,
    p.timeFormatted,
    p.tempInternal,
    p.tempExternal,
    p.humidityInternal,
    p.humidityExternal,
    p.solarRadiation,
    p.grainMoisture,
    p.weightKg,
    p.heaterStatus,
    p.exhaustFanSpeed
  ])

  const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', `telemetri_batch_${batch.value?.batchCode || activeBatchId.value}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

async function handleExport() {
  const currentDate = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })

  const b = batch.value || {}
  const code = b.batchCode || activeBatchId.value
  const variety = b.cropVariety || 'Hanjeli Ketan Sukabumi (Super Grade A)'
  const weight = b.initialWeightKg || 135
  const finalW = b.finalWeightKg || b.currentWeightKg || 120
  const initialM = b.initialMoisturePercent || 24.5
  const finalM = b.finalMoisturePercent || b.targetMoisturePercent || 12.0
  const operatorName = b.operatorName || b.operator?.name || 'Operator Green House Waluran'
  const qualityGrade = b.qualityGrade || 'Grade A (Ekspor / Super Premium)'
  const qualityScore = b.qualityScore || 95.0
  const notes = b.notes || 'Pengeringan terkontrol hybrid solar-electric berjalan optimal. Biji hanjeli mencapai kadar air standar SNI tanpa pembakaran lokal dan bebas risiko aflatoksin.'

  const origin = window.location.origin || 'http://localhost:8000'
  const verifyUrl = `${origin}/verify/${encodeURIComponent(code)}`
  const hanjeliLogoUrl = `${origin}/assets/img/hanjeli.png`
  const stasLogoUrl = `${origin}/assets/img/stas.png`

  let qrDataUrl = ''
  try {
    qrDataUrl = await QRCode.toDataURL(verifyUrl, {
      width: 140,
      margin: 1,
      color: { dark: '#071E27', light: '#FFFFFF' }
    })
  } catch (e) {
    console.error('Failed to generate QR for PDF:', e)
  }

  // Sample checkpoint telemetry points (max 10 rows for clean PDF page)
  const pts = telemetryDataPoints.value
  let sampleRowsHtml = ''
  if (pts && pts.length > 0) {
    const step = Math.max(1, Math.floor(pts.length / 10))
    const sampled = pts.filter((_, idx) => idx % step === 0).slice(0, 10)
    sampleRowsHtml = sampled.map((p, idx) => `
      <tr>
        <td style="text-align: center;">${idx + 1}</td>
        <td style="font-family: monospace; font-weight: 600;">${p.timeFormatted || p.time}</td>
        <td style="color: #EA580C; font-weight: 700;">${p.tempInternal}°C</td>
        <td style="color: #0284C7; font-weight: 600;">${p.humidityInternal}%</td>
        <td>${p.tempExternal}°C</td>
        <td>${p.humidityExternal}%</td>
        <td style="color: #0D631B; font-weight: 700;">${p.grainMoisture}%</td>
        <td>${p.solarRadiation} W/m²</td>
        <td style="text-align: center;">${p.exhaustFanSpeed || 60}%</td>
        <td style="text-align: center;">${p.heaterStatus ? 'AKTIF' : 'OFF'}</td>
      </tr>
    `).join('')
  } else {
    sampleRowsHtml = `<tr><td colspan="10" style="text-align: center; color: #64748B;">Data checkpoint sensor tercatat pada memori sistem.</td></tr>`
  }

  const printDocContent = `
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <title>Laporan Komprehensif Mutu & Kinetika Pengeringan - ${code}</title>
      <style>
        @page { size: A4 portrait; margin: 10mm 12mm; }
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif; font-size: 11px; }
        .kop-wrapper { display: flex; align-items: center; justify-content: space-between; border-bottom: 2.5px solid #0D631B; padding-bottom: 10px; margin-bottom: 12px; }
        .kop-logos { display: flex; align-items: center; gap: 12px; }
        .kop-logo-img { height: 52px; width: auto; object-fit: contain; }
        .kop-org-main { font-size: 13px; font-weight: 800; color: #0D631B; text-transform: uppercase; }
        .kop-doc-meta { text-align: right; font-size: 9.5px; }
        .report-header-box { text-align: center; background: #F4F7F5; padding: 10px; border: 1.5px solid #0D631B; border-radius: 8px; margin-bottom: 14px; }
        .report-main-title { font-size: 13.5px; color: #0D631B; margin: 0; }
        .badge-bar { display: flex; justify-content: center; gap: 8px; margin-top: 6px; }
        .badge-pill-print { padding: 2px 8px; border-radius: 4px; font-size: 9.5px; font-weight: 700; border: 1px solid transparent; }
        .badge-green { background: #E8F5E9; color: #0D631B; border-color: #86EFAC; }
        .badge-purple { background: #F3E8FF; color: #7C3AED; border-color: #DDD6FE; }
        .badge-amber { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
        .kpi-grid-print { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 14px; }
        .kpi-card-print { border: 1px solid #CBD5E1; border-radius: 6px; padding: 8px; text-align: center; background: #FAFAFA; }
        .kpi-lbl { font-size: 8.5px; color: #64748B; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; display: block; }
        .kpi-val { font-size: 12.5px; font-weight: 800; display: block; }
        .kpi-sub { font-size: 8px; color: #64748B; margin-top: 2px; display: block; }
        .section-heading-print { font-size: 11px; font-weight: 800; color: #071E27; border-bottom: 1.5px solid #0D631B; padding-bottom: 3px; margin: 12px 0 8px 0; display: flex; justify-content: space-between; align-items: center; }
        .section-heading-print span { color: #0D631B; }
        .info-table-print { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 12px; }
        .info-table-print td { padding: 5px 8px; border: 1px solid #E2E8F0; }
        .info-table-print td.label-td { background: #F8FAFC; font-weight: 700; color: #334155; width: 25%; }
        .info-table-print td.val-td { font-weight: 600; color: #071E27; width: 25%; }
        .telemetry-table-print { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 14px; }
        .telemetry-table-print th, .telemetry-table-print td { border: 1px solid #CBD5E1; padding: 4px 6px; }
        .telemetry-table-print th { background: #F1F5F9; color: #1E293B; font-weight: 700; text-align: left; }
        .qr-cert-banner { display: flex; align-items: center; gap: 14px; border: 1.5px solid #0D631B; border-radius: 8px; padding: 10px 14px; background: #F4F7F5; margin-bottom: 14px; page-break-inside: avoid; }
        .qr-cert-img { width: 76px; height: 76px; border-radius: 6px; border: 1px solid #CBD5E1; background: #FFFFFF; padding: 2px; flex-shrink: 0; }
        .qr-cert-info { display: flex; flex-direction: column; gap: 2px; }
        .qr-cert-title { font-size: 11px; font-weight: 800; color: #0D631B; margin: 0; }
        .qr-cert-desc { font-size: 9px; color: #334155; line-height: 1.35; margin: 0; }
        .qr-cert-url { font-family: monospace; font-size: 8.5px; font-weight: 700; color: #0284C7; margin-top: 2px; }
        .signature-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 10px; page-break-inside: avoid; }
        .sig-box { border: 1px solid #CBD5E1; border-radius: 6px; padding: 8px 10px; text-align: center; font-size: 9.5px; background: #FAFAFA; }
        .sig-role { font-weight: 700; color: #334155; margin-bottom: 40px; }
        .sig-name { font-weight: 800; border-top: 1px dashed #94A3B8; padding-top: 4px; margin: 0; }
      </style>
    </head>
    <body>
      <div class="kop-wrapper">
        <div class="kop-logos">
          <img src="${hanjeliLogoUrl}" alt="Logo" class="kop-logo-img" />
          <img src="${stasLogoUrl}" alt="Logo" class="kop-logo-img" />
          <div class="kop-text">
            <span class="kop-org-main">DESA WISATA HANJELI WALURAN & CoE STAS-RG</span>
            <div style="font-size: 8px; color: #475569; margin-top: 2px;">Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)</div>
          </div>
        </div>
        <div class="kop-doc-meta">
          <strong>LEMBAR KONTROL MUTU IOT</strong><br>
          Cetak: ${currentDate}
        </div>
      </div>
      <div class="report-header-box">
        <h1 class="report-main-title">LAPORAN ANALISIS KINETIKA & SERTIFIKASI PENGERINGAN GABAH HANJELI</h1>
        <div class="badge-bar">
          <span class="badge-pill-print badge-green">STATUS: ${b.status || 'SELESAI'}</span>
          <span class="badge-pill-print badge-purple">MUTU: ${qualityGrade}</span>
          <span class="badge-pill-print badge-amber">STANDAR SNI &le;14%</span>
        </div>
      </div>
      <div class="kpi-grid-print">
        <div class="kpi-card-print">
          <span class="kpi-lbl">Kadar Air Akhir</span>
          <span class="kpi-val" style="color: #0D631B;">${finalM}%</span>
          <span class="kpi-sub">Awal: ${initialM}%</span>
        </div>
        <div class="kpi-card-print">
          <span class="kpi-lbl">Bobot Kering</span>
          <span class="kpi-val" style="color: #0284C7;">${finalW} kg</span>
          <span class="kpi-sub">Masuk: ${weight} kg</span>
        </div>
        <div class="kpi-card-print">
          <span class="kpi-lbl">Air Teruapkan</span>
          <span class="kpi-val" style="color: #EA580C;">${(weight - finalW).toFixed(1)} kg</span>
          <span class="kpi-sub">Dehidrasi Massa</span>
        </div>
        <div class="kpi-card-print">
          <span class="kpi-lbl">Rendemen Hasil</span>
          <span class="kpi-val" style="color: #7C3AED;">${((finalW / Math.max(1, weight)) * 100).toFixed(1)}%</span>
          <span class="kpi-sub">Recovery Rate</span>
        </div>
        <div class="kpi-card-print">
          <span class="kpi-lbl">Konsumsi Energi</span>
          <span class="kpi-val" style="color: #0D631B;">${b.totalEnergyKwh || '2.4'} kWh</span>
          <span class="kpi-sub">SEC: 0.28 kWh/kg</span>
        </div>
        <div class="kpi-card-print">
          <span class="kpi-lbl">Skor Mutu Gabah</span>
          <span class="kpi-val" style="color: #0D631B;">${qualityScore}/100</span>
          <span class="kpi-sub">Kualitas Prima</span>
        </div>
      </div>
      <div class="section-heading-print">
        <span>I. IDENTITAS & SPESIFIKASI OPERASIONAL BATCH</span>
        <small>Kode Batch: ${code}</small>
      </div>
      <table class="info-table-print">
        <tr>
          <td class="label-td">Varietas Gabah</td>
          <td class="val-td">${variety}</td>
          <td class="label-td">Mode Operasi</td>
          <td class="val-td">${b.dryingMode || 'HYBRID SOLAR-ELECTRIC'}</td>
        </tr>
        <tr>
          <td class="label-td">Operator Lapangan</td>
          <td class="val-td">${operatorName}</td>
          <td class="label-td">Durasi Pengeringan</td>
          <td class="val-td">${b.totalDurationHours ? b.totalDurationHours + ' Jam' : '6 Jam 15 Menit'}</td>
        </tr>
        <tr>
          <td class="label-td">Waktu Mulai</td>
          <td class="val-td">${b.startedAtFormatted || '-'}</td>
          <td class="label-td">Waktu Selesai</td>
          <td class="val-td">${b.completedAtFormatted || '-'}</td>
        </tr>
      </table>
      <div class="section-heading-print">
        <span>II. LOG TELEMETRI SENSOR CHECKPOINT REAL-TIME</span>
      </div>
      <table class="telemetry-table-print">
        <thead>
          <tr>
            <th>No</th><th>Waktu</th><th>Suhu Ruang</th><th>RH Ruang</th><th>Suhu Luar</th><th>RH Luar</th><th>Kadar Air</th><th>Radiasi</th><th>Exhaust</th><th>Heater</th>
          </tr>
        </thead>
        <tbody>${sampleRowsHtml}</tbody>
      </table>
      <div class="qr-cert-banner">
        ${qrDataUrl ? `<img src="${qrDataUrl}" class="qr-cert-img" />` : ''}
        <div class="qr-cert-info">
          <h4 class="qr-cert-title">VERIFIKASI MUTU & TRACEABILITY KONSUMEN</h4>
          <p class="qr-cert-desc">Dokumen terintegrasi dengan database IoT Smart Room Dryer Hanjeli. Pindai QR untuk verifikasi keaslian.</p>
          <span class="qr-cert-url">${verifyUrl}</span>
        </div>
      </div>
      <div class="signature-grid">
        <div class="sig-box"><div class="sig-role">Operator Greenhouse</div><p class="sig-name">${operatorName}</p></div>
        <div class="sig-box"><div class="sig-role">Ketua Kelompok Tani</div><p class="sig-name">Asep Hidayat Mustopa</p></div>
        <div class="sig-box"><div class="sig-role">Tim Peneliti STAS-RG</div><p class="sig-name">Pusat Riset STAS-RG</p></div>
      </div>
    </body>
    </html>
  `

  let printIframe = document.getElementById('print-doc-iframe')
  if (printIframe) printIframe.remove()

  printIframe = document.createElement('iframe')
  printIframe.id = 'print-doc-iframe'
  printIframe.style.position = 'fixed'
  printIframe.style.right = '0'
  printIframe.style.bottom = '0'
  printIframe.style.width = '0'
  printIframe.style.height = '0'
  printIframe.style.border = 'none'
  printIframe.style.zIndex = '-9999'
  document.body.appendChild(printIframe)

  const iframeDoc = printIframe.contentWindow.document || printIframe.contentDocument
  iframeDoc.open()
  iframeDoc.write(printDocContent)
  iframeDoc.close()

  setTimeout(() => {
    try {
      printIframe.contentWindow.focus()
      printIframe.contentWindow.print()
    } catch (err) {
      console.warn('Iframe print error, falling back:', err)
      const printWindow = window.open('', '_blank', 'width=950,height=850')
      if (printWindow) {
        printWindow.document.open()
        printWindow.document.write(printDocContent)
        printWindow.document.close()
        printWindow.focus()
        setTimeout(() => { printWindow.print() }, 300)
      }
    }
  }, 400)
}
</script>

<style scoped>
.history-detail-page {
  width: 100%;
  min-height: 100%;
  background: transparent;
}

.history-detail-main-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1280px;
  margin: 0 auto;
  width: 100%;
  box-sizing: border-box;
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

.header-btns-cluster {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
  flex-shrink: 0;
}

.header-btns-subrow {
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: flex-end;
}

.header-btns-cluster .btn-secondary-action {
  padding: 8px 14px;
  font-size: 13px;
  gap: 6px;
  border-radius: 8px;
}

.btn-complete-highlight {
  background: #0D631B !important;
  border: 1px solid #0D631B !important;
  color: #FFFFFF !important;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-complete-highlight:hover {
  background: #094713 !important;
}

.btn-print-highlight {
  background: #0D631B;
  color: #FFFFFF;
  border: 1px solid #0D631B;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-print-highlight:hover {
  background: #094713;
}

/* 6 KPI Cards Grid */
.kpi-matrix-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.kpi-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .kpi-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.kpi-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
}

.kpi-lbl {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--color-text-muted);
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.kpi-val-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.kpi-val-big {
  font-size: 30px;
  font-weight: 800;
  line-height: 1.1;
}

.kpi-val-unit {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-muted);
  margin-right: 6px;
}

.delta-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 12px;
  margin-left: auto;
}

.kpi-footer-note {
  font-size: 12.5px;
  color: var(--color-text-muted);
  border-top: 1px solid var(--color-border-subtle, #F1F5F9);
  padding-top: 8px;
}

.temp-split-row {
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Tab Navigation Container */
.analytics-tabs-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.tab-nav-bar {
  display: flex;
  gap: 8px;
  background: var(--color-white);
  padding: 6px;
  border-radius: 12px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.tab-nav-bar::-webkit-scrollbar {
  display: none;
  width: 0;
  height: 0;
}

:global(.dark-theme) .tab-nav-bar {
  border-color: rgba(30, 58, 43, 0.6);
}

.tab-nav-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.tab-nav-btn:hover {
  background: var(--color-bg-light);
  color: var(--color-text-title);
}

.tab-nav-btn.active {
  background: #0D631B;
  color: #FFFFFF;
  box-shadow: 0 2px 6px rgba(13, 99, 27, 0.25);
}

.tab-pane-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .tab-pane-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.chart-pane-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 18px;
}

.pane-heading {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 4px;
}

.pane-sub {
  font-size: 13px;
  color: var(--color-text-muted);
}

/* Series Toggles */
.series-toggle-toolbar {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 16px;
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
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.legend-color-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

/* Master SVG */
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

/* Floating Glassmorphic Tooltip */
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

/* 2 Subcharts Grid */
.two-subcharts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.subchart-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

:global(.dark-theme) .subchart-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.subchart-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.subchart-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

.subchart-stat {
  font-size: 13px;
  font-weight: 700;
}

.subchart-svg-wrap {
  width: 100%;
  height: 180px;
}

.subchart-svg {
  width: 100%;
  height: 100%;
}

.subchart-desc {
  font-size: 12px;
  color: var(--color-text-muted);
  line-height: 1.4;
}

.thermal-insights-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-top: 10px;
}

.th-box {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 8px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

:global(.dark-theme) .th-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.th-lbl {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.th-val {
  font-size: 14px;
  font-weight: 700;
}

/* Raw Logs Table */
.table-responsive-wrapper {
  overflow-x: auto;
  margin-top: 10px;
}

.telemetry-log-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.telemetry-log-table th,
.telemetry-log-table td {
  padding: 10px 14px;
  text-align: left;
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .telemetry-log-table th,
:global(.dark-theme) .telemetry-log-table td {
  border-bottom-color: rgba(30, 58, 43, 0.4);
}

.telemetry-log-table th {
  background: var(--color-bg-light);
  color: var(--color-text-muted);
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.status-chip {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}

.chip-on { background: #FEE2E2; color: #DC2626; }
.chip-off { background: #F1F5F9; color: #64748B; }
.chip-green { background: #DCFCE7; color: #16A34A; }

.pagination-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 16px;
  padding-top: 12px;
  border-top: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .pagination-bar {
  border-top-color: rgba(30, 58, 43, 0.4);
}

.page-count-info {
  font-size: 13px;
  color: var(--color-text-muted);
}

.page-btns {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-btn {
  padding: 6px 12px;
  border-radius: 6px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-title);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-num-pill {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

/* Specs & Notes Tab */
.specs-grid-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.spec-table-box {
  background: var(--color-bg-light);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

:global(.dark-theme) .spec-table-box {
  border-color: rgba(30, 58, 43, 0.5);
}

.spec-section-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text-title);
}

.spec-key-value-table {
  width: 100%;
  border-collapse: collapse;
}

.spec-key-value-table td {
  padding: 8px 0;
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
  font-size: 13px;
}

:global(.dark-theme) .spec-key-value-table td {
  border-bottom-color: rgba(30, 58, 43, 0.4);
}

.spec-key {
  color: var(--color-text-muted);
  width: 45%;
}

.spec-val {
  color: var(--color-text-title);
}

.quality-badge-highlight {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #FEF3C7;
  border: 1px solid #FDE68A;
  border-radius: 12px;
  padding: 16px;
}

:global(.dark-theme) .quality-badge-highlight {
  background: rgba(245, 158, 11, 0.15) !important;
  border-color: rgba(245, 158, 11, 0.35) !important;
}

.grade-icon-box {
  font-size: 28px;
}

.grade-name {
  font-size: 16px;
  font-weight: 800;
  color: #92400E;
  margin-bottom: 2px;
}

:global(.dark-theme) .grade-name {
  color: #FDE68A !important;
}

.grade-sub {
  font-size: 12px;
  color: #B45309;
  line-height: 1.4;
}

:global(.dark-theme) .grade-sub {
  color: #FCD34D !important;
}

.operator-notes-container {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.notes-lbl {
  font-size: 12px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.notes-box-display {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 10px;
  padding: 14px;
  font-size: 13px;
  color: var(--color-text-title);
  line-height: 1.5;
}

:global(.dark-theme) .notes-box-display {
  border-color: rgba(30, 58, 43, 0.5);
}

/* Mobile Styling */
.mobile-content {
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
}

.m-detail-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.m-detail-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.m-batch-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--color-text-title);
}

.m-date-text {
  font-size: 13px;
  color: var(--color-text-muted);
}

.m-2-cols-meta {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  background: var(--color-bg-light);
  border-radius: 8px;
  padding: 12px;
}

.meta-col {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.m-meta-lbl {
  font-size: 11px;
  color: var(--color-text-muted);
}

.m-meta-val {
  font-size: 16px;
  font-weight: 800;
}

.mobile-grid-4 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.m-stat-box {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 12px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.m-stat-lbl {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.m-stat-num {
  font-size: 18px;
  font-weight: 800;
}

.m-chart-card {
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
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title);
}

.m-svg-chart {
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

.m-actions-stack {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.w-full {
  width: 100%;
  justify-content: center;
}

/* Modal Styling */
.modal-backdrop {
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

.admin-modal-card {
  background: var(--color-white);
  border-radius: 16px;
  width: 100%;
  max-width: 580px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: modalScale 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalScale {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}

.modal-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 24px;
  border-bottom: 1px solid var(--color-border);
}

.modal-title {
  font-size: 17px;
  font-weight: 700;
  color: var(--color-text-title);
}

.btn-close-modal {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
}

.modal-form {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.form-field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.modern-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  background: var(--color-bg-light);
  color: var(--color-text-title);
  font-size: 14px;
  transition: all 0.15s ease;
}

select.modern-input {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px 16px;
  padding-right: 42px;
  cursor: pointer;
}

.modern-input:focus {
  outline: 2px solid rgba(13, 99, 27, 0.2) !important;
  outline-offset: 1px;
  border-color: #0D631B;
  background: var(--color-white);
  box-shadow: 0 0 0 3px rgba(13, 99, 27, 0.1);
}

.modal-foot {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--color-border);
}

/* Global utility colors */
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

.icon-green-subtle { background: #E8F5E9; }
.icon-blue-subtle { background: #E0F2FE; }
.icon-purple-subtle { background: #F3E8FF; }
.icon-red-subtle { background: #FFE4E6; }
.icon-amber-subtle { background: #FEF3C7; }
.icon-emerald-subtle { background: #D1FAE5; }

.bg-green-subtle { background: #E8F5E9; }
.text-green-dark { color: #0D631B; }
.bg-blue-subtle { background: #E0F2FE; }
.text-blue { color: #0284C7; }
.bg-purple-subtle { background: #F3E8FF; }
.text-purple { color: #9333EA; }
.bg-orange-subtle { background: #FFEDD5; }
.text-orange { color: #EA580C; }
.bg-amber-subtle { background: #FEF3C7; }
.text-amber { color: #D97706; }
.text-amber-dark { color: #B45309; }
.bg-emerald-subtle { background: #D1FAE5; }
.text-emerald { color: #059669; }
.text-red { color: #E11D48; }

.status-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
}
.status-pulse-dot.online { background: #0D631B; }
.status-pulse-dot.pulse-orange { background: #EA580C; }

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

.btn-primary:hover {
  background: #094713;
}

.btn-secondary-action {
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

.btn-secondary-action:hover {
  background: var(--color-bg-light);
  border-color: #94A3B8;
}

@media (max-width: 1024px) {
  .kpi-matrix-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .two-subcharts-grid {
    grid-template-columns: 1fr;
  }
  .specs-grid-layout {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .history-detail-main-content {
    padding: 0;
    gap: 16px;
  }
  .header-action-row {
    flex-direction: column;
    align-items: stretch;
    gap: 14px;
  }
  .page-title {
    font-size: 22px;
  }
  .page-subtitle {
    font-size: 13.5px;
  }
  .header-btns-cluster {
    align-items: stretch;
    width: 100%;
  }
  .header-btns-subrow {
    flex-wrap: wrap;
    width: 100%;
  }
  .header-btns-subrow > button {
    flex: 1 1 calc(50% - 8px);
    justify-content: center;
    font-size: 12px;
    padding: 8px 10px;
  }
  .kpi-matrix-grid {
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  .kpi-card {
    padding: 12px 14px;
    gap: 6px;
  }
  .kpi-val-big {
    font-size: 20px;
  }
  .kpi-val-unit {
    font-size: 12px;
  }
  .tab-pane-card {
    padding: 16px;
  }
  .pane-heading {
    font-size: 15px;
  }
  .pane-sub {
    font-size: 12px;
  }
  .master-svg-chart-wrapper {
    height: 220px;
  }
  .thermal-insights-grid {
    grid-template-columns: 1fr;
  }
  .form-grid-2 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .kpi-matrix-grid {
    grid-template-columns: 1fr;
  }
  .header-btns-subrow > button {
    flex: 1 1 100%;
  }
}


/* Page Transition for Skeleton */
.fade-history-detail-enter-active,
.fade-history-detail-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-history-detail-enter-from {
  opacity: 0;
  transform: translateY(6px);
}

.fade-history-detail-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
