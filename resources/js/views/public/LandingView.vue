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
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        :aria-expanded="isMobileMenuOpen"
                        :title="isMobileMenuOpen ? 'Tutup Menu' : 'Buka Menu'"
                    >
                        <svg
                            v-if="!isMobileMenuOpen"
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
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
                        >
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

                <!-- Hero Right Column: 3D Illustration with Interactive Parallax -->
                <div class="hero-right">
                    <div 
                        class="hero-illustration-wrap"
                        ref="hero3dRef"
                        @mousemove="handleHeroMouseMove"
                        @mouseleave="handleHeroMouseLeave"
                        @mouseenter="handleHeroMouseEnter"
                        :style="hero3dStyle"
                    >
                        <!-- Ambient 3D Depth Glow -->
                        <div class="hero-3d-ambient-glow"></div>
                        
                        <!-- Main 3D Hero Graphic -->
                        <img
                            src="/assets/icons/hero/hero.png"
                            alt="Smart Dryer Greenhouse Hanjeli"
                            class="hero-main-img"
                            :style="heroImg3dStyle"
                        />

                        <!-- Dynamic Specular Glare / Lighting Reflection -->
                        <div 
                            class="hero-3d-glare"
                            :style="heroGlareStyle"
                        ></div>
                    </div>
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

                    <!-- Card 5: LDR Sensor -->
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
                                        ? "Kesiapan Sensor Cahaya (LDR)"
                                        : "Light Sensor Ready (LDR)"
                                }}
                            </h3>
                            <p class="hw-desc">
                                {{
                                    currentLang === "id"
                                        ? "Mendukung integrasi pemantauan intensitas cahaya lingkungan ruang pengering untuk audit mikroklimat ruang pengeringan terpadu di desa wisata."
                                        : "Supports ambient light intensity monitoring integration for integrated microclimate environmental audits in the tourism village."
                                }}
                            </p>
                        </div>
                        <div class="hw-card-footer">
                            <span class="hw-spec-tag">{{
                                currentLang === "id"
                                    ? "Kalibrasi Lux Terbuka"
                                    : "Open Lux Calibration"
                            }}</span>
                            <span class="hw-badge-orange">{{
                                currentLang === "id"
                                    ? "Siap Pasang"
                                    : "Plug & Play"
                            }}</span>
                        </div>
                    </div>

                    <!-- Card 6: Energy Efficiency -->
                    <div class="hw-card hw-card-featured">
                        <div class="hw-card-top">
                            <div class="hw-icon-box">
                                <img
                                    src="/assets/icons/hardware/Efisiensi.png"
                                    alt="Efisiensi Energi"
                                    class="icon-3d-img"
                                />
                            </div>
                            <h3 class="hw-title text-white">
                                {{
                                    currentLang === "id"
                                        ? "Efisiensi Energi Berkelanjutan"
                                        : "Sustainable Energy Efficiency"
                                }}
                            </h3>
                            <p class="hw-desc text-white-sub">
                                {{
                                    currentLang === "id"
                                        ? "Sistem memutus arus ketika suhu ideal tercapai, menghemat konsumsi listrik operasional kelompok tani secara signifikan dibanding oven konvensional."
                                        : "Automatically cuts power when ideal temperature is achieved, drastically reducing farmer electricity expenses compared to conventional ovens."
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
                                        ? "Hemat daya hingga 40% per batch"
                                        : "Up to 40% energy savings per batch"
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
import { authService } from "../../services/authService";
import { telemetryService } from "../../services/telemetryService";
import { batchService } from "../../services/batchService";
import { socketService } from "../../services/socketService";
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

function handleClickOutside(e) {
    if (langDropdownRef.value && !langDropdownRef.value.contains(e.target)) {
        isLangOpen.value = false;
    }
    if (navbarRef.value && !navbarRef.value.contains(e.target)) {
        isMobileMenuOpen.value = false;
    }
}

// 3D Hero Tilt & Parallax Interactive Logic
const hero3dRef = ref(null);
const heroTiltX = ref(0);
const heroTiltY = ref(0);
const isHeroHovered = ref(false);
const glareX = ref(50);
const glareY = ref(50);
const glareOpacity = ref(0);

function handleHeroMouseEnter() {
    isHeroHovered.value = true;
    glareOpacity.value = 0.55;
}

function handleHeroMouseMove(e) {
    if (!hero3dRef.value) return;
    const rect = hero3dRef.value.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    // Smooth 3D tilt angles up to ±16 degrees
    const rotateY = ((x - centerX) / centerX) * 16;
    const rotateX = -((y - centerY) / centerY) * 16;
    
    heroTiltX.value = rotateX;
    heroTiltY.value = rotateY;
    
    glareX.value = (x / rect.width) * 100;
    glareY.value = (y / rect.height) * 100;
}

function handleHeroMouseLeave() {
    isHeroHovered.value = false;
    heroTiltX.value = 0;
    heroTiltY.value = 0;
    glareOpacity.value = 0;
}

const hero3dStyle = computed(() => {
    if (!isHeroHovered.value) {
        return {
            transform: 'perspective(1100px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)',
            transition: 'transform 0.65s cubic-bezier(0.16, 1, 0.3, 1)',
        };
    }
    return {
        transform: `perspective(1100px) rotateX(${heroTiltX.value.toFixed(2)}deg) rotateY(${heroTiltY.value.toFixed(2)}deg) scale3d(1.06, 1.06, 1.06)`,
        transition: 'transform 0.08s ease-out',
    };
});

const heroImg3dStyle = computed(() => {
    if (!isHeroHovered.value) {
        return {
            transform: 'translateZ(0px)',
            filter: 'drop-shadow(0 20px 38px rgba(13, 99, 27, 0.22)) drop-shadow(0 6px 14px rgba(0, 0, 0, 0.1))',
            transition: 'transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), filter 0.65s ease',
        };
    }
    const shadowX = (-heroTiltY.value * 2.2).toFixed(1);
    const shadowY = (heroTiltX.value * 2.2 + 28).toFixed(1);
    return {
        transform: 'translateZ(36px)',
        filter: `drop-shadow(${shadowX}px ${shadowY}px 48px rgba(13, 99, 27, 0.35)) drop-shadow(${Number(shadowX) * 0.5}px ${Number(shadowY) * 0.5}px 18px rgba(0, 0, 0, 0.18))`,
        transition: 'transform 0.08s ease-out, filter 0.08s ease-out',
    };
});

const heroGlareStyle = computed(() => ({
    background: `radial-gradient(circle at ${glareX.value}% ${glareY.value}%, rgba(255, 255, 255, 0.45) 0%, rgba(203, 255, 194, 0.2) 30%, transparent 65%)`,
    opacity: glareOpacity.value,
    transition: isHeroHovered.value ? 'opacity 0.2s ease' : 'opacity 0.5s ease',
}));

// Real Database & IoT States
const telemetry = ref({
    tempInternal: 39.4,
    humidityInternal: 42.0,
    tempExternal: 28.5,
    grainMoisture: 14.2,
    hasData: false,
});

const actuators = ref({
    exhaustFanStatus: true,
    exhaustFanSpeed: 100,
    circFanStatus: true,
    circFanSpeed: 100,
    auxHeaterStatus: false,
});

const activeBatch = ref(null);
const latestBatch = ref(null);
const telemetryHistory = ref([]);
let pollInterval = null;

const isActiveCycle = computed(() => {
    return !!(
        activeBatch.value &&
        (activeBatch.value.status === "ACTIVE" ||
            activeBatch.value.status === "RUNNING" ||
            activeBatch.value.status === "PAUSED")
    );
});

const currentTempDisplay = computed(() => {
    if (
        telemetry.value.hasData &&
        typeof telemetry.value.tempInternal === "number"
    ) {
        return telemetry.value.tempInternal.toFixed(1);
    }
    return "39.4";
});

const currentHumidityDisplay = computed(() => {
    if (
        telemetry.value.hasData &&
        typeof telemetry.value.humidityInternal === "number"
    ) {
        return Math.round(telemetry.value.humidityInternal);
    }
    return "42";
});

const tempStatusClass = computed(() => {
    const t = parseFloat(currentTempDisplay.value);
    if (t >= 38.0 && t <= 40.5) return "status-good";
    if (t > 40.5) return "status-warn";
    return "status-info";
});

const tempStatusText = computed(() => {
    const t = parseFloat(currentTempDisplay.value);
    if (currentLang.value === "id") {
        if (t >= 38.0 && t <= 40.5) return "Optimal (38-40°C)";
        if (t > 40.5) return "Suhu Tinggi (>40°C)";
        return "Meningkat ke Target";
    } else {
        if (t >= 38.0 && t <= 40.5) return "Optimal (38-40°C)";
        if (t > 40.5) return "High Temp (>40°C)";
        return "Rising to Target";
    }
});

const humidityStatusClass = computed(() => {
    const h = parseFloat(currentHumidityDisplay.value);
    if (h <= 45) return "status-good";
    return "status-trend";
});

const humidityStatusText = computed(() => {
    const h = parseFloat(currentHumidityDisplay.value);
    if (currentLang.value === "id") {
        if (h <= 14) return "Kadar Kering Standar";
        if (h <= 45) return "Menurun stabil";
        return "Proses Penguapan";
    } else {
        if (h <= 14) return "Dry Grain Standard";
        if (h <= 45) return "Decreasing steadily";
        return "Evaporation Phase";
    }
});

const isBlowerActive = computed(() => {
    return !!(
        actuators.value.circFanStatus || actuators.value.exhaustFanStatus
    );
});

const blowerSpeedDisplay = computed(() => {
    return (
        actuators.value.circFanSpeed || actuators.value.exhaustFanSpeed || 100
    );
});

const displayBatchCrop = computed(() => {
    return (
        activeBatch.value?.cropVariety ||
        latestBatch.value?.cropVariety ||
        "Hanjeli Ketan Sukabumi"
    );
});

const displayBatchCode = computed(() => {
    return (
        activeBatch.value?.batchCode ||
        activeBatch.value?.id ||
        latestBatch.value?.batchCode ||
        latestBatch.value?.id ||
        "HJ-2026-005"
    );
});

const displayBatchWeight = computed(() => {
    const w =
        activeBatch.value?.initialWeightKg ||
        activeBatch.value?.currentWeightKg ||
        latestBatch.value?.initialWeightKg ||
        25;
    return typeof w === "number" ? w.toFixed(0) : w;
});

// Dynamic SVG Sparkline generator based on real database telemetry history
const sparklinePoints = computed(() => {
    if (!telemetryHistory.value || telemetryHistory.value.length < 2) {
        return [
            { x: 0, y: 45 },
            { x: 50, y: 34 },
            { x: 100, y: 38 },
            { x: 160, y: 25 },
            { x: 220, y: 22 },
            { x: 280, y: 27 },
            { x: 330, y: 23 },
            { x: 380, y: 22 },
        ];
    }

    const values = telemetryHistory.value.map((t) =>
        typeof t.tempInternal === "number" ? t.tempInternal : 38,
    );
    const minVal = Math.min(...values, 30);
    const maxVal = Math.max(...values, 45);
    const range = maxVal - minVal || 1;

    return values.map((val, idx) => {
        const x = (idx / (values.length - 1)) * 380;
        const normalized = (val - minVal) / range;
        const y = 50 - normalized * 38;
        return { x: Math.round(x * 10) / 10, y: Math.round(y * 10) / 10 };
    });
});

const sparklineLinePath = computed(() => {
    const pts = sparklinePoints.value;
    if (!pts.length) return "M 0 45 L 380 22";
    return `M ${pts.map((p) => `${p.x} ${p.y}`).join(" L ")}`;
});

const sparklineAreaPath = computed(() => {
    return `${sparklineLinePath.value} L 380 60 L 0 60 Z`;
});

async function fetchLiveData() {
    try {
        const res = await telemetryService.getCurrent();
        if (res?.telemetry) {
            telemetry.value = {
                ...telemetry.value,
                ...res.telemetry,
                hasData: res.telemetry.hasData !== false,
            };
        }
        if (res?.actuators) {
            actuators.value = { ...actuators.value, ...res.actuators };
        }
    } catch (err) {
        console.warn("Landing telemetry fetch:", err);
    }

    try {
        const batchRes = await batchService.getActiveBatch();
        const active =
            batchRes?.activeBatch || (batchRes?.id ? batchRes : null);
        if (active && active.id) {
            activeBatch.value = active;
        } else {
            activeBatch.value = null;
            const allBatches = await batchService.getAll();
            const list = Array.isArray(allBatches)
                ? allBatches
                : allBatches?.data || [];
            if (list.length > 0) {
                latestBatch.value = list[0];
            }
        }
    } catch (err) {
        console.warn("Landing active batch fetch:", err);
    }

    try {
        const histRes = await telemetryService.getHistory(6);
        const list = Array.isArray(histRes) ? histRes : histRes?.data || [];
        if (list.length >= 2) {
            telemetryHistory.value = list.slice(-15);
        }
    } catch (err) {
        console.warn("Landing telemetry history fetch:", err);
    }
}

function goToApp() {
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

    // 1. Fetch initial live telemetry & batch from MySQL database
    fetchLiveData();

    // 2. Real-time WebSocket connection
    socketService.connect();
    socketService.on("telemetry_new", (data) => {
        if (data) {
            telemetry.value = {
                ...telemetry.value,
                ...data,
                hasData: true,
            };
        }
    });
    socketService.on("batch_update", () => {
        fetchLiveData();
    });

    // 3. Initialize Framer Motion animations
    nextTick(() => {
        initFramerMotion();
    });

    // 4. Fallback polling every 4 seconds to guarantee fresh data
    pollInterval = setInterval(fetchLiveData, 4000);
});

onUnmounted(() => {
    const scrollContainer = getScrollableContainer();
    if (scrollContainer && scrollContainer !== window) {
        scrollContainer.removeEventListener("scroll", handleScroll);
    }
    window.removeEventListener("scroll", handleScroll);
    document.removeEventListener("click", handleClickOutside);
    if (pollInterval) clearInterval(pollInterval);
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
    border-bottom: 1px solid #cbd5e1;
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
    color: #1e293b;
    text-decoration: none;
    background: transparent;
    transition: all 0.2s ease;
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

.mobile-drawer-links a:hover,
.mobile-drawer-links a.active {
    background: rgba(13, 99, 27, 0.08);
    color: #0d631b;
}

.mobile-drawer-links a.active .link-icon-chip {
    background: #0d631b;
    color: #ffffff;
}

.mobile-drawer-cta {
    padding-top: 10px;
    border-top: 1px solid rgba(203, 213, 225, 0.8);
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
    padding: 60px 24px 80px;
    overflow: hidden;
    scroll-margin-top: 72px;
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
    margin-bottom: 18px;
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
    margin: 0 0 18px 0;
    letter-spacing: -0.5px;
}

.hero-desc {
    font-size: 16.5px;
    line-height: 1.6;
    color: #40493d;
    margin: 0 0 32px 0;
    max-width: 560px;
}

.hero-cta-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
    margin-bottom: 40px;
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

.hero-illustration-wrap {
    width: 100%;
    max-width: 540px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transform-style: preserve-3d;
    cursor: grab;
    user-select: none;
    padding: 18px;
    box-sizing: border-box;
    will-change: transform;
}

.hero-illustration-wrap:active {
    cursor: grabbing;
}

.hero-3d-ambient-glow {
    position: absolute;
    width: 360px;
    height: 360px;
    background: radial-gradient(circle, rgba(46, 125, 50, 0.32) 0%, rgba(203, 255, 194, 0.18) 45%, transparent 70%);
    border-radius: 50%;
    filter: blur(42px);
    pointer-events: none;
    z-index: 1;
    transform: translateZ(-25px);
    animation: heroGlowPulse 5s ease-in-out infinite alternate;
}

@keyframes heroGlowPulse {
    0% { transform: scale(0.92) translateZ(-25px); opacity: 0.6; }
    100% { transform: scale(1.18) translateZ(-25px); opacity: 0.95; }
}

.hero-main-img {
    width: 100%;
    max-height: 510px;
    object-fit: contain;
    display: block;
    position: relative;
    z-index: 2;
    transform-style: preserve-3d;
    pointer-events: none;
    will-change: transform, filter;
}

.hero-3d-glare {
    position: absolute;
    inset: 10px;
    border-radius: 30px;
    pointer-events: none;
    z-index: 3;
    mix-blend-mode: overlay;
    transform: translateZ(40px);
}

.hero-floating-badge {
    position: absolute;
    z-index: 4;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    border: 1.5px solid rgba(13, 99, 27, 0.2);
    border-radius: 100px;
    box-shadow: 0 10px 25px rgba(13, 99, 27, 0.15), 0 2px 6px rgba(0, 0, 0, 0.05);
    font-size: 11.5px;
    font-weight: 700;
    color: #0D631B;
    pointer-events: none;
    transform-style: preserve-3d;
    will-change: transform;
}

.badge-iot {
    top: 24px;
    left: -10px;
    animation: floatBadge1 4s ease-in-out infinite alternate;
}

.badge-greenhouse {
    bottom: 24px;
    right: -10px;
    animation: floatBadge2 4.5s ease-in-out infinite alternate;
}

.badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #16A34A;
}

.pulse-green {
    box-shadow: 0 0 0 rgba(22, 163, 74, 0.6);
    animation: pulseDot 2s infinite;
}

@keyframes pulseDot {
    0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(22, 163, 74, 0); }
    100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}

@keyframes floatBadge1 {
    0% { transform: translateY(0px); }
    100% { transform: translateY(-7px); }
}

@keyframes floatBadge2 {
    0% { transform: translateY(0px); }
    100% { transform: translateY(7px); }
}

@media (max-width: 768px) {
    .hero-floating-badge {
        display: none;
    }
    .hero-3d-ambient-glow {
        width: 250px;
        height: 250px;
    }
}

.live-preview-card {
    width: 100%;
    max-width: 440px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    transition:
        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
        box-shadow 0.35s ease,
        border-color 0.3s ease;
}

.live-preview-card:hover {
    transform: translateY(-5px);
    box-shadow:
        0 24px 48px -12px rgba(13, 99, 27, 0.18),
        0 8px 24px -6px rgba(0, 0, 0, 0.06);
    border-color: rgba(13, 99, 27, 0.35);
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.preview-room-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.live-pulse-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

.pulse-idle {
    background: #005db7 !important;
    box-shadow: 0 0 0 3px rgba(0, 93, 183, 0.2) !important;
}

.preview-room-title {
    font-size: 14px;
    font-weight: 700;
    color: #111d23;
}

.badge-status-running {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: #e8f5e9;
    color: #0d631b;
    border-radius: 9999px;
    border: 1px solid #c8e6c9;
}

.badge-status-idle {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: #e3f0f8;
    color: #005db7;
    border-radius: 9999px;
    border: 1px solid #cbd5e1;
}

.preview-metrics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.metric-box {
    background: #e9f6fd;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    transition:
        transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.28s ease,
        border-color 0.2s ease;
    cursor: default;
}

.metric-box:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 10px 22px -4px rgba(0, 93, 183, 0.16);
    border-color: #005db7;
}

.metric-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.metric-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #40493d;
}

