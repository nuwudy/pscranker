<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PscmSession;
use App\Models\PscmUserAttempt;

class PscmSessionController extends Controller
{
    public function show($slug)
    {
        $session = PscmSession::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('learner.pscm_sessions.show', compact('session'));
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
