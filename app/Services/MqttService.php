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
     * Publish telemetry data to MQTT broker.
     */
    public function publishTelemetry(array $payload): bool
    {
        try {
            $client = $this->createClient();
            $topic = config('mqtt.topics.telemetry', 'hanjeli/greenhouse/telemetry');
            $client->publish($topic, json_encode($payload), 0);
            $client->disconnect();
            return true;
        } catch (\Throwable $e) {
            Log::warning('MQTT Publish Telemetry Error: ' . $e->getMessage());
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
        $isHeaterExplicit = isset($payload['heaterStatus']) || isset($payload['heater_status']) || isset($payload['heater_on']) || isset($payload['auxHeaterStatus']);
        if ($isHeaterExplicit) {
            $heaterActive = (bool) ($payload['heaterStatus'] ?? $payload['heater_status'] ?? $payload['heater_on'] ?? $payload['auxHeaterStatus'] ?? false);
        } else {
            // Evaluasi logika hardware Smart Dryer Hanjeli (Jadwal NTP WIB + Sensor Trigger):
            // 1. Suhu >= 50.0°C -> Lockout Wajib OFF (SNI)
            // 2. Jadwal Operasional NTP WIB: 18:00 - 05:00 WIB -> Otomatis ON
            // 3. Di luar jadwal (siang hari): ON jika Hujan || Lembap >= 70% || Suhu <= 35.0°C
            $tempVal = (float) ($payload['tempInternal'] ?? $payload['temp_internal'] ?? $payload['temperature'] ?? 0.0);
            $humVal = (float) ($payload['humidityInternal'] ?? $payload['humidity_internal'] ?? $payload['humidity'] ?? 0.0);
            $isRain = (bool) ($payload['isRaining'] ?? $payload['is_raining'] ?? false);
            $currentHourWib = Carbon::now('Asia/Jakarta')->hour;
            $isNightSchedule = ($currentHourWib >= 18 || $currentHourWib < 5);

            if ($tempVal >= 50.0) {
                $heaterActive = false;
            } elseif ($isNightSchedule) {
                $heaterActive = true;
            } else {
                $heaterActive = ($isRain || $humVal >= 70.0 || $tempVal <= 35.0);
            }
        }

        $telemetry = Telemetry::create([
            'batch_id' => $batchId,
            'temp_internal' => (float) ($payload['tempInternal'] ?? $payload['temp_internal'] ?? $payload['temperature'] ?? 0.0),
            'humidity_internal' => (float) ($payload['humidityInternal'] ?? $payload['humidity_internal'] ?? $payload['humidity'] ?? 0.0),
            'temp_external' => (float) ($payload['tempExternal'] ?? $payload['temp_external'] ?? 30.0),
            'humidity_external' => (float) ($payload['humidityExternal'] ?? $payload['humidity_external'] ?? 65.0),
            'solar_radiation' => (float) ($payload['solarRadiation'] ?? $payload['solar_radiation'] ?? $payload['lux'] ?? $payload['light'] ?? 700.0),
            'is_raining' => (bool) ($payload['isRaining'] ?? $payload['is_raining'] ?? false),
            'grain_moisture' => (float) ($payload['grainMoisture'] ?? $payload['grain_moisture'] ?? $payload['soil'] ?? 14.0),
            'weight_kg' => (float) ($payload['weightKg'] ?? $payload['weight_kg'] ?? 45.0),
            'heater_status' => $heaterActive,
            'heater_level' => (int) ($payload['heaterLevel'] ?? $payload['heater_level'] ?? ($heaterActive ? 100 : 0)),
            'exhaust_fan_status' => (bool) ($payload['exhaustFanStatus'] ?? $payload['exhaust_fan_status'] ?? ($payload['fan'] ?? '') === 'ON'),
            'exhaust_fan_speed' => (int) ($payload['exhaustFanSpeed'] ?? $payload['exhaust_fan_speed'] ?? 50),
            'recorded_at' => Carbon::now(),
        ]);

        // Sync ActuatorState when system is running in AUTO (non-override) mode
        $actuatorState = ActuatorState::first();
        if ($actuatorState && !$actuatorState->is_override_active) {
            $actuatorState->update([
                'aux_heater_status' => $heaterActive,
                'aux_heater_level' => $heaterActive ? 100 : 0,
            ]);
        }

        // 3. Update Batch Current Metrics & Check Moisture Target
        if ($batchId) {
            $batch = Batch::find($batchId);
            if ($batch && $batch->status === 'ACTIVE') {
                $batchData = [
                    'current_moisture_percent' => $telemetry->grain_moisture,
                    'current_weight_kg' => $telemetry->weight_kg,
                ];

                // Live quality grading calculation from real telemetry
                try {
                    $scoringService = app(\App\Services\QualityScoringService::class);
                    $assessment = $scoringService->evaluateBatchQuality($batch, (float) $telemetry->grain_moisture);
                    $batchData['quality_score'] = $assessment['qualityScore'];
                    $batchData['quality_grade'] = $assessment['gradeLabel'];
                } catch (\Throwable $e) {
                    Log::warning('Live quality evaluation skipped: ' . $e->getMessage());
                }

                $batch->update($batchData);

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

            $deviceId = $payload['deviceId'] ?? $payload['device_id'] ?? 'inode-AOO2';
            event(new TelemetryUpdated($telemetry, $actuatorData, $deviceId));
        } catch (\Throwable $e) {
            Log::debug('WebSocket broadcast skipped (Reverb offline/standby): ' . $e->getMessage());
        }

        return $telemetry;
    }
}
