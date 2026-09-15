<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BatchCompletedMail;
use App\Mail\CriticalAlertMail;
use App\Mail\SendOtpResetPasswordMail;
use App\Mail\WelcomeUserMail;
use App\Mail\SystemAlertNotificationMail;
use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    /**
     * Get system & IoT settings.
     */
    public function index(): JsonResponse
    {
        $setting = SystemSetting::firstOrCreate([], [
            'is_system_active' => true,
            'iot_mode' => 'SIMULATION',
            'environment_mode' => 'LOCAL',
            'max_safe_temp' => 55.0,
            'min_safe_temp' => 35.0,
            'target_moisture_default' => 12.0,
            'sampling_interval_seconds' => 5,
            'wifi_ssid' => 'GreenHouse_Hanjeli_IoT',
            'ip_address' => '192.168.1.105',
            'mqtt_host' => 'broker.emqx.io',
            'mqtt_port' => 1883,
            'mqtt_topic' => 'greenhouse/hanjeli/dryer01/sensor',
            'whatsapp_config' => [
                'enabled' => true,
                'targetNumber' => '+62 813-8899-2211',
                'apiUrl' => 'https://api.fonnte.com/send',
                'apiKey' => 'wA_s3cret_t0k3n_2023',
            ],
            'telegram_config' => [
                'enabled' => false,
                'botToken' => '6892182910:AAFn23_mock_token',
                'chatId' => '-100192837465',
            ],
        ]);

        return response()->json([
            'isSystemActive' => (bool) ($setting->is_system_active ?? true),
            'iotMode' => $setting->iot_mode ?? 'SIMULATION',
            'environmentMode' => $setting->environment_mode ?? 'LOCAL',
            'maxSafeTemp' => $setting->max_safe_temp,
            'minSafeTemp' => $setting->min_safe_temp,
            'targetMoistureDefault' => $setting->target_moisture_default,
            'samplingIntervalSeconds' => $setting->sampling_interval_seconds,
            'wifiSsid' => $setting->wifi_ssid,
            'ipAddress' => $setting->ip_address,
            'mqttHost' => $setting->mqtt_host,
            'mqttPort' => $setting->mqtt_port,
            'mqttTopic' => $setting->mqtt_topic,
            'whatsapp' => $setting->whatsapp_config,
            'telegram' => $setting->telegram_config,
        ]);
    }

    /**
     * Update system & IoT settings.
     */
    public function update(Request $request): JsonResponse
    {
        $setting = SystemSetting::firstOrCreate([], []);

        $updates = [];
        if ($request->has('isSystemActive')) $updates['is_system_active'] = filter_var($request->isSystemActive, FILTER_VALIDATE_BOOLEAN);
        if ($request->has('iotMode')) $updates['iot_mode'] = strtoupper($request->iotMode);
        if ($request->has('environmentMode')) $updates['environment_mode'] = strtoupper($request->environmentMode);
        if ($request->has('maxSafeTemp')) $updates['max_safe_temp'] = (float) $request->maxSafeTemp;
        if ($request->has('minSafeTemp')) $updates['min_safe_temp'] = (float) $request->minSafeTemp;
        if ($request->has('targetMoistureDefault')) $updates['target_moisture_default'] = (float) $request->targetMoistureDefault;
        if ($request->has('samplingIntervalSeconds')) $updates['sampling_interval_seconds'] = (int) $request->samplingIntervalSeconds;
        if ($request->has('wifiSsid')) $updates['wifi_ssid'] = $request->wifiSsid;
        if ($request->has('ipAddress')) $updates['ip_address'] = $request->ipAddress;
        if ($request->has('mqttHost')) $updates['mqtt_host'] = $request->mqttHost;
        if ($request->has('mqttPort')) $updates['mqtt_port'] = (int) $request->mqttPort;
        if ($request->has('mqttTopic')) $updates['mqtt_topic'] = $request->mqttTopic;
        if ($request->has('whatsapp')) $updates['whatsapp_config'] = $request->whatsapp;
        if ($request->has('telegram')) $updates['telegram_config'] = $request->telegram;

        $setting->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan sistem berhasil disimpan ke database.',
            'settings' => [
                'isSystemActive' => (bool) ($setting->is_system_active ?? true),
                'iotMode' => $setting->iot_mode ?? 'SIMULATION',
                'environmentMode' => $setting->environment_mode ?? 'LOCAL',
                'maxSafeTemp' => $setting->max_safe_temp,
                'minSafeTemp' => $setting->min_safe_temp,
                'targetMoistureDefault' => $setting->target_moisture_default,
                'samplingIntervalSeconds' => $setting->sampling_interval_seconds,
                'wifiSsid' => $setting->wifi_ssid,
                'ipAddress' => $setting->ip_address,
                'mqttHost' => $setting->mqtt_host,
                'mqttPort' => $setting->mqtt_port,
                'mqttTopic' => $setting->mqtt_topic,
                'whatsapp' => $setting->whatsapp_config,
                'telegram' => $setting->telegram_config,
            ],
        ]);
    }

    /**
     * Send a test email using any of the system templates.
     */
    public function testEmail(Request $request): JsonResponse
    {
        $targetEmail = $request->input('email', 'operator@hanjeli.id');
        $type = $request->input('type', 'alert'); // 'otp', 'alert', 'batch', 'welcome'

        try {
            switch ($type) {
                case 'otp':
                    Mail::to($targetEmail)->send(new SendOtpResetPasswordMail('Operator Greenhouse', '849201', 15));
                    $message = "Email uji coba kode OTP berhasil dikirim ke {$targetEmail}.";
                    break;

                case 'batch':
                    $batch = Batch::orderBy('created_at', 'desc')->first();
                    if (!$batch) {
                        $batch = new Batch([
                            'batch_code' => 'HJ-TEST-001',
                            'crop_variety' => 'Hanjeli Ketan Sukabumi (Super)',
                            'initial_weight_kg' => 50.0,
                            'final_weight_kg' => 42.5,
                            'initial_moisture_percent' => 24.5,
                            'final_moisture_percent' => 11.8,
                            'total_duration_hours' => 8.5,
                            'energy_kwh' => 12.8,
                            'quality_grade' => 'Grade A (Ekspor)',
                            'operator_name' => 'Operator Greenhouse',
                        ]);
                    }
                    Mail::to($targetEmail)->send(new BatchCompletedMail($batch));
                    $message = "Email laporan selesai pengeringan batch berhasil dikirim ke {$targetEmail}.";
                    break;

                case 'welcome':
                    Mail::to($targetEmail)->send(new WelcomeUserMail('Operator Greenhouse', $targetEmail, 'OPERATOR'));
                    $message = "Email sambutan akun baru berhasil dikirim ke {$targetEmail}.";
                    break;

                case 'alert':
                default:
                    $sampleAlert = [
                        'title' => 'Uji Coba Notifikasi Sistem Cerdas',
                        'message' => 'Ini adalah pesan uji coba template email universal untuk seluruh notifikasi, status siklus pengeringan, dan alert darurat Smart Dryer Hanjeli.',
                        'level' => 'CRITICAL',
                        'category' => 'SENSOR',
                        'batchCode' => 'HJ-TEST-001',
                        'recordedTime' => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
                    ];
                    Mail::to($targetEmail)->send(new SystemAlertNotificationMail(
                        alert: $sampleAlert,
                        tempInternal: 56.4,
                        humidityInternal: 78.0,
                        grainMoisture: 14.2
                    ));
                    $message = "Email notifikasi alert sistem berhasil dikirim ke {$targetEmail}.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sentTo' => $targetEmail,
                'templateType' => $type,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test message to Telegram Bot / Group.
     */
    public function testTelegram(Request $request): JsonResponse
    {
        $setting = SystemSetting::first();
        $config = $setting?->telegram_config ?? [];

        $botToken = $request->input('botToken', $config['botToken'] ?? '');
        $chatId = $request->input('chatId', $config['chatId'] ?? '');

        if (empty($botToken) || empty($chatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bot Token dan Chat ID Telegram wajib diisi.',
            ], 422);
        }

        $result = \App\Services\TelegramNotificationService::sendTestMessage($botToken, $chatId);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Send test message to WhatsApp Gateway.
     */
    public function testWhatsApp(Request $request): JsonResponse
    {
        $setting = SystemSetting::first();
        $config = $setting?->whatsapp_config ?? [];

        $targetNumber = $request->input('targetNumber', $config['targetNumber'] ?? '');
        $apiUrl = $request->input('apiUrl', $config['apiUrl'] ?? 'https://api.fonnte.com/send');
        $apiKey = $request->input('apiKey', $config['apiKey'] ?? '');
        $provider = $request->input('provider', $config['provider'] ?? 'fonnte');

        if (empty($targetNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp tujuan wajib diisi.',
            ], 422);
        }

        $testConfig = [
            'targetNumber' => $targetNumber,
            'apiUrl' => $apiUrl,
            'apiKey' => $apiKey,
            'provider' => $provider,
        ];

        $result = \App\Services\WhatsAppNotificationService::sendTestMessage($testConfig);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Trigger Daily Digest dispatch on demand.
     */
    public function sendDailyDigest(Request $request): JsonResponse
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('dryer:daily-digest');

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan Harian (Daily Digest) berhasil dikompilasi dan disiarkan ke Telegram & WhatsApp.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyiarkan Daily Digest: ' . $e->getMessage(),
            ], 500);
        }
    }
}
