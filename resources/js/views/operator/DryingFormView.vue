<template>
    <div class="drying-form-page">
        <Transition name="fade-drying" mode="out-in">
            <DryingFormSkeleton v-if="isInitialLoading" />
            <div v-else class="drying-form-loaded-content">
                <div class="drying-form-main-content">
                    <!-- Header Area -->
                    <div class="form-header-card">
                        <h1 class="page-title">
                            Mulai Pengeringan Biji Hanjeli
                        </h1>
                        <p class="page-subtitle">
                            Isi data gabah dan pilih cara pengeringan sebelum
                            menyalakan alat.
                        </p>
                    </div>

                    <!-- Peringatan jika ada sesi yang sedang berjalan -->
                    <div v-if="hasActiveBatch" class="active-batch-lock-banner">
                        <div class="ab-lock-icon">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#D97706"
                                stroke-width="2.2"
                            >
                                <rect
                                    x="3"
                                    y="11"
                                    width="18"
                                    height="11"
                                    rx="2"
                                    ry="2"
                                ></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div class="ab-lock-content">
                            <strong>Sesi Pengeringan Sedang Berjalan</strong>
                            <p>
                                Saat ini alat sedang mengeringkan
                                <strong>{{
                                    activeBatchData?.cropVariety
                                }}</strong>
                                (Kode: {{ activeBatchData?.batchCode }}).
                                Selesaikan sesi yang berjalan terlebih dahulu
                                sebelum memulai sesi baru.
                            </p>
                            <div class="ab-lock-btn-row">
                                <button
                                    type="button"
                                    class="btn-lock-view"
                                    @click="router.push('/drying/active')"
                                >
                                    Pantau Sesi Berjalan &rarr;
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Peringatan jika mikrokontroler ESP32 belum terhubung -->
                    <div
                        v-else-if="!isHardwareConnected"
                        class="hw-warning-banner"
                    >
                        <div class="hw-warn-icon">
                            <svg
                                width="22"
                                height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#B45000"
                                stroke-width="2.2"
                            >
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="hw-warn-text">
                            <strong>Sambungan Alat Belum Terdeteksi</strong>
                            <p>
                                Pastikan alat pengering ESP32 di ruang
                                greenhouse sudah menyala dan terhubung ke Wi-Fi.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="btn-warn-setup"
                            @click="openHardwareSetup"
                        >
                            Cek Sambungan
                        </button>
                    </div>

                    <!-- Layout Utama Form & Ringkasan -->
                    <div class="form-grid-layout">
                        <!-- Kolom Kiri: Formulir Input -->
                        <div class="form-inputs-col">
                            <!-- Pilihan Cepat / Preset -->
                            <div class="preset-box">
                                <span class="preset-title"
                                    >Pilihan Pengeringan:</span
                                >
                                <div class="preset-btns">
                                    <button
                                        type="button"
                                        class="btn-preset"
                                        :class="{
                                            active:
                                                formData.timingMode === 'AUTO',
                                        }"
                                        @click="setTimingMode('AUTO')"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-preset-icon">
                                            <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                                        </svg>
                                        <span
                                            >Standar Otomatis (Target 12%
                                            SNI)</span
                                        >
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-preset"
                                        :class="{
                                            active:
                                                formData.timingMode === 'MANUAL',
                                        }"
                                        @click="setTimingMode('MANUAL')"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-preset-icon">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span>Kustom / Manual (Atur Jam & Waktu)</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Bagian 1: Data Gabah -->
                            <div class="form-card-section">
                                <h3 class="section-heading">
                                    1. Data Gabah Hanjeli
                                </h3>

                                <div class="form-two-cols">
                                    <div class="form-field">
                                        <label class="form-label"
                                            >Jenis / Varietas Gabah</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formData.cropVariety"
                                            placeholder="Contoh: Hanjeli Ketan, Hanjeli Batu"
                                            class="clean-input"
                                            required
                                        />
                                    </div>

                                    <div class="form-field">
                                        <label class="form-label"
                                            >Nama Petani / Operator</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formData.operatorName"
                                            placeholder="Nama operator yang bertugas"
                                            class="clean-input"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="form-two-cols"
                                    style="margin-top: 16px"
                                >
                                    <div class="form-field">
                                        <label class="form-label"
                                            >Berat Gabah Basah Masuk (kg)</label
                                        >
                                        <div class="input-unit-wrap">
                                            <input
                                                type="number"
                                                step="0.5"
                                                min="1"
                                                v-model.number="
                                                    formData.initialWeightKg
                                                "
                                                class="clean-input with-unit"
                                                required
                                            />
                                            <span class="unit-text">kg</span>
                                        </div>
                                        <span class="field-hint"
                                            >Ditimbang manual oleh operator dengan timbangan fisik (alat IoT belum memiliki load cell otomatis). Kapasitas maks 200 kg.</span
                                        >
                                    </div>

                                    <div class="form-field">
                                        <label class="form-label"
                                            >Kadar Air Saat Masuk (%)</label
                                        >
                                        <div class="input-unit-wrap">
                                            <input
                                                type="number"
                                                step="0.5"
                                                min="10"
                                                max="60"
                                                v-model.number="
                                                    formData.initialMoisturePercent
                                                "
                                                class="clean-input with-unit"
                                                required
                                            />
                                            <span class="unit-text">%</span>
                                        </div>
                                        <span class="field-hint"
                                            >Diukur dengan Grain Moisture Meter manual atau gunakan standar panen (umumnya 22% - 28%).</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 2: Penjadwalan Waktu Pengeringan (Otomatis / Manual) -->
                            <div class="form-card-section timing-config-card">
                                <div class="section-header-row">
                                    <div>
                                        <h3 class="section-heading">
                                            2. Penjadwalan & Waktu Pengeringan
                                        </h3>
                                        <p class="section-desc">
                                            Pilih apakah pengeringan berjalan otomatis dengan prediksi cerdas atau diatur jamnya secara manual.
                                        </p>
                                    </div>
                                    <div class="timing-mode-switch">
                                        <button
                                            type="button"
                                            class="switch-tab-btn"
                                            :class="{
                                                active:
                                                    formData.timingMode === 'AUTO',
                                            }"
                                            @click="setTimingMode('AUTO')"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-btn-icon">
                                                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                                            </svg>
                                            <span>Otomatis (Prediksi)</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="switch-tab-btn"
                                            :class="{
                                                active:
                                                    formData.timingMode === 'MANUAL',
                                            }"
                                            @click="setTimingMode('MANUAL')"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-btn-icon">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span>Manual / Jadwal</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Tampilan 1: Mode Otomatis (Prediksi Cerdas SNI) -->
                                <div
                                    v-if="formData.timingMode === 'AUTO'"
                                    class="auto-mode-view"
                                >
                                    <div class="auto-predict-banner">
                                        <div class="predict-badge-row">
                                            <span class="predict-chip-active">
                                                <span class="pulse-green-dot"></span>
                                                Prediksi Waktu Otomatis Aktif
                                            </span>
                                            <span class="predict-sni-badge"
                                                >Target Standar Mutu SNI 12%</span
                                            >
                                        </div>

                                        <div class="auto-timeline-quad">
                                            <div class="quad-item">
                                                <span class="quad-lbl">JAM MULAI</span>
                                                <strong class="quad-val">Sekarang (Realtime)</strong>
                                                <span class="quad-sub">Saat tombol ditekan</span>
                                            </div>
                                            <div class="quad-item">
                                                <span class="quad-lbl">ESTIMASI LAMA WAKTU</span>
                                                <strong class="quad-val text-amber"
                                                    >~{{ estimatedDurationHours }} Jam</strong
                                                >
                                                <span class="quad-sub"
                                                    >Estimasi model pengeringan</span
                                                >
                                            </div>
                                            <div class="quad-item">
                                                <span class="quad-lbl">PERKIRAAN SELESAI</span>
                                                <strong class="quad-val text-green"
                                                    >Pukul {{ estimatedFinishTimeAuto }} WIB</strong
                                                >
                                                <span class="quad-sub">Hari ini (otomatis)</span>
                                            </div>
                                            <div class="quad-item">
                                                <span class="quad-lbl">KONTROL SUHU</span>
                                                <strong class="quad-val text-emerald">Otomatis Termostat</strong>
                                                <span class="quad-sub">Zona ideal 48°C - 50°C</span>
                                            </div>
                                        </div>

                                        <div class="auto-explain-note">
                                            <svg
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#166534"
                                                stroke-width="2"
                                            >
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            </svg>
                                            <p>
                                                Dalam <strong>Mode Otomatis</strong>, sistem memprediksi durasi dan jam selesai secara otomatis melalui model kinetika pengeringan suhu & waktu ({{ formData.initialMoisturePercent }}% &rarr; 12.0%). Pemanas dan sirkulasi akan otomatis berhenti setelah standar mutu tercapai.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tampilan 2: Mode Kustom / Manual (Atur Jam & Rentang Waktu) -->
                                <div v-else class="manual-mode-view">
                                    <div class="manual-type-selector">
                                        <span class="selector-lbl"
                                            >Cara Menentukan Waktu Manual:</span
                                        >
                                        <div class="type-pill-btns">
                                            <button
                                                type="button"
                                                class="type-pill"
                                                :class="{
                                                    active:
                                                        formData.manualScheduleType ===
                                                        'RANGE',
                                                }"
                                                @click="
                                                    formData.manualScheduleType =
                                                        'RANGE'
                                                "
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pill-icon">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                <span>Rentang Jam (Dari Jam ... s/d Jam ...)</span>
                                            </button>
                                            <button
                                                type="button"
                                                class="type-pill"
                                                :class="{
                                                    active:
                                                        formData.manualScheduleType ===
                                                        'DURATION',
                                                }"
                                                @click="
                                                    formData.manualScheduleType =
                                                        'DURATION'
                                                "
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pill-icon">
                                                    <path d="M5 22h14M5 2h14M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>
                                                </svg>
                                                <span>Durasi Waktu Langsung (Jam)</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Opsi A: Rentang Jam (Dari Jam ke Jam) -->
                                    <div
                                        v-if="
                                            formData.manualScheduleType === 'RANGE'
                                        "
                                        class="range-time-box"
                                    >
                                        <div class="form-two-cols">
                                            <div class="form-field">
                                                <label class="form-label">
                                                    <span>Dari Jam (Mulai Pengeringan)</span>
                                                    <span class="time-sub-tag">WIB</span>
                                                </label>
                                                <input
                                                    type="time"
                                                    v-model="formData.startTime"
                                                    @change="onTimeRangeChanged"
                                                    class="clean-input input-time"
                                                    required
                                                />
                                                <span class="field-hint"
                                                    >Waktu mulai proses pengeringan.</span
                                                >
                                            </div>

                                            <div class="form-field">
                                                <label class="form-label">
                                                    <span>Sampai Jam (Selesai Pengeringan)</span>
                                                    <span class="time-sub-tag">WIB</span>
                                                </label>
                                                <input
                                                    type="time"
                                                    v-model="formData.endTime"
                                                    @change="onTimeRangeChanged"
                                                    class="clean-input input-time"
                                                    required
                                                />
                                                <span class="field-hint"
                                                    >Waktu pengeringan dijadwalkan berakhir.</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Banner Hasil Kalkulasi Rentang Jam -->
                                        <div class="schedule-summary-banner">
                                            <div class="ssb-icon ssb-icon-target">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                            </div>
                                            <div class="ssb-text">
                                                <strong>Jadwal Pengeringan Terpilih:</strong>
                                                <p>
                                                    Mulai pukul
                                                    <strong>{{ formData.startTime }} WIB</strong>
                                                    sampai dengan pukul
                                                    <strong>{{ formData.endTime }} WIB</strong>
                                                    &bull; Total Durasi Operasi:
                                                    <span class="highlight-duration"
                                                        >{{ manualCalculatedDuration }} Jam</span
                                                    >
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Opsi B: Durasi Langsung (Jam) -->
                                    <div v-else class="duration-time-box">
                                        <div class="form-field">
                                            <label class="form-label"
                                                >Pilihan Cepat Durasi Pengeringan:</label
                                            >
                                            <div class="quick-hours-grid">
                                                <button
                                                    v-for="hrs in [3, 4, 6, 8, 10, 12]"
                                                    :key="hrs"
                                                    type="button"
                                                    class="btn-hour-pill"
                                                    :class="{
                                                        active:
                                                            Number(
                                                                formData.manualDurationHours
                                                            ) === hrs,
                                                    }"
                                                    @click="setQuickDuration(hrs)"
                                                >
                                                    {{ hrs }} Jam
                                                </button>
                                            </div>
                                        </div>

                                        <div
                                            class="form-two-cols"
                                            style="margin-top: 14px"
                                        >
                                            <div class="form-field">
                                                <label class="form-label"
                                                    >Atur Jam Mulai</label
                                                >
                                                <input
                                                    type="time"
                                                    v-model="formData.startTime"
                                                    class="clean-input input-time"
                                                />
                                            </div>

                                            <div class="form-field">
                                                <label class="form-label"
                                                    >Durasi Operasi Manual (Jam)</label
                                                >
                                                <div class="input-unit-wrap">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="1"
                                                        max="48"
                                                        v-model.number="
                                                            formData.manualDurationHours
                                                        "
                                                        @input="onManualDurationInput"
                                                        class="clean-input with-unit"
                                                    />
                                                    <span class="unit-text">Jam</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Banner Hasil Kalkulasi Durasi -->
                                        <div class="schedule-summary-banner">
                                            <div class="ssb-icon ssb-icon-duration">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 22h14M5 2h14M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>
                                                </svg>
                                            </div>
                                            <div class="ssb-text">
                                                <strong>Perhitungan Jadwal Selesai:</strong>
                                                <p>
                                                    Mulai pukul
                                                    <strong>{{ formData.startTime }} WIB</strong>
                                                    dengan durasi
                                                    <strong
                                                        >{{
                                                            formData.manualDurationHours
                                                        }}
                                                        Jam</strong
                                                    >
                                                    &rarr; Diperkirakan selesai pada pukul
                                                    <span class="highlight-duration"
                                                        >{{
                                                            manualFinishTimeFromDuration
                                                        }}
                                                        WIB</span
                                                    >
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Penyesuaian Target Kadar Air Manual -->
                                    <div class="manual-additional-params">
                                        <div class="form-two-cols">
                                            <div class="form-field">
                                                <label class="form-label"
                                                    >Target Kadar Air Kustom (%)</label
                                                >
                                                <div class="input-unit-wrap">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="8"
                                                        max="25"
                                                        v-model.number="
                                                            formData.targetMoisturePercent
                                                        "
                                                        class="clean-input with-unit"
                                                    />
                                                    <span class="unit-text">%</span>
                                                </div>
                                                <span class="field-hint"
                                                    >Standar SNI hanjeli adalah 12.0%.</span
                                                >
                                            </div>

                                            <div class="form-field">
                                                <label class="form-label"
                                                    >Batas Suhu Maksimal Ruang (°C)</label
                                                >
                                                <div class="input-unit-wrap">
                                                    <input
                                                        type="number"
                                                        step="1"
                                                        min="35"
                                                        max="60"
                                                        v-model.number="
                                                            formData.maxTempLimit
                                                        "
                                                        class="clean-input with-unit"
                                                    />
                                                    <span class="unit-text">°C</span>
                                                </div>
                                                <span class="field-hint"
                                                    >Suhu aman untuk mutu biji adalah 48°C - 50°C.</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 3: Cara Pengeringan (Mode Pemanasan) -->
                            <div class="form-card-section">
                                <h3 class="section-heading">
                                    3. Pilih Cara Pengeringan
                                </h3>
                                <p class="section-desc">
                                    Pilih mode yang sesuai dengan kondisi cuaca
                                    hari ini.
                                </p>

                                <div class="mode-cards-stack">
                                    <!-- Mode 1: Otomatis (Rekomendasi) -->
                                    <div
                                        class="mode-card"
                                        :class="{
                                            active:
                                                formData.dryingMode ===
                                                'HYBRID_SOLAR_ELECTRIC',
                                        }"
                                        @click="
                                            formData.dryingMode =
                                                'HYBRID_SOLAR_ELECTRIC'
                                        "
                                    >
                                        <div class="mode-radio">
                                            <div
                                                v-if="
                                                    formData.dryingMode ===
                                                    'HYBRID_SOLAR_ELECTRIC'
                                                "
                                                class="radio-dot"
                                            ></div>
                                        </div>
                                        <div class="mode-body">
                                            <div class="mode-title-row">
                                                <strong class="mode-name"
                                                    >Otomatis (Matahari +
                                                    Pemanas Listrik)</strong
                                                >
                                                <span class="recom-badge"
                                                    >Sangat Disarankan</span
                                                >
                                            </div>
                                            <p class="mode-info-text">
                                                Sistem memanfaatkan panas
                                                matahari. Jika mendung, malam,
                                                atau hujan, pemanas listrik
                                                otomatis menyala menjaga suhu
                                                stabil 48°C - 50°C.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Mode 2: Hanya Sinar Matahari -->
                                    <div
                                        class="mode-card"
                                        :class="{
                                            active:
                                                formData.dryingMode ===
                                                'PURE_SOLAR',
                                        }"
                                        @click="
                                            formData.dryingMode = 'PURE_SOLAR'
                                        "
                                    >
                                        <div class="mode-radio">
                                            <div
                                                v-if="
                                                    formData.dryingMode ===
                                                    'PURE_SOLAR'
                                                "
                                                class="radio-dot"
                                            ></div>
                                        </div>
                                        <div class="mode-body">
                                            <strong class="mode-name"
                                                >Hanya Sinar Matahari
                                                Alami</strong
                                            >
                                            <p class="mode-info-text">
                                                Pemanas listrik dimatikan
                                                sepenuhnya. Sangat hemat energi
                                                saat cuaca panas terik seharian.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Mode 3: Pemanas Tambahan Penuh -->
                                    <div
                                        class="mode-card"
                                        :class="{
                                            active:
                                                formData.dryingMode ===
                                                'ELECTRIC_BOOSTER',
                                        }"
                                        @click="
                                            formData.dryingMode =
                                                'ELECTRIC_BOOSTER'
                                        "
                                    >
                                        <div class="mode-radio">
                                            <div
                                                v-if="
                                                    formData.dryingMode ===
                                                    'ELECTRIC_BOOSTER'
                                                "
                                                class="radio-dot"
                                            ></div>
                                        </div>
                                        <div class="mode-body">
                                            <strong class="mode-name"
                                                >Pemanas Cepat (Booster)</strong
                                            >
                                            <p class="mode-info-text">
                                                Pemanas listrik bekerja aktif
                                                lebih intensif untuk pengeringan
                                                cepat saat musim hujan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 4: Catatan Tambahan (Opsional) -->
                            <div class="form-card-section">
                                <h3 class="section-heading">
                                    4. Catatan Tambahan (Opsional)
                                </h3>
                                <textarea
                                    v-model="formData.notes"
                                    rows="2"
                                    class="clean-input"
                                    placeholder="Misal: Gabah panen dari sawah blok A Waluran..."
                                ></textarea>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Ringkasan Perkiraan Hasil & Tombol Mulai -->
                        <div class="summary-col">
                            <div class="summary-card">
                                <h3 class="summary-title">
                                    Perkiraan Hasil Pengeringan
                                </h3>
                                <p class="summary-sub">
                                    {{
                                        formData.timingMode === 'AUTO'
                                            ? 'Dihitung otomatis berdasarkan data gabah Anda.'
                                            : 'Mengikuti jadwal manual yang Anda tentukan.'
                                    }}
                                </p>

                                <div class="summary-stats-list">
                                    <!-- Mode Waktu & Jadwal -->
                                    <div class="stat-line">
                                        <span class="lbl">Mode Waktu:</span>
                                        <span
                                            class="timing-badge-pill"
                                            :class="
                                                formData.timingMode === 'AUTO'
                                                    ? 'timing-badge-auto'
                                                    : 'timing-badge-manual'
                                            "
                                        >
                                            <svg v-if="formData.timingMode === 'AUTO'" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;">
                                                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                                            </svg>
                                            <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span>{{
                                                formData.timingMode === 'AUTO'
                                                    ? 'Otomatis (Prediksi SNI)'
                                                    : 'Manual Terjadwal'
                                            }}</span>
                                        </span>
                                    </div>

                                    <div class="stat-line highlight-line">
                                        <span class="lbl">{{
                                            formData.timingMode === 'AUTO'
                                                ? 'Estimasi Waktu:'
                                                : 'Jadwal Operasi:'
                                        }}</span>
                                        <strong
                                            class="val"
                                            :class="
                                                formData.timingMode === 'AUTO'
                                                    ? 'text-amber'
                                                    : 'text-green'
                                            "
                                        >
                                            {{
                                                formData.timingMode === 'AUTO'
                                                    ? `~${estimatedDurationHours} Jam`
                                                    : `${activeDurationDisplay} Jam`
                                            }}
                                        </strong>
                                    </div>

                                    <div class="stat-sub-info-row">
                                        <span class="sub-info-lbl">{{
                                            formData.timingMode === 'AUTO'
                                                ? 'Perkiraan Selesai:'
                                                : 'Rentang Waktu:'
                                        }}</span>
                                        <span class="sub-info-val">{{
                                            formData.timingMode === 'AUTO'
                                                ? `Hari ini pukul ${estimatedFinishTimeAuto} WIB`
                                                : scheduleDisplaySummary
                                        }}</span>
                                    </div>

                                    <div class="stat-line">
                                        <span class="lbl"
                                            >Kadar Air Masuk:</span
                                        >
                                        <strong class="val"
                                            >{{
                                                formData.initialMoisturePercent
                                            }}%</strong
                                        >
                                    </div>
                                    <div class="stat-line">
                                        <span class="lbl"
                                            >Target Kadar Air:</span
                                        >
                                        <strong class="val text-green"
                                            >{{
                                                formData.targetMoisturePercent
                                            }}%
                                            {{
                                                formData.targetMoisturePercent === 12
                                                    ? '(Standar SNI)'
                                                    : ''
                                            }}</strong
                                        >
                                    </div>
                                    <div class="stat-line">
                                        <span class="lbl"
                                            >Estimasi Berat Kering:</span
                                        >
                                        <strong class="val"
                                            >{{
                                                estimatedFinalWeightKg
                                            }}
                                            kg</strong
                                        >
                                    </div>
                                    <div class="stat-line">
                                        <span class="lbl">Penyusutan Air:</span>
                                        <strong class="val text-purple"
                                            >{{
                                                estimatedWaterLossKg
                                            }}
                                            kg</strong
                                        >
                                    </div>
                                </div>

                                <!-- Tombol Mulai Utama -->
                                <div class="summary-action-box">
                                    <button
                                        v-if="hasActiveBatch"
                                        type="button"
                                        class="btn-submit-main btn-session-active"
                                        @click="router.push('/drying/active')"
                                    >
                                        Buka Pengeringan Aktif &rarr;
                                    </button>

                                    <button
                                        v-else
                                        type="button"
                                        class="btn-submit-main"
                                        @click="handleStart"
                                        :disabled="isSubmitting || !isHardwareConnected"
                                        :class="{ 'btn-submit-disabled': !isHardwareConnected }"
                                    >
                                        <span v-if="!isHardwareConnected"
                                            >Menunggu Sensor Terbaca...</span
                                        >
                                        <span v-else-if="!isSubmitting"
                                            >Mulai Pengeringan Sekarang</span
                                        >
                                        <span v-else>Memulai Proses...</span>
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-cancel-link"
                                        @click="$emit('cancel')"
                                    >
                                        Batal & Kembali
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Bluetooth BLE & Wi-Fi Setup Modal -->
        <DeviceSetupBleModal
            :is-open="isWifiSetupOpen"
            @close="isWifiSetupOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { batchService } from "../../services/batchService";
