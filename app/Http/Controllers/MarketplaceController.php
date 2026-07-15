<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('status', 'published')->where('visibility', 'marketplace')
            ->with(['owner', 'category']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('price', 0);
            } else {
                $query->where('price', '>', 0);
            }
        }
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low': $query->orderBy('price', 'asc'); break;
                case 'price_high': $query->orderBy('price', 'desc'); break;
                case 'rating': $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc'); break;
                default: $query->latest();
            }
        } else {
            $query->latest();
        }

        $courses = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('marketplace.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        if ($course->visibility !== 'marketplace' || $course->status !== 'published') {
            abort(404);
        }
        $course->load(['owner', 'category', 'modules.lessons', 'reviews.user']);
        $enrolled = auth()->check() ? auth()->user()->enrollments()->where('course_id', $course->id)->first() : null;
        return view('marketplace.show', compact('course', 'enrolled'));
    }
}
