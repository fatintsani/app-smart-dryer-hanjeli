<?php

namespace Tests\Feature;

use App\Events\ActuatorUpdated;
use App\Events\AlertTriggered;
use App\Events\TelemetryUpdated;
use App\Models\Batch;
use App\Models\Telemetry;
use App\Services\MqttService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RealtimeBroadcastingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_telemetry_ingestion_dispatches_telemetry_updated_broadcast_event(): void
    {
        Event::fake([TelemetryUpdated::class]);

        $response = $this->postJson('/api/telemetry/ingest', [
            'tempInternal' => 48.5,
            'humidityInternal' => 45.2,
            'tempExternal' => 31.0,
            'humidityExternal' => 60.0,
            'solarRadiation' => 820.0,
            'grainMoisture' => 12.8,
            'weightKg' => 41.5,
            'heaterStatus' => false,
            'exhaustFanSpeed' => 75,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        Event::assertDispatched(TelemetryUpdated::class, function (TelemetryUpdated $event) {
            return $event->telemetry['tempInternal'] === 48.5
                && $event->telemetry['grainMoisture'] === 12.8
                && $event->broadcastOn()[0]->name === 'greenhouse.telemetry';
        });
    }

    public function test_actuator_control_dispatches_actuator_updated_broadcast_event(): void
    {
        Event::fake([ActuatorUpdated::class]);

        $response = $this->patchJson('/api/actuators/control', [
            'exhaustFanSpeed' => 85,
            'isOverrideActive' => true,
            'overrideMode' => 'MANUAL',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        Event::assertDispatched(ActuatorUpdated::class, function (ActuatorUpdated $event) {
            return $event->actuators['exhaustFanSpeed'] === 85
                && $event->actuators['isOverrideActive'] === true
                && $event->broadcastOn()[0]->name === 'greenhouse.actuators';
        });
    }

    public function test_mqtt_service_processes_payload_and_fires_events(): void
    {
        Event::fake([TelemetryUpdated::class, AlertTriggered::class]);

        /** @var MqttService $mqttService */
        $mqttService = app(MqttService::class);

        $telemetry = $mqttService->processTelemetryPayload([
            'tempInternal' => 62.0, // Exceeds safe temp -> triggers critical alert
            'humidityInternal' => 50.0,
            'grainMoisture' => 11.2,
            'weightKg' => 38.0,
        ]);

        $this->assertInstanceOf(Telemetry::class, $telemetry);
        $this->assertEquals(62.0, $telemetry->temp_internal);

        Event::assertDispatched(TelemetryUpdated::class);
        Event::assertDispatched(AlertTriggered::class, function (AlertTriggered $event) {
            return $event->alert['level'] === 'CRITICAL'
                && $event->broadcastOn()[0]->name === 'greenhouse.alerts';
        });
    }
}
