<template>
  <div class="app-root" :class="{ 'dark-theme': isDark }">
    <!-- Main View Area -->
    <main class="main-viewport-container">
      <!-- 1. Standalone Page Layout (Login, Forgot Password, Error 404/500/403) -->
      <div v-if="isStandalonePage" class="standalone-viewport">
        <router-view v-slot="{ Component }">
          <Transition name="framer-motion-view" mode="out-in">
            <component 
              :is="Component" 
              :is-mobile="!effectiveIsDesktop"
              :current-lang="currentLang"
              @login-success="handleLoginSuccess"
              @forgot-password="router.push('/forgot-password')"
              @back-to-login="router.push('/login')"
            />
          </Transition>
        </router-view>
      </div>

      <!-- 2. Authenticated App Layout (Desktop Shell) -->
      <div v-else-if="effectiveIsDesktop" class="desktop-layout-frame">
        <div class="desktop-main-wrapper">
          <DesktopSidebar 
            :current-tab="currentNavTab" 
            :is-collapsed="isSidebarCollapsed"
            :current-lang="currentLang"
            :user="currentUser"
            @toggle-collapse="toggleSidebarCollapse"
            @navigate="handleNavigate"
            @start-drying="handleNavigate('drying-form')"
          />

          <section class="desktop-content-area" :class="{ 'sidebar-collapsed': isSidebarCollapsed }">
            <!-- Desktop Header: Language, Dark Mode, Notif, Profile Menu -->
            <DesktopHeader 
              :is-dark="isDark"
              :current-lang="currentLang"
              :user="currentUser"
              :is-sidebar-collapsed="isSidebarCollapsed"
              @toggle-sidebar="toggleSidebarCollapse"
              @toggle-theme="toggleTheme"
              @change-lang="changeLang"
              @edit-profile="openEditProfile"
              @settings="handleNavigate('settings')"
              @navigate="handleNavigate"
              @logout="handleLogout"
              @open-simulator="isSimulatorOpen = true"
              @open-ai-copilot="isAiCopilotOpen = true"
            />

            <div class="desktop-subview-scroll">
              <router-view v-slot="{ Component }">
                <Transition name="framer-motion-view" mode="out-in">
                  <component 
                    :is="Component" 
                    :key="route.fullPath"
                    :is-mobile="false"
                    :current-lang="currentLang"
                    :user="currentUser"
                    @start-drying="handleNavigate('drying-form')"
                    @start-process="handleStartProcess"
                    @finish="handleFinishDrying"
                    @select-batch="handleSelectBatch"
                    @save-user="handleSaveProfile"
                    @cancel="handleNavigate('dashboard')"
                    @back="handleNavigate('dashboard')"
                    @open-simulator="isSimulatorOpen = true"
                  />
                </Transition>
              </router-view>
            </div>
          </section>
        </div>
      </div>

      <!-- 3. Authenticated App Layout (Mobile Shell) -->
      <div v-else class="mobile-layout-frame-outer">
        <div class="mobile-phone-container">
          <div class="mobile-app-body">
            <div class="mobile-main-wrapper">
              <!-- Mobile Header with context-aware back button, Theme, Lang & Profile -->
              <MobileHeader 
                :has-back="mobileHasBack"
                :title="mobileHeaderTitle"
                :is-dark="isDark"
                :current-lang="currentLang"
                :user="currentUser"
                @back="handleMobileBack"
                @logout="handleLogout"
                @toggle-theme="toggleTheme"
                @change-lang="changeLang"
                @edit-profile="openEditProfile"
                @settings="handleNavigate('settings')"
                @open-simulator="isSimulatorOpen = true"
                @open-ai-copilot="isAiCopilotOpen = true"
              />

              <!-- Subviews with Transition -->
              <div class="mobile-scroll-content">
                <router-view v-slot="{ Component }">
                  <Transition name="framer-motion-view" mode="out-in">
                    <component 
                      :is="Component" 
                      :key="route.fullPath"
                      :is-mobile="true"
                      :current-lang="currentLang"
                      :user="currentUser"
                      @start-drying="handleNavigate('drying-form')"
                      @start-process="handleStartProcess"
                      @finish="handleFinishDrying"
                      @select-batch="handleSelectBatch"
                      @save-user="handleSaveProfile"
                      @cancel="handleNavigate('dashboard')"
                      @back="handleNavigate('dashboard')"
                    />
                  </Transition>
                </router-view>
              </div>

              <!-- Mobile Bottom Nav -->
              <MobileBottomNav 
                :active-tab="mobileCurrentTab" 
                :current-lang="currentLang"
                :user="currentUser"
                @update:active-tab="handleMobileTabChange"
              />
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Global Interactive Edit Profile Modal -->
    <EditProfileModal 
      :is-open="isEditProfileOpen"
      :current-lang="currentLang"
      :user="currentUser"
      @close="isEditProfileOpen = false"
      @save="handleSaveProfile"
    />

    <!-- Global Hardware IoT Simulator Modal -->
    <HardwareSimulatorModal 
      :is-open="isSimulatorOpen"
      @close="isSimulatorOpen = false"
    />

    <!-- Global Alert Notification Toast Banner -->
    <AlertNotificationToast 
      v-model="isAlertToastOpen"
      :alert-data="currentAlertToast"
      :duration="6000"
      :current-lang="currentLang"
      @action="handleAlertToastAction"
    />

    <!-- Global Confirm & Alert Dialog Modal -->
    <ConfirmModal />

    <!-- PWA Install Prompt Banner for Mobile & Desktop -->
    <PwaInstallPrompt />

    <!-- Global Floating AI Copilot Trigger -->
    <div class="floating-ai-copilot-trigger" v-if="!isStandalonePage">
      <button 
        type="button"
        class="floating-copilot-btn"
        @click="isAiCopilotOpen = true"
        :title="currentLang === 'id' ? 'Tanya Hanjeli AI Copilot' : 'Ask Hanjeli AI Copilot'"
      >
        <div class="copilot-avatar-thumb">
          <img src="/assets/icons/profile/cs.png" alt="AI Profile" class="copilot-thumb-img" />
          <span class="copilot-pulse-badge"></span>
        </div>
        <span class="copilot-btn-label">{{ currentLang === 'id' ? 'Tanya AI' : 'Ask AI' }}</span>
      </button>
    </div>

    <!-- Global AI Copilot Contextual Assistant Drawer -->
    <AiCopilotDrawer 
      :is-open="isAiCopilotOpen" 
      :is-mobile="!effectiveIsDesktop"
      @close="isAiCopilotOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DesktopSidebar from './components/DesktopSidebar.vue'
