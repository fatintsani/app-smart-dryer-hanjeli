<template>
  <div class="error-page-root landing-page" :class="{ 'dark-landing': isDark }">
    <!-- 1. TOP STICKY NAVBAR -->
    <header class="landing-navbar" :class="{ 'navbar-scrolled': isScrolled, 'mobile-menu-active': isMobileMenuOpen }" ref="navbarRef">
      <div class="landing-nav-container">
        <!-- Logo Brand -->
        <div class="nav-brand" @click="router.push('/')">
          <div class="brand-icon-box">
            <img v-if="logoSrc" :src="logoSrc" alt="Hanjeli Logo" class="brand-logo-img" />
            <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
          </div>
          <div class="brand-text">
            <span class="brand-title">Smart Room Dryer</span>
            <span class="brand-sub">Desa Wisata Hanjeli</span>
          </div>
        </div>

        <!-- Center Menu Nav Links -->
        <nav class="nav-links desktop-only">
          <a href="/#tentang" @click.prevent="navigateToLanding('tentang')">
            {{ currentLang === 'id' ? 'Tentang' : 'About' }}
          </a>
          <a href="/#hardware" @click.prevent="navigateToLanding('hardware')">
            {{ currentLang === 'id' ? 'Hardware' : 'Hardware' }}
          </a>
          <a href="/#fitur" @click.prevent="navigateToLanding('fitur')">
            {{ currentLang === 'id' ? 'Fitur' : 'Features' }}
          </a>
          <a href="/#panduan" @click.prevent="navigateToLanding('panduan')">
            {{ currentLang === 'id' ? 'Panduan' : 'Guide' }}
          </a>
          <router-link to="/verify">
            {{ currentLang === 'id' ? 'Verifikasi' : 'Verification' }}
          </router-link>
        </nav>

        <!-- Right Controls: Language, Theme, & Action Button -->
        <div class="nav-actions">
          <!-- Language Selector Button with Flag -->
          <div class="lang-selector-wrapper" ref="langDropdownRef">
            <button 
              class="header-action-btn lang-btn" 
              @click.stop="isLangOpen = !isLangOpen"
              :title="currentLang === 'id' ? 'Bahasa: Indonesia (Klik untuk ganti)' : 'Language: English (Click to change)'"
            >
              <FlagIcon :code="currentLang" :size="22" />
            </button>

            <!-- Lang Dropdown Popup -->
            <div v-if="isLangOpen" class="lang-dropdown-menu">
              <button 
                class="lang-option" 
                :class="{ active: currentLang === 'id' }"
                @click="selectLang('id')"
              >
                <div class="lang-option-lead">
                  <FlagIcon code="id" :size="18" />
                  <span>Bahasa Indonesia</span>
                </div>
                <svg v-if="currentLang === 'id'" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </button>

              <button 
                class="lang-option" 
                :class="{ active: currentLang === 'en' }"
                @click="selectLang('en')"
              >
                <div class="lang-option-lead">
                  <FlagIcon code="en" :size="18" />
                  <span>English</span>
                </div>
                <svg v-if="currentLang === 'en'" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </button>
            </div>
          </div>

          <!-- Dark / Light Mode Toggle Button -->
          <button 
            class="header-action-btn theme-toggle-btn" 
            @click="toggleTheme"
            :title="isDark ? (currentLang === 'id' ? 'Beralih ke Mode Terang' : 'Switch to Light Mode') : (currentLang === 'id' ? 'Beralih ke Mode Gelap' : 'Switch to Dark Mode')"
          >
            <svg v-if="isDark" class="theme-icon sun-icon" viewBox="0 0 24 24" fill="none" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <svg v-else class="theme-icon moon-icon" viewBox="0 0 24 24" fill="none" stroke="#111D23" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
          </button>

          <!-- Primary CTA Button -->
          <button class="btn-nav-login desktop-only" @click="goToApp">
            <span>{{ isAuth ? (currentLang === 'id' ? 'Dashboard' : 'Dashboard') : (currentLang === 'id' ? 'Masuk' : 'Login') }}</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>

          <!-- Mobile Hamburger Toggle -->
          <button 
            class="mobile-menu-btn mobile-only" 
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            :aria-expanded="isMobileMenuOpen"
            :title="isMobileMenuOpen ? 'Tutup Menu' : 'Buka Menu'"
          >
            <svg v-if="!isMobileMenuOpen" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
            <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Dropdown / Drawer Menu -->
      <transition name="mobile-nav-expand">
        <div v-if="isMobileMenuOpen" class="landing-mobile-drawer">
          <div class="mobile-drawer-inner">
            <nav class="mobile-drawer-links">
              <a href="/#tentang" @click.prevent="navigateToLanding('tentang')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                <span>{{ currentLang === 'id' ? 'Tentang Inovasi' : 'About Innovation' }}</span>
              </a>
              <a href="/#hardware" @click.prevent="navigateToLanding('hardware')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                <span>{{ currentLang === 'id' ? 'Hardware & Sensor' : 'Hardware & Sensors' }}</span>
              </a>
              <a href="/#fitur" @click.prevent="navigateToLanding('fitur')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                <span>{{ currentLang === 'id' ? 'Fitur Aplikasi' : 'App Features' }}</span>
              </a>
              <a href="/#panduan" @click.prevent="navigateToLanding('panduan')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                <span>{{ currentLang === 'id' ? 'Panduan Penggunaan' : 'User Guide' }}</span>
              </a>
              <router-link to="/verify" @click="isMobileMenuOpen = false">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                <span>{{ currentLang === 'id' ? 'Verifikasi Mutu Batch' : 'Batch Quality Verification' }}</span>
              </router-link>
            </nav>
            <div class="mobile-drawer-cta">
              <button class="btn-drawer-login" @click="goToApp">
                <span>{{ isAuth ? (currentLang === 'id' ? 'Buka Dashboard' : 'Open Dashboard') : (currentLang === 'id' ? 'Masuk ke Sistem' : 'Login to System') }}</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </header>

    <!-- 2. MAIN ERROR CONTENT (No Card Frame - Clean Canvas) -->
    <main class="error-main-container">
      <div class="error-hero-wrapper">
        <!-- 3D Illustration -->
        <div class="error-img-box">
          <img src="/assets/icons/errors/403.png" alt="403 Forbidden" class="error-illustration-3d" />
        </div>

        <!-- Title & Subtitle -->
        <h1 class="error-hero-title">{{ $t('errors.accessTitle') }}</h1>
        <p class="error-hero-desc">{{ $t('errors.accessDesc') }}</p>

        <!-- Action Buttons -->
        <div class="error-action-buttons">
          <router-link :to="isAuth ? '/dashboard' : '/'" class="btn-error-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>{{ isAuth ? $t('errors.backToDashboard') : $t('errors.backToHome') }}</span>
          </router-link>

          <router-link to="/login" class="btn-error-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            <span>{{ $t('errors.switchAccount') }}</span>
          </router-link>
        </div>
      </div>
    </main>

    <!-- 3. FOOTER -->
    <footer class="landing-footer">
      <div class="footer-container">
        <div class="footer-top-grid">
          <!-- Column 1: Brand, Description & Social Links -->
          <div class="footer-col footer-col-brand">
            <div class="footer-brand-header">
              <div class="footer-brand-logo-box">
                <img v-if="logoSrc" :src="logoSrc" alt="Hanjeli Logo" class="footer-hanjeli-logo" />
                <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
              </div>
              <div class="footer-brand-meta">
                <h3 class="footer-brand-title">Desa Wisata Hanjeli</h3>
                <span class="footer-brand-badge">Smart Room Dryer IoT</span>
              </div>
            </div>
            <p class="footer-desc">
              {{ currentLang === 'id'
                ? 'Platform monitoring dan otomasi greenhouse pengeringan biji hanjeli berbasis IoT dan kendali histeresis real-time di Desa Wisata Hanjeli Waluran, Sukabumi (Kawasan Geopark Ciletuh) yang dikembangkan bersama Center of Excellence STAS-RG Telkom University.'
                : 'Real-time IoT-based smart greenhouse dryer monitoring and hysteresis automation platform at Desa Wisata Hanjeli Waluran, Sukabumi (Ciletuh Geopark Area), developed in research partnership with CoE STAS-RG Telkom University.'
              }}
            </p>
            <div class="footer-social-row">
              <a href="https://www.instagram.com/desawisatahanjeli" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Instagram @desawisatahanjeli">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
              </a>
              <a href="https://www.visithanjeli.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Website Resmi Desa Wisata Hanjeli">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="2" y1="12" x2="22" y2="12"></line>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
              </a>
              <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="YouTube Desa Wisata Hanjeli">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                  <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                </svg>
              </a>
            </div>
          </div>

          <!-- Column 2: Navigasi -->
          <div class="footer-col footer-col-nav">
            <div class="footer-col-header">
              <h4 class="footer-heading">{{ currentLang === 'id' ? 'Navigasi' : 'Navigation' }}</h4>
              <span class="footer-green-bar"></span>
            </div>
            <nav class="footer-nav-list">
              <a href="/#tentang" @click.prevent="navigateToLanding('tentang')">{{ currentLang === 'id' ? 'Tentang' : 'About' }}</a>
              <a href="/#hardware" @click.prevent="navigateToLanding('hardware')">{{ currentLang === 'id' ? 'Hardware' : 'Hardware' }}</a>
              <a href="/#fitur" @click.prevent="navigateToLanding('fitur')">{{ currentLang === 'id' ? 'Fitur' : 'Features' }}</a>
              <a href="/#panduan" @click.prevent="navigateToLanding('panduan')">{{ currentLang === 'id' ? 'Panduan' : 'Guide' }}</a>
            </nav>
          </div>

          <!-- Column 3: Info Kontak -->
          <div class="footer-col footer-col-contact">
            <div class="footer-col-header">
              <h4 class="footer-heading">{{ currentLang === 'id' ? 'Info Kontak' : 'Contact Info' }}</h4>
              <span class="footer-green-bar"></span>
            </div>
            <div class="footer-contact-list">
              <div class="footer-contact-item">
                <div class="footer-contact-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                </div>
                <div class="footer-contact-info">
                  <span class="contact-label">EMAIL</span>
                  <a href="mailto:stas-rg@telkomuniversity.ac.id" class="contact-value">stas-rg@telkomuniversity.ac.id</a>
                </div>
              </div>

              <div class="footer-contact-item">
                <div class="footer-contact-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </div>
                <div class="footer-contact-info">
                  <span class="contact-label">PHONE / WA</span>
                  <a href="https://wa.me/6285722182480" target="_blank" rel="noopener noreferrer" class="contact-value">0857-2218-2480 / 0813-9811-5760</a>
                </div>
              </div>

              <div class="footer-contact-item">
                <div class="footer-contact-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                  </svg>
                </div>
                <div class="footer-contact-info">
                  <span class="contact-label">WEBSITE</span>
                  <a href="https://www.visithanjeli.com" target="_blank" rel="noopener noreferrer" class="contact-value">www.visithanjeli.com</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Column 4: Lokasi Map -->
          <div class="footer-col footer-col-map">
            <div class="footer-col-header">
              <h4 class="footer-heading">{{ currentLang === 'id' ? 'Lokasi Desa Wisata' : 'Tourism Village Location' }}</h4>
              <span class="footer-green-bar"></span>
            </div>
            <div class="footer-map-card">
              <div class="footer-map-iframe-box">
                <iframe
                  src="https://maps.google.com/maps?q=Desa%20Wisata%20Hanjeli,%20Jl.%20Pamoyan,%20Waluran%20Mandiri,%20Kec.%20Waluran,%20Kabupaten%20Sukabumi,%20Jawa%20Barat%2043175&t=&z=14&ie=UTF8&iwloc=&output=embed"
                  width="100%"
                  height="125"
                  style="border:0;"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  title="Peta Lokasi Desa Wisata Hanjeli - Waluran, Sukabumi (Kawasan Geopark Ciletuh)"
                ></iframe>
              </div>
              <div class="footer-map-address" title="Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Copyright Bar -->
        <div class="footer-bottom-row">
          <span class="footer-copy">
            {{ currentLang === 'id' 
              ? 'Hak Cipta © 2026 Desa Wisata Hanjeli — Riset & Pengembangan bersama CoE STAS-RG Telkom University. Semua hak dilindungi.' 
              : 'Copyright © 2026 Desa Wisata Hanjeli — Research & Innovation with CoE STAS-RG Telkom University. All rights reserved.' 
            }}
          </span>
          <div class="footer-meta-tags">
            <span class="footer-tagline">
              {{ currentLang === 'id' ? 'Smart Room Dryer IoT • Geopark Ciletuh Sukabumi' : 'Smart Room Dryer IoT • Ciletuh Geopark Sukabumi' }}
            </span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import FlagIcon from '../../components/FlagIcon.vue'