.metric-number-row {
    display: flex;
    align-items: baseline;
    gap: 3px;
}

.metric-val {
    font-size: 34px;
    font-weight: 800;
    color: #111d23;
    line-height: 1;
}

.metric-unit {
    font-size: 16px;
    font-weight: 700;
    color: #111d23;
}

.metric-badge-status {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
}

.status-good {
    color: #0d631b;
}

.status-trend {
    color: #005db7;
}

.status-warn {
    color: #ba1a1a;
}

.status-info {
    color: #005db7;
}

.preview-chart-box {
    background: #e3f0f8;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: all 0.2s ease;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #111d23;
}

.chart-sub {
    font-size: 11px;
    font-weight: 500;
    color: #40493d;
}

.chart-svg-container {
    width: 100%;
    height: 60px;
}

.sparkline-svg {
    width: 100%;
    height: 100%;
    overflow: visible;
}

.preview-actuator-row {
    background: #ddeaf2;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
}

.actuator-left {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: #111d23;
}

.actuator-status-on {
    font-size: 12px;
    font-weight: 700;
    color: #0d631b;
}

.actuator-status-off {
    font-size: 12px;
    font-weight: 700;
    color: #707a6c;
}

.preview-batch-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 4px;
}

.batch-avatar-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    background: #e3f0f8;
    border: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.batch-seed-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.batch-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.batch-crop-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #111d23;
}

