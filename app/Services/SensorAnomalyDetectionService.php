<?php

namespace App\Services;

use App\Events\AlertTriggered;
use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use App\Models\Telemetry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SensorAnomalyDetectionService
{
    /**
     * Physical safety threshold boundaries
     */
    protected array $physicalBounds = [
        'temp_internal' => ['min' => 0.0, 'max' => 85.0, 'unit' => '°C', 'name' => 'Suhu Internal Ruang'],
        'temp_external' => ['min' => 10.0, 'max' => 60.0, 'unit' => '°C', 'name' => 'Suhu Eksternal Luar'],
        'humidity_internal' => ['min' => 5.0, 'max' => 100.0, 'unit' => '% RH', 'name' => 'Kelembaban Internal'],
        'humidity_external' => ['min' => 10.0, 'max' => 100.0, 'unit' => '% RH', 'name' => 'Kelembaban Eksternal'],
        'grain_moisture' => ['min' => 5.0, 'max' => 50.0, 'unit' => '%', 'name' => 'Kadar Air Gabah'],
        'solar_radiation' => ['min' => 0.0, 'max' => 1600.0, 'unit' => 'W/m²', 'name' => 'Radiasi Surya'],
        'weight_kg' => ['min' => 0.0, 'max' => 500.0, 'unit' => 'kg', 'name' => 'Bobot Massa'],
    ];

    /**
     * Inspect a newly received telemetry against active actuator state and recent history.
     * Returns array of detected anomalies.
     */
    public function inspectTelemetry(Telemetry $telemetry, ?ActuatorState $actuators = null): array
    {
        $detectedAnomalies = [];
        $batchId = $telemetry->batch_id;

        if (!$actuators) {
            $actuators = ActuatorState::latest()->first();
        }

        // Fetch recent historical points (excluding current telemetry if already persisted)
        $recentPoints = Telemetry::when($telemetry->id, fn($q) => $q->where('id', '!=', $telemetry->id))
            ->orderBy('recorded_at', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        // -------------------------------------------------------------
        // 1. ANOMALY: Physical Out-of-Bounds Detection
        // -------------------------------------------------------------
        $boundsAnomaly = $this->checkPhysicalBounds($telemetry);
        if ($boundsAnomaly) {
            $detectedAnomalies[] = $boundsAnomaly;
        }

        // -------------------------------------------------------------
        // 2. ANOMALY: Thermal Paradox (Suhu Drop Saat Pemanas Aktif / Pintu Terbuka / Sensor Lepas)
        // -------------------------------------------------------------
        $thermalAnomaly = $this->checkThermalParadox($telemetry, $actuators, $recentPoints);
        if ($thermalAnomaly) {
            $detectedAnomalies[] = $thermalAnomaly;
        }

        // -------------------------------------------------------------
        // 3. ANOMALY: Moisture Sudden Spike (Lonjakan Kadar Air di Tengah Batch)
        // -------------------------------------------------------------
        $moistureAnomaly = $this->checkMoistureSpike($telemetry, $recentPoints, $batchId);
        if ($moistureAnomaly) {
            $detectedAnomalies[] = $moistureAnomaly;
        }

        // -------------------------------------------------------------
        // 4. ANOMALY: Sensor Freeze / Flatline Detection
        // -------------------------------------------------------------
        $freezeAnomaly = $this->checkSensorFreeze($recentPoints);
        if ($freezeAnomaly) {
            $detectedAnomalies[] = $freezeAnomaly;
        }

        // -------------------------------------------------------------
        // 5. ANOMALY: Solar-Thermal Inversion (Radiasi Tinggi Tetapi Ruang Dingin)
        // -------------------------------------------------------------
        $solarAnomaly = $this->checkSolarThermalInversion($telemetry, $actuators);
        if ($solarAnomaly) {
            $detectedAnomalies[] = $solarAnomaly;
        }

        // -------------------------------------------------------------
        // 6. ANOMALY: Exhaust Desaturation Failure
        // -------------------------------------------------------------
        $exhaustAnomaly = $this->checkExhaustFailure($telemetry, $actuators, $recentPoints);
        if ($exhaustAnomaly) {
            $detectedAnomalies[] = $exhaustAnomaly;
        }

        // Process and persist any newly triggered system alerts
        foreach ($detectedAnomalies as $anomaly) {
            $this->triggerAnomalyAlert($anomaly, $batchId);
        }

        return $detectedAnomalies;
    }

    /**
     * Check if values exceed impossible physical sensor thresholds
     */
    protected function checkPhysicalBounds(Telemetry $t): ?array
    {
        $fields = [
            'temp_internal' => $t->temp_internal,
            'temp_external' => $t->temp_external,
            'humidity_internal' => $t->humidity_internal,
            'humidity_external' => $t->humidity_external,
            'grain_moisture' => $t->grain_moisture,
            'solar_radiation' => $t->solar_radiation,
        ];

        foreach ($fields as $field => $val) {
            if ($val === null) continue;
            $bounds = $this->physicalBounds[$field];
            if ($val < $bounds['min'] || $val > $bounds['max']) {
                return [
                    'code' => 'OUT_OF_PHYSICAL_BOUNDS',
                    'level' => 'CRITICAL',
                    'title' => "Pembacaan Ekstrem: {$bounds['name']} Tidak Wajar",
                    'description' => "Sensor mendeteksi nilai {$val} {$bounds['unit']}, di luar batas operasional normal ({$bounds['min']} - {$bounds['max']} {$bounds['unit']}).",
                    'rootCause' => "Kemungkinan kerusakan probe sensor, pin floating, short-circuit kabel I2C/analog, atau kesalahan firmware pembaca ADC.",
                    'action' => "Periksa koneksi fisik kabel sensor pada pin ESP32 dan lakukan kalibrasi ulang melalui menu Admin Perangkat.",
                    'field' => $field,
                    'value' => $val,
                ];
            }
        }

        return null;
    }

    /**
     * Check Thermal Paradox: Auxiliary heater ON or high solar, but internal temperature drops suddenly
     */
    protected function checkThermalParadox(Telemetry $t, ActuatorState $actuators, $recent): ?array
    {
        $heaterActive = (bool)($actuators->aux_heater_status || $t->heater_status);
        $heaterLevel = (int)($actuators->aux_heater_level ?? $t->heater_level ?? 0);

        // Immediate Condition 1: Heater is ACTIVE, but internal temp is noticeably lower than external temp (e.g. door wide open or cold blast)
        if ($heaterActive && ($t->temp_internal < $t->temp_external - 2.0)) {
            $diff = round($t->temp_external - $t->temp_internal, 1);
            return [
                'code' => 'THERMAL_PARADOX_HEATER_ACTIVE',
                'level' => 'CRITICAL',
                'title' => 'Anomali Termal: Pintu Greenhouse Terbuka / Drop Suhu',
                'description' => "Suhu ruang pengering ({$t->temp_internal}°C) berada {$diff}°C LEBIH RENDAH dibanding suhu luar ({$t->temp_external}°C) padahal Auxiliary Heater sedang AKTIF (Level {$heaterLevel}).",
                'rootCause' => 'Pintu greenhouse kemungkinan besar terbuka lebar, dinding penutup polikarbonat berlubang, atau modul pemanas keramik terputus dari arus.',
                'action' => 'Segera periksa apakah pintu greenhouse tertutup rapat dan periksa fisik pemanas bantu.',
                'dropDelta' => $diff,
            ];
        }

        // Relative Condition 2: Heater active and rapid temperature drop compared to recent points
        if ($heaterActive && $recent->isNotEmpty()) {
            $previous = $recent->last();
            $tempDiff = $previous->temp_internal - $t->temp_internal;
            if ($tempDiff >= 3.0) {
                return [
                    'code' => 'THERMAL_PARADOX_HEATER_ACTIVE',
                    'level' => 'CRITICAL',
                    'title' => 'Anomali Termal: Penurunan Suhu Mendadak Saat Pemanas Aktif',
                    'description' => "Suhu ruang pengering anjlok sebesar {$tempDiff}°C (dari {$previous->temp_internal}°C ke {$t->temp_internal}°C) padahal Auxiliary Heater sedang AKTIF.",
                    'rootCause' => 'Terjadi pelepasan panas masif mendadak, pintu dibuka paksa, atau sensor suhu terlepas.',
                    'action' => 'Periksa apakah ada pintu atau ventilasi atas yang terbuka mendadak.',
                    'dropDelta' => round($tempDiff, 1),
                ];
            }
        }

        // Condition 3: Solar radiation is high (> 600 W/m²), but internal temp drops below external temp
        if ($t->solar_radiation >= 600 && ($t->temp_internal < $t->temp_external - 1.5)) {
            return [
                'code' => 'THERMAL_INVERSION_DAYTIME',
                'level' => 'WARNING',
                'title' => 'Anomali Termal: Suhu Ruang Lebih Rendah dari Luar Saat Terik',
                'description' => "Radiasi surya terik ({$t->solar_radiation} W/m²), tetapi suhu internal ({$t->temp_internal}°C) lebih dingin dibanding suhu lingkungan luar ({$t->temp_external}°C).",
                'rootCause' => 'Sirkulasi udara luar terlalu deras, pintu akses terbuka lebar, atau sensor suhu internal terpapar hembusan angin dingin.',
                'action' => 'Pastikan pintu masuk greenhouse tertutup dan periksa posisi probe sensor suhu internal.',
                'tempInternal' => $t->temp_internal,
                'tempExternal' => $t->temp_external,
            ];
        }

        return null;
    }

    /**
     * Check if grain moisture increases unnaturally during active drying
     */
    protected function checkMoistureSpike(Telemetry $t, $recent, ?int $batchId): ?array
    {
        if ($recent->isEmpty()) return null;

        $previous = $recent->last();
        if ($previous->grain_moisture && $t->grain_moisture) {
            $spike = round($t->grain_moisture - $previous->grain_moisture, 1);

            // Moisture spiked by > 1.8% between consecutive cycles
            if ($spike >= 1.8) {
                return [
                    'code' => 'MOISTURE_SUDDEN_SPIKE',
                    'level' => 'WARNING',
                    'title' => 'Anomali Kadar Air: Lonjakan Kelembaban Gabah Mendadak',
                    'description' => "Kadar air biji Hanjeli melonjak naik sebesar +{$spike}% (dari {$previous->grain_moisture}% ke {$t->grain_moisture}%) di tengah proses pengeringan.",
                    'rootCause' => 'Kemungkinan adanya rembesan air hujan / kondensasi atap menetes ke rak (*tray*), penambahan gabah basah baru tanpa pencatatan, atau posisi probe kapasitif kadar air bergeser.',
                    'action' => 'Periksa apakah ada kebocoran atap di atas rak pengering dan pastikan operator tidak mencampur gabah baru ke dalam batch yang sedang berjalan.',
                    'spikePercent' => $spike,
                ];
            }
        }

        return null;
    }

    /**
     * Check if all sensor readings are frozen / flatline across multiple consecutive cycles
     */
    protected function checkSensorFreeze($recent): ?array
    {
        if ($recent->count() < 5) return null;

        $temps = $recent->pluck('temp_internal')->unique();
        $hums = $recent->pluck('humidity_internal')->unique();
        $moists = $recent->pluck('grain_moisture')->unique();

        if ($temps->count() === 1 && $hums->count() === 1 && $moists->count() === 1) {
            $val = $temps->first();
            return [
                'code' => 'SENSOR_DATA_FLATLINE',
                'level' => 'WARNING',
                'title' => 'Anomali Sensor: Pembacaan Macet / Flatline Terdeteksi',
                'description' => "Nilai sensor Suhu ({$val}°C), Kelembaban ({$hums->first()}%), dan Kadar Air ({$moists->first()}%) tidak berubah sama sekali selama beberapa siklus log berturut-turut.",
                'rootCause' => 'Microcontroller ESP32 mengalami buffer hang, modul ADC stuck, atau simulator sensor macet.',
                'action' => 'Kirim perintah Ping / Restart pada mikrokontroler melalui menu Admin Perangkat atau periksa kestabilan catu daya 5V/3.3V ESP32.',
            ];
        }

        return null;
    }

    /**
     * Check Solar-Thermal Inversion
     */
    protected function checkSolarThermalInversion(Telemetry $t, ActuatorState $actuators): ?array
    {
        if ($t->solar_radiation >= 850 && $t->temp_internal < 30.0 && !$actuators->exhaust_fan_status) {
            return [
                'code' => 'SOLAR_THERMAL_STAGNATION',
                'level' => 'INFO',
                'title' => 'Retensi Termal Rendah Terhadap Radiasi Tinggi',
                'description' => "Radiasi matahari terik ({$t->solar_radiation} W/m²), tetapi greenhouse belum mencapai akumulasi panas standar (>32°C).",
                'rootCause' => 'Kolektor surya atap mungkin berdebu tebal atau insulasi greenhouse mengalami celah ventilasi terbuka.',
                'action' => 'Periksa kebersihan penutup polikarbonat transparan di atap greenhouse.',
            ];
        }

        return null;
    }

    /**
     * Check if Exhaust Fan is running 100% but internal RH keeps increasing drastically
     */
    protected function checkExhaustFailure(Telemetry $t, ActuatorState $actuators, $recent): ?array
    {
        if ($actuators->exhaust_fan_status && $actuators->exhaust_fan_speed >= 90 && $t->humidity_internal >= 80.0) {
            if ($recent->count() >= 4) {
                $first = $recent->first();
                $last = $t;
                if ($last->humidity_internal > $first->humidity_internal + 3.0) {
                    return [
                        'code' => 'EXHAUST_INEFFECTIVE',
                        'level' => 'WARNING',
                        'title' => 'Kipas Exhaust Berjalan Penuh Namun RH Tetap Naik',
                        'description' => "Kipas exhaust berjalan pada kecepatan 100%, tetapi kelembapan internal justru naik ke {$t->humidity_internal}% RH.",
                        'rootCause' => 'Kemungkinan terjadi penumpukan uap air berlebih di rak paling bawah, saluran ventilasi keluar tersumbat debu/kotoran, atau udara luar sedang hujan badai.',
                        'action' => 'Periksa apakah kisi-kisi saluran buang kipas exhaust terhalang dan aktifkan kipas sirkulasi internal untuk meratakan aliran udara.',
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Trigger System Alert and broadcast with anti-spam rate limiting
     */
    protected function triggerAnomalyAlert(array $anomaly, ?int $batchId): void
    {
        $cacheKey = 'anomaly_alert_throttle_' . md5($anomaly['code'] . '_' . ($batchId ?? 0));

        // Throttle same anomaly alert to once every 15 minutes
        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, 900); // 15 minutes

        $alert = SystemAlert::create([
            'batch_id' => $batchId,
            'level' => $anomaly['level'] ?? 'WARNING',
            'category' => 'SENSOR',
            'title' => $anomaly['title'],
            'message' => $anomaly['description'] . "\n\nRekomendasi Aksi: " . $anomaly['action'],
            'is_read' => false,
        ]);

        try {
            event(new AlertTriggered($alert));
        } catch (\Throwable $e) {
            Log::debug('Anomaly alert broadcast skipped: ' . $e->getMessage());
        }
    }

    /**
     * Alias for getHealthMatrix
     */
    public function getHealthStatus(): array
    {
        return $this->getHealthMatrix();
    }

    /**
     * Get overall Sensor Health Matrix for UI dashboards
     */
    public function getHealthMatrix(): array
    {
        $latest = Telemetry::orderBy('recorded_at', 'desc')->first();
        $actuator = ActuatorState::latest()->first();

        $activeAnomalies = [];
        if ($latest) {
            $activeAnomalies = $this->inspectTelemetry($latest, $actuator);
        }

        // Calculate health score (100% if no anomalies, reduced per severity)
        $score = 100;
        foreach ($activeAnomalies as $a) {
            if ($a['level'] === 'CRITICAL') $score -= 30;
            elseif ($a['level'] === 'WARNING') $score -= 15;
            else $score -= 5;
        }
        $score = max(20, min(100, $score));

        $status = match (true) {
            $score >= 90 => 'HEALTHY',
            $score >= 70 => 'WARNING',
            default => 'CRITICAL',
        };

        $statusText = match ($status) {
            'HEALTHY' => 'Optimal (Semua Sensor Normal)',
            'WARNING' => 'Peringatan (Perlu Pengawasan)',
            'CRITICAL' => 'Kritis (Tindakan Diperlukan Segera)',
        };

        return [
            'success' => true,
            'healthScore' => $score,
            'status' => $status,
            'statusText' => $statusText,
            'anomalyCount' => count($activeAnomalies),
            'anomalies' => $activeAnomalies,
            'checkedAt' => now()->toIso8601String(),
            'sensors' => [
                'tempInternal' => [
                    'name' => 'Suhu Ruang Internal',
                    'value' => $latest?->temp_internal ?? 0,
                    'unit' => '°C',
                    'status' => 'NORMAL',
                ],
                'humidityInternal' => [
                    'name' => 'Kelembapan Ruang Internal',
                    'value' => $latest?->humidity_internal ?? 0,
                    'unit' => '% RH',
                    'status' => 'NORMAL',
                ],
                'grainMoisture' => [
                    'name' => 'Kadar Air Gabah',
                    'value' => $latest?->grain_moisture ?? 0,
                    'unit' => '%',
                    'status' => 'NORMAL',
                ],
                'solarRadiation' => [
                    'name' => 'Radiasi Surya Atap',
                    'value' => $latest?->solar_radiation ?? 0,
                    'unit' => 'W/m²',
                    'status' => 'NORMAL',
                ],
            ],
        ];
    }
}
