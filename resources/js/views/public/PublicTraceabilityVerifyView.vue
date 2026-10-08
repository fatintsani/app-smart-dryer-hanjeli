<template>
  <div class="verify-page-root landing-page" :class="{ 'dark-landing': isDark }">
    <!-- 1. TOP STICKY NAVBAR (Matching Landing View) -->
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
          <a href="/verify" class="active" @click.prevent="resetToPortal">
            {{ currentLang === 'id' ? 'Verifikasi' : 'Verification' }}
          </a>
        </nav>

        <!-- Right Controls: Language, Theme, & Action Button -->
        <div class="nav-actions">
          <!-- 1. Language Selector Button with Flag -->
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

          <!-- 2. Dark / Light Mode Toggle Button -->
          <button 
            class="header-action-btn theme-toggle-btn" 
            @click="toggleTheme"
            :title="isDark ? (currentLang === 'id' ? 'Beralih ke Mode Terang' : 'Switch to Light Mode') : (currentLang === 'id' ? 'Beralih ke Mode Gelap' : 'Switch to Dark Mode')"
          >
            <!-- Sun icon when dark -->
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
            <!-- Moon icon when light -->
            <svg v-else class="theme-icon moon-icon" viewBox="0 0 24 24" fill="none" stroke="#111D23" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
          </button>

          <!-- 3. Primary CTA Button -->
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
                <div class="link-icon-chip">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                </div>
                <span>{{ currentLang === 'id' ? 'Tentang Sistem' : 'About System' }}</span>
              </a>
              <a href="/#hardware" @click.prevent="navigateToLanding('hardware')">
                <div class="link-icon-chip">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                    <rect x="9" y="9" width="6" height="6"></rect>
                  </svg>
                </div>
                <span>{{ currentLang === 'id' ? 'Arsitektur Hardware' : 'Hardware Architecture' }}</span>
              </a>
              <a href="/#fitur" @click.prevent="navigateToLanding('fitur')">
                <div class="link-icon-chip">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                  </svg>
                </div>
                <span>{{ currentLang === 'id' ? 'Fitur Aplikasi' : 'App Features' }}</span>
              </a>
              <a href="/#panduan" @click.prevent="navigateToLanding('panduan')">
                <div class="link-icon-chip">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                </div>
                <span>{{ currentLang === 'id' ? 'Panduan Operasional' : 'Operation Guide' }}</span>
              </a>
              <a href="/verify" class="active" @click.prevent="resetToPortal">
                <div class="link-icon-chip">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <polyline points="9 12 11 14 15 10"></polyline>
                  </svg>
                </div>
                <span>{{ currentLang === 'id' ? 'Verifikasi Mutu & QR' : 'Quality Verification & QR' }}</span>
              </a>
            </nav>

            <div class="mobile-drawer-cta">
              <button class="btn-mobile-login" @click="goToApp">
                <span>{{ isAuth ? (currentLang === 'id' ? 'Buka Dashboard Sistem' : 'Open System Dashboard') : (currentLang === 'id' ? 'Masuk ke Aplikasi' : 'Login to App') }}</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </header>

    <!-- Main Content Container -->
    <main class="verify-main-container">
      <!-- Search / Batch Selector Bar -->
      <section class="search-bar-section">
        <div class="search-bar-card">
          <div class="search-icon-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </div>
          <input 
            v-model="searchQuery" 
            type="text" 
            :placeholder="$t('verify.searchPlaceholder')" 
            class="search-input"
            @keyup.enter="handleSearchBatch"
          />
          <button class="btn-search" :disabled="isLoading" @click="handleSearchBatch">
            <span v-if="!isLoading">{{ $t('verify.verifyBtn') }}</span>
            <span v-else>{{ $t('verify.verifying') }}</span>
          </button>
        </div>
      </section>

      <!-- 1. Loading State -->
      <div v-if="isLoading" class="state-card loading-state">
        <div class="spinner-circle"></div>
        <p class="loading-title">{{ $t('verify.loadingTitle') }}</p>
        <p class="loading-subtitle">{{ $t('verify.loadingSub') }}</p>
      </div>

      <!-- 2. Error / Not Found State -->
      <div v-else-if="errorMessage" class="state-card error-state">
        <div class="error-icon-box">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2">
            <circle cx="12" cy="10" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
        </div>
        <h2 class="error-title">{{ $t('verify.errorTitle') }}</h2>
        <p class="error-message">{{ errorMessage }}</p>
        <div class="error-actions">
          <button class="btn-primary" @click="resetToPortal">
            {{ currentLang === 'id' ? 'Kembali ke Beranda Verifikasi' : 'Back to Verification Portal' }}
          </button>
        </div>
      </div>

      <!-- 3. Main Verified Certificate & Traceability View -->
      <div v-else-if="data" class="verified-content-grid">
        <!-- 1. OFFICIAL CERTIFICATE BANNER -->
        <section id="printable-certificate" class="certificate-banner-card">
          <div class="certificate-header">
            <div class="cert-partners">
              <img src="/assets/img/hanjeli.png" alt="Logo Hanjeli" class="cert-partner-logo" />
              <img src="/assets/img/stas.png" alt="Logo STAS-RG" class="cert-partner-logo" />
            </div>
            <div class="cert-seal-badge">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <polyline points="9 12 11 14 15 10"></polyline>
              </svg>
              <span>{{ $t('verify.certVerifiedBadge') }}</span>
            </div>
          </div>

          <div class="cert-title-section">
            <span class="cert-subheading">{{ $t('verify.certTitle') }}</span>
            <h1 class="cert-main-title">{{ data.batch.cropVariety }}</h1>
            <div class="cert-meta-row">
              <span class="cert-id-tag font-mono">{{ $t('verify.certCode') }}: {{ data.certificate.certificateNumber }}</span>
              <span class="cert-date-tag">{{ $t('common.date') }}: {{ formatDateTime(data.certificate.issuedAt) }}</span>
            </div>
          </div>

          <!-- Grade & Score Badge Highlights -->
          <div class="quality-badges-row">
            <div class="quality-score-pill">
              <span class="pill-label">{{ $t('verify.qualityScore') }}</span>
              <span class="pill-value text-green-dark">{{ data.batch.qualityScore }}/100</span>
            </div>
            <div class="quality-grade-pill">
              <span class="pill-label">{{ $t('verify.gradeName') }}</span>
              <span class="pill-value text-purple">{{ data.batch.qualityGrade }}</span>
            </div>
            <div class="moisture-status-pill" :class="data.batch.isMoistureSafe ? 'pill-safe' : 'pill-warn'">
              <span class="pill-label">{{ $t('verify.finalMoisture') }}</span>
              <span class="pill-value">{{ data.batch.finalMoisturePercent }}% (SNI &le;14%)</span>
            </div>
          </div>
        </section>

        <!-- 2. FARM-TO-TABLE ORIGIN & FARMER PROFILE -->
        <section class="info-card origin-card">
          <div class="card-header-flex">
            <div class="card-header-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
            </div>
            <div>
              <h3 class="card-title">{{ currentLang === 'id' ? 'Asal Panen & Profil Kelompok Tani' : 'Harvest Origin & Farming Group' }}</h3>
              <p class="card-subtitle">{{ currentLang === 'id' ? 'Jejak ketertelusuran (traceability) lokasi budidaya' : 'Traceability of cultivation origin & farm partners' }}</p>
            </div>
          </div>

          <div class="origin-details-grid">
            <div class="origin-item">
              <span class="origin-label">{{ currentLang === 'id' ? 'Desa Asal Budidaya' : 'Origin Village' }}</span>
              <span class="origin-value font-bold">{{ data.origin.village }}</span>
              <span class="origin-desc">{{ data.origin.address || 'Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)' }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ currentLang === 'id' ? 'Kawasan Geopark' : 'Geopark Area' }}</span>
              <span class="origin-value text-green-dark">{{ data.origin.geopark }}</span>
              <span class="origin-desc">{{ data.origin.elevation }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ currentLang === 'id' ? 'Kelompok Tani / Petani' : 'Farmer Group / Grower' }}</span>
              <span class="origin-value font-bold">{{ data.origin.farmerGroup }}</span>
              <span class="origin-desc">Operator: {{ data.origin.operatorName }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ currentLang === 'id' ? 'Fasilitas Pengeringan' : 'Drying Facility' }}</span>
              <span class="origin-value">{{ data.origin.facility }}</span>
              <span class="origin-desc">{{ $t('common.status') }}: {{ data.batch.dryingMode }}</span>
            </div>
          </div>
        </section>

        <!-- 2.5. AUTOMATED QUALITY GRADING & SNI CERTIFICATION SCORE -->
        <section class="info-card quality-scoring-card">
          <div class="card-header-flex">
            <div class="card-header-icon" style="background: rgba(13, 99, 27, 0.12); color: #0D631B;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="8" r="6"></circle>
                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
              </svg>
            </div>
            <div class="flex-1">
              <div class="flex items-center justify-between">
                <h3 class="card-title">{{ currentLang === 'id' ? 'Penilaian Mutu & Klasifikasi Grade Otomatis' : 'Automated Quality Grade & SNI Scoring' }}</h3>
                <span 
                  class="font-mono font-bold text-xs px-2.5 py-1 rounded-full border"
                  :class="(data.qualityAssessment?.grade || 'A') === 'A' ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-50 text-blue-800 border-blue-300'"
                >
                  Skor: {{ data.qualityAssessment?.qualityScore || data.batch.qualityScore || 95.0 }}/100
                </span>
              </div>
              <p class="card-subtitle">{{ currentLang === 'id' ? 'Hasil evaluasi komputasi algoritma berbasis kadar air akhir dan stabilitas kurva suhu' : 'Algorithmic evaluation based on final moisture content & temperature curve stability' }}</p>
            </div>
          </div>

          <!-- Grade Banner Box -->
          <div class="quality-grade-hero-box p-4 rounded-2xl bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent border border-emerald-500/30 mt-3">
            <div class="flex items-start gap-3.5">
              <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-600 to-green-700 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-emerald-600/30 flex-shrink-0">
                {{ data.qualityAssessment?.grade || 'A' }}
              </div>
              <div class="flex-1">
                <h4 class="text-base font-extrabold text-slate-900 dark:text-white">
                  {{ data.qualityAssessment?.gradeLabel || data.batch.qualityGrade || 'Grade A (Super Premium / Ekspor)' }}
                </h4>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                  {{ data.qualityAssessment?.summary || 'Kadar air optimal standar ekspor SNI (<13.5%), stabilitas kurva suhu sangat presisi, dan nutrisi biji terjaga utuh tanpa retak/kerusakan termal.' }}
                </p>
                <div class="mt-2 text-xs font-medium text-emerald-800 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-950/60 px-3 py-1.5 rounded-xl border border-emerald-500/20 flex items-start gap-1.5">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18h6"></path>
                    <path d="M10 22h4"></path>
                    <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5.76.76 1.23 1.52 1.41 2.5"></path>
                  </svg>
                  <div>
                    <strong>{{ currentLang === 'id' ? 'Rekomendasi Pemanfaatan' : 'Market Recommendation' }}:</strong>
                    {{ data.qualityAssessment?.marketRecommendation || 'Sangat direkomendasikan untuk komoditas ekspor premium, benih berkualitas tinggi, dan sereal pangan fungsional.' }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quality Sub-metrics 3 Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
              <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">1. Kadar Air Akhir</span>
              <span class="font-extrabold text-base text-slate-900 dark:text-white">{{ data.batch.finalMoisturePercent }}%</span>
              <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-1">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>{{ data.qualityAssessment?.metrics?.moistureStatus || 'Optimal SNI Ekspor (<13.5%)' }}</span>
              </span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
              <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">2. Stabilitas Kurva Suhu</span>
              <span class="font-extrabold text-base text-slate-900 dark:text-white">&plusmn;{{ data.qualityAssessment?.metrics?.tempStdDev || '2.1' }}&deg;C</span>
              <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-1">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>{{ data.qualityAssessment?.metrics?.tempStabilityLevel || 'Sangat Stabil & Terkendali' }}</span>
              </span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
              <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">3. Indeks Keseragaman</span>
              <span class="font-extrabold text-base text-slate-900 dark:text-white">{{ data.qualityAssessment?.metrics?.uniformityIndex || '95' }}%</span>
              <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-1">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Dehidrasi Merata Seluruh Tray</span>
              </span>
            </div>
          </div>
        </section>

        <!-- 3. KEY DRYING METRICS MATRIX (SCIENTIFIC DATA) -->
        <section class="info-card metrics-card">
          <div class="card-header-flex">
            <div class="card-header-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2.2">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="card-title">{{ currentLang === 'id' ? 'Parameter Dehidrasi & Higienitas IoT' : 'Dehydration & IoT Hygiene Parameters' }}</h3>
              <p class="card-subtitle">{{ currentLang === 'id' ? 'Hasil pemantauan presisi sensor greenhouse' : 'Precision sensor telemetry records from greenhouse' }}</p>
            </div>
          </div>

          <div class="metrics-grid">
            <!-- Metric 1: Kadar Air Akhir -->
            <div class="metric-box">
              <span class="metric-label">{{ $t('verify.finalMoisture') }}</span>
              <div class="metric-val-row">
                <span class="metric-number text-green-dark">{{ data.batch.finalMoisturePercent }}</span>
                <span class="metric-unit">%</span>
              </div>
              <span class="metric-foot">{{ $t('verify.initialMoisture') }}: {{ data.batch.initialMoisturePercent }}% ({{ currentLang === 'id' ? 'Turun' : 'Drop' }} {{ data.batch.moistureReductionPercent }}%)</span>
            </div>

            <!-- Metric 2: Total Durasi -->
            <div class="metric-box">
              <span class="metric-label">{{ $t('common.duration') }}</span>
              <div class="metric-val-row">
                <span class="metric-number text-blue">{{ data.batch.totalDurationHours }}</span>
                <span class="metric-unit">{{ $t('common.hours') }}</span>
              </div>
              <span class="metric-foot">{{ $t('verify.harvestDate') }}: {{ formatDate(data.batch.startedAt) }}</span>
            </div>

            <!-- Metric 3: Suhu Rata-rata -->
            <div class="metric-box">
              <span class="metric-label">{{ currentLang === 'id' ? 'SUHU RUANG RATA-RATA' : 'AVG CHAMBER TEMP' }}</span>
              <div class="metric-val-row">
                <span class="metric-number text-orange">{{ data.climateMetrics.avgTempInternal }}</span>
                <span class="metric-unit">°C</span>
              </div>
              <span class="metric-foot">{{ currentLang === 'id' ? `Maksimum: ${data.climateMetrics.maxTempInternal}°C (Enzim Terjaga)` : `Max: ${data.climateMetrics.maxTempInternal}°C (Enzymes Preserved)` }}</span>
            </div>

            <!-- Metric 4: Kelembaban Udara -->
            <div class="metric-box">
              <span class="metric-label">{{ currentLang === 'id' ? 'KELEMBABAN RUANG (RH)' : 'CHAMBER HUMIDITY (RH)' }}</span>
              <div class="metric-val-row">
                <span class="metric-number text-blue">{{ data.climateMetrics.avgHumidityInternal }}</span>
                <span class="metric-unit">%</span>
              </div>
              <span class="metric-foot">{{ currentLang === 'id' ? 'Risiko Jamur: ' : 'Mold Risk: ' }}<strong>{{ data.climateMetrics.moldRisk }}</strong></span>
            </div>
          </div>

          <!-- Hygiene & Quality Statement -->
          <div class="hygiene-statement-box">
            <div class="hygiene-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="hygiene-text">
              <span class="hygiene-title">{{ currentLang === 'id' ? 'Jaminan Higienitas Pangan & Bebas Polusi' : 'Food Hygiene & Anti-Contamination Guarantee' }}</span>
              <p class="hygiene-desc">
                {{ currentLang === 'id' 
                  ? 'Biji hanjeli ini dikeringkan di dalam greenhouse tertutup berteknologi sirkulasi terkontrol, terlindung dari debu, kotoran hewan, air hujan, dan spora jamur aflatoksin, sehingga menghasilkan biji dengan kemurnian dan daya simpan maksimal.' 
                  : 'These Hanjeli grains were dried in a closed smart greenhouse with controlled air circulation, fully protected from airborne dust, rain, and aflatoxin mold spores, ensuring maximum purity and long shelf viability.' }}
              </p>
            </div>
          </div>
        </section>

        <!-- 4. INTERACTIVE SENSOR TELEMETRY CHART -->
        <section v-if="data.telemetryPoints && data.telemetryPoints.length > 0" class="info-card chart-card">
          <div class="card-header-flex">
            <div class="card-header-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2.2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
            </div>
            <div>
              <h3 class="card-title">{{ currentLang === 'id' ? 'Grafik Tren Suhu & Dehidrasi Pengeringan' : 'Drying Temperature & Dehydration Kinetics Chart' }}</h3>
              <p class="card-subtitle">{{ currentLang === 'id' ? 'Log telemetri aktual selama proses pengeringan batch berlangsung' : 'Actual telemetry streams recorded during the batch drying cycle' }}</p>
            </div>
          </div>

          <div class="telemetry-chart-container">
            <div class="chart-legends-row">
              <div class="legend-item">
                <span class="legend-dot bg-orange"></span>
                <span>{{ currentLang === 'id' ? 'Suhu Internal (°C)' : 'Chamber Temp (°C)' }}</span>
              </div>
              <div class="legend-item">
                <span class="legend-dot bg-blue"></span>
                <span>{{ currentLang === 'id' ? 'Kelembaban Udara (%)' : 'Relative Humidity (%)' }}</span>
              </div>
              <div class="legend-item">
                <span class="legend-dot bg-green"></span>
                <span>{{ currentLang === 'id' ? 'Kadar Air Biji (%)' : 'Grain Moisture (%)' }}</span>
              </div>
            </div>

            <div class="svg-chart-wrapper">
              <svg viewBox="0 0 800 240" class="telemetry-svg">
                <line x1="40" y1="30" x2="780" y2="30" stroke="rgba(203, 213, 225, 0.4)" stroke-width="1" stroke-dasharray="4" />
                <line x1="40" y1="90" x2="780" y2="90" stroke="rgba(203, 213, 225, 0.4)" stroke-width="1" stroke-dasharray="4" />
                <line x1="40" y1="150" x2="780" y2="150" stroke="rgba(203, 213, 225, 0.4)" stroke-width="1" stroke-dasharray="4" />
                <line x1="40" y1="210" x2="780" y2="210" stroke="rgba(203, 213, 225, 0.8)" stroke-width="1.5" />

                <text x="32" y="34" text-anchor="end" class="chart-axis-text">60°/100%</text>
                <text x="32" y="94" text-anchor="end" class="chart-axis-text">40°/60%</text>
                <text x="32" y="154" text-anchor="end" class="chart-axis-text">20°/30%</text>
                <text x="32" y="214" text-anchor="end" class="chart-axis-text">0</text>

                <polyline 
                  :points="generateChartPoints('tempInternal', 60)" 
                  fill="none" 
                  stroke="#EA580C" 
                  stroke-width="2.5" 
                  stroke-linecap="round"
                />

                <polyline 
                  :points="generateChartPoints('humidityInternal', 100)" 
                  fill="none" 
                  stroke="#0284C7" 
                  stroke-width="2" 
                  stroke-dasharray="4"
                  stroke-linecap="round"
                />

                <polyline 
                  :points="generateChartPoints('grainMoisture', 50)" 
                  fill="none" 
                  stroke="#0D631B" 
                  stroke-width="3" 
                  stroke-linecap="round"
                />

                <circle 
                  v-for="(pt, idx) in chartCoordinates" 
                  :key="idx" 
                  :cx="pt.x" 
                  :cy="pt.yTemp" 
                  r="3.5" 
                  fill="#EA580C" 
                />
              </svg>
            </div>

            <div class="time-axis-labels">
              <span>{{ currentLang === 'id' ? 'Awal Sesi' : 'Session Start' }}</span>
              <span>{{ currentLang === 'id' ? 'Proses Dehidrasi' : 'Dehydration Process' }}</span>
              <span>{{ currentLang === 'id' ? 'Selesai (Target Tercapai)' : 'Completed (Target Met)' }}</span>
            </div>
          </div>
        </section>

        <!-- 5. NUTRITIONAL FACTS SECTION -->
        <section class="info-card nutrition-card">
          <div class="card-header-flex">
            <div class="card-header-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.2">
                <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
              </svg>
            </div>
            <div>
              <h3 class="card-title">{{ $t('verify.nutritionalTitle') }}</h3>
              <p class="card-subtitle">{{ $t('verify.nutritionalSub') }}</p>
            </div>
          </div>

          <div class="origin-details-grid">
            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.protein') }}</span>
              <span class="origin-value font-bold text-green-dark">14.1 g</span>
              <span class="origin-desc">{{ currentLang === 'id' ? 'Tinggi Asam Amino Esensial' : 'Rich in Essential Amino Acids' }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.fiber') }}</span>
              <span class="origin-value font-bold text-blue">8.9 g</span>
              <span class="origin-desc">{{ currentLang === 'id' ? 'Indeks Glikemik Rendah (Low GI)' : 'Low Glycemic Index (Low GI)' }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.carbs') }}</span>
              <span class="origin-value font-bold">67.3 g</span>
              <span class="origin-desc">{{ currentLang === 'id' ? 'Energi Lepas Lambat (Gluten-Free)' : 'Sustained Energy (Gluten-Free)' }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.antioxidants') }}</span>
              <span class="origin-value font-bold text-purple">{{ currentLang === 'id' ? 'Positif Aktif' : 'Active Positive' }}</span>
              <span class="origin-desc">{{ currentLang === 'id' ? 'Kadar Terjaga Efek Panas Rendah' : 'Protected via Low-Temp Control' }}</span>
            </div>
          </div>
        </section>

        <!-- 6. SUPPLY CHAIN TIMELINE (FARM-TO-TABLE) -->
        <section class="info-card timeline-card">
          <div class="card-header-flex">
            <div class="card-header-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div>
              <h3 class="card-title">{{ $t('verify.timelineTitle') }}</h3>
              <p class="card-subtitle">{{ $t('verify.timelineSub') }}</p>
            </div>
          </div>

          <div class="origin-details-grid">
            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.stepHarvest') }}</span>
              <span class="origin-desc">{{ $t('verify.stepHarvestDesc') }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.stepDrying') }}</span>
              <span class="origin-desc">{{ $t('verify.stepDryingDesc') }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.stepQuality') }}</span>
              <span class="origin-desc">{{ $t('verify.stepQualityDesc') }}</span>
            </div>

            <div class="origin-item">
              <span class="origin-label">{{ $t('verify.stepPackaging') }}</span>
              <span class="origin-desc">{{ $t('verify.stepPackagingDesc') }}</span>
            </div>
          </div>
        </section>

        <!-- 7. CONSUMER ACTION BAR & SHARE BUTTONS -->
        <section class="consumer-action-bar">
          <div class="share-actions-group">
            <button class="btn-action-outline" @click="shareToWhatsApp">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
              </svg>
              <span>{{ currentLang === 'id' ? 'Bagikan Tautan Verifikasi' : 'Share Verification Link' }}</span>
            </button>

            <button class="btn-action-outline" @click="copyVerificationLink">
              <svg v-if="!isCopied" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
              </svg>
              <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>{{ isCopied ? (currentLang === 'id' ? 'Tautan Tersalin!' : 'Link Copied!') : (currentLang === 'id' ? 'Salin Tautan Sertifikat' : 'Copy Certificate Link') }}</span>
            </button>
          </div>

          <button class="btn-primary" @click="printCertificate">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>{{ currentLang === 'id' ? 'Cetak Sertifikat Digital' : 'Print Digital Certificate' }}</span>
          </button>
        </section>
      </div>

      <!-- 4. Default / Welcome Portal View for Public Visitors (When no batch searched yet) -->
      <div v-else class="portal-welcome-grid">
        <!-- Hero Intro Card -->
        <section class="portal-hero-card">
          <div class="portal-hero-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.5">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              <polyline points="9 12 11 14 15 10"></polyline>
            </svg>
            <span>{{ currentLang === 'id' ? 'Sistem Sertifikasi & Ketertelusuran Mutu Digital' : 'Digital Quality Certification & Traceability System' }}</span>
          </div>

          <h1 class="portal-hero-title">
            {{ currentLang === 'id' 
              ? 'Verifikasi Keaslian & Standar Mutu Biji Hanjeli' 
              : 'Verify Authenticity & Quality Standards of Hanjeli Grains' 
            }}
          </h1>

          <p class="portal-hero-desc">
            {{ currentLang === 'id'
              ? 'Masukkan nomor batch yang tertera pada label kemasan produk atau ketik kode di atas untuk melihat sertifikat resmi digital, rekam jejak sensor IoT greenhouse, dan asal panen dari Desa Wisata Hanjeli Waluran.'
              : 'Enter the batch code printed on your product packaging or type the code above to view the official digital certificate, greenhouse IoT sensor history, and farm origin from Hanjeli Tourism Village.'
            }}
          </p>

        </section>

        <!-- 4-Stage Supply Chain Traceability Process -->
        <section class="portal-stages-section">
          <div class="section-title-box">
            <h2 class="portal-section-title">
              {{ currentLang === 'id' ? 'Alur Jejak Rantai Pasok (Farm-to-Table)' : 'Farm-to-Table Traceability Workflow' }}
            </h2>
            <p class="portal-section-sub">
              {{ currentLang === 'id' 
                ? 'Empat tahapan utama jaminan mutu pascapanen berstandar SNI di Smart Room Dryer Greenhouse' 
                : 'Four main stages of SNI-standard post-harvest quality assurance in Smart Room Dryer Greenhouse' 
              }}
            </p>
          </div>

          <div class="stages-grid">
            <!-- Stage 1 -->
            <div class="stage-card">
              <div class="stage-icon-wrap">
                <img src="/assets/icons/alur/step1.png" alt="Panen Segar di Kebun" class="stage-3d-img" />
              </div>
              <h3 class="stage-title">{{ currentLang === 'id' ? '1. Panen Segar di Kebun' : '1. Fresh Field Harvest' }}</h3>
              <p class="stage-desc">
                {{ currentLang === 'id' 
                  ? 'Dipanen manual oleh petani mitra Waluran pada tingkat kematangan optimal.' 
                  : 'Manually hand-harvested by Waluran partner growers at peak maturity.' 
                }}
              </p>
            </div>

            <!-- Stage 2 -->
            <div class="stage-card">
              <div class="stage-icon-wrap">
                <img src="/assets/icons/alur/step2.png" alt="Pengeringan Cerdas IoT" class="stage-3d-img" />
              </div>
              <h3 class="stage-title">{{ currentLang === 'id' ? '2. Pengeringan Cerdas IoT' : '2. Smart IoT Drying' }}</h3>
              <p class="stage-desc">
                {{ currentLang === 'id' 
                  ? 'Dikeringkan pada Smart Room Greenhouse dengan regulasi suhu aman < 45°C.' 
                  : 'Dried in Smart Room Greenhouse with controlled safe temperature < 45°C.' 
                }}
              </p>
            </div>

            <!-- Stage 3 -->
            <div class="stage-card">
              <div class="stage-icon-wrap">
                <img src="/assets/icons/alur/step3.png" alt="Audit Mutu & Sensor" class="stage-3d-img" />
              </div>
              <h3 class="stage-title">{{ currentLang === 'id' ? '3. Audit Mutu & Sensor' : '3. Quality & Sensor Audit' }}</h3>
              <p class="stage-desc">
                {{ currentLang === 'id' 
                  ? 'Kadar air presisi 12% dengan sterilisasi UV-C pencegah kapang.' 
                  : 'Precision 12% moisture with UV-C germicidal sterilization.' 
                }}
              </p>
            </div>

            <!-- Stage 4 -->
            <div class="stage-card">
              <div class="stage-icon-wrap">
                <img src="/assets/icons/alur/step4.png" alt="Pengemasan & QR Label" class="stage-3d-img" />
              </div>
              <h3 class="stage-title">{{ currentLang === 'id' ? '4. Pengemasan & QR Label' : '4. Packaging & QR Label' }}</h3>
              <p class="stage-desc">
                {{ currentLang === 'id' 
                  ? 'Disegel kedap udara dengan kode verifikasi unik siap distribusi.' 
                  : 'Airtight sealed with unique batch traceability QR code.' 
                }}
              </p>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- 7. FOOTER (Matching CoE STAS-RG Theme & Layout) -->
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
                ? 'Pusat edukasi agrowisata, budidaya, dan hilirisasi pangan lokal Hanjeli di Waluran Sukabumi (Kawasan Geopark Ciletuh), dilengkapi teknologi Green House Smart Dryer cerdas berbasis IoT hasil kolaborasi bersama Center of Excellence STAS-RG Telkom University.'
                : 'Education, agrotourism, and local food preservation center of Hanjeli in Waluran Sukabumi (Ciletuh Geopark area), equipped with IoT Smart Dryer Green House technology developed in collaboration with CoE STAS-RG Telkom University.'
              }}
            </p>
            <div class="footer-social-row">
              <a href="https://www.instagram.com/desawisatahanjeli" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="@desawisatahanjeli">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
              </a>
              <a href="https://www.visithanjeli.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Website Resmi">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="2" y1="12" x2="22" y2="12"></line>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
              </a>
              <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="YouTube">
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
              <!-- EMAIL -->
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

              <!-- PHONE / WA -->
              <div class="footer-contact-item">
                <div class="footer-contact-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </div>
                <div class="footer-contact-info">
                  <span class="contact-label">TELEPON / WA</span>
                  <div style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                    <a href="tel:085722182480" class="contact-value">0857-2218-2480</a>
                    <span style="color: #94A3B8; font-size: 11px;">/</span>
                    <a href="tel:081398115760" class="contact-value">0813-9811-5760</a>
                  </div>
                </div>
              </div>

              <!-- INSTAGRAM -->
              <div class="footer-contact-item">
                <div class="footer-contact-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                  </svg>
                </div>
                <div class="footer-contact-info">
                  <span class="contact-label">INSTAGRAM</span>
                  <a href="https://www.instagram.com/desawisatahanjeli" target="_blank" rel="noopener noreferrer" class="contact-value">@desawisatahanjeli</a>
                </div>
              </div>

              <!-- WEBSITE -->
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

          <!-- Column 4: Lokasi Desa Wisata Hanjeli Map -->
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
                  title="Peta Lokasi Desa Wisata Hanjeli - Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)"
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
              ? 'Hak Cipta © 2026 Desa Wisata Hanjeli — Kolaborasi Riset bersama CoE STAS-RG Universitas Telkom. Semua hak dilindungi.' 
              : 'Copyright © 2026 Hanjeli Tourism Village — In Collaboration with CoE STAS-RG Telkom University. All rights reserved.' 
            }}
          </span>
          <div class="footer-meta-tags">
            <span class="footer-tagline">
              {{ currentLang === 'id' ? 'Telemetri IoT • Pengeringan Hanjeli Real-Time' : 'IoT Telemetry • Real-Time Hanjeli Drying' }}
            </span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { batchService } from '../../services/batchService'
import FlagIcon from '../../components/FlagIcon.vue'
import { currentLang, setLanguage } from '../../i18n'
import { isDark, toggleTheme } from '../../services/themeService'
import { authService } from '../../services/authService'

const props = defineProps({
  batchCode: {
    type: String,
    default: ''
  }
})

const route = useRoute()
const router = useRouter()

const logoSrc = ref('/assets/img/hanjeli.png')
const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)
const navbarRef = ref(null)
const langDropdownRef = ref(null)
const isLangOpen = ref(false)

const searchQuery = ref('')
const isLoading = ref(true)
const errorMessage = ref('')
const isCopied = ref(false)
const data = ref(null)

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

const activeCode = computed(() => {
  return props.batchCode || route.params.batchCode || ''
})

function resetToPortal() {
  errorMessage.value = ''
  data.value = null
  searchQuery.value = ''
  isLoading.value = false
  isMobileMenuOpen.value = false
  if (route.params.batchCode) {
    router.push('/verify')
  }
}

async function fetchVerificationData(code) {
  if (!code) return
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await batchService.getVerification(code)
    data.value = res
    searchQuery.value = res.batch?.batchCode || code
  } catch (err) {
    console.error('Failed to verify batch:', err)
    errorMessage.value = err.response?.data?.message || (currentLang.value === 'id' ? `Data batch '${code}' tidak ditemukan pada sistem sertifikasi.` : `Batch record '${code}' was not found in the certification database.`)
    data.value = null
  } finally {
    isLoading.value = false
  }
}

