<template>
  <div class="history-detail-skeleton-page" aria-hidden="true">
    <div class="history-detail-skeleton-container">
      
      <!-- 1. Header Card Skeleton (1:1 with detail-header-card) -->
      <div class="skeleton-card skeleton-detail-header">
        <div class="sk-header-left">
          <div class="skeleton-bar sk-back-btn"></div>
          <div class="sk-title-badge-row">
            <div class="skeleton-bar sk-batch-code"></div>
            <div class="skeleton-bar sk-status-pill"></div>
            <div class="skeleton-bar sk-badge-grade"></div>
          </div>
          <div class="skeleton-bar sk-meta-sub"></div>
        </div>

        <div class="sk-header-actions">
          <div class="skeleton-bar sk-btn-main"></div>
          <div class="sk-action-cluster">
            <div class="skeleton-bar sk-btn-icon"></div>
            <div class="skeleton-bar sk-btn-icon"></div>
            <div class="skeleton-bar sk-btn-icon"></div>
          </div>
        </div>
      </div>

      <!-- 2. Sertifikasi Mutu Hero Banner Skeleton (1:1 with cert-hero-card) -->
      <div class="skeleton-card skeleton-cert-hero">
        <div class="sk-cert-left">
          <div class="sk-cert-tags">
            <div class="skeleton-bar sk-cert-tag"></div>
            <div class="skeleton-bar sk-cert-tag-grade"></div>
          </div>
          <div class="skeleton-bar sk-cert-heading"></div>
          <div class="skeleton-bar sk-cert-desc"></div>
        </div>

        <div class="sk-cert-score-box">
          <div class="skeleton-bar sk-score-lbl"></div>
          <div class="skeleton-bar sk-score-val"></div>
          <div class="skeleton-bar sk-score-sub"></div>
        </div>
      </div>

      <!-- 3. 4 Kartu Hasil Utama Skeleton (1:1 with hero-results-grid) -->
      <div class="skeleton-results-grid-4">
        <div v-for="i in 4" :key="'res-skel-' + i" class="skeleton-card skeleton-result-card">
          <div class="sk-result-top">
            <div class="skeleton-bar sk-res-title"></div>
            <div class="skeleton-bar sk-res-icon"></div>
          </div>
          <div class="sk-result-val-row">
            <div class="skeleton-bar sk-res-num"></div>
            <div class="skeleton-bar sk-res-unit"></div>
          </div>
          <div class="skeleton-bar sk-res-badge"></div>
        </div>
      </div>

      <!-- 4. Simple Trend Chart Card Skeleton (1:1 with chart-card-wrapper) -->
      <div class="skeleton-card skeleton-chart-card">
        <div class="sk-chart-header">
          <div class="sk-chart-titles">
            <div class="skeleton-bar sk-chart-title"></div>
            <div class="skeleton-bar sk-chart-subtitle"></div>
          </div>
          <div class="sk-chart-legends">
            <div class="skeleton-bar sk-legend-pill"></div>
            <div class="skeleton-bar sk-legend-pill"></div>
          </div>
        </div>

        <div class="sk-chart-canvas">
          <div class="sk-chart-grid-line" style="top: 25%;"></div>
          <div class="sk-chart-grid-line" style="top: 50%;"></div>
          <div class="sk-chart-grid-line" style="top: 75%;"></div>
          <div class="sk-mock-curves">
            <div class="sk-curve-line curve-purple"></div>
            <div class="sk-curve-line curve-red"></div>
          </div>
        </div>
      </div>

      <!-- 5. Two Columns: Details on Left, Notes on Right -->
      <div class="sk-two-cols-layout">
        <div class="skeleton-card sk-panel-card">
          <div class="skeleton-bar sk-panel-heading"></div>
          <div class="sk-panel-rows">
            <div v-for="p in 5" :key="'sk-prow-' + p" class="sk-panel-row">
              <div class="skeleton-bar sk-row-lbl"></div>
              <div class="skeleton-bar sk-row-val"></div>
            </div>
          </div>
        </div>

        <div class="skeleton-card sk-panel-card">
          <div class="skeleton-bar sk-panel-heading"></div>
          <div class="sk-notes-box">
            <div class="skeleton-bar sk-note-line"></div>
            <div class="skeleton-bar sk-note-line" style="width: 85%;"></div>
            <div class="skeleton-bar sk-note-line" style="width: 70%;"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
// HistoryDetailSkeleton faithful 1:1 replica of HistoryDetailView
</script>