import DesktopHeader from './components/DesktopHeader.vue'
import MobileHeader from './components/MobileHeader.vue'
import MobileBottomNav from './components/MobileBottomNav.vue'
import EditProfileModal from './components/EditProfileModal.vue'
import HardwareSimulatorModal from './components/HardwareSimulatorModal.vue'
import AlertNotificationToast from './components/AlertNotificationToast.vue'
import ConfirmModal from './components/ConfirmModal.vue'
import PwaInstallPrompt from './components/PwaInstallPrompt.vue'
import AiCopilotDrawer from './components/AiCopilotDrawer.vue'

import { authService } from './services/authService'
import { socketService } from './services/socketService'
import { settingsService } from './services/settingsService'
import { isDark, toggleTheme } from './services/themeService'
import { currentLang, setLanguage } from './i18n'
import { initPwa } from './services/pwaService'

const route = useRoute()
const router = useRouter()

const windowWidth = ref(window.innerWidth)
const isAiCopilotOpen = ref(false)

// Global Alert Notification Toast State
const isAlertToastOpen = ref(false)
const currentAlertToast = ref({
  type: 'critical',
  title: 'Peringatan Suhu Kritis (>55°C)',
  message: 'Sensor DHT22 mendeteksi suhu melebihi batas keselamatan (55.8°C). Sistem pengering siaga.',
  time: 'Baru saja',
  actionLabel: 'Cek Monitoring',
  actionRoute: '/monitoring'
})

function triggerGlobalAlert(alert) {
  currentAlertToast.value = alert
  isAlertToastOpen.value = true
}