import { confirmDialog, alertDialog } from "../../services/confirmDialogService";
import { telemetryService } from "../../services/telemetryService";
import connectionManager from "../../services/connectionManager";
import DeviceSetupBleModal from "../../components/DeviceSetupBleModal.vue";
import DryingFormSkeleton from "../../components/DryingFormSkeleton.vue";

defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["start-process", "cancel"]);

const router = useRouter();
const isInitialLoading = ref(true);
const selectedPreset = ref("biji_hanjeli");
const isSubmitting = ref(false);
const isWifiSetupOpen = ref(false);

const hasActiveBatch = ref(false);
const activeBatchData = ref(null);
const currentTelemetry = ref(null);

async function checkActiveBatch() {
    try {
        const res = await batchService.getActiveBatch();
        if (res && res.activeBatch) {
            hasActiveBatch.value = true;
            activeBatchData.value = res.activeBatch;
        } else {
            hasActiveBatch.value = false;
            activeBatchData.value = null;
        }
    } catch (err) {
        console.warn("Check active batch error:", err);
    }
}

async function loadCurrentTelemetry() {
    try {
        const res = await telemetryService.getCurrent();
        if (res && res.telemetry) {
            currentTelemetry.value = res.telemetry;
        }
    } catch (err) {
        console.warn("Check current telemetry error:", err);
    }
}

