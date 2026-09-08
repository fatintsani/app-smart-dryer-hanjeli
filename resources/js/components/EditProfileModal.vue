<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content-card">
      <!-- Modal Header -->
      <div class="modal-header">
        <div class="header-text-group">
          <h3 class="modal-title">{{ currentLang === 'id' ? 'Edit Profil Pengguna' : 'Edit User Profile' }}</h3>
          <p class="modal-subtitle">{{ currentLang === 'id' ? 'Perbarui informasi akun dan penanggung jawab pengeringan' : 'Update account information and dryer operator' }}</p>
        </div>
        <button class="modal-close-btn" @click="$emit('close')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Modal Body Form -->
      <form @submit.prevent="handleSubmit" class="modal-form">
        <!-- Avatar Preview Row -->
        <div class="avatar-edit-row">
          <div class="avatar-large">
            <span>{{ userInitials }}</span>
          </div>
          <div class="avatar-meta">
            <span class="avatar-meta-title">{{ currentLang === 'id' ? 'Foto Profil & Inisial' : 'Profile Picture & Initials' }}</span>
            <span class="avatar-meta-sub">{{ currentLang === 'id' ? 'Otomatis di-generate dari nama lengkap' : 'Auto-generated from full name' }}</span>
          </div>
        </div>

        <div class="form-grid-2">
          <!-- Full Name -->
          <div class="input-group">
            <label class="form-label">{{ currentLang === 'id' ? 'Nama Lengkap' : 'Full Name' }}</label>
            <input 
              type="text" 
              v-model="formData.name" 
              class="modal-input" 
              required 
            />
          </div>

          <!-- Role -->
          <div class="input-group">
            <label class="form-label">{{ currentLang === 'id' ? 'Peran / Jabatan' : 'Role / Position' }}</label>
            <input 
              type="text" 
              v-model="formData.role" 
              class="modal-input" 
              required 
            />
          </div>
        </div>

        <div class="form-grid-2">
          <!-- Email -->
          <div class="input-group">
            <label class="form-label">Email</label>
            <input 
              type="email" 
              v-model="formData.email" 
              class="modal-input" 
              required 
            />
          </div>

          <!-- Phone Number -->
          <div class="input-group">
            <label class="form-label">{{ currentLang === 'id' ? 'Nomor WhatsApp / HP' : 'WhatsApp Number' }}</label>
            <input 
              type="tel" 
              v-model="formData.phone" 
              placeholder="+62 812-3456-7890" 
              class="modal-input" 
            />
          </div>
        </div>

        <!-- Location -->
        <div class="input-group">
          <label class="form-label">{{ currentLang === 'id' ? 'Lokasi Green House / Kebun' : 'Green House Location' }}</label>
          <input 
            type="text" 
            v-model="formData.location" 
            placeholder="Desa Wisata Waluran, Sukabumi" 
            class="modal-input" 
          />
        </div>

        <!-- Modal Footer Actions -->
        <div class="modal-footer">
          <button type="button" class="btn-cancel" @click="$emit('close')">
            {{ currentLang === 'id' ? 'Batal' : 'Cancel' }}
          </button>
          <button type="submit" class="btn-save">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>{{ currentLang === 'id' ? 'Simpan Perubahan' : 'Save Changes' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: {
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
      name: 'Operator Green House',
      email: 'operator@hanjeli.id',
      phone: '+62 812-3456-7890',
      role: 'Operator Green House',
      location: 'Desa Wisata Waluran, Sukabumi'
    })
  }
})

const emit = defineEmits(['close', 'save'])

const formData = ref({
  name: props.user.name || '',
  email: props.user.email || '',
  phone: props.user.phone || '',
  role: props.user.role || '',
  location: props.user.location || ''
})

watch(() => props.user, (newUser) => {
  if (newUser) {
    formData.value = { ...newUser }
  }
}, { deep: true, immediate: true })

const userInitials = computed(() => {
  if (!formData.value.name) return 'PA'
  const parts = formData.value.name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return parts[0].slice(0, 2).toUpperCase()
})

function handleSubmit() {
  emit('save', { ...formData.value })
  emit('close')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(7, 30, 39, 0.65);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content-card {
  width: 100%;
  max-width: 520px;
  background: #FFFFFF;
  border-radius: 16px;
    display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #E2E8F0;
  animation: slideUp 0.2s ease;
}

@keyframes slideUp {
  from { transform: translateY(16px); opacity: 0.8; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  border-bottom: 1px solid #F1F5F9;
}

.header-text-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.modal-title {
  font-size: 18px;
  font-weight: 700;
  color: #071E27;
}

.modal-subtitle {
  font-size: 13px;
  color: #64748B;
}

.modal-close-btn {
  background: transparent;
  color: #94A3B8;
  padding: 4px;
  border-radius: 6px;
  cursor: pointer;
}

.modal-close-btn:hover {
  background: #F1F5F9;
  color: #0F172A;
}

.modal-form {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.avatar-edit-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
}

.avatar-large {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2E7D32 0%, #0D631B 100%);
  color: #FFFFFF;
  font-size: 18px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
    flex-shrink: 0;
}

.avatar-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.avatar-meta-title {
  font-size: 13px;
  font-weight: 700;
  color: #071E27;
}

.avatar-meta-sub {
  font-size: 11px;
  color: #64748B;
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 12px;
  font-weight: 600;
  color: #334155;
}

.modal-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 14px;
  color: #071E27;
  background: #FFFFFF;
  outline: none;
  transition: all 0.2s;
}

.modal-input:focus {
  border-color: #0D631B;
  }

.modal-footer {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  padding-top: 10px;
  border-top: 1px solid #F1F5F9;
}

.btn-cancel {
  padding: 10px 16px;
  background: #F1F5F9;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
}

.btn-cancel:hover {
  background: #E2E8F0;
  color: #1E293B;
}

.btn-save {
  padding: 10px 18px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-save:hover {
  background: #2E7D32;
}

@media (max-width: 520px) {
  .form-grid-2 {
    grid-template-columns: 1fr;
  }
}
</style>
