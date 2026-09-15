<template>
  <div class="drying-form-skeleton-wrapper" aria-hidden="true">
    <!-- Header Placeholder -->
    <div class="dfs-header">
      <div class="dfs-title-block">
        <div class="dfs-shimmer dfs-title"></div>
        <div class="dfs-shimmer dfs-subtitle"></div>
      </div>
    </div>

    <!-- Quick Preset Selector Bar Skeleton -->
    <div class="dfs-preset-bar">
      <div class="dfs-shimmer dfs-preset-lbl"></div>
      <div class="dfs-preset-pills">
        <div class="dfs-shimmer dfs-pill" style="width: 170px;"></div>
        <div class="dfs-shimmer dfs-pill" style="width: 140px;"></div>
        <div class="dfs-shimmer dfs-pill" style="width: 130px;"></div>
        <div class="dfs-shimmer dfs-pill" style="width: 130px;"></div>
      </div>
    </div>

    <!-- Hardware Status Banner Skeleton -->
    <div class="dfs-hw-banner">
      <div class="dfs-shimmer dfs-hw-icon"></div>
      <div class="dfs-hw-info">
        <div class="dfs-shimmer dfs-hw-title"></div>
        <div class="dfs-shimmer dfs-hw-text"></div>
      </div>
    </div>

    <!-- 2 Columns Grid: Form on Left, Predictive Summary on Right -->
    <div class="dfs-grid">
      <!-- Left: Form Sections -->
      <div class="dfs-form-card">
        <!-- Section 1 -->
        <div class="dfs-section">
          <div class="dfs-sec-head">
            <div class="dfs-shimmer dfs-sec-num"></div>
            <div class="dfs-sec-titles">
              <div class="dfs-shimmer dfs-sec-title" style="width: 240px;"></div>
              <div class="dfs-shimmer dfs-sec-sub" style="width: 320px;"></div>
            </div>
          </div>
          <div class="dfs-inputs-row-2">
            <div class="dfs-input-group">
              <div class="dfs-shimmer dfs-input-lbl"></div>
              <div class="dfs-shimmer dfs-input-box"></div>
            </div>
            <div class="dfs-input-group">
              <div class="dfs-shimmer dfs-input-lbl"></div>
              <div class="dfs-shimmer dfs-input-box"></div>
            </div>
          </div>
          <div class="dfs-input-group">
            <div class="dfs-shimmer dfs-input-lbl"></div>
            <div class="dfs-shimmer dfs-input-box"></div>
          </div>
        </div>

        <!-- Section 2 -->
        <div class="dfs-section">
          <div class="dfs-sec-head">
            <div class="dfs-shimmer dfs-sec-num"></div>
            <div class="dfs-sec-titles">
              <div class="dfs-shimmer dfs-sec-title" style="width: 280px;"></div>
              <div class="dfs-shimmer dfs-sec-sub" style="width: 360px;"></div>
            </div>
          </div>
          <div class="dfs-inputs-row-3">
            <div class="dfs-input-group">
              <div class="dfs-shimmer dfs-input-lbl"></div>
              <div class="dfs-shimmer dfs-input-box"></div>
            </div>
            <div class="dfs-input-group">
              <div class="dfs-shimmer dfs-input-lbl"></div>
              <div class="dfs-shimmer dfs-input-box"></div>
            </div>
            <div class="dfs-input-group">
              <div class="dfs-shimmer dfs-input-lbl"></div>
              <div class="dfs-shimmer dfs-input-box"></div>
            </div>
          </div>
        </div>

        <!-- Section 3 -->
        <div class="dfs-section">
          <div class="dfs-sec-head">
            <div class="dfs-shimmer dfs-sec-num"></div>
            <div class="dfs-sec-titles">
              <div class="dfs-shimmer dfs-sec-title" style="width: 260px;"></div>
              <div class="dfs-shimmer dfs-sec-sub" style="width: 300px;"></div>
            </div>
          </div>
          <div class="dfs-mode-cards-grid">
            <div class="dfs-shimmer dfs-mode-card"></div>
            <div class="dfs-shimmer dfs-mode-card"></div>
            <div class="dfs-shimmer dfs-mode-card"></div>
          </div>
        </div>
      </div>

      <!-- Right: Agritech Predictive Calculations & Action Box -->
      <div class="dfs-sidebar-card">
        <div class="dfs-shimmer dfs-sidebar-title"></div>
        <div class="dfs-shimmer dfs-sidebar-sub"></div>

        <div class="dfs-kpi-stack">
          <div class="dfs-kpi-item">
            <div class="dfs-shimmer dfs-kpi-dot"></div>
            <div class="dfs-kpi-texts">
              <div class="dfs-shimmer dfs-kpi-lbl"></div>
              <div class="dfs-shimmer dfs-kpi-val"></div>
            </div>
          </div>
          <div class="dfs-kpi-item">
            <div class="dfs-shimmer dfs-kpi-dot"></div>
            <div class="dfs-kpi-texts">
              <div class="dfs-shimmer dfs-kpi-lbl"></div>
              <div class="dfs-shimmer dfs-kpi-val"></div>
            </div>
          </div>
          <div class="dfs-kpi-item">
            <div class="dfs-shimmer dfs-kpi-dot"></div>
            <div class="dfs-kpi-texts">
              <div class="dfs-shimmer dfs-kpi-lbl"></div>
              <div class="dfs-shimmer dfs-kpi-val"></div>
            </div>
          </div>
          <div class="dfs-kpi-item">
            <div class="dfs-shimmer dfs-kpi-dot"></div>
            <div class="dfs-kpi-texts">
              <div class="dfs-shimmer dfs-kpi-lbl"></div>
              <div class="dfs-shimmer dfs-kpi-val"></div>
            </div>
          </div>
        </div>

        <div class="dfs-summary-box">
          <div class="dfs-shimmer dfs-sum-line" style="width: 90%;"></div>
          <div class="dfs-shimmer dfs-sum-line" style="width: 75%;"></div>
          <div class="dfs-shimmer dfs-sum-line" style="width: 85%;"></div>
        </div>

        <div class="dfs-shimmer dfs-btn-start"></div>
        <div class="dfs-shimmer dfs-btn-cancel"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton placeholder for Drying Form initialization view
