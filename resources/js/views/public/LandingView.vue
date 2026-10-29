<template>
    <div class="landing-page" :class="{ 'dark-landing': isDark }">
        <!-- 1. TOP STICKY NAVBAR -->
        <header
            class="landing-navbar"
            :class="{
                'navbar-scrolled': isScrolled,
                'mobile-menu-active': isMobileMenuOpen,
            }"
            ref="navbarRef"
        >
            <div class="landing-nav-container">
                <!-- Logo Brand -->
                <div class="nav-brand" @click="scrollTo('tentang')">
                    <div class="brand-icon-box">
                        <img
                            v-if="logoSrc"
                            :src="logoSrc"
                            alt="Hanjeli Logo"
                            class="brand-logo-img"
                        />
                        <svg
                            v-else
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#0D631B"
                            stroke-width="2.2"
                        >
                            <path
                                d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                            ></path>
                        </svg>
                    </div>
                    <div class="brand-text">
                        <span class="brand-title">Smart Room Dryer</span>
                        <span class="brand-sub">Desa Wisata Hanjeli</span>
                    </div>
                </div>

                <!-- Center Menu Nav Links -->
                <nav class="nav-links desktop-only">
                    <a
                        href="#tentang"
                        :class="{ active: activeSection === 'tentang' }"
                        @click.prevent="scrollTo('tentang')"
                    >
                        {{ currentLang === "id" ? "Tentang" : "About" }}
                    </a>
                    <a
                        href="#hardware"
                        :class="{ active: activeSection === 'hardware' }"
                        @click.prevent="scrollTo('hardware')"
                    >
                        {{ currentLang === "id" ? "Hardware" : "Hardware" }}
                    </a>
                    <a
                        href="#fitur"
                        :class="{ active: activeSection === 'fitur' }"
                        @click.prevent="scrollTo('fitur')"
                    >
                        {{ currentLang === "id" ? "Fitur" : "Features" }}
                    </a>
                    <a
                        href="#panduan"
                        :class="{ active: activeSection === 'panduan' }"
                        @click.prevent="scrollTo('panduan')"
                    >
                        {{ currentLang === "id" ? "Panduan" : "Guide" }}
                    </a>
                    <a
                        href="/verify"
                        :class="{ active: route?.path?.startsWith('/verify') }"
                        @click.prevent="goToVerify"
                    >
                        {{
                            currentLang === "id" ? "Verifikasi" : "Verification"
                        }}
                    </a>
                </nav>

                <!-- Right Controls: Language, Theme, & Action Button -->
                <div class="nav-actions">
                    <!-- 1. Language Selector Button with Flag -->
                    <div class="lang-selector-wrapper" ref="langDropdownRef">
                        <button
                            class="header-action-btn lang-btn"
                            @click.stop="isLangOpen = !isLangOpen"
                            :title="
                                currentLang === 'id'
                                    ? 'Bahasa: Indonesia (Klik untuk ganti)'
                                    : 'Language: English (Click to change)'
                            "
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
                                <svg
                                    v-if="currentLang === 'id'"
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0D631B"
                                    stroke-width="3"
                                >
                                    <polyline
                                        points="20 6 9 17 4 12"
                                    ></polyline>
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
                                <svg
                                    v-if="currentLang === 'en'"
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0D631B"
                                    stroke-width="3"
                                >
                                    <polyline
                                        points="20 6 9 17 4 12"
                                    ></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Dark / Light Mode Toggle Button -->
                    <button
                        class="header-action-btn theme-toggle-btn"
                        @click="toggleTheme"
                        :title="
                            isDark
                                ? currentLang === 'id'
                                    ? 'Beralih ke Mode Terang'
                                    : 'Switch to Light Mode'
                                : currentLang === 'id'
                                  ? 'Beralih ke Mode Gelap'
                                  : 'Switch to Dark Mode'
                        "
                    >
                        <!-- Sun icon when dark -->
                        <svg
                            v-if="isDark"
                            class="theme-icon sun-icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#FBBF24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line
                                x1="4.22"
                                y1="4.22"
                                x2="5.64"
                                y2="5.64"
                            ></line>
                            <line
                                x1="18.36"
                                y1="18.36"
                                x2="19.78"
                                y2="19.78"
                            ></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line
                                x1="4.22"
                                y1="19.78"
                                x2="5.64"
                                y2="18.36"
                            ></line>
                            <line
                                x1="18.36"
                                y1="5.64"
                                x2="19.78"
                                y2="4.22"
                            ></line>
                        </svg>
                        <!-- Moon icon when light -->
                        <svg
                            v-else
                            class="theme-icon moon-icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#111D23"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"
                            ></path>
                        </svg>
                    </button>

                    <!-- 3. Primary CTA Button -->
                    <button class="btn-nav-login desktop-only" @click="goToApp">
                        <span>{{
                            isAuth
                                ? currentLang === "id"
                                    ? "Dashboard"
                                    : "Dashboard"
                                : currentLang === "id"
                                  ? "Masuk"
                                  : "Login"
                        }}</span>
                        <svg
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>

                    <!-- Mobile Hamburger Toggle -->
                    <button
                        class="mobile-menu-btn mobile-only"
                        @click.stop="toggleMobileMenu"
                        :aria-expanded="isMobileMenuOpen"
                        :title="isMobileMenuOpen ? 'Tutup Menu' : 'Buka Menu'"
                        aria-label="Menu"
                    >
                        <svg
                            v-if="!isMobileMenuOpen"
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                        <svg
                            v-else
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown / Drawer Menu -->
            <transition name="mobile-nav-expand">
                <div v-if="isMobileMenuOpen" class="landing-mobile-drawer" @click.stop>
                    <div class="mobile-drawer-inner">
                        <nav class="mobile-drawer-links">
                            <a
                                href="#tentang"
                                :class="{ active: activeSection === 'tentang' }"
                                @click.prevent="scrollTo('tentang')"
                            >
                                <div class="link-icon-chip">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line
                                            x1="12"
                                            y1="16"
                                            x2="12"
                                            y2="12"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="8"
                                            x2="12.01"
                                            y2="8"
                                        ></line>
                                    </svg>
                                </div>
                                <span>{{
                                    currentLang === "id"
                                        ? "Tentang Sistem"
                                        : "About System"
                                }}</span>
                            </a>
                            <a
                                href="#hardware"
                                :class="{
                                    active: activeSection === 'hardware',
                                }"
                                @click.prevent="scrollTo('hardware')"
                            >
                                <div class="link-icon-chip">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <rect
                                            x="4"
                                            y="4"
                                            width="16"
                                            height="16"
                                            rx="2"
                                            ry="2"
                                        ></rect>
                                        <rect
                                            x="9"
                                            y="9"
                                            width="6"
                                            height="6"
                                        ></rect>
                                    </svg>
                                </div>
                                <span>{{
                                    currentLang === "id"
                                        ? "Arsitektur Hardware"
                                        : "Hardware Architecture"
                                }}</span>
                            </a>
                            <a
                                href="#fitur"
                                :class="{ active: activeSection === 'fitur' }"
                                @click.prevent="scrollTo('fitur')"
                            >
                                <div class="link-icon-chip">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <polyline
                                            points="22 12 18 12 15 21 9 3 6 12 2 12"
                                        ></polyline>
                                    </svg>
                                </div>
                                <span>{{
                                    currentLang === "id"
                                        ? "Fitur Aplikasi"
                                        : "App Features"
                                }}</span>
                            </a>
                            <a
                                href="#panduan"
                                :class="{ active: activeSection === 'panduan' }"
                                @click.prevent="scrollTo('panduan')"
                            >
                                <div class="link-icon-chip">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                        ></path>
                                        <polyline
                                            points="14 2 14 8 20 8"
                                        ></polyline>
                                    </svg>
                                </div>
                                <span>{{
                                    currentLang === "id"
                                        ? "Panduan Operasional"
                                        : "Operation Guide"
                                }}</span>
                            </a>
                            <a
                                href="/verify"
                                @click.prevent="
                                    router.push('/verify');
                                    isMobileMenuOpen = false;
                                "
                            >
                                <div class="link-icon-chip">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#0D631B"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                        ></path>
                                        <polyline
                                            points="9 12 11 14 15 10"
                                        ></polyline>
                                    </svg>
                                </div>
                                <span>{{
                                    currentLang === "id"
                                        ? "Verifikasi Mutu & QR"
                                        : "Quality Verification & QR"
                                }}</span>
                            </a>
                        </nav>

                        <div class="mobile-drawer-cta">
                            <button class="btn-mobile-login" @click="goToApp">
                                <span>{{
                                    isAuth
                                        ? currentLang === "id"
                                            ? "Buka Dashboard Sistem"
                                            : "Open System Dashboard"
                                        : currentLang === "id"
                                          ? "Masuk ke Aplikasi"
                                          : "Login to App"
                                }}</span>
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                >
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline
                                        points="12 5 19 12 12 19"
                                    ></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </header>

        <!-- Backdrop Overlay for Mobile Drawer -->
        <transition name="mobile-backdrop">
            <div
                v-if="isMobileMenuOpen"
                class="mobile-drawer-backdrop"
                @click="isMobileMenuOpen = false"
            ></div>
        </transition>

        <!-- 2. HERO SECTION -->
        <section class="hero-section" id="tentang">
            <!-- Background Ambient Glow -->
            <div class="ambient-glow glow-green"></div>
            <div class="ambient-glow glow-blue"></div>

            <div class="section-container hero-grid">
                <!-- Hero Left Column -->
                <div class="hero-left">
                    <!-- Top Badge -->
                    <div class="hero-top-badge">
                        <span class="tag-dot"></span>
                        <span>{{
                            currentLang === "id"
                                ? "Inovasi Pascapanen Hanjeli Berbasis IoT"
                                : "IoT-Based Hanjeli Post-Harvest Innovation"
                        }}</span>
                    </div>

                    <!-- Headline -->
                    <h1 class="hero-title">
                        <template v-if="currentLang === 'id'">
                            Solusi Cerdas Pengeringan Biji<br />
                            Hanjeli Berbasis IoT
                        </template>
                        <template v-else>
                            Smart IoT-Based Drying Solution<br />
                            for Hanjeli Grains
                        </template>
                    </h1>

                    <!-- Subtitle -->
                    <p class="hero-desc">
                        {{
                            currentLang === "id"
                                ? "Mengoptimalkan mutu hasil panen Hanjeli dengan kendali otomatis suhu dan kelembapan secara real-time. Higienis, terukur, dan hemat energi."
                                : "Optimizing Hanjeli crop harvest quality with automated real-time temperature and humidity control. Hygienic, measurable, and energy-efficient."
                        }}
                    </p>

                    <!-- Action Buttons -->
                    <div class="hero-cta-row">
                        <button class="btn-hero-primary" @click="goToApp">
                            <span>{{
                                currentLang === "id"
                                    ? "Masuk ke Sistem Monitoring"
                                    : "Enter Monitoring System"
                            }}</span>
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.5"
                            >
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                        <button
                            class="btn-hero-secondary"
                            @click="scrollTo('hardware')"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#0D631B"
                                stroke-width="2.2"
                            >
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span>{{
                                currentLang === "id"
                                    ? "Pelajari Hardware & Fitur"
                                    : "Explore Hardware & Features"
                            }}</span>
                        </button>
                    </div>

                    <!-- Feature Highlights Mini Row -->
                    <div class="hero-mini-cards-row">
                        <div class="mini-card">
                            <div class="mini-card-icon">
                                <img
                                    src="/assets/icons/hero/Suhu.png"
                                    alt="Target 40°C"
                                    class="icon-3d-img"
                                />
                            </div>
                            <div class="mini-card-content">
                                <span class="mini-card-title">Target 40°C</span>
                                <span class="mini-card-sub">{{
                                    currentLang === "id"
                                        ? "Suhu Aman Nutrisi"
                                        : "Nutrient Safe Temp"
                                }}</span>
                            </div>
                        </div>

                        <div class="mini-card">
                            <div class="mini-card-icon">
                                <img
                                    src="/assets/icons/hero/Histeresis.png"
                                    alt="Histeresis Pintar"
                                    class="icon-3d-img"
                                />
                            </div>
                            <div class="mini-card-content">
                                <span class="mini-card-title">{{
                                    currentLang === "id"
                                        ? "Histeresis Pintar"
                                        : "Smart Hysteresis"
                                }}</span>
                                <span class="mini-card-sub">{{
                                    currentLang === "id"
                                        ? "Rentang 38°C – 40°C"
                                        : "Range 38°C – 40°C"
                                }}</span>
                            </div>
                        </div>

                        <div class="mini-card">
                            <div class="mini-card-icon">
                                <img
                                    src="/assets/icons/hero/Sensor.png"
                                    alt="Sensor DHT22"
                                    class="icon-3d-img"
                                />
                            </div>
                            <div class="mini-card-content">
                                <span class="mini-card-title">{{
                                    currentLang === "id"
                                        ? "Sensor DHT22"
                                        : "DHT22 Sensor"
                                }}</span>
                                <span class="mini-card-sub">{{
                                    currentLang === "id"
                                        ? "Presisi ±0.5°C & 2%RH"
                                        : "Precision ±0.5°C & 2%RH"
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image-only hero; operational charts remain on monitoring pages. -->
                <div class="hero-right">
                    <LandingHeroPhoto />
                </div>
            </div>
        </section>

        <!-- 3. HARDWARE ARCHITECTURE SECTION -->
        <section class="section-wrapper bg-soft-blue" id="hardware">
            <div class="section-container">
                <!-- Section Header -->
                <div class="section-header-center">
                    <span class="section-tagline">{{
                        currentLang === "id"
                            ? "ARSITEKTUR TERUJI & PRESISI"
                            : "PROVEN & PRECISE ARCHITECTURE"
                    }}</span>
                    <h2 class="section-heading">
                        {{
                            currentLang === "id"
                                ? "Mengenal Hardware IoT Smart Room Dryer"
                                : "Understanding Smart Room Dryer IoT Hardware"
                        }}
                    </h2>
                    <p class="section-subtext">
                        {{
                            currentLang === "id"
                                ? "Komposisi instrumen industri hemat daya dirancang khusus untuk ketahanan lingkungan perkebunan tropis Waluran Sukabumi."
                                : "Power-efficient industrial instruments designed specifically for the tropical plantation environment of Waluran Sukabumi."
                        }}
                    </p>
                </div>

                <!-- Hardware Cards 6-Grid -->
                <div class="hardware-grid">
                    <!-- Card 1: ESP32 -->
                    <div class="hw-card">
                        <div class="hw-card-top">
                            <div class="hw-icon-box">
                                <img
                                    src="/assets/icons/hardware/Microcontroller.png"
                                    alt="ESP32 Microcontroller"
                                    class="icon-3d-img"
                                />
                            </div>
                            <h3 class="hw-title">ESP32 Microcontroller</h3>
                            <p class="hw-desc">
                                {{
                                    currentLang === "id"
                                        ? "Pusat kendali pintar dengan konektivitas Wi-Fi terintegrasi untuk kalkulasi histeresis lokal dan transmisi data sensor kontinu ke server cloud tanpa jeda."
                                        : "Smart control hub with integrated Wi-Fi for local hysteresis calculation and seamless continuous sensor data transmission to cloud servers."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <span class="hw-spec-tag">Dual Core 240MHz</span>
                            <span class="hw-badge-green">Wi-Fi 2.4 GHz</span>
                        </div>
                    </div>

                    <!-- Card 2: DHT22 -->
                    <div class="hw-card">
                        <div class="hw-card-top">
                            <div class="hw-icon-box">
                                <img
                                    src="/assets/icons/hardware/SensorDHT.png"
                                    alt="Sensor DHT22"
                                    class="icon-3d-img"
                                />
                            </div>
                            <h3 class="hw-title">
                                {{
                                    currentLang === "id"
                                        ? "Sensor DHT22 Presisi Tinggi"
                                        : "High Precision DHT22 Sensor"
                                }}
                            </h3>
                            <p class="hw-desc">
                                {{
                                    currentLang === "id"
                                        ? "Membaca parameter suhu ruangan (°C) dan tingkat kelembapan relatif (%RH) secara akurat untuk memastikan gabah hanjeli tidak mengalami overheat."
                                        : "Accurately reads chamber temperature (°C) and relative humidity (%RH) to ensure Hanjeli grains never experience overheating."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <span class="hw-spec-tag">Range: 0-100% RH</span>
                            <span class="hw-badge-blue"
                                >±0.5°C
                                {{
                                    currentLang === "id"
                                        ? "Presisi"
                                        : "Precision"
                                }}</span
                            >
                        </div>
                    </div>

                    <!-- Card 3: PTC Heater -->
                    <div class="hw-card">
                        <div class="hw-card-top">
                            <div class="hw-icon-box">
                                <img
                                    src="/assets/icons/hardware/PTC.png"
                                    alt="PTC Ceramic Heater"
                                    class="icon-3d-img"
                                />
                            </div>
                            <h3 class="hw-title">
                                PTC Heater 12V 120W + Blower
                            </h3>
                            <p class="hw-desc">
                                {{
                                    currentLang === "id"
                                        ? "Pemanas keramik berefisiensi tinggi dipadu dengan hembusan sirkulasi merata ke seluruh rak, aman tanpa merusak kandungan gizi biji hanjeli."
                                        : "High-efficiency ceramic heater combined with uniform forced convection across all drying racks, preserving Hanjeli nutritional integrity."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <span class="hw-spec-tag">{{
                                currentLang === "id"
                                    ? "Daya: 120W Rendah Emisi"
                                    : "Power: 120W Low Emission"
                            }}</span>
                            <span class="hw-badge-red">{{
                                currentLang === "id"
                                    ? "Konveksi Merata"
                                    : "Even Convection"
                            }}</span>
                        </div>
                    </div>

                    <!-- Card 4: Relay & Hysteresis -->
                    <div class="hw-card">
                        <div class="hw-card-top">
                            <div class="hw-icon-box">
                                <img
                                    src="/assets/icons/hardware/Relay.png"
                                    alt="Modul Relay & Histeresis"
                                    class="icon-3d-img"
                                />
                            </div>
                            <h3 class="hw-title">
                                {{
                                    currentLang === "id"
                                        ? "Modul Relay & Histeresis"
                                        : "Relay & Hysteresis Module"
                                }}
                            </h3>
                            <p class="hw-desc">
                                {{
                                    currentLang === "id"
                                        ? "Siklus cerdas: ON otomatis saat suhu chamber drop di ≤38°C, dan OFF saat mencapai target ≥40°C. Suhu stabil terjaga tanpa perlu campur tangan manual."
                                        : "Smart hysteresis: Auto ON when chamber temperature drops to ≤38°C, and OFF at target ≥40°C. Maintains stable temperature hands-free."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <span class="hw-spec-tag"
                                >Switching Solid-State</span
                            >
                            <span class="hw-badge-green">Auto 38°C - 40°C</span>
                        </div>
                    </div>

                    <!-- Card 5: LDR / Light Sensor -->
                    <div class="hw-card">
                        <div class="hw-card-top">
                            <div class="hw-icon-box">
                                <img
                                    src="/assets/icons/hardware/LDR.png"
                                    alt="Sensor Cahaya LDR"
                                    class="icon-3d-img"
                                />
                            </div>
                            <h3 class="hw-title">
                                {{
                                    currentLang === "id"
                                        ? "Sensor Intensitas Cahaya (LDR / BH1750)"
                                        : "Light Intensity Sensor (LDR / BH1750)"
                                }}
                            </h3>
                            <p class="hw-desc">
                                {{
                                    currentLang === "id"
                                        ? "Memantau tingkat intensitas cahaya matahari dan radiasi surya ruangan pengering secara kontinu untuk optimalisasi dehidrasi biji hanjeli."
                                        : "Continuously monitors solar radiation and ambient light intensity in the drying chamber for optimized grain dehydration kinetics."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <span class="hw-spec-tag">{{
                                currentLang === "id"
                                    ? "I2C SDA 21 / SCL 22"
                                    : "I2C SDA 21 / SCL 22"
                            }}</span>
                            <span class="hw-badge-orange">{{
                                currentLang === "id"
                                    ? "Sensor Aktif"
                                    : "Active Sensor"
                            }}</span>
                        </div>
                    </div>

                    <!-- Card 6: Rain Sensor -->
                    <div class="hw-card hw-card-featured">
                        <div class="hw-card-top">
                            <div class="hw-icon-box hw-icon-box-featured">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
                                    <line x1="8" y1="19" x2="8" y2="21"></line>
                                    <line x1="8" y1="13" x2="8" y2="15"></line>
                                    <line x1="16" y1="19" x2="16" y2="21"></line>
                                    <line x1="16" y1="13" x2="16" y2="15"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="12" y1="15" x2="12" y2="17"></line>
                                </svg>
                            </div>
                            <h3 class="hw-title text-white">
                                {{
                                    currentLang === "id"
                                        ? "Sensor Deteksi Hujan (Rain Sensor)"
                                        : "Rain Detection Sensor"
                                }}
                            </h3>
                            <p class="hw-desc text-white-sub">
                                {{
                                    currentLang === "id"
                                        ? "Mendeteksi rintik hujan dan kebasahan luar secara instan via analog ADC pin 34 untuk proteksi ruang greenhouse dan otomasi pemanas cadangan."
                                        : "Instantly detects raindrops and external precipitation via analog ADC pin 34 for greenhouse protection and automated heating assist."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <div class="hw-badge-pill-light">
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#FFFFFF"
                                    stroke-width="2.5"
                                >
                                    <polyline
                                        points="20 6 9 17 4 12"
                                    ></polyline>
                                </svg>
                                <span>{{
                                    currentLang === "id"
                                        ? "Analog ADC Pin 34 • Respon Cepat"
                                        : "Analog ADC Pin 34 • Fast Response"
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assembly Showcase Feature Banner -->
                <div class="assembly-banner-card">
                    <div class="assembly-visual">
                        <img
                            src="/assets/img/iot_panel.jpg"
                            alt="Box Panel IoT ESP32 Smart Room Dryer"
                            class="assembly-photo-img"
                        />
                    </div>
                    <div class="assembly-content">
                        <span class="assembly-tagline">{{
                            currentLang === "id"
                                ? "STANDAR KEAMANAN RUANG PANEN"
                                : "HARVEST SAFETY STANDARDS"
                        }}</span>
                        <h3 class="assembly-title">
                            {{
                                currentLang === "id"
                                    ? "Dirakit Khusus untuk Lingkungan Kelompok Tani"
                                    : "Custom Engineered for Farmer Communities"
                            }}
                        </h3>
                        <p class="assembly-desc">
                            {{
                                currentLang === "id"
                                    ? "Semua kabel terisolasi rapi di dalam panel berpenutup anti-debu dengan proteksi arus pendek, aman digunakan oleh operator kelompok tani di Desa Wisata Hanjeli Waluran."
                                    : "All wiring is neatly insulated inside a dustproof, weather-resistant enclosure with short-circuit protection, safe for operator use in Waluran."
                            }}
                        </p>
                        <div class="assembly-badges-row">
                            <div class="assembly-badge">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0D631B"
                                    stroke-width="2.5"
                                >
                                    <path
                                        d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                    ></path>
                                </svg>
                                <span>{{
                                    currentLang === "id"
                                        ? "Proteksi Tegangan"
                                        : "Voltage Protection"
                                }}</span>
                            </div>
                            <div class="assembly-badge">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0D631B"
                                    stroke-width="2.5"
                                >
                                    <path
                                        d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                    ></path>
                                </svg>
                                <span>{{
                                    currentLang === "id"
                                        ? "Anti Percikan Debu"
                                        : "Dust Splash Resistant"
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. WEB APP FEATURES SECTION -->
        <section class="section-wrapper" id="fitur">
            <div class="section-container">
                <!-- Section Header -->
                <div class="section-header-center">
                    <span class="section-tagline">{{
                        currentLang === "id"
                            ? "ANTARMUKA RESPONSIF & SEDERHANA"
                            : "RESPONSIVE & SIMPLE INTERFACE"
                    }}</span>
                    <h2 class="section-heading">
                        {{
                            currentLang === "id"
                                ? "Keunggulan Fitur Aplikasi Web"
                                : "Key Features of Web Application"
                        }}
                    </h2>
                    <p class="section-subtext">
                        {{
                            currentLang === "id"
                                ? "Platform monitoring yang mudah dipahami oleh petani maupun peneliti, dapat diakses dari smartphone, tablet, maupun laptop."
                                : "A monitoring platform easy to understand for both farmers and researchers, accessible via smartphones, tablets, or laptops."
                        }}
                    </p>
                </div>

                <!-- 4 App Feature Cards Grid -->
                <div class="app-features-grid">
                    <!-- Card 1 -->
                    <div class="feature-card">
                        <div class="feature-icon-box">
                            <img
                                src="/assets/icons/fitur/Monitoring.png"
                                alt="Monitoring Real-time"
                                class="icon-3d-img"
                            />
                        </div>
                        <h3 class="feature-title">
                            {{
                                currentLang === "id"
                                    ? "Monitoring Real-time"
                                    : "Real-time Monitoring"
                            }}
                        </h3>
                        <p class="feature-desc">
                            {{
                                currentLang === "id"
                                    ? "Pantau fluktuasi grafik suhu chamber dan kelembapan hanjeli detik demi detik langsung dari gawai petani tanpa harus membuka pintu ruangan."
                                    : "Monitor chamber temperature and grain humidity fluctuations second-by-second directly from your smartphone without opening the dryer doors."
                            }}
                        </p>
                        <div class="feature-footer">
                            <span class="feature-tag text-green">{{
                                currentLang === "id"
                                    ? "Interval update 3s"
                                    : "3s Update Interval"
                            }}</span>
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#0D631B"
                                stroke-width="2.5"
                            >
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="feature-card">
                        <div class="feature-icon-box">
                            <img
                                src="/assets/icons/fitur/Kontrol.png"
                                alt="Kontrol Siklus Mudah"
                                class="icon-3d-img"
                            />
                        </div>
                        <h3 class="feature-title">
                            {{
                                currentLang === "id"
                                    ? "Kontrol Siklus Mudah"
                                    : "Simple Cycle Control"
                            }}
                        </h3>
                        <p class="feature-desc">
                            {{
                                currentLang === "id"
                                    ? "Mulai dan akhiri sesi pengeringan dengan satu ketukan tombol. Sistem mengaktifkan pemanas dan sirkulasi secara terpadu tanpa setelan rumit."
                                    : "Start and conclude drying sessions with one tap. The system orchestrates heating and airflow automatically without complex setup."
                            }}
                        </p>
                        <div class="feature-footer">
                            <span class="feature-tag text-blue"
                                >One-Touch Trigger</span
                            >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#005DB7"
                                stroke-width="2.5"
                            >
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="feature-card">
                        <div class="feature-icon-box">
                            <img
                                src="/assets/icons/fitur/Riwayat.png"
                                alt="Riwayat & Export Laporan"
                                class="icon-3d-img"
                            />
                        </div>
                        <h3 class="feature-title">
                            {{
                                currentLang === "id"
                                    ? "Riwayat & Export Laporan"
                                    : "History & Export Reports"
                            }}
                        </h3>
                        <p class="feature-desc">
                            {{
                                currentLang === "id"
                                    ? "Arsip lengkap histori tiap siklus pengeringan, durasi total, dan penurunan kadar air. Cetak laporan sertifikasi mutu PDF atau unduh CSV."
                                    : "Comprehensive archives of drying history, duration, and moisture decrease. Generate official PDF certification reports or export CSV data."
                            }}
                        </p>
                        <div class="feature-footer">
                            <span class="feature-tag text-orange">{{
                                currentLang === "id"
                                    ? "PDF & Format CSV"
                                    : "PDF & CSV Format"
                            }}</span>
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#B45000"
                                stroke-width="2.5"
                            >
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="feature-card">
                        <div class="feature-icon-box">
                            <img
                                src="/assets/icons/fitur/Multi-Login.png"
                                alt="Multi-Login Fleksibel"
                                class="icon-3d-img"
                            />
                        </div>
                        <h3 class="feature-title">
                            {{
                                currentLang === "id"
                                    ? "Multi-Login Fleksibel"
                                    : "Flexible Multi-Login"
                            }}
                        </h3>
                        <p class="feature-desc">
                            {{
                                currentLang === "id"
                                    ? "Akses cepat dan aman menggunakan sandi reguler, login instan Akun Google, hingga otentikasi biometrik Passkey (sidik jari ponsel)."
                                    : "Swift and secure authentication via password, instant Google sign-in, or modern WebAuthn biometrics (Passkey fingerprint)."
                            }}
                        </p>
                        <div class="feature-footer">
                            <span class="feature-tag text-green"
                                >Google & Passkey Ready</span
                            >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#0D631B"
                                stroke-width="2.5"
                            >
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Landscape Banner Card -->
                <div class="panoramic-banner">
                    <img
                        src="/assets/img/plantation_landscape.jpg"
                        alt="Perkebunan Hanjeli Waluran Sukabumi"
                        class="panoramic-img"
                    />
                    <div class="panoramic-overlay">
                        <span class="panoramic-tagline">{{
                            currentLang === "id"
                                ? "PASCAPANEN BERNILAI TAMBAH"
                                : "VALUE-ADDED POST-HARVEST"
                        }}</span>
                        <h3 class="panoramic-title">
                            {{
                                currentLang === "id"
                                    ? "Menjaga Kemurnian & Daya Simpan Hanjeli Waluran"
                                    : "Preserving Purity & Shelf Life of Waluran Hanjeli"
                            }}
                        </h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. WORKFLOW / SOP SECTION -->
        <section class="section-wrapper bg-soft-blue" id="panduan">
            <div class="section-container">
                <!-- Section Header -->
                <div class="section-header-center">
                    <span class="section-tagline">{{
                        currentLang === "id"
                            ? "PROSEDUR STANDAR OPERASIONAL"
                            : "STANDARD OPERATING PROCEDURE"
                    }}</span>
                    <h2 class="section-heading">
                        {{
                            currentLang === "id"
                                ? "Alur Kerja Operasional Pengeringan"
                                : "Operational Drying Workflow"
                        }}
                    </h2>
                    <p class="section-subtext">
                        {{
                            currentLang === "id"
                                ? "Empat langkah mudah dari pemuatan gabah basah hingga hanjeli kering siap kemas berstandar pangan unggul."
                                : "Four simple steps from loading raw grain to ready-to-package premium dried Hanjeli."
                        }}
                    </p>
                </div>

                <!-- 4 Step Process Cards -->
                <div class="steps-grid">
                    <!-- Step 1 -->
                    <div class="step-card">
                        <div class="step-icon-wrap">
                            <img
                                src="/assets/icons/alur/step1.png"
                                alt="Langkah 1"
                                class="step-icon-img"
                            />
                        </div>
                        <h3 class="step-title">
                            {{
                                currentLang === "id"
                                    ? "Masukkan Gabah Hanjeli"
                                    : "Load Hanjeli Grain"
                            }}
                        </h3>
                        <p class="step-desc">
                            {{
                                currentLang === "id"
                                    ? "Ratakan biji Hanjeli yang telah dicuci bersih dan ditiriskan ke atas rak pengering bertingkat di dalam ruangan khusus."
                                    : "Evenly spread washed and drained Hanjeli grains onto multi-tier drying racks inside the specialized chamber."
                            }}
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-card">
                        <div class="step-icon-wrap">
                            <img
                                src="/assets/icons/alur/step2.png"
                                alt="Langkah 2"
                                class="step-icon-img"
                            />
                        </div>
                        <h3 class="step-title">
                            {{
                                currentLang === "id"
                                    ? "Mulai Pengeringan"
                                    : "Start Drying Cycle"
                            }}
                        </h3>
                        <p class="step-desc">
                            {{
                                currentLang === "id"
                                    ? 'Masuk ke aplikasi web via smartphone dan tekan tombol "Mulai Pengeringan". Target suhu standar 40°C langsung aktif.'
                                    : 'Open the web app on your phone and tap "Start Drying". The 40°C standard target temperature activates instantly.'
                            }}
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="step-card">
                        <div class="step-icon-wrap">
                            <img
                                src="/assets/icons/alur/step3.png"
                                alt="Langkah 3"
                                class="step-icon-img"
                            />
                        </div>
                        <h3 class="step-title">
                            {{
                                currentLang === "id"
                                    ? "Otomasi Suhu ESP32"
                                    : "ESP32 Auto Regulation"
                            }}
                        </h3>
                        <p class="step-desc">
                            {{
                                currentLang === "id"
                                    ? "Mikrokontroler & Relay mengontrol heater secara otomatis menjaga stabilitas panas hingga batas kadar air aman tercapai."
                                    : "Microcontroller & Relays modulate heaters automatically to sustain optimal heat until target moisture is achieved."
                            }}
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="step-card">
                        <div class="step-icon-wrap">
                            <img
                                src="/assets/icons/alur/step4.png"
                                alt="Langkah 4"
                                class="step-icon-img"
                            />
                        </div>
                        <h3 class="step-title">
                            {{
                                currentLang === "id"
                                    ? "Unduh Laporan Mutu"
                                    : "Download Quality Report"
                            }}
                        </h3>
                        <p class="step-desc">
                            {{
                                currentLang === "id"
                                    ? "Selesai pengeringan, pantau rangkuman grafik termal dan cetak arsip batch (PDF/CSV) untuk jaminan mutu pembeli wisata."
                                    : "Upon completion, review the thermal summary and export certified batch reports (PDF/CSV) for tourist market assurance."
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. BOTTOM CALL TO ACTION -->
        <section class="section-wrapper cta-section">
            <div class="section-container">
                <div class="cta-banner-card">
                    <!-- 3D Large Illustration on Far Left -->
                    <div class="cta-icon-wrap">
                        <img
                            src="/assets/icons/akses/akses.png"
                            alt="Akses Operator & Pengelola"
                            class="cta-3d-img"
                        />
                    </div>

                    <!-- Content in Middle -->
                    <div class="cta-content">
                        <span class="cta-tagline">{{
                            currentLang === "id"
                                ? "AKSES OPERATOR & PENGELOLA"
                                : "OPERATOR & MANAGER ACCESS"
                        }}</span>
                        <h2 class="cta-heading">
                            {{
                                currentLang === "id"
                                    ? "Siap Meningkatkan Kualitas Panen Hanjeli?"
                                    : "Ready to Elevate Your Hanjeli Harvest?"
                            }}
                        </h2>
                        <p class="cta-sub">
                            {{
                                currentLang === "id"
                                    ? "Masuk ke dasbor terpusat untuk memantau status operasional alat pengering secara real-time dari manapun Anda berada."
                                    : "Access the centralized dashboard to monitor drying operations in real-time wherever you are."
                            }}
                        </p>
                    </div>

                    <!-- Action Button on Right -->
                    <div class="cta-action">
                        <button class="btn-cta-white" @click="goToApp">
                            <span>{{
                                isAuth
                                    ? currentLang === "id"
                                        ? "Masuk ke Dashboard"
                                        : "Open Dashboard"
                                    : currentLang === "id"
                                      ? "Masuk Sekarang"
                                      : "Login Now"
                            }}</span>
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#0D631B"
                                stroke-width="2.5"
                            >
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. FOOTER (Matching CoE STAS-RG Theme & Layout) -->
        <footer class="landing-footer">
            <div class="section-container">
                <div class="footer-top-grid">
                    <!-- Column 1: Brand, Description & Social Links -->
                    <div class="footer-col footer-col-brand">
                        <div class="footer-brand-header">
                            <div class="footer-brand-logo-box">
                                <img
                                    v-if="logoSrc"
                                    :src="logoSrc"
                                    alt="Hanjeli Logo"
                                    class="footer-hanjeli-logo"
                                />
                                <svg
                                    v-else
                                    width="22"
                                    height="22"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0D631B"
                                    stroke-width="2.2"
                                >
                                    <path
                                        d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                                    ></path>
                                </svg>
                            </div>
                            <div class="footer-brand-meta">
                                <h3 class="footer-brand-title">
                                    Desa Wisata Hanjeli
                                </h3>
                                <span class="footer-brand-badge"
                                    >Smart Room Dryer IoT</span
                                >
                            </div>
                        </div>
                        <p class="footer-desc">
                            {{
                                currentLang === "id"
                                    ? "Pusat edukasi agrowisata, budidaya, dan hilirisasi pangan lokal Hanjeli di Waluran Sukabumi (Kawasan Geopark Ciletuh), dilengkapi teknologi Green House Smart Dryer cerdas berbasis IoT hasil kolaborasi bersama Center of Excellence STAS-RG Telkom University."
                                    : "Education, agrotourism, and local food preservation center of Hanjeli in Waluran Sukabumi (Ciletuh Geopark area), equipped with IoT Smart Dryer Green House technology developed in collaboration with CoE STAS-RG Telkom University."
                            }}
                        </p>
                        <div class="footer-social-row">
                            <a
                                href="https://www.instagram.com/desawisatahanjeli"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="footer-social-btn"
                                title="@desawisatahanjeli"
                            >
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect
                                        x="2"
                                        y="2"
                                        width="20"
                                        height="20"
                                        rx="5"
                                        ry="5"
                                    ></rect>
                                    <path
                                        d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"
                                    ></path>
                                    <line
                                        x1="17.5"
                                        y1="6.5"
                                        x2="17.51"
                                        y2="6.5"
                                    ></line>
                                </svg>
                            </a>
                            <a
                                href="https://www.linkedin.com"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="footer-social-btn"
                                title="LinkedIn"
                            >
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"
                                    ></path>
                                    <rect
                                        x="2"
                                        y="9"
                                        width="4"
                                        height="12"
                                    ></rect>
                                    <circle cx="4" cy="4" r="2"></circle>
                                </svg>
                            </a>
                            <a
                                href="https://github.com"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="footer-social-btn"
                                title="GitHub"
                            >
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"
                                    ></path>
                                </svg>
                            </a>
                            <a
                                href="https://youtube.com"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="footer-social-btn"
                                title="YouTube"
                            >
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"
                                    ></path>
                                    <polygon
                                        points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"
                                    ></polygon>
                                </svg>
                            </a>
                            <a
                                href="https://www.visithanjeli.com"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="footer-social-btn"
                                title="Website Resmi"
                            >
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path
                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"
                                    ></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Column 2: Navigasi -->
                    <div class="footer-col footer-col-nav">
                        <div class="footer-col-header">
                            <h4 class="footer-heading">
                                {{
                                    currentLang === "id"
                                        ? "Navigasi"
                                        : "Navigation"
                                }}
                            </h4>
                            <span class="footer-green-bar"></span>
                        </div>
                        <nav class="footer-nav-list">
                            <a
                                href="#tentang"
                                @click.prevent="scrollTo('tentang')"
                                >{{
                                    currentLang === "id" ? "Tentang" : "About"
                                }}</a
                            >
                            <a
                                href="#hardware"
                                @click.prevent="scrollTo('hardware')"
                                >{{
                                    currentLang === "id"
                                        ? "Hardware"
                                        : "Hardware"
                                }}</a
                            >
                            <a
                                href="#fitur"
                                @click.prevent="scrollTo('fitur')"
                                >{{
                                    currentLang === "id" ? "Fitur" : "Features"
                                }}</a
                            >
                            <a
                                href="#panduan"
                                @click.prevent="scrollTo('panduan')"
                                >{{
                                    currentLang === "id" ? "Panduan" : "Guide"
                                }}</a
                            >
                        </nav>
                    </div>

                    <!-- Column 3: Info Kontak -->
                    <div class="footer-col footer-col-contact">
                        <div class="footer-col-header">
                            <h4 class="footer-heading">
                                {{
                                    currentLang === "id"
                                        ? "Info Kontak"
                                        : "Contact Info"
                                }}
                            </h4>
                            <span class="footer-green-bar"></span>
                        </div>
                        <div class="footer-contact-list">
                            <!-- EMAIL -->
                            <div class="footer-contact-item">
                                <div class="footer-contact-icon">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                                        ></path>
                                        <polyline
                                            points="22,6 12,13 2,6"
                                        ></polyline>
                                    </svg>
                                </div>
                                <div class="footer-contact-info">
                                    <span class="contact-label">EMAIL</span>
                                    <a
                                        href="mailto:stas-rg@telkomuniversity.ac.id"
                                        class="contact-value"
                                        >stas-rg@telkomuniversity.ac.id</a
                                    >
                                </div>
                            </div>

                            <!-- PHONE / WA -->
                            <div class="footer-contact-item">
                                <div class="footer-contact-icon">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="footer-contact-info">
                                    <span class="contact-label">TELEPON / WA</span>
                                    <div style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                                        <a
                                            href="tel:085722182480"
                                            class="contact-value"
                                            >0857-2218-2480</a
                                        >
                                        <span style="color: #94A3B8; font-size: 11px;">/</span>
                                        <a
                                            href="tel:081398115760"
                                            class="contact-value"
                                            >0813-9811-5760</a
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- INSTAGRAM -->
                            <div class="footer-contact-item">
                                <div class="footer-contact-icon">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                    </svg>
                                </div>
                                <div class="footer-contact-info">
                                    <span class="contact-label">INSTAGRAM</span>
                                    <a
                                        href="https://www.instagram.com/desawisatahanjeli"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="contact-value"
                                        >@desawisatahanjeli</a
                                    >
                                </div>
                            </div>

                            <!-- WEBSITE -->
                            <div class="footer-contact-item">
                                <div class="footer-contact-icon">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line
                                            x1="2"
                                            y1="12"
                                            x2="22"
                                            y2="12"
                                        ></line>
                                        <path
                                            d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="footer-contact-info">
                                    <span class="contact-label">WEBSITE</span>
                                    <a
                                        href="https://www.visithanjeli.com"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="contact-value"
                                        >www.visithanjeli.com</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 4: Lokasi Desa Wisata Hanjeli Map -->
                    <div class="footer-col footer-col-map">
                        <div class="footer-col-header">
                            <h4 class="footer-heading">
                                {{
                                    currentLang === "id"
                                        ? "Lokasi Desa Wisata"
                                        : "Tourism Village Location"
                                }}
                            </h4>
                            <span class="footer-green-bar"></span>
                        </div>
                        <div class="footer-map-card">
                            <div class="footer-map-iframe-box">
                                <iframe
                                    src="https://maps.google.com/maps?q=Desa%20Wisata%20Hanjeli,%20Jl.%20Pamoyan,%20Waluran%20Mandiri,%20Kec.%20Waluran,%20Kabupaten%20Sukabumi,%20Jawa%20Barat%2043175&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                    width="100%"
                                    height="125"
                                    style="border: 0"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Peta Lokasi Desa Wisata Hanjeli - Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)"
                                ></iframe>
                            </div>
                            <div
                                class="footer-map-address"
                                title="Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)"
                            >
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0D631B"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"
                                    ></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span
                                    >Waluran, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat (Kawasan Geopark Ciletuh)</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Copyright Bar -->
                <div class="footer-bottom-row">
                    <span class="footer-copy">
                        {{
                            currentLang === "id"
                                ? "Hak Cipta © 2026 Desa Wisata Hanjeli — Kolaborasi Riset bersama CoE STAS-RG Universitas Telkom. Semua hak dilindungi."
                                : "Copyright © 2026 Hanjeli Tourism Village — In Collaboration with CoE STAS-RG Telkom University. All rights reserved."
                        }}
                    </span>
                    <div class="footer-meta-tags">
                        <span class="footer-tagline">
                            {{
                                currentLang === "id"
                                    ? "Telemetri IoT • Pengeringan Hanjeli Real-Time"
                                    : "IoT Telemetry • Real-Time Hanjeli Drying"
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import { animate, inView, stagger } from "motion";
import FlagIcon from "../../components/FlagIcon.vue";
import LandingHeroPhoto from "../../components/LandingHeroPhoto.vue";
import { authService } from "../../services/authService";
import { isDark, toggleTheme } from "../../services/themeService";
import { currentLang, setLanguage } from "../../i18n";

const route = useRoute();
const router = useRouter();
const activeSection = ref("tentang");
const logoSrc = ref("/assets/img/hanjeli.png");

const currentUser = ref(authService.getCurrentUser());
const isAuth = computed(() => !!currentUser.value);

// Sticky & Mobile Navbar States
const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);
const navbarRef = ref(null);

// Language Dropdown State
const isLangOpen = ref(false);
const langDropdownRef = ref(null);

function selectLang(lang) {
    setLanguage(lang);
    isLangOpen.value = false;
}

function toggleMobileMenu() {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
    if (isMobileMenuOpen.value) {
        isLangOpen.value = false;
    }
}

function handleClickOutside(e) {
    if (langDropdownRef.value && !langDropdownRef.value.contains(e.target)) {
        isLangOpen.value = false;
    }
    if (
        isMobileMenuOpen.value &&
        e.target &&
        e.target.isConnected &&
        navbarRef.value &&
        !navbarRef.value.contains(e.target)
    ) {
        isMobileMenuOpen.value = false;
    }
}

function goToApp() {
    isMobileMenuOpen.value = false;
    if (isAuth.value) {
        if (currentUser.value?.role === "ADMIN") {
            router.push("/admin");
        } else {
            router.push("/dashboard");
        }
    } else {
        router.push("/login");
    }
}

function goToVerify() {
    isMobileMenuOpen.value = false;
    router.push("/verify");
}

let isProgrammaticScrolling = false;
let scrollTimeout = null;

function getScrollableContainer() {
    return (
        document.querySelector(".standalone-viewport") ||
        document.querySelector(".desktop-subview-scroll") ||
        document.querySelector(".mobile-scroll-content") ||
        document.documentElement
    );
}

function scrollTo(sectionId) {
    activeSection.value = sectionId;
    isMobileMenuOpen.value = false;
    const el = document.getElementById(sectionId);
    if (el) {
        isProgrammaticScrolling = true;
        clearTimeout(scrollTimeout);

        const navHeight = navbarRef.value?.offsetHeight || 72;
        const scrollContainer = getScrollableContainer();

        if (
            scrollContainer &&
            scrollContainer !== document.documentElement &&
            scrollContainer !== window
        ) {
            const containerRect = scrollContainer.getBoundingClientRect();
            const elRect = el.getBoundingClientRect();
            const targetScrollTop =
                scrollContainer.scrollTop +
                (elRect.top - containerRect.top) -
                navHeight +
                2;

            scrollContainer.scrollTo({
                top: Math.max(0, targetScrollTop),
                behavior: "smooth",
            });
        } else {
            const elementPosition =
                el.getBoundingClientRect().top + window.scrollY;
            const offsetPosition = Math.max(0, elementPosition - navHeight + 2);
            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });
        }

        scrollTimeout = setTimeout(() => {
            isProgrammaticScrolling = false;
            activeSection.value = sectionId;
        }, 850);
    }
}

function handleScroll() {
    const scrollContainer = getScrollableContainer();
    const currentScrollY =
        scrollContainer &&
        scrollContainer !== document.documentElement &&
        scrollContainer !== window
            ? scrollContainer.scrollTop
            : window.scrollY;

    isScrolled.value = currentScrollY > 20;
    if (isProgrammaticScrolling) return;

    const sections = ["tentang", "hardware", "fitur", "panduan"];
    const scrollPosition = currentScrollY + 180;

    for (let i = sections.length - 1; i >= 0; i--) {
        const id = sections[i];
        const el = document.getElementById(id);
        if (el) {
            const top = el.offsetTop;
            if (scrollPosition >= top) {
                activeSection.value = id;
                break;
            }
        }
    }
}

function initFramerMotion() {
    try {
        const smoothEase = [0.16, 1, 0.3, 1];
        const springBouncy = [0.34, 1.4, 0.64, 1];

        // 1. HARDWARE ARCHITECTURE SECTION (Scroll inView - Spring Pop & Slide)
        inView(
            "#hardware .section-header-center",
            ({ target }) => {
                animate(
                    target,
                    { opacity: [0, 1], y: [28, 0], scale: [0.96, 1] },
                    { duration: 0.65, easing: smoothEase },
                );
            },
            { margin: "0px 0px -40px 0px" },
        );

        inView(
            "#hardware .hardware-grid",
            () => {
                animate(
                    "#hardware .hw-card",
                    {
                        opacity: [0, 1],
                        y: [40, 0],
                        scale: [0.9, 1],
                        rotate: [-1.5, 0],
                    },
                    {
                        delay: stagger(0.08),
                        duration: 0.6,
                        easing: springBouncy,
                    },
                );
            },
            { margin: "0px 0px -50px 0px" },
        );

        inView(
            ".assembly-banner-card",
            ({ target }) => {
                animate(
                    target,
                    { opacity: [0, 1], x: [-45, 0], scale: [0.96, 1] },
                    { duration: 0.75, easing: smoothEase },
                );
            },
            { margin: "0px 0px -50px 0px" },
        );

        // 2. WEB APPLICATION FEATURES SECTION (Scroll inView - Vertical Wave)
        inView(
            "#fitur .section-header-center",
            ({ target }) => {
                animate(
                    target,
                    { opacity: [0, 1], y: [-20, 0] },
                    { duration: 0.6, easing: smoothEase },
                );
            },
            { margin: "0px 0px -40px 0px" },
        );

        inView(
            "#fitur .app-features-grid",
            () => {
                animate(
                    "#fitur .feature-card",
                    { opacity: [0, 1], y: [50, 0], scale: [0.92, 1] },
                    {
                        delay: stagger(0.12),
                        duration: 0.65,
                        easing: springBouncy,
                    },
                );
            },
            { margin: "0px 0px -50px 0px" },
        );

        // 3. WORKFLOW & SOP SECTION (Scroll inView - Horizontal Domino & Cinematic Zoom)
        inView(
            "#panduan .section-header-center",
            ({ target }) => {
                animate(
                    target,
                    { opacity: [0, 1], scale: [0.94, 1] },
                    { duration: 0.6, easing: smoothEase },
                );
            },
            { margin: "0px 0px -40px 0px" },
        );

        inView(
            "#panduan .steps-grid",
            () => {
                animate(
                    "#panduan .step-card",
                    { opacity: [0, 1], x: [-35, 0], scale: [0.94, 1] },
                    { delay: stagger(0.12), duration: 0.6, easing: smoothEase },
                );
            },
            { margin: "0px 0px -50px 0px" },
        );

        inView(
            ".panoramic-banner",
            ({ target }) => {
                animate(
                    target,
                    { opacity: [0, 1], scale: [1.06, 1], y: [25, 0] },
                    { duration: 0.9, easing: smoothEase },
                );
            },
            { margin: "0px 0px -50px 0px" },
        );

        // 4. CALL TO ACTION SECTION (Scroll inView - Spring Pop)
        inView(
            ".cta-banner-card",
            ({ target }) => {
                animate(
                    target,
                    { opacity: [0, 1], scale: [0.88, 1], y: [25, 0] },
                    { duration: 0.7, easing: springBouncy },
                );
            },
            { margin: "0px 0px -40px 0px" },
        );

        // 5. FOOTER SECTION (Scroll inView - Subtle Staggered Fade Up)
        inView(
            ".landing-footer",
            () => {
                animate(
                    ".landing-footer .footer-col",
                    { opacity: [0, 1], y: [24, 0] },
                    { delay: stagger(0.08), duration: 0.6, easing: smoothEase },
                );
                animate(
                    ".landing-footer .footer-bottom-row",
                    { opacity: [0, 1], y: [14, 0] },
                    { delay: 0.35, duration: 0.55, easing: smoothEase },
                );
            },
            { margin: "0px 0px -30px 0px" },
        );
    } catch (e) {
        console.warn("Motion initialization note:", e);
    }
}

onMounted(() => {
    const scrollContainer = getScrollableContainer();
    if (scrollContainer && scrollContainer !== window) {
        scrollContainer.addEventListener("scroll", handleScroll, {
            passive: true,
        });
    }
    window.addEventListener("scroll", handleScroll, { passive: true });
    document.addEventListener("click", handleClickOutside);

    // Initialize Framer Motion animations
    nextTick(() => {
        initFramerMotion();
    });

});

onUnmounted(() => {
    const scrollContainer = getScrollableContainer();
    if (scrollContainer && scrollContainer !== window) {
        scrollContainer.removeEventListener("scroll", handleScroll);
    }
    window.removeEventListener("scroll", handleScroll);
    document.removeEventListener("click", handleClickOutside);
    if (scrollTimeout) clearTimeout(scrollTimeout);
});
</script>

<style scoped>
/* Core Colors & Typography */
.landing-page {
    width: 100%;
    min-height: 100vh;
    box-sizing: border-box;
    background-color: #f4faff;
    color: #111d23;
    font-family:
        "Plus Jakarta Sans",
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Roboto,
        sans-serif;
    overflow-x: clip;
    position: relative;
    transition:
        background-color 0.25s ease,
        color 0.25s ease;
}

/* 1. Navbar */
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
    transition:
        background-color 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;
}

.landing-navbar.navbar-scrolled {
    background: rgba(244, 250, 255, 0.97);
    box-shadow:
        0 4px 20px -2px rgba(13, 99, 27, 0.08),
        0 2px 8px -1px rgba(0, 0, 0, 0.04);
    border-bottom-color: #cbd5e1;
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
    color: #111d23;
    line-height: 1.2;
    white-space: nowrap;
}

.brand-sub {
    font-size: 12px;
    font-weight: 500;
    color: #40493d;
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
    color: #40493d;
    text-decoration: none;
    transition: all 0.2s ease;
}

.nav-links a:hover {
    color: #0d631b;
    background: rgba(13, 99, 27, 0.06);
}

.nav-links a.active {
    background: #0d631b;
    color: #ffffff;
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-self: end;
}

/* Visibility helpers */
.mobile-only {
    display: none !important;
}

/* Seamless & Borderless Action Buttons (Matching Dashboard) */
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
    color: #0f172a;
    transform: translateY(-1px);
}

.mobile-menu-toggle-btn {
    border-radius: 8px;
    color: #111d23;
}

.mobile-menu-toggle-btn:hover,
.mobile-menu-toggle-btn.is-active {
    background: rgba(13, 99, 27, 0.1);
    color: #0d631b;
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
    background: #ffffff;
    border: 1px solid #cbd5e1;
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
    color: #111d23;
    cursor: pointer;
    transition: all 0.15s ease;
    width: 100%;
}

.lang-option:hover {
    background: #f4f7f5;
    color: #0d631b;
}

.lang-option.active {
    background: #e8f5e9;
    color: #0d631b;
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
    background: linear-gradient(180deg, #15803d 0%, #0d631b 55%, #094713 100%);
    color: #ffffff !important;
    font-size: 13.5px;
    font-weight: 700;
    border: 1px solid rgba(13, 99, 27, 0.4);
    border-radius: 10px;
    cursor: pointer;
    white-space: nowrap;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.45),
        inset 0 -1px 0 rgba(0, 0, 0, 0.3),
        0 2px 8px rgba(13, 99, 27, 0.28) !important;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    -webkit-user-select: none;
    user-select: none;
}

.btn-nav-login:hover {
    background: linear-gradient(180deg, #16a34a 0%, #15803d 55%, #0d631b 100%);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.6),
        inset 0 -1px 0 rgba(0, 0, 0, 0.2),
        0 4px 14px rgba(13, 99, 27, 0.38) !important;
    transform: translateY(-1.5px);
}

.btn-nav-login:active {
    background: linear-gradient(180deg, #0d631b 0%, #094713 100%);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
    transform: scale(0.98);
}

/* Mobile Menu Toggle Button */
.mobile-menu-btn {
    width: 38px;
    height: 38px;
    background: transparent;
    border: none;
    border-radius: 10px;
    display: none;
    align-items: center;
    justify-content: center;
    color: #1e293b;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    outline: none;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    flex-shrink: 0;
}

.mobile-menu-btn svg,
.mobile-menu-btn svg * {
    pointer-events: none;
}

.mobile-menu-btn:hover {
    background: rgba(13, 99, 27, 0.08);
    color: #0d631b;
}

.mobile-menu-btn:active {
    background: rgba(13, 99, 27, 0.16);
    transform: scale(0.94);
}

.dark-landing .mobile-menu-btn {
    color: #f1f5f9;
}

.dark-landing .mobile-menu-btn:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #4ade80;
}

.dark-landing .mobile-menu-btn:active {
    background: rgba(255, 255, 255, 0.14);
}

.landing-navbar.mobile-menu-active {
    background: rgba(244, 250, 255, 0.98);
    box-shadow: 0 4px 20px -2px rgba(13, 99, 27, 0.08);
}

.dark-landing .landing-navbar.mobile-menu-active {
    background: rgba(11, 25, 17, 0.98);
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4);
}

/* Mobile Drawer Backdrop */
.mobile-drawer-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    z-index: 998;
}

.mobile-backdrop-enter-active,
.mobile-backdrop-leave-active {
    transition: opacity 0.25s ease;
}

.mobile-backdrop-enter-from,
.mobile-backdrop-leave-to {
    opacity: 0;
}

/* Mobile Drawer & Dropdown */
.landing-mobile-drawer {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    width: 100%;
    max-height: calc(100vh - 72px);
    overflow-y: auto;
    background: rgba(244, 250, 255, 0.98);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border-bottom: 1px solid #cbd5e1;
    box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.12);
    box-sizing: border-box;
    z-index: 999;
}

.dark-landing .landing-mobile-drawer {
    background: rgba(11, 25, 17, 0.98);
    border-bottom-color: #1e3a2b;
    box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.45);
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
    color: #1e293b;
    text-decoration: none;
    background: transparent;
    transition: all 0.2s ease;
}

.dark-landing .mobile-drawer-links a {
    color: #f1f5f9;
}

.mobile-drawer-links a .link-icon-chip {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(13, 99, 27, 0.08);
    color: #0d631b;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.dark-landing .mobile-drawer-links a .link-icon-chip {
    background: rgba(74, 222, 128, 0.15);
    color: #4ade80;
}

.mobile-drawer-links a:hover,
.mobile-drawer-links a.active {
    background: rgba(13, 99, 27, 0.08);
    color: #0d631b;
}

.dark-landing .mobile-drawer-links a:hover,
.dark-landing .mobile-drawer-links a.active {
    background: rgba(74, 222, 128, 0.12);
    color: #4ade80;
}

.mobile-drawer-links a.active .link-icon-chip {
    background: #0d631b;
    color: #ffffff;
}

.dark-landing .mobile-drawer-links a.active .link-icon-chip {
    background: #0d631b;
    color: #ffffff;
}

.mobile-drawer-cta {
    padding-top: 10px;
    border-top: 1px solid rgba(203, 213, 225, 0.8);
}

.dark-landing .mobile-drawer-cta {
    border-top-color: rgba(30, 58, 43, 0.8);
}

.btn-mobile-login {
    position: relative;
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(180deg, #15803d 0%, #0d631b 55%, #094713 100%);
    color: #ffffff !important;
    border: 1px solid rgba(13, 99, 27, 0.4);
    border-radius: 12px;
    font-size: 14.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.45),
        inset 0 -1px 0 rgba(0, 0, 0, 0.3),
        0 4px 14px rgba(13, 99, 27, 0.28) !important;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    -webkit-user-select: none;
    user-select: none;
}

.btn-mobile-login:hover {
    background: linear-gradient(180deg, #16a34a 0%, #15803d 55%, #0d631b 100%);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.6),
        inset 0 -1px 0 rgba(0, 0, 0, 0.2),
        0 6px 18px rgba(13, 99, 27, 0.38) !important;
    transform: translateY(-1.5px);
}

.btn-mobile-login:active {
    background: linear-gradient(180deg, #0d631b 0%, #094713 100%);
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

/* 2. Hero Section */
.hero-section {
    position: relative;
    padding: 24px 24px 32px;
    overflow: hidden;
    scroll-margin-top: 68px;
    box-sizing: border-box;
}

.ambient-glow {
    position: absolute;
    border-radius: 9999px;
    filter: blur(60px);
    pointer-events: none;
    z-index: 0;
}

.glow-green {
    width: 400px;
    height: 400px;
    right: -80px;
    top: -60px;
    background: rgba(13, 99, 27, 0.08);
}

.glow-blue {
    width: 360px;
    height: 360px;
    left: -80px;
    bottom: 0px;
    background: rgba(0, 93, 183, 0.08);
}

.section-container {
    width: 100%;
    max-width: 1200px;
    box-sizing: border-box;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.hero-grid {
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    gap: 40px;
    align-items: center;
}

.hero-left {
    display: flex;
    flex-direction: column;
}

.hero-release-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 14px;
    background: #e8f5e9;
    border: 1px solid #c8e6c9;
    border-radius: 9999px;
    width: fit-content;
    margin-bottom: 18px;
}

.pill-pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pillPulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
}

@keyframes pillPulse {
    to {
        box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    }
}

.pill-version {
    font-size: 11.5px;
    font-weight: 800;
    color: #0d631b;
    letter-spacing: 0.3px;
}

.pill-sep {
    color: #81c784;
    font-size: 10px;
}

.pill-label {
    font-size: 12px;
    font-weight: 600;
    color: #2e7d32;
    letter-spacing: 0.2px;
}

.hero-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 5px 14px;
    background: #ddeaf2;
    border: 1px solid #cbd5e1;
    border-radius: 9999px;
    width: fit-content;
    margin-bottom: 20px;
}

.hero-top-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: #e8f5e9;
    border: 1px solid #c8e6c9;
    border-radius: 9999px;
    width: fit-content;
    margin-bottom: 16px;
}

.hero-badge-img {
    width: 22px;
    height: 22px;
    object-fit: contain;
    display: block;
}

.hero-top-badge span {
    font-size: 12.5px;
    font-weight: 700;
    color: #0d631b;
    letter-spacing: 0.2px;
}

/* Reusable 3D Image Icon Style */
.icon-3d-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.1));
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.tag-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #0d631b;
}

.tag-text {
    font-size: 12px;
    font-weight: 600;
    color: #0d631b;
    letter-spacing: 0.2px;
}

.hero-title {
    font-size: 38px;
    font-weight: 800;
    color: #111d23;
    line-height: 1.25;
    margin: 0 0 16px 0;
    letter-spacing: -0.5px;
}

.hero-desc {
    font-size: 16px;
    line-height: 1.6;
    color: #40493d;
    margin: 0 0 24px 0;
    max-width: 560px;
}

.hero-cta-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
    margin-bottom: 28px;
}

.btn-hero-primary {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 26px;
    background: linear-gradient(180deg, #15803d 0%, #0d631b 55%, #094713 100%);
    color: #ffffff !important;
    font-size: 14.5px;
    font-weight: 700;
    border: 1px solid rgba(13, 99, 27, 0.4);
    border-radius: 12px;
    cursor: pointer;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.45),
        inset 0 -1px 0 rgba(0, 0, 0, 0.3),
        0 4px 14px rgba(13, 99, 27, 0.3) !important;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    -webkit-user-select: none;
    user-select: none;
}

.btn-hero-primary:hover {
    background: linear-gradient(180deg, #16a34a 0%, #15803d 55%, #0d631b 100%);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.6),
        inset 0 -1px 0 rgba(0, 0, 0, 0.2),
        0 6px 18px rgba(13, 99, 27, 0.4) !important;
    transform: translateY(-1.5px);
}

.btn-hero-primary:active {
    background: linear-gradient(180deg, #0d631b 0%, #094713 100%);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
    transform: scale(0.98);
}

.btn-hero-secondary {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
    color: #1e293b !important;
    font-size: 14.5px;
    font-weight: 700;
    border: 1px solid rgba(203, 213, 225, 0.9);
    border-radius: 12px;
    cursor: pointer;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 1),
        0 2px 8px rgba(0, 0, 0, 0.04) !important;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    -webkit-user-select: none;
    user-select: none;
}

.btn-hero-secondary:hover {
    border-color: #0d631b;
    color: #0d631b !important;
    background: linear-gradient(180deg, #ffffff 0%, #e8f5e9 100%);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 1),
        0 4px 12px rgba(13, 99, 27, 0.15) !important;
    transform: translateY(-1.5px);
}

.btn-hero-secondary:active {
    background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
    transform: scale(0.98);
}

.hero-mini-cards-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.mini-card {
    padding: 8px 12px;
    background: #e9f6fd;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 9px;
    transition:
        transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.28s ease,
        border-color 0.2s ease;
    cursor: default;
}

.mini-card:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 18px -4px rgba(13, 99, 27, 0.12);
    border-color: #0d631b;
}

.mini-card-icon {
    width: 38px;
    height: 38px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 0;
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.mini-card:hover .mini-card-icon {
    transform: scale(1.12);
}

.mini-card-content {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.mini-card-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #111d23;
}

.mini-card-sub {
    font-size: 10.5px;
    font-weight: 500;
    color: #40493d;
    line-height: 1.25;
}

/* Hero Right: 3D Illustration Interactive Stage */
.hero-right {
    display: flex;
    align-items: center;
    justify-content: center;
    perspective: 1200px;
}

/* 3. Reusable Section Layout */
.section-wrapper {
    padding: 80px 24px;
    scroll-margin-top: 72px;
}

.bg-soft-blue {
    background-color: #e9f6fd;
    border-top: 1px solid #cbd5e1;
    border-bottom: 1px solid #cbd5e1;
    transition: all 0.25s ease;
}

.section-header-center {
    text-align: center;
    max-width: 720px;
    margin: 0 auto 52px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.section-tagline {
    font-size: 13px;
    font-weight: 700;
    color: #0d631b;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.section-heading {
    font-size: 32px;
    font-weight: 800;
    color: #111d23;
    line-height: 1.3;
    margin: 0;
}

.section-subtext {
    font-size: 15.5px;
    color: #40493d;
    line-height: 1.6;
    margin: 0;
}

/* Hardware Cards Grid */
.hardware-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
    margin-bottom: 40px;
}

.hw-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 16px;
    padding: 26px 22px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 18px;
    transition:
        transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.32s ease,
        border-color 0.25s ease,
        background-color 0.25s ease;
    cursor: default;
}

.hw-card:hover {
    transform: translateY(-8px);
    box-shadow:
        0 20px 36px -8px rgba(13, 99, 27, 0.16),
        0 6px 16px -4px rgba(0, 0, 0, 0.05);
    border-color: #0d631b;
}

.hw-card-top {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.hw-icon-box {
    width: 76px;
    height: 76px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 2px;
    padding: 0;
    transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.hw-card:hover .hw-icon-box {
    transform: scale(1.12) rotate(4deg);
}

.hw-title {
    font-size: 18.5px;
    font-weight: 700;
    color: #111d23;
    margin: 0;
    line-height: 1.35;
}

.hw-desc {
    font-size: 14px;
    line-height: 1.6;
    color: #40493d;
    margin: 0;
}

.hw-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 14px;
    border-top: 1px solid #e2e8f0;
    font-size: 12px;
    font-weight: 600;
}

.hw-spec-tag {
    color: #40493d;
}

.hw-badge-green {
    color: #0d631b;
}

.hw-badge-blue {
    color: #005db7;
}

.hw-badge-red {
    color: #ba1a1a;
}

.hw-badge-orange {
    color: #b45000;
}

.hw-card-featured {
    background: #0d631b;
    border-color: #0d631b;
}

.hw-card-featured:hover {
    background: #0b5517;
    border-color: #0b5517;
    box-shadow: 0 22px 42px -8px rgba(13, 99, 27, 0.32);
}

.hw-icon-box-featured {
    width: 64px !important;
    height: 64px !important;
    background: rgba(255, 255, 255, 0.18) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.35) !important;
    border-radius: 18px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow:
        0 8px 20px -4px rgba(0, 0, 0, 0.2),
        inset 0 1px 1px rgba(255, 255, 255, 0.4) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    margin-bottom: 6px;
    transition:
        transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1),
        background-color 0.25s ease,
        border-color 0.25s ease !important;
}

.hw-card-featured:hover .hw-icon-box-featured {
    background: rgba(255, 255, 255, 0.26) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    transform: scale(1.08) translateY(-2px) !important;
}

.text-white {
    color: #ffffff !important;
}

.text-white-sub {
    color: rgba(255, 255, 255, 0.9) !important;
}

.hw-badge-pill-light {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

/* Assembly Banner Card */
.assembly-banner-card {
    background: #e3f0f8;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    overflow: hidden;
    transition:
        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
        box-shadow 0.35s ease,
        border-color 0.25s ease;
}

.assembly-banner-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 22px 45px -10px rgba(0, 0, 0, 0.14);
    border-color: #0d631b;
}

.assembly-visual {
    position: relative;
    width: 100%;
    min-height: 280px;
    overflow: hidden;
    background: #d7e4ec;
    display: flex;
}

.assembly-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.assembly-banner-card:hover .assembly-photo-img {
    transform: scale(1.05);
}

.panel-live-tag {
    position: absolute;
    bottom: 16px;
    left: 16px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid #cbd5e1;
    padding: 6px 12px;
    border-radius: 9999px;
    font-size: 11.5px;
    font-weight: 700;
    color: #111d23;
    z-index: 2;
}

.pulse-dot-sm {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
}

.assembly-content {
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.assembly-tagline {
    font-size: 12px;
    font-weight: 700;
    color: #0d631b;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.assembly-title {
    font-size: 24px;
    font-weight: 700;
    color: #111d23;
    margin: 0 0 12px 0;
    line-height: 1.3;
}

.assembly-desc {
    font-size: 14.5px;
    color: #40493d;
    line-height: 1.6;
    margin: 0 0 24px 0;
}

.assembly-badges-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.assembly-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: #0d631b;
}

/* 4. Web App Features */
.app-features-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
    margin-bottom: 36px;
}

.feature-card {
    background: #e9f6fd;
    border: 1px solid #cbd5e1;
    border-radius: 16px;
    padding: 26px 22px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 18px;
    transition:
        transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.32s ease,
        border-color 0.25s ease,
        background-color 0.25s ease;
    cursor: default;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow:
        0 20px 36px -8px rgba(0, 93, 183, 0.16),
        0 6px 16px -4px rgba(0, 0, 0, 0.05);
    border-color: #005db7;
}

.feature-icon-box {
    width: 76px;
    height: 76px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0;
    margin-bottom: 2px;
    transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.feature-card:hover .feature-icon-box {
    transform: scale(1.12) rotate(-4deg);
}

.feature-title {
    font-size: 18.5px;
    font-weight: 700;
    color: #111d23;
    margin: 0;
    line-height: 1.35;
}

.feature-desc {
    font-size: 14px;
    line-height: 1.6;
    color: #40493d;
    margin: 0;
}

.feature-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid #cbd5e1;
    font-size: 13px;
    font-weight: 600;
}

.feature-footer svg {
    transition: transform 0.2s ease;
}

.feature-card:hover .feature-footer svg {
    transform: translateX(4px);
}

.text-green {
    color: #0d631b;
}

.text-blue {
    color: #005db7;
}

.text-orange {
    color: #b45000;
}

/* 5. SOP Steps */
.steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
    margin-bottom: 0;
}

.step-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 16px;
    padding: 26px 22px 22px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    transition:
        transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.32s ease,
        border-color 0.25s ease;
    cursor: default;
}

.step-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 34px -8px rgba(13, 99, 27, 0.15);
    border-color: #0d631b;
}

.step-icon-wrap {
    width: 76px;
    height: 76px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 2px;
    padding: 0;
    transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.step-card:hover .step-icon-wrap {
    transform: scale(1.12) rotate(4deg);
}

.step-icon-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.1));
}

.step-title {
    font-size: 18px;
    font-weight: 700;
    color: #111d23;
    margin: 0;
    line-height: 1.35;
}

.step-desc {
    font-size: 14px;
    line-height: 1.6;
    color: #40493d;
    margin: 0;
}

.panoramic-banner {
    width: 100%;
    height: 250px;
    border-radius: 14px;
    border: 1px solid #cbd5e1;
    overflow: hidden;
    position: relative;
    background: #111d23;
    transition:
        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
        box-shadow 0.35s ease;
}

.panoramic-banner:hover {
    transform: translateY(-4px);
    box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.25);
}

.panoramic-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 60%;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.panoramic-banner:hover .panoramic-img {
    transform: scale(1.05);
}

.panoramic-overlay {
    position: absolute;
    inset: 0;
    padding: 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(
        90deg,
        rgba(17, 29, 35, 0.85) 0%,
        rgba(17, 29, 35, 0.4) 60%,
        transparent 100%
    );
    z-index: 2;
}

.panoramic-tagline {
    font-size: 12px;
    font-weight: 700;
    color: #a3f69c;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
}

.panoramic-title {
    font-size: 24px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    max-width: 480px;
    line-height: 1.35;
}

/* 6. CTA Banner */
.cta-section {
    padding: 60px 24px;
}

.cta-banner-card {
    background: #0d631b;
    border: 1px solid #0d631b;
    border-radius: 16px;
    padding: 38px 48px;
    display: flex;
    align-items: center;
    gap: 36px;
    position: relative;
    overflow: hidden;
    transition:
        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
        box-shadow 0.35s ease;
}

.cta-banner-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 24px 48px -12px rgba(13, 99, 27, 0.35);
}

.cta-icon-wrap {
    width: 110px;
    height: 110px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.cta-banner-card:hover .cta-icon-wrap {
    transform: scale(1.1) rotate(-3deg);
}

.cta-3d-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.28));
}

