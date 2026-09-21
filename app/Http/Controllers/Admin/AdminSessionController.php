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
        // Normalize any gaps or legacy duplicate order entries
        $this->normalizeMixedTrainOrder();

        $categories = Category::orderBy('order')->get();

        // Active mixed practice train ordered by sequence
        $mixedTrain = Session::with(['category'])
            ->where('in_general_stream', true)
            ->orderBy('general_stream_order', 'asc')
            ->orderBy('id', 'asc')
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
        // 1. Direct single session order update (session_id + target_order)
        if ($request->filled('session_id') && $request->filled('target_order')) {
            $request->validate([
                'session_id' => 'required|exists:learning_sessions,id',
                'target_order' => 'required|integer|min:1',
            ]);

            $session = Session::findOrFail($request->input('session_id'));
            $this->setSessionTrainOrder($session, (int)$request->input('target_order'), $session->general_stream_order);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Moved '{$session->title}' to Train Step #{$session->fresh()->general_stream_order}.",
                    'mixedTrain' => Session::with(['category'])->where('in_general_stream', true)->orderBy('general_stream_order')->orderBy('id')->get(),
                ]);
            }

            return back()->with('success', "Moved '{$session->title}' to Train Step #{$session->fresh()->general_stream_order}.");
        }

        // 2. Full array reorder (ordered_ids)
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
                'mixedTrain' => Session::with(['category'])->where('in_general_stream', true)->orderBy('general_stream_order')->orderBy('id')->get(),
            ]);
        }

        return back()->with('success', 'Train order updated successfully!');
    }

    /**
     * Place a session at a specific train order, shifting colliding sessions.
     */
    public function setSessionTrainOrder(Session $targetSession, ?int $desiredOrder, ?int $oldOrder = null): void
    {
        if (!$targetSession->in_general_stream || $desiredOrder === null || $desiredOrder <= 0) {
            $targetSession->in_general_stream = false;
            $targetSession->general_stream_order = null;
            $targetSession->save();
            return;
        }

        $desiredOrder = (int)$desiredOrder;

        if ($oldOrder !== null && $oldOrder > 0) {
            if ($desiredOrder < $oldOrder) {
                // Moving up: shift items in [$desiredOrder, $oldOrder - 1] up (+1)
                Session::where('in_general_stream', true)
                    ->where('id', '!=', $targetSession->id)
                    ->where('general_stream_order', '>=', $desiredOrder)
                    ->where('general_stream_order', '<', $oldOrder)
                    ->increment('general_stream_order');
            } elseif ($desiredOrder > $oldOrder) {
                // Moving down: shift items in [$oldOrder + 1, $desiredOrder] down (-1)
                Session::where('in_general_stream', true)
                    ->where('id', '!=', $targetSession->id)
                    ->where('general_stream_order', '>', $oldOrder)
                    ->where('general_stream_order', '<=', $desiredOrder)
                    ->decrement('general_stream_order');
            }
        } else {
            // Newly placed into train: shift existing items >= $desiredOrder up (+1)
            Session::where('in_general_stream', true)
                ->where('id', '!=', $targetSession->id)
                ->where('general_stream_order', '>=', $desiredOrder)
                ->increment('general_stream_order');
        }

        $targetSession->in_general_stream = true;
        $targetSession->general_stream_order = $desiredOrder;
        $targetSession->save();
    }

    /**
     * Place a session at a specific category/subject unit order, shifting colliding sessions.
     */
    public function setSessionCategoryOrder(Session $targetSession, ?int $categoryId, ?int $desiredOrder, ?int $oldOrder = null, ?int $oldCategoryId = null): void
    {
        if ($desiredOrder === null || $desiredOrder <= 0) {
            return;
        }

        $desiredOrder = (int)$desiredOrder;

        $query = Session::where('id', '!=', $targetSession->id);
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } else {
            $query->whereNull('category_id');
        }

        if ($oldCategoryId == $categoryId && $oldOrder !== null && $oldOrder > 0) {
            if ($desiredOrder < $oldOrder) {
                (clone $query)->where('order', '>=', $desiredOrder)
                    ->where('order', '<', $oldOrder)
                    ->increment('order');
            } elseif ($desiredOrder > $oldOrder) {
                (clone $query)->where('order', '>', $oldOrder)
                    ->where('order', '<=', $desiredOrder)
                    ->decrement('order');
            }
        } else {
            (clone $query)->where('order', '>=', $desiredOrder)
                ->increment('order');
        }

        $targetSession->order = $desiredOrder;
        $targetSession->save();
    }

    /**
     * Normalize general stream order sequence so there are no gaps or duplicates.
     */
    public function normalizeMixedTrainOrder(): void
    {
        $sessions = Session::where('in_general_stream', true)
            ->orderBy('general_stream_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($sessions as $index => $s) {
            $expected = $index + 1;
            if ($s->general_stream_order !== $expected) {
                $s->general_stream_order = $expected;
                $s->save();
            }
        }
    }

    /**
     * Normalize subject unit orders within a category so there are no gaps or duplicates.
     */
    public function normalizeCategoryOrder(?int $categoryId): void
    {
        $query = Session::query();
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } else {
            $query->whereNull('category_id');
        }

        $sessions = $query->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($sessions as $index => $s) {
            $expected = $index + 1;
            if ($s->order !== $expected) {
                $s->order = $expected;
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
            'session' => new Session([
                'access_level' => 'premium',
                'is_premium' => true,
                'price' => 199.00,
            ]),
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
            'pass_mark' => 'nullable|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1|max:180',
            'contents' => 'nullable|array',
            'contents.*.type' => 'required|string|in:image,video,audio,text,html,map_globe,hook_mcq,practice_mcq',
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

        $accessLevel = $request->input('access_level');
        if (!$accessLevel) {
            if ($request->has('is_premium')) {
                $accessLevel = $request->boolean('is_premium') ? 'premium' : 'guest';
            } else {
                $accessLevel = 'premium';
            }
        }
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
            'price' => $isPremium ? ($request->filled('price') ? (float)$request->input('price') : 199.00) : null,
            'in_general_stream' => $inGeneralStream,
            'general_stream_order' => $generalStreamOrder,
            'creation_mode' => $creationMode,
            'custom_html' => $creationMode === 'code' ? $request->input('custom_html') : null,
            'pass_mark' => $request->filled('pass_mark') ? (int)$request->input('pass_mark') : 50,
            'time_limit_minutes' => $request->filled('time_limit_minutes') ? (int)$request->input('time_limit_minutes') : 10,
        ]);

        // Place and re-sequence category order & train order cleanly without collision
        $this->setSessionCategoryOrder($session, $categoryId, $order);
        if ($inGeneralStream) {
            $this->setSessionTrainOrder($session, $generalStreamOrder);
        } else {
            $session->update(['in_general_stream' => false, 'general_stream_order' => null]);
            $this->normalizeMixedTrainOrder();
        }

        if ($creationMode === 'manual') {
            $this->syncContentsAndQuestions($session, $request);
        }

        $fresh = $session->fresh();
        return redirect()->route('admin.sessions.edit', $session)
            ->with('success', "Learning Session created successfully! (Unit #{$fresh->order} in subject, Train Step #" . ($fresh->general_stream_order ?? 'N/A') . ")")
            ->with('view_url', route('session.show', $session->slug))
            ->with('finished_url', route('session.show', ['slug' => $session->slug, 'preview' => 'finished']));
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
            'pass_mark' => 'nullable|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1|max:180',
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

        $oldCategoryId = $session->category_id;
        $oldSubjectOrder = $session->order;
        $oldTrainOrder = $session->in_general_stream ? $session->general_stream_order : null;

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

        $accessLevel = $request->input('access_level');
        if (!$accessLevel) {
            if ($request->has('is_premium')) {
                $accessLevel = $request->boolean('is_premium') ? 'premium' : 'guest';
            } else {
                $accessLevel = $session->access_level ?? ($session->is_premium ? 'premium' : 'guest');
            }
        }
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
            'price' => $isPremium ? ($request->filled('price') ? (float)$request->input('price') : 199.00) : null,
            'in_general_stream' => $inGeneralStream,
            'general_stream_order' => $generalStreamOrder,
            'creation_mode' => $creationMode,
            'custom_html' => $creationMode === 'code' ? $request->input('custom_html') : $session->custom_html,
            'pass_mark' => $request->filled('pass_mark') ? (int)$request->input('pass_mark') : ($session->pass_mark ?? 50),
            'time_limit_minutes' => $request->filled('time_limit_minutes') ? (int)$request->input('time_limit_minutes') : ($session->time_limit_minutes ?? 10),
        ]);

        // Re-sequence subject category order
        if ($oldCategoryId && $oldCategoryId != $categoryId) {
            $this->normalizeCategoryOrder($oldCategoryId);
        }
        $this->setSessionCategoryOrder($session, $categoryId, $order, $oldSubjectOrder, $oldCategoryId);

        // Re-sequence general mixed practice train order
        if ($inGeneralStream) {
            $this->setSessionTrainOrder($session, $generalStreamOrder, $oldTrainOrder);
        } else {
            $session->update(['in_general_stream' => false, 'general_stream_order' => null]);
            $this->normalizeMixedTrainOrder();
        }

        if ($creationMode === 'manual') {
            $this->syncContentsAndQuestions($session, $request);
            
            // Clear all user progress for this session when content is updated to prevent stale OMR scorecards
            \App\Models\UserSessionProgress::where('session_id', $session->id)->delete();
        }

        $fresh = $session->fresh();
        return redirect()->route('admin.sessions.edit', $session)
            ->with('success', "Session updated successfully! (Unit #{$fresh->order} in subject, Train Step #" . ($fresh->general_stream_order ?? 'N/A') . ")")
            ->with('view_url', route('session.show', $session->slug))
            ->with('finished_url', route('session.show', ['slug' => $session->slug, 'preview' => 'finished']));
    }

    /**
     * Remove the specified session.
     */
    public function destroy(Session $session)
    {
        $categoryId = $session->category_id;
        $sessionTitle = $session->title;
        $session->contents()->delete();
        $session->questions()->delete();
        $session->progress()->delete();
        $session->delete();

        $this->normalizeMixedTrainOrder();
        $this->normalizeCategoryOrder($categoryId);

        return redirect()->route('admin.sessions.index')
            ->with('success', "Session '{$sessionTitle}' was deleted successfully.");
    }

    /**
     * Sync content blocks from the modular builder.
     *
     * session_contents is the single source of truth.
     * hook_mcq and practice_mcq blocks automatically feed the OMR exam
     * via Session::getEffectiveOmrQuestionsAttribute() at query time.
     * We no longer write to the `questions` table from here.
     */
    private function syncContentsAndQuestions(Session $session, Request $request): void
    {
        // Wipe legacy questions table data for this session (clean slate).
        // Questions are now derived purely from session_contents blocks.
        $session->questions()->delete();

        if (!$request->has('contents_json')) {
            return;
        }

        $contentsData = json_decode($request->input('contents_json'), true) ?? [];

        // Delete existing content blocks and rewrite fresh
        $session->contents()->delete();

        foreach ($contentsData as $idx => $block) {
            if (empty($block['type'])) {
                continue;
            }

            SessionContent::create([
                'session_id'   => $session->id,
                'unit_order'   => (int) ($block['unit_order'] ?? 1),
                'unit_title'   => $block['unit_title'] ?? null,
                'type'         => $block['type'],
                'content_data' => $block['content_data'] ?? [],
                'order'        => $idx + 1,
            ]);
        }
    }
}
