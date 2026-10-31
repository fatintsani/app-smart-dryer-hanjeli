<template>
    <Teleport to="body">
        <div
            v-if="isOpen"
            class="qr-modal-overlay"
            @click.self="$emit('close')"
        >
            <div class="qr-modal-card" role="dialog" aria-modal="true">
                <!-- 1. Header Sederhana -->
                <div class="qr-modal-header">
                    <div class="header-title-flex">
                        <div class="header-icon-box">
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#1B5E20"
                                stroke-width="2.2"
                            >
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        <div>
                            <h2 class="qr-modal-title">
                                Cetak Label & QR Kemasan
                            </h2>
                            <p class="qr-modal-subtitle">
                                Pilih jenis stiker untuk ditempel pada kemasan
                                gabah / beras hanjeli.
                            </p>
                        </div>
                    </div>
                    <button
                        class="close-btn"
                        @click="$emit('close')"
                        title="Tutup"
                    >
                        <svg
                            width="18"
                            height="18"
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

                <!-- 2. Pilihan Format Cetak (Tabs Sederhana) -->
                <div class="label-mode-tabs">
                    <button
                        type="button"
                        class="mode-tab-btn"
                        :class="{ active: activeTemplate === 'retail' }"
                        @click="activeTemplate = 'retail'"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <span>Stiker Satuan</span>
                    </button>

                    <button
                        type="button"
                        class="mode-tab-btn"
                        :class="{ active: activeTemplate === 'thermal' }"
                        @click="activeTemplate = 'thermal'"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Printer Kasir (Thermal)</span>
                    </button>

                    <button
                        type="button"
                        class="mode-tab-btn"
                        :class="{ active: activeTemplate === 'a4_grid' }"
                        @click="activeTemplate = 'a4_grid'"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Kertas A4 (Banyak Stiker)</span>
                    </button>
                </div>

                <!-- 3. Konten Modal: Preview di Kiri, Pengaturan Sederhana di Kanan -->
                <div class="qr-modal-body custom-scrollbar">
                    <div class="label-workspace-grid">
                        <!-- Left: Preview Stiker -->
                        <div class="preview-section">
                            <div class="preview-header-bar">
                                <span class="preview-tag">
                                    Pratinjau:
                                    <strong>{{ templateTitle }}</strong>
                                </span>
                            </div>

                            <div class="label-preview-stage">
                                <!-- 1. RETAIL COLOR STICKER TEMPLATE -->
                                <div
                                    v-if="activeTemplate === 'retail'"
                                    id="sticker-node-retail"
                                    class="packaging-sticker-card retail-theme"
                                >
                                    <!-- Top Header -->
                                    <div class="sticker-top">
                                        <div class="sticker-logos">
                                            <img
                                                src="/assets/img/hanjeli.png"
                                                alt="Logo Hanjeli"
                                                class="sticker-logo-img"
                                            />
                                            <div class="sticker-brand-info">
                                                <span class="sticker-org"
                                                    >DESA WISATA HANJELI</span
                                                >
                                                <span class="sticker-sub"
                                                    >WALURAN SUKABUMI &bull;
                                                    STAS-RG</span
                                                >
                                            </div>
                                        </div>
                                        <div
                                            v-if="formCustom.showGrade"
                                            class="sticker-badge-grade"
                                            :class="gradeBadgeClass"
                                        >
                                            GRADE {{ currentGradeLetter }}
                                        </div>
                                    </div>

                                    <div class="sticker-divider"></div>

                                    <!-- Main Content -->
                                    <div class="sticker-main-content">
                                        <div class="sticker-qr-box">
                                            <canvas
                                                ref="qrCanvasRetail"
                                                class="qr-canvas"
                                            ></canvas>
                                            <span class="sticker-scan-hint"
                                                >SCAN TRACEABILITY</span
                                            >
                                        </div>

                                        <div class="sticker-details">
                                            <div class="sticker-product-name">
                                                {{
                                                    formCustom.productName ||
                                                    batch?.cropVariety ||
                                                    "Biji Hanjeli Pilihan Super"
                                                }}
                                            </div>
                                            <div
                                                class="sticker-netto-badge"
                                                v-if="formCustom.netto"
                                            >
                                                Netto: {{ formCustom.netto }}
                                            </div>

                                            <div class="sticker-meta-grid">
                                                <div class="sticker-meta-item">
                                                    <span class="meta-label"
                                                        >NO. BATCH:</span
                                                    >
                                                    <span
                                                        class="meta-value font-mono font-bold"
                                                        >{{ batchCode }}</span
                                                    >
                                                </div>

                                                <div
                                                    class="sticker-meta-item"
                                                    v-if="
                                                        formCustom.showMoisture
                                                    "
                                                >
                                                    <span class="meta-label"
                                                        >KADAR AIR:</span
                                                    >
                                                    <span
                                                        class="meta-value text-green font-bold"
                                                        >{{ finalMoisture }}%
                                                        <small
                                                            class="meta-sub-sni"
                                                            >(SNI)</small
                                                        ></span
                                                    >
                                                </div>

                                                <div class="sticker-meta-item">
                                                    <span class="meta-label"
                                                        >TGL KERING:</span
                                                    >
                                                    <span class="meta-value">{{
                                                        formattedDate
                                                    }}</span>
                                                </div>

                                                <div
                                                    class="sticker-meta-item"
                                                    v-if="formCustom.expiryDate"
                                                >
                                                    <span class="meta-label"
                                                        >BAIK DIGUNAKAN:</span
                                                    >
                                                    <span
                                                        class="meta-value font-bold"
                                                        >{{
                                                            formCustom.expiryDate
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sticker-divider"></div>

                                    <!-- Footer -->
                                    <div class="sticker-footer">
                                        <div class="cert-guarantee">
                                            <svg
                                                width="13"
                                                height="13"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#1B5E20"
                                                stroke-width="2.5"
                                            >
                                                <path
                                                    d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                                ></path>
                                                <polyline
                                                    points="9 12 11 14 15 10"
                                                ></polyline>
                                            </svg>
                                            <span>{{
                                                formCustom.pirt ||
                                                "P-IRT & SNI Terverifikasi"
                                            }}</span>
                                        </div>
                                        <span class="cert-id font-mono">{{
                                            certificateNumber
                                        }}</span>
                                    </div>
                                </div>

                                <!-- 2. THERMAL MONOCHROME PRINTER TEMPLATE -->
                                <div
                                    v-else-if="activeTemplate === 'thermal'"
                                    id="sticker-node-thermal"
                                    class="packaging-sticker-card thermal-theme"
                                >
                                    <div class="thermal-header">
                                        <h4 class="thermal-title">
                                            DESA WISATA HANJELI
                                        </h4>
                                        <p class="thermal-subtitle">
                                            SMART SOLAR DRYER &bull; SUKABUMI
                                        </p>
                                        <div class="thermal-line"></div>
                                    </div>

                                    <div class="thermal-product-block">
                                        <h3 class="thermal-prod-name">
                                            {{
                                                formCustom.productName ||
                                                batch?.cropVariety ||
                                                "BIJI HANJELI KERING"
                                            }}
                                        </h3>
                                        <div class="thermal-grade-netto">
                                            GRADE:
                                            {{ currentGradeLetter }} &bull;
                                            NETTO:
                                            {{ formCustom.netto || "500g" }}
                                        </div>
                                    </div>

                                    <div class="thermal-qr-container">
                                        <div class="thermal-canvas-wrap">
                                            <canvas
                                                ref="qrCanvasThermal"
                                                class="thermal-qr-canvas"
                                            ></canvas>
                                        </div>
                                        <div class="thermal-specs">
                                            <div class="thermal-row">
                                                BATCH:
                                                <strong>{{ batchCode }}</strong>
                                            </div>
                                            <div class="thermal-row">
                                                KA:
                                                <strong
                                                    >{{ finalMoisture }}%
                                                    (SNI)</strong
                                                >
                                            </div>
                                            <div class="thermal-row">
                                                TGL:
                                                <strong>{{
                                                    formattedDate
                                                }}</strong>
                                            </div>
                                            <div class="thermal-row">
                                                EXP:
                                                <strong>{{
                                                    formCustom.expiryDate
                                                }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="thermal-line"></div>
                                    <div class="thermal-footer">
                                        <div>SCAN QR UNTUK SERTIFIKAT MUTU</div>
                                        <div class="thermal-cert-code">
                                            {{ certificateNumber }}
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. A4 MULTI-STICKER GRID PREVIEW -->
                                <div
                                    v-else-if="activeTemplate === 'a4_grid'"
                                    class="a4-sheet-preview-frame"
                                >
                                    <div class="a4-grid-info-bar">
                                        <span
                                            >Layout Lembaran A4:
                                            <strong
                                                >{{ a4GridCount }} Stiker per
                                                Halaman</strong
                                            ></span
                                        >
                                    </div>
                                    <div
                                        class="a4-mini-grid"
                                        :class="`cols-${a4GridCols}`"
                                    >
                                        <div
                                            v-for="n in a4GridCount"
                                            :key="n"
                                            class="a4-mini-sticker-cell"
                                        >
                                            <div class="mini-sticker-header">
                                                <span class="mini-title"
                                                    >{{
                                                        formCustom.productName ||
                                                        "Hanjeli"
                                                    }}
                                                    (Grade
                                                    {{
                                                        currentGradeLetter
                                                    }})</span
                                                >
                                                <span
                                                    class="mini-ka"
                                                    v-if="
                                                        formCustom.showMoisture
                                                    "
                                                    >{{ finalMoisture }}%</span
                                                >
                                            </div>
                                            <div class="mini-sticker-body">
                                                <div
                                                    class="mini-qr-placeholder"
                                                >
                                                    <img
                                                        :src="qrDataUrl"
                                                        alt="QR Code"
                                                        class="mini-qr-img"
                                                    />
                                                </div>
                                                <div class="mini-details">
                                                    <div class="mini-row">
                                                        <span class="lbl"
                                                            >BATCH:</span
                                                        >
                                                        <strong class="val">{{
                                                            batchCode
                                                        }}</strong>
                                                    </div>
                                                    <div class="mini-row">
                                                        <span class="lbl"
                                                            >NETTO:</span
                                                        >
                                                        <span class="val">{{
                                                            formCustom.netto
                                                        }}</span>
                                                    </div>
                                                    <div class="mini-row">
                                                        <span class="lbl"
                                                            >TGL:</span
                                                        >
                                                        <span class="val">{{
                                                            formattedDate
                                                        }}</span>
                                                    </div>
                                                    <div
                                                        class="mini-row"
                                                        v-if="
                                                            formCustom.expiryDate
                                                        "
                                                    >
                                                        <span class="lbl"
                                                            >EXP:</span
                                                        >
                                                        <span class="val">{{
                                                            formCustom.expiryDate
                                                        }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mini-sticker-foot">
                                                <span class="mini-pirt">{{
                                                    formCustom.pirt
                                                }}</span>
                                                <span class="mini-cert">{{
                                                    certificateNumber
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Panel Pengaturan Sederhana -->
                        <div class="customizer-panel">
                            <div class="panel-header-simple">
                                <h3 class="panel-section-title">
                                    Isi Data Stiker
                                </h3>
                                <p class="panel-section-sub">
                                    Atur berat dan masa simpan gabah sebelum
                                    dicetak.
                                </p>
                            </div>

                            <div class="form-fields-stack">
                                <!-- Pilihan Cepat Berat Kemasan (Netto) -->
                                <div class="field-item">
                                    <label class="field-lbl"
                                        >Pilihan Berat Kemasan (Netto)</label
                                    >
                                    <div class="netto-pills-row">
                                        <button
                                            type="button"
                                            v-for="opt in [
                                                '250g',
                                                '500g',
                                                '1 kg',
                                                '5 kg',
                                                '10 kg',
                                            ]"
                                            :key="opt"
                                            class="netto-pill-btn"
                                            :class="{
                                                active:
                                                    formCustom.netto.startsWith(
                                                        opt.replace(
                                                            'g',
                                                            ' gram',
                                                        ),
                                                    ) ||
                                                    formCustom.netto.startsWith(
                                                        opt,
                                                    ),
                                            }"
                                            @click="
                                                formCustom.netto =
                                                    opt.includes('g') &&
                                                    !opt.includes('kg')
                                                        ? opt.replace(
                                                              'g',
                                                              ' gram',
                                                          )
                                                        : opt
                                            "
                                        >
                                            {{ opt }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Nama Produk & Masa Simpan -->
                                <div class="grid-2-fields">
                                    <div class="field-item">
                                        <label class="field-lbl"
                                            >Nama Produk</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formCustom.productName"
                                            placeholder="Nama varietas"
                                            class="settings-input"
                                        />
                                    </div>

                                    <div class="field-item">
                                        <label class="field-lbl"
                                            >Baik Digunakan Hingga</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formCustom.expiryDate"
                                            placeholder="Contoh: Okt 2027"
                                            class="settings-input"
                                        />
                                    </div>
                                </div>

                                <!-- Template Specific Configs for A4 Grid -->
                                <div
                                    v-if="activeTemplate === 'a4_grid'"
                                    class="field-item a4-settings-card"
                                >
                                    <label class="field-lbl"
                                        >Jumlah Stiker per Lembar A4:</label
                                    >
                                    <div class="a4-buttons-group">
                                        <button
                                            type="button"
                                            class="size-btn"
                                            :class="{
                                                active: a4GridCount === 8,
                                            }"
                                            @click="setA4Grid(8, 2)"
                                        >
                                            8 Stiker (Besar)
                                        </button>
                                        <button
                                            type="button"
                                            class="size-btn"
                                            :class="{
                                                active: a4GridCount === 12,
                                            }"
                                            @click="setA4Grid(12, 3)"
                                        >
                                            12 Stiker (Sedang)
                                        </button>
                                        <button
                                            type="button"
                                            class="size-btn"
                                            :class="{
                                                active: a4GridCount === 24,
                                            }"
                                            @click="setA4Grid(24, 4)"
                                        >
                                            24 Stiker (Kompak)
                                        </button>
                                    </div>
                                </div>

                                <!-- Pengaturan Tambahan (P-IRT, Mutu & Link) - Collapsible -->
                                <div class="advanced-collapsible-box">
                                    <button
                                        type="button"
                                        class="btn-toggle-advanced-opts"
                                        @click="
                                            isAdvancedOpen = !isAdvancedOpen
                                        "
                                    >
                                        <div class="adv-left">
                                            <svg
                                                width="15"
                                                height="15"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="3"
                                                ></circle>
                                                <path
                                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"
                                                ></path>
                                            </svg>
                                            <span
                                                >Opsi Tambahan (P-IRT, Mutu &
                                                Link)</span
                                            >
                                        </div>
                                        <svg
                                            class="adv-arrow"
                                            :class="{
                                                'is-rotated': isAdvancedOpen,
                                            }"
                                            width="14"
                                            height="14"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <polyline
                                                points="6 9 12 15 18 9"
                                            ></polyline>
                                        </svg>
                                    </button>

                                    <div
                                        v-if="isAdvancedOpen"
                                        class="advanced-content-body"
                                    >
                                        <div class="field-item">
                                            <label class="field-lbl"
                                                >Nomor P-IRT / Izin Usaha</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formCustom.pirt"
                                                placeholder="Nomor izin P-IRT"
                                                class="settings-input"
                                            />
                                        </div>

                                        <div class="field-item">
                                            <label class="field-lbl"
                                                >Tampilkan di Stiker:</label
                                            >
                                            <div class="checkbox-options-stack">
                                                <label
                                                    class="checkbox-label-row"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        v-model="
                                                            formCustom.showGrade
                                                        "
                                                        class="custom-chk"
                                                    />
                                                    <span
                                                        >Sertakan Badge Mutu
                                                        (Grade)</span
                                                    >
                                                </label>
                                                <label
                                                    class="checkbox-label-row"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        v-model="
                                                            formCustom.showMoisture
                                                        "
                                                        class="custom-chk"
                                                    />
                                                    <span
                                                        >Sertakan Kadar Air
                                                        (SNI)</span
                                                    >
                                                </label>
                                            </div>
                                        </div>

                                        <div class="url-copy-box">
                                            <span class="url-label"
                                                >Link Verifikasi Pembeli:</span
                                            >
                                            <div class="url-input-group">
                                                <input
                                                    type="text"
                                                    readonly
                                                    :value="verificationUrl"
                                                    class="url-input"
                                                />
                                                <button
                                                    type="button"
                                                    class="btn-copy"
                                                    @click="copyVerificationUrl"
                                                >
                                                    <span>{{
                                                        isCopied
                                                            ? "Tersalin!"
                                                            : "Salin"
                                                    }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Footer Tindakan Cepat -->
                <div class="qr-modal-footer">
                    <div class="footer-secondary-group">
                        <button
                            type="button"
                            class="btn-secondary"
                            @click="openPublicVerificationTab"
                        >
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                                ></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                            <span>Web Verifikasi</span>
                        </button>

                        <button
                            type="button"
                            class="btn-outline"
                            @click="downloadStickerPng"
                        >
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                                ></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>{{ printButtonLabel }}</span>
                            <span>Unduh Gambar</span>
                        </button>
                    </div>

                    <button
                        type="button"
                        class="btn-primary btn-print-main"
                        @click="handleMainPrint"
                    >
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path
                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
                            ></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>{{ printButtonLabel }}</span>
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive, computed, watch, nextTick } from "vue";
import QRCode from "qrcode";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    batch: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["close"]);

