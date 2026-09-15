<template>
  <div class="monitoring-skeleton-page">
    <div class="monitoring-skeleton-container">
      <!-- 1. Header Action Row Skeleton -->
      <div class="skeleton-header-row">
        <div class="skeleton-title-group">
          <div class="skeleton-bar skeleton-title-bar"></div>
          <div class="skeleton-bar skeleton-sub-bar"></div>
        </div>
        <div class="skeleton-header-actions">
          <div class="skeleton-bar skeleton-btn-export"></div>
          <div class="skeleton-bar skeleton-badge-live"></div>
        </div>
      </div>

      <!-- 2. Dual Connectivity Status Card Skeleton (Faithful 1:1 replica of ConnectionStatusCard) -->
      <div class="connection-status-card">
        <!-- Row 1: Source mode switcher -->
        <div class="source-mode-bar">
          <div class="source-mode-info">
            <div class="skeleton-bar" style="width: 170px; height: 13px;"></div>
            <div class="skeleton-bar" style="width: 320px; height: 11px; margin-top: 4px;"></div>
          </div>
          <div class="source-control-container">
            <div class="skeleton-bar" style="width: 280px; height: 34px; border-radius: 9px;"></div>
          </div>
        </div>

        <!-- Row 2: Hardware Connection Section -->
        <div class="hardware-connection-section">
          <div class="card-header-row">
            <div class="header-title-group">
              <div class="skeleton-bar" style="width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;"></div>
              <div class="title-texts">
                <div class="skeleton-bar" style="width: 165px; height: 15px;"></div>
                <div class="skeleton-bar" style="width: 200px; height: 12px; margin-top: 4px;"></div>
              </div>
            </div>
            <div class="skeleton-bar" style="width: 270px; height: 32px; border-radius: 9px;"></div>
          </div>

          <!-- Standby Notice Banner -->
          <div class="standby-notice-banner-skeleton">
            <div class="skeleton-bar" style="width: 18px; height: 18px; border-radius: 50%; flex-shrink: 0;"></div>
            <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
              <div class="skeleton-bar" style="width: 220px; height: 13px;"></div>
              <div class="skeleton-bar" style="width: 85%; height: 11px;"></div>
            </div>
          </div>

          <!-- 4 Connection Metric Boxes Grid -->
          <div class="connection-grid">
            <div class="info-box-skeleton">
              <div class="skeleton-bar" style="width: 90px; height: 10px;"></div>
              <div class="skeleton-bar" style="width: 120px; height: 15px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 110px; height: 10px; margin-top: 4px;"></div>
            </div>
            <div class="info-box-skeleton">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="skeleton-bar" style="width: 120px; height: 10px;"></div>
                <div class="skeleton-bar" style="width: 32px; height: 10px; border-radius: 4px;"></div>
              </div>
              <div class="skeleton-bar" style="width: 130px; height: 15px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 90px; height: 10px; margin-top: 4px;"></div>
            </div>
            <div class="info-box-skeleton">
              <div class="skeleton-bar" style="width: 135px; height: 10px;"></div>
              <div class="skeleton-bar" style="width: 140px; height: 15px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 120px; height: 10px; margin-top: 4px;"></div>
            </div>
            <div class="info-box-skeleton">
              <div class="skeleton-bar" style="width: 80px; height: 10px;"></div>
              <div class="skeleton-bar" style="width: 120px; height: 15px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 70px; height: 10px; margin-top: 4px;"></div>
            </div>
          </div>

          <!-- Bottom Action Row -->
          <div class="action-footer-row-skeleton">
            <div class="skeleton-bar" style="width: 290px; height: 13px;"></div>
            <div class="skeleton-bar" style="width: 165px; height: 34px; border-radius: 8px;"></div>
          </div>
        </div>
      </div>

      <!-- 3. 4 Top KPI Cards Grid Skeleton -->
      <div class="skeleton-stats-grid-4">
        <div v-for="i in 4" :key="'kpi-' + i" class="skeleton-card skeleton-stat-card">
          <div class="skeleton-stat-head">
            <div class="skeleton-bar" style="width: 110px; height: 12px;"></div>
            <div class="skeleton-circle skeleton-circle-sm"></div>
          </div>
          <div class="skeleton-stat-body">
            <div class="skeleton-bar skeleton-number-bar"></div>
            <div class="skeleton-bar" style="width: 40px; height: 14px;"></div>
          </div>
          <div class="skeleton-stat-footer">
            <div class="skeleton-bar" style="width: 80px; height: 18px; border-radius: 6px;"></div>
            <div class="skeleton-bar" style="width: 90px; height: 14px;"></div>
          </div>
        </div>
      </div>

      <!-- 4. Master Waveform Analytics Chart Card Skeleton -->
      <div class="skeleton-card skeleton-master-chart">
        <div class="skeleton-chart-header">
          <div class="skeleton-chart-title-box">
            <div class="skeleton-circle skeleton-circle-md"></div>
            <div>
              <div class="skeleton-bar" style="width: 320px; height: 18px;"></div>
              <div class="skeleton-bar" style="width: 420px; height: 12px; margin-top: 6px;"></div>
            </div>
          </div>
          <div class="skeleton-header-tools">
            <div class="skeleton-bar" style="width: 140px; height: 24px; border-radius: 12px;"></div>
            <div class="skeleton-bar" style="width: 90px; height: 32px; border-radius: 8px;"></div>
          </div>
        </div>

        <!-- Series Toggle Toolbar Skeleton -->
        <div class="skeleton-series-toolbar">
          <div v-for="t in 6" :key="'pill-' + t" class="skeleton-bar" style="width: 125px; height: 30px; border-radius: 8px;"></div>
        </div>

        <!-- Chart SVG Canvas Skeleton with Shimmer Grid -->
        <div class="skeleton-chart-canvas">
          <div class="skeleton-grid-line" style="top: 20px;"></div>
          <div class="skeleton-grid-line" style="top: 70px;"></div>
          <div class="skeleton-grid-line" style="top: 120px;"></div>
          <div class="skeleton-grid-line" style="top: 170px;"></div>
          <div class="skeleton-grid-line" style="top: 220px;"></div>
          <div class="skeleton-grid-line" style="top: 270px;"></div>

          <div class="skeleton-wave-container">
            <div class="skeleton-wave-mock wave-red"></div>
            <div class="skeleton-wave-mock wave-blue"></div>
          </div>
        </div>

        <!-- 4 Quick Analytics Cards Strip Skeleton -->
        <div class="skeleton-analytics-strip">
          <div v-for="a in 4" :key="'ana-' + a" class="skeleton-strip-item">
            <div class="skeleton-bar" style="width: 120px; height: 11px;"></div>
            <div class="skeleton-bar" style="width: 70px; height: 18px; margin-top: 6px;"></div>
          </div>
        </div>
      </div>

      <!-- 5. 3 Real-Time Microclimate Deep-Dive Cards Grid Skeleton -->
      <div class="skeleton-deepdive-grid">
        <!-- Card 1: Delta T -->
        <div class="skeleton-card skeleton-deepdive-card">
          <div class="skeleton-card-head">
            <div class="skeleton-bar" style="width: 160px; height: 16px;"></div>
            <div class="skeleton-bar" style="width: 60px; height: 20px; border-radius: 6px;"></div>
          </div>
          <div class="skeleton-mini-canvas"></div>
          <div class="skeleton-metric-rows">
            <div v-for="r in 3" :key="'r1-' + r" class="skeleton-metric-row">
              <div class="skeleton-bar" style="width: 110px; height: 12px;"></div>
              <div class="skeleton-bar" style="width: 60px; height: 14px;"></div>
            </div>
          </div>
        </div>

        <!-- Card 2: Psychrometric Matrix -->
        <div class="skeleton-card skeleton-deepdive-card">
          <div class="skeleton-card-head">
            <div class="skeleton-bar" style="width: 180px; height: 16px;"></div>
            <div class="skeleton-bar" style="width: 70px; height: 20px; border-radius: 6px;"></div>
          </div>
          <div class="skeleton-mini-canvas"></div>
          <div class="skeleton-metric-rows">
            <div v-for="r in 3" :key="'r2-' + r" class="skeleton-metric-row">
              <div class="skeleton-bar" style="width: 120px; height: 12px;"></div>
              <div class="skeleton-bar" style="width: 65px; height: 14px;"></div>
            </div>
          </div>
        </div>

        <!-- Card 3: Actuator State -->
        <div class="skeleton-card skeleton-deepdive-card">
          <div class="skeleton-card-head">
            <div class="skeleton-bar" style="width: 150px; height: 16px;"></div>
            <div class="skeleton-bar" style="width: 80px; height: 20px; border-radius: 6px;"></div>
          </div>
          <div class="skeleton-actuator-grid">
            <div v-for="ac in 4" :key="'ac-' + ac" class="skeleton-actuator-item">
              <div class="skeleton-circle skeleton-circle-sm"></div>
              <div style="flex: 1;">
                <div class="skeleton-bar" style="width: 80px; height: 12px;"></div>
                <div class="skeleton-bar" style="width: 50px; height: 16px; margin-top: 4px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 6. Live Ingestion Data Table Skeleton -->
      <div class="skeleton-card skeleton-table-card">
        <div class="skeleton-table-header">
          <div>
            <div class="skeleton-bar" style="width: 200px; height: 18px;"></div>
            <div class="skeleton-bar" style="width: 280px; height: 12px; margin-top: 4px;"></div>
          </div>
          <div class="skeleton-bar" style="width: 160px; height: 32px; border-radius: 8px;"></div>
        </div>

        <div class="skeleton-table-mock">
          <div class="skeleton-table-head-row">
            <div v-for="th in 6" :key="'th-' + th" class="skeleton-bar" style="height: 14px; flex: 1;"></div>
          </div>
          <div v-for="tr in 5" :key="'tr-' + tr" class="skeleton-table-body-row">
            <div v-for="td in 6" :key="'td-' + td" class="skeleton-bar" style="height: 12px; flex: 1;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.monitoring-skeleton-page {
  width: 100%;
  animation: fadeInSkeleton 0.3s ease-in-out;
}

