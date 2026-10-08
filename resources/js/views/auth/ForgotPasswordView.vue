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
            <h3 class="emblem-org">{{ $t('common.facilityName') }}</h3>
            <p class="emblem-loc">{{ $t('common.facilityLoc') }}</p>
          </div>
        </div>

        <!-- Bottom Tagline & STAS Developer Credit -->
        <div class="banner-bottom">
          <div class="eco-tag-row">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CBFFC2" stroke-width="2.2" class="eco-icon">
              <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
              <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
            </svg>
            <div class="banner-headline-wrap">
              <h4 class="banner-headline-main">{{ $t('auth.heroTitle') }}</h4>
              <p class="banner-headline-sub">{{ $t('auth.heroSub') }}</p>
            </div>
          </div>
          
          <div class="banner-stas-card">
            <StasLogo :size="28" />
            <div class="stas-card-text">
              <span class="stas-lead">{{ $t('auth.hardwareBy') }}</span>
              <strong class="stas-name">{{ $t('auth.devCredit') }}</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Content Area -->
      <div class="forgot-form-side">
        <div class="form-wrapper">
          <!-- Top Row: Back button -->
          <div class="top-nav-row">
            <button class="back-link-btn" @click="$emit('back-to-login')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
              </svg>
              <span>{{ $t('auth.backToLogin') }}</span>
            </button>
          </div>

          <!-- Feedback Banners -->
          <div v-if="errorMessage" class="feedback-banner error-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="error-icon">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div class="feedback-text">
              <strong>{{ $t('common.error') }}</strong>
              <span>{{ errorMessage }}</span>
            </div>
          </div>

          <div v-if="successMessage" class="feedback-banner success-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div class="feedback-text">
              <strong>{{ $t('common.success') }}</strong>
              <span>{{ successMessage }}</span>
            </div>
          </div>

          <!-- Multi-Step Wizard with Transitions -->
          <Transition name="framer-step" mode="out-in">
            <!-- STEP 1: Input Email -->
            <div v-if="currentStep === 1" key="step1" class="step-card">
              <div class="step-header">
                <h1 class="step-title">{{ $t('auth.forgotStep1Title') }}</h1>
                <p class="step-subtitle">
                  {{ $t('auth.forgotStep1Desc') }}
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
                      :placeholder="$t('auth.emailPlaceholder')" 
                      class="text-input" 
                      required 
                    />
                  </div>
                </div>

                <button type="submit" class="submit-action-btn" :disabled="isLoading">
                  <span v-if="!isLoading">{{ $t('auth.sendResetLink') }}</span>
                  <span v-else>{{ $t('auth.sendingResetLink') }}</span>
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
                <h1 class="step-title">{{ $t('auth.forgotStep2Title') }}</h1>
                <p class="step-subtitle">
                  {{ $t('auth.forgotStep2Desc') }} (<strong>{{ resetEmail }}</strong>).
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
                    {{ $t('auth.resendOtpIn') }} <strong>00:{{ resendCountdown < 10 ? '0' + resendCountdown : resendCountdown }}</strong>
                  </span>
                  <button v-else type="button" class="resend-btn" @click="handleResendCode">
                    {{ $t('auth.resendOtp') }}
                  </button>
                </div>

                <button type="submit" class="submit-action-btn" :disabled="isOtpIncomplete || isLoading">
                  <span v-if="!isLoading">{{ $t('auth.verifyOtpBtn') }}</span>
                  <span v-else>{{ $t('auth.verifyingOtp') }}</span>
                </button>
              </form>
            </div>

            <!-- STEP 3: Create New Password -->
            <div v-else-if="currentStep === 3" key="step3" class="step-card">
              <div class="step-header">
                <h1 class="step-title">{{ $t('auth.forgotStep3Title') }}</h1>
                <p class="step-subtitle">
                  {{ $t('auth.forgotStep3Desc') }}
                </p>
              </div>

              <form @submit.prevent="handleSaveNewPassword" class="step-form">
                <!-- New Password -->
                <div class="input-group">
                  <label class="input-label">{{ $t('auth.newPassword') }}</label>
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
                      :placeholder="$t('auth.newPasswordPlaceholder')" 
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
                  <label class="input-label">{{ $t('auth.confirmPassword') }}</label>
                  <div class="input-field-wrapper">
                    <span class="field-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                    </span>
                    <input 
                      :type="showNewPassword ? 'text' : 'password'" 
                      v-model="confirmNewPassword" 
                      :placeholder="$t('auth.confirmPasswordPlaceholder')" 
                      class="text-input" 
                      required 
                    />
                  </div>
                  <span v-if="confirmNewPassword && newPassword !== confirmNewPassword" class="error-text">
                    {{ $t('auth.passwordMismatch') }}
                  </span>
                </div>

                <button 
                  type="submit" 
                  class="submit-action-btn" 
                  :disabled="!isPasswordValid || isLoading"
                >
                  <span v-if="!isLoading">{{ $t('auth.resetPasswordBtn') }}</span>
                  <span v-else>{{ $t('auth.resettingPassword') }}</span>
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

              <h1 class="step-title">{{ $t('auth.resetSuccessTitle') }}</h1>
              <p class="step-subtitle">
                {{ $t('auth.resetSuccessMsg') }}
              </p>

              <button class="submit-action-btn" @click="$emit('back-to-login')">
                <span>{{ $t('auth.backToLogin') }}</span>
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
              <span>{{ $t('common.facilityName') }}</span>
              <span class="dot-sep">•</span>
              <span>{{ $t('common.devCredit') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Layout -->
    <div v-else class="mobile-forgot-container">
      <div class="mobile-forgot-card">
        <div class="top-nav-row">
          <button class="m-back-btn" @click="$emit('back-to-login')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>{{ $t('auth.backToLogin') }}</span>
          </button>
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
          <h2 class="m-step-title">{{ $t('auth.forgotStep1Title') }}</h2>
          <p class="m-step-sub">{{ $t('auth.forgotStep1Desc') }}</p>

          <form @submit.prevent="handleSendEmail" class="m-form">
            <div class="input-group">
              <label class="input-label">{{ $t('auth.email') }}</label>
              <input type="email" v-model="resetEmail" :placeholder="$t('auth.emailPlaceholder')" class="text-input" required />
            </div>
            <button type="submit" class="submit-action-btn" :disabled="isLoading">
              {{ isLoading ? $t('auth.sendingResetLink') : $t('auth.sendResetLink') }}
            </button>
          </form>
        </div>

        <div v-else-if="currentStep === 2" class="m-step-block">
          <h2 class="m-step-title">{{ $t('auth.forgotStep2Title') }}</h2>
          <p class="m-step-sub">{{ $t('auth.forgotStep2Desc') }} ({{ resetEmail }})</p>

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
              {{ isLoading ? $t('auth.verifyingOtp') : $t('auth.verifyOtpBtn') }}
            </button>
          </form>
        </div>

        <div v-else-if="currentStep === 3" class="m-step-block">
          <h2 class="m-step-title">{{ $t('auth.forgotStep3Title') }}</h2>
          <p class="m-step-sub">{{ $t('auth.forgotStep3Desc') }}</p>

          <form @submit.prevent="handleSaveNewPassword" class="m-form">
            <div class="input-group">
              <label class="input-label">{{ $t('auth.newPassword') }}</label>
              <input type="password" v-model="newPassword" :placeholder="$t('auth.newPasswordPlaceholder')" class="text-input" required />
            </div>
            <div class="input-group">
              <label class="input-label">{{ $t('auth.confirmPassword') }}</label>
              <input type="password" v-model="confirmNewPassword" :placeholder="$t('auth.confirmPasswordPlaceholder')" class="text-input" required />
            </div>
            <button type="submit" class="submit-action-btn" :disabled="!isPasswordValid || isLoading">
              {{ isLoading ? $t('auth.resettingPassword') : $t('auth.resetPasswordBtn') }}
            </button>
          </form>
        </div>

        <div v-else-if="currentStep === 4" class="m-step-block success-block">
          <div class="success-icon-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <h2 class="m-step-title">{{ $t('auth.resetSuccessTitle') }}</h2>
          <p class="m-step-sub">{{ $t('auth.resetSuccessMsg') }}</p>
          <button class="submit-action-btn" @click="$emit('back-to-login')">
            {{ $t('auth.backToLogin') }}
          </button>
        </div>

        <p class="mobile-dev-credit">{{ $t('common.hardwareBy') }} {{ $t('common.devCredit') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import HanjeliLogo from '../../components/HanjeliLogo.vue'
import StasLogo from '../../components/StasLogo.vue'
import { authService, formatAuthError } from '../../services/authService'
import { currentLang, t } from '../../i18n'

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
  if (!newPassword.value) return currentLang.value === 'id' ? 'Kekuatan Kata Sandi' : 'Password Strength'
  if (newPassword.value.length < 6) return currentLang.value === 'id' ? 'Lemah' : 'Weak'
  if (newPassword.value.length < 8) return currentLang.value === 'id' ? 'Sedang' : 'Medium'
  return currentLang.value === 'id' ? 'Kuat' : 'Strong'
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

async function handleSendEmail() {
  clearBanners()
  if (!resetEmail.value) {
    errorMessage.value = t('auth.emailRequired', 'Silakan masukkan alamat email akun Anda.')
    return
  }

  isLoading.value = true
  try {
    const res = await authService.forgotPassword(resetEmail.value)
    successMessage.value = res.message || t('auth.forgotStep2Desc', 'Kode OTP telah dikirimkan ke email Anda.')
    currentStep.value = 2
    otpDigits.value = ['', '', '', '', '', '']
    startCountdown()
    nextTick(() => {
      if (otpInputRefs.value[0]) otpInputRefs.value[0].focus()
    })
  } catch (err) {
    console.error('Failed to send reset code:', err)
    errorMessage.value = formatAuthError(err) || (currentLang.value === 'id' ? 'Email tidak terdaftar atau terjadi gangguan koneksi.' : 'Email is not registered or network error.')
  } finally {
    isLoading.value = false
  }
}

function handleOtpInput(idx, event) {
  const val = event.target.value
  if (val && idx < 5) {
    nextTick(() => {
      if (otpInputRefs.value[idx + 1]) otpInputRefs.value[idx + 1].focus()
    })
  }
}

function handleOtpKeydown(idx, event) {
  if (event.key === 'Backspace' && !otpDigits.value[idx] && idx > 0) {
    nextTick(() => {
      if (otpInputRefs.value[idx - 1]) otpInputRefs.value[idx - 1].focus()
    })
  }
}

function handleOtpPaste(event) {
  event.preventDefault()
  const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim()
  if (/^\d{6}$/.test(pasteData)) {
    pasteData.split('').forEach((char, idx) => {
      otpDigits.value[idx] = char
    })
    nextTick(() => {
      if (otpInputRefs.value[5]) otpInputRefs.value[5].focus()
    })
  }
}

async function handleResendCode() {
  clearBanners()
  isLoading.value = true
  try {
    await authService.forgotPassword(resetEmail.value)
    successMessage.value = currentLang.value === 'id' ? 'Kode baru telah dikirimkan ke email Anda.' : 'A new OTP code has been sent to your email.'
    startCountdown()
  } catch (err) {
    errorMessage.value = formatAuthError(err) || (currentLang.value === 'id' ? 'Gagal mengirim ulang kode OTP.' : 'Failed to resend OTP code.')
  } finally {
    isLoading.value = false
  }
}

async function handleVerifyOtp() {
  clearBanners()
  const code = otpDigits.value.join('')
  if (code.length < 6) return

  isLoading.value = true
  try {
    const res = await authService.verifyOtp(resetEmail.value, code)
    resetToken.value = res.token || res.data?.token || ''
    successMessage.value = currentLang.value === 'id' ? 'Kode OTP valid. Silakan buat kata sandi baru.' : 'OTP verified. Please set your new password.'
    currentStep.value = 3
  } catch (err) {
    console.error('Failed to verify OTP:', err)
    errorMessage.value = formatAuthError(err) || t('auth.otpInvalid', 'Kode OTP tidak valid atau telah kedaluwarsa.')
  } finally {
    isLoading.value = false
  }
}

async function handleSaveNewPassword() {
  clearBanners()
  if (newPassword.value !== confirmNewPassword.value) {
    errorMessage.value = t('auth.passwordMismatch', 'Konfirmasi kata sandi tidak cocok.')
    return
  }

  isLoading.value = true
  try {
    const code = otpDigits.value.join('')
    await authService.resetPassword({
      email: resetEmail.value,
      token: resetToken.value || code,
      password: newPassword.value,
      password_confirmation: confirmNewPassword.value
    })
    currentStep.value = 4
  } catch (err) {
    console.error('Failed to reset password:', err)
    errorMessage.value = formatAuthError(err) || (currentLang.value === 'id' ? 'Gagal memperbarui kata sandi. Silakan coba kembali.' : 'Failed to update password. Please try again.')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Page Root */
.forgot-view-page-root {
  min-height: 100vh;
  width: 100%;
  background: #EBF3F9;
  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Desktop Frame */
.desktop-forgot-container {
  display: flex;
  width: 100%;
  height: 100vh;
  max-height: 100vh;
  overflow: hidden;
  background: #FFFFFF;
}

/* Left Banner */
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

/* Right Side */
.forgot-form-side {
  flex: 1;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 36px;
  background: #FFFFFF;
  overflow-y: auto;
}

.form-wrapper {
  width: 100%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.top-nav-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.back-link-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  border: none;
  font-size: 0.8125rem;
  font-weight: 700;
  color: #64748B;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.back-link-btn:hover {
  color: #0D631B;
  background: #E8F5E9;
  transform: translateX(-2px);
}

.lang-selector-wrapper {
  position: relative;
}

.lang-pill-btn {
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  border: 1px solid rgba(203, 213, 225, 0.9);
  border-radius: 8px;
  padding: 5px 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 1), 0 1px 2px rgba(0, 0, 0, 0.04);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.lang-pill-btn:hover {
  border-color: #0D631B;
  background: linear-gradient(180deg, #FFFFFF 0%, #E8F5E9 100%);
  transform: translateY(-1px);
}

.lang-pill-btn-sm {
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  border: 1px solid rgba(203, 213, 225, 0.9);
  border-radius: 6px;
  padding: 4px 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 1);
  transition: all 0.2s;
}

.lang-pill-btn-sm:hover {
  border-color: #0D631B;
}

.lang-dropdown-menu {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  padding: 6px;
  min-width: 160px;
  z-index: 100;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.lang-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 10px;
  border-radius: 6px;
  border: none;
  background: transparent;
  color: #071E27;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}

.lang-option:hover {
  background: rgba(13, 99, 27, 0.08);
  color: #0D631B;
}

.lang-option.active {
  background: rgba(13, 99, 27, 0.12);
  color: #0D631B;
  font-weight: 700;
}

.lang-option-lead {
  display: flex;
  align-items: center;
  gap: 8px;
}

.brand-dual-logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-divider {
  width: 1px;
  height: 24px;
  background: #CBD5E1;
}

/* Feedback */
.feedback-banner {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 0.8125rem;
}

.error-banner {
  background: #FEF2F2;
  border: 1px solid #FCA5A5;
  color: #B91C1C;
}

.success-banner {
  background: #F0FDF4;
  border: 1px solid #86EFAC;
  color: #15803D;
}

.feedback-text {
  display: flex;
  flex-direction: column;
}

/* Step Card */
.step-card {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.step-header {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.step-icon-badge {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(13, 99, 27, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 4px;
}

.step-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: #071E27;
  margin: 0;
}

.step-subtitle {
  font-size: 0.875rem;
  color: #64748B;
  line-height: 1.4;
  margin: 0;
}

.step-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.input-label {
  font-size: 0.8125rem;
  font-weight: 700;
  color: #071E27;
}

.input-field-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.field-icon {
  position: absolute;
  left: 12px;
  pointer-events: none;
  display: flex;
  align-items: center;
}

.text-input {
  width: 100%;
  padding: 10px 14px 10px 38px;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  font-size: 0.875rem;
  color: #071E27;
  background: #F8FAFC;
  outline: none;
  transition: all 0.2s;
}

.text-input:focus {
  border-color: #0D631B;
  background: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(13, 99, 27, 0.12);
}

.toggle-pwd-btn {
  position: absolute;
  right: 12px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
}

/* OTP Boxes */
.otp-boxes-row {
  display: flex;
  gap: 8px;
  justify-content: space-between;
}

.otp-digit-box {
  width: 48px;
  height: 52px;
  text-align: center;
  font-size: 1.375rem;
  font-weight: 800;
  color: #071E27;
  background: #F8FAFC;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  outline: none;
  transition: all 0.2s;
}

.otp-digit-box:focus {
  border-color: #0D631B;
  background: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(13, 99, 27, 0.15);
}

.resend-row {
  display: flex;
  justify-content: center;
  font-size: 0.8125rem;
}

.countdown-text {
  color: #64748B;
}

.resend-btn {
  background: transparent;
  border: none;
  color: #0D631B;
  font-weight: 700;
  cursor: pointer;
}

.resend-btn:hover {
  text-decoration: underline;
}

/* Password Strength */
.pwd-strength-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 4px;
}

.strength-bar-track {
  flex: 1;
  height: 4px;
  background: #E2E8F0;
  border-radius: 2px;
  overflow: hidden;
}

.strength-bar-fill {
  height: 100%;
  transition: width 0.3s;
}

.strength-bar-fill.weak { background: #DC2626; }
.strength-bar-fill.medium { background: #EA580C; }
.strength-bar-fill.strong { background: #0D631B; }

.strength-label {
  font-size: 0.6875rem;
  font-weight: 700;
}

.strength-label.weak { color: #DC2626; }
.strength-label.medium { color: #EA580C; }
.strength-label.strong { color: #0D631B; }

.error-text {
  font-size: 0.75rem;
  color: #DC2626;
  font-weight: 600;
}

/* Submit Action Button */
.submit-action-btn {
  position: relative;
  width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 18px;
  background: linear-gradient(180deg, #15803D 0%, #0D631B 55%, #094713 100%);
  color: #FFFFFF !important;
  border: 1px solid rgba(13, 99, 27, 0.4);
  border-radius: 10px;
  font-size: 0.875rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.45),
    inset 0 -1px 0 rgba(0, 0, 0, 0.3),
    0 2px 8px rgba(13, 99, 27, 0.28) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  -webkit-user-select: none;
  user-select: none;
}

.submit-action-btn:hover:not(:disabled) {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  border-color: rgba(13, 99, 27, 0.5);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.6),
    inset 0 -1px 0 rgba(0, 0, 0, 0.2),
    0 4px 14px rgba(13, 99, 27, 0.38) !important;
  transform: translateY(-1.5px);
}

.submit-action-btn:active:not(:disabled) {
  background: linear-gradient(180deg, #0D631B 0%, #094713 100%);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
  transform: scale(0.98);
}

.submit-action-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
  transform: none;
}

/* Success Card */
.success-card {
  align-items: center;
  text-align: center;
}

.success-icon-box {
  width: 64px;
  height: 64px;
  border-radius: 20px;
  background: rgba(13, 99, 27, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Security Footer */
.security-footer {
  margin-top: 12px;
  padding-top: 14px;
  border-top: 1px solid #F1F5F9;
  display: flex;
  justify-content: center;
}

.footer-collab-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
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