const activeTemplate = ref("retail"); // 'retail', 'thermal', 'a4_grid'
const isCopied = ref(false);
const isAdvancedOpen = ref(false);
const qrDataUrl = ref("");

const qrCanvasRetail = ref(null);
const qrCanvasThermal = ref(null);

const a4GridCount = ref(8);
const a4GridCols = ref(2);

function setA4Grid(count, cols) {
    a4GridCount.value = count;
    a4GridCols.value = cols;
}

const formCustom = reactive({
    productName: "",
    netto: "1 kg",
    expiryDate: "",
    pirt: "P-IRT No. 2153202010482-26",
    showGrade: true,
    showMoisture: true,
});

const batchCode = computed(() => props.batch?.batchCode || "HNJ-20260904-01");

const verificationUrl = computed(() => {
    const origin = window.location.origin || "http://localhost:8000";
    return `${origin}/verify/${encodeURIComponent(batchCode.value)}`;
});

const finalMoisture = computed(() => {
    return (
        props.batch?.finalMoisturePercent ??
        props.batch?.currentMoisturePercent ??
        props.batch?.targetMoisturePercent ??
        12.0
    );
});

const formattedDate = computed(() => {
    const dateStr =
        props.batch?.completedAt ||
        props.batch?.startedAt ||
        props.batch?.createdAt;
    if (!dateStr)
        return new Date().toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    try {
        return new Date(dateStr).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    } catch {
        return dateStr;
    }
});

