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
        $validated = $request->validate([
            'batchId' => 'nullable',
            'tempInternal' => 'required|numeric',
            'humidityInternal' => 'required|numeric',
            'tempExternal' => 'nullable|numeric',
            'humidityExternal' => 'nullable|numeric',
            'solarRadiation' => 'nullable|numeric',
            'grainMoisture' => 'nullable|numeric',
            'weightKg' => 'nullable|numeric',
            'heaterStatus' => 'nullable|boolean',
            'heaterLevel' => 'nullable|integer',
            'exhaustFanStatus' => 'nullable|boolean',
            'exhaustFanSpeed' => 'nullable|integer',
        ]);

        $telemetry = $mqttService->processTelemetryPayload($validated);

        return response()->json([
            'success' => true,
            'id' => $telemetry->id,
            'recordedAt' => $telemetry->recorded_at->toIso8601String(),
        ], 201);
    }
}
