<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('title_malayalam')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('time_limit_seconds')->default(180); // 3 minutes standard
            $table->integer('question_time_limit')->default(20); // 20s per question
            $table->decimal('total_marks', 6, 2)->default(10.00);
            $table->decimal('negative_marking_rate', 4, 2)->default(0.33); // Kerala PSC standard -1/3
            $table->string('difficulty')->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
