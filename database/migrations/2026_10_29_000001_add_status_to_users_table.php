<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('PENDING')->after('role');
            $table->timestamp('approved_at')->nullable()->after('status');
        });

        // Set all existing users to APPROVED so existing accounts remain fully accessible
        DB::table('users')->update([
            'status' => 'APPROVED',
            'approved_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_at']);
        });
    }
};

