<script setup>
import { ref, computed } from 'vue';
import { pwaState, promptInstall, dismissInstallPrompt } from '../services/pwaService';

const isInstalling = ref(false);
const showIosModal = ref(false);

const shouldShowBanner = computed(() => {
  if (pwaState.isInstalled || pwaState.isDismissed) return false;
  return pwaState.isInstallable || pwaState.isIOS;
});

const handleInstallClick = async () => {
  if (pwaState.isIOS) {
    showIosModal.value = true;
    return;
  }
  isInstalling.value = true;
  try {
    await promptInstall();
  } finally {
    isInstalling.value = false;
  }
};
</script>

<template>
  <div class="pwa-install-wrapper">
    <!-- Floating PWA Install Banner -->
    <transition name="pwa-slide-up">
      <aside
        v-if="shouldShowBanner"
        class="pwa-banner-card"
        role="region"
        aria-label="Pemasangan Aplikasi PWA"
      >
        <div class="pwa-card-body">
          <div class="pwa-icon-box">
            <img src="/pwa-192x192.png" alt="Smart Dryer App" class="pwa-app-logo" />
          </div>

          <div class="pwa-content">
            <div class="pwa-header-row">
              <div class="pwa-title-wrap">
                <span class="pwa-title">Install Smart Dryer</span>
                <span class="pwa-badge">PWA</span>
              </div>
              <button
                type="button"
                @click="dismissInstallPrompt"
                class="pwa-btn-close"
                title="Tutup banner"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>
            
            <p class="pwa-desc">
              Pasang langsung di HP Anda seperti aplikasi native tanpa perlu download dari PlayStore.
            </p>

            <div class="pwa-actions-row">
              <button
                type="button"
                @click="handleInstallClick"
                :disabled="isInstalling"
                class="pwa-btn-install"
              >
                <svg v-if="!isInstalling" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="7 10 12 15 17 10"></polyline>
                  <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span v-if="isInstalling">Memasang...</span>
                <span v-else>{{ pwaState.isIOS ? 'Cara Install di iOS' : 'Install Aplikasi' }}</span>
              </button>
              
              <button
                type="button"
                @click="dismissInstallPrompt"
                class="pwa-btn-dismiss"
              >
                Nanti
              </button>
            </div>
          </div>
        </div>
      </aside>
    </transition>

    <!-- iOS Install Instructions Modal (Teleport to body so it never breaks layout) -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div
          v-if="showIosModal"
          class="ios-modal-overlay"
          @click.self="showIosModal = false"
        >
          <div class="ios-modal-card">
            <div class="ios-modal-header">
              <div class="ios-logo-box">
                <img src="/pwa-192x192.png" alt="Smart Dryer" class="ios-app-logo" />
              </div>
              <h3 class="ios-modal-title">Install di iPhone / iPad</h3>
              <p class="ios-modal-subtitle">
                Ikuti 2 langkah mudah di browser Safari berikut:
              </p>
            </div>

            <div class="ios-steps-container">
              <div class="ios-step-item">
                <span class="ios-step-number">1</span>
                <div class="ios-step-text">
                  <strong class="ios-step-heading">Ketuk ikon 'Bagikan' (Share)</strong>
                  <p class="ios-step-desc">Ikon kotak dengan panah ke atas di bilah navigasi bawah Safari.</p>
                </div>
              </div>
              <div class="ios-step-item">
                <span class="ios-step-number">2</span>
                <div class="ios-step-text">
                  <strong class="ios-step-heading">Pilih 'Tambah ke Layar Utama'</strong>
                  <p class="ios-step-desc">(Add to Home Screen) untuk memasang aplikasi di layar perangkat Anda.</p>
                </div>
              </div>
            </div>

            <button
              type="button"
              @click="showIosModal = false"
              class="ios-btn-confirm"
            >
              Mengerti, Saya Akan Coba
            </button>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<style scoped>
/* PWA Banner Card (Floating) */
.pwa-banner-card {
  position: fixed;
  bottom: 84px;
  left: 14px;
  right: 14px;
  z-index: 1000;
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(16, 185, 129, 0.35);
  border-radius: 16px;
  padding: 14px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.05);
  backdrop-filter: blur(12px);
  box-sizing: border-box;
}

:global(.dark-theme) .pwa-banner-card {
  background: #0B242F;
  border-color: #1E4E61;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
}

@media (min-width: 769px) {
  .pwa-banner-card {
    left: auto;
    right: 24px;
    bottom: 24px;
    width: 380px;
    max-width: calc(100vw - 48px);
  }
}

.pwa-card-body {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.pwa-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #0D631B, #16A34A);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  padding: 4px;
  box-sizing: border-box;
  box-shadow: 0 4px 10px rgba(13, 99, 27, 0.25);
}

