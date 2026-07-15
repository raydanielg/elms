@extends('layouts.dashboard')

@section('page_title', 'Edit Assignment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="animate-slide-down"><h2 class="text-2xl font-bold text-gray-800">Edit Assignment</h2></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
        <form data-ajax action="{{ route('courses.assignments.update', [$course, $assignment]) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Title *</label><input type="text" name="title" required value="{{ $assignment->title }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none"></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Instructions</label><textarea name="instructions" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-maroon-300 outline-none">{{ $assignment->instructions }}</textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Max Points *</label><input type="number" name="max_points" required min="1" value="{{ $assignment->max_points }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-1.5">Due Date</label><input type="date" name="due_date" value="{{ $assignment->due_date?->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"></div>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="allow_late_submission" {{ $assignment->allow_late_submission ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"> Allow Late Submission</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" {{ $assignment->is_published ? 'checked' : '' }} class="w-4 h-4 rounded text-maroon-600"> Published</label>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-maroon-600 to-maroon-800 text-white rounded-xl font-bold text-sm">Save Changes</button>
                <a href="{{ route('courses.assignments.show', [$course, $assignment]) }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
