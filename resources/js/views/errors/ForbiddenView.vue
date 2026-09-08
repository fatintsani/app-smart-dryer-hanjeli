<template>
  <div class="error-page-container">
    <div class="ambient-glow purple-glow"></div>

    <div class="error-card">
      <div class="error-badge-wrap">
        <div class="error-code-glow purple-text">403</div>
        <div class="error-icon-box purple-box">
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </div>
      </div>

      <div class="error-content">
        <span class="error-tag purple-tag">Akses Terbatas • 403 Forbidden</span>
        <h1 class="error-title">Akses Tidak Diizinkan</h1>
        <p class="error-desc">
          Anda tidak memiliki hak akses (role permissions) yang sesuai untuk membuka halaman ini. Silakan hubungi Administrator atau masuk dengan akun yang memiliki hak akses lebih tinggi.
        </p>

        <div class="action-buttons">
          <router-link to="/dashboard" class="btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            </svg>
            <span>Kembali ke Dashboard</span>
          </router-link>

          <router-link to="/login" class="btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            <span>Ganti Akun</span>
          </router-link>
        </div>
      </div>

      <div class="error-footer">
        <span>Smart Room Dryer Hanjeli • Desa Wisata Hanjeli & STAS-RG</span>
      </div>
    </div>
  </div>
</template>

<script setup>
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

.purple-glow {
  background: radial-gradient(circle, rgba(109, 40, 217, 0.12) 0%, rgba(109, 40, 217, 0) 70%);
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

.purple-box {
  background: #FAF5FF;
  border: 2px solid #E9D5FF;
  color: #7E22CE;
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

.purple-tag {
  color: #6B21A8;
  background: #F3E8FF;
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
