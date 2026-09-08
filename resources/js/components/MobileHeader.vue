<template>
  <header class="mobile-header">
    <template v-if="!hasBack">
      <div class="header-left">
        <HanjeliLogo :size="34" />
        <span class="brand-text">Smart Dryer</span>
      </div>
      
      <div class="header-right">
        <!-- Lang Toggle (SVG Flag Only, Borderless) -->
        <button 
          class="mobile-icon-btn lang-pill-btn" 
          @click="$emit('change-lang', currentLang === 'id' ? 'en' : 'id')"
          :title="currentLang === 'id' ? 'Ganti ke English' : 'Switch to Indonesian'"
        >
          <FlagIcon :code="currentLang" :size="20" />
        </button>

        <!-- Dark Mode Toggle (Icon Only, Borderless) -->
        <button 
          class="mobile-icon-btn theme-btn" 
          @click="$emit('toggle-theme')"
          :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
        >
          <svg v-if="isDark" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
          </svg>
          <svg v-else width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#071E27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
          </svg>
        </button>

        <!-- Notification Bell (with mobile sheet) -->
        <div class="mobile-notif-wrapper" ref="mobileNotifRef">
          <button 
            class="mobile-icon-btn notif-btn" 
            @click="isNotifOpen = !isNotifOpen"
            :title="currentLang === 'id' ? 'Pemberitahuan & Alert' : 'Notifications & Alerts'"
          >
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span v-if="unreadCount > 0" class="m-notif-badge">{{ unreadCount }}</span>
          </button>

          <!-- Mobile Alert Notification Dropdown/Sheet -->
          <div v-if="isNotifOpen" class="mobile-notif-popup">
            <div class="mn-header">
              <div class="mn-title-group">
                <h4>{{ currentLang === 'id' ? 'Pemberitahuan & Alert' : 'Alerts & Notifs' }}</h4>
                <span class="mn-count">{{ unreadCount }}</span>
              </div>
              <button class="mn-read-btn" @click="markAllAsRead">
                {{ currentLang === 'id' ? 'Tandai Dibaca' : 'Mark Read' }}
              </button>
            </div>

            <!-- Filter chips -->
            <div class="mn-filters">
              <button 
                class="mn-chip" 
                :class="{ active: notifFilter === 'all' }"
                @click="notifFilter = 'all'"
              >
                {{ currentLang === 'id' ? 'Semua' : 'All' }}
              </button>
              <button 
                class="mn-chip" 
                :class="{ active: notifFilter === 'alert' }"
                @click="notifFilter = 'alert'"
              >
                {{ currentLang === 'id' ? 'Alerts' : 'Alerts' }}
              </button>
              <button 
                class="mn-chip" 
                :class="{ active: notifFilter === 'system' }"
                @click="notifFilter = 'system'"
              >
                {{ currentLang === 'id' ? 'Sistem' : 'System' }}
              </button>
            </div>

            <!-- List -->
            <div class="mn-list">
              <div 
                v-for="notif in filteredNotifications" 
                :key="notif.id"
                class="mn-card"
                :class="[`type-${notif.type}`, { unread: notif.unread }]"
                @click="markAsRead(notif.id)"
              >
                <div class="mn-card-lead">
                  <span class="mn-badge-type">{{ notif.type.toUpperCase() }}</span>
                  <span class="mn-time">{{ currentLang === 'id' ? notif.time : notif.timeEn }}</span>
                </div>
                <h5 class="mn-item-title">{{ currentLang === 'id' ? notif.title : notif.titleEn }}</h5>
                <p class="mn-item-msg">{{ currentLang === 'id' ? notif.message : notif.messageEn }}</p>

                <div v-if="notif.actionLabel" class="mn-action-row">
                  <button class="mn-action-btn" @click.stop="triggerAlertAction(notif)">
                    {{ currentLang === 'id' ? notif.actionLabel : notif.actionLabelEn }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Button (with popup) -->
        <div class="mobile-profile-wrapper" ref="mobileProfileRef">
          <button 
            class="mobile-avatar-btn" 
            @click="isMenuOpen = !isMenuOpen"
            :class="{ active: isMenuOpen }"
          >
            <span>{{ userInitials }}</span>
          </button>

          <!-- Mobile Profile Menu Sheet/Popup -->
          <div v-if="isMenuOpen" class="mobile-profile-popup">
            <div class="mp-header">
              <div class="mp-avatar">
                <span>{{ userInitials }}</span>
              </div>
              <div class="mp-user-info">
                <strong>{{ user.name }}</strong>
                <span>{{ user.email }}</span>
                <span class="mp-role">{{ user.location }}</span>
              </div>
            </div>

            <div class="mp-divider"></div>

            <div class="mp-menu-list">
              <button class="mp-item-btn" @click="triggerEditProfile">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>{{ currentLang === 'id' ? 'Edit Profil' : 'Edit Profile' }}</span>
              </button>

              <button class="mp-item-btn" @click="triggerSettings">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="3"></circle>
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>{{ currentLang === 'id' ? 'Pengaturan' : 'Settings' }}</span>
              </button>
            </div>

            <div class="mp-divider"></div>

            <button class="mp-logout-btn" @click="triggerLogout">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>{{ currentLang === 'id' ? 'Keluar' : 'Logout' }}</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="subpage-header">
        <button class="back-btn" @click="$emit('back')">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
        </button>
        <h2 class="subpage-title">{{ title }}</h2>
        <div class="subpage-right">
          <slot name="header-right"></slot>
        </div>
      </div>
    </template>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import HanjeliLogo from './HanjeliLogo.vue'
import FlagIcon from './FlagIcon.vue'
import { alertService } from '../services/alertService'
import { socketService } from '../services/socketService'
import { simulatorService } from '../services/simulatorService'
import { systemState } from '../services/settingsService'

const props = defineProps({
  hasBack: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  isDark: {
    type: Boolean,
    default: false
  },
  currentLang: {
    type: String,
    default: 'id'
  },
  user: {
    type: Object,
    default: () => ({
      name: 'Dr. Ir. Fatin Tsani',
      email: 'admin@hanjeli.com',
      role: 'ADMIN',
      location: 'Desa Wisata Waluran, Sukabumi'
    })
  }
})

const emit = defineEmits([
  'back',
  'logout',
  'toggle-theme',
  'change-lang',
  'edit-profile',
  'settings',
  'navigate',
  'open-simulator'
])

const isMenuOpen = ref(false)
const isNotifOpen = ref(false)
const mobileProfileRef = ref(null)
const mobileNotifRef = ref(null)
const notifFilter = ref('all')

const notifications = ref([])
let alertSyncTimer = null

function transformAlert(a) {
  const type = a.type === 'DANGER' || a.level === 'CRITICAL' ? 'critical' : a.type === 'WARNING' || a.level === 'WARNING' ? 'warning' : a.type === 'SUCCESS' || a.level === 'SUCCESS' ? 'success' : 'info'
  const timeStr = a.time || (a.createdAt ? new Date(a.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'Baru saja')
  const timeEnStr = a.timeEn || timeStr

  return {
    id: a.id,
    type,
    title: a.title,
    titleEn: a.titleEn || a.title,
    message: a.message,
    messageEn: a.messageEn || a.message,
    time: timeStr,
    timeEn: timeEnStr,
    unread: !a.isRead && !a.is_read,
    actionLabel: a.actionLabel || (type === 'critical' || type === 'warning' ? 'Lihat Monitoring' : 'Lihat Detail'),
    actionLabelEn: a.actionLabelEn || (type === 'critical' || type === 'warning' ? 'Live Monitoring' : 'View Details'),
    route: a.route || (a.category === 'SENSOR' ? 'monitoring' : a.category === 'BATCH' ? 'history' : a.category === 'DEVICE' ? 'guide' : 'monitoring')
  }
}

async function loadAlerts() {
  try {
    const res = await alertService.getAll()
    const list = Array.isArray(res) ? res : res?.alerts || []
    notifications.value = list.map(transformAlert)
  } catch (err) {
    console.warn('Could not load alerts from DB:', err.message)
  }
}

onMounted(() => {
  loadAlerts()

  // Real-time synchronization with database every 5 seconds
  alertSyncTimer = setInterval(loadAlerts, 5000)

  socketService.on('alert_new', (newAlert) => {
    notifications.value.unshift(transformAlert(newAlert))
  })
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  if (alertSyncTimer) clearInterval(alertSyncTimer)
  socketService.off('alert_new')
  document.removeEventListener('click', handleClickOutside)
})

const unreadCount = computed(() => {
  return notifications.value.filter(n => n.unread).length
})

const filteredNotifications = computed(() => {
  if (notifFilter.value === 'alert') {
    return notifications.value.filter(n => n.type === 'critical' || n.type === 'warning')
  }
  if (notifFilter.value === 'system') {
    return notifications.value.filter(n => n.type === 'info' || n.type === 'success')
  }
  return notifications.value
})

async function markAsRead(id) {
  const notif = notifications.value.find(n => n.id === id)
  if (notif) notif.unread = false
  try {
    await alertService.markAsRead(id)
  } catch (e) {
    console.warn('Mark as read note:', e.message)
  }
}

async function markAllAsRead() {
  notifications.value.forEach(n => { n.unread = false })
  try {
    await alertService.markAllAsRead()
  } catch (e) {
    console.warn('Mark all as read note:', e.message)
  }
}

async function clearAllAlerts() {
  notifications.value = []
  try {
    await alertService.clearAll()
  } catch (e) {
    console.warn('Clear all alerts note:', e.message)
  }
}

function triggerAlertAction(notif) {
  markAsRead(notif.id)
  isNotifOpen.value = false
  if (notif.route) {
    emit('navigate', notif.route)
  }
}

const userInitials = computed(() => {
  if (!props.user?.name) return 'PA'
  const parts = props.user.name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return parts[0].slice(0, 2).toUpperCase()
})

function triggerEditProfile() {
  isMenuOpen.value = false
  emit('edit-profile')
}

function triggerSettings() {
  isMenuOpen.value = false
  emit('settings')
}

function triggerLogout() {
  isMenuOpen.value = false
  emit('logout')
}

function handleClickOutside(e) {
  if (mobileProfileRef.value && !mobileProfileRef.value.contains(e.target)) {
    isMenuOpen.value = false
  }
  if (mobileNotifRef.value && !mobileNotifRef.value.contains(e.target)) {
    isNotifOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.mobile-header {
  height: 60px;
  background: #FFFFFF;
  border-bottom: 1px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  position: sticky;
  top: 0;
  z-index: 30;
  width: 100%;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-text {
  font-size: 19px;
  font-weight: 700;
  color: #0D631B;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 6px;
}

.mobile-icon-btn {
  background: transparent;
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.mobile-icon-btn:hover {
  background: rgba(0, 0, 0, 0.05);
}

.mobile-notif-wrapper {
  position: relative;
}

.m-notif-badge {
  position: absolute;
  top: 3px;
  right: 3px;
  background: #BA1A1A;
  color: #FFFFFF;
  font-size: 9px;
  font-weight: 700;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-notif-popup {
  position: absolute;
  top: 44px;
  right: -50px;
  width: 310px;
  max-width: 90vw;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
    padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 60;
}

.mn-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #F1F5F9;
  padding-bottom: 6px;
}

.mn-title-group {
  display: flex;
  align-items: center;
  gap: 6px;
}

.mn-title-group h4 {
  font-size: 13px;
  font-weight: 700;
  color: #0F172A;
}

.mn-count {
  font-size: 10px;
  background: #E8F5E9;
  color: #0D631B;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 10px;
}

.mn-read-btn {
  background: transparent;
  font-size: 11px;
  font-weight: 600;
  color: #0D631B;
  cursor: pointer;
}

.mn-filters {
  display: flex;
  gap: 4px;
}

.mn-chip {
  padding: 3px 8px;
  background: #F1F5F9;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  color: #64748B;
  border: none;
}

.mn-chip.active {
  background: #0D631B;
  color: #FFFFFF;
}

.mn-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
  max-height: 260px;
  overflow-y: auto;
}

.mn-card {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #E2E8F0;
  background: #FFFFFF;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.mn-card.unread {
  background: #F8FAFC;
  border-color: #CBD5E1;
}

.mn-card.type-critical {
  border-left: 3px solid #BA1A1A;
}
.mn-card.type-warning {
  border-left: 3px solid #D97706;
}
.mn-card.type-success {
  border-left: 3px solid #0D631B;
}

.mn-card-lead {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.mn-badge-type {
  font-size: 9px;
  font-weight: 800;
  color: #64748B;
}

.mn-time {
  font-size: 9px;
  color: #94A3B8;
}

.mn-item-title {
  font-size: 12px;
  font-weight: 700;
  color: #0F172A;
}

.mn-item-msg {
  font-size: 11px;
  color: #475569;
  line-height: 14px;
}

.mn-action-row {
  margin-top: 4px;
}

.mn-action-btn {
  font-size: 10.5px;
  font-weight: 600;
  color: #0D631B;
  background: #E8F5E9;
  padding: 3px 8px;
  border-radius: 4px;
}

.mobile-profile-wrapper {
  position: relative;
}

.mobile-avatar-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2E7D32 0%, #0D631B 100%);
  color: #FFFFFF;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
    border: none;
}

.mobile-avatar-btn.active {
  }

.mobile-profile-popup {
  position: absolute;
  top: 44px;
  right: 0;
  width: 250px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
    padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  z-index: 50;
}

.mp-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mp-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2E7D32 0%, #0D631B 100%);
  color: white;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.mp-user-info {
  display: flex;
  flex-direction: column;
  gap: 1px;
  overflow: hidden;
}

.mp-user-info strong {
  font-size: 13px;
  color: #0F172A;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.mp-user-info span {
  font-size: 11px;
  color: #64748B;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.mp-role {
  font-size: 10px !important;
  color: #0D631B !important;
  font-weight: 600;
}

.mp-divider {
  height: 1px;
  background: #F1F5F9;
}

.mp-menu-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.mp-item-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  border-radius: 6px;
  background: transparent;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  text-align: left;
}

.mp-item-btn:hover {
  background: #F8FAFC;
}

.mp-logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 8px;
  background: #FEE2E2;
  color: #991B1B;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}

.subpage-header {
  display: flex;
  align-items: center;
  width: 100%;
  gap: 12px;
}

.back-btn {
  background: transparent;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #071E27;
}

.subpage-title {
  font-size: 18px;
  font-weight: 700;
  color: #071E27;
  flex: 1;
}

.subpage-right {
  display: flex;
  align-items: center;
}
</style>
