<template>
  <div class="admin-skeleton-wrapper" aria-hidden="true">
    <!-- Header Area -->
    <div class="as-header">
      <div class="as-title-block">
        <div class="as-shimmer as-title"></div>
        <div class="as-shimmer as-subtitle"></div>
      </div>
      <div class="as-actions">
        <div class="as-shimmer as-btn" style="width: 130px;"></div>
        <div class="as-shimmer as-btn" style="width: 140px;"></div>
      </div>
    </div>

    <!-- 4 Stats Cards Grid -->
    <div class="as-stats-grid">
      <div v-for="i in 4" :key="i" class="as-stat-card">
        <div class="as-stat-top">
          <div class="as-shimmer as-stat-lbl"></div>
          <div class="as-shimmer as-stat-icon"></div>
        </div>
        <div class="as-stat-mid">
          <div class="as-shimmer as-stat-num"></div>
          <div class="as-shimmer as-stat-chip"></div>
        </div>
        <div class="as-shimmer as-stat-sub"></div>
      </div>
    </div>

    <!-- Main Section Card: Table or Chart -->
    <div class="as-main-card">
      <div class="as-card-top">
        <div class="as-card-titles">
          <div class="as-shimmer as-card-title"></div>
          <div class="as-shimmer as-card-sub"></div>
        </div>
        <div class="as-shimmer as-search-box"></div>
      </div>

      <!-- Table Header & 4 Rows Placeholder -->
      <div class="as-table-header">
        <div class="as-shimmer as-th" style="width: 20%;"></div>
        <div class="as-shimmer as-th" style="width: 25%;"></div>
        <div class="as-shimmer as-th" style="width: 20%;"></div>
        <div class="as-shimmer as-th" style="width: 15%;"></div>
        <div class="as-shimmer as-th" style="width: 20%;"></div>
      </div>

      <div v-for="r in 4" :key="r" class="as-table-row">
        <div class="as-shimmer as-td" style="width: 20%;"></div>
        <div class="as-shimmer as-td" style="width: 25%;"></div>
        <div class="as-shimmer as-td" style="width: 20%;"></div>
        <div class="as-shimmer as-td" style="width: 15%;"></div>
        <div class="as-shimmer as-td" style="width: 20%;"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton placeholder for Admin Views (Overview, Users, Devices, Logs, Parameters)
</script>

<style scoped>
.admin-skeleton-wrapper {
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
  .admin-skeleton-wrapper {
    padding: 16px 16px 40px;
    gap: 16px;
  }
}

.as-shimmer {
  position: relative;
  overflow: hidden;
  background-color: #E2E8F0;
  border-radius: 8px;
}

.as-shimmer::after {
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
  animation: asShimmerSweep 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  content: '';
}

:global(.dark-theme) .as-shimmer {
  background-color: #113847 !important;
}

:global(.dark-theme) .as-shimmer::after {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.05) 40%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 60%,
    rgba(255, 255, 255, 0) 100%
  ) !important;
}

@keyframes asShimmerSweep {
  100% { transform: translateX(100%); }
}

.as-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

@media (max-width: 768px) {
  .as-header {
    flex-direction: column;
  }
}

.as-title-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.as-title {
  width: 260px;
  height: 28px;
  border-radius: 6px;
}

.as-subtitle {
  width: 420px;
  height: 15px;
  border-radius: 4px;
}

.as-actions {
  display: flex;
  gap: 10px;
}

.as-btn {
  height: 38px;
  border-radius: 10px;
}

.as-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

@media (max-width: 1100px) {
  .as-stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .as-stats-grid {
    grid-template-columns: 1fr;
  }
}

.as-stat-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

:global(.dark-theme) .as-stat-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.as-stat-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.as-stat-lbl {
  width: 130px;
  height: 12px;
  border-radius: 4px;
}

.as-stat-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.as-stat-mid {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.as-stat-num {
  width: 70px;
  height: 30px;
  border-radius: 6px;
}

.as-stat-chip {
  width: 60px;
  height: 18px;
  border-radius: 12px;
}

.as-stat-sub {
  width: 150px;
  height: 12px;
  border-radius: 4px;
}

.as-main-card {
  background: #FFFFFF;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

:global(.dark-theme) .as-main-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
}

.as-card-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.as-card-titles {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.as-card-title {
  width: 240px;
  height: 20px;
  border-radius: 4px;
}

.as-card-sub {
  width: 360px;
  height: 13px;
  border-radius: 4px;
}

.as-search-box {
  width: 240px;
  height: 38px;
  border-radius: 10px;
}

.as-table-header {
  display: flex;
  padding: 10px 12px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.8);
  gap: 16px;
}

:global(.dark-theme) .as-table-header {
  border-color: #1E4E61 !important;
}

.as-th {
  height: 14px;
  border-radius: 4px;
}

.as-table-row {
  display: flex;
  align-items: center;
  padding: 14px 12px;
  border-bottom: 1px solid rgba(226, 232, 240, 0.4);
  gap: 16px;
}

:global(.dark-theme) .as-table-row {
  border-color: rgba(30, 78, 97, 0.4) !important;
}

.as-td {
  height: 18px;
  border-radius: 4px;
}
</style>