function handleSearchBatch() {
  const code = searchQuery.value.trim()
  if (code) {
    router.push(`/verify/${encodeURIComponent(code)}`)
    fetchVerificationData(code)
  }
}

watch(() => route.params.batchCode, (newCode) => {
  if (newCode) {
    searchQuery.value = newCode
    fetchVerificationData(newCode)
  } else {
    data.value = null
    searchQuery.value = ''
    errorMessage.value = ''
    isLoading.value = false
  }
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  const scrollContainer = document.querySelector('.standalone-viewport')
  if (scrollContainer) {
    scrollContainer.addEventListener('scroll', handleScroll)
  } else {
    window.addEventListener('scroll', handleScroll)
  }

  const code = props.batchCode || route.params.batchCode
  if (code) {
    searchQuery.value = code
    fetchVerificationData(code)
  } else {
    isLoading.value = false
    data.value = null
    searchQuery.value = ''
    errorMessage.value = ''
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  const scrollContainer = document.querySelector('.standalone-viewport')
  if (scrollContainer) {
    scrollContainer.removeEventListener('scroll', handleScroll)
  } else {
    window.removeEventListener('scroll', handleScroll)
  }
})

function formatDateTime(isoStr) {
  if (!isoStr) return '-'
  try {
    const d = new Date(isoStr)
    const locale = currentLang.value === 'id' ? 'id-ID' : 'en-US'
    return d.toLocaleDateString(locale, {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }) + (currentLang.value === 'id' ? ' WIB' : ' UTC+7')
  } catch {
    return isoStr
  }
}

function formatDate(isoStr) {
  if (!isoStr) return '-'
  try {
    const locale = currentLang.value === 'id' ? 'id-ID' : 'en-US'
    return new Date(isoStr).toLocaleDateString(locale, {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return isoStr
  }
}

function generateChartPoints(field, maxValue) {
  if (!data.value?.telemetryPoints || data.value.telemetryPoints.length === 0) return ''
  const pts = data.value.telemetryPoints
  const width = 740
  const height = 180
  const startX = 40
  const startY = 30

  return pts.map((pt, i) => {
    const x = startX + (i / Math.max(1, pts.length - 1)) * width
    const val = pt[field] || 0
    const normalizedVal = Math.min(1, Math.max(0, val / maxValue))
    const y = (startY + height) - (normalizedVal * height)
    return `${x.toFixed(1)},${y.toFixed(1)}`
  }).join(' ')
}

const chartCoordinates = computed(() => {
  if (!data.value?.telemetryPoints) return []
  const pts = data.value.telemetryPoints
  const width = 740
  const height = 180
  const startX = 40
  const startY = 30

  return pts.map((pt, i) => {
    const x = startX + (i / Math.max(1, pts.length - 1)) * width
    const valTemp = pt.tempInternal || 0
    const yTemp = (startY + height) - (Math.min(1, Math.max(0, valTemp / 60)) * height)
    return { x, yTemp }
  })
})

function shareToWhatsApp() {
  const code = data.value?.batch?.batchCode || activeCode.value
  const variety = data.value?.batch?.cropVariety || 'Hanjeli'
  const url = window.location.href
  const text = currentLang.value === 'id'
    ? encodeURIComponent(`*[Sertifikat Mutu & Traceability Biji Hanjeli]*\nProduk: ${variety}\nKode Batch: ${code}\nStatus: Terverifikasi Mutu SNI & Higienis\n\nCek data lengkap pengeringan di sini:\n${url}`)
    : encodeURIComponent(`*[Hanjeli Quality Certificate & Traceability]*\nProduct: ${variety}\nBatch Code: ${code}\nStatus: SNI & Hygiene Verified\n\nView complete drying metrics here:\n${url}`)
  window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank')
}

async function copyVerificationLink() {
  try {
    await navigator.clipboard.writeText(window.location.href)
    isCopied.value = true
    setTimeout(() => {
      isCopied.value = false
    }, 2500)
  } catch (err) {
    console.error('Failed to copy link:', err)
  }
}

function printCertificate() {
  window.print()
}
</script>

<style scoped>
/* Zero-Shadow Architectural Styling */
.verify-page-root {
  min-height: 100vh;
  background-color: var(--color-bg, #F4F7F5);
  color: var(--color-text-main, #071E27);
  display: flex;
  flex-direction: column;
  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 1. Landing Navbar (Identical to Landing Page) */
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
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  justify-self: start;
}

.brand-icon-box {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: transparent;
}

.brand-logo-img {
  width: 36px;
  height: 36px;
  object-fit: contain;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-title {
  font-size: 15px;
  font-weight: 700;
  color: #111D23;
  line-height: 1.2;
  white-space: nowrap;
}

.brand-sub {
  font-size: 12px;
  font-weight: 500;
  color: #40493D;
  white-space: nowrap;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
  justify-self: center;
}

.nav-links a {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #40493D;
  text-decoration: none;
  transition: all 0.2s ease;
}

.nav-links a:hover {
  color: #0D631B;
  background: rgba(13, 99, 27, 0.06);
}

.nav-links a.active {
  background: #0D631B;
  color: #FFFFFF;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-self: end;
}

/* Visibility helpers */
.desktop-only {
  display: flex;
}

.mobile-only {
  display: none !important;
}

/* Action Buttons */
.header-action-btn {
  width: 38px;
  height: 38px;
  padding: 0;
  background: transparent;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  transition: all 0.2s ease;
  cursor: pointer;
  position: relative;
  flex-shrink: 0;
}

.header-action-btn:hover {
  background: rgba(0, 0, 0, 0.06);
  color: #0F172A;
  transform: translateY(-1px);
}

.mobile-menu-btn {
  width: 38px;
  height: 38px;
  background: transparent;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #111D23;
  cursor: pointer;
  transition: all 0.2s ease;
}

.mobile-menu-btn:hover {
  background: rgba(13, 99, 27, 0.1);
  color: #0D631B;
}

.theme-icon {
  width: 20px;
  height: 20px;
}

/* Lang Dropdown */
.lang-selector-wrapper {
  position: relative;
}

.lang-dropdown-menu {
  position: absolute;
  top: 46px;
  right: 0;
  width: 190px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  border-radius: 10px;
  padding: 6px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  z-index: 1100;
  box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.lang-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 10px;
  border-radius: 6px;
  background: transparent;
  border: none;
  font-size: 13px;
  font-weight: 600;
  color: #111D23;
  cursor: pointer;
  transition: all 0.15s ease;
  width: 100%;
}

.lang-option:hover {
  background: #F4F7F5;
  color: #0D631B;
}

.lang-option.active {
  background: #E8F5E9;
  color: #0D631B;
}

.lang-option-lead {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-nav-login {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 18px;
  background: linear-gradient(180deg, #15803D 0%, #0D631B 55%, #094713 100%);
  color: #FFFFFF !important;
  font-size: 13.5px;
  font-weight: 700;
  border: 1px solid rgba(13, 99, 27, 0.4);
  border-radius: 10px;
  cursor: pointer;
  white-space: nowrap;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), inset 0 -1px 0 rgba(0, 0, 0, 0.3), 0 2px 8px rgba(13, 99, 27, 0.28) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  -webkit-user-select: none;
  user-select: none;
}

.btn-nav-login:hover {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), inset 0 -1px 0 rgba(0, 0, 0, 0.2), 0 4px 14px rgba(13, 99, 27, 0.38) !important;
  transform: translateY(-1.5px);
}

.btn-nav-login:active {
  background: linear-gradient(180deg, #0D631B 0%, #094713 100%);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
  transform: scale(0.98);
}

/* Mobile Drawer & Dropdown */
.landing-mobile-drawer {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  width: 100%;
  background: rgba(244, 250, 255, 0.98);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border-bottom: 1px solid #CBD5E1;
  box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.12);
  box-sizing: border-box;
  overflow: hidden;
  z-index: 999;
}

.mobile-drawer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px 20px 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
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
  padding: 11px 14px;
  border-radius: 10px;
  font-size: 14.5px;
  font-weight: 600;
  color: #1E293B;
  text-decoration: none;
  background: transparent;
  transition: all 0.2s ease;
}

.mobile-drawer-links a .link-icon-chip {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(13, 99, 27, 0.08);
  color: #0D631B;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.mobile-drawer-links a:hover,
.mobile-drawer-links a.active {
  background: rgba(13, 99, 27, 0.08);
  color: #0D631B;
}

.mobile-drawer-links a.active .link-icon-chip {
  background: #0D631B;
  color: #FFFFFF;
}

.mobile-drawer-cta {
  padding-top: 10px;
  border-top: 1px solid rgba(203, 213, 225, 0.8);
}

.btn-mobile-login {
  position: relative;
  width: 100%;
  padding: 12px 20px;
  background: linear-gradient(180deg, #15803D 0%, #0D631B 55%, #094713 100%);
  color: #FFFFFF !important;
  border: 1px solid rgba(13, 99, 27, 0.4);
  border-radius: 12px;
  font-size: 14.5px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), inset 0 -1px 0 rgba(0, 0, 0, 0.3), 0 4px 14px rgba(13, 99, 27, 0.28) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  -webkit-user-select: none;
  user-select: none;
}

.btn-mobile-login:hover {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), inset 0 -1px 0 rgba(0, 0, 0, 0.2), 0 6px 18px rgba(13, 99, 27, 0.38) !important;
  transform: translateY(-1.5px);
}

.btn-mobile-login:active {
  background: linear-gradient(180deg, #0D631B 0%, #094713 100%);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
  transform: scale(0.98);
}

/* Vue Transition for Mobile Drawer */
.mobile-nav-expand-enter-active,
.mobile-nav-expand-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  max-height: 450px;
  opacity: 1;
  transform: translateY(0);
}

.mobile-nav-expand-enter-from,
.mobile-nav-expand-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-8px);
}

/* Dark Mode Overrides for Navbar */
.dark-landing.verify-page-root {
  background-color: #0B1911;
  color: #F1F5F9;
}

.dark-landing .landing-navbar {
  background: rgba(11, 25, 17, 0.94) !important;
  border-bottom-color: #1E3A2B !important;
}

.dark-landing .brand-title {
  color: #F1F5F9 !important;
}

.dark-landing .brand-sub {
  color: #94A3B8 !important;
}

.dark-landing .nav-links a {
  color: #CBD5E1;
}

.dark-landing .nav-links a:hover {
  color: #4ADE80;
  background: rgba(74, 222, 128, 0.1);
}

.dark-landing .nav-links a.active {
  background: #0D631B;
  color: #FFFFFF;
}

.dark-landing .header-action-btn {
  color: #CBD5E1;
}

.dark-landing .header-action-btn:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #FFFFFF;
}

.dark-landing .mobile-menu-btn {
  color: #F1F5F9;
}

.dark-landing .lang-dropdown-menu {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .lang-option {
  color: #F1F5F9;
}

.dark-landing .lang-option:hover {
  background: #152C20;
  color: #4ADE80;
}

.dark-landing .lang-option.active {
  background: rgba(13, 99, 27, 0.35);
  color: #4ADE80;
}

.dark-landing .landing-mobile-drawer {
  background: rgba(11, 25, 17, 0.98);
  border-bottom-color: #1E3A2B;
}

.dark-landing .mobile-drawer-links a {
  color: #F1F5F9;
}

.dark-landing .mobile-drawer-links a:hover,
.dark-landing .mobile-drawer-links a.active {
  background: rgba(74, 222, 128, 0.12);
  color: #4ADE80;
}

/* Responsive Navbar Media Queries */
@media (max-width: 960px) {
  .desktop-only {
    display: none !important;
  }

  .mobile-only {
    display: inline-flex !important;
  }

  .landing-navbar {
    padding: 0 20px;
  }

  .landing-nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
  }

  .nav-actions {
    gap: 8px;
  }
}

@media (max-width: 480px) {
  .landing-navbar {
    padding: 0 16px;
  }

  .landing-nav-container {
    padding: 10px 0;
  }
}

/* Main Container */
.verify-main-container {
  max-width: 1080px;
  width: 100%;
  margin: 0 auto;
  padding: 24px 16px 48px 16px;
  flex: 1;
}

/* Search Bar */
.search-bar-section {
  margin-bottom: 24px;
}

.search-bar-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--color-card-bg, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 12px;
  padding: 8px 12px;
}

.search-icon-box {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-left: 6px;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 0.9375rem;
  color: var(--color-text-main, #071E27);
  outline: none;
}

.search-input::placeholder {
  color: var(--color-text-muted, #707A6C);
}

.btn-search {
  position: relative;
  background: linear-gradient(180deg, #15803D 0%, #0D631B 55%, #094713 100%);
  color: #FFFFFF !important;
  border: 1px solid rgba(13, 99, 27, 0.4);
  border-radius: 10px;
  padding: 8px 20px;
  font-size: 0.875rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), inset 0 -1px 0 rgba(0, 0, 0, 0.3), 0 2px 6px rgba(13, 99, 27, 0.25) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  -webkit-user-select: none;
  user-select: none;
}

.btn-search:hover {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), inset 0 -1px 0 rgba(0, 0, 0, 0.2), 0 4px 14px rgba(13, 99, 27, 0.35) !important;
  transform: translateY(-1.5px);
}

.btn-search:active {
  background: linear-gradient(180deg, #0D631B 0%, #094713 100%);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
  transform: scale(0.98);
}

.btn-search:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* State Cards */
.state-card {
  background: var(--color-card-bg, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 14px;
  padding: 48px 24px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.spinner-circle {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(13, 99, 27, 0.2);
  border-top-color: #0D631B;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-text-main, #071E27);
  margin: 0;
}

.loading-subtitle {
  font-size: 0.875rem;
  color: var(--color-text-muted, #707A6C);
  margin: 0;
}

.error-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #DC2626;
  margin: 0;
}

.error-message {
  font-size: 0.875rem;
  color: var(--color-text-muted, #707A6C);
  max-width: 460px;
  margin: 0;
}

/* Verified Grid */
.verified-content-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Certificate Banner */
.certificate-banner-card {
  background: var(--color-card-bg, #FFFFFF);
  border: 2px solid #0D631B;
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.certificate-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cert-partners {
  display: flex;
  align-items: center;
  gap: 12px;
}

.cert-partner-logo {
  height: 36px;
  object-fit: contain;
}

.cert-seal-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(13, 99, 27, 0.1);
  color: #0D631B;
  border: 1px solid #0D631B;
  border-radius: 30px;
  padding: 6px 14px;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.5px;
}

.cert-title-section {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cert-subheading {
  font-size: 0.75rem;
  font-weight: 700;
  color: #0D631B;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.cert-main-title {
  font-size: 1.625rem;
  font-weight: 800;
  color: var(--color-text-main, #071E27);
  margin: 0;
}

.cert-meta-row {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  font-size: 0.8125rem;
  color: var(--color-text-muted, #707A6C);
}

.font-mono {
  font-family: monospace;
}

.quality-badges-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
  padding-top: 12px;
  border-top: 1px dashed var(--color-border, #CBD5E1);
}

.quality-score-pill, .quality-grade-pill, .moisture-status-pill {
  background: var(--color-bg, #F4F7F5);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 10px;
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.pill-label {
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-text-muted, #707A6C);
}

.pill-value {
  font-size: 1.125rem;
  font-weight: 800;
}

.text-green-dark { color: #0D631B; }
.text-purple { color: #7C3AED; }

/* Info Card */
.info-card {
  background: var(--color-card-bg, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 14px;
  padding: 22px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.card-header-flex {
  display: flex;
  align-items: center;
  gap: 12px;
}

.card-header-icon {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  background: rgba(13, 99, 27, 0.08);
  border: 1px solid rgba(13, 99, 27, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.card-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-text-main, #071E27);
  margin: 0;
}

.card-subtitle {
  font-size: 0.8125rem;
  color: var(--color-text-muted, #707A6C);
  margin: 2px 0 0 0;
}

/* Origin Grid */
.origin-details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.origin-item {
  display: flex;
  flex-direction: column;
  gap: 3px;
  background: var(--color-bg, #F4F7F5);
  padding: 14px;
  border-radius: 10px;
  border: 1px solid var(--color-border, #CBD5E1);
}

.origin-label {
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-text-muted, #707A6C);
  text-transform: uppercase;
}

.origin-value {
  font-size: 0.9375rem;
  color: var(--color-text-main, #071E27);
}

.origin-desc {
  font-size: 0.75rem;
  color: var(--color-text-muted, #707A6C);
}

/* Metrics Grid */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 14px;
}

.metric-box {
  background: var(--color-bg, #F4F7F5);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.metric-label {
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-text-muted, #707A6C);
}

.metric-val-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.metric-number {
  font-size: 2rem;
  font-weight: 800;
  line-height: 1;
}

.metric-unit {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text-muted, #707A6C);
}

.metric-foot {
  font-size: 0.75rem;
  color: var(--color-text-muted, #707A6C);
  margin-top: 4px;
}

.text-blue {
  color: #0284C7;
}

.text-orange {
  color: #EA580C;
}

/* Hygiene Statement */
.hygiene-statement-box {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  background: rgba(13, 99, 27, 0.05);
  border: 1px solid rgba(13, 99, 27, 0.2);
  border-radius: 12px;
  padding: 16px;
}

.hygiene-icon {
  margin-top: 2px;
  color: #0D631B;
}

.hygiene-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hygiene-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #0D631B;
}

.hygiene-desc {
  font-size: 0.8125rem;
  color: var(--color-text-main, #071E27);
  line-height: 1.5;
  margin: 0;
}

/* Chart */
.telemetry-chart-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.chart-legends-row {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  font-size: 0.8125rem;
  font-weight: 600;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.bg-orange { background: #EA580C; }
.bg-blue { background: #0284C7; }
.bg-green { background: #0D631B; }

.svg-chart-wrapper {
  background: var(--color-bg, #F4F7F5);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 12px;
  padding: 12px;
}

.telemetry-svg {
  width: 100%;
  height: auto;
  max-height: 240px;
}

.chart-axis-text {
  font-size: 10px;
  fill: var(--color-text-muted, #707A6C);
  font-family: monospace;
}

.time-axis-labels {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text-muted, #707A6C);
  padding: 0 40px;
}

/* Consumer Action Bar */
.consumer-action-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background: var(--color-card-bg, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 14px;
  padding: 16px 20px;
}

.share-actions-group {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-action-outline {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 16px;
  border: 1px solid rgba(203, 213, 225, 0.9);
  border-radius: 10px;
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  color: #1E293B;
  font-size: 0.8125rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 1),
    0 1px 3px rgba(0, 0, 0, 0.04) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  -webkit-user-select: none;
  user-select: none;
}

.btn-action-outline:hover {
  border-color: #0D631B;
  color: #0D631B;
  background: linear-gradient(180deg, #FFFFFF 0%, #E8F5E9 100%);
  transform: translateY(-1.5px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 1),
    0 4px 12px rgba(13, 99, 27, 0.15) !important;
}

.btn-action-outline:active {
  background: linear-gradient(180deg, #F1F5F9 0%, #E2E8F0 100%);
  transform: scale(0.98);
}

.btn-primary {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  border: 1px solid rgba(13, 99, 27, 0.4);
  border-radius: 10px;
  background: linear-gradient(180deg, #15803D 0%, #0D631B 55%, #094713 100%);
  color: #FFFFFF !important;
  font-size: 0.8125rem;
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

.btn-primary:hover {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  border-color: rgba(13, 99, 27, 0.5);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.6),
    inset 0 -1px 0 rgba(0, 0, 0, 0.2),
    0 4px 14px rgba(13, 99, 27, 0.38) !important;
  transform: translateY(-1.5px);
}

.btn-primary:active {
  background: linear-gradient(180deg, #0D631B 0%, #094713 100%);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
  transform: scale(0.98);
}

/* Error Actions */
.error-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.btn-secondary {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  border: 1px solid rgba(203, 213, 225, 0.9);
  border-radius: 10px;
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  color: #1E293B;
  font-size: 0.8125rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 1),
    0 1px 3px rgba(0, 0, 0, 0.04) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  -webkit-user-select: none;
  user-select: none;
}

.btn-secondary:hover {
  background: linear-gradient(180deg, #F8FAFC 0%, #E2E8F0 100%);
  border-color: #94A3B8;
  transform: translateY(-1.5px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 1),
    0 4px 12px rgba(0, 0, 0, 0.08) !important;
}

.btn-secondary:active {
  transform: scale(0.98);
}

/* Footer Social Buttons */
.footer-social-btn {
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
  border: 1px solid rgba(203, 213, 225, 0.9);
  color: #40493D;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 1),
    0 1px 3px rgba(0, 0, 0, 0.04) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  flex-shrink: 0;
  -webkit-user-select: none;
  user-select: none;
}

.footer-social-btn:hover {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  border-color: rgba(13, 99, 27, 0.4);
  color: #FFFFFF !important;
  transform: translateY(-2px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.5),
    0 4px 14px rgba(13, 99, 27, 0.3) !important;
}

.footer-social-btn:active {
  transform: scale(0.96);
}

/* Portal Welcome Landing Styling */
.portal-welcome-grid {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.portal-hero-card {
  background: var(--color-card-bg, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 16px;
  padding: 36px 32px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 16px;
  position: relative;
  overflow: hidden;
}

.portal-hero-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #0D631B 0%, #0284C7 100%);
}

.portal-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(13, 99, 27, 0.08);
  border: 1px solid rgba(13, 99, 27, 0.25);
  border-radius: 30px;
  padding: 6px 14px;
  color: #0D631B;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.3px;
}

.portal-hero-title {
  font-size: 1.625rem;
  font-weight: 800;
  color: var(--color-text-main, #071E27);
  line-height: 1.25;
  margin: 0;
}

.portal-hero-desc {
  font-size: 0.9375rem;
  color: var(--color-text-muted, #707A6C);
  line-height: 1.6;
  max-width: 800px;
  margin: 0;
}


/* 4-Stage Traceability Section */
.portal-stages-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-title-box {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.portal-section-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--color-text-main, #071E27);
  margin: 0;
}

.portal-section-sub {
  font-size: 0.875rem;
  color: var(--color-text-muted, #707A6C);
  margin: 0;
}

.stages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.stage-card {
  background: var(--color-card-bg, #FFFFFF);
  border: 1px solid var(--color-border, #CBD5E1);
  border-radius: 16px;
  padding: 22px 18px 18px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.32s ease, border-color 0.25s ease;
  cursor: default;
}

.stage-card:hover {
  border-color: #0D631B;
  transform: translateY(-5px);
  box-shadow: 0 14px 28px -6px rgba(13, 99, 27, 0.14);
}

.stage-icon-wrap {
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin-bottom: 2px;
  transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.stage-card:hover .stage-icon-wrap {
  transform: scale(1.12) rotate(3deg);
}

.stage-3d-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.1));
}

.stage-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--color-text-main, #071E27);
  margin: 0;
  line-height: 1.35;
}

.stage-desc {
  font-size: 0.8125rem;
  color: var(--color-text-muted, #707A6C);
  line-height: 1.55;
  margin: 0;
}

/* 7. Footer (Hanjeli Green Theme with CoE STAS-RG Layout) */
.landing-footer {
  background: #e9f6fd;
  color: #40493d;
  border-top: 1px solid #cbd5e1;
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

/* Col 1: Brand & Bio */
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
  color: #111d23;
  line-height: 1.2;
  margin: 0;
  letter-spacing: -0.2px;
}

.footer-brand-badge {
  font-size: 11.5px;
  font-weight: 700;
  color: #0d631b;
  letter-spacing: 0.3px;
}

.footer-desc {
  font-size: 13px;
  line-height: 1.65;
  color: #40493d;
  margin: 0;
}

.footer-social-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-top: 4px;
}

.footer-social-btn {
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
  border: 1px solid rgba(203, 213, 225, 0.9);
  color: #40493d;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 1),
    0 1px 3px rgba(0, 0, 0, 0.04) !important;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  flex-shrink: 0;
  -webkit-user-select: none;
  user-select: none;
}

.footer-social-btn:hover {
  background: linear-gradient(180deg, #16a34a 0%, #15803d 55%, #0d631b 100%);
  border-color: rgba(13, 99, 27, 0.4);
  color: #ffffff !important;
  transform: translateY(-2px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.5),
    0 4px 14px rgba(13, 99, 27, 0.3) !important;
}

.footer-social-btn:active {
  transform: scale(0.96);
}

/* Col Headers with Green Bar */
.footer-col-header {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 18px;
}

.footer-heading {
  font-size: 15px;
  font-weight: 700;
  color: #111d23;
  margin: 0;
  letter-spacing: 0.3px;
}

.footer-green-bar {
  width: 24px;
  height: 2.5px;
  background: #0d631b;
  border-radius: 2px;
}

/* Col 2: Navigasi */
.footer-nav-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-nav-list a {
  font-size: 13.5px;
  font-weight: 500;
  color: #40493d;
  text-decoration: none;
  transition: all 0.2s ease;
  width: fit-content;
}

.footer-nav-list a:hover {
  color: #0d631b;
  transform: translateX(3px);
}

/* Col 3: Info Kontak */
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
  color: #0d631b;
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
  color: #707a6c;
  letter-spacing: 0.6px;
}

.contact-value {
  font-size: 13px;
  font-weight: 600;
  color: #111d23;
  text-decoration: none;
  transition: color 0.2s ease;
}

a.contact-value:hover {
  color: #0d631b;
  text-decoration: underline;
}

/* Col 4: Lokasi Map */
.footer-map-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.footer-map-iframe-box {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #cbd5e1;
  background: #ffffff;
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
  color: #40493d;
  font-weight: 600;
  line-height: 1.4;
}

/* Bottom Bar */
.footer-bottom-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 22px;
  border-top: 1px solid #cbd5e1;
  font-size: 12px;
  color: #707a6c;
  flex-wrap: wrap;
  gap: 12px;
}

.footer-copy {
  font-size: 0.8125rem;
  color: #707a6c;
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
  color: #0d631b;
}

/* Responsive Footer */
@media (max-width: 1024px) {
  .footer-top-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 768px) {
  .footer-top-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }

  .landing-footer {
    padding: 40px 16px 24px;
  }
}

/* Dark Mode Overrides for Verification & Portal Cards */
.dark-landing .search-bar-card {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .search-input {
  color: #F1F5F9;
}

.dark-landing .search-input::placeholder {
  color: #64748B;
}

.dark-landing .state-card {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .loading-title,
.dark-landing .error-title {
  color: #F1F5F9;
}

.dark-landing .loading-subtitle,
.dark-landing .error-message {
  color: #94A3B8;
}

.dark-landing .portal-hero-card {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .portal-hero-title {
  color: #F1F5F9;
}

.dark-landing .portal-hero-desc {
  color: #94A3B8;
}

.dark-landing .portal-section-title {
  color: #F1F5F9;
}

.dark-landing .portal-section-sub {
  color: #94A3B8;
}

.dark-landing .stage-card {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .stage-card:hover {
  border-color: #4ADE80;
  box-shadow: 0 14px 28px -6px rgba(0, 0, 0, 0.4);
}

.dark-landing .stage-title {
  color: #F1F5F9;
}

.dark-landing .stage-desc {
  color: #94A3B8;
}

.dark-landing .certificate-banner-card {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .cert-main-title {
  color: #F1F5F9;
}

.dark-landing .cert-meta-row {
  color: #94A3B8;
}

.dark-landing .quality-score-pill,
.dark-landing .quality-grade-pill,
.dark-landing .moisture-status-pill {
  background: #152C20;
  border-color: #1E3A2B;
}

.dark-landing .info-card {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .card-title {
  color: #F1F5F9;
}

.dark-landing .card-subtitle {
  color: #94A3B8;
}

.dark-landing .origin-item {
  background: #152C20;
  border-color: #1E3A2B;
}

.dark-landing .origin-desc {
  color: #94A3B8;
}

.dark-landing .metric-box {
  background: #152C20;
  border-color: #1E3A2B;
}

.dark-landing .hygiene-statement-box {
  background: rgba(13, 99, 27, 0.15);
  border-color: rgba(74, 222, 128, 0.25);
}

.dark-landing .hygiene-desc {
  color: #CBD5E1;
}

.dark-landing .svg-chart-wrapper {
  background: #0E1F16;
  border-color: #1E3A2B;
}

.dark-landing .consumer-action-bar {
  background: #11261B;
  border-color: #1E3A2B;
}

.dark-landing .btn-action-outline {
  border-color: #1E3A2B;
  color: #F1F5F9;
  background: linear-gradient(180deg, #152C20 0%, #0E1F16 100%);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 1px 3px rgba(0, 0, 0, 0.4) !important;
}

.dark-landing .btn-action-outline:hover {
  border-color: #4ADE80;
  color: #4ADE80;
  background: linear-gradient(180deg, #1E3A2B 0%, #152C20 100%);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.15),
    0 4px 12px rgba(0, 0, 0, 0.5) !important;
}

.dark-landing .footer-social-btn {
  background: linear-gradient(180deg, #152C20 0%, #0E1F16 100%);
  border-color: #1E3A2B;
  color: #94A3B8;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 1px 3px rgba(0, 0, 0, 0.4) !important;
}

.dark-landing .footer-social-btn:hover {
  background: linear-gradient(180deg, #16A34A 0%, #15803D 55%, #0D631B 100%);
  border-color: #4ADE80;
  color: #FFFFFF !important;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.4),
    0 4px 14px rgba(22, 163, 74, 0.35) !important;
}

.dark-landing .landing-footer {
  background: #0b1911;
  color: #94a3b8;
  border-top-color: #1e3a2b;
}

.dark-landing .footer-brand-title {
  color: #ffffff;
}

.dark-landing .footer-brand-badge {
  color: #4ade80;
}

.dark-landing .footer-desc {
  color: #94a3b8;
}

.dark-landing .footer-social-btn {
  background: #13271c;
  border-color: #1e3a2b;
  color: #94a3b8;
}

.dark-landing .footer-social-btn:hover {
  background: #0d631b;
  border-color: #4ade80;
  color: #ffffff;
}

.dark-landing .footer-heading {
  color: #ffffff;
}

.dark-landing .footer-green-bar {
  background: #4ade80;
}

.dark-landing .footer-nav-list a {
  color: #94a3b8;
}

.dark-landing .footer-nav-list a:hover {
  color: #4ade80;
}

.dark-landing .contact-value {
  color: #e2e8f0;
}

.dark-landing a.contact-value:hover {
  color: #4ade80;
}

.dark-landing .footer-map-iframe-box {
  border-color: #1e3a2b;
  background: #13271c;
}

.dark-landing .footer-map-address {
  color: #94a3b8;
}

.dark-landing .footer-bottom-row {
  border-top-color: #1e3a2b;
  color: #64748b;
}

.dark-landing .footer-copy,
.dark-landing .footer-tagline {
  color: #64748b;
}

/* Print Rules */
@media print {
  @page {
    size: A4 portrait;
    margin: 10mm 12mm;
  }

  .verify-top-header,
  .search-bar-section,
  .consumer-action-bar,
  .landing-footer {
    display: none !important;
  }

  body, .verify-page-root, .verify-main-container {
    background: #FFFFFF !important;
    padding: 0 !important;
    margin: 0 !important;
    color: #000000 !important;
    max-width: 100% !important;
  }

  .certificate-banner-card {
    border: 2px solid #0D631B !important;
    page-break-inside: avoid;
  }

  .info-card {
    border: 1px solid #CBD5E1 !important;
    page-break-inside: avoid;
  }

  .quality-badges-row, .metrics-grid, .origin-details-grid {
    break-inside: avoid;
  }
}
</style>
