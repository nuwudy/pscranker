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
        Schema::table('learning_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_sessions', 'pass_mark')) {
                $table->unsignedInteger('pass_mark')->default(50)->after('xp_reward');
            }
            if (!Schema::hasColumn('learning_sessions', 'time_limit_minutes')) {
                $table->unsignedInteger('time_limit_minutes')->default(10)->after('pass_mark');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('learning_sessions', 'time_limit_minutes')) {
                $table->dropColumn('time_limit_minutes');
            }
            if (Schema::hasColumn('learning_sessions', 'pass_mark')) {
                $table->dropColumn('pass_mark');
            }
        });
    }
};
