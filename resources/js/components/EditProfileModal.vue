<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content-card">
      <!-- Modal Header -->
      <div class="modal-header">
        <div class="header-text-group">
          <h3 class="modal-title">{{ currentLang === 'id' ? 'Edit Profil Pengguna' : 'Edit User Profile' }}</h3>
          <p class="modal-subtitle">{{ currentLang === 'id' ? 'Perbarui informasi akun dan foto profil penanggung jawab' : 'Update account information and profile photo' }}</p>
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
        <!-- Avatar Upload & Preview Section -->
        <div class="avatar-edit-card" :class="{ 'is-dragging': isDragging }" @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop">
          <div class="avatar-preview-wrap" @click="triggerFileInput" title="Klik untuk mengganti foto profil">
            <div class="avatar-large">
              <img v-if="formData.avatarUrl" :src="formData.avatarUrl" alt="Avatar" class="avatar-preview-img" />
              <span v-else class="avatar-initials-text">{{ userInitials }}</span>
            </div>
            <div class="avatar-camera-badge" :title="currentLang === 'id' ? 'Ubah foto profil' : 'Change photo'">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </div>
          </div>

          <div class="avatar-meta-col">
            <div class="avatar-title-row">
              <span class="avatar-meta-title">{{ currentLang === 'id' ? 'Foto Profil & Inisial' : 'Profile Picture & Initials' }}</span>
              <span v-if="formData.avatarUrl" class="badge-custom-photo">{{ currentLang === 'id' ? 'Foto Kustom' : 'Custom Photo' }}</span>
              <span v-else class="badge-auto-initials">{{ currentLang === 'id' ? 'Inisial Otomatis' : 'Auto Initials' }}</span>
            </div>
            
            <p class="avatar-meta-sub">
              {{ formData.avatarUrl 
                ? (currentLang === 'id' ? 'Foto profil aktif. Anda dapat mengganti atau menghapusnya.' : 'Active custom photo. You can change or remove it.')
                : (currentLang === 'id' ? 'Unggah gambar JPG/PNG/WEBP (Maks. 3MB) atau gunakan inisial.' : 'Upload JPG/PNG/WEBP image (Max 3MB) or use initials.') 
              }}
            </p>

            <!-- Action Buttons for Image Upload -->
            <div class="avatar-actions-group">
              <button type="button" class="btn-avatar-upload" @click="triggerFileInput">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>{{ formData.avatarUrl ? (currentLang === 'id' ? 'Ganti Foto' : 'Change Photo') : (currentLang === 'id' ? 'Pilih Gambar' : 'Upload Image') }}</span>
              </button>

              <button v-if="formData.avatarUrl" type="button" class="btn-avatar-remove" @click="removeAvatar">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                <span>{{ currentLang === 'id' ? 'Hapus Foto' : 'Remove' }}</span>
              </button>
            </div>

            <!-- Hidden Native File Input -->
            <input 
              type="file" 
              ref="fileInputRef" 
              accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" 
              class="hidden-file-input" 
              @change="handleFileChange" 
            />

            <!-- Error message if any -->
            <span v-if="uploadError" class="avatar-upload-error">{{ uploadError }}</span>
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
            placeholder="Jl. Pamoyan, Waluran Mandiri, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat 43175, Indonesia" 
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
      location: 'Jl. Pamoyan, Waluran Mandiri, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat 43175, Indonesia',
      avatarUrl: ''
    })
  }
})

const emit = defineEmits(['close', 'save'])

const fileInputRef = ref(null)
const isDragging = ref(false)
const uploadError = ref('')

const formData = ref({
  name: props.user?.name || '',
  email: props.user?.email || '',
  phone: props.user?.phone || '',
  role: props.user?.role || '',
  location: props.user?.location || '',
  avatarUrl: props.user?.avatarUrl || props.user?.avatar || ''
})

watch(() => props.user, (newUser) => {
  if (newUser) {
    formData.value = { 
      name: newUser.name || '',
      email: newUser.email || '',
      phone: newUser.phone || '',
      role: newUser.role || '',
      location: newUser.location || '',
      avatarUrl: newUser.avatarUrl || newUser.avatar || ''
    }
  }
}, { deep: true, immediate: true })

const userInitials = computed(() => {
  if (!formData.value.name) return 'PA'
  const parts = formData.value.name.trim().split(' ').filter(Boolean)
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return parts[0].slice(0, 2).toUpperCase()
})