const certificateNumber = computed(() => {
    return (
        "CERT-HJ-" +
        String(batchCode.value)
            .replace(/[^A-Za-z0-9]/g, "")
            .toUpperCase()
    );
});

const currentGradeLetter = computed(() => {
    const gradeStr = String(props.batch?.qualityGrade || "A").toUpperCase();
    if (gradeStr.includes("GRADE B") || gradeStr === "B") return "B";
    if (gradeStr.includes("GRADE C") || gradeStr === "C") return "C";
    return "A";
});

const gradeBadgeClass = computed(() => {
    if (currentGradeLetter.value === "A") return "badge-grade-a";
    if (currentGradeLetter.value === "B") return "badge-grade-b";
    return "badge-grade-c";
});

const templateTitle = computed(() => {
    if (activeTemplate.value === "thermal")
        return "Struk Thermal Kasir (58/80mm)";
    if (activeTemplate.value === "a4_grid")
        return `Lembaran Kertas A4 (${a4GridCount.value} Stiker)`;
    return "Stiker Satuan Warna";
});

function handleMainPrint() {
    if (activeTemplate.value === "thermal") {
        printThermal();
    } else if (activeTemplate.value === "a4_grid") {
        printA4Grid();
    } else {
        printRetailLabel();
    }
}

const printButtonLabel = computed(() => {
    if (activeTemplate.value === "thermal") return "Cetak Struk Kasir";
    if (activeTemplate.value === "a4_grid")
        return `Cetak Lembar A4 (${a4GridCount.value} Label)`;
    return "Cetak Label Stiker";
});