onMounted(async () => {
    await checkActiveBatch();
    await loadCurrentTelemetry();
    isInitialLoading.value = false;

    connectionManager.on('telemetry_live', (data) => {
        if (data && data.hasData) {
            currentTelemetry.value = data;
        }
    });
});

onUnmounted(() => {
    connectionManager.off('telemetry_live');
});

const isHardwareConnected = computed(() => {
    return Boolean(connectionManager.state.isHardwareActive || currentTelemetry.value?.isLive);
});

function openHardwareSetup() {
    isWifiSetupOpen.value = true;
}

// Time calculation helpers
function getCurrentTimeHHMM() {
    const now = new Date();
    const hh = String(now.getHours()).padStart(2, "0");
    const mm = String(now.getMinutes()).padStart(2, "0");
    return `${hh}:${mm}`;
}

function addHoursToTimeHHMM(timeStr, hours) {
    try {
        if (!timeStr) timeStr = getCurrentTimeHHMM();
        const [h, m] = timeStr.split(":").map(Number);
        const totalMinutes = Math.round((h * 60 + m) + (Number(hours) * 60));
        const endH = Math.floor((totalMinutes / 60) % 24);
        const endM = totalMinutes % 60;
        return `${String(endH).padStart(2, "0")}:${String(endM).padStart(2, "0")}`;
    } catch (e) {
        return "17:00";
    }
}