function triggerFileInput() {
  uploadError.value = ''
  if (fileInputRef.value) {
    fileInputRef.value.click()
  }
}

function handleDrop(e) {
  isDragging.value = false
  uploadError.value = ''
  const files = e.dataTransfer?.files
  if (files && files.length > 0) {
    processImageFile(files[0])
  }
}

function handleFileChange(event) {
  const file = event.target.files?.[0]
  if (file) {
    processImageFile(file)
  }
  // Reset native input so selecting the same file triggers change
  event.target.value = ''
}

function processImageFile(file) {
  uploadError.value = ''
  
  // Validation: Must be image
  if (!file.type.startsWith('image/')) {
    uploadError.value = props.currentLang === 'id' ? 'Berkas harus berupa gambar (JPG, PNG, WEBP).' : 'File must be an image.'
    return
  }

  // Validation: Max 4MB
  if (file.size > 4 * 1024 * 1024) {
    uploadError.value = props.currentLang === 'id' ? 'Ukuran gambar maksimal 4MB.' : 'Maximum image size is 4MB.'
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const rawDataUrl = e.target?.result
    if (!rawDataUrl) return

    // Compress & downscale image using HTML5 Canvas for optimal performance
    compressAndSetImage(rawDataUrl)
  }
  reader.onerror = () => {
    uploadError.value = props.currentLang === 'id' ? 'Gagal membaca berkas gambar.' : 'Failed to read image file.'
  }
  reader.readAsDataURL(file)
}

function compressAndSetImage(dataUrl) {
  const img = new Image()
  img.onload = () => {
    const maxDimension = 480
    let { width, height } = img

    if (width > maxDimension || height > maxDimension) {
      if (width > height) {
        height = Math.round((height * maxDimension) / width)
        width = maxDimension
      } else {
        width = Math.round((width * maxDimension) / height)
        height = maxDimension
      }
    }

    const canvas = document.createElement('canvas')
    canvas.width = width
    canvas.height = height
    const ctx = canvas.getContext('2d')
    ctx.drawImage(img, 0, 0, width, height)

    // Export as clean WebP/JPEG data url
    const optimizedDataUrl = canvas.toDataURL('image/jpeg', 0.88)
    formData.value.avatarUrl = optimizedDataUrl
  }
  img.onerror = () => {
    formData.value.avatarUrl = dataUrl
  }
  img.src = dataUrl
}

function removeAvatar() {
  formData.value.avatarUrl = ''
  uploadError.value = ''
}

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
  max-width: 540px;
  background: var(--color-white);
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid var(--color-border);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  animation: slideUp 0.2s ease;
}

:global(.dark-theme) .modal-content-card {
  background: #0B242F !important;
  border-color: #1E4E61 !important;
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
  border-bottom: 1px solid var(--color-border-subtle);
}

:global(.dark-theme) .modal-header {
  border-bottom-color: rgba(30, 78, 97, 0.5);
}

.header-text-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.modal-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-title);
}

:global(.dark-theme) .modal-title {
  color: #FFFFFF !important;
}

.modal-subtitle {
  font-size: 13px;
  color: var(--color-text-muted);
}

:global(.dark-theme) .modal-subtitle {
  color: #94A3B8 !important;
}

