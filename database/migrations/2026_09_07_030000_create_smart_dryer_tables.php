<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. System Settings Table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_system_active')->default(true);
            $table->string('iot_mode')->default('SIMULATION'); // 'SIMULATION' | 'HARDWARE'
            $table->string('environment_mode')->default('LOCAL'); // 'LOCAL' | 'PRODUCTION'
            $table->float('max_safe_temp')->default(55.0);
            $table->float('min_safe_temp')->default(35.0);
            $table->float('target_moisture_default')->default(12.0);
            $table->integer('sampling_interval_seconds')->default(5);
            $table->string('wifi_ssid')->nullable()->default('GreenHouse_Hanjeli_IoT');
            $table->string('ip_address')->nullable()->default('192.168.1.105');
            $table->string('mqtt_host')->nullable()->default('broker.emqx.io');
            $table->integer('mqtt_port')->default(1883);
            $table->string('mqtt_topic')->nullable()->default('greenhouse/hanjeli/dryer01/sensor');
            $table->json('whatsapp_config')->nullable();
            $table->json('telegram_config')->nullable();
            $table->timestamps();
        });

        // 2. Batches (Drying Sessions) Table
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->unique();
            $table->string('crop_variety')->default('Hanjeli Ketan Sukabumi (Grade A)');
            $table->float('initial_weight_kg')->default(50.0);
            $table->float('current_weight_kg')->default(50.0);
            $table->float('final_weight_kg')->nullable();
            $table->float('initial_moisture_percent')->default(24.5);
            $table->float('current_moisture_percent')->default(24.5);
            $table->float('final_moisture_percent')->nullable();
            $table->float('target_moisture_percent')->default(12.0);
            $table->enum('status', ['ACTIVE', 'PAUSED', 'COMPLETED', 'ABORTED'])->default('ACTIVE');
            $table->string('drying_mode')->default('HYBRID_AUTO');
            $table->string('tray_level')->default('Semua Rak (1, 2, 3)');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('operator_name')->nullable()->default('Operator Green House');
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->float('total_duration_hours')->nullable()->default(0.0);
            $table->float('energy_kwh')->nullable()->default(0.0);
            $table->float('quality_score')->nullable()->default(92.0);
            $table->string('quality_grade')->nullable()->default('Grade A');
            $table->timestamps();
        });

        // 3. Telemetries (Sensor Readings) Table
        Schema::create('telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->float('temp_internal')->default(0.0);
            $table->float('humidity_internal')->default(0.0);
            $table->float('temp_external')->default(0.0);
            $table->float('humidity_external')->default(0.0);
            $table->float('solar_radiation')->default(0.0);
            $table->float('grain_moisture')->default(0.0);
            $table->float('weight_kg')->default(0.0);
            $table->boolean('heater_status')->default(false);
            $table->integer('heater_level')->default(0);
            $table->boolean('exhaust_fan_status')->default(false);
            $table->integer('exhaust_fan_speed')->default(0);
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['batch_id', 'recorded_at']);
        });

        // 4. Actuator States Table
        Schema::create('actuator_states', function (Blueprint $table) {
            $table->id();
            $table->boolean('exhaust_fan_status')->default(false);
            $table->integer('exhaust_fan_speed')->default(0);
            $table->boolean('intake_fan_status')->default(false);
            $table->boolean('circ_fan_status')->default(false);
            $table->boolean('aux_heater_status')->default(false);
            $table->integer('aux_heater_level')->default(0);
            $table->boolean('is_override_active')->default(false);
            $table->string('override_mode')->default('AUTO');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Devices (IoT Hardware, Sensors & Gateway) Table
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('purpose');
            $table->string('location');
            $table->string('category')->default('SENSOR'); // GATEWAY, SENSOR, ACTUATOR, NETWORK
            $table->string('icon')->default('temp'); // microchip, temp, sun, relay, wifi
            $table->string('user_signal')->default('Sangat Bagus (Stabil)');
            $table->string('pin_gpio')->nullable();
            $table->string('operating_range')->nullable();
            $table->string('accuracy')->nullable();
            $table->string('status')->default('online'); // online, offline, maintenance
            $table->boolean('is_active')->default(true);
            $table->string('last_heartbeat')->default('Baru saja');
            $table->timestamps();
        });

        // 6. System Alerts Table
        Schema::create('system_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->enum('level', ['INFO', 'WARNING', 'CRITICAL', 'EMERGENCY'])->default('INFO');
            $table->string('category')->default('SYSTEM');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_alerts');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('actuator_states');
        Schema::dropIfExists('telemetries');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('system_settings');
    }
};
