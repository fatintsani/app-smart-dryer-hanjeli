<?php

namespace App\Services;

use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\SystemSetting;
use App\Models\Telemetry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DryingPredictionService
{
    /**
     * Coordinate for Desa Wisata Hanjeli, Waluran, Kab. Sukabumi, Jawa Barat
     */
    protected float $latitude = -7.1825;
    protected float $longitude = 106.5514;

    /**
     * Predict drying completion ETA and return detailed kinetic telemetry projection.
     */
    public function predictBatchEta(?int $batchId = null): array
    {
        // 1. Retrieve Batch
        $batch = null;
        if ($batchId) {
            $batch = Batch::find($batchId);
        } else {
            $batch = Batch::whereIn('status', ['ACTIVE', 'PAUSED'])
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$batch) {
            $batch = Batch::orderBy('created_at', 'desc')->first();
        }

        // 2. Fetch Latest Telemetry & Actuator State
        $latestTelemetry = Telemetry::orderBy('recorded_at', 'desc')->first();
        $recentTelemetries = Telemetry::orderBy('recorded_at', 'desc')->take(12)->get()->reverse()->values();
        $actuator = ActuatorState::latest()->first();
        $setting = SystemSetting::first();

        // 3. Extract baseline parameters
        $initialMoisture = (float)($batch?->initial_moisture_percent ?? 24.5);
        $currentMoisture = (float)($latestTelemetry?->grain_moisture ?? ($batch?->current_moisture_percent ?? 18.2));
        $targetMoisture = (float)($batch?->target_moisture_percent ?? ($setting?->target_moisture_default ?? 12.0));
        $initialWeight = (float)($batch?->initial_weight_kg ?? 125.0);
        $currentWeight = (float)($latestTelemetry?->weight_kg ?? ($batch?->current_weight_kg ?? 118.0));
        $tempInternal = (float)($latestTelemetry?->temp_internal ?? 42.5);
        $tempExternal = (float)($latestTelemetry?->temp_external ?? 30.0);
        $humidityInternal = (float)($latestTelemetry?->humidity_internal ?? 50.0);
        $humidityExternal = (float)($latestTelemetry?->humidity_external ?? 70.0);
        $solarRadiation = (float)($latestTelemetry?->solar_radiation ?? 650.0);
        $exhaustSpeed = (int)($actuator?->exhaust_fan_speed ?? 70);
        $heaterStatus = (bool)($actuator?->aux_heater_status ?? false);
        $heaterLevel = (int)($actuator?->aux_heater_level ?? 0);

        // 4. Fetch or fallback Sukabumi local weather forecast
        $weather = $this->getLocalWeatherForecast();

        // 5. Calculate Drying Kinetics (Page's Model + Environmental Regression)
        $kinetics = $this->computePageKinetics([
            'initialMoisture' => $initialMoisture,
            'currentMoisture' => $currentMoisture,
            'targetMoisture' => $targetMoisture,
            'tempInternal' => $tempInternal,
            'tempExternal' => $tempExternal,
            'humidityInternal' => $humidityInternal,
            'humidityExternal' => $humidityExternal,
            'solarRadiation' => $solarRadiation,
            'exhaustSpeed' => $exhaustSpeed,
            'heaterStatus' => $heaterStatus,
            'heaterLevel' => $heaterLevel,
            'initialWeight' => $initialWeight,
            'currentWeight' => $currentWeight,
            'weather' => $weather,
            'recentTelemetries' => $recentTelemetries,
        ]);

        // 6. Generate Projected Trajectory Curve (Future Forecast Points)
        $trajectory = $this->generateForecastTrajectory(
            $currentMoisture,
            $targetMoisture,
            $kinetics['dryingRatePerHour'],
            $kinetics['remainingHours'],
            $tempInternal,
            $humidityInternal,
            $weather
        );

        // 7. Calculate Completion Time
        $now = Carbon::now('Asia/Jakarta');
        $completionTime = $now->copy()->addMinutes((int)round($kinetics['remainingMinutes']));

        // 8. Generate Smart Actionable Recommendations
        $recommendations = $this->generateRecommendations(
            $tempInternal,
            $humidityInternal,
            $solarRadiation,
            $exhaustSpeed,
            $heaterStatus,
            $weather,
            $kinetics['dryingRatePerHour'],
            $currentMoisture,
            $targetMoisture
        );

        return [
            'success' => true,
            'batchId' => $batch?->id,
            'batchCode' => $batch?->batch_code ?? 'HJ-ACTIVE',
            'cropVariety' => $batch?->crop_variety ?? 'Hanjeli Ketan Sukabumi',
            'status' => $batch?->status ?? 'ACTIVE',
            'currentMoisture' => round($currentMoisture, 1),
            'targetMoisture' => round($targetMoisture, 1),
            'initialMoisture' => round($initialMoisture, 1),
            'moistureDifference' => round(max(0, $currentMoisture - $targetMoisture), 1),
            'dryingRatePerHour' => round($kinetics['dryingRatePerHour'], 2),
            'remainingHours' => round($kinetics['remainingHours'], 1),
            'remainingMinutes' => (int)round($kinetics['remainingMinutes']),
            'formattedRemaining' => $this->formatDuration($kinetics['remainingMinutes']),
            'estimatedCompletionAt' => $completionTime->toIso8601String(),
            'estimatedCompletionFormatted' => $completionTime->translatedFormat('H:i \W\I\B (d M)'),
            'estimatedCompletionTimeOnly' => $completionTime->format('H:i \W\I\B'),
            'isToday' => $completionTime->isToday(),
            'confidenceScore' => $kinetics['confidenceScore'],
            'modelType' => "Page's Modified Thin-Layer Kinetics + Environmental Gradient Regression",
            'parameters' => [
                'k_rate_constant' => round($kinetics['k'], 4),
                'n_exponent' => round($kinetics['n'], 3),
                'equilibrium_moisture_Me' => round($kinetics['Me'], 2),
                'thermal_potential_dT' => round($tempInternal - $tempExternal, 1),
                'air_exchange_factor' => round($exhaustSpeed / 100, 2),
            ],
            'weatherContext' => $weather,
            'recommendations' => $recommendations,
            'forecastTrajectory' => $trajectory,
            'generatedAt' => $now->toIso8601String(),
        ];
    }

    /**
     * Compute Page's Drying Kinetics with environmental modifiers
     */
    protected function computePageKinetics(array $params): array
    {
        $currentM = $params['currentMoisture'];
        $targetM = $params['targetMoisture'];
        $initialM = $params['initialMoisture'];

        if ($currentM <= $targetM) {
            return [
                'remainingHours' => 0.0,
                'remainingMinutes' => 0,
                'dryingRatePerHour' => 0.0,
                'confidenceScore' => 99,
                'k' => 0.0,
                'n' => 1.08,
                'Me' => 8.5,
            ];
        }

        // 1. Calculate Equilibrium Moisture Content (Me) using Henderson-Thompson approximation for grains
        // Me depends on Temperature (°C) and Relative Humidity (%)
        $T_K = $params['tempInternal'] + 273.15; // Kelvin
        $RH = max(5, min(95, $params['humidityInternal'])) / 100.0;
        $Me = max(7.0, min(11.5, 100 * pow((-log(1.0 - $RH) / (0.00011 * ($T_K - 220))), 0.45)));

        // 2. Base Drying Constant (k0) based on Arrhenius-like temperature effect
        $tempFactor = pow(($params['tempInternal'] / 35.0), 1.65);
        
        // 3. Humidity Deficit Factor: Dryer air accelerates drying
        $rhFactor = max(0.4, (100.0 - $params['humidityInternal']) / 55.0);

        // 4. Solar Radiation & Thermal Collector Factor
        $solarFactor = 1.0 + ($params['solarRadiation'] / 1200.0) * 0.45;

        // 5. Airflow & Heater Factor
        $fanFactor = 0.85 + (($params['exhaustSpeed'] / 100.0) * 0.35);
        $heaterFactor = $params['heaterStatus'] ? (1.0 + ($params['heaterLevel'] * 0.12)) : 1.0;

        // 6. Weather Forecast Compensation
        $weatherModifier = 1.0;
        if (!empty($params['weather']['cloudCover'])) {
            $cloud = $params['weather']['cloudCover'];
            if ($cloud > 70 && !$params['heaterStatus']) {
                $weatherModifier *= 0.82;
            }
        }

        // Combine into Page's Drying Rate Constant k (per hour)
        $k_base = 0.078;
        $k = $k_base * $tempFactor * $rhFactor * $solarFactor * $fanFactor * $heaterFactor * $weatherModifier;
        $n = 1.09;

        // 7. Calculate historical drop rate if telemetry exists
        $telemetries = $params['recentTelemetries'];
        $historicalRate = null;
        if ($telemetries && $telemetries->count() >= 3) {
            $first = $telemetries->first();
            $last = $telemetries->last();
            $timeDiffMins = Carbon::parse($first->recorded_at)->diffInMinutes(Carbon::parse($last->recorded_at));
            if ($timeDiffMins >= 10 && $first->grain_moisture && $last->grain_moisture) {
                $drop = $first->grain_moisture - $last->grain_moisture;
                if ($drop > 0) {
                    $historicalRate = ($drop / $timeDiffMins) * 60;
                }
            }
        }

        // 8. Calculate Remaining Time using Page's equation:
        // Moisture Ratio MR = (M(t) - Me) / (M0 - Me) = exp(-k * t^n)
        $ratio = max(0.05, ($targetM - $Me) / max(0.1, ($currentM - $Me)));
        if ($ratio >= 1.0) {
            $remainingHours = 0.5;
        } else {
            $rawRemaining = pow(-log($ratio) / max(0.01, $k), 1.0 / $n);
            $remainingHours = max(0.3, min(24.0, $rawRemaining));
        }

        // Instantaneous Drying Rate (% per hour)
        $moistureDiff = max(0.1, $currentM - $targetM);
        $dryingRatePerHour = $remainingHours > 0 ? ($moistureDiff / $remainingHours) : 1.2;

        // Blend with historical observation if valid
        if ($historicalRate !== null && $historicalRate > 0.1 && $historicalRate < 4.0) {
            $dryingRatePerHour = ($dryingRatePerHour * 0.6) + ($historicalRate * 0.4);
            $remainingHours = $moistureDiff / $dryingRatePerHour;
        }

        $remainingMinutes = $remainingHours * 60.0;

        // 9. Confidence Score Calculation
        $confidence = 88;
        if ($params['solarRadiation'] > 400 && $params['tempInternal'] >= 38) {
            $confidence += 6;
        }
        if ($telemetries && $telemetries->count() >= 6) {
            $confidence += 4;
        }
        $confidence = min(98, max(75, $confidence));

        return [
            'remainingHours' => $remainingHours,
            'remainingMinutes' => $remainingMinutes,
            'dryingRatePerHour' => $dryingRatePerHour,
            'confidenceScore' => $confidence,
            'k' => $k,
            'n' => $n,
            'Me' => $Me,
        ];
    }

    /**
     * Generate step-by-step future projected moisture and environmental curve
     */
    protected function generateForecastTrajectory(
        float $currentMoisture,
        float $targetMoisture,
        float $ratePerHour,
        float $remainingHours,
        float $currentTemp,
        float $currentRh,
        array $weather
    ): array {
        $trajectory = [];
        $now = Carbon::now('Asia/Jakarta');
        
        $steps = max(5, min(12, (int)ceil($remainingHours * 2)));
        $intervalMinutes = max(15, ($remainingHours * 60.0) / max(1, $steps));

        $moistureDelta = max(0.0, $currentMoisture - $targetMoisture);

        for ($i = 0; $i <= $steps; $i++) {
            $stepTime = $now->copy()->addMinutes((int)round($i * $intervalMinutes));
            $progressFraction = $steps > 0 ? ($i / (float)$steps) : 1.0;
            
            $decayFraction = 1.0 - exp(-2.2 * $progressFraction);
            $normalizedDecay = $decayFraction / (1.0 - exp(-2.2));
            $projectedMoisture = max($targetMoisture, $currentMoisture - ($moistureDelta * $normalizedDecay));

            $hour = (int)$stepTime->format('H');
            $solarProjected = 0;
            if ($hour >= 7 && $hour <= 17) {
                $solarProjected = max(0, 850 * sin(M_PI * ($hour - 6) / 11));
            }

            $tempProjected = $currentTemp;
            if ($hour >= 18 || $hour <= 6) {
                $tempProjected = max(34.0, $currentTemp - 4.5);
            }

            $trajectory[] = [
                'step' => $i,
                'time' => $stepTime->format('H:i'),
                'timeIso' => $stepTime->toIso8601String(),
                'timestamp' => $stepTime->timestamp,
                'projectedMoisture' => round($projectedMoisture, 1),
                'projectedTemp' => round($tempProjected, 1),
                'projectedRh' => round(min(75, max(35, $currentRh - ($progressFraction * 6.0))), 0),
                'projectedSolar' => round($solarProjected, 0),
                'isCurrent' => $i === 0,
                'isCompleted' => $i === $steps || $projectedMoisture <= $targetMoisture,
            ];
        }

        return $trajectory;
    }

    /**
     * Fetch real-time and forecasted weather for Waluran Sukabumi via Open-Meteo
     */
    public function getLocalWeatherForecast(): array
    {
        return Cache::remember('sukabumi_weather_forecast', 1800, function () {
            try {
                $url = "https://api.open-meteo.com/v1/forecast";
                $response = Http::timeout(4)->get($url, [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,cloud_cover,direct_normal_irradiance,weather_code',
                    'hourly' => 'temperature_2m,relative_humidity_2m,direct_normal_irradiance,precipitation_probability',
                    'timezone' => 'Asia/Jakarta',
                    'forecast_days' => 1,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];
                    $weatherCode = $current['weather_code'] ?? 1;
                    $condition = $this->interpretWeatherCode($weatherCode);

                    return [
                        'location' => 'Desa Wisata Hanjeli, Waluran, Sukabumi',
                        'temperature' => round($current['temperature_2m'] ?? 29.5, 1),
                        'humidity' => round($current['relative_humidity_2m'] ?? 72, 0),
                        'cloudCover' => round($current['cloud_cover'] ?? 35, 0),
                        'solarIrradiance' => round($current['direct_normal_irradiance'] ?? 650, 0),
                        'precipitation' => round($current['precipitation'] ?? 0.0, 1),
                        'conditionCode' => $weatherCode,
                        'conditionText' => $condition['text'],
                        'conditionIcon' => $condition['icon'],
                        'dryingImpact' => $condition['impact'],
                        'isSunny' => $condition['isSunny'],
                        'source' => 'Open-Meteo Live Satellite Forecast',
                    ];
                }
            } catch (\Throwable $e) {
                Log::info('Weather fetch fallback: ' . $e->getMessage());
            }

            $hour = (int)Carbon::now('Asia/Jakarta')->format('H');
            $isDay = ($hour >= 6 && $hour <= 17);
            $solar = $isDay ? (720 - abs(12 - $hour) * 90) : 0;

            return [
                'location' => 'Desa Wisata Hanjeli, Waluran, Sukabumi',
                'temperature' => $isDay ? 31.0 : 24.5,
                'humidity' => $isDay ? 65 : 82,
                'cloudCover' => 30,
                'solarIrradiance' => max(0, $solar),
                'precipitation' => 0.0,
                'conditionCode' => 1,
                'conditionText' => $isDay ? 'Cerah Berawan' : 'Malam Sejuk & Tenang',
                'conditionIcon' => $isDay ? 'sun' : 'moon',
                'dryingImpact' => $isDay ? 'Kondisi Pengeringan Efektif (Optimal)' : 'Pemanas Bantu Direkomendasikan',
                'isSunny' => $isDay,
                'source' => 'Sukabumi Microclimate Heuristic Model',
            ];
        });
    }

    /**
     * Map WMO Weather Code to Indonesian agricultural context
     */
    protected function interpretWeatherCode(int $code): array
    {
        return match (true) {
            $code === 0 => [
                'text' => 'Cerah Tanpa Awan',
                'icon' => 'sun',
                'impact' => 'Sangat Cepat - Radiasi Surya Maksimal',
                'isSunny' => true,
            ],
            $code >= 1 && $code <= 3 => [
                'text' => 'Cerah Berawan',
                'icon' => 'cloud-sun',
                'impact' => 'Optimal - Suhu Green House Stabil',
                'isSunny' => true,
            ],
            $code >= 45 && $code <= 48 => [
                'text' => 'Berkabut / Lembap',
                'icon' => 'cloud-fog',
                'impact' => 'Perlu Exhaust Fan Ekstra',
                'isSunny' => false,
            ],
            $code >= 51 && $code <= 67 => [
                'text' => 'Gerimis Ringan / Hujan Lokal',
                'icon' => 'cloud-rain',
                'impact' => 'Nyalakan Auxiliary Heater Level 2',
                'isSunny' => false,
            ],
            $code >= 80 && $code <= 99 => [
                'text' => 'Hujan Lebat / Petir',
                'icon' => 'cloud-lightning',
                'impact' => 'Tutup Ventilasi Luar, Aktifkan Pemanas Penuh',
                'isSunny' => false,
            ],
            default => [
                'text' => 'Berawan Sukabumi',
                'icon' => 'cloud',
                'impact' => 'Normal - Pengeringan Hybrid Berjalan',
                'isSunny' => true,
            ],
        };
    }

    /**
     * Generate Actionable AI Recommendations to speed up drying safely
     */
    protected function generateRecommendations(
        float $tempInternal,
        float $humidityInternal,
        float $solarRadiation,
        int $exhaustSpeed,
        bool $heaterStatus,
        array $weather,
        float $ratePerHour,
        float $currentMoisture,
        float $targetMoisture
    ): array {
        $recs = [];

        if ($currentMoisture <= $targetMoisture) {
            $recs[] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'title' => 'Target Kadar Air Tercapai (Siap Simpan)',
                'description' => "Kadar air biji sudah mencapai {$currentMoisture}%. Anda dapat menyelesaikan batch ini dan memindahkan gabah ke ruang pendinginan sebelum pengemasan.",
                'actionText' => 'Selesaikan Batch',
            ];
            return $recs;
        }

        // 1. Airflow recommendation
        if ($humidityInternal > 60 && $exhaustSpeed < 75) {
            $recs[] = [
                'type' => 'info',
                'icon' => 'wind',
                'title' => 'Tingkatkan Kecepatan Exhaust Fan ke 80%',
                'description' => "Kelembaban ruang ({$humidityInternal}% RH) cukup tinggi. Mempercepat evakuasi uap air dapat memangkas waktu pengeringan ~30-45 menit.",
                'actionText' => 'Atur Kipas 80%',
            ];
        }

        // 2. Solar & Heater optimization
        if ($solarRadiation >= 600 && $tempInternal >= 42.0 && $heaterStatus) {
            $recs[] = [
                'type' => 'warning',
                'icon' => 'zap-off',
                'title' => 'Matikan Pemanas Bantu (Hemat Energi)',
                'description' => "Radiasi matahari alami melimpah ({$solarRadiation} W/m²) dan suhu ruang {$tempInternal}°C sudah sangat ideal. Pemanas dapat dimatikan untuk efisiensi listrik.",
                'actionText' => 'Matikan Heater',
            ];
        } elseif ($solarRadiation < 300 && $tempInternal < 38.0 && !$heaterStatus) {
            $recs[] = [
                'type' => 'warning',
                'icon' => 'flame',
                'title' => 'Aktifkan Auxiliary Heater (Pencegahan Perlambatan)',
                'description' => "Radiasi surya rendah ({$solarRadiation} W/m²). Menyalakan pemanas Level 2 akan mempertahankan laju pengeringan stabil di ~1.2%/jam.",
                'actionText' => 'Nyalakan Heater',
            ];
        }

        // 3. Thermal safety warning
        if ($tempInternal > 52.0) {
            $recs[] = [
                'type' => 'danger',
                'icon' => 'alert-triangle',
                'title' => 'Suhu Mendekati Ambang Batas 55°C',
                'description' => "Suhu ruang terlampau panas ({$tempInternal}°C). Buka ventilasi penuh untuk mencegah kerusakan struktur nutrisi dan daya kecambah biji Hanjeli.",
                'actionText' => 'Buka Ventilasi 100%',
            ];
        } else {
            $recs[] = [
                'type' => 'primary',
                'icon' => 'sparkles',
                'title' => 'Kinetika Pengeringan Optimal',
                'description' => "Laju dehidrasi saat ini ~{$ratePerHour}%/jam. Biji terhindar dari *case hardening* dan mutu fisik beras Hanjeli terjaga prima.",
                'actionText' => 'Kondisi Prima',
            ];
        }

        return $recs;
    }

    /**
     * Format minutes into human-readable Indonesian string
     */
    protected function formatDuration(float $minutes): string
    {
        $mins = (int)round($minutes);
        if ($mins <= 0) return '0 Menit (Selesai)';
        
        $hours = floor($mins / 60);
        $remMins = $mins % 60;

        if ($hours > 0 && $remMins > 0) {
            return "{$hours} Jam {$remMins} Menit";
        } elseif ($hours > 0) {
            return "{$hours} Jam";
        } else {
            return "{$remMins} Menit";
        }
    }
}
