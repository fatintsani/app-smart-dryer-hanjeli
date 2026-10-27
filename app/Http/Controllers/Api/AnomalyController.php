<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\Telemetry;
use App\Services\MqttService;
use App\Services\SensorAnomalyDetectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnomalyController extends Controller
{
    protected SensorAnomalyDetectionService $anomalyService;

    public function __construct(SensorAnomalyDetectionService $anomalyService)
    {
        $this->anomalyService = $anomalyService;
    }

    /**
     * Get real-time sensor health matrix and active anomaly diagnosis.
     */
    public function status(): JsonResponse
    {
        $matrix = $this->anomalyService->getHealthMatrix();

        return response()->json($matrix);
    }

    /**
     * Run on-demand anomaly inspection on the latest telemetry.
     */
    public function check(): JsonResponse
    {
        $latest = Telemetry::orderBy('recorded_at', 'desc')->first();
        if (!$latest) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data telemetri yang tersimpan untuk diinspeksi.',
            ], 404);
        }

        $actuator = ActuatorState::latest()->first();
        $anomalies = $this->anomalyService->inspectTelemetry($latest, $actuator);

        return response()->json([
            'success' => true,
            'anomalyCount' => count($anomalies),
            'anomalies' => $anomalies,
        ]);
    }
}