.cta-content {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.cta-tagline {
    font-size: 12px;
    font-weight: 700;
    color: #a3f69c;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.cta-heading {
    font-size: 28px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 8px 0;
    line-height: 1.25;
}

.cta-sub {
    font-size: 14.5px;
    color: rgba(255, 255, 255, 0.92);
    line-height: 1.55;
    margin: 0;
    max-width: 620px;
}

.cta-action {
    flex-shrink: 0;
}

.btn-cta-white {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 28px;
    background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
    color: #0d631b !important;
    font-size: 14.5px;
    font-weight: 800;
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    cursor: pointer;
    white-space: nowrap;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 1),
        0 4px 14px rgba(0, 0, 0, 0.12) !important;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    -webkit-user-select: none;
    user-select: none;
}

.btn-cta-white:hover {
    background: #ffffff;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 1),
        0 6px 20px rgba(0, 0, 0, 0.18) !important;
    transform: translateY(-2px);
}

.btn-cta-white:active {
    transform: scale(0.98);
}

/* 7. Footer (Hanjeli Green Theme with CoE STAS-RG Layout) */
.landing-footer {
    background: #e9f6fd;
    color: #40493d;
    border-top: 1px solid #cbd5e1;
    padding: 56px 24px 28px;
    transition: all 0.25s ease;
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

/* Col 4: Lokasi Kampus Map */
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

.footer-meta-tags {
    display: flex;
    align-items: center;
    gap: 10px;
}

.footer-v-tag {
    font-size: 10px;
    font-weight: 700;
    color: #0d631b;
    background: #e8f5e9;
    border: 1px solid #c8e6c9;
    padding: 2px 7px;
    border-radius: 4px;
    letter-spacing: 0.3px;
}

.footer-copy {
    color: #707a6c;
}

.footer-tagline {
    color: #707a6c;
    font-weight: 600;
}

/* Color Helpers */
.icon-green {
    background: rgba(13, 99, 27, 0.1);
}

.icon-blue {
    background: rgba(0, 93, 183, 0.1);
}

.icon-cyan {
    background: rgba(2, 136, 209, 0.1);
}

.icon-red {
    background: rgba(186, 26, 26, 0.1);
}

.icon-orange {
    background: rgba(180, 80, 0, 0.1);
}

.icon-white {
    background: rgba(255, 255, 255, 0.2);
}

/* =========================================================
   DARK THEME STYLING FOR LANDING VIEW
   ========================================================= */
.dark-landing {
    background-color: #07130d !important;
    color: #f1f5f9 !important;
}

.dark-landing .landing-navbar {
    background: rgba(11, 25, 17, 0.94) !important;
    border-bottom-color: #1e3a2b !important;
}

.dark-landing .brand-icon-box,

.dark-landing .brand-title,
.dark-landing .hero-title,
.dark-landing .section-heading,
.dark-landing .hw-title,
.dark-landing .feature-title,
.dark-landing .step-title,
.dark-landing .footer-brand-title,
.dark-landing .footer-col-title,
.dark-landing .mini-card-title,
.dark-landing .assembly-title,
.dark-landing .lang-option {
    color: #f1f5f9 !important;
}

.dark-landing .brand-sub,
.dark-landing .hero-desc,
.dark-landing .mini-card-sub,
.dark-landing .hw-desc,
.dark-landing .feature-desc,
.dark-landing .step-desc,
.dark-landing .footer-desc,
.dark-landing .footer-address,
.dark-landing .footer-link-item,
.dark-landing .footer-bottom-row,
.dark-landing .section-subtext,
.dark-landing .assembly-desc,
.dark-landing .hw-spec-tag,
.dark-landing .footer-copy {
    color: #94a3b8 !important;
}

.dark-landing .nav-links a {
    color: #94a3b8;
}

.dark-landing .nav-links a:hover {
    color: #4ade80;
    background: rgba(74, 222, 128, 0.1);
}

.dark-landing .nav-links a.active {
    background: #0d631b;
    color: #ffffff;
}

.dark-landing .header-action-btn {
    background: transparent !important;
    border: none !important;
    color: #94a3b8 !important;
}

.dark-landing .header-action-btn:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #f8fafc !important;
}

