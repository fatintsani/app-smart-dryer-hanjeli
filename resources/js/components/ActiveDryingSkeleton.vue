<template>
  <div class="active-drying-skeleton-page" aria-hidden="true">
    <div class="active-drying-skeleton-container">
      
      <!-- 1. Header Card Skeleton (1:1 with active-header-card) -->
      <div class="skeleton-card skeleton-active-header">
        <div class="sk-header-left">
          <div class="sk-batch-row">
            <div class="skeleton-bar sk-batch-title"></div>
            <div class="skeleton-bar sk-batch-code"></div>
            <div class="skeleton-bar sk-status-pill"></div>
          </div>
          <div class="skeleton-bar sk-operator-info"></div>
        </div>

        <div class="sk-header-actions">
          <div class="skeleton-bar sk-btn-finish"></div>
          <div class="sk-secondary-btns">
            <div class="skeleton-bar sk-btn-secondary"></div>
            <div class="skeleton-bar sk-btn-secondary"></div>
          </div>
        </div>
      </div>

      <!-- 2. Big Moisture & Time Progress Card Skeleton (1:1 with drying-progress-card) -->
      <div class="skeleton-card skeleton-progress-card">
        <div class="sk-progress-top-row">
          <div class="sk-prog-time-box">
            <div class="skeleton-bar sk-clock-icon"></div>
            <div class="sk-clock-texts">
              <div class="skeleton-bar sk-stat-small-lbl"></div>
              <div class="skeleton-bar sk-clock-display"></div>
            </div>
          </div>

          <div class="sk-prog-metric-box">
            <div class="skeleton-bar sk-stat-small-lbl"></div>
            <div class="skeleton-bar sk-prog-val"></div>
          </div>

          <div class="sk-prog-metric-box">
            <div class="skeleton-bar sk-stat-small-lbl"></div>
            <div class="skeleton-bar sk-prog-val"></div>
          </div>
        </div>

        <!-- Track bar wrapper -->
        <div class="sk-progress-track-wrapper">
          <div class="sk-track-info-row">
            <div class="skeleton-bar sk-track-lbl"></div>
            <div class="skeleton-bar sk-track-chip"></div>
            <div class="skeleton-bar sk-track-lbl"></div>
          </div>
          <div class="skeleton-bar sk-progress-bar-fill"></div>
        </div>
      </div>

      <!-- 3. 4 Current Room Condition Cards Skeleton (1:1 with active-kpi-grid) -->
      <div class="skeleton-kpi-grid">
        <div v-for="i in 4" :key="'active-kpi-skel-' + i" class="skeleton-card skeleton-kpi-card">
          <div class="sk-kpi-top">
            <div class="skeleton-bar sk-kpi-title"></div>
            <div class="skeleton-bar sk-kpi-icon"></div>
          </div>
          <div class="sk-kpi-val-row">
            <div class="skeleton-bar sk-kpi-num"></div>
            <div class="skeleton-bar sk-kpi-unit"></div>
          </div>
          <div class="sk-kpi-footer">
            <div class="skeleton-bar sk-kpi-badge"></div>
          </div>
        </div>
      </div>

      <!-- 4. Simple Trend Chart Card Skeleton (1:1 with active-chart-card) -->
      <div class="skeleton-card skeleton-active-chart">
        <div class="sk-chart-head">
          <div class="sk-chart-titles">
            <div class="skeleton-bar sk-chart-heading"></div>
            <div class="skeleton-bar sk-chart-desc"></div>
          </div>
          <div class="sk-legend-chips">
            <div class="skeleton-bar sk-legend-chip"></div>
            <div class="skeleton-bar sk-legend-chip"></div>
          </div>
        </div>

        <div class="sk-chart-canvas">
          <div class="sk-chart-grid-line" style="top: 20%;"></div>
          <div class="sk-chart-grid-line" style="top: 50%;"></div>
          <div class="sk-chart-grid-line" style="top: 75%;"></div>
          <div class="sk-mock-curves">
            <div class="sk-curve-line curve-purple"></div>
            <div class="sk-curve-line curve-red"></div>
          </div>
        </div>

        <div class="sk-chart-footer">
          <div class="sk-chart-time-labels">
            <div v-for="t in 6" :key="'sk-time-' + t" class="skeleton-bar sk-time-tag"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
// ActiveDryingSkeleton faithful 1:1 replica of ActiveDryingView
</script>

<style scoped>
/* ==========================================================================
   ACTIVE DRYING SKELETON BASE LAYOUT
   ========================================================================== */
