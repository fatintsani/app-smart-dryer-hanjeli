<template>
  <div class="dashboard-skeleton-page">
    <!-- Responsive Full Dashboard Skeleton Layout -->
    <div class="desktop-content">
      <!-- 1. Header Row Skeleton -->
      <div class="header-action-row">
        <div class="title-group">
          <div class="skeleton-bar skeleton-title-bar"></div>
          <div class="skeleton-bar skeleton-sub-bar"></div>
        </div>
        <div class="skeleton-bar skeleton-btn-bar"></div>
      </div>

      <!-- 2. Dual Connectivity Status Card Skeleton -->
      <div class="connection-status-card">
        <!-- Row 1: Source mode switcher -->
        <div class="source-mode-bar">
          <div class="source-mode-info">
            <div class="skeleton-bar" style="width: 160px; height: 13px;"></div>
            <div class="skeleton-bar" style="width: 280px; height: 11px; margin-top: 4px;"></div>
          </div>
          <div class="source-control-container">
            <div class="skeleton-bar" style="width: 260px; height: 32px; border-radius: 9px;"></div>
          </div>
        </div>

        <!-- Row 2: Hardware Connection Section -->
        <div class="hardware-connection-section">
          <div class="card-header-row">
            <div class="header-title-group">
              <div class="skeleton-bar" style="width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;"></div>
              <div class="title-texts">
                <div class="skeleton-bar" style="width: 160px; height: 14px;"></div>
                <div class="skeleton-bar" style="width: 180px; height: 11px; margin-top: 4px;"></div>
              </div>
            </div>
            <div class="skeleton-bar" style="width: 240px; height: 30px; border-radius: 9px;"></div>
          </div>

          <!-- Standby Notice Banner (Pure Monochrome Skeleton) -->
          <div class="standby-notice-banner-skeleton">
            <div class="skeleton-bar" style="width: 18px; height: 18px; border-radius: 50%; flex-shrink: 0;"></div>
            <div style="flex: 1; display: flex; flex-direction: column; gap: 4px; min-width: 0;">
              <div class="skeleton-bar" style="width: 200px; height: 13px;"></div>
              <div class="skeleton-bar" style="width: 85%; height: 11px;"></div>
            </div>
          </div>

          <!-- 4 Connection Metric Boxes Grid -->
          <div class="connection-grid">
            <!-- Box 1 -->
            <div class="info-box-skeleton">
              <div class="skeleton-bar" style="width: 80px; height: 10px;"></div>
              <div class="skeleton-bar" style="width: 110px; height: 14px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 90px; height: 10px; margin-top: 4px;"></div>
            </div>
            <!-- Box 2 -->
            <div class="info-box-skeleton">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="skeleton-bar" style="width: 110px; height: 10px;"></div>
                <div class="skeleton-bar" style="width: 28px; height: 10px; border-radius: 4px;"></div>
              </div>
              <div class="skeleton-bar" style="width: 120px; height: 14px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 80px; height: 10px; margin-top: 4px;"></div>
            </div>
            <!-- Box 3 -->
            <div class="info-box-skeleton">
              <div class="skeleton-bar" style="width: 120px; height: 10px;"></div>
              <div class="skeleton-bar" style="width: 130px; height: 14px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 100px; height: 10px; margin-top: 4px;"></div>
            </div>
            <!-- Box 4 -->
            <div class="info-box-skeleton">
              <div class="skeleton-bar" style="width: 75px; height: 10px;"></div>
              <div class="skeleton-bar" style="width: 100px; height: 14px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 60px; height: 10px; margin-top: 4px;"></div>
            </div>
          </div>

          <!-- Bottom Action Row -->
          <div class="action-footer-row-skeleton">
            <div class="skeleton-bar" style="width: 260px; height: 12px;"></div>
            <div class="skeleton-bar" style="width: 150px; height: 32px; border-radius: 8px;"></div>
          </div>
        </div>
      </div>

      <!-- 3. 5 Stat Cards Grid Skeleton (Zero Overflow, Responsive minmax) -->
      <div class="stats-grid-5">
        <!-- 1. Suhu -->
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="skeleton-bar" style="width: 85px; height: 11px;"></div>
            <div class="skeleton-bar skeleton-icon-box"></div>
          </div>
          <div class="stat-value-row">
            <div class="skeleton-bar" style="width: 48px; height: 28px; border-radius: 6px;"></div>
            <div class="skeleton-bar" style="width: 16px; height: 14px;"></div>
            <div class="skeleton-bar" style="width: 44px; height: 16px; border-radius: 12px; margin-left: auto;"></div>
          </div>
          <div class="stat-trend">
            <div class="skeleton-bar" style="width: 105px; height: 12px;"></div>
          </div>
        </div>

        <!-- 2. Kelembapan -->
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="skeleton-bar" style="width: 90px; height: 11px;"></div>
            <div class="skeleton-bar skeleton-icon-box"></div>
          </div>
          <div class="stat-value-row">
            <div class="skeleton-bar" style="width: 42px; height: 28px; border-radius: 6px;"></div>
            <div class="skeleton-bar" style="width: 24px; height: 14px;"></div>
            <div class="skeleton-bar" style="width: 48px; height: 16px; border-radius: 12px; margin-left: auto;"></div>
          </div>
          <div class="stat-trend">
            <div class="skeleton-bar" style="width: 105px; height: 12px;"></div>
          </div>
        </div>

        <!-- 3. Radiasi Surya -->
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="skeleton-bar" style="width: 80px; height: 11px;"></div>
            <div class="skeleton-bar skeleton-icon-box"></div>
          </div>
          <div class="stat-value-row">
            <div class="skeleton-bar" style="width: 48px; height: 28px; border-radius: 6px;"></div>
            <div class="skeleton-bar" style="width: 24px; height: 14px;"></div>
            <div class="skeleton-bar" style="width: 48px; height: 16px; border-radius: 12px; margin-left: auto;"></div>
          </div>
          <div class="stat-trend">
            <div class="skeleton-bar" style="width: 44px; height: 18px; border-radius: 999px;"></div>
          </div>
        </div>

        <!-- 4. Kadar Air Gabah -->
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="skeleton-bar" style="width: 85px; height: 11px;"></div>
            <div class="skeleton-bar skeleton-icon-box"></div>
          </div>
          <div class="stat-value-row">
            <div class="skeleton-bar" style="width: 44px; height: 28px; border-radius: 6px;"></div>
            <div class="skeleton-bar" style="width: 12px; height: 14px;"></div>
          </div>
          <div class="progress-bar-wrapper">
            <div class="skeleton-bar" style="width: 100%; height: 7px; border-radius: 10px;"></div>
            <div class="skeleton-bar" style="width: 120px; height: 10px;"></div>
          </div>
        </div>

        <!-- 5. Kipas & Aktuator -->
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="skeleton-bar" style="width: 90px; height: 11px;"></div>
            <div class="skeleton-bar skeleton-icon-box"></div>
          </div>
          <div class="stat-value-row">
            <div class="skeleton-bar" style="width: 44px; height: 28px; border-radius: 6px;"></div>
            <div class="skeleton-bar" style="width: 20px; height: 14px;"></div>
            <div class="skeleton-bar" style="width: 48px; height: 16px; border-radius: 12px; margin-left: auto;"></div>
          </div>
          <div class="stat-trend">
            <div class="skeleton-bar" style="width: 95px; height: 12px;"></div>
          </div>
        </div>
      </div>

      <!-- 4. Master Dual-Axis Waveform Chart Card Skeleton -->
      <div class="master-chart-card">
        <div class="master-chart-header">
          <div class="header-title-box">
            <div class="skeleton-bar" style="width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;"></div>
            <div style="min-width: 0;">
              <div class="skeleton-bar" style="width: 260px; height: 17px;"></div>
              <div class="skeleton-bar" style="width: 340px; height: 12px; margin-top: 5px;"></div>
            </div>
          </div>
          <div class="skeleton-bar" style="width: 120px; height: 24px; border-radius: 12px; flex-shrink: 0;"></div>
        </div>

        <!-- Series Toggles Bar Skeleton -->
        <div class="skeleton-series-toolbar">
          <div v-for="t in 5" :key="'toggle-' + t" class="skeleton-bar" style="width: 115px; height: 30px; border-radius: 8px;"></div>
        </div>

        <!-- Chart Canvas Skeleton with Grid Shimmer -->
        <div class="skeleton-chart-canvas">
          <div class="skeleton-grid-line" style="top: 25px;"></div>
          <div class="skeleton-grid-line" style="top: 75px;"></div>
          <div class="skeleton-grid-line" style="top: 125px;"></div>
          <div class="skeleton-grid-line" style="top: 175px;"></div>
          <div class="skeleton-grid-line" style="top: 225px;"></div>

          <!-- Synthetic Waveform Silhouette -->
          <div class="skeleton-wave-container">
            <div class="skeleton-wave-mock"></div>
          </div>
        </div>

        <!-- 4 Quick Analytics Strip Skeleton -->
        <div class="skeleton-analytics-strip">
          <div v-for="a in 4" :key="'analytic-' + a" class="skeleton-strip-item">
            <div class="skeleton-bar" style="width: 95px; height: 11px;"></div>
            <div class="skeleton-bar" style="width: 55px; height: 16px; margin-top: 5px;"></div>
          </div>
        </div>
      </div>

      <!-- 5. Bottom System Status & Activity Skeleton (2 Columns) -->
      <div class="skeleton-bottom-grid">
        <!-- Status Card -->
        <div class="master-chart-card">
          <div class="header-title-box">
            <div class="skeleton-bar" style="width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;"></div>
            <div style="min-width: 0;">
              <div class="skeleton-bar" style="width: 160px; height: 16px;"></div>
              <div class="skeleton-pills-row">
                <div class="skeleton-bar" style="width: 60px; height: 20px; border-radius: 999px;"></div>
                <div class="skeleton-bar" style="width: 80px; height: 20px; border-radius: 999px;"></div>
                <div class="skeleton-bar" style="width: 100px; height: 20px; border-radius: 999px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Alerts Card -->
        <div class="master-chart-card">
          <div class="skeleton-bar" style="width: 140px; height: 16px; margin-bottom: 14px;"></div>
          <div v-for="al in 3" :key="'alert-' + al" class="skeleton-timeline-item">
            <div class="skeleton-bar" style="width: 14px; height: 14px; border-radius: 50%; margin-top: 3px; flex-shrink: 0;"></div>
            <div style="flex: 1; min-width: 0;">
              <div class="skeleton-bar" style="width: 160px; height: 13px;"></div>
              <div class="skeleton-bar" style="width: 220px; height: 11px; margin-top: 4px;"></div>
              <div class="skeleton-bar" style="width: 50px; height: 9px; margin-top: 4px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})
