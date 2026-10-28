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
        $isLive = $latest && abs($latest->recorded_at->diffInSeconds($now)) <= 20;

        if (!$latest || !$isLive) {
            return response()->json([
                'telemetry' => [
                    'tempInternal' => $latest ? $latest->temp_internal : 0.0,
                    'humidityInternal' => $latest ? $latest->humidity_internal : 0.0,
                    'tempExternal' => $latest ? $latest->temp_external : 0.0,
                    'humidityExternal' => $latest ? $latest->humidity_external : 0.0,
                    'solarRadiation' => $latest ? $latest->solar_radiation : 0.0,
                    'grainMoisture' => $latest ? $latest->grain_moisture : 0.0,
                    'weightCurrentKg' => $latest ? $latest->weight_kg : 0.0,
                    'heaterStatus' => $latest ? $latest->heater_status : false,
                    'heaterLevel' => $latest ? $latest->heater_level : 0,
                    'hasData' => false,
                    'isLive' => false,
                    'timestamp' => $latest ? $latest->recorded_at->toIso8601String() : $now->toIso8601String(),
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
                'tempInternal' => $latest->temp_internal,
                'humidityInternal' => $latest->humidity_internal,
                'tempExternal' => $latest->temp_external,
                'humidityExternal' => $latest->humidity_external,
                'solarRadiation' => $latest->solar_radiation,
                'grainMoisture' => $latest->grain_moisture,
                'weightCurrentKg' => $latest->weight_kg,
                'heaterStatus' => $latest->heater_status,
                'heaterLevel' => $latest->heater_level,
                'hasData' => true,
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
     * Get historical telemetry data points.
     */
    public function history(Request $request): JsonResponse
    {
        $hours = (int) ($request->query('hours', 24));
        $batchId = $request->query('batchId');

        $query = Telemetry::orderBy('recorded_at', 'asc');

        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $since = Carbon::now()->subHours($hours);
            $query->where('recorded_at', '>=', $since);
        }

        $records = $query->limit(500)->get()->map(function ($t) {
            $d = $t->recorded_at ?? $t->created_at;
            return [
                'id' => $t->id,
                'tempInternal' => $t->temp_internal,
                'humidityInternal' => $t->humidity_internal,
                'tempExternal' => $t->temp_external,
                'humidityExternal' => $t->humidity_external,
                'solarRadiation' => $t->solar_radiation,
                'grainMoisture' => $t->grain_moisture,
                'weightCurrentKg' => $t->weight_kg,
                'timestamp' => $d->toIso8601String(),
                'label' => $d->format('H:i'),
            ];
        });

        return response()->json($records);
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

        if ($validDevice) {
            $validDevice->update([
                'last_heartbeat' => Carbon::now()->locale('id')->diffForHumans(),
                'status' => 'online',
            ]);
        }

        $telemetry = $mqttService->processTelemetryPayload($payload);
        $actuatorState = ActuatorState::first();

        return response()->json([
            'success' => true,
            'id' => $telemetry->id,
            'deviceId' => $validDevice?->code ?? 'SRD-001',
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