@keyframes fadeInSkeleton {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

.monitoring-skeleton-container {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Shimmer Keyframe Animation */
.skeleton-bar,
.skeleton-circle,
.skeleton-wave-mock,
.skeleton-mini-canvas {
  position: relative;
  overflow: hidden;
  background: #E2E8F0;
  border-radius: 6px;
}

:global(.dark-theme) .skeleton-bar,
:global(.dark-theme) .skeleton-circle,
:global(.dark-theme) .skeleton-wave-mock,
:global(.dark-theme) .skeleton-mini-canvas {
  background: #113847 !important;
}

.skeleton-bar::after,
.skeleton-circle::after,
.skeleton-wave-mock::after,
.skeleton-mini-canvas::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transform: translateX(-100%);
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.45) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: skeletonShimmer 1.6s infinite ease-in-out;
}

:global(.dark-theme) .skeleton-bar::after,
:global(.dark-theme) .skeleton-circle::after,
:global(.dark-theme) .skeleton-wave-mock::after,
:global(.dark-theme) .skeleton-mini-canvas::after {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.08) 50%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes skeletonShimmer {
  100% {
    transform: translateX(100%);
  }
}

/* Base Card Skeleton */
.skeleton-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  box-sizing: border-box;
}

:global(.dark-theme) .skeleton-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

