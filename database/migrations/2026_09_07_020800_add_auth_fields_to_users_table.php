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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('OPERATOR')->after('email');
            $table->string('phone', 30)->nullable()->after('role');
            $table->text('avatar')->nullable()->after('phone');
            $table->string('google_id', 100)->nullable()->unique()->after('avatar');
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'avatar', 'google_id']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
