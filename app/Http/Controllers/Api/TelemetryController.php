<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CriticalAlertMail;
use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\Telemetry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TelemetryController extends Controller
{
    /**
     * Get the latest telemetry and actuator status.
     */
    public function current(): JsonResponse
    {
        $latest = Telemetry::orderBy('recorded_at', 'desc')->first();
        $actuators = ActuatorState::first();

        if (!$actuators) {
            $actuators = ActuatorState::create([
                'exhaust_fan_status' => true,
                'exhaust_fan_speed' => 65,
                'intake_fan_status' => true,
                'circ_fan_status' => true,
                'aux_heater_status' => true,
                'aux_heater_level' => 40,
                'is_override_active' => false,
                'override_mode' => 'AUTO',
            ]);
        }

        $now = Carbon::now();
        $diffSeconds = $latest ? abs($latest->recorded_at->diffInSeconds($now)) : null;
        $isLive = $latest && $diffSeconds <= 90;
        $deviceCode = \App\Models\Device::where('is_active', true)->where('status', 'online')->value('code') 
            ?? \App\Models\Device::where('is_active', true)->value('code') 
            ?? 'inode-AOO2';

        if (!$latest) {
            return response()->json([
                'telemetry' => [
                    'deviceId' => $deviceCode,
                    'tempInternal' => 0.0,
                    'humidityInternal' => 0.0,
                    'tempExternal' => 0.0,
                    'humidityExternal' => 0.0,
                    'solarRadiation' => 0.0,
                    'isRaining' => false,
                    'grainMoisture' => 0.0,
                    'weightCurrentKg' => 0.0,
                    'heaterStatus' => false,
                    'heaterLevel' => 0,
                    'hasData' => false,
                    'isLive' => false,
                    'timestamp' => $now->toIso8601String(),
                ],
                'actuators' => [
                    'exhaustFanStatus' => $actuators->exhaust_fan_status,
                    'exhaustFanSpeed' => $actuators->exhaust_fan_speed,
                    'intakeFanStatus' => $actuators->intake_fan_status,
                    'circFanStatus' => $actuators->circ_fan_status,
                    'auxHeaterStatus' => $actuators->aux_heater_status,
                    'auxHeaterLevel' => $actuators->aux_heater_level,
                    'isOverrideActive' => $actuators->is_override_active,
                    'overrideMode' => $actuators->override_mode,
                ],
            ]);
        }

        return response()->json([
            'telemetry' => [
                'id' => $latest->id,
                'deviceId' => $deviceCode,
                'tempInternal' => (float) $latest->temp_internal,
                'humidityInternal' => (float) $latest->humidity_internal,
                'tempExternal' => (float) $latest->temp_external,
                'humidityExternal' => (float) $latest->humidity_external,
                'solarRadiation' => (float) $latest->solar_radiation,
                'isRaining' => (bool) $latest->is_raining,
                'grainMoisture' => (float) $latest->grain_moisture,
                'weightCurrentKg' => (float) $latest->weight_kg,
                'heaterStatus' => (bool) $latest->heater_status,
                'heaterLevel' => (int) $latest->heater_level,
                'hasData' => true,
                'isLive' => $isLive,
                'lastSeenSeconds' => $diffSeconds,
                'timestamp' => $latest->recorded_at->toIso8601String(),
            ],
            'actuators' => [
                'exhaustFanStatus' => $actuators->exhaust_fan_status,
                'exhaustFanSpeed' => $actuators->exhaust_fan_speed,
                'intakeFanStatus' => $actuators->intake_fan_status,
                'circFanStatus' => $actuators->circ_fan_status,
                'auxHeaterStatus' => $actuators->aux_heater_status,
                'auxHeaterLevel' => $actuators->aux_heater_level,
                'isOverrideActive' => $actuators->is_override_active,
                'overrideMode' => $actuators->override_mode,
            ],
        ]);
    }

    /**
     * Get historical telemetry data points with multi-period analytics (1D, 1W, 1M, All).
     */
    public function history(Request $request): JsonResponse
    {
        $period = $request->query('period');
        $hours = (int) ($request->query('hours', 24));

        if ($period === '1d') {
            $hours = 24;
        } elseif ($period === '1w') {
            $hours = 168;
        } elseif ($period === '1m') {
            $hours = 720;
        } elseif ($period === 'all') {
            $hours = 87600; // 10 years
        }

        $batchId = $request->query('batchId');
        $query = Telemetry::orderBy('recorded_at', 'asc');

        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $since = Carbon::now()->subHours($hours);
            $query->where('recorded_at', '>=', $since);

            // Fallback: If no telemetry in the last X hours from now(),
            // anchor to the latest recorded timestamp in database so operator always gets real data
            if ($query->count() === 0 && Telemetry::exists()) {
                $latestTime = Telemetry::max('recorded_at') ?? Carbon::now();
                $sinceAnchor = Carbon::parse($latestTime)->subHours($hours);
                $query = Telemetry::where('recorded_at', '>=', $sinceAnchor)->orderBy('recorded_at', 'asc');
            }
        }

        $rawRecords = $query->limit(600)->get();

        // Downsample evenly if records exceed 60 to keep SVG rendering fast & sleek
        $totalRaw = $rawRecords->count();
        if ($totalRaw > 60) {
            $step = $totalRaw / 60;
            $sampled = collect();
            for ($i = 0; $i < 60; $i++) {
                $idx = min((int) round($i * $step), $totalRaw - 1);
                $sampled->push($rawRecords[$idx]);
            }
            $targetRecords = $sampled;
        } else {
            $targetRecords = $rawRecords;
        }

        $records = $targetRecords->map(function ($t) use ($hours) {
            $d = $t->recorded_at ?? $t->created_at;
            // Format label according to timeframe length
            $label = $hours <= 24 ? $d->format('H:i') : ($hours <= 168 ? $d->format('d M H:i') : $d->format('d M'));
            return [
                'id' => $t->id,
                'tempInternal' => (float) $t->temp_internal,
                'humidityInternal' => (float) $t->humidity_internal,
                'tempExternal' => $t->temp_external !== null ? (float) $t->temp_external : null,
                'humidityExternal' => $t->humidity_external !== null ? (float) $t->humidity_external : null,
                'solarRadiation' => $t->solar_radiation !== null ? (float) $t->solar_radiation : null,
                'grainMoisture' => $t->grain_moisture !== null ? (float) $t->grain_moisture : null,
                'weightCurrentKg' => $t->weight_kg !== null ? (float) $t->weight_kg : null,
                'heaterStatus' => (bool) $t->heater_status,
                'timestamp' => $d->toIso8601String(),
                'label' => $label,
                'fullDate' => $d->translatedFormat('d M Y, H:i') . ' WIB',
            ];
        });

        // Compute analytical metrics from the raw records for maximum statistical precision
        $count = $rawRecords->count();
        if ($count > 0) {
            $avgTemp = round($rawRecords->avg('temp_internal'), 1);
            $minTemp = round($rawRecords->min('temp_internal'), 1);
            $maxTemp = round($rawRecords->max('temp_internal'), 1);
            $avgHum = round($rawRecords->avg('humidity_internal'), 0);
            $minHum = round($rawRecords->min('humidity_internal'), 0);
            $maxHum = round($rawRecords->max('humidity_internal'), 0);

            $idealCount = $rawRecords->filter(fn($r) => $r->temp_internal >= 40 && $r->temp_internal <= 50)->count();
            $idealRatio = round(($idealCount / $count) * 100, 1);

            $heaterActiveCount = $rawRecords->filter(fn($r) => !empty($r->heater_status))->count();
            $heaterRatio = round(($heaterActiveCount / $count) * 100, 1);

            if ($idealRatio >= 80) {
                $stabilityLabel = 'Sangat Stabil (Standar SNI)';
                $stabilityClass = 'status-excellent';
            } elseif ($idealRatio >= 50) {
                $stabilityLabel = 'Cukup Stabil';
                $stabilityClass = 'status-good';
            } else {
                $stabilityLabel = 'Perlu Penyesuaian Pemanas';
                $stabilityClass = 'status-warning';
            }

            $periodName = $period === '1w' ? '1 Minggu' : ($period === '1m' ? '1 Bulan' : ($period === 'all' ? 'Seluruh Riwayat' : '1 Hari Terakhir'));
            $insightMessage = "Dalam periode {$periodName}, {$idealRatio}% waktu suhu ruang pengering stabil di zona ideal SNI (40°C - 50°C). Suhu terendah {$minTemp}°C dan tertinggi {$maxTemp}°C.";
        } else {
            $avgTemp = 44.5;
            $minTemp = 38.0;
            $maxTemp = 49.0;
            $avgHum = 58;
            $minHum = 42;
            $maxHum = 68;
            $idealRatio = 88.0;
            $heaterRatio = 25.0;
            $stabilityLabel = 'Optimal';
            $stabilityClass = 'status-excellent';
            $insightMessage = 'Belum ada telemetri fisik terekam pada rentang waktu ini. Menampilkan simulasi data ideal untuk evaluasi visual.';
        }

        $analytics = [
            'period' => $period ?? ($hours <= 24 ? '1d' : ($hours <= 168 ? '1w' : ($hours <= 720 ? '1m' : 'all'))),
            'hours' => $hours,
            'totalPoints' => $count,
            'avgTemp' => $avgTemp,
            'minTemp' => $minTemp,
            'maxTemp' => $maxTemp,
            'avgHumidity' => $avgHum,
            'minHumidity' => $minHum,
            'maxHumidity' => $maxHum,
            'idealRatioPercent' => $idealRatio,
            'heaterActivePercent' => $heaterRatio,
            'stabilityLabel' => $stabilityLabel,
            'stabilityClass' => $stabilityClass,
            'insightMessage' => $insightMessage,
        ];

        if ($request->has('period') || $request->boolean('include_analytics', false)) {
            return response()->json([
                'records' => $records->values(),
                'analytics' => $analytics,
            ]);
        }

        return response()->json($records->values());
    }

    /**
     * Ingest telemetry reading from ESP32 or Simulator.
     */
    public function ingest(Request $request, \App\Services\MqttService $mqttService): JsonResponse
    {
        // 1. Authenticate Hardware Device via X-Device-Token, X-Device-Key, Bearer Token, or input
        $token = $request->header('X-Device-Token') 
            ?? $request->header('X-Device-Key')
            ?? $request->header('X-API-Key')
            ?? $request->bearerToken() 
            ?? $request->header('Authorization') 
            ?? $request->input('device_token')
            ?? $request->input('api_key');

        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        // Master bypass tokens for local simulation engine or active device matching
        $isLocalSimToken = $token === 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c' 
            || $token === 'esp32-sim-token-master'
            || $token === 'DEVICE_API_KEY_BARU'
            || $token === 'NCtJiDY-xuz9iPr8LYy4Mb15bXN-tB3EJOcrJ-ei754';

        $validDevice = null;
        if ($token && !$isLocalSimToken) {
            try {
                $validDevice = \App\Models\Device::where(function ($q) use ($token) {
                    $q->where('device_token', $token)
                      ->orWhere('code', $token);
                })->where('is_active', true)->first();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Device lookup failed: ' . $e->getMessage());
            }
        }

        if (!$validDevice && !$isLocalSimToken) {
            return response()->json([
                'success' => false,
                'error' => 'UNAUTHORIZED_DEVICE',
                'message' => 'Akses ditolak: X-Device-Token atau X-Device-Key tidak valid atau tidak disertakan pada header permintaan. Silakan konfigurasikan token perangkat resmi melalui menu Admin Perangkat.',
            ], 401);
        }

        // Normalize incoming payload for both camelCase and snake_case ESP32 payloads
        $tempInternal = $request->input('tempInternal', $request->input('temp_internal', $request->input('temperature')));
        $humidityInternal = $request->input('humidityInternal', $request->input('humidity_internal', $request->input('rh_internal', $request->input('rhInternal', $request->input('humidity')))));
        $tempExternal = $request->input('tempExternal', $request->input('temp_external', $request->input('external_temp')));
        $humidityExternal = $request->input('humidityExternal', $request->input('humidity_external', $request->input('rh_external', $request->input('rhInternal', $request->input('external_humidity')))));
        $solarRadiation = $request->input('solarRadiation', $request->input('solar_radiation', $request->input('solar_intensity', $request->input('lux'))));
        $grainMoisture = $request->input('grainMoisture', $request->input('grain_moisture', $request->input('moisture_grain')));
        $weightKg = $request->input('weightKg', $request->input('weight_kg', $request->input('weight')));

        if ($tempInternal === null || $humidityInternal === null) {
            return response()->json([
                'success' => false,
                'error' => 'VALIDATION_ERROR',
                'message' => 'Suhu internal (tempInternal / temp_internal / temperature) dan Kelembapan internal (humidityInternal / humidity_internal / humidity) wajib disertakan.',
            ], 422);
        }

        $isHeaterOn = $request->boolean('heaterStatus', $request->boolean('heater_status', $request->boolean('heater_on')));

        $payload = [
            'batchId' => $request->input('batchId', $request->input('batch_id')),
            'tempInternal' => (float) $tempInternal,
            'humidityInternal' => (float) $humidityInternal,
            'tempExternal' => $tempExternal !== null ? (float) $tempExternal : null,
            'humidityExternal' => $humidityExternal !== null ? (float) $humidityExternal : null,
            'solarRadiation' => $solarRadiation !== null ? (float) $solarRadiation : null,
            'grainMoisture' => $grainMoisture !== null ? (float) $grainMoisture : null,
            'weightKg' => $weightKg !== null ? (float) $weightKg : null,
            'heaterStatus' => $isHeaterOn,
            'heaterLevel' => (int) $request->input('heaterLevel', $request->input('heater_level', $isHeaterOn ? 100 : 0)),
            'exhaustFanStatus' => $request->boolean('exhaustFanStatus', $request->boolean('exhaust_fan_status')),
            'exhaustFanSpeed' => (int) $request->input('exhaustFanSpeed', $request->input('exhaust_fan_speed', 0)),
            'isRaining' => $request->boolean('is_raining', $request->boolean('isRaining')),
            'rainAdc' => (int) $request->input('rain_adc', $request->input('rainAdc', 4095)),
            'dhtOk' => $request->boolean('dht_ok', $request->boolean('dhtOk', true)),
            'bh1750Ok' => $request->boolean('bh1750_ok', $request->boolean('bh1750Ok', true)),
        ];

        $incomingDeviceId = $request->input('deviceId', $request->input('device_id', $validDevice?->code ?? 'inode-AOO2'));

        if ($validDevice) {
            $validDevice->update([
                'last_heartbeat' => Carbon::now()->locale('id')->diffForHumans(),
                'status' => 'online',
            ]);
        }

        $payload['deviceId'] = $incomingDeviceId;
        $telemetry = $mqttService->processTelemetryPayload($payload);
        $actuatorState = ActuatorState::first();

        return response()->json([
            'success' => true,
            'id' => $telemetry->id,
            'deviceId' => $incomingDeviceId,
            'recordedAt' => $telemetry->recorded_at->toIso8601String(),
            'actuators' => $actuatorState ? [
                'exhaustFanStatus' => (bool) $actuatorState->exhaust_fan_status,
                'exhaustFanSpeed' => (int) $actuatorState->exhaust_fan_speed,
                'intakeFanStatus' => (bool) $actuatorState->intake_fan_status,
                'circFanStatus' => (bool) $actuatorState->circ_fan_status,
                'auxHeaterStatus' => (bool) $actuatorState->aux_heater_status,
                'auxHeaterLevel' => (int) $actuatorState->aux_heater_level,
                'isOverrideActive' => (bool) $actuatorState->is_override_active,
                'overrideMode' => (string) $actuatorState->override_mode,
            ] : null,
        ], 201);
    }

    /**
     * Trigger manual telemetry downsampling / database compression.
     */
    public function downsample(Request $request, \App\Services\TelemetryAggregationService $service): JsonResponse
    {
        $batchId = $request->input('batchId', $request->input('batch_id'));
        $intervalMinutes = max(1, (int) $request->input('intervalMinutes', $request->input('interval', 5)));
        $force = (bool) $request->input('force', false);

        if ($batchId) {
            $batch = Batch::where('id', $batchId)->orWhere('batch_code', $batchId)->first();
            if (!$batch) {
                return response()->json([
                    'success' => false,
                    'message' => "Batch '{$batchId}' tidak ditemukan.",
                ], 404);
            }

            $result = $service->downsampleBatch($batch, $intervalMinutes, $force);
            return response()->json([
                'success' => true,
                'message' => $result['status'] === 'SUCCESS' 
                    ? "Berhasil mengompresi data telemetri batch #{$batch->batch_code} (hemat {$result['savedRows']} baris / {$result['reductionPercentage']}%)." 
                    : $result['reason'],
                'result' => $result,
            ]);
        }

        $summary = $service->downsampleAllCompletedBatches($intervalMinutes, $force);

        return response()->json([
            'success' => true,
            'message' => "Proses downsampling selesai untuk {$summary['totalBatchesProcessed']} batch (hemat {$summary['totalSavedRows']} baris / {$summary['overallReductionPercentage']}%).",
            'summary' => $summary,
        ]);
    }

    /**
     * Get database telemetry storage usage and compression statistics.
     */
    public function storageStats(\App\Services\TelemetryAggregationService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'stats' => $service->getStorageStats(),
        ]);
    }
}
