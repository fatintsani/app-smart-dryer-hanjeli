<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationDispatchService
{
    /**
     * Dispatch an emergency / system alert across all active notification channels (Email, Telegram, WhatsApp).
     */
    public static function dispatchSystemAlert(
        SystemAlert $alert,
        ?float $tempInternal = null,
        ?float $humidityInternal = null,
        ?float $grainMoisture = null
    ): void {
        // Anti-spam throttle: prevent repeated identical alerts within 10 minutes
        $throttleKey = 'notif_dispatch_throttle_' . md5($alert->title . '_' . $alert->level . '_' . ($alert->batch_id ?? 0));
        if (Cache::has($throttleKey) && $alert->level !== 'CRITICAL') {
            Log::debug("Notification dispatch throttled for alert #{$alert->id}: '{$alert->title}'");
            return;
        }
        Cache::put($throttleKey, true, 600); // 10 mins

        // 1. Email Channel
        try {
            AlertNotificationService::sendAlertEmail($alert, $tempInternal, $humidityInternal, $grainMoisture);
        } catch (\Throwable $e) {
            Log::warning('Email alert dispatch error: ' . $e->getMessage());
        }

        // 2. Telegram Bot Channel
        try {
            TelegramNotificationService::sendEmergencyAlert($alert, $tempInternal, $humidityInternal, $grainMoisture);
        } catch (\Throwable $e) {
            Log::warning('Telegram alert dispatch error: ' . $e->getMessage());
        }

        // 3. WhatsApp Gateway Channel
        try {
            WhatsAppNotificationService::sendEmergencyAlert($alert, $tempInternal, $humidityInternal, $grainMoisture);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp alert dispatch error: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch batch completion notification across all active channels.
     */
    public static function dispatchBatchCompleted(Batch $batch): void
    {
        Log::info("Dispatching batch completed notifications for Batch {$batch->batch_code}");

        // 1. Telegram Bot Channel
        try {
            TelegramNotificationService::sendBatchCompletedAlert($batch);
        } catch (\Throwable $e) {
            Log::warning('Telegram batch completed notification error: ' . $e->getMessage());
        }

        // 2. WhatsApp Gateway Channel
        try {
            WhatsAppNotificationService::sendBatchCompletedAlert($batch);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp batch completed notification error: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch daily digest summary across all active channels.
     */
    public static function dispatchDailyDigest(array $stats): void
    {
        Log::info("Dispatching Daily Digest across active channels");

        // 1. Telegram Bot Channel
        try {
            TelegramNotificationService::sendDailyDigest($stats);
        } catch (\Throwable $e) {
            Log::warning('Telegram daily digest notification error: ' . $e->getMessage());
        }

        // 2. WhatsApp Gateway Channel
        try {
            WhatsAppNotificationService::sendDailyDigest($stats);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp daily digest notification error: ' . $e->getMessage());
        }
    }
}