function calculateDiffHours(startTimeStr, endTimeStr) {
    try {
        if (!startTimeStr || !endTimeStr) return "6.0";
        const [h1, m1] = startTimeStr.split(":").map(Number);
        const [h2, m2] = endTimeStr.split(":").map(Number);
        let diffMinutes = (h2 * 60 + m2) - (h1 * 60 + m1);
        if (diffMinutes <= 0) {
            diffMinutes += 24 * 60; // overnight adjustment
        }
        return (diffMinutes / 60).toFixed(1);
    } catch (e) {
        return "6.0";
    }
}

const initialStartTime = getCurrentTimeHHMM();
const initialEndTime = addHoursToTimeHHMM(initialStartTime, 6);

const formData = reactive({
    cropVariety: "Biji Hanjeli Sukabumi",
    operatorName: "Operator Green House",
    initialWeightKg: 100.0,
    initialMoisturePercent: 24.0,
    targetMoisturePercent: 12.0,
    dryingMode: "HYBRID_SOLAR_ELECTRIC",
    timingMode: "AUTO", // 'AUTO' (Prediksi Otomatis SNI) | 'MANUAL' (Atur Jam Sendiri)
    manualScheduleType: "RANGE", // 'RANGE' (Dari jam ke jam) | 'DURATION' (Durasi jam langsung)
    startTime: initialStartTime,
    endTime: initialEndTime,
    manualDurationHours: 6.0,
    maxTempLimit: 50.0,
    notes: "Pengeringan standar suhu 48°C - 50°C target SNI 12%.",
});

