<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

class MemeBankController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');
        $categories = Category::withCount('questions')->get();

        $query = Question::with('category')
            ->where(function ($q) {
                $q->whereNotNull('trap_warning')
                  ->orWhereNotNull('meme_image_url');
            });

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $memes = $query->paginate(12);

        return view('pages.memebank', compact('memes', 'categories', 'categorySlug'));
    }
}