// Initialize form defaults when batch changes
watch(
    () => props.batch,
    (b) => {
        if (b) {
            formCustom.productName =
                b.cropVariety || "Hanjeli Ketan Sukabumi (Grade A)";
            const d = new Date();
            d.setFullYear(d.getFullYear() + 1);
            formCustom.expiryDate = d.toLocaleDateString("id-ID", {
                month: "short",
                year: "numeric",
            });
        }
    },
    { immediate: true, deep: true },
);

async function generateQr() {
    await nextTick();
    try {
        qrDataUrl.value = await QRCode.toDataURL(verificationUrl.value, {
            width: 260,
            margin: 1,
            color: { dark: "#071E27", light: "#FFFFFF" },
            errorCorrectionLevel: "M",
        });

        if (qrCanvasRetail.value) {
            await QRCode.toCanvas(qrCanvasRetail.value, verificationUrl.value, {
                width: 130,
                margin: 1,
                color: { dark: "#071E27", light: "#FFFFFF" },
                errorCorrectionLevel: "M",
            });
        }

        if (qrCanvasThermal.value) {
            await QRCode.toCanvas(
                qrCanvasThermal.value,
                verificationUrl.value,
                {
                    width: 120,
                    margin: 1,
                    color: { dark: "#000000", light: "#FFFFFF" },
                    errorCorrectionLevel: "M",
                },
            );
        }
    } catch (err) {
        console.error("Failed to generate QR Code:", err);
    }
}

watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            setTimeout(generateQr, 60);
        }
    },
);

watch(
    () => activeTemplate.value,
    () => {
        setTimeout(generateQr, 60);
    },
);

async function copyVerificationUrl() {
    try {
        await navigator.clipboard.writeText(verificationUrl.value);
        isCopied.value = true;
        setTimeout(() => {
            isCopied.value = false;
        }, 2500);
    } catch (err) {
        console.error("Failed to copy URL:", err);
    }
}

function openPublicVerificationTab() {
    window.open(verificationUrl.value, "_blank");
}

function downloadStickerPng() {
    if (!qrDataUrl.value) return;
    const link = document.createElement("a");
    link.download = `STIKER-${batchCode.value}-${activeTemplate.value}.png`;
    link.href = qrDataUrl.value;
    link.click();
}

