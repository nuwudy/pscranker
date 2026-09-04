<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DrillAttempt;
use App\Models\MediaFile;
use App\Models\Question;
use App\Models\Session;
use App\Models\SiteSetting;
use App\Models\SubscriptionPayment;
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
            'active_subscribers' => User::where('subscribed_until', '>', now())->count(),
            'total_revenue' => SubscriptionPayment::where('status', 'paid')->sum('amount'),
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

        // Recent subscription payments
        $recentPayments = SubscriptionPayment::with('user')
            ->latest()
            ->take(6)
            ->get();

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

        // Current pricing settings & preview tiers
        $pricingSettings = [
            'course_base_monthly_fee' => (float) \App\Models\SiteSetting::get('course_base_monthly_fee', 299),
            'rebate_2m' => (float) \App\Models\SiteSetting::get('rebate_2m', 10),
            'rebate_3m' => (float) \App\Models\SiteSetting::get('rebate_3m', 15),
            'rebate_6m' => (float) \App\Models\SiteSetting::get('rebate_6m', 25),
            'rebate_12m' => (float) \App\Models\SiteSetting::get('rebate_12m', 40),
            'razorpay_key_id' => \App\Models\SiteSetting::get('razorpay_key_id', ''),
            'razorpay_key_secret' => \App\Models\SiteSetting::get('razorpay_key_secret', ''),
        ];

        $pricingTiers = \App\Models\SiteSetting::getPricingTiers();

        return view('admin.dashboard', compact(
            'stats',
            'recentProgress',
            'recentDrills',
            'recentPayments',
            'sessions',
            'recentMedia',
            'categories',
            'pricingSettings',
            'pricingTiers'
        ));
    }

    /**
     * Update prepaid pricing and rebate schedule from admin dashboard.
     */
    public function updatePricingSettings(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'course_base_monthly_fee' => 'required|numeric|min:1',
            'rebate_2m' => 'required|numeric|min:0|max:100',
            'rebate_3m' => 'required|numeric|min:0|max:100',
            'rebate_6m' => 'required|numeric|min:0|max:100',
            'rebate_12m' => 'required|numeric|min:0|max:100',
            'razorpay_key_id' => 'nullable|string|max:100',
            'razorpay_key_secret' => 'nullable|string|max:100',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\SiteSetting::set($key, $value ?? '');
        }

        return redirect()->back()->with('success', 'Base fee and progressive rebate schedule updated successfully! All student pricing updated.');
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
