<template>
  <div class="settings-skeleton-wrapper" aria-hidden="true">
    <!-- Header -->
    <div class="ss-header">
      <div class="ss-title-block">
        <div class="ss-shimmer ss-title"></div>
        <div class="ss-shimmer ss-subtitle"></div>
      </div>
      <div class="ss-shimmer ss-save-btn"></div>
    </div>

    <!-- Tabs Navigation Bar -->
    <div class="ss-tabs-bar">
      <div class="ss-shimmer ss-tab" style="width: 140px;"></div>
      <div class="ss-shimmer ss-tab" style="width: 130px;"></div>
      <div class="ss-shimmer ss-tab" style="width: 150px;"></div>
      <div class="ss-shimmer ss-tab" style="width: 130px;"></div>
    </div>

    <!-- Main Settings Card 1 -->
    <div class="ss-card">
      <div class="ss-card-header">
        <div class="ss-shimmer ss-card-icon"></div>
        <div class="ss-card-titles">
          <div class="ss-shimmer ss-card-title"></div>
          <div class="ss-shimmer ss-card-sub"></div>
        </div>
      </div>
      <div class="ss-options-grid-3">
        <div class="ss-shimmer ss-option-card"></div>
        <div class="ss-shimmer ss-option-card"></div>
        <div class="ss-shimmer ss-option-card"></div>
      </div>
    </div>

    <!-- Main Settings Card 2 -->
    <div class="ss-card">
      <div class="ss-card-header">
        <div class="ss-shimmer ss-card-icon"></div>
        <div class="ss-card-titles">
          <div class="ss-shimmer ss-card-title"></div>
          <div class="ss-shimmer ss-card-sub"></div>
        </div>
      </div>
      <div class="ss-form-rows">
        <div class="ss-form-row">
          <div class="ss-shimmer ss-row-lbl"></div>
          <div class="ss-shimmer ss-row-input"></div>
        </div>
        <div class="ss-form-row">
          <div class="ss-shimmer ss-row-lbl"></div>
          <div class="ss-shimmer ss-row-input"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton placeholder for SettingsView
</script>

<style scoped>
.settings-skeleton-wrapper {
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
  .settings-skeleton-wrapper {
    padding: 0;
    gap: 16px;
  }
}

.ss-shimmer {
  position: relative;
  overflow: hidden;
  background-color: #E2E8F0;
  border-radius: 8px;
}

.ss-shimmer::after {
  position: absolute;
  top: 0; right: 0; bottom: 0; left: 0;
  transform: translateX(-100%);
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.45) 40%,
    rgba(255, 255, 255, 0.7) 50%,
    rgba(255, 255, 255, 0.45) 60%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: ssShimmerSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  content: '';
}

:global(.dark-theme) .ss-shimmer {
  background-color: #113847 !important;
}

:global(.dark-theme) .ss-shimmer::after {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.05) 40%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 60%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes ssShimmerSweep {
  100% { transform: translateX(100%); }
}

.ss-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

.ss-title-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ss-title {
  width: 260px;
  height: 28px;
  border-radius: 6px;
}

.ss-subtitle {
  width: 440px;
  height: 15px;
  border-radius: 4px;
}

.ss-save-btn {
  width: 150px;
  height: 40px;
  border-radius: 10px;
}

.ss-tabs-bar {
  display: flex;
  gap: 10px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.8);
  padding-bottom: 12px;
}

:global(.dark-theme) .ss-tabs-bar {
  border-color: #1E4E61 !important;
}

.ss-tab {
  height: 38px;
  border-radius: 10px;
}

.ss-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

:global(.dark-theme) .ss-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.ss-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
}

.ss-card-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
}

.ss-card-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.ss-card-title {
  width: 240px;
  height: 18px;
  border-radius: 4px;
}

.ss-card-sub {
  width: 380px;
  height: 13px;
  border-radius: 4px;
}

.ss-options-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

@media (max-width: 768px) {
  .ss-options-grid-3 {
    grid-template-columns: 1fr;
  }
}

.ss-option-card {
  height: 110px;
  border-radius: 12px;
}

.ss-form-rows {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.ss-form-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  padding-bottom: 14px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.6);
}

:global(.dark-theme) .ss-form-row {
  border-color: rgba(30, 78, 97, 0.4) !important;
}

.ss-row-lbl {
  width: 220px;
  height: 15px;
  border-radius: 4px;
}

.ss-row-input {
  width: 140px;
  height: 36px;
  border-radius: 8px;
}
</style>