.modal-close-btn {
  background: transparent;
  color: var(--color-text-muted);
  padding: 4px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.modal-close-btn:hover {
  background: var(--color-bg-light);
  color: var(--color-text-title);
}

:global(.dark-theme) .modal-close-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

.modal-form {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  max-height: 80vh;
  overflow-y: auto;
}

/* Avatar Upload Card */
.avatar-edit-card {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 16px;
  background: var(--color-bg-light);
  border: 1.5px dashed var(--color-border);
  border-radius: 14px;
  transition: all 0.2s ease;
}

:global(.dark-theme) .avatar-edit-card {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
}

.avatar-edit-card.is-dragging {
  border-color: #0D631B;
  background: rgba(13, 99, 27, 0.05);
}

:global(.dark-theme) .avatar-edit-card.is-dragging {
  border-color: #4ADE80;
  background: rgba(74, 222, 128, 0.1);
}

.avatar-preview-wrap {
  position: relative;
  cursor: pointer;
  flex-shrink: 0;
}

.avatar-large {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2E7D32 0%, #0D631B 100%);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(13, 99, 27, 0.2);
  border: 2px solid #FFFFFF;
  transition: transform 0.2s ease;
}

:global(.dark-theme) .avatar-large {
  border-color: #0B242F;
}

.avatar-preview-wrap:hover .avatar-large {
  transform: scale(1.04);
}

.avatar-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-initials-text {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.avatar-camera-badge {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #0D631B;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #FFFFFF;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
  transition: background-color 0.2s;
}

:global(.dark-theme) .avatar-camera-badge {
  background: #2E7D32;
  border-color: #0B242F;
}

.avatar-preview-wrap:hover .avatar-camera-badge {
  background: #2E7D32;
}

.avatar-meta-col {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.avatar-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.avatar-meta-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
}

:global(.dark-theme) .avatar-meta-title {
  color: #FFFFFF !important;
}

.badge-custom-photo {
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 6px;
  background: rgba(13, 99, 27, 0.12);
  color: #0D631B;
}

:global(.dark-theme) .badge-custom-photo {
  background: rgba(74, 222, 128, 0.2);
  color: #4ADE80;
}

.badge-auto-initials {
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 6px;
  background: var(--color-border-subtle);
  color: var(--color-text-muted);
}

:global(.dark-theme) .badge-auto-initials {
  background: rgba(255, 255, 255, 0.1);
  color: #94A3B8;
}

.avatar-meta-sub {
  font-size: 12px;
  color: var(--color-text-muted);
  line-height: 1.4;
  margin: 0;
}

:global(.dark-theme) .avatar-meta-sub {
  color: #94A3B8 !important;
}

.avatar-actions-group {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 6px;
  flex-wrap: wrap;
}

.btn-avatar-upload {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  border: none;
}

.btn-avatar-upload:hover {
  background: #2E7D32;
}

.btn-avatar-remove {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 10px;
  background: #FEE2E2;
  color: #DC2626;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

:global(.dark-theme) .btn-avatar-remove {
  background: rgba(239, 68, 68, 0.2);
  color: #FCA5A5;
}

.btn-avatar-remove:hover {
  background: #FCA5A5;
  color: #991B1B;
}

.hidden-file-input {
  display: none;
}

.avatar-upload-error {
  font-size: 11px;
  color: #DC2626;
  font-weight: 600;
  margin-top: 2px;
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
  color: var(--color-text-title);
}

:global(.dark-theme) .form-label {
  color: #E2E8F0 !important;
}

.modal-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  color: var(--color-text-title);
  background: var(--color-bg-light);
  outline: none;
  transition: all 0.2s;
}

:global(.dark-theme) .modal-input {
  background: #071E27 !important;
  border-color: #1E4E61 !important;
  color: #FFFFFF !important;
}

.modal-input:focus {
  border-color: #0D631B;
  outline: 2px solid rgba(13, 99, 27, 0.2) !important;
  outline-offset: 1px;
  background: var(--color-white);
}

:global(.dark-theme) .modal-input:focus {
  border-color: #4ADE80 !important;
  outline-color: rgba(74, 222, 128, 0.25) !important;
  background: #071E27 !important;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  padding-top: 10px;
  border-top: 1px solid var(--color-border-subtle);
}

:global(.dark-theme) .modal-footer {
  border-top-color: rgba(30, 78, 97, 0.5);
}

.btn-cancel {
  padding: 10px 16px;
  background: var(--color-bg-light);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s;
}

:global(.dark-theme) .btn-cancel {
  background: #0E2C39 !important;
  border-color: #1E4E61 !important;
  color: #CBD5E1 !important;
}

.btn-cancel:hover {
  background: #E2E8F0;
  color: #1E293B;
}

:global(.dark-theme) .btn-cancel:hover {
  background: #133947 !important;
  color: #FFFFFF !important;
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
  cursor: pointer;
  transition: background-color 0.2s;
  border: none;
}

.btn-save:hover {
  background: #2E7D32;
}

@media (max-width: 520px) {
  .form-grid-2 {
    grid-template-columns: 1fr;
  }
  .avatar-edit-card {
    flex-direction: column;
    text-align: center;
    align-items: center;
  }
  .avatar-title-row {
    justify-content: center;
  }
  .avatar-actions-group {
    justify-content: center;
  }
}
</style>

