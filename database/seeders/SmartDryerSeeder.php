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
                'name' => 'Gateway Mikrokontroler ESP32 Utama',
                'code' => 'ESP32-GH-HANJELI-01',
                'device_token' => 'esp32_sec_7f9a2b1c8e3d4f5a6b7c8d9e0f1a2b3c',
                'mac_address' => '24:6F:28:B4:7A:1C',
                'ip_address' => '192.168.1.105',
                'firmware_version' => 'v2.5.0',
                'hardware_version' => 'ESP32 DevKit V1 (30-Pin)',
                'target_firmware_version' => null,
                'ota_status' => 'IDLE',
                'ota_progress' => 0,
                'purpose' => 'Pusat kendali sensor fisik (DHT22, BH1750, Rain), kontrol relai pemanas PTC (55°C-60°C), dan pengirim telemetri',
                'location' => 'Panel Box Utama Greenhouse',
                'category' => 'GATEWAY',
                'icon' => 'microchip',
                'user_signal' => 'Sangat Bagus (-54 dBm)',
                'pin_gpio' => 'GPIO 21, 22, 25, 26, 27, 34',
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
                'device_token' => 'esp32_sec_1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d',
                'mac_address' => '24:6F:28:B4:7A:2D',
                'ip_address' => '192.168.1.106',
                'firmware_version' => 'v2.5.0',
                'hardware_version' => 'DHT22 / AM2302 High Precision',
                'target_firmware_version' => null,
                'ota_status' => 'IDLE',
                'ota_progress' => 0,
                'purpose' => 'Memantau tingkat panas (°C) dan kelembapan (% RH) udara ruang dryer',
                'location' => 'Rak Pengering Tengah Greenhouse',
                'category' => 'SENSOR',
                'icon' => 'temp',
                'user_signal' => 'Bagus & Stabil',
                'pin_gpio' => 'GPIO 25 (Digital Data One-Wire)',
                'operating_range' => '-40°C ~ 80°C / 0-100% RH',
                'accuracy' => '±0.5°C / ±2% RH',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 3,
                'name' => 'Sensor Intensitas Cahaya Lingkungan (BH1750)',
                'code' => 'BH1750-SOL-01',
                'device_token' => 'esp32_sec_3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f',
                'mac_address' => '24:6F:28:B4:7A:3E',
                'ip_address' => '192.168.1.107',
                'firmware_version' => 'v2.5.0',
                'hardware_version' => 'BH1750FVI Digital Light Sensor',
                'target_firmware_version' => null,
                'ota_status' => 'IDLE',
                'ota_progress' => 0,
                'purpose' => 'Mendeteksi intensitas cahaya matahari (0 - 65535 Lux) untuk efisiensi energi pengeringan',
                'location' => 'Atap / Dinding Terang Greenhouse',
                'category' => 'SENSOR',
                'icon' => 'sun',
                'user_signal' => 'Sangat Bagus',
                'pin_gpio' => 'I2C Bus: SDA GPIO 21, SCL GPIO 22',
                'operating_range' => '1 ~ 65535 Lux',
                'accuracy' => '±20% (Resolusi 1 Lux)',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 4,
                'name' => 'Sensor Deteksi Hujan & Presipitasi',
                'code' => 'RAIN-ADC-01',
                'device_token' => 'esp32_sec_5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b',
                'mac_address' => '24:6F:28:B4:7A:4F',
                'ip_address' => '192.168.1.108',
                'firmware_version' => 'v2.5.0',
                'hardware_version' => 'Rain Drop Sensor Module + LM393',
                'target_firmware_version' => null,
                'ota_status' => 'IDLE',
                'ota_progress' => 0,
                'purpose' => 'Mendeteksi rintik hujan dan kondisi basah luar (Threshold ADC 2000)',
                'location' => 'Permukaan Luar Atap Greenhouse',
                'category' => 'SENSOR',
                'icon' => 'sun',
                'user_signal' => 'Normal & Siaga',
                'pin_gpio' => 'GPIO 34 (ADC 12-Bit Input)',
                'operating_range' => 'ADC 0 ~ 4095',
                'accuracy' => 'Threshold < 2000 Hujan',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 5,
                'name' => 'Modul Relai Pemanas Ganda PTC 1 & 2',
                'code' => 'RELAY-PTC-01',
                'device_token' => 'esp32_sec_7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d',
                'mac_address' => '24:6F:28:B4:7A:5A',
                'ip_address' => '192.168.1.109',
                'firmware_version' => 'v2.5.0',
                'hardware_version' => '2-Channel Optocoupler Relay Module',
                'target_firmware_version' => null,
                'ota_status' => 'IDLE',
                'ota_progress' => 0,
                'purpose' => 'Mengontrol pemanas PTC 1 & 2 otomatis (<55°C ON, >60°C OFF) dengan safety shut-off',
                'location' => 'Kotak Daya Listrik Utama',
                'category' => 'ACTUATOR',
                'icon' => 'relay',
                'user_signal' => 'Sempurna & Siaga',
                'pin_gpio' => 'CH1: GPIO 26, CH2: GPIO 27',
                'operating_range' => 'AC 220V 10A / DC 30V 10A',
                'accuracy' => 'Optocoupler Isolated',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
            [
                'id' => 6,
                'name' => 'Layar Monitor Lokal LCD 20x4 I2C',
                'code' => 'LCD-2004-01',
                'device_token' => 'esp32_sec_9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4e',
                'mac_address' => '24:6F:28:B4:7A:6B',
                'ip_address' => '192.168.1.110',
                'firmware_version' => 'v2.5.0',
                'hardware_version' => 'LCD 2004 Blue Backlight + PCF8574 I2C',
                'target_firmware_version' => null,
                'ota_status' => 'IDLE',
                'ota_progress' => 0,
                'purpose' => 'Menampilkan telemetri suhu, kelembapan, lux, hujan, dan status heater secara bergantian di panel alat',
                'location' => 'Pintu Depan Kotak Panel Kontrol',
                'category' => 'GATEWAY',
                'icon' => 'microchip',
                'user_signal' => 'Aktif Beroperasi',
                'pin_gpio' => 'I2C Address 0x27 (SDA 21, SCL 22)',
                'operating_range' => '5V DC / 20 Karakter x 4 Baris',
                'accuracy' => 'Refresh 4000ms Switch Page',
                'status' => 'online',
                'is_active' => true,
                'last_heartbeat' => 'Baru saja',
            ],
        ];

        foreach ($devices as $d) {
            Device::updateOrCreate(['id' => $d['id']], $d);
        }

        // Seed Firmware Releases
        \App\Models\FirmwareRelease::updateOrCreate(
            ['version' => 'v2.5.0'],
            [
                'release_title' => 'Firmware v2.5.0 (Optimized PID & Deep Sleep Telemetry)',
                'changelog' => "1. Optimasi algoritma PID pada relay exhaust fan.\n2. Peningkatan ketahanan koneksi Wi-Fi auto-reconnect.\n3. Dukungan otentikasi X-Device-Token & payload kompresi.",
                'file_path' => '/firmware/bin/hanjeli_esp32_v2.5.0.bin',
                'file_size_bytes' => 1248560,
                'checksum_sha256' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                'is_latest' => true,
                'is_stable' => true,
                'min_hardware_version' => 'ESP32-WROOM-32D Rev 1.0',
            ]
        );

        \App\Models\FirmwareRelease::updateOrCreate(
            ['version' => 'v2.4.2'],
            [
                'release_title' => 'Firmware v2.4.2 (Production Baseline)',
                'changelog' => "1. Baseline rilis operasional Greenhouse Hanjeli.\n2. Ingestion sensor DHT22, Load Cell, Pyranometer, dan kontrol aktuator relai.",
                'file_path' => '/firmware/bin/hanjeli_esp32_v2.4.2.bin',
                'file_size_bytes' => 1198400,
                'checksum_sha256' => '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08',
                'is_latest' => false,
                'is_stable' => true,
                'min_hardware_version' => 'ESP32-WROOM-32D Rev 1.0',
            ]
        );

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
