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
        $search = trim((string) $request->get('search', ''));
        
        $query = PscmSession::latest();
        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('lesson_content', 'like', "%{$search}%")
                  ->orWhere('hook_question', 'like', "%{$search}%");
            });
        }
        $sessions = $query->paginate(15)->withQueryString();

        return view('admin.pscm_sessions.index', compact('sessions', 'categories', 'selectedCategory', 'search'));
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

        // Direct file upload handling if user submitted files with the form
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $path = $file->store('media/videos', 'public');
            $vUrl = url('/storage/' . $path);
            $data['media']['video_url'] = $vUrl;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $vUrl,
                'file_type' => 'video',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        } elseif ($request->filled('video_url')) {
            $vUrl = trim($request->input('video_url'));
            if ($vUrl && !str_starts_with($vUrl, 'http://') && !str_starts_with($vUrl, 'https://')) {
                $vUrl = url($vUrl);
            }
            $data['media']['video_url'] = $vUrl;
        }

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('media/images', 'public');
            $iUrl = url('/storage/' . $path);
            $data['media']['image_url'] = $iUrl;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $iUrl,
                'file_type' => 'image',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        } elseif ($request->filled('image_url')) {
            $iUrl = trim($request->input('image_url'));
            if ($iUrl && !str_starts_with($iUrl, 'http://') && !str_starts_with($iUrl, 'https://')) {
                $iUrl = url($iUrl);
            }
            $data['media']['image_url'] = $iUrl;
        }

        if ($request->filled('image_caption')) {
            $data['media']['image_caption'] = trim($request->input('image_caption'));
        }

        if (!empty($data['practice_mcqs'])) {
            $data['practice_mcqs'] = is_array($data['practice_mcqs']) ? $data['practice_mcqs'] : (json_decode($data['practice_mcqs'], true) ?? []);
        } else {
            $data['practice_mcqs'] = [];
        }
        
        if (!empty($data['hook_question'])) {
            $data['hook_question'] = is_array($data['hook_question']) ? $data['hook_question'] : (json_decode($data['hook_question'], true) ?? []);
        } else {
            $data['hook_question'] = [];
        }
        
        PscmSession::create($data);

        return redirect()->route('admin.pscm.index')->with('success', 'MicroLearning Session created successfully.');
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
            'access_level' => 'nullable|string|in:free,registered,premium',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        if ($request->has('access_level')) {
            $data['access_level'] = $request->input('access_level', 'premium');
        }
        
        $media = is_array($pscmSession->media) ? $pscmSession->media : [];

        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $path = $file->store('media/videos', 'public');
            $vUrl = url('/storage/' . $path);
            $media['video_url'] = $vUrl;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $vUrl,
                'file_type' => 'video',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        } elseif ($request->has('video_url')) {
            $vUrl = trim($request->input('video_url', ''));
            if ($vUrl && !str_starts_with($vUrl, 'http://') && !str_starts_with($vUrl, 'https://')) {
                $vUrl = url($vUrl);
            }
            $media['video_url'] = $vUrl;
        }

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('media/images', 'public');
            $iUrl = url('/storage/' . $path);
            $media['image_url'] = $iUrl;

            MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'url' => $iUrl,
                'file_type' => 'image',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        } elseif ($request->has('image_url')) {
            $iUrl = trim($request->input('image_url', ''));
            if ($iUrl && !str_starts_with($iUrl, 'http://') && !str_starts_with($iUrl, 'https://')) {
                $iUrl = url($iUrl);
            }
            $media['image_url'] = $iUrl;
        }

        if ($request->has('image_caption')) {
            $media['image_caption'] = trim($request->input('image_caption', ''));
        }

        $data['media'] = $media;

        if (!empty($data['practice_mcqs'])) {
            $data['practice_mcqs'] = is_array($data['practice_mcqs']) ? $data['practice_mcqs'] : (json_decode($data['practice_mcqs'], true) ?? []);
        } else {
            $data['practice_mcqs'] = [];
        }

        if (!empty($data['hook_question'])) {
            $data['hook_question'] = is_array($data['hook_question']) ? $data['hook_question'] : (json_decode($data['hook_question'], true) ?? []);
        } else {
            $data['hook_question'] = [];
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
