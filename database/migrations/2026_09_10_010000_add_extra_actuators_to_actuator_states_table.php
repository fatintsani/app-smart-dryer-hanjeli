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
        Schema::table('actuator_states', function (Blueprint $table) {
            if (!Schema::hasColumn('actuator_states', 'uv_light_status')) {
                $table->boolean('uv_light_status')->default(false)->after('aux_heater_level');
            }
            if (!Schema::hasColumn('actuator_states', 'rotary_tray_status')) {
                $table->boolean('rotary_tray_status')->default(false)->after('uv_light_status');
            }
            if (!Schema::hasColumn('actuator_states', 'dehumidifier_status')) {
                $table->boolean('dehumidifier_status')->default(false)->after('rotary_tray_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actuator_states', function (Blueprint $table) {
            $table->dropColumn(['uv_light_status', 'rotary_tray_status', 'dehumidifier_status']);
        });
    }
};
