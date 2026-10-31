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
                                                selectedPreset ===
                                                'biji_hanjeli',
                                        }"
                                        @click="applyPreset('biji_hanjeli')"
                                    >
                                        <span
                                            >Standar Hanjeli (Target 12%
                                            SNI)</span
                                        >
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-preset"
                                        :class="{
                                            active: selectedPreset === 'custom',
                                        }"
                                        @click="selectedPreset = 'custom'"
                                    >
                                        <span>Kustom / Manual</span>
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
                                            >Kapasitas ruang pengering maksimal
                                            200 kg.</span
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
                                            >Umumnya gabah panen basah berkisar
                                            22% - 28%.</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 2: Cara Pengeringan (Mode) -->
                            <div class="form-card-section">
                                <h3 class="section-heading">
                                    2. Pilih Cara Pengeringan
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

                            <!-- Catatan Tambahan (Opsional) -->
                            <div class="form-card-section">
                                <h3 class="section-heading">
                                    3. Catatan Tambahan (Opsional)
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
                                    Dihitung otomatis berdasarkan data gabah
                                    Anda.
                                </p>

                                <div class="summary-stats-list">
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
                                            }}% (Standar SNI)</strong
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
                                    <div class="stat-line highlight-line">
                                        <span class="lbl"
                                            >Estimasi Lama Waktu:</span
                                        >
                                        <strong class="val text-amber"
                                            >~{{
                                                estimatedDurationHours
                                            }}
                                            Jam</strong
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
                                        :disabled="isSubmitting"
                                    >
                                        <span v-if="!isSubmitting"
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
import { ref, reactive, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { batchService } from "../../services/batchService";
import { confirmDialog } from "../../services/confirmDialogService";
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

onMounted(async () => {
    await checkActiveBatch();
    isInitialLoading.value = false;
});

const isHardwareConnected = computed(() => {
    return connectionManager.state.isHardwareActive;
});

function openHardwareSetup() {
    isWifiSetupOpen.value = true;
}

const formData = reactive({
    cropVariety: "Biji Hanjeli Sukabumi",
    operatorName: "Operator Green House",
    initialWeightKg: 100.0,
    initialMoisturePercent: 24.0,
    targetMoisturePercent: 12.0,
    dryingMode: "HYBRID_SOLAR_ELECTRIC",
    maxTempLimit: 50.0,
    notes: "Pengeringan standar suhu 48°C - 50°C target SNI 12%.",
});

function applyPreset(presetKey) {
    selectedPreset.value = presetKey;
    if (presetKey === "biji_hanjeli") {
        formData.cropVariety = "Biji Hanjeli Sukabumi";
        formData.initialWeightKg = 100.0;
        formData.initialMoisturePercent = 24.0;
        formData.targetMoisturePercent = 12.0;
        formData.dryingMode = "HYBRID_SOLAR_ELECTRIC";
        formData.maxTempLimit = 50.0;
        formData.notes =
            "Standar pengeringan biji hanjeli target mutu SNI (≤ 12.0%).";
    }
}

// Perhitungan Hasil Praktis
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

const estimatedDurationHours = computed(() => {
    const m0 = formData.initialMoisturePercent || 24;
    const me = formData.targetMoisturePercent || 12;
    const drop = Math.max(1, m0 - me);
    const rate = formData.dryingMode === "ELECTRIC_BOOSTER" ? 2.0 : 1.5;
    return (drop / rate).toFixed(1);
});

async function handleStart() {
    const confirmed = await confirmDialog({
        title: "Mulai Pengeringan Gabah?",
        message: `Mulai pengeringan ${formData.cropVariety} (${formData.initialWeightKg} kg) dengan target kadar air ${formData.targetMoisturePercent}%?`,
        details:
            "Pastikan gabah sudah diratakan di rak pengering sebelum memulai.",
        type: "success",
        confirmText: "Mulai Sekarang",
        cancelText: "Cek Kembali",
    });

    if (!confirmed) return;

    isSubmitting.value = true;
    try {
        const payload = {
            cropVariety: formData.cropVariety || "Biji Hanjeli",
            operatorName: formData.operatorName || "Operator Green House",
            initialWeightKg: Number(formData.initialWeightKg) || 100,
            initialMoisturePercent:
                Number(formData.initialMoisturePercent) || 24,
            targetMoisturePercent:
                Number(formData.targetMoisturePercent) || 12.0,
            dryingMode: formData.dryingMode || "HYBRID_SOLAR_ELECTRIC",
            notes: formData.notes || "Sesi pengeringan aktif baru.",
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
</style>
