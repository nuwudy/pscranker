<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->constrained('learning_sessions')->nullOnDelete();
            $table->string('phase_type')->nullable(); // 'diagnostic', 'reinforcement', 'omr'
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();
            $table->text('trap_warning_text')->nullable();
            $table->json('options')->nullable()->change();
            $table->foreignId('category_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn([
                'session_id',
                'phase_type',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'trap_warning_text',
            ]);
        });
    }
};
