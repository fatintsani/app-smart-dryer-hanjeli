<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Device;
use App\Models\User;
use Database\Seeders\SmartDryerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartDryerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_dashboard_stats_returns_valid_data(): void
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'activeBatch',
                'summary' => [
                    'completedBatchesCount',
                    'totalWeightDriedKg',
                    'totalEnergyKwh',
                    'averageQualityScore',
                ],
                'currentTelemetry',
                'actuators',
                'recentBatches',
            ]);
    }

    public function test_batches_crud_and_lifecycle(): void
    {
        // 1. Get all batches
        $response = $this->getJson('/api/batches');
        $response->assertStatus(200);

        // 2. Start a new drying batch
        $createRes = $this->postJson('/api/batches', [
            'cropVariety' => 'Hanjeli Ketan Sukabumi (Grade A Test)',
            'initialWeightKg' => 75.0,
            'initialMoisturePercent' => 24.5,
            'targetMoisturePercent' => 12.0,
            'dryingMode' => 'HYBRID_AUTO',
            'operatorName' => 'Operator Test',
        ]);

        $createRes->assertStatus(201)
            ->assertJsonPath('success', true);

        $batchId = $createRes->json('batch.id');

        // 3. Pause batch
        $pauseRes = $this->patchJson("/api/batches/{$batchId}/pause");
        $pauseRes->assertStatus(200)
            ->assertJsonPath('batch.status', 'PAUSED');

        // 4. Resume batch
        $resumeRes = $this->patchJson("/api/batches/{$batchId}/resume");
        $resumeRes->assertStatus(200)
            ->assertJsonPath('batch.status', 'ACTIVE');

        // 5. Complete batch
        $completeRes = $this->patchJson("/api/batches/{$batchId}/complete", [
            'finalMoisturePercent' => 11.9,
            'finalWeightKg' => 64.2,
        ]);
        $completeRes->assertStatus(200)
            ->assertJsonPath('batch.status', 'COMPLETED')
            ->assertJsonPath('batch.finalMoisturePercent', 11.9);

        // 6. Export batch
        $exportRes = $this->getJson("/api/batches/{$batchId}/export");
        $exportRes->assertStatus(200)
            ->assertJsonStructure([
                'batchCode',
                'cropVariety',
                'initialWeightKg',
                'finalMoisturePercent',
            ]);
    }

    public function test_telemetry_and_actuators_endpoints(): void
    {
        // Current Telemetry
        $currentRes = $this->getJson('/api/telemetry/current');
        $currentRes->assertStatus(200)
            ->assertJsonStructure(['telemetry', 'actuators']);

        // History
        $histRes = $this->getJson('/api/telemetry/history?hours=24');
        $histRes->assertStatus(200);

        // Ingest reading
        $ingestRes = $this->postJson('/api/telemetry/ingest', [
            'tempInternal' => 45.2,
            'humidityInternal' => 52.0,
            'solarRadiation' => 780.0,
            'grainMoisture' => 13.5,
            'weightKg' => 42.0,
            'heaterStatus' => false,
            'exhaustFanSpeed' => 80,
        ]);
        $ingestRes->assertStatus(201)
            ->assertJsonPath('success', true);

        // Actuators control
        $actuatorRes = $this->patchJson('/api/actuators/control', [
            'exhaustFanSpeed' => 90,
            'isOverrideActive' => true,
        ]);
        $actuatorRes->assertStatus(200)
            ->assertJsonPath('actuators.exhaustFanSpeed', 90)
            ->assertJsonPath('actuators.isOverrideActive', true);
    }

    public function test_devices_guide_endpoints(): void
    {
        // 1. Get devices
        $listRes = $this->getJson('/api/devices');
        $listRes->assertStatus(200);

        // 2. Add device
        $addRes = $this->postJson('/api/devices', [
            'name' => 'Sensor Suhu Rak Bawah 2',
            'type' => 'Sensor Suhu & Kelembapan',
            'location' => 'Rak Pengering Bawah',
        ]);
        $addRes->assertStatus(201)
            ->assertJsonPath('success', true);

        $deviceId = $addRes->json('device.id');

        // 3. Ping device
        $pingRes = $this->postJson("/api/devices/{$deviceId}/ping");
        $pingRes->assertStatus(200)
            ->assertJsonPath('status', 'online');

        // 4. Toggle device
        $toggleRes = $this->patchJson("/api/devices/{$deviceId}/toggle");
        $toggleRes->assertStatus(200);

        // 5. Ping all
        $pingAllRes = $this->postJson('/api/devices/ping-all');
        $pingAllRes->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_settings_and_alerts(): void
    {
        // Settings Get & Update
        $getSettings = $this->getJson('/api/settings');
        $getSettings->assertStatus(200);

        $updateSettings = $this->putJson('/api/settings', [
            'maxSafeTemp' => 56.0,
            'targetMoistureDefault' => 11.5,
        ]);
        $updateSettings->assertStatus(200)
            ->assertJsonPath('success', true);

        // Alerts
        $alertsRes = $this->getJson('/api/alerts');
        $alertsRes->assertStatus(200);
    }

    public function test_public_batch_verification_endpoint(): void
    {
        $batch = Batch::first();
        $this->assertNotNull($batch);

        // 1. Verify via public API
        $response = $this->getJson("/api/public/verify/{$batch->batch_code}");
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('verified', true)
            ->assertJsonStructure([
                'success',
                'verified',
                'certificate' => [
                    'certificateNumber',
                    'issuedAt',
                    'status',
                    'issuer',
                    'standard',
                ],
                'batch' => [
                    'batchCode',
                    'cropVariety',
                    'finalMoisturePercent',
                    'qualityGrade',
                    'qualityScore',
                ],
                'origin' => [
                    'village',
                    'geopark',
                    'farmerGroup',
                ],
                'climateMetrics' => [
                    'avgTempInternal',
                    'avgHumidityInternal',
                    'isHygienic',
                ],
                'telemetryPoints',
                'qrVerificationUrl',
            ]);

        // 2. Test invalid batch code returns 404
        $invalidRes = $this->getJson('/api/public/verify/INVALID-BATCH-999');
        $invalidRes->assertStatus(404)
            ->assertJsonPath('verified', false);
    }
}
