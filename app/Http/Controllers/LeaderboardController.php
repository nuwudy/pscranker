<?php

namespace App\Http\Controllers;

use App\Models\DrillAttempt;
use App\Models\Quiz;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = $request->query('timeframe', 'today');

        $query = DrillAttempt::with('quiz')
            ->orderByDesc('score')
            ->orderBy('time_taken_seconds');

        if ($timeframe === 'today') {
            $query->whereDate('created_at', today());
        }

        $leaderboard = $query->take(50)->get();

        // If today is empty for mock, fallback to all-time
        if ($leaderboard->isEmpty()) {
            $leaderboard = DrillAttempt::with('quiz')
                ->orderByDesc('score')
                ->orderBy('time_taken_seconds')
                ->take(50)
                ->get();
        }

        return view('pages.leaderboard', compact('leaderboard', 'timeframe'));
    }
}
