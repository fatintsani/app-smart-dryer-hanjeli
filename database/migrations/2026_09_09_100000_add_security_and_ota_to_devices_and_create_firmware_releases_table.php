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
        // 1. Add security and OTA fields to devices table
        Schema::table('devices', function (Blueprint $table) {
            $table->string('device_token')->nullable()->unique()->after('code');
            $table->string('mac_address')->nullable()->after('device_token');
            $table->string('ip_address')->nullable()->after('mac_address');
            $table->string('firmware_version')->default('v2.4.2')->after('ip_address');
            $table->string('hardware_version')->default('ESP32-WROOM-32D Rev 1.0')->after('firmware_version');
            $table->string('target_firmware_version')->nullable()->after('hardware_version');
            $table->string('ota_status')->default('IDLE')->after('target_firmware_version'); // IDLE, PENDING, DOWNLOADING, FLASHING, SUCCESS, FAILED
            $table->integer('ota_progress')->default(0)->after('ota_status'); // 0 - 100
            $table->timestamp('last_ota_at')->nullable()->after('ota_progress');
            $table->text('last_ota_log')->nullable()->after('last_ota_at');
        });

        // 2. Create firmware_releases table
        Schema::create('firmware_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique(); // e.g. 'v2.5.0'
            $table->string('release_title');
            $table->text('changelog')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size_bytes')->default(0);
            $table->string('checksum_sha256')->nullable();
            $table->boolean('is_latest')->default(false);
            $table->boolean('is_stable')->default(true);
            $table->string('min_hardware_version')->default('ESP32-WROOM-32D Rev 1.0');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firmware_releases');

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn([
                'device_token',
                'mac_address',
                'ip_address',
                'firmware_version',
                'hardware_version',
                'target_firmware_version',
                'ota_status',
                'ota_progress',
                'last_ota_at',
                'last_ota_log',
            ]);
        });
    }
};
