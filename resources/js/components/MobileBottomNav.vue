<template>
  <nav class="mobile-bottom-nav">
    <!-- Admin Tabs -->
    <template v-if="isAdmin">
      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'admin' || activeTab === 'admin-overview' }"
        @click="$emit('update:activeTab', 'admin-overview')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
        </div>
        <span>{{ currentLang === 'id' ? 'Ringkasan' : 'Overview' }}</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'admin-users' }"
        @click="$emit('update:activeTab', 'admin-users')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
        <span>{{ currentLang === 'id' ? 'Pengguna' : 'Users' }}</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'admin-devices' }"
        @click="$emit('update:activeTab', 'admin-devices')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
            <rect x="9" y="9" width="6" height="6"></rect>
            <line x1="9" y1="1" x2="9" y2="4"></line>
            <line x1="15" y1="1" x2="15" y2="4"></line>
            <line x1="9" y1="20" x2="9" y2="23"></line>
            <line x1="15" y1="20" x2="15" y2="23"></line>
          </svg>
        </div>
        <span>IoT</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'admin-logs' }"
        @click="$emit('update:activeTab', 'admin-logs')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
          </svg>
        </div>
        <span>Audit</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'admin-parameters' || activeTab === 'admin-settings' || activeTab === 'settings' }"
        @click="$emit('update:activeTab', 'admin-parameters')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="21" x2="4" y2="14"></line>
            <line x1="4" y1="10" x2="4" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12" y2="3"></line>
            <line x1="20" y1="21" x2="20" y2="16"></line>
            <line x1="20" y1="12" x2="20" y2="3"></line>
          </svg>
        </div>
        <span>Parameter</span>
      </button>
    </template>

    <!-- Operator Tabs -->
    <template v-else>
      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'dashboard' }"
        @click="$emit('update:activeTab', 'dashboard')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
          </svg>
        </div>
        <span>{{ $t('nav.dashboard') }}</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'monitoring' }"
        @click="$emit('update:activeTab', 'monitoring')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
          </svg>
        </div>
        <span>{{ $t('nav.monitoring') }}</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'drying' || activeTab === 'active-drying' }"
        @click="$emit('update:activeTab', 'drying')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M17 5H7M19 12H5M17 19H7"/>
          </svg>
        </div>
        <span>{{ $t('nav.drying') }}</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'history' || activeTab === 'history-detail' }"
        @click="$emit('update:activeTab', 'history')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <span>{{ $t('nav.history') }}</span>
      </button>

      <button 
        class="nav-tab" 
        :class="{ active: activeTab === 'guide' }"
        @click="$emit('update:activeTab', 'guide')"
      >
        <div class="icon-container">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
        </div>
        <span>{{ $t('nav.guide') }}</span>
      </button>
    </template>
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  activeTab: {
    type: String,
    default: 'dashboard'
  },
  user: {
    type: Object,
    default: () => ({ role: 'OPERATOR' })
  }
})

const isAdmin = computed(() => {
  return props.user?.role === 'ADMIN'
})

defineEmits(['update:activeTab'])
</script>

<style scoped>
.mobile-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 64px;
  background: #FFFFFF;
  border-top: 1px solid var(--color-border);
  display: flex;
  justify-content: space-around;
  align-items: center;
  z-index: 40;
  max-width: 480px;
  margin: 0 auto;
}

.nav-tab {
  background: transparent;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  color: #40493D;
  font-size: 11px;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 20px;
  transition: all 0.2s;
  border: none;
  cursor: pointer;
}

.icon-container {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-container svg {
  width: 20px;
  height: 20px;
}

.nav-tab.active {
  color: #0D631B;
}

.nav-tab.active .icon-container {
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 9999px;
  padding: 4px;
}

.nav-tab.active .icon-container svg {
  width: 16px;
  height: 16px;
}

:global(.dark-theme) .mobile-bottom-nav {
  background: #0B242F;
  border-top-color: #1E4E61;
}

:global(.dark-theme) .nav-tab {
  color: #CBD5E1;
}

:global(.dark-theme) .nav-tab.active {
  color: #4ADE80;
}

:global(.dark-theme) .nav-tab.active .icon-container {
  background: #16A34A;
  color: #FFFFFF;
}
</style>