function handleAlertToastAction(alert) {
  if (alert.actionRoute) {
    router.push(alert.actionRoute)
  }
}

// Sidebar Preferences
const isSidebarCollapsed = ref(localStorage.getItem('sidebar_collapsed') === 'true')

function toggleSidebarCollapse() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  localStorage.setItem('sidebar_collapsed', isSidebarCollapsed.value ? 'true' : 'false')
}

// User Profile Data
const currentUser = ref(authService.getCurrentUser() || {
  name: 'Operator Green House',
  email: 'stas-rg@telkomuniversity.ac.id',
  phone: '0857-2218-2480',
  role: 'OPERATOR',
  location: 'Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)'
})

const isEditProfileOpen = ref(false)
const isSimulatorOpen = ref(false)

function onResize() {
  windowWidth.value = window.innerWidth
}

onMounted(() => {
  window.addEventListener('resize', onResize)
  
  // Initialize PWA and Service Worker
  initPwa()

  // Hydrate global system settings (active state, iot mode, environment)
  settingsService.getSettings()

  // Initialize WebSocket / real-time telemetry stream
  socketService.connect()
  socketService.on('alert_new', (alert) => {
    triggerGlobalAlert({
      type: alert.type === 'DANGER' ? 'critical' : alert.type === 'WARNING' ? 'warning' : 'info',
      title: alert.title,
      message: alert.message,
      time: 'Baru saja',
      actionLabel: 'Lihat',
      actionRoute: '/monitoring'
    })
  })
})

onUnmounted(() => {
  window.removeEventListener('resize', onResize)
  socketService.stop()
})

watch(() => route.path, () => {
  window.scrollTo(0, 0)
  const scrollEl = document.querySelector('.desktop-subview-scroll')
  if (scrollEl) scrollEl.scrollTop = 0
})

const effectiveIsDesktop = computed(() => {
  return windowWidth.value > 900
})

// Check if current route is standalone (no sidebar / top header wrapper)
const isStandalonePage = computed(() => {
  if (!route.name) return false
  const standaloneNames = ['Landing', 'Login', 'ForgotPassword', 'NotFound', 'ServerError', 'Forbidden']
  return standaloneNames.includes(route.name) || route.meta?.guestOnly || route.meta?.standalone
})

// Current Tab highlighted in desktop sidebar
const currentNavTab = computed(() => {
  const path = route.path
  if (path === '/admin' || path === '/admin/overview' || path === '/admin/dashboard') return 'admin-overview'
  if (path.startsWith('/admin/users')) return 'admin-users'
  if (path.startsWith('/admin/devices')) return 'admin-devices'
  if (path.startsWith('/admin/simulator') || path.startsWith('/admin/testbed')) return 'admin-simulator'
  if (path.startsWith('/admin/logs')) return 'admin-logs'
  if (path.startsWith('/admin/parameters')) return 'admin-parameters'
  if (path.startsWith('/monitoring')) return 'monitoring'
  if (path.startsWith('/drying')) return 'drying'
  if (path.startsWith('/history')) return 'history'
  if (path.startsWith('/guide')) return 'guide'
  if (path.startsWith('/settings')) return 'settings'
  return currentUser.value?.role === 'ADMIN' ? 'admin-overview' : 'dashboard'
})

// Mobile Header back button condition
const mobileHasBack = computed(() => {
  return ['DryingForm', 'ActiveDrying', 'HistoryDetail', 'Settings', 'Guide'].includes(route.name) || route.path.startsWith('/history/')
})

