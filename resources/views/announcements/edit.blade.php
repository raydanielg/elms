@extends('layouts.dashboard')

@section('page_title', 'Edit Announcement')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Edit Announcement</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('announcements.update', $announcement) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Title *</label><input type="text" name="title" required value="{{ $announcement->title }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Body *</label><textarea name="body" required rows="6" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">{{ $announcement->body }}</textarea></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Audience</label>
                    <select name="audience" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="all" {{ $announcement->audience === 'all' ? 'selected' : '' }}>All Users</option>
                        <option value="students" {{ $announcement->audience === 'students' ? 'selected' : '' }}>Students Only</option>
                        <option value="teachers" {{ $announcement->audience === 'teachers' ? 'selected' : '' }}>Teachers Only</option>
                        <option value="course" {{ $announcement->audience === 'course' ? 'selected' : '' }}>Specific Course</option>
                    </select>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Course</label>
                    <select name="course_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="">Select course</option>
                        @foreach($courses as $course)<option value="{{ $course->id }}" {{ $announcement->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>@endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_pinned" {{ $announcement->is_pinned ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"> Pin this announcement</label>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Save Changes</button>
                <a href="{{ route('announcements.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
