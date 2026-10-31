<template>
  <div class="monitoring-skeleton-page">
    <div class="monitoring-skeleton-container">
      
      <!-- 1. Header Card Skeleton (1:1 with monitoring-header-card) -->
      <div class="skeleton-card skeleton-header-card">
        <div class="sk-header-left">
          <div class="skeleton-bar sk-pill"></div>
          <div class="skeleton-bar sk-title"></div>
          <div class="skeleton-bar sk-sub"></div>
        </div>
        <div class="sk-header-right">
          <div class="skeleton-bar sk-btn-export"></div>
        </div>
      </div>

      <!-- 2. Kartu Indikator Sensor Ruangan (4 Kartu) Skeleton -->
      <div class="skeleton-sensor-grid">
        <div v-for="i in 4" :key="'sensor-skel-' + i" class="skeleton-card skeleton-sensor-card">
          <div class="sk-sensor-head">
            <div class="skeleton-bar sk-sensor-label"></div>
            <div class="skeleton-bar sk-sensor-icon"></div>
          </div>
          <div class="sk-sensor-val-row">
            <div class="skeleton-bar sk-sensor-number"></div>
            <div class="skeleton-bar sk-sensor-unit"></div>
          </div>
          <div class="sk-sensor-badge-row">
            <div class="skeleton-bar sk-sensor-badge"></div>
          </div>
        </div>
      </div>

      <!-- 3. Pusat Kendali Alat & Pemanas Skeleton (1:1 with ActuatorControlCard) -->
      <div class="skeleton-card skeleton-actuator-card">
        <div class="sk-act-head">
          <div class="sk-act-head-left">
            <div class="skeleton-bar sk-act-icon"></div>
            <div class="sk-act-titles">
              <div class="skeleton-bar sk-act-title"></div>
              <div class="skeleton-bar sk-act-sub"></div>
            </div>
          </div>
          <div class="skeleton-bar sk-act-mode-toggle"></div>
        </div>

        <div class="sk-act-quick-bar">
          <div class="skeleton-bar sk-act-status-text"></div>
          <div class="sk-act-quick-btns">
            <div class="skeleton-bar sk-act-qbtn"></div>
            <div class="skeleton-bar sk-act-qbtn"></div>
            <div class="skeleton-bar sk-act-qbtn"></div>
          </div>
        </div>

        <div class="skeleton-bar sk-act-reason-box"></div>

        <div class="sk-act-switches-grid">
          <div class="sk-act-switch-box">
            <div class="sk-act-sw-info">
              <div class="skeleton-bar sk-act-sw-icon"></div>
              <div class="sk-act-sw-texts">
                <div class="skeleton-bar sk-act-sw-title"></div>
                <div class="skeleton-bar sk-act-sw-desc"></div>
              </div>
            </div>
            <div class="skeleton-bar sk-act-toggle-pill"></div>
          </div>
          <div class="sk-act-switch-box">
            <div class="sk-act-sw-info">
              <div class="skeleton-bar sk-act-sw-icon"></div>
              <div class="sk-act-sw-texts">
                <div class="skeleton-bar sk-act-sw-title"></div>
                <div class="skeleton-bar sk-act-sw-desc"></div>
              </div>
            </div>
            <div class="skeleton-bar sk-act-toggle-pill"></div>
          </div>
        </div>

        <div class="sk-act-led-bar">
          <div class="skeleton-bar sk-act-led-label"></div>
          <div class="sk-act-led-items">
            <div class="skeleton-bar sk-act-led-chip"></div>
            <div class="skeleton-bar sk-act-led-chip"></div>
            <div class="skeleton-bar sk-act-led-chip"></div>
          </div>
        </div>
      </div>

      <!-- 4. Analisis Perkembangan Suhu & Kelembapan Chart Card Skeleton -->
      <div class="skeleton-card skeleton-chart-card">
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

        <!-- Canvas Area with Guidelines -->
        <div class="sk-chart-canvas">
          <div class="sk-chart-grid-line" style="top: 25%;"></div>
          <div class="sk-chart-grid-line" style="top: 50%;"></div>
          <div class="sk-chart-grid-line" style="top: 75%;"></div>
          <div class="sk-mock-curves">
            <div class="sk-curve-line curve-red"></div>
            <div class="sk-curve-line curve-blue"></div>
          </div>
        </div>

        <!-- Bottom Legend & Time Labels -->
        <div class="sk-chart-footer">
          <div class="sk-chart-legend">
            <div class="skeleton-bar sk-legend-pill"></div>
            <div class="skeleton-bar sk-legend-pill"></div>
          </div>
          <div class="sk-chart-time-labels">
            <div v-for="t in 5" :key="'sk-time-' + t" class="skeleton-bar sk-time-tag"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.monitoring-skeleton-page {
  width: 100%;
  min-height: 100%;
  animation: fadeInSkeleton 0.25s ease-in-out;
}

@keyframes fadeInSkeleton {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Identical Container to .monitoring-main-container */
.monitoring-skeleton-container {
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
  box-sizing: border-box;
}

/* Card base */
.skeleton-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  box-sizing: border-box;
}

