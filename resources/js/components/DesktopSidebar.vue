<template>
  <aside class="desktop-sidebar" :class="{ 'collapsed': isCollapsed, 'admin-theme': isAdmin }">
    <!-- Brand Header (Centered & Clean) -->
    <div class="sidebar-header">
      <div class="logo-box">
        <HanjeliLogo :size="isCollapsed ? 38 : 64" />
      </div>

      <div v-if="!isCollapsed" class="brand-text-block">
        <h1 class="brand-title">{{ $t('common.smartRoomDryer') }}</h1>
        <p class="brand-subtitle">{{ $t('common.facilityName') }}</p>
      </div>
    </div>

    <!-- Navigation Menu (Role Aware) -->
    <nav class="sidebar-nav">
      <!-- === ADMIN NAVIGATION === -->
      <template v-if="isAdmin">
        <!-- 1. Ringkasan Sistem -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'admin' || currentTab === 'admin-overview' }"
          @click="$emit('navigate', 'admin-overview')"
          :title="isCollapsed ? $t('nav.adminOverview') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.adminOverview') }}</span>
        </button>

        <!-- 2. Manajemen Pengguna -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'admin-users' }"
          @click="$emit('navigate', 'admin-users')"
          :title="isCollapsed ? $t('nav.adminUsers') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.adminUsers') }}</span>
        </button>

        <!-- 3. Perangkat & Sensor IoT -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'admin-devices' }"
          @click="$emit('navigate', 'admin-devices')"
          :title="isCollapsed ? $t('nav.adminDevices') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
            <rect x="9" y="9" width="6" height="6"></rect>
            <line x1="9" y1="1" x2="9" y2="4"></line>
            <line x1="15" y1="1" x2="15" y2="4"></line>
            <line x1="9" y1="20" x2="9" y2="23"></line>
            <line x1="15" y1="20" x2="15" y2="23"></line>
            <line x1="20" y1="9" x2="23" y2="9"></line>
            <line x1="20" y1="14" x2="23" y2="14"></line>
            <line x1="1" y1="9" x2="4" y2="9"></line>
            <line x1="1" y1="14" x2="4" y2="14"></line>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.adminDevices') }}</span>
        </button>

        <!-- 4. Simulator & Uji IoT -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'admin-simulator' }"
          @click="$emit('navigate', 'admin-simulator')"
          :title="isCollapsed ? $t('nav.adminSimulator') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.adminSimulator') }}</span>
        </button>

        <!-- 5. Log & Audit Trail -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'admin-logs' }"
          @click="$emit('navigate', 'admin-logs')"
          :title="isCollapsed ? $t('nav.adminLogs') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.adminLogs') }}</span>
        </button>

        <!-- 5. Parameter Master -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'admin-parameters' || currentTab === 'admin-settings' }"
          @click="$emit('navigate', 'admin-parameters')"
          :title="isCollapsed ? $t('nav.adminParameters') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="21" x2="4" y2="14"></line>
            <line x1="4" y1="10" x2="4" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12" y2="3"></line>
            <line x1="20" y1="21" x2="20" y2="16"></line>
            <line x1="20" y1="12" x2="20" y2="3"></line>
            <line x1="1" y1="14" x2="7" y2="14"></line>
            <line x1="9" y1="8" x2="15" y2="8"></line>
            <line x1="17" y1="16" x2="23" y2="16"></line>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.adminParameters') }}</span>
        </button>

        <!-- 6. Pengaturan Sistem -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'settings' }"
          @click="$emit('navigate', 'settings')"
          :title="isCollapsed ? $t('nav.settings') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.settings') }}</span>
        </button>
      </template>

      <!-- === OPERATOR NAVIGATION === -->
      <template v-else>
        <!-- 1. Dashboard -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'dashboard' }"
          @click="$emit('navigate', 'dashboard')"
          :title="isCollapsed ? $t('nav.dashboard') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4 4h7v7H4V4zm0 9h7v7H4v-7zm9-9h7v7h-7V4zm0 9h7v7h-7v-7z"/>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.dashboard') }}</span>
        </button>

        <!-- 2. Monitoring -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'monitoring' }"
          @click="$emit('navigate', 'monitoring')"
          :title="isCollapsed ? $t('nav.monitoring') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.monitoring') }}</span>
        </button>

        <!-- 3. Pengeringan -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'drying' || currentTab === 'active-drying' || currentTab === 'drying-form' }"
          @click="$emit('navigate', 'drying')"
          :title="isCollapsed ? $t('nav.drying') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M17 5H7M19 12H5M17 19H7"/>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.drying') }}</span>
        </button>

        <!-- 4. Riwayat -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'history' || currentTab === 'history-detail' }"
          @click="$emit('navigate', 'history')"
          :title="isCollapsed ? $t('nav.history') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.history') }}</span>
        </button>

        <!-- 5. Panduan & Info -->
        <button 
          class="nav-item" 
          :class="{ active: currentTab === 'guide' }"
          @click="$emit('navigate', 'guide')"
          :title="isCollapsed ? $t('nav.guide') : ''"
        >
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
          </svg>
          <span v-if="!isCollapsed" class="nav-label">{{ $t('nav.guide') }}</span>
        </button>
      </template>
    </nav>

    <!-- Bottom Actions & Creator Credit -->
    <div class="sidebar-footer">
      <!-- Action Button (Operators only) -->
      <button 
        v-if="!isAdmin"
        class="action-dry-btn" 
        @click="$emit('start-drying')"
        :title="isCollapsed ? $t('nav.startDrying') : ''"
      >
        <svg width="14" height="14" viewBox="0 0 12 14" fill="currentColor">
          <path d="M1.5 1.5L10.5 7L1.5 12.5V1.5Z"/>
        </svg>
        <span v-if="!isCollapsed">{{ $t('nav.startDrying') }}</span>
      </button>

      <!-- Creator & Hardware Partner Badge -->
      <div class="sidebar-creator-badge" :title="isCollapsed ? 'Hardware & App by CoE STAS-RG' : ''">
        <StasLogo :size="isCollapsed ? 26 : 28" />
        <div v-if="!isCollapsed" class="creator-text">
          <span class="creator-label">{{ $t('common.hardwareBy') }}</span>
          <span class="creator-org">CoE STAS-RG</span>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import HanjeliLogo from './HanjeliLogo.vue'
