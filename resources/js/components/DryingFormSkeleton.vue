<template>
  <div class="drying-form-skeleton-page" aria-hidden="true">
    <div class="drying-form-skeleton-container">
      
      <!-- 1. Header Card Skeleton (1:1 with form-header-card) -->
      <div class="skeleton-card skeleton-header-card">
        <div class="skeleton-bar sk-title"></div>
        <div class="skeleton-bar sk-subtitle"></div>
      </div>

      <!-- 2. Preset Box Skeleton (1:1 with preset-box) -->
      <div class="skeleton-card skeleton-preset-box">
        <div class="skeleton-bar sk-preset-title"></div>
        <div class="sk-preset-btns">
          <div class="skeleton-bar sk-preset-btn"></div>
          <div class="skeleton-bar sk-preset-btn"></div>
        </div>
      </div>

      <!-- 3. Form Grid Layout: Inputs on Left, Summary on Right -->
      <div class="sk-grid-layout">
        <!-- Left: Form Sections -->
        <div class="sk-inputs-col">
          
          <!-- Section 1: Data Gabah Hanjeli -->
          <div class="skeleton-card sk-form-section">
            <div class="skeleton-bar sk-sec-heading"></div>
            <div class="skeleton-bar sk-sec-desc"></div>

            <div class="sk-two-cols">
              <div class="sk-field">
                <div class="skeleton-bar sk-field-label"></div>
                <div class="skeleton-bar sk-field-input"></div>
              </div>
              <div class="sk-field">
                <div class="skeleton-bar sk-field-label"></div>
                <div class="skeleton-bar sk-field-input"></div>
              </div>
            </div>

            <div class="sk-two-cols" style="margin-top: 14px;">
              <div class="sk-field">
                <div class="skeleton-bar sk-field-label"></div>
                <div class="skeleton-bar sk-field-input"></div>
              </div>
              <div class="sk-field">
                <div class="skeleton-bar sk-field-label"></div>
                <div class="skeleton-bar sk-field-input"></div>
              </div>
            </div>

            <div class="sk-field" style="margin-top: 14px;">
              <div class="skeleton-bar sk-field-label"></div>
              <div class="skeleton-bar sk-field-input"></div>
            </div>
          </div>

          <!-- Section 2: Mode Waktu & Pengaturan Jadwal -->
          <div class="skeleton-card sk-form-section">
            <div class="sk-sec-header-row">
              <div>
                <div class="skeleton-bar sk-sec-heading"></div>
                <div class="skeleton-bar sk-sec-desc"></div>
              </div>
              <div class="skeleton-bar sk-timing-switch"></div>
            </div>

            <!-- Auto predict banner placeholder -->
            <div class="sk-predict-banner">
              <div class="sk-predict-badge-row">
                <div class="skeleton-bar sk-chip-pill"></div>
                <div class="skeleton-bar sk-chip-pill"></div>
              </div>
              <div class="sk-timeline-quad">
                <div v-for="q in 4" :key="'sk-q-' + q" class="sk-quad-item">
                  <div class="skeleton-bar sk-quad-lbl"></div>
                  <div class="skeleton-bar sk-quad-val"></div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Right: Summary Card (Sticky) -->
        <div class="sk-summary-col">
          <div class="skeleton-card sk-summary-card">
            <div class="skeleton-bar sk-sum-title"></div>
            <div class="skeleton-bar sk-sum-desc"></div>

            <div class="sk-summary-list">
              <div v-for="s in 5" :key="'sk-sum-row-' + s" class="sk-summary-row">
                <div class="skeleton-bar sk-sum-lbl"></div>
                <div class="skeleton-bar sk-sum-val"></div>
              </div>
            </div>

            <div class="skeleton-bar sk-btn-submit"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
// DryingFormSkeleton faithful 1:1 replica of DryingFormView
</script>

<style scoped>
/* ==========================================================================
   DRYING FORM SKELETON BASE LAYOUT
   ========================================================================== */
