<template>
  <div class="devices-guide-page">
    <Transition name="fade-guide" mode="out-in">
      <GuideSkeleton v-if="isInitialLoading" />
      <div v-else class="guide-loaded-content">
        <div class="guide-container">
          <!-- Header Area -->
          <div class="page-header-row">
            <div class="page-header-group">
              <div class="page-title-wrap">
                <div>
                  <h1 class="page-title">{{ $t('guide.title') }}</h1>
              <p class="page-subtitle">{{ $t('guide.subtitle') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- System Readiness Overview Banner -->
      <div class="system-overview-banner" :class="isSystemActive ? 'banner-ready' : 'banner-standby'">
        <div class="banner-left">
          <div class="banner-status-icon">
            <svg v-if="isSystemActive" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.4">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.4">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
          </div>
          <div>
            <div class="banner-title">
              {{ isSystemActive ? $t('guide.allSensorsReady') : $t('guide.espWaiting') }}
            </div>
            <p class="banner-desc">
              {{ isSystemActive ? $t('guide.allSensorsDesc') : $t('guide.espWaitingDesc') }}
            </p>
          </div>
        </div>

        <div class="banner-indicators">
          <div class="indicator-item">
            <span class="ind-lbl">{{ $t('guide.mainMcu') }}</span>
            <span class="ind-val text-green-dark">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                <rect x="9" y="9" width="6" height="6"></rect>
              </svg>
              ESP32 DevKit V1
            </span>
          </div>
          <div class="indicator-sep"></div>
          <div class="indicator-item">
            <span class="ind-lbl">{{ $t('guide.commChannel') }}</span>
            <span class="ind-val text-green-dark">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
              </svg>
              Dual Mode (Wi-Fi & BLE)
            </span>
          </div>
          <div class="indicator-sep"></div>
          <div class="indicator-item">
            <span class="ind-lbl">{{ $t('guide.totalModules') }}</span>
            <span class="ind-val text-green-dark">
              {{ $t('guide.modulesUnit', { count: hardwareCatalog.length }) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="guide-nav-tabs">
        <button 
          class="guide-tab-btn" 
          :class="{ active: activeTab === 'devices' }"
          @click="activeTab = 'devices'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
            <line x1="8" y1="21" x2="16" y2="21"></line>
            <line x1="12" y1="17" x2="12" y2="21"></line>
          </svg>
          <span>{{ $t('guide.tabCatalog') }} ({{ hardwareCatalog.length }})</span>
        </button>

        <button 
          class="guide-tab-btn" 
          :class="{ active: activeTab === 'sop' }"
          @click="activeTab = 'sop'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span>{{ $t('guide.tabSop') }}</span>
        </button>

        <button 
          class="guide-tab-btn" 
          :class="{ active: activeTab === 'faq' }"
          @click="activeTab = 'faq'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <span>{{ $t('guide.tabFaq') }}</span>
        </button>

        <button 
          class="guide-tab-btn" 
          :class="{ active: activeTab === 'arduino' }"
          @click="activeTab = 'arduino'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="16 18 22 12 16 6"></polyline>
            <polyline points="8 6 2 12 8 18"></polyline>
          </svg>
          <span>{{ $t('guide.tabArduino') }} ({{ arduinoPrograms.length }})</span>
        </button>
      </div>

      <!-- TAB 1: DAFTAR SENSOR (RINGKAS & SIMPEL UNTUK OPERATOR) -->
      <div v-if="activeTab === 'devices'" class="tab-pane">
        <div class="devices-simple-grid">
          <div 
            v-for="hw in hardwareCatalog" 
            :key="hw.id" 
            class="sensor-simple-card"
          >
            <!-- Card Top Header -->
            <div class="sensor-card-top">
              <div class="sensor-icon-box" :class="getIconBoxClass(hw.iconType)">
                <!-- Microchip (ESP32) -->
                <svg v-if="hw.iconType === 'mcu'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                  <rect x="9" y="9" width="6" height="6"></rect>
                  <line x1="9" y1="1" x2="9" y2="4"></line>
                  <line x1="15" y1="1" x2="15" y2="4"></line>
                  <line x1="9" y1="20" x2="9" y2="23"></line>
                  <line x1="15" y1="20" x2="15" y2="23"></line>
                  <line x1="20" y1="9" x2="23" y2="9"></line>
                  <line x1="20" y1="14" x2="23" y2="14"></line>
                  <line x1="1" y1="9" x2="4" y2="9"></line>
                  <line x1="1" y1="14" x2="4" y2="14"></line>
                </svg>
                <!-- Temp / Humidity Sensor -->
                <svg v-else-if="hw.iconType === 'temp'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                </svg>
                <!-- Solar / Pyranometer -->
                <svg v-else-if="hw.iconType === 'sun'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="4"></circle>
                  <path d="M12 2v2"></path>
                  <path d="M12 20v2"></path>
                  <path d="m4.93 4.93 1.41 1.41"></path>
                  <path d="m17.66 17.66 1.41 1.41"></path>
                  <path d="M2 12h2"></path>
                  <path d="M20 12h2"></path>
                  <path d="m6.34 17.66-1.41 1.41"></path>
                  <path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
                <!-- Weight / Moisture -->
                <svg v-else-if="hw.iconType === 'weight'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="9"></circle>
                  <polyline points="12 7 12 12 15 15"></polyline>
                </svg>
                <!-- Relay / Actuator -->
                <svg v-else-if="hw.iconType === 'relay'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <!-- WiFi / Wireless -->
                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                  <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                  <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
              </div>

              <div class="sensor-title-info">
                <h3 class="sensor-simple-title">{{ hw.name }}</h3>
                <span class="sensor-category-tag">{{ hw.category }}</span>
              </div>

              <div class="sensor-status-tag" :class="isSystemActive ? 'tag-online' : 'tag-standby'">
                <span class="status-pulse-dot" :class="isSystemActive ? 'online' : 'standby'"></span>
                <span>{{ isSystemActive ? $t('guide.statusActive') : $t('guide.statusStandby') }}</span>
              </div>
            </div>

            <!-- Card Description -->
            <p class="sensor-simple-desc">{{ hw.purpose }}</p>

            <!-- Card Bottom Meta -->
            <div class="sensor-card-bottom">
              <div class="sensor-loc-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>{{ hw.location }}</span>
              </div>

              <div class="sensor-unit-pill">
                <span>{{ hw.unitBadge }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Partnership & Developer Section -->
        <div class="collab-strip-card">
          <div class="collab-item">
            <HanjeliLogo :size="44" />
            <div class="collab-text">
              <span class="collab-role">{{ $t('guide.collabOwnerRole') }}</span>
              <strong class="collab-name">{{ $t('guide.collabOwnerName') }}</strong>
              <p class="collab-desc">{{ $t('guide.collabOwnerDesc') }}</p>
            </div>
          </div>
          <div class="collab-divider"></div>
          <div class="collab-item">
            <StasLogo :size="44" />
            <div class="collab-text">
              <span class="collab-role">{{ $t('guide.collabDevRole') }}</span>
              <strong class="collab-name">{{ $t('guide.collabDevName') }}</strong>
              <p class="collab-desc">{{ $t('guide.collabDevDesc') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 2: SOP PENGERINGAN HANJELI -->
      <div v-if="activeTab === 'sop'" class="tab-pane">
        <div class="sop-container">
          <div class="sop-hero-card">
            <div class="sop-hero-icon">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
              </svg>
            </div>
            <div>
              <h2 class="sop-hero-title">{{ $t('guide.sopHeroTitle') }}</h2>
              <p class="sop-hero-subtitle">{{ $t('guide.sopHeroSubtitle') }}</p>
            </div>
          </div>

          <div class="sop-steps-grid">
            <!-- Step 1 -->
            <div class="sop-step-card">
              <div class="sop-step-header">
                <div class="sop-step-num">1</div>
                <h3 class="sop-step-title">{{ $t('guide.sopStep1Title') }}</h3>
              </div>
              <div class="sop-step-body">
                <p>{{ $t('guide.sopStep1Desc') }}</p>
                <div class="sop-tip-box">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                  <span>{{ $t('guide.sopStep1Tip') }}</span>
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="sop-step-card">
              <div class="sop-step-header">
                <div class="sop-step-num">2</div>
                <h3 class="sop-step-title">{{ $t('guide.sopStep2Title') }}</h3>
              </div>
              <div class="sop-step-body">
                <p>{{ $t('guide.sopStep2Desc') }}</p>
                <div class="sop-tip-box">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                  <span>{{ $t('guide.sopStep2Tip') }}</span>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="sop-step-card">
              <div class="sop-step-header">
                <div class="sop-step-num">3</div>
                <h3 class="sop-step-title">{{ $t('guide.sopStep3Title') }}</h3>
              </div>
              <div class="sop-step-body">
                <p>{{ $t('guide.sopStep3Desc') }}</p>
                <div class="sop-tip-box">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                  <span>{{ $t('guide.sopStep3Tip') }}</span>
                </div>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="sop-step-card">
              <div class="sop-step-header">
                <div class="sop-step-num">4</div>
                <h3 class="sop-step-title">{{ $t('guide.sopStep4Title') }}</h3>
              </div>
              <div class="sop-step-body">
                <p>{{ $t('guide.sopStep4Desc') }}</p>
                <div class="sop-tip-box">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                  <span>{{ $t('guide.sopStep4Tip') }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Safety & Cleanliness Callout -->
          <div class="sop-safety-card">
            <div class="safety-icon-box">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#BA1A1A" stroke-width="2.2">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <div>
              <h4 class="safety-title">{{ $t('guide.sopSafetyTitle') }}</h4>
              <ul class="safety-list">
                <li><strong>{{ $t('guide.sopSafetyItem1Bold') }}</strong> {{ $t('guide.sopSafetyItem1Text') }}</li>
                <li><strong>{{ $t('guide.sopSafetyItem2Bold') }}</strong> {{ $t('guide.sopSafetyItem2Text') }}</li>
                <li><strong>{{ $t('guide.sopSafetyItem3Bold') }}</strong> {{ $t('guide.sopSafetyItem3Text') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 3: SOLUSI MASALAH & BANTUAN (FAQ) -->
      <div v-if="activeTab === 'faq'" class="tab-pane">
        <div class="faq-container">
          <div class="faq-hero-card">
            <div class="faq-hero-icon">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#005DB7" stroke-width="2.2">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <div>
              <h2 class="faq-hero-title">{{ $t('guide.faqHeroTitle') }}</h2>
              <p class="faq-hero-subtitle">{{ $t('guide.faqHeroSubtitle') }}</p>
            </div>
          </div>

          <div class="faq-list">
            <!-- FAQ 1 -->
            <div class="faq-card" :class="{ open: openFaq === 1 }">
              <button class="faq-header" @click="openFaq = openFaq === 1 ? null : 1">
                <span class="faq-question">{{ $t('guide.faq1Question') }}</span>
                <svg class="faq-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <div v-if="openFaq === 1" class="faq-body">
                <ol class="faq-steps">
                  <li>{{ $t('guide.faq1Step1') }}</li>
                  <li v-html="$t('guide.faq1Step2')"></li>
                  <li v-html="$t('guide.faq1Step3')"></li>
                  <li>{{ $t('guide.faq1Step4') }}</li>
                </ol>
              </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-card" :class="{ open: openFaq === 2 }">
              <button class="faq-header" @click="openFaq = openFaq === 2 ? null : 2">
                <span class="faq-question">{{ $t('guide.faq2Question') }}</span>
                <svg class="faq-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <div v-if="openFaq === 2" class="faq-body">
                <p v-html="$t('guide.faq2Answer')"></p>
              </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-card" :class="{ open: openFaq === 3 }">
              <button class="faq-header" @click="openFaq = openFaq === 3 ? null : 3">
                <span class="faq-question">{{ $t('guide.faq3Question') }}</span>
                <svg class="faq-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <div v-if="openFaq === 3" class="faq-body">
                <p>{{ $t('guide.faq3Answer') }}</p>
              </div>
            </div>
          </div>

          <!-- Help Contact Box -->
          <div class="faq-contact-card">
            <div class="contact-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
            </div>
            <div class="contact-info">
              <h4 class="contact-title">{{ $t('guide.contactTitle') }}</h4>
              <p class="contact-sub">{{ $t('guide.contactSub') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 4: PROGRAM ARDUINO / ESP32 (2 PROGRAM) -->
      <div v-if="activeTab === 'arduino'" class="tab-pane">
        <div class="arduino-tab-container">
          <!-- Intro Banner -->
          <div class="arduino-intro-banner">
            <div class="arduino-intro-content">
              <div class="arduino-logo-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2">
                  <polyline points="16 18 22 12 16 6"></polyline>
                  <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
              </div>
              <div class="arduino-intro-text">
                <h3 class="arduino-intro-title">{{ $t('guide.arduinoIntroTitle') }}</h3>
                <p class="arduino-intro-sub" v-html="$t('guide.arduinoIntroSub')"></p>
              </div>
            </div>
          </div>

          <!-- Program Selector Cards -->
          <div class="program-selector-grid">
            <div 
              v-for="prog in arduinoPrograms" 
              :key="prog.id"
              class="program-card"
              :class="{ active: selectedProgramId === prog.id }"
              @click="selectedProgramId = prog.id"
            >
              <div class="program-card-header">
                <span class="program-badge" :class="prog.badgeClass">{{ prog.badge }}</span>
                <span class="program-filename">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  {{ prog.filename }}
                </span>
              </div>
              <h4 class="program-card-title">{{ prog.title }}</h4>
              <p class="program-card-desc">{{ prog.desc }}</p>
              <div class="program-card-footer">
                <span class="program-select-indicator">
                  <svg v-if="selectedProgramId === prog.id" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  {{ selectedProgramId === prog.id ? $t('guide.selectedProgram') : $t('guide.selectThisProgram') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Active Program Details & Code Viewer -->
          <div class="active-program-wrapper">
            <!-- Program Meta Card -->
            <div class="program-meta-card">
              <div class="program-meta-grid">
                <div class="meta-item">
                  <span class="meta-label">{{ $t('guide.targetBoard') }}</span>
                  <span class="meta-val font-semibold">{{ selectedProgram.target }}</span>
                </div>
                <div class="meta-item">
                  <span class="meta-label">{{ $t('guide.serialBaudrate') }}</span>
                  <span class="meta-val font-mono">{{ selectedProgram.baudrate }} bps</span>
                </div>
                <div class="meta-item-full">
                  <span class="meta-label">{{ $t('guide.requiredLibraries') }}</span>
                  <div class="meta-lib-tags">
                    <span v-for="lib in selectedProgram.libraries" :key="lib" class="lib-tag">
                      {{ lib }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Top Actions: Copy & Download -->
              <div class="program-actions">
                <button 
                  class="action-btn-copy" 
                  @click="copyProgramCode(selectedProgram.code)"
                  :class="{ copied: isCopied }"
                >
                  <svg v-if="!isCopied" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                  </svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>{{ isCopied ? $t('guide.copiedToClipboard') : $t('guide.copyProgram') }}</span>
                </button>

                <button class="action-btn-download" @click="downloadInoFile(selectedProgram)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>{{ $t('guide.downloadIno') }}</span>
                </button>
              </div>
            </div>

            <!-- Code Editor Viewer -->
            <div class="code-editor-box">
              <div class="code-editor-header">
                <div class="code-header-left">
                  <div class="code-dots">
                    <span class="code-dot dot-red"></span>
                    <span class="code-dot dot-yellow"></span>
                    <span class="code-dot dot-green"></span>
                  </div>
                  <span class="code-file-title">{{ selectedProgram.filename }}</span>
                </div>
                <div class="code-header-right">
                  <span class="code-lines-count">{{ $t('guide.linesCount', { count: selectedProgram.code.split('\n').length }) }}</span>
                  <button class="code-header-copy-btn" @click="copyProgramCode(selectedProgram.code)">
                    <svg v-if="isCopied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline-block; vertical-align:middle; margin-right:4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:4px;"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span>{{ isCopied ? $t('guide.copied') : $t('guide.copyCode') }}</span>
                  </button>
                </div>
              </div>

              <div class="code-editor-content">
                <pre class="code-pre"><code>{{ selectedProgram.code }}</code></pre>
              </div>
            </div>

            <!-- Step by Step Flashing Guide -->
            <div class="flashing-guide-card">
              <h4 class="guide-steps-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                {{ $t('guide.flashGuideTitle') }}
              </h4>
              <div class="steps-grid">
                <div class="step-card">
                  <div class="step-num">1</div>
                  <div class="step-desc" v-html="$t('guide.flashStep1Text')"></div>
                </div>
                <div class="step-card">
                  <div class="step-num">2</div>
                  <div class="step-desc" v-html="$t('guide.flashStep2Text')"></div>
                </div>
                <div class="step-card">
                  <div class="step-num">3</div>
                  <div class="step-desc" v-html="$t('guide.flashStep3Text')"></div>
                </div>
                <div class="step-card">
                  <div class="step-num">4</div>
                  <div class="step-desc" v-html="$t('guide.flashStep4Text')"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import HanjeliLogo from '../../components/HanjeliLogo.vue';
import StasLogo from '../../components/StasLogo.vue';
import connectionManager from '../../services/connectionManager';
import { ESP32_DUAL_MODE_CODE, ESP32_SIMULATOR_CODE } from '../../data/arduinoCode';
import GuideSkeleton from '../../components/GuideSkeleton.vue';
import { useI18n } from '../../i18n';

const { t, currentLang } = useI18n();

const isInitialLoading = ref(true);
const activeTab = ref('devices');
const openFaq = ref(1);

onMounted(() => {
  setTimeout(() => {
    isInitialLoading.value = false;
  }, 350);
});

const selectedProgramId = ref('dual-mode');
const isCopied = ref(false);

const arduinoPrograms = computed(() => [
  {
    id: 'dual-mode',
    title: t('guide.progDualTitle'),
    filename: 'esp32_smart_dryer_dual_mode.ino',
    badge: t('guide.progDualBadge'),
    badgeClass: 'badge-prod',
    desc: t('guide.progDualDesc'),
    target: t('guide.progDualTarget'),
    libraries: ['PubSubClient (Nick O\'Leary)', 'ArduinoJson (Benoit Blanchon v6/v7)', 'WiFi.h', 'BLEDevice.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_DUAL_MODE_CODE,
  },
  {
    id: 'simulator',
    title: t('guide.progSimTitle'),
    filename: 'esp32_smart_dryer_simulator.ino',
    badge: t('guide.progSimBadge'),
    badgeClass: 'badge-sim',
    desc: t('guide.progSimDesc'),
    target: t('guide.progSimTarget'),
    libraries: ['PubSubClient (Nick O\'Leary)', 'ArduinoJson (v6/v7)', 'BLEDevice.h', 'WiFi.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_SIMULATOR_CODE,
  }
]);

const selectedProgram = computed(() => {
  return arduinoPrograms.value.find(p => p.id === selectedProgramId.value) || arduinoPrograms.value[0];
});

async function copyProgramCode(code) {
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(code);
    } else {
      throw new Error('Clipboard API not available');
    }
    isCopied.value = true;
    setTimeout(() => {
      isCopied.value = false;
    }, 2500);
  } catch (err) {
    // Fallback for non-secure contexts
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    isCopied.value = true;
    setTimeout(() => {
      isCopied.value = false;
    }, 2500);
  }
}

function downloadInoFile(program) {
  const blob = new Blob([program.code], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = program.filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

// Reactive connection state from connectionManager
const isSystemActive = computed(() => {
  return connectionManager.state.isConnected || connectionManager.state.isHardwareActive;
});

// Live sensor telemetry snapshot from connectionManager
const currentTelemetry = computed(() => {
  return connectionManager.state.lastDataTimestamp ? connectionManager.getStatusSnapshot() : null;
});

// Structured Hardware Catalog for Smart Room Dryer Hanjeli (Ringkas & Sesuai Kebutuhan Operator)
const hardwareCatalog = computed(() => [
  {
    id: 'mcu-esp32',
    name: t('guide.hwMcuName'),
    category: t('guide.hwMcuCategory'),
    purpose: t('guide.hwMcuPurpose'),
    location: t('guide.hwMcuLocation'),
    unitBadge: t('guide.hwMcuBadge'),
    iconType: 'mcu',
  },
  {
    id: 'sensor-dht22-int',
    name: t('guide.hwTempIntName'),
    category: t('guide.hwTempIntCategory'),
    purpose: t('guide.hwTempIntPurpose'),
    location: t('guide.hwTempIntLocation'),
    unitBadge: t('guide.hwTempIntBadge'),
    iconType: 'temp',
  },
  {
    id: 'sensor-dht22-ext',
    name: t('guide.hwTempExtName'),
    category: t('guide.hwTempExtCategory'),
    purpose: t('guide.hwTempExtPurpose'),
    location: t('guide.hwTempExtLocation'),
    unitBadge: t('guide.hwTempExtBadge'),
    iconType: 'temp',
  },
  {
    id: 'sensor-solar',
    name: t('guide.hwSolarName'),
    category: t('guide.hwSolarCategory'),
    purpose: t('guide.hwSolarPurpose'),
    location: t('guide.hwSolarLocation'),
    unitBadge: t('guide.hwSolarBadge'),
    iconType: 'sun',
  },
  {
    id: 'sensor-moisture-weight',
    name: t('guide.hwMoistureName'),
    category: t('guide.hwMoistureCategory'),
    purpose: t('guide.hwMoisturePurpose'),
    location: t('guide.hwMoistureLocation'),
    unitBadge: t('guide.hwMoistureBadge'),
    iconType: 'weight',
  },
  {
    id: 'actuator-relays',
    name: t('guide.hwRelayName'),
    category: t('guide.hwRelayCategory'),
    purpose: t('guide.hwRelayPurpose'),
    location: t('guide.hwRelayLocation'),
    unitBadge: t('guide.hwRelayBadge'),
    iconType: 'relay',
  },
]);

function getIconBoxClass(iconType) {
  if (iconType === 'mcu') return 'icon-green-subtle';
  if (iconType === 'temp') return 'icon-blue-subtle';
  if (iconType === 'sun') return 'icon-amber-subtle';
  if (iconType === 'weight') return 'icon-purple-subtle';
  if (iconType === 'relay') return 'icon-orange-subtle';
  return 'icon-blue-subtle';
}
</script>

<style scoped>
.devices-guide-page {
  width: 100%;
  padding: 32px 40px 60px;
  box-sizing: border-box;
  background: transparent;
}

.guide-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Page Header */
.page-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.page-title-wrap {
  display: flex;
  align-items: center;
  gap: 14px;
}

.title-icon-badge {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: rgba(13, 99, 27, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .title-icon-badge {
  background: rgba(46, 125, 50, 0.25);
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--color-text-title);
  margin: 0;
  letter-spacing: -0.5px;
  line-height: 1.2;
}

:global(.dark-theme) .page-title {
  color: #F1F5F9;
}

.page-subtitle {
  font-size: 16px;
  color: var(--color-text-muted);
  margin: 4px 0 0 0;
  line-height: 1.5;
}

:global(.dark-theme) .page-subtitle {
  color: #94A3B8;
}

/* Header Status Pill */
.header-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

.status-system-online {
  background: #ECFDF5;
  color: #065F46;
  border: 1px solid #A7F3D0;
}

:global(.dark-theme) .status-system-online {
  background: rgba(16, 185, 129, 0.15);
  color: #6EE7B7;
  border-color: rgba(16, 185, 129, 0.35);
}

.status-system-standby {
  background: #FFFBEB;
  color: #92400E;
  border: 1px solid #FDE68A;
}

:global(.dark-theme) .status-system-standby {
  background: rgba(245, 158, 11, 0.15);
  color: #FDE68A;
  border-color: rgba(245, 158, 11, 0.35);
}

/* System Overview Banner */
.system-overview-banner {
  border-radius: 14px;
  padding: 16px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  transition: all 0.25s ease;
}

.banner-ready {
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
}

:global(.dark-theme) .banner-ready {
  background: rgba(16, 185, 129, 0.1);
  border-color: rgba(16, 185, 129, 0.25);
}

.banner-standby {
  background: #FFFBEB;
  border: 1px solid #FDE68A;
}

:global(.dark-theme) .banner-standby {
  background: rgba(245, 158, 11, 0.1);
  border-color: rgba(245, 158, 11, 0.25);
}

.banner-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.banner-status-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}

:global(.dark-theme) .banner-status-icon {
  background: #0B242F;
}

.banner-title {
  font-size: 14px;
  font-weight: 800;
  color: #071E27;
}

:global(.dark-theme) .banner-title {
  color: #FFFFFF;
}

.banner-desc {
  font-size: 12px;
  color: rgba(64, 73, 61, 0.75);
  margin: 2px 0 0 0;
}

:global(.dark-theme) .banner-desc {
  color: #94A3B8;
}

.banner-indicators {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
}

.indicator-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.ind-lbl {
  font-size: 10.5px;
  font-weight: 700;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.ind-val {
  font-size: 12.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.text-green-dark {
  color: #0D631B;
}

:global(.dark-theme) .text-green-dark {
  color: #4ADE80;
}

.indicator-sep {
  width: 1px;
  height: 28px;
  background: rgba(191, 202, 186, 0.5);
}

:global(.dark-theme) .indicator-sep {
  background: rgba(30, 78, 97, 0.6);
}

/* Guide Nav Tabs */
.guide-nav-tabs {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid rgba(191, 202, 186, 0.4);
  padding-bottom: 2px;
}

:global(.dark-theme) .guide-nav-tabs {
  border-bottom-color: rgba(30, 78, 97, 0.5);
}

.guide-tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 700;
  color: #64748B;
  cursor: pointer;
  border-radius: 8px 8px 0 0;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.guide-tab-btn:hover {
  color: #0D631B;
}

:global(.dark-theme) .guide-tab-btn:hover {
  color: #4ADE80;
}

.guide-tab-btn.active {
  color: #0D631B;
  border-bottom-color: #0D631B;
  background: rgba(13, 99, 27, 0.06);
}

:global(.dark-theme) .guide-tab-btn.active {
  color: #4ADE80;
  border-bottom-color: #4ADE80;
  background: rgba(74, 222, 128, 0.1);
}

/* Tab Pane */
.tab-pane {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Devices Simple Grid (Simpel & Praktis untuk Operator) */
.devices-simple-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 14px;
}

.sensor-simple-card {
  background: var(--color-white, #FFFFFF);
  border: 1px solid rgba(203, 213, 225, 0.5);
  border-radius: 14px;
  padding: 16px 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: all 0.2s ease;
}

:global(.dark-theme) .sensor-simple-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.sensor-simple-card:hover {
  border-color: rgba(13, 99, 27, 0.4);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

:global(.dark-theme) .sensor-simple-card:hover {
  border-color: rgba(74, 222, 128, 0.4);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
}

.sensor-card-top {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sensor-icon-box {
  width: 38px;
  height: 38px;
  min-width: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-green-subtle { background: rgba(13, 99, 27, 0.1); color: #0D631B; }
.icon-blue-subtle { background: rgba(0, 93, 183, 0.1); color: #005DB7; }
.icon-amber-subtle { background: rgba(180, 80, 0, 0.1); color: #B45000; }
.icon-purple-subtle { background: rgba(126, 34, 206, 0.1); color: #7E22CE; }
.icon-orange-subtle { background: rgba(234, 88, 12, 0.1); color: #EA580C; }

:global(.dark-theme) .icon-green-subtle { background: rgba(46, 125, 50, 0.25); color: #4ADE80; }
:global(.dark-theme) .icon-blue-subtle { background: rgba(0, 93, 183, 0.25); color: #60A5FA; }
:global(.dark-theme) .icon-amber-subtle { background: rgba(180, 80, 0, 0.25); color: #FBBF24; }
:global(.dark-theme) .icon-purple-subtle { background: rgba(126, 34, 206, 0.25); color: #C084FC; }
:global(.dark-theme) .icon-orange-subtle { background: rgba(234, 88, 12, 0.25); color: #FB923C; }

.sensor-title-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.sensor-simple-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title, #071E27);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

:global(.dark-theme) .sensor-simple-title {
  color: #FFFFFF;
}

.sensor-category-tag {
  font-size: 10px;
  font-weight: 600;
  color: #64748B;
}

:global(.dark-theme) .sensor-category-tag {
  color: #94A3B8;
}

.sensor-status-tag {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 8px;
  border-radius: 20px;
  font-size: 10.5px;
  font-weight: 700;
  flex-shrink: 0;
}

.tag-online {
  background: #ECFDF5;
  color: #065F46;
  border: 1px solid #A7F3D0;
}

:global(.dark-theme) .tag-online {
  background: rgba(16, 185, 129, 0.15);
  color: #6EE7B7;
  border-color: rgba(16, 185, 129, 0.3);
}

.tag-standby {
  background: #FFFBEB;
  color: #92400E;
  border: 1px solid #FDE68A;
}

:global(.dark-theme) .tag-standby {
  background: rgba(245, 158, 11, 0.15);
  color: #FDE68A;
  border-color: rgba(245, 158, 11, 0.3);
}

.status-pulse-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.status-pulse-dot.online {
  background: #10B981;
}

.status-pulse-dot.standby {
  background: #F59E0B;
}

.sensor-simple-desc {
  font-size: 12px;
  color: var(--color-text-body, #40493D);
  margin: 0;
  line-height: 1.4;
}

:global(.dark-theme) .sensor-simple-desc {
  color: #CBD5E1;
}

.sensor-card-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(203, 213, 225, 0.35);
}

:global(.dark-theme) .sensor-card-bottom {
  border-top-color: rgba(30, 78, 97, 0.4);
}

.sensor-loc-pill {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: #64748B;
  font-weight: 600;
}

:global(.dark-theme) .sensor-loc-pill {
  color: #94A3B8;
}

.sensor-unit-pill {
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
  background: rgba(0, 93, 183, 0.08);
  color: #005DB7;
}

:global(.dark-theme) .sensor-unit-pill {
  background: rgba(59, 130, 246, 0.15);
  color: #93C5FD;
}

/* Collaboration Banner */
.collab-strip-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 18px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

:global(.dark-theme) .collab-strip-card {
  background: #0B242F;
  border-color: rgba(30, 78, 97, 0.6);
}

.collab-item {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  min-width: 280px;
}

.collab-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.collab-role {
  font-size: 10px;
  font-weight: 700;
  color: var(--color-text-dim, #64748B);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.collab-name {
  font-size: 13.5px;
  font-weight: 800;
  color: var(--color-text-title);
}

:global(.dark-theme) .collab-name {
  color: #FFFFFF;
}

.collab-desc {
  font-size: 11.5px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.35;
}

:global(.dark-theme) .collab-desc {
  color: #94A3B8;
}

.collab-divider {
  width: 1px;
  height: 48px;
  background: var(--color-border-subtle, rgba(191, 202, 186, 0.4));
}

:global(.dark-theme) .collab-divider {
  background: rgba(30, 78, 97, 0.5);
}

/* SOP Styles */
.sop-container, .faq-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sop-hero-card, .faq-hero-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 16px;
}

:global(.dark-theme) .sop-hero-card,
:global(.dark-theme) .faq-hero-card {
  background: #0B242F !important;
  border-color: rgba(30, 78, 97, 0.6) !important;
}

.sop-hero-icon, .faq-hero-icon {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  background: rgba(13, 99, 27, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .sop-hero-icon {
  background: rgba(46, 125, 50, 0.25);
}

:global(.dark-theme) .sop-hero-icon svg {
  stroke: #4ADE80;
}

.faq-hero-icon {
  background: rgba(0, 93, 183, 0.1);
}

:global(.dark-theme) .faq-hero-icon {
  background: rgba(0, 93, 183, 0.25);
}

:global(.dark-theme) .faq-hero-icon svg {
  stroke: #60A5FA;
}

.sop-hero-title, .faq-hero-title {
  font-size: 16px;
  font-weight: 800;
  color: var(--color-text-title);
  margin: 0;
}

:global(.dark-theme) .sop-hero-title,
:global(.dark-theme) .faq-hero-title {
  color: #FFFFFF !important;
}

.sop-hero-subtitle, .faq-hero-subtitle {
  font-size: 12.5px;
  color: var(--color-text-muted);
  margin: 3px 0 0 0;
}

:global(.dark-theme) .sop-hero-subtitle,
:global(.dark-theme) .faq-hero-subtitle {
  color: #94A3B8 !important;
}

.sop-steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 16px;
}

.sop-step-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

:global(.dark-theme) .sop-step-card {
  background: #0B242F !important;
  border-color: rgba(30, 78, 97, 0.6) !important;
}

.sop-step-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sop-step-num {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .sop-step-num {
  background: #2E7D32;
  color: #FFFFFF;
}

.sop-step-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0;
}

:global(.dark-theme) .sop-step-title {
  color: #FFFFFF !important;
}

.sop-step-body {
  font-size: 12px;
  color: var(--color-text-body);
  line-height: 1.45;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

:global(.dark-theme) .sop-step-body {
  color: #CBD5E1 !important;
}

.sop-tip-box {
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  border-radius: 8px;
  padding: 8px 10px;
  font-size: 11px;
  color: #065F46;
  display: flex;
  align-items: flex-start;
  gap: 6px;
}

:global(.dark-theme) .sop-tip-box {
  background: rgba(16, 185, 129, 0.12) !important;
  border-color: rgba(16, 185, 129, 0.25) !important;
  color: #86EFAC !important;
}

:global(.dark-theme) .sop-tip-box svg {
  stroke: #86EFAC !important;
}

.sop-safety-card {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 14px;
  padding: 16px 20px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

:global(.dark-theme) .sop-safety-card {
  background: rgba(239, 68, 68, 0.12) !important;
  border-color: rgba(239, 68, 68, 0.25) !important;
}

.safety-icon-box {
  flex-shrink: 0;
  margin-top: 2px;
}

.safety-title {
  font-size: 13px;
  font-weight: 800;
  color: #991B1B;
  margin: 0 0 6px 0;
}

:global(.dark-theme) .safety-title {
  color: #FCA5A5 !important;
}

.safety-list {
  margin: 0;
  padding-left: 18px;
  font-size: 11.5px;
  color: #7F1D1D;
  line-height: 1.5;
}

:global(.dark-theme) .safety-list {
  color: #FECACA !important;
}

/* FAQ */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.faq-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  overflow: hidden;
}

:global(.dark-theme) .faq-card {
  background: #0B242F !important;
  border-color: rgba(30, 78, 97, 0.6) !important;
}

.faq-header {
  width: 100%;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: none;
  background: transparent;
  cursor: pointer;
  text-align: left;
}

.faq-question {
  font-size: 13px;
  font-weight: 700;
  color: var(--color-text-title);
}

:global(.dark-theme) .faq-question {
  color: #FFFFFF !important;
}

.faq-chevron {
  color: var(--color-text-muted);
  transition: transform 0.2s ease;
}

.faq-card.open .faq-chevron {
  transform: rotate(180deg);
}

.faq-body {
  padding: 0 18px 16px 18px;
  font-size: 12px;
  color: var(--color-text-body);
  line-height: 1.5;
}

:global(.dark-theme) .faq-body {
  color: #CBD5E1 !important;
}

.faq-steps {
  margin: 0;
  padding-left: 18px;
}

.faq-contact-card {
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
}

:global(.dark-theme) .faq-contact-card {
  background: rgba(16, 185, 129, 0.12) !important;
  border-color: rgba(16, 185, 129, 0.25) !important;
}

.contact-title {
  font-size: 13px;
  font-weight: 700;
  color: #065F46;
  margin: 0;
}

:global(.dark-theme) .contact-title {
  color: #86EFAC !important;
}

.contact-sub {
  font-size: 11.5px;
  color: #047857;
  margin: 2px 0 0 0;
}

:global(.dark-theme) .contact-sub {
  color: #6EE7B7 !important;
}

:global(.dark-theme) .faq-contact-card .contact-icon svg {
  stroke: #86EFAC;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.4; transform: scale(0.85); }
}

/* ==============================================================================
   TAB 4: ARDUINO & ESP32 FIRMWARE STYLES
   ============================================================================== */
.arduino-tab-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.arduino-intro-banner {
  background: linear-gradient(135deg, rgba(236, 253, 245, 0.95) 0%, rgba(240, 253, 250, 0.9) 100%);
  border: 1px solid #A7F3D0;
  border-radius: 16px;
  padding: 20px 24px;
}

:global(.dark-theme) .arduino-intro-banner {
  background: linear-gradient(135deg, rgba(13, 99, 27, 0.15) 0%, rgba(6, 78, 59, 0.25) 100%) !important;
  border-color: rgba(74, 222, 128, 0.25) !important;
}

.arduino-intro-content {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.arduino-logo-badge {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--color-white);
  border: 1px solid #BBF7D0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 6px rgba(13, 99, 27, 0.08);
}

:global(.dark-theme) .arduino-logo-badge {
  background: #0B242F !important;
  border-color: rgba(74, 222, 128, 0.3) !important;
}

:global(.dark-theme) .arduino-logo-badge svg {
  stroke: #4ADE80;
}

.arduino-intro-title {
  font-size: 16px;
  font-weight: 800;
  color: #065F46;
  margin: 0 0 4px 0;
}

:global(.dark-theme) .arduino-intro-title {
  color: #86EFAC !important;
}

.arduino-intro-sub {
  font-size: 13px;
  color: #047857;
  margin: 0;
  line-height: 1.5;
}

:global(.dark-theme) .arduino-intro-sub {
  color: #6EE7B7 !important;
}

.arduino-intro-sub code {
  background: rgba(13, 99, 27, 0.1);
  padding: 2px 6px;
  border-radius: 4px;
  font-family: monospace;
  font-weight: 600;
}

:global(.dark-theme) .arduino-intro-sub code {
  background: rgba(74, 222, 128, 0.15);
  color: #86EFAC;
}

/* Program Selector Grid */
.program-selector-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 16px;
}

.program-card {
  background: var(--color-white);
  border: 2px solid var(--color-border);
  border-radius: 16px;
  padding: 18px 20px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  gap: 10px;
  text-align: left;
}

:global(.dark-theme) .program-card {
  background: #0B242F !important;
  border-color: rgba(30, 78, 97, 0.6) !important;
}

.program-card:hover {
  transform: translateY(-2px);
  border-color: #0D631B;
  box-shadow: 0 6px 16px rgba(13, 99, 27, 0.08);
}

:global(.dark-theme) .program-card:hover {
  border-color: #4ADE80 !important;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.program-card.active {
  border-color: #0D631B;
  background: #F0FDF4;
  box-shadow: 0 4px 14px rgba(13, 99, 27, 0.12);
}

:global(.dark-theme) .program-card.active {
  border-color: #4ADE80 !important;
  background: rgba(13, 99, 27, 0.25) !important;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
}

.program-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
}

.program-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge-prod {
  background: #DCFCE7;
  color: #15803D;
  border: 1px solid #86EFAC;
}

:global(.dark-theme) .badge-prod {
  background: rgba(22, 163, 74, 0.25);
  color: #4ADE80;
  border-color: rgba(74, 222, 128, 0.4);
}

.badge-sim {
  background: #EFF6FF;
  color: #1D4ED8;
  border: 1px solid #93C5FD;
}

:global(.dark-theme) .badge-sim {
  background: rgba(37, 99, 235, 0.25);
  color: #93C5FD;
  border-color: rgba(147, 197, 253, 0.4);
}

.program-filename {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-family: monospace;
  font-weight: 600;
  color: var(--color-text-muted);
}

:global(.dark-theme) .program-filename {
  color: #94A3B8;
}

.program-card-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0;
  line-height: 1.35;
}

:global(.dark-theme) .program-card-title {
  color: #FFFFFF !important;
}

.program-card-desc {
  font-size: 12.5px;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.5;
  flex: 1;
}

:global(.dark-theme) .program-card-desc {
  color: #CBD5E1 !important;
}

.program-card-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-top: 6px;
  border-top: 1px dashed var(--color-border);
}

:global(.dark-theme) .program-card-footer {
  border-top-color: rgba(51, 65, 85, 0.6);
}

.program-select-indicator {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 700;
  color: #0D631B;
}

:global(.dark-theme) .program-select-indicator {
  color: #4ADE80;
}

/* Active Program Wrapper */
.active-program-wrapper {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

/* Program Meta Card */
.program-meta-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 18px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

:global(.dark-theme) .program-meta-card {
  background: #0B242F !important;
  border-color: rgba(30, 78, 97, 0.6) !important;
}

.program-meta-grid {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 20px;
  flex: 1;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
}

.meta-item-full {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  width: 100%;
}

.meta-label {
  color: var(--color-text-muted);
  font-size: 12px;
  font-weight: 500;
}

:global(.dark-theme) .meta-label {
  color: #94A3B8;
}

.meta-val {
  color: var(--color-text-title);
  font-size: 12.5px;
}

:global(.dark-theme) .meta-val {
  color: #F1F5F9 !important;
}

.meta-lib-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.lib-tag {
  background: var(--color-bg-light);
  color: var(--color-text-body);
  font-size: 11px;
  font-family: monospace;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid var(--color-border);
}

:global(.dark-theme) .lib-tag {
  background: #1E293B;
  color: #CBD5E1;
  border-color: #334155;
}

.program-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.action-btn-copy {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #0D631B;
  color: #FFFFFF;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(13, 99, 27, 0.2);
}

.action-btn-copy:hover {
  background: #0b5016;
  transform: translateY(-1px);
}

.action-btn-copy.copied {
  background: #16A34A;
}

.action-btn-download {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--color-bg-light);
  color: var(--color-text-title);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

:global(.dark-theme) .action-btn-download {
  background: #1E293B;
  color: #F8FAFC;
  border-color: #334155;
}

.action-btn-download:hover {
  background: #E2E8F0;
  transform: translateY(-1px);
}

:global(.dark-theme) .action-btn-download:hover {
  background: #334155;
}

/* Code Editor Viewer Box */
.code-editor-box {
  background: #0F172A;
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid #1E293B;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.code-editor-header {
  background: #1E293B;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #334155;
}

.code-header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.code-dots {
  display: flex;
  align-items: center;
  gap: 6px;
}

.code-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.dot-red { background: #EF4444; }
.dot-yellow { background: #F59E0B; }
.dot-green { background: #10B981; }

.code-file-title {
  font-size: 12px;
  font-family: monospace;
  font-weight: 600;
  color: #94A3B8;
}

.code-header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.code-lines-count {
  font-size: 11px;
  color: #64748B;
  font-family: monospace;
}

.code-header-copy-btn {
  background: #334155;
  color: #F1F5F9;
  border: 1px solid #475569;
  border-radius: 6px;
  padding: 4px 10px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.code-header-copy-btn:hover {
  background: #475569;
}

.code-editor-content {
  max-height: 480px;
  overflow-y: auto;
  padding: 16px 20px;
  background: #090D16;
}

.code-pre {
  margin: 0;
  font-family: 'Fira Code', 'Consolas', 'Courier New', monospace;
  font-size: 12.5px;
  line-height: 1.6;
  color: #E2E8F0;
  white-space: pre;
  tab-size: 2;
}

/* Flashing Guide Card */
.flashing-guide-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 20px 24px;
}

:global(.dark-theme) .flashing-guide-card {
  background: #0B242F !important;
  border-color: rgba(30, 78, 97, 0.6) !important;
}

.guide-steps-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-title);
  margin: 0 0 16px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

:global(.dark-theme) .guide-steps-title {
  color: #FFFFFF !important;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 14px;
}

.step-card {
  background: var(--color-bg-light);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 14px;
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

:global(.dark-theme) .step-card {
  background: #112F3D !important;
  border-color: rgba(30, 78, 97, 0.8) !important;
}

.step-num {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #0D631B;
  color: #FFFFFF;
  font-size: 12px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .step-num {
  background: #4ADE80;
  color: #064E3B;
}

.step-desc {
  font-size: 12px;
  color: var(--color-text-body);
  line-height: 1.5;
}

:global(.dark-theme) .step-desc {
  color: #CBD5E1 !important;
}

.step-desc code {
  background: rgba(0, 0, 0, 0.06);
  padding: 1px 5px;
  border-radius: 4px;
  font-family: monospace;
  font-size: 11px;
}

:global(.dark-theme) .step-desc code {
  background: rgba(255, 255, 255, 0.1);
  color: #93C5FD;
}

.step-desc code {
  background: rgba(0, 0, 0, 0.06);
  padding: 1px 5px;
  border-radius: 4px;
  font-family: monospace;
  font-size: 11px;
}

:global(.dark-theme) .step-desc code {
  background: rgba(255, 255, 255, 0.1);
  color: #93C5FD;
}

/* Page Transition for Skeleton */
.fade-guide-enter-active,
.fade-guide-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-guide-enter-from {
  opacity: 0;
  transform: translateY(6px);
}

.fade-guide-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
