<?php

namespace App\Http\Controllers;

use App\Models\CourseVersion;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseVersionController extends Controller
{
    public function index(Course $course)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $versions = CourseVersion::where('course_id', $course->id)->latest()->get();
        return view('course-versions.index', compact('versions', 'course'));
    }

    public function createVersion(Course $course)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $lastVersion = CourseVersion::where('course_id', $course->id)->max('version_number') ?? 0;

        $snapshot = [
            'title' => $course->title,
            'description' => $course->description,
            'modules' => $course->modules()->with('lessons')->get()->toArray(),
        ];

        CourseVersion::create([
            'course_id' => $course->id,
            'version_number' => $lastVersion + 1,
            'content_snapshot' => $snapshot,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Version ' . ($lastVersion + 1) . ' created']);
    }

    public function publish(Course $course, CourseVersion $version)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $snapshot = $version->content_snapshot;

        if (isset($snapshot['title'])) $course->update(['title' => $snapshot['title']]);
        if (isset($snapshot['description'])) $course->update(['description' => $snapshot['description']]);

        $version->update(['status' => 'published', 'published_at' => now()]);

        CourseVersion::where('course_id', $course->id)
            ->where('id', '!=', $version->id)
            ->where('status', 'published')
            ->update(['status' => 'archived']);

        return response()->json(['message' => 'Version published']);
    }

    public function show(Course $course, CourseVersion $version)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        return view('course-versions.show', compact('version', 'course'));
    }
}
