<?php

namespace App\Console\Commands;

use App\Events\ActuatorUpdated;
use App\Models\ActuatorState;
use App\Services\MqttService;
use Illuminate\Console\Command;
use PhpMqtt\Client\Exceptions\MqttClientException;

class GreenhouseMqttWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'greenhouse:mqtt-worker 
                            {--host= : Override MQTT broker host}
                            {--port= : Override MQTT broker port}
                            {--topic= : Override telemetry topic}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the background MQTT Worker daemon to receive ESP32 hardware telemetry and broadcast to WebSockets';

    /**
     * Execute the console command.
     */
    public function handle(MqttService $mqttService): int
    {
        $host = $this->option('host') ?: config('mqtt.host', 'broker.hivemq.com');
        $port = (int) ($this->option('port') ?: config('mqtt.port', 1883));
        $telemetryTopic = $this->option('topic') ?: config('mqtt.topics.telemetry', 'hanjeli/greenhouse/telemetry');
        $actuatorsTopic = config('mqtt.topics.actuators', 'hanjeli/greenhouse/actuators');

        $this->components->info("Starting Smart Dryer Hanjeli MQTT Worker...");
        $this->components->twoColumnDetail('Broker Host', "{$host}:{$port}");
        $this->components->twoColumnDetail('Telemetry Topic', $telemetryTopic);
        $this->components->twoColumnDetail('Actuators Topic', $actuatorsTopic);
        $this->components->twoColumnDetail('Broadcast Target', 'Laravel Reverb / WebSockets (greenhouse.telemetry)');

        while (true) {
            try {
                $client = $mqttService->createClient();
                $this->info("✓ Connected to MQTT Broker successfully. Listening for ESP32 packets...");

                $telemetryHandler = function (string $topic, string $message) use ($mqttService) {
                    $this->line("<fg=gray>[" . now()->format('H:i:s') . "]</> <fg=cyan>MQTT Inbound [{$topic}]:</> {$message}");

                    $data = json_decode($message, true);
                    if (!is_array($data)) {
                        $this->warn("Received non-JSON payload, skipping.");
                        return;
                    }

                    try {
                        $telemetry = $mqttService->processTelemetryPayload($data);
                        $this->info("✓ Ingested reading ID #{$telemetry->id} (Temp: {$telemetry->temp_internal}°C, Moisture: {$telemetry->grain_moisture}%) -> Broadcasted to WebSockets.");
                    } catch (\Throwable $e) {
                        $this->error("Error processing telemetry payload: " . $e->getMessage());
                    }
                };

                // Subscribe to all standard Telemetry topics
                $client->subscribe('hanjeli/greenhouse/telemetry', $telemetryHandler, 0);
                $client->subscribe('greenhouse/telemetry', $telemetryHandler, 0);
                $client->subscribe('smartroomdryer/device/+/telemetry', $telemetryHandler, 0);
                if ($telemetryTopic !== 'hanjeli/greenhouse/telemetry' && $telemetryTopic !== 'greenhouse/telemetry') {
                    $client->subscribe($telemetryTopic, $telemetryHandler, 0);
                }

                $actuatorHandler = function (string $topic, string $message) {
                    $data = json_decode($message, true);
                    if (is_array($data)) {
                        $state = ActuatorState::firstOrCreate([], []);
                        $actUpdates = array_filter([
                            'exhaust_fan_status' => isset($data['exhaustFanStatus']) ? (bool) $data['exhaustFanStatus'] : null,
                            'exhaust_fan_speed' => isset($data['exhaustFanSpeed']) ? (int) $data['exhaustFanSpeed'] : null,
                            'intake_fan_status' => isset($data['intakeFanStatus']) ? (bool) $data['intakeFanStatus'] : null,
                            'circ_fan_status' => isset($data['circFanStatus']) ? (bool) $data['circFanStatus'] : null,
                            'aux_heater_status' => isset($data['auxHeaterStatus']) ? (bool) $data['auxHeaterStatus'] : (isset($data['heaterStatus']) ? (bool) $data['heaterStatus'] : null),
                            'heater_ch1_status' => isset($data['heaterCh1Status']) ? (bool) $data['heaterCh1Status'] : (isset($data['relayCh1']) ? (bool) $data['relayCh1'] : null),
                            'heater_ch2_status' => isset($data['heaterCh2Status']) ? (bool) $data['heaterCh2Status'] : (isset($data['relayCh2']) ? (bool) $data['relayCh2'] : null),
                            'aux_heater2_status' => isset($data['auxHeater2Status']) ? (bool) $data['auxHeater2Status'] : (isset($data['heaterCh2Status']) ? (bool) $data['heaterCh2Status'] : null),
                            'aux_heater_level' => isset($data['auxHeaterLevel']) ? (int) $data['auxHeaterLevel'] : null,
                        ], fn($v) => !is_null($v));

                        try {
                            $actCols = \Illuminate\Support\Facades\Schema::hasTable('actuator_states')
                                ? \Illuminate\Support\Facades\Schema::getColumnListing('actuator_states')
                                : [];
                            $cleanUpdates = !empty($actCols) ? array_intersect_key($actUpdates, array_flip($actCols)) : $actUpdates;
                            $state->update($cleanUpdates);
                        } catch (\Throwable $e) {
                            $state->update(array_intersect_key($actUpdates, array_flip(['exhaust_fan_status', 'exhaust_fan_speed', 'intake_fan_status', 'circ_fan_status', 'aux_heater_status', 'aux_heater_level'])));
                        }

                        event(new ActuatorUpdated($state));
                        $this->info("✓ Updated actuator state from hardware feedback -> Broadcasted to WebSockets.");
                    }
                };

                // Subscribe to all standard Actuators feedback topics
                $client->subscribe('hanjeli/greenhouse/actuators', $actuatorHandler, 0);
                $client->subscribe('greenhouse/actuators', $actuatorHandler, 0);
                if ($actuatorsTopic !== 'hanjeli/greenhouse/actuators' && $actuatorsTopic !== 'greenhouse/actuators') {
                    $client->subscribe($actuatorsTopic, $actuatorHandler, 0);
                }

                // Enter loop
                $client->loop(true);
            } catch (MqttClientException $e) {
                $this->error("MQTT Client connection error: " . $e->getMessage());
                $this->warn("Reconnecting in 5 seconds...");
                sleep(5);
            } catch (\Throwable $e) {
                $this->error("Unexpected error in worker loop: " . $e->getMessage());
                $this->warn("Reconnecting in 5 seconds...");
                sleep(5);
            }
        }

        return Command::SUCCESS;
    }
}
