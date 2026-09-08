<template>
  <div class="forgot-view-page-root">
    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="desktop-forgot-container">
    <!-- Left Hero Banner (Matches 1-Screen Login Branding) -->
    <div class="forgot-banner">
      <div class="banner-overlay"></div>

      <!-- Center Emblem Graphic -->
      <div class="banner-center-emblem">
        <div class="emblem-card">
          <HanjeliLogo :size="130" />
        </div>
        <div class="emblem-meta">
          <h3 class="emblem-org">Desa Wisata Hanjeli</h3>
          <p class="emblem-loc">Waluran, Sukabumi - Jawa Barat</p>
        </div>
      </div>

      <!-- Bottom Tagline & STAS Credit -->
      <div class="banner-bottom">
        <div class="eco-tag-row">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CBFFC2" stroke-width="2.2" class="eco-icon">
            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
          </svg>
          <div class="banner-headline-wrap">
            <h4 class="banner-headline-main">Pemulihan Keamanan Akun</h4>
            <p class="banner-headline-sub">Teknologi IoT cerdas pengering Hanjeli.</p>
          </div>
        </div>
        
        <div class="banner-stas-card">
          <StasLogo :size="28" />
          <div class="stas-card-text">
            <span class="stas-lead">Hardware & App by</span>
            <strong class="stas-name">Center of Excellence STAS-RG</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Content Area -->
    <div class="forgot-form-side">
      <div class="form-wrapper">
        <!-- Back to Login Header Button -->
        <button class="back-link-btn" @click="$emit('back-to-login')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>{{ $t('auth.backToLogin') }}</span>
        </button>

        <!-- Brand Dual Logo -->
        <div class="brand-dual-logo">
          <HanjeliLogo :size="38" />
          <div class="brand-divider"></div>
          <StasLogo :size="30" />
        </div>

        <!-- Feedback Banners -->
        <div v-if="errorMessage" class="feedback-banner error-banner">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="error-icon">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div class="feedback-text">
            <strong>Gagal Memproses</strong>
            <span>{{ errorMessage }}</span>
          </div>
        </div>

        <div v-if="successMessage" class="feedback-banner success-banner">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <div class="feedback-text">
            <strong>Berhasil</strong>
            <span>{{ successMessage }}</span>
          </div>
        </div>

        <!-- Multi-Step Wizard with Transitions -->
        <Transition name="framer-step" mode="out-in">
          <!-- STEP 1: Input Email -->
          <div v-if="currentStep === 1" key="step1" class="step-card">
            <div class="step-header">
              <h1 class="step-title">{{ $t('auth.forgotTitle') }}</h1>
              <p class="step-subtitle">
                {{ $t('auth.forgotSubtitle') }}
              </p>
            </div>

            <form @submit.prevent="handleSendEmail" class="step-form">
              <div class="input-group">
                <label class="input-label">{{ $t('auth.email') }}</label>
                <div class="input-field-wrapper">
                  <span class="field-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                      <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                  </span>
                  <input 
                    type="email" 
                    v-model="resetEmail" 
                    placeholder="nama@domain.com" 
                    class="text-input" 
                    required 
                  />
                </div>
              </div>

              <button type="submit" class="submit-action-btn" :disabled="isLoading">
                <span v-if="!isLoading">Kirim Kode Verifikasi</span>
                <span v-else>Mengirimkan...</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </form>
          </div>

          <!-- STEP 2: Input OTP Verification Code -->
          <div v-else-if="currentStep === 2" key="step2" class="step-card">
            <div class="step-header">
              <div class="step-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                  <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                </svg>
              </div>
              <h1 class="step-title">Verifikasi Kode Keamanan</h1>
              <p class="step-subtitle">
                Kode 6-digit telah dikirimkan ke <strong>{{ resetEmail }}</strong>.
              </p>
            </div>

            <form @submit.prevent="handleVerifyOtp" class="step-form">
              <!-- 6 OTP Input Boxes -->
              <div class="otp-boxes-row" @paste="handleOtpPaste">
                <input 
                  v-for="(digit, idx) in otpDigits" 
                  :key="idx"
                  :ref="el => { otpInputRefs[idx] = el }"
                  type="text" 
                  inputmode="numeric"
                  maxlength="1"
                  v-model="otpDigits[idx]"
                  class="otp-digit-box"
                  @input="handleOtpInput(idx, $event)"
                  @keydown="handleOtpKeydown(idx, $event)"
                  required
                />
              </div>

              <div class="resend-row">
                <span v-if="resendCountdown > 0" class="countdown-text">
                  Kirim ulang kode dalam <strong>00:{{ resendCountdown < 10 ? '0' + resendCountdown : resendCountdown }}</strong>
                </span>
                <button v-else type="button" class="resend-btn" @click="handleResendCode">
                  Kirim Ulang Kode OTP
                </button>
              </div>

              <button type="submit" class="submit-action-btn" :disabled="isOtpIncomplete || isLoading">
                <span v-if="!isLoading">Verifikasi & Lanjutkan</span>
                <span v-else>Memverifikasi...</span>
              </button>
            </form>
          </div>

          <!-- STEP 3: Create New Password -->
          <div v-else-if="currentStep === 3" key="step3" class="step-card">
            <div class="step-header">
              <h1 class="step-title">Buat Kata Sandi Baru</h1>
              <p class="step-subtitle">
                Pastikan kata sandi baru Anda kuat dan belum pernah digunakan sebelumnya.
              </p>
            </div>

            <form @submit.prevent="handleSaveNewPassword" class="step-form">
              <!-- New Password -->
              <div class="input-group">
                <label class="input-label">Kata Sandi Baru</label>
                <div class="input-field-wrapper">
                  <span class="field-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                      <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                  </span>
                  <input 
                    :type="showNewPassword ? 'text' : 'password'" 
                    v-model="newPassword" 
                    placeholder="Minimal 6 karakter kombinasi" 
                    class="text-input" 
                    required 
                  />
                  <button type="button" class="toggle-pwd-btn" @click="showNewPassword = !showNewPassword">
                    <svg v-if="!showNewPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                      <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                      <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                      <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                      <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                      <line x1="2" x2="22" y1="2" y2="22"></line>
                    </svg>
                  </button>
                </div>

                <!-- Password Strength Meter -->
                <div class="pwd-strength-container">
                  <div class="strength-bar-track">
                    <div class="strength-bar-fill" :class="passwordStrengthClass" :style="{ width: passwordStrengthPercent }"></div>
                  </div>
                  <span class="strength-label" :class="passwordStrengthClass">{{ passwordStrengthText }}</span>
                </div>
              </div>

              <!-- Confirm New Password -->
              <div class="input-group">
                <label class="input-label">Konfirmasi Kata Sandi Baru</label>
                <div class="input-field-wrapper">
                  <span class="field-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </span>
                  <input 
                    :type="showNewPassword ? 'text' : 'password'" 
                    v-model="confirmNewPassword" 
                    placeholder="Ulangi kata sandi baru" 
                    class="text-input" 
                    required 
                  />
                </div>
                <span v-if="confirmNewPassword && newPassword !== confirmNewPassword" class="error-text">
                  Kata sandi tidak cocok.
                </span>
              </div>

              <button 
                type="submit" 
                class="submit-action-btn" 
                :disabled="!isPasswordValid || isLoading"
              >
                <span v-if="!isLoading">Simpan Kata Sandi Baru</span>
                <span v-else>Menyimpan...</span>
              </button>
            </form>
          </div>

          <!-- STEP 4: Success Message -->
          <div v-else-if="currentStep === 4" key="step4" class="step-card success-card">
            <div class="success-icon-box">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>

            <h1 class="step-title">Kata Sandi Berhasil Diperbarui!</h1>
            <p class="step-subtitle">
              Kata sandi akun Anda telah sukses diubah. Silakan masuk menggunakan kata sandi baru Anda.
            </p>

            <button class="submit-action-btn" @click="$emit('back-to-login')">
              <span>Masuk ke Akun Sekarang</span>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </div>
        </Transition>

        <!-- Compact Footer -->
        <div class="security-footer">
          <div class="footer-collab-row">
            <span>Desa Wisata Hanjeli</span>
            <span class="dot-sep">•</span>
            <span>CoE STAS-RG</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile Layout -->
  <div v-else class="mobile-forgot-container">
    <div class="mobile-forgot-card">
      <button class="m-back-btn" @click="$emit('back-to-login')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Login</span>
      </button>

      <!-- Dual Brand Center -->
      <div class="m-brand-center">
        <div class="m-dual-logo-box">
          <HanjeliLogo :size="48" />
          <div class="m-logo-divider"></div>
          <StasLogo :size="36" />
        </div>
        <h1 class="m-brand-title">Smart Room Dryer</h1>
      </div>

      <!-- Feedback Banners Mobile -->
      <div v-if="errorMessage" class="feedback-banner error-banner">
        <span>{{ errorMessage }}</span>
      </div>
      <div v-if="successMessage" class="feedback-banner success-banner">
        <span>{{ successMessage }}</span>
      </div>

      <!-- Multi-step on Mobile -->
      <div v-if="currentStep === 1" class="m-step-block">
        <h2 class="m-step-title">Lupa Kata Sandi</h2>
        <p class="m-step-sub">Masukkan email Anda untuk menerima kode verifikasi.</p>

        <form @submit.prevent="handleSendEmail" class="m-form">
          <div class="input-group">
            <label class="input-label">Email</label>
            <input type="email" v-model="resetEmail" placeholder="nama@domain.com" class="text-input" required />
          </div>
          <button type="submit" class="submit-action-btn" :disabled="isLoading">
            {{ isLoading ? 'Mengirim...' : 'Kirim Kode Verifikasi' }}
          </button>
        </form>
      </div>

      <div v-else-if="currentStep === 2" class="m-step-block">
        <h2 class="m-step-title">Kode Verifikasi</h2>
        <p class="m-step-sub">Masukkan 6 digit kode dari email Anda.</p>

        <form @submit.prevent="handleVerifyOtp" class="m-form">
          <div class="otp-boxes-row" @paste="handleOtpPaste">
            <input 
              v-for="(digit, idx) in otpDigits" 
              :key="idx"
              :ref="el => { otpInputRefs[idx] = el }"
              type="text" 
              inputmode="numeric"
              maxlength="1"
              v-model="otpDigits[idx]"
              class="otp-digit-box"
              @input="handleOtpInput(idx, $event)"
              @keydown="handleOtpKeydown(idx, $event)"
              required
            />
          </div>
          <button type="submit" class="submit-action-btn" :disabled="isOtpIncomplete || isLoading">
            {{ isLoading ? 'Memverifikasi...' : 'Verifikasi Kode' }}
          </button>
        </form>
      </div>

      <div v-else-if="currentStep === 3" class="m-step-block">
        <h2 class="m-step-title">Sandi Baru</h2>
        <p class="m-step-sub">Masukkan kata sandi baru untuk akun Anda.</p>

        <form @submit.prevent="handleSaveNewPassword" class="m-form">
          <div class="input-group">
            <label class="input-label">Kata Sandi Baru</label>
            <input type="password" v-model="newPassword" placeholder="Minimal 6 karakter kombinasi" class="text-input" required />
          </div>
          <div class="input-group">
            <label class="input-label">Konfirmasi Sandi</label>
            <input type="password" v-model="confirmNewPassword" placeholder="Ulangi kata sandi baru" class="text-input" required />
          </div>
          <button type="submit" class="submit-action-btn" :disabled="!isPasswordValid || isLoading">
            {{ isLoading ? 'Menyimpan...' : 'Simpan Kata Sandi' }}
          </button>
        </form>
      </div>

      <div v-else-if="currentStep === 4" class="m-step-block success-block">
        <div class="success-icon-box">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <h2 class="m-step-title">Berhasil Diperbarui!</h2>
        <p class="m-step-sub">Silakan masuk dengan kata sandi baru Anda.</p>
        <button class="submit-action-btn" @click="$emit('back-to-login')">
          Masuk ke Akun
        </button>
      </div>

      <p class="mobile-dev-credit">Hardware & Application by CoE STAS-RG</p>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import HanjeliLogo from '../../components/HanjeliLogo.vue'
