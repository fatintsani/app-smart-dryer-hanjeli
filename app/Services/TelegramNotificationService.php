<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Send emergency / critical alert to Telegram.
     */
    public static function sendEmergencyAlert(
        SystemAlert $alert,
        ?float $tempInternal = null,
        ?float $humidityInternal = null,
        ?float $grainMoisture = null
    ): bool {
        try {
            $setting = SystemSetting::first();
            $config = $setting?->telegram_config ?? [];

            if (empty($config['enabled']) || empty($config['botToken']) || empty($config['chatId'])) {
                return false;
            }

            // Check event filter if specified
            if (isset($config['events']['emergency']) && !$config['events']['emergency']) {
                return false;
            }

            $botToken = $config['botToken'];
            $chatId = $config['chatId'];

            $levelEmoji = match ($alert->level) {
                'CRITICAL' => '*[PERINGATAN KRITIS]*',
                'WARNING' => '*[PERINGATAN]*',
                default => '*[INFORMASI]*',
            };

            $timeStr = Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';

            $text = "{$levelEmoji}\n\n";
            $text .= "*{$alert->title}*\n";
            $text .= "{$alert->message}\n\n";
            $text .= "• *Lokasi:* Greenhouse Hanjeli Sukabumi\n";
            $text .= "• *Waktu:* {$timeStr}\n";

            if ($tempInternal !== null || $humidityInternal !== null || $grainMoisture !== null) {
                $text .= "\n*[Kondisi Sensor Terkini]*\n";
                if ($tempInternal !== null) $text .= "• Suhu Ruang: `{$tempInternal} °C`\n";
                if ($humidityInternal !== null) $text .= "• Kelembaban: `{$humidityInternal} % RH`\n";
                if ($grainMoisture !== null) $text .= "• Kadar Air: `{$grainMoisture} %`\n";
            }

            $text .= "\n_Buka Dashboard: " . url('/dashboard') . "_";

            return self::sendMessage($botToken, $chatId, $text);
        } catch (\Throwable $e) {
            Log::warning('Telegram emergency alert dispatch failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send batch completed notification to Telegram.
     */
    public static function sendBatchCompletedAlert(Batch $batch): bool
    {
        try {
            $setting = SystemSetting::first();
            $config = $setting?->telegram_config ?? [];

            if (empty($config['enabled']) || empty($config['botToken']) || empty($config['chatId'])) {
                return false;
            }

            if (isset($config['events']['batch_completed']) && !$config['events']['batch_completed']) {
                return false;
            }

            $botToken = $config['botToken'];
            $chatId = $config['chatId'];

            $finalMoisture = $batch->final_moisture_percent ?? $batch->current_moisture_percent ?? 12.0;
            $initialMoisture = $batch->initial_moisture_percent ?? 24.5;
            $durationHours = $batch->total_duration_hours ?? 8.5;
            $grade = $batch->quality_grade ?? 'Grade A (Standar SNI)';
            $timeStr = Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';

            $text = "*[SESI PENGERINGAN SELESAI (TARGET MOISTURE REACHED)]*\n\n";
            $text .= "• *Kode Batch:* `{$batch->batch_code}`\n";
            $text .= "• *Varietas:* {$batch->crop_variety}\n";
            $text .= "• *Bobot:* {$batch->initial_weight_kg} kg -> *{$batch->final_weight_kg} kg*\n";
            $text .= "• *Kadar Air:* {$initialMoisture}% -> *{$finalMoisture}%* (Target Tercapai)\n";
            $text .= "• *Total Durasi:* {$durationHours} Jam\n";
            $text .= "• *Grade Kualitas:* *{$grade}*\n";
            $text .= "• *Estimasi Energi:* {$batch->energy_kwh} kWh\n";
            $text .= "• *Waktu Selesai:* {$timeStr}\n\n";
            $text .= "_Verifikasi Sertifikat & Mutu: " . url('/verify/' . $batch->batch_code) . "_";

            return self::sendMessage($botToken, $chatId, $text);
        } catch (\Throwable $e) {
            Log::warning('Telegram batch completed alert failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Daily Digest summary to Telegram.
     */
    public static function sendDailyDigest(array $stats): bool
    {
        try {
            $setting = SystemSetting::first();
            $config = $setting?->telegram_config ?? [];

            if (empty($config['enabled']) || empty($config['botToken']) || empty($config['chatId'])) {
                return false;
            }

            if (isset($config['events']['daily_digest']) && !$config['events']['daily_digest']) {
                return false;
            }

            $botToken = $config['botToken'];
            $chatId = $config['chatId'];

            $dateStr = Carbon::now()->translatedFormat('l, d F Y');
            $activeBatchStr = !empty($stats['activeBatchCode']) ? "`{$stats['activeBatchCode']}` ({$stats['activeMoisture']}%)" : "Tidak ada batch aktif";

            $text = "*[RINGKASAN HARIAN PENGERINGAN GREENHOUSE (DAILY DIGEST)]*\n";
            $text .= "• *Tanggal:* {$dateStr}\n";
            $text .= "• *Fasilitas:* Desa Wisata Hanjeli, Waluran, Sukabumi\n\n";

            $text .= "*[Operasional & Produksi]*\n";
            $text .= "• Batch Selesai Hari Ini: *{$stats['batchesCompletedToday']} batch*\n";
            $text .= "• Total Hanjeli Dikeringkan: *{$stats['totalKgToday']} kg*\n";
            $text .= "• Batch Sedang Berjalan: {$activeBatchStr}\n\n";

            $text .= "*[Rata-rata Lingkungan]*\n";
            $text .= "• Suhu Internal Rata-rata: *{$stats['avgTempInternal']} °C*\n";
            $text .= "• Kelembaban Internal Rata-rata: *{$stats['avgHumidityInternal']} % RH*\n";
            $text .= "• Radiasi Surya Maksimum: *{$stats['maxSolarRadiation']} W/m²*\n\n";

            $text .= "*[Integritas & Kesehatan Alat]*\n";
            $text .= "• Skor Sensor Health: *{$stats['healthScore']}%* ({$stats['healthStatus']})\n";
            $text .= "• Total Peringatan Hari Ini: *{$stats['alertCountToday']} alert*\n";
            $text .= "• Konsumsi Energi: *{$stats['totalEnergyKwh']} kWh*\n\n";

            $text .= "_Pantau Sistem: " . url('/dashboard') . "_";

            return self::sendMessage($botToken, $chatId, $text);
        } catch (\Throwable $e) {
            Log::warning('Telegram daily digest failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test Telegram connection by sending a sample message.
     */
    public static function sendTestMessage(string $botToken, string $chatId): array
    {
        $timeStr = Carbon::now()->translatedFormat('d F Y, H:i:s') . ' WIB';

        $text = "*[UJI COBA KONEKSI TELEGRAM BOT SMART DRYER]*\n\n";
        $text .= "• *Status:* Terhubung & Berfungsi Normal\n";
        $text .= "• *Fasilitas:* Greenhouse Hanjeli Waluran Sukabumi\n";
        $text .= "• *Waktu Uji:* {$timeStr}\n\n";
        $text .= "Pesan ini mengonfirmasi bahwa Bot Token dan Chat ID Telegram Anda telah dikonfigurasi dengan benar di sistem Smart Dryer. Seluruh notifikasi darurat, status batch selesai, dan daily digest akan disiarkan ke chat ini.";

        $response = self::executeTelegramRequest($botToken, $chatId, $text);

        return $response;
    }

    /**
     * Send raw markdown message to Telegram Bot API.
     */
    protected static function sendMessage(string $botToken, string $chatId, string $text): bool
    {
        $res = self::executeTelegramRequest($botToken, $chatId, $text);
        return $res['success'] ?? false;
    }

    /**
     * Execute HTTP POST to Telegram Bot API.
     */
    protected static function executeTelegramRequest(string $botToken, string $chatId, string $text): array
    {
        $botToken = trim($botToken);
        $chatId = trim($chatId);

        if (empty($botToken) || empty($chatId)) {
            return [
                'success' => false,
                'message' => 'Bot Token dan Chat ID tidak boleh kosong.',
            ];
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        try {
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
            ]);

            $json = $response->json();

            if ($response->successful() && !empty($json['ok'])) {
                Log::info("Telegram message successfully delivered to chat ID {$chatId}");
                return [
                    'success' => true,
                    'message' => 'Pesan Telegram berhasil dikirim ke Chat ID ' . $chatId,
                    'data' => $json['result'] ?? null,
                ];
            }

            $errMsg = $json['description'] ?? 'Gagal mengirim pesan Telegram (Kode: ' . $response->status() . ')';
            Log::warning("Telegram API Error: {$errMsg}");

            return [
                'success' => false,
                'message' => $errMsg,
                'error_code' => $json['error_code'] ?? $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('Telegram HTTP Client Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke Telegram API gagal: ' . $e->getMessage(),
            ];
        }
    }
}
