<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Send emergency / critical alert to WhatsApp.
     */
    public static function sendEmergencyAlert(
        SystemAlert $alert,
        ?float $tempInternal = null,
        ?float $humidityInternal = null,
        ?float $grainMoisture = null
    ): bool {
        try {
            $setting = SystemSetting::first();
            $config = $setting?->whatsapp_config ?? [];

            if (empty($config['enabled']) || empty($config['targetNumber'])) {
                return false;
            }

            if (isset($config['events']['emergency']) && !$config['events']['emergency']) {
                return false;
            }

            $levelEmoji = match ($alert->level) {
                'CRITICAL' => '*[PERINGATAN KRITIS SMART DRYER]*',
                'WARNING' => '*[PERINGATAN SMART DRYER]*',
                default => '*[INFORMASI SMART DRYER]*',
            };

            $timeStr = Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';

            $message = "{$levelEmoji}\n\n";
            $message .= "*{$alert->title}*\n";
            $message .= "{$alert->message}\n\n";
            $message .= "• *Lokasi:* Greenhouse Hanjeli Sukabumi\n";
            $message .= "• *Waktu:* {$timeStr}\n";

            if ($tempInternal !== null || $humidityInternal !== null || $grainMoisture !== null) {
                $message .= "\n*[Sensor Terkini]*\n";
                if ($tempInternal !== null) $message .= "• Suhu Ruang: {$tempInternal} °C\n";
                if ($humidityInternal !== null) $message .= "• Kelembaban: {$humidityInternal} % RH\n";
                if ($grainMoisture !== null) $message .= "• Kadar Air: {$grainMoisture} %\n";
            }

            $message .= "\nDashboard: " . url('/dashboard');

            return self::sendMessage($config, $message);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp emergency alert dispatch failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send batch completed notification to WhatsApp.
     */
    public static function sendBatchCompletedAlert(Batch $batch): bool
    {
        try {
            $setting = SystemSetting::first();
            $config = $setting?->whatsapp_config ?? [];

            if (empty($config['enabled']) || empty($config['targetNumber'])) {
                return false;
            }

            if (isset($config['events']['batch_completed']) && !$config['events']['batch_completed']) {
                return false;
            }

            $finalMoisture = $batch->final_moisture_percent ?? $batch->current_moisture_percent ?? 12.0;
            $initialMoisture = $batch->initial_moisture_percent ?? 24.5;
            $durationHours = $batch->total_duration_hours ?? 8.5;
            $grade = $batch->quality_grade ?? 'Grade A (Standar SNI)';
            $timeStr = Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';

            $message = "*[SESI PENGERINGAN SELESAI (TARGET MOISTURE REACHED)]*\n\n";
            $message .= "• *Kode Batch:* {$batch->batch_code}\n";
            $message .= "• *Varietas:* {$batch->crop_variety}\n";
            $message .= "• *Bobot:* {$batch->initial_weight_kg} kg -> *{$batch->final_weight_kg} kg*\n";
            $message .= "• *Kadar Air:* {$initialMoisture}% -> *{$finalMoisture}%* (Target Tercapai)\n";
            $message .= "• *Total Durasi:* {$durationHours} Jam\n";
            $message .= "• *Grade Kualitas:* *{$grade}*\n";
            $message .= "• *Estimasi Energi:* {$batch->energy_kwh} kWh\n";
            $message .= "• *Waktu Selesai:* {$timeStr}\n\n";
            $message .= "Sertifikat & Traceability: " . url('/verify/' . $batch->batch_code);

            return self::sendMessage($config, $message);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp batch completed alert failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Daily Digest summary to WhatsApp.
     */
    public static function sendDailyDigest(array $stats): bool
    {
        try {
            $setting = SystemSetting::first();
            $config = $setting?->whatsapp_config ?? [];

            if (empty($config['enabled']) || empty($config['targetNumber'])) {
                return false;
            }

            if (isset($config['events']['daily_digest']) && !$config['events']['daily_digest']) {
                return false;
            }

            $dateStr = Carbon::now()->translatedFormat('l, d F Y');
            $activeBatchStr = !empty($stats['activeBatchCode']) ? "{$stats['activeBatchCode']} ({$stats['activeMoisture']}%)" : "Tidak ada";

            $message = "*[RINGKASAN HARIAN PENGERINGAN GREENHOUSE (DAILY DIGEST)]*\n";
            $message .= "• *Tanggal:* {$dateStr}\n";
            $message .= "• *Fasilitas:* Desa Wisata Hanjeli, Waluran, Sukabumi\n\n";

            $message .= "*[Operasional & Produksi]*\n";
            $message .= "• Batch Selesai Hari Ini: *{$stats['batchesCompletedToday']} batch*\n";
            $message .= "• Total Hanjeli Dikeringkan: *{$stats['totalKgToday']} kg*\n";
            $message .= "• Batch Berjalan: {$activeBatchStr}\n\n";

            $message .= "*[Rata-rata Lingkungan]*\n";
            $message .= "• Suhu Internal Rata-rata: *{$stats['avgTempInternal']} °C*\n";
            $message .= "• Kelembaban Internal Rata-rata: *{$stats['avgHumidityInternal']} % RH*\n";
            $message .= "• Radiasi Surya Maksimum: *{$stats['maxSolarRadiation']} W/m²*\n\n";

            $message .= "*[Integritas & Kesehatan Alat]*\n";
            $message .= "• Sensor Health Score: *{$stats['healthScore']}%* ({$stats['healthStatus']})\n";
            $message .= "• Total Peringatan: *{$stats['alertCountToday']} alert*\n";
            $message .= "• Konsumsi Energi: *{$stats['totalEnergyKwh']} kWh*\n\n";

            $message .= "Pantau Real-time: " . url('/dashboard');

            return self::sendMessage($config, $message);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp daily digest failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a test WhatsApp message.
     */
    public static function sendTestMessage(array $config): array
    {
        $targetNumber = $config['targetNumber'] ?? $config['target_number'] ?? '';
        $apiUrl = $config['apiUrl'] ?? $config['api_url'] ?? 'https://api.fonnte.com/send';
        $apiKey = $config['apiKey'] ?? $config['api_key'] ?? '';
        $provider = $config['provider'] ?? 'fonnte';

        if (empty($targetNumber)) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tujuan tidak boleh kosong.',
            ];
        }

        $timeStr = Carbon::now()->translatedFormat('d F Y, H:i:s') . ' WIB';

        $message = "*[UJI COBA WHATSAPP GATEWAY SMART DRYER HANJELI]*\n\n";
        $message .= "• *Status:* Terhubung & Aktif\n";
        $message .= "• *Fasilitas:* Greenhouse Hanjeli Waluran Sukabumi\n";
        $message .= "• *Waktu Uji:* {$timeStr}\n\n";
        $message .= "Pesan ini mengonfirmasi integrasi WhatsApp Gateway pada nomor *{$targetNumber}* telah berhasil dikonfigurasi. Sistem akan mengirimkan notifikasi batch selesai, alert darurat, dan ringkasan harian secara otomatis.";

        return self::executeGatewayRequest($apiUrl, $apiKey, $targetNumber, $message, $provider);
    }

    /**
     * Send message using configured gateway provider.
     */
    protected static function sendMessage(array $config, string $message): bool
    {
        $targetNumber = $config['targetNumber'] ?? $config['target_number'] ?? '';
        $apiUrl = $config['apiUrl'] ?? $config['api_url'] ?? 'https://api.fonnte.com/send';
        $apiKey = $config['apiKey'] ?? $config['api_key'] ?? '';
        $provider = $config['provider'] ?? 'fonnte';

        if (empty($targetNumber)) return false;

        $res = self::executeGatewayRequest($apiUrl, $apiKey, $targetNumber, $message, $provider);
        return $res['success'] ?? false;
    }

    /**
     * Dispatch HTTP request to WhatsApp Gateway (Fonnte, Wablas, or Custom Webhook).
     */
    protected static function executeGatewayRequest(string $apiUrl, string $apiKey, string $targetNumber, string $message, string $provider = 'fonnte'): array
    {
        // Sanitize phone number (remove +, spaces, hyphens)
        $cleanNumber = preg_replace('/[^0-9]/', '', $targetNumber);
        if (str_starts_with($cleanNumber, '08')) {
            $cleanNumber = '628' . substr($cleanNumber, 2);
        }

        try {
            $httpClient = Http::timeout(12);

            // 1. Fonnte Gateway
            if (str_contains($apiUrl, 'fonnte.com') || $provider === 'fonnte') {
                $response = $httpClient->withHeaders([
                    'Authorization' => $apiKey,
                ])->post($apiUrl ?: 'https://api.fonnte.com/send', [
                    'target' => $cleanNumber,
                    'message' => $message,
                    'countryCode' => '62',
                ]);
            }
            // 2. Wablas Gateway
            elseif (str_contains($apiUrl, 'wablas.com') || $provider === 'wablas') {
                $response = $httpClient->withHeaders([
                    'Authorization' => $apiKey,
                ])->post($apiUrl, [
                    'phone' => $cleanNumber,
                    'message' => $message,
                ]);
            }
            // 3. Generic Webhook Gateway (Twilio / Custom Server)
            else {
                $response = $httpClient->withHeaders([
                    'Authorization' => $apiKey ? "Bearer {$apiKey}" : '',
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, [
                    'recipient' => $cleanNumber,
                    'phone' => $cleanNumber,
                    'message' => $message,
                    'type' => 'SMART_DRYER_ALERT',
                    'timestamp' => now()->toIso8601String(),
                ]);
            }

            $json = $response->json() ?? [];
            $isSuccess = $response->successful() && (!isset($json['status']) || $json['status'] === true || $json['status'] === 'success' || (isset($json['status']) && $json['status'] == 200));

            if ($isSuccess) {
                Log::info("WhatsApp message successfully dispatched to {$cleanNumber}");
                return [
                    'success' => true,
                    'message' => "Pesan WhatsApp berhasil dikirim ke nomor {$cleanNumber}.",
                    'data' => $json,
                ];
            }

            $errMsg = $json['reason'] ?? $json['message'] ?? 'Gateway menolak pengiriman pesan (Status: ' . $response->status() . ')';
            Log::warning("WhatsApp Gateway Error: {$errMsg}");

            return [
                'success' => false,
                'message' => $errMsg,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp HTTP Gateway Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke WhatsApp Gateway gagal: ' . $e->getMessage(),
            ];
        }
    }
}
