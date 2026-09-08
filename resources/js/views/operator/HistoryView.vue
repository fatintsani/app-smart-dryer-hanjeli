<template>
  <div class="history-page">
    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="desktop-content">
      <!-- Header & Action Row -->
      <div class="page-header-row">
        <div class="page-header-group">
          <div class="title-with-badge">
            <h1 class="page-title">{{ $t('history.title') }}</h1>
          </div>
          <p class="page-subtitle">{{ $t('history.subtitle') }}</p>
        </div>

        <!-- Export Action Buttons -->
        <div class="export-actions-group">
          <button class="btn-export-secondary" @click="openExportModal('csv')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="8" y1="13" x2="16" y2="13"></line>
              <line x1="8" y1="17" x2="16" y2="17"></line>
              <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>{{ $t('common.downloadCsv') }}</span>
          </button>

          <button class="btn-export-primary" @click="openExportModal('pdf')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>{{ $t('common.downloadPdf') }}</span>
          </button>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TOP AGGREGATE HISTORICAL KPIS (4 CARDS) -->
      <!-- ========================================================================= -->
      <div class="history-kpi-grid">
        <div class="hist-kpi-card">
          <div class="hist-kpi-header">
            <span class="hist-kpi-lbl">TOTAL BATCH TERPROSES</span>
            <div class="icon-circle icon-green-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
          </div>
          <div class="hist-kpi-val-row">
            <span class="hist-kpi-num text-green-dark">{{ batchRecords.length }}</span>
            <span class="hist-kpi-unit">Sesi</span>
            <span class="delta-chip bg-green-subtle text-green-dark">{{ countByStatus('completed') }} Selesai</span>
          </div>
          <span class="hist-kpi-sub">{{ countByStatus('running') }} sesi aktif sedang berjalan</span>
        </div>

        <div class="hist-kpi-card">
          <div class="hist-kpi-header">
            <span class="hist-kpi-lbl">TOTAL MASSA GABAH KERING</span>
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
            <span class="delta-chip bg-purple-subtle text-purple">~{{ (aggregateTotalWeightKg / 1000).toFixed(2) }} Ton</span>
          </div>
          <span class="hist-kpi-sub">Total rendemen hasil panen hanjeli terverifikasi</span>
        </div>

        <div class="hist-kpi-card">
          <div class="hist-kpi-header">
            <span class="hist-kpi-lbl">RATA-RATA PENURUNAN KADAR AIR</span>
            <div class="icon-circle icon-blue-subtle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
              </svg>
            </div>
          </div>
          <div class="hist-kpi-val-row">
            <span class="hist-kpi-num text-blue">{{ aggregateAvgMoistureReduction }}</span>
            <span class="hist-kpi-unit">% ΔM</span>
            <span class="delta-chip bg-blue-subtle text-blue">SNI ≤ 12%</span>
          </div>
          <span class="hist-kpi-sub">Rata-rata kadar air akhir: <strong>{{ aggregateAvgFinalMoisture }}%</strong></span>
        </div>

        <div class="hist-kpi-card">
          <div class="hist-kpi-header">
            <span class="hist-kpi-lbl">KONSUMSI LISTRIK & BIAYA</span>
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
          <span class="hist-kpi-sub">Efisiensi penghematan tenaga surya: <strong>~74%</strong></span>
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
              <h2 class="hist-chart-heading">Grafik Perbandingan Kinerja Antar Batch Riwayat</h2>
              <p class="hist-chart-sub">Perbandingan Massa Gabah (kg), Durasi Pengeringan (Jam), dan Kadar Air Akhir (%) pada sesi pengeringan terakhir</p>
            </div>
          </div>
          <span class="badge-pill bg-green-subtle text-green-dark">Komparasi Multi-Batch</span>
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
            <text x="830" y="155" font-size="9" fill="#9333EA" font-weight="600">Target SNI 12.0%</text>

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
                <span class="tt-label">Massa Gabah:</span>
                <span class="tt-val font-bold">{{ hoverChartItem.item.weight }}</span>
              </div>
              <div class="tooltip-item">
                <span class="tt-dot" style="background: #D97706;"></span>
                <span class="tt-label">Durasi:</span>
                <span class="tt-val">{{ hoverChartItem.item.duration }}</span>
              </div>
              <div class="tooltip-item">
                <span class="tt-dot" style="background: #9333EA;"></span>
                <span class="tt-label">Kadar Air Akhir:</span>
                <span class="tt-val font-bold text-purple">{{ hoverChartItem.item.finalHumidity }}</span>
              </div>
              <div class="tooltip-item">
                <span class="tt-dot" style="background: #E11D48;"></span>
                <span class="tt-label">Rata-rata Suhu:</span>
                <span class="tt-val">{{ hoverChartItem.item.avgTemp }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="chart-legend-row">
          <span class="legend-pill"><span class="legend-box" style="background: #0D631B; opacity: 0.3;"></span> Massa Gabah (kg)</span>
          <span class="legend-pill"><span class="legend-line" style="background: #D97706;"></span> Durasi Pengeringan (Jam)</span>
          <span class="legend-pill"><span class="legend-line" style="background: #9333EA;"></span> Kadar Air Akhir (%)</span>
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
            placeholder="Cari kode batch, varietas, atau operator..." 
            class="search-input"
          />
        </div>

        <!-- Filter Status Pills -->
        <div class="filter-pills-row">
          <button 
            class="pill-btn" 
            :class="{ active: currentFilter === 'all' }"
            @click="currentFilter = 'all'"
          >
            Semua Batch ({{ batchRecords.length }})
          </button>
          <button 
            class="pill-btn" 
            :class="{ active: currentFilter === 'running' }"
            @click="currentFilter = 'running'"
          >
            Aktif ({{ countByStatus('running') }})
          </button>
          <button 
            class="pill-btn" 
            :class="{ active: currentFilter === 'completed' }"
            @click="currentFilter = 'completed'"
          >
            Selesai ({{ countByStatus('completed') }})
          </button>
        </div>

        <!-- View Mode Toggle (Grid vs Table) -->
        <div class="view-toggle-group">
          <button 
            class="view-toggle-btn" 
            :class="{ active: viewMode === 'cards' }"
            @click="viewMode = 'cards'"
            title="Tampilan Kartu Rinci"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </button>
          <button 
            class="view-toggle-btn" 
            :class="{ active: viewMode === 'table' }"
            @click="viewMode = 'table'"
            title="Tampilan Tabel Lengkap"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- VIEW 1: BATCH CARDS LIST -->
      <!-- ========================================================================= -->
      <div v-if="viewMode === 'cards' && filteredBatches.length > 0" class="batch-list">
        <div 
          v-for="batch in filteredBatches" 
          :key="batch.id" 
          class="batch-card"
        >
          <div class="batch-card-left">
            <div 
              class="batch-icon-box"
              :class="batch.status === 'completed' ? 'icon-green-subtle' : 'icon-orange-subtle'"
            >
              <svg v-if="batch.status === 'completed'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#B45000" stroke-width="2.5">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
              </svg>
            </div>
            <div class="batch-details">
              <div class="batch-title-row">
                <h3 class="batch-name">{{ batch.id }}</h3>
                <span class="batch-variety-tag">{{ batch.cropVariety }}</span>
                <span 
                  class="badge-pill" 
                  :class="batch.status === 'completed' ? 'bg-green-subtle text-green-dark' : 'bg-orange-subtle text-orange'"
                >
                  {{ batch.statusLabel }}
                </span>
                <span class="weight-badge-mini">{{ batch.weight }}</span>
              </div>
              <div class="batch-time">{{ batch.date }} • {{ batch.time }} • Operator: <strong>{{ batch.operator }}</strong></div>
              
              <div class="batch-meta-row">
                <span class="meta-inline">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2">
                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                  </svg>
                  Rata-rata <strong>{{ batch.avgTemp }}</strong>
                </span>
                <span class="meta-inline">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                  </svg>
                  Kadar Air Akhir: <strong class="text-blue">{{ batch.finalHumidity }}</strong>
                </span>
                <span class="meta-inline">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M2 12h2M20 12h2"></path>
                  </svg>
                  Durasi: <strong>{{ batch.duration }}</strong>
                </span>
                <span class="meta-inline meta-energy">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                  </svg>
                  {{ batch.energy }} ({{ batch.cost }})
                </span>
              </div>
            </div>
          </div>
          
          <div class="batch-card-actions">
            <button 
              v-if="batch.status === 'running'" 
              class="btn-card-complete" 
              title="Selesaikan sesi pengeringan ini sekarang"
              @click.stop="openCompleteModal(batch)"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <span>Selesaikan</span>
            </button>
            <button class="btn-card-quick-export" title="Unduh data telemetri batch ini ke CSV" @click.stop="exportSingleBatchCSV(batch)">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>CSV</span>
            </button>
            <button class="btn-detail-outline" @click="openDetail(batch)">
              Lihat Detail
            </button>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- VIEW 2: DENSE AUDIT TABLE VIEW -->
      <!-- ========================================================================= -->
      <div v-else-if="viewMode === 'table' && filteredBatches.length > 0" class="batch-table-card">
        <div class="table-responsive-wrapper">
          <table class="history-data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Batch</th>
                <th>Varietas Hanjeli</th>
                <th>Status</th>
                <th>Tanggal / Waktu</th>
                <th>Bobot Gabah</th>
                <th>Rata-rata Suhu</th>
                <th>Kadar Air Akhir</th>
                <th>Durasi</th>
                <th>Konsumsi Listrik</th>
                <th>Operator</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(b, idx) in filteredBatches" :key="b.id">
                <td>{{ idx + 1 }}</td>
                <td class="font-bold text-dark">{{ b.id }}</td>
                <td>{{ b.cropVariety }}</td>
                <td>
                  <span class="status-chip" :class="b.status === 'completed' ? 'chip-green' : 'chip-orange'">
                    {{ b.statusLabel }}
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
                      title="Selesaikan" 
                      @click.stop="openCompleteModal(b)"
                    >
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                      </svg>
                    </button>
                    <button class="table-btn-icon" title="Lihat Detail" @click="openDetail(b)">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                    <button class="table-btn-icon" title="Ekspor CSV" @click="exportSingleBatchCSV(b)">
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
      </div>

      <!-- Empty State Desktop -->
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
        <h3 class="empty-title">Tidak Ada Riwayat Yang Cocok</h3>
        <p class="empty-desc">
          {{ searchQuery ? `Tidak ditemukan arsip sesi pengeringan dengan kata kunci "${searchQuery}".` : (currentFilter !== 'all' ? `Saat ini tidak ada sesi pengeringan dalam status "${currentFilter === 'running' ? 'Aktif' : 'Selesai'}".` : 'Belum ada data arsip sesi pengeringan yang tersimpan.') }}
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
            <span>Tampilkan Semua Batch</span>
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
            <span>Mulai Sesi Pengeringan Baru</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MOBILE VIEW LAYOUT -->
    <!-- ========================================================================= -->
    <div v-else class="mobile-content">
      <div class="mobile-header-area">
        <div class="mobile-title-row">
          <div>
            <h1 class="mobile-title">Riwayat Pengeringan</h1>
            <p class="mobile-sub">Arsip sesi pengeringan gabah Hanjeli.</p>
          </div>
          <button class="btn-m-export-icon" @click="openExportModal('pdf')" title="Ekspor Laporan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Ekspor</span>
          </button>
        </div>
      </div>

      <!-- Horizontal Filter Pills -->
      <div class="mobile-filter-pills">
        <button 
          class="m-pill" 
          :class="{ active: mobileFilter === 'all' }"
          @click="mobileFilter = 'all'"
        >
          Semua ({{ batchRecords.length }})
        </button>
        <button 
          class="m-pill" 
          :class="{ active: mobileFilter === 'running' }"
          @click="mobileFilter = 'running'"
        >
          Aktif
        </button>
        <button 
          class="m-pill" 
          :class="{ active: mobileFilter === 'completed' }"
          @click="mobileFilter = 'completed'"
        >
          Selesai
        </button>
      </div>

      <!-- Mobile Session Cards Stack -->
      <div v-if="mobileFilteredBatches.length > 0" class="mobile-session-stack">
        <div 
          v-for="batch in mobileFilteredBatches" 
          :key="batch.id"
          class="mobile-session-card"
          @click="openDetail(batch)"
        >
          <div class="m-card-top">
            <span 
              class="badge-pill"
              :class="batch.status === 'completed' ? 'bg-green-subtle text-green-dark' : 'bg-orange-subtle text-orange'"
            >
              {{ batch.statusLabel }}
            </span>
            <div class="m-card-top-right">
              <button 
                v-if="batch.status === 'running'" 
                class="m-btn-complete-quick" 
                title="Selesaikan"
                @click.stop="openCompleteModal(batch)"
              >
                Selesaikan
              </button>
              <div class="weight-tag">
                <strong>{{ batch.weight }}</strong>
                <span>Hanjeli</span>
              </div>
            </div>
          </div>

          <h3 class="m-session-title">{{ batch.id }}</h3>
          <p class="m-session-date">{{ batch.cropVariety }} • {{ batch.date }}</p>

          <div class="m-session-metrics-grid">
            <div class="m-metric">
              <span class="metric-lbl">Rata-rata Suhu</span>
              <span class="metric-val text-red">{{ batch.avgTemp }}</span>
            </div>
            <div class="m-metric">
              <span class="metric-lbl">Kadar Air Akhir</span>
              <span class="metric-val text-blue font-bold">{{ batch.finalHumidity }}</span>
            </div>
            <div class="m-metric">
              <span class="metric-lbl">Durasi</span>
              <span class="metric-val">{{ batch.duration }}</span>
            </div>
            <div class="m-metric">
              <span class="metric-lbl">Energi Listrik</span>
              <span class="metric-val text-green-dark">{{ batch.energy }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Empty State -->
      <div v-else class="empty-history-card m-empty-card">
        <div class="empty-icon-circle">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
        </div>
        <h3 class="empty-title">Tidak Ada Riwayat</h3>
        <p class="empty-desc">Tidak ditemukan arsip sesi pengeringan pada filter ini.</p>
        <button 
          v-if="mobileFilter !== 'all'" 
          type="button" 
          class="btn-reset-filter"
          @click="mobileFilter = 'all'"
        >
          Tampilkan Semua Batch
        </button>
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
                <h3 class="dialog-title">Selesaikan Sesi Pengeringan</h3>
                <p class="dialog-sub">Konfirmasi penyelesaian batch pengeringan gabah</p>
              </div>
            </div>
            <button class="btn-close-dialog" @click="isCompleteModalOpen = false" title="Tutup">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>

          <div class="export-section" style="padding: 16px 0;">
            <p style="font-size: 13.5px; color: var(--color-text-main); line-height: 1.6; margin-bottom: 14px;">
              Apakah Anda yakin ingin menyelesaikan proses pengeringan untuk batch <strong>{{ selectedBatchToComplete?.id }}</strong> ({{ selectedBatchToComplete?.cropVariety }})?
            </p>
            <div style="background: rgba(13, 99, 27, 0.06); border: 1px solid rgba(13, 99, 27, 0.18); border-radius: 10px; padding: 12px 14px; font-size: 12.5px; color: var(--color-text-main); display: flex; flex-direction: column; gap: 6px;">
              <div>• Status akan diperbarui permanen menjadi <strong>SELESAI (COMPLETED)</strong>.</div>
              <div>• Kadar air akhir akan dikunci pada <strong>{{ selectedBatchToComplete?.finalHumidity }}</strong>.</div>
              <div>• Rekapitulasi durasi, energi, dan estimasi biaya operasional akan diarsipkan.</div>
            </div>
          </div>

          <div class="modal-footer-actions">
            <button class="btn-cancel" @click="isCompleteModalOpen = false">Batal</button>
            <button class="btn-confirm-export" :disabled="isCompletingBatch" @click="executeCompleteBatch" style="background: #0D631B; border-color: #0D631B;">
              <svg v-if="!isCompletingBatch" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <span v-if="!isCompletingBatch">Ya, Selesaikan Batch</span>
              <span v-else>Menyelesaikan Batch...</span>
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
                <h3 class="dialog-title">Ekspor Rekap Data Riwayat</h3>
                <p class="dialog-sub">Unduh arsip komprehensif seluruh batch pengeringan</p>
              </div>
            </div>
            <button class="btn-close-dialog" @click="closeExportModal" title="Tutup">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>

          <!-- Format Choice -->
          <div class="export-section">
            <label class="section-label">Pilih Format Dokumen</label>
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
                  <div class="format-name">Laporan PDF Resmi</div>
                  <div class="format-desc">Format cetak A4 resmi dengan Kop Hanjeli, ringkasan KPI, dan kolom tanda tangan.</div>
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
                  <div class="format-name">Excel / CSV Spreadsheet</div>
                  <div class="format-desc">File data mentah terformat (*.csv) siap diolah di Microsoft Excel atau SPSS.</div>
                </div>
                <div class="format-radio-indicator">
                  <div class="radio-inner" v-if="selectedFormat === 'csv'"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Scope Choice -->
          <div class="export-section">
            <label class="section-label">Cakupan Data</label>
            <div class="scope-options-row">
              <label class="scope-label" :class="{ active: exportScope === 'all' }">
                <input type="radio" v-model="exportScope" value="all" />
                <span>Semua Riwayat ({{ batchRecords.length }} Batch)</span>
              </label>
              <label class="scope-label" :class="{ active: exportScope === 'filtered' }">
                <input type="radio" v-model="exportScope" value="filtered" />
                <span>Sesuai Filter & Pencarian ({{ effectiveFilteredBatches.length }} Batch)</span>
              </label>
            </div>
          </div>

          <!-- Modal Action Buttons -->
          <div class="modal-footer-actions">
            <button class="btn-cancel" @click="closeExportModal">Batal</button>
            <button class="btn-confirm-export" :disabled="isExporting" @click="executeExport">
              <svg v-if="!isExporting" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span v-if="!isExporting">{{ selectedFormat === 'pdf' ? 'Buka & Cetak PDF' : 'Unduh File Excel / CSV' }}</span>
              <span v-else>Memproses Dokumen...</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { batchService } from '../../services/batchService'

const router = useRouter()

const props = defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['select-batch'])

function openDetail(batch) {
  const targetId = batch.rawId || batch.id
  emit('select-batch', targetId)
  router.push(`/history/${targetId}`)
}

const batchRecords = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const currentFilter = ref('all')
const mobileFilter = ref('all')
const viewMode = ref('cards') // 'cards' or 'table'

// Complete Batch Modal state
const isCompleteModalOpen = ref(false)
const selectedBatchToComplete = ref(null)
const isCompletingBatch = ref(false)

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
    await fetchBatches()
  } catch (err) {
    alert(err.message || 'Gagal menyelesaikan batch pengeringan.')
  } finally {
    isCompletingBatch.value = false
  }
}

