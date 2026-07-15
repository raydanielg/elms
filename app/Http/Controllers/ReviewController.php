<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Course;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('course_id', $course->id)->where('user_id', auth()->id())->first();
        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this course.'], 422);
        }

        Review::create([
            'course_id' => $course->id,
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json(['message' => 'Review posted successfully!']);
    }

    public function destroy(Course $course, Review $review)
    {
        if ($review->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted.']);
    }
}