function setTimingMode(mode) {
    formData.timingMode = mode;
    selectedPreset.value = mode === "AUTO" ? "biji_hanjeli" : "custom";
    if (mode === "AUTO") {
        formData.targetMoisturePercent = 12.0;
        formData.notes = "Standar pengeringan biji hanjeli otomatis target mutu SNI (≤ 12.0%).";
    } else {
        formData.notes = `Pengeringan manual terjadwal (${formData.startTime} - ${formData.endTime} WIB).`;
    }
}

function applyPreset(presetKey) {
    selectedPreset.value = presetKey;
    if (presetKey === "biji_hanjeli") {
        setTimingMode("AUTO");
        formData.cropVariety = "Biji Hanjeli Sukabumi";
        formData.initialWeightKg = 100.0;
        formData.initialMoisturePercent = 24.0;
        formData.targetMoisturePercent = 12.0;
        formData.dryingMode = "HYBRID_SOLAR_ELECTRIC";
        formData.maxTempLimit = 50.0;
    } else if (presetKey === "custom") {
        setTimingMode("MANUAL");
    }
}

function setQuickDuration(hours) {
    formData.manualDurationHours = hours;
    formData.endTime = addHoursToTimeHHMM(formData.startTime, hours);
}

function onTimeRangeChanged() {
    formData.manualDurationHours = parseFloat(calculateDiffHours(formData.startTime, formData.endTime));
}

function onManualDurationInput() {
    formData.endTime = addHoursToTimeHHMM(formData.startTime, formData.manualDurationHours);
}