import StasLogo from '../../components/StasLogo.vue'
import { authService, formatAuthError } from '../../services/authService'

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['back-to-login'])

const currentStep = ref(1) // 1: Email, 2: OTP, 3: New Password, 4: Success
const resetEmail = ref('')
const resetToken = ref('')
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const otpDigits = ref(['', '', '', '', '', ''])
const otpInputRefs = ref([])
const resendCountdown = ref(45)
let countdownTimer = null

const newPassword = ref('')
const confirmNewPassword = ref('')
const showNewPassword = ref(false)

const isOtpIncomplete = computed(() => {
  return otpDigits.value.some(d => !d)
})

const passwordStrengthPercent = computed(() => {
  if (!newPassword.value) return '0%'
  let score = 0
  if (newPassword.value.length >= 8) score += 40
  if (/[0-9]/.test(newPassword.value)) score += 30
  if (/[A-Z]/.test(newPassword.value) || /[^A-Za-z0-9]/.test(newPassword.value)) score += 30
  return `${score}%`
})

const passwordStrengthClass = computed(() => {
  const len = newPassword.value.length
  if (len === 0) return ''
  if (len < 6) return 'weak'
  if (len < 8) return 'medium'
  return 'strong'
})

const passwordStrengthText = computed(() => {
  if (!newPassword.value) return 'Kekuatan Kata Sandi'
  if (newPassword.value.length < 6) return 'Lemah'
  if (newPassword.value.length < 8) return 'Sedang'
  return 'Kuat'
})