/* Header Skeleton */
.skeleton-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.skeleton-title-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.skeleton-title-bar {
  width: 280px;
  height: 28px;
  border-radius: 8px;
}

.skeleton-sub-bar {
  width: 360px;
  height: 14px;
}

.skeleton-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.skeleton-btn-export {
  width: 120px;
  height: 38px;
  border-radius: 8px;
}

.skeleton-badge-live {
  width: 160px;
  height: 38px;
  border-radius: 8px;
}

/* 2. Connection Status Card */
.connection-status-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 18px 22px;
  box-sizing: border-box;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

:global(.dark-theme) .connection-status-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.source-mode-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .source-mode-bar {
  border-bottom-color: #1E4E61 !important;
}

.source-mode-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.hardware-connection-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.card-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.header-title-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.title-texts {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.standby-notice-banner-skeleton {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--color-bg-light, #F8FAFC);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 9px;
  padding: 10px 14px;
  box-sizing: border-box;
}

:global(.dark-theme) .standby-notice-banner-skeleton {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.connection-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
  gap: 10px;
}

.info-box-skeleton {
  background: var(--color-bg-light, #F8FAFC);
  border: 1px solid rgba(203, 213, 225, 0.4);
  padding: 10px 14px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

:global(.dark-theme) .info-box-skeleton {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.action-footer-row-skeleton {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  padding-top: 4px;
}

/* Circles */
.skeleton-circle-sm {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  flex-shrink: 0;
}

.skeleton-circle-md {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  flex-shrink: 0;
}

/* 4 Stats Grid */
.skeleton-stats-grid-4 {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.skeleton-stat-card {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.skeleton-stat-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.skeleton-stat-body {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.skeleton-number-bar {
  width: 70px;
  height: 32px;
  border-radius: 6px;
}

.skeleton-stat-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Master Chart Card */
.skeleton-master-chart {
  padding: 20px;
}

.skeleton-chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 12px;
}

.skeleton-chart-title-box {
  display: flex;
  align-items: center;
  gap: 12px;
}

.skeleton-header-tools {
  display: flex;
  align-items: center;
  gap: 10px;
}

.skeleton-series-toolbar {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.skeleton-chart-canvas {
  position: relative;
  width: 100%;
  height: 300px;
  background: var(--color-bg-light, #F8FAF9);
  border: 1px dashed var(--color-border);
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 16px;
}

:global(.dark-theme) .skeleton-chart-canvas {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.skeleton-grid-line {
  position: absolute;
  left: 60px;
  right: 60px;
  height: 1px;
  background: rgba(0, 0, 0, 0.06);
}

:global(.dark-theme) .skeleton-grid-line {
  background: rgba(255, 255, 255, 0.05);
}

.skeleton-wave-container {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.35;
}

.skeleton-wave-mock {
  width: 80%;
  height: 90px;
  border-radius: 45px;
  transform: rotate(-2deg);
}

.skeleton-analytics-strip {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
  padding: 12px 16px;
  background: var(--color-bg-light, #F8FAF9);
  border: 1px solid var(--color-border);
  border-radius: 8px;
}

:global(.dark-theme) .skeleton-analytics-strip {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.skeleton-strip-item {
  display: flex;
  flex-direction: column;
}

/* 3-Column Deep Dive Grid */
.skeleton-deepdive-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

.skeleton-deepdive-card {
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.skeleton-card-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.skeleton-mini-canvas {
  width: 100%;
  height: 140px;
  border-radius: 8px;
}

:global(.dark-theme) .skeleton-mini-canvas {
  background: #113847 !important;
}

.skeleton-metric-rows {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.skeleton-metric-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.skeleton-actuator-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.skeleton-actuator-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  background: var(--color-bg-light, #F8FAF9);
  border-radius: 8px;
}

:global(.dark-theme) .skeleton-actuator-item {
  background: #071E27 !important;
}

/* Table Skeleton */
.skeleton-table-card {
  padding: 20px;
}

.skeleton-table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.skeleton-table-mock {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.skeleton-table-head-row {
  display: flex;
  gap: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--color-border);
}

:global(.dark-theme) .skeleton-table-head-row {
  border-bottom-color: #1E4E61 !important;
}

.skeleton-table-body-row {
  display: flex;
  gap: 16px;
  padding: 8px 0;
  border-bottom: 1px dashed var(--color-border);
}

:global(.dark-theme) .skeleton-table-body-row {
  border-bottom-color: rgba(30, 78, 97, 0.4) !important;
}

/* Responsiveness */
@media (max-width: 1024px) {
  .skeleton-stats-grid-4 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .skeleton-deepdive-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .skeleton-stats-grid-4 {
    grid-template-columns: 1fr;
  }
  .skeleton-analytics-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
