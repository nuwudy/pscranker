<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_session_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_token')->nullable()->index();
            $table->foreignId('session_id')->constrained('learning_sessions')->cascadeOnDelete();
            $table->string('current_phase')->default('diagnostic'); // 'diagnostic', 'lesson', 'reinforcement', 'omr', 'summary'
            $table->string('diagnostic_status')->nullable(); // 'pending', 'correct', 'incorrect'
            $table->decimal('reinforcement_score', 5, 2)->default(0.00);
            $table->decimal('omr_score', 5, 2)->default(0.00);
            $table->decimal('net_marks', 5, 2)->default(0.00);
            $table->integer('xp_earned')->default(0);
            $table->integer('time_taken_seconds')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_session_progress');
    }
};
