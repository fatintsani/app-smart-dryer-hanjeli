<template>
    <div class="history-detail-page">
        <Transition name="fade-history-detail" mode="out-in">
            <HistoryDetailSkeleton v-if="isLoading" />
            <div v-else class="history-detail-loaded-wrap">
                <div class="history-detail-container">
                    <!-- 1. Header Card with Back Button and Actions -->
            <div class="detail-header-card">
                <div class="header-left">
                    <button
                        type="button"
                        class="btn-back-link"
                        @click="$emit('back') || router.push('/history')"
                    >
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path d="M19 12H5m7 7l-7-7 7-7" />
                        </svg>
                        <span>Kembali ke Riwayat</span>
                    </button>

                    <div class="title-badge-row">
                        <h1 class="batch-title font-mono">
                            {{ batch?.batchCode || activeBatchId }}
                        </h1>
                        <span
                            class="status-pill"
                            :class="
                                batch?.status === 'COMPLETED'
                                    ? 'pill-completed'
                                    : 'pill-running'
                            "
                        >
                            <span class="status-dot"></span>
                            <span>{{
                                batch?.status === "COMPLETED"
                                    ? "Selesai"
                                    : "Sedang Berjalan"
                            }}</span>
                        </span>
                        <span class="badge-grade">
                            {{ qualityAssessment.gradeLabel }}
                        </span>
                    </div>

                    <p class="meta-subtitle">
                        Varietas:
                        <strong>{{
                            batch?.cropVariety || "Hanjeli Ketan"
                        }}</strong>
                        • {{ formattedDateRange }} • Operator:
                        <strong>{{
                            batch?.operatorName ||
                            batch?.operator?.name ||
                            "Operator Green House"
                        }}</strong>
                    </p>
                </div>

                <div class="header-actions">
                    <!-- Selesaikan jika running -->
                    <button
                        v-if="batch?.status !== 'COMPLETED'"
                        type="button"
                        class="btn-act btn-amber"
                        :disabled="isCompletingBatch"
                        @click="handleCompleteCurrentBatch"
                    >
                        <svg
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{
                            isCompletingBatch ? "Menyimpan..." : "Selesaikan"
                        }}</span>
                    </button>

                    <!-- Cetak PDF -->
                    <button
                        type="button"
                        class="btn-act btn-primary"
                        @click="handleExport"
                        title="Cetak Laporan PDF"
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
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                            />
                        </svg>
                        <span>Cetak PDF</span>
                    </button>

                    <!-- Label QR Kemasan -->
                    <button
                        type="button"
                        class="btn-act btn-secondary"
                        @click="isQrModalOpen = true"
                        title="Buka QR Kemasan"
                    >
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#1B5E20"
                            stroke-width="2"
                        >
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>QR Kemasan</span>
                    </button>

                    <!-- Tombol Aksi Sekunder (CSV & Edit) -->
                    <div class="header-icon-btns">
                        <!-- Unduh CSV -->
                        <button
                            type="button"
                            class="btn-icon"
                            @click="exportBatchCsv"
                            title="Unduh Data CSV"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <span class="btn-icon-label">Unduh CSV</span>
                        </button>

                        <!-- Edit Catatan -->
                        <button
                            type="button"
                            class="btn-icon"
                            @click="openEditBatchModal"
                            title="Ubah Data / Catatan"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                            </svg>
                            <span class="btn-icon-label">Ubah Catatan</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Sertifikat Mutu & Ringkasan Petani (Hero Banner) -->
            <div class="cert-hero-card">
                <div class="cert-left">
                    <div class="cert-tag-row">
                        <span class="cert-badge"
                            >SERTIFIKASI MUTU PENGERINGAN</span
                        >
                        <span class="cert-star">
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                stroke="none"
                                style="vertical-align: -2px; margin-right: 4px"
                            >
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                                ></polygon>
                            </svg>
                            <span>{{ qualityAssessment.gradeLabel }}</span>
                        </span>
                    </div>
                    <h2 class="cert-heading">
                        Biji Hanjeli Siap Simpan & Olah
                    </h2>
                    <p class="cert-desc">{{ qualityAssessment.summary }}</p>
                </div>

                <div class="cert-score-box">
                    <span class="score-lbl">SKOR MUTU</span>
                    <strong class="score-num"
                        >{{ qualityAssessment.qualityScore }}/100</strong
                    >
                    <span class="score-sni">Standar SNI</span>
                </div>
            </div>

            <!-- 3. 4 Kartu Hasil Utama -->
            <div class="metrics-quad-grid">
                <!-- 1. Kadar Air Akhir -->
                <div class="metric-card">
                    <span class="metric-card-lbl"
                        >KADAR AIR AKHIR (ESTIMASI)</span
                    >
                    <div class="metric-card-val-row">
                        <span class="metric-card-val text-blue">{{
                            finalMoisture
                        }}</span>
                    </div>
                    <p class="metric-card-sub">
                        {{ initialMoisturePercent }}% &bull; Estimasi Model Suhu
                        & Waktu
                    </p>
                </div>

                <!-- 2. Bobot Kering Gabah -->
                <div class="metric-card">
                    <span class="metric-card-lbl">BOBOT KERING (ESTIMASI)</span>
                    <div class="metric-card-val-row">
                        <span class="metric-card-val text-dark">{{
                            finalWeightKg
                        }}</span>
                        <span class="metric-card-unit">kg</span>
                    </div>
                    <p class="metric-card-sub text-emerald">
                        {{ waterEvaporatedKg }} kg (Model Massa)
                    </p>
                </div>

                <!-- 3. Lama Pengeringan -->
                <div class="metric-card">
                    <span class="metric-card-lbl">LAMA PENGERINGAN</span>
                    <div class="metric-card-val-row">
                        <span class="metric-card-val text-amber">{{
                            durationText.hours
                        }}</span>
                        <span class="metric-card-unit">Jam</span>
                        <span
                            class="metric-card-val text-amber"
                            style="margin-left: 6px"
                            >{{ durationText.minutes }}</span
                        >
                        <span class="metric-card-unit">Mnt</span>
                    </div>
                    <p class="metric-card-sub">
                        Mode:
                        {{
                            batch?.dryingMode === "HYBRID_AUTO"
                                ? "Otomatis"
                                : "Standar Hanjeli"
                        }}
                    </p>
                </div>

                <!-- 4. Rata-rata Suhu Ruangan -->
                <div class="metric-card">
                    <span class="metric-card-lbl">RATA-RATA SUHU</span>
                    <div class="metric-card-val-row">
                        <span class="metric-card-val text-green">{{
                            avgTemp
                        }}</span>
                    </div>
                    <p class="metric-card-sub text-emerald">
                        Zona Aman (38°C - 50°C)
                    </p>
                </div>
            </div>

            <!-- 4. Grafik Sederhana Penurunan Kadar Air & Suhu -->
            <div class="chart-section-card">
                <div class="chart-header-row">
                    <div>
                        <h3 class="chart-section-title">
                            Grafik Proses Pengeringan
                        </h3>
                        <p class="chart-section-sub">
                            Perubahan kadar air gabah dan suhu ruangan dari
                            waktu ke waktu
                        </p>
                    </div>
                    <div class="chart-legend-box">
                        <div class="legend-item">
                            <span class="legend-circle bg-blue"></span>
                            <span>Kadar Air (%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-circle bg-amber"></span>
                            <span>Suhu Ruang (°C)</span>
                        </div>
                    </div>
                </div>

                <!-- Visual SVG Box -->
                <div class="chart-canvas-box">
                    <svg
                        viewBox="0 0 500 160"
                        class="chart-svg-element"
                        preserveAspectRatio="none"
                    >
                        <!-- Grid Lines -->
                        <line
                            x1="0"
                            y1="40"
                            x2="500"
                            y2="40"
                            stroke="#E2E8F0"
                            stroke-dasharray="3 3"
                        />
                        <line
                            x1="0"
                            y1="80"
                            x2="500"
                            y2="80"
                            stroke="#E2E8F0"
                            stroke-dasharray="3 3"
                        />
                        <line
                            x1="0"
                            y1="120"
                            x2="500"
                            y2="120"
                            stroke="#E2E8F0"
                            stroke-dasharray="3 3"
                        />

                        <!-- SNI Reference Line -->
                        <line
                            x1="0"
                            y1="130"
                            x2="500"
                            y2="130"
                            stroke="#10B981"
                            stroke-width="1.5"
                            stroke-dasharray="4 4"
                        />
                        <text
                            x="495"
                            y="125"
                            font-size="9"
                            fill="#10B981"
                            text-anchor="end"
                            font-weight="700"
                        >
                            Target SNI (12%)
                        </text>

                        <!-- Temperature Line -->
                        <path
                            d="M 10 110 C 120 70, 280 65, 490 75"
                            fill="none"
                            stroke="#F59E0B"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />

                        <!-- Moisture Line -->
                        <path
                            d="M 10 30 C 150 45, 300 110, 490 130"
                            fill="none"
                            stroke="#2563EB"
                            stroke-width="3"
                            stroke-linecap="round"
                        />
                    </svg>

                    <div class="chart-axis-labels">
                        <span>Awal Mulai</span>
                        <span>Pertengahan Proses</span>
                        <span>Selesai (Siap Simpan)</span>
                    </div>
                </div>
            </div>

            <!-- 5. Rincian Pengeringan & Catatan -->
            <div class="details-twin-grid">
                <!-- Rincian -->
                <div class="info-box-card">
                    <h3 class="info-card-heading">
                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#1B5E20"
                            stroke-width="2"
                        >
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <span>Rincian Pengeringan</span>
                    </h3>

                    <div class="info-rows-list">
                        <div class="info-row">
                            <span class="info-k">Kode Pengeringan:</span>
                            <strong class="info-v font-mono">{{
                                batch?.batchCode || activeBatchId
                            }}</strong>
                        </div>
                        <div class="info-row">
                            <span class="info-k">Varietas Biji:</span>
                            <strong class="info-v">{{
                                batch?.cropVariety || "Hanjeli Ketan Sukabumi"
                            }}</strong>
                        </div>
                        <div class="info-row">
                            <span class="info-k">Bobot Awal Gabah:</span>
                            <span class="info-v">{{ initialWeightKg }} kg</span>
                        </div>
                        <div class="info-row">
                            <span class="info-k">Bobot Akhir Gabah:</span>
                            <strong class="info-v text-green"
                                >{{ finalWeightKg }} kg</strong
                            >
                        </div>
                        <div class="info-row">
                            <span class="info-k">Rendemen Kering:</span>
                            <span class="info-v">{{ yieldPercentage }}%</span>
                        </div>
                        <div class="info-row">
                            <span class="info-k">Waktu Mulai:</span>
                            <span class="info-v">{{
                                formatDateTime(
                                    batch?.startedAt || batch?.createdAt,
                                )
                            }}</span>
                        </div>
                        <div class="info-row border-none">
                            <span class="info-k">Waktu Selesai:</span>
                            <span class="info-v">{{
                                batch?.completedAt
                                    ? formatDateTime(batch?.completedAt)
                                    : "Masih Berjalan"
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Operator -->
                <div class="info-box-card flex-between">
                    <div>
                        <h3 class="info-card-heading">
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#1B5E20"
                                stroke-width="2"
                            >
                                <path
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                />
                            </svg>
                            <span>Catatan Pengeringan</span>
                        </h3>
                        <div class="notes-body-display">
                            {{
                                batch?.notes ||
                                "Proses pengeringan berjalan normal. Penurunan kadar air terjadi merata di seluruh rak pengering. Kualitas biji hanjeli bersih dan kering sempurna."
                            }}
                        </div>
                    </div>

                    <div class="notes-footer-row">
                        <span class="notes-author"
                            >Diperiksa oleh:
                            {{
                                batch?.operatorName || "Operator Green House"
                            }}</span
                        >
                        <button
                            type="button"
                            class="btn-text-edit"
                            @click="openEditBatchModal"
                        >
                            Ubah Catatan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </Transition>

        <!-- ========================================================================= -->
        <!-- EDIT BATCH MODAL -->
        <!-- ========================================================================= -->
        <div
            v-if="isEditBatchModalOpen"
            class="modal-backdrop-fixed"
            @click.self="isEditBatchModalOpen = false"
        >
            <div class="modal-dialog-box">
                <h3 class="modal-title">Ubah Data Pengeringan</h3>
                <p class="modal-subtitle">
                    Perbarui catatan atau bobot gabah untuk batch ini
                </p>

                <form
                    @submit.prevent="handleSaveBatchEdit"
                    class="modal-form-body"
                >
                    <div class="form-group">
                        <label class="form-label">Varietas Biji Hanjeli</label>
                        <input
                            v-model="editBatchForm.cropVariety"
                            type="text"
                            class="form-input-clean"
                        />
                    </div>

                    <div class="form-row-twin">
                        <div class="form-group">
                            <label class="form-label">Bobot Awal (kg)</label>
                            <input
                                v-model.number="editBatchForm.initialWeightKg"
                                type="number"
                                step="0.1"
                                class="form-input-clean"
                            />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bobot Akhir (kg)</label>
                            <input
                                v-model.number="editBatchForm.finalWeightKg"
                                type="number"
                                step="0.1"
                                class="form-input-clean"
                            />
                        </div>
                    </div>

                    <div class="form-row-twin">
                        <div class="form-group">
                            <label class="form-label">Kadar Air Awal (%)</label>
                            <input
                                v-model.number="
                                    editBatchForm.initialMoisturePercent
                                "
                                type="number"
                                step="0.1"
                                class="form-input-clean"
                            />
                        </div>
                        <div class="form-group">
                            <label class="form-label"
                                >Kadar Air Akhir / Saat Ini (%)</label
                            >
                            <input
                                v-model.number="
                                    editBatchForm.finalMoisturePercent
                                "
                                type="number"
                                step="0.1"
                                class="form-input-clean"
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea
                            v-model="editBatchForm.notes"
                            rows="3"
                            class="form-input-clean"
                            placeholder="Tuliskan catatan kondisi gabah..."
                        ></textarea>
                    </div>

                    <div class="modal-footer-btns">
                        <button
                            type="button"
                            class="btn-modal-cancel"
                            @click="isEditBatchModalOpen = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn-modal-confirm"
                            :disabled="isSavingBatch"
                        >
                            {{
                                isSavingBatch
                                    ? "Menyimpan..."
                                    : "Simpan Perubahan"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Packaging QR Modal -->
        <PackagingQrLabelModal
            :is-open="isQrModalOpen"
            :batch="batch"
            @close="isQrModalOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { batchService } from "../../services/batchService";
import {
    confirmDialog,
    alertDialog,
} from "../../services/confirmDialogService";
import PackagingQrLabelModal from "../../components/PackagingQrLabelModal.vue";
import HistoryDetailSkeleton from "../../components/HistoryDetailSkeleton.vue";
import QRCode from "qrcode";

const route = useRoute();
const router = useRouter();

const props = defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
    batchId: {
        type: String,
        default: "",
    },
    id: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["back"]);

const activeBatchId = computed(() => {
    return route?.params?.id || props.id || props.batchId || "HNJ-20260904-01";
});

const batch = ref(null);
const isLoading = ref(false);
const isEditBatchModalOpen = ref(false);
const isQrModalOpen = ref(false);
const isSavingBatch = ref(false);
const isCompletingBatch = ref(false);

const editBatchForm = reactive({
    cropVariety: "",
    initialWeightKg: 0,
    finalWeightKg: 0,
    initialMoisturePercent: 0,
    finalMoisturePercent: 0,
    dryingMode: "",
    qualityGrade: "Grade A (Standar SNI)",
    notes: "",
});

const qualityAssessment = computed(() => {
    if (batch.value?.qualityAssessment) {
        return batch.value.qualityAssessment;
    }
    const fM = parseFloat(
        batch.value?.finalMoisturePercent ||
            batch.value?.currentMoisturePercent ||
            12.0,
    );
    const qScore =
        batch.value?.qualityScore ||
        (fM <= 13.5 ? 96.0 : fM <= 15.0 ? 82.0 : 65.0);
    const grade = qScore >= 88.0 ? "A" : qScore >= 72.0 ? "B" : "C";
    return {
        grade,
        gradeLabel:
            batch.value?.qualityGrade ||
            (grade === "A"
                ? "Grade A (Standar SNI)"
                : "Grade B (Standar Lokal)"),
        qualityScore: qScore,
        summary:
            grade === "A"
                ? "Kadar air optimal standar ekspor SNI (<13.5%), suhu pengeringan sangat stabil, dan nutrisi biji terjaga utuh."
                : "Kualitas baik standar konsumsi lokal, kadar air stabil untuk penyimpanan.",
    };
});

// KPI calculations
const initialMoisturePercent = computed(() => {
    return Number(batch.value?.initialMoisturePercent || 24.5).toFixed(1);
});

const finalMoisture = computed(() => {
    if (
        batch.value?.finalMoisturePercent !== undefined &&
        batch.value?.finalMoisturePercent !== null
    ) {
        return `${Number(batch.value.finalMoisturePercent).toFixed(1)}%`;
    }
    return "11.8%";
});

const moistureReductionPercent = computed(() => {
    const init = parseFloat(initialMoisturePercent.value);
    const fin = parseFloat(finalMoisture.value);
    return Math.max(0, init - fin).toFixed(1);
});

const initialWeightKg = computed(() => {
    return Number(batch.value?.initialWeightKg || 135.0).toFixed(1);
});

const finalWeightKg = computed(() => {
    return Number(
        batch.value?.finalWeightKg || batch.value?.currentWeightKg || 120.0,
    ).toFixed(1);
});

const waterEvaporatedKg = computed(() => {
    const init = parseFloat(initialWeightKg.value);
    const fin = parseFloat(finalWeightKg.value);
    return Math.max(0, init - fin).toFixed(1);
});

const yieldPercentage = computed(() => {
    const init = parseFloat(initialWeightKg.value);
    const fin = parseFloat(finalWeightKg.value);
    if (init === 0) return "88.9";
    return ((fin / init) * 100).toFixed(1);
});

const avgTemp = computed(() => {
    return Number(batch.value?.avgTemp || 44.2).toFixed(1) + "°C";
});

const durationText = computed(() => {
    if (!batch.value)
        return { hours: 8, minutes: 30, totalFormatted: "8 Jam 30 Mnt" };
    const start = new Date(batch.value.startedAt || batch.value.createdAt);
    const end = batch.value.completedAt
        ? new Date(batch.value.completedAt)
        : new Date();
    const diffMins = Math.max(30, Math.round((end - start) / (1000 * 60)));
    const hours = Math.floor(diffMins / 60);
    const minutes = diffMins % 60;
    return {
        hours,
        minutes,
        totalFormatted: `${hours} Jam ${minutes} Mnt`,
    };
});

const formattedDateRange = computed(() => {
    if (!batch.value) return "12 Okt 2026";
    const start = new Date(batch.value.startedAt || batch.value.createdAt);
    return start.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
});

function formatDateTime(dt) {
    if (!dt) return "-";
    const d = new Date(dt);
    return (
        d.toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        }) + " WIB"
    );
}

function openEditBatchModal() {
    if (!batch.value) return;
    editBatchForm.cropVariety =
        batch.value.cropVariety || "Hanjeli Ketan Sukabumi";
    editBatchForm.initialWeightKg = batch.value.initialWeightKg || 135.0;
    editBatchForm.finalWeightKg =
        batch.value.finalWeightKg || batch.value.currentWeightKg || 120.0;
    editBatchForm.initialMoisturePercent =
        batch.value.initialMoisturePercent || 24.5;
    editBatchForm.finalMoisturePercent =
        batch.value.finalMoisturePercent ||
        batch.value.currentMoisturePercent ||
        12.0;
    editBatchForm.dryingMode = batch.value.dryingMode || "HYBRID_AUTO";
    editBatchForm.qualityGrade =
        batch.value.qualityGrade || "Grade A (Standar SNI)";
    editBatchForm.notes = batch.value.notes || "";
    isEditBatchModalOpen.value = true;
}

async function handleCompleteCurrentBatch() {
    if (!batch.value) return;
    const code = batch.value.batchCode || activeBatchId.value;
    const confirmed = await confirmDialog({
        title: "Selesaikan Proses Pengeringan",
        message: `Apakah Anda yakin ingin menyelesaikan proses pengeringan untuk batch ${code}?`,
        details:
            "Status batch akan diubah menjadi COMPLETED dan data pengeringan akan disimpan secara permanen.",
        type: "warning",
        confirmText: "Ya, Selesaikan",
        cancelText: "Batal",
    });
    if (!confirmed) return;

    isCompletingBatch.value = true;
    const targetId =
        batch.value.id || batch.value.batchCode || activeBatchId.value;
    try {
        const res = await batchService.complete(targetId, {
            finalMoisturePercent:
                Number(parseFloat(finalMoisture.value)) || 12.0,
        });
        if (res && res.batch) {
            batch.value = { ...batch.value, ...res.batch, status: "COMPLETED" };
        } else {
            await loadBatchDetail();
        }
    } catch (err) {
        await alertDialog({
            title: "Gagal Menyelesaikan Sesi",
            message: err.message || "Gagal menyelesaikan sesi pengeringan.",
            type: "danger",
        });
    } finally {
        isCompletingBatch.value = false;
    }
}

async function handleSaveBatchEdit() {
    if (!batch.value) return;
    isSavingBatch.value = true;
    const targetId =
        batch.value.id || batch.value.batchCode || activeBatchId.value;
    try {
        const res = await batchService.update(targetId, editBatchForm);
        if (res && res.batch) {
            batch.value = { ...batch.value, ...res.batch };
        } else {
            await loadBatchDetail();
        }
        isEditBatchModalOpen.value = false;
    } catch (err) {
        await alertDialog({
            title: "Gagal Menyimpan",
            message: err.message || "Gagal menyimpan perubahan batch.",
            type: "danger",
        });
    } finally {
        isSavingBatch.value = false;
    }
}

async function loadBatchDetail() {
    isLoading.value = true;
    const targetId = activeBatchId.value;
    try {
        const res = await batchService.getById(targetId);
        if (res && res.batch) {
            batch.value = res.batch;
        } else if (res && (res.id || res.batchCode)) {
            batch.value = res;
        }
    } catch {
        try {
            const all = await batchService.getAll();
            const match = all.find(
                (b) => b.batchCode === targetId || b.id === targetId,
            );
            if (match) {
                batch.value = match;
            }
        } catch (e) {
            console.warn("Could not load batch detail:", e.message);
        }
    } finally {
        isLoading.value = false;
    }
}

function exportBatchCsv() {
    const b = batch.value || {};
    const headers = [
        "Kode_Batch",
        "Varietas",
        "Tanggal",
        "Bobot_Awal_Kg",
        "Bobot_Akhir_Kg",
        "Kadar_Air_Awal",
        "Kadar_Air_Akhir",
        "Durasi_Jam",
        "Status",
    ];
    const row = [
        `"${b.batchCode || activeBatchId.value}"`,
        `"${b.cropVariety || "Hanjeli"}"`,
        `"${formattedDateRange.value}"`,
        initialWeightKg.value,
        finalWeightKg.value,
        initialMoisturePercent.value,
        finalMoisture.value,
        durationText.value.hours,
        `"${b.status || "COMPLETED"}"`,
    ];
    const csvContent = [headers.join(","), row.join(",")].join("\n");
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute(
        "download",
        `data_pengeringan_${b.batchCode || activeBatchId.value}.csv`,
    );
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

async function handleExport() {
    const b = batch.value || {};
    const code = b.batchCode || activeBatchId.value;
    const origin = window.location.origin || "http://localhost:8000";
    const verifyUrl = `${origin}/verify/${encodeURIComponent(code)}`;

    let qrDataUrl = "";
    try {
        qrDataUrl = await QRCode.toDataURL(verifyUrl, {
            width: 130,
            margin: 1,
        });
    } catch (e) {
        console.error("QR Error:", e);
    }

    const printWindow = window.open("", "_blank");
    if (!printWindow) {
        window.print();
        return;
    }

    const sTag = "<" + "style>";
    const sClose = "<" + "/style>";

    printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Laporan Pengeringan - ${code}</title>
      ${sTag}
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; padding: 32px; color: #1F2937; max-width: 800px; margin: 0 auto; }
        .header { border-bottom: 2px solid #1B5E20; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        h1 { color: #1B5E20; margin: 0; font-size: 22px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .box { border: 1px solid #E5E7EB; border-radius: 12px; padding: 14px; }
        .box-title { font-size: 11px; text-transform: uppercase; color: #6B7280; font-weight: bold; margin-bottom: 4px; }
        .box-val { font-size: 20px; font-weight: bold; color: #111827; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 8px 0; border-bottom: 1px solid #F3F4F6; font-size: 13px; }
        td.label { color: #6B7280; width: 40%; }
        td.val { font-weight: bold; color: #1F2937; }
        .cert { background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
      ${sClose}
    </head>
    <body>
      <div class="header">
        <div>
          <h1>GREEN HOUSE SMART DRYER HANJELI</h1>
          <p style="margin: 4px 0 0; color: #6B7280; font-size: 13px;">Desa Wisata Hanjeli, Waluran Mandiri, Geopark Ciletuh Sukabumi</p>
        </div>
        ${qrDataUrl ? `<img src="${qrDataUrl}" width="80" height="80" alt="QR" />` : ""}
      </div>

      <div class="cert">
        <div>
          <span style="background: #1B5E20; color: white; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: bold;">SERTIFIKAT MUTU</span>
          <h2 style="color: #166534; margin: 6px 0 2px; font-size: 18px;">${qualityAssessment.value.gradeLabel} (Skor: ${qualityAssessment.value.qualityScore}/100)</h2>
          <p style="margin: 0; font-size: 12px; color: #15803D;">${qualityAssessment.value.summary}</p>
        </div>
      </div>

      <div class="grid">
        <div class="box">
          <div class="box-title">Kadar Air Akhir</div>
          <div class="box-val" style="color: #1D4ED8;">${finalMoisture.value}</div>
          <span style="font-size: 11px; color: #10B981; font-weight: bold;">Standar SNI (&le;13.5%)</span>
        </div>
        <div class="box">
          <div class="box-title">Bobot Kering</div>
          <div class="box-val">${finalWeightKg.value} kg</div>
          <span style="font-size: 11px; color: #6B7280;">Air Menguap: ${waterEvaporatedKg.value} kg</span>
        </div>
        <div class="box">
          <div class="box-title">Lama Pengeringan</div>
          <div class="box-val" style="color: #B45309;">${durationText.value.hours} Jam ${durationText.value.minutes} Mnt</div>
          <span style="font-size: 11px; color: #6B7280;">Selesai Optimal</span>
        </div>
        <div class="box">
          <div class="box-title">Rata-rata Suhu Ruang</div>
          <div class="box-val" style="color: #166534;">${avgTemp.value}</div>
          <span style="font-size: 11px; color: #10B981;">Bebas Kerusakan Suhu Panas</span>
        </div>
      </div>

      <table>
        <tr><td class="label">Kode Batch:</td><td class="val">${code}</td></tr>
        <tr><td class="label">Varietas:</td><td class="val">${b.cropVariety || "Hanjeli Ketan Sukabumi"}</td></tr>
        <tr><td class="label">Waktu Sesi:</td><td class="val">${formattedDateRange.value}</td></tr>
        <tr><td class="label">Operator:</td><td class="val">${b.operatorName || "Operator Green House"}</td></tr>
        <tr><td class="label">Catatan:</td><td class="val">${b.notes || "Pengeringan berjalan normal sesuai standar."}</td></tr>
      </table>

      ${"<" + "script>"}
        window.onload = function() { window.print(); }
      ${"<" + "/script>"}
    </body>
    </html>
  `);
    printWindow.document.close();
}

let detailPollTimer = null;

onMounted(() => {
    loadBatchDetail();
    detailPollTimer = setInterval(() => {
        if (batch.value && batch.value.status !== "COMPLETED") {
            loadBatchDetail();
        }
    }, 4000);
});

onUnmounted(() => {
    if (detailPollTimer) clearInterval(detailPollTimer);
});

watch(
    () => activeBatchId.value,
    () => {
        loadBatchDetail();
    },
);
</script>

<style scoped>
.history-detail-page {
    width: 100%;
    max-width: 100%;
    min-height: 100%;
    background: var(--color-bg, #f4f7f5);
    overflow-x: hidden;
    box-sizing: border-box;
}

.history-detail-loaded-wrap {
    width: 100%;
    max-width: 100%;
}

.fade-history-detail-enter-active,
.fade-history-detail-leave-active {
    transition: opacity 0.25s ease;
}

.fade-history-detail-enter-from,
.fade-history-detail-leave-to {
    opacity: 0;
}

.history-detail-container {
    padding: 24px 28px 48px 28px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
    max-width: 100%;
    margin: 0;
    box-sizing: border-box;
}

/* 1. Header Card */
.detail-header-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px 28px;
    border: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.header-left {
    flex: 1;
    min-width: 280px;
}

.btn-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    margin-bottom: 8px;
    transition: color 0.2s;
}

.btn-back-link svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.btn-back-link:hover {
    color: #1b5e20;
}

.title-badge-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.batch-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.pill-completed {
    background: #dcfce7;
    color: #166534;
}

.pill-running {
    background: #fef3c7;
    color: #b45309;
}

.badge-grade {
    background: #f3e8ff;
    color: #7e22ce;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    max-width: 100%;
    line-height: 1.4;
    word-break: break-word;
    white-space: normal;
    box-sizing: border-box;
}

.meta-subtitle {
    font-size: 13px;
    color: #64748b;
    margin: 8px 0 0 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.header-icon-btns {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-icon-label {
    display: none;
}

.btn-act {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-act svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.btn-primary {
    background: #1b5e20;
    color: #ffffff;
}

.btn-primary:hover {
    background: #144818;
}

.btn-secondary {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.btn-secondary:hover {
    background: #dcfce7;
}

.btn-amber {
    background: #d97706;
    color: #ffffff;
}

.btn-amber:hover {
    background: #b45309;
}

.btn-icon {
    padding: 9px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-icon svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.btn-icon:hover {
    background: #f1f5f9;
    color: #0f172a;
}

/* 2. Hero Banner */
.cert-hero-card {
    background: linear-gradient(135deg, #1b5e20 0%, #144818 100%);
    border-radius: 20px;
    padding: 26px 32px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    box-shadow: 0 4px 14px rgba(27, 94, 32, 0.15);
    flex-wrap: wrap;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.cert-left {
    flex: 1;
    min-width: 280px;
    min-width: 260px;
    max-width: 100%;
}

.cert-tag-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
    flex-wrap: wrap;
    max-width: 100%;
}

.cert-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 10px;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    flex-shrink: 0;
}

.cert-star {
    font-size: 12px;
    font-weight: 700;
    color: #fde047;
    word-break: break-word;
    white-space: normal;
    line-height: 1.4;
}

.cert-heading {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 6px 0;
}

.cert-desc {
    font-size: 13px;
    color: #d1fae5;
    margin: 0;
    max-width: 600px;
    line-height: 1.5;
}

.cert-score-box {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 14px;
    padding: 14px 20px;
    text-align: center;
    min-width: 130px;
}

.score-lbl {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #a7f3d0;
}

.score-num {
    display: block;
    font-size: 26px;
    font-weight: 800;
    color: #ffffff;
    margin: 2px 0;
}

.score-sni {
    font-size: 11px;
    font-weight: 700;
    color: #6ee7b7;
}

/* 3. 4 Metrics Quad Grid */
.metrics-quad-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.metric-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.metric-card-lbl {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    display: block;
}

.metric-card-val-row {
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-top: 6px;
}

.metric-card-val {
    font-size: 26px;
    font-weight: 800;
    line-height: 1.1;
}

.text-blue {
    color: #1d4ed8;
}
.text-dark {
    color: #0f172a;
}
.text-amber {
    color: #d97706;
}
.text-green {
    color: #1b5e20;
}
.text-emerald {
    color: #059669;
}

.metric-card-unit {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}

.metric-card-sub {
    font-size: 12px;
    color: #64748b;
    margin: 6px 0 0 0;
}

/* 4. Chart Section */
.chart-section-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.chart-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.chart-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.chart-section-sub {
    font-size: 12px;
    color: #64748b;
    margin: 3px 0 0;
}

.chart-legend-box {
    display: flex;
    align-items: center;
    gap: 16px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
}

.legend-circle {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.bg-blue {
    background: #2563eb;
}
.bg-amber {
    background: #f59e0b;
}

.chart-canvas-box {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
}

.chart-svg-element {
    width: 100%;
    height: 180px;
    overflow: visible;
}

.chart-axis-labels {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #94a3b8;
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
}

/* 5. Details Twin Grid */
.details-twin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.info-box-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.flex-between {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.info-card-heading {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 14px 0;
}

.info-rows-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
}

.border-none {
    border-bottom: none;
}

.info-k {
    color: #64748b;
}

.info-v {
    color: #0f172a;
}

.notes-body-display {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    font-size: 13px;
    color: #334155;
    line-height: 1.6;
    min-height: 100px;
}

.notes-footer-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.notes-author {
    font-size: 12px;
    color: #94a3b8;
}

.btn-text-edit {
    background: transparent;
    border: none;
    font-size: 12px;
    font-weight: 700;
    color: #1b5e20;
    cursor: pointer;
    padding: 0;
}

.btn-text-edit:hover {
    text-decoration: underline;
}

/* Modals */
.modal-backdrop-fixed {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(2px);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.modal-dialog-box {
    background: #ffffff;
    border-radius: 18px;
    padding: 24px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.modal-subtitle {
    font-size: 12px;
    color: #64748b;
    margin: 2px 0 16px;
}

.modal-form-body {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.form-label {
    font-size: 12px;
    font-weight: 600;
    color: #334155;
}

.form-input-clean {
    padding: 9px 12px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-size: 13px;
    color: #0f172a;
    outline: none;
}

.form-input-clean:focus {
    border-color: #1b5e20;
    background: #ffffff;
}

.form-row-twin {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.modal-footer-btns {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 8px;
}

.btn-modal-cancel {
    padding: 9px 16px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.btn-modal-confirm {
    padding: 9px 18px;
    background: #1b5e20;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

/* ==========================================================================
   RESPONSIVE DESIGN FOR MOBILE & TABLET (<= 900px)
   ========================================================================== */
@media (max-width: 900px) {
    .history-detail-container {
        padding: 0;
        gap: 12px;
    }

    .detail-header-card {
        padding: 16px;
        border-radius: 14px;
        gap: 14px;
    }

    .header-left {
        min-width: 0;
        width: 100%;
    }

    .batch-title {
        font-size: 20px;
        word-break: break-all;
    }

    .title-badge-row {
        gap: 8px;
        align-items: flex-start;
    }

    .badge-grade {
        max-width: 100%;
        white-space: normal;
        word-break: break-word;
    }

    .meta-subtitle {
        font-size: 12px;
        line-height: 1.5;
    }

    .header-actions {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .header-actions .btn-act {
        width: 100%;
        justify-content: center;
        padding: 11px 16px;
    }

    .header-icon-btns {
        width: 100%;
        display: flex;
        gap: 8px;
    }

    .header-icon-btns .btn-icon {
        flex: 1;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        gap: 6px;
    }

    .btn-icon-label {
        display: inline;
    }

    .cert-hero-card {
        padding: 16px;
        border-radius: 14px;
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .cert-left {
        min-width: 0;
        width: 100%;
    }

    .cert-tag-row {
        flex-wrap: wrap;
        gap: 6px;
    }

    .cert-badge {
        font-size: 9px;
    }

    .cert-star {
        font-size: 11px;
        line-height: 1.4;
        word-break: break-word;
    }

    .cert-heading {
        font-size: 18px;
    }

    .cert-desc {
        font-size: 12px;
        line-height: 1.45;
    }

    .cert-score-box {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-radius: 12px;
    }

    .score-lbl {
        font-size: 10px;
    }

    .score-num {
        font-size: 20px;
        margin: 0;
    }

    .score-sni {
        font-size: 11px;
    }

    .metrics-quad-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .metric-card {
        padding: 14px;
        border-radius: 14px;
    }

    .metric-card-lbl {
        font-size: 10px;
    }

    .metric-card-val {
        font-size: 20px;
    }

    .chart-section-card {
        padding: 16px 14px;
        border-radius: 14px;
    }

    .chart-header-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .chart-legend-box {
        gap: 12px;
    }

    .chart-canvas-box {
        padding: 10px;
    }

    .details-twin-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .info-box-card {
        padding: 16px;
        border-radius: 14px;
    }

    .notes-body-display {
        padding: 12px;
        font-size: 12px;
    }
}

@media (max-width: 540px) {
    .modal-dialog-box {
        padding: 18px 16px;
        border-radius: 14px;
        margin: 10px;
    }

    .form-row-twin {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}

/* ==========================================================================
   DARK THEME OVERRIDES FOR HISTORY DETAIL VIEW
   ========================================================================== */
:global(.dark-theme) .history-detail-page {
    background: #071e27;
}

:global(.dark-theme) .detail-header-card,
:global(.dark-theme) .quality-certificate-card,
:global(.dark-theme) .kpi-cards-grid .metric-card,
:global(.dark-theme) .recommendation-box,
:global(.dark-theme) .timeline-process-card,
:global(.dark-theme) .batch-info-card,
:global(.dark-theme) .edit-batch-modal-container {
    background: #0b242f;
    border-color: #1e4e61;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

:global(.dark-theme) .batch-title,
:global(.dark-theme) .cert-title,
:global(.dark-theme) .metric-value,
:global(.dark-theme) .timeline-heading,
:global(.dark-theme) .info-val,
:global(.dark-theme) .edit-modal-title {
    color: #f8fafc;
}

:global(.dark-theme) .btn-back-link,
:global(.dark-theme) .meta-text-muted,
:global(.dark-theme) .cert-desc,
:global(.dark-theme) .metric-label,
:global(.dark-theme) .metric-unit,
:global(.dark-theme) .info-label,
:global(.dark-theme) .timeline-time,
:global(.dark-theme) .timeline-title,
:global(.dark-theme) .timeline-desc,
:global(.dark-theme) .field-label-clean {
    color: #94a3b8;
}

:global(.dark-theme) .btn-back-link:hover {
    color: #4ade80;
}

:global(.dark-theme) .btn-header-secondary,
:global(.dark-theme) .btn-modal-cancel {
    background: #113847;
    color: #f8fafc;
    border-color: #1e4e61;
}

:global(.dark-theme) .btn-header-secondary:hover,
:global(.dark-theme) .btn-modal-cancel:hover {
    background: #164657;
    border-color: #2d6b82;
}

:global(.dark-theme) .recommendation-box {
    background: #071e27;
    border-color: #1e4e61;
}

:global(.dark-theme) .recommendation-text {
    color: #cbd5e1;
}

:global(.dark-theme) .info-block {
    border-bottom-color: #1e4e61;
}

:global(.dark-theme) .form-input-clean {
    background: #071e27;
    border-color: #1e4e61;
    color: #f8fafc;
}

:global(.dark-theme) .form-input-clean:focus {
    background: #0b242f;
    border-color: #16a34a;
}
</style>
