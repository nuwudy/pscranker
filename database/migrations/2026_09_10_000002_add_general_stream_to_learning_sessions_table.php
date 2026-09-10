<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->boolean('in_general_stream')->default(true)->after('is_active');
            $table->integer('general_stream_order')->nullable()->after('in_general_stream');
        });

        // Initialize general_stream_order sequentially for existing sessions
        $sessions = DB::table('learning_sessions')->orderBy('id', 'asc')->get();
        $order = 1;
        foreach ($sessions as $session) {
            DB::table('learning_sessions')
                ->where('id', $session->id)
                ->update([
                    'in_general_stream' => true,
                    'general_stream_order' => $order++,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropColumn(['in_general_stream', 'general_stream_order']);
        });
    }
};