// Perhitungan Hasil Praktis & Waktu
const manualCalculatedDuration = computed(() => {
    return calculateDiffHours(formData.startTime, formData.endTime);
});

const manualFinishTimeFromDuration = computed(() => {
    return addHoursToTimeHHMM(formData.startTime, formData.manualDurationHours);
});

const estimatedDurationHours = computed(() => {
    if (formData.timingMode === "MANUAL") {
        if (formData.manualScheduleType === "RANGE") {
            return manualCalculatedDuration.value;
        } else {
            return Number(formData.manualDurationHours || 6).toFixed(1);
        }
    }
    // Mode Otomatis: Prediksi berdasarkan kadar air & laju pengeringan
    const m0 = formData.initialMoisturePercent || 24;
    const me = formData.targetMoisturePercent || 12;
    const drop = Math.max(1, m0 - me);
    const rate = formData.dryingMode === "ELECTRIC_BOOSTER" ? 2.0 : 1.5;
    return (drop / rate).toFixed(1);
});

const estimatedFinishTimeAuto = computed(() => {
    return addHoursToTimeHHMM(getCurrentTimeHHMM(), Number(estimatedDurationHours.value));
});

const activeDurationDisplay = computed(() => {
    return estimatedDurationHours.value;
});

const scheduleDisplaySummary = computed(() => {
    if (formData.timingMode === "MANUAL") {
        if (formData.manualScheduleType === "RANGE") {
            return `${formData.startTime} s/d ${formData.endTime} WIB`;
        } else {
            return `Pukul ${formData.startTime} WIB (Durasi ${formData.manualDurationHours} Jam)`;
        }
    }
    return `Otomatis (Selesai ~${estimatedFinishTimeAuto.value} WIB)`;
});

const estimatedFinalWeightKg = computed(() => {
    const w0 = formData.initialWeightKg || 100;
    const m0 = formData.initialMoisturePercent || 24;
    const me = formData.targetMoisturePercent || 12;
    if (m0 <= me) return w0.toFixed(1);
    const wf = w0 * ((100 - m0) / (100 - me));
    return wf.toFixed(1);
});

const estimatedWaterLossKg = computed(() => {
    const w0 = formData.initialWeightKg || 100;
    const wf = parseFloat(estimatedFinalWeightKg.value);
    return Math.max(0, w0 - wf).toFixed(1);
});