// Mobile Header Title
const mobileHeaderTitle = computed(() => {
  if (route.name === 'AdminOverview' || route.path === '/admin') return currentLang.value === 'id' ? 'Ringkasan Sistem' : 'System Overview'
  if (route.name === 'AdminUsers' || route.path === '/admin/users') return currentLang.value === 'id' ? 'Manajemen Pengguna' : 'User Management'
  if (route.name === 'AdminDevices' || route.path === '/admin/devices') return currentLang.value === 'id' ? 'Perangkat & Sensor IoT' : 'IoT Devices'
  if (route.name === 'AdminSimulator' || route.path === '/admin/simulator') return currentLang.value === 'id' ? 'Simulator & Uji IoT' : 'IoT Simulator & Testbed'
  if (route.name === 'AdminLogs' || route.path === '/admin/logs') return currentLang.value === 'id' ? 'Log & Audit Trail' : 'Audit Logs'
  if (route.name === 'AdminParameters' || route.path === '/admin/parameters') return currentLang.value === 'id' ? 'Parameter Master' : 'Master Parameters'
  if (route.name === 'HistoryDetail') return 'Detail Riwayat Batch'
  if (route.name === 'DryingForm') return 'Mulai Pengeringan'
  if (route.name === 'ActiveDrying') return 'Pengeringan Aktif'
  if (route.name === 'Settings') return currentLang.value === 'id' ? 'Pengaturan Akun & Alat' : 'Account & Device Settings'
  if (route.name === 'Guide') return 'Panduan Alat'
  return ''
})

const mobileCurrentTab = computed(() => {
  const path = route.path
  if (path === '/admin' || path === '/admin/overview' || path === '/admin/dashboard') return 'admin-overview'
  if (path.startsWith('/admin/users')) return 'admin-users'
  if (path.startsWith('/admin/devices')) return 'admin-devices'
  if (path.startsWith('/admin/simulator') || path.startsWith('/admin/testbed')) return 'admin-simulator'
  if (path.startsWith('/admin/logs')) return 'admin-logs'
  if (path.startsWith('/admin/parameters')) return 'admin-parameters'
  if (path.startsWith('/monitoring')) return 'monitoring'
  if (path.startsWith('/drying')) return 'drying'
  if (path.startsWith('/history')) return 'history'
  if (path.startsWith('/guide')) return 'guide'
  if (path.startsWith('/settings')) return 'settings'
  return currentUser.value?.role === 'ADMIN' ? 'admin-overview' : 'dashboard'
})



function changeLang(lang) {
  setLanguage(lang)
}

function openEditProfile() {
  isEditProfileOpen.value = true
}

async function handleSaveProfile(updatedUser) {
  try {
    const res = await authService.updateProfile(updatedUser)
    currentUser.value = { ...currentUser.value, ...(res.user || updatedUser) }
  } catch (err) {
    console.error('Update profile error:', err)
    currentUser.value = { ...currentUser.value, ...updatedUser }
  }
}

function handleNavigate(view) {
  if (view === 'admin' || view === 'admin-dashboard' || view === 'admin-overview') router.push('/admin')
  else if (view === 'admin-users') router.push('/admin/users')
  else if (view === 'admin-devices') router.push('/admin/devices')
  else if (view === 'admin-simulator') router.push('/admin/simulator')
  else if (view === 'admin-logs') router.push('/admin/logs')
  else if (view === 'admin-parameters' || view === 'admin-settings') router.push('/admin/parameters')
  else if (view === 'dashboard') {
    if (currentUser.value?.role === 'ADMIN') router.push('/admin')
    else router.push('/dashboard')
  }
  else if (view === 'monitoring') router.push('/monitoring')
  else if (view === 'drying' || view === 'drying-form') router.push('/drying/new')
  else if (view === 'active-drying') router.push('/drying/active')
  else if (view === 'history') router.push('/history')
  else if (view === 'guide') router.push('/guide')
  else if (view === 'settings') router.push('/settings')
  else if (view === 'login') router.push('/login')
  else if (view === 'forgot-password') router.push('/forgot-password')
}

function handleMobileTabChange(tab) {
  handleNavigate(tab)
}

function handleLoginSuccess(user) {
  if (user) {
    currentUser.value = { ...currentUser.value, ...user }
  }
  if (user?.role === 'ADMIN') {
    router.push('/admin')
  } else {
    const redirect = (route.query.redirect && !route.query.redirect.includes('admin')) ? route.query.redirect : '/dashboard'
    router.push(redirect)
  }
}

async function handleLogout() {
  try {
    await authService.logout()
  } finally {
    currentUser.value = null
    window.location.href = '/login'
  }
}

function handleStartProcess() {
  router.push('/drying/active')
}

function handleFinishDrying() {
  router.push('/history')
}