.dark-landing .lang-dropdown-menu {
    background: #13271c;
    border-color: #1e3a2b;
}

.dark-landing .lang-option:hover {
    background: #1a3326;
    color: #4ade80;
}

.dark-landing .lang-option.active {
    background: #1e3a2b;
    color: #4ade80;
}

.dark-landing .hero-release-pill {
    background: #152c20;
    border-color: #1e3a2b;
}

.dark-landing .pill-version {
    color: #4ade80;
}

.dark-landing .pill-label {
    color: #86efac;
}

.dark-landing .pill-sep {
    color: #1e4e61;
}

.dark-landing .footer-v-tag {
    color: #4ade80;
    background: rgba(74, 222, 128, 0.15);
    border-color: rgba(74, 222, 128, 0.3);
}

.dark-landing .hero-tag-pill {
    background: #152c20;
    border-color: #1e3a2b;
}

.dark-landing .tag-text {
    color: #4ade80;
}

.dark-landing .btn-hero-secondary {
    background: #13271c;
    color: #4ade80;
    border-color: #1e3a2b;
}

.dark-landing .btn-hero-secondary:hover {
    background: #183324;
    border-color: #4ade80;
}

.dark-landing .mini-card,
.dark-landing .feature-card,
.dark-landing .step-card,
.dark-landing .hw-card,
.dark-landing .assembly-banner-card {
    background: #13271c !important;
    border-color: #1e3a2b !important;
}

