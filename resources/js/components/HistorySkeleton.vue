<template>
  <div class="history-skeleton-wrapper" aria-hidden="true">
    <!-- Header & Action Row Placeholder -->
    <div class="hs-header-row">
      <div class="hs-title-block">
        <div class="hs-shimmer hs-title"></div>
        <div class="hs-shimmer hs-subtitle"></div>
      </div>
      <div class="hs-export-actions">
        <div class="hs-shimmer hs-btn-export" style="width: 140px;"></div>
        <div class="hs-shimmer hs-btn-export" style="width: 140px;"></div>
      </div>
    </div>

    <!-- 4 Historical KPI Cards -->
    <div class="hs-kpi-grid">
      <div v-for="i in 4" :key="i" class="hs-kpi-card">
        <div class="hs-kpi-top">
          <div class="hs-shimmer hs-kpi-lbl"></div>
          <div class="hs-shimmer hs-kpi-icon"></div>
        </div>
        <div class="hs-kpi-mid">
          <div class="hs-shimmer hs-kpi-num"></div>
          <div class="hs-shimmer hs-kpi-chip"></div>
        </div>
        <div class="hs-shimmer hs-kpi-sub"></div>
      </div>
    </div>

    <!-- Comparative Batch Performance Chart Card -->
    <div class="hs-chart-card">
      <div class="hs-chart-top">
        <div class="hs-chart-titles">
          <div class="hs-shimmer hs-chart-title"></div>
          <div class="hs-shimmer hs-chart-sub"></div>
        </div>
        <div class="hs-shimmer hs-badge-pill"></div>
      </div>
      <div class="hs-shimmer hs-chart-canvas"></div>
      <div class="hs-legend-row">
        <div class="hs-shimmer hs-legend-pill" style="width: 130px;"></div>
        <div class="hs-shimmer hs-legend-pill" style="width: 160px;"></div>
        <div class="hs-shimmer hs-legend-pill" style="width: 140px;"></div>
      </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="hs-filter-toolbar">
      <div class="hs-shimmer hs-search-box"></div>
      <div class="hs-filter-pills">
        <div class="hs-shimmer hs-pill" style="width: 80px;"></div>
        <div class="hs-shimmer hs-pill" style="width: 90px;"></div>
        <div class="hs-shimmer hs-pill" style="width: 90px;"></div>
      </div>
      <div class="hs-shimmer hs-date-picker"></div>
    </div>

    <!-- Batch Records Table Card -->
    <div class="hs-table-card">
      <!-- Table Header -->
      <div class="hs-table-header-row">
        <div class="hs-shimmer hs-th" style="width: 15%;"></div>
        <div class="hs-shimmer hs-th" style="width: 25%;"></div>
        <div class="hs-shimmer hs-th" style="width: 18%;"></div>
        <div class="hs-shimmer hs-th" style="width: 14%;"></div>
        <div class="hs-shimmer hs-th" style="width: 12%;"></div>
        <div class="hs-shimmer hs-th" style="width: 16%;"></div>
      </div>

      <!-- Table Rows (5 Placeholder rows) -->
      <div v-for="r in 5" :key="r" class="hs-table-row">
        <div class="hs-td-batch">
          <div class="hs-shimmer hs-batch-code"></div>
          <div class="hs-shimmer hs-batch-tray"></div>
        </div>
        <div class="hs-td-variety">
          <div class="hs-shimmer hs-variety-name"></div>
          <div class="hs-shimmer hs-variety-date"></div>
        </div>
        <div class="hs-td-moisture">
          <div class="hs-shimmer hs-moisture-val"></div>
          <div class="hs-shimmer hs-moisture-bar"></div>
        </div>
        <div class="hs-td-weight">
          <div class="hs-shimmer hs-weight-val"></div>
          <div class="hs-shimmer hs-weight-sub"></div>
        </div>
        <div class="hs-td-status">
          <div class="hs-shimmer hs-status-chip"></div>
        </div>
        <div class="hs-td-action">
          <div class="hs-shimmer hs-action-btn"></div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="hs-pagination-row">
        <div class="hs-shimmer hs-pag-info"></div>
        <div class="hs-pag-btns">
          <div class="hs-shimmer hs-pag-btn"></div>
          <div class="hs-shimmer hs-pag-btn"></div>
          <div class="hs-shimmer hs-pag-btn"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton loading component for HistoryView (Riwayat Batch Pengeringan)
</script>

<style scoped>
.history-skeleton-wrapper {
  width: 100%;
  max-width: 1280px;
  margin: 0 auto;
  padding: 32px 40px 60px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

@media (max-width: 768px) {
  .history-skeleton-wrapper {
    padding: 0;
    gap: 16px;
  }
}

/* Shimmer Animation Token */
.hs-shimmer {
  position: relative;
  overflow: hidden;
  background-color: #E2E8F0;
  border-radius: 8px;
}

.hs-shimmer::after {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transform: translateX(-100%);
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.45) 40%,
    rgba(255, 255, 255, 0.7) 50%,
    rgba(255, 255, 255, 0.45) 60%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: hsShimmerSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  content: '';
}