/* Shimmer base */
.skeleton-bar {
  position: relative;
  overflow: hidden;
  background: #E2E8F0;
  border-radius: 6px;
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
    rgba(255, 255, 255, 0.5) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: skeletonShimmer 1.6s infinite ease-in-out;
}

@keyframes skeletonShimmer {
  100% {
    transform: translateX(100%);
  }
}

/* 1. Header Card */
.skeleton-header-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.sk-header-left {
  flex: 1;
  min-width: 260px;
}

.sk-pill {
  width: 200px;
  height: 24px;
  border-radius: 9999px;
  margin-bottom: 12px;
}

.sk-title {
  width: 380px;
  max-width: 90%;
  height: 26px;
  margin-bottom: 8px;
}

.sk-sub {
  width: 520px;
  max-width: 95%;
  height: 14px;
}

.sk-btn-export {
  width: 150px;
  height: 40px;
  border-radius: 10px;
}

/* 2. Sensor Grid (4 Cards) */
.skeleton-sensor-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.skeleton-sensor-card {
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 140px;
}

.sk-sensor-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.sk-sensor-label {
  width: 140px;
  max-width: 70%;
  height: 13px;
}

.sk-sensor-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  flex-shrink: 0;
}

.sk-sensor-val-row {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 12px;
}

.sk-sensor-number {
  width: 70px;
  height: 34px;
}

.sk-sensor-unit {
  width: 25px;
  height: 16px;
}

.sk-sensor-badge {
  width: 110px;
  height: 22px;
  border-radius: 6px;
}

/* 3. Actuator Card */
.skeleton-actuator-card {
  padding: 22px 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.sk-act-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.sk-act-head-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.sk-act-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  flex-shrink: 0;
}

.sk-act-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sk-act-title {
  width: 240px;
  max-width: 80%;
  height: 18px;
}

.sk-act-sub {
  width: 360px;
  max-width: 90%;
  height: 12px;
}

.sk-act-mode-toggle {
  width: 160px;
  height: 34px;
  border-radius: 8px;
}

.sk-act-quick-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-radius: 10px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-act-status-text {
  width: 220px;
  height: 16px;
}

.sk-act-quick-btns {
  display: flex;
  gap: 8px;
}

.sk-act-qbtn {
  width: 120px;
  height: 32px;
  border-radius: 8px;
}

.sk-act-reason-box {
  width: 100%;
  height: 60px;
  border-radius: 12px;
  margin-top: 14px;
}

.sk-act-switches-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
}

.sk-act-switch-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  border-radius: 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  gap: 12px;
}

.sk-act-sw-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.sk-act-sw-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  flex-shrink: 0;
}

.sk-act-sw-texts {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.sk-act-sw-title {
  width: 170px;
  max-width: 85%;
  height: 14px;
}

.sk-act-sw-desc {
  width: 220px;
  max-width: 95%;
  height: 11px;
}

.sk-act-toggle-pill {
  width: 44px;
  height: 24px;
  border-radius: 12px;
  flex-shrink: 0;
}

.sk-act-led-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 10px;
  border-top: 1px dashed #E2E8F0;
  gap: 12px;
  flex-wrap: wrap;
}

.sk-act-led-label {
  width: 200px;
  height: 12px;
}

.sk-act-led-items {
  display: flex;
  gap: 12px;
}

.sk-act-led-chip {
  width: 130px;
  height: 18px;
  border-radius: 4px;
}

/* 4. Chart Card */
.skeleton-chart-card {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
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
  width: 310px;
  max-width: 80%;
  height: 20px;
}

.sk-chart-period-badge {
  width: 90px;
  height: 20px;
  border-radius: 20px;
}

.sk-chart-subtitle {
  width: 480px;
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
  left: 10%;
  right: 10%;
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding-top: 4px;
}

.sk-chart-legend {
  display: flex;
  gap: 12px;
}

.sk-legend-pill {
  width: 90px;
  height: 14px;
}

.sk-chart-time-labels {
  display: flex;
  gap: 16px;
}

.sk-time-tag {
  width: 45px;
  height: 11px;
}

/* ==========================================================================
   RESPONSIVE (MOBILE & TABLET)
   ========================================================================== */
@media (max-width: 1024px) {
  .skeleton-sensor-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 900px) {
  .monitoring-skeleton-container {
    padding: 0;
    gap: 12px;
  }

  .skeleton-card {
    padding: 16px;
    border-radius: 14px;
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

  .sk-act-switches-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .sk-chart-canvas {
    height: 180px;
  }
}

@media (max-width: 600px) {
  .skeleton-sensor-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .sk-metric-badges-strip {
    grid-template-columns: 1fr;
  }

  .sk-act-quick-btns {
    width: 100%;
    justify-content: space-between;
  }

  .sk-act-qbtn {
    flex: 1;
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

:global(.dark-theme) .sk-act-quick-bar,
:global(.dark-theme) .sk-act-switch-box,
:global(.dark-theme) .sk-metric-badges-strip,
:global(.dark-theme) .sk-chart-canvas {
  background: #071E27;
  border-color: #1E4E61;
}

:global(.dark-theme) .sk-chart-grid-line {
  background: #1E4E61;
  border-color: #1E4E61;
}
</style>
