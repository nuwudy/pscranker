<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display the full media library interface.
     */
    public function index(Request $request)
    {
        $query = MediaFile::with('uploader')->latest();

        if ($request->filled('type') && in_array($request->type, ['image', 'audio', 'video', 'document'])) {
            $query->where('file_type', $request->type);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $mediaFiles = $query->paginate(24)->withQueryString();

        $stats = [
            'total' => MediaFile::count(),
            'images' => MediaFile::where('file_type', 'image')->count(),
            'audios' => MediaFile::where('file_type', 'audio')->count(),
            'videos' => MediaFile::where('file_type', 'video')->count(),
        ];

        return view('admin.media.index', compact('mediaFiles', 'stats'));
    }

    /**
     * JSON API to fetch media files for the content builder picker modal.
     */
    public function apiList(Request $request): JsonResponse
    {
        $query = MediaFile::latest();

        if ($request->filled('type') && in_array($request->type, ['image', 'audio', 'video', 'document'])) {
            $query->where('file_type', $request->type);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $files = $query->take(40)->get();

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Upload and store a new media file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:102400', // 100MB max
                'mimes:jpg,jpeg,png,webp,gif,svg,mp3,wav,ogg,m4a,mp4,webm,mov,avi,pdf',
            ],
            'title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $extension = strtolower($file->getClientOriginalExtension());

        // Determine file type category
        if (str_starts_with($mimeType, 'image/')) {
            $fileType = 'image';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $fileType = 'audio';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $fileType = 'video';
        } else {
            $fileType = 'document';
        }

        // Generate safe unique filename
        $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = Str::slug($nameOnly) . '-' . time() . '.' . $extension;
        $folder = "media/{$fileType}s";

        // Store file onto public disk
        $filePath = $file->storeAs($folder, $safeName, 'public');
        $url = Storage::url($filePath);

        $media = MediaFile::create([
            'name' => $request->input('title') ?: $originalName,
            'file_path' => $filePath,
            'url' => $url,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'uploaded_by' => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Media file uploaded successfully!',
                'media' => $media,
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', "File \"{$originalName}\" uploaded successfully!");
    }

    /**
     * Delete a media file.
     */
    public function destroy(MediaFile $medium)
    {
        if (Storage::disk('public')->exists($medium->file_path)) {
            Storage::disk('public')->delete($medium->file_path);
        }

        $name = $medium->name;
        $medium->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Media {$name} deleted.",
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', "Media file \"{$name}\" deleted successfully.");
    }
}
