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
            <div class="header-action-group">
              <button 
                type="button" 
                class="btn-open-manual-pdf" 
                @click="openPdfNewTab"
                title="Buka Dokumen Manual Book PDF di Tab Baru"
              >
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span>{{ $t('guide.openManualPdf') || 'Buka Manual Book PDF' }}</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="icon-external">
                  <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                  <polyline points="15 3 21 3 21 9"></polyline>
                  <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
              </button>
            </div>
          </div>

          <!-- Status Kesiapan SOP Pengeringan Hanjeli -->
      <div class="system-overview-banner banner-ready">
        <div class="banner-left">
          <div class="banner-status-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.4">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
          <div>
            <div class="banner-title">
              Prosedur Operasional Standar (SOP) Pengeringan
            </div>
            <p class="banner-desc">
              Pedomani 5 tahapan baku pengeringan biji hanjeli untuk menjamin kualitas fisik prima dan kadar air standar SNI (≤ 12.0%).
            </p>
          </div>
        </div>

        <div class="banner-indicators">
          <div class="indicator-item">
            <span class="ind-lbl">KOMODITAS UTAMA</span>
            <span class="ind-val text-green-dark">
              Biji Hanjeli Sukabumi
            </span>
          </div>
          <div class="indicator-sep"></div>
          <div class="indicator-item">
            <span class="ind-lbl">STANDAR MUTU</span>
            <span class="ind-val text-green-dark">
              SNI (Kadar Air ≤ 12.0%)
            </span>
          </div>
          <div class="indicator-sep"></div>
          <div class="indicator-item">
            <span class="ind-lbl">RENTANG SUHU AMAN</span>
            <span class="ind-val text-green-dark">
              48°C - 50°C
            </span>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs (Khusus Operator: SOP, Kendala, & Manual Book) -->
      <div class="guide-nav-tabs">
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
          class="guide-tab-btn guide-tab-btn-manual" 
          :class="{ active: activeTab === 'manual' }"
          @click="activeTab = 'manual'"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
          </svg>
          <span>{{ $t('guide.tabManual') || 'Buku Panduan (Manual Book PDF)' }}</span>
          <span class="manual-tab-badge">PDF</span>
        </button>
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
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2">
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

      <!-- TAB 3: BUKU PANDUAN MANUAL BOOK PDF -->
      <div v-if="activeTab === 'manual'" class="tab-pane">
        <div class="manual-book-container">
          <!-- Hero Card Manual Book -->
          <div class="manual-hero-card">
            <div class="manual-hero-left">
              <div class="manual-icon-badge">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="2.2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
              </div>
              <div class="manual-hero-info">
                <div class="manual-hero-meta">
                  <span class="manual-tag">DOKUMEN RESMI ABDIMAS</span>
                  <span class="manual-tag-sub">Format PDF • 18 Halaman • 1.05 MB</span>
                </div>
                <h3 class="manual-hero-title">MANUAL BOOK ABDIMAS DESA HANJELI - WEBSITE SMART DRYER</h3>
                <p class="manual-hero-desc">
                  Buku petunjuk operasional lengkap: tata cara login, konfigurasi konektivitas ESP32 (Wi-Fi, BLE, MQTT), navigasi dasbor, monitoring telemetri, pembuatan batch pengeringan, hingga pencetakan label QR ketertelusuran produk.
                </p>
              </div>
            </div>
            <div class="manual-hero-actions">
              <button 
                type="button" 
                class="btn-manual-action btn-manual-open" 
                @click="openPdfNewTab"
                title="Buka dokumen di tab browser terpisah"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                  <polyline points="15 3 21 3 21 9"></polyline>
                  <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                <span>Buka di Tab Baru</span>
              </button>
              <a 
                href="/manual-book/download" 
                download="MANUAL-BOOK-WEBSITE-SMART-DRYER-HANJELI.pdf"
                class="btn-manual-action btn-manual-download"
                title="Unduh file PDF ke komputer / smartphone"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="7 10 12 15 17 10"></polyline>
                  <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Unduh PDF</span>
              </a>
            </div>
          </div>

          <!-- Embedded PDF Viewer Card -->
          <div class="manual-viewer-card">
            <div class="manual-viewer-toolbar">
              <div class="viewer-toolbar-left">
                <span class="viewer-doc-indicator"></span>
                <span class="viewer-doc-name">MANUAL BOOK ABDIMAS DESA HANJELI - WEBSITE SMART DRYER.pdf</span>
              </div>
              <div class="viewer-toolbar-right">
                <span class="viewer-hint">Gunakan tombol zoom & navigasi halaman di toolbar PDF bawaan</span>
                <button 
                  type="button" 
                  class="btn-viewer-fullscreen" 
                  @click="openPdfNewTab" 
                  title="Buka tampilan penuh dokumen di tab baru"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <polyline points="9 21 3 21 3 15"></polyline>
                    <line x1="21" y1="3" x2="14" y2="10"></line>
                    <line x1="3" y1="21" x2="10" y2="14"></line>
                  </svg>
                  <span>Layar Penuh</span>
                </button>
              </div>
            </div>
            <div class="viewer-frame-container">
              <iframe 
                src="/docs/manual-book.pdf#toolbar=1&navpanes=1" 
                class="manual-pdf-iframe"
                title="Manual Book Smart Dryer Hanjeli"
                loading="lazy"
              ></iframe>
            </div>
          </div>
        </div>
      </div>

      <!-- Partnership & Developer Section -->
      <div class="collab-strip-card" style="margin-top: 24px;">
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
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import HanjeliLogo from '../../components/HanjeliLogo.vue';
import StasLogo from '../../components/StasLogo.vue';
import GuideSkeleton from '../../components/GuideSkeleton.vue';
import { useI18n } from '../../i18n';

