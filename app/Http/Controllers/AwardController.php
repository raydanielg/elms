<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Certificate;
use App\Services\RecognitionEngine;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $awards = Award::where(function ($q) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', auth()->user()->tenant_id);
        })->with('recipient', 'giver', 'course')->latest()->paginate(20);
        return view('awards.index', compact('awards'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:student_of_month,top_performer,most_improved,perfect_attendance,instructor_recognition,custom',
            'awarded_to' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'period' => 'nullable|string',
            'is_public' => 'boolean',
            'issue_certificate' => 'boolean',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['awarded_by'] = auth()->id();

        $award = Award::create($validated);

        if ($request->boolean('issue_certificate')) {
            $enrollment = \App\Models\Enrollment::where('user_id', $validated['awarded_to'])
                ->where('course_id', $validated['course_id'] ?? 0)->first();
            if ($enrollment) {
                $cert = app(RecognitionEngine::class)->issueCertificate($enrollment, 'achievement', auth()->id());
                $award->update(['certificate_id' => $cert?->id]);
            }
        }

        app(NotificationService::class)->notify(
            $validated['awarded_to'],
            'success',
            'Award Received!',
            "You received the \"{$validated['title']}\" award. " . ($validated['description'] ?? '')
        );

        return response()->json(['message' => 'Award granted successfully']);
    }

    public function destroy(Award $award)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $award->delete();
        return response()->json(['message' => 'Award removed']);
    }

    public function honorRoll()
    {
        $awards = Award::where('is_public', true)
            ->where(function ($q) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', auth()->user()->tenant_id);
            })->with('recipient', 'course')->latest()->limit(50)->get();
        return view('awards.honor-roll', compact('awards'));
    }
}