import { currentLang, setLanguage } from '../../i18n'
import { isDark, toggleTheme } from '../../services/themeService'
import { authService } from '../../services/authService'

const router = useRouter()
const logoSrc = ref('/assets/img/hanjeli.png')
const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)
const navbarRef = ref(null)
const langDropdownRef = ref(null)
const isLangOpen = ref(false)

const isAuth = computed(() => authService.isAuthenticated())

function selectLang(lang) {
  setLanguage(lang)
  isLangOpen.value = false
}

function goToApp() {
  if (isAuth.value) {
    router.push('/dashboard')
  } else {
    router.push('/login')
  }
}

function navigateToLanding(sectionId) {
  isMobileMenuOpen.value = false
  router.push(`/#${sectionId}`)
}

function handleClickOutside(event) {
  if (langDropdownRef.value && !langDropdownRef.value.contains(event.target)) {
    isLangOpen.value = false
  }
}

function handleScroll(e) {
  const scrollTop = e?.target?.scrollTop ?? window.scrollY ?? 0
  isScrolled.value = scrollTop > 20
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.error-page-root {
  min-height: 100vh;
  background-color: var(--color-bg, #F4F7F5);
  color: var(--color-text-main, #071E27);
  display: flex;
  flex-direction: column;
  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 1. Landing Navbar */
.landing-navbar {
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  padding: 0 24px;
  box-sizing: border-box;
  z-index: 1000;
  background: rgba(244, 250, 255, 0.92);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom: 1px solid rgba(203, 213, 225, 0.8);
  transition: background-color 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.landing-navbar.navbar-scrolled {
  background: rgba(244, 250, 255, 0.97);
  box-shadow: 0 4px 20px -2px rgba(13, 99, 27, 0.08), 0 2px 8px -1px rgba(0, 0, 0, 0.04);
  border-bottom-color: #CBD5E1;
}

.landing-nav-container {
  width: 100%;
  max-width: 1200px;
  box-sizing: border-box;
  margin: 0 auto;
  padding: 14px 0;
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 32px;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  text-decoration: none;
  flex-shrink: 0;
}

.brand-icon-box {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  flex-shrink: 0;
}

.brand-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-title {
  font-size: 17.5px;
  font-weight: 800;
  color: #111D23;
  line-height: 1.2;
  letter-spacing: -0.2px;
}

.brand-sub {
  font-size: 11.5px;
  font-weight: 600;
  color: #0D631B;
  letter-spacing: 0.2px;
}

.nav-links {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 28px;
}

.nav-links a {
  font-size: 14px;
  font-weight: 600;
  color: #40493D;
  text-decoration: none;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  padding: 6px 0;
  position: relative;
}

.nav-links a:hover,
.nav-links a.active {
  color: #0D631B;
}

.nav-links a.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2.5px;
  background: #0D631B;
  border-radius: 2px;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-self: end;
}

.header-action-btn {
  width: 38px;
  height: 38px;
  border-radius: 9px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #40493D;
  padding: 0;
}

.header-action-btn:hover {
  background: #F0FDF4;
  border-color: #0D631B;
  color: #0D631B;
}

.lang-selector-wrapper {
  position: relative;
}

.lang-dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 190px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  border-radius: 12px;
  box-shadow: 0 10px 30px -4px rgba(0, 0, 0, 0.12);
  padding: 6px;
  z-index: 1050;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.lang-option {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 9px 12px;
  border-radius: 8px;
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 600;
  color: #40493D;
  cursor: pointer;
  transition: all 0.15s ease;
}

.lang-option:hover {
  background: #F1F5F9;
  color: #0D631B;
}

.lang-option.active {
  background: #E8F5E9;
  color: #0D631B;
}

.lang-option-lead {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-nav-login {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8.5px 18px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 9px;
  font-size: 13.5px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 2px 8px rgba(13, 99, 27, 0.22);
}

.btn-nav-login:hover {
  background: #084011;
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(13, 99, 27, 0.32);
}

.mobile-menu-btn {
  width: 38px;
  height: 38px;
  border-radius: 9px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #111D23;
}

.mobile-only {
  display: none !important;
}

.desktop-only {
  display: flex !important;
}

/* Mobile Drawer */
.landing-mobile-drawer {
  background: #FFFFFF;
  border-top: 1px solid #E2E8F0;
  padding: 18px 24px 24px;
}

.mobile-drawer-inner {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.mobile-drawer-links {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.mobile-drawer-links a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 14.5px;
  font-weight: 600;
  color: #40493D;
  text-decoration: none;
}

.mobile-drawer-links a:hover {
  background: #F1F5F9;
  color: #0D631B;
}

.btn-drawer-login {
  width: 100%;
  padding: 12px 18px;
  background: #0D631B;
  color: #FFFFFF;
  border-radius: 10px;
  font-size: 14.5px;
  font-weight: 700;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
}

/* 2. Main Error Content (No Card) */
.error-main-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 64px 24px 72px;
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  box-sizing: border-box;
}

.error-hero-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  max-width: 600px;
  width: 100%;
}

.error-img-box {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.error-illustration-3d {
  width: 190px;
  height: auto;
  max-height: 190px;
  object-fit: contain;
  filter: drop-shadow(0 16px 32px rgba(109, 40, 217, 0.2));
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  user-select: none;
}

.error-illustration-3d:hover {
  transform: translateY(-4px) scale(1.03);
}

.error-hero-title {
  font-size: 28px;
  font-weight: 800;
  color: #071E27;
  line-height: 1.25;
  margin: 0 0 12px;
  letter-spacing: -0.4px;
}

.error-hero-desc {
  font-size: 15px;
  color: #64748B;
  line-height: 1.65;
  max-width: 520px;
  margin: 0 0 32px;
}

.error-action-buttons {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
  width: 100%;
}

.btn-error-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 14px;
  font-weight: 700;
  border-radius: 10px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 3px 10px rgba(13, 99, 27, 0.25);
}

.btn-error-primary:hover {
  background: #084011;
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(13, 99, 27, 0.35);
}

.btn-error-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 22px;
  background: #FFFFFF;
  color: #334155;
  font-size: 14px;
  font-weight: 600;
  border-radius: 10px;
  text-decoration: none;
  border: 1.5px solid #CBD5E1;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-error-secondary:hover {
  background: #F8FAFC;
  border-color: #94A3B8;
  color: #0F172A;
  transform: translateY(-1px);
}

/* 3. Footer */
.landing-footer {
  background: #E9F6FD;
  color: #40493D;
  border-top: 1px solid #CBD5E1;
  padding: 56px 24px 28px;
  transition: all 0.25s ease;
  margin-top: auto;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-top-grid {
  display: grid;
  grid-template-columns: 1.35fr 0.75fr 1.25fr 1.15fr;
  gap: 36px;
  margin-bottom: 40px;
}

.footer-col {
  display: flex;
  flex-direction: column;
}

.footer-col-brand {
  gap: 14px;
}

.footer-brand-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.footer-brand-logo-box {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  flex-shrink: 0;
}

.footer-hanjeli-logo {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}

.footer-brand-meta {
  display: flex;
  flex-direction: column;
}

.footer-brand-title {
  font-size: 17.5px;
  font-weight: 800;
  color: #111D23;
  line-height: 1.2;
  margin: 0;
  letter-spacing: -0.2px;
}

.footer-brand-badge {
  font-size: 11.5px;
  font-weight: 700;
  color: #0D631B;
  letter-spacing: 0.3px;
}

.footer-desc {
  font-size: 13px;
  line-height: 1.65;
  color: #40493D;
  margin: 0;
}

.footer-social-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-top: 4px;
}

.footer-social-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  color: #40493D;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  flex-shrink: 0;
}

.footer-social-btn:hover {
  background: #0D631B;
  border-color: #0D631B;
  color: #FFFFFF;
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(13, 99, 27, 0.25);
}

.footer-col-header {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 18px;
}

.footer-heading {
  font-size: 15px;
  font-weight: 700;
  color: #111D23;
  margin: 0;
  letter-spacing: 0.3px;
}

.footer-green-bar {
  width: 24px;
  height: 2.5px;
  background: #0D631B;
  border-radius: 2px;
}

.footer-nav-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-nav-list a {
  font-size: 13.5px;
  font-weight: 500;
  color: #40493D;
  text-decoration: none;
  transition: all 0.2s ease;
  width: fit-content;
}

.footer-nav-list a:hover {
  color: #0D631B;
  transform: translateX(3px);
}

.footer-contact-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.footer-contact-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.footer-contact-icon {
  width: 22px;
  height: 22px;
  background: transparent;
  border: none;
  color: #0D631B;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

.footer-contact-icon svg {
  stroke: currentColor;
}

.footer-contact-info {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.contact-label {
  font-size: 10px;
  font-weight: 700;
  color: #707A6C;
  letter-spacing: 0.6px;
}

.contact-value {
  font-size: 13px;
  font-weight: 600;
  color: #111D23;
  text-decoration: none;
  transition: color 0.2s ease;
}

a.contact-value:hover {
  color: #0D631B;
  text-decoration: underline;
}

.footer-map-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.footer-map-iframe-box {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #CBD5E1;
  background: #FFFFFF;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
}

.footer-map-iframe-box iframe {
  display: block;
}

.footer-map-address {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  color: #40493D;
  font-weight: 600;
  line-height: 1.4;
}

.footer-bottom-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 22px;
  border-top: 1px solid #CBD5E1;
  font-size: 12px;
  color: #707A6C;
  flex-wrap: wrap;
  gap: 12px;
}

.footer-copy {
  font-size: 0.8125rem;
  color: #707A6C;
  margin: 0;
}

.footer-meta-tags {
  display: flex;
  align-items: center;
  gap: 10px;
}

.footer-tagline {
  font-size: 11px;
  font-weight: 600;
  color: #0D631B;
}

/* Responsive Media Queries */
@media (max-width: 1024px) {
  .footer-top-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 860px) {
  .desktop-only {
    display: none !important;
  }

  .mobile-only {
    display: inline-flex !important;
  }

  .landing-nav-container {
    display: flex;
    justify-content: space-between;
  }
}

@media (max-width: 768px) {
  .error-hero-title {
    font-size: 24px;
  }

  .error-hero-desc {
    font-size: 14px;
  }

  .error-illustration-3d {
    width: 160px;
    max-height: 160px;
  }

  .footer-top-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }

  .landing-footer {
    padding: 40px 16px 24px;
  }
}

/* Dark Mode Overrides */
.dark-landing .landing-navbar {
  background: rgba(15, 23, 42, 0.92);
  border-bottom-color: rgba(51, 65, 85, 0.8);
}

.dark-landing .brand-title,
.dark-landing .error-hero-title,
.dark-landing .footer-brand-title,
.dark-landing .footer-heading,
.dark-landing .contact-value {
  color: #F8FAFC !important;
}

.dark-landing .error-hero-desc,
.dark-landing .footer-desc,
.dark-landing .footer-copy,
.dark-landing .footer-map-address {
  color: #94A3B8 !important;
}

.dark-landing .error-page-root {
  background-color: #0B1120;
}

.dark-landing .landing-footer {
  background: #0F172A;
  border-top-color: #1E293B;
}

.dark-landing .btn-error-secondary {
  background: #1E293B;
  border-color: #334155;
  color: #F8FAFC;
}

.dark-landing .btn-error-secondary:hover {
  background: #334155;
}
</style>
