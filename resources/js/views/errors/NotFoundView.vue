<template>
  <div class="error-page-container">
    <!-- Ambient Green House Glow -->
    <div class="ambient-glow"></div>

    <div class="error-card">
      <!-- Error Illustration / Badge -->
      <div class="error-badge-wrap">
        <div class="error-code-glow">404</div>
        <div class="error-icon-box">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="leaf-icon">
            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
          </svg>
          <div class="error-question-mark">?</div>
        </div>
      </div>

      <!-- Text Content -->
      <div class="error-content">
        <span class="error-tag">{{ $t('errors.notFoundTag') }}</span>
        <h1 class="error-title">{{ $t('errors.notFoundTitle') }}</h1>
        <p class="error-desc">
          {{ $t('errors.notFoundDesc') }}
        </p>

        <!-- Quick Suggestions -->
        <div class="suggested-routes">
          <span class="suggest-label">{{ $t('errors.suggestedRoutes') }}</span>
          <div class="suggest-chips">
            <router-link to="/dashboard" class="suggest-chip">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                <rect width="7" height="5" x="3" y="16" rx="1"></rect>
              </svg>
              <span>{{ $t('nav.dashboard') }}</span>
            </router-link>

            <router-link to="/monitoring" class="suggest-chip">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
              <span>{{ $t('nav.monitoring') }}</span>
            </router-link>

            <router-link to="/history" class="suggest-chip">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v5h5"></path>
                <path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"></path>
                <path d="M12 7v5l4 2"></path>
              </svg>
              <span>{{ $t('nav.history') }}</span>
            </router-link>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <router-link to="/dashboard" class="btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>{{ $t('errors.backToDashboard') }}</span>
          </router-link>

          <button type="button" @click="goBack" class="btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5"></path>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>{{ $t('common.back') }}</span>
          </button>
        </div>
      </div>

      <!-- Footer Brand Info -->
      <div class="error-footer">
        <span>{{ $t('common.smartRoomDryer') }} • {{ $t('common.facilityName') }} & {{ $t('common.devCredit') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goBack() {
  if (window.history.length > 2) {
    router.back()
  } else {
    router.push('/dashboard')
  }
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
  background: radial-gradient(circle, rgba(13, 99, 27, 0.12) 0%, rgba(13, 99, 27, 0) 70%);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  pointer-events: none;
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
  background: #F0FDF4;
  border: 2px solid #BBF7D0;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0D631B;
  }

.error-question-mark {
  position: absolute;
  top: -6px;
  right: -6px;
  width: 24px;
  height: 24px;
  background: #DC2626;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 800;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
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
  color: #0D631B;
  background: #DCFCE7;
  padding: 4px 12px;
  border-radius: 999px;
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

.suggested-routes {
  width: 100%;
  margin-top: 8px;
  padding: 14px 16px;
  background: #F8FAFC;
  border-radius: 12px;
  border: 1px solid #E2E8F0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  text-align: left;
}

.suggest-label {
  font-size: 11.5px;
  font-weight: 600;
  color: #64748B;
}

.suggest-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.suggest-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  text-decoration: none;
  transition: all 0.2s;
}

.suggest-chip:hover {
  border-color: #0D631B;
  color: #0D631B;
  background: #F0FDF4;
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
  background: #0D631B;
  color: #FFFFFF;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-decoration: none;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #15803D;
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
  cursor: pointer;
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
