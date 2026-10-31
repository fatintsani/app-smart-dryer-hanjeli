<template>
  <div class="dashboard-skeleton-page" aria-hidden="true">
    <div class="dashboard-skeleton-container">
      
      <!-- 1. Welcome Header Card Skeleton (1:1 with dash-welcome-card) -->
      <div class="skeleton-card skeleton-welcome-card">
        <div class="sk-welcome-left">
          <div class="skeleton-bar sk-status-pill"></div>
          <div class="skeleton-bar sk-welcome-title"></div>
          <div class="skeleton-bar sk-welcome-desc"></div>
        </div>
        <div class="sk-welcome-right">
          <div class="skeleton-bar sk-action-btn"></div>
        </div>
      </div>

      <!-- 2. 4 Hero Indicator Cards Grid Skeleton (1:1 with hero-indicators-grid) -->
      <div class="skeleton-indicators-grid">
        <div v-for="i in 4" :key="'indicator-skel-' + i" class="skeleton-card skeleton-indicator-card">
          <div class="sk-indicator-top">
            <div class="skeleton-bar sk-indicator-icon"></div>
            <div class="skeleton-bar sk-indicator-tag"></div>
          </div>
          <div class="sk-indicator-body">
            <div class="skeleton-bar sk-indicator-title"></div>
            <div class="sk-number-row">
              <div class="skeleton-bar sk-big-number"></div>
              <div class="skeleton-bar sk-unit"></div>
            </div>
            <div class="skeleton-bar sk-hint-line"></div>
          </div>
        </div>
      </div>

      <!-- 3. Chart Card Skeleton (1:1 with dash-chart-card) -->
      <div class="skeleton-card skeleton-chart-card">
        <!-- Header: Title, Subtitle, and Tabs -->
        <div class="sk-chart-header">
          <div class="sk-chart-header-left">
            <div class="sk-chart-title-row">
              <div class="skeleton-bar sk-chart-title"></div>
              <div class="skeleton-bar sk-chart-period-badge"></div>
            </div>
            <div class="skeleton-bar sk-chart-subtitle"></div>
          </div>
          <div class="sk-chart-header-right">
            <div class="skeleton-bar sk-chart-tabs"></div>
            <div class="skeleton-bar sk-chart-btn-csv"></div>
          </div>
        </div>

        <!-- Simple Summary Badges Strip Skeleton -->
        <div class="sk-metric-badges-strip">
          <div v-for="s in 4" :key="'sk-m-' + s" class="skeleton-bar sk-metric-chip"></div>
        </div>

        <!-- Legend and Hint Row -->
        <div class="sk-chart-legend-row">
          <div class="sk-legend-group">
            <div class="skeleton-bar sk-legend-pill"></div>
            <div class="skeleton-bar sk-legend-pill"></div>
            <div class="skeleton-bar sk-legend-pill"></div>
          </div>
          <div class="skeleton-bar sk-legend-hint"></div>
        </div>

        <!-- SVG Canvas Layout Placeholder -->
        <div class="sk-chart-canvas">
          <div class="sk-chart-grid-line" style="top: 25%;"></div>
          <div class="sk-chart-grid-line" style="top: 50%;"></div>
          <div class="sk-chart-grid-line" style="top: 75%;"></div>
          <div class="sk-mock-curves">
            <div class="sk-curve-line curve-red"></div>
            <div class="sk-curve-line curve-blue"></div>
          </div>
        </div>

        <!-- Chart Time Labels -->
        <div class="sk-chart-footer">
          <div class="sk-chart-time-labels">
            <div v-for="t in 7" :key="'sk-time-' + t" class="skeleton-bar sk-time-tag"></div>
          </div>
        </div>
      </div>

      <!-- 4. Recent Alerts Card Skeleton -->
      <div class="skeleton-card skeleton-alerts-card">
        <div class="skeleton-bar sk-alerts-title"></div>
        <div class="sk-alerts-list">
          <div v-for="a in 2" :key="'sk-alert-' + a" class="sk-alert-item">
            <div class="skeleton-bar sk-alert-dot"></div>
            <div class="sk-alert-text">
              <div class="skeleton-bar sk-alert-heading"></div>
              <div class="skeleton-bar sk-alert-msg"></div>
            </div>
            <div class="skeleton-bar sk-alert-time"></div>
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
/* ==========================================================================
   DASHBOARD SKELETON BASE LAYOUT (1:1 with DashboardView.vue)
   ========================================================================== */
.dashboard-skeleton-page {
  width: 100%;
  min-height: calc(100vh - 72px);
  background: var(--color-bg-page, #F0F4F2);
  box-sizing: border-box;
}

:global(.dark-theme) .dashboard-skeleton-page {
  background: #04131A !important;
}

.dashboard-skeleton-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-sizing: border-box;
}

/* ==========================================================================
   ANIMATED SHIMMER ATOM
   ========================================================================== */
.skeleton-bar {
  background: #E2E8F0;
  border-radius: 6px;
  position: relative;
  overflow: hidden;
  box-sizing: border-box;
}

.skeleton-bar::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transform: translateX(-100%);
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.6) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: sk-shimmer 1.6s infinite ease-in-out;
}

@keyframes sk-shimmer {
  100% {
    transform: translateX(100%);
  }
}

/* Base Card Skeleton */
.skeleton-card {
  background: #FFFFFF;
  border-radius: 16px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  box-sizing: border-box;
}

/* ==========================================================================
   1. WELCOME HEADER CARD
   ========================================================================== */
