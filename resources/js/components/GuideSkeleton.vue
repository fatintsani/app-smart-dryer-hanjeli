<template>
  <div class="guide-skeleton-container" aria-hidden="true">
    <!-- 1. Header Area Skeleton -->
    <div class="page-header-row">
      <div class="page-header-group">
        <div class="skeleton-bar skeleton-title-bar"></div>
        <div class="skeleton-bar skeleton-sub-bar"></div>
      </div>
    </div>

    <!-- 2. System Readiness Overview Banner Skeleton -->
    <div class="system-overview-banner-skeleton">
      <div class="banner-left">
        <div class="skeleton-bar banner-status-icon-skeleton"></div>
        <div class="banner-text-skeleton">
          <div class="skeleton-bar" style="width: 220px; height: 16px;"></div>
          <div class="skeleton-bar" style="width: 340px; height: 12px; margin-top: 5px;"></div>
        </div>
      </div>

      <div class="banner-indicators-skeleton">
        <div class="indicator-item-skeleton">
          <div class="skeleton-bar" style="width: 90px; height: 10px;"></div>
          <div class="skeleton-bar" style="width: 125px; height: 14px; margin-top: 4px;"></div>
        </div>
        <div class="indicator-sep-skeleton"></div>
        <div class="indicator-item-skeleton">
          <div class="skeleton-bar" style="width: 90px; height: 10px;"></div>
          <div class="skeleton-bar" style="width: 135px; height: 14px; margin-top: 4px;"></div>
        </div>
        <div class="indicator-sep-skeleton"></div>
        <div class="indicator-item-skeleton">
          <div class="skeleton-bar" style="width: 90px; height: 10px;"></div>
          <div class="skeleton-bar" style="width: 110px; height: 14px; margin-top: 4px;"></div>
        </div>
      </div>
    </div>

    <!-- 3. Navigation Tabs Skeleton -->
    <div class="guide-nav-tabs-skeleton">
      <div class="skeleton-bar tab-btn-skeleton" style="width: 170px;"></div>
      <div class="skeleton-bar tab-btn-skeleton" style="width: 130px;"></div>
      <div class="skeleton-bar tab-btn-skeleton" style="width: 220px;"></div>
      <div class="skeleton-bar tab-btn-skeleton" style="width: 200px;"></div>
    </div>

    <!-- 4. Sensor Catalog Grid Skeleton (6 items) -->
    <div class="devices-simple-grid-skeleton">
      <div v-for="i in 6" :key="'guide-sensor-' + i" class="sensor-simple-card-skeleton">
        <!-- Card Top Header -->
        <div class="sensor-card-top-skeleton">
          <div class="skeleton-bar sensor-icon-box-skeleton"></div>
          <div class="sensor-title-info-skeleton">
            <div class="skeleton-bar" style="width: 140px; height: 15px;"></div>
            <div class="skeleton-bar" style="width: 75px; height: 11px; margin-top: 4px;"></div>
          </div>
          <div class="skeleton-bar sensor-status-tag-skeleton"></div>
        </div>

        <!-- Card Description -->
        <div class="sensor-desc-skeleton">
          <div class="skeleton-bar" style="width: 100%; height: 12px;"></div>
          <div class="skeleton-bar" style="width: 75%; height: 12px; margin-top: 4px;"></div>
        </div>

        <!-- Card Bottom Meta -->
        <div class="sensor-card-bottom-skeleton">
          <div class="skeleton-bar" style="width: 115px; height: 24px; border-radius: 999px;"></div>
          <div class="skeleton-bar" style="width: 55px; height: 24px; border-radius: 6px;"></div>
        </div>
      </div>
    </div>

    <!-- 5. Partnership & Collaboration Strip Skeleton -->
    <div class="collab-strip-card-skeleton">
      <div class="collab-item-skeleton">
        <div class="skeleton-bar collab-logo-skeleton"></div>
        <div class="collab-text-skeleton">
          <div class="skeleton-bar" style="width: 120px; height: 11px;"></div>
          <div class="skeleton-bar" style="width: 160px; height: 15px; margin-top: 4px;"></div>
          <div class="skeleton-bar" style="width: 90%; height: 12px; margin-top: 5px;"></div>
        </div>
      </div>
      <div class="collab-divider-skeleton"></div>
      <div class="collab-item-skeleton">
        <div class="skeleton-bar collab-logo-skeleton"></div>
        <div class="collab-text-skeleton">
          <div class="skeleton-bar" style="width: 140px; height: 11px;"></div>
          <div class="skeleton-bar" style="width: 180px; height: 15px; margin-top: 4px;"></div>
          <div class="skeleton-bar" style="width: 90%; height: 12px; margin-top: 5px;"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Ultra-High-Fidelity Pure Monochrome Skeleton for DevicesGuideView
