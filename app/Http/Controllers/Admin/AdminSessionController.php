<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\SessionContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSessionController extends Controller
{
    /**
     * Display a listing of sessions for admin.
     */
    public function index()
    {
        $sessions = Session::with(['category'])
            ->withCount(['contents', 'questions'])
            ->orderBy('order', 'asc')
            ->paginate(15);

        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new session.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.sessions.form', [
            'session' => new Session(),
            'categories' => $categories,
            'isEdit' => false,
            'contents' => collect(),
            'diagnosticQuestions' => collect(),
            'reinforcementQuestions' => collect(),
            'omrQuestions' => collect(),
        ]);
    }

    /**
     * Store a newly created session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_malayalam' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:learning_sessions,slug',
            'category_id' => 'nullable|exists:categories,id',
            'order' => 'required|integer',
            'xp_reward' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'contents' => 'nullable|array',
            'contents.*.type' => 'required|string|in:image,video,audio,text,html',
            'contents.*.content_data' => 'required|array',
            'contents.*.order' => 'required|integer',
            'questions' => 'nullable|array',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $count = Session::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $session = Session::create([
            'title' => $validated['title'],
            'title_malayalam' => $validated['title_malayalam'] ?? null,
            'slug' => $slug,
            'category_id' => $validated['category_id'] ?? null,
            'order' => $validated['order'],
            'xp_reward' => $validated['xp_reward'],
            'is_active' => $request->boolean('is_active', true),
            'is_premium' => $request->boolean('is_premium'),
            'price' => $request->boolean('is_premium') ? ($request->input('price') ?: 199.00) : null,
        ]);

        $this->syncContentsAndQuestions($session, $request);

        return redirect()->route('admin.sessions.edit', $session)
            ->with('success', 'Learning Session created successfully!');
    }

    /**
     * Show the form for editing the session.
     */
    public function edit(Session $session)
    {
        $session->load(['category', 'contents', 'questions']);
        $categories = Category::orderBy('name')->get();

        $contents = $session->contents()->orderBy('order', 'asc')->get();
        $diagnosticQuestions = $session->questions()->where('phase_type', 'diagnostic')->get();
        $reinforcementQuestions = $session->questions()->where('phase_type', 'reinforcement')->get();
        $omrQuestions = $session->questions()->where('phase_type', 'omr')->get();

        return view('admin.sessions.form', [
            'session' => $session,
            'categories' => $categories,
            'isEdit' => true,
            'contents' => $contents,
            'diagnosticQuestions' => $diagnosticQuestions,
            'reinforcementQuestions' => $reinforcementQuestions,
            'omrQuestions' => $omrQuestions,
        ]);
    }

    /**
     * Update the specified session.
     */
    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_malayalam' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:learning_sessions,slug,' . $session->id,
            'category_id' => 'nullable|exists:categories,id',
            'order' => 'required|integer',
            'xp_reward' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
        ]);

        $session->update([
            'title' => $validated['title'],
            'title_malayalam' => $validated['title_malayalam'] ?? null,
            'slug' => Str::slug($validated['slug']),
            'category_id' => $validated['category_id'] ?? null,
            'order' => $validated['order'],
            'xp_reward' => $validated['xp_reward'],
            'is_active' => $request->boolean('is_active', true),
            'is_premium' => $request->boolean('is_premium'),
            'price' => $request->boolean('is_premium') ? ($request->input('price') ?: 199.00) : null,
        ]);

        $this->syncContentsAndQuestions($session, $request);

        return redirect()->route('admin.sessions.edit', $session)
            ->with('success', 'Session updated successfully!');
    }

    /**
     * Remove the specified session.
     */
    public function destroy(Session $session)
    {
        $session->delete();
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session deleted successfully.');
    }

    /**
     * Helper to sync content blocks and questions.
     */
    private function syncContentsAndQuestions(Session $session, Request $request): void
    {
        // 1. Process Content Blocks (JSON payload or array)
        if ($request->has('contents_json')) {
            $contentsData = json_decode($request->input('contents_json'), true) ?? [];
            $session->contents()->delete();

            foreach ($contentsData as $idx => $block) {
                if (!empty($block['type'])) {
                    SessionContent::create([
                        'session_id' => $session->id,
                        'type' => $block['type'],
                        'content_data' => $block['content_data'] ?? [],
                        'order' => $idx + 1,
                    ]);
                }
            }
        }

        // 2. Process Questions (JSON payload)
        if ($request->has('questions_json')) {
            $questionsData = json_decode($request->input('questions_json'), true) ?? [];
            $session->questions()->delete();

            foreach ($questionsData as $q) {
                if (!empty($q['question_text'])) {
                    Question::create([
                        'session_id' => $session->id,
                        'category_id' => $session->category_id,
                        'phase_type' => $q['phase_type'] ?? 'reinforcement',
                        'question_text' => $q['question_text'],
                        'question_text_malayalam' => $q['question_text_malayalam'] ?? null,
                        'option_a' => $q['option_a'] ?? '',
                        'option_b' => $q['option_b'] ?? '',
                        'option_c' => $q['option_c'] ?? '',
                        'option_d' => $q['option_d'] ?? '',
                        'correct_option' => strtoupper(trim($q['correct_option'] ?? 'A')),
                        'explanation' => $q['explanation'] ?? null,
                        'explanation_malayalam' => $q['explanation_malayalam'] ?? null,
                        'trap_warning' => $q['trap_warning_text'] ?? ($q['trap_warning'] ?? null),
                        'trap_warning_text' => $q['trap_warning_text'] ?? ($q['trap_warning'] ?? null),
                        'psc_exam_reference' => $q['psc_exam_reference'] ?? null,
                        'points' => 1.00,
                        'negative_points' => 0.33,
                    ]);
                }
            }
        }
    }
}
