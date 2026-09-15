<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DryingPredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DryingPredictionController extends Controller
{
    protected DryingPredictionService $predictionService;

    public function __construct(DryingPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Get predictive drying time ETA for currently active batch.
     */
    public function getActivePrediction(Request $request): JsonResponse
    {
        $prediction = $this->predictionService->predictBatchEta();

        return response()->json($prediction);
    }

    /**
     * Get predictive drying time ETA for a specific batch ID.
     */
    public function getBatchPrediction(Request $request, int $id): JsonResponse
    {
        $prediction = $this->predictionService->predictBatchEta($id);

        return response()->json($prediction);
    }

    /**
     * Get live Sukabumi local weather context.
     */
    public function getWeather(Request $request): JsonResponse
    {
        $weather = $this->predictionService->getLocalWeatherForecast();

        return response()->json([
            'success' => true,
            'weather' => $weather,
        ]);
    }
}
