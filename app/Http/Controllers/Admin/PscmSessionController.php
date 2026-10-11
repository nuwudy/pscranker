<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PscmSession;
use Illuminate\Support\Str;

class PscmSessionController extends Controller
{
    public function index(Request $request)
    {
        $categories = PscmSession::CATEGORIES;
        $selectedCategory = $request->get('category');
        
        $query = PscmSession::latest();
        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }
        $sessions = $query->paginate(15)->withQueryString();

        return view('admin.pscm_sessions.index', compact('sessions', 'categories', 'selectedCategory'));
    }

    public function create()
    {
        $categories = PscmSession::CATEGORIES;
        return view('admin.pscm_sessions.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'hook_question' => 'required|string',
            'access_level' => 'nullable|string|in:free,registered,premium',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['access_level'] = $request->input('access_level', 'premium') ?: 'premium';
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        
        if (!empty($data['media'])) {
            $data['media'] = is_array($data['media']) ? $data['media'] : (json_decode($data['media'], true) ?? []);
        } else {
            $data['media'] = [];
        }

        // Merge discrete media inputs if provided
        if ($request->filled('video_url')) {
            $data['media']['video_url'] = trim($request->input('video_url'));
        }
        if ($request->filled('image_url')) {
            $data['media']['image_url'] = trim($request->input('image_url'));
        }
        if ($request->filled('image_caption')) {
            $data['media']['image_caption'] = trim($request->input('image_caption'));
        }

        if (!empty($data['practice_mcqs'])) {
            $data['practice_mcqs'] = json_decode($data['practice_mcqs'], true) ?? [];
        } else {
            $data['practice_mcqs'] = [];
        }
        
        if (!empty($data['hook_question'])) {
            $data['hook_question'] = json_decode($data['hook_question'], true) ?? [];
        }
        
        PscmSession::create($data);

        return redirect()->route('admin.pscm.index')->with('success', 'Session created successfully.');
    }

    public function edit(PscmSession $pscmSession)
    {
        $categories = PscmSession::CATEGORIES;
        return view('admin.pscm_sessions.form', [
            'session' => $pscmSession,
            'categories' => $categories
        ]);
    }

    public function destroy(PscmSession $pscmSession)
    {
        $pscmSession->delete();
        return redirect()->route('admin.pscm.index')->with('success', 'Session deleted successfully.');
    }

    public function update(Request $request, PscmSession $pscmSession)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'hook_question' => 'required|string',
            'access_level' => 'nullable|string|in:free,registered,premium',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        if ($request->has('access_level')) {
            $data['access_level'] = $request->input('access_level', 'premium');
        }
        
        if (!empty($data['media'])) {
            $data['media'] = is_array($data['media']) ? $data['media'] : (json_decode($data['media'], true) ?? []);
        } else {
            $data['media'] = [];
        }

        // Merge discrete media inputs if provided
        if ($request->has('video_url')) {
            $data['media']['video_url'] = trim($request->input('video_url', ''));
        }
        if ($request->has('image_url')) {
            $data['media']['image_url'] = trim($request->input('image_url', ''));
        }
        if ($request->has('image_caption')) {
            $data['media']['image_caption'] = trim($request->input('image_caption', ''));
        }

        if (!empty($data['practice_mcqs'])) {
            $data['practice_mcqs'] = json_decode($data['practice_mcqs'], true) ?? [];
        } else {
            $data['practice_mcqs'] = [];
        }

        if (!empty($data['hook_question'])) {
            $data['hook_question'] = json_decode($data['hook_question'], true) ?? [];
        }
        
        $pscmSession->update($data);

        return redirect()->route('admin.pscm.index')->with('success', 'Session updated successfully.');
    }

    public function generate(Request $request, \App\Services\AiMicrolearningGenerator $aiService)
    {
        $request->validate([
            'hook_question' => 'required|string'
        ]);

        try {
            $hookQuestion = $request->hook_question;
            $generated = $aiService->generate($hookQuestion);
            
            return response()->json([
                'success' => true,
                'lesson_content' => $generated['lesson_content'] ?? '',
                'practice_mcqs' => $generated['practice_mcqs'] ?? [],
                'hook_question_json' => $generated['hook_question'] ?? null,
                'category' => $generated['category'] ?? null,
                'suggested_title' => $generated['suggested_title'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