function handleSelectBatch(batchId) {
  router.push(`/history/${batchId}`)
}

function handleMobileBack() {
  if (route.name === 'HistoryDetail') {
    router.push('/history')
  } else {
    if (window.history.length > 2) {
      router.back()
    } else {
      router.push('/dashboard')
    }
  }
}
</script>

<style>
/* CSS Variables & Global Tokens */
:root {
  --color-primary: #0D631B;
  --color-primary-hover: #15803D;
  --color-primary-light: #E8F5E9;
  --color-accent: #0284C7;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;
  --color-success: #10B981;
  --color-bg: #F4F7F5;
  --color-card-bg: #FFFFFF;
  --color-text-main: #071E27;
  --color-text-muted: #707A6C;
  --color-border: #CBD5E1;
  --color-header-bg: #FFFFFF;
  --shadow-sm: none;
  --shadow-md: none;
  --shadow-lg: none;
}

.dark-theme {
  --color-bg: #071E27;
  --color-card-bg: #0B242F;
  --color-text-main: #F8FAFC;
  --color-text-muted: #94A3B8;
  --color-border: #1E4E61;
  --color-header-bg: #0B242F;
}

html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
  background-color: var(--color-bg);
  color: var(--color-text-main);
  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  -webkit-font-smoothing: antialiased;
}

.app-root {
  height: 100vh;
  height: 100dvh;
  width: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.main-viewport-container {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.standalone-viewport {
  width: 100%;
  height: 100%;
  overflow-y: auto;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

/* Desktop Frame */
.desktop-layout-frame {
  display: flex;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.desktop-main-wrapper {
  display: flex;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.desktop-content-area {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  background: var(--color-bg);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.desktop-subview-scroll {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0;
  -webkit-overflow-scrolling: touch;
}

/* Mobile Frame */
.mobile-layout-frame-outer {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  background: #EBF3F9;
  overflow: hidden;
}

.dark-theme .mobile-layout-frame-outer {
  background: #041016;
}

.mobile-phone-container {
  width: 100%;
  max-width: 480px;
  height: 100%;
  background: var(--color-bg-light, #F4F7F5);
  border-left: 1px solid var(--color-border);
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}

.dark-theme .mobile-phone-container {
  background: #071E27;
  border-color: #1E4E61;
}

.mobile-app-body {
  flex: 1;
  min-height: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.mobile-main-wrapper {
  flex: 1;
  min-height: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

.mobile-scroll-content {
  flex: 1;
  min-height: 0;
  height: 100%;
  overflow-y: auto;
  overflow-x: hidden;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior-y: contain;
  padding: 16px 16px 84px 16px;
  box-sizing: border-box;
}

@media (max-width: 768px) {
  .mobile-layout-frame-outer {
    background: var(--color-bg-light, #F4F7F5);
  }

  .dark-theme .mobile-layout-frame-outer {
    background: #071E27;
  }

  .mobile-phone-container {
    max-width: 100%;
    border-left: none;
    border-right: none;
  }
}

/* Framer Motion Style Smooth Transitions */
.framer-motion-view-enter-active,
.framer-motion-view-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.framer-motion-view-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.framer-motion-view-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Floating AI Copilot Trigger Button */
.floating-ai-copilot-trigger {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 900;
}

@media (max-width: 768px) {
  .floating-ai-copilot-trigger {
    bottom: 80px; /* Above bottom navigation */
    right: 16px;
  }
}

.floating-copilot-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 6px 16px 6px 8px;
  background: var(--color-primary-dark, #0D631B);
  color: #FFFFFF;
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 9999px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.02em;
  cursor: pointer;
  transition: all 0.25s ease;
}

.floating-copilot-btn:hover {
  background: #084011;
  transform: translateY(-2px);
}

.copilot-avatar-thumb {
  position: relative;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  flex-shrink: 0;
}

.copilot-thumb-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 1.5px solid #FFFFFF;
  display: block;
}

.copilot-pulse-badge {
  position: absolute;
  bottom: -1px;
  right: -1px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #34d399;
  border: 1.5px solid #0D631B;
  box-shadow: 0 0 6px #34d399;
  animation: pulseDot 2s infinite;
}
</style>
