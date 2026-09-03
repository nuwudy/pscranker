<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DrillAttempt;
use App\Models\MediaFile;
use App\Models\Question;
use App\Models\Session;
use App\Models\User;
use App\Models\UserSessionProgress;

class DashboardController extends Controller
{
    /**
     * Display the main admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_sessions' => Session::count(),
            'active_sessions' => Session::where('is_active', true)->count(),
            'total_questions' => Question::count(),
            'diagnostic_questions' => Question::where('phase_type', 'diagnostic')->count(),
            'reinforcement_questions' => Question::where('phase_type', 'reinforcement')->count(),
            'omr_questions' => Question::where('phase_type', 'omr')->count(),
            'total_media' => MediaFile::count(),
            'media_storage_formatted' => $this->formatBytes(MediaFile::sum('file_size')),
            'total_users' => User::count(),
            'total_session_attempts' => UserSessionProgress::count(),
            'total_session_completions' => UserSessionProgress::whereNotNull('completed_at')->count(),
            'total_drill_attempts' => DrillAttempt::count(),
            'total_xp_awarded' => UserSessionProgress::sum('xp_earned'),
            'avg_omr_score' => round(UserSessionProgress::where('omr_score', '>', 0)->avg('omr_score') ?? 0, 2),
        ];

        // Recent session progress
        $recentProgress = UserSessionProgress::with(['session', 'user'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        // Recent speed drill attempts
        $recentDrills = DrillAttempt::latest()->take(6)->get();

        // Learning sessions inventory with content and question counts
        $sessions = Session::with(['category'])
            ->withCount([
                'contents',
                'questions',
                'questions as diagnostic_count' => fn($q) => $q->where('phase_type', 'diagnostic'),
                'questions as reinforcement_count' => fn($q) => $q->where('phase_type', 'reinforcement'),
                'questions as omr_count' => fn($q) => $q->where('phase_type', 'omr'),
            ])
            ->orderBy('order', 'asc')
            ->get();

        // Recent uploaded media files
        $recentMedia = MediaFile::latest()->take(6)->get();

        // Categories breakdown
        $categories = Category::withCount(['questions', 'sessions'])->orderBy('order')->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentProgress',
            'recentDrills',
            'sessions',
            'recentMedia',
            'categories'
        ));
    }

    /**
     * Format raw bytes into human readable format.
     */
    private function formatBytes($bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
