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
        $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);

        // Determine file type category
        if (str_starts_with($mimeType, 'image/')) {
            $fileType = 'image';
            $folder = 'media/images';
            
            // Automatic WebP conversion & optimization pipeline
            $stored = $this->processAndStoreImage($file, $folder, $nameOnly, $extension);
            $filePath = $stored['file_path'];
            $mimeType = $stored['mime_type'];
            $fileSize = $stored['file_size'];
        } else {
            if (str_starts_with($mimeType, 'audio/')) {
                $fileType = 'audio';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $fileType = 'video';
            } else {
                $fileType = 'document';
            }
            $folder = "media/{$fileType}s";
            $safeName = Str::slug($nameOnly) . '-' . time() . '.' . $extension;
            $filePath = $file->storeAs($folder, $safeName, 'public');
        }

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

    /**
     * Convert uploaded image to high-efficiency WebP format if supported.
     * Preserves alpha channel for transparent PNGs, scales oversized images (>1920px),
     * and falls back cleanly if GD/Imagick WebP is unavailable.
     *
     * @return array{file_path: string, mime_type: string, file_size: int, safe_name: string}
     */
    private function processAndStoreImage($file, string $folder, string $nameOnly, string $extension): array
    {
        // 1. If already WebP, SVG, or GIF (animated), store as-is
        if (in_array($extension, ['webp', 'svg', 'gif'])) {
            $safeName = Str::slug($nameOnly) . '-' . time() . '.' . $extension;
            $filePath = $file->storeAs($folder, $safeName, 'public');
            return [
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'safe_name' => $safeName,
            ];
        }

        // 2. Try conversion using PHP GD
        if (function_exists('imagewebp')) {
            $sourcePath = $file->getRealPath();
            $image = null;

            if ($extension === 'jpg' || $extension === 'jpeg') {
                if (function_exists('imagecreatefromjpeg')) {
                    $image = @imagecreatefromjpeg($sourcePath);
                }
            } elseif ($extension === 'png') {
                if (function_exists('imagecreatefrompng')) {
                    $image = @imagecreatefrompng($sourcePath);
                    if ($image) {
                        imagepalettetotruecolor($image);
                        imagealphablending($image, true);
                        imagesavealpha($image, true);
                    }
                }
            }

            if ($image) {
                // Constrain max dimensions to 1920px
                $origWidth = imagesx($image);
                $origHeight = imagesy($image);
                $maxDim = 1920;

                if ($origWidth > $maxDim || $origHeight > $maxDim) {
                    if ($origWidth >= $origHeight) {
                        $newWidth = $maxDim;
                        $newHeight = (int) round(($origHeight / $origWidth) * $maxDim);
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = (int) round(($origWidth / $origHeight) * $maxDim);
                    }
                    $scaled = imagescale($image, $newWidth, $newHeight);
                    if ($scaled) {
                        imagedestroy($image);
                        $image = $scaled;
                    }
                }

                $safeName = Str::slug($nameOnly) . '-' . time() . '.webp';
                $tempPath = tempnam(sys_get_temp_dir(), 'webp_');
                $converted = imagewebp($image, $tempPath, 82);
                imagedestroy($image);

                if ($converted && file_exists($tempPath)) {
                    $relativeFilePath = "{$folder}/{$safeName}";
                    Storage::disk('public')->put($relativeFilePath, file_get_contents($tempPath));
                    $fileSize = filesize($tempPath);
                    @unlink($tempPath);

                    return [
                        'file_path' => $relativeFilePath,
                        'mime_type' => 'image/webp',
                        'file_size' => $fileSize,
                        'safe_name' => $safeName,
                    ];
                }
            }
        }

        // 3. Try conversion using Imagick if GD is not available
        if (class_exists(\Imagick::class)) {
            try {
                $imagick = new \Imagick($file->getRealPath());
                if (in_array('WEBP', $imagick->queryFormats())) {
                    $imagick->setImageFormat('webp');
                    $imagick->setImageCompressionQuality(82);

                    $w = $imagick->getImageWidth();
                    $h = $imagick->getImageHeight();
                    if ($w > 1920 || $h > 1920) {
                        $imagick->scaleImage(min($w, 1920), min($h, 1920), true);
                    }

                    $safeName = Str::slug($nameOnly) . '-' . time() . '.webp';
                    $relativeFilePath = "{$folder}/{$safeName}";
                    Storage::disk('public')->put($relativeFilePath, $imagick->getImageBlob());
                    $fileSize = strlen($imagick->getImageBlob());
                    $imagick->clear();
                    $imagick->destroy();

                    return [
                        'file_path' => $relativeFilePath,
                        'mime_type' => 'image/webp',
                        'file_size' => $fileSize,
                        'safe_name' => $safeName,
                    ];
                }
            } catch (\Throwable $e) {
                // Fallback to normal upload
            }
        }

        // 4. Safe fallback: Store original file without modification
        $safeName = Str::slug($nameOnly) . '-' . time() . '.' . $extension;
        $filePath = $file->storeAs($folder, $safeName, 'public');

        return [
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'safe_name' => $safeName,
        ];
    }
}
