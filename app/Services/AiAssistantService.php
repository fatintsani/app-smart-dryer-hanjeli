<?php

namespace App\Services;

use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use App\Models\Telemetry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantService
{
    protected DryingPredictionService $predictionService;

    public function __construct(DryingPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }
    /**
     * Generate AI answer based on user prompt, conversation history, and real-time IoT greenhouse context.
     */
    public function generateAnswer(string $userPrompt, array $history = [], string $lang = 'id'): array
    {
        $context = $this->buildRealtimeContext();
        $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $model = config('services.gemini.model', 'gemini-3.6-flash');

        // Auto-detect English from prompt or lang param
        $isEnglish = ($lang === 'en') || $this->detectEnglish($userPrompt);
        $activeLang = $isEnglish ? 'en' : 'id';

        if (!empty($apiKey)) {
            try {
                $response = $this->callGeminiApi($apiKey, $model, $userPrompt, $history, $context, $activeLang);
                if ($response['success']) {
                    return [
                        'success' => true,
                        'source' => 'gemini-llm',
                        'reply' => $response['text'],
                        'contextSnapshot' => $context['summary'],
                        'quickActions' => $this->generateFollowUpActions($userPrompt, $context, $activeLang),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini AI API Error, falling back to heuristic engine: ' . $e->getMessage());
            }
        }

        // Fallback to domain-specific heuristic agronomy engine
        $fallbackReply = $this->generateHeuristicReply($userPrompt, $context, $activeLang);

        return [
            'success' => true,
            'source' => 'heuristic-agro-engine',
            'reply' => $fallbackReply,
            'contextSnapshot' => $context['summary'],
            'quickActions' => $this->generateFollowUpActions($userPrompt, $context, $activeLang),
        ];
    }

    /**
     * Detect if text is predominantly English
     */
    protected function detectEnglish(string $text): bool
    {
        $englishKeywords = ['what', 'how', 'when', 'why', 'is', 'are', 'can', 'should', 'batch', 'status', 'humidity', 'temperature', 'drying', 'time', 'estimate', 'fan', 'heater', 'safe', 'moisture', 'explain', 'recommend'];
        $lower = strtolower($text);
        $matches = 0;
        foreach ($englishKeywords as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $lower)) {
                $matches++;
            }
        }
        return $matches >= 2;
    }

    /**
     * Build comprehensive live context from Database & IoT Telemetry.
     */
    public function buildRealtimeContext(): array
    {
        // 1. Active Batch
        $activeBatch = Batch::whereIn('status', ['ACTIVE', 'PAUSED'])
            ->orderBy('created_at', 'desc')
            ->first();

        // If no active batch, get latest batch
        $latestBatch = $activeBatch ?: Batch::orderBy('created_at', 'desc')->first();

        // 2. Latest Telemetry
        $latestTelemetry = Telemetry::orderBy('recorded_at', 'desc')->first();
        $recentTelemetries = Telemetry::orderBy('recorded_at', 'desc')->take(6)->get();

        // 3. Actuator State
        $actuator = ActuatorState::latest()->first();

        // 4. Active Alerts
        $activeAlerts = SystemAlert::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // 5. System Settings
        $setting = SystemSetting::first();

        // Compute trends
        $dryingRate = null;
        if ($recentTelemetries->count() >= 2) {
            $first = $recentTelemetries->last();
            $last = $recentTelemetries->first();
            $timeDiffHours = Carbon::parse($first->recorded_at)->diffInMinutes(Carbon::parse($last->recorded_at)) / 60;
            if ($timeDiffHours > 0 && $first->grain_moisture && $last->grain_moisture) {
                $moistureDiff = $first->grain_moisture - $last->grain_moisture;
                $dryingRate = round($moistureDiff / $timeDiffHours, 2); // % drop per hour
            }
        }

        // Predictive ETA & Weather from DryingPredictionService
        $prediction = null;
        try {
            $prediction = $this->predictionService->predictBatchEta($latestBatch?->id);
        } catch (\Throwable $e) {
            // silent fallback
        }

        $summary = [
            'hasActiveBatch' => (bool)$activeBatch,
            'batchCode' => $latestBatch?->batch_code ?? 'TIDAK ADA',
            'cropVariety' => $latestBatch?->crop_variety ?? 'Hanjeli Ketan Sukabumi',
            'batchStatus' => $latestBatch?->status ?? 'IDLE',
            'initialWeightKg' => $latestBatch?->initial_weight_kg ?? 0,
            'currentWeightKg' => $latestBatch?->current_weight_kg ?? ($latestTelemetry?->weight_kg ?? 0),
            'initialMoisture' => $latestBatch?->initial_moisture_percent ?? 0,
            'currentMoisture' => $latestTelemetry?->grain_moisture ?? ($latestBatch?->current_moisture_percent ?? 0),
            'targetMoisture' => $latestBatch?->target_moisture_percent ?? ($setting?->target_moisture_default ?? 14.0),
            'tempInternal' => $latestTelemetry?->temp_internal ?? 42.5,
            'tempExternal' => $latestTelemetry?->temp_external ?? 30.2,
            'humidityInternal' => $latestTelemetry?->humidity_internal ?? 58.0,
            'humidityExternal' => $latestTelemetry?->humidity_external ?? 74.5,
            'solarRadiation' => $latestTelemetry?->solar_radiation ?? 620.0,
            'exhaustFanStatus' => (bool)($actuator?->exhaust_fan_status ?? false),
            'exhaustFanSpeed' => $actuator?->exhaust_fan_speed ?? 0,
            'heaterStatus' => (bool)($actuator?->aux_heater_status ?? false),
            'heaterLevel' => $actuator?->aux_heater_level ?? 0,
            'circFanStatus' => (bool)($actuator?->circ_fan_status ?? true),
            'activeAlertsCount' => $activeAlerts->count(),
            'maxSafeTemp' => $setting?->max_safe_temp ?? 55.0,
            'minSafeTemp' => $setting?->min_safe_temp ?? 35.0,
            'dryingRatePerHour' => $prediction['dryingRatePerHour'] ?? $dryingRate ?? 1.2,
            'estimatedCompletionFormatted' => $prediction['estimatedCompletionFormatted'] ?? 'Belum terhitung',
            'estimatedCompletionTimeOnly' => $prediction['estimatedCompletionTimeOnly'] ?? '-',
            'remainingDuration' => $prediction['formattedRemaining'] ?? '-',
            'aiConfidenceScore' => $prediction['confidenceScore'] ?? 90,
            'weatherSummary' => ($prediction['weatherContext']['conditionText'] ?? 'Cerah') . ' (' . ($prediction['weatherContext']['temperature'] ?? 30) . '°C, RH ' . ($prediction['weatherContext']['humidity'] ?? 65) . '%)',
            'weatherImpact' => $prediction['weatherContext']['dryingImpact'] ?? 'Optimal',
            'timestamp' => now()->translatedFormat('d F Y, H:i:s T'),
        ];

        return [
            'summary' => $summary,
            'batch' => $latestBatch,
            'telemetry' => $latestTelemetry,
            'actuator' => $actuator,
            'alerts' => $activeAlerts,
            'prediction' => $prediction,
        ];
    }

    /**
     * Get dynamic suggested questions for quick actions.
     */
    public function getDynamicSuggestions(string $lang = 'id'): array
    {
        $context = $this->buildRealtimeContext();
        $s = $context['summary'];
        $isEn = ($lang === 'en');

        $suggestions = [];

        if ($s['hasActiveBatch']) {
            $suggestions[] = [
                'id' => 'eval_batch',
                'icon' => 'bolt',
                'title' => $isEn ? 'Evaluate Current Batch' : 'Evaluasi Batch Saat Ini',
                'prompt' => $isEn 
                    ? "How is the drying evaluation for batch {$s['batchCode']} right now? Is it progressing optimally?"
                    : "Bagaimana evaluasi kondisi pengeringan batch {$s['batchCode']} saat ini? Apakah sudah berjalan optimal?",
            ];

            $suggestions[] = [
                'id' => 'eta_drying',
                'icon' => 'clock',
                'title' => $isEn ? 'Estimated Completion Time (ETA)' : 'Estimasi Waktu Selesai (ETA)',
                'prompt' => $isEn
                    ? "Based on the current moisture trend ({$s['currentMoisture']}%) towards the target {$s['targetMoisture']}%, what is the estimated completion time?"
                    : "Berdasarkan tren kadar air sekarang ({$s['currentMoisture']}%) menuju target {$s['targetMoisture']}%, berapa estimasi waktu pengeringan selesai?",
            ];

            if ($s['humidityInternal'] > 65 || $s['humidityExternal'] > 75) {
                $suggestions[] = [
                    'id' => 'humidity_warning',
                    'icon' => 'cloud-rain',
                    'title' => $isEn ? 'High Humidity Strategy' : 'Strategi Kelembaban Tinggi',
                    'prompt' => $isEn
                        ? "Air humidity is currently high (Internal RH {$s['humidityInternal']}%, External RH {$s['humidityExternal']}%). What is the best action for exhaust fan and heater?"
                        : "Kelembaban udara sedang tinggi (RH dalam {$s['humidityInternal']}%, luar {$s['humidityExternal']}%). Apa tindakan terbaik untuk kipas exhaust dan heater?",
                ];
            } else {
                $suggestions[] = [
                    'id' => 'energy_opt',
                    'icon' => 'bulb',
                    'title' => $isEn ? 'Energy & Fan Optimization' : 'Optimasi Energi & Kipas',
                    'prompt' => $isEn
                        ? "Current solar radiation is {$s['solarRadiation']} W/m² and chamber temp is {$s['tempInternal']}°C. Can I save auxiliary heater/fan energy?"
                        : "Radiasi matahari saat ini {$s['solarRadiation']} W/m² dan suhu ruang {$s['tempInternal']}°C. Apakah saya bisa menghemat daya pemanas/kipas?",
                ];
            }

            $suggestions[] = [
                'id' => 'quality_check',
                'icon' => 'shield',
                'title' => $isEn ? 'Quality & Mold Risk Check' : 'Cek Risiko Mutu & Jamur',
                'prompt' => $isEn
                    ? "Is there any risk to grain structure or mold formation with the current temperature and humidity profile?"
                    : "Apakah ada risiko kerusakan struktur biji hanjeli atau pertumbuhan jamur dengan profil suhu dan kelembaban saat ini?",
            ];
        } else {
            $suggestions[] = [
                'id' => 'new_batch_guide',
                'icon' => 'sprout',
                'title' => $isEn ? 'New Batch Startup Guide' : 'Panduan Mulai Batch Baru',
                'prompt' => $isEn
                    ? "I want to start a new Hanjeli drying batch. What are the recommended temperature setpoint, target moisture, and duration?"
                    : "Saya ingin memulai batch pengeringan baru untuk biji Hanjeli. Berapa setpoint suhu awal, target kadar air, dan durasi yang direkomendasikan?",
            ];
            $suggestions[] = [
                'id' => 'microclimate_check',
                'icon' => 'thermometer',
                'title' => $isEn ? 'Greenhouse Climate Readiness' : 'Kesiapan Ruang Greenhouse',
                'prompt' => $isEn
                    ? "Check the greenhouse microclimate readiness right now (Temp: {$s['tempInternal']}°C, RH: {$s['humidityInternal']}%). Is it ready for drying?"
                    : "Cek kondisi mikroklimat ruang greenhouse saat ini (Suhu: {$s['tempInternal']}°C, RH: {$s['humidityInternal']}%). Apakah sudah siap digunakan?",
            ];
            $suggestions[] = [
                'id' => 'hanjeli_standard',
                'icon' => 'book',
                'title' => $isEn ? 'Hanjeli Quality Standards' : 'Standar Mutu Biji Hanjeli',
                'prompt' => $isEn
                    ? "Explain safe moisture standards and quality parameters for post-harvest Hanjeli grain packaging."
                    : "Jelaskan standar kadar air aman dan parameter mutu biji hanjeli untuk pascapanen siap kemas UMKM.",
            ];
        }

        return [
            'hasActiveBatch' => $s['hasActiveBatch'],
            'batchCode' => $s['batchCode'],
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Call Google Gemini API with system instructions and contextual grounding.
     */
    protected function callGeminiApi(string $apiKey, string $model, string $userPrompt, array $history, array $context, string $lang = 'id'): array
    {
        $s = $context['summary'];
        $langInstruction = ($lang === 'en')
            ? "CRITICAL LANGUAGE DIRECTIVE: The user language is English. You MUST respond completely in clear, natural English. Format output with clean Markdown."
            : "PETUNJUK BAHASA: Pengguna menggunakan Bahasa Indonesia. Anda HARUS merespons secara penuh dalam Bahasa Indonesia yang baik dan profesional. Format output dengan Markdown rapi.";

        $systemInstruction = <<<TEXT
You are "Hanjeli AI Copilot", an expert agronomy and IoT automation assistant for the Smart Room Dryer Greenhouse at Desa Wisata Hanjeli, Sukabumi.
Your core tasks:
1. Help operators monitor and optimize the drying process of Hanjeli grains (Coix lacryma-jobi).
2. Provide scientific and practical insights based on live IoT telemetry.
3. Guidelines:
   - Safe thermal range: 40°C - 50°C (Absolute max: 55°C).
   - Safe storage moisture: 12% - 14% (Wet harvest initial: 24% - 32%).
   - Relative humidity (RH): If RH > 70%, evacuate moisture with exhaust fan.
   - Auxiliary Heater: Turn on during night/rain/low solar radiation (<300 W/m²).
4. Tone: Helpful, professional, concise, solution-oriented. Avoid excessive emojis.

{$langInstruction}

LIVE GREENHOUSE TELEMETRY SNAPSHOT:
- Timestamp: {$s['timestamp']}
- Batch Status: {$s['batchStatus']} (Code: {$s['batchCode']}, Variety: {$s['cropVariety']})
- Grain Moisture: {$s['currentMoisture']}% (Target: {$s['targetMoisture']}%, Initial: {$s['initialMoisture']}%)
- Grain Weight: {$s['currentWeightKg']} kg (Initial: {$s['initialWeightKg']} kg)
- Internal Chamber Temp: {$s['tempInternal']} °C (Safe: {$s['minSafeTemp']}°C - {$s['maxSafeTemp']}°C)
- External Temp: {$s['tempExternal']} °C
- Internal Humidity (RH): {$s['humidityInternal']} %
- External Humidity (RH): {$s['humidityExternal']} %
- Solar Radiation: {$s['solarRadiation']} W/m²
- Exhaust Fan: {$s['exhaustFanStatus']} (Speed: {$s['exhaustFanSpeed']}%)
- Circulation Fan: {$s['circFanStatus']}
- Auxiliary Heater: {$s['heaterStatus']} (Level: {$s['heaterLevel']})
- Estimated Drying Rate: {$s['dryingRatePerHour']} % per hour
- Active Alerts: {$s['activeAlertsCount']} alerts active.
TEXT;

        $contents = [];

        // Add relevant history (max 6 items)
        $trimmedHistory = array_slice($history, -6);
        foreach ($trimmedHistory as $item) {
            $role = ($item['sender'] ?? '') === 'user' ? 'user' : 'model';
            $text = $item['text'] ?? '';
            if (!empty($text)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]],
                ];
            }
        }

        // Add current prompt
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userPrompt]],
        ];

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 1200,
                'topP' => 0.9,
            ],
        ];

        // Format model URL
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $res = Http::timeout(25)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($res->successful()) {
            $data = $res->json();
            $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($replyText) {
                return ['success' => true, 'text' => trim($replyText)];
            }
        }

        return ['success' => false, 'error' => $res->body()];
    }

    /**
     * Domain heuristic agronomy rule engine (Guaranteed response even if offline/no API key).
     */
    protected function generateHeuristicReply(string $prompt, array $context, string $lang = 'id'): string
    {
        $s = $context['summary'];
        $lower = strtolower($prompt);
        $isEn = ($lang === 'en');

        // 1. Check Batch Status / Overall Evaluation
        if (str_contains($lower, 'evaluasi') || str_contains($lower, 'kondisi batch') || str_contains($lower, 'bagaimana') || str_contains($lower, 'evaluat') || str_contains($lower, 'status') || str_contains($lower, 'condition') || str_contains($lower, 'how')) {
            if ($isEn) {
                $moistureStatus = $s['currentMoisture'] <= $s['targetMoisture'] 
                    ? '**Target Reached**: Moisture level is in safe storage range.' 
                    : '**In Progress**: Progressing towards target ' . $s['targetMoisture'] . '%.';

                $tempStatus = ($s['tempInternal'] >= $s['minSafeTemp'] && $s['tempInternal'] <= $s['maxSafeTemp'])
                    ? '**Optimal** (' . $s['tempInternal'] . '°C, within 35-55°C safe range).'
                    : ($s['tempInternal'] > $s['maxSafeTemp'] 
                        ? '**Overheating Alert** (' . $s['tempInternal'] . '°C > 55°C limit). Turn on exhaust fans immediately!'
                        : '**Below Target** (' . $s['tempInternal'] . '°C < 35°C). Auxiliary Heater activation recommended.');

                $heaterMsg = $s['heaterStatus'] ? "Active (Level {$s['heaterLevel']})" : "Off";
                $fanMsg = $s['exhaustFanStatus'] ? "Active ({$s['exhaustFanSpeed']}%)" : "Off";

                return <<<MD
### Batch Evaluation Report: **{$s['batchCode']}**

Current summary of Hanjeli drying chamber conditions:

* **Hanjeli Variety**: {$s['cropVariety']}
* **Moisture Status**: {$s['currentMoisture']}% (Initial: {$s['initialMoisture']}% -> Target: {$s['targetMoisture']}%)
  * {$moistureStatus}
* **Greenhouse Chamber Temp**: {$tempStatus}
* **Relative Humidity (RH)**: Internal **{$s['humidityInternal']}%** | External **{$s['humidityExternal']}%**
* **Actuator Status**: Heater **{$heaterMsg}** | Exhaust Fan **{$fanMsg}**

---
#### Recommended Actions:
1. **Air Circulation**: Keep circulation blower active for uniform heat distribution across all trays.
2. **Moisture Exhaust**: If chamber RH $>65\%$, raise *Exhaust Fan* speed to evacuate evaporated moisture.
3. **Heating**: {$this->getHeaterRecommendation($s, 'en')}
MD;
            } else {
                $moistureStatus = $s['currentMoisture'] <= $s['targetMoisture'] 
                    ? '**Target Tercapai**: Kadar air sudah berada di ambang aman simpan.' 
                    : '**Sedang Proses**: Menuju target ' . $s['targetMoisture'] . '%.';

                $tempStatus = ($s['tempInternal'] >= $s['minSafeTemp'] && $s['tempInternal'] <= $s['maxSafeTemp'])
                    ? '**Optimal** (' . $s['tempInternal'] . '°C, dalam rentang aman 35-55°C).'
                    : ($s['tempInternal'] > $s['maxSafeTemp'] 
                        ? '**Peringatan Panas Berlebih** (' . $s['tempInternal'] . '°C > batas aman 55°C). Segera buka exhaust fan!'
                        : '**Di Bawah Target** (' . $s['tempInternal'] . '°C < 35°C). Disarankan aktifkan Auxiliary Heater.');

                $heaterMsg = $s['heaterStatus'] ? "Aktif (Level {$s['heaterLevel']})" : "Mati";
                $fanMsg = $s['exhaustFanStatus'] ? "Aktif ({$s['exhaustFanSpeed']}%)" : "Mati";

                return <<<MD
### Laporan Evaluasi Batch: **{$s['batchCode']}**

Berikut adalah ringkasan analisis proses pengeringan biji Hanjeli saat ini:

* **Varietas Hanjeli**: {$s['cropVariety']}
* **Status Kadar Air**: {$s['currentMoisture']}% (Awal: {$s['initialMoisture']}% -> Target: {$s['targetMoisture']}%)
  * {$moistureStatus}
* **Suhu Ruang Greenhouse**: {$tempStatus}
* **Kelembaban Udara (RH)**: Ruang Dalam **{$s['humidityInternal']}%** | Luar **{$s['humidityExternal']}%**
* **Status Aktuator**: Pemanas **{$heaterMsg}** | Exhaust Fan **{$fanMsg}**

---
#### Rekomendasi Tindakan:
1. **Sirkulasi Udara**: Pastikan kipas sirkulasi tetap aktif untuk meratakan panas di semua rak (*tray*).
2. **Evakuasi Lembab**: Jika RH dalam ruang $>65\%$, naikkan kecepatan *Exhaust Fan* untuk membuang uap air hasil evaporasi biji.
3. **Pemanas**: {$this->getHeaterRecommendation($s, 'id')}
MD;
            }
        }

        // 2. ETA / Estimasi Waktu Selesai
        if (str_contains($lower, 'estimasi') || str_contains($lower, 'waktu') || str_contains($lower, 'kapan') || str_contains($lower, 'eta') || str_contains($lower, 'selesai') || str_contains($lower, 'estimate') || str_contains($lower, 'finish') || str_contains($lower, 'time')) {
            $diffMoisture = max(0, $s['currentMoisture'] - $s['targetMoisture']);
            
            if ($diffMoisture <= 0) {
                return $isEn 
                    ? "**Drying Process is Complete!**\n\nHanjeli grain moisture has reached **{$s['currentMoisture']}%**, meeting safe storage standard (**{$s['targetMoisture']}%**). You can proceed to final cooling and packaging."
                    : "**Proses Pengeringan Sudah Selesai!**\n\nKadar air biji Hanjeli saat ini telah mencapai **{$s['currentMoisture']}%**, memenuhi target standar simpan (**{$s['targetMoisture']}%**). Anda dapat menyelesaikan batch ini dan melanjutkan ke tahap penimbangan akhir (*cooling down* & *packaging*).";
            }

            $rate = $s['dryingRatePerHour'] ?? 1.2;
            $etaTimeOnly = $s['estimatedCompletionTimeOnly'] ?? '-';
            $duration = $s['remainingDuration'] ?? ($isEn ? '~3-4 Hours' : '~3-4 Jam');
            $confidence = $s['aiConfidenceScore'] ?? 92;
            $weather = $s['weatherSummary'] ?? ($isEn ? 'Partly Sunny' : 'Cerah Berawan');

            if ($isEn) {
                return <<<MD
### ⏱️ Estimated Completion Time (Page's AI Kinetics)

Based on Hanjeli thin-layer drying kinetics model and live Sukabumi weather telemetry:

* **Current Grain Moisture**: **{$s['currentMoisture']}%** (Initial: {$s['initialMoisture']}%)
* **Target Moisture**: **{$s['targetMoisture']}%** (Remaining reduction: **{$diffMoisture}%**)
* **Current Dehydration Rate**: **~{$rate}% per hour**
* **Local Weather Forecast**: **{$weather}**
* **Model Confidence**: **{$confidence}% Confidence**

---
#### Predicted Finish Time:
**At {$etaTimeOnly}** (Remaining: **{$duration}**)

> *AI Recommendation:* Maintain chamber temperature between **42°C - 48°C** and run active *Exhaust Fans* to prevent case hardening.
MD;
            } else {
                return <<<MD
### ⏱️ Estimasi Waktu Selesai Pengeringan (Page's AI Kinetics)

Berdasarkan kalkulasi model kinetika pengeringan gabah Hanjeli dan prakiraan cuaca Sukabumi terkini:

* **Kadar Air Saat Ini**: **{$s['currentMoisture']}%** (Awal: {$s['initialMoisture']}%)
* **Target Simpan SNI**: **{$s['targetMoisture']}%** (Sisa reduksi: **{$diffMoisture}%**)
* **Laju Dehidrasi Terkini**: **~{$rate}% per jam**
* **Prakiraan Cuaca Lokal**: **{$weather}**
* **Akurasi Model AI**: **{$confidence}% Confidence**

---
#### Prediksi Jam Selesai:
**Pukul {$etaTimeOnly}** (Tersisa: **{$duration} lagi**)

> *Rekomendasi AI:* Pertahankan suhu ruang di kisaran **42°C - 48°C** dan buang kelembaban dengan *Exhaust Fan* aktif agar butir gabah Hanjeli kering secara merata tanpa retak (*case hardening*).
MD;
            }
        }

        // 3. Suhu, RH, Cuaca, Pemanas & Kipas
        if (str_contains($lower, 'suhu') || str_contains($lower, 'kelembaban') || str_contains($lower, 'kipas') || str_contains($lower, 'pemanas') || str_contains($lower, 'cuaca') || str_contains($lower, 'mendung') || str_contains($lower, 'hujan') || str_contains($lower, 'hemat') || str_contains($lower, 'energi') || str_contains($lower, 'temp') || str_contains($lower, 'humid') || str_contains($lower, 'fan') || str_contains($lower, 'heater') || str_contains($lower, 'solar') || str_contains($lower, 'radiation')) {
            if ($isEn) {
                return <<<MD
### Microclimate Analysis & Actuator Recommendations

* **Internal / External Temp**: **{$s['tempInternal']}°C** / **{$s['tempExternal']}°C**
* **Internal / External RH**: **{$s['humidityInternal']}%** / **{$s['humidityExternal']}%**
* **Solar Radiation**: **{$s['solarRadiation']} W/m²**

#### Actuator Control Guidelines:
1. **Auxiliary Heater**:
   - {$this->getHeaterRecommendation($s, 'en')}
2. **Exhaust Fan (Moisture Evacuation)**:
   - When chamber RH $>60\%$, set fan speed to **75% - 100%** to rapidly evacuate moist air.
3. **Circulation Fan (Heat Homogenization)**:
   - Keep continuously **ACTIVE** to avoid localized hotspots on upper racks and stagnant moisture on bottom trays.
MD;
            } else {
                return <<<MD
### Analisis Mikroklimat & Rekomendasi Aktuator

* **Suhu Ruang / Luar**: **{$s['tempInternal']}°C** / **{$s['tempExternal']}°C**
* **Kelembaban Ruang / Luar**: **{$s['humidityInternal']}%** / **{$s['humidityExternal']}%**
* **Radiasi Matahari**: **{$s['solarRadiation']} W/m²**

#### Panduan Pengaturan Aktuator:
1. **Auxiliary Heater (Pemanas Bantu)**:
   - {$this->getHeaterRecommendation($s, 'id')}
2. **Exhaust Fan (Pembuang Lembap)**:
   - Saat RH ruang $>60\%$, atur kecepatan kipas ke **75% - 100%** agar uap air dari penguapan biji cepat keluar.
3. **Circulation Fan (Pemerata Suhu)**:
   - Biarkan selalu **AKTIF** untuk mencegah titik panas (*hotspot*) pada rak paling atas dan kelembaban terjebak di rak bawah.
MD;
            }
        }

        // 4. Kualitas, Mutu, Jamur, Aflatoksin
        if (str_contains($lower, 'jamur') || str_contains($lower, 'kualitas') || str_contains($lower, 'mutu') || str_contains($lower, 'aflatoksin') || str_contains($lower, 'rusak') || str_contains($lower, 'mold') || str_contains($lower, 'quality') || str_contains($lower, 'damage') || str_contains($lower, 'standard')) {
            if ($isEn) {
                return <<<MD
### Hanjeli Grain Quality Assurance & Mold Prevention

Key post-harvest preservation guidelines for Hanjeli (*Coix lacryma-jobi*):

* **Critical Mold/Aflatoxin Threshold**: Occurs when grain moisture stays $>18\%$ with chamber RH $>75\%$ for over 8 hours. Current internal RH: **{$s['humidityInternal']}%** (safe with active ventilation).
* **Maximum Temperature Threshold**: Never exceed **55°C**. Temperatures $>55°C$ degrade protein/starch quality and trigger grain cracking (*case hardening*).
* **Safe Packaging Standard**: Target equilibrium moisture content is **12% - 14%**. At this level, grains can be safely stored in airtight packaging for 6-12 months without mold or pest infestation.
MD;
            } else {
                return <<<MD
### Jaminan Mutu & Pencegahan Jamur Biji Hanjeli

Pengeringan biji Hanjeli (*Coix lacryma-jobi*) memiliki titik kritis pascapanen:

* **Titik Kritis Aflatoksin/Jamur**: Terjadi bila biji dibiarkan pada kadar air $>18\%$ dengan RH ruang $>75\%$ selama lebih dari 8 jam. Kondisi saat ini: RH ruang **{$s['humidityInternal']}%** (relatif aman jika ventilasi berjalan).
* **Batas Maksimal Suhu**: Jangan melebihi **55°C**. Suhu $>55°C$ dapat merusak kandungan karbohidrat kompleks/protein dan menyebabkan biji mudah pecah (*case hardening*).
* **Target Standar Nasional / SNI**: Kadar air simpan optimal adalah **12% - 14%**. Pada kadar ini, biji dapat disimpan hingga 6-12 bulan dalam kemasan kedap udara tanpa risiko apek atau berkutu.
MD;
            }
        }

        // 5. Default General Response
        if ($isEn) {
            return <<<MD
Hello! I am **Hanjeli AI Copilot**, your smart agronomy drying assistant.

Current greenhouse telemetry snapshot:
* **Active Batch**: **{$s['batchCode']}** ({$s['cropVariety']})
* **Grain Moisture**: **{$s['currentMoisture']}%** (Target: {$s['targetMoisture']}%)
* **Chamber Temperature**: **{$s['tempInternal']}°C** | **Chamber RH**: **{$s['humidityInternal']}%**
* **Solar Radiation**: **{$s['solarRadiation']} W/m²**

How can I assist you today? You can ask about:
1. *Active batch drying performance evaluation.*
2. *Estimated completion time (ETA).*
3. *Exhaust fan and auxiliary heater control recommendations.*
4. *Mold prevention and grain quality standards.*
MD;
        } else {
            return <<<MD
Halo! Saya **Hanjeli AI Copilot**, asisten cerdas greenhouse pengeringan Hanjeli Anda.

Saat ini sistem memantau:
* **Batch Aktif**: **{$s['batchCode']}** ({$s['cropVariety']})
* **Kadar Air Biji**: **{$s['currentMoisture']}%** (Target: {$s['targetMoisture']}%)
* **Suhu Ruang**: **{$s['tempInternal']}°C** | **RH Ruang**: **{$s['humidityInternal']}%**
* **Radiasi Matahari**: **{$s['solarRadiation']} W/m²**

Ada yang bisa saya bantu? Anda bisa bertanya tentang:
1. *Evaluasi performa pengeringan batch aktif.*
2. *Estimasi jam selesai pengeringan (ETA).*
3. *Rekomendasi pengaturan kipas exhaust & pemanas bantu.*
4. *Pencegahan jamur dan standar mutu biji Hanjeli.*
MD;
        }
    }

    protected function getHeaterRecommendation(array $s, string $lang = 'id'): string
    {
        $isEn = ($lang === 'en');

        if ($s['solarRadiation'] >= 600 && $s['tempInternal'] >= 42) {
            return $isEn
                ? "Solar radiation is high ({$s['solarRadiation']} W/m²). Auxiliary heater can be **TURNED OFF** to save electrical power."
                : "Radiasi matahari tinggi ({$s['solarRadiation']} W/m²). Pemanas bantu dapat **DIMATIKAN** untuk menghemat konsumsi energi listrik greenhouse.";
        }

        if ($s['solarRadiation'] < 300 || $s['tempInternal'] < 38) {
            return $isEn
                ? "Solar radiation is low/cloudy ({$s['solarRadiation']} W/m²). It is recommended to turn on **Auxiliary Heater** at Level 2 to sustain drying rate."
                : "Radiasi matahari rendah/mendung ({$s['solarRadiation']} W/m²). Disarankan menyalakan **Auxiliary Heater** pada Level 2 agar laju pengeringan tidak terhenti.";
        }

        return $isEn
            ? "Solar radiation is moderate ({$s['solarRadiation']} W/m²). Heater can be kept at Level 1 as backup."
            : "Radiasi matahari sedang ({$s['solarRadiation']} W/m²). Pemanas dapat diatur pada Level 1 sebagai pendukung.";
    }

    /**
     * Generate interactive follow-up action pills.
     */
    protected function generateFollowUpActions(string $userPrompt, array $context, string $lang = 'id'): array
    {
        $s = $context['summary'];
        $isEn = ($lang === 'en');

        if ($isEn) {
            return [
                [
                    'label' => 'Check Batch Status',
                    'prompt' => "Give a summary of batch {$s['batchCode']} status right now.",
                ],
                [
                    'label' => 'Estimated Finish Time',
                    'prompt' => "When will this batch reach {$s['targetMoisture']}% moisture content?",
                ],
                [
                    'label' => 'Energy Saving Advice',
                    'prompt' => "How can I optimize fans and heaters for energy efficiency right now?",
                ],
            ];
        }

        return [
            [
                'label' => 'Cek Kondisi Batch',
                'prompt' => "Berikan ringkasan status batch {$s['batchCode']} saat ini.",
            ],
            [
                'label' => 'Estimasi Waktu Selesai',
                'prompt' => "Kapan kira-kira batch ini mencapai kadar air {$s['targetMoisture']}%?",
            ],
            [
                'label' => 'Saran Hemat Energi',
                'prompt' => "Bagaimana cara optimasi kipas dan pemanas untuk efisiensi energi saat ini?",
            ],
        ];
    }
}
