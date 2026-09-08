<?php

namespace Database\Seeders;

use App\Models\ActuatorState;
use App\Models\Batch;
use App\Models\Device;
use App\Models\SystemAlert;
use App\Models\SystemSetting;
use App\Models\Telemetry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SmartDryerSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', 'OPERATOR')->first();
        $admin = User::where('role', 'ADMIN')->first();

        // 1. Seed System Settings
        SystemSetting::updateOrCreate(
            ['id' => 1],
            [
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
                    'botToken' => '',
                    'chatId' => '',
                ],
            ]
        );

        // 2. Seed Actuator States
        ActuatorState::updateOrCreate(
            ['id' => 1],
            [
                'exhaust_fan_status' => true,
                'exhaust_fan_speed' => 65,
                'intake_fan_status' => true,
                'circ_fan_status' => true,
                'aux_heater_status' => true,
                'aux_heater_level' => 40,
                'is_override_active' => false,
                'override_mode' => 'AUTO',
                'updated_by' => $operator?->id,
            ]
        );

        // 3. Seed Devices Registry
        $devices = [
            [
                'id' => 1,
                'name' => 'ESP32 Pengendali Utama',
                'code' => 'ESP32-DEV-01',
                'purpose' => 'Otak pengendali suhu, kipas, & transmisi data sistem',
                'location' => 'Panel Kontrol Greenhouse',
                'category' => 'GATEWAY',
                'icon' => 'microchip',
                'user_signal' => 'Sangat Bagus (Stabil)',
                'pin_gpio' => 'GPIO 2, 4, 16, 17, 21, 22',
                'operating_range' => '3.3V / -40°C ~ 85°C',
                'accuracy' => '240 MHz Dual Core',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 2,
                'name' => 'Sensor Suhu & Kelembapan Ruang (DHT22)',
                'code' => 'DHT22-INT-01',
                'purpose' => 'Memantau tingkat panas (°C) dan kelembapan (% RH) udara',
                'location' => 'Rak Pengering Tengah (Zona 2)',
                'category' => 'SENSOR',
                'icon' => 'temp',
                'user_signal' => 'Bagus & Stabil',
                'pin_gpio' => 'GPIO 21 (One-Wire)',
                'operating_range' => '-40°C ~ 80°C / 0-100% RH',
                'accuracy' => '±0.5°C / ±2% RH',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 3,
                'name' => 'Sensor Cahaya Matahari (Pyranometer)',
                'code' => 'PYRA-SOL-01',
                'purpose' => 'Mendeteksi terik radiasi matahari untuk efisiensi daya pemanas',
                'location' => 'Atap Kaca Greenhouse',
                'category' => 'SENSOR',
                'icon' => 'sun',
                'user_signal' => 'Sangat Bagus',
                'pin_gpio' => 'GPIO 34 (ADC 12-Bit)',
                'operating_range' => '0 ~ 1500 W/m²',
                'accuracy' => '±5 W/m²',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 4,
                'name' => 'Sakelar Pemanas & Kipas (Relai 4-Channel)',
                'code' => 'RELAY-4CH-01',
                'purpose' => 'Menyalakan pemanas keramik & kipas sirkulasi otomatis',
                'location' => 'Kotak Listrik Utama',
                'category' => 'ACTUATOR',
                'icon' => 'relay',
                'user_signal' => 'Normal & Siaga',
                'pin_gpio' => 'GPIO 12, 13, 14, 27',
                'operating_range' => 'AC 220V 10A / DC 30V 10A',
                'accuracy' => 'Optocoupler Isolated',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 5,
                'name' => 'Pemancar Wi-Fi Greenhouse',
                'code' => 'AP-WIFI-01',
                'purpose' => 'Menyambungkan seluruh alat ke layar monitor operator',
                'location' => 'Ruang Stasiun Greenhouse',
                'category' => 'NETWORK',
                'icon' => 'wifi',
                'user_signal' => 'Sempurna (-58 dBm)',
                'pin_gpio' => '802.11 b/g/n 2.4GHz',
                'operating_range' => 'Hingga 50 Meter',
                'accuracy' => '150 Mbps WPA2-PSK',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
        ];

        foreach ($devices as $d) {
            Device::updateOrCreate(['id' => $d['id']], $d);
        }

        // 4. Seed Historical Batches
        // Batch 1 (5 days ago)
        $b1 = Batch::updateOrCreate(
            ['batch_code' => 'HJ-2026-001'],
            [
                'crop_variety' => 'Hanjeli Merah Purbalingga',
                'initial_weight_kg' => 55.0,
                'current_weight_kg' => 46.2,
                'final_weight_kg' => 46.2,
                'initial_moisture_percent' => 25.0,
                'current_moisture_percent' => 12.0,
                'target_moisture_percent' => 12.0,
                'status' => 'COMPLETED',
                'drying_mode' => 'HYBRID_AUTO',
                'tray_level' => 'Semua Rak (1, 2, 3)',
                'operator_id' => $operator?->id,
                'operator_name' => $operator?->name ?? 'Operator Greenhouse',
                'notes' => 'Pengeringan lancar, cuaca sebagian besar terik.',
                'started_at' => Carbon::now()->subDays(5)->setTime(8, 0, 0),
                'completed_at' => Carbon::now()->subDays(5)->setTime(18, 0, 0),
                'total_duration_hours' => 10.0,
                'energy_kwh' => 15.0,
                'quality_score' => 95.0,
                'quality_grade' => 'Grade A (Ekspor)',
            ]
        );

        // Batch 2 (3 days ago)
        $b2 = Batch::updateOrCreate(
            ['batch_code' => 'HJ-2026-002'],
            [
                'crop_variety' => 'Hanjeli Ketan Sukabumi (Grade A)',
                'initial_weight_kg' => 60.0,
                'current_weight_kg' => 50.8,
                'final_weight_kg' => 50.8,
                'initial_moisture_percent' => 24.0,
                'current_moisture_percent' => 12.2,
                'target_moisture_percent' => 12.0,
                'status' => 'COMPLETED',
                'drying_mode' => 'HYBRID_AUTO',
                'tray_level' => 'Semua Rak (1, 2, 3)',
                'operator_id' => $operator?->id,
                'operator_name' => $operator?->name ?? 'Operator Greenhouse',
                'notes' => 'Menggunakan bantuan pemanas keramik saat mendung sore hari.',
                'started_at' => Carbon::now()->subDays(3)->setTime(7, 30, 0),
                'completed_at' => Carbon::now()->subDays(3)->setTime(18, 45, 0),
                'total_duration_hours' => 11.2,
                'energy_kwh' => 16.8,
                'quality_score' => 94.0,
                'quality_grade' => 'Grade A (Ekspor)',
            ]
        );

        // Batch 3 (Yesterday)
        $b3 = Batch::updateOrCreate(
            ['batch_code' => 'HJ-2026-003'],
            [
                'crop_variety' => 'Hanjeli Putih Organik',
                'initial_weight_kg' => 45.0,
                'current_weight_kg' => 38.5,
                'final_weight_kg' => 38.5,
                'initial_moisture_percent' => 23.5,
                'current_moisture_percent' => 11.8,
                'target_moisture_percent' => 12.0,
                'status' => 'COMPLETED',
                'drying_mode' => 'SOLAR_PRIORITY',
                'tray_level' => 'Rak Tengah & Atas',
                'operator_id' => $operator?->id,
                'operator_name' => $operator?->name ?? 'Operator Greenhouse',
                'notes' => 'Kualitas sangat prima, warna gabah cerah merata.',
                'started_at' => Carbon::now()->subDay()->setTime(8, 15, 0),
                'completed_at' => Carbon::now()->subDay()->setTime(17, 45, 0),
                'total_duration_hours' => 9.5,
                'energy_kwh' => 14.2,
                'quality_score' => 96.5,
                'quality_grade' => 'Grade A (Ekspor)',
            ]
        );

        // 5. Seed Active Batch (Started 4 hours ago)
        $activeBatch = Batch::updateOrCreate(
            ['batch_code' => 'HJ-2026-004'],
            [
                'crop_variety' => 'Hanjeli Ketan Sukabumi (Grade A)',
                'initial_weight_kg' => 50.0,
                'current_weight_kg' => 42.5,
                'initial_moisture_percent' => 24.5,
                'current_moisture_percent' => 13.8,
                'target_moisture_percent' => 12.0,
                'status' => 'ACTIVE',
                'drying_mode' => 'HYBRID_AUTO',
                'tray_level' => 'Semua Rak (1, 2, 3)',
                'operator_id' => $operator?->id,
                'operator_name' => $operator?->name ?? 'Operator Greenhouse',
                'notes' => 'Batch sesi pengeringan aktif siang ini. Kondisi suhu dan sirkulasi udara normal.',
                'started_at' => Carbon::now()->subHours(4),
                'total_duration_hours' => 4.0,
                'energy_kwh' => 6.0,
                'quality_score' => 95.0,
                'quality_grade' => 'Grade A',
            ]
        );

        // 6. Seed Telemetry Points for Active Batch & History Graphs (Last 12 hours)
        Telemetry::truncate();
        $startTime = Carbon::now()->subHours(12);

        for ($i = 0; $i <= 24; $i++) {
            $pointTime = $startTime->copy()->addMinutes($i * 30);
            $fraction = $i / 24;

            // Simulating realistic greenhouse solar thermal curve
            $hour = (int) $pointTime->format('H');
            $solarPeak = sin(max(0, ($hour - 6) / 12 * M_PI));
            $tempInternal = round(35.0 + ($solarPeak * 14.5) + (sin($i) * 1.5), 1);
            $tempExternal = round(26.0 + ($solarPeak * 7.5) + (cos($i) * 1.0), 1);
            $humidityInternal = round(max(38, 75.0 - ($solarPeak * 32.0)), 0);
            $humidityExternal = round(max(50, 85.0 - ($solarPeak * 25.0)), 0);
            $solarRadiation = round(max(0, $solarPeak * 850.0 + (sin($i * 2) * 40)), 0);
            
            // Moisture dropping steadily from 24.5% down to 13.8%
            $grainMoisture = round(24.5 - ($fraction * 10.7), 1);
            $weightKg = round(50.0 - ($fraction * 7.5), 1);

            $isHeaterOn = $tempInternal < 40.0;
            $heaterLevel = $isHeaterOn ? 45 : 0;
            $exhaustFanSpeed = $tempInternal > 45.0 ? 80 : 60;

            Telemetry::create([
                'batch_id' => $activeBatch->id,
                'temp_internal' => $tempInternal,
                'humidity_internal' => $humidityInternal,
                'temp_external' => $tempExternal,
                'humidity_external' => $humidityExternal,
                'solar_radiation' => $solarRadiation,
                'grain_moisture' => $grainMoisture,
                'weight_kg' => $weightKg,
                'heater_status' => $isHeaterOn,
                'heater_level' => $heaterLevel,
                'exhaust_fan_status' => true,
                'exhaust_fan_speed' => $exhaustFanSpeed,
                'recorded_at' => $pointTime,
            ]);
        }

        // 7. Seed System Alerts & Logs
        SystemAlert::truncate();
        SystemAlert::create([
            'batch_id' => $activeBatch->id,
            'level' => 'INFO',
            'category' => 'BATCH',
            'title' => 'Sesi Pengeringan Dimulai',
            'message' => 'Operator memulai sesi pengeringan batch #HJ-2026-004 (Hanjeli Ketan Sukabumi).',
            'is_read' => true,
            'created_at' => Carbon::now()->subHours(4),
        ]);

        SystemAlert::create([
            'batch_id' => $activeBatch->id,
            'level' => 'INFO',
            'category' => 'ACTUATOR',
            'title' => 'Relai Kipas & Pemanas Aktif',
            'message' => 'Sistem otomatisasi menyalakan kipas exhaust 65% dan pemanas tambahan 40%.',
            'is_read' => true,
            'created_at' => Carbon::now()->subHours(3)->subMinutes(30),
        ]);

        SystemAlert::create([
            'batch_id' => $activeBatch->id,
            'level' => 'INFO',
            'category' => 'SENSOR',
            'title' => 'Pemberitahuan Penurunan Kadar Air',
            'message' => 'Kadar air hanjeli telah turun di bawah 15.0%. Target 12.0% diperkirakan tercapai dalam 2 jam.',
            'is_read' => false,
            'created_at' => Carbon::now()->subMinutes(25),
        ]);
    }
}
