<template>
  <div class="error-page-container">
    <div class="ambient-glow amber-glow"></div>

    <div class="error-card">
      <div class="error-badge-wrap">
        <div class="error-code-glow amber-text">500</div>
        <div class="error-icon-box amber-box">
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
        </div>
      </div>

      <div class="error-content">
        <span class="error-tag amber-tag">{{ $t('errors.serverTag') }}</span>
        <h1 class="error-title">{{ $t('errors.serverTitle') }}</h1>
        <p class="error-desc">
          {{ $t('errors.serverDesc') }}
        </p>

        <!-- Diagnostic tips -->
        <div class="diagnostic-box">
          <div class="diag-header">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <strong>{{ $t('common.info') }}:</strong>
          </div>
          <ul class="diag-list">
            <li>Laravel API backend: <code>http://localhost:8000</code></li>
            <li>MySQL Database: <code>port 3306</code></li>
          </ul>
        </div>

        <div class="action-buttons">
          <button type="button" @click="reloadPage" class="btn-primary amber-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
              <path d="M21 3v5h-5"></path>
              <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
              <path d="M8 16H3v5"></path>
            </svg>
            <span>{{ $t('errors.reload') }}</span>
          </button>

          <router-link to="/dashboard" class="btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            </svg>
            <span>{{ $t('errors.backToDashboard') }}</span>
          </router-link>
        </div>
      </div>

      <div class="error-footer">
        <span>{{ $t('common.smartRoomDryer') }} • {{ $t('common.facilityName') }} & {{ $t('common.devCredit') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
function reloadPage() {
  window.location.reload()
}
</script>

<style scoped>
.error-page-container {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F8FAFC;
  padding: 24px 16px;
  position: relative;
  overflow: hidden;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.ambient-glow {
  position: absolute;
  width: 500px;
  height: 500px;
  border-radius: 50%;
  pointer-events: none;
}

.amber-glow {
  background: radial-gradient(circle, rgba(217, 119, 6, 0.12) 0%, rgba(217, 119, 6, 0) 70%);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.error-card {
  width: 100%;
  max-width: 540px;
  background: #FFFFFF;
  border-radius: 24px;
  border: 1px solid #E2E8F0;
    padding: 40px 32px;
  text-align: center;
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
}

.error-badge-wrap {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 8px;
}

.error-code-glow {
  font-size: 88px;
  font-weight: 900;
  color: #F1F5F9;
  letter-spacing: -2px;
  line-height: 1;
  user-select: none;
}

.error-icon-box {
  position: absolute;
  width: 72px;
  height: 72px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.amber-box {
  background: #FFFBEB;
  border: 2px solid #FDE68A;
  color: #D97706;
  }

.error-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  width: 100%;
}

.error-tag {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  padding: 4px 12px;
  border-radius: 999px;
}

.amber-tag {
  color: #B45309;
  background: #FEF3C7;
}

.error-title {
  font-size: 24px;
  font-weight: 800;
  color: #0F172A;
  letter-spacing: -0.5px;
  margin: 0;
}

.error-desc {
  font-size: 14px;
  color: #64748B;
  line-height: 1.6;
  max-width: 440px;
  margin: 0;
}

.diagnostic-box {
  width: 100%;
  margin-top: 6px;
  padding: 12px 16px;
  background: #F8FAFC;
  border-radius: 12px;
  border: 1px solid #E2E8F0;
  text-align: left;
  font-size: 12px;
  color: #475569;
}

.diag-header {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #0F172A;
  margin-bottom: 6px;
}

.diag-list {
  margin: 0;
  padding-left: 18px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.diag-list code {
  background: #E2E8F0;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: monospace;
  color: #0F172A;
}

.action-buttons {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  width: 100%;
  margin-top: 10px;
}

.btn-primary {
  flex: 1;
  padding: 10px 18px;
  color: #FFFFFF;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  border: none;
  transition: opacity 0.2s;
}

.amber-btn {
  background: #D97706;
}

.amber-btn:hover {
  background: #B45309;
}

.btn-secondary {
  flex: 1;
  padding: 10px 18px;
  background: #F1F5F9;
  border: 1px solid #E2E8F0;
  color: #334155;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #E2E8F0;
  color: #0F172A;
}

.error-footer {
  font-size: 11px;
  color: #94A3B8;
  border-top: 1px solid #F1F5F9;
  padding-top: 16px;
  width: 100%;
}
</style>
