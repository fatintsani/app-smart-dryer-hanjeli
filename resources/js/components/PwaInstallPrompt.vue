<template>
  <Transition name="pwa-slide">
    <div v-if="showInstallBanner" class="pwa-install-banner">
      <div class="pwa-banner-content">
        <div class="pwa-app-icon">
          <img src="/assets/img/hanjeli.png" alt="Smart Dryer Logo" class="pwa-img" />
        </div>
        <div class="pwa-text-info">
          <h4 class="pwa-title">Pasang Aplikasi Smart Dryer</h4>
          <p class="pwa-desc">Install di layar utama HP untuk akses cepat & hemat kuota.</p>
        </div>
      </div>
      <div class="pwa-actions">
        <button class="btn-pwa-dismiss" @click="dismissBanner">Nanti</button>
        <button class="btn-pwa-install" @click="installApp">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Install</span>
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const deferredPrompt = ref(null)
const showInstallBanner = ref(false)

function onBeforeInstallPrompt(e) {
  // Prevent mini-infobar from appearing on mobile
  e.preventDefault()
  deferredPrompt.value = e
  // Check if dismissed in this session
  if (!sessionStorage.getItem('pwa_dismissed')) {
    showInstallBanner.value = true
  }
}

function onAppInstalled() {
  showInstallBanner.value = false
  deferredPrompt.value = null
}

onMounted(() => {
  window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt)
  window.addEventListener('appinstalled', onAppInstalled)
})

onUnmounted(() => {
  window.removeEventListener('beforeinstallprompt', onBeforeInstallPrompt)
  window.removeEventListener('appinstalled', onAppInstalled)
})

async function installApp() {
  if (!deferredPrompt.value) return
  deferredPrompt.value.prompt()
  const { outcome } = await deferredPrompt.value.userChoice
  if (outcome === 'accepted') {
    showInstallBanner.value = false
  }
  deferredPrompt.value = null
}

function dismissBanner() {
  showInstallBanner.value = false
  sessionStorage.setItem('pwa_dismissed', 'true')
}
</script>

<style scoped>
.pwa-install-banner {
  position: fixed;
  bottom: 84px; /* Above mobile bottom nav */
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 32px);
  max-width: 440px;
  background: #071E27;
  color: #FFFFFF;
  border-radius: 14px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
    border: 1px solid rgba(255, 255, 255, 0.12);
  z-index: 999;
}

@media (min-width: 901px) {
  .pwa-install-banner {
    bottom: 24px;
    right: 24px;
    left: auto;
    transform: none;
  }
}

.pwa-banner-content {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.pwa-app-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #FFFFFF;
  padding: 3px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.pwa-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.pwa-text-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.pwa-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #FFFFFF;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pwa-desc {
  font-size: 11px;
  color: #94A3B8;
  margin: 0;
  line-height: 14px;
}

.pwa-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-pwa-dismiss {
  background: transparent;
  border: none;
  color: #94A3B8;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  padding: 6px 8px;
}

.btn-pwa-dismiss:hover {
  color: #FFFFFF;
}

.btn-pwa-install {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  padding: 8px 14px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-pwa-install:hover {
  background: #1B5E20;
}

.pwa-slide-enter-active,
.pwa-slide-leave-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.pwa-slide-enter-from,
.pwa-slide-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}

@media (min-width: 901px) {
  .pwa-slide-enter-from,
  .pwa-slide-leave-to {
    opacity: 0;
    transform: translateY(20px);
  }
}
</style>
