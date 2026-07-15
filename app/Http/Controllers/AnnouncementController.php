<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $query = Announcement::with(['user', 'course']);

        if (!auth()->user()->isSuperAdmin()) {
            $query->where(function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id)
                  ->orWhereNull('tenant_id');
            });
        }

        $announcements = $query->latest()->paginate(15);
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $courses = Course::where('owner_id', auth()->id())->orWhere('tenant_id', auth()->user()->tenant_id)->get();
        return view('announcements.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'audience' => 'required|in:all,students,teachers,course',
            'course_id' => 'nullable|exists:courses,id',
            'is_pinned' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['tenant_id'] = auth()->user()->tenant_id;

        Announcement::create($validated);

        return response()->json(['message' => 'Announcement published successfully!', 'redirect' => route('announcements.index')]);
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('user', 'course');
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $courses = Course::where('owner_id', auth()->id())->orWhere('tenant_id', auth()->user()->tenant_id)->get();
        return view('announcements.edit', compact('announcement', 'courses'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'audience' => 'required|in:all,students,teachers,course',
            'course_id' => 'nullable|exists:courses,id',
            'is_pinned' => 'boolean',
        ]);

        $announcement->update($validated);
        return response()->json(['message' => 'Announcement updated successfully!']);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['message' => 'Announcement deleted successfully!']);
    }
}
