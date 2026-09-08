<template>
  <div class="admin-page">
    <div class="admin-content">
      <!-- Top Title & Action Bar -->
      <div class="header-action-row">
        <div class="title-group">
          <h1 class="page-title">{{ $t('admin.tabDevices') }}</h1>
          <p class="page-subtitle">{{ $t('admin.deviceDesc') }}</p>
        </div>
      </div>

      <div class="devices-grid-2">
        <!-- Gateway ESP32 Card -->
        <div class="content-card">
          <div class="card-head-flex">
            <div class="icon-box-head">
              <div class="icon-circle icon-green-light">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                  <rect x="2" y="2" width="20" height="20" rx="4"></rect>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </div>
              <div>
                <h3 class="card-section-heading">ESP32 Gateway Node</h3>
                <p class="card-section-sub">Firmware v2.4.2 • Unit Greenhouse #01 Waluran</p>
              </div>
            </div>
            <span class="badge-pill bg-green-subtle text-green-dark">
              <span class="dot-green"></span> Online
            </span>
          </div>

          <div class="spec-list">
            <div class="spec-row">
              <span class="spec-label">Sinyal Wi-Fi / RSSI:</span>
              <strong class="text-green">-58 dBm (Sinyal Sangat Baik)</strong>
            </div>
            <div class="spec-row">
              <span class="spec-label">Ingestion API Endpoint:</span>
              <code>POST /api/v1/telemetry/ingest</code>
            </div>
            <div class="spec-row">
              <span class="spec-label">Secret Ingestion Key:</span>
              <code>esp32-greenhouse-***</code>
            </div>
            <div class="spec-row">
              <span class="spec-label">Frekuensi Pengiriman Data:</span>
              <strong>Setiap 5 Detik</strong>
            </div>
            <div class="spec-row">
              <span class="spec-label">Protocol Ingestion:</span>
              <strong>REST JSON + WebSocket Pusher</strong>
            </div>
          </div>
        </div>

        <!-- Sensor Health Matrix -->
        <div class="content-card">
          <div class="card-head-flex">
            <div>
              <h3 class="card-section-heading">Matrix Sensor Terkalibrasi</h3>
              <p class="card-section-sub">Status pembacaan live dan akurasi sensor greenhouse</p>
            </div>
          </div>

          <div class="sensor-matrix-list">
            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <strong>DHT22 Internal (Suhu & RH)</strong>
              </div>
              <span class="sensor-val">
                {{ telemetry.hasData ? `${telemetry.tempInternal.toFixed(1)}°C / ${telemetry.humidityInternal.toFixed(0)}% RH` : 'Siaga / Menunggu Data' }} (Akurasi: ±0.3°C)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <strong>Load Cell 200kg + HX711</strong>
              </div>
              <span class="sensor-val">
                {{ telemetry.hasData ? `${telemetry.weightCurrentKg.toFixed(1)} kg` : 'Siaga' }} (Nol Kalibrasi: OK)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <strong>Pyranometer Radiasi Surya</strong>
              </div>
              <span class="sensor-val">
                {{ telemetry.hasData ? `${telemetry.solarRadiation.toFixed(0)} W/m²` : 'Siaga' }} (Intensitas Radiasi)
              </span>
            </div>

            <div class="sensor-matrix-item">
              <div class="sensor-info">
                <span class="dot-green" :class="{ 'dot-offline': !telemetry.hasData }"></span>
                <strong>Capacitive Moisture Meter</strong>
              </div>
              <span class="sensor-val">
                {{ telemetry.hasData ? `${telemetry.grainMoisture.toFixed(1)}%` : 'Siaga' }} (Target: 12.0%)
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { telemetryService } from '../../services/telemetryService'
import { socketService } from '../../services/socketService'

const telemetry = ref({
  tempInternal: 0,
  tempExternal: 0,
  humidityInternal: 0,
  humidityExternal: 0,
  grainMoisture: 0,
  solarRadiation: 0,
  weightCurrentKg: 0,
  hasData: false,
})

async function fetchLiveTelemetry() {
  try {
    const res = await telemetryService.getCurrent()
    if (res.telemetry && res.telemetry.hasData) {
      telemetry.value = { ...telemetry.value, ...res.telemetry, hasData: true }
    }
  } catch (err) {
    console.warn('Could not load telemetry in AdminDevices:', err.message)
  }
}

onMounted(() => {
  fetchLiveTelemetry()

  socketService.on('telemetry_live', (data) => {
    if (data && data.hasData) {
      telemetry.value = { ...telemetry.value, ...data, hasData: true }
    }
  })
})

onUnmounted(() => {
  socketService.off('telemetry_live')
})
</script>

<style scoped>
.admin-page {
  width: 100%;
  min-height: 100%;
}

.admin-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 32px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: var(--color-text-title);
  line-height: 40px;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
  line-height: 24px;
}

.devices-grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 24px;
}

.content-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card-head-flex {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.icon-box-head {
  display: flex;
  align-items: center;
  gap: 14px;
}

.icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-green-light { background: rgba(13, 99, 27, 0.12); }

.card-section-heading {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text-title);
}

.card-section-sub {
  font-size: 13px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

.spec-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.spec-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: var(--color-bg);
  border-radius: 8px;
  font-size: 13px;
}

.spec-label {
  color: var(--color-text-muted);
}

.sensor-matrix-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sensor-matrix-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: var(--color-bg);
  border-radius: 8px;
  font-size: 13px;
}

.sensor-info {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--color-text-title);
}

.sensor-val {
  font-weight: 600;
  color: #0D631B;
}

.dot-green {
  width: 8px;
  height: 8px;
  background: #0D631B;
  border-radius: 50%;
  display: inline-block;
}

.badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
}

.bg-green-subtle { background: rgba(46, 125, 50, 0.15); }
.text-green-dark { color: #0D631B; }

@media (max-width: 900px) {
  .admin-content { padding: 20px 16px; }
  .devices-grid-2 { grid-template-columns: 1fr; }
}
</style>