// Hover state for chart
const hoverChartItem = ref(null)

function transformBatch(b) {
  const isRunning = b.status === 'ACTIVE' || b.status === 'PAUSED'
  const isCompleted = b.status === 'COMPLETED'
  const startDate = new Date(b.startedAt || b.createdAt)
  const endDate = b.completedAt ? new Date(b.completedAt) : null
  
  let durationHrs = 8.0
  if (endDate) {
    durationHrs = Math.max(0.5, ((endDate - startDate) / (1000 * 60 * 60)))
  } else {
    durationHrs = Math.max(0.5, ((new Date() - startDate) / (1000 * 60 * 60)))
  }

  let avgTemp = '42.0°C'
  let tempNum = 42.0
  let finalHumidity = (b.finalMoisturePercent || b.targetMoisturePercent || 12.0).toFixed(1) + '%'
  let humidityNum = Number(b.finalMoisturePercent || b.targetMoisturePercent || 12.0)
  let initialHumidityNum = Number(b.initialMoisturePercent || 25.0)

  if (b.telemetry && b.telemetry.length > 0) {
    const sumT = b.telemetry.reduce((acc, t) => acc + (t.tempInternal || 0), 0)
    tempNum = Number((sumT / b.telemetry.length).toFixed(1))
    avgTemp = `${tempNum}°C`
    const lastT = b.telemetry[0]
    humidityNum = Number((lastT.grainMoisture || lastT.humidityInternal || 12.0).toFixed(1))
    finalHumidity = `${humidityNum}%`
  }

  const energyNum = Number((durationHrs * 1.45).toFixed(1))
  const costNum = Math.round(energyNum * 1500)

  return {
    id: b.batchCode || 'HJ-2026-001',
    rawId: b.id,
    cropVariety: b.cropVariety || 'Hanjeli Ketan Sukabumi',
    status: isCompleted ? 'completed' : isRunning ? 'running' : 'aborted',
    statusLabel: isCompleted ? 'SELESAI' : b.status === 'ACTIVE' ? 'SEDANG BERJALAN' : b.status,
    date: startDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }),
    time: `${startDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} - ${endDate ? endDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'Sekarang'}`,
    weight: `${b.initialWeightKg || 120} kg`,
    weightNum: Number(b.initialWeightKg || 120),
    avgTemp,
    tempNum,
    initialHumidityNum,
    finalHumidity,
    humidityNum,
    duration: `${durationHrs.toFixed(1)} Jam`,
    durationNum: durationHrs,
    energy: `${energyNum} kWh`,
    energyNum,
    cost: `Rp ${costNum.toLocaleString('id-ID')}`,
    operator: b.operatorName || b.operator?.name || 'Operator Green House'
  }
}