</script>

<style scoped>
.guide-skeleton-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
  box-sizing: border-box;
}

/* Pure Neutral Monochrome Shimmer Animation */
.skeleton-bar {
  position: relative;
  overflow: hidden;
  background: #E2E8F0;
  border-radius: 6px;
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

/* 1. Header */
.page-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.page-header-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.skeleton-title-bar {
  width: 320px;
  height: 32px;
  border-radius: 8px;
}

.skeleton-sub-bar {
  width: 520px;
  max-width: 100%;
  height: 16px;
  border-radius: 4px;
}

/* 2. System Overview Banner */
.system-overview-banner-skeleton {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  box-sizing: border-box;
}

:global(.dark-theme) .system-overview-banner-skeleton {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.banner-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.banner-status-icon-skeleton {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  flex-shrink: 0;
}

.banner-text-skeleton {
  display: flex;
  flex-direction: column;
}

.banner-indicators-skeleton {
  display: flex;
  align-items: center;
  gap: 18px;
  flex-wrap: wrap;
}

.indicator-item-skeleton {
  display: flex;
  flex-direction: column;
}

.indicator-sep-skeleton {
  width: 1px;
  height: 30px;
  background: rgba(203, 213, 225, 0.5);
}

:global(.dark-theme) .indicator-sep-skeleton {
  background: rgba(30, 58, 43, 0.6);
}

/* 3. Navigation Tabs */
.guide-nav-tabs-skeleton {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid rgba(191, 202, 186, 0.4);
  padding-bottom: 2px;
  flex-wrap: wrap;
}

:global(.dark-theme) .guide-nav-tabs-skeleton {
  border-bottom-color: #1E4E61 !important;
}

.tab-btn-skeleton {
  height: 38px;
  border-radius: 8px 8px 0 0;
}

/* 4. Sensor Catalog Grid */
.devices-simple-grid-skeleton {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 14px;
}

.sensor-simple-card-skeleton {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-sizing: border-box;
}

:global(.dark-theme) .sensor-simple-card-skeleton {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.sensor-card-top-skeleton {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sensor-icon-box-skeleton {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  flex-shrink: 0;
}

.sensor-title-info-skeleton {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.sensor-status-tag-skeleton {
  width: 55px;
  height: 22px;
  border-radius: 999px;
  flex-shrink: 0;
}

.sensor-desc-skeleton {
  display: flex;
  flex-direction: column;
}

.sensor-card-bottom-skeleton {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 4px;
}

/* 5. Partnership & Collaboration Strip */
.collab-strip-card-skeleton {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  box-sizing: border-box;
  flex-wrap: wrap;
}

:global(.dark-theme) .collab-strip-card-skeleton {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.collab-item-skeleton {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
  min-width: 280px;
}

.collab-logo-skeleton {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  flex-shrink: 0;
}

.collab-text-skeleton {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.collab-divider-skeleton {
  width: 1px;
  height: 50px;
  background: rgba(203, 213, 225, 0.5);
}

:global(.dark-theme) .collab-divider-skeleton {
  background: #1E4E61 !important;
}

@media (max-width: 768px) {
  .system-overview-banner-skeleton {
    flex-direction: column;
    align-items: flex-start;
  }
  .banner-indicators-skeleton {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    width: 100%;
  }
  .indicator-sep-skeleton {
    display: none;
  }
  .collab-strip-card-skeleton {
    flex-direction: column;
    align-items: flex-start;
  }
  .collab-divider-skeleton {
    display: none;
  }
}
</style>