function printRetailLabel() {
    const printWindow = window.open("", "_blank", "width=650,height=520");
    printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Cetak Label Stiker Retail - ${batchCode.value}</title>
      ${"<" + "style>"}
        @page { size: 70mm 50mm; margin: 0; }
        * { box-sizing: border-box; }
        body { 
          margin: 0; 
          padding: 3mm; 
          font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif; 
          -webkit-print-color-adjust: exact; 
          print-color-adjust: exact;
          background: #FFFFFF;
        }
        .retail-card { 
          border: 1.5px solid #071E27; 
          border-radius: 6px; 
          padding: 6px 8px; 
          font-size: 9px; 
          display: flex; 
          flex-direction: column; 
          gap: 4px;
          height: 100%;
        }
        .sticker-top { display: flex; align-items: center; justify-content: space-between; }
        .sticker-logos { display: flex; align-items: center; gap: 6px; }
        .sticker-logo-img { width: 22px; height: 22px; object-fit: contain; }
        .sticker-brand-info { display: flex; flex-direction: column; }
        .sticker-org { font-size: 8.5px; font-weight: 800; color: #1B5E20; letter-spacing: 0.3px; }
        .sticker-sub { font-size: 6.5px; color: #64748B; font-weight: 600; }
        .sticker-badge-grade { background: #E8F5E9; color: #1B5E20; border: 1.2px solid #1B5E20; font-weight: 800; font-size: 7.5px; padding: 1px 5px; border-radius: 4px; }
        .sticker-divider { height: 1px; background: #E2E8F0; margin: 2px 0; }
        .sticker-main-content { display: flex; gap: 8px; align-items: center; }
        .sticker-qr-box { width: 56px; text-align: center; flex-shrink: 0; }
        .sticker-qr-box img { width: 52px; height: 52px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #CBD5E1; border-radius: 4px; }
        .sticker-scan-hint { font-size: 6px; font-weight: 800; color: #64748B; display: block; margin-top: 2px; }
        .sticker-details { flex: 1; min-width: 0; }
        .sticker-product-name { font-weight: 800; font-size: 9px; color: #071E27; line-height: 1.2; }
        .sticker-netto-badge { display: inline-block; font-size: 7px; font-weight: 700; color: #1B5E20; background: #E8F5E9; padding: 1px 4px; border-radius: 3px; margin-top: 2px; }
        .sticker-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2px 6px; margin-top: 3px; font-size: 7px; }
        .meta-label { color: #64748B; font-weight: 700; font-size: 6.5px; display: block; }
        .meta-value { color: #071E27; font-weight: 700; font-size: 7.5px; }
        .sticker-footer { display: flex; justify-content: space-between; align-items: center; font-size: 7px; color: #64748B; margin-top: auto; padding-top: 2px; }
        .cert-guarantee { color: #1B5E20; font-weight: 700; }
      ${"<" + "/style>"}
    </head>
    <body>
      <div class="retail-card">
        <div class="sticker-top">
          <div class="sticker-logos">
            <img src="/assets/img/hanjeli.png" alt="Logo Hanjeli" class="sticker-logo-img" />
            <div class="sticker-brand-info">
              <span class="sticker-org">DESA WISATA HANJELI</span>
              <span class="sticker-sub">WALURAN SUKABUMI &bull; STAS-RG</span>
            </div>
          </div>
          ${formCustom.showGrade ? `<div class="sticker-badge-grade">GRADE ${currentGradeLetter.value}</div>` : ""}
        </div>

        <div class="sticker-divider"></div>

        <div class="sticker-main-content">
          <div class="sticker-qr-box">
            <img src="${qrDataUrl.value}" alt="QR" />
            <span class="sticker-scan-hint">SCAN TRACEABILITY</span>
          </div>
          <div class="sticker-details">
            <div class="sticker-product-name">${formCustom.productName || "Biji Hanjeli Pilihan Super"}</div>
            ${formCustom.netto ? `<div class="sticker-netto-badge">Netto: ${formCustom.netto}</div>` : ""}
            <div class="sticker-meta-grid">
              <div>
                <span class="meta-label">NO. BATCH:</span>
                <span class="meta-value">${batchCode.value}</span>
              </div>
              ${formCustom.showMoisture ? `<div><span class="meta-label">KADAR AIR:</span><span class="meta-value" style="color:#1B5E20;">${finalMoisture.value}% (SNI)</span></div>` : ""}
              <div>
                <span class="meta-label">TGL KERING:</span>
                <span class="meta-value">${formattedDate.value}</span>
              </div>
              ${formCustom.expiryDate ? `<div><span class="meta-label">BAIK DIGUNAKAN:</span><span class="meta-value">${formCustom.expiryDate}</span></div>` : ""}
            </div>
          </div>
        </div>

        <div class="sticker-divider"></div>

        <div class="sticker-footer">
          <span class="cert-guarantee">&#10004; ${formCustom.pirt || "P-IRT & SNI Terverifikasi"}</span>
          <span style="font-family: monospace;">${certificateNumber.value}</span>
        </div>
      </div>
      ${"<" + "script>"}
        window.onload = function() { window.print(); window.close(); }
      ${"<" + "/script>"}
    </body>
    </html>
  `);
    printWindow.document.close();
}

function printThermal() {
    const printWindow = window.open("", "_blank", "width=420,height=600");
    printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Cetak Thermal - ${batchCode.value}</title>
      ${"<" + "style>"}
        @page { size: 58mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body { 
          margin: 0; 
          padding: 2mm 3mm; 
          font-family: monospace; 
          font-size: 9.5px; 
          color: #000000; 
          background: #FFFFFF; 
          line-height: 1.25;
        }
        .thermal-wrap { width: 100%; max-width: 52mm; margin: 0 auto; text-align: center; }
        .thermal-line { border-bottom: 1px dashed #000; margin: 4px 0; }
        .thermal-title { font-weight: 900; font-size: 11px; margin: 0; }
        .thermal-sub { font-size: 8px; margin: 1px 0; }
        .thermal-prod { font-weight: 800; font-size: 10px; margin: 2px 0; }
        .thermal-qr-row { display: flex; align-items: center; justify-content: center; gap: 8px; margin: 4px 0; }
        .thermal-qr-img { width: 26mm; height: 26mm; object-fit: contain; }
        .thermal-specs { text-align: left; font-size: 8.5px; }
        .thermal-foot { font-size: 8px; margin-top: 3px; }
      ${"<" + "/style>"}
    </head>
    <body>
      <div class="thermal-wrap">
        <div class="thermal-title">DESA WISATA HANJELI</div>
        <div class="thermal-sub">SMART SOLAR DRYER &bull; SUKABUMI</div>
        <div class="thermal-line"></div>
        <div class="thermal-prod">${formCustom.productName || "BIJI HANJELI KERING"}</div>
        <div style="font-size: 9px; font-weight: bold;">GRADE: ${currentGradeLetter.value} &bull; NETTO: ${formCustom.netto || "500g"}</div>
        
        <div class="thermal-qr-row">
          <img src="${qrDataUrl.value}" class="thermal-qr-img" alt="QR" />
          <div class="thermal-specs">
            <div>BATCH: <strong>${batchCode.value}</strong></div>
            <div>KA: <strong>${finalMoisture.value}% (SNI)</strong></div>
            <div>TGL: <strong>${formattedDate.value}</strong></div>
            <div>EXP: <strong>${formCustom.expiryDate}</strong></div>
          </div>
        </div>

        <div class="thermal-line"></div>
        <div class="thermal-foot">
          <div>SCAN QR UNTUK SERTIFIKAT MUTU</div>
          <div style="font-weight: bold; margin-top: 2px;">${certificateNumber.value}</div>
        </div>
      </div>
      ${"<" + "script>"}
        window.onload = function() { window.print(); window.close(); }
      ${"<" + "/script>"}
    </body>
    </html>
  `);
    printWindow.document.close();
}

function printA4Grid() {
    const printWindow = window.open("", "_blank", "width=950,height=750");

    let stickersHtml = "";
    for (let i = 0; i < a4GridCount.value; i++) {
        stickersHtml += `
      <div class="a4-sticker-card">
        <div class="a4-card-top">
          <div class="a4-brand-col">
            <span class="a4-brand-title">DESA WISATA HANJELI</span>
            <span class="a4-product-title">${formCustom.productName || "Hanjeli Ketan Sukabumi"}</span>
          </div>
          <span class="a4-grade-chip">GRADE ${currentGradeLetter.value}</span>
        </div>
        <div class="a4-card-body">
          <img src="${qrDataUrl.value}" class="a4-qr-img" alt="QR" />
          <div class="a4-details-col">
            <div class="a4-row"><span>BATCH:</span> <strong>${batchCode.value}</strong></div>
            <div class="a4-row"><span>KADAR AIR:</span> <strong>${finalMoisture.value}% (SNI)</strong></div>
            <div class="a4-row"><span>NETTO:</span> <strong>${formCustom.netto}</strong></div>
            <div class="a4-row"><span>TGL KERING:</span> <strong>${formattedDate.value}</strong></div>
            ${formCustom.expiryDate ? `<div class="a4-row"><span>EXP:</span> <strong>${formCustom.expiryDate}</strong></div>` : ""}
          </div>
        </div>
        <div class="a4-card-foot">
          <span>${formCustom.pirt || "P-IRT & SNI Terverifikasi"}</span>
          <span class="a4-cert-num">${certificateNumber.value}</span>
        </div>
      </div>
    `;
    }

    printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Lembar Stiker A4 (${a4GridCount.value} Label) - ${batchCode.value}</title>
      ${"<" + "style>"}
        @page { size: A4 portrait; margin: 8mm 8mm; }
        * { box-sizing: border-box; }
        body { 
          margin: 0; 
          padding: 0; 
          font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif; 
          -webkit-print-color-adjust: exact; 
          print-color-adjust: exact;
          background: #FFFFFF;
        }
        .a4-grid-container {
          display: grid;
          grid-template-columns: repeat(${a4GridCols.value}, 1fr);
          gap: 3.5mm;
          width: 100%;
        }
        .a4-sticker-card {
          border: 1.2px dashed #94A3B8;
          border-radius: 5px;
          padding: 2.8mm 3.2mm;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          background: #FFFFFF;
          page-break-inside: avoid;
          min-height: ${a4GridCount.value === 8 ? "58mm" : a4GridCount.value === 12 ? "42mm" : "30mm"};
        }
        .a4-card-top { 
          display: flex; 
          justify-content: space-between; 
          align-items: flex-start; 
          border-bottom: 1px solid #1B5E20; 
          padding-bottom: 1.5mm; 
        }
        .a4-brand-col { display: flex; flex-direction: column; }
        .a4-brand-title { font-size: 6.5px; font-weight: 800; color: #1B5E20; letter-spacing: 0.4px; }
        .a4-product-title { font-size: 8.5px; font-weight: 800; color: #071E27; line-height: 1.15; }
        .a4-grade-chip { background: #E8F5E9; color: #1B5E20; border: 1px solid #1B5E20; padding: 1px 4px; font-weight: 800; border-radius: 3px; font-size: 7px; }
        .a4-card-body { display: flex; gap: 3mm; align-items: center; margin: 1.5mm 0; }
        .a4-qr-img { width: 17mm; height: 17mm; object-fit: contain; flex-shrink: 0; border: 0.5px solid #CBD5E1; border-radius: 2px; }
        .a4-details-col { display: flex; flex-direction: column; gap: 0.8mm; font-size: 7px; font-family: monospace; }
        .a4-row { display: flex; gap: 3px; }
        .a4-row span { color: #64748B; font-weight: 600; }
        .a4-row strong { color: #071E27; font-weight: 800; }
        .a4-card-foot { display: flex; justify-content: space-between; font-size: 6px; color: #64748B; border-top: 1px solid #E2E8F0; padding-top: 1mm; margin-top: auto; }
        .a4-cert-num { font-family: monospace; font-weight: 700; }
      ${"<" + "/style>"}
    </head>
    <body>
      <div class="a4-grid-container">
        ${stickersHtml}
      </div>
      ${"<" + "script>"}
        window.onload = function() { window.print(); window.close(); }
      ${"<" + "/script>"}
    </body>
    </html>
  `);
    printWindow.document.close();
}
</script>

<style scoped>
.qr-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(7, 30, 39, 0.75);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 16px;
    box-sizing: border-box;
}

.qr-modal-card {
    width: 100%;
    max-width: 940px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    max-height: 92vh;
    box-sizing: border-box;
}

:global(.dark-theme) .qr-modal-card {
    background: #0b242f;
    border-color: #1e4e61;
}

.qr-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

:global(.dark-theme) .qr-modal-header {
    background: #0b242f;
    border-bottom-color: #1e4e61;
}

.header-title-flex {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(13, 99, 27, 0.1);
    border: 1px solid rgba(13, 99, 27, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.qr-modal-title {
    font-size: 1.125rem;
    font-weight: 800;
    color: #071e27;
    margin: 0;
}

:global(.dark-theme) .qr-modal-title {
    color: #f8fafc;
}

.qr-modal-subtitle {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 2px 0 0 0;
}

:global(.dark-theme) .qr-modal-subtitle {
    color: #94a3b8;
}

.close-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.close-btn:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #071e27;
}

:global(.dark-theme) .close-btn {
    border-color: #1e4e61;
    color: #94a3b8;
}

:global(.dark-theme) .close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

/* Mode Tabs */
.label-mode-tabs {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 4px 16px;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.label-mode-tabs::-webkit-scrollbar {
    display: none;
}

:global(.dark-theme) .label-mode-tabs {
    background: #071e27;
    border-bottom-color: #1e4e61;
}

.mode-tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    background: transparent;
    border: none;
    border-bottom: 2.5px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.tab-icon {
    flex-shrink: 0;
}

:global(.dark-theme) .mode-tab-btn {
    color: #94a3b8;
}

.mode-tab-btn.active {
    color: #1b5e20;
    border-bottom-color: #1b5e20;
    font-weight: 700;
}

:global(.dark-theme) .mode-tab-btn.active {
    color: #4ade80;
    border-bottom-color: #4ade80;
}

.qr-modal-body {
    padding: 20px 24px;
    overflow-y: auto;
    box-sizing: border-box;
}

.label-workspace-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 24px;
}

@media (max-width: 820px) {
    .label-workspace-grid {
        grid-template-columns: 1fr;
    }
}

.preview-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.preview-header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.preview-tag {
    font-size: 12px;
    color: #64748b;
}

:global(.dark-theme) .preview-tag {
    color: #94a3b8;
}

.label-preview-stage {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
    min-height: 280px;
    box-sizing: border-box;
}

:global(.dark-theme) .label-preview-stage {
    background: #071e27;
    border-color: #1e4e61;
}

/* Retail Sticker Styling */
.packaging-sticker-card.retail-theme {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    color: #071e27;
    border: 2px solid #071e27;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    gap: 8px;
    box-sizing: border-box;
}

.sticker-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sticker-logos {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sticker-logo-img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.sticker-brand-info {
    display: flex;
    flex-direction: column;
}

.sticker-org {
    font-size: 11.5px;
    font-weight: 800;
    color: #1b5e20;
    letter-spacing: 0.5px;
}

.sticker-sub {
    font-size: 8.5px;
    color: #64748b;
    font-weight: 600;
}

.sticker-badge-grade {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.badge-grade-a {
    background: #e8f5e9;
    color: #1b5e20;
    border: 1.5px solid #1b5e20;
}

.badge-grade-b {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1.5px solid #1d4ed8;
}

.badge-grade-c {
    background: #fef2f2;
    color: #dc2626;
    border: 1.5px solid #dc2626;
}

.sticker-divider {
    height: 1px;
    background: #e2e8f0;
}

.sticker-main-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.sticker-qr-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    width: 95px;
    flex-shrink: 0;
}

.qr-canvas {
    width: 90px !important;
    height: 90px !important;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 2px;
    background: #ffffff;
}

.sticker-scan-hint {
    font-size: 7.5px;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.5px;
}

.sticker-details {
    flex: 1;
    min-width: 0;
}

.sticker-product-name {
    font-size: 13px;
    font-weight: 800;
    color: #071e27;
    line-height: 1.25;
}

.sticker-netto-badge {
    display: inline-block;
    font-size: 9.5px;
    font-weight: 700;
    color: #1b5e20;
    background: #e8f5e9;
    padding: 2px 6px;
    border-radius: 4px;
    margin-top: 3px;
}

.sticker-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3px 6px;
    margin-top: 6px;
}

.meta-label {
    font-size: 8px;
    color: #64748b;
    font-weight: 700;
    display: block;
}

.meta-value {
    font-size: 9.5px;
    color: #071e27;
    font-weight: 700;
}

.meta-sub-sni {
    font-size: 8px;
    color: #64748b;
}

.text-green {
    color: #1b5e20;
}

.sticker-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 8.5px;
    color: #64748b;
}

.cert-guarantee {
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 700;
    color: #1b5e20;
}

/* Thermal Sticker Theme */
.packaging-sticker-card.thermal-theme {
    width: 100%;
    max-width: 320px;
    background: #ffffff;
    color: #000000;
    border: 2px dashed #000000;
    border-radius: 4px;
    padding: 12px 14px;
    font-family: monospace;
    box-sizing: border-box;
}

.thermal-header {
    text-align: center;
}

.thermal-title {
    font-weight: 900;
    font-size: 12px;
    margin: 0;
    letter-spacing: 0.5px;
}

.thermal-subtitle {
    font-size: 9.5px;
    margin: 2px 0 0 0;
    color: #334155;
}

.thermal-line {
    border-bottom: 1px dashed #000000;
    margin: 6px 0;
}

.thermal-product-block {
    text-align: center;
    margin: 4px 0;
}

.thermal-prod-name {
    font-weight: 900;
    font-size: 12px;
    margin: 0;
}

.thermal-grade-netto {
    font-size: 10px;
    font-weight: bold;
    margin-top: 2px;
}

.thermal-qr-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: 6px 0;
}

.thermal-canvas-wrap {
    flex-shrink: 0;
}

.thermal-qr-canvas {
    width: 80px !important;
    height: 80px !important;
    display: block;
}

.thermal-specs {
    font-size: 9px;
    text-align: left;
    line-height: 1.35;
}

.thermal-row {
    margin-bottom: 2px;
}

.thermal-footer {
    text-align: center;
    font-size: 8.5px;
}

.thermal-cert-code {
    font-weight: bold;
    margin-top: 2px;
}

/* A4 Grid Preview */
.a4-sheet-preview-frame {
    width: 100%;
    max-width: 440px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    box-sizing: border-box;
}

:global(.dark-theme) .a4-sheet-preview-frame {
    background: #0f281b;
    border-color: rgba(30, 58, 43, 0.8);
}

.a4-grid-info-bar {
    font-size: 11px;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 6px;
}

:global(.dark-theme) .a4-grid-info-bar {
    color: #94a3b8;
    border-bottom-color: rgba(30, 58, 43, 0.6);
}

.a4-mini-grid {
    display: grid;
    gap: 6px;
    max-height: 290px;
    overflow-y: auto;
    padding-right: 2px;
}

.a4-mini-grid.cols-2 {
    grid-template-columns: repeat(2, 1fr);
}

.a4-mini-grid.cols-3 {
    grid-template-columns: repeat(3, 1fr);
}

.a4-mini-grid.cols-4 {
    grid-template-columns: repeat(4, 1fr);
}

.a4-mini-sticker-cell {
    border: 1px dashed #94a3b8;
    border-radius: 6px;
    padding: 6px;
    background: #fafafa;
    display: flex;
    flex-direction: column;
    gap: 4px;
    overflow: hidden;
    box-sizing: border-box;
}

:global(.dark-theme) .a4-mini-sticker-cell {
    background: #0b1e14;
    border-color: #1e4e35;
}

.mini-sticker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 8.5px;
    font-weight: 800;
    color: #1b5e20;
    border-bottom: 0.5px solid #e2e8f0;
    padding-bottom: 2px;
}

:global(.dark-theme) .mini-sticker-header {
    color: #4ade80;
    border-bottom-color: #1e4e35;
}

.mini-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 75%;
}

.mini-ka {
    font-size: 8px;
    color: #1b5e20;
    font-weight: 700;
}

:global(.dark-theme) .mini-ka {
    color: #4ade80;
}

.mini-sticker-body {
    display: flex;
    gap: 6px;
    align-items: center;
    min-width: 0;
}

.mini-qr-placeholder {
    width: 36px;
    height: 36px;
    min-width: 36px;
    flex-shrink: 0;
    border: 0.5px solid #cbd5e1;
    border-radius: 3px;
    padding: 1px;
    background: #ffffff;
    box-sizing: border-box;
}

.mini-qr-img {
    width: 100% !important;
    height: 100% !important;
    max-width: 34px !important;
    max-height: 34px !important;
    object-fit: contain;
    display: block;
}

.mini-details {
    display: flex;
    flex-direction: column;
    font-size: 7.5px;
    line-height: 1.25;
    font-family: monospace;
    overflow: hidden;
    min-width: 0;
}

.mini-row {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mini-row .lbl {
    color: #64748b;
    font-size: 6.5px;
}

.mini-row .val {
    color: #071e27;
    font-size: 7.5px;
}

:global(.dark-theme) .mini-row .val {
    color: #f1f5f9;
}

.mini-sticker-foot {
    display: flex;
    justify-content: space-between;
    font-size: 6px;
    color: #64748b;
    border-top: 0.5px solid #e2e8f0;
    padding-top: 2px;
    margin-top: auto;
}

:global(.dark-theme) .mini-sticker-foot {
    border-top-color: #1e4e35;
    color: #94a3b8;
}

.mini-pirt {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 60%;
}

.mini-cert {
    font-family: monospace;
    font-weight: bold;
}

/* Customizer Form Controls */
.customizer-panel {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.panel-header-simple {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.panel-section-title {
    font-size: 15px;
    font-weight: 800;
    color: #071e27;
    margin: 0;
}

:global(.dark-theme) .panel-section-title {
    color: #f8fafc;
}

.panel-section-sub {
    font-size: 12px;
    color: #64748b;
    margin: 0;
}

:global(.dark-theme) .panel-section-sub {
    color: #94a3b8;
}

.form-fields-stack {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.field-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.field-lbl {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
}

:global(.dark-theme) .field-lbl {
    color: #cbd5e1;
}

/* Quick Netto Pills */
.netto-pills-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.netto-pill-btn {
    padding: 6px 13px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}

.netto-pill-btn:hover {
    border-color: #1b5e20;
    color: #1b5e20;
    background: #f0fdf4;
}

.netto-pill-btn.active {
    background: #1b5e20;
    color: #ffffff;
    border-color: #1b5e20;
    box-shadow: 0 2px 4px rgba(27, 94, 32, 0.2);
}

:global(.dark-theme) .netto-pill-btn {
    background: #071e27;
    border-color: #1e4e61;
    color: #cbd5e1;
}

:global(.dark-theme) .netto-pill-btn:hover {
    border-color: #16a34a;
    color: #4ade80;
    background: rgba(22, 101, 52, 0.2);
}

:global(.dark-theme) .netto-pill-btn.active {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
}

.grid-2-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.settings-input,
.settings-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
    background: #ffffff;
    color: #071e27;
    outline: none;
    box-sizing: border-box;
}

:global(.dark-theme) .settings-input,
:global(.dark-theme) .settings-select {
    background: #071e27;
    border-color: #1e4e61;
    color: #f8fafc;
}

.settings-input:focus,
.settings-select:focus {
    border-color: #1b5e20;
}

/* Collapsible Advanced Options */
.advanced-collapsible-box {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 4px;
}

:global(.dark-theme) .advanced-collapsible-box {
    border-color: #1e4e61;
}

.btn-toggle-advanced-opts {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    background: #f8fafc;
    border: none;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-toggle-advanced-opts:hover {
    background: #f1f5f9;
    color: #1b5e20;
}

:global(.dark-theme) .btn-toggle-advanced-opts {
    background: #0b242f;
    color: #94a3b8;
}

:global(.dark-theme) .btn-toggle-advanced-opts:hover {
    background: #113847;
    color: #4ade80;
}

.adv-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.adv-arrow {
    transition: transform 0.2s ease;
}

.adv-arrow.is-rotated {
    transform: rotate(180deg);
}

.advanced-content-body {
    padding: 12px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

:global(.dark-theme) .advanced-content-body {
    background: #071e27;
    border-top-color: #1e4e61;
}

.a4-settings-card {
    padding: 10px 12px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

:global(.dark-theme) .a4-settings-card {
    background: #071e27;
    border-color: #1e4e61;
}

.a4-buttons-group {
    display: flex;
    gap: 6px;
}

.size-btn {
    flex: 1;
    padding: 7px 8px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

:global(.dark-theme) .size-btn {
    background: #0b242f;
    border-color: #1e4e61;
    color: #94a3b8;
}

.size-btn.active {
    background: #1b5e20;
    color: #ffffff;
    border-color: #1b5e20;
}

:global(.dark-theme) .size-btn.active {
    background: #16a34a;
    color: #ffffff;
    border-color: #16a34a;
}

.checkbox-options-stack {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.checkbox-label-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
}

:global(.dark-theme) .checkbox-label-row {
    color: #cbd5e1;
}

.custom-chk {
    width: 15px;
    height: 15px;
    accent-color: #1b5e20;
    cursor: pointer;
}

.url-copy-box {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 2px;
}

.url-label {
    font-size: 11px;
    color: #64748b;
}

:global(.dark-theme) .url-label {
    color: #94a3b8;
}

.url-input-group {
    display: flex;
    gap: 6px;
}

.url-input {
    flex: 1;
    font-size: 11px;
    font-family: monospace;
    padding: 6px 8px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    outline: none;
}

:global(.dark-theme) .url-input {
    background: #0b242f;
    border-color: #1e4e61;
    color: #cbd5e1;
}

.btn-copy {
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 700;
    background: #1b5e20;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-copy:hover {
    background: #0a4f15;
}

/* Modal Footer */
.qr-modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 24px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
}

:global(.dark-theme) .qr-modal-footer {
    background: #0b242f;
    border-top-color: #1e4e61;
}

.footer-secondary-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-secondary,
.btn-outline,
.btn-primary,
.btn-print-main {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 9px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

:global(.dark-theme) .btn-secondary {
    background: #113847;
    border-color: #1e4e61;
    color: #cbd5e1;
}

:global(.dark-theme) .btn-secondary:hover {
    background: #164657;
    color: #ffffff;
}

.btn-outline {
    background: transparent;
    color: #1b5e20;
    border: 1.5px solid #1b5e20;
}

.btn-outline:hover {
    background: rgba(13, 99, 27, 0.08);
}

:global(.dark-theme) .btn-outline {
    color: #4ade80;
    border-color: #4ade80;
}

.btn-print-main {
    background: #1b5e20;
    color: #ffffff;
    border: none;
    box-shadow: 0 3px 10px rgba(27, 94, 32, 0.3);
}

.btn-print-main:hover {
    background: #144919;
    transform: translateY(-1px);
}

:global(.dark-theme) .btn-print-main {
    background: #16a34a;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.4);
}

:global(.dark-theme) .btn-print-main:hover {
    background: #15803d;
}

/* ==========================================================================
   RESPONSIVE MOBILE OVERRIDES FOR LABEL MODAL
   ========================================================================== */
@media (max-width: 640px) {
    .qr-modal-overlay {
        padding: 8px;
    }

    .qr-modal-card {
        border-radius: 16px;
        max-height: 95vh;
    }

    .qr-modal-header {
        padding: 12px 14px;
    }

    .header-icon-box {
        width: 32px;
        height: 32px;
    }

    .qr-modal-title {
        font-size: 1rem;
    }

    .qr-modal-subtitle {
        font-size: 0.75rem;
    }

    .label-mode-tabs {
        padding: 4px 10px;
        gap: 4px;
    }

    .mode-tab-btn {
        padding: 8px 10px;
        font-size: 11.5px;
        gap: 4px;
        flex-shrink: 0;
    }

    .qr-modal-body {
        padding: 12px 14px;
    }

    .label-workspace-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .label-preview-stage {
        padding: 10px;
        min-height: unset;
    }

    .packaging-sticker-card.retail-theme {
        width: 100%;
        max-width: 100%;
        padding: 10px 12px;
    }

    .sticker-main-content {
        gap: 8px;
    }

    .sticker-qr-box {
        width: 80px;
    }

    .qr-canvas {
        width: 75px !important;
        height: 75px !important;
    }

    .netto-pills-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 4px;
    }

    .netto-pill-btn {
        padding: 6px 2px;
        font-size: 11px;
        text-align: center;
    }

    .grid-2-fields {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .qr-modal-footer {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 10px 14px;
    }

    .footer-secondary-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        width: 100%;
    }

    .footer-secondary-group .btn-secondary,
    .footer-secondary-group .btn-outline {
        width: 100%;
        padding: 9px 8px;
        font-size: 12px;
        justify-content: center;
    }

    .btn-print-main {
        width: 100%;
        padding: 11px 16px;
        font-size: 13.5px;
        justify-content: center;
    }
}
</style>
