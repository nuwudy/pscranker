<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PscmSession;
use Illuminate\Support\Str;

class PscmSessionController extends Controller
{
    public function index()
    {
        $sessions = PscmSession::latest()->get();
        return view('admin.pscm_sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('admin.pscm_sessions.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'hook_question' => 'required|json',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $data['hook_question'] = json_decode($data['hook_question'], true);
        
        if (isset($data['media'])) $data['media'] = json_decode($data['media'], true);
        if (isset($data['practice_mcqs'])) $data['practice_mcqs'] = json_decode($data['practice_mcqs'], true);
        
        PscmSession::create($data);

        return redirect()->route('admin.pscm.index')->with('success', 'Session created successfully.');
    }

    public function edit(PscmSession $pscmSession)
    {
        return view('admin.pscm_sessions.form', ['session' => $pscmSession]);
    }

    public function update(Request $request, PscmSession $pscmSession)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'hook_question' => 'required|json',
        ]);

        $data = $request->all();
        $data['hook_question'] = json_decode($data['hook_question'], true);
        if (isset($data['media'])) $data['media'] = json_decode($data['media'], true);
        if (isset($data['practice_mcqs'])) $data['practice_mcqs'] = json_decode($data['practice_mcqs'], true);
        
        $pscmSession->update($data);

        return redirect()->route('admin.pscm.index')->with('success', 'Session updated successfully.');
    }

    public function generate(Request $request, \App\Services\AiMicrolearningGenerator $aiService)
    {
        $request->validate([
            'hook_question' => 'required|json'
        ]);

        try {
            $hookQuestion = json_decode($request->hook_question, true);
            $generated = $aiService->generate($hookQuestion);
            
            return response()->json([
                'success' => true,
                'lesson_content' => $generated['lesson_content'] ?? '',
                'practice_mcqs' => $generated['practice_mcqs'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
