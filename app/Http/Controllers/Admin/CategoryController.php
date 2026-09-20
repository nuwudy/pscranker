<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories / subject tracks.
     */
    public function index()
    {
        $categories = Category::withCount(['sessions', 'questions'])
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_malayalam' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:64',
            'badge_color' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $maxOrder = Category::max('order') ?? 0;

        $category = Category::create([
            'name' => $validated['name'],
            'name_malayalam' => $validated['name_malayalam'] ?? null,
            'slug' => $slug,
            'icon' => $validated['icon'] ?? 'sparkles',
            'badge_color' => $validated['badge_color'] ?? 'blue',
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? ($maxOrder + 1),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Subject Category created successfully!',
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', "Subject '{$category->name}' created successfully!");
    }

    /**
     * Quick-store AJAX endpoint for inline creation from session form.
     */
    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_malayalam' => 'nullable|string|max:255',
            'badge_color' => 'nullable|string|max:32',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        $count = Category::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $maxOrder = Category::max('order') ?? 0;

        $category = Category::create([
            'name' => $validated['name'],
            'name_malayalam' => $validated['name_malayalam'] ?? null,
            'slug' => $slug,
            'icon' => 'academic-cap',
            'badge_color' => $validated['badge_color'] ?? 'blue',
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'category' => $category,
            'categories' => Category::orderBy('name')->get(),
            'message' => 'New subject category created and selected!',
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_malayalam' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:64',
            'badge_color' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $category->update([
            'name' => $validated['name'],
            'name_malayalam' => $validated['name_malayalam'] ?? null,
            'slug' => Str::slug($validated['slug']),
            'icon' => $validated['icon'] ?? $category->icon,
            'badge_color' => $validated['badge_color'] ?? $category->badge_color,
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? $category->order,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Subject Category updated successfully!',
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', "Subject '{$category->name}' updated successfully!");
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        if ($category->sessions()->count() > 0) {
            return back()->with('error', "Cannot delete subject '{$category->name}' because it contains active learning sessions. Reassign them first.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', "Subject '{$name}' was deleted successfully.");
    }
}
