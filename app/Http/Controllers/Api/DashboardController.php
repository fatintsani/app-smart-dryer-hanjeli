<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\Telemetry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get aggregate statistics for the dashboard overview.
     */
    public function stats(): JsonResponse
    {
        $activeBatch = Batch::with(['operator', 'latestTelemetry'])
            ->whereIn('status', ['ACTIVE', 'PAUSED'])
            ->orderBy('created_at', 'desc')
            ->first();

        $completedBatchesCount = Batch::where('status', 'COMPLETED')->count();
        $totalWeightDriedKg = Batch::where('status', 'COMPLETED')->sum('initial_weight_kg');
        $totalEnergyKwh = Batch::where('status', 'COMPLETED')->sum('energy_kwh');
        $averageQualityScore = Batch::where('status', 'COMPLETED')->avg('quality_score') ?? 94.2;

        $latestTelemetry = Telemetry::orderBy('recorded_at', 'desc')->first();
        $actuatorState = ActuatorState::first();

        $dryingProgressPercent = 0;
        if ($activeBatch) {
            $initM = $activeBatch->initial_moisture_percent ?: 24.5;
            $currM = $activeBatch->current_moisture_percent ?: $initM;
            $targM = $activeBatch->target_moisture_percent ?: 12.0;

            if ($initM > $targM) {
                $progress = (($initM - $currM) / ($initM - $targM)) * 100;
                $dryingProgressPercent = round(max(0, min(100, $progress)), 1);
            }
        }

        // Recent completed batches for mini-table
        $recentBatches = Batch::orderBy('created_at', 'desc')->limit(5)->get()->map(function ($b) {
            return [
                'id' => $b->id,
                'batchCode' => $b->batch_code,
                'cropVariety' => $b->crop_variety,
                'status' => $b->status,
                'initialWeightKg' => $b->initial_weight_kg,
                'finalMoisturePercent' => $b->final_moisture_percent ?? $b->current_moisture_percent,
                'totalDurationHours' => $b->total_duration_hours,
                'qualityScore' => $b->quality_score,
                'qualityGrade' => $b->quality_grade,
                'startedAt' => $b->started_at?->toIso8601String(),
                'completedAt' => $b->completed_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'activeBatch' => $activeBatch ? [
                'id' => $activeBatch->id,
                'batchCode' => $activeBatch->batch_code,
                'cropVariety' => $activeBatch->crop_variety,
                'status' => $activeBatch->status,
                'initialWeightKg' => $activeBatch->initial_weight_kg,
                'currentWeightKg' => $activeBatch->current_weight_kg,
                'initialMoisturePercent' => $activeBatch->initial_moisture_percent,
                'currentMoisturePercent' => $activeBatch->current_moisture_percent,
                'targetMoisturePercent' => $activeBatch->target_moisture_percent,
                'dryingMode' => $activeBatch->drying_mode,
                'trayLevel' => $activeBatch->tray_level,
                'operatorName' => $activeBatch->operator_name ?? $activeBatch->operator?->name ?? 'Operator',
                'startedAt' => $activeBatch->started_at?->toIso8601String(),
                'dryingProgressPercent' => $dryingProgressPercent,
            ] : null,
            'summary' => [
                'completedBatchesCount' => $completedBatchesCount,
                'totalWeightDriedKg' => round($totalWeightDriedKg, 1),
                'totalEnergyKwh' => round($totalEnergyKwh, 1),
                'averageQualityScore' => round($averageQualityScore, 1),
            ],
            'currentTelemetry' => [
                'tempInternal' => $latestTelemetry ? (float) $latestTelemetry->temp_internal : 0.0,
                'tempExternal' => $latestTelemetry ? (float) $latestTelemetry->temp_external : 0.0,
                'humidityInternal' => $latestTelemetry ? (float) $latestTelemetry->humidity_internal : 0.0,
                'humidityExternal' => $latestTelemetry ? (float) $latestTelemetry->humidity_external : 0.0,
                'solarRadiation' => $latestTelemetry ? (float) $latestTelemetry->solar_radiation : 0.0,
                'grainMoisture' => $latestTelemetry ? (float) $latestTelemetry->grain_moisture : 0.0,
                'hasData' => $latestTelemetry !== null,
            ],
            'actuators' => [
                'exhaustFanStatus' => $actuatorState?->exhaust_fan_status ?? true,
                'exhaustFanSpeed' => $actuatorState?->exhaust_fan_speed ?? 65,
                'auxHeaterStatus' => $actuatorState?->aux_heater_status ?? true,
                'auxHeaterLevel' => $actuatorState?->aux_heater_level ?? 40,
            ],
            'recentBatches' => $recentBatches,
        ]);
    }
}
