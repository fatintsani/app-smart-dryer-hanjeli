<template>
  <div class="active-drying-skeleton-wrapper" aria-hidden="true">
    <!-- Header Placeholder -->
    <div class="ads-header">
      <div class="ads-title-block">
        <div class="ads-shimmer ads-back-btn"></div>
        <div class="ads-title-row">
          <div class="ads-shimmer ads-title"></div>
          <div class="ads-shimmer ads-batch-tag"></div>
        </div>
        <div class="ads-shimmer ads-subtitle"></div>
      </div>
      <div class="ads-actions-cluster">
        <div class="ads-shimmer ads-action-btn-main"></div>
        <div class="ads-aux-btns">
          <div class="ads-shimmer ads-action-btn-aux"></div>
          <div class="ads-shimmer ads-action-btn-aux"></div>
        </div>
      </div>
    </div>

    <!-- Duration, Progress & Mass Balance Card -->
    <div class="ads-progress-card">
      <div class="ads-progress-metrics">
        <div class="ads-dur-block">
          <div class="ads-shimmer ads-dur-icon"></div>
          <div class="ads-dur-texts">
            <div class="ads-shimmer ads-lbl" style="width: 90px;"></div>
            <div class="ads-shimmer ads-clock-val" style="width: 140px;"></div>
          </div>
        </div>
        <div class="ads-dur-block">
          <div class="ads-dur-texts">
            <div class="ads-shimmer ads-lbl" style="width: 110px;"></div>
            <div class="ads-shimmer ads-stat-val" style="width: 120px;"></div>
          </div>
        </div>
        <div class="ads-dur-block">
          <div class="ads-dur-texts">
            <div class="ads-shimmer ads-lbl" style="width: 100px;"></div>
            <div class="ads-shimmer ads-stat-val" style="width: 110px;"></div>
          </div>
        </div>
      </div>
      <div class="ads-shimmer ads-track-bar"></div>
      <div class="ads-track-labels">
        <div class="ads-shimmer ads-track-lbl" style="width: 110px;"></div>
        <div class="ads-shimmer ads-track-lbl" style="width: 160px;"></div>
        <div class="ads-shimmer ads-track-lbl" style="width: 110px;"></div>
      </div>
    </div>

    <!-- 6 Live Telemetry KPI Cards -->
    <div class="ads-stats-grid-6">
      <div v-for="i in 6" :key="i" class="ads-stat-card">
        <div class="ads-stat-top">
          <div class="ads-shimmer ads-stat-lbl"></div>
          <div class="ads-shimmer ads-stat-icon"></div>
        </div>
        <div class="ads-stat-mid">
          <div class="ads-shimmer ads-stat-num"></div>
          <div class="ads-shimmer ads-stat-chip"></div>
        </div>
        <div class="ads-shimmer ads-stat-footer"></div>
      </div>
    </div>

    <!-- Master Waveform Chart Card -->
    <div class="ads-chart-card">
      <div class="ads-chart-top">
        <div class="ads-chart-titles">
          <div class="ads-shimmer ads-chart-title"></div>
          <div class="ads-shimmer ads-chart-sub"></div>
        </div>
        <div class="ads-chart-toggles">
          <div class="ads-shimmer ads-toggle-btn" style="width: 110px;"></div>
          <div class="ads-shimmer ads-toggle-btn" style="width: 90px;"></div>
        </div>
      </div>
      <div class="ads-shimmer ads-chart-canvas"></div>
    </div>

    <!-- 2 Subpanels: Thermal & Psychrometric -->
    <div class="ads-subpanels-grid">
      <div class="ads-subpanel-card">
        <div class="ads-shimmer ads-subpanel-title"></div>
        <div class="ads-shimmer ads-subpanel-chart"></div>
        <div class="ads-shimmer ads-subpanel-desc"></div>
      </div>
      <div class="ads-subpanel-card">
        <div class="ads-shimmer ads-subpanel-title"></div>
        <div class="ads-shimmer ads-subpanel-chart"></div>
        <div class="ads-shimmer ads-subpanel-desc"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton placeholder for Active Drying monitoring view
</script>

<style scoped>
.active-drying-skeleton-wrapper {
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
  .active-drying-skeleton-wrapper {
    padding: 0;
    gap: 16px;
  }
}