.dark-landing .hw-card-footer,
.dark-landing .feature-footer,
.dark-landing .footer-bottom-row {
    border-color: #1e3a2b !important;
}


.dark-landing .feature-icon-box,
.dark-landing .hw-icon-box,
.dark-landing .mini-card-icon,
.dark-landing .step-icon-wrap {
    background: transparent !important;
    border: none !important;
}

.dark-landing .hw-icon-box-featured {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.28) !important;
}


.dark-landing .bg-soft-blue {
    background-color: #0e1f16 !important;
    border-color: #1e3a2b !important;
}

.dark-landing .assembly-visual {
    background: #0e1f16 !important;
}

.dark-landing .panel-live-tag {
    background: rgba(19, 39, 28, 0.95) !important;
    border-color: #1e3a2b !important;
    color: #f1f5f9 !important;
}

.dark-landing .landing-footer {
    background: #0b1911 !important;
    color: #94a3b8 !important;
    border-top-color: #1e3a2b !important;
}

.dark-landing .footer-brand-logo-box {
    background: transparent !important;
    border: none !important;
}

.dark-landing .footer-brand-title {
    color: #ffffff !important;
}

.dark-landing .footer-brand-badge {
    color: #4ade80 !important;
}

.dark-landing .footer-desc {
    color: #94a3b8 !important;
}

