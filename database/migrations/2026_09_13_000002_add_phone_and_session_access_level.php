<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add phone number column to users table
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 15)->nullable()->unique()->after('email');
            });
        }

        // Set admin phone number to 9895940500
        DB::table('users')
            ->where('email', 'admin@pscranker.com')
            ->update(['phone' => '9895940500']);

        // 2. Add access_level column to learning_sessions table
        if (!Schema::hasColumn('learning_sessions', 'access_level')) {
            Schema::table('learning_sessions', function (Blueprint $table) {
                $table->string('access_level', 20)->default('guest')->after('is_active');
            });

            // Synchronize existing access_level from is_premium
            DB::table('learning_sessions')
                ->where('is_premium', true)
                ->update(['access_level' => 'premium']);

            DB::table('learning_sessions')
                ->where('is_premium', false)
                ->update(['access_level' => 'guest']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }

        if (Schema::hasColumn('learning_sessions', 'access_level')) {
            Schema::table('learning_sessions', function (Blueprint $table) {
                $table->dropColumn('access_level');
            });
        }
    }
};
