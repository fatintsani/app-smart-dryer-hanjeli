<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="production-notice-card">
        <div class="notice-icon-box">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <h1 class="notice-title">Mode Produksi Alat Fisik Aktif</h1>
        <p class="notice-subtitle">
          Fungsi simulasi perangkat lunak telah dinonaktifkan secara permanen. Website kini beroperasi 100% menggunakan data telemetri aktual langsung dari unit mikrokontroler ESP32 Smart Dryer Hanjeli melalui jaringan WiFi (MQTT Reverb), Bluetooth BLE, dan USB Serial.
        </p>

        <div class="active-specs-grid">
          <div class="spec-item">
            <span class="spec-label">Mode IoT</span>
            <span class="spec-value active-green">HARDWARE (LIVE)</span>
          </div>
          <div class="spec-item">
            <span class="spec-label">Lingkungan</span>
            <span class="spec-value active-green">PRODUCTION</span>
          </div>
          <div class="spec-item">
            <span class="spec-label">Integritas Sensor</span>
            <span class="spec-value">Direct Physical Telemetry</span>
          </div>
        </div>

        <div class="notice-actions">
          <button class="btn-primary" @click="goToDevices">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
              <line x1="8" y1="21" x2="16" y2="21"></line>
              <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
            <span>Buka Manajemen Perangkat IoT</span>
          </button>
          <button class="btn-outline-action" @click="goToDashboard">
            <span>Kembali ke Dashboard</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goToDevices() {
  router.push({ name: 'admin-devices' })
}

function goToDashboard() {
  router.push({ name: 'operator-dashboard' })
}
</script>

<style scoped>
.admin-page {
  padding: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 70vh;
}

.production-notice-card {
  max-width: 640px;
  background: var(--bg-card, #ffffff);
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 16px;
  padding: 40px 32px;
  text-align: center;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}

.notice-icon-box {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(16, 185, 129, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px auto;
}

.notice-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-heading, #0f172a);
  margin-bottom: 12px;
}

.notice-subtitle {
  font-size: 0.95rem;
  color: var(--text-muted, #64748b);
  line-height: 1.6;
  margin-bottom: 28px;
}

.active-specs-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 32px;
  background: var(--bg-surface-secondary, #f8fafc);
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 12px;
  padding: 16px;
}

.spec-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.spec-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted, #64748b);
  font-weight: 600;
}

.spec-value {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--text-heading, #0f172a);
}

.spec-value.active-green {
  color: #10b981;
}

.notice-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 10px;
  background: #10b981;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.9rem;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #059669;
}

.btn-outline-action {
  padding: 12px 20px;
  border-radius: 10px;
  background: transparent;
  color: var(--text-main, #334155);
  font-weight: 600;
  font-size: 0.9rem;
  border: 1px solid var(--border-color, #cbd5e1);
  cursor: pointer;
  transition: all 0.2s;
}

.btn-outline-action:hover {
  background: var(--bg-hover, #f1f5f9);
}

@media (max-width: 640px) {
  .active-specs-grid {
    grid-template-columns: 1fr;
    text-align: left;
  }
}
</style>
