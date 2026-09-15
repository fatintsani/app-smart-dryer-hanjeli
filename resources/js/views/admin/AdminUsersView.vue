<template>
  <div class="admin-page">
    <Transition name="fade-admin-users" mode="out-in">
      <AdminSkeleton v-if="isInitialLoading" />
      <div v-else class="admin-users-loaded-content">
        <div class="admin-content">
          <!-- Top Title & Action Bar -->
          <div class="header-action-row">
            <div class="title-group">
          <h1 class="page-title">{{ $t('admin.tabUsers') }}</h1>
          <p class="page-subtitle">Kelola hak akses pengguna, administrator, dan operator sistem Smart Dryer Hanjeli.</p>
        </div>

        <div class="header-actions">
          <button class="btn-primary" @click="isCreateUserModalOpen = true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>{{ $t('admin.addNewUser') }}</span>
          </button>
        </div>
      </div>

      <!-- Alert / Feedback Banner -->
      <div v-if="adminAlert.text" class="admin-feedback-toast" :class="adminAlert.type">
        <svg v-if="adminAlert.type === 'success'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>{{ adminAlert.text }}</span>
        <button class="close-toast-btn" @click="adminAlert.text = ''" title="Tutup">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Users Management Content Card -->
      <div class="content-card">
        <div class="card-toolbar">
          <div class="search-input-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input 
              type="text" 
              v-model="userSearchQuery" 
              :placeholder="$t('admin.searchUser')" 
              class="search-text-field"
            />
          </div>

          <div class="filter-count-badge">
            <span>Menampilkan {{ filteredUsers.length }} Pengguna ({{ operatorCount }} Operator, {{ usersList.length - operatorCount }} Admin)</span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="modern-data-table">
            <thead>
              <tr>
                <th>Pengguna</th>
                <th>Role / Hak Akses</th>
                <th>Kontak / WhatsApp</th>
                <th>Sesi Batch</th>
                <th>Terdaftar Sejak</th>
                <th class="text-right">Aksi Manajemen</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in filteredUsers" :key="u.id">
                <td>
                  <div class="user-cell">
                    <div class="user-avatar" :class="`avatar-${(u.role || 'OPERATOR').toLowerCase()}`">
                      <img v-if="u.avatarUrl || u.avatar" :src="u.avatarUrl || u.avatar" alt="Avatar" class="table-avatar-img" />
                      <span v-else>{{ (u.name || 'U').slice(0, 2).toUpperCase() }}</span>
                    </div>
                    <div class="user-meta">
                      <strong class="user-name">{{ u.name }}</strong>
                      <span class="user-email">{{ u.email }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge-pill" :class="u.role === 'ADMIN' ? 'bg-orange-subtle text-orange' : 'bg-green-subtle text-green-dark'">
                    {{ u.role }}
                  </span>
                </td>
                <td>
                  <span class="phone-text">{{ u.phone || '-' }}</span>
                </td>
                <td>
                  <span class="badge-pill bg-gray-subtle text-muted">{{ u._count?.batches || 0 }} Batch</span>
                </td>
                <td>
                  <span class="date-text">{{ formatDate(u.createdAt) }}</span>
                </td>
                <td class="text-right">
                  <div class="table-actions-row">
                    <select 
                      class="role-select" 
                      :value="u.role" 
                      @change="handleRoleChange(u, $event.target.value)"
                      :disabled="isSelf(u)"
                    >
                      <option value="ADMIN">ADMIN</option>
                      <option value="OPERATOR">OPERATOR</option>
                    </select>

                    <button 
                      class="btn-icon-edit" 
                      @click="openEditUserModal(u)"
                      title="Edit Data Pengguna"
                    >
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>

                    <button 
                      class="btn-icon-danger" 
                      :disabled="isSelf(u)"
                      @click="promptDeleteUser(u)"
                      :title="isSelf(u) ? 'Tidak dapat menghapus akun sendiri' : 'Hapus akun pengguna'"
                    >
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredUsers.length === 0">
                <td colspan="6" class="empty-table-cell">
                  Tidak ditemukan data pengguna yang cocok.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- CREATE USER MODAL -->
    <div v-if="isCreateUserModalOpen" class="modal-backdrop" @click.self="isCreateUserModalOpen = false">
      <div class="admin-modal-card">
        <div class="modal-head">
          <h3 class="modal-title">{{ $t('admin.addNewUser') }}</h3>
          <button class="btn-close-modal" @click="isCreateUserModalOpen = false" title="Tutup">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleCreateUser" class="modal-form">
          <div class="form-field-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" v-model="newUser.name" placeholder="Masukkan nama lengkap" class="modern-input" required />
          </div>

          <div class="form-field-group">
            <label class="form-label">Alamat Email</label>
            <input type="email" v-model="newUser.email" placeholder="nama@domain.com" class="modern-input" required />
          </div>

          <div class="form-field-group">
            <label class="form-label">Role / Hak Akses</label>
            <select v-model="newUser.role" class="modern-input">
              <option value="OPERATOR">OPERATOR (Akses Dashboard & Pengeringan)</option>
              <option value="ADMIN">ADMINISTRATOR (Akses Penuh)</option>
            </select>
          </div>

          <div class="form-field-group">
            <label class="form-label">Nomor WhatsApp (Opsional)</label>
            <input type="tel" v-model="newUser.phone" placeholder="+62 812-3456-7890" class="modern-input" />
          </div>

          <div class="form-field-group">
            <label class="form-label">Kata Sandi Awal</label>
            <input type="password" v-model="newUser.password" placeholder="Minimal 6 karakter" minlength="6" class="modern-input" required />
          </div>

          <div class="modal-foot">
            <button type="button" class="btn-secondary-action" @click="isCreateUserModalOpen = false">Batal</button>
            <button type="submit" class="btn-primary" :disabled="isCreatingUser">
              {{ isCreatingUser ? 'Mendaftarkan...' : 'Daftarkan Pengguna' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div v-if="isEditUserModalOpen" class="modal-backdrop" @click.self="isEditUserModalOpen = false">
      <div class="admin-modal-card">
        <div class="modal-head">
          <h3 class="modal-title">Edit Data Pengguna</h3>
          <button class="btn-close-modal" @click="isEditUserModalOpen = false" title="Tutup">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleUpdateUser" class="modal-form">
          <div class="form-field-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" v-model="editUser.name" placeholder="Masukkan nama lengkap" class="modern-input" required />
          </div>

          <div class="form-field-group">
            <label class="form-label">Alamat Email</label>
            <input type="email" v-model="editUser.email" placeholder="nama@domain.com" class="modern-input" required />
          </div>

          <div class="form-field-group">
            <label class="form-label">Role / Hak Akses</label>
            <select v-model="editUser.role" class="modern-input">
              <option value="OPERATOR">OPERATOR (Akses Dashboard & Pengeringan)</option>
              <option value="ADMIN">ADMINISTRATOR (Akses Penuh)</option>
            </select>
          </div>

          <div class="form-field-group">
            <label class="form-label">Nomor WhatsApp (Opsional)</label>
            <input type="tel" v-model="editUser.phone" placeholder="+62 812-3456-7890" class="modern-input" />
          </div>

          <div class="form-field-group">
            <label class="form-label">Kata Sandi Baru (Opsional)</label>
            <input type="password" v-model="editUser.password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" minlength="6" class="modern-input" />
            <span class="field-hint" style="font-size:12px; color:var(--color-text-muted); margin-top:4px;">Biarkan kosong jika kata sandi pengguna tidak ingin diubah.</span>
          </div>

          <div class="modal-foot">
            <button type="button" class="btn-secondary-action" @click="isEditUserModalOpen = false">Batal</button>
            <button type="submit" class="btn-primary" :disabled="isUpdatingUser">
              {{ isUpdatingUser ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { authService } from '../../services/authService'
import { confirmDialog } from '../../services/confirmDialogService'
import AdminSkeleton from '../../components/AdminSkeleton.vue'

const isInitialLoading = ref(true)

const usersList = ref([])
const userSearchQuery = ref('')
const isCreateUserModalOpen = ref(false)
const isCreatingUser = ref(false)
const isEditUserModalOpen = ref(false)
const isUpdatingUser = ref(false)

const editUser = reactive({
  id: '',
  name: '',
  email: '',
  role: 'OPERATOR',
  phone: '',
  password: ''
})

const adminAlert = reactive({
  text: '',
  type: 'success'
})

function showAlert(text, type = 'success') {
  adminAlert.text = text
  adminAlert.type = type
  setTimeout(() => {
    if (adminAlert.text === text) adminAlert.text = ''
  }, 5000)
}

const newUser = reactive({
  name: '',
  email: '',
  role: 'OPERATOR',
  phone: '',
  password: ''
})

const operatorCount = computed(() => {
  return usersList.value.filter(u => u.role === 'OPERATOR').length
})

const filteredUsers = computed(() => {
  if (!userSearchQuery.value) return usersList.value
  const q = userSearchQuery.value.toLowerCase()
  return usersList.value.filter(u => 
    u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q) || u.role?.toLowerCase().includes(q)
  )
})

function isSelf(targetUser) {
  const current = authService.getCurrentUser()
  return current && (current.id === targetUser.id || current.email === targetUser.email)
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

async function fetchUsers(isFirstTime = false) {
  try {
    const res = await authService.getAllUsers()
    if (res && res.users && Array.isArray(res.users)) {
      usersList.value = res.users
    } else if (Array.isArray(res)) {
      usersList.value = res
    } else if (res && res.data && Array.isArray(res.data)) {
      usersList.value = res.data
    } else {
      usersList.value = []
    }
  } catch (err) {
    console.warn('Failed to fetch users:', err.message)
    usersList.value = []
  } finally {
    if (isFirstTime) {
      setTimeout(() => {
        isInitialLoading.value = false
      }, 350)
    }
  }
}

async function handleCreateUser() {
  if (!newUser.name || !newUser.email || !newUser.password) return
  isCreatingUser.value = true
  try {
    await authService.createUser(newUser)
    showAlert('Pengguna baru berhasil ditambahkan!')
    isCreateUserModalOpen.value = false
    newUser.name = ''
    newUser.email = ''
    newUser.password = ''
    newUser.phone = ''
    fetchUsers()
  } catch (err) {
    showAlert(err.message || 'Gagal menambahkan pengguna.', 'error')
  } finally {
    isCreatingUser.value = false
  }
}

function openEditUserModal(userObj) {
  editUser.id = userObj.id
  editUser.name = userObj.name || ''
  editUser.email = userObj.email || ''
  editUser.role = userObj.role || 'OPERATOR'
  editUser.phone = userObj.phone || ''
  editUser.password = ''
  isEditUserModalOpen.value = true
}

async function handleUpdateUser() {
  if (!editUser.name || !editUser.email) return
  isUpdatingUser.value = true
  try {
    const payload = {
      name: editUser.name,
      email: editUser.email,
      role: editUser.role,
      phone: editUser.phone || null
    }
    if (editUser.password) {
      payload.password = editUser.password
    }
    await authService.updateUser(editUser.id, payload)
    showAlert(`Data pengguna '${editUser.name}' berhasil diperbarui!`)
    isEditUserModalOpen.value = false
    fetchUsers()
  } catch (err) {
    showAlert(err.message || 'Gagal memperbarui data pengguna.', 'error')
  } finally {
    isUpdatingUser.value = false
  }
}

async function handleRoleChange(userObj, newRole) {
  try {
    await authService.updateUserRole(userObj.id, newRole)
    userObj.role = newRole
    showAlert(`Peran ${userObj.name} berhasil diubah menjadi ${newRole}`)
  } catch (err) {
    showAlert(err.message || 'Gagal mengubah peran.', 'error')
  }
}

async function promptDeleteUser(userObj) {
  const confirmed = await confirmDialog({
    title: 'Hapus Akun Pengguna?',
    message: `Apakah Anda yakin ingin menghapus akun ${userObj.name}? Pengguna ini tidak akan dapat mengakses sistem lagi.`,
    details: `Email: ${userObj.email} • Role: ${userObj.role}`,
    type: 'danger',
    confirmText: 'Ya, Hapus Akun',
    cancelText: 'Batal',
  })

  if (confirmed) {
    try {
      await authService.deleteUser(userObj.id)
      showAlert(`Pengguna ${userObj.name} berhasil dihapus.`)
      fetchUsers()
    } catch (err) {
      showAlert(err.message || 'Gagal menghapus pengguna.', 'error')
    }
  }
}

onMounted(() => {
  fetchUsers(true)
})
</script>

<style scoped>
.admin-page {
  width: 100%;
  min-height: 100%;
}

.admin-content {
  padding: 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 32px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.title-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: var(--color-text-title);
  line-height: 40px;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
  line-height: 24px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.admin-feedback-toast {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  }

.admin-feedback-toast.success {
  background: #E8F5E9;
  color: #0D631B;
  border: 1px solid #C8E6C9;
}

.admin-feedback-toast.error {
  background: #FFEBEE;
  color: #C62828;
  border: 1px solid #FFCDD2;
}

.close-toast-btn {
  background: transparent;
  border: none;
  font-size: 16px;
  cursor: pointer;
  margin-left: auto;
  color: inherit;
  opacity: 0.7;
}

.content-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
    padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.search-input-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 8px 14px;
  width: 320px;
  max-width: 100%;
}

.search-text-field {
  background: transparent;
  border: none;
  font-size: 14px;
  color: var(--color-text-title);
  width: 100%;
  outline: none;
}

.filter-count-badge {
  font-size: 13px;
  color: var(--color-text-muted);
  font-weight: 500;
}

.table-responsive {
  overflow-x: auto;
}

.modern-data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.modern-data-table th {
  text-align: left;
  padding: 12px 16px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  color: var(--color-text-muted);
  border-bottom: 2px solid var(--color-border);
  letter-spacing: 0.5px;
}

.modern-data-table td {
  padding: 14px 16px;
  border-bottom: 1px solid var(--color-border);
  color: var(--color-text-main);
  vertical-align: middle;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  overflow: hidden;
  flex-shrink: 0;
}

.table-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-admin { background: #F3E8FF; color: #7E22CE; }
.avatar-operator { background: #DCFCE7; color: #15803D; }

.user-meta {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-title);
}

.user-email {
  font-size: 12px;
  color: var(--color-text-muted);
}

.phone-text, .date-text {
  font-size: 13px;
  color: var(--color-text-muted);
}

.table-actions-row {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
}

.role-select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 14px 14px;
  padding: 6px 28px 6px 10px;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  background-color: var(--color-bg);
  color: var(--color-text-title);
  cursor: pointer;
  transition: all 0.2s ease;
}

.role-select:focus {
  outline: 2px solid rgba(13, 99, 27, 0.2) !important;
  border-color: #0D631B;
}

.btn-icon-edit {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid rgba(13, 99, 27, 0.25);
  background: rgba(13, 99, 27, 0.06);
  color: #0D631B;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon-edit:hover {
  background: #0D631B;
  color: #FFFFFF;
}

.btn-icon-danger {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid rgba(220, 38, 38, 0.2);
  background: rgba(220, 38, 38, 0.06);
  color: #DC2626;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon-danger:hover:not(:disabled) {
  background: #DC2626;
  color: #FFFFFF;
}

.btn-icon-danger:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.empty-table-cell {
  text-align: center;
  padding: 32px;
  color: var(--color-text-muted);
}

.btn-primary {
  padding: 10px 20px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
    transition: background 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #15803D;
}

.btn-secondary-action {
  padding: 10px 18px;
  background: var(--color-bg);
  color: var(--color-text-title);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary-action:hover {
  background: var(--color-white);
  border-color: #0D631B;
}

.badge-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
}

.bg-green-subtle { background: rgba(46, 125, 50, 0.15); }
.text-green-dark { color: #0D631B; }
.bg-orange-subtle { background: rgba(180, 80, 0, 0.12); }
.text-orange { color: #B45000; }
.bg-gray-subtle { background: rgba(112, 122, 108, 0.12); }
.text-muted { color: var(--color-text-muted); }

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 16px;
}

.admin-modal-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 28px;
  width: 100%;
  max-width: 480px;
  }

.modal-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.modal-title {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-title);
}

.btn-close-modal {
  background: transparent;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: var(--color-text-muted);
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-title);
}

.modern-input {
  padding: 10px 14px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  background: var(--color-bg);
  color: var(--color-text-title);
  outline: none;
  transition: all 0.2s ease;
}

select.modern-input {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px 16px;
  padding-right: 42px;
  cursor: pointer;
}

.modern-input:focus {
  border-color: #0D631B;
  outline: 2px solid rgba(13, 99, 27, 0.2) !important;
  outline-offset: 1px;
}

.modal-foot {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}

@media (max-width: 900px) {
  .header-action-row { flex-direction: column; align-items: stretch; gap: 12px; }
}

@media (max-width: 768px) {
  .admin-content {
    padding: 0;
    gap: 16px;
  }
  .page-title {
    font-size: 22px;
  }
  .page-subtitle {
    font-size: 13.5px;
  }
}

/* Page Transition for Skeleton */
.fade-admin-users-enter-active,
.fade-admin-users-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-admin-users-enter-from {
  opacity: 0;
  transform: translateY(6px);
}

.fade-admin-users-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