:global(.dark-theme) .hs-shimmer {
  background-color: #113847 !important;
}

:global(.dark-theme) .hs-shimmer::after {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.05) 40%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 60%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes hsShimmerSweep {
  100% {
    transform: translateX(100%);
  }
}

/* Header */
.hs-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

@media (max-width: 768px) {
  .hs-header-row {
    flex-direction: column;
  }
}

.hs-title-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hs-title {
  width: 280px;
  height: 28px;
  border-radius: 6px;
}

.hs-subtitle {
  width: 440px;
  height: 15px;
  border-radius: 4px;
}

.hs-export-actions {
  display: flex;
  gap: 10px;
}

.hs-btn-export {
  height: 40px;
  border-radius: 10px;
}

/* 4 Historical KPI Cards Grid */
.hs-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

@media (max-width: 1100px) {
  .hs-kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .hs-kpi-grid {
    grid-template-columns: 1fr;
  }
}

.hs-kpi-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

:global(.dark-theme) .hs-kpi-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hs-kpi-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.hs-kpi-lbl {
  width: 140px;
  height: 12px;
  border-radius: 4px;
}

.hs-kpi-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.hs-kpi-mid {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.hs-kpi-num {
  width: 80px;
  height: 30px;
  border-radius: 6px;
}

.hs-kpi-chip {
  width: 70px;
  height: 18px;
  border-radius: 12px;
}

.hs-kpi-sub {
  width: 160px;
  height: 12px;
  border-radius: 4px;
}

/* Chart Card */
.hs-chart-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

:global(.dark-theme) .hs-chart-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hs-chart-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.hs-chart-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.hs-chart-title {
  width: 300px;
  height: 20px;
  border-radius: 4px;
}

.hs-chart-sub {
  width: 440px;
  height: 13px;
  border-radius: 4px;
}

.hs-badge-pill {
  width: 130px;
  height: 24px;
  border-radius: 20px;
}

.hs-chart-canvas {
  width: 100%;
  height: 220px;
  border-radius: 12px;
}

.hs-legend-row {
  display: flex;
  gap: 12px;
}

.hs-legend-pill {
  height: 20px;
  border-radius: 10px;
}

/* Filter Toolbar */
.hs-filter-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  background: #FFFFFF;
  padding: 14px 20px;
  border-radius: 14px;
  border: 1px solid rgba(226, 232, 240, 0.8);
  flex-wrap: wrap;
}

:global(.dark-theme) .hs-filter-toolbar {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hs-search-box {
  width: 280px;
  height: 40px;
  border-radius: 10px;
}

.hs-filter-pills {
  display: flex;
  gap: 8px;
}

.hs-pill {
  height: 36px;
  border-radius: 20px;
}

.hs-date-picker {
  width: 180px;
  height: 38px;
  border-radius: 10px;
}

/* Table Card */
.hs-table-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  overflow-x: auto;
}

:global(.dark-theme) .hs-table-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hs-table-header-row {
  display: flex;
  align-items: center;
  padding: 10px 14px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.8);
  gap: 16px;
}

:global(.dark-theme) .hs-table-header-row {
  border-color: #1E4E61 !important;
}

.hs-th {
  height: 14px;
  border-radius: 4px;
}

.hs-table-row {
  display: flex;
  align-items: center;
  padding: 14px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.4);
  gap: 16px;
}

:global(.dark-theme) .hs-table-row {
  border-color: rgba(30, 78, 97, 0.4) !important;
}

.hs-td-batch {
  width: 15%;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hs-batch-code {
  width: 100px;
  height: 16px;
  border-radius: 4px;
}

.hs-batch-tray {
  width: 70px;
  height: 12px;
  border-radius: 3px;
}

.hs-td-variety {
  width: 25%;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hs-variety-name {
  width: 160px;
  height: 15px;
  border-radius: 4px;
}

.hs-variety-date {
  width: 110px;
  height: 11px;
  border-radius: 3px;
}

.hs-td-moisture {
  width: 18%;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.hs-moisture-val {
  width: 90px;
  height: 14px;
  border-radius: 4px;
}

.hs-moisture-bar {
  width: 100%;
  height: 6px;
  border-radius: 3px;
}

.hs-td-weight {
  width: 14%;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hs-weight-val {
  width: 75px;
  height: 14px;
  border-radius: 4px;
}

.hs-weight-sub {
  width: 60px;
  height: 11px;
  border-radius: 3px;
}

.hs-td-status {
  width: 12%;
}

.hs-status-chip {
  width: 75px;
  height: 24px;
  border-radius: 12px;
}

.hs-td-action {
  width: 16%;
  display: flex;
  justify-content: flex-end;
}

.hs-action-btn {
  width: 95px;
  height: 34px;
  border-radius: 8px;
}

/* Pagination Row */
.hs-pagination-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 10px;
}

.hs-pag-info {
  width: 140px;
  height: 14px;
  border-radius: 4px;
}

.hs-pag-btns {
  display: flex;
  gap: 6px;
}

.hs-pag-btn {
  width: 34px;
  height: 34px;
  border-radius: 8px;
}
</style>
