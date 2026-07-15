<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('courses')->with('parent')->latest()->paginate(15);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        Category::create($validated);

        return response()->json(['message' => 'Category created successfully!']);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);
        return response()->json(['message' => 'Category updated successfully!']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully!']);
    }
}
