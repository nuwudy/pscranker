<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PscmSession;
use App\Models\PscmUserAttempt;
use App\Http\Controllers\Learner\PscmSessionController;

class AccountController extends Controller
{
    /**
     * Display the candidate's My Account / Student Learning Train Hub.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Resolve student's next/active session on the train
        $continueSession = PscmSessionController::getContinueSession($request);
        $continueUnitNumber = $continueSession ? PscmSessionController::getSessionUnitNumber($continueSession) : 1;
        $totalUnits = PscmSession::where('status', 'published')->count();

        // 2. Fetch all published sessions in global sequential train order
        $sessions = PscmSession::where('status', 'published')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 3. Fetch student's completed attempts keyed by session_id
        $completedAttempts = PscmUserAttempt::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->get()
            ->unique('session_id')
            ->keyBy('session_id');

        $completedCount = $completedAttempts->count();
        $progressPercent = $totalUnits > 0 ? round(($completedCount / $totalUnits) * 100, 1) : 0;

        // Calculate average OMR score from attempts
        $totalScore = 0;
        $scoresCount = 0;
        foreach ($completedAttempts as $att) {
            if (isset($att->gamification['score'])) {
                $totalScore += floatval($att->gamification['score']);
                $scoresCount++;
            }
        }
        $averageScore = $scoresCount > 0 ? round($totalScore / $scoresCount, 2) : null;

        // 4. Build Compact Train Window (Nearby Stations around active unit)
        // Shows 5-6 stations centered on the student's current position
        $windowRadius = 2;
        $startIndex = max(0, $continueUnitNumber - 1 - $windowRadius);
        $trainWindowSlice = $sessions->slice($startIndex, 5)->values();

        $trainWindow = [];
        foreach ($trainWindowSlice as $offset => $sess) {
            $unitNum = $startIndex + $offset + 1;
            $isCompleted = isset($completedAttempts[$sess->id]);
            $attempt = $isCompleted ? $completedAttempts[$sess->id] : null;
            $score = $attempt && isset($attempt->gamification['score']) ? $attempt->gamification['score'] : null;
            $isActive = $continueSession && $continueSession->id === $sess->id;

            $trainWindow[] = [
                'unit_number' => $unitNum,
                'session' => $sess,
                'is_completed' => $isCompleted,
                'is_active' => $isActive,
                'score' => $score,
            ];
        }

        // 5. Build Rich Category Tiles with Subject Progress
        $categoryConfigs = [
            'History' => [
                'name' => 'History & Renaissance',
                'malayalam' => 'ചരിത്രവും നവോത്ഥാനവും',
                'icon' => '🏛️',
                'bg_gradient' => 'from-amber-500/10 via-orange-500/5 to-transparent',
                'border_color' => 'border-amber-200 hover:border-amber-400',
                'badge_color' => 'bg-amber-50 text-amber-900 border-amber-200',
                'bar_gradient' => 'from-amber-500 to-orange-500',
            ],
            'Geography' => [
                'name' => 'Geography & Nature',
                'malayalam' => 'ഭൂമിശാസ്ത്രം & പരിസ്ഥിതി',
                'icon' => '🌍',
                'bg_gradient' => 'from-emerald-500/10 via-teal-500/5 to-transparent',
                'border_color' => 'border-emerald-200 hover:border-emerald-400',
                'badge_color' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
                'bar_gradient' => 'from-emerald-500 to-teal-500',
            ],
            'Constitution' => [
                'name' => 'Constitution & Polity',
                'malayalam' => 'ഇന്ത്യൻ ഭരണഘടന',
                'icon' => '⚖️',
                'bg_gradient' => 'from-blue-500/10 via-indigo-500/5 to-transparent',
                'border_color' => 'border-blue-200 hover:border-blue-400',
                'badge_color' => 'bg-blue-50 text-blue-900 border-blue-200',
                'bar_gradient' => 'from-blue-600 to-indigo-600',
            ],
            'Science' => [
                'name' => 'General Science & Tech',
                'malayalam' => 'പൊതു ശാസ്ത്രം & ടെക്നോളജി',
                'icon' => '🔬',
                'bg_gradient' => 'from-purple-500/10 via-fuchsia-500/5 to-transparent',
                'border_color' => 'border-purple-200 hover:border-purple-400',
                'badge_color' => 'bg-purple-50 text-purple-900 border-purple-200',
                'bar_gradient' => 'from-purple-600 to-fuchsia-600',
            ],
            'Maths' => [
                'name' => 'Maths & Mental Ability',
                'malayalam' => 'ലഘുഗണിതം & റീസണിംഗ്',
                'icon' => '📐',
                'bg_gradient' => 'from-cyan-500/10 via-sky-500/5 to-transparent',
                'border_color' => 'border-cyan-200 hover:border-cyan-400',
                'badge_color' => 'bg-cyan-50 text-cyan-900 border-cyan-200',
                'bar_gradient' => 'from-cyan-500 to-blue-500',
            ],
            'Current Affairs' => [
                'name' => 'Current Affairs & Events',
                'malayalam' => 'ആനുകാലിക വിവരങ്ങൾ',
                'icon' => '📰',
                'bg_gradient' => 'from-rose-500/10 via-red-500/5 to-transparent',
                'border_color' => 'border-rose-200 hover:border-rose-400',
                'badge_color' => 'bg-rose-50 text-rose-900 border-rose-200',
                'bar_gradient' => 'from-rose-500 to-red-500',
            ],
            'English' => [
                'name' => 'General English',
                'malayalam' => 'ജനറൽ ഇംഗ്ലീഷ്',
                'icon' => '🅰️',
                'bg_gradient' => 'from-indigo-500/10 via-violet-500/5 to-transparent',
                'border_color' => 'border-indigo-200 hover:border-indigo-400',
                'badge_color' => 'bg-indigo-50 text-indigo-900 border-indigo-200',
                'bar_gradient' => 'from-indigo-600 to-violet-600',
            ],
            'Malayalam' => [
                'name' => 'Malayalam Language',
                'malayalam' => 'മലയാള ഭാഷാ സാഹിത്യം',
                'icon' => '✍️',
                'bg_gradient' => 'from-amber-500/10 via-yellow-500/5 to-transparent',
                'border_color' => 'border-amber-200 hover:border-amber-400',
                'badge_color' => 'bg-yellow-50 text-yellow-900 border-yellow-200',
                'bar_gradient' => 'from-amber-500 to-yellow-500',
            ],
            'GK' => [
                'name' => 'General Knowledge (GK)',
                'malayalam' => 'പൊതു വിജ്ഞാനം',
                'icon' => '💡',
                'bg_gradient' => 'from-slate-500/10 via-gray-500/5 to-transparent',
                'border_color' => 'border-slate-200 hover:border-slate-400',
                'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
                'bar_gradient' => 'from-slate-700 to-slate-900',
            ],
        ];

        // Group published sessions by category to count totals
        $categoryTotals = $sessions->groupBy('category')->map->count();

        // Count student completed sessions by category
        $completedCategoryCounts = [];
        foreach ($completedAttempts as $sessionId => $att) {
            $sess = $sessions->firstWhere('id', $sessionId);
            if ($sess && $sess->category) {
                $completedCategoryCounts[$sess->category] = ($completedCategoryCounts[$sess->category] ?? 0) + 1;
            }
        }

        $categoryTiles = [];
        foreach ($categoryConfigs as $catKey => $conf) {
            $catTotal = $categoryTotals[$catKey] ?? 0;
            $catCompleted = $completedCategoryCounts[$catKey] ?? 0;
            $catPercent = $catTotal > 0 ? round(($catCompleted / $catTotal) * 100) : 0;

            $categoryTiles[] = array_merge($conf, [
                'key' => $catKey,
                'total_units' => $catTotal,
                'completed_units' => $catCompleted,
                'percent' => $catPercent,
            ]);
        }

        // 6. Subscription & Plan details
        $isSubscribed = $user->isSubscribed();
        $subscriptionDays = $user->subscriptionDaysRemaining();

        // 7. Compact Search Index for Instant Client-Side Unit Finding
        $searchIndex = [];
        foreach ($sessions as $index => $sess) {
            $unitNum = $index + 1;
            $isCompleted = isset($completedAttempts[$sess->id]);
            $attempt = $isCompleted ? $completedAttempts[$sess->id] : null;
            $score = $attempt && isset($attempt->gamification['score']) ? $attempt->gamification['score'] : null;

            $searchIndex[] = [
                'unit' => $unitNum,
                'id' => $sess->id,
                'title' => $sess->title,
                'slug' => $sess->slug,
                'category' => $sess->category ?? 'GK',
                'access' => $sess->access_level ?? 'premium',
                'completed' => $isCompleted,
                'score' => $score !== null ? round(floatval($score), 1) : null,
                'url' => route('learner.pscm.show', $sess->slug),
            ];
        }

        return view('learner.account', compact(
            'user',
            'continueSession',
            'continueUnitNumber',
            'totalUnits',
            'completedCount',
            'progressPercent',
            'averageScore',
            'trainWindow',
            'categoryTiles',
            'isSubscribed',
            'subscriptionDays',
            'searchIndex'
        ));
    }
}
