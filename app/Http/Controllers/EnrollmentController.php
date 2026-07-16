<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->with('course.owner')
            ->latest()
            ->paginate(12);
        return view('enrollments.index', compact('enrollments'));
    }

    public function destroy(Enrollment $enrollment)
    {
        if ($enrollment->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $enrollment->delete();
        return response()->json(['message' => 'Enrollment cancelled successfully!']);
    }
}
