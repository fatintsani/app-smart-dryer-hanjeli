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
        // 1. Actuator States Table: Add CH1 & CH2 heater status
        Schema::table('actuator_states', function (Blueprint $table) {
            if (!Schema::hasColumn('actuator_states', 'heater_ch1_status')) {
                $table->boolean('heater_ch1_status')->default(false)->after('aux_heater_status');
            }
            if (!Schema::hasColumn('actuator_states', 'heater_ch2_status')) {
                $table->boolean('heater_ch2_status')->default(false)->after('heater_ch1_status');
            }
            if (!Schema::hasColumn('actuator_states', 'aux_heater2_status')) {
                $table->boolean('aux_heater2_status')->default(false)->after('heater_ch2_status');
            }
        });

        // 2. Telemetries Table: Add CH1 & CH2 telemetry logging
        Schema::table('telemetries', function (Blueprint $table) {
            if (!Schema::hasColumn('telemetries', 'heater_ch1_status')) {
                $table->boolean('heater_ch1_status')->nullable()->default(false)->after('heater_status');
            }
            if (!Schema::hasColumn('telemetries', 'heater_ch2_status')) {
                $table->boolean('heater_ch2_status')->nullable()->default(false)->after('heater_ch1_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actuator_states', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('actuator_states', 'heater_ch1_status')) $cols[] = 'heater_ch1_status';
            if (Schema::hasColumn('actuator_states', 'heater_ch2_status')) $cols[] = 'heater_ch2_status';
            if (Schema::hasColumn('actuator_states', 'aux_heater2_status')) $cols[] = 'aux_heater2_status';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('telemetries', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('telemetries', 'heater_ch1_status')) $cols[] = 'heater_ch1_status';
            if (Schema::hasColumn('telemetries', 'heater_ch2_status')) $cols[] = 'heater_ch2_status';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