.active-drying-skeleton-page {
  width: 100%;
  max-width: 100%;
  min-height: calc(100vh - 72px);
  background: var(--color-bg-page, #F0F4F2);
  box-sizing: border-box;
  overflow-x: hidden;
}

:global(.dark-theme) .active-drying-skeleton-page {
  background: #04131A !important;
}

.active-drying-skeleton-container {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-sizing: border-box;
}

/* Base Card Skeleton */
.skeleton-card {
  width: 100%;
  max-width: 100%;
  background: #FFFFFF;
  border-radius: 16px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  box-sizing: border-box;
  overflow: hidden;
}

/* Animated Shimmer Bar */
.skeleton-bar {
  background: #E2E8F0;
  border-radius: 6px;
  position: relative;
  overflow: hidden;
  box-sizing: border-box;
  max-width: 100%;
  min-width: 0;
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

/* ==========================================================================
   1. HEADER CARD SKELETON
   ========================================================================== */
.skeleton-active-header {
  padding: 22px 26px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.sk-header-left {
  flex: 1;
  min-width: 280px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-batch-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-batch-title {
  width: 200px;
  height: 28px;
}

.sk-batch-code {
  width: 100px;
  height: 24px;
  border-radius: 6px;
}

.sk-status-pill {
  width: 170px;
  height: 24px;
  border-radius: 20px;
}

.sk-operator-info {
  width: 380px;
  max-width: 100%;
  height: 14px;
}

.sk-header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-btn-finish {
  width: 200px;
  height: 48px;
  border-radius: 12px;
}

.sk-secondary-btns {
  display: flex;
  gap: 8px;
}

.sk-btn-secondary {
  width: 140px;
  height: 42px;
  border-radius: 10px;
}

/* ==========================================================================
   2. PROGRESS CARD SKELETON
   ========================================================================== */
.skeleton-progress-card {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.sk-progress-top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.sk-prog-time-box {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sk-clock-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  flex-shrink: 0;
}

.sk-clock-texts {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-stat-small-lbl {
  width: 100px;
  height: 11px;
}

.sk-clock-display {
  width: 140px;
  height: 26px;
}

.sk-prog-metric-box {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-prog-val {
  width: 130px;
  height: 22px;
}

.sk-progress-track-wrapper {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sk-track-info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.sk-track-lbl {
  width: 120px;
  height: 13px;
}

.sk-track-chip {
  width: 160px;
  height: 20px;
  border-radius: 12px;
}

.sk-progress-bar-fill {
  width: 100%;
  height: 14px;
  border-radius: 10px;
}

/* ==========================================================================
   3. 4 KPI INDICATOR CARDS
   ========================================================================== */
.skeleton-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.skeleton-kpi-card {
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sk-kpi-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sk-kpi-title {
  width: 110px;
  height: 13px;
}

.sk-kpi-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
}

.sk-kpi-val-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.sk-kpi-num {
  width: 65px;
  height: 30px;
}

.sk-kpi-unit {
  width: 22px;
  height: 16px;
}

.sk-kpi-footer {
  padding-top: 4px;
}

.sk-kpi-badge {
  width: 130px;
  height: 20px;
  border-radius: 6px;
}

/* ==========================================================================
   4. TREND CHART SKELETON
   ========================================================================== */
.skeleton-active-chart {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.sk-chart-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
}

.sk-chart-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-chart-heading {
  width: 280px;
  height: 20px;
}

.sk-chart-desc {
  width: 440px;
  max-width: 100%;
  height: 12px;
}

.sk-legend-chips {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.sk-legend-chip {
  width: 130px;
  height: 22px;
  border-radius: 14px;
}

.sk-chart-canvas {
  height: 200px;
  border-radius: 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  position: relative;
  overflow: hidden;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
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
  left: 6%;
  right: 6%;
  height: 3px;
  border-radius: 2px;
  opacity: 0.3;
}

.sk-curve-line.curve-purple {
  top: 45%;
  background: #7E22CE;
  transform: rotate(-2deg);
}

.sk-curve-line.curve-red {
  top: 55%;
  background: #DC2626;
  transform: rotate(1deg);
}

.sk-chart-footer {
  padding-top: 4px;
  width: 100%;
  box-sizing: border-box;
}

.sk-chart-time-labels {
  display: flex;
  justify-content: space-between;
  gap: 6px;
  width: 100%;
  box-sizing: border-box;
  overflow: hidden;
}

.sk-time-tag {
  flex: 1;
  max-width: 45px;
  height: 11px;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
@media (max-width: 1024px) {
  .skeleton-kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 900px) {
  .active-drying-skeleton-container {
    padding: 0;
    gap: 12px;
  }

  .skeleton-card {
    padding: 16px;
    border-radius: 14px;
  }

  .skeleton-active-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .sk-header-actions {
    width: 100%;
    flex-direction: column;
  }

  .sk-btn-finish {
    width: 100%;
  }

  .sk-secondary-btns {
    width: 100%;
  }

  .sk-btn-secondary {
    flex: 1;
  }

  .sk-chart-head {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .sk-chart-canvas {
    height: 170px;
  }
}

@media (max-width: 600px) {
  .skeleton-kpi-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .sk-progress-top-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .sk-prog-time-box,
  .sk-prog-metric-box {
    width: 100%;
  }

  .sk-track-info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
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

:global(.dark-theme) .sk-chart-canvas {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .sk-chart-grid-line {
  background: #1E4E61;
  border-color: #1E4E61;
}
</style>
