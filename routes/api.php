<?php

use App\Http\Controllers\Api\ActuatorController;
use App\Http\Controllers\Api\AiAssistantController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AnomalyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\DryingPredictionController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TelemetryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Smart Dryer Hanjeli
|--------------------------------------------------------------------------
*/

$smartDryerApi = function () {
    // ---------------------------------------------------------
    // 1. Authentication Routes
    // ---------------------------------------------------------
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/google', [AuthController::class, 'googleAuth']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Passkey / WebAuthn Biometrics
    Route::post('/auth/passkey/options', [AuthController::class, 'passkeyOptions']);
    Route::post('/auth/passkey/verify', [AuthController::class, 'verifyPasskey']);
    Route::post('/auth/passkey/register', [AuthController::class, 'registerPasskey']);

    // ---------------------------------------------------------
    // 2. Dashboard Overview Routes
    // ---------------------------------------------------------
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // ---------------------------------------------------------
    // 3. Batches (Drying Sessions & History) Routes
    // ---------------------------------------------------------
    Route::get('/batches', [BatchController::class, 'index']);
    Route::get('/batches/active', [BatchController::class, 'activeBatch']);
    Route::post('/batches', [BatchController::class, 'store']);
    Route::get('/batches/{id}', [BatchController::class, 'show']);
    Route::put('/batches/{id}', [BatchController::class, 'update']);
    Route::patch('/batches/{id}/pause', [BatchController::class, 'pause']);
    Route::patch('/batches/{id}/resume', [BatchController::class, 'resume']);
    Route::patch('/batches/{id}/complete', [BatchController::class, 'complete']);
    Route::get('/batches/{id}/export', [BatchController::class, 'export']);
    Route::get('/batches/{id}/verification', [BatchController::class, 'publicVerify']);
    Route::get('/batches/{id}/quality-assessment', [BatchController::class, 'getQualityAssessment']);
    Route::get('/batches/{id}/drying-prediction', [DryingPredictionController::class, 'getBatchPrediction']);
    Route::get('/drying/predictive-eta', [DryingPredictionController::class, 'getActivePrediction']);
    Route::get('/drying/weather-forecast', [DryingPredictionController::class, 'getWeather']);
    Route::get('/public/verify/{batch_code}', [BatchController::class, 'publicVerify']);

    // ---------------------------------------------------------
    // 4. Monitoring & Telemetry Routes
    // ---------------------------------------------------------
    Route::get('/telemetry/current', [TelemetryController::class, 'current']);
    Route::get('/telemetry/history', [TelemetryController::class, 'history']);
    Route::post('/telemetry/ingest', [TelemetryController::class, 'ingest']);
    Route::post('/telemetry/downsample', [TelemetryController::class, 'downsample']);
    Route::get('/telemetry/storage-stats', [TelemetryController::class, 'storageStats']);

    // Actuators & Relay Control
    Route::get('/actuators/status', [ActuatorController::class, 'status']);
    Route::patch('/actuators/control', [ActuatorController::class, 'control']);

    // ---------------------------------------------------------
    // 5. Devices, IoT Security & Firmware OTA Routes
    // ---------------------------------------------------------
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::post('/devices/ping-all', [DeviceController::class, 'pingAll']);
    Route::post('/devices/{id}/ping', [DeviceController::class, 'ping']);
    Route::post('/devices/{id}/regenerate-token', [DeviceController::class, 'regenerateToken']);
    Route::post('/devices/{id}/trigger-ota', [DeviceController::class, 'triggerOta']);
    Route::put('/devices/{id}', [DeviceController::class, 'update']);
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy']);

    // Firmware OTA Microcontroller Polling & Releases
    Route::get('/firmware/ota/check', [DeviceController::class, 'checkOta']);
    Route::post('/firmware/ota/progress', [DeviceController::class, 'reportOtaProgress']);
    Route::get('/firmware/releases', [DeviceController::class, 'getFirmwareReleases']);
    Route::post('/firmware/releases', [DeviceController::class, 'createFirmwareRelease']);

    // ---------------------------------------------------------
    // 6. Settings & System Alerts Routes
    // ---------------------------------------------------------
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);
    Route::post('/settings/test-email', [SettingController::class, 'testEmail']);
    Route::post('/settings/test-telegram', [SettingController::class, 'testTelegram']);
    Route::post('/settings/test-whatsapp', [SettingController::class, 'testWhatsApp']);
    Route::post('/settings/daily-digest/send-now', [SettingController::class, 'sendDailyDigest']);

    Route::get('/alerts', [AlertController::class, 'index']);
    Route::patch('/alerts/{id}/read', [AlertController::class, 'markAsRead']);
    Route::patch('/alerts/read-all', [AlertController::class, 'markAllAsRead']);
    Route::delete('/alerts/{id}', [AlertController::class, 'destroy']);
    Route::delete('/alerts/clear', [AlertController::class, 'clear']);

    // Sensor Anomaly Detection Routes
    Route::get('/anomalies/status', [AnomalyController::class, 'status']);
    Route::post('/anomalies/check', [AnomalyController::class, 'check']);
    Route::post('/anomalies/simulate', [AnomalyController::class, 'simulate']);



    // ---------------------------------------------------------
    // 7. AI Copilot & Drying Assistant Routes
    // ---------------------------------------------------------
    Route::post('/ai/chat', [AiAssistantController::class, 'chat']);
    Route::get('/ai/suggestions', [AiAssistantController::class, 'suggestions']);
    Route::get('/ai/context', [AiAssistantController::class, 'context']);

    // ---------------------------------------------------------
    // 8. Authenticated User & Admin Management Routes
    // ---------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // User Management (Admin)
        Route::get('/auth/users', [UserController::class, 'index']);
        Route::post('/auth/users', [UserController::class, 'store']);
        Route::put('/auth/users/{id}', [UserController::class, 'update']);
        Route::put('/auth/users/{id}/role', [UserController::class, 'updateRole']);
        Route::delete('/auth/users/{id}', [UserController::class, 'destroy']);
    });
};

// Direct /api/... routes
$smartDryerApi();

// Also prefix /v1 for backward compatibility
Route::prefix('v1')->group($smartDryerApi);
