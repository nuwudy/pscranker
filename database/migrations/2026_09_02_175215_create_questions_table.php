<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
            $table->text('question_text');
            $table->text('question_text_malayalam')->nullable();
            $table->json('options'); // [{key: 'A', text: '...', text_ml: '...'}, ...]
            $table->string('correct_option', 5); // 'A', 'B', 'C', 'D'
            $table->text('explanation')->nullable();
            $table->text('explanation_malayalam')->nullable();
            $table->text('trap_warning')->nullable(); // Humorous PSC trap micro-copy
            $table->string('meme_image_url')->nullable();
            $table->string('psc_exam_reference')->nullable(); // e.g. "LDC 2021", "CPO 2023"
            $table->string('difficulty')->default('medium');
            $table->decimal('points', 4, 2)->default(1.00);
            $table->decimal('negative_points', 4, 2)->default(0.33);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
