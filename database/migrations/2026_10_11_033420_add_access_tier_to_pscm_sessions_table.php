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
        Schema::table('pscm_sessions', function (Blueprint $table) {
            $table->string('access_level', 30)->default('premium')->after('status'); // 'free', 'registered', 'premium'
            $table->decimal('price', 8, 2)->nullable()->after('access_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pscm_sessions', function (Blueprint $table) {
            $table->dropColumn(['access_level', 'price']);
        });
    }
};
