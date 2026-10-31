<template>
  <div class="history-skeleton-page" aria-hidden="true">
    <div class="history-skeleton-container">
      
      <!-- 1. Header Card Skeleton (1:1 with history-header-card) -->
      <div class="skeleton-card skeleton-history-header">
        <div class="sk-header-left">
          <div class="skeleton-bar sk-title"></div>
          <div class="skeleton-bar sk-subtitle"></div>
        </div>
        <div class="sk-header-actions">
          <div class="skeleton-bar sk-btn-action"></div>
          <div class="skeleton-bar sk-btn-action"></div>
        </div>
      </div>

      <!-- 2. 3 Historical KPI Metric Cards (1:1 with kpi-summary-grid) -->
      <div class="skeleton-kpi-grid-3">
        <div v-for="i in 3" :key="'kpi-skel-' + i" class="skeleton-card skeleton-kpi-card">
          <div class="sk-kpi-left">
            <div class="skeleton-bar sk-kpi-label"></div>
            <div class="sk-kpi-number-row">
              <div class="skeleton-bar sk-kpi-num"></div>
              <div class="skeleton-bar sk-kpi-unit"></div>
            </div>
            <div class="skeleton-bar sk-kpi-hint"></div>
          </div>
          <div class="skeleton-bar sk-kpi-icon-box"></div>
        </div>
      </div>

      <!-- 3. Filter Toolbar Card Skeleton (1:1 with history-filter-card) -->
      <div class="skeleton-card skeleton-filter-card">
        <div class="sk-filter-top-row">
          <div class="skeleton-bar sk-search-box"></div>
          <div class="sk-filter-pills">
            <div class="skeleton-bar sk-filter-pill"></div>
            <div class="skeleton-bar sk-filter-pill"></div>
            <div class="skeleton-bar sk-filter-pill"></div>
          </div>
        </div>
        <div class="sk-filter-bottom-row">
          <div class="skeleton-bar sk-date-input"></div>
          <div class="skeleton-bar sk-sort-select"></div>
        </div>
      </div>

      <!-- 4. Batch Cards Grid Skeleton (1:1 with batch-cards-grid) -->
      <div class="sk-batch-cards-grid">
        <div v-for="c in 6" :key="'sk-batch-card-' + c" class="skeleton-card sk-batch-item-card">
          <!-- Card Header: Code & Status -->
          <div class="sk-card-top-row">
            <div class="skeleton-bar sk-batch-code"></div>
            <div class="skeleton-bar sk-status-pill"></div>
          </div>

          <!-- Variety & Meta -->
          <div class="skeleton-bar sk-crop-title"></div>
          <div class="skeleton-bar sk-meta-date"></div>

          <!-- 3 Metrics Box -->
          <div class="sk-metrics-triple">
            <div v-for="m in 3" :key="'sk-m-' + m" class="sk-metric-col">
              <div class="skeleton-bar sk-metric-lbl"></div>
              <div class="skeleton-bar sk-metric-val"></div>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="sk-card-actions">
            <div class="skeleton-bar sk-btn-detail"></div>
            <div class="skeleton-bar sk-btn-qr"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
// HistorySkeleton faithful 1:1 replica of HistoryView
</script>

<style scoped>
/* ==========================================================================
   HISTORY SKELETON BASE LAYOUT
   ========================================================================== */
.history-skeleton-page {
  width: 100%;
  max-width: 100%;
  min-height: calc(100vh - 72px);
  background: var(--color-bg-page, #F0F4F2);
  box-sizing: border-box;
  overflow-x: hidden;
}

:global(.dark-theme) .history-skeleton-page {
  background: #04131A !important;
}

.history-skeleton-container {
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

/* 1. Header Card */
.skeleton-history-header {
  padding: 22px 26px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.sk-header-left {
  flex: 1;
  min-width: 260px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-title {
  width: 260px;
  max-width: 100%;
  height: 28px;
}

.sk-subtitle {
  width: 440px;
  max-width: 100%;
  height: 14px;
}

.sk-header-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.sk-btn-action {
  width: 140px;
  height: 42px;
  border-radius: 10px;
}

/* 2. 3 KPI Metric Cards */
.skeleton-kpi-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
}

.skeleton-kpi-card {
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 14px;
}

.sk-kpi-left {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
  flex: 1;
}

.sk-kpi-label {
  width: 160px;
  max-width: 100%;
  height: 12px;
}

.sk-kpi-number-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.sk-kpi-num {
  width: 55px;
  height: 28px;
}

.sk-kpi-unit {
  width: 28px;
  height: 16px;
}

.sk-kpi-hint {
  width: 130px;
  max-width: 100%;
  height: 11px;
}

.sk-kpi-icon-box {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  flex-shrink: 0;
}

/* 3. Filter Toolbar Card */
.skeleton-filter-card {
  padding: 18px 22px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sk-filter-top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.sk-search-box {
  flex: 1;
  min-width: 220px;
  height: 40px;
  border-radius: 10px;
}

.sk-filter-pills {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.sk-filter-pill {
  width: 90px;
  height: 36px;
  border-radius: 8px;
}

.sk-filter-bottom-row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-date-input {
  width: 200px;
  height: 38px;
  border-radius: 8px;
}

.sk-sort-select {
  width: 160px;
  height: 38px;
  border-radius: 8px;
}

/* 4. Batch Cards Grid Skeleton */
.sk-batch-cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  width: 100%;
  box-sizing: border-box;
}

.sk-batch-item-card {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sk-card-top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sk-batch-code {
  width: 75px;
  height: 22px;
  border-radius: 6px;
}

.sk-status-pill {
  width: 95px;
  height: 22px;
  border-radius: 20px;
}

.sk-crop-title {
  width: 180px;
  max-width: 100%;
  height: 20px;
}

.sk-meta-date {
  width: 220px;
  max-width: 100%;
  height: 12px;
}

.sk-metrics-triple {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  padding: 12px;
  border-radius: 10px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  box-sizing: border-box;
}

.sk-metric-col {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-metric-lbl {
  width: 60px;
  max-width: 100%;
  height: 10px;
}

.sk-metric-val {
  width: 70px;
  max-width: 100%;
  height: 16px;
}

.sk-card-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-top: 4px;
}

.sk-btn-detail {
  flex: 1;
  height: 40px;
  border-radius: 10px;
}

.sk-btn-qr {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  flex-shrink: 0;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
@media (max-width: 1100px) {
  .sk-batch-cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 900px) {
  .history-skeleton-container {
    padding: 0;
    gap: 12px;
  }

  .skeleton-card {
    padding: 16px;
    border-radius: 14px;
  }

  .skeleton-history-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .sk-header-actions {
    width: 100%;
  }

  .sk-btn-action {
    flex: 1;
  }

  .skeleton-kpi-grid-3 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 680px) {
  .sk-batch-cards-grid {
    grid-template-columns: 1fr;
  }

  .sk-filter-top-row {
    flex-direction: column;
    align-items: stretch;
  }

  .sk-search-box {
    width: 100%;
  }

  .sk-filter-pills {
    width: 100%;
  }

  .sk-filter-pill {
    flex: 1;
  }

  .sk-filter-bottom-row {
    flex-direction: column;
    width: 100%;
  }

  .sk-date-input,
  .sk-sort-select {
    width: 100%;
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

:global(.dark-theme) .sk-metrics-triple {
  background: #071E27;
  border-color: #1E4E61;
}
</style>
