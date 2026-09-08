import { createRouter, createWebHistory } from 'vue-router'
import { authService } from '../services/authService'

// Admin Views
import AdminOverviewView from '../views/admin/AdminOverviewView.vue'
import AdminUsersView from '../views/admin/AdminUsersView.vue'
import AdminDevicesView from '../views/admin/AdminDevicesView.vue'
import AdminSimulatorView from '../views/admin/AdminSimulatorView.vue'
import AdminLogsView from '../views/admin/AdminLogsView.vue'
import AdminParametersView from '../views/admin/AdminParametersView.vue'

// Operator Views
import DashboardView from '../views/operator/DashboardView.vue'
import MonitoringView from '../views/operator/MonitoringView.vue'
import DryingFormView from '../views/operator/DryingFormView.vue'
import ActiveDryingView from '../views/operator/ActiveDryingView.vue'
import HistoryView from '../views/operator/HistoryView.vue'
import HistoryDetailView from '../views/operator/HistoryDetailView.vue'
import DevicesGuideView from '../views/operator/DevicesGuideView.vue'
import SettingsView from '../views/operator/SettingsView.vue'

// Public Landing Page
import LandingView from '../views/public/LandingView.vue'

// Auth Views
import LoginView from '../views/auth/LoginView.vue'
import ForgotPasswordView from '../views/auth/ForgotPasswordView.vue'

// Error Pages
import NotFoundView from '../views/errors/NotFoundView.vue'
import ServerErrorView from '../views/errors/ServerErrorView.vue'
import ForbiddenView from '../views/errors/ForbiddenView.vue'

const routes = [
  // Public Landing Page (Main page before login)
  {
    path: '/',
    name: 'Landing',
    component: LandingView,
    meta: { standalone: true, title: 'Smart Room Dryer • Inovasi Pertanian Desa Wisata Hanjeli' },
  },
  {
    path: '/landing',
    redirect: '/',
  },

  // Auth Routes (Guest Only)
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { guestOnly: true, title: 'Masuk / Daftar Akun' },
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPasswordView,
    meta: { guestOnly: true, title: 'Lupa Kata Sandi' },
  },

  // 1. ADMIN ONLY ROUTES
  {
    path: '/admin',
    name: 'AdminOverview',
    component: AdminOverviewView,
    meta: { requiresAuth: true, role: 'ADMIN', title: 'Ringkasan Sistem Administrator' },
  },
  {
    path: '/admin/dashboard',
    redirect: '/admin',
  },
  {
    path: '/admin/overview',
    redirect: '/admin',
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsersView,
    meta: { requiresAuth: true, role: 'ADMIN', title: 'Manajemen Pengguna' },
  },
  {
    path: '/admin/devices',
    name: 'AdminDevices',
    component: AdminDevicesView,
    meta: { requiresAuth: true, role: 'ADMIN', title: 'Perangkat & Sensor IoT' },
  },
  {
    path: '/admin/simulator',
    name: 'AdminSimulator',
    component: AdminSimulatorView,
    meta: { requiresAuth: true, role: 'ADMIN', title: 'Simulator & Uji IoT' },
  },
  {
    path: '/admin/testbed',
    redirect: '/admin/simulator',
  },
  {
    path: '/admin/logs',
    name: 'AdminLogs',
    component: AdminLogsView,
    meta: { requiresAuth: true, role: 'ADMIN', title: 'Log & Audit Trail' },
  },
  {
    path: '/admin/parameters',
    name: 'AdminParameters',
    component: AdminParametersView,
    meta: { requiresAuth: true, role: 'ADMIN', title: 'Parameter Master' },
  },
  {
    path: '/admin/settings',
    redirect: '/settings',
  },

  // 2. OPERATOR & SHARED AUTHENTICATED ROUTES
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true, role: 'OPERATOR', title: 'Dashboard Utama' },
  },
  {
    path: '/monitoring',
    name: 'Monitoring',
    component: MonitoringView,
    meta: { requiresAuth: true, title: 'Live Monitoring Sensor' },
  },
  {
    path: '/drying/new',
    name: 'DryingForm',
    component: DryingFormView,
    meta: { requiresAuth: true, role: 'OPERATOR', title: 'Mulai Pengeringan Baru' },
  },
  {
    path: '/drying-form',
    redirect: '/drying/new',
  },
  {
    path: '/drying/active',
    name: 'ActiveDrying',
    component: ActiveDryingView,
    meta: { requiresAuth: true, role: 'OPERATOR', title: 'Proses Pengeringan Aktif' },
  },
  {
    path: '/active-drying',
    redirect: '/drying/active',
  },
  {
    path: '/history',
    name: 'History',
    component: HistoryView,
    meta: { requiresAuth: true, role: 'OPERATOR', title: 'Riwayat Batch Pengeringan' },
  },
  {
    path: '/history/:id',
    name: 'HistoryDetail',
    component: HistoryDetailView,
    props: true,
    meta: { requiresAuth: true, role: 'OPERATOR', title: 'Detail Batch Pengeringan' },
  },
  {
    path: '/guide',
    name: 'Guide',
    component: DevicesGuideView,
    meta: { requiresAuth: true, title: 'Panduan Hardware & Sistem' },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: SettingsView,
    meta: { requiresAuth: true, title: 'Pengaturan Akun & Sistem' },
  },

  // 3. ERROR & FALLBACK ROUTES
  {
    path: '/403',
    name: 'Forbidden',
    component: ForbiddenView,
    meta: { title: 'Akses Ditolak (403)' },
  },
  {
    path: '/500',
    name: 'ServerError',
    component: ServerErrorView,
    meta: { title: 'Kesalahan Server (500)' },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFoundView,
    meta: { title: 'Halaman Tidak Ditemukan (404)' },
  },
]

export const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Navigation Guards: Auth, Role, Page Titles
router.beforeEach((to, from, next) => {
  // Set document title
  const appName = 'Smart Room Dryer Hanjeli'
  document.title = to.meta.title ? `${to.meta.title} | ${appName}` : appName

  const isAuthenticated = authService.isAuthenticated()
  const currentUser = authService.getCurrentUser()

  // Guest Only Routes (e.g. Login, ForgotPassword)
  if (to.meta.guestOnly && isAuthenticated) {
    if (currentUser?.role === 'ADMIN') {
      return next('/admin')
    }
    return next('/dashboard')
  }

  // Protected Routes
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({
      path: '/login',
      query: { redirect: to.fullPath },
    })
  }

  // Admin-only Route Check
  if (to.meta.role === 'ADMIN' && currentUser?.role !== 'ADMIN') {
    return next('/403')
  }

  // Operator-only Route Check
  if (to.meta.role === 'OPERATOR' && currentUser?.role === 'ADMIN') {
    return next('/admin')
  }

  next()
})

export default router
