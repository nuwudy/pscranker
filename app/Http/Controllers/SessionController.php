<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\UserSessionProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    /**
     * Display a listing of learning sessions.
     */
    public function index(Request $request)
    {
        $categories = Category::with(['sessions' => function ($query) {
            $query->where('is_active', true)->orderBy('order', 'asc');
        }])->orderBy('order', 'asc')->get();

        $generalSessions = Session::with(['category', 'contents', 'questions'])
            ->where('is_active', true)
            ->where('in_general_stream', true)
            ->orderBy('general_stream_order', 'asc')
            ->get();

        $featuredSession = $generalSessions->first()
            ?? Session::with(['category', 'contents', 'questions'])
                ->where('is_active', true)
                ->orderBy('order', 'asc')
                ->first();

        $activeTab = $request->query('subject') 
            ? $request->query('subject') 
            : ($request->query('stream') === 'subject' ? 'english' : 'general');

        return view('pages.sessions-index', compact('categories', 'generalSessions', 'featuredSession', 'activeTab'));
    }

    /**
     * Show the 4-phase micro-learning session runner.
     */
    public function show(string $slug, Request $request)
    {
        $session = Session::with([
            'category',
            'contents',
            'diagnosticQuestion',
            'reinforcementQuestions',
            'omrQuestions'
        ])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        $stream = $request->query('stream', 'subject');
        $previousSession = $session->getPreviousSession($stream);
        $nextSession = $session->getNextSession($stream);

        // Unit calculation depends on stream
        if ($stream === 'general' && $session->in_general_stream) {
            $totalUnits = Session::where('is_active', true)->where('in_general_stream', true)->count();
            $unitNumber = Session::where('is_active', true)
                ->where('in_general_stream', true)
                ->where('general_stream_order', '<=', $session->general_stream_order ?? 1)
                ->count() ?: ($session->general_stream_order ?? 1);
            $streamTitle = 'General Stream (Mixed Train)';
            $streamTitleMalayalam = 'ജനറൽ സ്ട്രീം (മിക്സഡ് മാസ്റ്റർ ട്രെയിൻ)';
        } else {
            $categorySessionsQuery = Session::where('is_active', true);
            if ($session->category_id) {
                $categorySessionsQuery->where('category_id', $session->category_id);
            }

            $totalUnits = (clone $categorySessionsQuery)->count();
            $unitNumber = (clone $categorySessionsQuery)->where('order', '<=', $session->order)->count() ?: $session->order;
            $streamTitle = ($session->category ? $session->category->name : 'Subject') . ' Stream';
            $streamTitleMalayalam = ($session->category ? $session->category->name_malayalam : 'പ്രത്യേക വിഷയം');
        }

        // Premium gating check (Admins and active subscribers bypass)
        $isAdmin = auth()->check() && (auth()->user()->email === 'admin@pscranker.com' || auth()->user()->is_admin ?? false);
        $isSubscribed = auth()->check() && auth()->user()->isSubscribed();
        $isLocked = $session->is_premium && !($isAdmin || $isSubscribed);

        return view('pages.session-runner', compact(
            'session',
            'previousSession',
            'nextSession',
            'unitNumber',
            'totalUnits',
            'isLocked',
            'stream',
            'streamTitle',
            'streamTitleMalayalam'
        ));
    }

    /**
     * Save progress state from the Alpine.js session runner.
     */
    public function saveProgress(Request $request, int $id): JsonResponse
    {
        $session = Session::findOrFail($id);

        $validated = $request->validate([
            'guest_token' => 'nullable|string|max:64',
            'current_phase' => 'required|string|in:diagnostic,lesson,reinforcement,omr,summary',
            'diagnostic_status' => 'nullable|string|in:pending,correct,incorrect',
            'reinforcement_score' => 'nullable|numeric',
            'omr_score' => 'nullable|numeric',
            'net_marks' => 'nullable|numeric',
            'xp_earned' => 'nullable|integer',
            'time_taken_seconds' => 'nullable|integer',
            'is_completed' => 'nullable|boolean',
        ]);

        $userId = auth()->id();
        $guestToken = $validated['guest_token'] ?? $request->cookie('pscranker_guest_token') ?? Str::random(32);

        $progress = UserSessionProgress::firstOrNew([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'session_id' => $session->id,
        ]);

        $progress->current_phase = $validated['current_phase'];
        if (isset($validated['diagnostic_status'])) {
            $progress->diagnostic_status = $validated['diagnostic_status'];
        }
        if (isset($validated['reinforcement_score'])) {
            $progress->reinforcement_score = $validated['reinforcement_score'];
        }
        if (isset($validated['omr_score'])) {
            $progress->omr_score = $validated['omr_score'];
        }
        if (isset($validated['net_marks'])) {
            $progress->net_marks = $validated['net_marks'];
        }
        if (isset($validated['xp_earned'])) {
            $progress->xp_earned = max($progress->xp_earned ?? 0, (int) $validated['xp_earned']);
        }
        if (isset($validated['time_taken_seconds'])) {
            $progress->time_taken_seconds = (int) $validated['time_taken_seconds'];
        }
        if (!empty($validated['is_completed']) && !$progress->completed_at) {
            $progress->completed_at = now();
        }

        $progress->save();

        return response()->json([
            'success' => true,
            'guest_token' => $guestToken,
            'progress' => $progress,
        ])->withCookie(cookie()->make('pscranker_guest_token', $guestToken, 60 * 24 * 30));
    }

    /**
     * Submit OMR sheet answers and calculate strict Kerala PSC scoring:
     * +1.00 for Correct, -0.33 deduction for Wrong, 0.00 for Unattempted.
     */
    public function submitOmr(Request $request, int $id): JsonResponse
    {
        $session = Session::with(['omrQuestions', 'reinforcementQuestions'])->findOrFail($id);

        $validated = $request->validate([
            'answers' => 'required|array', // [question_id => selected_option (A/B/C/D)]
            'time_taken_seconds' => 'nullable|integer',
            'guest_token' => 'nullable|string',
        ]);

        $omrQuestions = $session->effective_omr_questions;
        $submittedAnswers = $validated['answers'];

        $correctCount = 0;
        $wrongCount = 0;
        $unattemptedCount = 0;
        $questionDetails = [];

        foreach ($omrQuestions as $q) {
            $userAns = $submittedAnswers[$q->id] ?? null;
            $correctAns = strtoupper(trim($q->correct_option));
            $isAttempted = !empty($userAns);
            $isCorrect = false;

            if ($isAttempted) {
                if (strtoupper(trim($userAns)) === $correctAns) {
                    $correctCount++;
                    $isCorrect = true;
                } else {
                    $wrongCount++;
                }
            } else {
                $unattemptedCount++;
            }

            $questionDetails[] = [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_text_malayalam' => $q->question_text_malayalam,
                'user_answer' => $userAns,
                'correct_answer' => $correctAns,
                'is_correct' => $isCorrect,
                'is_attempted' => $isAttempted,
                'explanation' => $q->explanation,
                'explanation_malayalam' => $q->explanation_malayalam,
                'trap_warning' => $q->resolved_trap_warning,
            ];
        }

        $totalQuestions = $omrQuestions->count();
        $netMarks = round(($correctCount * 1.00) - ($wrongCount * 0.3333), 2);
        $maxPossibleMarks = $totalQuestions * 1.00;
        $accuracy = ($correctCount + $wrongCount) > 0 
            ? round(($correctCount / ($correctCount + $wrongCount)) * 100, 1) 
            : 0;

        // Determine Rank Badge
        $rankBadge = 'PSC Cadet 🔰';
        $badgeColor = 'slate';
        if ($netMarks >= ($totalQuestions * 0.85)) {
            $rankBadge = 'State 1st Ranker 🔥';
            $badgeColor = 'amber';
        } elseif ($netMarks >= ($totalQuestions * 0.65)) {
            $rankBadge = 'Deputy Collector Grade ⚡';
            $badgeColor = 'blue';
        } elseif ($netMarks >= ($totalQuestions * 0.40)) {
            $rankBadge = 'LDC Confirmed 🎯';
            $badgeColor = 'emerald';
        } elseif ($netMarks > 0) {
            $rankBadge = 'Probationer in Training 📚';
            $badgeColor = 'purple';
        } else {
            $rankBadge = 'Trap Alert! Needs Revision ⚠️';
            $badgeColor = 'red';
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total_questions' => $totalQuestions,
                'correct' => $correctCount,
                'wrong' => $wrongCount,
                'unattempted' => $unattemptedCount,
                'gross_marks' => $correctCount * 1.00,
                'negative_marks' => round($wrongCount * 0.3333, 2),
                'net_marks' => $netMarks,
                'max_marks' => $maxPossibleMarks,
                'accuracy' => $accuracy,
                'time_taken_seconds' => (int) ($validated['time_taken_seconds'] ?? 0),
                'rank_badge' => $rankBadge,
                'badge_color' => $badgeColor,
            ],
            'questions' => $questionDetails,
        ]);
    }
}
