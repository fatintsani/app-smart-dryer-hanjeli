<template>
  <div class="login-view-page-root">
    <!-- Desktop Login / Register View -->
    <div v-if="!isMobile" class="desktop-login-container">
      <!-- Left Hero Banner -->
      <div class="login-banner">
        <div class="banner-overlay"></div>

        <!-- Center Emblem Graphic: Hanjeli -->
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

      <!-- Right Login / Register Form Area -->
      <div class="login-form-side">
        <div class="form-wrapper">
          <!-- Top Title & Language Switcher -->
          <div class="form-header-row">
            <div class="brand-dual-logo">
              <HanjeliLogo :size="36" />
              <div class="brand-divider"></div>
              <StasLogo :size="28" />
              <span class="auth-version-badge">v1.0</span>
            </div>

            <!-- Language Selector Desktop -->
            <div class="lang-switch-wrap">
              <button 
                type="button" 
                class="lang-pill" 
                :class="{ active: currentLang === 'id' }" 
                @click="setLang('id')"
                title="Bahasa Indonesia"
              >
                <FlagIcon code="id" :size="16" />
                <span>ID</span>
              </button>
              <button 
                type="button" 
                class="lang-pill" 
                :class="{ active: currentLang === 'en' }" 
                @click="setLang('en')"
                title="English"
              >
                <FlagIcon code="gb" :size="16" />
                <span>EN</span>
              </button>
            </div>
          </div>

          <div class="brand-text-wrap">
            <h1 class="brand-title-green">{{ $t('auth.brandTitle') }}</h1>
            <p class="welcome-sub">{{ isRegisterMode ? $t('auth.welcomeRegisterSub') : $t('auth.welcomeLoginSub') }}</p>
          </div>

          <!-- Auth Mode Switcher Tab -->
          <div class="auth-tabs">
            <button 
              type="button" 
              class="tab-btn" 
              :class="{ active: !isRegisterMode }" 
              @click="switchMode(false)"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
              </svg>
              <span>{{ $t('auth.tabLogin') }}</span>
            </button>
            <button 
              type="button" 
              class="tab-btn" 
              :class="{ active: isRegisterMode }" 
              @click="switchMode(true)"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="19" y1="8" x2="19" y2="14"></line>
                <line x1="22" y1="11" x2="16" y2="11"></line>
              </svg>
              <span>{{ $t('auth.tabRegister') }}</span>
            </button>
          </div>

          <!-- Error Banner -->
          <div v-if="errorMessage" class="feedback-banner error-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="error-icon">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div class="feedback-text">
              <strong>{{ $t('auth.errorTitle') }}</strong>
              <span>{{ errorMessage }}</span>
            </div>
          </div>

          <!-- Success Banner -->
          <div v-if="successMessage" class="feedback-banner success-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div class="feedback-text">
              <strong>{{ $t('auth.successTitle') }}</strong>
              <span>{{ successMessage }}</span>
            </div>
          </div>

          <!-- 1. LOGIN FORM -->
          <form v-if="!isRegisterMode" @submit.prevent="handleLogin" class="login-form">
            <!-- Email Input -->
            <div class="input-group">
              <label class="input-label">{{ $t('auth.email') }}</label>
              <div class="input-field-wrapper" :class="{ 'field-error': emailError }">
                <span class="field-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                  </svg>
                </span>
                <input 
                  type="email" 
                  v-model="email" 
                  :placeholder="$t('auth.emailPlaceholder')" 
                  class="text-input" 
                  required
                  @input="clearFieldErrors"
                />
              </div>
              <span v-if="emailError" class="input-hint-error">{{ emailError }}</span>
            </div>

            <!-- Password Input -->
            <div class="input-group">
              <label class="input-label">{{ $t('auth.password') }}</label>
              <div class="input-field-wrapper" :class="{ 'field-error': passwordError }">
                <span class="field-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                </span>
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  v-model="password" 
                  :placeholder="$t('auth.passwordPlaceholder')" 
                  class="text-input" 
                  required
                  @input="clearFieldErrors"
                />
                <button 
                  type="button" 
                  class="toggle-pwd-btn" 
                  @click="showPassword = !showPassword"
                >
                  <svg v-if="!showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                    <line x1="2" x2="22" y1="2" y2="22"></line>
                  </svg>
                </button>
              </div>
              <span v-if="passwordError" class="input-hint-error">{{ passwordError }}</span>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="row-between">
              <label class="remember-me">
                <input type="checkbox" v-model="rememberMe" />
                <span>{{ $t('auth.rememberMe') }}</span>
              </label>
              <a href="#" class="forgot-link" @click.prevent="$emit('forgot-password')">{{ $t('auth.forgotPassword') }}</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-login-btn" :disabled="isLoading">
              <span v-if="!isLoading">{{ $t('auth.loginButton') }}</span>
              <span v-else>{{ $t('auth.loggingIn') }}</span>
              <svg v-if="!isLoading" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </form>

          <!-- 2. REGISTER / BUAT AKUN FORM -->
          <form v-else @submit.prevent="handleRegister" class="login-form">
            <!-- Name Input -->
            <div class="input-group">
              <label class="input-label">{{ $t('auth.fullName') }}</label>
              <div class="input-field-wrapper">
                <span class="field-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                </span>
                <input 
                  type="text" 
                  v-model="regName" 
                  :placeholder="$t('auth.fullNamePlaceholder')" 
                  class="text-input" 
                  required
                />
              </div>
            </div>

            <!-- Email Input -->
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
                  v-model="regEmail" 
                  :placeholder="$t('auth.emailPlaceholder')" 
                  class="text-input" 
                  required
                />
              </div>
            </div>

            <!-- Phone Input -->
            <div class="input-group">
              <label class="input-label">{{ $t('auth.phone') }} <span class="label-sub">{{ $t('auth.phoneOptional') }}</span></label>
              <div class="input-field-wrapper">
                <span class="field-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </span>
                <input 
                  type="tel" 
                  v-model="regPhone" 
                  :placeholder="$t('auth.phonePlaceholder')" 
                  class="text-input" 
                />
              </div>
            </div>

            <!-- Password Input -->
            <div class="input-group">
              <label class="input-label">{{ $t('auth.password') }} ({{ $t('auth.passwordMinHint') }})</label>
              <div class="input-field-wrapper">
                <span class="field-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                </span>
                <input 
                  :type="showRegPassword ? 'text' : 'password'" 
                  v-model="regPassword" 
                  :placeholder="$t('auth.passwordSecurePlaceholder')" 
                  class="text-input" 
                  required
                  minlength="6"
                />
                <button 
                  type="button" 
                  class="toggle-pwd-btn" 
                  @click="showRegPassword = !showRegPassword"
                >
                  <svg v-if="!showRegPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                    <line x1="2" x2="22" y1="2" y2="22"></line>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Register Submit Button -->
            <button type="submit" class="submit-login-btn register-submit-btn" :disabled="isLoading">
              <span v-if="!isLoading">{{ $t('auth.registerButton') }}</span>
              <span v-else>{{ $t('auth.registering') }}</span>
              <svg v-if="!isLoading" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <polyline points="16 11 18 13 22 9"></polyline>
              </svg>
            </button>
          </form>

          <!-- Divider -->
          <div class="divider-row">
            <div class="divider-line"></div>
            <span class="divider-text">{{ $t('auth.or') }}</span>
            <div class="divider-line"></div>
          </div>

          <!-- Alternative Social & Passkey Auth Buttons -->
          <div class="alt-auth-group">
            <button 
              type="button" 
              class="google-auth-full-btn" 
              @click="handleGoogleSignIn" 
              :disabled="isLoading"
            >
              <svg class="google-icon" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17Z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
              </svg>
              <span>{{ isRegisterMode ? $t('auth.registerWithGoogle') : $t('auth.continueWithGoogle') }}</span>
            </button>

            <button 
              v-if="!isRegisterMode"
              type="button" 
              class="passkey-auth-btn" 
              @click="handlePasskeySignIn" 
              :disabled="isLoading"
            >
              <svg class="passkey-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"></path>
                <path d="M14 13.12c0 2.38 0 6.38-1 8.88"></path>
                <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"></path>
                <path d="M2 12a10 10 0 0 1 18-6"></path>
                <path d="M2 16h.01"></path>
                <path d="M21.8 16c.2-2 .131-5.354 0-6"></path>
                <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"></path>
                <path d="M8.65 22c.21-.66.45-1.32.57-2"></path>
                <path d="M9 6.8a6 6 0 0 1 9 5.2v2"></path>
              </svg>
              <span>Masuk dengan Passkey / Biometrik</span>
            </button>
          </div>

          <!-- Security & Creator Credit Footer -->
          <div class="security-footer">
            <div class="footer-collab-row">
              <span>{{ $t('common.facilityName') }}</span>
              <span class="dot-sep">•</span>
              <span>{{ $t('auth.devCredit') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Login / Register View -->
    <div v-else class="mobile-login-container">
      <div class="mobile-login-card">
        <div class="mobile-brand-center">
          <div class="mobile-dual-logo-box">
            <HanjeliLogo :size="54" />
            <div class="mobile-logo-divider"></div>
            <StasLogo :size="42" />
          </div>
          <div class="mobile-brand-title-wrap">
            <h1 class="mobile-brand-title">{{ $t('auth.brandTitle') }}</h1>
            <span class="auth-version-badge">v1.0</span>
          </div>
          <p class="mobile-brand-sub">{{ $t('common.facilityName') }} • STAS</p>

          <!-- Mobile Language Selector -->
          <div class="lang-switch-wrap">
            <button 
              type="button" 
              class="lang-pill" 
              :class="{ active: currentLang === 'id' }" 
              @click="setLang('id')"
              title="Bahasa Indonesia"
            >
              <FlagIcon code="id" :size="15" />
              <span>ID</span>
            </button>
            <button 
              type="button" 
              class="lang-pill" 
              :class="{ active: currentLang === 'en' }" 
              @click="setLang('en')"
              title="English"
            >
              <FlagIcon code="gb" :size="15" />
              <span>EN</span>
            </button>
          </div>
        </div>

        <!-- Auth Switcher Mobile -->
        <div class="auth-tabs">
          <button 
            type="button" 
            class="tab-btn" 
            :class="{ active: !isRegisterMode }" 
            @click="switchMode(false)"
          >
            <span>{{ $t('auth.tabLogin') }}</span>
          </button>
          <button 
            type="button" 
            class="tab-btn" 
            :class="{ active: isRegisterMode }" 
            @click="switchMode(true)"
          >
            <span>{{ $t('auth.tabRegister') }}</span>
          </button>
        </div>

        <!-- Feedback Banners -->
        <div v-if="errorMessage" class="feedback-banner error-banner">
          <span>{{ errorMessage }}</span>
        </div>
        <div v-if="successMessage" class="feedback-banner success-banner">
          <span>{{ successMessage }}</span>
        </div>

        <!-- Mobile Login Form -->
        <form v-if="!isRegisterMode" @submit.prevent="handleLogin" class="mobile-form">
          <div class="input-group">
            <label class="input-label">{{ $t('auth.email') }}</label>
            <div class="input-field-wrapper" :class="{ 'field-error': emailError }">
              <span class="field-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                  <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                </svg>
              </span>
              <input type="email" v-model="email" :placeholder="$t('auth.emailPlaceholder')" class="text-input" required @input="clearFieldErrors" />
            </div>
            <span v-if="emailError" class="input-hint-error">{{ emailError }}</span>
          </div>

          <div class="input-group">
            <label class="input-label">{{ $t('auth.password') }}</label>
            <div class="input-field-wrapper" :class="{ 'field-error': passwordError }">
              <span class="field-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </span>
              <input :type="showPassword ? 'text' : 'password'" v-model="password" :placeholder="$t('auth.passwordPlaceholder')" class="text-input" required @input="clearFieldErrors" />
              <button type="button" class="toggle-pwd-btn" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#707A6C" stroke-width="2">
                  <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                  <line x1="2" x2="22" y1="2" y2="22"></line>
                </svg>
              </button>
            </div>
            <span v-if="passwordError" class="input-hint-error">{{ passwordError }}</span>
          </div>

          <div class="row-between">
            <label class="remember-me">
              <input type="checkbox" v-model="rememberMe" />
              <span>{{ $t('auth.rememberMe') }}</span>
            </label>
            <a href="#" class="forgot-link" @click.prevent="$emit('forgot-password')">{{ $t('auth.forgotPassword') }}</a>
          </div>

          <button type="submit" class="submit-login-btn" :disabled="isLoading">
            {{ isLoading ? $t('auth.loggingIn') : $t('auth.loginButton') }}
          </button>
        </form>

        <!-- Mobile Register Form -->
        <form v-else @submit.prevent="handleRegister" class="mobile-form">
          <div class="input-group">
            <label class="input-label">{{ $t('auth.fullName') }}</label>
            <input type="text" v-model="regName" :placeholder="$t('auth.fullNamePlaceholder')" class="text-input" required />
          </div>

          <div class="input-group">
            <label class="input-label">{{ $t('auth.email') }}</label>
            <input type="email" v-model="regEmail" :placeholder="$t('auth.emailPlaceholder')" class="text-input" required />
          </div>

          <div class="input-group">
            <label class="input-label">{{ $t('auth.password') }}</label>
            <input :type="showRegPassword ? 'text' : 'password'" v-model="regPassword" :placeholder="$t('auth.passwordMinHint')" class="text-input" required minlength="6" />
          </div>

          <button type="submit" class="submit-login-btn register-submit-btn" :disabled="isLoading">
            {{ isLoading ? $t('auth.registering') : $t('auth.registerButton') }}
          </button>
        </form>

        <!-- Divider Mobile -->
        <div class="divider-row">
          <div class="divider-line"></div>
          <span class="divider-text">{{ $t('auth.or') }}</span>
          <div class="divider-line"></div>
        </div>

        <!-- Alternative Social & Passkey Auth Mobile Buttons -->
        <div class="alt-auth-group">
          <button 
            type="button" 
            class="google-auth-full-btn" 
            @click="handleGoogleSignIn" 
            :disabled="isLoading"
          >
            <svg class="google-icon" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17Z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
            </svg>
            <span>{{ isRegisterMode ? $t('auth.registerWithGoogle') : $t('auth.continueWithGoogle') }}</span>
          </button>

          <!-- Passkey Mobile Button -->
          <button 
            v-if="!isRegisterMode"
            type="button" 
            class="passkey-auth-btn" 
            @click="handlePasskeySignIn" 
            :disabled="isLoading"
          >
            <svg class="passkey-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"></path>
              <path d="M14 13.12c0 2.38 0 6.38-1 8.88"></path>
              <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"></path>
              <path d="M2 12a10 10 0 0 1 18-6"></path>
              <path d="M2 16h.01"></path>
              <path d="M21.8 16c.2-2 .131-5.354 0-6"></path>
              <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"></path>
              <path d="M8.65 22c.21-.66.45-1.32.57-2"></path>
              <path d="M9 6.8a6 6 0 0 1 9 5.2v2"></path>
            </svg>
            <span>Masuk dengan Passkey / Biometrik</span>
          </button>
        </div>

        <div class="mobile-security-box">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#40493D" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
          <span>{{ $t('auth.securityProtected') }}</span>
        </div>

        <p class="mobile-dev-credit">{{ $t('auth.devCredit') }}</p>
      </div>
    </div>

    <!-- System Alert Dialog Modal -->
    <div v-if="isAlertModalOpen" class="modal-backdrop" @click.self="isAlertModalOpen = false">
      <div class="alert-modal-card">
        <div class="alert-modal-head">
          <div class="alert-modal-icon-badge" :class="alertModalType">
            <svg v-if="alertModalType === 'warning'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <svg v-else-if="alertModalType === 'error'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>
          <div class="alert-modal-titles">
            <h3>{{ alertModalTitle }}</h3>
            <p>{{ alertModalSubtitle }}</p>
          </div>
        </div>

        <div class="alert-modal-body">
          <p class="alert-modal-message">{{ alertModalMessage }}</p>
          <div v-if="alertModalCode" class="alert-code-box">
            <div class="code-box-header">
              <span>{{ currentLang === 'id' ? 'Petunjuk Konfigurasi .env:' : '.env Configuration Guide:' }}</span>
            </div>
            <code>{{ alertModalCode }}</code>
          </div>
        </div>

        <div class="alert-modal-foot">
          <button type="button" class="btn-alert-dismiss" @click="isAlertModalOpen = false">
            {{ $t('auth.alertDismiss') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import HanjeliLogo from '../../components/HanjeliLogo.vue'
import StasLogo from '../../components/StasLogo.vue'
import FlagIcon from '../../components/FlagIcon.vue'
import { authService, formatAuthError } from '../../services/authService'
import { currentLang, setLanguage, t } from '../../i18n'

defineProps({
  isMobile: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['login-success', 'forgot-password'])

// Mode state
const isRegisterMode = ref(false)

// Login fields
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberMe = ref(true)

// Register fields
const regName = ref('')
const regEmail = ref('')
const regPhone = ref('')
const regRole = ref('OPERATOR')
const regPassword = ref('')
const showRegPassword = ref(false)

// Status & Field Errors
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const emailError = ref('')
const passwordError = ref('')

// System Alert Modal States
const isAlertModalOpen = ref(false)
const alertModalType = ref('warning')
const alertModalTitle = ref('')
const alertModalSubtitle = ref('')
const alertModalMessage = ref('')
const alertModalCode = ref('')

function showAlertModal({ type = 'warning', title, subtitle = '', message, code = '' }) {
  alertModalType.value = type
  alertModalTitle.value = title
  alertModalSubtitle.value = subtitle || (currentLang.value === 'id' ? 'Pemberitahuan Sistem' : 'System Notification')
  alertModalMessage.value = message
  alertModalCode.value = code
  isAlertModalOpen.value = true
}

function setLang(lang) {
  setLanguage(lang)
}

function clearFieldErrors() {
  errorMessage.value = ''
  emailError.value = ''
  passwordError.value = ''
}

function switchMode(toRegister) {
  isRegisterMode.value = toRegister
  clearFieldErrors()
  successMessage.value = ''
}

// 1. Handle Regular Email & Password Login
async function handleLogin() {
  clearFieldErrors()

  if (!email.value) {
    emailError.value = t('auth.emailRequired')
    return
  }
  if (!password.value) {
    passwordError.value = t('auth.passwordRequired')
    return
  }

  isLoading.value = true
  try {
    const res = await authService.login(email.value, password.value)
    successMessage.value = t('auth.loginSuccess')
    setTimeout(() => {
      emit('login-success', res.user)
    }, 400)
  } catch (err) {
    console.error('Login error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

// 2. Handle User Registration
async function handleRegister() {
  clearFieldErrors()

  if (!regName.value || !regEmail.value || !regPassword.value) {
    errorMessage.value = t('auth.allFieldsRequired')
    return
  }
  if (regPassword.value.length < 6) {
    errorMessage.value = t('auth.passwordMinLength')
    return
  }

  isLoading.value = true
  try {
    const res = await authService.register({
      name: regName.value,
      email: regEmail.value,
      phone: regPhone.value || undefined,
      role: regRole.value,
      password: regPassword.value,
    })

    successMessage.value = t('auth.registerSuccess')
    setTimeout(() => {
      emit('login-success', res.user)
    }, 600)
  } catch (err) {
    console.error('Register error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

// 3. Official Google OAuth Integration
function handleGoogleSignIn() {
  clearFieldErrors()
  isLoading.value = true
  window.location.href = '/auth/google/redirect'
}

// 4. WebAuthn Passkey / Biometrics Authentication
async function handlePasskeySignIn() {
  clearFieldErrors()
  
  if (!authService.isPasskeySupported()) {
    showAlertModal({
      type: 'warning',
      title: 'Passkey Belum Didukung',
      subtitle: 'Kompatibilitas Perangkat',
      message: 'Browser atau perangkat Anda belum mendukung otentikasi biometrik WebAuthn Passkey.',
    })
    return
  }

  isLoading.value = true
  try {
    const res = await authService.passkeyLogin(email.value)
    successMessage.value = `Masuk dengan Passkey sebagai ${res.user.name} berhasil!`
    setTimeout(() => {
      emit('login-success', res.user)
    }, 400)
  } catch (err) {
    console.error('Passkey login error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

async function handlePasskeyRegister() {
  clearFieldErrors()
  const targetEmail = regEmail.value || email.value
  const targetName = regName.value || 'Operator Perangkat'

  if (!targetEmail) {
    errorMessage.value = 'Silakan masukkan alamat email terlebih dahulu untuk mendaftarkan passkey.'
    return
  }

  isLoading.value = true
  try {
    const res = await authService.passkeyRegister(targetEmail, targetName)
    successMessage.value = 'Passkey perangkat ini berhasil didaftarkan! Mengalihkan...'
    setTimeout(() => {
      emit('login-success', res.user)
    }, 600)
  } catch (err) {
    console.error('Passkey registration error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

// Parse Google JWT ID Token
function parseJwt(token) {
  try {
    const base64Url = token.split('.')[1]
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/')
    const jsonPayload = decodeURIComponent(
      atob(base64)
        .split('')
        .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
        .join('')
    )
    return JSON.parse(jsonPayload)
  } catch {
    return null
  }
}

async function handleGoogleCredentialResponse(response) {
  if (!response?.credential) return
  const payload = parseJwt(response.credential)
  if (!payload) return

  isLoading.value = true
  try {
    const res = await authService.googleAuth({
      email: payload.email,
      name: payload.name || payload.email.split('@')[0],
      avatarUrl: payload.picture,
      googleId: payload.sub,
    })

    successMessage.value = `Masuk sebagai ${res.user.name} berhasil!`
    setTimeout(() => {
      emit('login-success', res.user)
    }, 400)
  } catch (err) {
    console.error('Google auth error:', err)
    errorMessage.value = formatAuthError(err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const oauthToken = urlParams.get('token')
  const oauthUser = urlParams.get('user')
  const oauthError = urlParams.get('error')

  if (oauthError) {
    errorMessage.value = decodeURIComponent(oauthError)
  } else if (oauthToken && oauthUser) {
    try {
      const user = JSON.parse(decodeURIComponent(oauthUser))
      localStorage.setItem('smart_dryer_token', oauthToken)
      localStorage.setItem('smart_dryer_user', JSON.stringify(user))
      successMessage.value = `Masuk sebagai ${user.name} berhasil!`
      setTimeout(() => {
        emit('login-success', user)
      }, 400)
    } catch (e) {
      console.error('Failed to parse OAuth user:', e)
    }
  }

  const googleClientId = import.meta.env.VITE_GOOGLE_CLIENT_ID
  if (window.google?.accounts?.id && googleClientId && googleClientId.includes('.apps.googleusercontent.com')) {
    try {
      window.google.accounts.id.initialize({
        client_id: googleClientId,
        callback: handleGoogleCredentialResponse,
      })
    } catch (e) {
      console.warn('Could not auto-initialize Google GIS:', e.message)
    }
  }
})
</script>

<style scoped>
.login-view-page-root {
  width: 100%;
  height: 100%;
  min-height: 100vh;
  position: relative;
}

/* Desktop Layout */
.desktop-login-container {
  display: flex;
  width: 100%;
  height: 100vh;
  max-height: 100vh;
  overflow: hidden;
  background: #FFFFFF;
}

.login-banner {
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
.login-form-side {
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
  gap: 12px;
}

.form-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-dual-logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.auth-version-badge {
  font-size: 10px;
  font-weight: 700;
  color: #0D631B;
  background: #E8F5E9;
  border: 1px solid #C8E6C9;
  padding: 1px 6px;
  border-radius: 4px;
  letter-spacing: 0.3px;
  line-height: 1.3;
}

.mobile-brand-title-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.brand-divider {
  width: 1px;
  height: 22px;
  background: #CBD5E1;
}

.lang-switch-wrap {
  display: flex;
  background: #F1F5F9;
  padding: 3px;
  border-radius: 8px;
  gap: 2px;
}

.lang-pill {
  border: none;
  background: transparent;
  padding: 4px 8px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 700;
  color: #64748B;
  cursor: pointer;
  transition: all 0.15s;
}

.lang-pill.active {
  background: #FFFFFF;
  color: #0D631B;
  }

.brand-text-wrap {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.brand-title-green {
  font-size: 22px;
  font-weight: 800;
  color: #0D631B;
  letter-spacing: -0.3px;
}

.welcome-sub {
  font-size: 12.5px;
  color: #64748B;
  line-height: 16px;
}

/* Auth Tabs */
.auth-tabs {
  display: flex;
  background: #F1F5F9;
  padding: 4px;
  border-radius: 10px;
  gap: 4px;
}

.tab-btn {
  flex: 1;
  padding: 8px 12px;
  border: none;
  background: transparent;
  color: #64748B;
  font-size: 13px;
  font-weight: 600;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.tab-btn.active {
  background: #FFFFFF;
  color: #0D631B;
  }

/* Feedback Banners */
.feedback-banner {
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 12.5px;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  line-height: 17px;
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

/* Alternative Social & Passkey Auth Buttons Group */
.alt-auth-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: 100%;
}

/* Full Width Google Sign In Button */
.google-auth-full-btn {
  width: 100%;
  padding: 10px 16px;
  background: #FFFFFF;
  border: 1.5px solid #CBD5E1;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 13.5px;
  font-weight: 600;
  color: #1E293B;
  cursor: pointer;
  transition: all 0.2s ease;
  }

.google-auth-full-btn:hover:not(:disabled) {
  background: #F8FAFC;
  border-color: #94A3B8;
    transform: translateY(-1px);
}

.google-auth-full-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.google-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

/* Passkey Biometric Button */
.passkey-auth-btn {
  width: 100%;
  padding: 10px 16px;
  background: #F0FDF4;
  border: 1.5px solid #86EFAC;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 13.5px;
  font-weight: 600;
  color: #15803D;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 0;
  }

.passkey-auth-btn:hover:not(:disabled) {
  background: #DCFCE7;
  border-color: #4ADE80;
  color: #0D631B;
    transform: translateY(-1px);
}

.passkey-auth-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.passkey-icon {
  flex-shrink: 0;
  stroke: #16A34A;
  transition: transform 0.2s ease;
}

.passkey-auth-btn:hover .passkey-icon {
  transform: scale(1.1);
  stroke: #0D631B;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.input-label {
  font-size: 11.5px;
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
  left: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.text-input, .text-select {
  width: 100%;
  padding: 8px 10px 8px 34px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 13px;
  color: #071E27;
  background: #F8FAFC;
  transition: all 0.2s;
  outline: none;
}

.text-select {
  padding-left: 10px;
  cursor: pointer;
}

.field-error .text-input {
  border-color: #DC2626;
  background: #FEF2F2;
}

.input-hint-error {
  font-size: 11px;
  color: #DC2626;
  margin-top: 2px;
}

.text-input:focus, .text-select:focus {
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
  border: none;
}

.row-between {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #40493D;
  cursor: pointer;
}

.forgot-link {
  color: #0D631B;
  font-weight: 600;
  text-decoration: none;
}

.forgot-link:hover {
  text-decoration: underline;
}

.submit-login-btn {
  width: 100%;
  padding: 10px 14px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
}

.submit-login-btn:hover:not(:disabled) {
  background: #15803D;
}

.submit-login-btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.register-submit-btn {
  background: #047857;
}

.register-submit-btn:hover:not(:disabled) {
  background: #059669;
}

.divider-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 2px 0;
}

.divider-line {
  flex: 1;
  height: 1px;
  background: #E2E8F0;
}

.divider-text {
  font-size: 11px;
  color: #94A3B8;
  text-transform: uppercase;
}

.security-footer {
  margin-top: 4px;
  padding-top: 8px;
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
.mobile-login-container {
  min-height: 100vh;
  background: #EBF3F9;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.mobile-login-card {
  width: 100%;
  max-width: 400px;
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid #E2E8F0;
    padding: 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.mobile-brand-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
}

.mobile-dual-logo-box {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 8px 14px;
  background: #F8FAFC;
  border-radius: 14px;
  border: 1px solid #E2E8F0;
}

.mobile-logo-divider {
  width: 1px;
  height: 28px;
  background: #CBD5E1;
}

.mobile-brand-title {
  font-size: 19px;
  font-weight: 800;
  color: #0D631B;
}

.mobile-brand-sub {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.8px;
  color: #64748B;
}

.mobile-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.mobile-security-box {
  background: #F3FAFF;
  border: 1px solid #E2E8F0;
  padding: 8px 12px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-size: 11px;
  color: #40493D;
  text-align: center;
}

.mobile-dev-credit {
  font-size: 10px;
  color: #64748B;
  font-weight: 600;
  text-align: center;
}

/* System Alert Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(5px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  animation: fadeIn 0.2s ease-out;
}

.alert-modal-card {
  width: 100%;
  max-width: 440px;
  background: #FFFFFF;
  border-radius: 18px;
    overflow: hidden;
  display: flex;
  flex-direction: column;
  animation: popIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes popIn {
  0% {
    opacity: 0;
    transform: scale(0.95) translateY(8px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.alert-modal-head {
  padding: 22px 24px 16px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.alert-modal-icon-badge {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.alert-modal-icon-badge.warning {
  background: #FEF3C7;
  color: #D97706;
}

.alert-modal-icon-badge.error {
  background: #FEE2E2;
  color: #DC2626;
}

.alert-modal-icon-badge.info {
  background: #E8F5E9;
  color: #0D631B;
}

.alert-modal-titles {
  flex: 1;
}

.alert-modal-titles h3 {
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1.3;
}

.alert-modal-titles p {
  font-size: 12px;
  color: #64748B;
  margin: 3px 0 0 0;
}

.alert-modal-body {
  padding: 0 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.alert-modal-message {
  font-size: 13.5px;
  color: #334155;
  line-height: 1.55;
  margin: 0;
}

.alert-code-box {
  background: #0F172A;
  color: #F8FAFC;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 11.5px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  overflow-x: auto;
}

.code-box-header {
  font-size: 10.5px;
  color: #94A3B8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.alert-code-box code {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  color: #34D399;
  word-break: break-all;
}

.alert-modal-foot {
  padding: 14px 24px;
  background: #F8FAFC;
  border-top: 1px solid #F1F5F9;
  display: flex;
  justify-content: flex-end;
}

.btn-alert-dismiss {
  padding: 9px 22px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-alert-dismiss:hover {
  background: #15803D;
  }
</style>