</script>

<style scoped>
.drying-form-skeleton-wrapper {
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
  .drying-form-skeleton-wrapper {
    padding: 16px 16px 40px;
    gap: 16px;
  }
}

/* Shimmer Animation Token */
.dfs-shimmer {
  position: relative;
  overflow: hidden;
  background-color: #E2E8F0;
  border-radius: 8px;
}

.dfs-shimmer::after {
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
  animation: dfsShimmerSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  content: '';
}

:global(.dark-theme) .dfs-shimmer {
  background-color: #113847 !important;
}

:global(.dark-theme) .dfs-shimmer::after {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.05) 40%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 60%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes dfsShimmerSweep {
  100% {
    transform: translateX(100%);
  }
}

/* Header */
.dfs-header {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dfs-title {
  width: 340px;
  height: 28px;
  border-radius: 6px;
  margin-bottom: 8px;
}

.dfs-subtitle {
  width: 520px;
  height: 16px;
  border-radius: 4px;
}

/* Preset Bar */
.dfs-preset-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #FFFFFF;
  padding: 12px 18px;
  border-radius: 12px;
  border: 1px solid rgba(226, 232, 240, 0.8);
}

:global(.dark-theme) .dfs-preset-bar {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.dfs-preset-lbl {
  width: 90px;
  height: 14px;
  border-radius: 4px;
}

.dfs-preset-pills {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.dfs-pill {
  height: 34px;
  border-radius: 20px;
}

/* Hardware Status Banner */
.dfs-hw-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #FFFFFF;
  padding: 18px 24px;
  border-radius: 14px;
  border: 1px solid rgba(226, 232, 240, 0.8);
}

:global(.dark-theme) .dfs-hw-banner {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.dfs-hw-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  flex-shrink: 0;
}

.dfs-hw-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
}

.dfs-hw-title {
  width: 220px;
  height: 16px;
  border-radius: 4px;
}

.dfs-hw-text {
  width: 80%;
  height: 14px;
  border-radius: 4px;
}

/* Grid Layout */
.dfs-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 24px;
  align-items: start;
}

@media (max-width: 1024px) {
  .dfs-grid {
    grid-template-columns: 1fr;
  }
}

/* Form Card */
.dfs-form-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 28px;
}

:global(.dark-theme) .dfs-form-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.dfs-section {
  display: flex;
  flex-direction: column;
  gap: 18px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.6);
}

:global(.dark-theme) .dfs-section {
  border-color: rgba(30, 78, 97, 0.4) !important;
}

.dfs-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.dfs-sec-head {
  display: flex;
  align-items: center;
  gap: 14px;
}

.dfs-sec-num {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  flex-shrink: 0;
}

.dfs-sec-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.dfs-sec-title {
  height: 18px;
  border-radius: 4px;
}

.dfs-sec-sub {
  height: 13px;
  border-radius: 4px;
}

.dfs-inputs-row-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.dfs-inputs-row-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

@media (max-width: 640px) {
  .dfs-inputs-row-2,
  .dfs-inputs-row-3 {
    grid-template-columns: 1fr;
  }
}

.dfs-input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dfs-input-lbl {
  width: 120px;
  height: 13px;
  border-radius: 4px;
}

.dfs-input-box {
  width: 100%;
  height: 44px;
  border-radius: 10px;
}

.dfs-mode-cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

@media (max-width: 640px) {
  .dfs-mode-cards-grid {
    grid-template-columns: 1fr;
  }
}

.dfs-mode-card {
  height: 110px;
  border-radius: 12px;
}

/* Sidebar Card */
.dfs-sidebar-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

:global(.dark-theme) .dfs-sidebar-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.dfs-sidebar-title {
  width: 180px;
  height: 18px;
  border-radius: 4px;
}

.dfs-sidebar-sub {
  width: 240px;
  height: 13px;
  border-radius: 4px;
  margin-bottom: 4px;
}

.dfs-kpi-stack {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.dfs-kpi-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  background: #F8FAFC;
  border-radius: 10px;
}

:global(.dark-theme) .dfs-kpi-item {
  background: #071E27 !important;
  border: 1px solid #1E4E61 !important;
}

.dfs-kpi-dot {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  flex-shrink: 0;
}

.dfs-kpi-texts {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.dfs-kpi-lbl {
  width: 110px;
  height: 11px;
  border-radius: 3px;
}

.dfs-kpi-val {
  width: 70px;
  height: 16px;
  border-radius: 4px;
}

.dfs-summary-box {
  background: #F8FAFC;
  padding: 14px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

:global(.dark-theme) .dfs-summary-box {
  background: #071E27 !important;
  border: 1px solid #1E4E61 !important;
}

.dfs-sum-line {
  height: 12px;
  border-radius: 4px;
}

.dfs-btn-start {
  width: 100%;
  height: 48px;
  border-radius: 12px;
  margin-top: 6px;
}

.dfs-btn-cancel {
  width: 100%;
  height: 38px;
  border-radius: 10px;
}
</style>
