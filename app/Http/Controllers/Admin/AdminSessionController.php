<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MediaFile;
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
            ->orderBy('category_id', 'asc')
            ->orderBy('order', 'asc')
            ->paginate(20);

        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * Display the Mixed Practice Train Concocter studio.
     */
    public function mixedPractice(Request $request)
    {
        $categories = Category::orderBy('order')->get();

        // Active mixed practice train ordered by sequence
        $mixedTrain = Session::with(['category'])
            ->where('in_general_stream', true)
            ->orderBy('general_stream_order', 'asc')
            ->get();

        // All subject sessions with recently added first
        $availableSessions = Session::with(['category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.sessions.mixed-practice', compact('categories', 'mixedTrain', 'availableSessions'));
    }

    /**
     * Toggle a session in/out of the Mixed Practice Train.
     */
    public function toggleMixedPractice(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:learning_sessions,id',
        ]);

        $session = Session::findOrFail($request->input('session_id'));
        $newState = !$session->in_general_stream;

        if ($newState) {
            $maxOrder = Session::where('in_general_stream', true)->max('general_stream_order') ?? 0;
            $session->in_general_stream = true;
            $session->general_stream_order = $maxOrder + 1;
        } else {
            $session->in_general_stream = false;
            $session->general_stream_order = null;
        }
        $session->save();

        // Re-index remaining general_stream_orders so there are no gaps
        $this->normalizeMixedTrainOrder();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_general_stream' => $session->in_general_stream,
                'general_stream_order' => $session->general_stream_order,
                'session' => $session->fresh(['category']),
                'mixedTrain' => Session::with(['category'])->where('in_general_stream', true)->orderBy('general_stream_order')->get(),
            ]);
        }

        return back()->with('success', 'Mixed practice train updated successfully!');
    }

    /**
     * Reorder the sequence of the Mixed Practice Train.
     */
    public function reorderMixedPractice(Request $request)
    {
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:learning_sessions,id',
        ]);

        foreach ($request->input('ordered_ids') as $index => $id) {
            Session::where('id', $id)->update([
                'in_general_stream' => true,
                'general_stream_order' => $index + 1,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'mixedTrain' => Session::with(['category'])->where('in_general_stream', true)->orderBy('general_stream_order')->get(),
            ]);
        }

        return back()->with('success', 'Train order updated successfully!');
    }

    /**
     * Normalize general stream order sequence so there are no gaps.
     */
    protected function normalizeMixedTrainOrder(): void
    {
        $sessions = Session::where('in_general_stream', true)
            ->orderBy('general_stream_order', 'asc')
            ->get();

        foreach ($sessions as $index => $s) {
            if ($s->general_stream_order !== ($index + 1)) {
                $s->general_stream_order = $index + 1;
                $s->save();
            }
        }
    }

    /**
     * Show the form for creating a new session.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        // Precalculate next sequential unit number for each subject
        $nextOrdersByCategory = $categories->mapWithKeys(function ($cat) {
            $max = Session::where('category_id', $cat->id)->max('order') ?? 0;
            return [$cat->id => $max + 1];
        });

        $defaultNextOrder = (Session::whereNull('category_id')->max('order') ?? 0) + 1;

        // Automatically assign next step in Mixed Practice Train
        $nextTrainOrder = (Session::where('in_general_stream', true)->max('general_stream_order') ?? 0) + 1;

        return view('admin.sessions.form', [
            'session' => new Session(),
            'categories' => $categories,
            'isEdit' => false,
            'contents' => collect(),
            'diagnosticQuestions' => collect(),
            'reinforcementQuestions' => collect(),
            'omrQuestions' => collect(),
            'nextOrdersByCategory' => $nextOrdersByCategory,
            'defaultNextOrder' => $defaultNextOrder,
            'nextTrainOrder' => $nextTrainOrder,
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
            'feature_image' => 'nullable|string|max:1000',
            'feature_image_file' => 'nullable|image|max:10240',
            'feature_video' => 'nullable|string|max:1000',
            'feature_video_file' => 'nullable|file|mimes:mp4,mov,ogg,webm,qt,avi,mkv|max:102400',
            'slug' => 'nullable|string|max:255|unique:learning_sessions,slug',
            'category_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'xp_reward' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'access_level' => 'nullable|string|in:guest,registered,premium',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'in_general_stream' => 'boolean',
            'general_stream_order' => 'nullable|integer',
            'creation_mode' => 'nullable|string|in:manual,code',
            'custom_html' => 'nullable|string',
            'contents' => 'nullable|array',
            'contents.*.type' => 'required|string|in:image,video,audio,text,html,map_globe',
            'contents.*.content_data' => 'required|array',
            'contents.*.order' => 'required|integer',
            'questions' => 'nullable|array',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $count = Session::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $creationMode = $request->input('creation_mode', 'manual');

        $featureImage = $validated['feature_image'] ?? null;
        if ($request->hasFile('feature_image_file')) {
            $file = $request->file('feature_image_file');
            $path = $file->store('media/images', 'public');
            $featureImage = '/storage/' . $path;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $featureImage,
                'file_type' => 'image',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        $featureVideo = $validated['feature_video'] ?? null;
        if ($request->hasFile('feature_video_file')) {
            $file = $request->file('feature_video_file');
            $path = $file->store('media/videos', 'public');
            $featureVideo = '/storage/' . $path;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $featureVideo,
                'file_type' => 'video',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        // 1. Automatic Subject Sequence Assignment (keeps separate subjects intact)
        $categoryId = $validated['category_id'] ?? null;
        $order = ($request->filled('order') && (int)$request->input('order') > 0)
            ? (int)$request->input('order')
            : ((Session::where('category_id', $categoryId)->max('order') ?? 0) + 1);

        // 2. Automatic Mixed Practice Train Placement (auto-appends latest session)
        $inGeneralStream = $request->boolean('in_general_stream', true);
        $generalStreamOrder = null;
        if ($inGeneralStream) {
            $generalStreamOrder = ($request->filled('general_stream_order') && (int)$request->input('general_stream_order') > 0)
                ? (int)$request->input('general_stream_order')
                : ((Session::where('in_general_stream', true)->max('general_stream_order') ?? 0) + 1);
        }

        $accessLevel = $request->input('access_level') ?: ($request->boolean('is_premium') ? 'premium' : 'guest');
        $isPremium = ($accessLevel === 'premium');

        $session = Session::create([
            'title' => $validated['title'],
            'title_malayalam' => $validated['title_malayalam'] ?? null,
            'feature_image' => $featureImage,
            'feature_video' => $featureVideo,
            'slug' => $slug,
            'category_id' => $categoryId,
            'order' => $order,
            'xp_reward' => $validated['xp_reward'],
            'is_active' => $request->boolean('is_active', true),
            'access_level' => $accessLevel,
            'is_premium' => $isPremium,
            'price' => $isPremium ? ($request->input('price') ?: 199.00) : null,
            'in_general_stream' => $inGeneralStream,
            'general_stream_order' => $generalStreamOrder,
            'creation_mode' => $creationMode,
            'custom_html' => $creationMode === 'code' ? $request->input('custom_html') : null,
        ]);

        if ($creationMode === 'manual') {
            $this->syncContentsAndQuestions($session, $request);
        }

        return redirect()->route('admin.sessions.edit', $session)
            ->with('success', "Learning Session created successfully! (Unit #{$order} in subject, Train Step #{$session->fresh()->general_stream_order})");
    }

    /**
     * Show the form for editing the session.
     */
    public function edit(Session $session)
    {
        $session->load(['category', 'contents', 'questions']);
        $categories = Category::orderBy('name')->get();

        $nextOrdersByCategory = $categories->mapWithKeys(function ($cat) {
            $max = Session::where('category_id', $cat->id)->max('order') ?? 0;
            return [$cat->id => $max + 1];
        });

        $defaultNextOrder = (Session::whereNull('category_id')->max('order') ?? 0) + 1;
        $nextTrainOrder = (Session::where('in_general_stream', true)->max('general_stream_order') ?? 0) + 1;

        $contents = $session->contents()->orderBy('order', 'asc')->get();
        $diagnosticQuestions = $session->questions()->where('phase_type', 'diagnostic')->get();

        // Unified MCQs: union of reinforcement and omr (deduplicated so each question appears only once)
        $reinforcementQuestions = $session->questions()
            ->whereIn('phase_type', ['reinforcement', 'omr'])
            ->get()
            ->unique(fn($q) => trim($q->question_text . '|' . $q->question_text_malayalam))
            ->values();

        $omrQuestions = $reinforcementQuestions;

        return view('admin.sessions.form', [
            'session' => $session,
            'categories' => $categories,
            'isEdit' => true,
            'contents' => $contents,
            'diagnosticQuestions' => $diagnosticQuestions,
            'reinforcementQuestions' => $reinforcementQuestions,
            'omrQuestions' => $omrQuestions,
            'nextOrdersByCategory' => $nextOrdersByCategory,
            'defaultNextOrder' => $defaultNextOrder,
            'nextTrainOrder' => $nextTrainOrder,
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
            'feature_image' => 'nullable|string|max:1000',
            'feature_image_file' => 'nullable|image|max:10240',
            'feature_video' => 'nullable|string|max:1000',
            'feature_video_file' => 'nullable|file|mimes:mp4,mov,ogg,webm,qt,avi,mkv|max:102400',
            'slug' => 'required|string|max:255|unique:learning_sessions,slug,' . $session->id,
            'category_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'xp_reward' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'access_level' => 'nullable|string|in:guest,registered,premium',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'in_general_stream' => 'boolean',
            'general_stream_order' => 'nullable|integer',
            'creation_mode' => 'nullable|string|in:manual,code',
            'custom_html' => 'nullable|string',
        ]);

        $creationMode = $request->input('creation_mode', 'manual');

        $featureImage = $validated['feature_image'] ?? $session->feature_image;
        if ($request->hasFile('feature_image_file')) {
            $file = $request->file('feature_image_file');
            $path = $file->store('media/images', 'public');
            $featureImage = '/storage/' . $path;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $featureImage,
                'file_type' => 'image',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        $featureVideo = $validated['feature_video'] ?? $session->feature_video;
        if ($request->hasFile('feature_video_file')) {
            $file = $request->file('feature_video_file');
            $path = $file->store('media/videos', 'public');
            $featureVideo = '/storage/' . $path;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $featureVideo,
                'file_type' => 'video',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        $categoryId = $validated['category_id'] ?? null;
        $order = ($request->filled('order') && (int)$request->input('order') > 0)
            ? (int)$request->input('order')
            : ($session->order ?: ((Session::where('category_id', $categoryId)->max('order') ?? 0) + 1));

        $inGeneralStream = $request->boolean('in_general_stream', true);
        $generalStreamOrder = null;
        if ($inGeneralStream) {
            $generalStreamOrder = ($request->filled('general_stream_order') && (int)$request->input('general_stream_order') > 0)
                ? (int)$request->input('general_stream_order')
                : ($session->general_stream_order ?: ((Session::where('in_general_stream', true)->max('general_stream_order') ?? 0) + 1));
        }

        $accessLevel = $request->input('access_level') ?: ($request->boolean('is_premium') ? 'premium' : ($session->access_level ?? 'guest'));
        $isPremium = ($accessLevel === 'premium');

        $session->update([
            'title' => $validated['title'],
            'title_malayalam' => $validated['title_malayalam'] ?? null,
            'feature_image' => $featureImage,
            'feature_video' => $featureVideo,
            'slug' => Str::slug($validated['slug']),
            'category_id' => $categoryId,
            'order' => $order,
            'xp_reward' => $validated['xp_reward'],
            'is_active' => $request->boolean('is_active', true),
            'access_level' => $accessLevel,
            'is_premium' => $isPremium,
            'price' => $isPremium ? ($request->input('price') ?: 199.00) : null,
            'in_general_stream' => $inGeneralStream,
            'general_stream_order' => $generalStreamOrder,
            'creation_mode' => $creationMode,
            'custom_html' => $creationMode === 'code' ? $request->input('custom_html') : $session->custom_html,
        ]);

        if ($creationMode === 'manual') {
            $this->syncContentsAndQuestions($session, $request);
        }

        return redirect()->route('admin.sessions.edit', $session)
            ->with('success', 'Session updated successfully!');
    }

    /**
     * Remove the specified session.
     */
    public function destroy(Session $session)
    {
        $sessionTitle = $session->title;
        $session->contents()->delete();
        $session->questions()->delete();
        $session->progress()->delete();
        $session->delete();

        $this->normalizeMixedTrainOrder();

        return redirect()->route('admin.sessions.index')
            ->with('success', "Session '{$sessionTitle}' was deleted successfully.");
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
                    $phaseType = $q['phase_type'] ?? 'reinforcement';

                    Question::create([
                        'session_id' => $session->id,
                        'category_id' => $session->category_id,
                        'phase_type' => $phaseType,
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

                    // Auto-mirror reinforcement MCQs as OMR questions so they appear in OMR test automatically
                    if ($phaseType === 'reinforcement') {
                        Question::create([
                            'session_id' => $session->id,
                            'category_id' => $session->category_id,
                            'phase_type' => 'omr',
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
}