async function fetchBatches() {
  isLoading.value = true
  try {
    const data = await batchService.getAll()
    if (Array.isArray(data) && data.length > 0) {
      batchRecords.value = data.map(transformBatch)
    } else {
      // Fallback rich synthetic batch records if database is fresh
      batchRecords.value = [
        transformBatch({
          id: '1',
          batchCode: 'HJ-2026-001',
          cropVariety: 'Hanjeli Ketan Sukabumi (Grade A)',
          status: 'COMPLETED',
          initialWeightKg: 135,
          initialMoisturePercent: 24.5,
          finalMoisturePercent: 11.8,
          targetMoisturePercent: 12.0,
          startedAt: new Date(Date.now() - 3600000 * 28).toISOString(),
          completedAt: new Date(Date.now() - 3600000 * 20).toISOString(),
          operatorName: 'Ahmad Fauzi'
        }),
        transformBatch({
          id: '2',
          batchCode: 'HJ-2026-002',
          cropVariety: 'Hanjeli Pangan Olahan Tepung',
          status: 'COMPLETED',
          initialWeightKg: 150,
          initialMoisturePercent: 26.0,
          finalMoisturePercent: 12.0,
          targetMoisturePercent: 12.0,
          startedAt: new Date(Date.now() - 3600000 * 50).toISOString(),
          completedAt: new Date(Date.now() - 3600000 * 42).toISOString(),
          operatorName: 'Siti Rahma'
        }),
        transformBatch({
          id: '3',
          batchCode: 'HJ-2026-003',
          cropVariety: 'Hanjeli Teh',
          status: 'COMPLETED',
          initialWeightKg: 100,
          initialMoisturePercent: 22.0,
          finalMoisturePercent: 10.2,
          targetMoisturePercent: 10.0,
          startedAt: new Date(Date.now() - 3600000 * 74).toISOString(),
          completedAt: new Date(Date.now() - 3600000 * 67).toISOString(),
          operatorName: 'Budi Santoso'
        })
      ]
    }
  } catch (err) {
    console.warn('Failed to fetch batches from API:', err.message)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchBatches()
})

