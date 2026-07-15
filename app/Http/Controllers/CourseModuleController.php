<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseModuleController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['course_id'] = $course->id;
        $validated['sort_order'] = $course->modules()->count();

        $module = CourseModule::create($validated);

        return response()->json(['message' => 'Module created successfully!']);
    }

    public function update(Request $request, Course $course, CourseModule $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $module->update($validated);
        return response()->json(['message' => 'Module updated successfully!']);
    }

    public function destroy(Course $course, CourseModule $module)
    {
        $module->delete();
        return response()->json(['message' => 'Module deleted successfully!']);
    }
}

class LessonController extends Controller
{
    public function store(Request $request, CourseModule $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,pdf,text,link,iframe',
            'video_url' => 'nullable|string',
            'text_content' => 'nullable|string',
            'external_link' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
        ]);

        $validated['module_id'] = $module->id;
        $validated['sort_order'] = $module->lessons()->count();

        $lesson = Lesson::create($validated);

        return response()->json(['message' => 'Lesson created successfully!']);
    }

    public function update(Request $request, CourseModule $module, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,pdf,text,link,iframe',
            'video_url' => 'nullable|string',
            'text_content' => 'nullable|string',
            'external_link' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $lesson->update($validated);
        return response()->json(['message' => 'Lesson updated successfully!']);
    }

    public function destroy(CourseModule $module, Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(['message' => 'Lesson deleted successfully!']);
    }

    public function show(Course $course, Lesson $lesson)
    {
        $lesson->load('module.course');
        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();

        if (!$enrollment && !$lesson->is_preview) {
            return redirect()->route('courses.show', $course)->with('error', 'You must enroll to access this lesson.');
        }

        return view('courses.lesson', compact('course', 'lesson', 'enrollment'));
    }

    public function markComplete(Course $course, Lesson $lesson)
    {
        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return response()->json(['message' => 'Not enrolled'], 422);
        }

        $progress = $enrollment->lessonProgress()->firstOrCreate(
            ['lesson_id' => $lesson->id],
            ['status' => 'in_progress']
        );

        $progress->update(['status' => 'completed', 'completed_at' => now()]);

        $totalLessons = $course->lessons_count;
        $completedLessons = $enrollment->lessonProgress()->where('status', 'completed')->count();
        $percentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;

        $enrollment->update(['progress' => $percentage, 'last_accessed_at' => now()]);

        if ($percentage >= 100 && $enrollment->status !== 'completed') {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return response()->json(['message' => 'Lesson marked as complete!', 'progress' => $percentage]);
    }
}