</script>

<style scoped>
.dashboard-skeleton-page {
  width: 100%;
  box-sizing: border-box;
  animation: fadeInSkeleton 0.3s ease-in-out;
  overflow-x: hidden;
}

@keyframes fadeInSkeleton {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

.desktop-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 28px;
  max-width: 1400px;
  margin: 0 auto;
  box-sizing: border-box;
  width: 100%;
  overflow-x: hidden;
}

/* Pure Neutral Monochrome Shimmer Animation */
.skeleton-bar {
  position: relative;
  overflow: hidden;
  background: #E2E8F0;
  border-radius: 6px;
  max-width: 100%;
}

:global(.dark-theme) .skeleton-bar {
  background: #113847 !important;
}

.skeleton-bar::after {
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
    rgba(255, 255, 255, 0.55) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: skeletonShimmer 1.6s infinite ease-in-out;
}

:global(.dark-theme) .skeleton-bar::after {
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

/* Header Row */
.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.title-group {
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
  width: 420px;
  height: 14px;
}

.skeleton-btn-bar {
  width: 140px;
  height: 40px;
  border-radius: 10px;
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
  min-width: 0;
  overflow: hidden;
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
  min-width: 0;
}

:global(.dark-theme) .standby-notice-banner-skeleton {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.connection-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 10px;
  width: 100%;
}

.info-box-skeleton {
  background: var(--color-bg-light, #F8FAFC);
  border: 1px solid rgba(203, 213, 225, 0.4);
  padding: 10px 14px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
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

/* 3. Stats Grid (5 Cards - Safe minmax) */
.stats-grid-5 {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 14px;
  width: 100%;
  box-sizing: border-box;
}

.stat-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 10px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  box-sizing: border-box;
  min-width: 0;
  overflow: hidden;
}

:global(.dark-theme) .stat-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.stat-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.skeleton-icon-box {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  flex-shrink: 0;
}

.stat-value-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin: 2px 0;
  flex-wrap: nowrap;
  min-width: 0;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 6px;
  min-width: 0;
}

.progress-bar-wrapper {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-top: 4px;
}

/* 4. Master Waveform Chart */
.master-chart-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 22px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  box-sizing: border-box;
  min-width: 0;
  overflow: hidden;
}