.skeleton-welcome-card {
  padding: 22px 26px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.sk-welcome-left {
  flex: 1;
  min-width: 260px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-status-pill {
  width: 190px;
  height: 24px;
  border-radius: 20px;
}

.sk-welcome-title {
  width: 380px;
  max-width: 90%;
  height: 26px;
}

.sk-welcome-desc {
  width: 480px;
  max-width: 100%;
  height: 14px;
}

.sk-welcome-right {
  display: flex;
  align-items: center;
}

.sk-action-btn {
  width: 220px;
  height: 52px;
  border-radius: 12px;
}

/* ==========================================================================
   2. 4 HERO INDICATOR CARDS GRID
   ========================================================================== */
.skeleton-indicators-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.skeleton-indicator-card {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sk-indicator-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sk-indicator-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
}

.sk-indicator-tag {
  width: 95px;
  height: 22px;
  border-radius: 6px;
}

.sk-indicator-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-indicator-title {
  width: 100px;
  height: 13px;
}

.sk-number-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.sk-big-number {
  width: 70px;
  height: 34px;
  border-radius: 6px;
}

.sk-unit {
  width: 24px;
  height: 18px;
}

.sk-hint-line {
  width: 140px;
  max-width: 95%;
  height: 11px;
}

/* ==========================================================================
   3. CHART CARD SKELETON
   ========================================================================== */
.skeleton-chart-card {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sk-chart-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
}

.sk-chart-header-left {
  flex: 1;
  min-width: 260px;
}

.sk-chart-title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 6px;
}

.sk-chart-title {
  width: 320px;
  max-width: 80%;
  height: 22px;
}

.sk-chart-period-badge {
  width: 90px;
  height: 20px;
  border-radius: 20px;
}

.sk-chart-subtitle {
  width: 460px;
  max-width: 95%;
  height: 13px;
}

.sk-chart-header-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.sk-chart-tabs {
  width: 220px;
  height: 32px;
  border-radius: 8px;
}

.sk-chart-btn-csv {
  width: 105px;
  height: 32px;
  border-radius: 8px;
}

/* Simple Summary Badges Strip */
.sk-metric-badges-strip {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  padding: 8px 12px;
  border-radius: 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
}

.sk-metric-chip {
  width: 170px;
  height: 28px;
  border-radius: 8px;
}

.sk-chart-legend-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-legend-group {
  display: flex;
  gap: 12px;
}

.sk-legend-pill {
  width: 110px;
  height: 14px;
}

.sk-legend-hint {
  width: 260px;
  height: 12px;
}

.sk-chart-canvas {
  height: 220px;
  border-radius: 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  position: relative;
  overflow: hidden;
}

.sk-chart-grid-line {
  position: absolute;
  left: 0;
  right: 0;
  height: 1px;
  background: #E2E8F0;
  border-top: 1px dashed #CBD5E1;
}

.sk-mock-curves {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sk-curve-line {
  position: absolute;
  left: 8%;
  right: 8%;
  height: 3px;
  border-radius: 2px;
  opacity: 0.25;
}

.sk-curve-line.curve-red {
  top: 60%;
  background: #DC2626;
  transform: rotate(-1deg);
}

.sk-curve-line.curve-blue {
  top: 40%;
  background: #0284C7;
  transform: rotate(1deg);
}

.sk-chart-footer {
  padding-top: 4px;
}

.sk-chart-time-labels {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.sk-time-tag {
  width: 45px;
  height: 11px;
}

/* ==========================================================================
   4. RECENT ALERTS SKELETON
   ========================================================================== */
.skeleton-alerts-card {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sk-alerts-title {
  width: 200px;
  height: 18px;
}

.sk-alerts-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sk-alert-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 10px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
}

.sk-alert-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

.sk-alert-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.sk-alert-heading {
  width: 140px;
  height: 13px;
}

.sk-alert-msg {
  width: 320px;
  max-width: 90%;
  height: 11px;
}

.sk-alert-time {
  width: 70px;
  height: 11px;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
@media (max-width: 1024px) {
  .skeleton-indicators-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 900px) {
  .dashboard-skeleton-container {
    padding: 0;
    gap: 12px;
  }

  .skeleton-card {
    padding: 16px;
    border-radius: 14px;
  }

  .skeleton-welcome-card {
    flex-direction: column;
    align-items: flex-start;
  }

  .sk-action-btn {
    width: 100%;
    height: 48px;
  }

  .sk-welcome-right {
    width: 100%;
  }

  .sk-metric-badges-strip {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    padding: 8px;
  }

  .sk-metric-chip {
    width: 100%;
    height: 30px;
  }

  .sk-chart-canvas {
    height: 180px;
  }
}

@media (max-width: 600px) {
  .skeleton-indicators-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .sk-metric-badges-strip {
    grid-template-columns: 1fr;
  }
}

/* ==========================================================================
   DARK THEME OVERRIDES
   ========================================================================== */
:global(.dark-theme) .skeleton-card {
  background: #0B242F;
  border-color: #1E4E61;
}

:global(.dark-theme) .skeleton-bar {
  background: #113847 !important;
}

:global(.dark-theme) .skeleton-bar::after {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.08) 50%,
    rgba(255, 255, 255, 0) 100%
  );
}

:global(.dark-theme) .sk-metric-badges-strip,
:global(.dark-theme) .sk-chart-canvas,
:global(.dark-theme) .sk-alert-item {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .sk-chart-grid-line {
  background: #1E4E61;
  border-color: #1E4E61;
}
</style>