.batch-code-weight {
    font-size: 11.5px;
    font-weight: 500;
    color: #40493d;
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
.dark-landing .batch-avatar-icon {
    background: #152c20 !important;
    border-color: #1e3a2b !important;
}

.dark-landing .brand-title,
.dark-landing .hero-title,
.dark-landing .section-heading,
.dark-landing .hw-title,
.dark-landing .feature-title,
.dark-landing .step-title,
.dark-landing .footer-brand-title,
.dark-landing .footer-col-title,
.dark-landing .preview-room-title,
.dark-landing .chart-title,
.dark-landing .batch-crop-name,
.dark-landing .metric-val,
.dark-landing .metric-unit,
.dark-landing .mini-card-title,
.dark-landing .assembly-title,
.dark-landing .actuator-left,
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
.dark-landing .metric-label,
.dark-landing .chart-sub,
.dark-landing .batch-code-weight,
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
.dark-landing .live-preview-card,
.dark-landing .assembly-banner-card {
    background: #13271c !important;
    border-color: #1e3a2b !important;
}

.dark-landing .preview-header,
.dark-landing .hw-card-footer,
.dark-landing .feature-footer,
.dark-landing .footer-bottom-row {
    border-color: #1e3a2b !important;
}

.dark-landing .metric-box,
.dark-landing .preview-actuator-row {
    background: #183324 !important;
    border-color: #1e3a2b !important;
}

.dark-landing .feature-icon-box,
.dark-landing .hw-icon-box,
.dark-landing .mini-card-icon,
.dark-landing .step-icon-wrap {
    background: transparent !important;
    border: none !important;
}

.dark-landing .preview-chart-box {
    background: #152c20 !important;
    border-color: #1e3a2b !important;
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

.dark-landing .badge-status-running {
    background: rgba(13, 99, 27, 0.35);
    color: #4ade80;
    border-color: rgba(74, 222, 128, 0.3);
}

.dark-landing .badge-status-idle {
    background: rgba(0, 93, 183, 0.25);
    color: #60a5fa;
    border-color: rgba(96, 165, 250, 0.3);
}

/* Responsive Media Queries */
@media (max-width: 1024px) {
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

@media (max-width: 860px) {
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
