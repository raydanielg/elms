<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $assignments = $course->assignments()->withCount('submissions')->get();
        return view('assignments.index', compact('course', 'assignments'));
    }

    public function create(Course $course)
    {
        return view('assignments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'max_points' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
            'allow_late_submission' => 'boolean',
            'late_penalty_percent' => 'nullable|integer|min:0|max:100',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $validated['course_id'] = $course->id;
        Assignment::create($validated);

        return response()->json(['message' => 'Assignment created successfully!', 'redirect' => route('courses.assignments.index', $course)]);
    }

    public function show(Course $course, Assignment $assignment)
    {
        $assignment->load('submissions.user');
        $mySubmission = $assignment->submissions()->where('user_id', auth()->id())->first();
        return view('assignments.show', compact('course', 'assignment', 'mySubmission'));
    }

    public function edit(Course $course, Assignment $assignment)
    {
        return view('assignments.edit', compact('course', 'assignment'));
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'max_points' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
            'allow_late_submission' => 'boolean',
            'late_penalty_percent' => 'nullable|integer|min:0|max:100',
            'is_published' => 'boolean',
        ]);

        $assignment->update($validated);
        return response()->json(['message' => 'Assignment updated successfully!']);
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        $assignment->delete();
        return response()->json(['message' => 'Assignment deleted successfully!']);
    }

    public function submit(Request $request, Course $course, Assignment $assignment)
    {
        $validated = $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $existing = $assignment->submissions()->where('user_id', auth()->id())->first();
        if ($existing) {
            return response()->json(['message' => 'You have already submitted this assignment.'], 422);
        }

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('submissions', 'public');
        }
        unset($validated['file']);
        $validated['assignment_id'] = $assignment->id;
        $validated['user_id'] = auth()->id();
        $validated['status'] = $assignment->due_date && now()->isAfter($assignment->due_date) ? 'late' : 'submitted';

        AssignmentSubmission::create($validated);

        return response()->json(['message' => 'Assignment submitted successfully!']);
    }

    public function grade(Request $request, Course $course, Assignment $assignment, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:' . $assignment->max_points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Submission graded successfully!']);
    }
}
