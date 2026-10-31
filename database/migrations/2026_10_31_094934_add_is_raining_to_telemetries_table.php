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
        Schema::table('telemetries', function (Blueprint $table) {
            if (!Schema::hasColumn('telemetries', 'is_raining')) {
                $table->boolean('is_raining')->default(false)->after('solar_radiation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telemetries', function (Blueprint $table) {
            if (Schema::hasColumn('telemetries', 'is_raining')) {
                $table->dropColumn('is_raining');
            }
        });
    }
};
