<?php

namespace App\Http\Controllers;

use App\Models\CourseApproval;
use App\Models\Course;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CourseApprovalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('adminOrAbove');
        $query = CourseApproval::with('course', 'requestedBy');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('approval_type', $request->type);

        $approvals = $query->latest()->paginate(20);
        return view('course-approvals.index', compact('approvals'));
    }

    public function requestApproval(Request $request, Course $course)
    {
        $this->authorize('teacherOrAbove');
        $existing = CourseApproval::where('course_id', $course->id)->where('status', 'pending')->first();
        if ($existing) return back()->with('error', 'Approval already pending.');

        CourseApproval::create([
            'course_id' => $course->id,
            'approval_type' => $course->visibility === 'marketplace' ? 'platform' : 'institution',
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        return back()->with('success', 'Approval request submitted.');
    }

    public function approve(Request $request, CourseApproval $approval)
    {
        $this->authorize('adminOrAbove');
        $approval->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        $approval->course->update(['status' => 'published']);

        app(NotificationService::class)->notify(
            $approval->requested_by,
            'success',
            'Course Approved',
            "Your course \"{$approval->course->title}\" has been approved and is now published."
        );

        return response()->json(['message' => 'Course approved and published']);
    }

    public function reject(Request $request, CourseApproval $approval)
    {
        $this->authorize('adminOrAbove');
        $validated = $request->validate(['review_notes' => 'required|string']);
        $approval->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'review_notes' => $validated['review_notes'],
            'reviewed_at' => now(),
        ]);

        app(NotificationService::class)->notify(
            $approval->requested_by,
            'error',
            'Course Rejected',
            "Your course \"{$approval->course->title}\" was not approved. Reason: {$validated['review_notes']}"
        );

        return response()->json(['message' => 'Course rejected']);
    }
}
