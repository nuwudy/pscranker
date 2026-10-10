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
        Schema::create('pscm_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('topic_id')->nullable();
            $table->string('subject')->nullable();
            $table->json('hook_question');
            $table->json('media')->nullable();
            $table->longText('lesson_content')->nullable();
            $table->json('practice_mcqs');
            $table->json('omr_config')->nullable();
            $table->enum('status', ['draft', 'generated', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pscm_sessions');
    }
};
