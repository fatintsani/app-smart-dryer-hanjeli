<?php

namespace App\Services;

use App\Mail\SystemAlertNotificationMail;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertNotificationService
{
    /**
     * Send email notification for a system alert.
     */
    public static function sendAlertEmail(
        SystemAlert $alert,
        ?float $tempInternal = null,
        ?float $humidityInternal = null,
        ?float $grainMoisture = null
    ): bool {
        try {
            $setting = SystemSetting::first();
            
            // Check if mail alerts are disabled in settings if configured
            if ($setting && isset($setting->whatsapp_config['mail_disabled']) && $setting->whatsapp_config['mail_disabled']) {
                return false;
            }

            // Gather all user email addresses (Admins & Operators)
            $recipients = User::pluck('email')
                ->filter(fn ($email) => !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values()
                ->all();

            // Check if there is an additional target email specified
            $additionalEmail = config('mail.from.address');
            if (empty($recipients)) {
                $recipients = [$additionalEmail ?? 'operator@hanjeli.id'];
            }

            // Determine direct CTA route
            $route = match ($alert->category) {
                'SENSOR' => url('/monitoring'),
                'BATCH' => $alert->batch_id ? url('/history') : url('/active-drying'),
                'ACTUATOR' => url('/monitoring'),
                'SYSTEM' => url('/settings'),
                default => url('/monitoring'),
            };

            // Send Mailable
            Mail::to($recipients)->send(new SystemAlertNotificationMail(
                alert: $alert,
                tempInternal: $tempInternal,
                humidityInternal: $humidityInternal,
                grainMoisture: $grainMoisture,
                actionUrl: $route
            ));

            Log::info("System alert email dispatched to " . count($recipients) . " recipients for alert ID #{$alert->id}: '{$alert->title}'");
            return true;
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch alert email for alert ID #{$alert->id}: " . $e->getMessage());
            return false;
        }
    }
}