/* Shimmer Animation Token */
.ads-shimmer {
  position: relative;
  overflow: hidden;
  background-color: #E2E8F0;
  border-radius: 8px;
}

.ads-shimmer::after {
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
  animation: adsShimmerSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  content: '';
}

:global(.dark-theme) .ads-shimmer {
  background-color: #113847 !important;
}

:global(.dark-theme) .ads-shimmer::after {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.05) 40%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 60%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes adsShimmerSweep {
  100% {
    transform: translateX(100%);
  }
}

/* Header */
.ads-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

@media (max-width: 900px) {
  .ads-header {
    flex-direction: column;
  }
}

.ads-title-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ads-back-btn {
  width: 140px;
  height: 22px;
  border-radius: 6px;
  margin-bottom: 4px;
}

.ads-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ads-title {
  width: 320px;
  height: 28px;
  border-radius: 6px;
}

.ads-batch-tag {
  width: 120px;
  height: 24px;
  border-radius: 20px;
}

.ads-subtitle {
  width: 480px;
  height: 15px;
  border-radius: 4px;
}

.ads-actions-cluster {
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: flex-end;
}

@media (max-width: 900px) {
  .ads-actions-cluster {
    align-items: flex-start;
    width: 100%;
  }
}

.ads-action-btn-main {
  width: 180px;
  height: 42px;
  border-radius: 10px;
}

.ads-aux-btns {
  display: flex;
  gap: 8px;
}

.ads-action-btn-aux {
  width: 140px;
  height: 36px;
  border-radius: 8px;
}

/* Duration & Progress Card */
.ads-progress-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

:global(.dark-theme) .ads-progress-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.ads-progress-metrics {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.ads-dur-block {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ads-dur-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  flex-shrink: 0;
}

.ads-dur-texts {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.ads-lbl {
  height: 11px;
  border-radius: 3px;
}

.ads-clock-val {
  height: 28px;
  border-radius: 6px;
}

.ads-stat-val {
  height: 22px;
  border-radius: 6px;
}

.ads-track-bar {
  width: 100%;
  height: 12px;
  border-radius: 6px;
}

.ads-track-labels {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ads-track-lbl {
  height: 14px;
  border-radius: 4px;
}

/* 6 KPI Cards Grid */
.ads-stats-grid-6 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

@media (max-width: 1024px) {
  .ads-stats-grid-6 {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .ads-stats-grid-6 {
    grid-template-columns: 1fr;
  }
}

.ads-stat-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

:global(.dark-theme) .ads-stat-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.ads-stat-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ads-stat-lbl {
  width: 130px;
  height: 12px;
  border-radius: 4px;
}

.ads-stat-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.ads-stat-mid {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.ads-stat-num {
  width: 90px;
  height: 32px;
  border-radius: 6px;
}

.ads-stat-chip {
  width: 70px;
  height: 18px;
  border-radius: 12px;
}

.ads-stat-footer {
  width: 140px;
  height: 12px;
  border-radius: 4px;
}

/* Chart Card */
.ads-chart-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

:global(.dark-theme) .ads-chart-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.ads-chart-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.ads-chart-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.ads-chart-title {
  width: 260px;
  height: 20px;
  border-radius: 4px;
}

.ads-chart-sub {
  width: 380px;
  height: 13px;
  border-radius: 4px;
}

.ads-chart-toggles {
  display: flex;
  gap: 8px;
}

.ads-toggle-btn {
  height: 32px;
  border-radius: 8px;
}

.ads-chart-canvas {
  width: 100%;
  height: 280px;
  border-radius: 12px;
}

/* Subpanels Grid */
.ads-subpanels-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

@media (max-width: 900px) {
  .ads-subpanels-grid {
    grid-template-columns: 1fr;
  }
}

.ads-subpanel-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

:global(.dark-theme) .ads-subpanel-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.ads-subpanel-title {
  width: 200px;
  height: 16px;
  border-radius: 4px;
}

.ads-subpanel-chart {
  width: 100%;
  height: 130px;
  border-radius: 10px;
}

.ads-subpanel-desc {
  width: 90%;
  height: 12px;
  border-radius: 4px;
}
</style>
