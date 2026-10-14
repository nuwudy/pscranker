<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PscmSession;
use App\Models\PscmUserAttempt;

class PscmSessionController extends Controller
{
    /**
     * Resolve the session where the student left off.
     * Hierarchy:
     * 1. If authenticated: next session after latest completed attempt.
     * 2. Cached active session for authenticated user.
     * 3. Session / Cookie stored active session.
     * 4. Fallback: First published session ever created.
     */
    public static function getContinueSession(?Request $request = null): ?PscmSession
    {
        $request = $request ?? request();

        // 1. If authenticated, check latest completed attempt
        if (auth()->check()) {
            $latestAttempt = PscmUserAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->first();

            if ($latestAttempt && $latestAttempt->session && $latestAttempt->session->status === 'published') {
                $completed = $latestAttempt->session;

                // Find the next published session created chronologically after the completed one
                $next = PscmSession::where('status', 'published')
                    ->where(function ($q) use ($completed) {
                        $q->where('created_at', '>', $completed->created_at)
                          ->orWhere(function ($q2) use ($completed) {
                              $q2->where('created_at', '=', $completed->created_at)
                                 ->where('id', '>', $completed->id);
                          });
                    })
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($next) {
                    return $next;
                }

                // If student completed all sessions, return the last completed session
                return $completed;
            }

            // Check cached active session for user
            $cachedId = cache()->get('pscm_last_session_user_' . auth()->id());
            if ($cachedId) {
                $sess = PscmSession::where('id', $cachedId)->where('status', 'published')->first();
                if ($sess) {
                    return $sess;
                }
            }
        }

        // 2. Check Laravel session
        $sessId = session('pscm_last_session_id');
        if ($sessId) {
            $sess = PscmSession::where('id', $sessId)->where('status', 'published')->first();
            if ($sess) {
                return $sess;
            }
        }

        $sessSlug = session('pscm_last_session_slug');
        if ($sessSlug) {
            $sess = PscmSession::where('slug', $sessSlug)->where('status', 'published')->first();
            if ($sess) {
                return $sess;
            }
        }

        // 3. Check Cookie
        $cookieSlug = $request ? $request->cookie('pscm_last_slug') : null;
        if ($cookieSlug) {
            $sess = PscmSession::where('slug', $cookieSlug)->where('status', 'published')->first();
            if ($sess) {
                return $sess;
            }
        }

        // 4. Default: First session created (chronological origin)
        return PscmSession::where('status', 'published')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Calculate 1-indexed sequential unit position of a session across the curriculum.
     */
    public static function getSessionUnitNumber(PscmSession $session): int
    {
        return PscmSession::where('status', 'published')
            ->where(function ($q) use ($session) {
                $q->where('created_at', '<', $session->created_at)
                  ->orWhere(function ($q2) use ($session) {
                      $q2->where('created_at', '=', $session->created_at)
                         ->where('id', '<=', $session->id);
                  });
            })
            ->count();
    }

    /**
     * Direct endpoint to resume practice where the student left off.
     */
    public function continueSession(Request $request)
    {
        $session = self::getContinueSession($request);

        if (!$session) {
            return redirect()->route('learner.pscm.index')
                ->with('info', 'No published course units available yet.');
        }

        return redirect()->route('learner.pscm.show', $session->slug);
    }

    public function index(Request $request)
    {
        $categories = PscmSession::CATEGORIES;
        $selectedCategory = $request->get('category');
        
        // Curriculum order is chronological: created_at ASC
        $query = PscmSession::where('status', 'published')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }
        $sessions = $query->paginate(12)->withQueryString();

        $continueSession = self::getContinueSession($request);
        $continueUnitNumber = $continueSession ? self::getSessionUnitNumber($continueSession) : 1;
        $totalUnits = PscmSession::where('status', 'published')->count();

        return view('learner.pscm_sessions.index', compact('sessions', 'categories', 'selectedCategory', 'continueSession', 'continueUnitNumber', 'totalUnits'));
    }