:global(.dark-theme) .master-chart-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.master-chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.header-title-box {
  display: flex;
  align-items: center;
  gap: 12px;
}

.skeleton-series-toolbar {
  display: flex;
  gap: 8px;
  margin-bottom: 6px;
  flex-wrap: wrap;
}

.skeleton-chart-canvas {
  position: relative;
  width: 100%;
  height: 250px;
  background: var(--color-bg-light, #F8FAF9);
  border: 1px dashed rgba(203, 213, 225, 0.6);
  border-radius: 8px;
  overflow: hidden;
}

:global(.dark-theme) .skeleton-chart-canvas {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.skeleton-grid-line {
  position: absolute;
  left: 30px;
  right: 30px;
  height: 1px;
  background: rgba(0, 0, 0, 0.05);
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
  width: 85%;
  height: 65px;
  border-radius: 40px;
  transform: rotate(-2deg);
}

.skeleton-analytics-strip {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
  padding: 10px 14px;
  background: var(--color-bg-light, #F8FAF9);
  border: 1px solid rgba(203, 213, 225, 0.5);
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

/* 5. Bottom Grid Skeleton */
.skeleton-bottom-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
  width: 100%;
}

.skeleton-pills-row {
  display: flex;
  gap: 6px;
  margin-top: 8px;
  flex-wrap: wrap;
}

.skeleton-timeline-item {
  display: flex;
  gap: 10px;
  margin-bottom: 12px;
}

/* Mobile Specifics */
.mobile-skeleton-content {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  box-sizing: border-box;
  width: 100%;
  overflow-x: hidden;
}

.skeleton-mobile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.skeleton-mobile-stack {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.skeleton-mobile-stat {
  padding: 14px;
  display: flex;
  flex-direction: column;
}

.skeleton-mobile-btn {
  width: 100%;
  height: 46px;
  border-radius: 12px;
}

/* Responsive Grid Breakpoints matching DashboardView */
@media (max-width: 1280px) {
  .stats-grid-5 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 900px) {
  .stats-grid-5 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .skeleton-bottom-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .desktop-content {
    padding: 0;
    gap: 16px;
  }
  .header-action-row {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  .skeleton-title-bar {
    width: 200px;
  }
  .skeleton-sub-bar {
    width: 280px;
  }
  .skeleton-btn-bar {
    width: 100%;
    height: 44px;
  }
  .skeleton-analytics-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 580px) {
  .stats-grid-5 {
    grid-template-columns: 1fr;
    gap: 12px;
  }
}
</style>
