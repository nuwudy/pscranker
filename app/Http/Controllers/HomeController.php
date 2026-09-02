<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\DrillAttempt;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch featured rapid quiz
        $featuredQuiz = Quiz::with(['questions.category'])
            ->where('slug', '3-min-rapid-blitz')
            ->first() ?? Quiz::with(['questions.category'])->first();

        // Fetch categories with question count
        $categories = Category::withCount('questions')
            ->orderBy('order')
            ->get();

        // Top 3 live leaderboard for the quick-access card
        $leaderboardTop = DrillAttempt::orderByDesc('score')
            ->orderBy('time_taken_seconds')
            ->take(3)
            ->get();

        // High yield meme questions
        $memeQuestions = Question::whereNotNull('meme_image_url')
            ->orWhereNotNull('trap_warning')
            ->with('category')
            ->take(4)
            ->get();

        // Dynamic ticker stats
        $stats = [
            'active_drillers' => rand(1380, 1540),
            'traps_avoided' => number_format(rand(44500, 48200)),
            'average_score' => '6.84 / 10',
            'drills_completed_today' => rand(3200, 3950),
        ];

        return view('pages.home', compact('featuredQuiz', 'categories', 'leaderboardTop', 'memeQuestions', 'stats'));
    }
}