.pwa-app-logo {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 8px;
}

.pwa-content {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.pwa-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.pwa-title-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
}

.pwa-title {
  font-size: 13px;
  font-weight: 800;
  color: var(--color-text-title, #071E27);
  letter-spacing: -0.01em;
}

:global(.dark-theme) .pwa-title {
  color: #FFFFFF;
}

.pwa-badge {
  font-size: 9.5px;
  font-weight: 800;
  background: #DCFCE7;
  color: #166534;
  padding: 1px 6px;
  border-radius: 6px;
  border: 1px solid rgba(22, 101, 52, 0.2);
}

:global(.dark-theme) .pwa-badge {
  background: rgba(34, 197, 94, 0.2);
  color: #86EFAC;
  border-color: rgba(134, 239, 172, 0.25);
}

.pwa-btn-close {
  background: transparent;
  border: none;
  color: #94A3B8;
  cursor: pointer;
  padding: 3px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.pwa-btn-close:hover {
  color: #475569;
  background: rgba(0, 0, 0, 0.05);
}

:global(.dark-theme) .pwa-btn-close:hover {
  color: #FFFFFF;
  background: rgba(255, 255, 255, 0.1);
}

.pwa-desc {
  font-size: 11.5px;
  color: var(--color-text-muted, #64748B);
  line-height: 1.35;
  margin: 0;
}

:global(.dark-theme) .pwa-desc {
  color: #94A3B8;
}

.pwa-actions-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.pwa-btn-install {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: linear-gradient(135deg, #0D631B, #16A34A);
  color: #FFFFFF;
  border: none;
  border-radius: 9px;
  padding: 7px 12px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(13, 99, 27, 0.25);
  transition: all 0.2s ease;
}

.pwa-btn-install:hover {
  background: linear-gradient(135deg, #094713, #15803D);
  box-shadow: 0 4px 10px rgba(13, 99, 27, 0.35);
}

.pwa-btn-dismiss {
  background: transparent;
  border: 1px solid #CBD5E1;
  color: #64748B;
  border-radius: 9px;
  padding: 7px 12px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pwa-btn-dismiss:hover {
  background: rgba(0, 0, 0, 0.03);
  color: #334155;
}

:global(.dark-theme) .pwa-btn-dismiss {
  border-color: #1E4E61;
  color: #94A3B8;
}

:global(.dark-theme) .pwa-btn-dismiss:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #FFFFFF;
}

/* Transitions */
.pwa-slide-up-enter-active,
.pwa-slide-up-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.pwa-slide-up-enter-from,
.pwa-slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

/* iOS Instructions Modal */
.ios-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(6px);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  box-sizing: border-box;
}

.ios-modal-card {
  width: 100%;
  max-width: 360px;
  background: #FFFFFF;
  border-radius: 20px;
  padding: 22px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-sizing: border-box;
}

:global(.dark-theme) .ios-modal-card {
  background: #0B242F;
  border: 1px solid #1E4E61;
}

.ios-modal-header {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.ios-logo-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, #0D631B, #16A34A);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px;
  box-sizing: border-box;
  box-shadow: 0 6px 14px rgba(13, 99, 27, 0.3);
  margin-bottom: 4px;
}

.ios-app-logo {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 10px;
}

.ios-modal-title {
  font-size: 16px;
  font-weight: 800;
  color: #071E27;
  margin: 0;
}

:global(.dark-theme) .ios-modal-title {
  color: #FFFFFF;
}

.ios-modal-subtitle {
  font-size: 12px;
  color: #64748B;
  margin: 0;
}

:global(.dark-theme) .ios-modal-subtitle {
  color: #94A3B8;
}

.ios-steps-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  padding: 14px;
}

:global(.dark-theme) .ios-steps-container {
  background: #081B23;
  border-color: #1E4E61;
}

.ios-step-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.ios-step-number {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #10B981;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}

.ios-step-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.ios-step-heading {
  font-size: 12px;
  font-weight: 700;
  color: #1E293B;
}

:global(.dark-theme) .ios-step-heading {
  color: #E2E8F0;
}

.ios-step-desc {
  font-size: 11px;
  color: #64748B;
  margin: 0;
  line-height: 1.35;
}

:global(.dark-theme) .ios-step-desc {
  color: #94A3B8;
}

.ios-btn-confirm {
  width: 100%;
  background: linear-gradient(135deg, #0D631B, #16A34A);
  color: #FFFFFF;
  border: none;
  border-radius: 12px;
  padding: 10px 16px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.ios-btn-confirm:hover {
  background: linear-gradient(135deg, #094713, #15803D);
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