const route = useRoute();
const { t } = useI18n();

const isInitialLoading = ref(true);
const initialTab = route?.query?.tab === 'manual' ? 'manual' : (route?.query?.tab === 'faq' ? 'faq' : 'sop');
const activeTab = ref(initialTab);
const openFaq = ref(1);

watch(() => route?.query?.tab, (newTab) => {
  if (newTab === 'manual' || newTab === 'faq' || newTab === 'sop') {
    activeTab.value = newTab;
  }
});

const openPdfNewTab = () => {
  window.open('/docs/manual-book.pdf', '_blank');
};

onMounted(() => {
  if (route?.query?.tab === 'manual') {
    activeTab.value = 'manual';
  }
  setTimeout(() => {
    isInitialLoading.value = false;
  }, 350);
});
</script>

<style scoped>
.devices-guide-page {
  width: 100%;
  padding: 32px 40px 60px;
  box-sizing: border-box;
  background: transparent;
}

.guide-container {
  width: 100%;
  margin: 0;
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

.header-action-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-open-manual-pdf {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: #1B5E20;
  color: #FFFFFF;
  border: 1px solid #1B5E20;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 2px 8px rgba(27, 94, 32, 0.2);
}

.btn-open-manual-pdf:hover {
  background: #2E7D32;
  border-color: #2E7D32;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(27, 94, 32, 0.28);
}

.btn-open-manual-pdf .icon-external {
  opacity: 0.85;
}

:global(.dark-theme) .btn-open-manual-pdf {
  background: #10B981;
  border-color: #10B981;
  color: #052317;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
}

:global(.dark-theme) .btn-open-manual-pdf:hover {
  background: #34D399;
  border-color: #34D399;
  color: #052317;
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

/* Tab 3 Badge */
.manual-tab-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 7px;
  border-radius: 6px;
  background: rgba(27, 94, 32, 0.12);
  color: #1B5E20;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.5px;
  margin-left: 4px;
}

:global(.dark-theme) .manual-tab-badge {
  background: rgba(16, 185, 129, 0.2);
  color: #6EE7B7;
}

/* Manual Book Tab Styles */
.manual-book-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.manual-hero-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 24px 28px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

:global(.dark-theme) .manual-hero-card {
  background: #0B242F;
  border-color: #1E4E61;
}

.manual-hero-left {
  display: flex;
  align-items: flex-start;
  gap: 18px;
  flex: 1;
}

.manual-icon-badge {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

:global(.dark-theme) .manual-icon-badge {
  background: rgba(16, 185, 129, 0.12);
  border-color: rgba(16, 185, 129, 0.3);
}

:global(.dark-theme) .manual-icon-badge svg {
  stroke: #6EE7B7;
}

.manual-hero-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.manual-hero-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.manual-tag {
  display: inline-flex;
  align-items: center;
  padding: 3px 8px;
  border-radius: 6px;
  background: #DCFCE7;
  color: #166534;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.4px;
}

:global(.dark-theme) .manual-tag {
  background: rgba(16, 185, 129, 0.2);
  color: #6EE7B7;
}

.manual-tag-sub {
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
}

:global(.dark-theme) .manual-tag-sub {
  color: #94A3B8;
}

.manual-hero-title {
  margin: 2px 0 0;
  font-size: 17px;
  font-weight: 800;
  color: #0F172A;
  line-height: 1.35;
}

:global(.dark-theme) .manual-hero-title {
  color: #FFFFFF;
}

.manual-hero-desc {
  margin: 0;
  font-size: 13px;
  color: #475569;
  line-height: 1.55;
  max-width: 820px;
}

:global(.dark-theme) .manual-hero-desc {
  color: #CBD5E1;
}

.manual-hero-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.btn-manual-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  box-sizing: border-box;
}

.btn-manual-open {
  background: #1B5E20;
  color: #FFFFFF;
  border: 1px solid #1B5E20;
  box-shadow: 0 2px 6px rgba(27, 94, 32, 0.18);
}

.btn-manual-open:hover {
  background: #2E7D32;
  border-color: #2E7D32;
  transform: translateY(-1px);
}

:global(.dark-theme) .btn-manual-open {
  background: #10B981;
  border-color: #10B981;
  color: #052317;
}

:global(.dark-theme) .btn-manual-open:hover {
  background: #34D399;
  border-color: #34D399;
  color: #052317;
}

.btn-manual-download {
  background: #F8FAFC;
  color: #0F172A;
  border: 1px solid #CBD5E1;
}

.btn-manual-download:hover {
  background: #F1F5F9;
  border-color: #94A3B8;
  color: #0F172A;
  transform: translateY(-1px);
}

:global(.dark-theme) .btn-manual-download {
  background: #0E3443;
  color: #E2E8F0;
  border-color: #1E4E61;
}

:global(.dark-theme) .btn-manual-download:hover {
  background: #164254;
  color: #FFFFFF;
  border-color: #25657E;
}

/* Embedded PDF Viewer Card */
.manual-viewer-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

:global(.dark-theme) .manual-viewer-card {
  background: #0B242F;
  border-color: #1E4E61;
}

.manual-viewer-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 20px;
  background: #F8FAFC;
  border-bottom: 1px solid #E2E8F0;
  gap: 16px;
  flex-wrap: wrap;
}

