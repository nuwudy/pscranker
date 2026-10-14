<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DrillAttempt;
use App\Models\MediaFile;
use App\Models\Question;
use App\Models\PscmSession;
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
        // AI Micro-Learning is now the only learning system
        $totalPscm = PscmSession::count();
        $hookCount = PscmSession::whereNotNull('hook_question')->count();
        $practiceCount = PscmSession::get(['practice_mcqs'])->sum(fn ($s) => is_array($s->practice_mcqs) ? count($s->practice_mcqs) : 0);

        $stats = [
            'total_sessions' => $totalPscm,
            'active_sessions' => PscmSession::where('status', 'published')->count(),
            'total_questions' => $hookCount + $practiceCount,
            'diagnostic_questions' => $hookCount,
            'reinforcement_questions' => $practiceCount,
            'omr_questions' => $hookCount + $practiceCount,
            'total_media' => MediaFile::count(),
            'media_storage_formatted' => $this->formatBytes(MediaFile::sum('file_size')),
            'total_users' => User::count(),
            'active_subscribers' => User::where('subscribed_until', '>', now())->count(),
            'total_admins' => User::where('is_admin', true)->orWhere('email', 'admin@pscranker.com')->orWhere('phone', '9895940500')->count(),
            'total_revenue' => SubscriptionPayment::where('status', 'paid')->sum('amount'),
            'offline_revenue' => SubscriptionPayment::where('status', 'paid')->where('razorpay_order_id', 'like', 'OFFLINE_%')->sum('amount'),
            'total_session_attempts' => UserSessionProgress::count(),
            'total_session_completions' => UserSessionProgress::whereNotNull('completed_at')->count(),
            'total_drill_attempts' => DrillAttempt::count(),
            'total_xp_awarded' => UserSessionProgress::sum('xp_earned'),
            'avg_omr_score' => round(UserSessionProgress::where('omr_score', '>', 0)->avg('omr_score') ?? 0, 2),
        ];

        // Recent candidates and users
        $recentUsers = User::latest('id')->take(6)->get();

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

        // Latest AI micro-learning sessions
        $sessions = PscmSession::latest()->take(10)->get();

        // Recent uploaded media files
        $recentMedia = MediaFile::latest()->take(6)->get();

        // Categories breakdown (AI micro-learning categories)
        $countsByCategory = PscmSession::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categories = collect(PscmSession::CATEGORIES)->map(function ($label, $key) use ($countsByCategory) {
            $count = (int) ($countsByCategory[$key] ?? 0);
            return (object) [
                'name' => $label,
                'name_malayalam' => null,
                'sessions_count' => $count,
                'questions_count' => $count * 5,
            ];
        })->values();

        // Current pricing settings & preview tiers
        $pricingSettings = [
            'course_base_daily_fee' => (float) \App\Models\SiteSetting::get('course_base_daily_fee', 10.00),
            'course_base_monthly_fee' => (float) \App\Models\SiteSetting::get('course_base_monthly_fee', 300),
            'rebate_1w' => (float) \App\Models\SiteSetting::get('rebate_1w', 14),
            'rebate_1m' => (float) \App\Models\SiteSetting::get('rebate_1m', 25),
            'rebate_3m' => (float) \App\Models\SiteSetting::get('rebate_3m', 33),
            'rebate_6m' => (float) \App\Models\SiteSetting::get('rebate_6m', 40),
            'rebate_1y' => (float) \App\Models\SiteSetting::get('rebate_1y', 45),
            // legacy fallbacks
            'rebate_2m' => (float) \App\Models\SiteSetting::get('rebate_2m', 10),
            'rebate_12m' => (float) \App\Models\SiteSetting::get('rebate_12m', 40),
            'razorpay_key_id' => \App\Models\SiteSetting::get('razorpay_key_id', ''),
            'razorpay_key_secret' => \App\Models\SiteSetting::get('razorpay_key_secret', ''),
        ];

        $pricingTiers = \App\Models\SiteSetting::getPricingTiers();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
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
            'course_base_daily_fee' => 'nullable|numeric|min:0.5',
            'course_base_monthly_fee' => 'nullable|numeric|min:1',
            'rebate_1w' => 'nullable|numeric|min:0|max:100',
            'rebate_1m' => 'nullable|numeric|min:0|max:100',
            'rebate_3m' => 'nullable|numeric|min:0|max:100',
            'rebate_6m' => 'nullable|numeric|min:0|max:100',
            'rebate_1y' => 'nullable|numeric|min:0|max:100',
            'rebate_2m' => 'nullable|numeric|min:0|max:100',
            'rebate_12m' => 'nullable|numeric|min:0|max:100',
            'razorpay_key_id' => 'nullable|string|max:100',
            'razorpay_key_secret' => 'nullable|string|max:100',
        ]);

        if (isset($validated['course_base_daily_fee']) && $validated['course_base_daily_fee'] > 0) {
            \App\Models\SiteSetting::set('course_base_daily_fee', $validated['course_base_daily_fee']);
            \App\Models\SiteSetting::set('course_base_monthly_fee', round($validated['course_base_daily_fee'] * 30));
        }

        foreach ($validated as $key => $value) {
            if ($key !== 'course_base_daily_fee' && $value !== null) {
                \App\Models\SiteSetting::set($key, $value);
            }
        }

        return redirect()->back()->with('success', 'Daily base rate and progressive rebate schedule updated successfully! All candidate plans updated.');
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
