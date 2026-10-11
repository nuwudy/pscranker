<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PscmSession;
use App\Models\PscmUserAttempt;

class PscmSessionController extends Controller
{
    public function index(Request $request)
    {
        $categories = PscmSession::CATEGORIES;
        $selectedCategory = $request->get('category');
        
        $query = PscmSession::where('status', 'published')->latest();
        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }
        $sessions = $query->paginate(12)->withQueryString();

        return view('learner.pscm_sessions.index', compact('sessions', 'categories', 'selectedCategory'));
    }

    public function show($slug)
    {
        $session = PscmSession::where('slug', $slug)->where('status', 'published')->firstOrFail();
        
        // Find previous and next published sessions in the same category (or global fallback)
        $queryNext = PscmSession::where('status', 'published');
        $queryPrev = PscmSession::where('status', 'published');

        if ($session->category) {
            $queryNext->where('category', $session->category);
            $queryPrev->where('category', $session->category);
        }

        $nextSession = (clone $queryNext)
            ->where('created_at', '<', $session->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        $prevSession = (clone $queryPrev)
            ->where('created_at', '>', $session->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        return view('learner.pscm_sessions.show', compact('session', 'prevSession', 'nextSession'));
    }

    public function storeAttempt(Request $request, $sessionId)
    {
        // This is a stub for saving the completed attempt
        $session = PscmSession::findOrFail($sessionId);

        $attempt = PscmUserAttempt::create([
            'user_id' => auth()->id() ?? 1, // Mock user if not auth'd for testing
            'session_id' => $session->id,
            'practice_responses' => $request->input('practice_responses', []),
            'omr_responses' => $request->input('omr_responses', []),
            'gamification' => $request->input('gamification', []),
            'completed_at' => now(),
        ]);

        return response()->json(['success' => true, 'attempt_id' => $attempt->id]);
    }
}