.dark-landing .footer-social-btn {
    background: #13271c !important;
    border-color: #1e3a2b !important;
    color: #94a3b8 !important;
}

.dark-landing .footer-social-btn:hover {
    background: #0d631b !important;
    border-color: #4ade80 !important;
    color: #ffffff !important;
}

.dark-landing .footer-heading {
    color: #ffffff !important;
}

.dark-landing .footer-green-bar {
    background: #4ade80 !important;
}

.dark-landing .footer-nav-list a {
    color: #94a3b8 !important;
}

.dark-landing .footer-nav-list a:hover {
    color: #4ade80 !important;
}

.dark-landing .footer-contact-icon {
    background: transparent !important;
    border: none !important;
    color: #4ade80 !important;
}

.dark-landing .contact-label {
    color: #64748b !important;
}

.dark-landing .contact-value {
    color: #e2e8f0 !important;
}

.dark-landing a.contact-value:hover {
    color: #4ade80 !important;
}

.dark-landing .footer-map-iframe-box {
    border-color: #1e3a2b !important;
    background: #13271c !important;
}

.dark-landing .footer-map-address {
    color: #94a3b8 !important;
}

.dark-landing .footer-bottom-row {
    border-top-color: #1e3a2b !important;
    color: #64748b !important;
}

.dark-landing .footer-copy,
.dark-landing .footer-tagline {
    color: #64748b !important;
}



