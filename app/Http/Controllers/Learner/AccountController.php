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

        // 4. Subscription & Plan details
        $isSubscribed = $user->isSubscribed();
        $subscriptionDays = $user->subscriptionDaysRemaining();
        $categories = PscmSession::CATEGORIES;

        return view('learner.account', compact(
            'user',
            'continueSession',
            'continueUnitNumber',
            'totalUnits',
            'sessions',
            'completedAttempts',
            'completedCount',
            'progressPercent',
            'averageScore',
            'isSubscribed',
            'subscriptionDays',
            'categories'
        ));
    }
}
