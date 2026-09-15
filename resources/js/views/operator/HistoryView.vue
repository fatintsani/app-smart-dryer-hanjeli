<template>
  <div class="history-page">
    <Transition name="fade-history" mode="out-in">
      <HistorySkeleton v-if="isInitialLoading" key="skeleton" />
      <div v-else class="history-loaded-content" key="content">
        <!-- Unified Responsive Layout -->
        <div class="history-main-content">
          <!-- Header & Action Row -->
          <div class="page-header-row">
            <div class="page-header-group">
              <div class="title-with-badge">
                <h1 class="page-title">{{ $t('history.title') }}</h1>
              </div>
              <p class="page-subtitle">{{ $t('history.subtitle') }}</p>
            </div>

            <!-- Export Action Buttons -->
            <div class="header-btns-cluster">
              <div class="header-btns-subrow">
                <button class="btn-export-secondary" @click="openExportModal('csv')" :title="$t('history.csvTitleTooltip')">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="8" y1="13" x2="16" y2="13"></line>
                    <line x1="8" y1="17" x2="16" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                  </svg>
                  <span>{{ $t('history.downloadCsv') }}</span>
                </button>

                <button class="btn-export-secondary" @click="openExportModal('pdf')" :title="$t('history.pdfTitleTooltip')">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>{{ $t('history.downloadPdf') }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- ========================================================================= -->
          <!-- TOP AGGREGATE HISTORICAL KPIS (4 CARDS) -->
          <!-- ========================================================================= -->
          <div class="history-kpi-grid">
            <div class="hist-kpi-card">
              <div class="hist-kpi-header">
                <span class="hist-kpi-lbl">{{ $t('history.kpiTotalProcessed') }}</span>
                <div class="icon-circle icon-green-subtle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                </div>
              </div>
              <div class="hist-kpi-val-row">
                <span class="hist-kpi-num text-green-dark">{{ batchRecords.length }}</span>
                <span class="hist-kpi-unit">{{ $t('history.kpiUnitSessions') }}</span>
                <span class="delta-chip bg-green-subtle text-green-dark">{{ $t('history.kpiCompletedCount', { count: countByStatus('completed') }) }}</span>
              </div>
              <span class="hist-kpi-sub">{{ $t('history.kpiActiveCount', { count: countByStatus('running') }) }}</span>
            </div>

            <div class="hist-kpi-card">
              <div class="hist-kpi-header">
                <span class="hist-kpi-lbl">{{ $t('history.kpiTotalDryWeight') }}</span>
                <div class="icon-circle icon-purple-subtle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7E22CE" stroke-width="2.2">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                  </svg>
                </div>
              </div>
              <div class="hist-kpi-val-row">
                <span class="hist-kpi-num text-purple">{{ aggregateTotalWeightKg }}</span>
                <span class="hist-kpi-unit">kg</span>
                <span class="delta-chip bg-purple-subtle text-purple">{{ $t('history.kpiTonApprox', { tons: (aggregateTotalWeightKg / 1000).toFixed(2) }) }}</span>
              </div>
              <span class="hist-kpi-sub">{{ $t('history.kpiYieldSub') }}</span>
            </div>

            <div class="hist-kpi-card">
              <div class="hist-kpi-header">
                <span class="hist-kpi-lbl">{{ $t('history.kpiAvgMoistureDrop') }}</span>
                <div class="icon-circle icon-blue-subtle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="hist-kpi-val-row">
                <span class="hist-kpi-num text-blue">{{ aggregateAvgMoistureReduction }}</span>
                <span class="hist-kpi-unit">% ΔM</span>
                <span class="delta-chip bg-blue-subtle text-blue">{{ $t('history.kpiSniTarget') }}</span>
              </div>
              <span class="hist-kpi-sub">{{ $t('history.kpiAvgFinalMoisture') }} <strong>{{ aggregateAvgFinalMoisture }}%</strong></span>
            </div>

            <div class="hist-kpi-card">
              <div class="hist-kpi-header">
                <span class="hist-kpi-lbl">{{ $t('history.kpiPowerAndCost') }}</span>
                <div class="icon-circle icon-amber-subtle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                  </svg>
                </div>
              </div>
              <div class="hist-kpi-val-row">
                <span class="hist-kpi-num text-amber">{{ aggregateTotalEnergyKwh }}</span>
                <span class="hist-kpi-unit">kWh</span>
                <span class="delta-chip bg-amber-subtle text-amber-dark">Rp {{ aggregateTotalCostIdr }}</span>
              </div>
              <span class="hist-kpi-sub">{{ $t('history.kpiSolarSavingsSub', { percent: 74 }) }}</span>
            </div>
          </div>

          <!-- ========================================================================= -->
          <!-- HISTORICAL COMPARATIVE BATCH PERFORMANCE CHART -->
          <!-- ========================================================================= -->
          <div class="hist-chart-card">
            <div class="hist-chart-header">
              <div class="hist-chart-title-box">
                <div class="chart-badge-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                  </svg>
                </div>
                <div>
                  <h2 class="hist-chart-heading">{{ $t('history.chartTitle') }}</h2>
                  <p class="hist-chart-sub">{{ $t('history.chartSubtitle') }}</p>
                </div>
              </div>
              <span class="badge-pill bg-green-subtle text-green-dark">{{ $t('history.chartBadge') }}</span>
            </div>

            <!-- Comparative SVG Chart Canvas -->
            <div class="hist-svg-canvas-wrapper" @mousemove="handleChartMouseMove" @mouseleave="handleChartMouseLeave">
              <svg viewBox="0 0 1000 240" class="hist-svg" preserveAspectRatio="none">
                <!-- Grid Lines -->
                <line x1="60" y1="20" x2="940" y2="20" stroke="#E2E8F0" stroke-dasharray="2 2" />
                <text x="20" y="24" font-size="10.5" fill="#64748B" font-weight="600">200 kg / 20j</text>

                <line x1="60" y1="75" x2="940" y2="75" stroke="#E2E8F0" stroke-dasharray="2 2" />
                <text x="20" y="79" font-size="10.5" fill="#64748B" font-weight="600">150 kg / 15j</text>

                <line x1="60" y1="130" x2="940" y2="130" stroke="#E2E8F0" stroke-dasharray="2 2" />
                <text x="20" y="134" font-size="10.5" fill="#64748B" font-weight="600">100 kg / 10j</text>

                <line x1="60" y1="185" x2="940" y2="185" stroke="#CBD5E1" />
                <text x="20" y="189" font-size="10.5" fill="#64748B" font-weight="600">0 kg / 0j</text>

                <!-- Target Moisture Reference Line (12%) -->
                <line x1="60" y1="160" x2="940" y2="160" stroke="#9333EA" stroke-dasharray="3 3" opacity="0.6"/>
                <text x="830" y="155" font-size="9" fill="#9333EA" font-weight="600">{{ $t('history.chartTargetSni') }}</text>

                <!-- Batch Bars (Weight kg) -->
                <g v-for="(b, idx) in chartBarData" :key="idx">
                  <rect 
                    :x="b.barX" 
                    :y="b.barY" 
                    :width="b.barWidth" 
                    :height="b.barHeight" 
                    rx="6" 
                    fill="#0D631B" 
                    fill-opacity="0.25"
                    stroke="#0D631B"
                    stroke-width="1.5"
                  />
                  <text :x="b.centerX" y="206" font-size="10" fill="#64748B" text-anchor="middle" font-weight="600">{{ b.batchCode }}</text>
                </g>

                <!-- Duration Spline Curve (Amber) -->
                <path :d="chartDurationPath" fill="none" stroke="#D97706" stroke-width="3" stroke-linecap="round"/>

                <!-- Moisture Spline Curve (Purple) -->
                <path :d="chartMoisturePath" fill="none" stroke="#9333EA" stroke-width="3" stroke-linecap="round"/>

                <!-- Dots on Points -->
                <g v-for="(b, idx) in chartBarData" :key="'dot-' + idx">
                  <circle :cx="b.centerX" :cy="b.dotDurationY" r="4.5" fill="#D97706" stroke="#FFFFFF" stroke-width="1.5"/>
                  <circle :cx="b.centerX" :cy="b.dotMoistureY" r="4.5" fill="#9333EA" stroke="#FFFFFF" stroke-width="1.5"/>
                </g>

                <!-- Hover Crosshair -->
                <g v-if="hoverChartItem">
                  <line :x1="hoverChartItem.centerX" y1="20" :x2="hoverChartItem.centerX" y2="185" stroke="#1E293B" stroke-dasharray="3 3"/>
                </g>
              </svg>

              <!-- Floating Tooltip -->
              <div 
                v-if="hoverChartItem" 
                class="floating-chart-tooltip"
                :style="{ left: `${hoverChartItem.domPercentX}%` }"
              >
                <div class="tooltip-header">
                  <strong>{{ hoverChartItem.item.id }}</strong> ({{ hoverChartItem.item.cropVariety }})
                </div>
                <div class="tooltip-grid">
                  <div class="tooltip-item">
                    <span class="tt-dot" style="background: #0D631B;"></span>
                    <span class="tt-label">{{ $t('history.chartTtWeight') }}</span>
                    <span class="tt-val font-bold">{{ hoverChartItem.item.weight }}</span>
                  </div>
                  <div class="tooltip-item">
                    <span class="tt-dot" style="background: #D97706;"></span>
                    <span class="tt-label">{{ $t('history.chartTtDuration') }}</span>
                    <span class="tt-val">{{ hoverChartItem.item.duration }}</span>
                  </div>
                  <div class="tooltip-item">
                    <span class="tt-dot" style="background: #9333EA;"></span>
                    <span class="tt-label">{{ $t('history.chartTtMoisture') }}</span>
                    <span class="tt-val font-bold text-purple">{{ hoverChartItem.item.finalHumidity }}</span>
                  </div>
                  <div class="tooltip-item">
                    <span class="tt-dot" style="background: #E11D48;"></span>
                    <span class="tt-label">{{ $t('history.chartTtAvgTemp') }}</span>
                    <span class="tt-val">{{ hoverChartItem.item.avgTemp }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="chart-legend-row">
              <span class="legend-pill"><span class="legend-box" style="background: #0D631B; opacity: 0.3;"></span> {{ $t('history.chartLegendWeight') }}</span>
              <span class="legend-pill"><span class="legend-line" style="background: #D97706;"></span> {{ $t('history.chartLegendDuration') }}</span>
              <span class="legend-pill"><span class="legend-line" style="background: #9333EA;"></span> {{ $t('history.chartLegendMoisture') }}</span>
            </div>
          </div>

          <!-- ========================================================================= -->
          <!-- FILTER, SEARCH & VIEW MODE TOOLBAR -->
          <!-- ========================================================================= -->
          <div class="filter-toolbar-card">
            <!-- Search Input -->
            <div class="search-input-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input 
                type="text" 
                v-model="searchQuery" 
                :placeholder="$t('history.searchPlaceholder')" 
                class="search-input"
              />
            </div>

            <!-- Filter Status Pills -->
            <div class="filter-pills-row">
              <button 
                class="filter-pill" 
                :class="{ active: currentFilter === 'all' }"
                @click="currentFilter = 'all'"
              >
                {{ $t('history.filterAll') }} ({{ batchRecords.length }})
              </button>
              <button 
                class="filter-pill" 
                :class="{ active: currentFilter === 'running' }"
                @click="currentFilter = 'running'"
              >
                {{ $t('history.filterRunning') }} ({{ countByStatus('running') }})
              </button>
              <button 
                class="filter-pill" 
                :class="{ active: currentFilter === 'completed' }"
                @click="currentFilter = 'completed'"
              >
                {{ $t('history.filterCompleted') }} ({{ countByStatus('completed') }})
              </button>
            </div>

            <!-- Toolbar Actions on Right -->
            <div class="toolbar-right-actions">
              <!-- View Mode Toggle (Grid vs Table) -->
              <div class="view-mode-toggle">
                <button 
                  class="btn-toggle-view" 
                  :class="{ active: viewMode === 'grid' }"
                  @click="viewMode = 'grid'"
                  :title="$t('history.viewModeGrid')"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                  </svg>
                </button>
                <button 
                  class="btn-toggle-view" 
                  :class="{ active: viewMode === 'table' }"
                  @click="viewMode = 'table'"
                  :title="$t('history.viewModeTable')"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                  </svg>
                </button>
              </div>

              <!-- Refresh Button -->
              <button class="btn-refresh" @click="fetchBatches" :title="$t('history.btnRefreshTooltip')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" :class="{ 'spin-anim': isLoading }">
                  <path d="M23 4v6h-6"></path>
                  <path d="M1 20v-6h6"></path>
                  <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- ========================================================================= -->
          <!-- VIEW 1: RICH CARDS GRID VIEW (PAGINATED) -->
          <!-- ========================================================================= -->
          <div v-if="viewMode === 'grid' && paginatedBatches.length > 0" class="batch-list-wrapper">
            <div class="batches-grid-container">
              <div 
                v-for="batch in paginatedBatches" 
                :key="batch.id"
                class="batch-card"
                :class="{ 
                  'batch-card-active': batch.status === 'running',
                  'batch-card-selected': selectedBatchIds.includes(batch.id)
                }"
                @click="toggleBatchSelection(batch.id)"
              >
                <!-- Card Header -->
                <div class="batch-card-header">
                  <!-- Checkbox Selection -->
                  <div class="batch-select-checkbox-container" @click.stop>
                    <input 
                      type="checkbox" 
                      :checked="selectedBatchIds.includes(batch.id)"
                      @change="toggleBatchSelection(batch.id)"
                      class="batch-checkbox"
                      :title="$t('history.selectForCompare')"
                    />
                  </div>
                  <div class="batch-id-group">
                    <span class="batch-code font-bold">{{ batch.id }}</span>
                    <span class="batch-date text-muted">{{ batch.date }} • {{ batch.time }}</span>
                  </div>
                  <span 
                    class="status-chip"
                    :class="batch.status === 'completed' ? 'chip-green' : 'chip-orange'"
                  >
                    {{ batch.status === 'completed' ? $t('history.statusCompleted') : batch.status === 'running' ? $t('history.statusRunning') : $t('history.statusAborted') }}
                  </span>
                </div>

                <!-- Crop Variety & Weight Bar -->
                <div class="crop-info-row">
                  <div class="crop-name-box">
                    <span class="crop-label text-muted">{{ $t('history.cardVarietyLabel') }}</span>
                    <strong class="crop-title">{{ batch.cropVariety }}</strong>
                  </div>
                  <div class="grain-weight-tag">
                    <span class="weight-num text-green-dark">{{ batch.weight }}</span>
                    <span class="weight-lbl">Hanjeli</span>
                  </div>
                </div>

                <!-- 4 Metrics Grid Inside Card -->
                <div class="card-metrics-grid">
                  <div class="card-metric-box">
                    <span class="c-met-lbl">{{ $t('history.cardMetricAvgTemp') }}</span>
                    <span class="c-met-val text-red">{{ batch.avgTemp }}</span>
                  </div>
                  <div class="card-metric-box">
                    <span class="c-met-lbl">{{ $t('history.cardMetricFinalMoisture') }}</span>
                    <span class="c-met-val text-blue font-bold">{{ batch.finalHumidity }}</span>
                  </div>
                  <div class="card-metric-box">
                    <span class="c-met-lbl">{{ $t('history.cardMetricDuration') }}</span>
                    <span class="c-met-val">{{ batch.duration }}</span>
                  </div>
                  <div class="card-metric-box">
                    <span class="c-met-lbl">{{ $t('history.cardMetricEnergy') }}</span>
                    <span class="c-met-val text-green-dark">{{ batch.energy }}</span>
                  </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="card-footer-actions">
                  <button 
                    v-if="batch.status === 'running'" 
                    class="btn-card-complete-action" 
                    :title="$t('history.btnCompleteTooltip')"
                    @click.stop="openCompleteModal(batch)"
                  >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                      <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ $t('history.btnComplete') }}</span>
                  </button>
                  <button class="btn-card-label-action" :title="$t('history.btnLabelTooltip')" @click.stop="openQrModal(batch)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <rect x="3" y="3" width="7" height="7"></rect>
                      <rect x="14" y="3" width="7" height="7"></rect>
                      <rect x="14" y="14" width="7" height="7"></rect>
                      <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>{{ $t('history.btnLabel') }}</span>
                  </button>
                  <button class="btn-card-detail-action" @click.stop="openDetail(batch)">
                    <span>{{ $t('history.btnDetail') }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Pagination Bar (Cards) -->
            <div class="history-pagination-container">
              <div class="pagination-info-side">
                <span class="pagination-summary">
                  {{ $t('history.showingResults', { start: startRecordIndex, end: endRecordIndex, total: totalFilteredBatches }) }}
                </span>
                <div class="rows-per-page-selector">
                  <label class="rpp-label">{{ $t('history.rowsPerPage') }}</label>
                  <select v-model.number="itemsPerPage" class="rpp-select">
                    <option :value="5">5</option>
                    <option :value="6">6</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                  </select>
                </div>
              </div>

              <div class="pagination-nav-side" v-if="totalPages > 1">
                <button 
                  class="btn-pag-nav" 
                  :disabled="currentPage === 1" 
                  @click="goToPage(currentPage - 1)"
                  :title="$t('history.prevPage')"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                  </svg>
                  <span class="nav-text">{{ $t('history.prevPage') }}</span>
                </button>

                <div class="pagination-page-pills">
                  <template v-for="(p, pIdx) in paginationPages" :key="pIdx">
                    <span v-if="p === '...'" class="pag-ellipsis">...</span>
                    <button 
                      v-else 
                      class="pag-num-btn" 
                      :class="{ active: p === currentPage }" 
                      @click="goToPage(p)"
                    >
                      {{ p }}
                    </button>
                  </template>
                </div>

                <button 
                  class="btn-pag-nav" 
                  :disabled="currentPage >= totalPages" 
                  @click="goToPage(currentPage + 1)"
                  :title="$t('history.nextPage')"
                >
                  <span class="nav-text">{{ $t('history.nextPage') }}</span>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- ========================================================================= -->
          <!-- VIEW 2: DENSE AUDIT TABLE VIEW (PAGINATED) -->
          <!-- ========================================================================= -->
          <div v-else-if="viewMode === 'table' && paginatedBatches.length > 0" class="batch-table-card">
            <div class="table-responsive-wrapper">
              <table class="history-data-table">
                <thead>
                  <tr>
                    <th>{{ $t('history.colNo') }}</th>
                    <th>{{ $t('history.colBatchCode') }}</th>
                    <th>{{ $t('history.colVariety') }}</th>
                    <th>{{ $t('history.colStatus') }}</th>
                    <th>{{ $t('history.colDateTime') }}</th>
                    <th>{{ $t('history.colGrainWeight') }}</th>
                    <th>{{ $t('history.colAvgTemp') }}</th>
                    <th>{{ $t('history.colFinalMoisture') }}</th>
                    <th>{{ $t('history.colDuration') }}</th>
                    <th>{{ $t('history.colEnergy') }}</th>
                    <th>{{ $t('history.colOperator') }}</th>
                    <th>{{ $t('history.colAction') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="(b, idx) in paginatedBatches" 
                    :key="b.id"
                  >
                    <td>{{ startRecordIndex + idx }}</td>
                    <td class="font-bold text-dark">{{ b.id }}</td>
                    <td>{{ b.cropVariety }}</td>
                    <td>
                      <span class="status-chip" :class="b.status === 'completed' ? 'chip-green' : 'chip-orange'">
                        {{ b.status === 'completed' ? $t('history.statusCompleted') : b.status === 'running' ? $t('history.statusRunning') : $t('history.statusAborted') }}
                      </span>
                    </td>
                    <td class="text-muted">{{ b.date }}</td>
                    <td><strong>{{ b.weight }}</strong></td>
                    <td class="text-red font-bold">{{ b.avgTemp }}</td>
                    <td class="text-blue font-bold">{{ b.finalHumidity }}</td>
                    <td>{{ b.duration }}</td>
                    <td>{{ b.energy }}</td>
                    <td>{{ b.operator }}</td>
                    <td>
                      <div class="table-action-btns">
                        <button 
                          v-if="b.status === 'running'" 
                          class="table-btn-icon btn-table-complete" 
                          :title="$t('history.btnComplete')" 
                          @click.stop="openCompleteModal(b)"
                        >
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                          </svg>
                        </button>
                        <button class="table-btn-icon" :title="$t('history.btnDetail')" @click="openDetail(b)">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                          </svg>
                        </button>
                        <button class="table-btn-icon" :title="$t('history.btnLabelTooltip')" @click.stop="openQrModal(b)">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                          </svg>
                        </button>
                        <button class="table-btn-icon" :title="$t('history.exportCsvTooltip')" @click="exportSingleBatchCSV(b)">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination Bar (Table) -->
            <div class="history-pagination-container table-pagination-border">
              <div class="pagination-info-side">
                <span class="pagination-summary">
                  {{ $t('history.showingResults', { start: startRecordIndex, end: endRecordIndex, total: totalFilteredBatches }) }}
                </span>
                <div class="rows-per-page-selector">
                  <label class="rpp-label">{{ $t('history.rowsPerPage') }}</label>
                  <select v-model.number="itemsPerPage" class="rpp-select">
                    <option :value="5">5</option>
                    <option :value="6">6</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                  </select>
                </div>
              </div>

              <div class="pagination-nav-side" v-if="totalPages > 1">
                <button 
                  class="btn-pag-nav" 
                  :disabled="currentPage === 1" 
                  @click="goToPage(currentPage - 1)"
                  :title="$t('history.prevPage')"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                  </svg>
                  <span class="nav-text">{{ $t('history.prevPage') }}</span>
                </button>

                <div class="pagination-page-pills">
                  <template v-for="(p, pIdx) in paginationPages" :key="pIdx">
                    <span v-if="p === '...'" class="pag-ellipsis">...</span>
                    <button 
                      v-else 
                      class="pag-num-btn" 
                      :class="{ active: p === currentPage }" 
                      @click="goToPage(p)"
                    >
                      {{ p }}
                    </button>
                  </template>
                </div>

                <button 
                  class="btn-pag-nav" 
                  :disabled="currentPage >= totalPages" 
                  @click="goToPage(currentPage + 1)"
                  :title="$t('history.nextPage')"
                >
                  <span class="nav-text">{{ $t('history.nextPage') }}</span>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="empty-history-card">
            <div class="empty-icon-circle">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
            <h3 class="empty-title">{{ $t('history.emptyTitle') }}</h3>
            <p class="empty-desc">
              {{ searchQuery ? $t('history.emptySearch', { query: searchQuery }) : (currentFilter !== 'all' ? $t('history.emptyFilter', { status: currentFilter === 'running' ? $t('history.statusRunning') : $t('history.statusCompleted') }) : $t('history.emptyAll')) }}
            </p>
            <div class="empty-actions-row">
              <button 
                v-if="searchQuery || currentFilter !== 'all'" 
                type="button" 
                class="btn-reset-filter"
                @click="currentFilter = 'all'; searchQuery = ''; mobileFilter = 'all'"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                  <path d="M3 3v5h5"></path>
                </svg>
                <span>{{ $t('history.btnShowAll') }}</span>
              </button>
              <button 
                v-else 
                type="button" 
                class="btn-empty-start"
                @click="router.push('/drying/new')"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>{{ $t('history.btnStartNew') }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- COMPLETE BATCH CONFIRMATION MODAL -->
        <!-- ========================================================================= -->
        <Transition name="modal-fade">
          <div v-if="isCompleteModalOpen" class="modal-backdrop" @click.self="isCompleteModalOpen = false">
            <div class="export-dialog-card" style="max-width: 480px;">
              <div class="modal-head">
                <div class="head-title-icon">
                  <div class="icon-modal-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                      <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                  </div>
                  <div>
                    <h3 class="dialog-title">{{ $t('history.completeModalTitle') }}</h3>
                    <p class="dialog-sub">{{ $t('history.completeModalSubtitle') }}</p>
                  </div>
                </div>
                <button class="btn-close-dialog" @click="isCompleteModalOpen = false" :title="$t('common.close')">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>

              <div class="export-section" style="padding: 16px 0;">
                <p style="font-size: 13.5px; color: var(--color-text-main); line-height: 1.6; margin-bottom: 14px;">
                  {{ $t('history.completeModalPrompt', { batch: selectedBatchToComplete?.id, variety: selectedBatchToComplete?.cropVariety }) }}
                </p>
                <div style="background: rgba(13, 99, 27, 0.06); border: 1px solid rgba(13, 99, 27, 0.18); border-radius: 10px; padding: 12px 14px; font-size: 12.5px; color: var(--color-text-main); display: flex; flex-direction: column; gap: 6px;">
                  <div>• {{ $t('history.completeModalPoint1') }}</div>
                  <div>• {{ $t('history.completeModalPoint2', { moisture: selectedBatchToComplete?.finalHumidity }) }}</div>
                  <div>• {{ $t('history.completeModalPoint3') }}</div>
                </div>
              </div>

              <div class="modal-footer-actions">
                <button class="btn-cancel" @click="isCompleteModalOpen = false">{{ $t('common.cancel') }}</button>
                <button class="btn-confirm-export" :disabled="isCompletingBatch" @click="executeCompleteBatch" style="background: #0D631B; border-color: #0D631B;">
                  <svg v-if="!isCompletingBatch" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <span v-if="!isCompletingBatch">{{ $t('history.completeModalBtnConfirm') }}</span>
                  <span v-else>{{ $t('history.completeModalBtnCompleting') }}</span>
                </button>
              </div>
            </div>
          </div>
        </Transition>

        <!-- ========================================================================= -->
        <!-- EXPORT MODAL -->
        <!-- ========================================================================= -->
        <Transition name="modal-fade">
          <div v-if="isExportModalOpen" class="modal-backdrop" @click.self="closeExportModal">
            <div class="export-dialog-card">
              <div class="modal-head">
                <div class="head-title-icon">
                  <div class="icon-modal-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                      <polyline points="7 10 12 15 17 10"></polyline>
                      <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                  </div>
                  <div>
                    <h3 class="dialog-title">{{ $t('history.exportModalTitle') }}</h3>
                    <p class="dialog-sub">{{ $t('history.exportModalSubtitle') }}</p>
                  </div>
                </div>
                <button class="btn-close-dialog" @click="closeExportModal" :title="$t('common.close')">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>

              <!-- Format Choice -->
              <div class="export-section">
                <label class="section-label">{{ $t('history.exportFormatLabel') }}</label>
                <div class="format-options-grid">
                  <div 
                    class="format-card" 
                    :class="{ selected: selectedFormat === 'pdf' }"
                    @click="selectedFormat = 'pdf'"
                  >
                    <div class="format-card-icon icon-pdf">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                      </svg>
                    </div>
                    <div class="format-card-info">
                      <div class="format-name">{{ $t('history.exportPdfName') }}</div>
                      <div class="format-desc">{{ $t('history.exportPdfDesc') }}</div>
                    </div>
                    <div class="format-radio-indicator">
                      <div class="radio-inner" v-if="selectedFormat === 'pdf'"></div>
                    </div>
                  </div>

                  <div 
                    class="format-card" 
                    :class="{ selected: selectedFormat === 'csv' }"
                    @click="selectedFormat = 'csv'"
                  >
                    <div class="format-card-icon icon-csv">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="8" y1="13" x2="16" y2="13"></line>
                        <line x1="8" y1="17" x2="16" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                      </svg>
                    </div>
                    <div class="format-card-info">
                      <div class="format-name">{{ $t('history.exportCsvName') }}</div>
                      <div class="format-desc">{{ $t('history.exportCsvDesc') }}</div>
                    </div>
                    <div class="format-radio-indicator">
                      <div class="radio-inner" v-if="selectedFormat === 'csv'"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Scope Choice -->
              <div class="export-section">
                <label class="section-label">{{ $t('history.exportScopeLabel') }}</label>
                <div class="scope-options-row">
                  <label class="scope-label" :class="{ active: exportScope === 'all' }">
                    <input type="radio" v-model="exportScope" value="all" />
                    <span>{{ $t('history.exportScopeAll', { count: batchRecords.length }) }}</span>
                  </label>
                  <label class="scope-label" :class="{ active: exportScope === 'filtered' }">
                    <input type="radio" v-model="exportScope" value="filtered" />
                    <span>{{ $t('history.exportScopeFiltered', { count: effectiveFilteredBatches.length }) }}</span>
                  </label>
                </div>
              </div>

              <!-- Modal Action Buttons -->
              <div class="modal-footer-actions">
                <button class="btn-cancel" @click="closeExportModal">{{ $t('common.cancel') }}</button>
                <button class="btn-confirm-export" :disabled="isExporting" @click="executeExport">
                  <svg v-if="!isExporting" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span v-if="!isExporting">{{ selectedFormat === 'pdf' ? $t('history.exportBtnPdf') : $t('history.exportBtnCsv') }}</span>
                  <span v-else>{{ $t('history.exportBtnProcessing') }}</span>
                </button>
              </div>
            </div>
          </div>
        </Transition>

        <!-- Packaging QR Label Modal -->
        <PackagingQrLabelModal 
          :is-open="isQrModalOpen" 
          :batch="selectedQrBatch" 
          @close="isQrModalOpen = false" 
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { t } from '../../i18n'
import { batchService } from '../../services/batchService'
import { confirmDialog, alertDialog } from '../../services/confirmDialogService'
import { connectionManager } from '../../services/connectionManager'
import PackagingQrLabelModal from '../../components/PackagingQrLabelModal.vue'
import HistorySkeleton from '../../components/HistorySkeleton.vue'

const router = useRouter()

const props = defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['select-batch'])

const isInitialLoading = ref(true)

function openDetail(batch) {
  const targetId = batch.rawId || batch.id
  emit('select-batch', targetId)
  router.push(`/history/${targetId}`)
}

const batchRecords = ref([])
const selectedBatchIds = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const currentFilter = ref('all')
const mobileFilter = ref('all')
const viewMode = ref('grid') // 'grid' or 'table'

function toggleBatchSelection(id) {
  const idx = selectedBatchIds.value.indexOf(id)
  if (idx > -1) {
    selectedBatchIds.value.splice(idx, 1)
  } else {
    selectedBatchIds.value.push(id)
  }
}

// Pagination state
const currentPage = ref(1)
const itemsPerPage = ref(6)

// Complete Batch Modal state
const isCompleteModalOpen = ref(false)
const selectedBatchToComplete = ref(null)
const isCompletingBatch = ref(false)

// Packaging QR Modal state
const isQrModalOpen = ref(false)
const selectedQrBatch = ref(null)

function openQrModal(batch) {
  selectedQrBatch.value = {
    ...batch,
    batchCode: batch.id || batch.batchCode,
    cropVariety: batch.cropVariety,
    finalMoisturePercent: batch.humidityNum,
    qualityGrade: batch.qualityGrade || 'Grade A (Ekspor)',
    completedAt: batch.completedAt || batch.date
  }
  isQrModalOpen.value = true
}

function openCompleteModal(batch) {
  selectedBatchToComplete.value = batch
  isCompleteModalOpen.value = true
}

async function executeCompleteBatch() {
  if (!selectedBatchToComplete.value) return
  isCompletingBatch.value = true
  const targetId = selectedBatchToComplete.value.rawId || selectedBatchToComplete.value.id
  try {
    await batchService.complete(targetId, {
      finalMoisturePercent: selectedBatchToComplete.value.humidityNum || 12.0
    })
    isCompleteModalOpen.value = false
    selectedBatchToComplete.value = null
    await fetchBatches(false)
  } catch (err) {
    await alertDialog({
      title: t('common.error') || 'Gagal Menyelesaikan Batch',
      message: err.message || 'Gagal menyelesaikan batch pengeringan.',
      type: 'danger'
    })
  } finally {
    isCompletingBatch.value = false
  }
}

// Hover state for chart
const hoverChartItem = ref(null)

function transformBatch(b) {
  const isRunning = b.status === 'ACTIVE' || b.status === 'PAUSED' || b.status === 'running'
  const isCompleted = b.status === 'COMPLETED' || b.status === 'completed'
  const startDate = new Date(b.startedAt || b.createdAt)
  const endDate = b.completedAt ? new Date(b.completedAt) : null
  
  let durationHrs = Number(b.totalDurationHours || 0)
  if (durationHrs <= 0) {
    if (endDate) {
      durationHrs = Math.max(0.1, ((endDate - startDate) / (1000 * 60 * 60)))
    } else {
      durationHrs = Math.max(0.1, ((new Date() - startDate) / (1000 * 60 * 60)))
    }
  }

  const tempNum = Number(b.avgTemp !== undefined && b.avgTemp !== null ? b.avgTemp : 42.0)
  const avgTemp = `${tempNum.toFixed(1)}°C`
  
  const initialHumidityNum = Number(b.initialMoisturePercent !== undefined && b.initialMoisturePercent !== null ? b.initialMoisturePercent : 24.5)
  const humidityNum = Number(b.finalMoisturePercent !== null && b.finalMoisturePercent !== undefined 
    ? b.finalMoisturePercent 
    : (b.currentMoisturePercent !== null && b.currentMoisturePercent !== undefined ? b.currentMoisturePercent : (b.targetMoisturePercent || 12.0)))
  const finalHumidity = `${humidityNum.toFixed(1)}%`

  const weightNum = Number(b.finalWeightKg !== null && b.finalWeightKg !== undefined 
    ? b.finalWeightKg 
    : (b.currentWeightKg !== null && b.currentWeightKg !== undefined ? b.currentWeightKg : (b.initialWeightKg || 120)))
  const weight = `${weightNum.toFixed(1)} kg`

  const energyNum = Number(b.energyKwh !== undefined && b.energyKwh !== null ? b.energyKwh : (durationHrs * 1.45).toFixed(1))
  const costNum = Math.round(energyNum * 1500)

  return {
    id: b.batchCode || 'HJ-2026-001',
    batchCode: b.batchCode || 'HJ-2026-001',
    rawId: b.id,
    cropVariety: b.cropVariety || 'Hanjeli Ketan Sukabumi',
    status: isCompleted ? 'completed' : isRunning ? 'running' : 'aborted',
    statusLabel: isCompleted ? 'SELESAI' : isRunning ? 'SEDANG BERJALAN' : (b.status || 'DIBATALKAN'),
    date: startDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }),
    time: `${startDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} - ${endDate ? endDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'Sekarang'}`,
    weight,
    weightNum,
    avgTemp,
    tempNum,
    initialHumidityNum,
    finalHumidity,
    humidityNum,
    duration: `${durationHrs.toFixed(1)} Jam`,
    durationNum: durationHrs,
    energy: `${energyNum.toFixed(1)} kWh`,
    energyNum,
    cost: `Rp ${costNum.toLocaleString('id-ID')}`,
    operator: b.operatorName || b.operator?.name || 'Operator Green House',
    qualityGrade: b.qualityGrade || 'Grade A (Ekspor)',
    qualityScore: Number(b.qualityScore || 95),
    notes: b.notes || '',
    completedAt: b.completedAt,
    startedAt: b.startedAt || b.createdAt
  }
}

async function fetchBatches(isFirstTime = false) {
  if (isFirstTime) isLoading.value = true
  try {
    const data = await batchService.getAll()
    if (Array.isArray(data)) {
      batchRecords.value = data.map(transformBatch)
    }
  } catch (err) {
    console.warn('Failed to fetch batches from API:', err.message)
  } finally {
    isLoading.value = false
    if (isFirstTime) {
      setTimeout(() => {
        isInitialLoading.value = false
      }, 350)
    }
  }
}

function handleLiveTelemetry(liveData) {
  if (!liveData || !liveData.hasData) return
  // Update running batch in real-time
  const runningBatch = batchRecords.value.find(b => b.status === 'running')
  if (runningBatch) {
    if (liveData.grainMoisture !== undefined && liveData.grainMoisture !== null) {
      runningBatch.humidityNum = Number(liveData.grainMoisture)
      runningBatch.finalHumidity = `${runningBatch.humidityNum.toFixed(1)}%`
    }
    if (liveData.weightCurrentKg !== undefined && liveData.weightCurrentKg !== null) {
      runningBatch.weightNum = Number(liveData.weightCurrentKg)
      runningBatch.weight = `${runningBatch.weightNum.toFixed(1)} kg`
    }
    if (liveData.tempInternal !== undefined && liveData.tempInternal !== null) {
      runningBatch.tempNum = Number(liveData.tempInternal)
      runningBatch.avgTemp = `${runningBatch.tempNum.toFixed(1)}°C`
    }
  }
}

let historyPollTimer = null

onMounted(() => {
  fetchBatches(true)

  // Polling every 5 seconds if there are running batches
  historyPollTimer = setInterval(() => {
    if (batchRecords.value.some(b => b.status === 'running')) {
      fetchBatches(false)
    }
  }, 5000)

  // Real-time WebSocket / connection event listener
  connectionManager.on('telemetry_live', handleLiveTelemetry)
  connectionManager.on('telemetry_update', handleLiveTelemetry)
})

onUnmounted(() => {
  if (historyPollTimer) clearInterval(historyPollTimer)
  connectionManager.off('telemetry_live', handleLiveTelemetry)
  connectionManager.off('telemetry_update', handleLiveTelemetry)
})

function countByStatus(status) {
  return batchRecords.value.filter(b => b.status === status).length
}

// Aggregate Calculations
const aggregateTotalWeightKg = computed(() => {
  const sum = batchRecords.value.reduce((acc, b) => acc + (b.weightNum || 0), 0)
  return Number(sum.toFixed(1))
})

const aggregateAvgMoistureReduction = computed(() => {
  if (batchRecords.value.length === 0) return '0.0'
  const sum = batchRecords.value.reduce((acc, b) => acc + (b.initialHumidityNum - b.humidityNum), 0)
  return Math.max(0, sum / batchRecords.value.length).toFixed(1)
})

const aggregateAvgFinalMoisture = computed(() => {
  if (batchRecords.value.length === 0) return '0.0'
  const sum = batchRecords.value.reduce((acc, b) => acc + b.humidityNum, 0)
  return (sum / batchRecords.value.length).toFixed(1)
})

const aggregateTotalEnergyKwh = computed(() => {
  return batchRecords.value.reduce((acc, b) => acc + (b.energyNum || 0), 0).toFixed(1)
})

const aggregateTotalCostIdr = computed(() => {
  const kwh = parseFloat(aggregateTotalEnergyKwh.value)
  return Math.round(kwh * 1500).toLocaleString('id-ID')
})

// Filter & Search Logic
const filteredBatches = computed(() => {
  let list = batchRecords.value
  if (currentFilter.value !== 'all') {
    list = list.filter(b => b.status === currentFilter.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(b => 
      b.id.toLowerCase().includes(q) || 
      b.cropVariety.toLowerCase().includes(q) || 
      b.operator.toLowerCase().includes(q)
    )
  }
  return list
})

const mobileFilteredBatches = computed(() => {
  let list = batchRecords.value
  if (mobileFilter.value !== 'all') {
    list = list.filter(b => b.status === mobileFilter.value)
  }
  return list
})

const effectiveFilteredBatches = computed(() => {
  return props.isMobile ? mobileFilteredBatches.value : filteredBatches.value
})

// Pagination Computed Properties & Actions
const totalFilteredBatches = computed(() => effectiveFilteredBatches.value.length)

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(totalFilteredBatches.value / itemsPerPage.value))
})