async function handleStart() {
    if (!isHardwareConnected.value) {
        alertDialog({
            title: "Alat Pengering Belum Terbaca",
            message: "Alat sensor mikrokontroler (ESP32) belum terhubung atau belum mengirimkan data sensor. Pastikan alat pengering sudah dinyalakan dan sensor terbaca sebelum memulai pengeringan.",
            type: "warning",
            confirmText: "Mengerti",
        });
        return;
    }

    const isAuto = formData.timingMode === "AUTO";
    const timeDetail = isAuto
        ? `Mode Otomatis (Estimasi ~${estimatedDurationHours.value} jam, selesai sekitar pukul ${estimatedFinishTimeAuto.value} WIB)`
        : `Jadwal Manual (${scheduleDisplaySummary.value}, total durasi ${activeDurationDisplay.value} jam)`;

    const confirmed = await confirmDialog({
        title: "Mulai Pengeringan Gabah?",
        message: `Mulai pengeringan ${formData.cropVariety} (${formData.initialWeightKg} kg)?\n\n• ${timeDetail}\n• Target Kadar Air: ${formData.targetMoisturePercent}%`,
        details: isAuto
            ? "Sistem pintar akan otomatis memantau penurunan kadar air dan mematikan pemanas saat standar 12% SNI tercapai."
            : "Alat pengering akan beroperasi sesuai rentang waktu atau durasi yang telah Anda jadwalkan.",
        type: "success",
        confirmText: "Mulai Sekarang",
        cancelText: "Cek Kembali",
    });

    if (!confirmed) return;

    isSubmitting.value = true;
    try {
        const scheduleNote = isAuto
            ? `[MODE OTOMATIS: Estimasi ~${estimatedDurationHours.value} Jam, Target ${formData.targetMoisturePercent}% SNI]`
            : `[JADWAL MANUAL: ${scheduleDisplaySummary.value} (${activeDurationDisplay.value} Jam)]`;

        const fullNotes = formData.notes 
            ? `${scheduleNote} ${formData.notes}` 
            : scheduleNote;

        const payload = {
            cropVariety: formData.cropVariety || "Biji Hanjeli",
            operatorName: formData.operatorName || "Operator Green House",
            initialWeightKg: Number(formData.initialWeightKg) || 100,
            initialMoisturePercent:
                Number(formData.initialMoisturePercent) || 24,
            targetMoisturePercent:
                Number(formData.targetMoisturePercent) || 12.0,
            dryingMode: formData.dryingMode || "HYBRID_SOLAR_ELECTRIC",
            notes: fullNotes,
        };

        await batchService.create(payload);
        emit("start-process");
    } catch (err) {
        console.error("Start batch error:", err);
        // fallback navigate to active
        emit("start-process");
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<style scoped>
.drying-form-page {
    width: 100%;
    min-height: 100%;
}

.drying-form-main-content {
    padding: 24px 28px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
    margin: 0;
    box-sizing: border-box;
}

.form-header-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.back-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    background: transparent;
    padding: 0;
    margin-bottom: 12px;
}

.back-link-btn:hover {
    color: #1b5e20;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f291e;
    margin-bottom: 4px;
}

.page-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

/* Warning Banners */
.active-batch-lock-banner {
    background: #fef3c7;
    border: 1.5px solid #fde68a;
    border-radius: 14px;
    padding: 18px 22px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.ab-lock-content strong {
    display: block;
    font-size: 15px;
    color: #92400e;
    margin-bottom: 4px;
}

.ab-lock-content p {
    font-size: 13px;
    color: #b45309;
    margin: 0 0 10px 0;
}

.btn-lock-view {
    background: #d97706;
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}

.hw-warning-banner {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.hw-warn-text {
    flex: 1;
}

.hw-warn-text strong {
    display: block;
    font-size: 14px;
    color: #92400e;
}

.hw-warn-text p {
    font-size: 12px;
    color: #b45309;
    margin: 0;
}

.btn-warn-setup {
    background: #f59e0b;
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}

/* Grid Layout */
.form-grid-layout {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 20px;
}

@media (max-width: 900px) {
    .form-grid-layout {
        grid-template-columns: 1fr;
    }
}

.form-inputs-col {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.preset-box {
    background: #ffffff;
    border-radius: 14px;
    padding: 16px 20px;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.preset-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
}

.preset-btns {
    display: flex;
    gap: 8px;
}

.btn-preset {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-preset-icon {
    flex-shrink: 0;
}

.btn-preset.active {
    background: #e8f5e9;
    border-color: #1b5e20;
    color: #1b5e20;
    font-weight: 700;
}

.form-card-section {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.section-heading {
    font-size: 16px;
    font-weight: 700;
    color: #0f291e;
    margin-bottom: 6px;
}

.section-desc {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 16px;
}

.form-two-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 600px) {
    .form-two-cols {
        grid-template-columns: 1fr;
    }
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
}

.clean-input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1.5px solid #cbd5e1;
    font-size: 14px;
    color: #0f291e;
    background: #ffffff;
    transition: border-color 0.2s ease;
    box-sizing: border-box;
}

.clean-input:focus {
    outline: none;
    border-color: #1b5e20;
}

.input-unit-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.clean-input.with-unit {
    padding-right: 42px;
}

.unit-text {
    position: absolute;
    right: 14px;
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
}

.field-hint {
    font-size: 11px;
    color: #94a3b8;
}

/* Section Header & Timing Mode Switcher */
.section-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.timing-mode-switch {
    display: inline-flex;
    background: #f1f5f9;
    border-radius: 10px;
    padding: 3px;
    gap: 4px;
}

.switch-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.switch-tab-btn.active {
    background: #ffffff;
    color: #1b5e20;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

/* Auto Mode Predict Banner */
.auto-predict-banner {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.predict-badge-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.predict-chip-active {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #dcfce7;
    color: #166534;
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 9999px;
}

.pulse-green-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #16a34a;
    box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
}

.predict-sni-badge {
    font-size: 11.5px;
    font-weight: 700;
    color: #166534;
}

.auto-timeline-quad {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

@media (max-width: 768px) {
    .auto-timeline-quad {
        grid-template-columns: repeat(2, 1fr);
    }
}

.quad-item {
    background: #ffffff;
    border: 1px solid #dcfce7;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.quad-lbl {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.5px;
}

.quad-val {
    font-size: 14.5px;
    font-weight: 800;
    color: #0f291e;
}

.quad-sub {
    font-size: 11px;
    color: #64748b;
}

.auto-explain-note {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 8px;
    padding: 10px 12px;
}

.auto-explain-note p {
    font-size: 12px;
    color: #166534;
    line-height: 1.45;
    margin: 0;
}

/* Manual Mode Section */
.manual-type-selector {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}

.selector-lbl {
    font-size: 12.5px;
    font-weight: 700;
    color: #334155;
}

.type-pill-btns {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.type-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    border: 1.5px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pill-icon {
    flex-shrink: 0;
}

.type-pill.active {
    background: #eff6ff;
    border-color: #2563eb;
    color: #1d4ed8;
}

.input-time {
    font-weight: 700;
    font-size: 15px;
}

.time-sub-tag {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    margin-left: 4px;
}

.schedule-summary-banner {
    margin-top: 14px;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.ssb-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #e2e8f0;
    color: #1e293b;
    flex-shrink: 0;
}

.ssb-icon-target {
    background: #dbeafe;
    color: #1d4ed8;
}

.ssb-icon-duration {
    background: #fef3c7;
    color: #b45309;
}

.ssb-text strong {
    display: block;
    font-size: 12.5px;
    color: #1e293b;
    margin-bottom: 2px;
}

.ssb-text p {
    font-size: 12px;
    color: #475569;
    margin: 0;
}

.highlight-duration {
    font-weight: 800;
    color: #1b5e20;
    background: #dcfce7;
    padding: 1px 6px;
    border-radius: 4px;
}

.quick-hours-grid {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-hour-pill {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-hour-pill.active {
    background: #1b5e20;
    border-color: #1b5e20;
    color: #ffffff;
}

.manual-additional-params {
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
}

/* Timing Badge Pill for Summary Card */
.timing-badge-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}

.timing-badge-auto {
    background: #dcfce7;
    color: #166534;
}

.timing-badge-manual {
    background: #fef3c7;
    color: #92400e;
}

.stat-sub-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    padding: 0 4px;
    color: #64748b;
}

.sub-info-lbl {
    font-weight: 600;
}

.sub-info-val {
    font-weight: 700;
    color: #1e293b;
}

/* Mode Cards */
.mode-cards-stack {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.mode-card {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.2s ease;
}

.mode-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.mode-card.active {
    border-color: #1b5e20;
    background: #f0fdf4;
}

.mode-radio {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
}

.mode-card.active .mode-radio {
    border-color: #1b5e20;
}

.radio-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #1b5e20;
}

.mode-body {
    flex: 1;
}

.mode-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 4px;
    flex-wrap: wrap;
}

.mode-name {
    font-size: 14px;
    color: #0f291e;
}

.recom-badge {
    background: #dcfce7;
    color: #166534;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
}

.mode-info-text {
    font-size: 12px;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

/* Summary Card */
.summary-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: sticky;
    top: 20px;
}

.summary-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f291e;
    margin-bottom: 4px;
}

.summary-sub {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 20px;
}

.summary-stats-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}

.stat-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
}

.stat-line .lbl {
    color: #64748b;
}

.stat-line .val {
    color: #0f291e;
    font-weight: 700;
}

.stat-line.highlight-line {
    background: #f8fafc;
    padding: 10px 12px;
    border-radius: 8px;
}

.text-green {
    color: #1b5e20;
}
.text-purple {
    color: #7e22ce;
}
.text-amber {
    color: #d97706;
}

.summary-action-box {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn-submit-main {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    background: #1b5e20;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-submit-main:hover:not(:disabled) {
    background: #144a18;
    box-shadow: 0 4px 12px rgba(27, 94, 32, 0.25);
}

.btn-submit-main:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-submit-main.btn-submit-disabled {
    background: #64748b !important;
    opacity: 0.85;
    cursor: not-allowed;
    box-shadow: none !important;
}

.btn-session-active {
    background: #d97706;
}

.btn-cancel-link {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    padding: 8px;
    cursor: pointer;
}

.btn-cancel-link:hover {
    color: #0f291e;
}

@media (max-width: 900px) {
    .drying-form-main-content {
        padding: 0;
        gap: 12px;
    }

    .form-header-card {
        padding: 16px;
        border-radius: 14px;
    }

    .active-batch-lock-banner {
        padding: 14px 16px;
        border-radius: 14px;
        gap: 12px;
    }

    .form-card-section,
    .summary-card {
        padding: 16px;
        border-radius: 14px;
    }

    .form-inputs-col {
        gap: 12px;
    }
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR DRYING FORM VIEW
   ========================================================================== */
:global(.dark-theme) .form-header-card,
:global(.dark-theme) .preset-box,
:global(.dark-theme) .form-card-section,
:global(.dark-theme) .summary-card,
:global(.dark-theme) .mode-card {
    background: #0B242F;
    border-color: #1E4E61;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

:global(.dark-theme) .mode-card:hover {
    border-color: #2D6B82;
    background: #0E2C39;
}

:global(.dark-theme) .mode-card.active {
    border-color: #1B5E20;
    background: rgba(27, 94, 32, 0.15);
}

:global(.dark-theme) .page-title,
:global(.dark-theme) .section-heading,
:global(.dark-theme) .summary-title,
:global(.dark-theme) .mode-name,
:global(.dark-theme) .preset-title,
:global(.dark-theme) .stat-line .val {
    color: #F8FAFC;
}

:global(.dark-theme) .page-subtitle,
:global(.dark-theme) .summary-sub,
:global(.dark-theme) .section-desc,
:global(.dark-theme) .form-label,
:global(.dark-theme) .field-hint,
:global(.dark-theme) .unit-text,
:global(.dark-theme) .mode-info-text,
:global(.dark-theme) .stat-line .lbl,
:global(.dark-theme) .back-link-btn,
:global(.dark-theme) .btn-cancel-link {
    color: #94A3B8;
}

:global(.dark-theme) .back-link-btn:hover,
:global(.dark-theme) .btn-cancel-link:hover {
    color: #4ADE80;
}

:global(.dark-theme) .clean-input {
    background: #071E27;
    border-color: #1E4E61;
    color: #F8FAFC;
}

:global(.dark-theme) .clean-input:focus {
    background: #0B242F;
    border-color: #16A34A;
}

:global(.dark-theme) .btn-preset {
    background: #113847;
    border-color: #1E4E61;
    color: #CBD5E1;
}

:global(.dark-theme) .btn-preset.active {
    background: #1B5E20;
    border-color: #1B5E20;
    color: #FFFFFF;
}

:global(.dark-theme) .stat-line.highlight-line {
    background: #071E27;
    border-radius: 8px;
    padding: 8px 10px;
}

:global(.dark-theme) .summary-stats-list {
    border-bottom-color: #1E4E61;
}

:global(.dark-theme) .recom-badge {
    background: rgba(27, 94, 32, 0.3);
    color: #86EFAC;
}

:global(.dark-theme) .mode-radio {
    border-color: #1E4E61;
}

:global(.dark-theme) .mode-card.active .mode-radio {
    border-color: #4ADE80;
}

:global(.dark-theme) .radio-dot {
    background: #4ADE80;
}

:global(.dark-theme) .timing-mode-switch {
    background: #071E27;
}

:global(.dark-theme) .switch-tab-btn {
    color: #94A3B8;
}

:global(.dark-theme) .switch-tab-btn.active {
    background: #113847;
    color: #4ADE80;
}

:global(.dark-theme) .auto-predict-banner {
    background: #06281E;
    border-color: #14532D;
}

:global(.dark-theme) .quad-item {
    background: #0B242F;
    border-color: #1E4E61;
}

:global(.dark-theme) .quad-val {
    color: #F8FAFC;
}

:global(.dark-theme) .auto-explain-note {
    background: rgba(11, 36, 47, 0.7);
}

:global(.dark-theme) .auto-explain-note p {
    color: #86EFAC;
}

:global(.dark-theme) .type-pill {
    background: #0B242F;
    border-color: #1E4E61;
    color: #94A3B8;
}

:global(.dark-theme) .type-pill.active {
    background: #113847;
    border-color: #3B82F6;
    color: #93C5FD;
}

:global(.dark-theme) .schedule-summary-banner {
    background: #0B242F;
    border-color: #1E4E61;
}

:global(.dark-theme) .ssb-text strong {
    color: #F8FAFC;
}

:global(.dark-theme) .ssb-text p {
    color: #94A3B8;
}

:global(.dark-theme) .btn-hour-pill {
    background: #0B242F;
    border-color: #1E4E61;
    color: #CBD5E1;
}

:global(.dark-theme) .btn-hour-pill.active {
    background: #1B5E20;
    border-color: #1B5E20;
    color: #FFFFFF;
}

:global(.dark-theme) .manual-additional-params {
    border-top-color: #1E4E61;
}

:global(.dark-theme) .sub-info-val {
    color: #E2E8F0;
}

:global(.dark-theme) .ssb-icon {
    background: #113847;
    color: #E2E8F0;
}

:global(.dark-theme) .ssb-icon-target {
    background: rgba(37, 99, 235, 0.2);
    color: #60A5FA;
}

:global(.dark-theme) .ssb-icon-duration {
    background: rgba(217, 119, 6, 0.2);
    color: #FBBF24;
}
</style>
