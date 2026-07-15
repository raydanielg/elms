<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['owner', 'category', 'modules']);

        if (auth()->user()->isSuperAdmin()) {
            // sees all
        } elseif (auth()->user()->isAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        } elseif (auth()->user()->isTeacher() || auth()->user()->isSoloTeacher()) {
            $query->where('owner_id', auth()->id());
        } else {
            $query->where('status', 'published')->whereHas('enrollments', fn($q) => $q->where('user_id', auth()->id()));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $courses = $query->latest()->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'visibility' => 'required|in:private,marketplace',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'language' => 'nullable|string',
            'drip_enabled' => 'boolean',
            'drip_days' => 'nullable|integer|min:0',
            'has_certificate' => 'boolean',
            'duration_hours' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['owner_id'] = auth()->id();
        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'draft';

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully!',
            'redirect' => route('courses.show', $course)
        ]);
    }

    public function show(Course $course)
    {
        $course->load(['modules.lessons', 'owner', 'category', 'reviews.user', 'quizzes']);
        $enrolled = auth()->user()->enrollments()->where('course_id', $course->id)->first();
        return view('courses.show', compact('course', 'enrolled'));
    }

    public function edit(Course $course)
    {
        $categories = Category::where('is_active', true)->get();
        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:draft,published,archived,pending_review',
            'visibility' => 'required|in:private,marketplace',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'language' => 'nullable|string',
            'drip_enabled' => 'boolean',
            'drip_days' => 'nullable|integer|min:0',
            'has_certificate' => 'boolean',
            'duration_hours' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        if ($validated['status'] === 'published' && !$course->published_at) {
            $validated['published_at'] = now();
        }

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully!',
            'redirect' => route('courses.show', $course)
        ]);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json([
            'message' => 'Course deleted successfully!',
            'redirect' => route('courses.index')
        ]);
    }

    public function enroll(Course $course)
    {
        $existing = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
        if ($existing) {
            return response()->json(['message' => 'You are already enrolled in this course!'], 422);
        }

        Enrollment::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'status' => 'active',
            'progress' => 0,
        ]);

        return response()->json(['message' => 'Enrolled successfully! Welcome to the course.']);
    }
}
