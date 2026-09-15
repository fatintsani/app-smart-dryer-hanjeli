<template>
  <div class="history-detail-skeleton-wrapper" aria-hidden="true">
    <!-- Header Action Row Placeholder -->
    <div class="hds-header-row">
      <div class="hds-title-block">
        <div class="hds-shimmer hds-back-btn"></div>
        <div class="hds-title-row">
          <div class="hds-shimmer hds-title"></div>
          <div class="hds-shimmer hds-badge-pill"></div>
          <div class="hds-shimmer hds-badge-pill"></div>
        </div>
        <div class="hds-shimmer hds-subtitle"></div>
      </div>
      <div class="hds-actions-cluster">
        <div class="hds-shimmer hds-action-btn-main"></div>
        <div class="hds-aux-btns">
          <div class="hds-shimmer hds-action-btn-aux"></div>
          <div class="hds-shimmer hds-action-btn-aux"></div>
          <div class="hds-shimmer hds-action-btn-aux"></div>
        </div>
      </div>
    </div>

    <!-- 6 KPI Matrix Cards -->
    <div class="hds-kpi-grid-6">
      <div v-for="i in 6" :key="i" class="hds-kpi-card">
        <div class="hds-kpi-top">
          <div class="hds-shimmer hds-kpi-lbl"></div>
          <div class="hds-shimmer hds-kpi-icon"></div>
        </div>
        <div class="hds-kpi-mid">
          <div class="hds-shimmer hds-kpi-num"></div>
          <div class="hds-shimmer hds-kpi-chip"></div>
        </div>
        <div class="hds-shimmer hds-kpi-sub"></div>
      </div>
    </div>

    <!-- Master Telemetry Analysis Card -->
    <div class="hds-chart-card">
      <div class="hds-chart-top">
        <div class="hds-chart-titles">
          <div class="hds-shimmer hds-chart-title"></div>
          <div class="hds-shimmer hds-chart-sub"></div>
        </div>
        <div class="hds-chart-toggles">
          <div class="hds-shimmer hds-toggle-btn" style="width: 120px;"></div>
          <div class="hds-shimmer hds-toggle-btn" style="width: 100px;"></div>
        </div>
      </div>
      <div class="hds-shimmer hds-chart-canvas"></div>
    </div>

    <!-- Dual Subpanels: Thermal & Psychrometric Kinetics -->
    <div class="hds-subpanels-grid">
      <div class="hds-subpanel-card">
        <div class="hds-shimmer hds-subpanel-title"></div>
        <div class="hds-shimmer hds-subpanel-chart"></div>
        <div class="hds-shimmer hds-subpanel-desc"></div>
      </div>
      <div class="hds-subpanel-card">
        <div class="hds-shimmer hds-subpanel-title"></div>
        <div class="hds-shimmer hds-subpanel-chart"></div>
        <div class="hds-shimmer hds-subpanel-desc"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton loading component for HistoryDetailView (Detail Riwayat Batch)
</script>

<style scoped>
.history-detail-skeleton-wrapper {
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
  .history-detail-skeleton-wrapper {
    padding: 0;
    gap: 16px;
  }
}

/* Shimmer Animation Token */
.hds-shimmer {
  position: relative;
  overflow: hidden;
  background-color: #E2E8F0;
  border-radius: 8px;
}

.hds-shimmer::after {
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
  animation: hdsShimmerSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  content: '';
}

:global(.dark-theme) .hds-shimmer {
  background-color: #113847 !important;
}

:global(.dark-theme) .hds-shimmer::after {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.05) 40%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 60%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes hdsShimmerSweep {
  100% {
    transform: translateX(100%);
  }
}

/* Header */
.hds-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

@media (max-width: 900px) {
  .hds-header-row {
    flex-direction: column;
  }
}

.hds-title-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hds-back-btn {
  width: 140px;
  height: 22px;
  border-radius: 6px;
  margin-bottom: 4px;
}

.hds-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.hds-title {
  width: 260px;
  height: 28px;
  border-radius: 6px;
}

.hds-badge-pill {
  width: 110px;
  height: 24px;
  border-radius: 20px;
}

.hds-subtitle {
  width: 460px;
  height: 15px;
  border-radius: 4px;
}

.hds-actions-cluster {
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: flex-end;
}

@media (max-width: 900px) {
  .hds-actions-cluster {
    align-items: flex-start;
    width: 100%;
  }
}

.hds-action-btn-main {
  width: 160px;
  height: 40px;
  border-radius: 10px;
}

.hds-aux-btns {
  display: flex;
  gap: 8px;
}

.hds-action-btn-aux {
  width: 100px;
  height: 34px;
  border-radius: 8px;
}

/* 6 KPI Cards Grid */
.hds-kpi-grid-6 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

@media (max-width: 1024px) {
  .hds-kpi-grid-6 {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .hds-kpi-grid-6 {
    grid-template-columns: 1fr;
  }
}

.hds-kpi-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

:global(.dark-theme) .hds-kpi-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hds-kpi-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.hds-kpi-lbl {
  width: 130px;
  height: 12px;
  border-radius: 4px;
}

.hds-kpi-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.hds-kpi-mid {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.hds-kpi-num {
  width: 90px;
  height: 30px;
  border-radius: 6px;
}

.hds-kpi-chip {
  width: 70px;
  height: 18px;
  border-radius: 12px;
}

.hds-kpi-sub {
  width: 140px;
  height: 12px;
  border-radius: 4px;
}

/* Chart Card */
.hds-chart-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

:global(.dark-theme) .hds-chart-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hds-chart-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.hds-chart-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.hds-chart-title {
  width: 280px;
  height: 20px;
  border-radius: 4px;
}

.hds-chart-sub {
  width: 420px;
  height: 13px;
  border-radius: 4px;
}

.hds-chart-toggles {
  display: flex;
  gap: 8px;
}

.hds-toggle-btn {
  height: 32px;
  border-radius: 8px;
}

.hds-chart-canvas {
  width: 100%;
  height: 260px;
  border-radius: 12px;
}

/* Subpanels */
.hds-subpanels-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

@media (max-width: 900px) {
  .hds-subpanels-grid {
    grid-template-columns: 1fr;
  }
}

.hds-subpanel-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

:global(.dark-theme) .hds-subpanel-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.hds-subpanel-title {
  width: 200px;
  height: 16px;
  border-radius: 4px;
}

.hds-subpanel-chart {
  width: 100%;
  height: 130px;
  border-radius: 10px;
}

.hds-subpanel-desc {
  width: 90%;
  height: 12px;
  border-radius: 4px;
}
</style>