const startRecordIndex = computed(() => {
  if (totalFilteredBatches.value === 0) return 0
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const endRecordIndex = computed(() => {
  return Math.min(currentPage.value * itemsPerPage.value, totalFilteredBatches.value)
})

const paginatedBatches = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return effectiveFilteredBatches.value.slice(start, start + itemsPerPage.value)
})

const paginationPages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }
  const pages = []
  pages.push(1)
  if (current > 3) {
    pages.push('...')
  }
  const start = Math.max(2, current - 1)
  const end = Math.min(total - 1, current + 1)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  if (current < total - 2) {
    pages.push('...')
  }
  pages.push(total)
  return pages
})

function goToPage(page) {
  if (typeof page !== 'number') return
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

// Reset page to 1 when filters or items per page change
watch([searchQuery, currentFilter, mobileFilter, itemsPerPage], () => {
  currentPage.value = 1
})

const chartBarData = computed(() => {
  const list = batchRecords.value.slice(0, 7)
  if (list.length === 0) return []
  const maxW = 200
  const maxDur = 20
  const maxM = 30

  const width = 880
  const stepX = width / (list.length || 1)

  return list.map((item, idx) => {
    const centerX = Math.round(60 + idx * stepX + stepX / 2)
    const barWidth = Math.min(60, stepX * 0.5)
    const barX = Math.round(centerX - barWidth / 2)
    
    // Weight bar (0-200 kg maps to height 0-165, baseline y=185)
    const normW = Math.min(1, item.weightNum / maxW)
    const barHeight = Math.round(normW * 165)
    const barY = 185 - barHeight

    // Duration dot (0-20h maps to y=185 to y=20)
    const normDur = Math.min(1, item.durationNum / maxDur)
    const dotDurationY = Math.round(185 - normDur * 165)

    // Moisture dot (0-30% maps to y=185 to y=20)
    const normM = Math.min(1, item.humidityNum / maxM)
    const dotMoistureY = Math.round(185 - normM * 165)

    return {
      item,
      batchCode: item.id,
      centerX,
      barX,
      barY,
      barWidth,
      barHeight,
      dotDurationY,
      dotMoistureY,
      domPercentX: (centerX / 1000) * 100
    }
  })
})

const chartDurationPath = computed(() => {
  const data = chartBarData.value
  if (data.length === 0) return ''
  return data.map((d, idx) => `${idx === 0 ? 'M' : 'L'} ${d.centerX} ${d.dotDurationY}`).join(' ')
})

const chartMoisturePath = computed(() => {
  const data = chartBarData.value
  if (data.length === 0) return ''
  return data.map((d, idx) => `${idx === 0 ? 'M' : 'L'} ${d.centerX} ${d.dotMoistureY}`).join(' ')
})

function handleChartMouseMove(e) {
  const data = chartBarData.value
  if (data.length === 0) return
  const rect = e.currentTarget.getBoundingClientRect()
  const offsetX = e.clientX - rect.left
  const percentX = Math.max(0, Math.min(1, offsetX / rect.width))
  
  const index = Math.round(percentX * (data.length - 1))
  hoverChartItem.value = data[index] || null
}

function handleChartMouseLeave() {
  hoverChartItem.value = null
}

// Single Batch CSV Export
function exportSingleBatchCSV(batch) {
  const headers = ['No_Batch', 'Varietas', 'Tanggal', 'Bobot_Kg', 'Status', 'Rata_Suhu', 'Kadar_Air_Akhir', 'Durasi', 'Energi_kWh', 'Biaya', 'Operator']
  const row = [
    batch.id,
    `"${batch.cropVariety}"`,
    `"${batch.date}"`,
    batch.weightNum,
    `"${batch.statusLabel}"`,
    `"${batch.avgTemp}"`,
    `"${batch.finalHumidity}"`,
    `"${batch.duration}"`,
    `"${batch.energy}"`,
    `"${batch.cost}"`,
    `"${batch.operator}"`
  ]
  const csvContent = [headers.join(','), row.join(',')].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', `rekap_${batch.id}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Export Modal
const isExportModalOpen = ref(false)
const selectedFormat = ref('pdf')
const exportScope = ref('all')
const isExporting = ref(false)

function openExportModal(fmt = 'pdf') {
  selectedFormat.value = fmt
  isExportModalOpen.value = true
}

function closeExportModal() {
  isExportModalOpen.value = false
}

function executeExport() {
  const dataList = exportScope.value === 'filtered' ? effectiveFilteredBatches.value : batchRecords.value
  if (dataList.length === 0) {
    alertDialog({
      title: 'Data Tidak Tersedia',
      message: 'Tidak ada data yang dapat diekspor.',
      type: 'info'
    })
    return
  }

  if (selectedFormat.value === 'csv') {
    const headers = ['No', 'No_Batch', 'Varietas', 'Tanggal', 'Bobot_Kg', 'Status', 'Rata_Suhu', 'Kadar_Air_Akhir', 'Durasi', 'Energi_kWh', 'Biaya', 'Operator']
    const rows = dataList.map((b, i) => [
      i + 1,
      `"${b.id}"`,
      `"${b.cropVariety}"`,
      `"${b.date}"`,
      b.weightNum,
      `"${b.statusLabel}"`,
      `"${b.avgTemp}"`,
      `"${b.finalHumidity}"`,
      `"${b.duration}"`,
      `"${b.energy}"`,
      `"${b.cost}"`,
      `"${b.operator}"`
    ])
    const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.setAttribute('href', url)
    link.setAttribute('download', `rekap_riwayat_pengeringan_hanjeli.csv`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    closeExportModal()
  } else {
    // Print PDF
    const origin = window.location.origin || 'http://localhost:8000'
    const hanjeliLogoUrl = `${origin}/assets/img/hanjeli.png`
    const stasLogoUrl = `${origin}/assets/img/stas.png`
    const currentDate = new Date().toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })

    const totalBatches = dataList.length
    const totalWeight = dataList.reduce((acc, b) => acc + (b.weightNum || 0), 0)
    const avgMoisture = (dataList.reduce((acc, b) => acc + (b.humidityNum || 12.0), 0) / Math.max(1, totalBatches)).toFixed(1)
    const totalEnergy = dataList.reduce((acc, b) => acc + (b.energyNum || 0), 0).toFixed(1)
    const completedCount = dataList.filter(b => b.status === 'completed' || b.status === 'COMPLETED').length
    const gradeAPercent = Math.round((completedCount / Math.max(1, totalBatches)) * 100)

    const tableRowsHtml = dataList.map((b, i) => `
      <tr>
        <td style="text-align: center; font-weight: 600;">${i + 1}</td>
        <td style="font-family: monospace; font-weight: 800; color: #071E27;">${b.id}</td>
        <td style="font-weight: 600;">${b.cropVariety}</td>
        <td>${b.date}</td>
        <td style="text-align: right; font-weight: 700;">${b.weight}</td>
        <td style="text-align: center; color: #EA580C; font-weight: 700;">${b.avgTemp}</td>
        <td style="text-align: center; color: #0284C7; font-weight: 800;">${b.finalHumidity}</td>
        <td style="text-align: center;">${b.duration}</td>
        <td style="text-align: center;">${b.energy}</td>
        <td style="text-align: center;">
          <span style="display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: 800; ${b.status === 'completed' ? 'background: #E8F5E9; color: #0D631B; border: 1px solid #86EFAC;' : 'background: #FFEDD5; color: #EA580C; border: 1px solid #FDBA74;'}">
            ${b.statusLabel || b.status}
          </span>
        </td>
        <td>${b.operator}</td>
      </tr>
    `).join('')

    const printContent = `
      <!DOCTYPE html>
      <html lang="id">
      <head>
        <meta charset="UTF-8">
        <title>Rekapitulasi Komprehensif Riwayat Pengeringan Hanjeli</title>
        <style>
          @page { size: A4 landscape; margin: 10mm 12mm; }
          * { box-shadow: none !important; text-shadow: none !important; box-sizing: border-box; }
          body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #071E27;
            margin: 0;
            padding: 6px;
            background: #FFFFFF;
            line-height: 1.4;
          }

          /* Kop Resmi */
          .kop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2.5px solid #0D631B;
            padding-bottom: 8px;
            margin-bottom: 12px;
          }
          .kop-left {
            display: flex;
            align-items: center;
            gap: 12px;
          }
          .kop-logo {
            height: 48px;
            width: auto;
            object-fit: contain;
          }
          .kop-org {
            font-size: 12.5px;
            font-weight: 800;
            color: #0D631B;
            letter-spacing: 0.5px;
            text-transform: uppercase;
          }
          .kop-sub {
            font-size: 10px;
            font-weight: 600;
            color: #1E293B;
          }
          .kop-addr {
            font-size: 9px;
            color: #64748B;
          }
          .kop-meta {
            text-align: right;
            font-size: 9px;
            color: #475569;
            border-left: 1px solid #CBD5E1;
            padding-left: 10px;
          }

          /* Report Header */
          .report-bar {
            background: #F4F7F5;
            border: 1.5px solid #0D631B;
            border-radius: 6px;
            padding: 8px 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
          }
          .report-title {
            font-size: 12.5px;
            font-weight: 800;
            color: #0D631B;
            text-transform: uppercase;
            margin: 0;
          }
          .report-filter-badge {
            font-size: 9.5px;
            font-weight: 700;
            color: #334155;
            background: #FFFFFF;
            padding: 3px 8px;
            border: 1px solid #CBD5E1;
            border-radius: 4px;
          }

          /* 5 KPI Summary Matrix */
          .summary-matrix {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 12px;
          }
          .sum-card {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
          }
          .sum-lbl {
            font-size: 8px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
          }
          .sum-val {
            font-size: 13.5px;
            font-weight: 800;
          }
          .sum-sub {
            font-size: 7.5px;
            color: #64748B;
            display: block;
            margin-top: 2px;
          }

          /* Table */
          .table-rekap {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 14px;
          }
          .table-rekap th, .table-rekap td {
            border: 1px solid #CBD5E1;
            padding: 5px 6px;
          }
          .table-rekap th {
            background: #F1F5F9;
            color: #1E293B;
            font-weight: 700;
            text-align: left;
          }

          /* Signature */
          .sig-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 14px;
            page-break-inside: avoid;
          }
          .sig-box {
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
            font-size: 9px;
            background: #FAFAFA;
          }
          .sig-role {
            font-weight: 700;
            color: #334155;
            margin-bottom: 40px;
          }
          .sig-name {
            font-weight: 800;
            color: #071E27;
            border-top: 1px dashed #94A3B8;
            padding-top: 3px;
            margin: 0;
          }

          .footer-note {
            text-align: center;
            font-size: 8px;
            color: #64748B;
            margin-top: 10px;
            border-top: 1px solid #E2E8F0;
            padding-top: 4px;
          }
        </style>
      </head>
      <body>
        <!-- KOP RESMI -->
        <div class="kop-header">
          <div class="kop-left">
            <img src="${hanjeliLogoUrl}" alt="Logo Hanjeli" class="kop-logo" />
            <img src="${stasLogoUrl}" alt="Logo STAS-RG" class="kop-logo" />
            <div>
              <div class="kop-org">DESA WISATA HANJELI WALURAN & CoE STAS-RG</div>
              <div class="kop-sub">Sistem Cerdas Greenhouse Smart Room Dryer Berbasis IoT & Tenaga Surya</div>
              <div class="kop-addr">Jl. Pamoyan, Waluran Mandiri, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat 43175, Indonesia</div>
            </div>
          </div>
          <div class="kop-meta">
            <strong>DOKUMEN REKAPITULASI ARSIP</strong><br>
            Cetak: ${currentDate}<br>
            Cakupan: ${exportScope.value === 'filtered' ? 'Data Terfilter' : 'Seluruh Arsip'}
          </div>
        </div>

        <!-- REPORT BAR -->
        <div class="report-bar">
          <h2 class="report-title">REKAPITULASI KINERJA OPERASIONAL PENGERINGAN GABAH HANJELI</h2>
          <span class="report-filter-badge">Total Arsip: ${totalBatches} Sesi Batch</span>
        </div>

        <!-- SUMMARY MATRIX -->
        <div class="summary-matrix">
          <div class="sum-card">
            <span class="sum-lbl">Total Batch Terarsip</span>
            <span class="sum-val" style="color: #0D631B;">${totalBatches} Batch</span>
            <span class="sum-sub">${completedCount} Berhasil Selesai</span>
          </div>
          <div class="sum-card">
            <span class="sum-lbl">Total Gabah Terkeringkan</span>
            <span class="sum-val" style="color: #071E27;">${totalWeight} kg</span>
            <span class="sum-sub">Kapasitas Maksimal</span>
          </div>
          <div class="sum-card">
            <span class="sum-lbl">Rata-rata Kadar Air Akhir</span>
            <span class="sum-val" style="color: #0284C7;">${avgMoisture}%</span>
            <span class="sum-sub">Standar SNI &le; 14%</span>
          </div>
          <div class="sum-card">
            <span class="sum-lbl">Total Konsumsi Listrik</span>
            <span class="sum-val" style="color: #EA580C;">${totalEnergy} kWh</span>
            <span class="sum-sub">Efisiensi Surya ~74%</span>
          </div>
          <div class="sum-card">
            <span class="sum-lbl">Kesesuaian Mutu Grade A</span>
            <span class="sum-val" style="color: #7C3AED;">${gradeAPercent}%</span>
            <span class="sum-sub">Lolos Mutu Ekspor</span>
          </div>
        </div>

        <!-- TABLE ARSIP -->
        <table class="table-rekap">
          <thead>
            <tr>
              <th style="width: 25px; text-align: center;">No</th>
              <th>Kode Batch</th>
              <th>Varietas Hanjeli</th>
              <th>Tanggal / Waktu</th>
              <th style="text-align: right;">Bobot Gabah</th>
              <th style="text-align: center;">Rata-rata Suhu</th>
              <th style="text-align: center;">Kadar Air Akhir</th>
              <th style="text-align: center;">Durasi</th>
              <th style="text-align: center;">Konsumsi Listrik</th>
              <th style="text-align: center;">Status</th>
              <th>Operator</th>
            </tr>
          </thead>
          <tbody>
            ${tableRowsHtml}
          </tbody>
        </table>

        <!-- TANDA TANGAN RESMI -->
        <div class="sig-row">
          <div class="sig-box">
            <div class="sig-role">Operator Greenhouse,</div>
            <p class="sig-name">Tim Teknis Lapangan</p>
          </div>
          <div class="sig-box">
            <div class="sig-role">Pengelola Desa Wisata Hanjeli,</div>
            <p class="sig-name">Asep Hidayat Mustopa</p>
          </div>
          <div class="sig-box">
            <div class="sig-role">Pusat Riset CoE STAS-RG,</div>
            <p class="sig-name">Tim Peneliti STAS-RG</p>
          </div>
        </div>

        <div class="footer-note">
          Rekapitulasi resmi dihasilkan secara otomatis oleh Platform IoT Smart Room Dryer Hanjeli &bull; Desa Wisata Hanjeli Waluran &bull; CoE STAS-RG
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
    iframeDoc.write(printContent)
    iframeDoc.close()

    closeExportModal()

    setTimeout(() => {
      try {
        printIframe.contentWindow.focus()
        printIframe.contentWindow.print()
      } catch (err) {
        console.warn('Iframe print error, falling back:', err)
        const printWindow = window.open('', '_blank', 'width=1050,height=850')
        if (printWindow) {
          printWindow.document.open()
          printWindow.document.write(printContent)
          printWindow.document.close()
          printWindow.focus()
          setTimeout(() => { printWindow.print() }, 300)
        }
      }
    }, 400)
  }
}
</script>

<style scoped>
.history-page {
  width: 100%;
  min-height: 100%;
  background: transparent;
}

.history-main-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 1280px;
  margin: 0 auto;
}

.page-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
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

.btn-export-primary, .btn-export-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.btn-export-primary {
  background: #0D631B;
  color: #FFFFFF;
  border: 1px solid #0D631B;
}
.btn-export-primary:hover { 
  background: #094713; 
  border-color: #094713;
}

.btn-komparasi {
  background: #0284C7 !important;
  border-color: #0284C7 !important;
  color: #FFFFFF !important;
}
.btn-komparasi:hover {
  background: #0369A1 !important;
  border-color: #0369A1 !important;
}

.btn-laporan {
  background: #0D631B !important;
  border-color: #0D631B !important;
  color: #FFFFFF !important;
}
.btn-laporan:hover {
  background: #094713 !important;
  border-color: #094713 !important;
}

.btn-export-secondary {
  background: var(--color-white);
  color: var(--color-text-title);
  border: 1px solid rgba(203, 213, 225, 0.6);
}
.btn-export-secondary:hover { 
  background: var(--color-bg-light); 
  border-color: #94A3B8; 
}

/* 4 KPI Cards Grid */
.history-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.hist-kpi-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .hist-kpi-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.hist-kpi-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
}

.hist-kpi-lbl {
  font-size: 11px;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.hist-kpi-val-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.hist-kpi-num {
  font-size: 26px;
  font-weight: 800;
  line-height: 1.1;
}

.hist-kpi-unit {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-muted);
  margin-right: 6px;
}

.hist-kpi-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

/* Comparative Chart Card */
.hist-chart-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .hist-chart-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.hist-chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.hist-chart-title-box {
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

.hist-chart-heading {
  font-size: 16.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.hist-chart-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.hist-svg-canvas-wrapper {
  position: relative;
  width: 100%;
  height: 240px;
  background: #FCFDFD;
  border: 1px solid rgba(203, 213, 225, 0.4);
  border-radius: 10px;
  overflow: hidden;
  cursor: crosshair;
}

:global(.dark-theme) .hist-svg-canvas-wrapper {
  background: #0F172A;
  border-color: #1E293B;
}

.hist-svg {
  width: 100%;
  height: 100%;
}

.floating-chart-tooltip {
  position: absolute;
  top: 15px;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(203, 213, 225, 0.6);
  border-radius: 10px;
  padding: 10px 14px;
  pointer-events: none;
  z-index: 20;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  min-width: 200px;
}

:global(.dark-theme) .floating-chart-tooltip {
  background: rgba(15, 23, 42, 0.92);
  border-color: #334155;
}

.tooltip-header {
  font-size: 12px;
  color: var(--color-text-title);
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
  padding-bottom: 6px;
  margin-bottom: 8px;
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
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.tt-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.tooltip-row strong {
  color: var(--color-text-title);
  font-weight: 700;
}

/* Chart Legend Row */
.chart-legend-row {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  padding-top: 14px;
  border-top: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .chart-legend-row {
  border-top-color: rgba(30, 58, 43, 0.4);
}

.legend-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-title);
  background: var(--color-bg-light);
  padding: 5px 12px;
  border-radius: 20px;
  border: 1px solid rgba(203, 213, 225, 0.4);
}

:global(.dark-theme) .legend-pill {
  border-color: rgba(30, 58, 43, 0.5);
}

.legend-box {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  display: inline-block;
  border: 1px solid #0D631B;
}

.legend-line {
  width: 16px;
  height: 3.5px;
  border-radius: 2px;
  display: inline-block;
}

/* Filter & View Toolbar Card */
.filter-toolbar-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .filter-toolbar-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.search-input-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 10px;
  padding: 8px 14px;
  flex: 1;
  max-width: 380px;
}

.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13.5px;
  color: var(--color-text-title);
  width: 100%;
}

.filter-pills-row {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-pill {
  padding: 8px 16px;
  border-radius: 20px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-muted);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

:global(.dark-theme) .filter-pill {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}

.filter-pill.active {
  background: #0D631B;
  color: #FFFFFF;
  border-color: #0D631B;
}

:global(.dark-theme) .filter-pill.active {
  background: #0D631B;
  color: #FFFFFF;
  border-color: #4ADE80;
}

.toolbar-right-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.view-mode-toggle {
  display: flex;
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 10px;
  overflow: hidden;
}

:global(.dark-theme) .view-mode-toggle {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}

.btn-toggle-view {
  padding: 8px 12px;
  border: none;
  background: transparent;
  color: var(--color-text-muted);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-toggle-view.active {
  background: #E8F5E9;
  color: #0D631B;
}

:global(.dark-theme) .btn-toggle-view.active {
  background: rgba(13, 99, 27, 0.35);
  color: #4ADE80;
}

.btn-refresh {
  padding: 8px 12px;
  border-radius: 10px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-muted);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}

:global(.dark-theme) .btn-refresh {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}

.btn-refresh:hover {
  color: #0D631B;
  border-color: #0D631B;
}

/* Batches Grid Container */
.batches-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 16px;
}

.batch-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  cursor: pointer;
}

:global(.dark-theme) .batch-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.batch-card:hover {
  border-color: #A5D6A7;
  box-shadow: 0 4px 12px rgba(13, 99, 27, 0.06);
}

.batch-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.batch-id-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
  min-width: 0;
}

.batch-code {
  font-size: 15px;
  color: var(--color-text-title);
}

.batch-date {
  font-size: 11.5px;
}

.crop-info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: var(--color-bg-light);
  border-radius: 10px;
}

.crop-name-box {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.crop-label {
  font-size: 10.5px;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.crop-title {
  font-size: 13px;
  color: var(--color-text-title);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.grain-weight-tag {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  flex-shrink: 0;
}

.weight-num {
  font-size: 14px;
  font-weight: 800;
}

.weight-lbl {
  font-size: 10.5px;
  color: var(--color-text-muted);
}

.card-metrics-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.card-metric-box {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 8px 10px;
  background: var(--color-bg-light);
  border-radius: 8px;
}

.c-met-lbl {
  font-size: 10.5px;
  color: var(--color-text-muted);
  font-weight: 600;
}

.c-met-val {
  font-size: 13.5px;
  font-weight: 700;
}

.card-footer-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: auto;
  padding-top: 10px;
  border-top: 1px solid var(--color-border-subtle, #F1F5F9);
}

.btn-card-complete-action {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 7px 12px;
  border-radius: 8px;
  border: none;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-card-complete-action:hover {
  background: #094713;
}

.btn-card-label-action {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 7px 12px;
  border-radius: 8px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-title);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

:global(.dark-theme) .btn-card-label-action {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}

.btn-card-label-action:hover {
  border-color: #0D631B;
  color: #0D631B;
}

.btn-card-detail-action {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 7px 14px;
  border-radius: 8px;
  border: 1px solid #0D631B;
  background: #E8F5E9;
  color: #0D631B;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  margin-left: auto;
  transition: all 0.15s ease;
}

:global(.dark-theme) .btn-card-detail-action {
  background: rgba(13, 99, 27, 0.2);
  border-color: #4ADE80;
  color: #4ADE80;
}

.btn-card-detail-action:hover {
  background: #0D631B;
  color: #FFFFFF;
}

/* Table Card */
.batch-table-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .batch-table-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.history-data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.history-data-table th, .history-data-table td {
  padding: 12px 14px;
  text-align: left;
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .history-data-table th, 
:global(.dark-theme) .history-data-table td {
  border-bottom-color: rgba(30, 58, 43, 0.4);
}

.history-data-table th {
  background: var(--color-bg-light);
  color: var(--color-text-muted);
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
}

.table-action-btns {
  display: flex;
  gap: 6px;
}

.table-btn-icon {
  padding: 6px;
  border-radius: 8px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-muted);
  cursor: pointer;
}

.table-btn-icon:hover {
  color: #0D631B;
  border-color: #0D631B;
}

/* Modal Dialog */
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

.export-dialog-card {
  background: var(--color-white);
  border-radius: 16px;
  width: 100%;
  max-width: 540px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.modal-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.head-title-icon {
  display: flex;
  align-items: center;
  gap: 12px;
}

.icon-modal-circle {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dialog-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text-title);
  margin-bottom: 2px;
}

.dialog-sub {
  font-size: 12px;
  color: var(--color-text-muted);
}

.btn-close-dialog {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
}

.export-section {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.section-label {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

.format-options-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.format-card {
  border: 1.5px solid rgba(203, 213, 225, 0.5);
  border-radius: 12px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
}

:global(.dark-theme) .format-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.format-card.selected {
  border-color: #0D631B;
  background: #E8F5E9;
}

.format-name {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

.format-desc {
  font-size: 11px;
  color: var(--color-text-muted);
  line-height: 1.3;
}

.scope-options-row {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.scope-label {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
}

.scope-label.active {
  background: #E8F5E9;
  border-color: #0D631B;
  font-weight: 600;
}

.modal-footer-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 6px;
}

.btn-cancel {
  padding: 10px 16px;
  border-radius: 8px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: transparent;
  color: var(--color-text-muted);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-confirm-export {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
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

.mobile-header-area {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px;
}

.mobile-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.mobile-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--color-text-title);
}

.mobile-sub {
  font-size: 12.5px;
  color: var(--color-text-muted);
}

.btn-m-export-icon {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 6px;
  border: 1px solid #0D631B;
  background: #E8F5E9;
  color: #0D631B;
  font-size: 12px;
  font-weight: 700;
}

.mobile-filter-pills {
  display: flex;
  gap: 8px;
  overflow-x: auto;
}

.m-pill {
  padding: 8px 14px;
  border-radius: 20px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-muted);
  font-size: 12.5px;
  font-weight: 600;
  white-space: nowrap;
}

.m-pill.active {
  background: #0D631B;
  color: #FFFFFF;
  border-color: #0D631B;
}

.mobile-session-stack {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.mobile-session-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.m-card-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.weight-tag strong {
  color: #7E22CE;
}

.m-session-title {
  font-size: 16px;
  font-weight: 800;
  color: var(--color-text-title);
}

.m-session-date {
  font-size: 12px;
  color: var(--color-text-muted);
}

.m-session-metrics-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  background: var(--color-bg-light);
  border-radius: 8px;
  padding: 10px;
}

.m-metric {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.metric-lbl {
  font-size: 10.5px;
  color: var(--color-text-muted);
}

.metric-val {
  font-size: 13.5px;
  font-weight: 700;
}

.btn-card-complete {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #0D631B;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-card-complete:hover {
  background: #094713;
  border-color: #094713;
}

.btn-table-complete {
  background: #E8F5E9 !important;
  color: #0D631B !important;
  border: 1px solid rgba(13, 99, 27, 0.25) !important;
}

.btn-table-complete:hover {
  background: #0D631B !important;
  color: #FFFFFF !important;
}

.btn-table-complete:hover svg {
  stroke: #FFFFFF !important;
}

.m-card-top-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.m-btn-complete-quick {
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #0D631B;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

.m-btn-complete-quick:active {
  background: #094713;
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

.icon-green-subtle { background: #E8F5E9; }
.icon-purple-subtle { background: #F3E8FF; }
.icon-blue-subtle { background: #E0F2FE; }
.icon-amber-subtle { background: #FEF3C7; }
.icon-orange-subtle { background: #FFEDD5; }

.text-green-dark { color: #0D631B; }
.text-purple { color: #7E22CE; }
.text-blue { color: #005DB7; }
.text-amber { color: #D97706; }
.text-amber-dark { color: #B45309; }
.text-red { color: #BA1A1A; }

.bg-green-subtle { background: #E8F5E9; }
.bg-purple-subtle { background: #F3E8FF; }
.bg-blue-subtle { background: #E0F2FE; }
.bg-amber-subtle { background: #FEF3C7; }
.bg-orange-subtle { background: #FFEDD5; }

.delta-chip {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
  margin-left: auto;
}

.status-chip {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}
.chip-green { background: #DCFCE7; color: #16A34A; }
.chip-orange { background: #FFEDD5; color: #EA580C; }

/* Empty State Card Styling */
.empty-history-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 56px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 10px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  width: 100%;
  box-sizing: border-box;
}

:global(.dark-theme) .empty-history-card {
  border-color: rgba(30, 58, 43, 0.6);
  background: var(--color-bg-card, #13271C);
}

.empty-icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #E8F5E9;
  border: 1px solid rgba(13, 99, 27, 0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 4px;
  flex-shrink: 0;
}

:global(.dark-theme) .empty-icon-circle {
  background: rgba(13, 99, 27, 0.25);
  border-color: rgba(74, 222, 128, 0.3);
}

:global(.dark-theme) .empty-icon-circle svg {
  stroke: #4ADE80 !important;
}

.empty-title {
  font-size: 17px;
  font-weight: 800;
  color: var(--color-text-title);
  margin: 0;
}

.empty-desc {
  font-size: 13.5px;
  color: var(--color-text-muted);
  max-width: 440px;
  margin: 0;
  line-height: 1.55;
}

.empty-actions-row {
  margin-top: 10px;
  display: flex;
  gap: 10px;
}

.btn-reset-filter {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  border-radius: 8px;
  border: 1px solid #0D631B;
  background: #E8F5E9;
  color: #0D631B;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-reset-filter:hover {
  background: #0D631B;
  color: #FFFFFF;
}

:global(.dark-theme) .btn-reset-filter {
  background: rgba(13, 99, 27, 0.25);
  color: #4ADE80;
  border-color: #4ADE80;
}

:global(.dark-theme) .btn-reset-filter:hover {
  background: #0D631B;
  color: #FFFFFF;
}

.btn-empty-start {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-empty-start:hover {
  background: #094713;
}

.batch-list-wrapper {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* ========================================================================= */
/* PAGINATION STYLES */
/* ========================================================================= */
.history-pagination-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 12px;
  padding: 12px 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  gap: 20px;
  box-sizing: border-box;
  flex-wrap: wrap;
}

.table-pagination-border {
  border-top: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 0 0 14px 14px;
  border-left: none;
  border-right: none;
  border-bottom: none;
  background: transparent;
  padding: 14px 20px;
}

:global(.dark-theme) .history-pagination-container {
  background: var(--color-bg-card, #13271C);
  border-color: rgba(30, 58, 43, 0.6);
}

:global(.dark-theme) .table-pagination-border {
  border-top-color: rgba(30, 58, 43, 0.6);
  background: transparent;
}

.pagination-info-side {
  display: flex;
  align-items: center;
  gap: 24px;
  flex-shrink: 0;
}

.pagination-summary {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.rows-per-page-selector {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
  flex-shrink: 0;
}

.rpp-label {
  font-size: 12.5px;
  font-weight: 500;
  color: var(--color-text-muted);
  white-space: nowrap;
  display: inline-block;
}

.rpp-select {
  padding: 5px 10px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 700;
  border: 1px solid rgba(203, 213, 225, 0.6);
  background: var(--color-white);
  color: var(--color-text-title);
  cursor: pointer;
  outline: none;
  transition: border-color 0.15s ease;
  white-space: nowrap;
  flex-shrink: 0;
}

.rpp-select:hover, .rpp-select:focus {
  border-color: #0D631B;
}

:global(.dark-theme) .rpp-select {
  background: #0F172A;
  border-color: rgba(74, 222, 128, 0.25);
  color: #F8FAFC;
}

.pagination-nav-side {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-pag-nav {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  border: 1px solid rgba(203, 213, 225, 0.6);
  background: var(--color-white);
  color: var(--color-text-title);
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-pag-nav:hover:not(:disabled) {
  background: var(--color-bg-light);
  border-color: #94A3B8;
}

.btn-pag-nav:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

:global(.dark-theme) .btn-pag-nav {
  background: #0F172A;
  border-color: rgba(74, 222, 128, 0.25);
  color: #F8FAFC;
}

:global(.dark-theme) .btn-pag-nav:hover:not(:disabled) {
  background: rgba(13, 99, 27, 0.25);
  border-color: #4ADE80;
}

.pagination-page-pills {
  display: flex;
  align-items: center;
  gap: 4px;
}

.pag-num-btn {
  min-width: 32px;
  height: 32px;
  padding: 0 6px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12.5px;
  font-weight: 700;
  border: 1px solid rgba(203, 213, 225, 0.6);
  background: var(--color-white);
  color: var(--color-text-title);
  cursor: pointer;
  transition: all 0.15s ease;
}

.pag-num-btn:hover:not(.active) {
  background: var(--color-bg-light);
  border-color: #94A3B8;
}

.pag-num-btn.active {
  background: #0D631B !important;
  color: #FFFFFF !important;
  border-color: #0D631B !important;
  box-shadow: 0 2px 8px rgba(13, 99, 27, 0.25);
}

:global(.dark-theme) .pag-num-btn {
  background: #0F172A;
  border-color: rgba(74, 222, 128, 0.2);
  color: #F8FAFC;
}

:global(.dark-theme) .pag-num-btn:hover:not(.active) {
  background: rgba(13, 99, 27, 0.2);
  border-color: #4ADE80;
}

:global(.dark-theme) .pag-num-btn.active {
  background: #0D631B !important;
  color: #FFFFFF !important;
  border-color: #4ADE80 !important;
  box-shadow: 0 0 12px rgba(74, 222, 128, 0.35);
}

.pag-ellipsis {
  padding: 0 4px;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-muted);
}

.mobile-session-stack-wrap {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.m-pag-container {
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 10px;
  padding: 14px;
  border-radius: 12px;
}

.m-empty-card {
  padding: 40px 16px;
  margin: 4px 0;
}

@media (max-width: 1024px) {
  .history-kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}


/* Batch Selection & Multi-Batch Comparison Styles */
.batch-select-checkbox-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
}

.batch-checkbox {
  width: 17px;
  height: 17px;
  border-radius: 4px;
  border: 1.5px solid #CBD5E1;
  cursor: pointer;
  accent-color: #0D631B;
}

.batch-card-selected {
  border-color: #0284C7 !important;
  background: rgba(2, 132, 199, 0.03) !important;
  box-shadow: 0 0 0 1px #0284C7 !important;
}

.row-selected {
  background: rgba(2, 132, 199, 0.06) !important;
}

/* Floating Comparison Action Bar */
.floating-comparison-bar {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  background: #0F172A;
  color: #FFFFFF;
  padding: 12px 20px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  gap: 24px;
  box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.45);
  z-index: 1000;
  border: 1px solid rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
}

.floating-bar-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.floating-count-badge {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #0284C7;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
}

.floating-title {
  font-size: 13.5px;
  display: block;
  line-height: 1.2;
}

.floating-sub {
  font-size: 11px;
  color: #94A3B8;
  margin: 2px 0 0 0;
}

.floating-bar-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-floating-cancel {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #FFFFFF;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-floating-cancel:hover {
  background: rgba(255, 255, 255, 0.2);
}

.btn-floating-compare {
  background: #0284C7;
  border: none;
  color: #FFFFFF;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
}

.btn-floating-compare:hover {
  background: #0369A1;
  transform: translateY(-1px);
}

.slide-up-float-enter-active,
.slide-up-float-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-float-enter-from {
  opacity: 0;
  transform: translate(-50%, 30px);
}

.slide-up-float-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}

/* Mobile & Tablet Responsive Styles */
@media (max-width: 768px) {
  .history-main-content {
    padding: 0;
    gap: 16px;
  }

  .page-title {
    font-size: 22px;
  }

  .page-subtitle {
    font-size: 13.5px;
  }

  .page-header-row {
    flex-direction: column;
    gap: 12px;
  }

  .header-btns-cluster {
    width: 100%;
    align-items: stretch;
  }

  .header-btns-subrow {
    width: 100%;
    display: flex;
  }

  .header-btns-subrow .btn-export-secondary {
    flex: 1;
    text-align: center;
  }

  .history-kpi-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .hist-chart-card {
    padding: 16px;
    border-radius: 12px;
  }

  .filter-toolbar-card {
    padding: 12px 14px;
    gap: 12px;
  }

  .search-input-box {
    max-width: 100%;
    width: 100%;
  }

  .filter-pills-row {
    width: 100%;
    overflow-x: auto;
    flex-wrap: nowrap;
    padding-bottom: 2px;
    -webkit-overflow-scrolling: touch;
  }

  .filter-pill {
    white-space: nowrap;
  }

  .toolbar-right-actions {
    width: 100%;
    justify-content: space-between;
  }

  .batches-grid-container {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .batch-card {
    padding: 14px;
    border-radius: 12px;
  }

  .history-pagination-container {
    padding: 12px 14px;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .pagination-info-side {
    flex-direction: column;
    gap: 6px;
    align-items: center;
  }

  .floating-comparison-bar {
    width: 92%;
    max-width: 92%;
    flex-direction: column;
    gap: 12px;
    bottom: 16px;
    padding: 12px 16px;
  }

  .floating-bar-info {
    width: 100%;
  }

  .floating-bar-actions {
    width: 100%;
    justify-content: space-between;
  }
}

@media (max-width: 480px) {
  .history-kpi-val-row {
    flex-wrap: wrap;
  }

  .hist-kpi-num {
    font-size: 22px;
  }

  .card-metrics-grid {
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }
}

/* History Skeleton / Content Transition */
.fade-history-enter-active,
.fade-history-leave-active {
  transition: opacity 0.2s ease;
}

.fade-history-enter-from,
.fade-history-leave-to {
  opacity: 0;
}
</style>