import StasLogo from './StasLogo.vue'

const props = defineProps({
  currentTab: {
    type: String,
    default: 'dashboard'
  },
  isCollapsed: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: () => ({ role: 'OPERATOR' })
  }
})

const isAdmin = computed(() => {
  return props.user?.role === 'ADMIN'
})

defineEmits(['navigate', 'start-drying', 'toggle-collapse'])
</script>

<style scoped>
.desktop-sidebar {
  width: 256px;
  min-width: 256px;
  background: #FFFFFF;
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  height: 100vh;
  flex-shrink: 0;
  z-index: 20;
  transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  overflow-x: hidden;
}

/* Collapsed State */
.desktop-sidebar.collapsed {
  width: 76px;
  min-width: 76px;
}

.sidebar-header {
  padding: 20px 14px 16px;
  border-bottom: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
}

.logo-box {
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-text-block {
  text-align: center;
  display: flex;
  flex-direction: column;
}

.brand-title {
  color: #0D631B;
  font-size: 22px;
  font-weight: 700;
  line-height: 28px;
  white-space: nowrap;
}

.brand-subtitle {
  color: #40493D;
  font-size: 14px;
  font-weight: 500;
  line-height: 20px;
  white-space: nowrap;
}

.sidebar-role-tag {
  font-size: 10px;
  font-weight: 800;
  color: #7C3AED;
  background: #EDE9FE;
  padding: 2px 8px;
  border-radius: 4px;
  margin-top: 4px;
  letter-spacing: 0.5px;
}

.sidebar-nav {
  flex: 1;
  padding: 14px 10px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  overflow-y: auto;
  overflow-x: hidden;
}

.nav-item {
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 12px;
  background: transparent;
  color: #40493D;
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  text-align: left;
  transition: all 0.15s ease;
  white-space: nowrap;
  border: none;
  cursor: pointer;
}

.desktop-sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 12px 0;
  gap: 0;
}

.nav-item:hover {
  background: #F3FAFF;
  color: #0D631B;
}

.nav-item.active {
  background: #0D631B;
  color: #FFFFFF;
}

.nav-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.nav-label {
  white-space: nowrap;
}

.sidebar-footer {
  padding: 14px 10px 16px;
  border-top: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.action-dry-btn {
  width: 100%;
  padding: 11px 14px;
  background: #0D631B;
    border-radius: 8px;
  color: white;
  font-size: 13px;
  font-weight: 600;
  line-height: 18px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  transition: background 0.2s;
  white-space: nowrap;
  border: none;
  cursor: pointer;
}

.action-dry-btn.admin-btn-action {
  background: #0284C7;
}

.action-dry-btn.admin-btn-action:hover {
  background: #0369A1;
}

.desktop-sidebar.collapsed .action-dry-btn {
  padding: 11px 0;
  gap: 0;
}

.action-dry-btn:hover {
  background: #2E7D32;
}

.sidebar-creator-badge {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  margin-top: 2px;
  overflow: hidden;
}

.desktop-sidebar.collapsed .sidebar-creator-badge {
  justify-content: center;
  padding: 8px 0;
}

.creator-text {
  display: flex;
  flex-direction: column;
  text-align: left;
  white-space: nowrap;
}

.creator-label {
  font-size: 10px;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.creator-org {
  font-size: 12px;
  font-weight: 700;
  color: #0F172A;
}

/* =========================================================
   DARK THEME HIGH-CONTRAST SIDEBAR STYLES
   ========================================================= */
:global(.dark-theme) .desktop-sidebar {
  background: #0B242F;
  border-right-color: #1E4E61;
}

:global(.dark-theme) .sidebar-header {
  border-bottom-color: #1E4E61;
}

:global(.dark-theme) .brand-title {
  color: #4ADE80;
}

:global(.dark-theme) .brand-subtitle {
  color: #94A3B8;
}

:global(.dark-theme) .sidebar-role-tag {
  background: rgba(124, 58, 237, 0.25);
  color: #C4B5FD;
}

:global(.dark-theme) .nav-item {
  color: #CBD5E1;
}

:global(.dark-theme) .nav-item:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #4ADE80;
}

:global(.dark-theme) .nav-item.active {
  background: #16A34A;
  color: #FFFFFF;
}

:global(.dark-theme) .sidebar-footer {
  border-top-color: #1E4E61;
}

:global(.dark-theme) .sidebar-creator-badge {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.12);
}

:global(.dark-theme) .creator-label {
  color: #94A3B8;
}

:global(.dark-theme) .creator-org {
  color: #F8FAFC;
}
</style>
