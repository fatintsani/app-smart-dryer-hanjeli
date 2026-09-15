<?php

namespace App\Services;

use App\Events\ActuatorUpdated;
use App\Events\AlertTriggered;
use App\Events\TelemetryUpdated;
use App\Mail\CriticalAlertMail;
use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use App\Models\Telemetry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    protected ?MqttClient $client = null;

    /**
     * Create an MQTT Client instance.
     */
    public function createClient(): MqttClient
    {
        $host = config('mqtt.host', 'broker.hivemq.com');
        $port = (int) config('mqtt.port', 1883);
        $clientId = config('mqtt.client_id', 'smartdryer_hanjeli_' . uniqid());

        $client = new MqttClient($host, $port, $clientId);

        $settings = (new ConnectionSettings())
            ->setKeepAliveInterval(config('mqtt.keep_alive', 60))
            ->setUseTls(config('mqtt.use_tls', false));

        $username = config('mqtt.username');
        $password = config('mqtt.password');
        if ($username) {
            $settings->setUsername($username)->setPassword($password);
        }

        $client->connect($settings, config('mqtt.clean_session', true));

        return $client;
    }

    /**
     * Publish actuator control command to MQTT broker.
     */
    public function publishControl(array $actuatorData): bool
    {
        try {
            $client = $this->createClient();
            $topic = config('mqtt.topics.control', 'hanjeli/greenhouse/control');
            $payload = json_encode([
                'type' => 'ACTUATOR_CONTROL',
                'timestamp' => now()->toIso8601String(),
                'data' => $actuatorData,
            ]);

            $client->publish($topic, $payload, 0);
            $client->disconnect();
            return true;
        } catch (\Throwable $e) {
            Log::warning('MQTT Publish Control Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ingest and process telemetry payload (from MQTT or HTTP).
     */
    public function processTelemetryPayload(array $payload): Telemetry
    {
        // 1. Resolve Active Batch
        $batchId = $payload['batchId'] ?? $payload['batch_id'] ?? null;
        if (!$batchId) {
            $activeBatch = Batch::where('status', 'ACTIVE')->orderBy('created_at', 'desc')->first();
            $batchId = $activeBatch?->id;
        }

        // 2. Save Telemetry with flexible schema mapping
        $telemetry = Telemetry::create([
            'batch_id' => $batchId,
            'temp_internal' => (float) ($payload['tempInternal'] ?? $payload['temp_internal'] ?? $payload['temperature'] ?? 0.0),
            'humidity_internal' => (float) ($payload['humidityInternal'] ?? $payload['humidity_internal'] ?? $payload['humidity'] ?? 0.0),
            'temp_external' => (float) ($payload['tempExternal'] ?? $payload['temp_external'] ?? 30.0),
            'humidity_external' => (float) ($payload['humidityExternal'] ?? $payload['humidity_external'] ?? 65.0),
            'solar_radiation' => (float) ($payload['solarRadiation'] ?? $payload['solar_radiation'] ?? $payload['light'] ?? 700.0),
            'grain_moisture' => (float) ($payload['grainMoisture'] ?? $payload['grain_moisture'] ?? $payload['soil'] ?? 14.0),
            'weight_kg' => (float) ($payload['weightKg'] ?? $payload['weight_kg'] ?? 45.0),
            'heater_status' => (bool) ($payload['heaterStatus'] ?? $payload['heater_status'] ?? false),
            'heater_level' => (int) ($payload['heaterLevel'] ?? $payload['heater_level'] ?? 0),
            'exhaust_fan_status' => (bool) ($payload['exhaustFanStatus'] ?? $payload['exhaust_fan_status'] ?? ($payload['fan'] ?? '') === 'ON'),
            'exhaust_fan_speed' => (int) ($payload['exhaustFanSpeed'] ?? $payload['exhaust_fan_speed'] ?? 50),
            'recorded_at' => Carbon::now(),
        ]);

        // 3. Update Batch Current Metrics & Check Moisture Target
        if ($batchId) {
            $batch = Batch::find($batchId);
            if ($batch && $batch->status === 'ACTIVE') {
                $batch->update([
                    'current_moisture_percent' => $telemetry->grain_moisture,
                    'current_weight_kg' => $telemetry->weight_kg,
                ]);

                if ($telemetry->grain_moisture <= ($batch->target_moisture_percent ?? 12.0)) {
                    $alreadyAlerted = SystemAlert::where('batch_id', $batch->id)
                        ->where('title', 'like', '%Target Kadar Air%')
                        ->exists();

                    if (!$alreadyAlerted) {
                        $alert = SystemAlert::create([
                            'batch_id' => $batch->id,
                            'level' => 'INFO',
                            'category' => 'BATCH',
                            'title' => 'Target Kadar Air Tercapai',
                            'message' => "Kadar air hanjeli pada batch #{$batch->batch_code} telah mencapai {$telemetry->grain_moisture}% (Target: {$batch->target_moisture_percent}%). Gabah siap dipanen!",
                            'is_read' => false,
                        ]);
                        event(new AlertTriggered($alert));
                    }
                }
            }
        }

        // 4. Threshold Checks & Alerts
        $setting = SystemSetting::first();
        $maxSafeTemp = $setting?->max_safe_temp ?? 55.0;
        $minSafeTemp = $setting?->min_safe_temp ?? 35.0;

        if ($telemetry->temp_internal > $maxSafeTemp) {
            $recentTempAlert = SystemAlert::where('category', 'SENSOR')
                ->where('title', 'like', '%Suhu Panas%')
                ->where('created_at', '>=', Carbon::now()->subMinutes(15))
                ->exists();

            if (!$recentTempAlert) {
                $title = 'Peringatan Suhu Panas Ruang Pengering';
                $msg = "Suhu ruang pengering mencapai {$telemetry->temp_internal}°C (melebihi batas aman {$maxSafeTemp}°C). Kipas sirkulasi & exhaust ditingkatkan.";

                $alert = SystemAlert::create([
                    'batch_id' => $batchId,
                    'level' => 'CRITICAL',
                    'category' => 'SENSOR',
                    'title' => $title,
                    'message' => $msg,
                    'is_read' => false,
                ]);
            }
        } elseif ($telemetry->temp_internal < $minSafeTemp && $batchId) {
            $recentColdAlert = SystemAlert::where('category', 'SENSOR')
                ->where('title', 'like', '%Suhu Rendah%')
                ->where('created_at', '>=', Carbon::now()->subMinutes(30))
                ->exists();

            if (!$recentColdAlert) {
                $alert = SystemAlert::create([
                    'batch_id' => $batchId,
                    'level' => 'WARNING',
                    'category' => 'SENSOR',
                    'title' => 'Suhu Rendah di Ruang Pengering',
                    'message' => "Suhu ruang pengering turun ke {$telemetry->temp_internal}°C (di bawah batas optimal {$minSafeTemp}°C). Pemanas bantu direkomendasikan.",
                    'is_read' => false,
                ]);
                event(new AlertTriggered($alert));
            }
        }

        if ($telemetry->humidity_internal >= 82.0 && $batchId) {
            $recentHumAlert = SystemAlert::where('category', 'SENSOR')
                ->where('title', 'like', '%Kelembapan Udara%')
                ->where('created_at', '>=', Carbon::now()->subMinutes(25))
                ->exists();

            if (!$recentHumAlert) {
                $alert = SystemAlert::create([
                    'batch_id' => $batchId,
                    'level' => 'WARNING',
                    'category' => 'SENSOR',
                    'title' => 'Kelembapan Udara Ruang Tinggi',
                    'message' => "Kelembapan udara internal mencapai {$telemetry->humidity_internal}% RH. Pastikan kipas pembuangan bekerja optimal.",
                    'is_read' => false,
                ]);
                event(new AlertTriggered($alert));
            }
        }

        // 5. Intelligent Multi-Pattern Sensor Anomaly Detection
        try {
            $actuators = ActuatorState::first();
            $anomalyService = app(\App\Services\SensorAnomalyDetectionService::class);
            $anomalyService->inspectTelemetry($telemetry, $actuators);
        } catch (\Throwable $e) {
            Log::warning('Anomaly detection inspection error: ' . $e->getMessage());
        }

        // 6. Broadcast Telemetry Event to WebSockets (Laravel Reverb)
        try {
            $actuators = ActuatorState::first();
            $actuatorData = $actuators ? [
                'exhaustFanStatus' => (bool) $actuators->exhaust_fan_status,
                'exhaustFanSpeed' => (int) $actuators->exhaust_fan_speed,
                'intakeFanStatus' => (bool) $actuators->intake_fan_status,
                'circFanStatus' => (bool) $actuators->circ_fan_status,
                'auxHeaterStatus' => (bool) $actuators->aux_heater_status,
                'auxHeaterLevel' => (int) $actuators->aux_heater_level,
                'isOverrideActive' => (bool) $actuators->is_override_active,
                'overrideMode' => (string) $actuators->override_mode,
            ] : null;

            event(new TelemetryUpdated($telemetry, $actuatorData));
        } catch (\Throwable $e) {
            Log::debug('WebSocket broadcast skipped (Reverb offline/standby): ' . $e->getMessage());
        }

        return $telemetry;
    }
}