<style scoped>
/* ==========================================================================
   HISTORY DETAIL SKELETON BASE LAYOUT
   ========================================================================== */
.history-detail-skeleton-page {
  width: 100%;
  min-height: calc(100vh - 72px);
  background: var(--color-bg-page, #F0F4F2);
  box-sizing: border-box;
}

:global(.dark-theme) .history-detail-skeleton-page {
  background: #04131A !important;
}

.history-detail-skeleton-container {
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
.skeleton-detail-header {
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

.sk-back-btn {
  width: 140px;
  height: 18px;
}

.sk-title-badge-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.sk-batch-code {
  width: 160px;
  height: 28px;
}

.sk-status-pill {
  width: 110px;
  height: 24px;
  border-radius: 20px;
}

.sk-badge-grade {
  width: 80px;
  height: 24px;
  border-radius: 6px;
}

.sk-meta-sub {
  width: 360px;
  max-width: 95%;
  height: 14px;
}

.sk-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.sk-btn-main {
  width: 150px;
  height: 44px;
  border-radius: 10px;
}

.sk-action-cluster {
  display: flex;
  gap: 8px;
}

.sk-btn-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
}

/* 2. Cert Hero Banner */
.skeleton-cert-hero {
  padding: 24px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  background: #F0FDF4;
  border-color: #BBF7D0;
}

.sk-cert-left {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sk-cert-tags {
  display: flex;
  gap: 10px;
}

.sk-cert-tag {
  width: 190px;
  height: 22px;
  border-radius: 20px;
}

.sk-cert-tag-grade {
  width: 130px;
  height: 22px;
  border-radius: 20px;
}

.sk-cert-heading {
  width: 320px;
  max-width: 90%;
  height: 24px;
}

.sk-cert-desc {
  width: 480px;
  max-width: 100%;
  height: 14px;
}

.sk-cert-score-box {
  width: 130px;
  padding: 16px;
  border-radius: 14px;
  background: #FFFFFF;
  border: 1px solid #BBF7D0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.sk-score-lbl {
  width: 70px;
  height: 10px;
}

.sk-score-val {
  width: 80px;
  height: 32px;
}

.sk-score-sub {
  width: 75px;
  height: 10px;
}

/* 3. 4 Results Cards */
.skeleton-results-grid-4 {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.skeleton-result-card {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sk-result-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sk-res-title {
  width: 120px;
  height: 12px;
}

.sk-res-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
}

.sk-result-val-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.sk-res-num {
  width: 65px;
  height: 30px;
}

.sk-res-unit {
  width: 24px;
  height: 16px;
}

.sk-res-badge {
  width: 130px;
  height: 20px;
  border-radius: 6px;
}

/* 4. Chart Card */
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

.sk-chart-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-chart-title {
  width: 300px;
  height: 20px;
}

.sk-chart-subtitle {
  width: 420px;
  max-width: 95%;
  height: 12px;
}

.sk-chart-legends {
  display: flex;
  gap: 10px;
}

.sk-legend-pill {
  width: 120px;
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

/* 5. Two Columns */
.sk-two-cols-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.sk-panel-card {
  padding: 22px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sk-panel-heading {
  width: 200px;
  height: 18px;
}

.sk-panel-rows {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sk-panel-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 0;
  border-bottom: 1px dashed #E2E8F0;
}

.sk-row-lbl {
  width: 130px;
  height: 12px;
}

.sk-row-val {
  width: 110px;
  height: 14px;
}

.sk-notes-box {
  padding: 16px;
  border-radius: 10px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-note-line {
  width: 100%;
  height: 12px;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
@media (max-width: 1024px) {
  .skeleton-results-grid-4 {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 900px) {
  .history-detail-skeleton-container {
    padding: 0;
    gap: 12px;
  }

  .skeleton-card {
    padding: 16px;
    border-radius: 14px;
  }

  .skeleton-detail-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .sk-header-actions {
    width: 100%;
  }

  .sk-btn-main {
    flex: 1;
  }

  .skeleton-cert-hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .sk-cert-score-box {
    width: 100%;
  }

  .sk-two-cols-layout {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .skeleton-results-grid-4 {
    grid-template-columns: 1fr;
    gap: 10px;
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

:global(.dark-theme) .skeleton-cert-hero {
  background: #06281E;
  border-color: #14532D;
}

:global(.dark-theme) .sk-cert-score-box,
:global(.dark-theme) .sk-chart-canvas,
:global(.dark-theme) .sk-notes-box {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .sk-panel-row {
  border-bottom-color: #1E4E61;
}
</style>
