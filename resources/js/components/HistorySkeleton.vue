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

      <!-- 4. Batch Records Table Card Skeleton (1:1 with history-list-card) -->
      <div class="skeleton-card skeleton-table-card">
        <div class="sk-table-header-row">
          <div class="skeleton-bar sk-col-th" style="width: 130px;"></div>
          <div class="skeleton-bar sk-col-th" style="width: 160px;"></div>
          <div class="skeleton-bar sk-col-th" style="width: 140px;"></div>
          <div class="skeleton-bar sk-col-th" style="width: 100px;"></div>
          <div class="skeleton-bar sk-col-th" style="width: 110px;"></div>
          <div class="skeleton-bar sk-col-th" style="width: 90px;"></div>
          <div class="skeleton-bar sk-col-th" style="width: 110px;"></div>
        </div>

        <div class="sk-table-rows">
          <div v-for="r in 5" :key="'sk-row-' + r" class="sk-table-row">
            <div class="skeleton-bar sk-col-td" style="width: 120px; height: 18px;"></div>
            <div class="skeleton-bar sk-col-td" style="width: 150px; height: 16px;"></div>
            <div class="skeleton-bar sk-col-td" style="width: 130px; height: 14px;"></div>
            <div class="skeleton-bar sk-col-td" style="width: 90px; height: 14px;"></div>
            <div class="skeleton-bar sk-col-td" style="width: 100px; height: 16px;"></div>
            <div class="skeleton-bar sk-col-td" style="width: 80px; height: 22px; border-radius: 20px;"></div>
            <div class="skeleton-bar sk-col-td" style="width: 95px; height: 32px; border-radius: 8px;"></div>
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
  min-height: calc(100vh - 72px);
  background: var(--color-bg-page, #F0F4F2);
  box-sizing: border-box;
}

:global(.dark-theme) .history-skeleton-page {
  background: #04131A !important;
}

.history-skeleton-container {
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
  background: #FFFFFF;
  border-radius: 16px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  box-sizing: border-box;
}

/* Animated Shimmer Bar */
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
  height: 28px;
}

.sk-subtitle {
  width: 440px;
  max-width: 95%;
  height: 14px;
}

.sk-header-actions {
  display: flex;
  gap: 10px;
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
}

.sk-kpi-label {
  width: 160px;
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
  min-width: 240px;
  height: 40px;
  border-radius: 10px;
}

.sk-filter-pills {
  display: flex;
  gap: 8px;
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

/* 4. Table Card */
.skeleton-table-card {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  overflow-x: auto;
}

.sk-table-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 12px;
  border-bottom: 1px solid #E2E8F0;
  gap: 16px;
  min-width: 700px;
}

.sk-col-th {
  height: 13px;
}

.sk-table-rows {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 700px;
}

.sk-table-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px dashed #E2E8F0;
  gap: 16px;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
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

:global(.dark-theme) .sk-table-header-row {
  border-bottom-color: #1E4E61;
}

:global(.dark-theme) .sk-table-row {
  border-bottom-color: rgba(30, 78, 97, 0.4);
}
</style>