function countByStatus(status) {
  return batchRecords.value.filter(b => b.status === status).length
}

// Aggregate Calculations
const aggregateTotalWeightKg = computed(() => {
  return batchRecords.value.reduce((acc, b) => acc + (b.weightNum || 0), 0)
})

const aggregateAvgMoistureReduction = computed(() => {
  if (batchRecords.value.length === 0) return '12.8'
  const sum = batchRecords.value.reduce((acc, b) => acc + (b.initialHumidityNum - b.humidityNum), 0)
  return (sum / batchRecords.value.length).toFixed(1)
})

const aggregateAvgFinalMoisture = computed(() => {
  if (batchRecords.value.length === 0) return '11.8'
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

// Comparative Chart Coordinates (SVG)
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
    batch.cropVariety,
    batch.date,
    batch.weightNum,
    batch.statusLabel,
    batch.avgTemp,
    batch.finalHumidity,
    batch.duration,
    batch.energy,
    batch.cost,
    batch.operator
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
    alert('Tidak ada data yang dapat diekspor.')
    return
  }

  if (selectedFormat.value === 'csv') {
    const headers = ['No', 'No_Batch', 'Varietas', 'Tanggal', 'Bobot_Kg', 'Status', 'Rata_Suhu', 'Kadar_Air_Akhir', 'Durasi', 'Energi_kWh', 'Biaya', 'Operator']
    const rows = dataList.map((b, i) => [
      i + 1,
      b.id,
      b.cropVariety,
      b.date,
      b.weightNum,
      b.statusLabel,
      b.avgTemp,
      b.finalHumidity,
      b.duration,
      b.energy,
      b.cost,
      b.operator
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
    const printWindow = window.open('', '_blank', 'width=900,height=800')
    if (!printWindow) {
      alert('Harap izinkan popup di browser Anda untuk mencetak PDF.')
      return
    }

    const tableRowsHtml = dataList.map((b, i) => `
      <tr>
        <td>${i + 1}</td>
        <td><strong>${b.id}</strong></td>
        <td>${b.cropVariety}</td>
        <td>${b.date}</td>
        <td>${b.weight}</td>
        <td>${b.avgTemp}</td>
        <td><strong>${b.finalHumidity}</strong></td>
        <td>${b.duration}</td>
        <td>${b.energy}</td>
        <td>${b.operator}</td>
      </tr>
    `).join('')

    const printContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Rekap Riwayat Pengeringan Hanjeli</title>
        <style>
          @page { size: A4 landscape; margin: 12mm; }
          body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #071E27; }
          .header { border-bottom: 2px solid #0D631B; padding-bottom: 8px; margin-bottom: 14px; }
          .title { font-size: 16px; font-weight: 800; color: #0D631B; text-transform: uppercase; }
          table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10.5px; }
          th, td { border: 1px solid #CBD5E1; padding: 6px 8px; text-align: left; }
          th { background: #F1F5F9; font-weight: bold; }
        </style>
      </head>
      <body>
        <div class="header">
          <div class="title">REKAPITULASI ARSIP PENGERINGAN GABAH HANJELI</div>
          <small>Green House Smart Dryer • Desa Wisata Hanjeli Sukabumi</small>
        </div>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Batch</th>
              <th>Varietas Hanjeli</th>
              <th>Tanggal</th>
              <th>Bobot</th>
              <th>Suhu Rata2</th>
              <th>Kadar Air Akhir</th>
              <th>Durasi</th>
              <th>Energi Listrik</th>
              <th>Operator</th>
            </tr>
          </thead>
          <tbody>
            ${tableRowsHtml}
          </tbody>
        </table>
      </body>
      </html>
    `
    printWindow.document.write(printContent)
    printWindow.document.close()
    closeExportModal()
  }
}
</script>

<style scoped>
.history-page {
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

.export-actions-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-export-primary, .btn-export-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 16px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-export-primary {
  background: #0D631B;
  color: #FFFFFF;
  border: none;
}
.btn-export-primary:hover { background: #094713; }

.btn-export-secondary {
  background: var(--color-white);
  color: var(--color-text-title);
  border: 1px solid rgba(203, 213, 225, 0.5);
}
.btn-export-secondary:hover { background: var(--color-bg-light); border-color: #94A3B8; }

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
}

.pill-btn {
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

.pill-btn.active {
  background: #0D631B;
  color: #FFFFFF;
  border-color: #0D631B;
}

.view-toggle-group {
  display: flex;
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 10px;
  overflow: hidden;
}

.view-toggle-btn {
  padding: 8px 12px;
  border: none;
  background: transparent;
  color: var(--color-text-muted);
  cursor: pointer;
}

.view-toggle-btn.active {
  background: #E8F5E9;
  color: #0D631B;
}

/* Batch Cards */
.batch-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.batch-card {
  background: var(--color-white);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .batch-card {
  border-color: rgba(30, 58, 43, 0.6);
}

.batch-card:hover {
  border-color: #A5D6A7;
  box-shadow: 0 4px 12px rgba(13, 99, 27, 0.06);
}

.batch-card-left {
  display: flex;
  align-items: center;
  gap: 18px;
}

.batch-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.batch-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.batch-title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.batch-name {
  font-size: 16px;
  font-weight: 800;
  color: var(--color-text-title);
}

.batch-variety-tag {
  font-size: 13px;
  color: var(--color-text-muted);
}

.weight-badge-mini {
  font-size: 11px;
  font-weight: 700;
  color: #7E22CE;
  background: #F3E8FF;
  padding: 2px 8px;
  border-radius: 12px;
}

.batch-time {
  font-size: 12.5px;
  color: var(--color-text-muted);
}

.batch-meta-row {
  display: flex;
  gap: 16px;
  margin-top: 4px;
  font-size: 12.5px;
  flex-wrap: wrap;
}

.meta-inline {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  color: var(--color-text-muted);
}

.meta-inline strong {
  color: var(--color-text-title);
}

.batch-card-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-card-quick-export {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  background: var(--color-white);
  color: var(--color-text-muted);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
}

.btn-card-quick-export:hover {
  border-color: #0D631B;
  color: #0D631B;
}

.btn-detail-outline {
  padding: 8px 16px;
  border-radius: 8px;
  border: 1px solid #0D631B;
  background: #E8F5E9;
  color: #0D631B;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-detail-outline:hover {
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
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
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

.m-empty-card {
  padding: 40px 16px;
  margin: 4px 0;
}

@media (max-width: 1024px) {
  .history-kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
