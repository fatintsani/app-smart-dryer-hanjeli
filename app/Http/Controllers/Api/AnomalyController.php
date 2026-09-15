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

    /**
     * Simulate anomaly scenario for early warning testing.
     */
    public function simulate(Request $request, MqttService $mqttService): JsonResponse
    {
        $scenario = $request->input('scenario', 'THERMAL_DROP_DOOR_OPEN');

        $activeBatch = Batch::where('status', 'ACTIVE')->orderBy('created_at', 'desc')->first();
        $batchId = $activeBatch?->id;

        // Generate synthetic telemetry matching the requested anomaly scenario
        $payload = match ($scenario) {
            'THERMAL_DROP_DOOR_OPEN' => [
                'batchId' => $batchId,
                'tempInternal' => 24.5, // Sudden drop from typical 45°C
                'humidityInternal' => 78.0,
                'tempExternal' => 31.0,
                'humidityExternal' => 65.0,
                'solarRadiation' => 750.0,
                'grainMoisture' => 14.5,
                'weightKg' => 125.0,
                'heaterStatus' => true,
                'heaterLevel' => 3,
                'exhaustFanStatus' => false,
                'exhaustFanSpeed' => 0,
            ],
            'MOISTURE_SPIKE' => [
                'batchId' => $batchId,
                'tempInternal' => 43.0,
                'humidityInternal' => 60.0,
                'tempExternal' => 30.0,
                'humidityExternal' => 65.0,
                'solarRadiation' => 680.0,
                'grainMoisture' => 18.2, // Sudden spike from ~13%
                'weightKg' => 127.0,
                'heaterStatus' => false,
                'heaterLevel' => 0,
                'exhaustFanStatus' => true,
                'exhaustFanSpeed' => 70,
            ],
            'SENSOR_OUT_OF_BOUNDS' => [
                'batchId' => $batchId,
                'tempInternal' => 99.8, // Impossible physical reading
                'humidityInternal' => 110.0,
                'tempExternal' => 30.0,
                'humidityExternal' => 65.0,
                'solarRadiation' => 650.0,
                'grainMoisture' => 14.0,
                'weightKg' => 125.0,
                'heaterStatus' => false,
                'heaterLevel' => 0,
                'exhaustFanStatus' => true,
                'exhaustFanSpeed' => 50,
            ],
            default => [
                'batchId' => $batchId,
                'tempInternal' => 42.5,
                'humidityInternal' => 48.0,
                'tempExternal' => 30.0,
                'humidityExternal' => 65.0,
                'solarRadiation' => 700.0,
                'grainMoisture' => 13.8,
                'weightKg' => 124.5,
                'heaterStatus' => false,
                'heaterLevel' => 0,
                'exhaustFanStatus' => true,
                'exhaustFanSpeed' => 70,
            ],
        };

        $telemetry = $mqttService->processTelemetryPayload($payload);
        $anomalies = $this->anomalyService->inspectTelemetry($telemetry);

        return response()->json([
            'success' => true,
            'scenario' => $scenario,
            'message' => 'Simulasi anomali sensor berhasil dipicu dan diinjeksi ke sistem telemetri.',
            'detectedAnomalies' => $anomalies,
        ]);
    }
}