/* Responsive Media Queries */
@media (max-width: 1024px) {
    .hero-section {
        padding: 40px 20px 60px;
    }

    .hero-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .hardware-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .app-features-grid,
    .steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .assembly-banner-card {
        grid-template-columns: 1fr;
    }

    .footer-top-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 900px) {
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

@media (max-width: 768px) {
    .hero-title {
        font-size: 28px;
    }

    .hero-mini-cards-row {
        grid-template-columns: 1fr;
    }

    .hardware-grid,
    .app-features-grid,
    .steps-grid {
        grid-template-columns: 1fr;
    }

    .cta-banner-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 32px 24px;
        gap: 20px;
    }

    .cta-icon-wrap {
        width: 80px;
        height: 80px;
    }

    .footer-top-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .landing-navbar {
        padding: 0 16px;
    }

    .hero-section,
    .hardware-section,
    .app-features-section,
    .sop-section,
    .cta-banner-section,
    .landing-footer {
        padding-left: 16px;
        padding-right: 16px;
    }

    .landing-nav-container {
        padding: 10px 0;
    }

    .brand-icon-box {
        width: auto;
        height: auto;
        background: transparent;
    }

    .brand-logo-img {
        width: 30px;
        height: 30px;
    }

    .brand-title {
        font-size: 13.5px;
    }

    .brand-sub {
        font-size: 10.5px;
    }

    .header-action-btn {
        width: 34px;
        height: 34px;
    }

    .mobile-drawer-inner {
        padding: 14px 14px 20px 14px;
    }

    .mobile-drawer-links a {
        padding: 10px 12px;
        font-size: 13.5px;
    }

    .btn-mobile-login {
        font-size: 13.5px;
        padding: 11px 16px;
    }
}
</style>