    public function show(Request $request, $slug)
    {
        $session = PscmSession::where('slug', $slug)->where('status', 'published')->firstOrFail();
        
        // Find previous and next sessions strictly chronologically irrespective of category
        $nextSession = PscmSession::where('status', 'published')
            ->where(function ($q) use ($session) {
                $q->where('created_at', '>', $session->created_at)
                  ->orWhere(function ($q2) use ($session) {
                      $q2->where('created_at', '=', $session->created_at)
                         ->where('id', '>', $session->id);
                  });
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        $prevSession = PscmSession::where('status', 'published')
            ->where(function ($q) use ($session) {
                $q->where('created_at', '<', $session->created_at)
                  ->orWhere(function ($q2) use ($session) {
                      $q2->where('created_at', '=', $session->created_at)
                         ->where('id', '<', $session->id);
                  });
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // Calculate unit numbering across global curriculum
        $unitNumber = self::getSessionUnitNumber($session);
        $totalUnits = PscmSession::where('status', 'published')->count();

        // Record student's current active session
        session([
            'pscm_last_session_id' => $session->id,
            'pscm_last_session_slug' => $session->slug,
        ]);
        if (auth()->check()) {
            cache()->put('pscm_last_session_user_' . auth()->id(), $session->id, now()->addDays(90));
        }
        cookie()->queue('pscm_last_slug', $session->slug, 60 * 24 * 90);

        // 3-Tier Access Gating Check:
        // 'free' -> Open to everyone without login
        // 'registered' -> Free for all logged-in users
        // 'premium' -> Exclusive to subscribed/PRO users (or admin)
        $user = auth()->user();
        $isAdmin = $user && $user->isAdmin();
        $isSubscribed = $user && $user->isSubscribed();

        $accessLevel = $session->access_level ?? 'premium';
        $isLocked = false;
        $lockReason = null; // 'requires_registration' or 'requires_premium'

        if ($accessLevel === 'premium') {
            if (!$isAdmin && !$isSubscribed) {
                $isLocked = true;
                $lockReason = 'requires_premium';
            }
        } elseif ($accessLevel === 'registered') {
            if (!$user) {
                $isLocked = true;
                $lockReason = 'requires_registration';
            }
        }

        return view('learner.pscm_sessions.show', compact('session', 'prevSession', 'nextSession', 'unitNumber', 'totalUnits', 'isLocked', 'lockReason'));
    }

    public function storeAttempt(Request $request, $sessionId)
    {
        $session = PscmSession::findOrFail($sessionId);

        $userId = auth()->id();
        if (!$userId) {
            $fallbackUser = \App\Models\User::first();
            $userId = $fallbackUser ? $fallbackUser->id : null;
        }

        $attempt = null;
        if ($userId) {
            $attempt = PscmUserAttempt::create([
                'user_id' => $userId,
                'session_id' => $session->id,
                'practice_responses' => $request->input('practice_responses', []),
                'omr_responses' => $request->input('omr_responses', []),
                'gamification' => $request->input('gamification', []),
                'completed_at' => now(),
            ]);
        }

        // Record completed session
        session([
            'pscm_last_completed_id' => $session->id,
            'pscm_last_completed_slug' => $session->slug,
        ]);
        if (auth()->check()) {
            cache()->put('pscm_latest_completed_user_' . auth()->id(), $session->id, now()->addDays(90));
        }

        // Automatically point next resume target to the next unit
        $nextSession = PscmSession::where('status', 'published')
            ->where(function ($q) use ($session) {
                $q->where('created_at', '>', $session->created_at)
                  ->orWhere(function ($q2) use ($session) {
                      $q2->where('created_at', '=', $session->created_at)
                         ->where('id', '>', $session->id);
                  });
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if ($nextSession) {
            session([
                'pscm_last_session_id' => $nextSession->id,
                'pscm_last_session_slug' => $nextSession->slug,
            ]);
            if (auth()->check()) {
                cache()->put('pscm_last_session_user_' . auth()->id(), $nextSession->id, now()->addDays(90));
            }
            cookie()->queue('pscm_last_slug', $nextSession->slug, 60 * 24 * 90);
        }

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt?->id,
            'next_slug' => $nextSession?->slug,
        ]);
    }
}