:global(.dark-theme) .manual-viewer-toolbar {
  background: #0E3443;
  border-bottom-color: #1E4E61;
}

.viewer-toolbar-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.viewer-doc-indicator {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #10B981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

.viewer-doc-name {
  font-size: 13px;
  font-weight: 700;
  color: #0F172A;
}

:global(.dark-theme) .viewer-doc-name {
  color: #FFFFFF;
}

.viewer-toolbar-right {
  display: flex;
  align-items: center;
  gap: 14px;
}

.viewer-hint {
  font-size: 12px;
  color: #64748B;
}

:global(.dark-theme) .viewer-hint {
  color: #94A3B8;
}

.btn-viewer-fullscreen {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-viewer-fullscreen:hover {
  background: #F1F5F9;
  color: #0F172A;
  border-color: #94A3B8;
}

:global(.dark-theme) .btn-viewer-fullscreen {
  background: #0B242F;
  border-color: #1E4E61;
  color: #CBD5E1;
}

:global(.dark-theme) .btn-viewer-fullscreen:hover {
  background: #164254;
  color: #FFFFFF;
}

.viewer-frame-container {
  width: 100%;
  background: #525659;
  position: relative;
  min-height: 820px;
}

.manual-pdf-iframe {
  width: 100%;
  height: 860px;
  border: none;
  display: block;
}

@media (max-width: 768px) {
  .manual-hero-card {
    flex-direction: column;
    align-items: flex-start;
    padding: 18px;
  }
  .manual-hero-actions {
    width: 100%;
    flex-direction: column;
  }
  .btn-manual-action {
    width: 100%;
    justify-content: center;
  }
  .manual-pdf-iframe {
    height: 600px;
  }
  .viewer-hint {
    display: none;
  }
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