const isPasswordValid = computed(() => {
  return newPassword.value.length >= 6 && newPassword.value === confirmNewPassword.value
})

function clearBanners() {
  errorMessage.value = ''
  successMessage.value = ''
}

function startCountdown() {
  resendCountdown.value = 60
  if (countdownTimer) clearInterval(countdownTimer)
  countdownTimer = setInterval(() => {
    if (resendCountdown.value > 0) {
      resendCountdown.value--
    } else {
      clearInterval(countdownTimer)
    }
  }, 1000)
}

// 1. Send OTP to User's Email (Mailpit)
async function handleSendEmail() {
  clearBanners()
  if (!resetEmail.value) {
    errorMessage.value = 'Silakan masukkan alamat email akun Anda.'
    return
  }

  isLoading.value = true
  try {
    const res = await authService.forgotPassword(resetEmail.value)
    successMessage.value = res.message || 'Kode OTP telah dikirimkan ke email Anda.'
    currentStep.value = 2
    otpDigits.value = ['', '', '', '', '', '']
    startCountdown()
    nextTick(() => {
      if (otpInputRefs.value[0]) otpInputRefs.value[0].focus()
    })
  } catch (err) {
    console.error('Forgot password send error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

function handleOtpInput(idx, event) {
  clearBanners()
  const val = event.target.value
  const cleanVal = val.replace(/\s/g, '')
  if (cleanVal.length > 1) {
    const chars = cleanVal.slice(0, 6).split('')
    chars.forEach((c, i) => {
      if (idx + i < 6) {
        otpDigits.value[idx + i] = c
      }
    })
    const nextIdx = Math.min(idx + chars.length, 5)
    nextTick(() => {
      if (otpInputRefs.value[nextIdx]) {
        otpInputRefs.value[nextIdx].focus()
      }
    })
    return
  }
  otpDigits.value[idx] = cleanVal
  if (cleanVal && idx < 5) {
    nextTick(() => {
      if (otpInputRefs.value[idx + 1]) {
        otpInputRefs.value[idx + 1].focus()
      }
    })
  }
}

function handleOtpPaste(event) {
  clearBanners()
  event.preventDefault()
  const clipboardData = event.clipboardData || window.clipboardData
  if (!clipboardData) return
  const pasted = clipboardData.getData('text').trim()
  const chars = pasted.slice(0, 6).split('')
  chars.forEach((c, i) => {
    otpDigits.value[i] = c
  })
  const nextIdx = Math.min(chars.length, 5)
  nextTick(() => {
    if (otpInputRefs.value[nextIdx]) {
      otpInputRefs.value[nextIdx].focus()
    }
  })
}

function handleOtpKeydown(idx, event) {
  clearBanners()
  if (event.key === 'Backspace') {
    if (!otpDigits.value[idx] && idx > 0) {
      nextTick(() => {
        if (otpInputRefs.value[idx - 1]) {
          otpInputRefs.value[idx - 1].focus()
        }
      })
    }
  }
}

// 2. Resend OTP code
async function handleResendCode() {
  clearBanners()
  isLoading.value = true
  try {
    const res = await authService.forgotPassword(resetEmail.value)
    successMessage.value = res.message || 'Kode OTP baru telah dikirimkan ke email Anda.'
    startCountdown()
  } catch (err) {
    console.error('Resend OTP error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

// 3. Verify OTP code
async function handleVerifyOtp() {
  clearBanners()
  const otpCode = otpDigits.value.join('')
  if (otpCode.length !== 6) {
    errorMessage.value = 'Silakan masukkan 6 digit kode verifikasi secara lengkap.'
    return
  }

  isLoading.value = true
  try {
    const res = await authService.verifyOtp(resetEmail.value, otpCode)
    resetToken.value = res.resetToken || ''
    successMessage.value = 'Kode OTP berhasil diverifikasi.'
    currentStep.value = 3
  } catch (err) {
    console.error('Verify OTP error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

// 4. Save new password
async function handleSaveNewPassword() {
  clearBanners()
  if (!isPasswordValid.value) {
    errorMessage.value = 'Pastikan kata sandi minimal 6 karakter dan konfirmasi kata sandi cocok.'
    return
  }

  isLoading.value = true
  try {
    await authService.resetPassword({
      token: resetToken.value,
      email: resetEmail.value,
      otp: otpDigits.value.join(''),
      newPassword: newPassword.value,
    })
    successMessage.value = 'Kata sandi berhasil diperbarui.'
    currentStep.value = 4
  } catch (err) {
    console.error('Reset password save error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Feedback Banners */
.feedback-banner {
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 12.5px;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  line-height: 17px;
  margin-bottom: 14px;
}

.feedback-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.error-banner {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  color: #DC2626;
}

.error-icon {
  flex-shrink: 0;
  margin-top: 1px;
}

.success-banner {
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  color: #16A34A;
}

/* Desktop Layout */
.desktop-forgot-container {
  display: flex;
  width: 100%;
  height: 100vh;
  max-height: 100vh;
  overflow: hidden;
  background: #FFFFFF;
}

.forgot-banner {
  flex: 1;
  height: 100vh;
  background: linear-gradient(135deg, #1B5E20 0%, #0D631B 50%, #073810 100%);
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 32px 36px;
  overflow: hidden;
  color: white;
}

.banner-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.12), transparent 70%);
  pointer-events: none;
}

.banner-center-emblem {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 12px;
  flex: 1;
  z-index: 2;
  text-align: center;
}

.emblem-card {
  padding: 12px 16px;
  background: #FFFFFF;
  border-radius: 20px;
    display: flex;
  align-items: center;
  justify-content: center;
}

.emblem-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.emblem-org {
  font-size: 18px;
  font-weight: 700;
  color: #FFFFFF;
  letter-spacing: 0.3px;
}

.emblem-loc {
  font-size: 12px;
  color: #CBFFC2;
  opacity: 0.9;
}

.banner-bottom {
  max-width: 440px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 2;
}

.eco-tag-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.eco-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.banner-headline-wrap {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.banner-headline-main {
  font-size: 15px;
  font-weight: 700;
  color: #FFFFFF;
  line-height: 20px;
}

.banner-headline-sub {
  font-size: 12.5px;
  color: #CBFFC2;
  line-height: 17px;
  opacity: 0.92;
}

.banner-stas-card {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  padding: 8px 12px;
  border-radius: 10px;
}

.stas-card-text {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.stas-lead {
  font-size: 9.5px;
  color: #E2E8F0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stas-name {
  font-size: 12px;
  font-weight: 700;
  color: #CBFFC2;
}

/* Right Form Side */
.forgot-form-side {
  flex: 1;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 36px;
  background: #FFFFFF;
  overflow: hidden;
}

.form-wrapper {
  width: 100%;
  max-width: 380px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.back-link-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  border: none;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  cursor: pointer;
  padding: 0;
  margin-bottom: 2px;
  align-self: flex-start;
  transition: color 0.15s;
}

.back-link-btn:hover {
  color: #0D631B;
}

.brand-dual-logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-divider {
  width: 1px;
  height: 22px;
  background: #CBD5E1;
}

.step-card {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.step-header {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.step-title {
  font-size: 20px;
  font-weight: 800;
  color: #071E27;
  letter-spacing: -0.3px;
}

.step-subtitle {
  font-size: 12.5px;
  color: #64748B;
  line-height: 17px;
}

.step-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.input-label {
  font-size: 12px;
  font-weight: 600;
  color: #071E27;
}

.input-field-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.field-icon {
  position: absolute;
  left: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.text-input {
  width: 100%;
  padding: 8px 10px 8px 36px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 13.5px;
  color: #071E27;
  background: #F8FAFC;
  transition: all 0.2s;
  outline: none;
}

.text-input:focus {
  border-color: #0D631B;
  background: #FFFFFF;
  }

.toggle-pwd-btn {
  position: absolute;
  right: 10px;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.submit-action-btn {
  width: 100%;
  padding: 10px 14px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
  margin-top: 4px;
}

.submit-action-btn:hover:not(:disabled) {
  background: #2E7D32;
}

.submit-action-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* OTP boxes */
.otp-boxes-row {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  margin: 6px 0;
  box-sizing: border-box;
}

.otp-digit-box {
  flex: 1 1 0;
  width: 0;
  min-width: 0;
  max-width: 52px;
  height: 48px;
  text-align: center;
  font-size: 20px;
  font-weight: 700;
  border: 1.5px solid #CBD5E1;
  border-radius: 8px;
  background: #F8FAFC;
  outline: none;
  transition: all 0.2s;
  color: #071E27;
  padding: 0;
  box-sizing: border-box;
}

.otp-digit-box:focus {
  border-color: #0D631B;
  background: #FFFFFF;
  }

.resend-row {
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 12px;
}

.countdown-text {
  color: #64748B;
}

.resend-btn {
  background: transparent;
  color: #0D631B;
  font-weight: 700;
  border: none;
  cursor: pointer;
}

.resend-btn:hover {
  text-decoration: underline;
}

/* Password Strength Indicator */
.pwd-strength-container {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}

.strength-bar-track {
  flex: 1;
  height: 4px;
  background: #E2E8F0;
  border-radius: 4px;
  overflow: hidden;
}

.strength-bar-fill {
  height: 100%;
  transition: width 0.3s ease, background-color 0.3s ease;
}

.strength-bar-fill.weak { background-color: #EF4444; }
.strength-bar-fill.medium { background-color: #F59E0B; }
.strength-bar-fill.strong { background-color: #10B981; }

.strength-label {
  font-size: 10.5px;
  font-weight: 600;
}
.strength-label.weak { color: #EF4444; }
.strength-label.medium { color: #F59E0B; }
.strength-label.strong { color: #10B981; }

.error-text {
  font-size: 11px;
  color: #BA1A1A;
}

/* Success Card */
.success-card {
  align-items: center;
  text-align: center;
  padding: 10px 0;
}

.success-icon-box {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #E8F5E9;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 4px;
}

/* Transition between steps */
.framer-step-enter-active {
  transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.framer-step-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.framer-step-enter-from {
  opacity: 0;
  transform: translateY(12px) scale(0.98);
}
.framer-step-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.99);
}

.security-footer {
  margin-top: 2px;
  padding-top: 10px;
  border-top: 1px solid #F1F5F9;
  display: flex;
  justify-content: center;
}

.footer-collab-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #64748B;
}

.dot-sep {
  color: #CBD5E1;
}

/* Mobile Layout */
.mobile-forgot-container {
  min-height: 100vh;
  background: #EBF3F9;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.mobile-forgot-card {
  width: 100%;
  max-width: 360px;
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px 20px;
    display: flex;
  flex-direction: column;
  gap: 16px;
}

.m-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  border: none;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
  cursor: pointer;
  padding: 0;
}

.m-brand-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.m-dual-logo-box {
  display: flex;
  align-items: center;
  gap: 10px;
}

.m-logo-divider {
  width: 1px;
  height: 24px;
  background: #CBD5E1;
}

.m-brand-title {
  font-size: 18px;
  font-weight: 800;
  color: #0D631B;
}

.m-step-block {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.m-step-title {
  font-size: 17px;
  font-weight: 700;
  color: #071E27;
}

.m-step-sub {
  font-size: 12px;
  color: #64748B;
  line-height: 16px;
}

.m-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.success-block {
  align-items: center;
  text-align: center;
}

.mobile-dev-credit {
  text-align: center;
  font-size: 10.5px;
  color: #94A3B8;
  margin-top: 4px;
}
</style>
