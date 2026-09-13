<?php

use App\Models\Category;
use App\Models\Session;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sequentially normalizes existing subject unit orders and mixed practice train orders.
     */
    public function up(): void
    {
        // 1. Normalize subject unit orders per category so ties are sequenced 1, 2, 3...
        $categories = Category::all();
        foreach ($categories as $cat) {
            $sessions = Session::where('category_id', $cat->id)
                ->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($sessions as $index => $session) {
                $expected = $index + 1;
                if ($session->order !== $expected) {
                    $session->update(['order' => $expected]);
                }
            }
        }

        // Also normalize any uncategorized sessions
        $uncategorized = Session::whereNull('category_id')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($uncategorized as $index => $session) {
            $expected = $index + 1;
            if ($session->order !== $expected) {
                $session->update(['order' => $expected]);
            }
        }

        // 2. Normalize mixed practice general stream orders so there are no gaps
        $mixedSessions = Session::where('in_general_stream', true)
            ->orderBy('general_stream_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($mixedSessions as $index => $session) {
            $expected = $index + 1;
            if ($session->general_stream_order !== $expected) {
                $session->update(['general_stream_order' => $expected]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op normalization
    }
};
