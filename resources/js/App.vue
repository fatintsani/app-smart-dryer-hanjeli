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

import { authService } from './services/authService'
import { socketService } from './services/socketService'
import { settingsService } from './services/settingsService'
import { isDark, toggleTheme } from './services/themeService'
import { currentLang, setLanguage } from './i18n'

const route = useRoute()
const router = useRouter()

const windowWidth = ref(window.innerWidth)

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
  email: 'operator@hanjeli.com',
  phone: '+62 813-8899-2211',
  role: 'OPERATOR',
  location: 'Desa Wisata Waluran, Sukabumi'
})

const isEditProfileOpen = ref(false)
const isSimulatorOpen = ref(false)

function onResize() {
  windowWidth.value = window.innerWidth
}

onMounted(() => {
  window.addEventListener('resize', onResize)
  
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
  --color-bg: #0B1911;
  --color-card-bg: #13271C;
  --color-text-main: #F1F5F9;
  --color-text-muted: #94A3B8;
  --color-border: #1E3A2B;
  --color-header-bg: #13271C;
}

html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
  background-color: var(--color-bg);
  color: var(--color-text-main);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  -webkit-font-smoothing: antialiased;
}

.app-root {
  height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.main-viewport-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.standalone-viewport {
  width: 100%;
  height: 100vh;
  overflow-y: auto;
}

/* Desktop Frame */
.desktop-layout-frame {
  display: flex;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.desktop-main-wrapper {
  display: flex;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.desktop-content-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  height: 100vh;
  overflow: hidden;
  background: var(--color-bg);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.desktop-subview-scroll {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0;
}

/* Mobile Frame */
.mobile-layout-frame-outer {
  width: 100%;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  background: #EBF3F9;
}

.mobile-phone-container {
  width: 100%;
  max-width: 480px;
  min-height: 100vh;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
}

.mobile-app-body {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.mobile-main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  position: relative;
  padding-bottom: 64px;
}

.mobile-scroll-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
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
</style>