.drying-form-skeleton-page {
  width: 100%;
  max-width: 100%;
  min-height: calc(100vh - 72px);
  background: var(--color-bg-page, #F0F4F2);
  box-sizing: border-box;
  overflow-x: hidden;
}

:global(.dark-theme) .drying-form-skeleton-page {
  background: #04131A !important;
}

.drying-form-skeleton-container {
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
.skeleton-header-card {
  padding: 22px 26px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-title {
  width: 340px;
  max-width: 100%;
  height: 28px;
}

.sk-subtitle {
  width: 480px;
  max-width: 100%;
  height: 14px;
}

/* 2. Preset Box */
.skeleton-preset-box {
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-preset-title {
  width: 150px;
  height: 14px;
}

.sk-preset-btns {
  display: flex;
  gap: 10px;
}

.sk-preset-btn {
  width: 220px;
  height: 38px;
  border-radius: 8px;
}

/* 3. Grid Layout */
.sk-grid-layout {
  display: grid;
  grid-template-columns: 1.5fr 1fr;
  gap: 20px;
}

.sk-inputs-col {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.sk-form-section {
  padding: 22px;
}

.sk-sec-heading {
  width: 200px;
  height: 18px;
  margin-bottom: 6px;
}

.sk-sec-desc {
  width: 320px;
  max-width: 90%;
  height: 12px;
  margin-bottom: 16px;
}

.sk-two-cols {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.sk-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-field-label {
  width: 120px;
  height: 12px;
}

.sk-field-input {
  width: 100%;
  height: 44px;
  border-radius: 10px;
}

.sk-sec-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.sk-timing-switch {
  width: 230px;
  height: 36px;
  border-radius: 10px;
}

.sk-predict-banner {
  padding: 16px;
  border-radius: 14px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sk-predict-badge-row {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.sk-chip-pill {
  width: 160px;
  height: 22px;
  border-radius: 20px;
}

.sk-timeline-quad {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.sk-quad-item {
  padding: 10px;
  border-radius: 8px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-quad-lbl {
  width: 70px;
  height: 10px;
}

.sk-quad-val {
  width: 90px;
  height: 16px;
}

/* Summary Card (Right) */
.sk-summary-col {
  display: flex;
  flex-direction: column;
}

.sk-summary-card {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sk-sum-title {
  width: 180px;
  height: 20px;
}

.sk-sum-desc {
  width: 260px;
  max-width: 95%;
  height: 12px;
}

.sk-summary-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 14px 0;
  border-top: 1px solid #E2E8F0;
  border-bottom: 1px solid #E2E8F0;
}

.sk-summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sk-sum-lbl {
  width: 100px;
  height: 12px;
}

.sk-sum-val {
  width: 110px;
  height: 14px;
}

.sk-btn-submit {
  width: 100%;
  height: 50px;
  border-radius: 12px;
  margin-top: 8px;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
@media (max-width: 900px) {
  .drying-form-skeleton-container {
    padding: 0;
    gap: 12px;
  }

  .skeleton-card {
    padding: 16px;
    border-radius: 14px;
  }

  .sk-grid-layout {
    grid-template-columns: 1fr;
  }

  .sk-preset-btns {
    width: 100%;
  }

  .sk-preset-btn {
    flex: 1;
  }

  .sk-timeline-quad {
    grid-template-columns: repeat(2, 1fr);
  }

  .sk-sec-header-row {
    flex-direction: column;
    align-items: flex-start;
  }

  .sk-timing-switch {
    width: 100%;
  }
}

@media (max-width: 600px) {
  .sk-two-cols {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .sk-preset-btns {
    flex-direction: column;
  }

  .sk-predict-badge-row {
    flex-direction: column;
    gap: 8px;
  }

  .sk-chip-pill {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .sk-timeline-quad {
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

:global(.dark-theme) .sk-predict-banner,
:global(.dark-theme) .sk-quad-item {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .sk-summary-list {
  border-color: #1E4E61;
}
</style>
